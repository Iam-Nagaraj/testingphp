<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DwollaCustomer extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['user_id','customer_id'];

	const TYPE_CUSTOMER = 1;
	const TYPE_ACCOUNT = 2;

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

}
