<?php

namespace App\Jobs;

use App\Http\Controllers\Api\DwollaController;
use App\Models\Configuration;
use App\Models\DwollaCustomer;
use App\Models\Transaction;
use App\Models\User;
use App\Models\Wallet;
use Illuminate\Support\Str;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ProcessAchAmount implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $token;
    protected $toUser;
    protected $fromUser;
    protected $payment_type;

    /**
     * Create a new job instance.
     */
    public function __construct($token, $toUser, $fromUser, $payment_type=Transaction::MANUAL)
    {
        $this->token = $token;
        $this->toUser = $toUser;
        $this->fromUser = $fromUser;
        $this->payment_type = $payment_type;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
		try{
			$DwollaController = new DwollaController();
			if($this->payment_type == Transaction::INSTANT){
				$PlatformFee = Configuration::where('config_key', 'instant_platform_fee')->first(); 
			} else {
				$PlatformFee = Configuration::where('config_key', 'manual_platform_fee')->first(); 
			}
			$currentUserWallet = Wallet::where('user_id', $this->fromUser)->first();
			$adminModel = User::where('role_id', getConfigConstant('ADMIN_ROLE_ID'))->first();
			$adminWallet = Wallet::where('user_id', $adminModel->id)->first();
			$platform_fees = $PlatformFee->config_value;

			$uuid = Str::uuid()->toString();
			
			$cash_back_data = [
				'amount' => $platform_fees,
				'source_id' => $currentUserWallet->wallet_id,
				'destination_id' => $adminWallet->wallet_id,
				'correlationId' => $uuid
			];
			
			// Convert associative array to an object
			$cash_back_object = (object) $cash_back_data;

			$fundTransfer = $DwollaController->fundTransfer($this->token, $cash_back_object);

			if($currentUserWallet->cashback_balance > $platform_fees){
				$currentUserWallet->cashback_balance = $currentUserWallet->cashback_balance - $platform_fees;
				$currentUserWallet->save();	
			} elseif($currentUserWallet->balance > $platform_fees) {
				$currentUserWallet->balance = $currentUserWallet->balance - $platform_fees;
				$currentUserWallet->save();
			} else {
				$currentUserWallet->negative_balance = $currentUserWallet->negative_balance + $platform_fees;
				$currentUserWallet->save();
			}

			$adminWallet->balance = $adminWallet->balance + $platform_fees;
			$adminWallet->save();

		} catch (\Exception $ex) {
			\Log::info('ProcessAchAmount '.$ex);
		}
    }
}
