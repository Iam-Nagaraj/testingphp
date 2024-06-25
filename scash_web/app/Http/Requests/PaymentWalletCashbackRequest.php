<?php

namespace App\Http\Requests;

use App\Rules\CheckBalanceRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class PaymentWalletCashbackRequest extends FormRequest
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
            'payment_method' => 'required', // 1=wallet,2=cashback
            'paid_to' => 'required',
            'amount' =>[ 'required','numeric','min:1',new CheckBalanceRule]
             ];
    }

    public function messages(): array
    {
        return [
            'amount.min' =>'The amount must be minimun $1'
             ];
    }

    // public function failedValidation(Validator $validator)
    // {
    //     $data_error = [];
    //     $error = $validator->errors()->all(); #if validation fail print error messages
    //     foreach ($error as $key => $errors):
    //         $data_error['status'] = 400;
    //         $data_error['message'] = $errors;
    //     endforeach;
    //     //write your bussiness logic here otherwise it will give same old JSON response
    //     throw new HttpResponseException(response()->json($data_error));

    // }
}
