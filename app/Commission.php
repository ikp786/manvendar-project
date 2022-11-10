<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Commission extends Model
{
    protected $fillable = ['provider_id','scheme_id','admin','md','d','r','type','user_id','max_commission','purchage_cast'];

    public function provider(){
        return $this->belongsTo('App\Provider');
    }
     public function Rechargeprovider(){
        return $this->belongsTo('App\Rechargeprovider');
    }
    
}
