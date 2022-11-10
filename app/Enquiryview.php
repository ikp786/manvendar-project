<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Enquiryview extends Model
{
    protected $fillable = ['id','conv_id','name','message','status'];
}
