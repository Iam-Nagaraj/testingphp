<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Faq extends Model
{
    use HasFactory;

    protected $fillable = ['title','description','status'];

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
