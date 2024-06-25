<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PlatformFee extends Model
{
	use HasFactory;

	protected $table = "platform_fees";

	protected $fillable = ['user_id', 'fee'];

	public function _updateOrCreate(User $user, Request $request)
	{
		return self::updateOrCreate(
			['user_id' => $user->id, 'id' => $request->id],
			[
				'user_id' => $user->id,
				'fee' => $request->fee,
			]
		);
	}

	public function remove($id)
	{
		self::where('id', $id)->delete();
	}

	public function user()
	{
		return $this->belongsTo(User::class, 'user_id');
	}

	public static function fetchOne($condition)
	{
		return self::where($condition)->first();
	}

	public function fetchByID(int $id)
	{
		return self::find($id);
	}

	public function updateSatus(array $condition, array $input)
	{
		return self::updateOrCreate($condition, $input);
	}
}
