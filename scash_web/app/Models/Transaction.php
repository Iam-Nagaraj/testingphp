<?php

namespace App\Models;

use DateTime;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    const TYPE_MY_WALLET_DEPOSIT = 0; //Same user with bank & wallet
    const TYPE_MY_WALLET_WITHDRAW = 1; //Same user with bank & wallet
    const TYPE_WALLET_TO_WALLET = 2; //Two different users

    const WALLET = 1; 
    const CASHBACK = 2; 

    const MANUAL = 0;
    const INSTANT = 1;

    const STATUS_PENDING = 0; 
    const STATUS_COMPLETED = 1; 
    const STATUS_FAILED = 2; 
    const STATUS_CANCELLED = 3; 


    protected $fillable = ['wallet_id','amount','status','type','transaction_id','from_user_id','to_user_id','wallet_type','payment_type', 'account_id'];

	protected $appends=[
        'transaction_date', 'transfer_type', 'transaction_type'
    ];

	protected function getTransactionDateAttribute()
    {
        if(!empty($this->created_at)){
			$dateTime = new DateTime($this->created_at);
			$formattedDateTime = $dateTime->format('d-m-Y');
            return $formattedDateTime;
        } else {
            return 'N/A';
        }
    }

    protected function getTransferTypeAttribute()
    {
        if($this->wallet_type == self::TYPE_MY_WALLET_WITHDRAW){
            return 'Withdraw';
        } elseif($this->type == self::TYPE_MY_WALLET_DEPOSIT){
            return 'Deposit';
        } else {
            return 'WalletToWallet';
        }
    }

    protected function getTransactionTypeAttribute()
    {
        if($this->type == self::CASHBACK){
            return 'Cashback';
        } else {
            return 'Wallet';
        }
    }


	public function sender()
	{
		return $this->belongsTo(User::class, 'from_user_id')->select('id','first_name','last_name', 'name', 'uuid', 'phone_number', 'country_code', 'role_id');
	}

    public function receiver()
	{
		return $this->belongsTo(User::class, 'to_user_id')->select('id','first_name','last_name', 'name', 'uuid', 'phone_number', 'country_code', 'role_id');
	}

    public function accountDetails()
	{
		return $this->belongsTo(DwollaAccount::class, 'account_id')->select('id','user_id','json_data', 'default_account', 'is_default');
	}

}
