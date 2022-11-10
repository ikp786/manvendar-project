<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Api extends Model
{
    protected $fillable = ['api_name','username','password','api_url','api_key','status'];


    public function status(){
        return $this->belongsTo('App\Status');
    }
	public static function getActiveProdut()
	{
		return Api::where(['status_id'=> 1])->lists('api_name', 'id')->toArray();
		
	}
}
