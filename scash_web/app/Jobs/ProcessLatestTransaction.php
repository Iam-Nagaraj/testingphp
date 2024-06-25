<?php

namespace App\Jobs;

use App\Http\Controllers\Merchant\DwollaController;
use App\Models\Configuration;
use App\Models\DwollaAccount;
use App\Models\DwollaCustomer;
use App\Models\Notification;
use App\Models\Transaction;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ProcessLatestTransaction implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $token;
    protected $toUser;
    protected $fromUser;
    protected $transaction_type;
    protected $message;
    protected $notification_type;
    protected $payment_type;
    protected $transaction_status;
    protected $cashback;
    protected $correlationId;
    protected $account_id;

    /**
     * Create a new job instance.
     */
    public function __construct($token, $toUser, $fromUser, $transaction_type, $message, $notification_type, $payment_type=Transaction::MANUAL, $transaction_status=Transaction::STATUS_PENDING, $cashback=0, $correlationId, $account_id)
    {
        $this->token = $token;
        $this->toUser = $toUser;
        $this->fromUser = $fromUser;
        $this->transaction_type = $transaction_type;
        $this->message = $message;
        $this->notification_type = $notification_type;
        $this->payment_type = $payment_type;
        $this->transaction_status = $transaction_status;
        $this->cashback = $cashback;
        $this->correlationId = $correlationId;
        $this->account_id = $account_id;

    }

    /**
     * Execute the job.
     */
    public function handle()
    {
        try{
            $DwollaCustomer = DwollaCustomer::where('user_id', $this->fromUser)->first();

            $customerID = $DwollaCustomer->customer_id;

            $TransferData = $this->TransferListAPI($this->token, $customerID, $this->correlationId); //fetch transaction list from dwolla
            
        } catch (\Exception $ex) {
            \Log::info('ProcessLatestTransaction'.$ex);
        }

    }

    public function TransferListAPI($token, $customerID, $correlationId)
    {
        sleep(4);

        $DwollaController = new DwollaController();
        $TransferList = $DwollaController->searchTransferList($token, $customerID, $correlationId);

        if($TransferList->total > 0){

            if(!empty($TransferList->_embedded) && !empty($TransferList->_embedded->transfers[0])){
                $lastestTransfer = $TransferList->_embedded->transfers[0];

                $PlatformFee = Configuration::where('config_key', 'platform_fee')->first(); 
                $platform_fees = $PlatformFee->config_value;

                $dwollaAccount = DwollaAccount::where('default_account', $this->account_id)->first();

                if(!empty($lastestTransfer->id)){
                    $transactionModel = Transaction::where('uuid', $this->correlationId)->first(); //save transaction id to database
                    $transactionModel->transaction_id = $lastestTransfer->id;
                    $transactionModel->save();
                }
        
            }

            $notification = new Notification();
            $notification->from = $this->fromUser;
            $notification->to = $this->toUser;
            $notification->message = $this->message;
            $notification->type = $this->notification_type;
            $notification->save();

        } else {
            $this->TransferListAPI($token, $customerID, $correlationId);
        }
        
        return true;


    }
}
