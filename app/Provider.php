<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Provider extends Model
{
    protected $fillable = ['provider_name','provider_code','ege_code','sertype','service_id','vender_code','api_id','suvidhaa'];


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


