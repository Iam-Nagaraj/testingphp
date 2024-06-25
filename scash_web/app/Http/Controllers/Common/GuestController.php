<?php

namespace App\Http\Controllers\Common;

use App\Http\Controllers\Controller;
use App\Http\Requests\ChangePasswordRequest;
use App\Models\User;
use Illuminate\Http\Request;
use App\Traits\UploadFile;
use Exception;
use App\Traits\DeleteFile;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Hash;

class GuestController extends Controller
{
	use UploadFile, DeleteFile;

	public function __construct()
	{
		//
	}

	public function index()
	{
		return view('guest.home');
	}

	public function passwordResetConfirmation()
	{
		return view('guest.password.passwordResetConfirmation');
	}

	public function resetPassword($email)
	{
		try{
			$emailData = Crypt::decryptString($email);
			$userData = User::where('email', $emailData)->first();
			if (empty($userData)) {
				abort(404);
			}
			return view('guest.password.resetPassword', compact('email'));

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

}
