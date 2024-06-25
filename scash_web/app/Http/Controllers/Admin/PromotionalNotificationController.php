<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Api\NotificationController;
use App\Http\Controllers\Controller;
use App\Http\Requests\PromotionalNotificationRequest;
use App\Models\DeviceToken;
use App\Models\PromotionalNotification;
use App\Models\State;
use App\Models\Transaction;
use App\Models\User;
use App\Models\UserAddress;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Yajra\DataTables\DataTables;

class PromotionalNotificationController extends Controller
{
	protected PromotionalNotification $promotionalNotificationService;

	public function __construct(PromotionalNotification $promotionalNotificationService)
	{
		$this->promotionalNotificationService = $promotionalNotificationService;
	}

	/**
	 * PromotionalNotification Page
	 *
	 */
	public function index()
	{
		try {
			return view('admin.promotionalNotification.index');
		} catch (Exception $ex) {
			return $this->sendError([], $ex->getMessage());
		}
	}

	/**
	 * PromotionalNotification Table List
	 *
	 */
	public function table(Request $request)
	{

		$promotionalNotifications = $this->promotionalNotificationService->with('merchant');
		if ($request->has('search') && !empty($request->get('search'))) {
			$searchValue = '%' . $request->get('search')['value'] . '%';
			$promotionalNotifications->where(function ($query) use ($searchValue) {
				$query->where('subject', 'LIKE', $searchValue);
			});
		}
		$promotionalNotifications = $promotionalNotifications->latest('id');
		return Datatables::of($promotionalNotifications)->addColumn('action', function ($row) {
			return view('admin.promotionalNotification.table-action')->with(
				[
					'id' => $row->id, 
					'view_url' => route('admin.promotionalNotification.view', ['id' => $row->id]),
					'delete_url' => route('admin.promotionalNotification.delete', ['id' => $row->id])
				]
			);
		})
			->rawColumns(['action'])->make(true);
	}

	/**
	 * PromotionalNotification Create Form
	 *
	 */
	public function view(Request $request)
	{
		$merchantModel = User::select('id','name')->where('role_id', getConfigConstant('MERCHANT_ROLE_ID'))->pluck('name','id');
		$detail = $this->promotionalNotificationService->fetchByID($request->id);
		return view('admin.promotionalNotification.view')->with(['detail' => $detail, 'merchantModel' => $merchantModel]);
	}

	public function create()
	{
		$merchantModel = User::select('id','name')->where('role_id', getConfigConstant('MERCHANT_ROLE_ID'))->pluck('name','id');
		try {
			return view('admin.promotionalNotification.create', compact('merchantModel'));
		} catch (Exception $ex) {
			return $this->sendError([], $ex->getMessage());
		}
	}

	/**
	 * PromotionalNotification Save data
	 *
	 */
	public function save(PromotionalNotificationRequest $request)
	{

		try {
			$userModel = PromotionalNotification::updateOrCreate(
				['id' => $request->id],
				[
					'subject' => $request->subject,
					'text' => $request->text,
					'date' => $request->date,
					'time' => $request->time,
					'zip_code' => json_encode($request->zip_code),
					'send_to' => $request->send_to,
				]
			);

			return $this->sendResponse(true, $userModel, 'PromotionalNotification successfully saved.');

			throw ValidationException::withMessages([
				'auth' => [trans('auth.failed')],
			]);
		
		} catch (Exception $ex) {
			return $this->sendError([], $ex->getMessage());
		}
	}

	/**
	 * PromotionalNotification Delete Resource
	 *
	 */
	public function delete(Request $request)
	{
		try {
			$user = $this->promotionalNotificationService->fetchByID($request->id);

			if (!$user) {
				throw new Exception("User not found.", 404);
			}

			$this->promotionalNotificationService->remove($user->id);

			return redirect()->back()->with(['delete' => 'deleted successfully']);

		} catch (Exception $ex) {
			return $this->sendError([], $ex->getMessage());
		}
	}

	public function send()
	{
		$NotificationController = new NotificationController();
		$notificationList = PromotionalNotification::whereDate('date', today())
			->where('status', PromotionalNotification::STATUS_PENDING)->get();

		foreach($notificationList as $single){
			if(!empty($single->merchant_id)){
				$userList = Transaction::where('to_user_id', $single->merchant_id)->groupBy('from_user_id')->pluck('from_user_id');
				$tokenList = DeviceToken::whereIn('user_id', $userList)->get();
			} elseif(!empty($single->city)){
				$userList = UserAddress::where('city', $single->city)->pluck('user_id');
				$tokenList = DeviceToken::whereIn('user_id', $userList)->get();
			} elseif(!empty($single->state)){
				$userList = UserAddress::where('state', $single->state)->pluck('user_id');
				$tokenList = DeviceToken::whereIn('user_id', $userList)->get();
			}
			
			foreach($tokenList as $deviceTokenModel){
				
				if(!empty($deviceTokenModel) && !empty($deviceTokenModel->token)){
					$device_token = $deviceTokenModel->token;
					$title = $single->subject;
					$message = $single->text;
					$sendData = ['subject' => $single->subject];
					
					$abc = $NotificationController->sendPushNotification($device_token, $title, $message, $sendData);
					
				}

			}
			
			$single = PromotionalNotification::where('id', $single->id)->update(['status'=> PromotionalNotification::STATUS_SEND]);

		}


	}
}
