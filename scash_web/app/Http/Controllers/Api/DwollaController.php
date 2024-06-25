<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\accessTokenRequest;
use App\Http\Requests\PlaidDwollaFundingRequest;
use App\Jobs\ApproveCustomer;
use App\Models\DwollaCustomer;
use App\Models\Transaction;
use App\Models\User;
use App\Models\Wallet;
use App\Models\Webhook;
use App\Traits\DwollaTrait;
use DwollaSwagger\Configuration;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class DwollaController extends Controller
{
    use DwollaTrait;

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
     ** path="/api/v1/auth/dwolla-access-token",
     *   tags={"Dwolla"},
     *   summary="Only for test",
     *   operationId="dwolla-access-token",
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
	public function createDwollaAccessToken()
	{
        try {
            $access_token = $this->createAccessToken();
    
            return $this->sendResponse($access_token, 'Token fetched successfully.');

		} catch (\Exception $ex) {
			return $this->sendError([], $ex->getMessage());
		}
	}

    /**
     * @OA\POST(
     ** path="/api/v1/auth/on-demand-authorizations",
     *   tags={"Dwolla"},
     *   summary="Only for test",
     *   operationId="demandAuthorizations",
     *   @OA\RequestBody(
     *	    required=true,
	 *  	@OA\JsonContent(
	 *      	type="object",
	 *      	@OA\Property(property="access_token", type="string", example="0CrTLaIgkktZaDzvCPadBpSHPkbOmxWHXtdeM6kRGGqXZnLTQi"),
	 *      )
	 *   ),
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
	public function demandAuthorizations(accessTokenRequest $request)
	{
        try {
            $authorisation = $this->onDemandAuthorizations($request->access_token);
            if(!empty($authorisation->code) && $authorisation->code == 'ExpiredAccessToken'){
                return $this->sendError([], 'Token Expired Try Again');
            }
    
            return $this->sendResponse($authorisation, 'Demand Authorisation fetched successfully.');

		} catch (\Exception $ex) {
			return $this->sendError([], $ex->getMessage());
		}
	}

    public function createPlaidDwollaFunding(PlaidDwollaFundingRequest $request)
    {
        try {
            $userModel = User::where('id', Auth::user()->id)->with('address')->first();
            $DwollaCustomer = DwollaCustomer::where('user_id', Auth::user()->id)->first();
            
            $customer_id = '';
            $AccessToken = $this->createAccessToken();

            if(empty($DwollaCustomer)){
                if(
                    empty($userModel->first_name) || 
                    empty($userModel->last_name) || 
                    empty($userModel->date_of_birth) ||
                    empty($userModel->address->address) ||
                    empty($userModel->address->city) ||
                    empty($userModel->address->state) 
                    ) {
                        return $this->sendError([], 'Your details not fill, so bank account will not be created');
                    }
                    $customer_id = $this->createCustomers($AccessToken['access_token'], $userModel, $request);
                    if(!empty($customer_id->code) && $customer_id->code == 'ExpiredAccessToken'){
                        return $this->sendError([], 'Token Expired Try Again');
                    }
                    if(!empty($customer_id->code) && $customer_id->code == 'ValidationError'){
                        return $this->sendError($customer_id, 'Data not filled properly in your profile');
                    }
                if(!empty($customer_id)){
                    $DwollaCustomer = new DwollaCustomer();
                    $DwollaCustomer->user_id = $userModel->id;
                    $DwollaCustomer->customer_id = $customer_id;
                    $DwollaCustomer->save();

                }
            }

            $DwollaCustomer = DwollaCustomer::where('user_id', Auth::user()->id)->first();

            $data = $this->plaidDwollaFundingSource($AccessToken['access_token'], $request->plaid_processor_token, $request->bank_name, $DwollaCustomer->customer_id);
            if(!empty($data->code) && $data->code == 'ExpiredAccessToken'){
                return $this->sendError([], 'Token Expired');
            }

            $walletID = $this->MyWalletData($AccessToken['access_token'], $DwollaCustomer->customer_id);
            $walletModel = Wallet::updateOrCreate(
                ['user_id' => Auth::user()->id],
				[
                    'user_id' => Auth::user()->id,
					'wallet_id' => $walletID['id'],
                    ]
                );

            return $this->sendResponse([], 'Bank Added Successfully.');

        } catch (\Exception $ex) {
			return $this->sendError([], $ex->getMessage());
		}

    }

    public function MyWalletData($AccessToken, $customerID)
    {
        $bankList = $this->BankList($AccessToken, $customerID);
        if(!empty($bankList) && !empty($bankList->_embedded)){
            $fundingSources = $bankList->_embedded->{"funding-sources"};
            $fundingSourcesData = end($fundingSources);
            $data['id'] = $fundingSourcesData->id;

            return $data;

        }
    }

    public function webhooks(Request $request)
	{

        try {

            if($request->topic == 'customer_bank_transfer_completed' && $request->resourceId){
                Log::info('customer_bank_transfer_completed '. $request->resourceId);
                $resourceId = $request->resourceId;
                $transactionModel = Transaction::where('transaction_id', $resourceId)->first();
                if(!empty($transactionModel) && $transactionModel->status == Transaction::STATUS_PENDING){
                    if($transactionModel->wallet_type == Transaction::TYPE_MY_WALLET_WITHDRAW){				
                        $transactionModel->status = Transaction::STATUS_COMPLETED;
                        $transactionModel->save();
                    }

                    if($transactionModel->wallet_type == Transaction::TYPE_MY_WALLET_DEPOSIT){				
                        $transactionModel->status = Transaction::STATUS_COMPLETED;
                        $transactionModel->save();

                        $walletModel = Wallet::where('user_id', $transactionModel->to_user_id)->first();
                        Log::info('$walletModel->balance + $transactionModel->amount '. $walletModel->balance .' '. $transactionModel->amount);
                        $walletModel->balance = $walletModel->balance + $transactionModel->amount;

                        $walletModel->save();
                    }

                    return 'done';
                }
            }


            if($request->topic == 'customer_transfer_failed' && $request->resourceId){
                $resourceId = $request->resourceId;
                $transactionModel = Transaction::where('transaction_id', $resourceId)->first();
                if(!empty($transactionModel)){
                    $transactionModel->status = Transaction::STATUS_FAILED;
                    $transactionModel->save();
                }

                return 'done';
            }

            if($request->topic == 'customer_transfer_cancelled' && $request->resourceId){
                $resourceId = $request->resourceId;
                $transactionModel = Transaction::where('transaction_id', $resourceId)->first();
                if(!empty($transactionModel)){
                    $transactionModel->status = Transaction::STATUS_CANCELLED;
                    $transactionModel->save();
                }

                return 'done';
            }

            if($request->topic == 'transfer_completed' && $request->resourceId){
                $resourceId = $request->resourceId;
                $transactionModel = Transaction::where('transaction_id', $resourceId)->first();
                if(!empty($transactionModel)){
                    $transactionModel->status = Transaction::STATUS_COMPLETED;
                    $transactionModel->save();
                }

                return 'done';
            }
            
            if($request->topic == 'customer_verification_document_approved'){

                $customerUrl = $request->_links['customer']['href'];
                
                $data = explode("customers/", $customerUrl);
                $customerId = $data[1];
                
                if($customerId){
                    dispatch(new ApproveCustomer($customerId));
                }

                return 'done';
            }

            if($request->topic == 'bank_transfer_completed'){
                
                $resourceId = $request->resourceId;
                $transactionModel = Transaction::where('transaction_id', $resourceId)->first();
                if(!empty($transactionModel) && $transactionModel->status == Transaction::STATUS_PENDING){
                    if($transactionModel->wallet_type == Transaction::TYPE_MY_WALLET_WITHDRAW){				
                        $transactionModel->status = Transaction::STATUS_COMPLETED;
                        $transactionModel->save();
                    }

                    if($transactionModel->wallet_type == Transaction::TYPE_MY_WALLET_DEPOSIT){				
                        $transactionModel->status = Transaction::STATUS_COMPLETED;
                        $transactionModel->save();

                        $walletModel = Wallet::where('user_id', $transactionModel->to_user_id)->first();
                        $walletModel->balance = $walletModel->balance + $transactionModel->amount;
                        $walletModel->save();
                    }

                    return 'done';
                }
            }

        } catch (\Exception $ex) {
            \Log::info('WebHook'. $ex);
        }

	}
    

}
