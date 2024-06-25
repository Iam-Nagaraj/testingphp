<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Http\Controllers\Api\NotificationController;
use App\Models\DeviceToken;
use App\Models\PromotionalNotification as ModelsPromotionalNotification;
use App\Models\Transaction;
use App\Models\UserAddress;
use Illuminate\Support\Facades\Log;

class CheckPromotionalNotification extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'send:promotional-notification';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        Log::info('Scheduler PromotionalNotification');

        try{
			$NotificationController = new NotificationController();
			$notificationList = ModelsPromotionalNotification::whereDate('date', today())
			->where('status', ModelsPromotionalNotification::STATUS_PENDING)->get();
			
			foreach($notificationList as $single)
			{
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
				
				foreach($tokenList as $deviceTokenModel)
				{
					
					if(!empty($deviceTokenModel) && !empty($deviceTokenModel->token)){
						$device_token = $deviceTokenModel->token;
						$title = $single->subject;
						$message = $single->text;
						$sendData = ['subject' => $single->subject];
						
						$abc = $NotificationController->sendPushNotification($device_token, $title, $message, $sendData);
					}

				}
				
				$single = ModelsPromotionalNotification::where('id', $single->id)->update(['status'=> ModelsPromotionalNotification::STATUS_SEND]);

			}
		} catch (\Exception $ex) {
			Log::info('PromotionalNotification'.$ex);
		}
    }
}
