<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use App\Http\Resources\UserVerifyResource;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\URL;
// use Laravel\Sanctum\HasApiTokens;
use Laravel\Passport\HasApiTokens;
use Laravel\Passport\Token;
use Illuminate\Support\Str;

class User extends Authenticatable
{

	use HasApiTokens, HasFactory, Notifiable, SoftDeletes;

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array<int, string>
	 */
	protected $fillable = [
		'first_name',
		'last_name',
		'name',
		'email',
		'phone_number',
		'country_code',
		'password',
		'zipcode',
		'date_of_birth',
		'email_verified_at',
		'phone_number_verified_at',
		'is_email_verified',
		'is_phone_number_verified',
		'status',
		'role_id',
		'uuid'

	];

	const ROLE_SUPER_ADMIN = 1;
	const ROLE_ADMIN = 2;
	const ROLE_MERCHANT = 3;
	const ROLE_CUSTOMER = 4;
	const ROLE_STORE = 5;

	/**
	 * The attributes that should be hidden for serialization.
	 *
	 * @var array<int, string>
	 */
	protected $hidden = [
		'password',
		'remember_token',
	];

	/**
	 * The attributes that should be cast.
	 *
	 * @var array<string, string>
	 */
	protected $casts = [
		'email_verified_at' => 'datetime',
		'password' => 'hashed',
		'phone_number_verified_at' => 'datetime'
	];

	protected $appends = [
        'image_url', 'business_document', 'status_name', 'tax_percentage', 'cashback_rule', 'referalCode','referalAmount', 'supportMail', 'is_pin_generated'
    ];

	protected function getReferalCodeAttribute()
    {
		$userReferalCode = UserReferalCode::where('user_id', $this->id)->first();
		return $userReferalCode->referal_code??'';
    }

	protected function getReferalAmountAttribute()
    {
		$ConfigurationModel = Configuration::where('config_key', 'referral')->first();
		return $ConfigurationModel->config_value??'';
    }

	protected function getSupportMailAttribute()
    {
		$ConfigurationModel = Configuration::where('config_key', 'support_email')->first();
		return $ConfigurationModel->config_value??'';
    }

	protected function getCashbackRuleAttribute()
    {
		$default  = 3.3;
		$cashback_percentage = 3.3;
		$detail = Configuration::where('config_key', 'platform_fee')->first();
		if($this->role_id ==  getConfigConstant('MERCHANT_ROLE_ID') && !empty($this->BusinessDetail->business_category)){
			$business_category= BusinessCategory::select('id')
			->where('id', $this->BusinessDetail->business_category)->first();
			$cashback_business_type    = Cashback::where('business_category_id',$business_category->id ?? '')->first();
			$cashback_percentage = ($this->role_id ==  getConfigConstant('MERCHANT_ROLE_ID')) ? $cashback_business_type->percentage ?? 0 : 0;
		}
		$default = [
			'standard_cashback_percentage' => $cashback_percentage, //admin standard cashback
			'ts_total_amount' => 0,
			'ts_extra_percentage' => 0,
			'rp_extra_percentage' => 0,
			'platform_fee' => $detail->config_value,
		];
		if($this->role_id == 3){

			$CashbackRule = CashbackRule::where('user_id', $this->id)->first();
			if(!empty($CashbackRule)){
				$CashbackRule->standard_cashback_percentage = $CashbackRule->standard_cashback_percentage + $cashback_percentage; //added admin standard cashback
				$default = $CashbackRule;
				$default->platform_fee = !empty($detail) ? $detail->config_value : 0;
			}
		}
		return $default;
    }

	protected function getIsPinGeneratedAttribute()
	{
		if(!empty($this->pin)){
			return true;
		} else {
			return false;
		}
	}

	protected function getImageUrlAttribute()
    {
		$UserMedia = UserMedia::where('user_id', $this->id)->where('type', UserMedia::TYPE_IMAGE)->first();

        if(!empty($UserMedia) && !empty($UserMedia->file)){
            return $UserMedia->file;
        } else {
            return URL::to('/').''.'/assets/user-dummy.png';
        }
    }

	protected function getTaxPercentageAttribute()
	{
		$adminModel = User::where('role_id', getConfigConstant('ADMIN_ROLE_ID'))->first();
		$merchantTax = Tax::where('user_id', $this->id)->first();
		$adminTax = Tax::where('user_id', $adminModel->id)->first();
		if(empty($merchantTax)){
			return $adminTax->tax;
		} else {
			if($merchantTax->tax < $adminTax->tax ||  $merchantTax->tax == 0){
				return $adminTax->tax;
			} else {
				return $merchantTax->tax;
			}
		}
	}

	protected function getStatusNameAttribute()
    {
		switch ($this->status) {
			case getConfigConstant('STATUS_ACTIVE'):
			  return 'Active';
			  break;
			case getConfigConstant('STATUS_PENDING'):
			  return 'Pending';
			  break;
			case getConfigConstant('STATUS_KYC_VERIFICATION'):
			  return 'Kyc';
			  break;
			default:
			  return 'In Active';
		  }

    }

	protected function getBusinessDocumentAttribute()
    {
		$UserMedia = UserMedia::where('user_id', $this->id)->where('type', UserMedia::TYPE_DOCUMENT)->first();

        if(!empty($UserMedia) && !empty($UserMedia->file)){
            return $UserMedia->file;
        } else {
            return null;
        }
    }


	public function getNameAttribute($value)
	{
		return $value ?? $this->first_name . ' ' . $this->last_name;
	}


	public function store(array $input)
	{
		$input['name'] = $input['name'] ?? $this->first_name . ' ' . $this->last_name;
		$input['first_name'] = $input['first_name'];
		$input['last_name'] = $input['last_name'];
		$input['phone_number'] = $input['phone_number'];
		$input['country_code'] = $input['country_code'];
		$input['password'] = Hash::make($input['password']);
		$uuid = Str::uuid()->toString();
		$input['uuid'] = $uuid;
		$userVerifyResource = new UserVerifyResource($input);

		$userVerifyArray = $userVerifyResource->toArray();

		$input = array_merge($input, $userVerifyArray);
		return self::create($input);
	}

	public function updateRecord(array $condition, array $input)
	{
		$input['name'] = $input['name'] ?? $this->first_name . ' ' . $this->last_name;
		return self::updateOrCreate($condition, $input);
	}

	public function updateSatus(array $condition, array $input)
	{
		return self::updateOrCreate($condition, $input);
	}


	public function fetchOne(array $whereArray)
	{
		return self::with('media', 'address', 'kyc')->where($whereArray)->latest()->first();
	}

	public function device_token()
	{
		return $this->hasMany(DeviceToken::class, 'user_id');
	}



	public function fetchByID(int $id)
	{
		return self::with('media', 'address', 'kyc')->find($id);
	}



	public function scopeIsMerchant($query)
	{
		return $query->where('role_id', getConfigConstant('MERCHANT_ROLE_ID'));
	}



	public function merchant()
	{
		return $this->hasOne(User::class, 'id')->where('role_id', getConfigConstant('MERCHANT_ROLE_ID'));
	}

	public function media()
	{
		return $this->hasMany(UserMedia::class, 'user_id')->isActive();
	}

	public function address()
	{
		return $this->hasOne(UserAddress::class, 'user_id')->isActive();
	}

	public function tax()
	{
		return $this->hasOne(Tax::class, 'user_id');
	}

	public function verification()
	{
		return $this->hasOne(Verification::class, 'email', 'email');
	}


	public function kyc()
	{
		return $this->hasOne(UserKyc::class, 'user_id')->isActive();
	}

	public function getMerchant($id = null)
	{
		$data = self::Has('merchant')->with(['media', 'address', 'kyc']);
		if ($id) {
			$data->where('id', $id);
		}
		return $data = $data->get();
	}

	public function remove(int $id)
	{
		self::where('id', $id)->delete();
	}

	public function profile(int $id)
	{
		return self::with('address', 'kyc', 'media')->where('id', $id)->first();
	}


	public function token()
	{
		return $this->hasMany(Token::class);
	}


	public function revokeTokens()
	{
		$this->token->each(function ($token, $key) {
			$token->revoke();
		});
	}



	public function scopeIsUser($query, $status = null)
	{
		return $query->where('role_id', getConfigConstant('USER_ROLE_ID'));
	}

	public function scopeByStatus($query, $status = null)
	{
		$status = $status ?? getConfigConstant('STATUS_ACTIVE');
		$query->where('status', $status);
		return $query;
	}

	public function BusinessDetail()
	{
		return $this->belongsTo(BusinessDetail::class, 'id', 'user_id');
	}

	public function WalletDetails()
	{
		return $this->belongsTo(Wallet::class, 'id', 'user_id');
	}




	public function nearByFetch($latitude, $longitude, $distance = 10)
	{
		// $query = self::with(['address', 'cashback' => function ($q) {
		// 	$q->latest('cashback')->first();
		$query = self::with(['address' => function ($q) {
			$q->first();
		}])->isMerchant()
			->join('user_addresses', 'users.id', '=', 'user_addresses.user_id');

		if (!empty($latitude) && !empty($longitude)) {
			$query->selectRaw(
				'users.*, (6371 * acos(cos(radians(?)) *
            cos(radians(user_addresses.latitude)) *
            cos(radians(user_addresses.longitude) - radians(?)) +
            sin(radians(?)) *
            sin(radians(user_addresses.latitude)))) AS distance',
				[$latitude, $longitude, $latitude]
			)->having('distance', '<=', $distance)->orderBy('distance');
		} else {
			$query->addSelect('users.*')->latest('id');
		}

		return $query->get();
	}

	public function nearByFetchId($latitude, $longitude, $distance = 10, $currentUserId)
	{
		$query = self::with(['address' => function ($q) {
			$q->first();
		}])->isMerchant()
			->join('user_addresses', 'users.id', '=', 'user_addresses.user_id');

		if (!empty($latitude) && !empty($longitude)) {
			$query->selectRaw(
				'users.*, (6371 * acos(cos(radians(?)) *
            cos(radians(user_addresses.latitude)) *
            cos(radians(user_addresses.longitude) - radians(?)) +
            sin(radians(?)) *
            sin(radians(user_addresses.latitude)))) AS distance',
				[$latitude, $longitude, $latitude]
			)->having('distance', '<=', $distance)->orderBy('distance')
			->leftJoin('transactions AS t', function ($join) use ($currentUserId) {
                $join->on('users.id', '=', 't.to_user_id')
                    ->where('t.from_user_id', '=', $currentUserId);
                })
            ->whereNull('t.to_user_id')
			;
		} else {
			$query->addSelect('users.*')
			->leftJoin('transactions AS t', function ($join) use ($currentUserId) {
                $join->on('users.id', '=', 't.to_user_id')
                    ->where('t.from_user_id', '=', $currentUserId);
                })
            ->whereNull('t.to_user_id');
		}

		return $query->pluck('id');
	}


	public function wallet()
	{
		return $this->hasOne(Wallet::class, 'user_id');
	}
}
