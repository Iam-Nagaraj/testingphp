<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use App\Traits\FcmTrait;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{

	use FcmTrait;

	public function list()
	{
		$notifications = Notification::where('to', Auth::user()->id)->latest()->paginate(10);

		$data = Notification::where('to', Auth::user()->id)->where('is_read', 0)->pluck('id')->toArray();
		$update = Notification::whereIn('id', $data)->update(['is_read' => 1]);

		return $this->sendResponse($notifications, 'Notification fetched successfully.');
	}

}
