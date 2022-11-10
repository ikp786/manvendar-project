<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class State extends Model
{
    //
    protected $fillable=['name','type','code','short_code','capital'];
    public function members(){
        return $this->hasMany('App\Member');
    }
    public function stateList()
    {
    	return State::pluck('name', 'id')->toArray();
    }
}
