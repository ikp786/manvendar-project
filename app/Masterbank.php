<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Masterbank extends Model
{
	protected $fillable = ['bank_name','ifsc','account_digit','bank_sort_name','bank_code','status_id'];
    public static function bankDownList()
	{
		return Masterbank::select('bank_name','saral','smart','sharp','secure')->where('bank_status',0)->get();
		//return Masterbank::where('bank_status',0)->lists('bank_name','id')->toArray();
	}
}
