<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class VoucherPrice extends Model
{
    protected $fillable =['price','voucher_brand_id'];
}
