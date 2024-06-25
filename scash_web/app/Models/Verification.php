<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Verification extends Model
{
    use HasFactory;

    protected $fillable = [
        'email', 'phone_number', 'country_code', 'status', 'code', 'expired_at', 'email_code', 'email_status', 'user_id'
    ];

    public function store($input)
    {
        $start = date('Y-m-d H:i:s');
        $input = array_merge($input, ['expired_at' => date('Y-m-d H:i:s', strtotime('+1 hour', strtotime($start)))]);
        return self::create($input);
    }

    public function updateRecord($condition,$input){

        return self::updateOrcreate($condition,$input);
    }

    public static function  fetchOne($whereArray){
        return self::where($whereArray)->latest()->first();
    }

    public function fetch($whereArray){
        return self::where($whereArray)->latest()->get();
    }
    
    
}
