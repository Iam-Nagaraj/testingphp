<?php

namespace App\Http\Controllers\Merchant;

use App\Http\Controllers\Controller;
use App\Models\BusinessCategory;
use App\Models\Cashback;
use App\Models\CashbackRule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class CashbackRuleController extends Controller
{
    public function form(Request $request)
	{
    $user_business_details = Auth::user()->BusinessDetail;
    $data['default']  = 0.00;
    if(isset($user_business_details)){
		$business_category = BusinessCategory::select('id')->where('id',$user_business_details->business_category)->first();
		$business_type    = Cashback::where('business_category_id',$business_category->id ?? '')->first();
		$data['default']  = isset($business_type) ? $business_type->percentage : 0.00;
    }
		$data['cashback_rule'] = CashbackRule::where('user_id',Auth::user()->id)->first();
		return view('merchant.cashback_rule.form',$data);
	}

	public function save(Request $request)
	{
    $user_business_details = Auth::user()->BusinessDetail;
    $default  = 0.00;
    if(isset($user_business_details)){
      $business_category= BusinessCategory::select('id')->where('name',$user_business_details->business_type)->first();
      $business_type    = Cashback::where('business_category_id',$business_category->id ?? '')->first();
      $default  = isset($business_type) ? $business_type->percentage : 0.00;
    }			$rules = array(
				'standard_cashback_percentage' => 'required|numeric|between:0.01,100|min:'.$default,
				'ts_total_amount' => 'required|numeric|between:0.01,9999999999.99',
				'ts_extra_percentage' => 'required|numeric|between:0.01,100',
				'rp_total_amount' => 'required|numeric|between:0.01,9999999999.99',
				'rp_extra_percentage' => 'required|numeric|between:0.01,100',
			);
			$validation  = Validator::make($request->all(), $rules)->validate();

		$data['standard_cashback_percentage'] = $request->standard_cashback_percentage;
		$data['ts_total_amount'] = $request->ts_total_amount;
		$data['ts_extra_percentage'] = $request->ts_extra_percentage;
		$data['ts_status'] = isset($request->ts_status) && $request->ts_status == 'on' ? 1 : 0;
		$data['rp_total_amount'] = $request->rp_total_amount;
		$data['rp_extra_percentage'] = $request->rp_extra_percentage;
		$data['rp_status'] = isset($request->rp_status) && $request->rp_status == 'on' ? 1 : 0;

		$data['cashback_rule'] = CashbackRule::updateOrCreate(['user_id'=>Auth::user()->id],$data);
		return redirect()->route('merchant.cashback.rule.form');
	}
}
