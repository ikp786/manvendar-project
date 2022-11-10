<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ApiResponseToApp extends Model
{
    protected $fillable=['response_to_app','response_from_app','request_type','user_id','record_id','message','api_id'];
}
