<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Contracts\Encryption\DecryptException;
use Auth;
use Illuminate\Http\Request;

use App\User;
use App\UserPassword;
use App\PasswordChange;

use App\Profile;

use App\UserCookieSet;
use App\UserLoggedInDetail;
use Validator;
use App\Traits\CustomTraits;
use Redirect;
use Carbon\Carbon;
use Hash;

class LoginController extends Controller
{
    use CustomTraits;
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/home';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }
    
    public function force_redirect(Request $request)
    {  
        $token  =  session('request_token');
        $mobile  =  session('request_mob_number'); 
        $message  =  session('message');
         
       return view('passwordReset.showPasswordFormNewReset',compact('token','mobile'));
    }
         
    public function verify_again_redirect(Request $request)
    {   
        $token  =  session('request_token');
        $mobile  =  session('request_mob_number'); 
        $message  =  session('message');
        $user_id = session('user_id');     
        
        return view('passwordReset.showVerificationPasswordReset',compact('token','mobile','user_id'));
    }
    
    public function generateTransactionpinFrontEnd(Request $request)
	{
	        //dd($request);
		    //print_r($request->all());die;
		
			$transactionpin= $request->txn_pin;
			$confirmation_transactionpin=$request->confirm_txn_pin;
			$transactionpin=$confirmation_transactionpin;
			$user_id = $request->ver_user_id;
			
			$userPinRecord=Profile::where('user_id',$user_id)->first();
			if($request->otp != $userPinRecord->txn_otp)
			{
				return back()->with('fails', "OTP Wrong");
			}
			if($transactionpin==$confirmation_transactionpin)
			{  
				Profile::where('user_id', $user_id)->update(['txn_pin' => $transactionpin,'txn_otp' =>'','txn_pin_change_date' =>date("Y-m-d H:i:s")]);
				
				// session()->put('message',"Transaction PIN Successfully Updated");
				
				$request->session()->flash('message', 'Transaction PIN Successfully Updated');
				
				}
			else{
				return back()->with('fails', "Confirm Transaction Pin did not matched");
			}
			 
    		return redirect('/');
		
	}
	
 
    
    public function login(Request $request)
    {   
        
       /* $isAcitveService = $this->isActiveService(25);
		if($isAcitveService){ 
			return response()->json(['status' => 0, 'message' => $isAcitveService->message]);
		}*/
			
        $mobile = $request->mobile;

        $loginThrough = $request->loginThrough;
       
        if ($request->caseType == "FIRST") {

            try {
                $login_count = User::select('id', 'mobile', 'status_id', 'password', 'otp_number', 'total_logins', 'otp_verify', 'opt', 'another_sys_otp', 'profile_id','updated_at','wrong_attempt','wrong_attempt_date')->where('mobile', $mobile)->firstOrFail();
            } catch (\Exception  $e) {
                return response()->json(['status' => 0, 'message' => 'your credentials wrong!']);
            }
            if ($login_count->status_id == 0)
                return response()->json(['status' => 0, 'message' => 'Your are not activated yet. Please contact with Administrator']);
            
               
            if (Hash::check($request->password, $login_count->password)){
                 
                $user_data_count = UserPassword::where('mobile_no', $mobile)->count(); 
                 
                if($user_data_count<1){
                	UserPassword::create(['mobile_no'=>$mobile,'password'=>$login_count->password]);
                }
                
                $login_data_check = UserPassword::where('mobile_no', $mobile)->orderBy('id', 'desc')->take(1)->first(); 
                
                
                $now = Carbon::now()->toDateString();
                $end_date = Carbon::parse($login_data_check->updated_at);
                $no_of_days = $end_date->diffInDays($now);
               
                if($no_of_days>'30'){
                        
                        $token = str_random(60);
            			$digits = 6;
            			$otp = rand(pow(10, $digits-1), pow(10, $digits)-1);
            			session()->put('request_mob_number',$request->mobile);
            			session()->put('request_token',$token);
            			PasswordChange::where(['mobile'=>$request->mobile])->delete();
            			PasswordChange::create(['mobile'=>$request->mobile,'token'=>$token,'otp'=>$otp,'user_id'=>$login_count->id]);
            			 
        				$mobile=$request->mobile;
            			$message="Priya Partner, OTP  $otp for update Password";
            			$message = urlencode($message);
            			CustomTraits::sendSMS($mobile,$message,1);
            			$message="Password Request OTP has been sent on Registered Mobile Number";
		                // session()->put('message',$message);
			            $request->session()->flash('message', $message);    
            			return response()->json(['status' => 18, 'message' => "Your Login Password Not Updated Between 30 Days. Please Update Login Password"]);
            			 
			        
                }
                else
                {
                    $credential = ['mobile' => $mobile, 'password' => $request->password, 'status_id' => 1];
    				$cookesName = "isNewSystemFound_".$mobile;
                    $isNewSystemFound = $request->cookie($cookesName);
                    if ($login_count->total_logins < 1 && $login_count->profile->is_opt_verification) {
                        $digits = 4;
                        $otp = rand(pow(10, $digits - 1), pow(10, $digits) - 1);
    
                        $message = "Dear Partner, Your Login OTP : $otp, Thanks";
                        $message = urlencode($message);
                        CustomTraits::sendSMS($mobile, $message, 1);
                        $login_count->otp_number = $otp;
                        $login_count->total_logins = 1;
                        $login_count->save();
                        return response()->json(['status' => 1, 'message' => "OTP has been sent on your mobile number!"]);
                    } else if ($login_count->otp_verify == 0 && $login_count->profile->is_opt_verification) {
                        if (!$this->isActiveService(101))
                            return response()->json(['status' => 3, 'message' => "Enter Today OTP"]);
                    } else if ($isNewSystemFound == '' && $login_count->profile->is_sys_verification) {
                        $digits = 6;
                        $otp = rand(pow(10, $digits - 1), pow(10, $digits) - 1);
                        $message = "Dear Partner, Your Login OTP : $otp, Thanks";
                        $message = urlencode($message);
                        // CustomTraits::sendSMS($mobile, $message, 1);
                        
                        
                        CustomTraits::sendByA2ZPlusSms($mobile,$message);
                        
                        $login_count->another_sys_otp = $otp;
                        $login_count->save();
                        return response()->json(['status' => 5, 'message' => "New System Found, Verify your system with OTP"]);
                    } 
                    User::where('mobile', $mobile)->update(['wrong_attempt' => 0]);
                    
                    $profile_data_check = Profile::where('user_id', $login_count->id)->orderBy('id', 'desc')->take(1)->first(); 
                     
                    $now = Carbon::now()->toDateString();
                    $ver_pin_end_date = Carbon::parse($profile_data_check->txn_pin_change_date);
                    $ver_pin_no_of_days = $ver_pin_end_date->diffInDays($now);
                   
                    if($ver_pin_no_of_days<'30' || $profile_data_check->txn_pin_change_date==''){
                        
                        return response()->json(['status' => 6, 'message' => "Logged In"]);
                   
                    }else if($ver_pin_no_of_days>'30'){
                        
                        $token = str_random(60);
            			$digits = 4;
            			$otp = rand(pow(10, $digits-1), pow(10, $digits)-1);
            			session()->put('request_mob_number',$request->mobile);
            			session()->put('request_token',$token);
            			session()->put('user_id',$login_count->id);
            			Profile::where('user_id', $login_count->id)->update(['txn_otp' => $otp]);
            		  
        				$mobile=$request->mobile;
            			$message="Priya Partner, OTP  $otp for Verification Pin Update";
            			$message = urlencode($message);
            		 	
            			CustomTraits::sendByA2ZPlusSms($mobile,$message);
            			 
            			$message="Verification Pin Update Request OTP has been sent on Registered Mobile Number";
		                // session()->put('message',$message);
			            $request->session()->flash('message', $message);      
            			return response()->json(['status' => 19, 'message' => "Your Verification Pin Is Not Updated Between 30 Days. Please Update Verification Pin"]);
            			
                    }
                    
                } 
                
            } else{
                
                //echo "else call";  
                // User::where('mobile',$mobile)->increment('wrong_attempt',1);
                $tot_wr_attempt = $login_count->wrong_attempt+1;
                
                User::where('mobile', $mobile)->update(['wrong_attempt' => $tot_wr_attempt,'wrong_attempt_date' =>date("Y-m-d H:i:s")]);
                 
                
                $att_timestamp = $login_count->wrong_attempt_date;
                $att_datetime = explode(" ",$att_timestamp);
                $attempt_date = $att_datetime[0];
                
                if(date("Y-m-d")!=$attempt_date){
                    User::where('mobile', $mobile)->update(['wrong_attempt' => 1]);
                	 
                } 
                if(date("Y-m-d")==$attempt_date && $tot_wr_attempt>'2'){
                      
                        $token = str_random(60);
            			$digits = 6;
            			$otp = rand(pow(10, $digits-1), pow(10, $digits)-1);
            			session()->put('request_mob_number',$request->mobile);
            			session()->put('request_token',$token);
            			PasswordChange::where(['mobile'=>$request->mobile])->delete();
            			PasswordChange::create(['mobile'=>$request->mobile,'token'=>$token,'otp'=>$otp,'user_id'=>$login_count->id]);
            			 
        				$mobile=$request->mobile;
            			 
            			$message="You A2Z Account is locked for the day, after three unsuccessful attempts, Please Login with a OTP $otp  for update Password.";
            			$message = urlencode($message);
            			
            			$cr = CustomTraits::sendByA2ZPlusSms($mobile,$message);
            		
            			$message="Password Request OTP has been sent on Registered Mobile Number";
		                // session()->put('message',$message);
			            $request->session()->flash('message', $message);  
			    		return response()->json(['status' => 18, 'message' => "You A2Z Account is locked for the day, after three unsuccessful attempts, Please Login with OTP."]);
                        
            	}else{
            	    if($login_count->wrong_attempt < 3){
            	        // $left_attempts = 3 - $login_count->wrong_attempt;
            	        $my_attempts = $login_count->wrong_attempt+1;
                        return response()->json(['status' => 0, 'message' => "Invalid Credential, You have made ".$my_attempts." unsuccessful attempt(s). The maximum retry attempts allowed for this access mode are 3."]);
                    }
            	}  
                return response()->json(['status' => 0, 'message' => "Wrong Username Or Password!"]);
            } 
        }
        if ($request->isOtpVerified == 0)
            $credential = ['mobile' => $mobile, 'password' => $request->password, 'status_id' => 1, 'otp_number' => $request->otp];
        elseif ($request->isOtpVerified == 1)
            $credential = ['mobile' => $mobile, 'password' => $request->password, 'status_id' => 1];
         
        return $this->authenticate($request, $credential);
    }
    public function authenticate($request, $credential)
    {
  
        if (Auth::attempt($credential)) {
            switch (Auth::user()->role_id) {
                case 5:
                    // $intended_page = '/premium-wallet';
                    // $intended_page = '/my-wallet';
                    $intended_page = '/dashboard';
                    break;
                case 4:
                    $intended_page = '/payment-request-view';
                    break;
                case 3:
                    $intended_page = '/dashboard';
                    break;
                case 1:
                    $intended_page = '/dashboard';
                    break;
                case 19:
                    $intended_page = '/dashboard';
                    break;    
				case 7: 
					$intended_page = '/recharge-nework';
					break;	
                default:
                    $intended_page = '/home';
            }
            if (Auth::user()->otp_verify == 0)
                User::where('id', Auth::id())->update(['otp_verify' => 1]);
            //User::where('id',Auth::id())->increment('total_logins',1);
            $userDetails = User::selectRaw('id,total_logins,last_login_at,last_login_ip')->find(Auth::id());
            $userDetails->total_logins += 1;
            $userDetails->last_login_at = Carbon::now()->toDateTimeString();
            $userDetails->last_login_ip = $request->getClientIp();
            $userDetails->save();
            try { 
                $ip = $_SERVER['REMOTE_ADDR'];
                $location_data = (unserialize(@file_get_contents('http://www.geoplugin.net/php.gp?ip=' . $ip)));

                UserLoggedInDetail::create(['user_id' => Auth::id(), 'ip_address' => $ip, 'ip_address' => $ip, 'browser' => $_SERVER['HTTP_USER_AGENT'], 'latitude' => $location_data['geoplugin_latitude'], 'longitude' => $location_data['geoplugin_longitude'], 'country_name' => $location_data['geoplugin_countryName'], 'region_name' => $location_data['geoplugin_regionName'], 'city' => $location_data['geoplugin_city']]);
                //print_r($location_data);die;
            } catch (Exception $e) {
                //throw $e;
            } 
            
            $request->session()->put('url.intended', url('/') . $intended_page);
            return redirect()->intended($intended_page); //Origional */
        } else {

            return back()->with('alert-success', 'User Or Password Invalid!');
        }
    }
    public function logout(Request $request)
    {
        $this->guard()->logout();
		$request->session()->flush();
		$request->session()->regenerate();
		return redirect('/');
    }
    
    public function resendOTP(Request $request)
    {
        try {
            $login_count = User::select('id', 'mobile', 'status_id', 'password', 'otp_number', 'total_logins', 'otp_verify', 'opt')->where('mobile', $request->mobile)->firstOrFail();
        } catch (\Exception  $e) {
            return response()->json(['status' => 0, 'message' => 'Invalid Mobile Number']);
        }
        $message = "Dear Partner, Your Login OTP : " . $login_count->otp_number . ", Thanks";
        $message = urlencode($message);
        CustomTraits::sendSMS($login_count->mobile, $message, 1);
        return response()->json(['status' => 1, 'message' => "OTP has been sent on your mobile number!"]);
    }
    
    public function systemVerification(Request $request)
    {
        try {
			
            $login_count = User::select('id', 'mobile', 'another_sys_otp')->where('mobile', $request->mobile)->firstOrFail();
        } catch (\Exception  $e) {
			throw $e;
            return response()->json(['status' => 0, 'message' => 'Invalid Mobile Number']);
        }
        if ($login_count->another_sys_otp == $request->systemVerificationOtp) {
            \Cookie::queue("isNewSystemFound_".$request->mobile, "SYS_COKKES_VALUE", 60 * 24 * 5);
            return response()->json(['status' => 1, 'message' => "System Verifed"]);
        }
        return response()->json(['status' => 0, 'message' => "Invalid OTP"]);
    }
}
