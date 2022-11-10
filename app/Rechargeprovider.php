<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Rechargeprovider extends Model
{
    protected $fillable = ['provider_name','provider_code1','provider_code2','provider_code3','api_id'];


    public function service(){
        return $this->belongsTo('App\Service');
    }

    public function api(){
        return $this->belongsTo('App\Api');

    }
    public function provider(){
        return $this->belongsTo('App\Provider');
    }
    public function status(){
        return $this->belongsTo('App\Status');
    }
}


