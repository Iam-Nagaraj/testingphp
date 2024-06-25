<?php

namespace App\Http\Controllers\Merchant;

use App\Http\Controllers\Controller;
use App\Models\DwollaCustomer;
use App\Models\PlaidAccount;
use App\Models\User;
use App\Traits\PlaidTrait;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class PladeController extends Controller
{
	use PlaidTrait;

    private $client_id;
	private $secret;
	private $plaid_url;
	private $linkToken;
	protected DwollaController $dwollaService;

	public function __construct(DwollaController $dwollaService)
	{
        $this->client_id = config('services.plaid.client_id');
		$this->secret = config('services.plaid.secret');
		$this->plaid_url = config('services.plaid.url');
		$this->linkToken = 'link-sandbox-9c2a6faf-45a3-417a-a1ac-fa47f0f7e3a2';
        $this->dwollaService = $dwollaService;

	}

	public function plaidDwollaToken(Request $request)
	{
		$data = [];

		$dwolla_access_token = $this->dwollaService->traitAccessToken();			

		try {

            $userModel = User::where('id', Auth::user()->id)->with('address','BusinessDetail')->first();
            $DwollaCustomer = DwollaCustomer::where('user_id', Auth::user()->id)->first();
            
			if($request->mask){
				$bank_name = $request->mask;
			} else {
				$bank_name = $userModel->first_name.' '.$userModel->last_name.' '.$request->mask;
			}
            
			$processorData = $this->plaidProcessorDwolla($request->access_token, $request->account_id);
			if(!empty($processorData->error_code) && $processorData->error_code == "INVALID_ACCOUNT_ID"){
				return $this->sendError([], 'Invalid Account ID');
			}			
			if(!empty($processorData->error_code) && $processorData->error_code == "NO_AUTH_ACCOUNTS"){
				return $this->sendError([], $processorData->error_message);
			}			
			
			if(!empty($processorData) && !empty($processorData->processor_token)){
				$data['processor_token'] = $processorData->processor_token;
                
				$DwollaFundingSource = $this->dwollaService->webPlaidDwollaFundingSource($dwolla_access_token, $processorData->processor_token, $bank_name, $DwollaCustomer->customer_id);
				if(!empty($DwollaFundingSource->code) && $DwollaFundingSource->code == 'ServerError'){
                    return $this->sendError([], $DwollaFundingSource->message);
                }

				   
                if(!empty($DwollaFundingSource->code) && $DwollaFundingSource->code == 'DuplicateResource'){
                    return $this->sendError([], 'This Account is already added.');
                }

			}

			$PlaidAccountModel = PlaidAccount::where('user_id', Auth::user()->id)->first();
			$json_data = json_decode($PlaidAccountModel->json_data);
			$new_json_data = [];
			foreach($json_data as $singleJson){
				$new_json_data[] = $singleJson;
				if($singleJson->account_id == $request->account_id){
					$singleJson->is_connected = true;
					$new_json_data[] = $singleJson;
				}

			}

			$PlaidAccountModel->json_data = json_encode($new_json_data);
			$PlaidAccountModel->save();

            return $this->sendResponse($data, 'Bank Added Successfully.');

        } catch (Exception $ex) {
            return $this->sendError([], $ex->getMessage());
        }
	}


}
