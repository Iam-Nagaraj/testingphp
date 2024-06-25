<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MerchantProfileRequest extends FormRequest
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
            'first_name' => 'required',
            'last_name' => 'required',
			'address' => 'required',
			'line_1' => 'required',
			'latitude' => 'required',
			'longitude' => 'required',
			'state' => 'required',
			'city' => 'required',
			'postal_code' => 'required',
            'business_name' => 'required',
            'leagal_name' => 'required',
            'business_category' => 'required',
            'about_business' => 'required',
        ];
    }

}
