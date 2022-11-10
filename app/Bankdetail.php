<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Bankdetail extends Model
{
    protected $fillable = ['bank_name', 'bank_account_number', 'bank_ifsc', 'bank_account_name', 'company_id'];
}
