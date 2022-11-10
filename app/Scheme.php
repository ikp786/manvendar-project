<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Scheme extends Model
{
    protected $fillable = ['scheme_name','status_id','user_id','company_id'];

    public function status(){
        return $this->belongsTo('App\Status');
    }
}
