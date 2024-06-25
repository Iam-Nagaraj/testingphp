<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Configuration extends Model
{
	use HasFactory;

	protected $fillable = ['config_key', 'config_value'];

	protected $casts = ['data'];

	public function getConfigValueAttribute($value)
	{
		if ($this->isJson($value)) {
			$value = json_decode($value, true);
		}
		return $value;
	}


	function isJson($string)
	{
		json_decode($string);
		return json_last_error() === JSON_ERROR_NONE;
	}

	public function getDataAttribute()
	{
		$dataArray = $this->pluck('config_value', 'config_key')->toArray();
		return (object)$dataArray;
	}

	public function fetch($key)
	{
		$key = is_array($key)?$key:[$key];
		return self::whereIn('config_key', $key)->get();
	}

	public function fetchOne($key)
	{
		return self::where('config_key', $key)->first();
	}

	public function remove($id)
	{
		return self::where('id', $id)->delete();
	}
}
