<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserCookieSet extends Model
{
    //
	protected $fillable=['user_id','cookie_restrict'];
}
