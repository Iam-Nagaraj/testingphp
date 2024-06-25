<?php

namespace App\Http\Controllers\Merchant;

use App\Http\Controllers\Controller;
use App\Http\Requests\CashbackRequest;
use App\Http\Requests\WebCashBackRequest;
use App\Models\Cashback;
use App\Models\Transaction;
use App\Models\User;
use App\Models\Wallet;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\DataTables;

class CashbackController extends Controller
{
	protected Cashback $cashbackService;
	public function __construct(Cashback $cashbackService)
	{
		$this->cashbackService = $cashbackService;
	}

	public function index(Request $request)
	{
		try {
			$user = Auth::user();
			DB::statement("SET sql_mode=(SELECT REPLACE(@@sql_mode,'ONLY_FULL_GROUP_BY',''))");

			$cashback = $this->cashbackService->with('user')->byMerchant($user->id)->byDefault()->where('cashback', 'LIKE', '%' . $request->search . '%')->byStatus($request->status)->latest('id')->paginate($request->length, ['*'], 'pageNumber', $request->page);

			return $this->sendResponse($cashback, 'Cashback fetched successfully.');
		} catch (Exception $ex) {
			return $this->sendError([], $ex->getMessage());
		}
	}

	public function save(CashbackRequest $request)
	{
		try {
			$user = Auth::user();
			$request->merge(['type' => $request->cashback_type??getConfigConstant('MERCHANT_CASHBACK_TYPE')]);
			$detail = $this->cashbackService->_updateOrCreate($user, $request);
			return $this->sendResponse($detail, 'Cashback save successfully.');
		} catch (Exception $ex) {
			return $this->sendError([], $ex->getMessage());
		}
	}

	public function statusChange(Request $request)
	{
		try {
			$id = $request->id;
			$status = $request->status;
			$users = $this->cashbackService->updateSatus(['id' => $id], ['status' => $status]);

			return $this->sendResponse($users, 'Cashback Status Changed successfully.');
		} catch (Exception $ex) {
			return $this->sendError([], $ex->getMessage());
		}
	}

	public function list()
	{
		try {
			return view('merchant.cashback.index');
		} catch (Exception $ex) {
			return $this->sendError([], $ex->getMessage());
		}
	}

	public function view(Request $request)
	{
		$detail = $this->cashbackService->fetchByID($request->id);
		return view('merchant.cashback.view')->with(['detail' => $detail]);
	}

	public function create()
	{
		try {
			return view('merchant.cashback.create');
		} catch (Exception $ex) {
			return $this->sendError([], $ex->getMessage());
		}
	}

	public function store(WebCashBackRequest $request)
	{
		try {
			$user = Auth::user();
			$request->type = getConfigConstant('MERCHANT_CASHBACK_TYPE');
			$detail = $this->cashbackService->_updateOrCreate($user, $request);
			return $this->sendResponse($detail, 'Cashback save successfully.');
		} catch (Exception $ex) {
			return $this->sendError([], $ex->getMessage());
		}
	}

	public function delete(Request $request)
	{
		try {
			$user = $this->cashbackService->fetchByID($request->id);

			if (!$user) {
				throw new Exception("Cashback not found.", 404);
			}

			$this->cashbackService->remove($user->id);

			return $this->sendResponse($user, 'Cashback deleted successfully.');
		} catch (Exception $ex) {
			return $this->sendError([], $ex->getMessage());
		}
	}

	public function table(Request $request)
	{

		$cashbacks = $this->cashbackService->with('user')->byMerchant(Auth::user()->id);
		if ($request->has('search') && !empty($request->get('search'))) {
			$searchValue = '%' . $request->get('search')['value'] . '%';
		}
		$cashbacks = $cashbacks->latest('id');
		return DataTables::of($cashbacks)->addColumn('action', function ($row) {
			return view('merchant.cashback.table-action')->with(
				[
					'id' => $row->id, 
					'view_url' => route('merchant.cashback.view', ['id' => $row->id]),
					'delete_url' => route('merchant.cashback.delete', ['id' => $row->id])
				]
			);
		})
			->rawColumns(['action'])->make(true);
	}

	public function checkTotalCashback()
	{
		$users = User::all();

		foreach($users as $user){
			
			$cashback_earned = Transaction::where('type', 2)->where('to_user_id', $user->id)->sum('amount');
			if($cashback_earned){
				$wallet = Wallet::where('user_id', $user->id)->first();
				if($wallet){
					$wallet->cashback_earned = $cashback_earned;
					$wallet->save();
				}
			}
		}

		return 'done';
	}

}
