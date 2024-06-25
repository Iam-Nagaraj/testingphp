<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class Cashback extends Model
{
	use HasFactory;

	protected $fillable = ['business_category_id', 'status', 'percentage'];

	protected $appends = [
        'business_category_name'
    ];

	protected function getBusinessCategoryNameAttribute()
    {

		if(!empty($this->businessCategory) && !empty($this->businessCategory->name)){
			return $this->businessCategory->name;
		}
		return 'N/A';
    }

	public function scopeIsActive($query)
	{
		return $query->where('status', getConfigConstant('STATUS_ACTIVE'));
	}


	public function scopeByMerchant($query, $user_id)
	{
		return $query->where('user_id', $user_id);
	}



	public function _updateOrCreate(User $user, Request $request)
	{
		return self::updateOrCreate(
			[ 'id' => $request->cashback_id],
			[
				'business_category_id' => $request->business_category_id,
				'percentage' => $request->percentage
			]
		);
	}


	public function remove($id)
	{
		self::where('id', $id)->delete();
	}


	public function user()
	{
		return $this->belongsTo(User::class, 'user_id');
	}

	public function businessCategory()
	{
		return $this->belongsTo(BusinessCategory::class);
	}


	public static function fetchOne($condition)
	{
		return self::where($condition)->first();
	}

	public function fetchDefault()
	{
		return self::where('type', getConfigConstant('CASHBACK_DEFAULT_TYPE'))->first();
	}

	public function scopeByStatus($query, $status = null)
	{
		$status = $status ?? getConfigConstant('STATUS_ACTIVE');
		$query->where('status', $status);
		return $query;
	}

	public function scopeByDefault($query)
	{
		return $query->where(function ($q) {
			$q->where('type', getConfigConstant('CASHBACK_DEFAULT_TYPE'))
				->orWhereNotExists(function ($subquery) {
					$subquery->select(DB::raw(1))
						->from('cashbacks as inner_cashbacks')
						->whereRaw('inner_cashbacks.type = cashbacks.type')
						->havingRaw('COUNT(*) = 2');
				});
		});
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
