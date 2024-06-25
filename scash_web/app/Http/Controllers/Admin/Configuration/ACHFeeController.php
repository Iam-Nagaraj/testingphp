<?php

namespace App\Http\Controllers\Admin\Configuration;

use App\Http\Controllers\Controller;
use App\Http\Requests\AchFeeRequest;
use App\Models\Configuration;
use Exception;

class ACHFeeController extends Controller
{
	public function __construct()
	{
		//
	}

	/**
	 * PlatForm Fee Page
	 *
	 */
	public function index()
	{
		try {
			$manual_platform_fee = Configuration::where('config_key', 'manual_platform_fee')->first();
			$instant_platform_fee = Configuration::where('config_key', 'instant_platform_fee')->first();
			return view('admin.configuration.achPayment', compact('manual_platform_fee', 'instant_platform_fee'));
		} catch (Exception $ex) {
			return $this->sendError([], $ex->getMessage());
		}
	}

	/**
	 * PlatForm Fee Save data
	 *
	 */
	public function save(AchFeeRequest $request)
	{
		try {

			Configuration::updateOrCreate(
				['config_key' => 'manual_platform_fee'],
				[
					'config_key' => 'manual_platform_fee',
					'config_value' => $request->manual_platform_fee,
				]
			);

			Configuration::updateOrCreate(
				['config_key' => 'instant_platform_fee'],
				[
					'config_key' => 'instant_platform_fee',
					'config_value' => $request->instant_platform_fee,
				]
			);

			return $this->sendResponse([], 'Data Saved successfully.');
		} catch (Exception $ex) {
			return $this->sendError([], $ex->getMessage());
		}
	}
	
}
