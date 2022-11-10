<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Enquiry extends Model
{
    protected $fillable = ['name','email','location','mobile','message','sales_remark','manager_remark','assigned'];
}
