<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Apiresponse extends Model
{
    protected $fillable = ['message','api_type','user_id','report_id' ,'request_message','api_id','request_from_api','request_from_api'];
}
