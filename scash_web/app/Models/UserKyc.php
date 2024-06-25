<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class UserKyc extends Model
{
    use HasFactory;

    protected $fillable = ['user_id','goverment_id','status'];

    protected $appends = ['goverment_id_url'];

    public function scopeIsActive($query)
	{
		return $query->where('status', getConfigConstant('STATUS_ACTIVE'));
	}


	public function _create(User $user, Request $request)
	{
		return self::create(
			[
				'user_id' => $user->id,
				'goverment_id' => $request->goverment_id

			]
		);
	}

	public function _update(User $user, Request $request)
	{
		return self::updateOrCreate(
			['user_id' => $user->id],
			[
				'user_id' => $user->id,
				'goverment_id' => $request->goverment_id

			]
		);
	}

	public function remove($id)
	{
		self::where('id', $id)->delete();
	}


    public function getGovermentIdUrlAttribute(){
		return getS3Url($this->goverment_id);
	}

}
