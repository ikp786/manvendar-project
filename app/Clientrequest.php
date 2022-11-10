<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Clientrequest extends Model
{
    protected $fillable = ['u_id','mobile','ifsc','account','bene','amount','c_name','RFU1','RFU2','RFU3','ProductCode'];
	
	
}
