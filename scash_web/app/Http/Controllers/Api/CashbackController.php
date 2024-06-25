<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CashbackResource;
use App\Models\Cashback;
use Exception;
use Illuminate\Http\Request;

class CashbackController extends Controller
{
	protected Cashback $cashbackService;

	public function __construct(Cashback $cashbackService)
	{
		$this->cashbackService = $cashbackService;
	}

    public function index(Request $request){
		try {
			$detail = $this->cashbackService->nearByFetch($request->latitude,$request->longitude);
			return $this->sendResponse(new CashbackResource($detail), 'Cashback Fetched successfully.');
		} catch (Exception $ex) {
			return $this->sendError([], $ex->getMessage());
		}
	}
}
