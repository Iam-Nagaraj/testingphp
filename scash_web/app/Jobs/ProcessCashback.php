<?php

namespace App\Jobs;

use App\Http\Controllers\Api\DwollaController;
use App\Http\Controllers\Api\NotificationController;
use App\Models\DeviceToken;
use App\Models\DwollaCustomer;
use App\Models\Notification;
use App\Models\Transaction;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ProcessCashback implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $token;
    protected $toUser;
    protected $fromUser;
    protected $transaction_type;
    protected $data;
    protected $notification_type;

    /**
     * Create a new job instance.
     */
    public function __construct($token, $toUser, $fromUser, $transaction_type, $data, $notification_type)
    {
        $this->token = $token;
        $this->toUser = $toUser;
        $this->fromUser = $fromUser;
        $this->transaction_type = $transaction_type;
        $this->data = $data;
        $this->notification_type = $notification_type;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {

            $DwollaController = new DwollaController();
            $NotificationController = new NotificationController();

            $fundTransfer = $DwollaController->fundTransfer($this->token, $this->data);

            $DwollaCustomer = DwollaCustomer::where('user_id', $this->toUser)->first();

            $customerID = $DwollaCustomer->customer_id;
            
            $TransferList = $DwollaController->transferList($this->token, $customerID); //fetch transaction list from dwolla

            if(!empty($TransferList->_embedded) && !empty($TransferList->_embedded->transfers[0])){
                $lastestTransfer = $TransferList->_embedded->transfers[0];

                if(!empty($lastestTransfer->id)){
                    $transactionModel = new Transaction(); //save transaction id to database
                    $transactionModel->transaction_id = $lastestTransfer->id;
                    $transactionModel->amount = $lastestTransfer->amount->value;
                    $transactionModel->type = $this->transaction_type;
                    $transactionModel->wallet_type = $this->transaction_type;
                    $transactionModel->from_user_id = $this->toUser;
                    $transactionModel->to_user_id = $this->fromUser;
                    $transactionModel->status = Transaction::STATUS_COMPLETED;
                    $transactionModel->save();
                }
        
            }
            $message = "You received cashback of $".$this->data->amount;

            $deviceTokenModel = DeviceToken::where('user_id', $this->fromUser)->first();
            
            if(!empty($deviceTokenModel) && !empty($deviceTokenModel->token)){
                $device_token = $deviceTokenModel->token;
                $title = 'Scash';
                $sendData = ['amount' => $this->data->amount];
                
                $NotificationController->sendPushNotification($device_token, $title, $message, $sendData);
                
            }
            
            $notification = new Notification();
            $notification->from = $this->toUser;
            $notification->to = $this->fromUser;
            $notification->message = $message;
            $notification->type = $this->notification_type;
            $notification->save();

            
        } catch (\Exception $ex) {
            \Log::info('ProcessCashback'.$ex);
        }

    }
}
