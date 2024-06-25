<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class UserAddress extends Model
{
	use HasFactory;

	protected $fillable = ['user_id', 'address', 'address_2', 'country', 'state', 'city', 'postal_code', 'latitude', 'longitude',
		'country_2', 'state_2', 'city_2', 'postal_code_2', 'latitude_2', 'longitude_2', 'line_1', 'line_2'
	];

	public function scopeIsActive($query)
	{
		return $query;
	}

	public function createUserAddress(User $user, Request $request)
	{
		return self::create([
			'user_id' => $user->id,
			'address' => $request->address,
			'address_2' => $request->address_2 ?? "",
			'state' => $request->state,
			'city' => $request->city,
			'postal_code' => isset($request->zipcode) ? $request->zipcode : $request->zipcode ?? '',
			'latitude' => $request->latitude ?? "",
			'longitude' => $request->longitude ?? "",


		]);
	}

	public function updateUserAddress(User $user, Request $request)
	{
		$existingAddress = $this->where('user_id', $user->id)->first();

		if ($existingAddress) {
			$existingAddress->update([
				'address' => $request->address,
				'address_2' => $request->address_2 ?? "",
				'state' => $request->state,
				'city' => $request->city,
				'postal_code' => isset($request->postal_code) ? $request->postal_code : $request->zipcode ?? '',
				'latitude' => $request->latitude ?? "",
				'longitude' => $request->longitude ?? "",
			]);
		} else {
			$this->createUserAddress($user, $request);
		}
	}

	public function remove($id)
	{
		self::where('user_id', $id)->delete();
	}
}
