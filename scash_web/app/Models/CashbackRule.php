<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CashbackRule extends Model
{
    use HasFactory;
    protected $fillable = ['user_id', 'standard_cashback_percentage', 'ts_total_amount', 'ts_extra_percentage', 'ts_status',  'rp_total_amount', 'rp_extra_percentage', 'rp_status'];
    const RULEINACTIVE =0;
    const RULEACTIVE =1;
}
