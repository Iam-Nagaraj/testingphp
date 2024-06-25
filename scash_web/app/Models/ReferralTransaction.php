<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Http\Request;

class ReferralTransaction extends Model
{
    use HasFactory;

    protected $fillable = ['from','to','referral_amount'];

	public function sender()
	{
		return $this->belongsTo(User::class, 'from')->select('id','first_name','last_name', 'name', 'phone_number', 'country_code');
	}

    public function receiver()
	{
		return $this->belongsTo(User::class, 'to')->select('id','first_name','last_name', 'name');
	}

}
