<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Merchant\DwollaController;
use App\Http\Requests\MerchantRequest;
use App\Jobs\SendEmailJob;
use App\Models\BusinessCategory;
use App\Models\BusinessDetail;
use App\Models\BusinessType;
use App\Models\DwollaCustomer;
use App\Models\Notification;
use App\Models\User;
use App\Models\UserAddress;
use App\Models\UserKyc;
use App\Models\UserMedia;
use App\Models\Verification;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Yajra\DataTables\DataTables;
use App\Traits\UploadFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class MerchantController extends Controller
{
	use UploadFile;

	protected User $userService;
	protected BusinessDetail $businessDetailService;
	protected UserMedia $userMediaService;
	protected UserAddress $userAddressService;
	protected UserKyc $userKycService;

	public function __construct(
		User $userService, 
		BusinessDetail $businessDetailService, 
		UserMedia $userMediaService, 
		UserAddress $userAddressService, 
		UserKyc $userKycService
	)
	{
		$this->userService = $userService;
		$this->businessDetailService = $businessDetailService;
		$this->userMediaService = $userMediaService;
		$this->userAddressService = $userAddressService;
		$this->userKycService = $userKycService;
	}

	public function index()
	{
		$BusinessCategory = BusinessCategory::select('*')->get();
		$BusinessType = BusinessType::select('*')->get();
		try {
			return view('admin.merchants.index', compact('BusinessCategory', 'BusinessType'));
		} catch (Exception $ex) {
			return $this->sendError([], $ex->getMessage());
		}
	}

	public function create()
	{
		try {
			return view('admin.merchants.create');
		} catch (Exception $ex) {
			return $this->sendError([], $ex->getMessage());
		}
	}

	public function save(Request $request)
	{

		$validatedData = $request->validate([
			'name' => 'required',
			'email' => 'required',
			'password' => 'required',
			'logo' =>' required',
			'phone_number' => 'required',
			'business_proff' => 'required',
		]);

		try {

			DB::beginTransaction();
			$userModel = new User();
			$userModel->name = $request->name;
			$userModel->email = $request->email;
			$userModel->phone_number = $request->phone_number;
			$userModel->country_code = $request->country_code;
			$userModel->password = Hash::make($request->password);
			$userModel->role_id = getConfigConstant('MERCHANT_ROLE_ID');
			$userModel->status = getConfigConstant('STATUS_KYC_VERIFICATION');

			if($userModel->save()){
				$verification = new Verification();
				$verification->phone_number = $request->phone_number;
				$verification->country_code = $request->country_code;
				$verification->email = $request->email;
				$start = date('Y-m-d H:i:s');
				$verification->expired_at = date('Y-m-d H:i:s', strtotime('+1 hour', strtotime($start)));
				if($verification->save()){
					$UserAddress = new UserAddress();
					$UserAddress->user_id = $userModel->id;
					$UserAddress->address = $request->address;
					$UserAddress->address_2 = $request->address_2;
					$UserAddress->country = $request->country;
					$UserAddress->state = $request->state;
					$UserAddress->city = $request->city;
					$UserAddress->latitude = $request->latitude;
					$UserAddress->longitude = $request->longitude;
					$UserAddress->line_1 = $request->line_1;
					$UserAddress->line_2 = $request->line_2;
					$UserAddress->country_2 = $request->country_2;
					$UserAddress->state_2 = $request->state_2;
					$UserAddress->city_2 = $request->city_2;
					$UserAddress->latitude_2 = $request->latitude_2;
					$UserAddress->longitude_2 = $request->longitude_2;
					if($UserAddress->save()){
						$logo = $request->logo;
						$uploadImage = $this->imageUpload($logo);
						
						$userMedia = new UserMedia();
						$userMedia->user_id = $userModel->id;
						$userMedia->file = $uploadImage['url'];
						$userMedia->type = UserMedia::TYPE_IMAGE;
						$userMedia->save();

						$business_proff = $request->business_proff;
						$business_proff = $this->imageUpload($business_proff);
						
						$userMedia = new UserMedia();
						$userMedia->user_id = $userModel->id;
						$userMedia->file = $business_proff['url'];
						$userMedia->type = UserMedia::TYPE_DOCUMENT;

						if($userMedia->save()){
							$details['email'] = $request->email;
							$details['password'] = $request->password;
							$responce = dispatch(new SendEmailJob($details, 'MerchantCredentials', $userModel->email));
							DB::commit();
							return $this->sendResponse(true, $userModel, 'Merchant successfully saved.');
						}
					}
				}
				DB::rollBack();

			} else {
				DB::rollBack();
				return redirect()->back()->with('errors', 'Something Went Wrong');
			}
		
		} catch (Exception $ex) {
			DB::rollBack();
			return $this->sendError([], $ex->getMessage());
		}
	}

	public function changeStatus(Request $request)
	{

		$user = User::where('id', $request->id)->first();
		$from = Auth::user()->id;
		$id = $user->id; 

		if($user->status == 1){
			$user->status = '2';
			$status_id = getConfigConstant('STATUS_INACTIVE');
			$message = 'Hi '. $user->name .' , your account just got rejected by Admin';
			$responce = dispatch(new SendEmailJob($message, 'AccountStatus', $user->email));
			_Notification($from, $id, $message, Notification::REJECTED);
		} else {
			$user->status = '1';
			$status_id = getConfigConstant('STATUS_ACTIVE');
			$message = 'Hi '. $user->name .' , your account just got approved by Admin';
			$responce = dispatch(new SendEmailJob($message, 'AccountStatus', $user->email));
			_Notification($from, $id, $message, Notification::APPROVED);
		}
		$user->save();
		return $this->sendResponse($user, 'Merchant Status Changed successfully.');

	}

	public function updateData(Request $request)
	{

		$validatedData = $request->validate([
			'name' => 'required',
		]);

		try {

			DB::beginTransaction();
			$userModel = User::where('id', $request->id)->first();
			$userModel->name = $request->name;
			
			if($userModel->status != $request->status && $request->status == getConfigConstant('STATUS_INACTIVE')){
				$message = 'Hi '. $request->name .' , your account just got rejected by Admin';
				$subject = 'Account Rejected';
				$responce = dispatch(new SendEmailJob($message, 'AccountStatus', $userModel->email));
			}

			$userModel->status = $request->status;

			if($userModel->save()){
				
				$UserAddress = $this->_saveAddress($request, $userModel);

				if($UserAddress->save()){
					if($request->hasFile('logo')){
						$logo = $request->logo;
						$uploadImage = $this->imageUpload($logo);
						
						$userMedia = UserMedia::where('user_id', $request->id)->where('type', UserMedia::TYPE_IMAGE)->first();
						$userMedia->user_id = $userModel->id;
						$userMedia->file = $uploadImage['url'];
						$userMedia->type = UserMedia::TYPE_IMAGE;
						$userMedia->save();
					}
					if($request->hasFile('business_proff')){
						$business_proff = $request->business_proff;
						$business_proff = $this->imageUpload($business_proff);
						
						$userMedia = UserMedia::where('user_id', $request->id)->where('type', UserMedia::TYPE_DOCUMENT)->first();
						$userMedia->user_id = $userModel->id;
						$userMedia->file = $business_proff['url'];
						$userMedia->type = UserMedia::TYPE_DOCUMENT;
						$userMedia->save();
					}
					
					DB::commit();
					return redirect('/merchant');

				}
			
				DB::rollBack();

			} else {
				DB::rollBack();
				return redirect()->back()->with('errors', 'Something Went Wrong');
			}
		
		} catch (Exception $ex) {
			DB::rollBack();
			return redirect()->back()->with('errors', $ex->getMessage());
		}
	}

	private function _saveAddress($request, $user)
	{
		$UserAddress = UserAddress::updateOrCreate(
			['user_id' => $user->id],
			[
				'user_id' => $user->id,
				'address' => $request->address,
				'address_2' => $request->address_2,
				'state' => $request->state,
				'city' => $request->city,
				'latitude' => $request->latitude,
				'longitude' => $request->longitude,
			]
		);

		return $UserAddress;
	}

	private function uploadImage($request)
	{
		try {
			if ($request->has('file')) {
				$file = $request->file;
				$uploadImage = $this->imageUpload($file);
				return $this->sendResponse($uploadImage, 'Image uploaded successfully.');
			}
		} catch (Exception $ex) {
			return $this->sendError([], $ex->getMessage());
		}
	}


	public function table(Request $request)
	{
		try {
			$merchants = $this->userService->with('address', 'media', 'kyc', 'verification', 'BusinessDetail')->isMerchant();
			if(isset($request->status)){
				$merchants = $merchants->byStatus($request->status);
			} else {
				$merchants = $merchants->byStatus(1);
			}

			if(!empty($request->filter) && !empty($request->filter['business_category'])){
				$registration_type = $request->filter['business_category'];
				$merchants = $merchants->whereHas('BusinessDetail', function ($query) use ($registration_type) {
					$query->where('business_category', $registration_type);
				});
			}
			if(!empty($request->filter) && !empty($request->filter['business_type'])){
				$business_type = $request->filter['business_type'];
				$merchants = $merchants->whereHas('BusinessDetail', function ($query) use ($business_type) {
					$query->where('registration_type', $business_type);
				});
			}
			if ($request->has('search') && !empty($request->get('search'))) {
				$searchValue = '%' . $request->get('search')['value'] . '%';
				$merchants->where(function ($query) use ($searchValue) {
					$query->where('name', 'LIKE', $searchValue)
						->orWhere('first_name', 'LIKE', $searchValue)
						->orWhere('last_name', 'LIKE', $searchValue)
						->orWhere('email', 'LIKE', $searchValue)
						->orWhere('phone_number', 'LIKE', $searchValue);

				});
			}
			$merchants = $merchants->latest('id');
			
			return DataTables::of($merchants)->addColumn('action', function ($row) {
				$buttonArr = [
					'id' => $row->id, 
					'view_url' => route('admin.merchant.view', ['id' => $row->uuid]), 
					'business_url' => route('admin.merchant.business-details', ['id' => $row->id]), 
					'delete_url' => route('admin.merchant.delete', ['id' => $row->id]),
					'uuid' => $row->uuid,
					'userName' => $row->first_name.' '.$row->last_name,
					'status' => $row->status,
				];
				return view('admin.merchants.table-action')->with(
					$buttonArr
				);
			})->rawColumns(['action'])->make(true);
		} catch (Exception $ex) {
			return $this->sendError([], $ex->getMessage());
		}
	}

	public function store(MerchantRequest $request)
	{
		try {
			return DB::transaction(function () use ($request) {
				$password = staticPasswordByName($request->name);
				$userData = [
					'name' => $request->name,
					'email' => $request->email,
					'phone_number' => $request->phone_number,
					'country_code' => $request->country_code,
					'password' => Hash::make($password),
					'role_id' => getConfigConstant('MERCHANT_ROLE_ID'),
				];

				$user = $this->userService->create($userData);

				if ($user) {
					$this->userMediaService->createUserMedia($user, $request);
					$this->userAddressService->createUserAddress($user, $request);
					$this->userKycService->_create($user, $request);
					$merchant = $user->getMerchant($user->id);

					return $this->sendResponse(['merchant' => $merchant], 'Merchant created successfully.');
				} else {
					throw new Exception("Error Processing Request", 1);
				}
			});
		} catch (Exception $ex) {
			return $this->sendError([], $ex->getMessage());
		}
	}


	public function update(MerchantRequest $request)
	{
		try {
			return DB::transaction(function () use ($request) {

				$user = $this->userService->fetchByID($request->id);

				if (!$user) {
					throw new Exception("User not found.", 404);
				}
				$password = staticPasswordByName($request->name);
				$userData = [
					'name' => $request->name,
					'phone_number' => $request->phone_number,
					'email' => $request->email,
					'country_code' => $request->country_code,
					'status' => $request->status ?? getConfigConstant('STATUS_PENDING')
				];

				if (isset($request->password) && $request->password) {
					$userData = array_merge($userData, ['password' => Hash::make($request->password)]);
				} else {
					$userData = array_merge($userData, ['password' => Hash::make($password)]);
				}

				$this->userService->updateRecord(['id' => $user->id], $userData);

				$this->userMediaService->updateUserMedia($user, $request);
				$this->userAddressService->updateUserAddress($user, $request);
				$this->userKycService->_update($user, $request);

				$merchant = $user->getMerchant($user->id);

				return $this->sendResponse(['merchant' => $merchant], 'Merchant updated successfully.');
			});
		} catch (Exception $ex) {
			return $this->sendError([], $ex->getMessage());
		}
	}

	public function delete(Request $request, $id)
	{
		try {
			$user = $this->userService->fetchByID($id);

			if (!$user) {
				throw new Exception("User not found.", 404);
			}

			$this->userService->remove($user->id);
			$this->userMediaService->remove($user->id);
			$this->userAddressService->remove($user->id);


			return redirect()->back()->with(['delete' => 'deleted successfully']);
		} catch (Exception $ex) {
			return $this->sendError([], $ex->getMessage());
		}
	}

	public function detail(Request $request)
	{
		try {
			$detail = $this->userService->fetchByID($request->id);

			if (!$detail) {
				throw new Exception("Merchant not found.", 404);
			}

			return $this->sendResponse($detail, 'Merchant fetched successfully.');
		} catch (Exception $ex) {
			return $this->sendError([], $ex->getMessage());
		}
	}

	public function view(Request $request)
	{
		$user = User::where('uuid', $request->id)->first();

		if(empty($user)){
			return redirect()->back();
		}
		$user_id = $user->id;
		$detail = $this->userService->fetchByID($user_id);
		$businessDetails = $this->businessDetailService->fetchOne(['user_id' => $user_id]);
		return view('admin.merchants.view')->with(['detail' => $detail, 'businessDetails' => $businessDetails]);
	}

	public function businessDetails($id)
	{
		$detail = $this->businessDetailService->fetchOne(['user_id' => $id]);
		if(!empty($detail)){
			return view('admin.merchants.business-details')->with(['detail' => $detail]);
		} else {
			abort(404);
		}
	}

	public function certifyMerchant(Request $request)
	{
		// try {
			$userModel = User::where('uuid', $request->customer)->first();
			if(!empty($userModel) && !empty($userModel->id)){
				$DwollaCustomer = DwollaCustomer::where('user_id', $userModel->id)->first();
				if(!empty($DwollaCustomer) && !empty($DwollaCustomer->customer_id)){
					$dwollaCustomerId = $DwollaCustomer->customer_id;
					$DwollaController = new DwollaController();
					$dwolla_access_token = $DwollaController->traitAccessToken();
					
					if(!empty($dwollaCustomerId)){
						$data = $DwollaController->traitCertifyBeneficialOwnership($dwolla_access_token, $request, $dwollaCustomerId);
						
						if(!empty($data) && isset($data->code) && $data->code == 'NotFound'){
							return $this->sendError([], $data->message, 500);
						}
						if(!empty($data) && isset($data->status) && $data->status == 'certified'){
							$userModel->status = getConfigConstant('STATUS_ACTIVE');
							$userModel->save();
							return $this->sendError([], 'certified', 200);
						}
						if(!empty($data) && isset($data->code) && $data->code == 'error'){
							return $this->sendError([], $data->message, 500);
						}
					}
			
					return true;

				}
			}
			return $this->sendError([], 'Data Not Valid', 500);
		// } catch (Exception $ex) {
		// 	\Log::info('certifyMerchant '.$ex);
		// 	return $this->sendError([], 'Server Error');
		// }
	}
}
