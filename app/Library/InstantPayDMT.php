<?php

namespace App\library {
use App\Apiresponse;
use Auth;
use App\Traits\CustomTraits;
    class InstantPayDMT
    {
		use CustomTraits;
       
		
		function accountNumberVerification($content,$userId,$insertedId)
		{
			$apiResp = Apiresponse::create(['message'=>'','api_id'=>16,'user_id'=>$userId,'api_type'=>"ACCOUNT_VERIFY",'report_id'=>$insertedId,'request_message'=>$content]);
			$url = config('constants.INSTANT_PAY_URL') ."/ws/imps/account_validate";//die;
			$data =  $this->getCurlPostIMethod($url,$content);
			$apiResp->message = $data;
			$apiResp->save();
			return $data;
		}
		function isBankdDownOrNot($content) 
		{
			$token = config('constants.INSTANT_KEY');
			$content =array('token'=>$token,'request'=>$content);
			$content = json_encode($content);//die;
			$url = config('constants.INSTANT_PAY_URL') ."/ws/dmi/bank_details";//die;
			return $this->getCurlPostIMethod($url,$content);
		}
		function checkCurrentBankStatus($content) 
		{
			$token = config('constants.INSTANT_KEY');
			$content =array('token'=>$token,'request'=>$content);
			$content = json_encode($content);//die;
			$url = config('constants.INSTANT_PAY_URL') ."/ws/dmi/bank_details";//die;
			return $this->getCurlPostIMethod($url,$content);
		}
      
	}
		



    }
