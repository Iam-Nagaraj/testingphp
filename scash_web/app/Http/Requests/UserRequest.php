<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserRequest extends FormRequest
{
	/**
	 * Determine if the user is authorized to make this request.
	 */
	public function authorize(): bool
	{
		return true;
	}

	/**
	 * Get the validation rules that apply to the request.
	 *
	 * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
	 */
	public function rules(): array
	{
		$request = [
			// 'image' => 'required',
			'first_name' => [
				'required',
				'string',
				'max:150'
			],
			'last_name' => [
				'required',
				'string',
				'max:150'
			],
			'phone_number' => 'required|unique:users,phone_number,' . $this->id,
			'country_code' => 'required',
			'email' => 'required|email:rfc,dns|unique:users,email,' . $this->id,
			'password' => 'nullable',
			'date_of_birth' => 'required',
			'zipcode' => 'nullable',
			'referal_code' => 'nullable',
			'ssn' => 'required',
		];

		if ($this->id) {
			$request = array_merge($request, [
				'address' => 'required',
				'address_2' => 'required',
				'state' => 'required',
				'city' => 'required',
			]);
		} else {
			$request = array_merge($request, [
				'is_agree' => 'required'
			]);
		}
		return $request;
	}
}
