<?php 

namespace App\Traits;

use App\Models\BusinessCategory;
use App\Models\BusinessSubCategory;
use DwollaSwagger\Configuration;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Storage;

trait DwollaTrait
{

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

    protected function _header($accessToken)
	{
		return [
			'Content-Type: application/vnd.dwolla.v1.hal+json',
			'Accept: application/vnd.dwolla.v1.hal+json',
			'Authorization: Bearer ' . $accessToken,
		];
	}

    public function createAccessToken()
	{

		$tokensApi = new \DwollaSwagger\TokensApi($this->apiClient);
		$appToken = $tokensApi->token();

        return ['access_token' => $appToken->access_token];

	}

    public function onDemandAuthorizations($accessToken)
	{

		$apiEndpoint = $this->dwolla_url.'/on-demand-authorizations';

		$headers = $this->_header($accessToken);

		$curl = curl_init();

		curl_setopt($curl, CURLOPT_URL, $apiEndpoint);
		curl_setopt($curl, CURLOPT_POST, 1);
		curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

		$response = curl_exec($curl);

		if ($response === false) {
			return 'Curl error: ' . curl_error($curl);
		} else {
			$data = json_decode($response);
			return $data;
		}

		curl_close($curl);

	}

    public function createCustomers($accessToken, $userData, $request)
	{

		$apiEndpoint = $this->dwolla_url.'/customers';
		
		$headers = $this->_header($accessToken);
		$dummy_address = '47 W 13th St, New York, NY 10011, USA';
		$dummy_city = 'New York';
		$dummy_state = 'NY';
		$dummy_postal_code = '10011';
		
		if($userData->BusinessDetail && $userData->BusinessDetail->ssn_itin){
			$ssn = Crypt::decryptString($userData->BusinessDetail->ssn_itin);
		} else {
			$code = rand(1111,9999);
			$ssn = $code;
		}
		if($userData->address && $userData->address->address){
			$dummy_address = $userData->address->address;
			$dummy_city = $userData->address->city;
			$dummy_state = $userData->address->state;
			$dummy_postal_code = $userData->address->postal_code;
		}

		$payload = [
			"firstName" => $userData->first_name,
			"lastName" => $userData->last_name,
			"email" => $userData->email,
			"ipAddress" => $request->ip(),
			"type" => "personal",
			"address1" => $dummy_address,
			"city" => $dummy_city,
			"state" => $dummy_state,
			"postalCode" => $dummy_postal_code,
			"dateOfBirth" => $userData->date_of_birth,
			"ssn" => $ssn,
		];

		$curl = curl_init();

		curl_setopt($curl, CURLOPT_URL, $apiEndpoint);
		curl_setopt($curl, CURLOPT_POST, 1);
		curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($payload));
		curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

		$response = curl_exec($curl);

		if ($response === false) {
			return 'Curl error: ' . curl_error($curl);
		} else {
			curl_close($curl);
			$data = json_decode($response);
            if(!empty($data->code) && $data->code == 'ExpiredAccessToken'){
                return $data;
            }
			if(!empty($data->code) && $data->code == 'ValidationError'){
                return $data;
            }
			$newCreatedCustomerData = $this->getCustomerdataByEmail($accessToken, $userData->email);
			return $newCreatedCustomerData;
		}

		curl_close($curl);

	}

	protected function _documentHeader($accessToken){
		return [
				'Authorization: Bearer '.$accessToken,
				'Accept: application/vnd.dwolla.v1.hal+json',
				'Cache-Control: no-cache',
				'Content-Type: multipart/form-data; boundary=----WebKitFormBoundary7MA4YWxkTrZu0gW'
			];
	}

	public function createCustomerDocument($accessToken, $request, $dwollaCustomerId)
	{
		$customerId = $dwollaCustomerId;
		$apiEndpoint = $this->dwolla_url.'/customers/'.$customerId.'/documents';
		
		$headers = $this->_documentHeader($accessToken);

		$path = $request->file('verification_document')->store('uploads');
		$completePath = Storage::path($path);

		$payload = [
			'documentType' => $request->document_type,
			'file' => new \CURLFile($completePath)
		];

		$curl = curl_init();

		curl_setopt($curl, CURLOPT_URL, $apiEndpoint);
		curl_setopt($curl, CURLOPT_POST, 1);
		curl_setopt($curl, CURLOPT_POSTFIELDS, $payload);
		curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

		$response = curl_exec($curl);

		if(Storage::exists($path)){
			Storage::delete($path);
		}

		if ($response === false) {
			return 'Curl error: ' . curl_error($curl);
		} else {
			curl_close($curl);
			$data = json_decode($response);

            if(!empty($data->code) && $data->code == 'ExpiredAccessToken'){
                return $data;
            }
			if(!empty($data->code) && $data->code == 'ValidationError'){
                return $data;
            }

			return $data;
		}

		curl_close($curl);
	}

	public function certifyBeneficialOwnership($accessToken, $request, $dwollaCustomerId)
	{
		$apiEndpoint = $this->dwolla_url.'/customers/'.$dwollaCustomerId.'/beneficial-ownership';
		
		$headers = $this->_header($accessToken);

		$payload = [
			"status" => 'certified',
		];

		$curl = curl_init();

		curl_setopt($curl, CURLOPT_URL, $apiEndpoint);
		curl_setopt($curl, CURLOPT_POST, 1);
		curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($payload));
		curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

		$response = curl_exec($curl);

		if ($response === false) {
			return 'Curl error: ' . curl_error($curl);
		} else {
			curl_close($curl);
			$data = json_decode($response);
            if(!empty($data->code) && $data->code == 'ExpiredAccessToken'){
                return $data;
            }
			if(!empty($data->code) && $data->code == 'ValidationError'){
                return $data;
            }
			return $data;

		}

		curl_close($curl);
	}

	public function createSoloBusinessCustomer($accessToken, $userData, $request)
	{

		$apiEndpoint = $this->dwolla_url.'/customers';
		
		$headers = $this->_header($accessToken);
		$dummy_address = '47 W 13th St, New York, NY 10011, USA';
		$dummy_city = 'New York';
		$dummy_state = 'NY';
		$dummy_postal_code = '10011';
		$businessClassification = '9ed3f670-7d6f-11e3-b1ce-5404a6144203';
		
		if($userData->BusinessDetail && $userData->BusinessDetail->ssn_itin){
			$ssn = Crypt::decryptString($userData->BusinessDetail->ssn_itin);
		} else {
			$code = rand(1111,9999);
			$ssn = $code;
		}
		if($userData->address && $userData->address->address){
			$dummy_address = $userData->address->address;
			$dummy_city = $userData->address->city;
			$dummy_state = $userData->address->state;
			$dummy_postal_code = $userData->address->postal_code;	
		}
		if(isset($request->business_sub_category)){
			$BusinessCategory = BusinessSubCategory::where('id', $request->business_sub_category)->first();
			if(!empty($BusinessCategory))
			{
				$businessClassification = $BusinessCategory->dwolla_key;
			}
		}

		$payload = [
			"firstName" => $userData->first_name,
			"lastName" => $userData->last_name,
			"email" => $userData->email,
			"ipAddress" => $request->ip(),
			"type" => "business",
			"address1" => $dummy_address,
			"city" => $dummy_city,
			"state" => $dummy_state,
			"postalCode" => $dummy_postal_code,
			"dateOfBirth" => $userData->date_of_birth,
			"ssn" => $ssn,
			"businessType"=> "soleProprietorship",
			"businessName" => $request->business_name,
			"businessClassification" => $businessClassification,
			"correlationId" => $request->correlationId,
		];


		$curl = curl_init();

		curl_setopt($curl, CURLOPT_URL, $apiEndpoint);
		curl_setopt($curl, CURLOPT_POST, 1);
		curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($payload));
		curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

		$response = curl_exec($curl);

		if ($response === false) {
			return 'Curl error: ' . curl_error($curl);
		} else {
			curl_close($curl);
			$data = json_decode($response);
            if(!empty($data->code) && $data->code == 'ExpiredAccessToken'){
                return $data;
            }
			if(!empty($data->code) && $data->code == 'ValidationError'){
                return $data;
            }
			$newCreatedCustomerData = $this->getCustomerdataByEmail($accessToken, $userData->email);
			return $newCreatedCustomerData;
		}

		curl_close($curl);

	}

	public function createNonSoloBusinessCustomer($accessToken, $userData, $request)
	{

		$apiEndpoint = $this->dwolla_url.'/customers';
		
		$headers = $this->_header($accessToken);
		$dummy_address = '47 W 13th St, New York, NY 10011, USA';
		$dummy_city = 'New York';
		$dummy_state = 'NY';
		$dummy_postal_code = '10011';
		$businessClassification = '9ed3f670-7d6f-11e3-b1ce-5404a6144203';
		
		if($userData->BusinessDetail && $userData->BusinessDetail->ssn_itin){
			$ssn = Crypt::decryptString($userData->BusinessDetail->ssn_itin);
		} else {
			$code = rand(1111,9999);
			$ssn = $code;
		}
		if($userData->address && $userData->address->address){
			$dummy_address = $userData->address->address;
			$dummy_city = $userData->address->city;
			$dummy_state = $userData->address->state;
			$dummy_postal_code = $userData->address->postal_code;
		}
		if(isset($request->business_category)){
			$BusinessCategory = BusinessSubCategory::where('id', $request->business_category)->first();
			if(!empty($BusinessCategory))
			{
				$businessClassification = $BusinessCategory->dwolla_key;
			}
		}

		$payload = [
			"firstName" => $userData->first_name,
			"lastName" => $userData->last_name,
			"email" => $userData->email,
			"ipAddress" => $request->ip(),
			"type" => "business",
			"address1" => $dummy_address,
			"city" => $dummy_city,
			"state" => $dummy_state,
			"postalCode" => $dummy_postal_code,

			"controller" => [
				"firstName" => $userData->first_name,
				"lastName" => $userData->last_name,
				"title" => "CEO",
				"ssn" => $ssn,
				"dateOfBirth" => $userData->date_of_birth,

				"address" => [
					"address1" => $dummy_address,
					"address2" => $dummy_address,
					"city" => $dummy_city,
					"stateProvinceRegion" => $dummy_state,
					"postalCode" => $dummy_postal_code,
					"country" => "US"
				]
			],

			"businessType"=> $request->businessType,
			"businessClassification" => $businessClassification,
			"businessName" => $request->business_name,
			"ein" => $request->business_ein,
			"correlationId" => $request->correlationId,
		];

		$curl = curl_init();

		curl_setopt($curl, CURLOPT_URL, $apiEndpoint);
		curl_setopt($curl, CURLOPT_POST, 1);
		curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($payload));
		curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

		$response = curl_exec($curl);

		if ($response === false) {
			return 'Curl error: ' . curl_error($curl);
		} else {
			curl_close($curl);
			$data = json_decode($response);
            if(!empty($data->code) && $data->code == 'ExpiredAccessToken'){
                return $data;
            }
			if(!empty($data->code) && $data->code == 'ValidationError'){
                return $data;
            }
			$newCreatedCustomerData = $this->getCustomerdataByEmail($accessToken, $userData->email);
			return $newCreatedCustomerData;
		}

		curl_close($curl);

	}

	public function updateCustomer($accessToken, $userData, $request, $customerId)
	{

		$apiEndpoint = $this->dwolla_url.'/customers/'.$customerId;
		
		$headers = $this->_header($accessToken);

		$payload = [
			"firstName" => $userData->first_name,
			"lastName" => $userData->last_name,
			"email" => $userData->email,
			"ipAddress" => $request->ip(),
			"type" => "personal",
			"address1" => $userData->address->address,
			"city" => $userData->address->city,
			"state" => $userData->address->state,
			"postalCode" => $userData->address->postal_code,
		];

		$curl = curl_init();

		curl_setopt($curl, CURLOPT_URL, $apiEndpoint);
		curl_setopt($curl, CURLOPT_POST, 1);
		curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($payload));
		curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

		$response = curl_exec($curl);

		if ($response === false) {
			return 'Curl error: ' . curl_error($curl);
		} else {
			curl_close($curl);
			$data = json_decode($response);
            if(!empty($data->code) && $data->code == 'ExpiredAccessToken'){
                return $data;
            }
			if(!empty($data->code) && $data->code == 'ValidationError'){
                return $data;
            }
			return $customerId;
		}

		curl_close($curl);

	}

	protected function getCustomerdataByEmail($accessToken, $email)
	{

		$apiEndpoint = $this->dwolla_url.'/customers?email='.$email;
		
		$headers = $this->_header($accessToken);

		$curl = curl_init();

		curl_setopt($curl, CURLOPT_URL, $apiEndpoint);
		curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

		$response = curl_exec($curl);

		if ($response === false) {
			return 'Curl error: ' . curl_error($curl);
		} else {
			$data = json_decode($response);
			if(!empty($data->_embedded) && !empty($data->_embedded->customers[0])){
				curl_close($curl);
				return $data;
			} else {
				return null;
			}
		}
		curl_close($curl);

	}

    protected function getCustomersIdByEmail($accessToken, $email)
	{

		$apiEndpoint = $this->dwolla_url.'/customers?email='.$email;
		
		$headers = $this->_header($accessToken);

		$curl = curl_init();

		curl_setopt($curl, CURLOPT_URL, $apiEndpoint);
		curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

		$response = curl_exec($curl);

		if ($response === false) {
			return 'Curl error: ' . curl_error($curl);
		} else {
			$data = json_decode($response);
			if(!empty($data->_embedded) && !empty($data->_embedded->customers[0])){
				curl_close($curl);
				return $data->_embedded->customers[0]->id;
			} else {
				return null;
			}
		}
		curl_close($curl);

	}

    public function createBankSource($accessToken, $customerID, $request)
	{

		$apiEndpoint = $this->dwolla_url.'/customers/'.$customerID.'/funding-sources';
		
		$headers = $this->_header($accessToken);

		$payload = [
			"_links" => $request->_links,
			"routingNumber" => $request->routingNumber,
			"accountNumber" => $request->accountNumber,
			"bankAccountType" => $request->bankAccountType,
			"name" => $request->name
		];

		$curl = curl_init();

		curl_setopt($curl, CURLOPT_URL, $apiEndpoint);
		curl_setopt($curl, CURLOPT_POST, 1);
		curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($payload));
		curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

		$response = curl_exec($curl);

		if ($response === false) {
            return 'Curl error: ' . curl_error($curl);
		} else {
            $data = json_decode($response);
			return $data;
		}

		curl_close($curl);

	}

	public function getBankSource($accessToken, $fundingSourceID)
	{

		$apiEndpoint = $fundingSourceID;
		
		$headers = $this->_header($accessToken);

		$curl = curl_init();

		curl_setopt($curl, CURLOPT_URL, $apiEndpoint);
		curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

		$response = curl_exec($curl);

		if ($response === false) {
            return 'Curl error: ' . curl_error($curl);
		} else {
            $data = json_decode($response);
			return $data;
		}

		curl_close($curl);

	}

    public function BankList($accessToken, $customerID)
	{

		$apiEndpoint = $this->dwolla_url.'/customers/'.$customerID.'/funding-sources';
		
		$headers = $this->_header($accessToken);

		$curl = curl_init();

		curl_setopt($curl, CURLOPT_URL, $apiEndpoint);
		curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

		$response = curl_exec($curl);

		if ($response === false) {
			return 'Curl error: ' . curl_error($curl);
		} else {
			$data = json_decode($response);
            return $data;
		}
		curl_close($curl);

	}

    public function microDeposits($accessToken, $request)
	{

		$apiEndpoint = $this->dwolla_url.'/funding-sources/'.$request->funding_source.'/micro-deposits';
		
		$headers = $this->_header($accessToken);

		$payload = [
            $request->amount_data
		];
		
		$curl = curl_init();

		curl_setopt($curl, CURLOPT_URL, $apiEndpoint);
		curl_setopt($curl, CURLOPT_POST, 1);
		curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($payload));
		curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

		$response = curl_exec($curl);

		if ($response === false) {
			return 'Curl error: ' . curl_error($curl);
		} else {
			$data = json_decode($response);
            dd($data);
			return $data;
		}

		curl_close($curl);

	}

    public function fundTransfer($accessToken, $request)
	{

		$apiEndpoint = $this->dwolla_url.'/transfers';
		
		$headers = $this->_header($accessToken);

		$payload = [
			"_links" => [
				"source" => [
					"href" => "https://api-sandbox.dwolla.com/funding-sources/".$request->source_id
				],
				"destination" => [
					"href" => "https://api-sandbox.dwolla.com/funding-sources/".$request->destination_id
				]
			],
			"amount" => [
				"currency" => "USD",
				"value" => $request->amount
			],
			"correlationId" => $request->correlationId
		];
		

		$curl = curl_init();

		curl_setopt($curl, CURLOPT_URL, $apiEndpoint);
		curl_setopt($curl, CURLOPT_POST, 1);
		curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($payload));
		curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

		$response = curl_exec($curl);

		if ($response === false) {
			return 'Curl error: ' . curl_error($curl);
		} else {
			$data = json_decode($response);
            return $data;
		}

		curl_close($curl);

	}

	public function sameDayFundTransfer($accessToken, $request)
	{

		$apiEndpoint = $this->dwolla_url.'/transfers';
		
		$headers = $this->_header($accessToken);

		$payload = [
			"_links" => [
				"source" => [
					"href" => "https://api-sandbox.dwolla.com/funding-sources/".$request->source_id
				],
				"destination" => [
					"href" => "https://api-sandbox.dwolla.com/funding-sources/".$request->destination_id
				]
			],
			"amount" => [
				"currency" => "USD",
				"value" => $request->amount
			],
			"clearing" => [
				"source" => "next-available"
			],
			"correlationId" => $request->correlationId
		];
		
		$curl = curl_init();

		curl_setopt($curl, CURLOPT_URL, $apiEndpoint);
		curl_setopt($curl, CURLOPT_POST, 1);
		curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($payload));
		curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

		$response = curl_exec($curl);

		if ($response === false) {
			return 'Curl error: ' . curl_error($curl);
		} else {
			$data = json_decode($response);
            return $data;
		}

		curl_close($curl);

	}

    public function transferList($accessToken, $customerID)
	{

		$apiEndpoint = $this->dwolla_url.'/customers/'.$customerID.'/transfers';
		
		$headers = $this->_header($accessToken);		

		$curl = curl_init();

		curl_setopt($curl, CURLOPT_URL, $apiEndpoint);
		curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

		$response = curl_exec($curl);

		if ($response === false) {
			return 'Curl error: ' . curl_error($curl);
		} else {
			$data = json_decode($response);
            return $data;
		}

		curl_close($curl);

	}

	public function transactionDetail($accessToken, $transactionID)
	{

		$apiEndpoint = $this->dwolla_url.'/transfers/'.$transactionID;
		
		$headers = $this->_header($accessToken);

		$curl = curl_init();

		curl_setopt($curl, CURLOPT_URL, $apiEndpoint);
		curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

		$response = curl_exec($curl);

		if ($response === false) {
			return 'Curl error: ' . curl_error($curl);
		} else {
			$data = json_decode($response);
            return $data;
		}

		curl_close($curl);

	}

	public function searchTransferList($accessToken, $customerID, $correlationId)
	{
		$apiEndpoint = $this->dwolla_url.'/customers/'.$customerID.'/transfers?correlationId='.$correlationId;
		
		
		$headers = $this->_header($accessToken);		

		$curl = curl_init();

		curl_setopt($curl, CURLOPT_URL, $apiEndpoint);
		curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

		$response = curl_exec($curl);

		if ($response === false) {
			return 'Curl error: ' . curl_error($curl);
		} else {
			$data = json_decode($response);
            return $data;
		}

		curl_close($curl);

	}

	public function searchAccountTransferList($accessToken, $accountID, $correlationId)
	{
		$apiEndpoint = $this->dwolla_url.'/accounts/'.$accountID.'/transfers?correlationId='.$correlationId;
		
		
		$headers = $this->_header($accessToken);		

		$curl = curl_init();

		curl_setopt($curl, CURLOPT_URL, $apiEndpoint);
		curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

		$response = curl_exec($curl);

		if ($response === false) {
			return 'Curl error: ' . curl_error($curl);
		} else {
			$data = json_decode($response);
            return $data;
		}

		curl_close($curl);

	}

	public function BankBalance($accessToken, $fundingSourceID)
	{

		$apiEndpoint = $this->dwolla_url.'/funding-sources/'.$fundingSourceID.'/balance';
		
		$headers = $this->_header($accessToken);

		$curl = curl_init();

		curl_setopt($curl, CURLOPT_URL, $apiEndpoint);
		curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

		$response = curl_exec($curl);

		if ($response === false) {
			return 'Curl error: ' . curl_error($curl);
		} else {
			$data = json_decode($response);
            return $data;
		}
		curl_close($curl);

	}

	public function plaidDwollaFundingSource($accessToken, $plaidToken, $bankName, $customerID)
	{
		$payload = array(
			"plaidToken" => $plaidToken,
			"name" => $bankName
		);

		$headers = $this->_header($accessToken);
		
		$apiEndpoint = $this->dwolla_url."/customers/".$customerID."/funding-sources";
		
		$curl = curl_init();
		curl_setopt($curl, CURLOPT_URL, $apiEndpoint);
		curl_setopt($curl, CURLOPT_POST, 1);
		curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($payload));
		curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		
		$response = curl_exec($curl);
		
		if ($response === false) {
			echo 'Curl error: ' . curl_error($curl);
			return curl_error($curl);
		} else {
			curl_close($curl);
			$data = json_decode($response);

			return $data;
		}
		
		curl_close($curl);
	}

	public function walletBalanceGet($fundingSourceID)
    {
        $access_token = $this->createAccessToken();
        $data = $this->BankBalance($access_token['access_token'], $fundingSourceID);
        $data->wallet_id = $fundingSourceID;
        return $data;
    }

	public function DeleteBank($accessToken, $funding_source)
	{

		$apiEndpoint = $this->dwolla_url.'/funding-sources/'.$funding_source;
		
		$data = array(
			'removed' => true
		);

		$headers = $this->_header($accessToken);

		$curl = curl_init($apiEndpoint);

		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'POST');
		curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));
		curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);

		$response = curl_exec($curl);

		if ($response === false) {
			return 'Curl error: ' . curl_error($curl);
		} else {
			$data = json_decode($response);
            return $data;
		}
		curl_close($curl);

	}
    

}