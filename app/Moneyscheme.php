<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Moneyscheme extends Model
{
    protected $fillable = ['scheme_name','status_id','user_id','company_id','scheme_for'];

    public function status(){
        return $this->belongsTo('App\Status');
    }
}
