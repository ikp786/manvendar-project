<?php

namespace App\Http\Controllers\Auth;

use App\User;
use Validator;
use Auth;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\ThrottlesLogins;
use Illuminate\Foundation\Auth\AuthenticatesAndRegistersUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use App\Traits\CustomTraits;
use Redirect;
use Hash;


class AuthController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Registration & Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users, as well as the
    | authentication of existing users. By default, this controller uses
    | a simple trait to add these behaviors. Why don't you explore it?
    |
    */

    use AuthenticatesAndRegistersUsers, ThrottlesLogins,CustomTraits;

    /**
     * Where to redirect users after login / registration.
     *
     * @var string
     */
    protected $redirectTo = '/home';

    /**
     * Create a new authentication controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest', ['except' => 'logout']);
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
	public function login(Request $request)
    {
     
        $mobile = Input::get('mobile');
		
		$loginThrough = $request->loginThrough;
		/* if($loginThrough == "OTP")
			$credential=['mobile'=>$mobile,'otp_number'=>$request->otp,'status_id'=>1];
		else */
		if($request->caseType=="FIRST")
		{
			
			try{
			$login_count = User::select('id','mobile','status_id','password','otp_number','total_logins','otp_verify')->where('mobile',$mobile)->firstOrFail();
			
			}
			catch(\Exception  $e)
			{
				throw $e;
				return response()->json(['status'=>0 ,'message' => 'your credentials wrong!']) ;
			}
			if($login_count->status_id == 0)
					return response()->json(['status'=>0 ,'message' => 'Your are not activated yet. Please contact with Administrator']) ;
			if(Hash::check($request->password, $login_count->password))
			{
				$credential=['mobile'=>$mobile,'password'=>$request->password,'status_id'=>1];
				if($login_count->total_logins < 1)
				{
					return response()->json(['status' => 4, 'message' =>"Use Today OTP"]);
					$digits = 4;
					$otp = rand(pow(10, $digits-1), pow(10, $digits)-1);

					$message = "Dear Partner, Your Login OTP : $otp, Thanks";
					$message = urlencode($message);
					CustomTraits::sendSMS($mobile, $message,1);
					$login_count->otp_number = $otp;
					$login_count->total_logins = 1;
					$login_count->save();
					return response()->json(['status' => 1, 'message' =>"OTP has been sent on your mobile number!"]);
				}
				else{
					return response()->json(['status' => 4, 'message' =>"Use Today OTP"]);
					if($login_count->otp_verify == 0)
					{
						return response()->json(['status' => 3, 'message' =>"Enter Today OTP"]);
					}
					return response()->json(['status' => 4, 'message' =>"Use Today OTP"]);
				}
			}
			else
				return response()->json(['status' => 0, 'message' =>"Wrong Username Or Password!"]);
		}
		if($request->isOtpVerified == 0)
			$credential=['mobile'=>$mobile,'password'=>$request->password,'status_id'=>1,'otp_number'=>$request->otp];
		elseif($request->isOtpVerified == 1)
			$credential=['mobile'=>$mobile,'password'=>$request->password,'status_id'=>1];
			//print_r($request->all());
			//print_r($request->all());
			//print_r($credential);die;
         return $this->authenticate($request,$credential);
      
    }
	public function authenticate($request,$credential)
    {
        
        if (Auth::attempt($credential)) 
		{	
			switch(Auth::user()->role_id)
			{
				case 5: $intended_page = '/premium-wallet';
				break;
				case 4: $intended_page = '/payment-request-view';
				break;
				case 3: $intended_page = '/dashboard';
				break;
				case 1: $intended_page = '/dashboard';
				break;
				case 7: $intended_page = '/recharge-nework';
				break;
				default:$intended_page = '/home';
			}
			if(Auth::user()->otp_verify ==0)
				User::where('id',Auth::id())->update(['otp_verify'=>1]);
			User::where('id',Auth::id())->increment('total_logins',1);
			$request->session()->put('url.intended',url('/').$intended_page);
			return redirect()->intended($intended_page);//Origional */
        } 
		else 
		{
            
            return back()->with('alert-success', 'User Or Password Invalid!');
        }
        
    }
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name' => 'required|max:255',
            'email' => 'required|email|max:255|unique:users',
            'password' => 'required|min:6|confirmed',
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return User
     */
   /*  protected function create(array $data)
    {
        return User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => bcrypt($data['password']),
        ]);
    } */
}
