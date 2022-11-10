<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
   protected $fillable = [
        'name', 'email', 'password', 'role', 'mobile','mobile_token','profile_id','balance_id','last_login_at','last_login_ip',
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];
    public function member(){
        return $this->hasOne('App\Member');
    }
    public function balance(){
        return $this->hasOne('App\Balance');
    }
    public function company(){
        return $this->belongsTo('App\Company');
    }
	public function profile_picture()
	{
		return $this->hasOne('App\Profile');
	}
    public function status(){
        return $this->belongsTo('App\Status');
    }
    public function profile(){
        return $this->belongsTo('App\Profile');
    }
    public function role(){
        return $this->belongsTo('App\Role');
    }
    public function getFullNameAttribute() {
        return ucfirst($this->name) . ' ' . $this->mobile;
    }
    public function upscheme(){
        return $this->belongsTo('App\Upscheme');
    }
    public function scheme(){
        return $this->belongsTo('App\Scheme');
    }
    public function moneyscheme(){
        return $this->belongsTo('App\MoneyScheme');
    }
	/* Below funtion is added by rajat*/
	public function txnslab()
	{
		return $this->hasOne('App\Txnslab');
	}
	public function mdpertxn()
	{
		return $this->hasOne('App\Mdpertxn');
	}
	public function parent()
    {
        return $this->belongsTo('App\User', 'parent_id');
    }
	public function getChildIdOfLoginedUser($logined_user_id,$role_id)
	{
		if($role_id == 5)
			return (array($role_id));
		else
			return User::where(['parent_id'=> $logined_user_id])->lists('id', 'id')->toArray();
	}
	public function getUserById($id)
	{
		
		return ($this->select('name','email','mobile','role_id','parent_id','balance_id')->where('id',$id)->first());
	}
	public function getChildOfChild($child_id)
	{
		return User::whereIn('parent_id',$child_id)->lists('id', 'id')->toArray();
	}
}
