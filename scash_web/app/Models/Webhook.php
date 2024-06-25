<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Webhook extends Model
{
    use HasFactory;

    protected $fillable = ['resourceId','topic','responce','new_responce'];

    public function fetch(){
        return self::all();
    }

}
