<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AepsUserRegistrationResponse extends Model
{
    //
	protected $fillable = ['request_from_api','user_id','mobile','pan_number','aadhaar_number','request_message','aeps_registration_id'];
}
