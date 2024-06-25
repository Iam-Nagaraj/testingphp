<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeviceToken extends Model
{
    use HasFactory;

    protected $fillable = [
		'type', 'token','user_id'
	];

	public function store($request, $user)
	{
		$input = ['token' => $request['device_token'], 'type' => $request['device_type']];
		self::where('user_id', $user->id)->delete();
		self::create([
			'user_id' => $user->id,
			'type' => $input['type'],
			'token' => $input['token']
		]);
	}
}
