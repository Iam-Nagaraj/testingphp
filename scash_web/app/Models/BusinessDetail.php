<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\URL;

class BusinessDetail extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
		'user_id','tax_type','registration_type','logo','business_name','username','about_business','business_type','business_category',
		'leagal_name','business_street_address','business_city','business_state','business_zip_code','business_ein','business_phone_number',
		'business_contact_address','contact_city','contact_state','contact_zip_code','dob','home_address','home_city','home_state',
		'home_zip_code','ssn_itin','email','Address_city_state','phone_number','instagram_username','mesanger_username',
	];

	protected $appends=[
        'image_url','tax_type_name'
    ];

	protected function getImageUrlAttribute()
    {
        if(!empty($this->logo) && !empty($this->logo)){
            return $this->logo;
        } else {
            return URL::to('/').''.'/assets/user-dummy.png';
        }
    }

	protected function getTaxTypeNameAttribute()
    {
        if($this->tax_type == getConfigConstant('BUSINESS_TYPE_SSN')){
            return 'SSN';
        } else {
            return 'EIN';
        }
    }
	
	public function BusinessType()
	{
		return $this->hasOne(BusinessType::class, 'id', 'registration_type');
	}

	public function MerchantProfile()
	{
		return $this->hasOne(User::class, 'id', 'user_id');
	}

	public function BusinessCategory()
	{
		return $this->hasOne(BusinessCategory::class, 'id', 'business_category');
	}

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
