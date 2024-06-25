<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\CashbackRequest;
use App\Http\Requests\WebCashBackRequest;
use App\Models\BusinessCategory;
use App\Models\Cashback;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\DataTables;

class CashbackController extends Controller
{
	protected Cashback $cashbackService;
	public function __construct(Cashback $cashbackService)
	{
		$this->cashbackService = $cashbackService;
	}

	/**
	 * City Page
	 *
	 */
	public function index()
	{
		try {
			$detail = $this->cashbackService->fetchDefault();

			return $this->sendResponse($detail, 'Cashback fetched successfully.');
		} catch (Exception $ex) {
			return $this->sendError([], $ex->getMessage());
		}
	}

	/**
	 * City List
	 *
	 */
	public function list()
	{
		try {
			$data['businessCategory'] = BusinessCategory::get();
			return view('admin.cashback.index',$data);
		} catch (Exception $ex) {
			return $this->sendError([], $ex->getMessage());
		}
	}

	/**
	 * Cash Back View
	 *
	 */
	public function view(Request $request)
	{
		$data['businessCategory'] = BusinessCategory::select('id','name')->where('status',BusinessCategory::ACTIVE)->get();
		$data['detail'] = $this->cashbackService->fetchByID($request->id);
		return view('admin.cashback.view',$data);
	}

	/**
	 * Cash Back Create Form
	 *
	 */
	public function create()
	{
		try {
			$cashback_business_categories = Cashback::where('status',1)->pluck('business_category_id');
			$data['businessCategory'] = BusinessCategory::whereNotIn('id',$cashback_business_categories)->get();
			
			return view('admin.cashback.create',$data);
		} catch (Exception $ex) {
			return $this->sendError([], $ex->getMessage());
		}
	}

	/**
	 * Cash Back Save data
	 *
	 */
	public function save(CashbackRequest $request)
	{
		try {
			$user = Auth::user();
			$detail = $this->cashbackService->_updateOrCreate($user, $request);
			return $this->sendResponse($detail, 'Cashback save successfully.');
		} catch (Exception $ex) {
			return $this->sendError([], $ex->getMessage());
		}
	}

	public function store(WebCashBackRequest $request)
	{
		try {
			$user = Auth::user();
			$detail = $this->cashbackService->_updateOrCreate($user, $request);
			return $this->sendResponse($detail, 'Cashback save successfully.');
		} catch (Exception $ex) {
			return $this->sendError([], $ex->getMessage());
		}
	}

	/**
	 * Cash Back Delete Resource
	 *
	 */
	public function delete(Request $request)
	{
		try {
			$user = $this->cashbackService->fetchByID($request->id);

			if (!$user) {
				throw new Exception("Cashback not found.", 404);
			}

			$this->cashbackService->remove($user->id);

			return redirect()->back()->with(['delete' => 'deleted successfully']);

		} catch (Exception $ex) {
			return $this->sendError([], $ex->getMessage());
		}
	}

	/**
	 * Cash Back Table List
	 *
	 */
	public function table(Request $request)
	{

		$cashbacks = $this->cashbackService->with('user');
		if ($request->has('search') && !empty($request->get('search'))) {
			$searchValue = '%' . $request->get('search')['value'] . '%';
		}
		$cashbacks = $cashbacks->latest('id');
		return Datatables::of($cashbacks)
		// ->addColumn('business_category_name', function ($cashbacks) use ($request) {
		// 	$business_category_name =$cashbacks->businessCategory->name;
		

		// 	return $business_category_name;
		// })
		->addColumn('action', function ($row) {
			return view('admin.cashback.table-action')->with(
				[
					'id' => $row->id, 
					'view_url' => route('admin.cashback.view', ['id' => $row->id]),
					'delete_url' => route('admin.cashback.delete', ['id' => $row->id])
				]
			);
		})
			->rawColumns(['action'])->make(true);
	}

	
}
