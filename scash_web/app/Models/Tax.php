<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class Tax extends Model
{
	use HasFactory;

	protected $table = "tax";

	protected $fillable = ['user_id', 'tax'];

	public function remove($id)
	{
		self::where('id', $id)->delete();
	}

	public static function fetchOne($condition)
	{
		return self::where($condition)->first();
	}

	public function fetchByID(int $id)
	{
		return self::find($id);
	}

	public function updateSatus(array $condition, array $input)
	{
		return self::updateOrCreate($condition, $input);
	}
}
