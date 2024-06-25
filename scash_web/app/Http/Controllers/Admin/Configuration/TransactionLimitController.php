<?php

namespace App\Http\Controllers\Admin\Configuration;

use App\Http\Controllers\Controller;
use App\Http\Requests\SupportEmailRequest;
use App\Http\Requests\TransactionLimitRequest;
use App\Models\Configuration;
use Exception;

class TransactionLimitController extends Controller
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
			$transaction_limit = Configuration::where('config_key', 'transaction_limit')->first();
			$full_day_transaction_limit = Configuration::where('config_key', 'full_day_transaction_limit')->first();
			return view('admin.configuration.transactionLimit', compact('transaction_limit', 'full_day_transaction_limit'));
		} catch (Exception $ex) {
			return $this->sendError([], $ex->getMessage());
		}
	}


	public function save(TransactionLimitRequest $request)
	{
		try {

			Configuration::updateOrCreate(
				['config_key' => 'transaction_limit'],
				[
					'config_key' => 'transaction_limit',
					'config_value' => $request->transaction_limit,
				]
			);
			Configuration::updateOrCreate(
				['config_key' => 'full_day_transaction_limit'],
				[
					'config_key' => 'full_day_transaction_limit',
					'config_value' => $request->full_day_transaction_limit,
				]
			);

			return $this->sendResponse([], 'Data Saved successfully.');
		} catch (Exception $ex) {
			return $this->sendError([], $ex->getMessage());
		}
	}

}
