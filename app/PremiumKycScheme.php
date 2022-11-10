<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PremiumKycScheme extends Model
{
    //
	protected $fillable = ['min_amt','max_amt','agent_charge','dist_comm','md_comm','admin_comm','wallet_scheme_id'];
}
