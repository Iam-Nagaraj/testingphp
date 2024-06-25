<?php

namespace App\Http\Controllers\Admin\Configuration;

use App\Http\Controllers\Controller;
use App\Http\Requests\SupportEmailRequest;
use App\Models\Configuration;
use Exception;

class SupportController extends Controller
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
			$detail = Configuration::where('config_key', 'support_email')->first();
			return view('admin.configuration.supportEmail', compact('detail'));
		} catch (Exception $ex) {
			return $this->sendError([], $ex->getMessage());
		}
	}


	public function save(SupportEmailRequest $request)
	{
		try {

			Configuration::updateOrCreate(
				['config_key' => 'support_email'],
				[
					'config_key' => 'support_email',
					'config_value' => $request->support_email,
				]
			);

			return $this->sendResponse([], 'Data Saved successfully.');
		} catch (Exception $ex) {
			return $this->sendError([], $ex->getMessage());
		}
	}

}
