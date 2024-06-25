<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\CityRequest;
use App\Models\City;
use App\Models\State;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Yajra\DataTables\DataTables;

class CityController extends Controller
{
	protected City $cityService;

	public function __construct(City $cityService)
	{
		$this->cityService = $cityService;
	}

	/**
	 * City Page
	 *
	 */
	public function index()
	{
		try {
			return view('admin.city.index');
		} catch (Exception $ex) {
			return $this->sendError([], $ex->getMessage());
		}
	}

	/**
	 * City Table List
	 *
	 */
	public function table(Request $request)
	{

		$citys = $this->cityService->with('state');
		if ($request->has('search') && !empty($request->get('search'))) {
			$searchValue = '%' . $request->get('search')['value'] . '%';
			$citys->where(function ($query) use ($searchValue) {
				$query->where('name', 'LIKE', $searchValue);
			});
		}
		$citys = $citys->latest('id');
		return Datatables::of($citys)->addColumn('action', function ($row) {
			return view('admin.city.table-action')->with(
				[
					'id' => $row->id, 
					'view_url' => route('admin.city.view', ['id' => $row->id]),
					'delete_url' => route('admin.city.delete', ['id' => $row->id])
				]
			);
		})
			->rawColumns(['action'])->make(true);
	}

	/**
	 * City Create Form
	 *
	 */
	public function view(Request $request)
	{
		$stateModel = State::select('id','name')->pluck('name','id');
		$detail = $this->cityService->fetchByID($request->id);
		return view('admin.city.view')->with(['detail' => $detail, 'stateModel' => $stateModel]);
	}

	public function create()
	{
		$stateModel = State::select('id','name')->pluck('name','id');
		try {
			return view('admin.city.create', compact('stateModel'));
		} catch (Exception $ex) {
			return $this->sendError([], $ex->getMessage());
		}
	}

	/**
	 * City Save data
	 *
	 */
	public function save(CityRequest $request)
	{

		try {
			$userModel = City::updateOrCreate(
				['id' => $request->id],
				[
					'name' => $request->name,
					'state_id' => $request->state_id,
					'latitude' => $request->latitude,
					'longitude' => $request->longitude,
				]
			);

			return $this->sendResponse(true, $userModel, 'City successfully saved.');

			throw ValidationException::withMessages([
				'auth' => [trans('auth.failed')],
			]);
		
		} catch (Exception $ex) {
			return $this->sendError([], $ex->getMessage());
		}
	}

	/**
	 * City Delete Resource
	 *
	 */
	public function delete(Request $request)
	{
		try {
			$user = $this->cityService->fetchByID($request->id);

			if (!$user) {
				throw new Exception("User not found.", 404);
			}

			$this->cityService->remove($user->id);

			return redirect()->back()->with(['delete' => 'deleted successfully']);

		} catch (Exception $ex) {
			return $this->sendError([], $ex->getMessage());
		}
	}
}
