<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Beneficiary extends Model
{
    protected $fillable = ['benficiary_id','account_number','ifsc','bank_name','mobile_number','vener_id','status_id','user_id','customer_number','name', 'api_id','otp','is_bank_verified'];
}
