<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Remitteregister extends Model
{
    protected $fillable = ['f_name','l_name','otp','mobile','user_id','status','total_limit','used_limit','remaining_limit','paycash_limit','paycash_used','paycash_remaining'];
}
