<?php

namespace App\Models;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SocialAccount extends Model
{

	use HasFactory;

	protected $fillable = ['user_id', 'provider_id', 'type'];

    public function fetch(){
        return self::all();
    }

    public function fetchOne(array $whereArray)
	{
		return self::where($whereArray)->latest()->first();
	}

    public function fetchByID(int $id)
	{
		return self::find($id);
	}

    public function remove(int $id)
	{
		self::where('id', $id)->delete();
	}

	public function _create(User $user, $request)
	{
		return self::create(
			[
				'user_id' => $user->id,
				'provider_id' => $request->provider_id,
				'type' => $request->type,

			]
		);
	}

}
