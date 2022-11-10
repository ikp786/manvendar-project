<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Refundrequest extends Model
{
    protected $fillable = ['ref_id','number','txnid','amount','otp','status'];
}
