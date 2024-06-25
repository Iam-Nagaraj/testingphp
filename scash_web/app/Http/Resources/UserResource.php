<?php

namespace App\Http\Resources;

use App\Models\Wallet;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
	/**
	 * Transform the resource into an array.
	 *
	 * @return array<string, mixed>
	 */
	public function toArray(Request $request): array
	{
		return [
			'id' => $this->id,
			'first_name' => $this->first_name,
			'last_name' => $this->last_name,
			'email' => $this->email,
			'date_of_birth' => $this->date_of_birth,
			'status' => $this->status,
			'is_pin_generated' => $this->is_pin_generated??false,
			'phone_number' => strval($this->phone_number),
			'country_code' => strval($this->country_code),
			'token' => isset($this->token) && is_string($this->token)?$this->token:"",
			'referalCode' => isset($this->referalCode)?$this->referalCode:"",
			'referalAmount' => isset($this->referalAmount)?$this->referalAmount:"",
			'supportMail' => isset($this->supportMail)?$this->supportMail:"",
			'image' => isset($this->media->first()->file) ? new UploadFileResource($this->media->first()->file) : "",
			'address' => isset($this->address) ? new UserAddressResource($this->address) : (object)[],
			'is_email_verified' => $this->is_email_verified,
			'is_phone_number_verified' => $this->is_phone_number_verified,
			'wallet' => isset($this->wallet) ? $this->wallet:$this->createWallet($this->id)
		];
	}
	public function createWallet($user_id)
	{
		$wallet=new Wallet;
		$wallet->user_id = $user_id;
		$wallet->balance = 0;
		$wallet->save();
		return $wallet;
	}

}
