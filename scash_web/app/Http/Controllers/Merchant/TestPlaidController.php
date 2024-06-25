<?php

namespace App\Http\Controllers\Merchant;

use App\Http\Controllers\Controller;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class TestPlaidController extends Controller
{

	private $client_id;
	private $secret;
	private $plaid_url;
	private $linkToken;
	private $publicToken;
	private $access_token;

	public function __construct()
	{
		$this->client_id = config('services.plaid.client_id');
		$this->secret = config('services.plaid.secret');
		$this->plaid_url = config('services.plaid.url');
		$this->linkToken = 'link-sandbox-8ff0fd76-9e79-4525-ae16-1eff12e0fc0c';
		$this->publicToken = 'public-sandbox-5f70c23d-8fa5-4caa-9a37-41228c4c9beb';
		$this->access_token = 'access-sandbox-0b741149-c7cb-4133-8748-aa2be3d7e472';

		// link_token => public_token => access_token
		// link_token => To link plaid
		// public_token => You get, After connect with bank & card details
		// access_token => help to work with Transaction, Auth, Balance

	}

	public function createToken()
	{

		$uniqueUserId = "3";
		$uniqueUserPNo = "+1 415 5550125";
		$webhookUrl = "https://webhook.example.com";
		$redirectUri = "http://merchant.localhost:8000/token-data";

		$url = $this->plaid_url."/link/token/create";

		$data = array(
			"client_id" => $this->client_id,
			"secret" => $this->secret,
			"user" => [
				"client_user_id" => '12',
				"phone_number" => $uniqueUserPNo,
				"email_address" => "tan@gmail.com"
			],
			"products" => ['auth','signal'],
			"required_if_supported_products" => ["identity"],
			"client_name" => "Client One",
			"country_codes" => ["US"],
			"language" => "en",
			"webhook" => $webhookUrl,
			"redirect_uri" => $redirectUri,
			"auth" => [
				"automated_microdeposits_enabled" => true
			]
		);

		$options = array(
			CURLOPT_URL => $url,
			CURLOPT_POST => true,
			CURLOPT_POSTFIELDS => json_encode($data),
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_HTTPHEADER => array('Content-Type: application/json'),
		);

		$curl = curl_init();
		curl_setopt_array($curl, $options);

		$response = curl_exec($curl);

		if ($response === false) {
			echo 'Curl error: ' . curl_error($curl);
			return curl_error($curl);
		} else {
			echo $response;
			$data = json_decode($response);
			return $data->link_token;
		}

		curl_close($curl);
	}

	public function webView(Request $request)
	{		
		return view('plaid.web-view');
	}

	public function publicTokenExchange()
	{

		$requestData = array(
			"client_id" => $this->client_id,
			"secret" => $this->secret,
			"public_token" => $this->publicToken
		);

		$url = $this->plaid_url."/item/public_token/exchange";

		$curl = curl_init($url);
		curl_setopt($curl, CURLOPT_POST, 1);
		curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($requestData));
		curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
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

		// $responce = [
		// 	 +"access_token": "access-sandbox-0b741149-c7cb-4133-8748-aa2be3d7e472"
		//   +"item_id": "q3Jg931vbZtJg5a5bDjGHzXLvNoooLfdxDenV"
		//   +"request_id": "Nz2AlWbw82pzQpn"
		// ];
	}

	public function transactionsGet()
	{
		$payload = array(
			"client_id" => $this->client_id,
			"secret" => $this->secret,
			"access_token" => $this->access_token,
			"start_date" => "2018-01-01",
			"end_date" => "2018-02-01",
			"options" => array(
				"count" => 250,
				"offset" => 100
			)
		);
		
		$url = $this->plaid_url."/transactions/get";
		
		$curl = curl_init($url);
		curl_setopt($curl, CURLOPT_POST, 1);
		curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($payload));
		curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
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

	public function authGet()
	{
		$payload = array(
			"client_id" => $this->client_id,
			"secret" => $this->secret,
			"access_token" => $this->access_token,
		);
		
		$url = $this->plaid_url."/auth/get";
		
		$curl = curl_init($url);
		curl_setopt($curl, CURLOPT_POST, 1);
		curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($payload));
		curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
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

	public function recipientCreate()
	{
		$payload = array(
			"client_id" => $this->client_id,
			"secret" => $this->secret,
			"name" => 'Radhika',
			"bacs" => [
				"account" => '26207729',
				"sort_code" => '560029',
			]
		);
		
		$url = $this->plaid_url."/payment_initiation/recipient/create";
		
		$curl = curl_init($url);
		curl_setopt($curl, CURLOPT_POST, 1);
		curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($payload));
		curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		
		$response = curl_exec($curl);
		
		if ($response === false) {
			echo 'Curl error: ' . curl_error($curl);
			dd(curl_error($curl));
		} else {
			curl_close($curl);
			$data = json_decode($response);
			if(!empty($data)){
				return $data->recipient_id;
			}

			return $data;
		}
		
		curl_close($curl);

		// $resipentId = "recipient-id-sandbox-284a137f-0bba-4b67-8468-c34172ab80fb";
	}

	public function paymentCreate()
	{
		$payload = array(
			"client_id" => $this->client_id,
			"secret" => $this->secret,
			"recipient_id" => "recipient-id-sandbox-284a137f-0bba-4b67-8468-c34172ab80fb",
			"amount" => [
				"currency" => 'GBP',
				"value" => 150.0,
			],
			"reference" => "testpayment"
		);
		
		$url = $this->plaid_url."/payment_initiation/payment/create";
		
		$curl = curl_init($url);
		curl_setopt($curl, CURLOPT_POST, 1);
		curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($payload));
		curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
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

		// $paymentId = "payment-id-sandbox-afbd4a2d-00bb-4943-bdc7-a94a0ea0d0cd";
	}

	public function paymentsList()
	{
		$payload = array(
			"client_id" => $this->client_id,
			"secret" => $this->secret,
		);
		
		$url = $this->plaid_url."/payment_initiation/payments/list";
		
		$curl = curl_init($url);
		curl_setopt($curl, CURLOPT_POST, 1);
		curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($payload));
		curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
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

	public function plaidProcessorDwolla()
	{
		$payload = array(
			"client_id" => $this->client_id,
			"secret" => $this->secret,
			"access_token" => $this->access_token,
			"account_id" => "vxPjk3kJvGhLyPN5E94vI5gBRoyDMdtq1vRxA",
			"processor" => "dwolla",
		);
		
		$url = $this->plaid_url."/processor/token/create";
		
		$curl = curl_init($url);
		curl_setopt($curl, CURLOPT_POST, 1);
		curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($payload));
		curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
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

		//$processor_token = "processor-sandbox-ae7488d0-a8e3-45a9-af62-550d31b29ae3";
	}

	public function accountsGet()
	{
		$payload = array(
			"client_id" => $this->client_id,
			"secret" => $this->secret,
			"access_token" => $this->access_token,
			// "account_ids" => ["KXwnZ4gzzWCLALpRpBkei8e3mLxWX1fR3Z48g"]
		);
		
		$url = $this->plaid_url."/accounts/get";
		
		$curl = curl_init($url);
		curl_setopt($curl, CURLOPT_POST, 1);
		curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($payload));
		curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
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

	public function balanceGet()
	{
		$payload = array(
			"client_id" => $this->client_id,
			"secret" => $this->secret,
			"access_token" => $this->access_token,
		);
		
		$url = $this->plaid_url."/accounts/balance/get";
		
		$curl = curl_init($url);
		curl_setopt($curl, CURLOPT_POST, 1);
		curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($payload));
		curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
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

		// $paymentId = "payment-id-sandbox-afbd4a2d-00bb-4943-bdc7-a94a0ea0d0cd";
	}

	public function itemGet()
	{
		$payload = array(
			"client_id" => $this->client_id,
			"secret" => $this->secret,
			"access_token" => $this->access_token,
		);
		
		$url = $this->plaid_url."/item/get";
		
		$curl = curl_init($url);
		curl_setopt($curl, CURLOPT_POST, 1);
		curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($payload));
		curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
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
				dd($data->item->institution_id);
			}

			return $data;
		}
		
		curl_close($curl);

	}

	public function institutionsGet()
	{
		$payload = array(
			"client_id" => $this->client_id,
			"secret" => $this->secret,
			"institution_id" => "ins_5",
			"country_codes" => ["US", "GB", "ES", "NL", "FR", "IE", "CA", "DE", "IT", "PL", "DK", "NO", "SE", "EE", "LT", "LV", "PT", "BE"]
		);
		
		$url = $this->plaid_url."/institutions/get_by_id";
		
		$curl = curl_init($url);
		curl_setopt($curl, CURLOPT_POST, 1);
		curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($payload));
		curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
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
				dd($data->item->institution_id);
			}

			return $data;
		}
		
		curl_close($curl);
	}


}
