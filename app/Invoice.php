<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    protected $fillable = ['id','month','pan_card','buyers_code','buyers_name','per_txn_ded','per_txn_amount','upfront_ded','upfront_amount','ver_count','ver_amount','service_tax_number'];
	
}
