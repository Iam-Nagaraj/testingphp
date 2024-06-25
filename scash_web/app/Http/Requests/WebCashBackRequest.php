<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class WebCashBackRequest extends FormRequest
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
            'business_category_id' => 'required|exists:business_category,id|unique:cashbacks,business_category_id,' . $this->cashback_id,
            'percentage' => 'required|numeric|between:0,99.99',
        ];
    }

    public function messages(): array
{
    return [
        'business_category_id.required' => 'The business category is required.',
        'business_category_id.exists' => 'The selected business category is invalid.',
        'business_category_id.unique' => 'The selected business category has already been used.',
        'percentage.required' => 'The percentage is required.',
        'percentage.numeric' => 'The percentage must be a number.',
        'percentage.between' => 'The percentage must be between 0 and 99.99.',
    ];
}
}
