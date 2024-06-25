<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cms extends Model
{
	use HasFactory;

	protected $fillable = ['cms_key', 'cms_value'];

	protected $casts = ['data'];

	public function getCmsValueAttribute($value)
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
		$dataArray = $this->pluck('cms_value', 'cms_key')->toArray();
		return (object)$dataArray;
	}

	public function fetchOne($key)
	{
		return self::where('cms_key', $key)->first();
	}
}
