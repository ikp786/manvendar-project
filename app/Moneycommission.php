<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Moneycommission extends Model
{
    protected $fillable = ['provider_id','scheme_id','admin','md','d','r','type','user_id'];

    public function moneyscheme(){
        return $this->belongsTo('App\Moneyscheme');
    }
     
    
}
