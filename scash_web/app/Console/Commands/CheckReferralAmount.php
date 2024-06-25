<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Api\DwollaController;
use App\Models\Configuration;
use App\Models\User;
use App\Models\Wallet;

class CheckReferralAmount extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'process:referral-amount';

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
        Log::info('Scheduler ProcessReferralAmount');

        try{
			$DwollaController = new DwollaController();

			$wallets = Wallet::where('referral_amount', '>', 0)->get();
			$adminModel = User::where('role_id', getConfigConstant('ADMIN_ROLE_ID'))->first();
			$adminWallet = Wallet::where('user_id', $adminModel->id)->first();

			$ConfigurationModel = Configuration::where('config_key', 'referral')->first();
			$referral_amount = $ConfigurationModel?$ConfigurationModel->config_value:0;

			$access_token_data = $DwollaController->createAccessToken();
			$access_token = $access_token_data['access_token'];
			
			foreach($wallets as $single){
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

				$fundTransfer = $DwollaController->fundTransfer($access_token, $cash_back_object);

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
