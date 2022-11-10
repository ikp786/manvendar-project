<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Voucher extends Model
{
    //
	protected $fillable =['external_order_id_out','product_guid','report_id'];
}
