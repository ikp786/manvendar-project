<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Netbank extends Model
{
    public function getPayBankAttribute()
    {
        return $this->attributes['bank_name'] . ' ' . $this->attributes['bank_account'];
    }
}
