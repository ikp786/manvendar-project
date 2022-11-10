<?php

namespace App;
use App\Loadcash;

use Illuminate\Database\Eloquent\Model;

class Pmethod extends Model
{
    public function loadcash() {
        return $this->belongsTo('Loadcash');

    }
}
