<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\BusinessTypeRequest;
use App\Models\BusinessType;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Yajra\DataTables\DataTables;

class BusinessTypeController extends Controller
{
	protected BusinessType $businessTypeService;

	public function __construct(BusinessType $businessTypeService)
	{
		$this->businessTypeService = $businessTypeService;
	}

	/**
	 * Business Type Page
	 *
	 */
	public function index()
	{
		try {
			return view('admin.businessType.index');
		} catch (Exception $ex) {
			return $this->sendError([], $ex->getMessage());
		}
	}

	/**
	 * Business Type Table List
	 *
	 */
	public function table(Request $request)
	{

		$businessTypes = $this->businessTypeService;
		if ($request->has('search') && !empty($request->get('search'))) {
			$searchValue = '%' . $request->get('search')['value'] . '%';
			$businessTypes->where(function ($query) use ($searchValue) {
				$query->where('name', 'LIKE', $searchValue);
			});
		}
		$businessTypes = $businessTypes->latest('id');
		return Datatables::of($businessTypes)->addColumn('action', function ($row) {
			return view('admin.businessType.table-action')->with(
				[
					'id' => $row->id, 
					'view_url' => route('admin.businessType.view', ['id' => $row->id]),
					'delete_url' => route('admin.businessType.delete', ['id' => $row->id])
				]
			);
		})
			->rawColumns(['action'])->make(true);
	}

	/**
	 * Business Type View
	 *
	 */
	public function view(Request $request)
	{
		$detail = $this->businessTypeService->fetchByID($request->id);
		return view('admin.businessType.view')->with(['detail' => $detail]);
	}

	/**
	 * Business Type Create Form
	 *
	 */
	public function create()
	{
		try {
			return view('admin.businessType.create');
		} catch (Exception $ex) {
			return $this->sendError([], $ex->getMessage());
		}
	}

	/**
	 * Business Type Save data
	 *
	 */
	public function save(BusinessTypeRequest $request)
	{

		try {
			$userModel = BusinessType::updateOrCreate(
				['id' => $request->id],
				[
					'name' => $request->name,
					'type' => $request->type,
					'dwolla_key' => $request->dwolla_key,
				]
			);

			return $this->sendResponse(true, $userModel, 'BusinessType successfully saved.');

			throw ValidationException::withMessages([
				'auth' => [trans('auth.failed')],
			]);
		
		} catch (Exception $ex) {
			return $this->sendError([], $ex->getMessage());
		}
	}

	/**
	 * Business Type Delete Resource
	 *
	 */
	public function delete(Request $request)
	{
		try {
			$user = $this->businessTypeService->fetchByID($request->id);

			if (!$user) {
				throw new Exception("User not found.", 404);
			}

			$this->businessTypeService->remove($user->id);

			return redirect()->back()->with(['delete' => 'deleted successfully']);

		} catch (Exception $ex) {
			return $this->sendError([], $ex->getMessage());
		}
	}
}
