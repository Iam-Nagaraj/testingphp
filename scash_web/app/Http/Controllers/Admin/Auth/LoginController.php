<?php

namespace App\Http\Controllers\Admin\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
	protected User $userService;

	public function __construct(User $userService)
	{
		$this->userService = $userService;
	}

	/**
	 * return Admin login
	 *
	 */
	public function login()
	{
		return view('admin.auth.login');
	}

	/**
	 * Admin Login
	 *
	 */
	public function loginSubmit(LoginRequest $request)
	{
		try {
			
			
			if (Auth::attempt([
				'email' => $request->email, 
				'password' => $request->password, 
				'role_id' => getConfigConstant('ADMIN_ROLE_ID')
				])) {
					$user = Auth::user();

					$token = $user->createToken('scash')->accessToken;

				return $this->sendResponse([
					'accessToken' => $token,
					'userData' => $user,
					'userAbilities' => [], // Adjust based on your user roles/permissions logic
				], 'Successfully logged in');
			}

			throw ValidationException::withMessages([
				'auth' => [trans('auth.failed')],
			]);
		} catch (Exception $ex) {
			return $this->sendError([], $ex->getMessage());
		}
	}

	/**
	 * Logout
	 *
	 */
	public function logout(Request $request)
	{
		try {
			Auth::logout();

			return redirect()->route('admin.auth.login');
		} catch (Exception $ex) {

			return $this->sendError([], $ex->getMessage());
		}
	}

	/**
	 * Profile Details
	 *
	 */
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
}
