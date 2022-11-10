<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class VoucherApiResponse extends Model
{
    //
	protected $fillable=['user_id','report_id','request_type','request_param'];
}
