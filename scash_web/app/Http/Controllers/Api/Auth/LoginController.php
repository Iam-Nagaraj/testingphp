<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Merchant\DwollaController;
use App\Http\Requests\ApiLoginRequest;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\SendOtpRequest;
use App\Http\Requests\SendReferalCodeRequest;
use App\Http\Requests\SocialLoginRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Http\Requests\UserRequest;
use App\Http\Requests\UserUpdateRequest;
use App\Http\Requests\VerifyOtpRequest;
use App\Http\Resources\UserDetailResource;
use Exception;
use Illuminate\Http\Request;
use App\Models\Verification;
use App\Models\DeviceToken;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Http\Resources\UserResource;
use App\Http\Resources\UserVerifyResource;
use App\Models\UserAddress;
use App\Models\UserMedia;
use App\Models\UserReferalCode;
use App\Models\UserThroughReferalCode;
use App\Traits\TwilioTrait;
use App\Jobs\SendEmailJob;
use App\Models\BusinessDetail;
use App\Models\Configuration;
use App\Models\DwollaCustomer;
use App\Models\ReferralTransaction;
use App\Models\SocialAccount;
use App\Models\Wallet;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Twilio\Rest\Client;

class LoginController extends Controller
{
	use TwilioTrait;

	protected Verification $verificationService;
	protected DeviceToken $deviceTokenService;
	protected User $userService;
	protected Wallet $walletService;
	protected $STATIC_OTP, $OTP_PENDING, $OTP_VERIFIED, $USER_ROLE_ID;
	protected UserMedia $userMediaService;
	protected UserAddress $userAddressService;
	protected UserReferalCode $userReferalCodeService;
	protected SocialAccount $userSocialAccount;
	protected UserThroughReferalCode $userThroughReferalCodeService;

	public function __construct(
		Verification $verificationService, 
		DeviceToken $deviceTokenService, 
		User $userService, 
		Wallet $walletService, 
		UserMedia $userMediaService, 
		UserAddress $userAddressService, 
		UserReferalCode $userReferalCodeService, 
		UserThroughReferalCode $userThroughReferalCodeService, 
		SocialAccount $userSocialAccount
		)
	{
		$this->verificationService = $verificationService;
		$this->deviceTokenService = $deviceTokenService;
		$this->userService = $userService;
		$this->walletService = $walletService;
		$this->userMediaService = $userMediaService;
		$this->userAddressService = $userAddressService;
		$this->STATIC_OTP = getConfigConstant('STATIC_OTP');
		$this->OTP_PENDING = getConfigConstant('OTP_PENDING');
		$this->OTP_VERIFIED = getConfigConstant('OTP_VERIFIED');
		$this->USER_ROLE_ID = getConfigConstant('USER_ROLE_ID');
		$this->userReferalCodeService =  $userReferalCodeService;
		$this->userSocialAccount =  $userSocialAccount;
		$this->userThroughReferalCodeService = $userThroughReferalCodeService;
	}

	/**
     * @OA\Post(
     ** path="/api/v1/auth/send-otp",
     *   tags={"Auth"},
     *   summary="Only for test",
     *   operationId="send-otp",
     *   @OA\RequestBody(
     *	    required=true,
	 *  	@OA\JsonContent(
	 *      	type="object",
	 *      	@OA\Property(property="email_phone_number", type="string", example="admin@gmail.com"),
	 *      	@OA\Property(property="country_code", type="string", example="+91"),
	 *      )
	 *   ),
     *   @OA\Response(
     *      response=401,
     *       description="Unauthenticated"
     *   ),
     *   @OA\Response(
     *      response=400,
     *      description="Bad Request"
     *   ),
     *   @OA\Response(
     *      response=404,
     *      description="not found"
     *   ),
     *      @OA\Response(
     *          response=403,
     *          description="Forbidden"
     *      )
     *)
    **/
	public function sendOtp(SendOtpRequest $request)
	{
		try {
			$input = $this->prepareInput($request);

			// $this->verificationService->store($input);
			$verification = new Verification();
			$start = date('Y-m-d H:i:s');
			$expired_at = date('Y-m-d H:i:s', strtotime('+1 hour', strtotime($start)));
			$code = rand(1000,9999);
			if(!empty($input['phone_number'])){
				$userModel = User::where('phone_number', $input['phone_number'])->first();
				// if(!empty($userModel) && $request->type == 'signup'){
				// 	return $this->sendError([], 'Phone number already exist!', 400);
				// } 
				if(!empty($userModel) && $request->type == 'login'){
					return $this->sendResponse([], 'success.');
				}
				// send SMS
				$verification->expired_at = $expired_at;
				$verification->code = $code;
				$verification->phone_number = $input['phone_number'];
				$verification->country_code = $input['country_code'];
				$verification->save();

				if(isset($request->hashKey)){
					$hashKey = $request->hashKey;
					$otp_message = 'Your Wallet verification code is: '.$code.' '.$hashKey;
				} else {
					$otp_message = 'Your Wallet verification code is: '.$code;
				}

				$responce = $this->sendSms('+'.$input['country_code'].''.$input['phone_number'], $otp_message);
			} else {
				$userModel = User::where('email', $input['email'])->first();
				if(!empty($userModel) && $request->type == 'signup'){
					return $this->sendError([], 'Email already exist!', 400);
				}
				if(!empty($userModel) && $request->type == 'login'){
					return $this->sendResponse([], 'success.');
				}

				$verification->expired_at = $expired_at;
				$verification->email_code = $code;
				$verification->email = $input['email'];
				$verification->save();

				// send mail using queue
				$details['code'] = $code;
				$details['email'] = $input['email'];

				$responce = dispatch(new SendEmailJob($details, 'OtpVerification', $input['email']));

			}

			return $this->sendResponse([], 'OTP sent successfully.');
		} catch (Exception $ex) {
			return $this->sendError([], $ex->getMessage());
		}
	}

	public function sendReferalCode(SendReferalCodeRequest $request)
	{

		foreach($request->phone_number as $single){
			$message = 'Join the Scash App & get rewarded! Use my code '.$request->referal_code.' to get cashback';
			$responce = $this->sendSms($single, $message);
		}
		
		return $this->sendResponse([], 'Referral Code Send successfully.');
		
	}

	/**
     * @OA\Post(
     ** path="/api/v1/auth/login",
     *   tags={"Auth"},
     *   summary="Only for test",
     *   operationId="user-login",
     *   @OA\RequestBody(
     *	    required=true,
	 *  	@OA\JsonContent(
	 *      	type="object",
	 *      	@OA\Property(property="email", type="string", example="admin@gmail.com"),
	 *      	@OA\Property(property="password", type="string", example="abc@123"),
	 *      )
	 *   ),
     *   @OA\Response(
     *      response=401,
     *       description="Unauthenticated"
     *   ),
     *   @OA\Response(
     *      response=400,
     *      description="Bad Request"
     *   ),
     *   @OA\Response(
     *      response=404,
     *      description="not found"
     *   ),
     *      @OA\Response(
     *          response=403,
     *          description="Forbidden"
     *      )
     *)
    **/
	public function login(ApiLoginRequest $request)
	{
		$input = $this->prepareInput($request);
		
		try {
			if(!empty($input['phone_number'])){
				$userModel = User::where('phone_number', $input['phone_number'])->first();
			} else {
				$userModel = User::where('email', $input['email'])->first();
			}

			if(empty($userModel)){
				return $this->sendError([], 'User Not Found!');
			}

			if (Auth::attempt([
				'email' => $userModel->email, 
				'password' => $request->password, 
				'role_id' => getConfigConstant('USER_ROLE_ID')
				])) {

				$user = Auth::user();
				$token = $user->createToken('scash')->accessToken;
				$user->token = $token;
				$userReferalCode = userReferalCode::where('user_id', $user->id)->first();
				$user->referalCode = $userReferalCode->referal_code??'';

				if(isset($request->device_token)){
					$data['device_token'] = $request->device_token;
					$data['device_type'] = $request->device_type;

					$this->handleDeviceToken($data, $user);
				}


				return $this->sendResponse(new UserResource($user), 'Sign Up successfully.');

			}

			return $this->sendError([], 'Incorrect Password');

		} catch (Exception $ex) {
			return $this->sendError([], $ex->getMessage());
		}
	}

	public function checkMerchantLogin(LoginRequest $request)
	{
		try {
			$userModel = User::where(['email' => $request->email])->first();
			if(empty($userModel)){
				return $this->sendError([], 'User Not Found!');
			}

			if (Auth::attempt([
				'email' => $request->email, 
				'password' => $request->password, 
				'role_id' => getConfigConstant('MERCHANT_ROLE_ID')
				])) {

				$user = Auth::user();
				$token = $user->createToken('scash')->accessToken;
				$user->token = $token;

				return $this->sendResponse(new UserResource($user), 'Sign Up successfully.');

			}

			return $this->sendError([], 'User Not Found');

		} catch (Exception $ex) {
			return $this->sendError([], $ex->getMessage());
		}
	}

	private function prepareInput($request): array
	{
		$inputResult = checkEmailOrMobile($request->email_phone_number);

		if (isset($inputResult['phone_number']) && empty($request->country_code)) {
			throw new Exception('Country code is required with phone number.');
		}

		$code = rand(1000,9999);

		return [
			'code' => $code,
			'email' => $inputResult['email'] ?? null,
			'phone_number' => $inputResult['phone_number'] ?? null,
			'country_code' => $request->country_code ?? null,
		];
	}

	/**
     * @OA\Post(
     ** path="/api/v1/auth/verify-otp",
     *   tags={"Auth"},
     *   summary="Only for test",
     *   operationId="verify-otp",
     *   @OA\RequestBody(
     *	    required=true,
	 *  	@OA\JsonContent(
	 *      	type="object",
	 *      	@OA\Property(property="email_phone_number", type="string", example="9636963696"),
	 *      	@OA\Property(property="country_code", type="string", example="+91"),
	 *      	@OA\Property(property="otp", type="string", example="4920"),
	 *      	@OA\Property(property="device_token", type="string", example="1234"),
	 *      	@OA\Property(property="device_type", type="string", example="IOS"),
	 *      )
	 *   ),
     *   @OA\Response(
     *      response=401,
     *       description="Unauthenticated"
     *   ),
     *   @OA\Response(
     *      response=400,
     *      description="Bad Request"
     *   ),
     *   @OA\Response(
     *      response=404,
     *      description="not found"
     *   ),
     *      @OA\Response(
     *          response=403,
     *          description="Forbidden"
     *      )
     *)
    **/
	public function verifyOtp(VerifyOtpRequest $request)
	{
		try {
			$input = $request->all();
			$inputData = $this->prepareInputData($input);
			$verificationData = $this->prepareVerificationData($inputData, $input['otp']);

			if(!empty($inputData['phone_number'])){

				$verify = Verification::where('phone_number', $inputData['phone_number'])
				->where('code', $request->otp)->first();
				
				if(empty($verify)){ throw new Exception('Invalid OTP.'); }

				$verify->status = getConfigConstant('OTP_VERIFIED');
				$verify->save();
				if($verify)
				{
					$handleVerificationSuccess = $this->handleVerificationSuccess($input, $verify, $inputData, $verificationData);
					return $this->sendResponse($handleVerificationSuccess, 'Verify Otp successfully.');
				}

			} else {
		
				$verify = Verification::where('email', $inputData['email'])
				->where('email_code', $request->otp)->first();

				if(empty($verify)){ throw new Exception('Invalid OTP.'); }
				$verify->email_status = getConfigConstant('OTP_VERIFIED');
				$verify->save();

				if($verify)
				{
					$handleVerificationSuccess = $this->handleVerificationSuccess($input, $verify, $inputData, $verificationData);
					return $this->sendResponse($handleVerificationSuccess, 'Verify Otp successfully.');
				}

			}

			throw new Exception('Invalid OTP.');
		} catch (Exception $ex) {
			return $this->sendError([], $ex->getMessage());
		}
	}

	private function prepareInputData($input): array
	{
		$inputResult = checkEmailOrMobile($input['email_phone_number']);

		if (isset($inputResult['phone_number']) && $input['country_code'] == '') {
			throw new Exception('Country code is required with phone number.');
		}


		if (isset($inputResult['email'])) {
			return ['email' => $inputResult['email']];
		} else if ($inputResult['phone_number']) {
			return [
				'phone_number' => $inputResult['phone_number'],
				'country_code' => $input['country_code'],
			];
		}

		return [];
	}

	private function prepareVerificationData(array $inputData, string $otp): array
	{
		return [
			'code' => $otp,
			'status' => $this->OTP_PENDING,
			...$inputData,
		];
	}

	private function handleVerificationSuccess(array $input, Verification $verify, array $inputData, array $verificationData)
	{

		// $this->updateVerificationStatus($verificationData, $verify);
		$createdUser = $this->userService->fetchOne($inputData);

		if ($createdUser) {
			$this->handleDeviceToken($input, $createdUser);
			$this->autoLoginUser($createdUser);

			return new UserResource($createdUser);
		}


		return new UserVerifyResource($verificationData);
	}

	private function updateVerificationStatus(array $verificationData, Verification $verify): void
	{
		$verify->updateRecord($verificationData, ['status' => $this->OTP_VERIFIED]);
	}

	private function autoLoginUser(User $createdUser): void
	{
		Auth::login($createdUser);
		$token = $createdUser->createToken('scash')->accessToken;
		$createdUser->token = $token;
	}

	private function handleDeviceToken(array $input, User $createdUser): void
	{
		if ($input['device_token']) {
			$this->deviceTokenService->store($input, $createdUser);
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


	public function signUp(UserRequest $request)
	{		

		try {
			if($request->referal_code){
				$userReferalCode = userReferalCode::where('referal_code', $request->referal_code)->first();
				if(empty($userReferalCode)){
					return $this->sendError([], 'Invalid Referal Code');
				}
			}

			$userModel = User::where('phone_number', $request->phone_number)->first();
			if(!empty($userModel)){
				return $this->sendError([], 'Phone number already exist');
			}
			$userModel = User::where('email', $request->email)->first();
			if(!empty($userModel)){
				return $this->sendError([], 'Email already exist');
			}

			$checkValidPhoneNumber = $this->checkValidPhoneNumber($request);
			if($checkValidPhoneNumber == 'false'){
				return $this->sendError([], 'Phone no is no valid, Try again', 403);
			}

			if($request->ssn == '0000'){
				return $this->sendError(['error_key' => 'need_9_digit'], 'SSN not valid, please try 9 digit ssn', 500);
			}
			if($request->ssn == '000-0'){
				return $this->sendError(['error_key' => 'need_9_digit'], 'SSN not valid, please try 9 digit ssn', 500);
			}

			DB::beginTransaction();


				$input = $request->all();

				$input = array_merge($input, ['role_id' => $this->USER_ROLE_ID]);

				$userVerifyResource = new UserVerifyResource($input);

				$userVerifyArray = $userVerifyResource->toArray();

				$user = $this->userService->store($input);

				if ($user) {

					if($request->hasFile('image')){
						$this->userMediaService->createUserMedia($user, $request);
					}
					$this->userReferalCodeService->_create($user);
					$this->userThroughReferalCodeService->_create($user, $request);

					if($request->referal_code){
						$userReferalCode = userReferalCode::where('referal_code', $request->referal_code)->first();
						if(!empty($userReferalCode)){
							$ConfigurationModel = Configuration::where('config_key', 'referral')->first();

							Wallet::updateOrCreate(
								['user_id' => $user->id],
								[
									'user_id' => $user->id,
									'referral_amount' => $ConfigurationModel?$ConfigurationModel->config_value:0
								]
							);

							Wallet::updateOrCreate(
								['user_id' => $userReferalCode->user_id],
								[
									'user_id' => $userReferalCode->user_id,
									'referral_amount' => $ConfigurationModel?$ConfigurationModel->config_value:0
								]
							);

							$ReferalTransaction = new ReferralTransaction();
							$ReferalTransaction->from = $userReferalCode->user_id;
							$ReferalTransaction->to = $user->id;
							$ReferalTransaction->referral_amount = $ConfigurationModel?$ConfigurationModel->config_value:0;
							$ReferalTransaction->save();
						}
					}

					BusinessDetail::updateOrCreate(
						['user_id' => $user->id],
						[
							'user_id' => $user->id,
							'registration_type' => getConfigConstant('BUSINESS_TYPE_SSN'),
							'ssn_itin' => !empty($request->ssn) ? Crypt::encryptString($request->ssn) : '',
						]
					);

					$dwollaCustomerData = $this->_createCustomer($request, $user);

					if(!empty($dwollaCustomerData) && $dwollaCustomerData['status'] == 'error'){
						if(isset($dwollaCustomerData['error_key'])){
							return $this->sendError(['error_key' => $dwollaCustomerData['error_key']], $dwollaCustomerData['data'], 500);
						} else {
							return $this->sendError([], $dwollaCustomerData['data'], 500);
						}
					}

					DB::commit();

					$userDetail = $user->profile($user->id);

					$this->autoLoginUser($userDetail);

					return $this->sendResponse(new UserResource($userDetail), 'Sign Up successfully.');
				} else {
					DB::rollBack();

					throw new Exception("Error Processing Request", 1);
				}
				
			
		} catch (Exception $ex) {
			DB::rollBack();
			Log::info($ex);
			return $this->sendError([], 'Server Error');
		}
	}

	private function _createCustomer($request, $user)
	{
		$DwollaController = new DwollaController();

		$dwolla_access_token = $DwollaController->traitAccessToken();
		$userModel = User::where('id', $user->id)->with('address','BusinessDetail')->first();
		$DwollaCustomer = DwollaCustomer::where('user_id', $user->id)->first();
		$errorString = '';

		$dwollaCustomerId = $DwollaController->traitGetCustomersIdByEmail($dwolla_access_token, $request->email);

		if(empty($dwollaCustomerId)){
		    $customer_data = $DwollaController->traitCreateCustomers($dwolla_access_token, $userModel, $request);

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
					return ['status' => 'error', 'data' => 'SSN not valid need document', 'error_key' => 'need_document'];
				}

				$wallet_data = $DwollaController->traitBankList($dwolla_access_token, $customer_id);
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

				return $DwollaCustomer;
		    }
		}  else {
			
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
				return ['status' => 'error', 'data' => 'SSN not valid need document', 'error_key' => 'need_document'];
			}
			
		}

		return $DwollaCustomer;

	}

	private function _customerDocument($request, $dwollaCustomerId)
	{
		$DwollaController = new DwollaController();

		$dwolla_access_token = $DwollaController->traitAccessToken();
		
		if(!empty($dwollaCustomerId)){
		    $document = $DwollaController->traitCreateCustomerDocument($dwolla_access_token, $request, $dwollaCustomerId);
		}

		return $dwollaCustomerId;

	}

	/**
     * @OA\Post(
     ** path="/api/v1/auth/social-login",
     *   tags={"Auth"},
     *   summary="Only for test",
     *   operationId="social-login",
     *   @OA\RequestBody(
     *	    required=true,
	 *  	@OA\JsonContent(
	 *      	type="object",
	 *      	@OA\Property(property="name", type="string", example="Ram"),
	 *      	@OA\Property(property="email", type="string", example="ram@gmail.com"),
	 *      	@OA\Property(property="password", type="string", example="12345678"),
	 *      	@OA\Property(property="provider_id", type="string", example="HGFHF5456"),
	 *      	@OA\Property(property="type", type="string", example="User"),
	 *      )
	 *   ),
     *   @OA\Response(
     *      response=401,
     *       description="Unauthenticated"
     *   ),
     *   @OA\Response(
     *      response=400,
     *      description="Bad Request"
     *   ),
     *   @OA\Response(
     *      response=404,
     *      description="not found"
     *   ),
     *      @OA\Response(
     *          response=403,
     *          description="Forbidden"
     *      )
     *)
    **/
	public function socialLogin(SocialLoginRequest $request)
	{
		try {
			return DB::transaction(function () use ($request) {
				
				$input = $request->all();
				
				$input = array_merge($input, ['role_id' => $this->USER_ROLE_ID]);
				
				$user = $this->userService->store($input);
				
				if ($user) {
					
					$this->userSocialAccount->_create($user, $request);
					$this->walletService->_create($user, $balance = 0);
					$this->userReferalCodeService->_create($user);
					$this->userThroughReferalCodeService->_create($user, $request);
					
					$userDetail = $user->profile($user->id);
					$this->autoLoginUser($userDetail);

					return $this->sendResponse(new UserResource($userDetail), 'Sign Up successfully.');
				} else {
					throw new Exception("Error Processing Request", 1);
				}
				
			});
		} catch (Exception $ex) {
			return $this->sendError([], $ex->getMessage());
		}
	}

	public function updateProfile(UserUpdateRequest $request)
	{
		try {

			return DB::transaction(function () use ($request) {

				if (isset($request->id)) {
					$user = $this->userService->profile($request->id);
				} else {
					$user = Auth::user();
				}

				if (!$user) {
					throw new Exception("User not found.", 404);
				}

				$userData = [
					'phone_number' => $request->phone_number
				];

				User::updateOrCreate(
					['id' => Auth::user()->id],
					$userData
				);

				$addressData = [
					'address' => $request->address,
					'state' => $request->state,
					'city' => $request->city,
					'latitude' => $request->latitude,
					'longitude' => $request->longitude,
					'postal_code' => $request->zipcode,
					'user_id' => Auth::user()->id
				];

				UserAddress::updateOrCreate(
					['user_id' => Auth::user()->id],
					$addressData
				);

				if(!empty($request->image)){
					UserMedia::updateOrCreate(
						['user_id' => $user->id],
						[
							'user_id' => $user->id,
							'file' => $request->image,
							'type' => '1',
						]
					);
				}

				$userDetail = $user->profile($user->id);
				
				$accessToken = $request->header('Authorization');

				$userDetail->token = $accessToken;

				return $this->sendResponse(new UserDetailResource($userDetail), 'User updated successfully.');
			});
		} catch (Exception $ex) {
			return $this->sendError([], $ex->getMessage());
		}
	}


	public function getProfile(Request $request)
	{
		try {
			return DB::transaction(function () use ($request) {

				if (isset($request->id)) {
					$userDetail = $this->userService->profile($request->id);
				} else {
					$userDetail = Auth::user();
				}

				if (!$userDetail) {
					throw new Exception("User not found.", 404);
				}
				$accessToken = $request->header('Authorization');

				$userDetail->token = $accessToken;
				$userReferalCode = userReferalCode::where('user_id', $userDetail->id)->first();
				$userDetail->referalCode = $userReferalCode->referal_code??'';

				return $this->sendResponse(new UserResource($userDetail), 'User fetched successfully.');
			});
		} catch (Exception $ex) {
			return $this->sendError([], $ex->getMessage());
		}
	}

	public function logout(Request $request)
    {
        if ($request->user()) {
            $request->user()->token()->delete();
        }
		return $this->sendResponse('', 'User Logged out successfully.');
    }
}
