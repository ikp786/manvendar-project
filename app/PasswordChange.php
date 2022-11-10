<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PasswordChange extends Model
{
    //
	protected $fillable=['mobile','token','otp','user_id'];
}
