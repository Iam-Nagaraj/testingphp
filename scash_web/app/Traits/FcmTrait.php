<?php 

namespace App\Traits;

trait FcmTrait
{

    public function sendPushNotification($device_token, $title, $message, $sendData)
    {
		$SERVER_API_KEY = config('services.fireBase.token');
        $SERVER_URL = config('services.fireBase.SendUrl');
  
        $data = [
            "to" => $device_token,
            "data" => $sendData, 
            "notification" => [
                "title" => $title,
                "body" => $message,  
                "mutable_content" => true,
                "sound" => "default",
                "channelId" => "1"
            ]
        ];
		
        $dataString = json_encode($data);
    
        $headers = [
            'Authorization: key=' . $SERVER_API_KEY,
            'Content-Type: application/json',
        ];

        $ch = curl_init();
      
        curl_setopt($ch, CURLOPT_URL, $SERVER_URL);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $dataString);
               
        $response = curl_exec($ch);
		$jsondata = json_decode($response);
		
        return $jsondata;
    }

    

}