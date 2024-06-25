<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class WebRegisterRequest extends FormRequest
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
            'registration_type' => 'required',
            'first_name' => 'required',
            'last_name' => 'required',
            'phone_number' => 'required',
            'email' => 'required',
            'password' => 'required',
            'address' => 'required',
            'state_long_name' => 'required',
            'city' => 'required',
            'state' => 'required',
            'country' => 'required',
            'latitude' => 'required',
            'longitude' => 'required',
            'zip_code' => 'required',
            'dob' => 'required',
            'business_name' => 'required',
            'logo' => 'required',
            'business_proff' => 'required',
            'leagal_name' => 'required',
            'business_address' => 'required',
            'business_state_long_name' => 'required',
            'business_city' => 'required',
            'business_state' => 'required',
            'business_country' => 'required',
            'business_zip_code' => 'required',
            'business_category' => 'required',
            'business_sub_category' => 'required',
            'privacy_policy' => 'required',
            // 'verification_document' => 'required',
            // 'document_type' => 'required',
        ];
    }
}
