<?php

use App\Models\Notification;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Route;

function getConfigConstant($key)
{
	return Config::get('constants.' . $key);
}



function getS3Url($s3Path)
{
	if(!empty($s3Path)){
		return Storage::disk('s3')->url($s3Path);
	} else {
		return 'N/A';
	}
}


function checkEmailOrMobile($inputValue)
{
	if (is_numeric($inputValue)) {
		return ['phone_number' => $inputValue];
	} elseif (filter_var($inputValue, FILTER_VALIDATE_EMAIL)) {
		return ['email' => $inputValue];
	}
	return false;
}


function staticPasswordByName($name){
	return ucfirst(explode(' ',$name)[0]).'@'.getConfigConstant('STATIC_OTP');
}

function isRouteActive($routeName)
{
    return Route::is($routeName) ? 'active' : '';
}

function _Notification($from, $to, $message, $type)
{
	$notification = new Notification();
	$notification->from = $from;
	$notification->to = $to;
	$notification->message = $message;
	$notification->type = $type;
	$notification->save();

	return $notification;
}