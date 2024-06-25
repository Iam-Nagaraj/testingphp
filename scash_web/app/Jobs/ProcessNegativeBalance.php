<?php

namespace App\Jobs;

use App\Models\Wallet;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ProcessNegativeBalance implements ShouldQueue
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
            \Log::info('ProcessNegativeBalance'.$ex);
        }
    }
}
