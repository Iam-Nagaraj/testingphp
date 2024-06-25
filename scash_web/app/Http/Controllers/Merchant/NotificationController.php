<?php

namespace App\Http\Controllers\Merchant;

use App\Http\Controllers\Controller;
use App\Models\Configuration;
use App\Models\Notification;
use App\Models\User;
use App\Models\Wallet;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
	protected Notification $notificationService;

	public function __construct(Notification $notificationService)
	{
		$this->notificationService = $notificationService;
	}

    public function index()
	{
		return view('merchant.notification.index');
	}

	public function list()
	{
		$notifications = Notification::select('message')->where('to', Auth::user()->id)->latest()->paginate(10);

		$data = Notification::where('to', Auth::user()->id)->where('is_read', 0)->pluck('id')->toArray();
		$update = Notification::whereIn('id', $data)->update(['is_read' => 1]);

		return $this->sendResponse($notifications, 'Notification fetched successfully.');
	}

	public function table()
	{
		$data = Notification::where('to', Auth::user()->id)->where('is_read', 0)->pluck('id')->toArray();
		$update = Notification::whereIn('id', $data)->update(['is_read' => 1]);

		$transactions = $this->notificationService;
		
		$transactions = $transactions->where('to', Auth::user()->id)->latest();

		return Datatables::of($transactions)->addColumn('action', function ($row) {
			return view('merchant.dashboard.table-action')->with(
				[
					'id' => $row->id, 
				]
			);
		})
			->rawColumns(['action'])->make(true);
	
	}

}
