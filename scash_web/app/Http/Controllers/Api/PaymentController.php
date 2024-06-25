<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\PaymentWalletCashbackRequest;
use App\Models\Cashback;
use App\Models\CashbackRule;
use App\Models\BusinessCategory;
use App\Models\Transaction;
use App\Models\Wallet;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PaymentController extends Controller
{
    public function pay(PaymentWalletCashbackRequest $request)
    {

        try {
            DB::beginTransaction();
            $user = Auth::user();
            $paid_to = User::where('id',$request->paid_to)->firstOrFail();
            $payment_method = $request->payment_method; // 1 for wallet, 2 for cashback
            $amount = $request->amount;
            $wallet = Wallet::where('user_id',$user->id)->first();

            $trasaction = Transaction::create([
                'amount' => $request->amount,
                'type' => $payment_method,
                'status' => 1,
                'sender_user_id' => $user->id,
                'receiver_user_id' => $paid_to->id,
            ]);
            $amount = (double) $amount;
            $cashback = 0;
            $cashbackRule = CashbackRule::where('user_id', $paid_to->id)->first();
            $business_category= BusinessCategory::select('id')->where('name',$user->business_type)->first();
            $cashback_business_type    = Cashback::where('business_category_id',$business_category->id ?? '')->first();
            $cashback_percentage = ($paid_to->role_id ==  getConfigConstant('MERCHANT_ROLE_ID')) ? $cashback_business_type->percentage ?? 0 : 0;
            if(isset($cashbackRule)){
              $cashback_percentage = $cashbackRule->standard_cashback_percentage;
              if( ($amount >= $cashbackRule->ts_total_amount) && ($cashbackRule->ts_status == CashbackRule::RULEACTIVE)){ // for increase transactional amount
                $cashback_percentage = $cashback_percentage + $cashbackRule->ts_extra_percentage;
              }
              $wallet->rp_cashback_balance = $wallet->rp_cashback_balance + $amount;
              if( ($wallet->rp_cashback_balance >= $cashbackRule->rp_total_amount) && ($cashbackRule->rp_status == CashbackRule::RULEACTIVE)){ // for repeat transactional amount
                $cashback_percentage = $cashback_percentage +  $cashbackRule->rp_extra_percentage;
                $wallet->rp_cashback_balance = $wallet->rp_cashback_balance - $cashbackRule->rp_total_amount;
              }
            }
          $cashback = round(($amount * $cashback_percentage)/100,2);
          if($payment_method == 1){
              $wallet->balance =  $wallet->balance - $amount ;
              $wallet->cashback_balance  = $wallet->cashback_balance + $cashback;
          }else{
            $wallet->cashback_balance =  $wallet->cashback_balance - $amount + $cashback ;
          }
            $wallet->save();
            $data['wallet'] = $wallet;
            $data['cashback'] = $cashback ;
            DB::commit();
           return  $this->sendResponse($data, 'Transaction Done.');
        } catch (\Exception $ex) {
            DB::rollBack();
            return $this->sendError([], $ex->getMessage());
        }
    }
}
