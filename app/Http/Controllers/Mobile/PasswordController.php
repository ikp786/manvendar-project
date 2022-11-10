<?php

namespace App\Http\Controllers\Mobile;

use App\Http\Controllers\Controller;
use App\Profile;
use App\Traits\CustomAuthTraits;
use Exception;
use Illuminate\Foundation\Auth\ResetsPasswords;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\User;
use App\PasswordChange ;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Validator;
use Response;
use DB;
use Session;
use Illuminate\Support\Facades\Redirect;
use App\Traits\CustomTraits;
class PasswordController extends Controller
{
	use CustomTraits,CustomAuthTraits;

    public function change_password(Request $request)
    {
        $rules = array(
            'token' => 'required',
            'userId' => 'required',
            'old_password' => 'required',
            'password' => 'required',
        );
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return Response::json(array(
                'success' => false,
                'errors' => $validator->getMessageBag()->toArray()
            ));
        }

        $authentication = $this->checkLoginAuthentication($request);
        if ($authentication['status'] == 1) {
            $userDetail = $authentication['userDetails'];
            $old_password = $request->old_password;
            $new_password = $request->password;
            $userdetail = User::find($userDetail->id);

            $current_password = $userdetail->password;

            if (Hash::check($old_password, $current_password))
            {


                $userdetail->password = Hash::make($new_password);
                $profiles= Profile::find($userdetail->profile->id);
                $profiles->pswd_res = 1;
                DB::beginTransaction();
                try{
                    $profiles->save();
                    $userdetail->save();
                    DB::commit();
                }
                catch(Exception $e)
                {
                    DB::rollback();
                    $message = "Something went Wrong";
                    return back()->with('statusfail', $message);
                }
                $message = "Password Successfully Changed, Please use New Password for Next Login";
                return response()->json([
                    'status'=>'1',
                    'message'=>$message,
                ]);
            } else {

                $message = "Password Not Changed, Try with correct Deatil, Thanks";
                return response()->json([
                    'status'=>'2',
                    'message'=>$message,
                ]);
            }






        }
        return $authentication;
    }
    public function generateOtpForPin(Request $request)
    {
        $rules = array(
            'token' => 'required',
            'userId' => 'required',
        );
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return Response::json(array(
                'success' => false,
                'errors' => $validator->getMessageBag()->toArray()
            ));
        }
        $authentication = $this->checkLoginAuthentication($request);
        if ($authentication['status'] == 1) {
            $userDetail = $authentication['userDetails'];
            $digits = 4;
            $otp = rand(pow(10, $digits - 1), pow(10, $digits) - 1);
            Profile::where("user_id", $request->userId)->update(['txn_otp' => $otp]);
            $msg = "$otp for Generate Transaction Pin";
            $message = urlencode($msg);
            CustomTraits::sendSMS($userDetail->mobile, $message, 1);
            return response()->json(['status' => 1, 'message' => "OTP has been sent registered mobile number"]);
        } else return $authentication;


    }
    public function generateTransactionpin(Request $request)
    {

        $rules = array(
            'token' => 'required',
            'userId' => 'required',
            'txn_pin' => 'required',
            'confirm_txn_pin' => 'required',
            'otp' => 'required',
        );
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return Response::json(array(
                'success' => false,
                'errors' => $validator->getMessageBag()->toArray()
            ));
        }
        $authentication = $this->checkLoginAuthentication($request);
        if ($authentication['status'] == 1) {
            $userDetail = $authentication['userDetails'];

            if ($userDetail->role_id == 5) {
                $transactionpin = $request->txn_pin;
                $confirmation_transactionpin = $request->confirm_txn_pin;
                $transactionpin = $confirmation_transactionpin;
                $userPinRecord = Profile::where('user_id', $userDetail->id)->first();
                if ($request->otp != $userPinRecord->txn_otp) {
                    return response()->json(array(
                        'status' => '2',
                        'message' => 'Wrong Otp',
                    ));
                }
                if ($transactionpin == $confirmation_transactionpin) {

                    $userPinRecord->txn_pin = $transactionpin;
                    $userPinRecord->txn_otp = '';
                    $userPinRecord->save();
                    return response()->json(array(
                        'status' => '1',
                        'message' => 'Pin created successfully',
                    ));
                } else {
                    return response()->json(array(
                        'status' => '2',
                        'message' => 'Pin did not match',
                    ));
                }
            }
            return response()->json(array(
                'status' => '2',
                'message' => 'Access denied!',
            ));

        } else return $authentication;
    }
	
	
	
	//forget password
	 //forget password
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
            $message="Priya Partner, OTP  $otp for update Password";
            $message = urlencode($message);
            CustomTraits::sendSMS($mobile,$message,1);
            $message="Password Request OTP has been sent on Registered Mobile Number";
            return response()->json([
                'status'=>1,
                'message'=>$message,
                'token'=>$token,
            ]);
        }

        return response()->json([
            'status'=>2,
            'message'=>"Mobile Number Does not exist",
        ]);


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
            return Response::json(array(
                'status' => 10,
                'message' => $validator->getMessageBag()->toArray()
            ));
        }

        $exist_record = PasswordChange::where(['otp'=>$request->otp])->select('user_id','id','token')->first();
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

                $exist_user->save();
                $password_change->delete();
                $message="Passowrd has been updated successfully";
                DB::commit();
                return response()->json([
                    'status'=>1,
                    'message'=>'Password has been updated successfully',
                ]);
            }
            catch(Exception $e)
            {
                DB::rollback();

                return response()->json([
                    'status'=>2,
                    'message'=>'Oops Something went wrong please try again',
                ]);
            }
        }


        return response()->json([
            'status'=>2,
            'message'=>'Please provide correct credentials',
        ]);

    }
}
