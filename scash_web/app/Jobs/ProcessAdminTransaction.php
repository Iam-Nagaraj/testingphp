<?php

namespace App\Jobs;

use App\Http\Controllers\Merchant\DwollaController;
use App\Models\Configuration;
use App\Models\DwollaCustomer;
use App\Models\Notification;
use App\Models\Transaction;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ProcessAdminTransaction implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $token;
    protected $toUser;
    protected $fromUser;
    protected $transaction_type;
    protected $payment_type;
    protected $transaction_status;
    protected $cashback;
    protected $type;
    protected $amount;
    protected $correlationId;
    protected $admin_platform_fee;

    /**
     * Create a new job instance.
     */
    public function __construct($token, $toUser, $fromUser, $transaction_type, $payment_type=Transaction::MANUAL, $transaction_status=Transaction::STATUS_PENDING, $cashback=0, $type=1, $amount=0, $correlationId=null, $admin_platform_fee=0)
    {
        $this->token = $token;
        $this->toUser = $toUser;
        $this->fromUser = $fromUser;
        $this->transaction_type = $transaction_type;
        $this->payment_type = $payment_type;
        $this->transaction_status = $transaction_status;
        $this->cashback = $cashback;
        $this->type = $type;
        $this->amount = $amount;
        $this->correlationId = $correlationId;
        $this->admin_platform_fee = $admin_platform_fee;

    }

    /**
     * Execute the job.
     */
    public function handle()
    {
        
        try{
            //Admin has no customer id, so fetching data using admin account id
            $DwollaCustomer = DwollaCustomer::where('user_id', $this->fromUser)->where('type', '2')->first();
            $accountID = $DwollaCustomer->customer_id;
            
            $TransferData = $this->TransferListAPI($this->token, $accountID, $this->correlationId); //fetch transaction list from dwolla


        } catch (\Exception $ex) {
            \Log::info('ProcessAdminTransaction'.$ex);
        }

    }

    public function TransferListAPI($token, $accountID, $correlationId)
    {
        sleep(4);

        $DwollaController = new DwollaController();
        $TransferList = $DwollaController->searchAccountTransferList($token, $accountID, $correlationId);


        if($TransferList->total > 0){

            if(!empty($TransferList->_embedded) && !empty($TransferList->_embedded->transfers[0])){
                $lastestTransfer = $TransferList->_embedded->transfers[0];

                if(!empty($lastestTransfer->id)){
                    $transactionModel = Transaction::where('uuid', $correlationId)->first();
                    $transactionModel->transaction_id = $lastestTransfer->id;
                    $transactionModel->save();

                }
            }
        } else {
            $this->TransferListAPI($token, $accountID, $correlationId);
        }
        
        return true;


    }
}
