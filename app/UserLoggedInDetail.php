<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserLoggedInDetail extends Model
{
    //
	protected $fillable=['user_id','ip_address','location','browser','latitude','longitude','country_name','region_name','city'];
	   
	public function user(){
        return $this->belongsTo('App\User'); 
    } 
}
