<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BusinessType extends Model
{
    use HasFactory, SoftDeletes;

	protected $table = "business_types";

    protected $fillable = ['name', 'type', 'dwolla_key'];

    public function fetch(){
        return self::all();
    }

	protected $appends=[
        'type_name'
    ];

	protected function getTypeNameAttribute()
    {
		if($this->type == 1){
			return 'SSN';
		} else {
			return 'EIN';
		}
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
