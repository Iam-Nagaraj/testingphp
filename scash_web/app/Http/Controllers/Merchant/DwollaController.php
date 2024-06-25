<?php

namespace App\Http\Controllers\Merchant;

use App\Http\Controllers\Controller;
use App\Models\DwollaCustomer;
use App\Traits\DwollaTrait;
use DwollaSwagger\Configuration;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;

class DwollaController extends Controller
{
	use DwollaTrait;

	private $client_id;
	private $secret;
	private $dwolla_url;
	private $apiClient;
	private $accessToken;

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

	public function traitAccessToken()
	{

		$appToken = $this->createAccessToken();;

		return $appToken['access_token'];

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

	public function traitCreateCustomers($accessToken, $userData, $request)
	{
		return $this->createCustomers($accessToken, $userData, $request);
	}

	public function traitCreateSoloBusinessCustomer($accessToken, $userData, $request)
	{
		return $this->createSoloBusinessCustomer($accessToken, $userData, $request);
	}

	public function traitGetCustomersIdByEmail($accessToken, $email)
	{
		return $this->getCustomersIdByEmail($accessToken, $email);
	}

	public function traitCreateCustomerDocument($accessToken, $request, $dwollaCustomerId)
	{
		return $this->createCustomerDocument($accessToken, $request, $dwollaCustomerId);
	}

	public function traitCertifyBeneficialOwnership($accessToken, $request, $dwollaCustomerId)
	{
		return $this->certifyBeneficialOwnership($accessToken, $request, $dwollaCustomerId);
	}

	public function traitCreateNonSoloBusinessCustomer($accessToken, $userData, $request)
	{
		return $this->createNonSoloBusinessCustomer($accessToken, $userData, $request);
	}

	public function traitUpdateCustomer($accessToken, $userData, $request, $customerId)
	{
		return $this->updateCustomer($accessToken, $userData, $request, $customerId);
	}

	public function traitBankList($accessToken, $customerID)
	{
		return $this->BankList($accessToken, $customerID);
	}

	public function traitfundTransfer($access_token, $request)
	{
		return $this->fundTransfer($access_token, $request);
	}

	public function traitsameDayFundTransfer($access_token, $request)
	{
		return $this->sameDayFundTransfer($access_token, $request);
	}

	public function traitLatestTransactionData($access_token)
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

	public function traitSearchTransactionData($access_token, $request)
    {
        $DwollaCustomer = DwollaCustomer::where('user_id', Auth::user()->id)->first();
        $customerID = $DwollaCustomer->customer_id;
        $TransferList = $this->searchTransferList($access_token, $customerID, $request->correlationId);
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

	public function traitBankBalance($accessToken, $fundingSourceID)
	{
		return $this->BankBalance($accessToken, $fundingSourceID);
	}



}

