<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Holiday extends Model
{
    protected $fillable = ['name','holiday_date','message_first','message_second','active_holiday'];
}
