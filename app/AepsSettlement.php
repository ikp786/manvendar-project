<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AepsSettlement extends Model
{
    protected $fillable = ['bank_name','name','account_number','ifsc','branch_name','user_id','status_id'];

   	public function status(){
        return $this->belongsTo('App\Status');
    }
    public function user(){
    	return $this->belongsTo('App\User');
    }
}
