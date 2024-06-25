<?php

namespace App\Http\Controllers\Merchant\Auth;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Merchant\DwollaController;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\MerchantRequest;
use App\Http\Requests\OtpVerificationRequest;
use App\Http\Requests\WebMerchantRequest;
use App\Http\Requests\WebRegisterRequest;
use App\Jobs\SendEmailJob;
use App\Models\BusinessCategory;
use App\Models\BusinessDetail;
use App\Models\BusinessType;
use App\Models\DwollaCustomer;
use App\Models\User;
use App\Models\UserAddress;
use App\Models\UserKyc;
use App\Models\UserMedia;
use App\Models\UserReferalCode;
use App\Models\UserThroughReferalCode;
use App\Models\Verification;
use App\Models\Wallet;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use App\Traits\TwilioTrait;
use App\Traits\UploadFile;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Validation\Rule;
use Twilio\Rest\Client;
use Illuminate\Support\Str;


class LoginController extends Controller
{
	use TwilioTrait, UploadFile;

	protected User $userService;
	protected UserKyc $userKycService;
	protected UserMedia $userMediaService;
	protected UserAddress $userAddressService;
	protected Verification $verificationService;
	protected UserReferalCode $userReferalCodeService;
	protected UserThroughReferalCode $userThroughReferalCodeService;
	protected DwollaController $dwollaService;


	public function __construct(
		User $userService, 
		UserKyc $userKycService, 
		UserMedia $userMediaService, 
		UserAddress $userAddressService, 
		Verification $verificationService,
		UserReferalCode $userReferalCodeService, 
		UserThroughReferalCode $userThroughReferalCodeService,
		DwollaController $dwollaService
		)
	{
		$this->userService = $userService;
		$this->userKycService = $userKycService;
		$this->userMediaService = $userMediaService;
		$this->userAddressService = $userAddressService;
		$this->verificationService = $verificationService;
		$this->userReferalCodeService =  $userReferalCodeService;
		$this->userThroughReferalCodeService = $userThroughReferalCodeService;
		$this->dwollaService = $dwollaService;
	}

	public function merchantLogin()
	{
		return view('merchant.auth.login');
	}

	public function merchantRegister()
	{
		$BusinessCategory = BusinessCategory::select('*')->get();
		$BusinessType = BusinessType::select('*')->get();
		return view('merchant.auth.register', compact('BusinessCategory','BusinessType'));
	}

	public function otpVerification()
	{
		return view('merchant.auth.otpVerification');
	}

	public function emailOtpVerification()
	{
		return view('merchant.auth.emailOtpVerification');
	}

	public function testTwillioSms($phone)
	{
		$responce = $this->sendSms($phone, 'Wallet To Wallet Twillio Test');
		return 'done';
	}
	
	public function login(LoginRequest $request)
	{
		try {
			$userModel = User::where(['email' => $request->email])->first();
			if(!empty($userModel) && $userModel->status != getConfigConstant('STATUS_ACTIVE')){
				return $this->sendError([], 'Your account is not active, contact admin !');
			}

			if (Auth::attempt(['email' => $request->email, 'password' => $request->password, 'status' => getConfigConstant('STATUS_ACTIVE')])) {

				$user = Auth::user();
				if ($user->role_id != getConfigConstant('MERCHANT_ROLE_ID')) {
					Auth::logout();
					throw new Exception(trans('auth.failed'));
				}

				$token = $user->createToken('scash')->accessToken;

				return $this->sendResponse([
					'accessToken' => $token,
					'userData' => $user,
					'userAbilities' => [], // Adjust based on your user roles/permissions logic
				], 'Successfully logged in');
			}

			throw new Exception(trans('auth.failed'));
		} catch (Exception $ex) {
			return $this->sendError([], $ex->getMessage());
		}
	}

	public function logout(Request $request)
	{
		try {
			$request->user()->revokeTokens();

			return $this->sendResponse([], 'Successfully logged out');
		} catch (Exception $ex) {

			return $this->sendError([], $ex->getMessage());
		}
	}

	public function webLogout(Request $request)
	{
		try {
			Auth::logout();
			return redirect()->route('merchant.auth.login');

		} catch (Exception $ex) {

			return $this->sendError([], $ex->getMessage());
		}
	}

	public function verifyEmail()
	{
		return view('merchant.auth.VerificationDone');
	}

	public function passwordResetConfirmation()
	{
		return view('merchant.auth.passwordResetConfirmation');
	}

	public function profile(Request $request)
	{
		try {
			$user = Auth::user();

			$id = $request->id ?? $user->id;

			$userDetail = $this->userService->profile($id);

			return $this->sendResponse($userDetail, 'Profile fetched successfully.');
		} catch (Exception $ex) {

			return $this->sendError([], $ex->getMessage());
		}
	}

	function checkValidPhoneNumber(Request $request)
	{	
		$account_sid = config('services.twilio.sid');
		$auth_token = config('services.twilio.token');
        $twilioPhoneNumber = config('services.twilio.phone_number');

		$client = new Client($account_sid, $auth_token);
		try{
			$lookup = $client->lookups->v1->phoneNumbers($request->country_code.$request->phone_number)->fetch();
			return 'true';
		}catch(\Throwable $th){
			return 'false';
		}
		
	}

	public function webRegister(WebRegisterRequest $request)
	{

		$uuid = Str::uuid()->toString();
		$request->correlationId = $uuid;

		$userModel = User::where(['email' => $request->email])->withTrashed()->first();
		if(!empty($userModel) && $userModel->status == getConfigConstant('STATUS_ACTIVE')){
			return $this->sendError([], 'This email is already registered', 403);
		}
		if(!empty($userModel) && $userModel->phone_number == $request->phone_number && $userModel->status == getConfigConstant('STATUS_ACTIVE')){
			return $this->sendError([], 'This email and phone number are already registered', 403);
		}

		$checkValidPhoneNumber = $this->checkValidPhoneNumber($request);
		if($checkValidPhoneNumber == 'false'){
			return $this->sendError([], 'Phone no is no valid, Try again', 403);
		}

		try {
			DB::beginTransaction();
			
			$userData = [
				'name' => $request->first_name.' '.$request->last_name,
				'first_name' => $request->first_name,
				'last_name' => $request->last_name,
				'email' => $request->email,
				'phone_number' => $request->phone_number,
				'country_code' => $request->country_code,
				'date_of_birth' => $request->dob,
				'password' => Hash::make($request->password),
				'role_id' => getConfigConstant('MERCHANT_ROLE_ID'),
				'status' => getConfigConstant('STATUS_KYC_VERIFICATION'),
				'uuid' => $uuid,
			];
			
			$user = User::updateOrCreate(
				['email' => $request->email],
				$userData
			);
			
			if ($user) {
				$KycDetails = $this->userKycService->_create($user, $request);
				$addessDetails = $this->_saveAddress($request, $user);
				$businessDetails = $this->_saveBusinessDetails($request, $user);
				$WalletDetails = $this->_saveWalletDetails($request, $user);
				$this->_uploadImage($request, $user);

				$dwollaCustomerData = $this->_createCustomer($request, $user);

				if(!empty($dwollaCustomerData) && $dwollaCustomerData['status'] == 'error'){
					return $this->sendError([], $dwollaCustomerData['data'], 500);
				}

				$this->_SaveOtpVerification($request);
				
				DB::commit();
				
				$details['email'] = $request->email;
				$details['password'] = $request->password;
				$responce = dispatch(new SendEmailJob($details, 'MerchantCredentials', $request->email));

				return $this->sendResponse(true, [], 'Register Successfull.');

			} else {
				DB::rollBack();
				return $this->sendError([], 'Server error, Try again', 500);
			}
			
		} catch (Exception $ex) {
			DB::rollBack();
			\Log::info($ex);
			return $this->sendError([], 'Server error, Try again', 500);
		}
	}

	private function _SaveOtpVerification($request)
	{
		$code = rand(1000,9999);
		$code1 = rand(1000,9999);
		$start = date('Y-m-d H:i:s');
		$details['code'] = $code1;
		$input = array_merge([
			'phone_number' => $request->phone_number, 
			'country_code' => $request->country_code, 
			'email' => $request->email, 
			'code' => $code, 
			'expired_at' => date('Y-m-d H:i:s', strtotime('+1 hour', strtotime($start))),
			'email_code' => $code1, 
			'status' => 0, 
			'email_status' => 0, 
		]);
		$user = Verification::updateOrCreate(
			['email' => $request->email, 'phone_number' => $request->phone_number],
			$input
		);
		if(!empty($input['country_code']) && !empty($input['phone_number'])){
			$sendSmsNumber = $input['country_code'].''.$input['phone_number'];
		} else {
			$sendSmsNumber = $input['phone_number'];
		}

		$responce = dispatch(new SendEmailJob($details, 'OtpVerification', $request->email));
		$otp_message = 'Your Wallet verification code is: '.$input['code'];
		$responce = $this->sendSms($sendSmsNumber, $otp_message);
		$responce = '';
		return $responce;
	}

	private function _saveAddress($request, $user)
	{
		$UserAddress = UserAddress::updateOrCreate(
			['user_id' => $user->id],
			[
				'user_id' => $user->id,
				'address' => $request->address,
				'country' => $request->country,
				'state' => $request->state,
				'city' => $request->city,
				'latitude' => $request->latitude,
				'longitude' => $request->longitude,
				'line_1' => $request->line_1,
				'line_2' => $request->line_2,
				'postal_code' => $request->zip_code,
				'address_2' => $request->address_2,
				'country_2' => $request->country_2,
				'state_2' => $request->state_2,
				'city_2' => $request->city_2,
				'latitude_2' => $request->latitude_2,
				'longitude_2' => $request->longitude_2,
				'postal_code_2' => $request->zip_code_2,
			]
		);

		return $UserAddress;
	}

	private function _saveBusinessDetails($request, $user)
	{
		$businessDetailsData = [
			'user_id' => $user->id,
			'tax_type' => $request->tax_type,
			'registration_type' => $request->registration_type,
			'business_name' => $request->business_name,
			'about_business' => $request->about_business,
			'business_type' => $request->business_type,
			'business_category' => $request->business_category,
			'leagal_name' => $request->leagal_name,
			'business_street_address' => $request->business_address,
			'business_city' => $request->business_city,
			'business_state' => $request->business_state,
			'business_zip_code' => $request->business_zip_code,
			'business_ein' => !empty($request->business_ein) ? Crypt::encryptString($request->business_ein) : '',
			'business_phone_number' => $request->phone_number,
			'ssn_itin' => !empty($request->ssn_itin) ? Crypt::encryptString($request->ssn_itin) : '',
			'email' => $request->email,		];
		
		if($request->hasFile('logo')){
			$uploadImage = $this->imageUpload($request->logo);
			$businessDetailsData['logo'] = $uploadImage['url'];
		}
		
		$businessDetails = BusinessDetail::updateOrCreate(
			['user_id' => $user->id],
			$businessDetailsData
		);
	}

	private function _saveWalletDetails($request, $user)
	{
		$WalletDetailsData = [
			'user_id' => $user->id,
			'wallet_id' => ''
		];
				
		$WalletDetails = Wallet::updateOrCreate(
			['user_id' => $user->id],
			$WalletDetailsData
		);
		return $WalletDetails;
	}

	private function _uploadImage($request, $user)
	{
		if($request->hasFile('logo')){
			$logo = $request->logo;
			$uploadImage = $this->imageUpload($logo);
			
			$userMedia = UserMedia::updateOrCreate(
				['user_id' => $user->id, 'type' => UserMedia::TYPE_IMAGE],
				[
					'user_id' => $user->id,
					'file' => $uploadImage['url'],
					'type' => UserMedia::TYPE_IMAGE
				]
			);
		}
		if($request->hasFile('business_proff')){
			$business_proff = $request->business_proff;
			$business_proff = $this->imageUpload($business_proff);

			$userMedia = UserMedia::updateOrCreate(
				['user_id' => $user->id, 'type' => UserMedia::TYPE_DOCUMENT],
				[
					'user_id' => $user->id,
					'file' => $business_proff['url'],
					'type' => UserMedia::TYPE_DOCUMENT
				]
			);
		}

		return $userMedia;
	}

	private function _createCustomer($request, $user)
	{
		$businessType = BusinessType::where('id', $request->registration_type)->first();
		$request->businessType = $businessType->dwolla_key;

		$dwolla_access_token = $this->dwollaService->traitAccessToken();
		$userModel = User::where('id', $user->id)->with('address','BusinessDetail')->first();
		$DwollaCustomer = DwollaCustomer::where('user_id', $user->id)->first();

		$dwollaCustomerId = $this->dwollaService->traitGetCustomersIdByEmail($dwolla_access_token, $request->email);

		$errorString = '';
		if(empty($dwollaCustomerId)){
			if($businessType->type == 1){
				$customer_data = $this->dwollaService->traitCreateSoloBusinessCustomer($dwolla_access_token, $userModel, $request);
			} else {
				$customer_data = $this->dwollaService->traitCreateNonSoloBusinessCustomer($dwolla_access_token, $userModel, $request);
			}

			if(!empty($customer_data->code) && $customer_data->code == 'ValidationError'){
				foreach($customer_data->_embedded->errors as $arrError){
					$errorString .= ' '.$arrError->message;
				}
				return ['status' => 'error', 'data' => $errorString];
			}

			$customer_id = $customer_data->_embedded->customers[0]->id;

		    if(!empty($customer_id)){
		        $DwollaCustomer = DwollaCustomer::updateOrCreate(
		            ['user_id' => $user->id],
		            [
		                'user_id' => $user->id,
		                'customer_id' => $customer_id,
		            ]
		        );

				if($customer_data->_embedded->customers[0]->status == 'document'){
					return ['status' => 'error', 'data' => 'SSN or EIN not valid need document'];
				}

				$wallet_data = $this->dwollaService->traitBankList($dwolla_access_token, $customer_id);
				if(!empty($wallet_data) && $wallet_data->_embedded){
					$wallet_single_data = end($wallet_data->_embedded->{'funding-sources'});
					if(!empty($wallet_single_data) && !empty($wallet_single_data->id)){
						Wallet::updateOrCreate(
							['user_id' => $user->id],
							[
								'user_id' => $user->id,
								'wallet_id' => $wallet_single_data->id
							]
						);

					}

				}

				if($businessType->type == 1){
					$user = User::updateOrCreate(
						['email' => $request->email],
						['status' => getConfigConstant('STATUS_ACTIVE'),]
					);
				} else {
					$user = User::updateOrCreate(
						['email' => $request->email],
						['status' => getConfigConstant('STATUS_KYC_VERIFICATION'),]
					);
				}


				return $DwollaCustomer;
		    }

			if($customer_data->_embedded->customers[0]->status == 'document'){
				return ['status' => 'error', 'data' => 'SSN or EIN not valid need document'];
			}


		} else {

			if($request->hasFile('verification_document')){
				$swollaCustomerDocument = $this->_customerDocument($request, $dwollaCustomerId);

				if(!empty($dwollaCustomerId)){
					$DwollaCustomer = DwollaCustomer::updateOrCreate(
						['user_id' => $user->id],
						[
							'user_id' => $user->id,
							'customer_id' => $dwollaCustomerId,
						]
					);
				}

			} else {
				return $this->sendError([], 'Document Required', 500);
			}
			
		}

		return $DwollaCustomer;

	}

	private function _customerDocument($request, $dwollaCustomerId)
	{
		$dwolla_access_token = $this->dwollaService->traitAccessToken();
		
		if(!empty($dwollaCustomerId)){
		    $document = $this->dwollaService->traitCreateCustomerDocument($dwolla_access_token, $request, $dwollaCustomerId);
		}

		return $dwollaCustomerId;

	}

	public function verifyOtp(OtpVerificationRequest $request)
	{

		try {
			$input = $request->all();
			$input = $this->prepareInput($request, $request->code);

			if(!empty($input['phone_number'])){
				// send SMS
				$verify = Verification::where('phone_number', $input['phone_number'])
				->where('code', $request->code)->first();
				
				if(empty($verify)){ throw new Exception('Invalid OTP.'); }

				$verify->status = getConfigConstant('OTP_VERIFIED');
				$verify->save();
				if($verify->status == getConfigConstant('OTP_VERIFIED') && $verify->email_status == getConfigConstant('OTP_VERIFIED'))
				{
					return $this->sendResponse([], 'Otp Verified successfully.', route('merchant.auth.verifyEmail'));
				}

			} else {
				// send mail using queue
				$verify = Verification::where('email', $input['email'])
				->where('email_code', $request->code)->first();

				if(empty($verify)){ throw new Exception('Invalid OTP.'); }
				$verify->email_status = getConfigConstant('OTP_VERIFIED');
				$verify->save();

				if($verify->status == getConfigConstant('OTP_VERIFIED') && $verify->email_status == getConfigConstant('OTP_VERIFIED'))
				{
					return $this->sendResponse([], 'Otp Verified successfully.', route('merchant.auth.verifyEmail'));
				}

			}
			
			return $this->sendResponse([], 'Otp Verified successfully.', route('merchant.auth.email-otp-verification'));

		} catch (Exception $ex) {
			return $this->sendError([], $ex->getMessage());
		}
	}

	private function prepareInput($request, $code): array
	{
		$inputResult = checkEmailOrMobile($request->email_phone_number);

		return [
			'code' => $code,
			'email' => $inputResult['email'] ?? null,
			'phone_number' => $inputResult['phone_number'] ?? null,
			'country_code' => $request->country_code ?? null,
		];
	}


	private function autoLoginUser(User $createdUser): void
	{
		Auth::login($createdUser);
		$token = $createdUser->createToken('scash')->accessToken;
		$createdUser->token = $token;
	}

	public function register(MerchantRequest $request)
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
					'status' => getConfigConstant('STATUS_KYC_VERIFICATION')
				];

				$user = $this->userService->create($userData);

				if ($user) {
					$this->userMediaService->createUserMedia($user, $request);
					$this->userAddressService->createUserAddress($user, $request);
					$this->userKycService->_create($user, $request);

					return $this->sendResponse($user, 'Successfully register account');
				} else {
					throw new Exception("Error Processing Request", 1);
				}
			});
		} catch (Exception $ex) {
			return $this->sendError([], $ex->getMessage());
		}
	}

	public function checkMailExist(Request $request)
	{
		$validatedData = $request->validate([
			'email' => [
				'required',
				'email'
			],
		]);

			return json_encode(["notexists" => 'null']);

	}
}
