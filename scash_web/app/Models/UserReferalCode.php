<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class UserReferalCode extends Model
{
	use HasFactory;

	protected $fillable = ['user_id', 'referal_code', 'status'];

	public function scopeIsActive($query)
	{
		return $query->where('status', getConfigConstant('STATUS_ACTIVE'));
	}


	public function _create(User $user)
	{
		return self::create(
			[
				'user_id' => $user->id,
				'referal_code' => $this->generateReferalCode(),

			]
		);
	}

	public function _update(User $user)
	{
		return self::updateOrCreate(
			['user_id' => $user->id],
			[
				'user_id' => $user->id,
				'referal_code' => $this->generateReferalCode(),

			]
		);
	}

	public function remove($id)
	{
		self::where('id', $id)->delete();
	}


	private function generateReferalCode()
	{
		return "SCASH" . rand(1000, 9999);
	}

	public function user()
	{
		return $this->belongsTo(User::class, 'user_id');
	}

	public static function fetchOne($condition)
	{
		return self::where($condition)->first();
	}
}
