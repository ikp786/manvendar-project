<?php 
namespace App\Traits;
use App\User;
use App\Report;
use App\Apiresponse;
use App\Balance;
use App\ActiveService;
use DB;
trait CustomAuthTraits
{
	public function checkLoginAuthentication($request)
	{
		$user_id = $request->userId;
        $password = $request->token;
        $userdetails = User::where('id',$user_id)->first();

         $isAppAllow=ActiveService::where(['api_id'=>26,'status_id'=>0])->first(); //need to change
        if($isAppAllow)
            return ['status'=>200,'message'=>$isAppAllow->message ."You have logged Out Successfully"];
		if($userdetails->status_id !=1)
			return ['status'=>2,'message'=>"You are de-activated"];

		if($userdetails->id == $user_id && $userdetails->mobile_token == $password)
			return ['status'=>1,'userDetails'=>$userdetails];
		else	
			return ['status'=>300,'message'=>"Token Missmatched"];
	} 
}