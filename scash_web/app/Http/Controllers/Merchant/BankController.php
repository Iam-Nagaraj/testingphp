<?php

namespace App\Http\Controllers\Merchant;

use App\Http\Controllers\Controller;
use App\Http\Requests\AmountRequest;
use App\Http\Requests\AmountWithdrawRequest;
use App\Http\Requests\UserPaymentRequest;
use App\Jobs\ProcessAchAmount;
use App\Jobs\ProcessLatestTransaction;
use App\Jobs\ProcessTransactionData;
use App\Models\Configuration;
use App\Models\DwollaAccount;
use App\Models\DwollaCustomer;
use App\Models\Notification;
use App\Models\PlaidAccount;
use App\Models\Transaction;
use App\Models\User;
use App\Models\Wallet;
use App\Traits\PlaidTrait;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
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

		return view('merchant.bank.index', compact('link_token','account_list'));

	}

	public function getBankList()
	{
		
		$bankList = [];
		$customerModel = DwollaCustomer::where('user_id', Auth::user()->id)->first();
		if(!empty($customerModel)){
			$dwolla_access_token = $this->dwollaService->traitAccessToken();
			$bankListData = $this->dwollaService->traitBankList($dwolla_access_token, $customerModel->customer_id);
			if(!empty($bankListData) && !empty($bankListData->_embedded)){
				$bankListData;
				$DwollaAccount = DwollaAccount::where('user_id', Auth::user()->id)->first();
				$i = 0;
				
				foreach($bankListData->_embedded->{"funding-sources"} as $singleBank){
					if($singleBank->removed == false){

						$bankData = [
							'bank_id' => $singleBank->id,
							'bank_name' => $singleBank->name,
							'type' => $singleBank->type,
							'status' => $singleBank->status,
							'bankAccountType' => $singleBank->bankAccountType??'',
							'added' => date('Y/m/d', strtotime($singleBank->created)),
						];
						$bankList[$i] = $bankData;
						if($singleBank->type != 'balance'){
							DwollaAccount::updateOrCreate(
								[
									'user_id' => Auth::user()->id,
									'default_account' => $singleBank->id,
								],
								[
									'user_id' => Auth::user()->id,
									'json_data' => json_encode($bankData),
									'default_account' => $singleBank->id,
								]
							);
						}

					}
					$i++;
				}
				

				return $this->sendResponse($bankList, 'Bank Account List.');

			}
		}
		return $this->sendError([], 'Token Expired Try Again');

	}

	public function getWebAccessToken(Request $request)
	{

		$data = [];
		$institution_name = "";
		$access_token_data = $this->accessTokenGet($request->publicToken);
		if(!empty($access_token_data) && !empty($access_token_data->access_token)){
			$account_list = $this->getAccount($access_token_data->access_token);
			if(!empty($account_list) && !empty($account_list->accounts)){
				$data['access_token'] = $access_token_data->access_token;
				$accountList = $account_list->accounts;
				$item_data = $this->itemGet($access_token_data->access_token);
				if(!empty($item_data) && !empty($item_data->item) && !empty($item_data->item->institution_id)){
					$institution_id = $item_data->item->institution_id;
					$institute_data = $this->institutionsGet($institution_id);
					if(!empty($institute_data) && !empty($institute_data->institution) && !empty($institute_data->institution->name)){
						$institution_name = $institute_data->institution->name;
					}
				}
				$abc = $this->updatePlaidAccount($accountList, $access_token_data->access_token, $institution_name);

				foreach($accountList as $singleAccount){
					$data[] = [
						'account_id' => $singleAccount->account_id,
						'mask' => $singleAccount->mask,
						'name' => $singleAccount->name,
						"user_account_name" => $institution_name.' '.$singleAccount->subtype.' Account '.$singleAccount->mask
					];
				}
				return $this->sendResponse($data, 'Accounts Fetch Successfully.');
			}
		}
		return $this->sendError([], 'Token Expired Try Again');

	}

	protected function updatePlaidAccount($accountList, $access_token_data, $institution_name)
	{
		
		$json_data = '';
		$newarray = [];
		$PlaidAccountModel = PlaidAccount::where('user_id', Auth::user()->id)->first();

		if(!empty($PlaidAccountModel) && !empty($PlaidAccountModel->json_data))
		{
			$a = $PlaidAccountModel->json_data;

			foreach($accountList as $single){
				$newarray[] = array_merge((array)$single, [
					'access_token' => $access_token_data, 
					'is_connected' => false, 
					'institution_name' => $institution_name,
					'user_account_name' => $institution_name.' '.$single->subtype.' Account '.$single->mask 
				]);
			}
			
			$json_data = json_encode(
				array_merge(
					json_decode($a, true),
					$newarray
				)
			);

		} else {
			foreach($accountList as $single){
				$newarray[] = array_merge((array)$single, [
					'access_token' => $access_token_data, 
					'is_connected' => false, 
					'institution_name' => $institution_name,
					'user_account_name' => $institution_name.' '.$single->subtype.' Account '.$single->mask 
				]);
			}

			$json_data = json_encode($newarray);
		}
		$PlaidAccount = PlaidAccount::updateOrCreate(
			['user_id' => Auth::user()->id],
			[
				'user_id' => Auth::user()->id,
				'json_data' => $json_data,
				]
			);
			
		return $PlaidAccount;

	}

	public function depositToWallet(AmountRequest $request)
	{
		$userData = User::where('id', Auth::user()->id)->first();

		$hashPin = Hash::make($request->pin);

		if(!Hash::check($request->pin, $userData->pin)){
			return $this->sendError([], 'Please Enter Correct Pin');
		}

		$configuration = Configuration::where('config_key','transaction_limit')->first();
		if(!empty($configuration->config_value) && $request->amount > $configuration->config_value){
			return $this->sendError([], "Single transaction limit is $configuration->config_value");
		}

		$uuid = Str::uuid()->toString();
		$request->correlationId = $uuid;

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
			$fromUser = Auth::user()->id;
			$toUser = Auth::user()->id;

			$message = "Deposit of $ $request->amount has been successfully done.";

			$from = Auth::user()->id;
			$to = Auth::user()->id;

			if($payment_type == Transaction::INSTANT){
				$PlatformFee = Configuration::where('config_key', 'instant_platform_fee')->first(); 
			} else {
				$PlatformFee = Configuration::where('config_key', 'platform_fee')->first(); 
			}
			$dwollaAccount = DwollaAccount::where('default_account', $request->account_id)->first();

			$transactionModel = new Transaction();
			$transactionModel->transaction_id = '';
			$transactionModel->amount = $request->amount;
			$transactionModel->wallet_type = Transaction::TYPE_MY_WALLET_DEPOSIT;
			$transactionModel->from_user_id = $from;
			$transactionModel->fee = $PlatformFee->config_value;
			$transactionModel->to_user_id = $to;
			$transactionModel->payment_type = $payment_type;
			$transactionModel->uuid = $request->correlationId;
			$transactionModel->account_id = $dwollaAccount->id??0;
			if($transactionModel->save()){

				dispatch(new ProcessLatestTransaction($access_token, $toUser, $fromUser, Transaction::TYPE_MY_WALLET_WITHDRAW, $message, Notification::WITHDRAW, Transaction::MANUAL, Transaction::STATUS_PENDING, 0, $request->correlationId, $request->destination_id));

				if($request->payment_type == 'instant'){
					dispatch(new ProcessAchAmount($access_token, $to, $from, $payment_type));
				}

				return $this->sendResponse([], 'Fund Transfer Successfully.'); 
			}

		} catch (\Exception $ex) {
			Log::info($ex->getMessage());
			return $this->sendError([], 'Server Error');
		}
		
	}

	public function wallet()
	{
		$walletModel = Wallet::where('user_id', Auth::user()->id)->first();
            
		return view('merchant.bank.wallet', compact('walletModel'));
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
			return view('merchant.dashboard.table-action')->with(
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

		if(!Hash::check($request->pin, $userData->pin)){
			return $this->sendError([], 'Please Enter Correct Pin');
		}

		$configuration = Configuration::where('config_key','transaction_limit')->first();
		if(!empty($configuration->config_value) && $request->amount > $configuration->config_value){
			return $this->sendError([], "Single transaction limit is $configuration->config_value");
		}

		$uuid = Str::uuid()->toString();
		$request->correlationId = $uuid;


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

			$toUser = Auth::user()->id;
			$fromUser = Auth::user()->id;
			$message = "Withdraw of $ $request->amount has been successfully done.";
			$payment_type = Transaction::MANUAL;

			$from = Auth::user()->id;
			$to = Auth::user()->id;
			if($payment_type == Transaction::INSTANT){
				$PlatformFee = Configuration::where('config_key', 'instant_platform_fee')->first(); 
			} else {
				$PlatformFee = Configuration::where('config_key', 'platform_fee')->first(); 
			}

			$dwollaAccount = DwollaAccount::where('default_account', $request->destination_id)->first();

			$transactionModel = new Transaction();
			$transactionModel->transaction_id = '';
			$transactionModel->amount = $request->amount;
			$transactionModel->wallet_type = Transaction::TYPE_MY_WALLET_WITHDRAW;
			$transactionModel->from_user_id = Auth::user()->id;
			$transactionModel->to_user_id = $request->to_user_id;
			$transactionModel->fee = $PlatformFee->config_value;
			$transactionModel->uuid = $request->correlationId;
			$transactionModel->account_id = $dwollaAccount->id??0;
			if($transactionModel->save()){

				dispatch(new ProcessLatestTransaction($access_token, $toUser, $fromUser, Transaction::TYPE_MY_WALLET_DEPOSIT, $message, Notification::DEPOSIT, $payment_type, Transaction::STATUS_PENDING, 0, $request->correlationId, $request->source_id));

				$walletModel->balance = $walletModel->balance - $request->amount;
				$walletModel->save();

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
		$fullDayTransactionLimit = Configuration::where('config_key','full_day_transaction_limit')->first();
		$todayTransaction = Transaction::whereDate('created_at', date("Y-m-d"))
		->where( function( $q ) {
			$q->where('from_user_id', Auth::user()->id)
			->orWhere('to_user_id', Auth::user()->id);
		})
		->sum('amount');

		if(!empty($fullDayTransactionLimit->config_value) && $todayTransaction >= $fullDayTransactionLimit->config_value){
			return $this->sendError([], "Full day transaction limit is $fullDayTransactionLimit->config_value");
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

			$from = Auth::user()->id;
			$to = $toUserData->id;
			$PlatformFee = Configuration::where('config_key', 'platform_fee')->first(); 

			$transactionModel = new Transaction();
			$transactionModel->transaction_id = '';
			$transactionModel->amount = $request->amount;
			$transactionModel->wallet_type = Transaction::TYPE_WALLET_TO_WALLET;
			$transactionModel->from_user_id = Auth::user()->id;
			$transactionModel->to_user_id = $toUserData->id;
			$transactionModel->fee = $PlatformFee->config_value;
			$transactionModel->status = Transaction::STATUS_COMPLETED;
			$transactionModel->uuid = $request->correlationId;
			if($transactionModel->save()){

				dispatch(new ProcessTransactionData($access_token, $toUser, $fromUser, Transaction::TYPE_WALLET_TO_WALLET, Transaction::MANUAL, Transaction::STATUS_COMPLETED, $cashback=0, $type=1, $request->amount, $request->correlationId, $admin_platform_fee=0));

				$walletModel->balance = $walletModel->balance - $request->amount;
				$walletModel->save();

				$message = "An amount of $".$request->amount." credited to your wallet.";

				_Notification($from, $to, $message, Notification::WITHDRAW);


				return $this->sendResponse([], 'Fund Transfer Successfully.');
			}
		


		} catch (\Exception $ex) {
			return $this->sendError([], $ex->getMessage());
		}
	}



}

