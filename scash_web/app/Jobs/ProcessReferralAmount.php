<?php

namespace App\Jobs;

use App\Http\Controllers\Api\DwollaController;
use App\Models\Configuration;
use App\Models\Transaction;
use App\Models\User;
use App\Models\Wallet;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ProcessReferralAmount implements ShouldQueue
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
			$DwollaController = new DwollaController();

			$wallets = Wallet::where('referral_amount', '>', 0)->get();
			$adminModel = User::where('role_id', getConfigConstant('ADMIN_ROLE_ID'))->first();
			$adminWallet = Wallet::where('user_id', $adminModel->id)->first();

			$ConfigurationModel = Configuration::where('config_key', 'referral')->first();
			$referralConfigModel = Configuration::where('config_key', 'referral_min_amount')->first();
			$referral_min_amount = $referralConfigModel->config_value;
			$referral_amount = $ConfigurationModel?$ConfigurationModel->config_value:0;

			$access_token_data = $DwollaController->createAccessToken();
			$access_token = $access_token_data['access_token'];
			
			foreach($wallets as $single){

				$user_id = $single->user_id;
				$userTransaction = Transaction::where('from_user_id', $user_id)->sum('amount');
				if($referral_min_amount > $userTransaction){
					continue;
				}

				if(empty($single->wallet_id)){
					continue;
				}

				$referal_data = [
					'amount' => $referral_amount,
					'source_id' => $adminWallet->wallet_id,
					'destination_id' => $single->wallet_id
				];

				// Convert associative array to an object
				$cash_back_object = (object) $referal_data;

				// $fundTransfer = $DwollaController->fundTransfer($access_token, $cash_back_object);

				if($adminWallet->balance >= $referral_amount){
					$adminWallet->balance = $adminWallet->balance - $referral_amount;
					$adminWallet->save();	
				} elseif($adminWallet->balance < $referral_amount) {
					$adminWallet->negative_balance = $adminWallet->negative_balance + $referral_amount;
					$adminWallet->save();
				}

				$userWallet = Wallet::where('wallet_id', $single->wallet_id)->first();
				$userWallet->balance = $userWallet->balance + $referral_amount;
				$userWallet->referral_amount = $userWallet->referral_amount - $referral_amount;
				$userWallet->save();

			}

		} catch (\Exception $ex) {
			Log::info('ProcessReferralAmount'.$ex);
		}
    }
}
