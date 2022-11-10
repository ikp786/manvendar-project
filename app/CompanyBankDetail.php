<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CompanyBankDetail extends Model
{
    //
	protected $fillable = ['bank_name','account_number','ifsc_code','branch_name','message_one','message_two','user_id'];
	
	public function user(){
        return $this->belongsTo('App\User');
    }
}
