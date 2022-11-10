<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Loadcash extends Model
{
    protected $fillable = ['user_id','pmethod_id','netbank_id','yacc','amount','bankref','status_id','d_picture','user_id','wallet','bank_name','request_remark','loc_batch_code','request_to','deposit_date','payment_mode','c_online_mode'];

    public function user(){
        return $this->belongsTo('App\User');
    }
    public function pmethod(){
        return $this->belongsTo('App\Pmethod');
    }
    public function status(){
        return $this->belongsTo('App\Status');
    }
    public function netbank(){
        return $this->belongsTo('App\CompanyBankDetail');
    }
	public function remark(){
        return $this->belongsTo('App\Remark');
    }
	public function report(){
        return $this->belongsTo('App\Report','id','payment_id');
    }
}
