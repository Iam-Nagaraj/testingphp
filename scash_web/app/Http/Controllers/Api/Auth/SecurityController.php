<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\Request;
use App\Models\User;
use App\Traits\TwilioTrait;
use App\Jobs\SendEmailJob;
use App\Mail\ForgotPassword;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SecurityController extends Controller
{
	use TwilioTrait;

	public function __construct()
	{
		//
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
			
			$ForgotPassword = new ForgotPassword($userData, 'API');
			Mail::to($request->email)->send($ForgotPassword);

			// $responce = dispatch(new SendEmailJob($userData, 'ForgotPassword', $request->email));
			return $this->sendResponse([], 'Email Successfully Send');
            
		} catch (Exception $ex) {
			Log::info($ex);
			return $this->sendError([], 'Server Error');
		}
	}

	public function generatePin(Request $request)
	{
		try{

			$validatedData = $request->validate([
				'pin' => ['required',],
			]);

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

	public function verifyPin(Request $request)
	{
		try{

			$validatedData = $request->validate([
				'pin' => ['required',],
			]);

			$userData = User::where('id', Auth::user()->id)->first();
			if(empty($userData) || empty($userData->pin)){
				return $this->sendError([], 'Please Generate Pin');
			}

			$hashPin = Hash::make($request->pin);


			if(Hash::check($request->pin, $userData->pin)){
				return $this->sendResponse([], 'Pin Match Successfully!');
			} else {
				return $this->sendError([], 'Pin Not Verified');
			}
            
		} catch (Exception $ex) {
			Log::info($ex);
			return $this->sendError([], 'Server Error');
		}
	}
}
