<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ApiAepsAgentRegistration extends Model
{
    //
	protected $fillable=['user_id','agent_id','pan_number','aadhaar_number','mobile','agent_name'];
	public function user()
	{
		return $this->belongsTo('App\User','user_id');
		
	}
}
