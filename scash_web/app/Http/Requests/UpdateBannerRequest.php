<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateBannerRequest extends FormRequest
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
        $rules = [
            'banner_image' => 'nullable',
            'start_date' => 'required',
            'url' => 'nullable',
            'end_date' => 'required',
            'type' => 'required',
        ];

        if ( $this->request->get('type') == 2 ) {
            $rules['user_id'] = 'required';
        }

        return $rules;
        
    }
}
