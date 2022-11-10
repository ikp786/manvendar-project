<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RemitterRegistration extends Model
{ 
	protected $fillable = ['fname','lname','mobile','otp','verify','rem_bal','rem_kyc_bal','kyc','is_quick_verify','client_code','verified_online'];
	 
}
