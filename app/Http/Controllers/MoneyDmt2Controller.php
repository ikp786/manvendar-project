<?php

namespace App\Http\Controllers;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests;
use App\Api;
use App\Masterbank;
use App\Company;
use Response;
use Validator;




	
class MoneyDmt2Controller extends Controller
{
	/*var $TERMINALID = 100210;
    var $LOGINKEY = 7053443683;
    var $MERCHANTID = 210;
    var $IP = '219.90.67.216';*/
    var $endpoint = "https://in.cyberplat.com/cgi-bin/instp/instp_pay_check.cgi";
    public function index()
	
    {
		$verify_code = Api::select('api_name')->where('acc_verify_id',1)->first();
            $ifsc = Masterbank::where(['bank_status'=>0,'saral'=>0])->get();
            $down_bank_lists='';
            foreach($ifsc as $ifs)
            {
                $down_bank_lists = $down_bank_lists.$ifs->bank_name.", ";
            }
            $c_id = Auth::user()->company_id;
            $updown = Company::where('id',$c_id)->first();
            $netbanks = Masterbank::lists('bank_name','bank_code');
    	return view('agent.money.dmt2',compact('netbanks','updown','ifsc','verify_code','down_bank_lists'));
    }
	
	public function validateMobile(Request $request)
	{
		if($request->ajax())
		{
			$mobile = $request->mobile;
			
			$MobileInfo = $mobile;
			if($MobileInfo =='')
			{
				return response()->json(['status'=>0,'message'=>"Mobile Number  Not Registered"]);
			}
			elseif($MobileInfo->verify == 0)
			{
				$otp = $this->sendOTP($MobileInfo);
				return response()->json(['status'=>2,'message'=>"OTP has been sent at entered mobile number"]);
			}
			else
				return response()->json(['status'=>1,'message'=>"Mobile Number Registered"]);
			
		}
		return view('errors.page-not-found');
	}
	

}