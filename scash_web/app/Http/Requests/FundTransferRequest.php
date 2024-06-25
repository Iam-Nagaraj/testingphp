<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class FundTransferRequest extends FormRequest
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
            'source_id' => 'required',
            'destination_id' => 'required',
            "amount" => 'required',
            "tip" => 'required',
            "tax" => 'required',
            "to_user_id" => 'required',
            "from_user_id" => 'required',
        ];
    }
}
