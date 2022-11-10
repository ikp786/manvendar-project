<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Myorder extends Model
{
    protected $fillable = ['txnid','user_id','product_id','amount','status_id'];
}
