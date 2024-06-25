<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Notification extends Model
{
    use HasFactory;

    const IS_READ_PENDING = 0;
    const IS_READ_DONE = 1;

	const APPROVED = 1;
	const REJECTED = 2;
	const DEPOSIT = 3;
	const WITHDRAW = 4;
	const WALLETTOWALLET = 5;
	const CASHBACK = 6;

    protected $fillable = ['from','to','type','message','is_read'];

    public function fetch(){
        return self::all();
    }

    public function sender()
	{
		return $this->hasOne(User::class, 'id', 'from');
	}

    public function receiver()
	{
		return $this->hasOne(User::class, 'id', 'to');
	}

    public function fetchOne(array $whereArray)
	{
		return self::where($whereArray)->latest()->first();
	}

    public function fetchByID(int $id)
	{
		return self::find($id);
	}

}
