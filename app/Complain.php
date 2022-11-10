<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Complain extends Model
{
    protected $fillable = ['txn_id','issue_type','remark','status_id','user_id'];

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
        return $this->belongsTo('App\Netbank');
    }
}
