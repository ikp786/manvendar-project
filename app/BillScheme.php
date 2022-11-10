<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BillScheme extends Model
{
    protected $fillable = ['min_amt','max_amt','agent_charge_type','agent_charge','agent_comm_type','agent_comm','dist_comm_type','dist_comm','md_comm_type','md_comm','admin_comm_type','admin_comm','wallet_scheme_id'];
}
