<?php

namespace App\Jobs;

use App\Http\Controllers\Api\NotificationController;
use App\Models\DeviceToken;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\PromotionalNotification as ModelsPromotionalNotification;
use App\Models\Transaction;
use App\Models\UserAddress;
use DateTime;

class PromotionalNotification implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
		//
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
		
		try{
			$NotificationController = new NotificationController();
			$notificationList = ModelsPromotionalNotification::whereDate('date', today())
			->where('status', ModelsPromotionalNotification::STATUS_PENDING)->first();

			$zipcode_arr = json_decode($notificationList->zip_code);

			foreach($zipcode_arr as $zip){

				if($notificationList->send_to == ModelsPromotionalNotification::SEND_MERCHANT){//merchant
					$UserArr = UserAddress::select('user_id')->where('postal_code', $zip)
					->leftJoin('users', 'users.id', '=', 'user_addresses.user_id')
					->where('role_id', 3)->pluck('user_id')->toArray();
					
				} elseif($notificationList->send_to == ModelsPromotionalNotification::SEND_USER){//users
					$UserArr = UserAddress::select('user_id')->where('postal_code', $zip)
					->leftJoin('users', 'users.id', '=', 'user_addresses.user_id')
					->where('role_id', 4)->pluck('user_id')->toArray();
					
				} elseif($notificationList->send_to == ModelsPromotionalNotification::SEND_MERCHANT_USER){
					$merchants = UserAddress::select('user_id')->where('postal_code', $zip)
					->leftJoin('users', 'users.id', '=', 'user_addresses.user_id')
					->where('role_id', 3)->pluck('user_id')->toArray();

					if(!empty($merchants)){
						$merchantUsers = Transaction::select('to_user_id')->where('wallet_type', Transaction::TYPE_WALLET_TO_WALLET)
						->whereIn('to_user_id', $merchants)
						->get();
						if(!empty($merchantUsers)){
							$uniqueUsers = $merchantUsers->unique('to_user_id')->pluck('to_user_id')->toArray();
							$allUsers = array_merge($merchants, $uniqueUsers);
							$uniqueData = array_unique($allUsers);
							$UserArr = DeviceToken::whereIn('user_id', $uniqueData)->where('token', '!=', 'null')->get();
						}
					}
				} else {
					$UserArr = UserAddress::select('user_id')->where('postal_code', $zip)
					->leftJoin('users', 'users.id', '=', 'user_addresses.user_id')
					->pluck('user_id')->toArray();
				}
				$DeviceToken = DeviceToken::whereIn('user_id', $UserArr)->where('token', '!=', 'null')->get();
				$NotificationController = new NotificationController();

				foreach($DeviceToken as $deviceTokenModel){
					
					if(!empty($deviceTokenModel) && !empty($deviceTokenModel->token)){
						$device_token = $deviceTokenModel->token;
						$title = $notificationList->subject;
						$message = $notificationList->text;
						$sendData = ['subject' => $notificationList->subject];
						$abc = $NotificationController->sendPushNotification($device_token, $title, $message, $sendData);
					}
				}
				$single = ModelsPromotionalNotification::where('id', $notificationList->id)->update(['status'=> ModelsPromotionalNotification::STATUS_SEND]);
			}

		} catch (\Exception $ex) {
			\Log::info('PromotionalNotification'.$ex);
		}
    }
}
