<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UserAddress;
use App\Models\UserKyc;
use App\Models\UserMedia;
use Exception;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class UserController extends Controller
{
	protected User $userService;
	protected UserMedia $userMediaService;
	protected UserAddress $userAddressService;
	protected UserKyc $userKycService;


	public function __construct(User $userService, UserMedia $userMediaService, UserAddress $userAddressService, UserKyc $userKycService)
	{
		$this->userService = $userService;
		$this->userMediaService = $userMediaService;
		$this->userAddressService = $userAddressService;
		$this->userKycService = $userKycService;
	}

	/**
	 * User Page
	 *
	 */
	public function index()
	{
		try {

			return view('admin.user.index');
		} catch (Exception $ex) {
			return $this->sendError([], $ex->getMessage());
		}
	}

	public function static()
	{
		try {

			return view('admin.user.index');
		} catch (Exception $ex) {
			return $this->sendError([], $ex->getMessage());
		}
	}

	/**
	 * User Table List
	 *
	 */
	public function table(Request $request)
	{

		$users = $this->userService->with('address', 'media')->isUser();
		if(isset($request->status) && $request->status != 1){
			$merchants = $users->where('status', '!=', 1);
		} else {
			$merchants = $users->byStatus(1);
		}
		if ($request->has('search') && !empty($request->get('search'))) {
			$searchValue = '%' . $request->get('search')['value'] . '%';
			$users->where(function ($query) use ($searchValue) {
				$query->where('name', 'LIKE', $searchValue)
					->orWhere('first_name', 'LIKE', $searchValue)
					->orWhere('last_name', 'LIKE', $searchValue)
					->orWhere('email', 'LIKE', $searchValue)
					->orWhere('phone_number', 'LIKE', $searchValue);
			});
		}
		$users = $users->latest('id');
		return Datatables::of($users)->addColumn('action', function ($row) {
			return view('admin.user.table-action')->with([
				'id' => $row->id, 
				'view_url' => route('admin.user.view', ['id' => $row->uuid]),
				'uuid' => $row->uuid,
				'userName' => $row->first_name.' '.$row->last_name
			]);
		})
			->rawColumns(['action'])->make(true);
	}

	public function userStatusChange(Request $request)
	{
		try {
			$id = $request->id;
			$status = $request->status;
			$users = $this->userService->updateSatus(['id' => $id], ['status' => $status]);

			return $this->sendResponse($users, 'User Status Changed successfully.');
		} catch (Exception $ex) {
			return $this->sendError([], $ex->getMessage());
		}
	}

	/**
	 * User Create Form
	 *
	 */
	public function view(Request $request)
	{
		$user = User::where('uuid', $request->id)->first();

		if(empty($user)){
			return redirect()->back();
		}
		$user_id = $user->id;

		$detail = $this->userService->fetchByID($user_id);
		return view('admin.user.view')->with(['detail' => $detail]);
	}

	public function changeStatus(Request $request)
	{
		$user = User::where('id', $request->user_id)->first();
		if($user->status == 1){
			$user->status = '2';
		} else {
			$user->status = '1';
		}
		$user->save();
		return $this->sendResponse($user, 'User Status Changed successfully.');

	}
}
