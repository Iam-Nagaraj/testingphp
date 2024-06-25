<?php

namespace App\Jobs;

use App\Http\Controllers\Api\DwollaController;
use App\Models\User;
use App\Models\Wallet;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Str;

class ProcessTaxAmount implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $token;
    protected $toUser;
    protected $fromUser;
    protected $tax;

    /**
     * Create a new job instance.
     */
    public function __construct($token, $toUser, $fromUser, $tax)
    {
        $this->token = $token;
        $this->toUser = $toUser;
        $this->fromUser = $fromUser;
        $this->tax = $tax;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
		try{
			$DwollaController = new DwollaController();

			$currentUserWallet = Wallet::where('user_id', $this->fromUser)->first();
			$adminModel = User::where('role_id', getConfigConstant('ADMIN_ROLE_ID'))->first();
			$adminWallet = Wallet::where('user_id', $adminModel->id)->first();
			$tax_amount = $this->tax;
			
			$cash_back_data = [
				'amount' => $tax_amount,
				'source_id' => $currentUserWallet->wallet_id,
				'destination_id' => $adminWallet->wallet_id,
				'correlationId' =>  $uuid = Str::uuid()->toString()
			];
			
			// Convert associative array to an object
			$cash_back_object = (object) $cash_back_data;

			$fundTransfer = $DwollaController->fundTransfer($this->token, $cash_back_object);

			if($currentUserWallet->cashback_balance > $tax_amount){
				$currentUserWallet->cashback_balance = $currentUserWallet->cashback_balance - $tax_amount;
				$currentUserWallet->save();	
			} elseif($currentUserWallet->balance > $tax_amount) {
				$currentUserWallet->balance = $currentUserWallet->balance - $tax_amount;
				$currentUserWallet->save();
			} else {
				$currentUserWallet->negative_balance = $currentUserWallet->negative_balance + $tax_amount;
				$currentUserWallet->save();
			}

			$adminWallet->balance = $adminWallet->balance + $tax_amount;
			$adminWallet->save();

		} catch (\Exception $ex) {
			\Log::info('ProcessTaxAmount'.$ex);
		}
    }
}
