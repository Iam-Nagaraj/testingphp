<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Merchant\DwollaController;
use App\Http\Requests\AmountRequest;
use App\Http\Requests\AmountWithdrawRequest;
use App\Http\Requests\UserPaymentRequest;
use App\Jobs\ProcessAchAmount;
use App\Jobs\ProcessAdminTransaction;
use App\Jobs\ProcessPlatformFee;
use App\Jobs\ProcessTransactionData;
use App\Models\Configuration;
use App\Models\DwollaAccount;
use App\Models\DwollaCustomer;
use App\Models\Notification;
use App\Models\PlaidAccount;
use App\Models\PlatformFee;
use App\Models\Transaction;
use App\Models\User;
use App\Models\Wallet;
use App\Traits\PlaidTrait;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class BankController extends Controller
{
	use PlaidTrait;

	private $client_id;
	private $secret;
	private $plaid_url;
	protected DwollaController $dwollaService;
	protected Transaction $transactionService;

	public function __construct(DwollaController $dwollaService, Transaction $transactionService)
	{
        $this->client_id = config('services.plaid.client_id');
		$this->secret = config('services.plaid.secret');
		$this->plaid_url = config('services.plaid.url');
        $this->dwollaService = $dwollaService;
		$this->transactionService = $transactionService;
	}

	public function index()
	{
		$userModel = User::where('id', Auth::user()->id)->first();
		$PlaidAccountModel = PlaidAccount::where('user_id', Auth::user()->id)->first();
		
		$account_list = [];
		if(!empty($PlaidAccountModel) && !empty($PlaidAccountModel->json_data)){
			$account_list = json_decode($PlaidAccountModel->json_data);
		}

		$link_token['link_token'] = $this->webLinkToken($userModel);

		return view('admin.bank.index', compact('link_token','account_list'));

	}

	public function getBankList()
	{
		
		$bankList = [];
		$bankListData = DwollaAccount::where('user_id', Auth::user()->id)->first();
		if(!empty($bankListData) && !empty($bankListData->json_data)){
			$bankListData = json_decode($bankListData->json_data);
			$bankList[] = [
				'bank_id' => $bankListData->bank_id,
				'bank_name' => $bankListData->bank_name,
				'type' => $bankListData->type,
				'status' => $bankListData->status,
				'bankAccountType' => '',
				'added' => date('Y/m/d', strtotime($bankListData->created)),
			];
			
			return $this->sendResponse($bankList, 'Bank Account List.');

		}
		
		return $this->sendError([], 'Token Expired Try Again');

	}

	public function depositToWallet(AmountRequest $request)
	{
		$userData = User::where('id', Auth::user()->id)->first();

		$hashPin = Hash::make($request->pin);

		$uuid = Str::uuid()->toString();
		$request->correlationId = $uuid;

		if(!Hash::check($request->pin, $userData->pin)){
			return $this->sendError([], 'Please Enter Correct Pin');
		}

		$configuration = Configuration::where('config_key','transaction_limit')->first();
		if(!empty($configuration->config_value) && $request->amount > $configuration->config_value){
			return $this->sendError([], "Single transaction limit is $configuration->config_value");
		}
        $payment_type = Transaction::MANUAL;
		try {

			$walletModel = Wallet::where('user_id', Auth::user()->id)->first();
			$access_token_data = $this->dwollaService->traitAccessToken();
			$access_token = $access_token_data;
			$request->source_id = $request->account_id;
			$request->destination_id = $walletModel->wallet_id;

			if($request->payment_type == 'instant'){
                $fundTransfer = $this->dwollaService->traitsameDayFundTransfer($access_token, $request);
                $payment_type = Transaction::INSTANT;
            } else {
                $fundTransfer = $this->dwollaService->traitfundTransfer($access_token, $request);
            }

			if(!empty($fundTransfer->code) && $fundTransfer->code == 'ValidationError'
			&& !empty($fundTransfer->_embedded->errors[0])
			&& $fundTransfer->_embedded->errors[0]->code == 'InsufficientFunds'){
				return $this->sendError([], 'Insufficient Funds Try Again');
			}

			$from = Auth::user()->id;
			$to = Auth::user()->id;

			// if($payment_type == Transaction::INSTANT){
			// 	$PlatformFee = Configuration::where('config_key', 'instant_platform_fee')->first(); 
			// } else {
			// 	$PlatformFee = Configuration::where('config_key', 'platform_fee')->first(); 
			// }

			
			$transactionModel = new Transaction();
			$transactionModel->transaction_id = '';
			$transactionModel->amount = $request->amount;
			$transactionModel->wallet_type = Transaction::TYPE_MY_WALLET_DEPOSIT;
			$transactionModel->from_user_id = $from;
			$transactionModel->fee = 0;
			$transactionModel->to_user_id = $to;
			$transactionModel->payment_type = $payment_type;
			$transactionModel->uuid = $request->correlationId;
			if($transactionModel->save()){

				$fromUser = Auth::user()->id;
				$toUser = Auth::user()->id;
				$message = "Deposit of $ $request->amount has been successfully credited to your wallet.";
				//admin has no customer ID, so finding transaction id using to user id
				dispatch(new ProcessAdminTransaction($access_token, $fromUser, $toUser, Transaction::TYPE_MY_WALLET_DEPOSIT, Transaction::MANUAL, Transaction::STATUS_PENDING, $cashback=0, $type=1, $request->amount, $request->correlationId, $admin_platform_fee=0));

				// _Notification($from, $to, $message, Notification::DEPOSIT);
				// if($request->payment_type == 'instant'){
				// 	dispatch(new ProcessAchAmount($access_token, $to, $from, $payment_type));
				// }

				return $this->sendResponse([], 'Fund Transfer Successfully.'); 
			}
			

		} catch (\Exception $ex) {
			return $this->sendError([], $ex->getMessage());
		}
		
	}

	public function transactions()
	{            
		$walletModel = Wallet::where('user_id', Auth::user()->id)->first();
		return view('admin.bank.transactions', compact('walletModel'));
	}

	public function bankTransactions(Request $request)
	{

		$transactions = $this->transactionService;
		if ($request->has('search') && !empty($request->get('search'))) {
			$searchValue = '%' . $request->get('search')['value'] . '%';
		}
		
		$transactions = $transactions->with('sender','receiver')->where('wallet_type', Transaction::TYPE_WALLET_TO_WALLET)
		->where( function( $q ) {
			$q->where('from_user_id', Auth::user()->id)
			->orWhere('to_user_id', Auth::user()->id);
		})
		->latest();

		return Datatables::of($transactions)->addColumn('action', function ($row) {
			return view('admin.dashboard.table-action')->with(
				[
					'id' => $row->id, 
				]
			);
		})
			->rawColumns(['action'])->make(true);
	
	}

	public function wallet()
	{
		$walletModel = Wallet::where('user_id', Auth::user()->id)->first();
		$DwollaAccount = DwollaAccount::where('user_id', Auth::user()->id)->first();
		$bank_id = '';

		if(!empty($DwollaAccount)){
			$accountData = json_decode($DwollaAccount->json_data);	
			$bank_id = $accountData->bank_id;
		}
            
		return view('admin.bank.wallet', compact('walletModel','bank_id'));
	}

	public function walletTransaction(Request $request)
	{

		$transactions = $this->transactionService;
		if ($request->has('search') && !empty($request->get('search'))) {
			$searchValue = '%' . $request->get('search')['value'] . '%';
		}
		
		$transactions = $transactions->where('wallet_type', '!=', Transaction::TYPE_WALLET_TO_WALLET)
		->where( function( $q ) {
			$q->where('from_user_id', Auth::user()->id)
			->orWhere('to_user_id', Auth::user()->id);
		})
		->latest();

		return Datatables::of($transactions)->addColumn('action', function ($row) {
			return view('admin.dashboard.table-action')->with(
				[
					'id' => $row->id, 
				]
			);
		})
			->rawColumns(['action'])->make(true);
	
	}

	public function withdrawFromWallet(AmountWithdrawRequest $request)
	{
		$userData = User::where('id', Auth::user()->id)->first();

		$hashPin = Hash::make($request->pin);
		$uuid = Str::uuid()->toString();
		$request->correlationId = $uuid;

		if(!Hash::check($request->pin, $userData->pin)){
			return $this->sendError([], 'Please Enter Correct Pin');
		}

		$configuration = Configuration::where('config_key','transaction_limit')->first();
		if(!empty($configuration->config_value) && $request->amount > $configuration->config_value){
			return $this->sendError([], "Single transaction limit is $configuration->config_value");
		}

		try {
			$walletModel = Wallet::where('user_id', Auth::user()->id)->first();
			$access_token_data = $this->dwollaService->traitAccessToken();
			$access_token = $access_token_data;
			$request->source_id = $walletModel->wallet_id;

            $fundTransfer = $this->dwollaService->traitfundTransfer($access_token, $request);
            if(!empty($fundTransfer->code) && $fundTransfer->code == 'ValidationError'
			&& !empty($fundTransfer->_embedded->errors[0])
			&& $fundTransfer->_embedded->errors[0]->code == 'InsufficientFunds'){
                return $this->sendError([], 'Insufficient Funds Try Again');
            }

			$payment_type = Transaction::MANUAL;
			$fromUser = Auth::user()->id;
			$toUser = Auth::user()->id;
			// if($payment_type == Transaction::INSTANT){
			// 	$PlatformFee = Configuration::where('config_key', 'instant_platform_fee')->first(); 
			// } else {
			// 	$PlatformFee = Configuration::where('config_key', 'platform_fee')->first(); 
			// }

			$transactionModel = new Transaction();
			$transactionModel->transaction_id = '';
			$transactionModel->amount = $request->amount;
			$transactionModel->wallet_type = Transaction::TYPE_MY_WALLET_WITHDRAW;
			$transactionModel->from_user_id = Auth::user()->id;
			$transactionModel->to_user_id = Auth::user()->id;
			$transactionModel->fee = 0;
			$transactionModel->payment_type = $payment_type;
			$transactionModel->uuid = $request->correlationId;
			if($transactionModel->save()){

				//admin has no customer ID, so finding transaction id using to user id
				dispatch(new ProcessAdminTransaction($access_token, $fromUser, $toUser, Transaction::TYPE_MY_WALLET_WITHDRAW, Transaction::MANUAL, Transaction::STATUS_PENDING, $cashback=0, $type=1, $request->amount, $request->correlationId, $admin_platform_fee=0));

				$walletModel->balance = $walletModel->balance - $request->amount;
				$walletModel->save();

				$message = "Withdraw of $ $request->amount has been successfully processed from your wallet to your linked account.";

				return $this->sendResponse([], 'Fund Transfer Successfully.');
			}



		} catch (\Exception $ex) {
			return $this->sendError([], $ex->getMessage());
		}
	}

	public function payToUser(UserPaymentRequest $request)
	{

		$userData = User::where('id', Auth::user()->id)->first();
		$toUserData = User::where('uuid', $request->user_id)->first();
		
		if(!Hash::check($request->pin, $userData->pin)){
			return $this->sendError([], 'Please Enter Correct Pin');
		}
		
		if(empty($toUserData)){
			return $this->sendError([], 'Selected user is missing');
		}
		
		$configuration = Configuration::where('config_key','transaction_limit')->first();
		if(!empty($configuration->config_value) && $request->amount > $configuration->config_value){
			return $this->sendError([], "Single transaction limit is $configuration->config_value");
		}
		
		$uuid = Str::uuid()->toString();
		$request->correlationId = $uuid;

		try {
			$walletModel = Wallet::where('user_id', Auth::user()->id)->first();
			$ToUserWalletModel = Wallet::where('user_id', $toUserData->id)->first();
			$access_token_data = $this->dwollaService->traitAccessToken();
			$access_token = $access_token_data;
			$request->source_id = $walletModel->wallet_id;
			$request->destination_id = $ToUserWalletModel->wallet_id;

            $fundTransfer = $this->dwollaService->traitfundTransfer($access_token, $request);
            if(!empty($fundTransfer->code) && $fundTransfer->code == 'ValidationError'
			&& !empty($fundTransfer->_embedded->errors[0])
			&& $fundTransfer->_embedded->errors[0]->code == 'InsufficientFunds'){
                return $this->sendError([], 'Insufficient Funds Try Again');
            }

			$fromUser = Auth::user()->id;
			$toUser = $toUserData->id;
			
           
			$transactionModel = new Transaction();
			$transactionModel->transaction_id = '';
			$transactionModel->amount = $request->amount;
			$transactionModel->wallet_type = Transaction::TYPE_WALLET_TO_WALLET;
			$transactionModel->from_user_id = Auth::user()->id;
			$transactionModel->to_user_id = $toUserData->id;
			$transactionModel->fee = 0;
			$transactionModel->status = Transaction::STATUS_COMPLETED;
			$transactionModel->uuid = $request->correlationId;
			if($transactionModel->save()){

				//admin has no customer ID, so finding transaction id using to user id
				dispatch(new ProcessTransactionData($access_token, $fromUser, $toUser, Transaction::TYPE_WALLET_TO_WALLET, Transaction::MANUAL, Transaction::STATUS_COMPLETED, $cashback=0, $type=1, $request->amount, $request->correlationId, $admin_platform_fee=0));

				$walletModel->balance = $walletModel->balance - $request->amount;
				$walletModel->save();

				// $message = "An amount of $".$request->amount." credited to your wallet.";

				// _Notification($from, $to, $message, Notification::WITHDRAW);


				return $this->sendResponse([], 'Fund Transfer Successfully.');
			}

		} catch (\Exception $ex) {
			return $this->sendError([], $ex->getMessage());
		}

	}



}

