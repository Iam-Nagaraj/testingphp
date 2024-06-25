<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\Models\User;
use App\Models\Wallet;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\DataTables;

class DashboardController extends Controller
{

	protected Transaction $transactionService;
	public function __construct(Transaction $transactionService)
	{
		$this->transactionService = $transactionService;
	}

	/**
	 * Dashboard Page
	 *
	 */
	public function index()
	{

		$walletModel = Wallet::where('user_id', Auth::user()->id)->first();
		$platformFee = Transaction::sum('fee');

		$merchantCounts = User::where('role_id', getConfigConstant('MERCHANT_ROLE_ID'))->count();
		$userCounts = User::where('role_id', getConfigConstant('USER_ROLE_ID'))->count();

		return view('admin.dashboard.index', compact('platformFee', 'merchantCounts', 'userCounts','walletModel'));
	}

	public function profile()
	{
		$detail = User::where('id', Auth::user()->id)->first();
		return view('admin.profile.index', compact('detail'));
	}


	/**
	 * Dashboard barChart data
	 *
	 */
	public function barChart()
	{
		$monthlyTotals = Transaction::select(
			DB::raw("DATE_FORMAT(created_at, '%Y-%m') AS month"),
			DB::raw("SUM(amount) AS total_amount")
		)
		->groupBy('month')
		->orderBy('month')
		->get()->pluck('total_amount','month');

		return $monthlyTotals;
	}

	/**
	 * Dashboard transaction table data
	 *
	 */
	public function table(Request $request)
	{

		$transactions = $this->transactionService;
		if ($request->has('search') && !empty($request->get('search'))) {
			$searchValue = '%' . $request->get('search')['value'] . '%';
		}
		
		$transactions = $transactions->with('sender','receiver')->where('wallet_type', Transaction::TYPE_WALLET_TO_WALLET)
		->where( function( $q ) {
			$q->where('from_user_id', Auth::user()->id)
			->orWhere('to_user_id', Auth::user()->id);
		})
		->latest();

		return Datatables::of($transactions)->addColumn('action', function ($row) {
			return view('admin.dashboard.table-action')->with(
				[
					'id' => $row->id, 
				]
			);
		})
			->rawColumns(['action'])->make(true);
	}
}