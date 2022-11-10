<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ActionOtpVerification extends Model
{
    protected $fillable=['mobile','status_id','txnid','user_id','password_otp'];
}
