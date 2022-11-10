<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AdhaarpayScheme extends Model
{
    protected $fillable = ['min_amt','max_amt','agent_charge','agent_comm','dist_comm','md_comm','admin_comm','wallet_scheme_id'];
}
