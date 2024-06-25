<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Http\Request;

class Wallet extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['user_id','balance','wallet_id','negative_balance','cashback_balance','referral_amount', 'cashback_earned'];

    public function fetch(){
        return self::all();
    }

    public function _create(User $user, $balance)
	{
		return self::create(
			[
				'user_id' => $user->id,
				'balance' => $balance

			]
		);
	}

    public function user()
	{
		return $this->hasOne(User::class, 'id', 'user_id');
	}

    public function fetchByState($state_id){
        return self::where('state_id',$state_id)->get();
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
}
