<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\MerchantResource;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use App\Http\Resources\MerchantDetailResource;
use App\Http\Resources\MerchantListResource;
use App\Models\CashbackRule;
use App\Models\Transaction;
use App\Models\Wallet;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class MerchantController extends Controller
{
    protected User $userService;

    public function __construct(User $userService)
    {
        $this->userService = $userService;
    }

    public function index(Request $request)
    {
        try {
            $latitude = $request->latitude??0;
            $longitude = $request->longitude??0;

            $detail = $this->userService->nearByFetch($latitude, $longitude);

            return $this->sendResponse(new MerchantResource($detail), 'Merchant Fetched successfully.');
        } catch (Exception $ex) {
            return $this->sendError([], $ex->getMessage());
        }
    }

    public function detail(Request $request)
    {
        try {
            $detail = $this->userService->getMerchant($request->id);
            $merchantData = new MerchantDetailResource($detail);
            $data = json_decode(json_encode($merchantData), true);

            $cashbackRule = CashbackRule::where('user_id', $request->id)->first();
            $FromUserWallet = Wallet::where('user_id', Auth::user()->id)->first();
            if( !empty($cashbackRule) && !empty($cashbackRule->rp_total_amount) ){
                if($FromUserWallet->rp_cashback_balance >= $cashbackRule->rp_total_amount){
                    $progressPercentage = '100';
                } else {
                    $progressPercentage = ($FromUserWallet->rp_cashback_balance * 100 ) / $cashbackRule->rp_total_amount ;
                }
    
            } else {
                $progressPercentage = '0';
            }
            $data['progressPercentage'] = round($progressPercentage);

            return $this->sendResponse($data, 'Merchant detail Fetched successfully.');
        } catch (Exception $ex) {
            return $this->sendError([], $ex->getMessage());
        }
    }

    public function myOffer(Request $request)
    {        
        try {
            $distance = 10;

            $latitude = $request->latitude??0;
            $longitude = $request->longitude??0;

            $currentUserId = Auth::id();

            $detail = $this->userService->nearByFetchId($latitude, $longitude, $distance, $currentUserId);

            $merchantList = User::whereIn('id', $detail);
            $merchantList = $merchantList->paginate(10);

            return $this->sendResponse(new MerchantListResource($merchantList), 'Merchant Fetched successfully.');

        } catch (Exception $ex) {
            return $this->sendError([], $ex->getMessage());
        }

    }
}
