<?php

namespace App\Models;

use App\Http\Requests\UserThroughReferalCodeRequest;
use Exception;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Model;
use Illuminate\Http\Request;

class UserThroughReferalCode extends Model
{
	use HasFactory;

	protected $fillable = ['user_id', 'referal_code_id', 'referal_code_user_id', 'status'];

	public function scopeIsActive($query)
	{
		return $query->where('status', getConfigConstant('STATUS_ACTIVE'));
	}

	public function _create(User $user, Request $request)
	{
		try {
			$referal_code_detail = $this->referal_code ? $this->referal_code->fetchOne(['referal_code' => $request->referal_code ?? ""]) : "";
			if (!$referal_code_detail && isset($request->referal_code)) {
				throw new Exception('The referal code is not exists.');
			}
			return self::create(
				[
					'user_id' => $user->id,
					'referal_code_id' => $referal_code_detail->referal_code,
					'referal_code_user_id' => $referal_code_detail->user_id,

				]
			);
		} catch (Exception $ex) {
			return $this->sendError([], $ex->getMessage());
		}
	}

	public function _update(User $user, Request $request)
	{
		try {
			$referal_code_detail = $this->referal_code ? $this->referal_code->fetchOne(['referal_code' => $request->referal_code ?? ""]) : "";
			if (!$referal_code_detail && isset($request->referal_code)) {
				throw new Exception('The referal code is not exists.');
			}
			return self::updateOrCreate(
				['user_id' => $user->id],
				[
					'user_id' => $user->id,
					'referal_code_id' => $referal_code_detail->referal_code,
					'referal_code_user_id' => $referal_code_detail->user_id,

				]
			);
		} catch (Exception $ex) {
			return $this->sendError([], $ex->getMessage());
		}
	}

	public function user()
	{
		return $this->belongsTo(User::class, 'user_id');
	}

	public function referal_code()
	{
		return $this->belongsTo(UserReferalCode::class, 'referal_code_id');
	}
}
