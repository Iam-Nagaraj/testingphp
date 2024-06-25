<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\accessTokenRequest;
use App\Http\Requests\addBankRequest;
use App\Http\Requests\BalanceRequest;
use App\Http\Requests\CashbackCalculationRequest;
use App\Http\Requests\cashbackToWalletRequest;
use App\Http\Requests\FundTransferRequest;
use App\Http\Requests\MicroDepositsRequest;
use App\Http\Requests\QrCodeRequest;
use App\Http\Requests\RemoveFundTransferRequest;
use App\Http\Requests\WalletFundTransferRequest;
use App\Jobs\ProcessAchAmount;
use App\Jobs\ProcessCashback;
use App\Jobs\ProcessLatestTransaction;
use App\Jobs\ProcessPlatformFee;
use App\Jobs\ProcessTaxAmount;
use App\Jobs\ProcessTransactionData;
use App\Models\BusinessCategory;
use App\Models\BusinessDetail;
use App\Models\Cashback;
use App\Models\CashbackRule;
use App\Models\Configuration as ModelsConfiguration;
use App\Models\DeviceToken;
use App\Models\DwollaAccount;
use App\Models\DwollaCustomer;
use App\Models\MerchantStore;
use App\Models\Notification;
use App\Models\Tax;
use App\Models\Transaction;
use App\Models\User;
use App\Models\Wallet;
use App\Traits\DwollaTrait;
use App\Traits\FcmTrait;
use DwollaSwagger\Configuration;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class BankController extends Controller
{
    use DwollaTrait, FcmTrait;

	private $client_id;
	private $secret;
	private $dwolla_url;
	private $apiClient;

	public function __construct()
	{
		$this->client_id = config('services.dwolla.client_id');
		$this->secret = config('services.dwolla.secret');
		$this->dwolla_url = config('services.dwolla.url');

		Configuration::$username = $this->client_id;
		Configuration::$password = $this->secret;

		$this->apiClient = new \DwollaSwagger\ApiClient($this->dwolla_url);
	}


	/**
     * @OA\POST(
     ** path="/api/v1/auth/add-bank",
     *   tags={"Bank"},
     *   summary="Only for test",
     *   operationId="add-bank",
     *   @OA\Response(
     *      response=201,
     *       description="Success",
     *      @OA\MediaType(
     *           mediaType="application/json",
     *      )
     *   ),
     *   @OA\Response(
     *      response=401,
     *       description="Unauthenticated"
     *   ),
     *   @OA\Response(
     *      response=400,
     *      description="Bad Request"
     *   ),
     *   @OA\Response(
     *      response=404,
     *      description="not found"
     *   ),
     *      @OA\Response(
     *          response=403,
     *          description="Forbidden"
     *      ),
     * security={{"bearer":{}}}
     *)
    **/
	public function addBank(addBankRequest $request)
	{
        try {
            $userModel = User::where('id', Auth::user()->id)->with('address')->first();
            $DwollaCustomer = DwollaCustomer::where('user_id', Auth::user()->id)->first();
            $customer_id = '';
            if(empty($DwollaCustomer)){
                if(
                    empty($userModel->first_name) || 
                    empty($userModel->last_name) || 
                    empty($userModel->date_of_birth) ||
                    empty($userModel->address->address) ||
                    empty($userModel->address->city)
                ) {
                    return $this->sendError([], 'Your details not fill, so bank account will not be created');
                }
                $customer_id = $this->createCustomers($request->access_token, $userModel, $request);
                if(!empty($customer_id->code) && $customer_id->code == 'ExpiredAccessToken'){
                    return $this->sendError([], 'Token Expired Try Again');
                }
                if(!empty($customer_id)){
                    $DwollaCustomer = new DwollaCustomer();
                    $DwollaCustomer->user_id = $userModel->id;
                    $DwollaCustomer->customer_id = $customer_id;
                    $DwollaCustomer->save();
                } else {
                    return $this->sendError([], 'Token Expired Try Again');
                }
            } else {
                $customerID = $DwollaCustomer->customer_id;
                $data = $this->createBankSource($request->access_token, $customerID, $request);
                if(!empty($data->code) && $data->code == 'ExpiredAccessToken'){
                    return $this->sendError([], 'Token Expired Try Again');
                }
            }

            return $this->sendResponse([], 'Bank created successfully.');


		} catch (\Exception $ex) {
			return $this->sendError([], $ex->getMessage());
		}
	}

    public function addCustomer(accessTokenRequest $request)
	{
        try {
            $userModel = User::where('id', Auth::user()->id)->with('address')->first();
            $DwollaCustomer = DwollaCustomer::where('user_id', Auth::user()->id)->first();
            $customer_id = '';
            if(empty($DwollaCustomer)){
                if(
                    empty($userModel->first_name) || 
                    empty($userModel->last_name) || 
                    empty($userModel->date_of_birth) ||
                    empty($userModel->address->address) ||
                    empty($userModel->address->city)
                ) {
                    return $this->sendError([], 'Your details not fill, so customer will not be created');
                }
                $customer_id = $this->createCustomers($request->access_token, $userModel, $request);
                if(!empty($customer_id->code) && $customer_id->code == 'ExpiredAccessToken'){
                    return $this->sendError([], 'Token Expired Try Again');
                }
                if(!empty($customer_id)){
                    $DwollaCustomer = new DwollaCustomer();
                    $DwollaCustomer->user_id = $userModel->id;
                    $DwollaCustomer->customer_id = $customer_id;
                    if($DwollaCustomer->save()){
                        return $this->sendResponse($DwollaCustomer, 'Customer created successfully.');
                    }
                } else {
                    return $this->sendError([], 'Token Expired Try Again');
                }
            }

            return $this->sendResponse($DwollaCustomer, 'Customer created successfully.');

		} catch (\Exception $ex) {
			return $this->sendError([], $ex->getMessage());
		}
	}

    /**
     * @OA\POST(
     ** path="/api/v1/auth/bank-list",
     *   tags={"Bank"},
     *   summary="Only for test",
     *   operationId="bank-list",
     *   @OA\Response(
     *      response=201,
     *       description="Success",
     *      @OA\MediaType(
     *           mediaType="application/json",
     *      )
     *   ),
     *   @OA\Response(
     *      response=401,
     *       description="Unauthenticated"
     *   ),
     *   @OA\Response(
     *      response=400,
     *      description="Bad Request"
     *   ),
     *   @OA\Response(
     *      response=404,
     *      description="not found"
     *   ),
     *      @OA\Response(
     *          response=403,
     *          description="Forbidden"
     *      ),
     * security={{"bearer":{}}}
     *)
    **/
	public function getBankList()
	{
        try {
            $current_user_id = Auth::user()->id;
            $walletModel = Wallet::where('user_id', $current_user_id)->first();
            $userModel = User::where('id', $current_user_id)->with('address')->first();
            $DwollaCustomer = DwollaCustomer::where('user_id', $current_user_id)->first();
            if(empty($DwollaCustomer)){
                return $this->sendResponse([], 'Bank List.');
            }
            $access_token_data = $this->createAccessToken();
            $access_token = $access_token_data['access_token'];
            $bankList = $this->BankList($access_token, $DwollaCustomer->customer_id);
            if(!empty($bankList->code) && $bankList->code == 'ExpiredAccessToken'){
                return $this->sendError([], 'Token Expired Try Again');
            }

            $notificationCount = Notification::where('to', $current_user_id)->where('is_read', 0)->count();
            $DwollaAccount = DwollaAccount::where('user_id', $current_user_id)->where('is_default', 1)->first();

            $i = 0;
				
            foreach($bankList->_embedded->{"funding-sources"} as $singleBank){
                if($singleBank->removed == false){
                    $bankData = [
                        'bank_id' => $singleBank->id,
                        'bank_name' => $singleBank->name,
                        'type' => $singleBank->type,
                        'status' => $singleBank->status,
                        'bankAccountType' => $singleBank->bankAccountType??'',
                        'added' => date('Y/m/d', strtotime($singleBank->created)),
                    ];
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

            $custom = collect([
                'wallet_balance' => $walletModel->balance, 
                'wallet_id' => $walletModel->wallet_id,
                'cashback_balance' => $walletModel->cashback_balance,
                'cashback_earned' => $walletModel->cashback_earned,
                'notification_count' => $notificationCount,
                'default_account' => $DwollaAccount->default_account??'',
            ]);
            $bankList = $custom->merge($bankList);
            
            return $this->sendResponse($bankList, 'Bank List.');


		} catch (\Exception $ex) {
			return $this->sendError([], $ex->getMessage());
		}
	}

    /**
     * @OA\POST(
     ** path="/api/v1/auth/micro-deposits",
     *   tags={"Bank"},
     *   summary="Only for test",
     *   operationId="micro-deposits",
     *   @OA\Response(
     *      response=201,
     *       description="Success",
     *      @OA\MediaType(
     *           mediaType="application/json",
     *      )
     *   ),
     *   @OA\Response(
     *      response=401,
     *       description="Unauthenticated"
     *   ),
     *   @OA\Response(
     *      response=400,
     *      description="Bad Request"
     *   ),
     *   @OA\Response(
     *      response=404,
     *      description="not found"
     *   ),
     *      @OA\Response(
     *          response=403,
     *          description="Forbidden"
     *      ),
     * security={{"bearer":{}}}
     *)
    **/
	public function verifyMicroDeposits(MicroDepositsRequest $request)
	{
        try {

            $microDeposits = $this->microDeposits($request->access_token, $request);
            if(!empty($microDeposits->code) && $microDeposits->code == 'ExpiredAccessToken'){
                return $this->sendError([], 'Token Expired Try Again');
            }

            return $this->sendResponse([], 'Verification Send Successfully.');


		} catch (\Exception $ex) {
			return $this->sendError([], $ex->getMessage());
		}
	}

    public function makeFundTransfer(FundTransferRequest $request)
	{
        \Log::info($request->all());

        $tax = 0;
        $tax_percentage = 1;
        $admin_platform_fee = 0;
        $request_cashback = 0;
        
        $destinationWallet = Wallet::where('wallet_id', $request->destination_id)->first();
        $sourceWallet = Wallet::where('wallet_id', $request->source_id)->first();

        if(empty($destinationWallet) || !is_string($request->destination_id)){
			return $this->sendError([], "Destination Data Missing", 500);
        }

        if(empty($sourceWallet) || !is_string($request->source_id)){
			return $this->sendError([], "Source Data Missing", 500);
        }

        $configuration = ModelsConfiguration::where('config_key','transaction_limit')->first();
        $deviceTokenModel = DeviceToken::where('user_id', $request->to_user_id)->first();
		if(!empty($configuration->config_value) && $request->amount > $configuration->config_value){
			return $this->sendError([], "$$configuration->config_value allowed per transaction for fraud protections");
		}
        $fullDayTransactionLimit = ModelsConfiguration::where('config_key','full_day_transaction_limit')->first();
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

            $access_token_data = $this->createAccessToken();
            $access_token = $access_token_data['access_token'];
            $fromUser = Auth::user()->id;
            $toUser = $request->to_user_id;
            $toUserData = User::where('id', $toUser)->first();
            if(isset($request->cashback) && $request->cashback > 0){
                $request_cashback = $request->cashback;
            }

            $amount = $request->amount;

            $FromUserWallet = Wallet::where('user_id' , Auth::user()->id)->first();
            if($FromUserWallet->balance < $request->amount){
                return $this->sendError([], 'Insufficient Funds In Wallet Try Again');
            }
            if($request->cashback && $FromUserWallet->cashback_balance < $request->cashback){
                return $this->sendError([], 'Insufficient Funds In Cashback Try Again');
            }
            $cashback = 0;
            $tip = $request->tip??0;
            

            $standard_cashback = 0;
            $ts_extra_cashback = 0;
            $rp_extra_cashback = 0;

            // ------Start Cashback Calculation----------
            if($toUserData->role_id ==  getConfigConstant('MERCHANT_ROLE_ID')){
                $platformFeeData = ModelsConfiguration::where('config_key', 'platform_fee')->first();
                $platform_fee_percentage = $platformFeeData->config_value;
                $amount = $request->amount + $request_cashback;
                $request->amount = $request->amount + $request_cashback; //add request_cashback for cashback calculation
                
                $tax_percentage = $this->getTaxPercentage($toUser);
                $tax = round(($request->amount * $tax_percentage) / 100,2);

                $cashbackRule = CashbackRule::where('user_id', $toUser)->first();
                
                $business_category= BusinessCategory::select('id')
                ->where('id', $toUserData->BusinessDetail->business_category)->first();
                $cashback_business_type    = Cashback::where('business_category_id',$business_category->id ?? '')->first();
                $cashback_percentage = ($toUserData->role_id ==  getConfigConstant('MERCHANT_ROLE_ID')) ? $cashback_business_type->percentage ?? 0 : 0;
                
                $standard_cashback = round(($amount * $cashback_percentage)/100,2);
                if(isset($cashbackRule)){
                    
                    $cashback_percentage = $cashbackRule->standard_cashback_percentage + $cashback_percentage;  // added admin standard cashback
                    $standard_cashback = round(($amount * $cashback_percentage)/100,2);

                    if( ($amount >= $cashbackRule->ts_total_amount) && ($cashbackRule->ts_status == CashbackRule::RULEACTIVE)){ // for increase transactional amount
                        $cashback_percentage = $cashback_percentage + $cashbackRule->ts_extra_percentage;
                        $ts_extra_cashback = round(($amount * $cashbackRule->ts_extra_percentage)/100,2);
                    }

                    $FromUserWallet->rp_cashback_balance = $FromUserWallet->rp_cashback_balance + $amount;
                    if( ($FromUserWallet->rp_cashback_balance >= $cashbackRule->rp_total_amount) && ($cashbackRule->rp_status == CashbackRule::RULEACTIVE)){ // for repeat transactional amount
                        $cashback_percentage = $cashback_percentage +  $cashbackRule->rp_extra_percentage;
                        $FromUserWallet->rp_cashback_balance = $FromUserWallet->rp_cashback_balance - $cashbackRule->rp_total_amount;
                        $rp_extra_cashback = round(($amount * $cashbackRule->rp_extra_percentage)/100,2);
                    }
                }

                $cashback = round(($amount * $cashback_percentage)/100,2); //merchant cashback
                $admin_platform_fee = round(($cashback * $platform_fee_percentage)/100,2); //platform fee from merchant cashback
                $cashback = $cashback - $admin_platform_fee;
                $request->amount = $amount + $tip;
                $amount = $request->amount - $request_cashback;
                $request->amount = $request->amount - $request_cashback; // remove request_cashback for sending separatetly
                $cashback = round($cashback ,2);

            }
            // ------End Cashback Calculation----------

            $PlatformFee = ModelsConfiguration::where('config_key', 'platform_fee')->first(); 
            $platform_fees = $PlatformFee->config_value;


            $message = "An amount of $".$request->amount + $request_cashback." credited to your wallet.";
            
            if(isset($request->amount) && $request->amount > 0){
                $uuid = Str::uuid()->toString();
		        $request->correlationId = $uuid;

                $fundTransfer = $this->fundTransfer($access_token, $request); // dwolla transer money trait
                $type = 1; //wallet

                $this->saveTransactionData($access_token, $toUser, $fromUser, Transaction::TYPE_WALLET_TO_WALLET,Transaction::MANUAL, Transaction::STATUS_COMPLETED, $cashback, $type=1, $request->amount, $request->correlationId, $admin_platform_fee);

                dispatch(new ProcessTransactionData($access_token, $toUser, $fromUser, Transaction::TYPE_WALLET_TO_WALLET,Transaction::MANUAL, Transaction::STATUS_COMPLETED, $cashback, $type=1, $request->amount, $request->correlationId, $admin_platform_fee));

                if(!empty($fundTransfer->code) && $fundTransfer->code == 'Forbidden'){
                    return $this->sendError([], $fundTransfer->message);
                }

                if(!empty($fundTransfer->code) && $fundTransfer->code == 'ValidationError'
                && !empty($fundTransfer->_embedded->errors[0])
                && $fundTransfer->_embedded->errors[0]->code == 'InsufficientFunds'){
                    return $this->sendError([], 'Insufficient Funds Try Again');
                }
            }

            if(isset($request->cashback) && $request->cashback > 0){
                $uuid = Str::uuid()->toString();
		        $request->correlationId = $uuid;

                $transer_data['source_id'] = $request->source_id;
                $transer_data['destination_id'] = $request->destination_id;
                $transer_data['amount'] = $request->cashback;
                $transer_data['correlationId'] = $request->correlationId;
                $transer_data = (object) $transer_data;
                $type = 2; //cashback
            
                $fundTransfer = $this->fundTransfer($access_token, $transer_data); // dwolla transer money trait

                $this->saveTransactionData($access_token, $toUser, $fromUser, Transaction::TYPE_WALLET_TO_WALLET,Transaction::MANUAL, Transaction::STATUS_COMPLETED, 0, $type=2, $transer_data->amount, $request->correlationId, 0);

                dispatch(new ProcessTransactionData($access_token, $toUser, $fromUser, Transaction::TYPE_WALLET_TO_WALLET,Transaction::MANUAL, Transaction::STATUS_COMPLETED, 0, $type=2, $transer_data->amount, $request->correlationId, $admin_platform_fee));
                if(!empty($fundTransfer->code) && $fundTransfer->code == 'Forbidden'){
                    return $this->sendError([], $fundTransfer->message);
                }

                if(!empty($fundTransfer->code) && $fundTransfer->code == 'ValidationError'
                && !empty($fundTransfer->_embedded->errors[0])
                && $fundTransfer->_embedded->errors[0]->code == 'InsufficientFunds'){
                    return $this->sendError([], 'Insufficient Funds Try Again');
                }
            }

            $notification = new Notification();
            $notification->from = $fromUser;
            $notification->to = $toUser;
            $notification->message = $message;
            $notification->type = Notification::WALLETTOWALLET;
            $notification->save();

            
            $FromUserWallet->balance = $FromUserWallet->balance - $request->amount - $cashback; // update sender wallet
            // update sender cashback => if merchant give any cashback 
            // update => amount payed from cashback balance
            $FromUserWallet->cashback_balance = $FromUserWallet->cashback_balance + $cashback - $request_cashback; 
            $FromUserWallet->cashback_earned  = $FromUserWallet->cashback_earned + $cashback; 
            $FromUserWallet->save();   

            $ToUserWallet = Wallet::where('user_id' , $request->to_user_id)->first();
            $ToUserWallet->balance = $ToUserWallet->balance + $request->amount + $request_cashback; // update receiver user wallet
            $ToUserWallet->save();

            if($toUserData->role_id == User::ROLE_STORE)
            {
                $MerchantStore = MerchantStore::where('user_id', $toUser)->first();
                if($MerchantStore && $MerchantStore->merchant_id){
                    $storeMerchantWallet = Wallet::where('user_id' , $MerchantStore->merchant_id)->first();    
                    if($storeMerchantWallet && $storeMerchantWallet->user_id){
                        $storeMerchantWallet->balance = $ToUserWallet->balance + $request->amount + $request_cashback; // update receiver user wallet
                        $storeMerchantWallet->save();
                    }
                }
            }


            if(!empty($deviceTokenModel) && !empty($deviceTokenModel->token)){
                $device_token = $deviceTokenModel->token;
                $title = 'Scash';
                $sendData = ['amount' => $request->amount];
        
                $this->sendPushNotification($device_token, $title, $message, $sendData);

            }

            $uuid = Str::uuid()->toString();
            $request->correlationId = $uuid;

            $cash_back_data = [
                'amount' => $cashback,
                'source_id' => $request->destination_id,
                'destination_id' => $request->source_id,
                'correlationId' => $request->correlationId
            ];

            // Convert associative array to an object
            $cash_back_object = (object) $cash_back_data;


            if($toUserData->role_id ==  getConfigConstant('MERCHANT_ROLE_ID')){
                dispatch(new ProcessCashback($access_token, $toUser, $fromUser, Transaction::CASHBACK, $cash_back_object, Notification::CASHBACK));
            }

            if($toUserData->role_id ==  getConfigConstant('MERCHANT_ROLE_ID')){
                if($admin_platform_fee > 0){
                    dispatch(new ProcessPlatformFee($access_token, $toUser, $fromUser, $admin_platform_fee));
                }
            }

            if($toUserData->role_id ==  getConfigConstant('MERCHANT_ROLE_ID')){
                if($tax > 0){
                    dispatch(new ProcessTaxAmount($access_token, $toUser, $fromUser, $tax));
                }
            }

            $final_transfer = [
                'amount' => $amount + $request_cashback, 
                'tip' => $tip, 
                'standard_cashback' => $standard_cashback,  
                'ts_extra_cashback' => $ts_extra_cashback,  
                'rp_extra_cashback' => $rp_extra_cashback,  
                'cashback' => $cashback,  
                'tax' => $tax, 
                'admin_platform_fee' => $admin_platform_fee
            ];

            return $this->sendResponse($final_transfer, 'Fund Transfer Successfully.');

		} catch (\Exception $ex) {
            \Log::info($ex);
			return $this->sendError([], $ex->getMessage());
		}

	}

    protected function saveTransactionData($token, $toUser, $fromUser, $transaction_type, $payment_type, $transaction_status, $cashback, $type, $amount, $correlationId, $admin_platform_fee)
    {
        $transactionModel = new Transaction(); //save transaction id to database
        $transactionModel->amount = $amount;
        $transactionModel->fee = $admin_platform_fee;
        $transactionModel->wallet_type = $transaction_type; //0=deposit,1=withdraw,2=WalletToWallet
        $transactionModel->from_user_id = $fromUser;
        $transactionModel->to_user_id = $toUser;
        $transactionModel->payment_type = $payment_type; //0=manual,1=instant
        $transactionModel->status = $transaction_status;
        $transactionModel->cashback = $cashback;
        $transactionModel->type = $type; //1=wallet,2=cashback
        $transactionModel->uuid = $correlationId; 

        $transactionModel->save();
    }

    public function calculateCashback(CashbackCalculationRequest $request)
	{
        $tax = 0;
        $tax_percentage = 1;
        $admin_platform_fee = 0;
        $request_cashback = 0;

        try {

            $toUser = $request->to_user_id;
            $toUserData = User::where('id', $toUser)->first();
            if(isset($request->cashback) && $request->cashback > 0){
                $request_cashback = $request->cashback;
            }

            $amount = $request->amount;

            $FromUserWallet = Wallet::where('user_id' , Auth::user()->id)->first();

            $cashback = 0;
            $tip = $request->tip??0;

            $standard_cashback = 0;
            $ts_extra_cashback = 0;
            $rp_extra_cashback = 0;

            // ------Start Cashback Calculation----------
            if($toUserData->role_id ==  getConfigConstant('MERCHANT_ROLE_ID')){
                $platformFeeData = ModelsConfiguration::where('config_key', 'platform_fee')->first();
                $platform_fee_percentage = $platformFeeData->config_value;
                $request->amount = $request->amount + $request_cashback; //add request_cashback for cashback calculation
                $amount = $request->amount + $request_cashback;
                
                $tax_percentage = $this->getTaxPercentage($toUser);
                $tax = round(($request->amount * $tax_percentage) / 100,2);

                $cashbackRule = CashbackRule::where('user_id', $toUser)->first();
                
                $business_category= BusinessCategory::select('id')
                ->where('id', $toUserData->BusinessDetail->business_category)->first();
                $cashback_business_type    = Cashback::where('business_category_id',$business_category->id ?? '')->first();
                $cashback_percentage = ($toUserData->role_id ==  getConfigConstant('MERCHANT_ROLE_ID')) ? $cashback_business_type->percentage ?? 0 : 0;
                
                $standard_cashback = round(($amount * $cashback_percentage)/100,2);
                if(isset($cashbackRule)){
                    
                    $cashback_percentage = $cashbackRule->standard_cashback_percentage + $cashback_percentage;  // added admin standard cashback
                    $standard_cashback = round(($amount * $cashback_percentage)/100,2);

                    if( ($amount >= $cashbackRule->ts_total_amount) && ($cashbackRule->ts_status == CashbackRule::RULEACTIVE)){ // for increase transactional amount
                        $cashback_percentage = $cashback_percentage + $cashbackRule->ts_extra_percentage;
                        $ts_extra_cashback = round(($amount * $cashbackRule->ts_extra_percentage)/100,2);
                    }

                    $FromUserWallet->rp_cashback_balance = $FromUserWallet->rp_cashback_balance + $amount;
                    if( ($FromUserWallet->rp_cashback_balance >= $cashbackRule->rp_total_amount) && ($cashbackRule->rp_status == CashbackRule::RULEACTIVE)){ // for repeat transactional amount
                        $cashback_percentage = $cashback_percentage +  $cashbackRule->rp_extra_percentage;
                        $FromUserWallet->rp_cashback_balance = $FromUserWallet->rp_cashback_balance - $cashbackRule->rp_total_amount;
                        $rp_extra_cashback = round(($amount * $cashbackRule->rp_extra_percentage)/100,2);
                    }
                }

                $cashback = round(($amount * $cashback_percentage)/100,2); //merchant cashback
                $admin_platform_fee = round(($cashback * $platform_fee_percentage)/100,2); //platform fee from merchant cashback
                $cashback = $cashback - $admin_platform_fee;
                $request->amount = $amount + $tip;
                $amount = $request->amount - $request_cashback;
                $request->amount = $request->amount - $request_cashback; // remove request_cashback for sending separatetly
                $cashback = round($cashback ,2);

            }
            // ------End Cashback Calculation----------


            $cashback_data = [
                'amount' => $amount + $request_cashback, 
                'tip' => $tip, 
                'standard_cashback' => $standard_cashback,  
                'ts_extra_cashback' => $ts_extra_cashback,  
                'rp_extra_cashback' => $rp_extra_cashback,  
                'cashback' => $cashback,  
                'tax' => $tax, 
                'admin_platform_fee' => $admin_platform_fee
            ];

            return $this->sendResponse($cashback_data, 'Cashback Data.');

		} catch (\Exception $ex) {
            \Log::info($ex);
			return $this->sendError([], $ex->getMessage());
		}

	}

    public function getTaxPercentage($merchan_id)
	{
		$adminModel = User::where('role_id', getConfigConstant('ADMIN_ROLE_ID'))->first();
		$merchantTax = Tax::where('user_id', $merchan_id)->first();
		$adminTax = Tax::where('user_id', $adminModel->id)->first();
		if(empty($merchantTax)){
			return $adminTax->tax;
		} else {
			if($merchantTax->tax < $adminTax->tax ||  $merchantTax->tax == 0){
				return $adminTax->tax;
			} else {
				return $merchantTax->tax;
			}
		}
	}

    public function withdrawFundTransfer(WalletFundTransferRequest $request)
	{
        $configuration = ModelsConfiguration::where('config_key','transaction_limit')->first();
		if(!empty($configuration->config_value) && $request->amount > $configuration->config_value){
			return $this->sendError([], "$$configuration->config_value allowed per transaction for fraud protections");
		}
        
        $DwollaAccount = DwollaAccount::updateOrCreate(
            [
                'user_id' => Auth::user()->id,
                'default_account' => $request->destination_id
            ],
            [
                'user_id' => Auth::user()->id,
                'default_account' => $request->destination_id,
                'is_default' => 1
            ]
        );

        $uuid = Str::uuid()->toString();
		$request->correlationId = $uuid;

        try {
            $access_token_data = $this->createAccessToken();
            $access_token = $access_token_data['access_token'];
            $fundTransfer = $this->fundTransfer($access_token, $request);
            $fromUser = Auth::user()->id;
            $toUser = $request->to_user_id;

            $FromUserWallet = Wallet::where('user_id' , Auth::user()->id)->first();
            if($FromUserWallet->balance < $request->amount){
                return $this->sendError([], 'Insufficient Funds Try Again');
            }
            
            if(!empty($fundTransfer->code) && $fundTransfer->code == 'ValidationError'
			&& !empty($fundTransfer->_embedded->errors[0])
			&& $fundTransfer->_embedded->errors[0]->code == 'InsufficientFunds'){
                return $this->sendError([], 'Insufficient Funds Try Again');
            }

            $FromUserWallet->balance = $FromUserWallet->balance - $request->amount;
            $FromUserWallet->save();

            $PlatformFee = ModelsConfiguration::where('config_key', 'platform_fee')->first(); 
            $platform_fees = $PlatformFee->config_value;

            $message = "Withdraw of $".$request->amount." has been successfully done.";

            $this->withdrawDepositTransaction($access_token, $toUser, $fromUser, Transaction::TYPE_MY_WALLET_WITHDRAW, $request->amount, $platform_fees, Transaction::MANUAL, Transaction::STATUS_PENDING, 0, $request->correlationId, $request->destination_id);

            dispatch(new ProcessLatestTransaction($access_token, $toUser, $fromUser, Transaction::TYPE_MY_WALLET_WITHDRAW, $message, Notification::WITHDRAW, Transaction::MANUAL, Transaction::STATUS_PENDING, 0, $request->correlationId, $request->destination_id));

            return $this->sendResponse([], 'Fund Transfer Successfully.');

		} catch (\Exception $ex) {
			return $this->sendError([], $ex->getMessage());
		}

	}

    protected function withdrawDepositTransaction($token, $toUser, $fromUser, $transaction_type, $amount, $platform_fees, $payment_type, $transaction_status, $cashback, $correlationId, $account_id)
    {
        $dwollaAccount = DwollaAccount::where('default_account', $account_id)->first();

        $transactionModel = new Transaction(); //save transaction id to database
        $transactionModel->amount = $amount;
        $transactionModel->fee = $platform_fees;
        $transactionModel->wallet_type = $transaction_type; //0=deposit,1=withdraw,2=WalletToWallet
        $transactionModel->from_user_id = $fromUser;
        $transactionModel->to_user_id = $toUser;
        $transactionModel->payment_type = $payment_type; //0=manual,1=instant
        $transactionModel->status = $transaction_status;
        $transactionModel->cashback = $cashback;
        $transactionModel->uuid = $correlationId;
        $transactionModel->account_id = $dwollaAccount->id??0;
        $transactionModel->save();
    }

    public function depositFundTransfer(WalletFundTransferRequest $request)
	{
        $configuration = ModelsConfiguration::where('config_key','transaction_limit')->first();
		if(!empty($configuration->config_value) && $request->amount > $configuration->config_value){
			return $this->sendError([], "$$configuration->config_value allowed per transaction for fraud protections");
		}
        $DwollaAccount = DwollaAccount::updateOrCreate(
            [
                'user_id' => Auth::user()->id,
                'default_account' => $request->source_id
            ],
            [
                'user_id' => Auth::user()->id,
                'default_account' => $request->source_id,
                'is_default' => 1
            ]
        );

        $payment_type = Transaction::MANUAL;

        $uuid = Str::uuid()->toString();
		$request->correlationId = $uuid;

        try {
            $access_token_data = $this->createAccessToken();
            $access_token = $access_token_data['access_token'];
            if($request->payment_type == '1'){
                $fundTransfer = $this->sameDayFundTransfer($access_token, $request);
                $payment_type = Transaction::INSTANT;
            } else {
                $fundTransfer = $this->fundTransfer($access_token, $request);
            }
            $fromUser = Auth::user()->id;
            $toUser = $request->to_user_id;

            if(!empty($fundTransfer->code) && $fundTransfer->code == 'ValidationError'
			&& !empty($fundTransfer->_embedded->errors[0])
			&& $fundTransfer->_embedded->errors[0]->code == 'InsufficientFunds'){
                return $this->sendError([], 'Insufficient Funds Try Again');
            }

            $message = "Deposit of $".$request->amount." has been successfully done.";

            $PlatformFee = ModelsConfiguration::where('config_key', 'platform_fee')->first(); 
            $platform_fees = $PlatformFee->config_value;


            $this->withdrawDepositTransaction($access_token, $toUser, $fromUser, Transaction::TYPE_MY_WALLET_DEPOSIT, $request->amount, $platform_fees, $payment_type, Transaction::STATUS_PENDING, 0, $request->correlationId, $request->source_id);

            dispatch(new ProcessLatestTransaction($access_token, $toUser, $fromUser, Transaction::TYPE_MY_WALLET_DEPOSIT, $message, Notification::DEPOSIT, $payment_type, Transaction::STATUS_PENDING, 0, $request->correlationId, $request->source_id));
            if($request->payment_type == 'instant'){
                dispatch(new ProcessAchAmount($access_token, $toUser, $fromUser, $payment_type));
            }

            return $this->sendResponse([], 'Fund Transfer Successfully.');


		} catch (\Exception $ex) {
			return $this->sendError([], $ex->getMessage());
		}

	}

    public function fundBalanceData(QrCodeRequest $request)
    {
        $userModel = User::where('uuid', $request->user_id)->first();
        if(empty($userModel)){
            return $this->sendError([], 'Data not found', 404);
        }
        $user_id = $userModel->id;

        $DwollaCustomer = DwollaCustomer::where('user_id', $user_id)->first();

        if(empty($DwollaCustomer)){
            return $this->sendError([], 'This user do not have customer ID');
        }

        $data['userData'] = $userModel;
        $walletModel = Wallet::where('user_id', $user_id)->first();
        $data['wallet_id'] = $walletModel->wallet_id;

        $adminModel = User::where('role_id', getConfigConstant('ADMIN_ROLE_ID'))->first();
        $taxModel = Tax::where('user_id', $adminModel->id)->first();
        $data['Tax'] = $taxModel->tax??'0';
        $PlatformFee = ModelsConfiguration::where('config_key', 'platform_fee')->first();
        if($PlatformFee){
            $data['PlatformFee'] = $PlatformFee->config_value??'0';
        } else {
            $data['PlatformFee'] = '0';
        }
        if($userModel->role_id == getConfigConstant('MERCHANT_ROLE_ID')){
            $data['cashback'] = [];
            $businessDetails = BusinessDetail::where('user_id', $user_id)->first();
            if(!empty($businessDetails)){
                $cashbackModel = Cashback::where('business_category_id', $businessDetails->business_category)->first();
                if(!empty($cashbackModel)){
                    $data['cashback'] = $cashbackModel;
                }
            }

        }

        return $this->sendResponse($data, 'User Data Fetch Successfully.');

    }

    function checkStringOrMobile($inputValue)
    {
        if (is_numeric($inputValue)) {
            return ['phone_number' => $inputValue];
        } elseif (filter_var($inputValue, FILTER_VALIDATE_EMAIL)) {
            return ['email' => $inputValue];
        } else {
            return ['string' => $inputValue];
        }
        return false;
    }

    public function individualList(Request $request)
    {

        $userModel = User::where('role_id', getConfigConstant('USER_ROLE_ID'));
        $dataType = $this->checkStringOrMobile($request->search);

        if(!empty($dataType['email'])){
            $userModel->where('email', 'LIKE', '%'.$request->search.'%');
        }

        if(!empty($dataType['string'])){
            $userModel->whereRaw("CONCAT(first_name, ' ', last_name) LIKE '%$request->search%'");
        }

        if(!empty($dataType['phone_number'])){
            $userModel->where('phone_number', 'LIKE', '%'.$request->search.'%');
        }


        $userModel = $userModel->leftJoin('wallets', 'users.id', '=', 'wallets.user_id');
        $userModel = $userModel->select('users.*', 'wallets.wallet_id');
        $userModel = $userModel->paginate(10);
        
        $adminModel = User::where('role_id', getConfigConstant('ADMIN_ROLE_ID'))->first();
        $taxModel = Tax::where('user_id', $adminModel->id)->first();
        $data['userList'] = $userModel;
        $data['Tax'] = $taxModel->tax;

        return $this->sendResponse($data, 'Individual List Fetch Successfully.');

    }

    public function getLatestTransaction()
    {
        $access_token = $this->createAccessToken();

        $data = $this->getLatestTransactionData($access_token['access_token']);
        if(!empty($data['code']) && $data['code'] == 'ExpiredAccessToken'){
            return $this->sendError([], 'Token Expired Try Again');
        }
        return $this->sendResponse($data, 'Latest Transaction Fetch Successfully.');

    }

    protected function getLatestTransactionData($access_token)
    {
        $DwollaCustomer = DwollaCustomer::where('user_id', Auth::user()->id)->first();
        $customerID = $DwollaCustomer->customer_id;
        $TransferList = $this->transferList($access_token, $customerID);
        if(!empty($TransferList->code) && $TransferList->code == 'ExpiredAccessToken'){
            return  ['code' => 'ExpiredAccessToken' ];
        }
        if(!empty($TransferList->_embedded) && !empty($TransferList->_embedded->transfers[0])){
            $lastestTransfer = $TransferList->_embedded->transfers[0];
            $transferData['transaction_id'] = $lastestTransfer->id;
            $transferData['status'] = $lastestTransfer->status;
            $transferData['amount'] = $lastestTransfer->amount->value;
            $transferData['created'] = $lastestTransfer->created;
            return $transferData;
        } else {
            return null;
        }
    }

    public function getTransferList(Request $request)
	{

        try {
            $walletModel = Wallet::where('user_id', Auth::user()->id)->first();
            $transactionModel = Transaction::where('wallet_type', Transaction::TYPE_WALLET_TO_WALLET)
            ->where( function( $q ) {
                $q->where('from_user_id', Auth::user()->id)
                ->orWhere('to_user_id', Auth::user()->id);
            })
            ->with('receiver','sender');
            
            if($request->transaction_type == 'cashback'){
                $transactionModel = $transactionModel->where('type', 2);
            } elseif($request->transaction_type == 'wallet'){
                $transactionModel = $transactionModel->where('type', '!=',2);
            }
            
            $transactionModel = $transactionModel->latest()->paginate(10);
            $custom = collect([
                'wallet_balance' => $walletModel->balance, 
                'wallet_id' => $walletModel->wallet_id, 
                'cashback_balance' => $walletModel->cashback_balance
            ]);
            $transactionModel = $custom->merge($transactionModel);
            

            return $this->sendResponse($transactionModel, 'Fund Transfer List Successfully.');

		} catch (\Exception $ex) {
			return $this->sendError([], $ex->getMessage());
		}

	}

    public function getTransactionList()
	{

        try {
            $walletModel = Wallet::where('user_id', Auth::user()->id)->first();
            $transactionModel = Transaction::
            where( function( $q ) {
                $q->where('from_user_id', Auth::user()->id)
                ->orWhere('to_user_id', Auth::user()->id);
            })
            ->with('receiver','sender')->latest()->paginate(10);

            $custom = collect([
                'wallet_balance' => $walletModel->balance, 
                'wallet_id' => $walletModel->wallet_id, 
                'cashback_balance' => $walletModel->cashback_balance
            ]);
            $transactionModel = $custom->merge($transactionModel);
            

            return $this->sendResponse($transactionModel, 'Fund Transfer List Successfully.');

		} catch (\Exception $ex) {
			return $this->sendError([], $ex->getMessage());
		}

	}

    public function getWalletTransferList()
	{

        try {
            $walletModel = Wallet::where('user_id', Auth::user()->id)->first();
            $transactionModel = Transaction::select('*', DB::raw("DATE_FORMAT(created_at, '%d %b %Y') AS formatted_created_at"))->where('wallet_type', '!=', Transaction::TYPE_WALLET_TO_WALLET)
            ->where( function( $q ) {
                $q->where('from_user_id', Auth::user()->id)
                ->orWhere('to_user_id', Auth::user()->id);
            })
            ->with('accountDetails')
            ->latest()->paginate(10);

            $custom = collect([
                'wallet_balance' => $walletModel->balance, 
                'wallet_id' => $walletModel->wallet_id,
                'cashback_balance' => $walletModel->cashback_balance
            ]);
            $transactionModel = $custom->merge($transactionModel);
            

            return $this->sendResponse($transactionModel, 'Fund Transfer List Successfully.');

		} catch (\Exception $ex) {
			return $this->sendError([], $ex->getMessage());
		}

	}

    public function getWalletBalance(BalanceRequest $request)
    {
        $walletModel = Wallet::where('user_id', Auth::user()->id)->first();

        return $this->sendResponse($walletModel, 'Balance Fetch Successfully.');
    }

    public function MyWalletData()
    {
        $AccessToken = $this->createAccessToken();
        $DwollaCustomer = DwollaCustomer::where('user_id', Auth::user()->id)->first();
        $customerID = $DwollaCustomer->customer_id;
        $bankList = $this->BankList($AccessToken['access_token'], $customerID);
        if(!empty($bankList) && !empty($bankList->_embedded)){
            $fundingSources = $bankList->_embedded->{"funding-sources"};
            $fundingSourcesData = end($fundingSources);
            $data['id'] = $fundingSourcesData->id;

            return $data;

        }
    }

    public function cashbackToWallet(cashbackToWalletRequest $request)
    {

        $user_id = Auth::user()->id;
        $wallet = Wallet::where('user_id', $user_id)->first();
        if($wallet->cashback_balance == 0 || $wallet->cashback_balance < $request->amount)
        {
			return $this->sendError([], 'Insufficent balance in cashback');
        }

        $wallet->balance = $wallet->balance + $request->amount;
        $wallet->cashback_balance = $wallet->cashback_balance - $request->amount;
        if($wallet->save()){
            return $this->sendResponse($wallet, 'Fund Transfer Successfully.');
        } else {
			return $this->sendError([], 'Something went wrong');
        }



    }

    public function removeBank(RemoveFundTransferRequest $request)
    {
        $current_user_id = Auth::user()->id;
       
        $funding_source = $request->funding_source;
        $AccessToken = $this->createAccessToken();
        $removeBank = $this->DeleteBank($AccessToken['access_token'], $funding_source);
        
        $walletModel = Wallet::where('user_id', $current_user_id)->first();

        $DwollaCustomer = DwollaCustomer::where('user_id', $current_user_id)->first();
        $customerID = $DwollaCustomer->customer_id;
        $bankList = $this->BankList($AccessToken['access_token'], $customerID);

        if(!empty($bankList->code) && $bankList->code == 'ExpiredAccessToken'){
            return $this->sendError([], 'Token Expired Try Again');
        }

        $custom = collect([
            'wallet_balance' => $walletModel->balance, 
            'wallet_id' => $walletModel->wallet_id,
            'cashback_balance' => $walletModel->cashback_balance,
        ]);
        $bankList = $custom->merge($bankList);
        
        return $this->sendResponse($bankList, 'Bank Removed Successfully.');

    }
    

}
