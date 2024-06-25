<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ReferralTransaction;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TransactionController extends Controller
{
    public function list(Request $request)
    {
        try {
            $user_id = Auth::user()->id;
            $data = Transaction::where('sender_user_id',$user_id)->orWhere('receiver_user_id',$user_id)->select('id','amount','sender_user_id','receiver_user_id')->with('sender','receiver')->get();
            return $this->sendResponse($data, 'Transaction List.');
        } catch (\Exception $ex) {
            return $this->sendError([], $ex->getMessage());
        }
    }

    public function myReferalList()
    {
        try {
            $user_id = Auth::user()->id;
            $data = ReferralTransaction::where('from',$user_id)->with('sender')->paginate(10);
            return $this->sendResponse($data, 'Referral transaction List.');
        } catch (\Exception $ex) {
            return $this->sendError([], $ex->getMessage());
        }
    }
}
