<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\BusinessCategoryRequest;
use App\Models\BusinessCategory;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Yajra\DataTables\DataTables;

class BusinessCategoryController extends Controller
{
	protected BusinessCategory $businessCategoryService;

	public function __construct(BusinessCategory $businessCategoryService)
	{
		$this->businessCategoryService = $businessCategoryService;
	}

	/**
	 * Business Category Page
	 *
	 */
	public function index()
	{
		try {
			return view('admin.businessCategory.index');
		} catch (Exception $ex) {
			return $this->sendError([], $ex->getMessage());
		}
	}

	/**
	 * Business Category Table List
	 *
	 */
	public function table(Request $request)
	{

		$businessCategorys = $this->businessCategoryService;
		if ($request->has('search') && !empty($request->get('search'))) {
			$searchValue = '%' . $request->get('search')['value'] . '%';
			$businessCategorys->where(function ($query) use ($searchValue) {
				$query->where('name', 'LIKE', $searchValue);
			});
		}
		$businessCategorys = $businessCategorys->latest('id');
		return Datatables::of($businessCategorys)->addColumn('action', function ($row) {
			return view('admin.businessCategory.table-action')->with(
				[
					'id' => $row->id, 
					'view_url' => route('admin.businessCategory.view', ['id' => $row->id]),
					'delete_url' => route('admin.businessCategory.delete', ['id' => $row->id])
				]
			);
		})
			->rawColumns(['action'])->make(true);
	}

	/**
	 * Business Category View
	 *
	 */
	public function view(Request $request)
	{
		$detail = $this->businessCategoryService->fetchByID($request->id);
		return view('admin.businessCategory.view')->with(['detail' => $detail]);
	}

	/**
	 * Business Category Create Form
	 *
	 */
	public function create()
	{
		try {
			return view('admin.businessCategory.create');
		} catch (Exception $ex) {
			return $this->sendError([], $ex->getMessage());
		}
	}

	/**
	 * Business Category Save data
	 *
	 */
	public function save(BusinessCategoryRequest $request)
	{

		try {
			$userModel = BusinessCategory::updateOrCreate(
				['id' => $request->id],
				[
					'name' => $request->name,
					'dwolla_key' => $request->dwolla_key
				]
			);

			return $this->sendResponse(true, $userModel, 'BusinessCategory successfully saved.');

			throw ValidationException::withMessages([
				'auth' => [trans('auth.failed')],
			]);
		
		} catch (Exception $ex) {
			return $this->sendError([], $ex->getMessage());
		}
	}

	/**
	 * Business Category Delete Resource
	 *
	 */
	public function delete(Request $request)
	{
		try {
			$user = $this->businessCategoryService->fetchByID($request->id);

			if (!$user) {
				throw new Exception("User not found.", 404);
			}

			$this->businessCategoryService->remove($user->id);

			return redirect()->back()->with(['delete' => 'deleted successfully']);

		} catch (Exception $ex) {
			return $this->sendError([], $ex->getMessage());
		}
	}
}
