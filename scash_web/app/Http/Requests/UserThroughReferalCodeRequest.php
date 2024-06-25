<?php

namespace App\Http\Requests;

use App\Models\UserReferalCode;
use Illuminate\Foundation\Http\FormRequest;

class UserThroughReferalCodeRequest extends FormRequest
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
		return [
			'referal_code_id' => ['nullable', function ($attribute, $value, $error) {
				if (UserReferalCode::fetchOne(['referal_code' => $value]) == false) {
					$error('The :attribute is not exists.');
				}
			}]
		];
	}
}
