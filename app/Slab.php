<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Slab extends Model
{
    protected $fillable = ['slab_id','scheme_id','admin','md','d','r','status_id','user_id','type'];
}
