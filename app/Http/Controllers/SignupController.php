<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Auth;
use Validator;
use Response;

use App\User;
use App\UserPassword;
use Intervention\Image\ImageManagerStatic as Image;
use App\Member;
use App\Profile;
use App\Balance;
use App\State;
use Hash;
use DB;
use Exception;
use App\Traits\CustomTraits;
use App\Http\Controllers\Controller;
use App\Traits\UserTraits;
class SignupController extends Controller
{
    use CustomTraits, UserTraits;

    public function index()
    {
        $state = new State();
        $state_list = $state->stateList();
        return view('signup.signup',compact('state_list'));
    }

    public function store(Request $request)
    {
        //print_r($request->all());die();
         $validator = Validator::make($request->all(), [

            'name' => 'required|string',
            'email' => 'required|email|unique:users',
            'mobile' => 'required|numeric|digits:10|unique:users',
            'pan_number' => 'required|unique:members',
            'adhar_number' => 'required|numeric|digits:12|unique:members',
            'role_id'      => 'required', 
            'pan_card_image'      => 'required', 
            'aadhaar_card_image'      => 'required', 
            //'aadhaar_img_back'      => 'required', 
			 'declaration'      => 'required',
        ]);
         if ($validator->fails()) {
            return redirect('newsignup')->withErrors($validator)->withInput();
        }
        else
        {
			$amount=0;
            $role_id = $request->role_id;
            if($role_id== 5){
                $prefix = "REM";
				$amount = 500;
			}

            elseif($role_id== 4)
			{
                $prefix = "DLM";
				$amount = 1000;
			}

            elseif($role_id== 3)
                $prefix = "MD";

            else{
                $role_id= 5; 
                $prefix = "REM";
            }
            $now = new \DateTime();
           
            $password=$this->randomPassword();
            //dd($password);die();
            $profile_result = array();
            $ctime = $now->format('Y-m-d H:i:s');
           
            DB::beginTransaction();
                try
                {   
                    
                    $my_password = bcrypt($password);
                    $token_api = str_random(60);
                    $user_id = User::insertGetId([
                           'name'       =>$request->input('name'),
                           'email'      =>$request->input('email'),
                           'mobile'     =>$request->input('mobile'),
                           'role_id'    =>$role_id,
                           'password'   =>$my_password,
                           'prefix'     =>$prefix,
                           'parent_id'  => 1,
                           'company_id' => 1,
                           'status_id'  => 0,
                           'scheme_id'  => 1,
                           'api_token'  => $token_api,
                           'ip_address' => \Request::ip(),
                           'created_by' => 1,
                           'created_at' => $ctime,
                        ]);
                    
                    /* Code for insert data in registation in user_password */
                    
                    $login_data_count = UserPassword::where('mobile_no', $request->input('mobile'))->count(); 
                    if($login_data_count<1){
                        UserPassword::create(['mobile_no'=>$request->input('mobile'),'password'=>$my_password]);
                    }
                    
                    $profile_result = array('user_id'=>$user_id);
                    if($request->file('profile_picture')) 
                    {
                        $result = $this->fileSave($request,"profile_picture","profile-picture",$user_id);
                        $profile_result = array_merge($profile_result,$result);
                    }
                    if($request->file('shop_image')) 
                    {
                        $result = $this->fileSave($request,"shop_image","shop-image",$user_id);
                        $profile_result = array_merge($profile_result,$result);
                    }
                    if($request->file('pan_card_image')) 
                    {
                        $result = $this->fileSave($request,"pan_card_image","pan-card-image",$user_id);
                        $profile_result = array_merge($profile_result,$result);
                    }
                    if($request->file('aadhaar_card_image')) 
                    {
                        $result = $this->fileSave($request,"aadhaar_card_image","aadhaar-card-image",$user_id);
                        $profile_result = array_merge($profile_result,$result);
                    }
                    if($request->file('aadhaar_img_back')) 
                    {
                        $result = $this->fileSave($request,"aadhaar_img_back","aadhaar-card-image-back",$user_id);
                        $profile_result = array_merge($profile_result,$result);
                    }
                    
                    $profile_result['region']=$request->region;
                    $balance = Balance::insertGetId(['user_id' => $user_id, 'user_balance' => 0, 'user_commission' => 0]);
                    $profile = Profile::insertGetId($profile_result);
                    $member = Member::insertGetId([
                                'user_id' => $user_id, 
                                'state_id' => $request->state_id, 
                                'pin_code' => $request->pin_code, 
                                'address' => $request->address, 
                                'office_address' => $request->office_address, 
                                'pan_number' => $request->pan_number, 
                                'adhar_number' => $request->adhar_number, 
                                'company' => $request->company, 
								'declaration' => $request->declaration,
                                'created_at'=>$ctime,
                                'amount' => 0, 
                                'dmt_one' => 1,
                                'dmt_two' => 0,
                                ]);
                  
                    $usern = User::find($user_id);
                    $usern->balance_id = $balance;
                    $usern->profile_id = $profile;
                    $usern->member_id = $member;
                    $usern->save();

                    $user = User::find($user_id);
                    $data['name'] = $request->input('name');
                    $data['mobile'] = $user->mobile;
                    $data['id'] = $user_id;
                    $data['password'] = $password;
                    $message = "Dear Admin, ". $request->input('name') ." is  registered as " . $usern->role->role_title . " By Signup,Thanks!";
                    $message = urlencode($message);
                    CustomTraits::sendSms(config('constants.ADMIN_MOBILE_NO'),$message,1);
                    $message = "Welcome to a2zsuvidhaa.com, We are glab to became a part of A2ZSUVIDHAA Member. Thanks A2ZSUVIDHAA.";
                    $message = urlencode($message);
                    CustomTraits::sendSms($user->mobile,$message,1); 
					if($amount>0)
						$this->debitUserRegistrationFee($user_id,$amount,1);
                    DB::commit();

                    session()->flash('message', "Your Details Submitted Successfully To The Company!!");
                     return redirect('/');
                
                    //return Redirect::back();
                    //return response()->json(['status' => 'success', 'message' => "Company Details Submitted Successfully "]);
                }
               
                catch(Exception $e)
                {
                    DB::rollback();
                    return response()->json(['status' => 'failure', 'message' => 'Oops! Something went wrong '.$e->getMessage()]);
                }
                $request->session()->flash('success','Company Details Submitted Successfully !!');
          
        }   //return back()->with('success','Company Details Updated Successfully !!');
    }

    private function fileSave($request,$image_name,$file_name,$user_id)
    {
        if (\Input::hasFile("$image_name")) {
           
            $file = \Input::file("$image_name");
            $filename = $user_id.'-'.$file_name.'-'.time(). '.' . $file->getClientOriginalExtension();
            
            $img_size = $file->getSize();
            $img = Image::make($file);
            
            if($img_size>1000000){   
                 $img->resize(340, 240)->save('deposit_slip/images/'.$filename);
            }else{
                 $upload_img = $file->move('deposit_slip/images', $filename);
            }  
            return (array($image_name=>$filename));
        }
    }

    private function randomPassword() 
    {
        $alphabet = "abcdefghijkmnopqrstuwxyzABCDEFGHJKLMNOPQRSTUWXYZ0123456789";
        $pass = array(); //remember to declare $pass as an array
        $alphaLength = strlen($alphabet) - 1; //put the length -1 in cache
        for ($i = 0; $i < 8; $i++) 
        {
            $n = rand(0, $alphaLength);
            $pass[] = $alphabet[$n];
        }
        return implode($pass); //turn the array into a string
    }
   
}