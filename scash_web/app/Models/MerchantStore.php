<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MerchantStore extends Model
{
	use HasFactory;

	protected $fillable = ['merchant_id','user_id','wallet_id','branch_id','name','email','phone','state','city','address','latitude','longitude'];

	protected $appends = [
        'wallet_balance'
    ];

	protected function getWalletBalanceAttribute()
    {
		$Wallet = Wallet::where('id', $this->wallet_id)->first();
		return $Wallet->balance??0;
    }

	public static function fetchOne($condition)
	{
		return self::where($condition)->first();
	}

	public function fetchByID(int $id)
	{
		return self::find($id);
	}

}
