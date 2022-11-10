<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use DB;
class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
    
    public static function is_assigned($loggedin_user_id=false,$action_id=false)
    {  
      	
        $GetPermission = DB::table('role_permissions')->where('user_id', @$loggedin_user_id)->where('action_id', $action_id)->first();
        if(!empty($GetPermission))
        {  /*
        	$GetPermission = DB::table('users')->
        		leftjoin('roles', 'users.role_id', '=', 'roles.id')->  
	        	whereIn('roles.type', ['1'])->
	        	where('users.id', @$loggedin_user_id)->first(['role_id','type']);

	        if(!empty($GetPermission))
    	    {*/	
    	        return '1';   
    	    //} 
        }else{  return '0'; }
    }
}
