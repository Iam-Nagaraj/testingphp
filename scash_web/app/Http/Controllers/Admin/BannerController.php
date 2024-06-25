<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Validation\ValidationException;
use App\Http\Controllers\Controller;
use App\Models\Banner;
use App\Models\State;
use App\Models\User;
use Exception;
use App\Traits\UploadFile;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use App\Http\Requests\BannerRequest;
use App\Http\Requests\UpdateBannerRequest;

class BannerController extends Controller
{
	use UploadFile;

	protected Banner $bannerService;

	public function __construct(Banner $bannerService)
	{
		$this->bannerService = $bannerService;
	}

	/**
	 * Banner Page
	 *
	 */
	public function index()
	{
		try {
			return view('admin.banner.index');
		} catch (Exception $ex) {
			return $this->sendError([], $ex->getMessage());
		}
	}

	public function banner()
	{
		$details = Banner::orderby('type', 'ASC')->get();

		return $this->sendResponse($details, 'Banners fetched successfully.');
	}

	/**
	 * Banner Table List
	 *
	 */
	public function table(Request $request)
	{

		$banners = $this->bannerService->with('merchant');
		if ($request->has('search') && !empty($request->get('search'))) {
			$searchValue = '%' . $request->get('search')['value'] . '%';
			$banners->where(function ($query) use ($searchValue) {
				$query->where('name', 'LIKE', $searchValue);
			});
		}
		$banners = $banners->latest('id');
		return Datatables::of($banners)->addColumn('action', function ($row) {
			return view('admin.banner.table-action')->with(
				[
					'id' => $row->id, 
					'view_url' => route('admin.banner.view', ['id' => $row->id]),
					'delete_url' => route('admin.banner.delete', ['id' => $row->id])
				]
			);
		})
			->rawColumns(['action'])->make(true);
	}

	/**
	 * Banner Create Form
	 *
	 */
	public function view(Request $request)
	{
		$merchantModel = User::select('id','name')->where('role_id', getConfigConstant('MERCHANT_ROLE_ID'))->pluck('name','id');

		$detail = $this->bannerService->fetchByID($request->id);
		return view('admin.banner.view')->with(['detail' => $detail, 'merchantModel' => $merchantModel]);
	}

	public function create()
	{
		$merchantModel = User::select('id','name')->where('role_id', getConfigConstant('MERCHANT_ROLE_ID'))->pluck('name','id');
		try {
			return view('admin.banner.create', compact('merchantModel'));
		} catch (Exception $ex) {
			return $this->sendError([], $ex->getMessage());
		}
	}

	/**
	 * Banner Save data
	 *
	 */
	public function save(BannerRequest $request)
	{

		try {
			$logo = $request->banner_image;
			$uploadImage = $this->imageUpload($logo);

			$userModel = Banner::updateOrCreate(
				['id' => $request->id],
				[
					'banner_image' => $uploadImage['url'],
					'name' => $request->name,
					'start_date' => $request->start_date,
					'end_date' => $request->end_date,
					'user_id' => $request->user_id,
					'url' => $request->url,
					'type' => $request->type,
				]
			);

			return $this->sendResponse(true, $userModel, 'Banner successfully saved.');

			throw ValidationException::withMessages([
				'auth' => [trans('auth.failed')],
			]);
		
		} catch (Exception $ex) {
			return $this->sendError([], $ex->getMessage());
		}
	}

	/**
	 * Banner update data
	 *
	 */
	public function update(UpdateBannerRequest $request)
	{

		try {

			$array_data = [
				'name' => $request->name,
				'start_date' => $request->start_date,
				'end_date' => $request->end_date,
				'user_id' => $request->user_id,
				'url' => $request->url,
				'type' => $request->type,
			];

			$new_array = $array_data;
			if($request->hasFile('banner_image')){
				$logo = $request->banner_image;
				$uploadImage = $this->imageUpload($logo);
				$array_data2 = [	'banner_image' => $uploadImage['url']];
				$new_array = array_merge($array_data, $array_data2);
			}

			$userModel = Banner::updateOrCreate(
				['id' => $request->id],
				$new_array
			);

			return $this->sendResponse(true, $userModel, 'Banner successfully saved.');

			throw ValidationException::withMessages([
				'auth' => [trans('auth.failed')],
			]);
		
		} catch (Exception $ex) {
			return $this->sendError([], $ex->getMessage());
		}
	}

	/**
	 * Banner Delete Resource
	 *
	 */
	public function delete(Request $request)
	{
		try {
			$user = $this->bannerService->fetchByID($request->id);

			if (!$user) {
				throw new Exception("User not found.", 404);
			}

			$this->bannerService->remove($user->id);

			return redirect()->back()->with(['delete' => 'deleted successfully']);

		} catch (Exception $ex) {
			return $this->sendError([], $ex->getMessage());
		}
	}
}
