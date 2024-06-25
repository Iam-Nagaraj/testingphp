<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\BusinessSubCategoryRequest;
use App\Models\BusinessCategory;
use App\Models\BusinessSubCategory;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Yajra\DataTables\DataTables;

class BusinessSubCategoryController extends Controller
{
	protected BusinessSubCategory $businessSubCategoryService;

	public function __construct(BusinessSubCategory $businessSubCategoryService)
	{
		$this->businessSubCategoryService = $businessSubCategoryService;
	}

	/**
	 * Business Category Page
	 *
	 */
	public function index()
	{
		try {
			return view('admin.businessSubCategory.index');
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

		$businessSubCategorys = $this->businessSubCategoryService;
		if ($request->has('search') && !empty($request->get('search'))) {
			$searchValue = '%' . $request->get('search')['value'] . '%';
			$businessSubCategorys->where(function ($query) use ($searchValue) {
				$query->where('name', 'LIKE', $searchValue);
			});
		}
		$businessSubCategorys = $businessSubCategorys->latest('id');
		return Datatables::of($businessSubCategorys)->addColumn('action', function ($row) {
			return view('admin.businessSubCategory.table-action')->with(
				[
					'id' => $row->id, 
					'view_url' => route('admin.businessSubCategory.view', ['id' => $row->id]),
					'delete_url' => route('admin.businessSubCategory.delete', ['id' => $row->id])
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
		$businessCategory = BusinessCategory::get();
		$detail = $this->businessSubCategoryService->fetchByID($request->id);
		return view('admin.businessSubCategory.view')->with(['detail' => $detail, 'businessCategory' => $businessCategory]);
	}

	/**
	 * Business Category Create Form
	 *
	 */
	public function create()
	{
		try {
			$businessCategory = BusinessCategory::get();
			return view('admin.businessSubCategory.create')->with(['businessCategory' => $businessCategory]);
		} catch (Exception $ex) {
			return $this->sendError([], $ex->getMessage());
		}
	}

	/**
	 * Business Category Save data
	 *
	 */
	public function save(BusinessSubCategoryRequest $request)
	{

		try {
			$userModel = BusinessSubCategory::updateOrCreate(
				['id' => $request->id],
				[
					'name' => $request->name,
					'parent_id' => $request->business_category,
					'dwolla_key' => $request->dwolla_key
				]
			);

			return $this->sendResponse(true, $userModel, 'BusinessSubCategory successfully saved.');

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
			$user = $this->businessSubCategoryService->fetchByID($request->id);

			if (!$user) {
				throw new Exception("User not found.", 404);
			}

			$this->businessSubCategoryService->remove($user->id);

			return redirect()->back()->with(['delete' => 'deleted successfully']);

		} catch (Exception $ex) {
			return $this->sendError([], $ex->getMessage());
		}
	}
}
