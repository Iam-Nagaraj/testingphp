<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StateRequest;
use App\Models\State;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Yajra\DataTables\DataTables;

class StateController extends Controller
{
	protected State $stateService;

	public function __construct(State $stateService)
	{
		$this->stateService = $stateService;
	}

	/**
	 * State Page
	 *
	 */
	public function index()
	{
		try {
			return view('admin.state.index');
		} catch (Exception $ex) {
			return $this->sendError([], $ex->getMessage());
		}
	}

	/**
	 * State Table List
	 *
	 */
	public function table(Request $request)
	{

		$states = $this->stateService;
		if ($request->has('search') && !empty($request->get('search'))) {
			$searchValue = '%' . $request->get('search')['value'] . '%';
			$states->where(function ($query) use ($searchValue) {
				$query->where('name', 'LIKE', $searchValue);
			});
		}
		$states = $states->latest('id');
		return Datatables::of($states)->addColumn('action', function ($row) {
			return view('admin.state.table-action')->with(
				[
					'id' => $row->id, 
					'view_url' => route('admin.state.view', ['id' => $row->id]),
					'delete_url' => route('admin.state.delete', ['id' => $row->id])
				]
			);
		})
			->rawColumns(['action'])->make(true);
	}

	/**
	 * State Create Form
	 *
	 */
	public function view(Request $request)
	{
		$detail = $this->stateService->fetchByID($request->id);
		return view('admin.state.view')->with(['detail' => $detail]);
	}

	public function create()
	{
		try {
			return view('admin.state.create');
		} catch (Exception $ex) {
			return $this->sendError([], $ex->getMessage());
		}
	}

	/**
	 * State Save data
	 *
	 */
	public function save(StateRequest $request)
	{

		try {
			$userModel = State::updateOrCreate(
				['id' => $request->id],
				[
					'name' => $request->name,
					'code' => $request->code,
				]
			);

			return $this->sendResponse(true, $userModel, 'State successfully saved.');

			throw ValidationException::withMessages([
				'auth' => [trans('auth.failed')],
			]);
		
		} catch (Exception $ex) {
			return $this->sendError([], $ex->getMessage());
		}
	}

	/**
	 * State Delete Resource
	 *
	 */
	public function delete(Request $request)
	{
		try {
			$user = $this->stateService->fetchByID($request->id);

			if (!$user) {
				throw new Exception("User not found.", 404);
			}

			$this->stateService->remove($user->id);

			return redirect()->back()->with(['delete' => 'deleted successfully']);

		} catch (Exception $ex) {
			return $this->sendError([], $ex->getMessage());
		}
	}
}
