<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class City extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['state_id','name','latitude','longitude'];

    public function fetch(){
        return self::all();
    }

    public function state()
	{
		return $this->hasOne(State::class, 'id', 'state_id');
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
