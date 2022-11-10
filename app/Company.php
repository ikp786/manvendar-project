<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    protected $fillable = ['company_name','company_email','user_id', 'company_phone','company_mobile','company_logo','company_website','company_address','ytoken'];
	
	public static function getApiDownList()
	{
		return Company::select('up_down','up_down2')->orderBy('id','asc')->first();
	}
	public static function MDLists() 
	{
		//return Company::pluck('company_name', 'id')->toArray();
		// return Company::select('id', 'company_name','user_id')->get();
		return Company::select('id', 'company_name','user_id','news','agent_header_color','agent_bg_color','md_bg_color','agent_font_color','md_font_color')->get();
	}
	public static function getUserIdOfCompanyId($company_id)
	{
		return Company::find($company_id);
	}
}
