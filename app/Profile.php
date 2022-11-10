<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Profile extends Model
{
    //
	protected $fillable=['is_sys_verification','is_opt_verification'];
	public function remark(){
        return $this->belongsTo('App\Remark');
    }
	public function profile_picture()
	{
		return $this->hasOne('App\User');
	}
}
