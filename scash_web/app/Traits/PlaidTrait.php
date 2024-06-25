<?php 

namespace App\Traits;

trait PlaidTrait
{

	private $client_id;
	private $secret;
	private $plaid_url;
	private $apiClient;
	private $webhookUrl;
	private $redirectUri;
    
    public function __construct()
	{

		$this->client_id = config('services.plaid.client_id');
		$this->secret = config('services.plaid.secret');
		$this->plaid_url = config('services.plaid.url');
		$this->webhookUrl = "https://webhook.example.com";
		$this->redirectUri = "http://merchant.localhost:8000/token-data";

	}

	public function webLinkToken($userModel)
	{
		$url = $this->plaid_url."/link/token/create";

		$uniqueUserId = $userModel->id;
		$uniqueUserPNo = $userModel->country_code.''.$userModel->phone_number;
		$client_name = $userModel->first_name.' '.$userModel->last_name;

		$data = array(
			"client_id" => $this->client_id,
			"secret" => $this->secret,
			"client_name" => 'Scash',
			"user" => [
				"client_user_id" => "$uniqueUserId",
				"phone_number" => $uniqueUserPNo,
				"email_address" => $userModel->email
			],
			"products" => ['auth','signal'],
			"required_if_supported_products" => ["identity"],
			"country_codes" => ["US"],
			"language" => "en",
			"webhook" => $this->webhookUrl,
			"redirect_uri" => $this->redirectUri,
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
			$data = json_decode($response);

			return $data->link_token;
		}

		curl_close($curl);
	}

	public function linkToken($userModel)
	{
		$url = $this->plaid_url."/link/token/create";

		$uniqueUserId = $userModel->id;
		$uniqueUserPNo = $userModel->country_code.''.$userModel->phone_number;
		$client_name = $userModel->first_name.' '.$userModel->last_name;

		$data = array(
			"client_id" => $this->client_id,
			"secret" => $this->secret,
			"client_name" => 'Scash',
			"user" => [
				"client_user_id" => "$uniqueUserId",
				"phone_number" => $uniqueUserPNo,
				"email_address" => $userModel->email
			],
			"products" => ['auth','signal'],
			"required_if_supported_products" => ["identity"],
			"country_codes" => ["US"],
			"language" => "en",
			"android_package_name" => "com.scash.cbl",
			"webhook" => $this->webhookUrl,
			"redirect_uri" => $this->redirectUri,
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
			$data = json_decode($response);

			return $data->link_token;
		}

		curl_close($curl);
	}

	public function accessToken($public_token)
	{

		$requestData = array(
			"client_id" => $this->client_id,
			"secret" => $this->secret,
			"public_token" => $public_token
		);

		$url = $this->plaid_url."/item/public_token/exchange";

		$curl = curl_init($url);
		curl_setopt($curl, CURLOPT_POST, 1);
		curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($requestData));
		curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

		$response = curl_exec($curl);

		if ($response === false) {
			return curl_error($curl);
		} else {
			curl_close($curl);
			$data = json_decode($response);
            return $this->sendResponse($data, 'Public Token fetched successfully.');
		}

		curl_close($curl);

	}

	public function accessTokenGet($public_token)
	{

		$requestData = array(
			"client_id" => $this->client_id,
			"secret" => $this->secret,
			"public_token" => $public_token
		);

		$url = $this->plaid_url."/item/public_token/exchange";

		$curl = curl_init($url);
		curl_setopt($curl, CURLOPT_POST, 1);
		curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($requestData));
		curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

		$response = curl_exec($curl);

		if ($response === false) {
			return curl_error($curl);
		} else {
			curl_close($curl);
			$data = json_decode($response);
            return $data;
		}

		curl_close($curl);

	}

	public function getAccount($access_token)
	{
		$payload = array(
			"client_id" => $this->client_id,
			"secret" => $this->secret,
			"access_token" => $access_token
		);
		
		$url = $this->plaid_url."/accounts/get";
		
		$curl = curl_init($url);
		curl_setopt($curl, CURLOPT_POST, 1);
		curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($payload));
		curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		
		$response = curl_exec($curl);
		
		if ($response === false) {
			return curl_error($curl);
		} else {
			curl_close($curl);
			$data = json_decode($response);
			return $data;
		}
		
		curl_close($curl);
	}

    public function plaidProcessorDwolla($access_token, $account_id)
	{
		$payload = array(
			"client_id" => $this->client_id,
			"secret" => $this->secret,
			"access_token" => $access_token,
			"account_id" => $account_id,
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
			return curl_error($curl);
		} else {
			curl_close($curl);
			$data = json_decode($response);

			return $data;
		}
		
		curl_close($curl);

	}

	public function itemGet($access_token)
	{
		$payload = array(
			"client_id" => $this->client_id,
			"secret" => $this->secret,
			"access_token" => $access_token,
		);
		
		$url = $this->plaid_url."/item/get";
		
		$curl = curl_init($url);
		curl_setopt($curl, CURLOPT_POST, 1);
		curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($payload));
		curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		
		$response = curl_exec($curl);
		
		if ($response === false) {
			return curl_error($curl);
		} else {
			curl_close($curl);
			$data = json_decode($response);
			return $data;
		}
		
		curl_close($curl);

	}

	public function institutionsGet($institute_id)
	{
		$payload = array(
			"client_id" => $this->client_id,
			"secret" => $this->secret,
			"institution_id" => $institute_id,
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
			return curl_error($curl);
		} else {
			curl_close($curl);
			$data = json_decode($response);
			return $data;
		}
		
		curl_close($curl);
	}

    

}