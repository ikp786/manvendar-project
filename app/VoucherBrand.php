<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class VoucherBrand extends Model
{
    protected $fillable =['name','voucher_categorie_id'];
	 public function price()
    {
        return $this->hasOne('App\VoucherPrice','voucher_brand_id');
    }
}
