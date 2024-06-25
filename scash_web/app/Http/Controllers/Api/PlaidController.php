<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\accessTokenRequest;
use App\Http\Requests\PlaidProcessorDwolla;
use App\Http\Requests\publicTokenRequest;
use Illuminate\Http\Request;
use App\Http\Resources\StateResource;
use App\Models\City;
use App\Models\PlaidAccount;
use App\Models\State;
use App\Models\User;
use App\Traits\PlaidTrait;
use Exception;
use Illuminate\Support\Facades\Auth;

class PlaidController extends Controller
{

    use PlaidTrait;

    private $client_id;
	private $secret;
	private $plaid_url;
	private $linkToken;

	public function __construct()
	{
        $this->client_id = config('services.plaid.client_id');
		$this->secret = config('services.plaid.secret');
		$this->plaid_url = config('services.plaid.url');
		$this->linkToken = 'link-sandbox-9c2a6faf-45a3-417a-a1ac-fa47f0f7e3a2';
	}


	/**
     * @OA\POST(
     ** path="/api/v1/auth/plaid-link-token",
     *   tags={"Plaid"},
     *   summary="Only for test",
     *   operationId="plaid-link-token",
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
	public function createLinkToken()
	{
        try {
            $userModel = User::where('id', Auth::user()->id)->first();
            
            $link_token = $this->linkToken($userModel);

            return $this->sendResponse($link_token, 'Token fetched successfully.');

		} catch (Exception $ex) {
			return $this->sendError([], $ex->getMessage());
		}
	}

    /**
     * @OA\POST(
     ** path="/api/v1/auth/plaid-access-token",
     *   tags={"Plaid"},
     *   summary="Only for test",
     *   operationId="plaid-access-token",
     *   @OA\RequestBody(
     *	    required=true,
	 *  	@OA\JsonContent(
	 *      	type="object",
	 *      	@OA\Property(property="public_token", type="string", example="public-sandbox-44f688e9-aaad-4dba-8c12-86291950fd8f"),
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
    public function createAccessToken(publicTokenRequest $request)
	{
        try {

            $data = $this->accessToken($request->public_token);
            return $this->sendResponse($data, 'Public Token fetched successfully.');

        } catch (Exception $ex) {
            return $this->sendError([], $ex->getMessage());
        }
	}

    /**
     * @OA\POST(
     ** path="/api/v1/auth/get-bank-account",
     *   tags={"Plaid"},
     *   summary="Only for test",
     *   operationId="get-bank-account",
     *   @OA\RequestBody(
     *	    required=true,
	 *  	@OA\JsonContent(
	 *      	type="object",
	 *      	@OA\Property(property="access_token", type="string", example="44f688e9-aaad-4dba-8c12-86291950fd8f"),
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
    public function getBankAccount(accessTokenRequest $request)
    {
        try {

            $institution_name = '';
            $data = $this->getAccount($request->access_token);
            $item_data = $this->itemGet($request->access_token);
            if(!empty($item_data) && !empty($item_data->item) && !empty($item_data->item->institution_id)){
                $institution_id = $item_data->item->institution_id;
                $institute_data = $this->institutionsGet($institution_id);
                if(!empty($institute_data) && !empty($institute_data->institution) && !empty($institute_data->institution->name)){
                    $institution_name = $institute_data->institution->name;
                }
            }
            $data->institution_name = $institution_name;
            $PlaidAccount = $this->updatePlaidAccount($data, $request->access_token, $institution_name);

            return $this->sendResponse($data, 'Account Fetch successfully.');

        } catch (Exception $ex) {
            return $this->sendError([], $ex->getMessage());
        }
    }

    protected function updatePlaidAccount($accountList, $access_token_data, $institution_name)
	{
		$json_data = '';
		$newarray = [];

		$PlaidAccountModel = PlaidAccount::where('user_id', Auth::user()->id)->first();
        
        if(!empty($accountList) && !empty($accountList->accounts)){
            $account_list = $accountList->accounts;
            
            if(!empty($PlaidAccountModel) && !empty($PlaidAccountModel->json_data))
            {
                $a = $PlaidAccountModel->json_data;
                foreach($account_list as $single){
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
                foreach($account_list as $single){
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

	}
    

    /**
     * @OA\POST(
     ** path="/api/v1/auth/get-plaid-processor-dwolla",
     *   tags={"Plaid"},
     *   summary="Only for test",
     *   operationId="get-plaid-processor-dwolla",
     *   @OA\RequestBody(
     *	    required=true,
	 *  	@OA\JsonContent(
	 *      	type="object",
	 *      	@OA\Property(property="access_token", type="string", example="44f688e9-aaad-4dba-8c12-86291950fd8f"),
	 *      	@OA\Property(property="account_id", type="string", example="JHGJG765765jhgjhghj456jh4564kj6h"),
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
    public function getPlaidProcessorDwolla(PlaidProcessorDwolla $request)
    {
        try {

            $data = $this->plaidProcessorDwolla($request->access_token, $request->account_id);
            return $this->sendResponse($data, 'Processor Token Fetch Successfully.');

        } catch (Exception $ex) {
            return $this->sendError([], $ex->getMessage());
        }
    }


}
