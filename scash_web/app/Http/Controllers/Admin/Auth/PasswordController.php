<?php

namespace App\Http\Controllers\Admin\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\ChangePasswordRequest;
use App\Http\Requests\PinRequest;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class PasswordController extends Controller
{
	protected User $userService;

	public function __construct(User $userService)
	{
		$this->userService = $userService;
	}

	/**
	 * Change Admin Password Screen
	 *
	 */
	public function changePassword()
	{
		return view('admin.auth.changepassword');
	}

	/**
	 * Change Admin Password
	 *
	 */
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

	public function generatePin()
	{
		return view('admin.pin.index');
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
