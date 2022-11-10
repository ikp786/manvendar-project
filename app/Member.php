<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Member extends Model
{
    protected $fillable = ['user_id','pan_number','adhar_number','pin_code','ip_address','company'];

    public function user(){
        return $this->hasOne('User');
    }
    public function scheme(){
        return $this->belongsTo('App\Upscheme');
    }
	public function verificationSchemeName(){
        return $this->belongsTo('App\WalletScheme','verification_scheme');
    }
    public function DmtOneWalletScheme(){
        return $this->belongsTo('App\WalletScheme','imps_wallet_scheme');
    }
    public function DmtTwoWalletScheme(){
        return $this->belongsTo('App\WalletScheme','dmt_two_wallet_scheme');
    }
    public function PaytmWalletScheme(){
        return $this->belongsTo('App\WalletScheme','paytm_wallet_scheme');
    }
    public function AepsWalletScheme(){
        return $this->belongsTo('App\WalletScheme','aeps_scheme');
    }
    public function BillSchemeName(){
        return $this->belongsTo('App\WalletScheme','bill_scheme');
    }
	public function state()
	{
		return $this->belongsTo('App\State');
	}
}
