<?php

namespace App\Http\Controllers\Admin\Configuration;

use App\Http\Controllers\Controller;
use App\Http\Requests\ReferalRequest;
use App\Jobs\ProcessReferralAmount;
use App\Models\Configuration;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\Referal;
use Exception;

class ReferalController extends Controller
{

	public function __construct()
	{
		//
	}

	/**
	 * Display a listing of the resource.
	 */

	public function index()
	{
		try {
			$detail = Configuration::where('config_key', 'referral')->first();
			$referral_min_amount = Configuration::where('config_key', 'referral_min_amount')->first();
			return view('admin.configuration.referal', compact('detail','referral_min_amount'));
		} catch (Exception $ex) {
			return $this->sendError([], $ex->getMessage());
		}
	}


	public function save(ReferalRequest $request)
	{
		try {

			Configuration::updateOrCreate(
				['config_key' => 'referral'],
				[
					'config_key' => 'referral',
					'config_value' => $request->referral,
				]
			);

			Configuration::updateOrCreate(
				['config_key' => 'referral_min_amount'],
				[
					'config_key' => 'referral_min_amount',
					'config_value' => $request->referral_min_amount,
				]
			);

			return $this->sendResponse([], 'Data Saved successfully.');
		} catch (Exception $ex) {
			return $this->sendError([], $ex->getMessage());
		}
	}

}
