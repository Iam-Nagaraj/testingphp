<?php

namespace App\Http\Controllers\Merchant\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\ChangePasswordRequest;
use App\Http\Requests\PinRequest;
use App\Jobs\SendEmailJob;
use App\Mail\ForgotPassword;
use App\Models\DwollaCustomer;
use App\Models\User;
use App\Models\Wallet;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\ValidationException;

class PasswordController extends Controller
{
	protected User $userService;

	public function __construct(User $userService)
	{
		$this->userService = $userService;
	}


	public function changePassword()
	{
		return view('merchant.auth.changepassword');
	}

	public function updatePassword(ChangePasswordRequest $request)
	{
		try{

            $user =  User::where('id', Auth::user()->id)->first();
			if (empty($user)) {
				throw new Exception("User Not Found", 1);
            } 
            $user->password =  Hash::make($request->password);

            if ($user->save()) {
				return $this->sendResponse($user, 'Successfully Updated');
            } else {
				throw new Exception("Error Processing Request", 1);
            }
		} catch (Exception $ex) {
			return $this->sendError([], $ex->getMessage());
		}

	}

	public function forgotPassword()
	{
		return view('merchant.auth.forgotPassword');
	}

	public function sendForgotPassword(Request $request)
	{
		try{

			$validatedData = $request->validate([
				'email' => [
					'required',
					'email'
				],
			]);

			$userData = User::where('email', $request->email)->first();

			if (empty($userData)) {
				return $this->sendError([], 'Email Not Found!');
			}
			$ForgotPassword = new ForgotPassword($userData, 'web');
			Mail::to($request->email)->send($ForgotPassword);

			// $responce = dispatch(new SendEmailJob($userData, 'ForgotPassword', $request->email));
			// return $this->sendResponse($userData, 'Successfully Send');
            
		} catch (Exception $ex) {
			return $this->sendError([], $ex->getMessage());
		}

	}

	public function resetPassword($email)
	{
		try{
			$emailData = Crypt::decryptString($email);
			$userData = User::where('email', $emailData)->first();
			if (empty($userData)) {
				abort(404);
			}
			return view('merchant.auth.resetPassword', compact('email'));

		} catch (Exception $ex) {
			abort(404);
		}
	}

	public function updateResetPassword(ChangePasswordRequest $request)
	{
		try{
			$emailData = Crypt::decryptString($request->password_token);
            $user =  User::where('email', $emailData)->first();
			if (empty($user)) {
				throw new Exception("Data Not Found", 1);
            } 
            $user->password =  Hash::make($request->password);

            if ($user->save()) {
				return $this->sendResponse($user, 'Successfully Updated');
            } else {
				throw new Exception("Error Processing Request", 1);
            }
		} catch (Exception $ex) {
			return $this->sendError([], $ex->getMessage());
		}

	}

	public function deleteOldUsers()
	{
		$users = User::get();

		foreach($users as $singleUser){

			$DwollaCustomer = DwollaCustomer::where('user_id', $singleUser->id)->first();
			if(empty($DwollaCustomer)){
				$delteResponce = User::where('id', $singleUser->id)->delete();
			}

			$WalletData = Wallet::where('user_id', $singleUser->id)->first();
			if(empty($WalletData)){
				$delteResponce = User::where('id', $singleUser->id)->delete();
			} else {
				if(empty($WalletData->wallet_id))
				{
					$delteResponce = User::where('id', $singleUser->id)->delete();
				}
			}

		}
		
		return 'done';
	}

	public function generatePin()
	{
		return view('merchant.pin.index');
	}

	public function storePin(PinRequest $request)
	{
		try{

			$userData = User::where('id', Auth::user()->id)->first();

			$hashPin = Hash::make($request->pin);

			$userData->pin = $hashPin;

			if ($userData->save()) {
				return $this->sendResponse([], 'Pin Successfully Generate!');
			} else {
				return $this->sendError([], 'Facing issue in pin generation');
			}

		} catch (Exception $ex) {
			Log::info($ex);
			return $this->sendError([], 'Server Error');
		}

	}

}
