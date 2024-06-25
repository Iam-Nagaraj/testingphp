<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DwollaAccount extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['user_id','json_data', 'default_account', 'is_default'];

	protected $appends = [
        'bank_data'
    ];

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

	public function getBankDataAttribute() {
		return json_decode($this->json_data);
	  }

}
