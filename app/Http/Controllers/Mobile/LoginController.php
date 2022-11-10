<?php

namespace App\Http\Controllers\Mobile;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Traits\CustomTraits;
use Validator;
use Response;
use App\User;
use App\AppDeviceLocation;
use App\ActiveService;
use App\Profile;
use Exception;
use Auth;
use DB;
use Hash;
class LoginController extends Controller
{
	use CustomTraits;
   public function agentLogin(Request $request)
    {
		 $rules = array(
            'case' => 'required',
			'password' => 'required',
			'mobileNumber' => 'required',
            'hardwareSerialNumber' => 'required',
            'deviceName' => 'required',
            'imei' => 'required',
        );
		$validator = Validator::make($request->all(), $rules);
		if ($validator->fails()) {
			return Response::json(array(
				'status' => 10,
				'message' => "Invalid Parameters",
				'missingParam' => $validator->getMessageBag()->toArray()
			)); // 400 being the HTTP code for an invalid request.
		}
        $isAppAllow=ActiveService::where(['api_id'=>26,'status_id'=>0])->first();
        if($isAppAllow)
            return ['status'=>2,'message'=>$isAppAllow->message];

        $mobile = $this->decrypt($request->mobileNumber);
        $password = $this->decrypt($request->password);

		$otp = $request->otp;
        if($request->case=='FIRST')
        {

			$company_id = 1;
			$login_count = User::select('id','mobile','status_id','password','otp_number','total_logins','otp_verify','profile_id')->where('mobile',$mobile)->first();
			if($login_count)
			{
				if($login_count->status_id !=1)
					return Response::json(['status'=>2,'message'=>'You are not activated yet! Please contanct with Admin']);
				if($mobile==$login_count->mobile)
				{
					if(Hash::check($password, $login_count->password))
					{
						if($request->imei !=$login_count->profile->imei_number)
						{
							$digits = 6;
							$otp = rand(pow(10, $digits-1), pow(10, $digits)-1);
							$message = "Dear Partner, Device verification OTP : $otp, Thanks";
							$message = urlencode($message);
							
							Profile::where('user_id',$login_count->id)->update(['imei_verify_otp'=>$otp]);
							CustomTraits::sendSMS($mobile, $message,1);
							return response()->json(['status'=>700,'message'=>"OTP has been sent registerd mobile number"]);
						}
						$credential=['mobile'=>$mobile,'password'=>$password,'status_id'=>1];
						return $this->authenticate($request,$credential);
						if($login_count->total_logins < 1)
						{
							$digits = 4;
							$otp = rand(pow(10, $digits-1), pow(10, $digits)-1);
							$message = "Dear Partner, Your Login OTP : $otp, Payjst Thanks";
							$message = urlencode($message);
							$this->sendSMS($request->mobile_number, $message,$login_count->company_id);
							$login_count->otp_number = $otp;
							$login_count->total_logins = 1;
							$login_count->save();
							return response()->json(['status' => 4, 'message' =>"OTP has been sent on your mobile number!"]);
						}
						else
						{
							if($login_count->otp_verify == 0)
							{
								return response()->json(['status' => 3, 'message' =>"Enter Today OTP"]);
							}
							$credential=['mobile'=>$mobile,'password'=>$password,'status_id'=>1];
							return $this->authenticate($request,$credential);
						}
					}else{
						return Response::json(['status'=>2,'message'=>'Your credentials are wrong. Please try again.']);
					}
				}
				else
				{
					return Response::json(['status'=>2,'message'=>'Wrong Mobile Number!']);
				}
			}
			else
			{
				return Response::json(['status'=>2,'message'=>'Your credentials are wrong. Please try again.']);
			}
        }
        elseif($request->case=='REOTP')
        {
			try{
				$login_count = User::select('id','mobile','status_id','password','otp_number','total_logins',
                    'otp_verify','is_login')->where('mobile',$mobile)->first();
			}
			catch(Exception $e){
				return Response::json(['status'=>2,'message'=>'Wrong Mobile Number!']);
			}
			if($login_count->status_id !=1)
					return Response::json(['status'=>2,'message'=>'You are not activated yet! Please contanct with Admin']);
			$message = "Dear Partner, Your Login OTP : $login_count->otp_number, Payjst Thanks";
			$message = urlencode($message);
			$this->sendSMS($request->mobile_number, $message,$login_count->company_id);
			return Response::json(['status'=>4,'message'=>"OTP has been sent on your mobile number!"]);
		}
		else{
			$credential=['mobile'=>$mobile,'password'=>$password,'status_id'=>1,'otp_number'=>$request->otp];
			return $this->authenticate($request,$credential);
		}
	}
	public function authenticate($request,$credential)
    {
        if (Auth::attempt($credential))
		{
			DB::beginTransaction();
			try{
				AppDeviceLocation::create([
										'device_name'=>isset($request->device_name)?$request->device_name:'',
										'user_id'=>Auth::id(),
										'email'=>Auth::user()->email,
										'IMEI'=>isset($request->imei)?$request->imei:'',
										'sim_subscriber_id'=>isset($request->simSubsciberId)?$request->simSubsciberId:'',
										'sim_serial_no'=>isset($request->simSerialNumber)?$request->simSerialNumber:'',
										'ip'=>\Request::ip(),
										'hardware_serial_no'=>isset($request->hardwareSerialNumber)?$request->hardwareSerialNumber:'',
										'longitude'=>isset($request->longitude)?$request->longitude:'',
										'latitude'=>isset($request->latitude)?$request->latitude:'',
										]);
				$token_key = str_random(30).''.Auth::id();
				$user =User::selectRaw('id,otp_verify,mobile_token,total_logins')->find(Auth::id());
				if(Auth::user()->otp_verify ==0)
					$user->otp_verify=1;
				$user->total_logins +=1;
				$user->mobile_token=$token_key;
				$user->save();
				DB::commit();
				return response()->json([
					'status' => 1,
					 'id' =>Auth::id(),
					 'token'=>$user->mobile_token,
					 'name' => Auth::user()->name,
					 'mobile' => Auth::user()->mobile,
					 'email' => Auth::user()->email,
					 'user_balance' => number_format(Auth::user()->balance->user_balance,2),
					 'otp_number' =>Auth::user()->otp_number,
					    'profile_picture' =>url('/')."/user-uploaded-files/".Auth::id()."/".Auth::user()->profile->profile_picture,
					 'role_id'=>Auth::user()->role_id,
					  'message' => 'Successfully Login',
                    'shop_name' => Auth::user()->member->company,
                    'address' => Auth::user()->member->address,
                    'shop_address' => Auth::user()->member->office_address,
                    'joining_date' => date("d-m-Y", strtotime(Auth::user()->created_at)),
                    'last_update' => date("d-m-Y", strtotime(Auth::user()->updated_at)),

					]);
			}
			catch(Exception $e)
			{
				DB::rollback();
				return response()->json(['status' => 2, "message"=>"Whoops! Something went wrong. Please try again"]);
			}
        }
		else
		{

           return response()->json(['status' => 2, "message"=>"Credential Wrong"]);
        }

    }

    private function decrypt($data){
        $decrypted = openssl_decrypt($data,
            "AES-128-CBC",
            "OPENUSERIDPASSWO",
            0);
        return $decrypted;
    }
	public function verifyNewMobileDevice(Request $request)
	{
		$rules=[
			'mobileNumber'=>'required|string',
			'otp'=>'required',
			'imei'=>'required'
		];
		$validator = Validator::make($request->all(),$rules);
		if($validator->fails())
		{
			return response()->json(['status'=>10,'message'=>"Invalid/Mission param",'errors'=>$validator->getMessageBag()->toArray()]);
		}
		try
		{
			$mobile = $this->decrypt($request->mobileNumber);
			$otp = $this->decrypt($request->otp);
			$userdetails  = User::selectRaw('id,mobile,profile_id')->where(['mobile'=>$mobile])->firstOrFail();
		}
		catch(Exception $e)
		{
			return response()->json(['status'=>400,'message'=>"Invalid mobile number,Please enter correct mobile number"]);
		}
		if($userdetails->profile->imei_verify_otp ==$otp)
		{
			Profile::where('user_id',$userdetails->id)->update(['imei_number'=>$request->imei]);
			return response()->json(['status'=>701,'message'=>"Device verification successfully"]);
		}
		else{
			return response()->json(['status'=>702,'message'=>"Invalid OTP"]);
		}
	}
	public function resendOTP(Request $request)
	{
		$rules=[
			'mobileNumber'=>'required|string',
			'type'=>'required'
		];
		$validator = Validator::make($request->all(),$rules);
		if($validator->fails())
		{
			return response()->json(['status'=>10,'message'=>"Invalid/Mission param",'errors'=>$validator->getMessageBag()->toArray()]);
		}
		try
		{
			$mobile = $this->decrypt($request->mobileNumber);
			$type = $this->decrypt($request->type);
			$userdetails  = User::selectRaw('id,mobile,profile_id')->where(['mobile'=>$mobile])->firstOrFail();
		}
		catch(Exception $e)
		{
			return response()->json(['status'=>400,'message'=>"Invalid mobile number,Please enter correct mobile number"]);
		}
		if($type=="NEW_DEVICE_OTP")
		{
			$otp = $userdetails->profile->imei_verify_otp;
			$message = "Dear Partner, Your device verification OTP : $otp, Thanks";
			$message = urlencode($message);
			CustomTraits::sendSMS($mobile, $message,1);
			return response()->json(['status'=>700,'message'=>"OTP has been sent registerd mobile number"]);
		}
		else{
			return response()->json(['status'=>703,'message'=>"Invalid Request Opt Type"]);
		}
	}
}
