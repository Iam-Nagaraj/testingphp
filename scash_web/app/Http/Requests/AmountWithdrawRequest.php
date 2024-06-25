<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AmountWithdrawRequest extends FormRequest
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
            "amount" => 'required|numeric|gt:0',
            "destination_id" => 'required',
            'pin' => 'required||numeric|between:1111,9999',
        ];
    }

    public function messages(){
        return [
            "destination_id.required" => __('Please Select Bank Account.'),
        ];
    }
}
