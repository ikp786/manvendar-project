<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\ResetsPasswords;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\User;

use App\UserPassword;

use App\PasswordChange ;
use App\Company ;
use Validator;
use Response;
use DB;
use Session;
use Illuminate\Support\Facades\Redirect;
use App\Traits\CustomTraits;
use Hash;

class PasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset requests
    | and uses a simple trait to include this behavior. You're free to
    | explore this trait and override any methods you wish to tweak.
    |
    */

    use ResetsPasswords;
	use CustomTraits;
    /**
     * Create a new password controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }
	public function showResetForm(Request $request, $token = null)
    {
		return view('passwordReset.mobile-form');
    }
	public function passwordReset(Request $request)
    {
        $this->validate($request, ['mobile' => 'required|numeric|digits:10']);
		$user_details= User::where('mobile',$request->mobile)->select('id','company_id')->first();
		if($user_details)
		{
			$token = str_random(60);
			$digits = 6;
			$otp = rand(pow(10, $digits-1), pow(10, $digits)-1);
			session()->put('request_mob_number',$request->mobile);
			PasswordChange::where(['mobile'=>$request->mobile])->delete();
			PasswordChange::create(['mobile'=>$request->mobile,'token'=>$token,'otp'=>$otp,'user_id'=>$user_details->id]);
			$mobile=$request->mobile;
			$message="Priya Patner, OTP  $otp for update Password";
			$message = urlencode($message);
			CustomTraits::sendSMS($mobile,$message,1);
			$message="Password Request OTP has been sent on Registered Mobile Number";
			return view('passwordReset.showPasswordForm',compact('token','message','mobile'));
		}
		
       return redirect()->back()->withErrors(['mobile' => "Mobile Number Does not exist"]);

       
    }
	public function showPasswordForm(Request $request)
    {
       
		$token = str_random(60);
		$psw = PasswordChange::where('mobile',session()->get('request_mob_number'))->first();
		
		if($psw)
		{
			PasswordChange::where(['mobile'=>$psw->mobile])->update(['token'=>$token]);
			return view('passwordReset.showPasswordForm',compact('token'));
		}
		session()->flash('fails',"Please make request of password reset again. Click Back");
		
		
       return redirect()->back()->withErrors(['mobile' => "Mobile Number Does not exist"]);

       
    }
	public function store(Request $request)
    {
		 $rules = array(
          	'password' => 'required',
            'password_confirmation' => 'required|same:password',
            'token' => 'required',
            'otp' => 'required|numeric|digits:6',
        );
		
		
		$validator = Validator::make($request->all(), $rules);
		if ($validator->fails()) {
			 return redirect()->back()->withErrors($validator->getMessageBag()->toArray());
			return Response::json(array(
				'success' => false,
				'errors' => $validator->getMessageBag()->toArray()
			)); 
		}
		
		$exist_record = PasswordChange::where(['otp'=>$request->otp])->select('user_id','id','token','mobile')->first();
		if($exist_record)
		{ 
			if($exist_record->token != $request->token)
			{
				session()->flash('fails', "Your Passowrd Reset Request has been expired");
				return Redirect::back();
			}
			$exist_user = User::find($exist_record->user_id);
			$exist_user->password = bcrypt($request->password);
			$password_change = PasswordChange::find($exist_record->id);
			DB::beginTransaction();
			try{
    			
    			$checkLoginExist = '0';
    			 
    			// $login_data_count = UserPassword::where('mobile_no', $exist_record->mobile)->where('password', $exist_user->password)->orderBy('id', 'desc')->take(3)->count(); 
                
                $login_data_count = UserPassword::where('mobile_no', $exist_record->mobile)->orderBy('id', 'desc')->take(3)->get();
                 
                    if(!empty($login_data_count)){
                        foreach ($login_data_count as $lgData) {  
                          
                            if (Hash::check($request->password, $lgData->password)){
                                 $checkLoginExist = '1'; 
                            } 
                        }
                    } 
                     
                    if($checkLoginExist == '1'){
                         
    				    session()->flash('fails', "You already used these passwords. Please enter other password.");
		                return Redirect::back();
    				
                    }else{
                        
        				$exist_user->save();
        				$password_change->delete();
        				
        				User::where('mobile', $exist_record->mobile)->update(['wrong_attempt' => 0]);
        				
        				UserPassword::create(['mobile_no'=>$exist_record->mobile,'password'=>$exist_user->password]);
        			  
        				$message="Passowrd has been updated successfully";
        				DB::commit();
        				//return redirect()->back()->withSuccess(['mobile' => "Password has been updated successfully"]); 
        				session()->flash('message', "Password has been updated successfully");
        				return redirect('/');
        				
                    }
    				 return Redirect::back();
			}
			catch(Exception $e)
			{
				DB::rollback();
				session()->flash('fails', "Oops Something went wrong please try again");
				return Redirect::back();
			}
		}
		session()->flash('fails', "Please provide correct credentials");
		 return Redirect::back();

    }
	public function sendsms($number, $message, $company_id)
    {
       
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $data = curl_exec($curl);
        curl_close($curl);
        return $data;
    }
}
