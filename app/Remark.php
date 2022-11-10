<?php

namespace App;

use Illuminate\Database\Eloquent\Model; 

class Remark extends Model
{
    //
	protected $fillable =['remark','created_by'];

	public function loadcash()
	{
		return $this->hasMany('App\Loadcash');
	}
}
