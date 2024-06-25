<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class WebMerchantRequest extends FormRequest
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
            'name' => [
				'required',
				'string',
				'max:150'
			],
            'phone_number' => 'required',
            // 'phone_number' => 'required|unique:users,phone_number,'. $this->id,
            'email' => 'required|email:rfc,dns',
            // 'email' => 'required|email:rfc,dns|unique:users,email,'. $this->id,
            'password' => ['required', 'min:8', 'regex:/[a-z]/', 'regex:/[A-Z]/', 'regex:/[0-9]/', 'regex:/[@$!%*#?&]/'],
            'goverment_id' => 'required',
            'logo' => 'required',
            'business_proff' => 'required',
            'address' => 'required',


        ];
    }
}
