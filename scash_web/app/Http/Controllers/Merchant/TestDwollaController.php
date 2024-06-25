<?php

namespace App\Http\Controllers\Merchant;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\Models\Wallet;
use App\Traits\FcmTrait;
use DwollaSwagger\Configuration;
use DwollaSwagger\models\CreateExchangeRequest;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class TestDwollaController extends Controller
{
	use FcmTrait;

	private $client_id;
	private $secret;
	private $dwolla_url;
	private $apiClient;
	private $accessToken;

	public function __construct($accessToken = null)
	{
		$this->client_id = config('services.dwolla.client_id');
		$this->secret = config('services.dwolla.secret');
		$this->dwolla_url = config('services.dwolla.url');
		$this->accessToken = 'YmWersUxo0Z5hnMHlHgRYFgShiM6qrNziq9ZR2E9Hm8W3GwdAv';

		Configuration::$username = $this->client_id;
		Configuration::$password = $this->secret;

		$this->apiClient = new \DwollaSwagger\ApiClient($this->dwolla_url);

	}

	protected function _header()
	{
		return [
			'Content-Type: application/vnd.dwolla.v1.hal+json',
			'Accept: application/vnd.dwolla.v1.hal+json',
			'Authorization: Bearer ' . $this->accessToken,
		];
	}

	public function createAccessToken()
	{
		$tokensApi = new \DwollaSwagger\TokensApi($this->apiClient);
		$appToken = $tokensApi->token();

		return $appToken->access_token;

	}

	public function webPlaidDwollaFundingSource($accessToken, $plaidToken, $bankName, $customerID)
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

	public function onDemandAuthorizations()
	{

		$apiEndpoint = $this->dwolla_url.'/on-demand-authorizations';

		$headers = $this->_header();

		$curl = curl_init();

		curl_setopt($curl, CURLOPT_URL, $apiEndpoint);
		curl_setopt($curl, CURLOPT_POST, 1);
		curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

		$response = curl_exec($curl);

		if ($response === false) {
			echo 'Curl error: ' . curl_error($curl);
			dd(curl_error($curl));
		} else {
			$data = json_decode($response);
			if(!empty($data->_links)){
				return $data;
			}
		}

		curl_close($curl);

		// $json = '{"_links":{"self":{"href":"https://api-sandbox.dwolla.com/on-demand-authorizations/1ee34bec-28b9-ee11-ac47-06f818744a9d",
		// 	"type":"application/vnd.dwolla.v1.hal+json","resource-type":"on-demand-authorization"}},
		// 	"bodyText":"I agree that all future payments to or facilitated by Scash will be processed by the Dwolla payment system 
		// 	from the selected account above. In order to cancel this authorization, I will change my payment settings within my Scash account.",
		// 	"buttonText":"Agree & Continue"}';

	}

	public function createCustomers()
	{
		
		$apiEndpoint = $this->dwolla_url.'/customers';
		
		$headers = $this->_header();
		$email = fake()->unique()->safeEmail();
		$code = rand(1000,9999);
		$payload = [
			"firstName" => fake()->name(),
			"lastName" => fake()->name(),
			"email" => $email,
			"ipAddress" => "12",
			"type" => "personal",
			"address1" => "S2t",
			"city" => "z2",
			"state" => "NY",
			"postalCode" => "11222",
			"dateOfBirth" => "1982-01-02",
			"ssn" => $code,
			"businessType"=> "soleProprietorship",
			"businessName" => fake()->name(),
			"businessClassification" => "9ed38153-7d6f-11e3-97e3-5404a6144203",
			"correlationId" => fake()->uuid(),
		];

		$curl = curl_init();

		curl_setopt($curl, CURLOPT_URL, $apiEndpoint);
		curl_setopt($curl, CURLOPT_POST, 1);
		curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($payload));
		curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

		$response = curl_exec($curl);

		if ($response === false) {
			echo 'Curl error: ' . curl_error($curl);
			dd(curl_error($curl));
		} else {
			curl_close($curl);
			$data = json_decode($response);
			dd($data);
			$newCreatedCustomerID = $this->getCustomersIdByEmail($email);
			dd($data, $newCreatedCustomerID);

			return $newCreatedCustomerID;
		}

		curl_close($curl);

	}

	public function createBusinessCustomers()
	{
		
		$apiEndpoint = $this->dwolla_url.'/customers';
		
		$headers = $this->_header();
		$email = fake()->unique()->safeEmail();
		$code = rand(1000,9999);
		$payload = [
			"firstName" => fake()->name(),
			"lastName" => fake()->name(),
			"email" => $email,
			"ipAddress" => "12",
			"type" => "business",
			"address1" => "S2t",
			"city" => "z2",
			"state" => "NY",
			"postalCode" => "11222",
			"dateOfBirth" => "1982-01-02",
			"ssn" => $code,
			"controller" => [
				"firstName" => "John",
				"lastName" => "Controller",
				"title" => "CEO",
				"ssn" => "6789",
				"dateOfBirth" => "1980-01-31",
				"address" => [
					"address1" => "1749 18th st",
					"address2" => "apt 12",
					"city" => "Des Moines",
					"stateProvinceRegion" => "IA",
					"postalCode" => "50266",
					"country" => "US"
				]
			],
			"businessClassification" => "9ed38153-7d6f-11e3-97e3-5404a6144203",
			"businessType"=> "llc",
			"businessName" => fake()->name(),
			"correlationId" => fake()->uuid(),
			"ein" => '25-5896325',
		];

		$curl = curl_init();

		curl_setopt($curl, CURLOPT_URL, $apiEndpoint);
		curl_setopt($curl, CURLOPT_POST, 1);
		curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($payload));
		curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

		$response = curl_exec($curl);

		if ($response === false) {
			echo 'Curl error: ' . curl_error($curl);
			dd(curl_error($curl));
		} else {
			curl_close($curl);
			$data = json_decode($response);
			dd($data);
			$newCreatedCustomerID = $this->getCustomersIdByEmail($email);
			dd($data, $newCreatedCustomerID);

			return $newCreatedCustomerID;
		}

		curl_close($curl);

	}

	public function certifyBeneficialOwnership()
	{
		$apiEndpoint = $this->dwolla_url.'/customers/bfb74b70-5d14-4626-aa07-9140eaf111d8/beneficial-ownership';
		
		$headers = $this->_header();
		
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
			echo 'Curl error: ' . curl_error($curl);
			dd(curl_error($curl));
		} else {
			curl_close($curl);
			$data = json_decode($response);
			dd($data);

			return $data;
		}

		curl_close($curl);
	}

	protected function getCustomersIdByEmail()
	{

		$apiEndpoint = $this->dwolla_url.'/customers?email=developer@scash.shop';
		
		$headers = $this->_header();

		$curl = curl_init();

		curl_setopt($curl, CURLOPT_URL, $apiEndpoint);
		curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

		$response = curl_exec($curl);

		if ($response === false) {
			echo 'Curl error: ' . curl_error($curl);
			dd(curl_error($curl));
		} else {
			$data = json_decode($response);
			if(!empty($data->_embedded) && !empty($data->_embedded->customers[0])){
				curl_close($curl);
				dd($data);
			} else {
				return null;
			}
		}
		curl_close($curl);

	}

	public function createBankSource()
	{

		$customerID = "0587f297-11f8-4a44-a29d-3d8c0d19c5b2";
		$apiEndpoint = $this->dwolla_url.'/customers/'.$customerID.'/funding-sources';
		$authorizationsJson = '{"_links":{"self":{"href":"https://api-sandbox.dwolla.com/on-demand-authorizations/1ee34bec-28b9-ee11-ac47-06f818744a9d",
			"type":"application/vnd.dwolla.v1.hal+json","resource-type":"on-demand-authorization"}},
			"bodyText":"I agree that all future payments to or facilitated by Scash will be processed by the Dwolla payment system 
			from the selected account above. In order to cancel this authorization, I will change my payment settings within my Scash account.",
			"buttonText":"Agree & Continue"}';
		
		$headers = $this->_header();

		$payload = [
			"_links" => $authorizationsJson,
			"routingNumber" => "222222226",
			"accountNumber" => "31926819",
			"bankAccountType" => "checking",
			"name" => "Shyam Sharma Checking"
		];

		$curl = curl_init();

		curl_setopt($curl, CURLOPT_URL, $apiEndpoint);
		curl_setopt($curl, CURLOPT_POST, 1);
		curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($payload));
		curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

		$response = curl_exec($curl);

		if ($response === false) {
			echo 'Curl error: ' . curl_error($curl);
			dd(curl_error($curl));
		} else {
			echo $response;
			$data = json_decode($response);
			dd($data);
		}

		curl_close($curl);

	}

	public function BankList()
	{
		$customerID = "87e4a055-e6ff-412d-9849-41124dc12c17";

		$apiEndpoint = $this->dwolla_url.'/customers/'.$customerID.'/funding-sources';
		
		$headers = $this->_header();

		$curl = curl_init();

		curl_setopt($curl, CURLOPT_URL, $apiEndpoint);
		curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

		$response = curl_exec($curl);

		if ($response === false) {
			echo 'Curl error: ' . curl_error($curl);
			dd(curl_error($curl));
		} else {
			$data = json_decode($response);
			if(!empty($data)){
				curl_close($curl);
				dd($data);
			} else {
				return null;
			}
		}
		curl_close($curl);

	}

	public function fundTransfer()
	{

		$DevFundID = "8f8aa95f-569d-498a-9ebb-d245093bf47b";
		$ShivangFundID = "a6b5f4d1-ed72-463b-b7b2-277e0e3b3ab8";
		$apiEndpoint = $this->dwolla_url.'/transfers';
		
		$headers = $this->_header();

		$payload = [
			"_links" => [
				"source" => [
					"href" => "https://api-sandbox.dwolla.com/funding-sources/".$DevFundID
				],
				"destination" => [
					"href" => "https://api-sandbox.dwolla.com/funding-sources/".$ShivangFundID
				]
			],
			"amount" => [
				"currency" => "USD",
				"value" => "1.00"
			],
		];
		

		$curl = curl_init();

		curl_setopt($curl, CURLOPT_URL, $apiEndpoint);
		curl_setopt($curl, CURLOPT_POST, 1);
		curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($payload));
		curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

		$response = curl_exec($curl);

		if ($response === false) {
			echo 'Curl error: ' . curl_error($curl);
			dd(curl_error($curl));
		} else {
			echo $response;
			$data = json_decode($response);
			dd($data);
		}

		curl_close($curl);

	}

	public function microDeposits()
	{

		$FundSource = "0c8b6bcd-5d5c-45f3-a38a-23d24ee3eb81";

		$apiEndpoint = $this->dwolla_url.'/funding-sources/'.$FundSource.'/micro-deposits';
		
		$headers = $this->_header();

		$payload = [
			"amount1" => [
				"value" => "0.03",
				"currency" => "USD",
			],
			"amount2" => [
				"value" => "0.09",
				"currency" => "USD",
			],
		];
		
		$curl = curl_init();

		curl_setopt($curl, CURLOPT_URL, $apiEndpoint);
		curl_setopt($curl, CURLOPT_POST, 1);
		curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($payload));
		curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

		$response = curl_exec($curl);

		if ($response === false) {
			echo 'Curl error: ' . curl_error($curl);
			dd(curl_error($curl));
		} else {
			echo $response;
			$data = json_decode($response);
			dd($data);
		}

		curl_close($curl);

	}

	public function createVirtualAccount()
	{

		$apiEndpoint = 'https://api-sandbox.dwolla.com/customers/3af3bd7c-6709-4d6d-ad4a-60c06d0b7042/funding-sources';
		
		$headers = $this->_header();

		$payload = [
			"name" => "Dev Shivang VAN One",
			"type" => "virtual",
			"bankAccountType" => "checking"
		];

		$curl = curl_init();

		curl_setopt($curl, CURLOPT_URL, $apiEndpoint);
		curl_setopt($curl, CURLOPT_POST, 1);
		curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($payload));
		curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

		$response = curl_exec($curl);

		if ($response === false) {
			echo 'Curl error: ' . curl_error($curl);
			dd(curl_error($curl));
		} else {
			$data = json_decode($response);
			if(!empty($data)){
				dd($data);
			} else {
				return null;
			}
		}
		curl_close($curl);

	}

	public function plaidDwollaFundingSource()
	{
		$payload = array(
			"plaidToken" => "processor-sandbox-a18bda30-8f0f-4903-bf8d-576f6e0672ed",
			"name" => "Tony Bank Saving 2222"
		);

		$headers = $this->_header();
		
		$apiEndpoint = $this->dwolla_url."/customers/f768e5e4-132c-4dbf-805f-e13871420391/funding-sources";
		
		$curl = curl_init();
		curl_setopt($curl, CURLOPT_URL, $apiEndpoint);
		curl_setopt($curl, CURLOPT_POST, 1);
		curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($payload));
		curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		
		$response = curl_exec($curl);
		
		if ($response === false) {
			echo 'Curl error: ' . curl_error($curl);
			dd(curl_error($curl));
		} else {
			curl_close($curl);
			$data = json_decode($response);
			if(!empty($data)){
				dd($data);
			}

			return $data;
		}
		
		curl_close($curl);
	}

	public function sendFcm()
	{
		
		$device_token = 'dAk_RpEqQZymVo0xI5umHK:APA91bEUwPtS_oVWXQTYV1rqMr9eNdoFjuAFHgyoFZ2_heLCU04FTlwiJhpr6_wSmSnKDBJXHrqc163KSkIEqf3DsVhzJQYl9oN6Fq_cqkyNr6zNMV1WizkXHYsvNbPh34X1URXzI6IV';
		$title = 'Testing FCM Title By Shivang';
		$message = 'Testing FCM Message By Shivang';
		$sendData = ['user_id' => 1, 'wallet_id' => 'JHGJHG87687GJHGJHg876y'];

		$data = $this->sendPushNotification($device_token, $title, $message, $sendData);
		return $data;
	}

	public function eventList()
	{

		$headers = $this->_header();
		
		$apiEndpoint = $this->dwolla_url."/events";
		
		$curl = curl_init();
		curl_setopt($curl, CURLOPT_URL, $apiEndpoint);
		curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		
		$response = curl_exec($curl);
		
		if ($response === false) {
			echo 'Curl error: ' . curl_error($curl);
			dd(curl_error($curl));
		} else {
			curl_close($curl);
			$data = json_decode($response);
			if(!empty($data)){
				dd($data);
			}

			return $data;
		}
		
		curl_close($curl);
	}

	public function eventRetrieve()
	{

		$headers = $this->_header();
		
		$apiEndpoint = $this->dwolla_url."/events/b0e4409a-1767-485b-9386-3c54f10bd821";
		
		$curl = curl_init();
		curl_setopt($curl, CURLOPT_URL, $apiEndpoint);
		curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		
		$response = curl_exec($curl);
		
		if ($response === false) {
			echo 'Curl error: ' . curl_error($curl);
			dd(curl_error($curl));
		} else {
			curl_close($curl);
			$data = json_decode($response);
			if(!empty($data)){
				dd($data);
			}

			return $data;
		}
		
		curl_close($curl);
	}

	public function transactionList()
	{

		$apiEndpoint = $this->dwolla_url.'/customers/f768e5e4-132c-4dbf-805f-e13871420391/transfers';
		// $apiEndpoint = $this->dwolla_url.'/customers/1e40e5e1-5d89-4183-951c-362fe0464556/transfers?correlationId=4e4cad73-3c06-431a-9ecb-622c930594a2';
		
		$headers = $this->_header();

		$curl = curl_init();

		curl_setopt($curl, CURLOPT_URL, $apiEndpoint);
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

	public function accountTransactionList()
	{

		// $apiEndpoint = $this->dwolla_url.'/accounts/4275e5d9-6956-49cc-9b86-fafe3407ec2d/transfers';
		$apiEndpoint = $this->dwolla_url.'/accounts/4275e5d9-6956-49cc-9b86-fafe3407ec2d/transfers?correlationId=53a91672-3cf7-46dd-978a-6fd4c406e5d2';
		
		$headers = $this->_header();

		$curl = curl_init();

		curl_setopt($curl, CURLOPT_URL, $apiEndpoint);
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


	public function transactionDetail()
	{

		$apiEndpoint = $this->dwolla_url.'/transfers/b2c5042c-050a-ef11-ac47-06f818744a9d';
		
		$headers = $this->_header();

		$curl = curl_init();

		curl_setopt($curl, CURLOPT_URL, $apiEndpoint);
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

	public function createWebhookSubscriptions()
	{
		$payload = array(
			"url"=> "https://devapi.scash.shop/api/v1/webhooks",
    		"secret" => "ScashWallet"
		);

		$headers = $this->_header();
		
		$apiEndpoint = $this->dwolla_url."/webhook-subscriptions";
		
		$curl = curl_init();
		curl_setopt($curl, CURLOPT_URL, $apiEndpoint);
		curl_setopt($curl, CURLOPT_POST, 1);
		curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($payload));
		curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		
		$response = curl_exec($curl);
		
		if ($response === false) {
			echo 'Curl error: ' . curl_error($curl);
			dd(curl_error($curl));
		} else {
			curl_close($curl);
			$data = json_decode($response);
			if(!empty($data)){
				dd($data);
			}
			return $data;
		}
		
		curl_close($curl);
	}

	public function deleteWebhookSubscriptions()
	{
		$apiEndpoint = $this->dwolla_url."/webhook-subscriptions/bb4f14bc-fe34-4638-b1eb-030af34f2489/webhooks?limit=50";

		$headers = $this->_header();

		$curl = curl_init($apiEndpoint);

		curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'GET');
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);

		$response = curl_exec($curl);

		if ($response === false) {
			echo 'Curl error: ' . curl_error($curl);
			dd(curl_error($curl));
		} else {
			curl_close($curl);
			$data = json_decode($response);
			if(!empty($data)){
				dd($data);
			}
			return $data;
		}
		
		curl_close($curl);
	}

	public function webhookSubscriptions()
	{

		$headers = $this->_header();
		
		$apiEndpoint = $this->dwolla_url."/webhook-subscriptions";
		
		$curl = curl_init();
		curl_setopt($curl, CURLOPT_URL, $apiEndpoint);
		curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		
		$response = curl_exec($curl);
		
		if ($response === false) {
			echo 'Curl error: ' . curl_error($curl);
			dd(curl_error($curl));
		} else {
			curl_close($curl);
			$data = json_decode($response);
			if(!empty($data)){
				dd($data);
			}
			return $data;
		}
		
		curl_close($curl);
	}

	public function webhooks(Request $request)
	{

		if($request->topic == 'customer_transfer_completed' && $request->resourceId){
			$resourceId = $request->resourceId;
			$transactionModel = Transaction::where('transaction_id', $resourceId)->first();

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

		if($request->topic == 'customer_transfer_failed' && $request->resourceId){
			$resourceId = $request->resourceId;
			$transactionModel = Transaction::where('transaction_id', $resourceId)->first();
			$transactionModel->status = Transaction::STATUS_FAILED;
			$transactionModel->save();

			return 'done';
		}

		if($request->topic == 'customer_transfer_cancelled' && $request->resourceId){
			$resourceId = $request->resourceId;
			$transactionModel = Transaction::where('transaction_id', $resourceId)->first();
			$transactionModel->status = Transaction::STATUS_CANCELLED;
			$transactionModel->save();

			return 'done';
		}

	}

	public function removeBank()
	{
		$fundingSource = 'e630ae79-ba9a-4ed5-a093-7b32fffc97fa';
		$endpoint = 'https://api-sandbox.dwolla.com/funding-sources/'.$fundingSource;

		$data = array(
			'removed' => true
		);

		$headers = $this->_header();

		$curl = curl_init($endpoint);

		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'POST');
		curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));
		curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);

		$response = curl_exec($curl);
		if ($response === false) {
			echo 'Curl error: ' . curl_error($curl);
			dd(curl_error($curl));
		} else {
			echo $response;
			$data = json_decode($response);
			dd($data);
		}

		curl_close($curl);
	}

	public function businessClassifications()
	{
		$apiEndpoint = $this->dwolla_url.'/business-classifications';
		
		$headers = $this->_header();

		$curl = curl_init();

		curl_setopt($curl, CURLOPT_URL, $apiEndpoint);
		curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

		$response = curl_exec($curl);

		if ($response === false) {
			return 'Curl error: ' . curl_error($curl);
		} else {
			$data = json_decode($response);
			
			$arr = [];
			dd($data);
			foreach($data->_embedded->{"business-classifications"} as $k => $single){
				foreach($single->_embedded->{"industry-classifications"} as $classify){
					$arr[] = [
						'id' => $classify->id,
						'name' => $classify->name,
						'parent_name' => $single->name
					];
				}
			}
			return $arr;

		}

		curl_close($curl);
	}

	public function testUploadDocument(){
		return view('plaid.dwolla-document');
	}

	public function uploadDocument(Request $request)
	{

		$apiEndpoint = $this->dwolla_url."/customers/bfb74b70-5d14-4626-aa07-9140eaf111d8/documents";
		
		$headers = $this->_header();

		$path = $request->file('document')->store('uploads');
		$completePath = Storage::path($path);

		$data = [
			'documentType' => 'passport',
			'file' => new \CURLFile($completePath)
		];

		// dd($data, $completePath);
		
		// cURL request
		$curl = curl_init();
		
		curl_setopt($curl, CURLOPT_URL, $apiEndpoint);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_POST, true);
		curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
		curl_setopt($curl, CURLOPT_HTTPHEADER, [
			'Authorization: Bearer NVX75hX0X3awUBGrTqbRnQkoN6FFqEmMJB4Q4WHzj2YBbFf0c8',
			'Accept: application/vnd.dwolla.v1.hal+json',
			'Cache-Control: no-cache',
			'Content-Type: multipart/form-data; boundary=----WebKitFormBoundary7MA4YWxkTrZu0gW'
		]);
		
		
		$response = curl_exec($curl);

		if(Storage::exists($path)){
			Storage::delete($path);
		}
		
		if ($response === false) {
			echo 'Curl error: ' . curl_error($curl);
			dd(curl_error($curl));
		} else {
			$data = json_decode($response);
			dd($data);
		}
		
		curl_close($curl);

	}

	public function masterAccount()
	{
		// dd($request->image);

		$endpoint = $this->dwolla_url;
		
		$headers = $this->_header();

		$curl = curl_init($endpoint);

		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'GET');
		curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);

		$response = curl_exec($curl);
		if ($response === false) {
			echo 'Curl error: ' . curl_error($curl);
			dd(curl_error($curl));
		} else {
			echo $response;
			$data = json_decode($response);
			dd($data);
		}

		curl_close($curl);
	}

}

