<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PromotionalNotification extends Model
{
    use HasFactory;

    const STATUS_PENDING = 0;
    const STATUS_SEND = 1;

    const SEND_MERCHANT = 1;
    const SEND_USER = 2;
    const SEND_MERCHANT_USER = 3;
    const SEND_ALL = 4;

    protected $fillable = ['subject','text','merchant_id','city', 'state', 'zip_code', 'lat', 'long', 'date', 'time','send_to'];

    protected $appends = [
        'send_status'
    ];

    protected function getSendStatusAttribute()
    {
        if($this->status == self::STATUS_SEND){
            return 'Send';
        } else {
            return 'Pending';
        }
    }

    public function fetch(){
        return self::all();
    }

    public function merchant()
	{
		return $this->hasOne(User::class, 'id', 'merchant_id');
	}

    public function fetchByState($merchant_id){
        return self::where('merchant_id', $merchant_id)->get();
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
