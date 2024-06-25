<?php

namespace App\Http\Controllers\Admin\Configuration;

use App\Http\Controllers\Controller;
use App\Http\Requests\TaxRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\Tax;
use Exception;

class TaxController extends Controller
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
			$detail = Tax::where('user_id', Auth::user()->id)->first();
			return view('admin.configuration.tax', compact('detail'));
		} catch (Exception $ex) {
			return $this->sendError([], $ex->getMessage());
		}
	}


	public function save(TaxRequest $request)
	{
		try {

			Tax::updateOrCreate(
				['user_id' => Auth::user()->id],
				[
					'user_id' => Auth::user()->id,
					'tax' => $request->tax,
				]
			);

			return $this->sendResponse([], 'Tax Saved successfully.');
		} catch (Exception $ex) {
			return $this->sendError([], $ex->getMessage());
		}
	}

}
