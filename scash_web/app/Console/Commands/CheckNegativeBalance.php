<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Wallet;
use Illuminate\Support\Facades\Log;

class CheckNegativeBalance extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'process:negative-balance';

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
        Log::info('Scheduler ProcessNegativeBalance');

        try{
            $negativeWallets = Wallet::where('negative_balance', '!=', 0)->get();
            foreach($negativeWallets as $single)
            {
                if($single->negative_balance > 0){

                    $wallet = Wallet::where('id', $single->id)->first();
                    $negative_balance = $wallet->negative_balance;
                    $balance = $wallet->balance;
                    $cashback = $wallet->cashback_balance;

                    $cashBackMin = min($negative_balance, $cashback);
                    $negative_balance = $negative_balance - $cashBackMin;
                    $cashback = $cashback - $cashBackMin;
                    
                    $balanceMin = min($negative_balance, $balance);
                    $negative_balance = $negative_balance - $balanceMin;
                    $balance = $balance - $balanceMin;

                    $wallet->negative_balance = $negative_balance;
                    $wallet->cashback_balance = $cashback;
                    $wallet->balance = $balance;
                    $wallet->save();

                }
            }
        } catch (\Exception $ex) {
            Log::info('ProcessNegativeBalance'.$ex);
        }
    }
}
