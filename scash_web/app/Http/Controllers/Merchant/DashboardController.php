<?php

namespace App\Http\Controllers\Merchant;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\Models\User;
use App\Traits\UploadFile;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Str;

class DashboardController extends Controller
{
	use UploadFile;

	protected Transaction $transactionService;
	public function __construct(Transaction $transactionService)
	{
		$this->transactionService = $transactionService;
	}

	public function index()
	{
		$lastMonthStart = Carbon::now()->subMonth()->startOfMonth();
		$lastMonthEnd = Carbon::now()->subMonth()->endOfMonth();
		$transactionQuery = Transaction::where('wallet_type', Transaction::TYPE_MY_WALLET_DEPOSIT)
		->where( function( $q ) {
			$q->where('from_user_id', Auth::user()->id)
			->orWhere('to_user_id', Auth::user()->id);
		});
		$query = clone $transactionQuery;
		$query2 = clone $transactionQuery;

		$lastMonthTransaction = $query->whereBetween('created_at', [$lastMonthStart, $lastMonthEnd])->sum('amount');
		$totalTransaction = $query2->sum('amount');
		
		return view('merchant.dashboard.index', compact('lastMonthTransaction', 'totalTransaction'));
	}

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
		})->latest('id');
		return Datatables::of($transactions)->addColumn('action', function ($row) {
			return view('merchant.dashboard.table-action')->with(
				[
					'id' => $row->id, 
				]
			);
		})
			->rawColumns(['action'])->make(true);
	}

	public function barChart()
	{
		$monthlyTotals = Transaction::where('to_user_id', Auth::user()->id)->select(
			DB::raw("DATE_FORMAT(created_at, '%Y-%m') AS month"),
			DB::raw("SUM(amount) AS total_amount")
		)
		->groupBy('month')
		->orderBy('month')
		->get()->pluck('total_amount','month');

		return $monthlyTotals;
	}

	public function usersUuid()
	{
		$users = User::all();
		foreach($users as $single){
			if(empty($single->uuid)){
				$uuid = Str::uuid()->toString();
				$userModel = User::where('id', $single->id)->first();
				$userModel->uuid = $uuid;
				$userModel->save();
			}
		}
		return 'Done';
	}

}