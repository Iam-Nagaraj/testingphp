<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\Auth;

class CheckBalanceRule implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $payment_method = request('payment_method');
       
        $user = Auth::user();
        if ($payment_method == 1 ) {
            $balance = isset($user->wallet) && $user->wallet->balance ? $user->wallet->balance :  0;
            if($balance < $value){
                $fail('The amount must be less than or equal to your wallet balance.');
            }
        }
        if ($payment_method == 2) {
            $balance = isset($user->wallet) && $user->wallet->cashback_balance ? $user->wallet->cashback_balance :  0;
            if( $balance < $value){
                $fail('The amount must be less than or equal to your cashback balance.');
            }
        }
       
    }
}
