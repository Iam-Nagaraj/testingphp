<?php 

namespace App\Traits;

use Twilio\Rest\Client;

trait TwilioTrait
{
    public function sendSms($to, $message)
    {
        $twilioSid = config('services.twilio.sid');
        $twilioToken = config('services.twilio.token');
        $twilioPhoneNumber = config('services.twilio.phone_number');

        $twilio = new Client($twilioSid, $twilioToken);

        $responce = $twilio->messages->create(
            $to,
            [
                'from' => $twilioPhoneNumber,
                'body' => $message,
            ]
        );
        return $responce;
    }
}