<?php

namespace App\Http\Controllers\Admin\Configuration;

use App\Http\Controllers\Controller;
use App\Http\Requests\PlatformFeeRequest;
use App\Models\Configuration;
use Exception;

class PlatformFeeController extends Controller
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
			$detail = Configuration::where('config_key', 'platform_fee')->first();
			return view('admin.configuration.platformFee', compact('detail'));
		} catch (Exception $ex) {
			return $this->sendError([], $ex->getMessage());
		}
	}

	/**
	 * PlatForm Fee Save data
	 *
	 */
	public function save(PlatformFeeRequest $request)
	{
		try {

			Configuration::updateOrCreate(
				['config_key' => 'platform_fee'],
				[
					'config_key' => 'platform_fee',
					'config_value' => $request->platform_fee,
				]
			);

			return $this->sendResponse([], 'Data Saved successfully.');
		} catch (Exception $ex) {
			return $this->sendError([], $ex->getMessage());
		}
	}
	
}
