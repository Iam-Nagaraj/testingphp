<?php

namespace App\Http\Resources;

use App\Models\Verification;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserVerifyResource extends JsonResource
{
	protected Verification $verificationService;
	protected $resourceData;
	// Constructor
	public function __construct($resourceData)
	{
		$this->verificationService = new Verification();
		$this->resourceData = $resourceData;
	}

	// Getter methods (optional)
	public function isEmailVerified(array $input)
	{
		return $this->verificationService->fetchOne(['email' => $input['email'], 'status' => getConfigConstant('OTP_VERIFIED')])?1:0;
	}

	public function isPhoneNumberVerified(array $input)
	{
		return $this->verificationService->fetchOne(['country_code' => $input['country_code'], 'phone_number' => $input['phone_number'], 'status' => getConfigConstant('OTP_VERIFIED')])?1:0;
	}



	/**
	 * Transform the resource into an array.
	 *
	 * @return array<string, mixed>
	 */
	public function toArray(Request $request = null): array
	{
		$resource = $this->resourceData;
		
		return [
			'is_email_verified' => isset($resource['email'])?$this->isEmailVerified($resource):0,
			'is_phone_number_verified' => isset($resource['phone_number'])?$this->isPhoneNumberVerified($resource):0
		];
	}
}
