<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Balance extends Model
{
    protected $fillable = ['user_balance','user_commission','d_user_balance','m_user_balance','a_user_balance','user_id'];
}
