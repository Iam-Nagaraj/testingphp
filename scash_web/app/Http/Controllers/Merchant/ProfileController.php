<?php

namespace App\Http\Controllers\Merchant;

use App\Http\Controllers\Controller;
use App\Http\Requests\EmailRequest;
use App\Http\Requests\MerchantProfileRequest;
use App\Http\Requests\OtpVerificationRequest;
use App\Http\Requests\PhoneRequest;
use App\Http\Requests\ProfileRequest;
use App\Jobs\SendEmailJob;
use App\Models\BusinessCategory;
use App\Models\BusinessDetail;
use App\Models\DwollaCustomer;
use App\Models\Tax;
use App\Models\Transaction;
use App\Models\User;
use App\Models\UserAddress;
use App\Models\UserMedia;
use App\Models\Verification;
use App\Traits\TwilioTrait;
use App\Traits\UploadFile;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\DataTables;

class ProfileController extends Controller
{
	use UploadFile, TwilioTrait;

	protected Transaction $transactionService;
	protected DwollaController $dwollaService;

	public function __construct(
		Transaction $transactionService,
		DwollaController $dwollaService
		)
	{
		$this->transactionService = $transactionService;
		$this->dwollaService = $dwollaService;
	}

	public function profile()
	{
		$BusinessCategory = BusinessCategory::select('id','name')->pluck('name','id');
		$detail = User::where('id', Auth::user()->id)->first();
		return view('merchant.profile.index', compact('detail','BusinessCategory'));
	}

	public function profileUpdate(MerchantProfileRequest $request)
	{

		try {
			$user_id = Auth::user()->id;

			DB::beginTransaction();
			$userModel = User::where('id', $user_id)->first();
			$userModel->name = $request->first_name.' '.$request->last_name;
			$userModel->first_name = $request->first_name;
			$userModel->last_name = $request->last_name;
			$userModel->zipcode = $request->zipcode;

			if($userModel->save()){
				
				$UserAddress = UserAddress::updateOrCreate(
					['user_id' => Auth::user()->id],
					[
						'user_id' => $user_id,
						'address' => $request->address,
						'country' => $request->country,
						'state' => $request->state,
						'city' => $request->city,
						'latitude' => $request->latitude,
						'longitude' => $request->longitude,
						'line_1' => $request->line_1,
						'line_2' => $request->line_2,
						'postal_code' => $request->postal_code,
					]
				);

				$adminModel = User::where('role_id', getConfigConstant('ADMIN_ROLE_ID'))->first();
				$adminTax = Tax::where('user_id', $adminModel->id)->first();
				$tax = $request->tax_percentage;
				if($adminTax->tax > $request->tax_percentage){
					$tax = $adminTax->tax;
				}

				$TaxModel = Tax::updateOrCreate(
					['user_id' => $user_id],
					[
						'user_id' => $user_id,
						'tax' => $tax,
					]
				);
												
				$businessDetails = BusinessDetail::updateOrCreate(
					['user_id' => $user_id],
					[
						'user_id' => $user_id,
						'business_name' => $request->business_name,
						'about_business' => $request->about_business,
						'business_category' => $request->business_category,
						'leagal_name' => $request->leagal_name,
						'business_street_address' => $request->address,
						'business_city' => $request->city,
						'business_state' => $request->state,
						'business_zip_code' => $request->postal_code,
					]
				);

				$updateCustomer = $this->_updateCustomer($request, $userModel);

				DB::commit();
				return $this->sendResponse([], 'Profile uploaded successfully.');

			} else {
				DB::rollBack();
				return $this->sendError([], 'Something went Wrong');
			}
		
		} catch (Exception $ex) {
			DB::rollBack();
			return $this->sendError([], $ex->getMessage());
		}
	}

	private function _updateCustomer($request, $user)
	{
		$dwolla_access_token = $this->dwollaService->traitAccessToken();
		$userModel = User::where('id', $user->id)->with('address','BusinessDetail')->first();
		$DwollaCustomer = DwollaCustomer::where('user_id', $user->id)->first();
		
		if(!empty($DwollaCustomer)){
			$customer_data = $this->dwollaService->traitUpdateCustomer($dwolla_access_token, $userModel, $request, $DwollaCustomer->customer_id);
		}

		return $DwollaCustomer;

	}

	public function updateEmail()
	{
		return view('merchant.profile.update-email');
	}

	public function updatePhone()
	{
		return view('merchant.profile.update-phone');
	}

	public function checkEmail(EmailRequest $request)
	{
		$sendOTP = $this->_EmailOtpVerification($request);
		return $this->sendResponse([], 'OTP Send Successfully.');
	}

	private function _EmailOtpVerification($request)
	{
		$code = rand(1000,9999);
		$code1 = rand(1000,9999);
		$start = date('Y-m-d H:i:s');
		$details['code'] = $code1;
		$input = array_merge([
			'email' => $request->email, 
			'expired_at' => date('Y-m-d H:i:s', strtotime('+1 hour', strtotime($start))),
			'email_code' => $code1, 
			'email_status' => 0, 
		]);
		$user = Verification::updateOrCreate(
			['email' => $request->email],
			$input
		);

		$responce = dispatch(new SendEmailJob($details, 'OtpVerification', $request->email));

		return true;
	}

	public function checkPhone(PhoneRequest $request)
	{
		$sendOTP = $this->_PhoneOtpVerification($request);
		return $this->sendResponse([], 'OTP Send Successfully.');
	}

	private function _PhoneOtpVerification($request)
	{
		$code1 = rand(1000,9999);
		$start = date('Y-m-d H:i:s');
		$details['code'] = $code1;
		$input = array_merge([
			'phone_number' => $request->phone_number, 
			'country_code' => $request->country_code, 
			'expired_at' => date('Y-m-d H:i:s', strtotime('+1 hour', strtotime($start))),
			'code' => $code1, 
			'status' => 0, 
		]);
		$user = Verification::updateOrCreate(
			['phone_number' => $request->phone_number],
			$input
		);
		$sendSmsNumber = $request->country_code.''.$request->phone_number;

		$otp_message = 'Your Wallet verification code is: '.$code1;
		$responce = $this->sendSms($sendSmsNumber, $otp_message);

		return true;
	}

	public function verifyOTP(OtpVerificationRequest $request)
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

				$userModel = User::where('id', Auth::user()->id)->first();
				$userModel->phone_number = $input['phone_number'];
				$userModel->country_code = $request->dial_code;
				$userModel->save();

				return $this->sendResponse([], 'Otp Verified successfully.');

			} else {
				// send mail using queue
				$verify = Verification::where('email', $input['email'])
				->where('email_code', $request->code)->first();

				if(empty($verify)){ throw new Exception('Invalid OTP.'); }
				$verify->email_status = getConfigConstant('OTP_VERIFIED');
				$verify->save();

				$userModel = User::where('id', Auth::user()->id)->first();
				$userModel->email = $input['email'];
				$userModel->save();

				return $this->sendResponse([], 'Otp Verified successfully.');

			}
			
			return $this->sendResponse([], 'Otp Verified successfully.');

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

}