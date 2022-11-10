<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Auth;
use Illuminate\Http\Request;
use App\User;
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
    public function login(Request $request)
    {
        $mobile = $request->mobile;

        $loginThrough = $request->loginThrough;
        /* if($loginThrough == "OTP")
            $credential=['mobile'=>$mobile,'otp_number'=>$request->otp,'status_id'=>1];
        else */

        if ($request->caseType == "FIRST") {

            try {
                $login_count = User::select('id', 'mobile', 'status_id', 'password', 'otp_number', 'total_logins', 'otp_verify', 'opt', 'another_sys_otp', 'profile_id')->where('mobile', $mobile)->firstOrFail();
            } catch (\Exception  $e) {
                return response()->json(['status' => 0, 'message' => 'your credentials wrong!']);
            }
            if ($login_count->status_id == 0)
                return response()->json(['status' => 0, 'message' => 'Your are not activated yet. Please contact with Administrator']);
            if (Hash::check($request->password, $login_count->password)) {
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
                    CustomTraits::sendSMS($mobile, $message, 1);
                    $login_count->another_sys_otp = $otp;
                    $login_count->save();
                    return response()->json(['status' => 5, 'message' => "New System Found, Verify your system with OTP"]);
                }
                return response()->json(['status' => 6, 'message' => "Logged In"]);
            } else
                return response()->json(['status' => 0, 'message' => "Wrong Username Or Password!"]);
        }
        if ($request->isOtpVerified == 0)
            $credential = ['mobile' => $mobile, 'password' => $request->password, 'status_id' => 1, 'otp_number' => $request->otp];
        elseif ($request->isOtpVerified == 1)
            $credential = ['mobile' => $mobile, 'password' => $request->password, 'status_id' => 1];
        /* print_r($credential);
		 print_r($request->all());die; */

        return $this->authenticate($request, $credential);
    }
    public function authenticate($request, $credential)
    {

        if (Auth::attempt($credential)) {
            switch (Auth::user()->role_id) {
                case 5:
                    $intended_page = '/premium-wallet';
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
                $location_data = (unserialize(file_get_contents('http://www.geoplugin.net/php.gp?ip=' . $ip)));

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
