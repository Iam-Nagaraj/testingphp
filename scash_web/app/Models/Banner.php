<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class banner extends Model
{
    use HasFactory;

    const TYPE_REFERRAL = 1;
    const TYPE_ONE_MERCHANT = 2;
    const TYPE_ALL_MERCHANT = 3;

    protected $fillable = ['banner_image','start_date','end_date','user_id','name','url','type'];

    protected $appends = [
        'type_name','img_url'
    ];

    protected function getTypeNameAttribute()
    {
        if($this->type == 1){
            return 'Referal';
        } else if($this->type == 2){
            return 'Merchant';
        } else {
            return 'Scanner';
        }
    }

    protected function getImgUrlAttribute()
    {
        return 'banner_image';
     
    }

    public function fetch(){
        return self::all();
    }

    public function merchant()
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
