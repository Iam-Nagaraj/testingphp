<?php

namespace App\Jobs;

use App\Http\Controllers\Merchant\DwollaController;
use App\Models\DwollaCustomer;
use App\Models\User;
use App\Models\Wallet;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ApproveCustomer implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $customerId;

    /**
     * Create a new job instance.
     */
    public function __construct($customerId)
    {
        $this->customerId = $customerId;
    }

    /**
     * Execute the job.
     */
    public function handle()
    {

        try{
            
            $dwollaCustomerModel = DwollaCustomer::where('customer_id', $this->customerId)->first();
            if(!empty($dwollaCustomerModel) && !empty($dwollaCustomerModel->user_id)){
                $user_id = $dwollaCustomerModel->user_id;
                
                $userModel = User::where('id', $user_id)->first();
                $userModel->status = getConfigConstant('STATUS_ACTIVE');
                $userModel->save();
                
                $DwollaCustomer = DwollaCustomer::where('user_id', $user_id)->first();
                
                $DwollaController = new DwollaController();
                
                $access_token_data = $DwollaController->createAccessToken();
                $access_token = $access_token_data['access_token'];
                $bankList = $DwollaController->BankList($access_token, $DwollaCustomer->customer_id);
                
                foreach($bankList->_embedded->{"funding-sources"} as $singleBank){
                    if($singleBank->type == 'balance'){
                        
                        Wallet::updateOrCreate(
                        ['user_id' => $user_id],
                        [
                            'user_id' => $user_id,
                            'wallet_id' => $singleBank->id
                            ]
                        );
                        
                    }
                }
            }

            return true;
            
        } catch (\Exception $ex) {
            \Log::info('ApproveCustomer'.$ex);
        }

    }

    
}
