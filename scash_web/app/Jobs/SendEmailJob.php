<?php

namespace App\Jobs;

use App\Mail\AccountStatus;
use App\Mail\ForgotPassword;
use App\Mail\MerchantCredentials;
use App\Mail\OtpVerification;
use App\Mail\TestMail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $details;
    protected $mailType;
    protected $toEmail;

    /**
     * Create a new job instance.
     */
    public function __construct($details, $mailType, $toEmail)
    {
        $this->details = $details;
        $this->mailType = $mailType;
        $this->toEmail = $toEmail;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try{

            switch ($this->mailType) {
                case 'OtpVerification':
                    $OtpVerification = new OtpVerification($this->details);
                    Mail::to($this->toEmail)->send($OtpVerification);
                break;
                case 'AccountStatus':
                    $AccountStatus = new AccountStatus($this->details);
                    Mail::to($this->toEmail)->send($AccountStatus);
                break;
                case 'MerchantCredentials':
                    $MerchantCredentials = new MerchantCredentials($this->details);
                    Mail::to($this->toEmail)->send($MerchantCredentials);
                break;
                case 'ForgotPassword':
                    $ForgotPassword = new ForgotPassword($this->details);
                    Mail::to($this->toEmail)->send($ForgotPassword);
                break;
                default:
                    $TestMail = new TestMail($this->details);
                    Mail::to('shivangcodebrew@gmail.com')->send($TestMail);
            }

        } catch (\Exception $ex) {
			\Log::info('SenEmailJob'.$ex);
		}
    }


}
