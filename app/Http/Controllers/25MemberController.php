<?php

namespace App\Http\Controllers;

use App\Balance;
use App\Company;
use App\Masterbank;
use App\Yesmasterbank;
use App\Moneyscheme;
use App\WalletScheme;
use Excel;
use App\Upscheme;
use App\Member;
use Illuminate\Http\Request;
use App\User;
use App\Scheme;
use App\Role;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use App\Http\Requests;
use Validator;
use Response;
use Mail;
use Hash;
use App\Circle;
use App\Report;
use App\Profile;
use App\Txnslab;
use App\Provider;
use App\Txnfirstslab;
use App\Txnsecondslab;
use App\Txnthiredslab;
use App\Remitteregister;
use App\ActionOtpVerification;
use App\Remark;
use App\State;
use Illuminate\Support\Facades\Input;
use App\Mdpertxn;
use App\Mdfirstpertxn;
use App\Mdsecondpertxn;
use App\Mdthiredpertxn;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Exception;
use App\Gstcommission;
use App\Traits\CustomTraits;
use App\Traits\UserTraits;
use App\Exports\MemberExport;
use Symfony\Component\HttpFoundation\Session\Session;


class MemberController extends Controller
{
	use CustomTraits, UserTraits;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
	 
    public function index()
    {
			 if (Auth::user()->role_id == 1) {
            $users = User::where('role_id','!=',1)->orderBy('id', 'DESC')->simplePaginate();
            $export_users = User::where('role_id', '!=',1)->where('role_id','!=',3)->pluck('name', 'id')->toArray();
			
          $state = new State();
  			  $state_list = $state->stateList();
          $parent_id = User::where('role_id', '!=',1)
              ->orderBy('id', 'DESC')->get();
          //return $users;
          $upfront = Upscheme::pluck('scheme', 'id');
          $roles = Role::whereIn('id',[3,4,5,7,10,15])->pluck('role_title', 'id');
          $schemes = Scheme::pluck('scheme_name', 'id');
  		   $moneySchemes = Moneyscheme::select('id','scheme_name','scheme_for')->get();
		$walletSchemes = WalletScheme::whereIn('scheme_for',[3,1,6,5,7])->get();
		$otpVerifications=ActionOtpVerification::orderBy('id','ASC')->get();
        return view('admin.member', compact('users', 'roles', 'upfront', 'parent_id','export_users','state_list','moneySchemes','walletSchemes','otpVerifications'))->with('schemes', $schemes);
		    }
    }

    public function retailer()
    {
		if(in_array(Auth::user()->role_id,array(1,2,3,4,12,14,11)))
		{
			$state = new State();
			$state_list = $state->stateList();
			$export_users=array();
			if (in_array(Auth::user()->role_id,array(1,12,14,11))) 
			{
				$users = User::where('role_id', 5)->orderBy('id', 'DESC')->simplePaginate();
				$export_users = User::where('role_id', 5)->pluck('name', 'id')->toArray();
				$parent_id = User::where('role_id', 4)->orderBy('id', 'DESC')->get();
			} 
			elseif (Auth::user()->role_id == 3) 
			{
				$members = User::where('parent_id', Auth::id())->get();
				$member_id = array();
				foreach ($members as $member) {
					$member_id[] = $member->id;
				}
				$memberid = array_merge(array(Auth::id()), $member_id);
				$users = User::where('role_id', 5)
					->whereIn('parent_id', $memberid)
					->orderBy('id', 'DESC')->simplePaginate();
				$parent_id = User::where(['role_id'=>4,'parent_id'=>Auth::id()])->where('company_id', Auth::user()->company_id)->orderBy('id', 'DESC')->get();
			} 
			else 
			{
				$users = User::where('parent_id', Auth::id())
					->where('role_id', 5)->orderBy('id', 'DESC')->simplePaginate();
				$parent_id = User::where(['role_id'=>4,'parent_id'=>Auth::id()])->orderBy('id', 'DESC')->get();
			}
			$roles = Role::where('id', 5)->pluck('role_title', 'id');
			
			
			$schemes = Scheme::pluck('scheme_name', 'id')->toArray();
		$moneySchemes = Moneyscheme::select('id','scheme_name','scheme_for')->get();
		$walletSchemes = WalletScheme::whereIn('scheme_for',[3,1,6,5,7])->get();
		$otpVerifications=ActionOtpVerification::orderBy('id','ASC')->get();
			return view('admin.member', compact('users', 'roles', 'parent_id','state_list','export_users','moneySchemes','walletSchemes','otpVerifications'))->with('schemes', $schemes);
		}
		return view('errors.page-not-found');
	}
	
	public function gstInfo()
	{
		if (Auth::user()->role_id == 4) 
		{
		 return view('admin.gstinfo');
		}
		return view('errors.page-not-found');
	}
	public function api_retailer()
    {
		 if(Auth::user()->role_id==1)
         {  
            $users = User::where('role_id', 7)
            ->orderBy('id', 'DESC')->paginate(40);
            $parent_id = User::where('role_id', 1)
            ->orderBy('id', 'DESC')->get();
        
		$export_users = User::where('role_id', 7)->pluck('name', 'id')->toArray();
        //return $users;
        $upfront = Upscheme::pluck('scheme', 'id');
        $roles = Role::where('id', 7)->pluck('role_title', 'id');
       
        $state = new State();
        $state_list = $state->stateList();
		$schemes = Scheme::pluck('scheme_name', 'id')->toArray();
		$walletSchemes = WalletScheme::whereIn('scheme_for',[3,1,6,5,7])->get();
		$moneySchemes = Moneyscheme::select('id','scheme_name','scheme_for')->get();
		$otpVerifications=ActionOtpVerification::orderBy('id','ASC')->get();
        return view('admin.member', compact('users', 'roles', 'upfront', 'parent_id','state_list','export_users','moneySchemes','walletSchemes','otpVerifications'))->with('schemes', $schemes);

		}
	}
	public function networkChain()
	{
		if(in_array(Auth::user()->role_id,array(1,3,4,)))
		{
			if (Auth::user()->role_id == 4)
				$users = User::selectRaw('id,name,mobile,email,status_id,role_id')->where(['parent_id'=>Auth::id(),'role_id'=>5])->orderBy('id','desc')->simplePaginate(30);
			elseif(Auth::user()->role_id == 3)
				$users = User::selectRaw('id,name,mobile,email,status_id,role_id')->where(['parent_id'=>Auth::id(),'role_id'=>4])->orderBy('id','desc')->simplePaginate(30);
			elseif(Auth::user()->role_id == 1)
				$users = User::selectRaw('id,name,mobile,email,status_id,role_id')->where(['parent_id'=>Auth::id(),'role_id'=>3])->orderBy('id','desc')->simplePaginate(30);
			return view('admin.network.network-chain',compact('users'));
		}
		else
			return view('errors.permission-denied');
	}
	public function networkViewChain($parentId)
	{
		if(in_array(Auth::user()->role_id,array(1,3)))
		{
			if(Auth::user()->role_id == 3)
				$users = User::selectRaw('id,name,mobile,email,status_id,role_id')->where(['parent_id'=>$parentId,'role_id'=>5])->orderBy('id','desc')->simplePaginate(30);
			elseif(Auth::user()->role_id == 1)
				$users = User::selectRaw('id,name,mobile,email,status_id,role_id')->where(['parent_id'=>Auth::id(),'role_id'=>3])->orderBy('id','desc')->simplePaginate(30);
			return view('admin.network.network-view-chain',compact('users'));
		}
		return view('errors.permission-denied');
	}
	public function networkAdminViewChain($parentId)
	{
		if(in_array(Auth::user()->role_id,array(1)))
		{
			if(Auth::user()->role_id == 1)
				$users = User::selectRaw('id,name,mobile,email,status_id,role_id')->where(['parent_id'=>$parentId,'role_id'=>4])->orderBy('id','desc')->simplePaginate(30);
			return view('admin.network.network-admin-dist-view-chain',compact('users'));
		}
		return view('errors.permission-denied');
	}
	public function networkSearch(Request $request)
	{
		if(in_array(Auth::user()->role_id,array(1,3,4)))
		{
			$users=array();
			if($request->searchOf !='')
			{
				if (Auth::user()->role_id == 4)
					$users = User::selectRaw('id,name,mobile,email,status_id,role_id')->where(['parent_id'=>Auth::id(),'role_id'=>5,'mobile'=>$request->mobile])->orderBy('id','desc')->simplePaginate(30);
				elseif(Auth::user()->role_id == 3)
				{
					$users = User::selectRaw('id,name,mobile,email,status_id,role_id')->where(['parent_id'=>Auth::id(),'role_id'=>$request->searchOf,'mobile'=>$request->mobile])->orderBy('id','desc')->simplePaginate(30);
				}
				elseif(Auth::user()->role_id == 1)
				{
					$users = User::selectRaw('id,name,mobile,email,status_id,role_id')->where(['parent_id'=>Auth::id(),'role_id'=>$request->searchOf,'mobile'=>$request->mobile])->orderBy('id','desc')->simplePaginate(30);
				}
			}
			return view('admin.network.network-search',compact('users'));
		}
		return view('errors.permission-denied');
	}
 public function allMember()
    {
		if(in_array(Auth::user()->role_id,array(1,12,14,11)))
		{
			$export_users=User::whereNotIn('role_id',[1,2,11,12,13,14])->orderBy('name','asc')->pluck('name','id')->toArray();
			//$export_users["0"]="All Member";
			
			asort($export_users);
			//print_r($export_users);die;
			$state = new State();
			$state_list = $state->stateList();
			$users = User::orderBy('id','DESC')->get();
			$parent_id =array();
			$upfront = Upscheme::lists('scheme', 'id');
			$roles = Role::whereIN('id', [3,2,4,5,10,11,12,13,14])->lists('role_title', 'id');
			if (!empty($_SERVER['HTTP_HOST'])) {
				$host = $_SERVER['HTTP_HOST'];
			} else {
				$host = "localhost:8888";
			}
			$company = Company::where('company_website', $host)->where('status', 1)->first();
			if ($company) {
				$company_id = $company->id;
			} else {
				$company_id = 0;
			}
			$schemes = Scheme::select('id', 'scheme_name')->where('company_id', '=', $company_id)->lists('scheme_name', 'id')->toArray();
			$otpVerifications=ActionOtpVerification::orderBy('id','ASC')->get();
			return view('admin.member', compact('users', 'roles', 'upfront', 'parent_id','state_list','export_users','otpVerifications'))->with('schemes', $schemes);
		}
		return view('errors.page-not-found');

    }
   

    public function member_otp(Request $request)
    {
        if (Auth::user()->role_id == 1) 
		{
            $users_otp = User::where('total_logins','!=', 0)
                ->orderBy('id', 'DESC')->get();
        	$otpVerifications=ActionOtpVerification::orderBy('id','ASC')->get();
			return view('admin.member_otp', compact('users_otp','otpVerifications'));
         }
         else
         {
            return view('errors.page-not-found');
         }

    }

    public function bnk_updown(Request $request)
    {
		if (in_array(Auth::user()->role_id,array(1,12))){
			$bank_up_down = Masterbank::selectRaw('id,bank_name,bank_code,bank_status,is_imps_txn_allow,manual_status,down_time,bank_sort_name')->where('id','!=',1)->orderBy('bank_status','asc')->orderBy('manual_status','asc')->get();
			return view('admin.bankupdown', compact('bank_up_down'));
		}
		else {
			return view('errors.page-not-found');
		}
		

    }
public function makeBankUpDown(Request $request)
    {
		$updateStatus = ($request->currentBankStatus) ? 0 : 1;
        Masterbank::where('id',$request->id)
        ->update(array($request->fieldName=>$updateStatus,'down_time'=>date('Y-m-d H:i:s')));
		$message = ($updateStatus) ? "Bank Up" : "Bank down";
		$message = $request->fieldName .' ' .$message;
		return response()->json(['status'=>1,'message'=>$message]);

    }
	public function accountSetting(Request $request)
    {
      if(Auth::user()->role_id == 4)
      {
     
        return view('profile.accountsetting');
      }
      else{
         return "No Permission to access this page";
      }

    }
    public function s_bnk_update(Request $request)
    {
        if($request->id!='')
        {

        $update =  Yesmasterbank::where('id',$request->id)
        ->update(array('bank_status'=>$request->bank_status,'saral' => $request->saral,'smart'=>$request->smart,'sharp'=>$request->sharp,'secure'=>$request->secure));

        if( $update ){  return "record Succesfully Updated!"; } else {  return "Somthing Wrong to Update!"; }
        }
        else { return "Select Bank"; }
    }
    public function bnk_comnt_update(Request $request)
    {
        if($request->s_comment !='')
        {
        DB::table('masterbanks')
         ->where('bank_status',0)
         ->update(array('saral_comment'=>$request->s_comment));
        }
        elseif($request->sm_comment !='')
        {
            DB::table('masterbanks')
            ->where('bank_status',0)
            ->update(array('smart_comment'=>$request->sm_comment));
        }
        elseif($request->sc_comment !='')
        {
            DB::table('yesmasterbanks')
            ->where('bank_status',0)
            ->update(array('secure_comment'=>$request->sc_comment));
        }
        else
        {
            DB::table('masterbanks')
            ->where('bank_status',0)
            ->update(array('sharp_comment'=>$request->sh_comment));

        }
        return "Update comment Succesfully";
    }

    public function provider_update(Request $request)
    {
        $id = $request->id;
        $p_code = $request->p_code;
        Provider::where('id',$id)
       ->update(array('api_id' => $p_code));
        return "Succesfully Updated";
    }

    public function inprocess_update(Request $request)
    {
        if(Auth::user()->role_id==1 && Auth::id()==110)
		{
			$id = $request->id;
			$inpro_api = $request->inpro_api;
			Company::where('id',$id)
		   ->update(array('txn_hold' => $inpro_api));
			return "Succesfully Updated";
		}
		else
		{
			return "No Permission to access this page";
		}
    }

        public function flush_otp(Request $request)
        {
			if($request->ajax() && Auth::user()->role_id == 1 && $request->case=="otp_flush")
			{
				DB::table('users')
				->whereIn('role_id',[4,3,5])
				->update(array('total_logins' => 0,'otp_verify' => 0));
				return "record Succesfully Updated";
			}
			return "You do not have Permission";
        }
        
    public function changepassword()
    {
		if(in_array(Auth::user()->role_id,array(5,7)))
		{ 
			$user=new user();
			$state = new State();
			$state_list = $state->stateList();
			return view('profile.agent-index',compact('state_list','user'));
		}    
		elseif (in_array(Auth::user()->role_id,array(3,4))) 
		{
			$user=new user();
			$state = new State();
			$state_list = $state->stateList();
			return view('profile.index',compact('state_list','user'));
       }   
		else
        return view('profile.index');
    }

    public function guest()
    {
        if (Auth::user()->role_id == 1) {
            $users = User::where('role_id', 6)->orderBy('id', 'DESC')->get();
        } else {
            $users = User::where('parent_id', Auth::id())->where('role_id', 6)->orderBy('id', 'DESC')->get();
        }
        $parent_id = User::where('role_id', 5)->orderBy('id', 'DESC')->get();
        //return $users;
        $roles = Role::lists('role_title', 'id');
        $upfront = Upscheme::lists('scheme', 'id');
        if (!empty($_SERVER['HTTP_HOST'])) {
            $host = $_SERVER['HTTP_HOST'];
        } else {
            $host = "localhost:8888";
        }
        $company = Company::where('company_website', $host)
            ->where('status', 1)
            ->first();
        if ($company) {
            $company_id = $company->id;
        } else {
            $company_id = 0;
        }
        $schemes = Scheme::select('id', 'scheme_name')->where('company_id', '=', $company_id)->lists('scheme_name', 'id')->toArray();
		$otpVerifications=ActionOtpVerification::orderBy('id','ASC')->get();
        return view('admin.member', compact('users', 'roles', 'upfront', 'parent_id','otpVerifications'))->with('schemes', $schemes);
    }

    public function my_member()
    {
        $data = ['page_title' => 'My Member'];
        $mymember = User::where('parent_id', Auth::id())->get();
        return view('member.mymember', compact('mymember'))->with($data);
    }

    public function add_member()
    {
        $circle = Circle::all();
        return view('member.create', compact('circle'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('member.create');
    }

    public function save_member(Request $request)
    {

        
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $rules = array(
            'name' => 'required',
			'email' => 'email|unique:users',
			'mobile' => 'required|numeric|digits:10|unique:users',
            'company' => 'required',
            /* 'shop_image' => 'max:500| mimes:jpeg,jpg,png,pdf', */
            /* 'profile_picture' => 'max:500| mimes:jpeg,jpg,png,pdf', */
            /* 'pan_card_image' => 'required| mimes:jpeg,jpg,png,pdf', */
            'pan_number' => 'required',
            /* 'aadhaar_card_image' =>'max:500| mimes:jpeg,jpg,png,pdf', */
			/* 'aadhaar_img_back' => 'max:500| mimes:jpeg,jpg,png,pdf', */
            'adhar_number' => 'required',
			/* 'cheque_image' =>'max:500| mimes:jpeg,jpg,png,pdf', */
			/* 'form_image' =>'max:3072| mimes:jpeg,jpg,png,pdf', */
            'address' => 'required|min:5',
            'office_address' => 'required|min:5',
            'pin_code' => 'required|numeric|digits:6',
            'amount' => 'numeric',
			'blocked_amount'=>'numeric',
            
        );
		//print_r($request->all());die;
		if(Auth::user()->role_id == 4)
		{
			$parent_id = Auth::id();
			$role_id = 5;
		}
		elseif(Auth::user()->role_id == 5)
		{
			$parent_id = Auth::id();
			$role_id = 15;
		}
		else
		{
			$parent_id = $request->parent_id;
			$role_id = $request->role_id;
		}
		$amount=0;
		if($role_id== 5)
		{
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
		elseif($role_id== 7)
			$prefix = "API";
		if($role_id<3)
		{
			return response()->json(['status' => 'Failure', 'message' => "Sorry! please check role type"]);
		}
		$validator = Validator::make($request->all(), $rules);
		if ($validator->fails()) {
			return Response::json(array(
				'success' => false,
				'errors' => $validator->getMessageBag()->toArray()
			)); // 400 being the HTTP code for an invalid request.
		}
		else
		{
			 $now = new \DateTime();
			$datetime = $now->getTimestamp();
			$ctime = $now->format('Y-m-d H:i:s');
			$company_id = 1;
			$userid = Auth::id();
			$hashed_random_password = Hash::make(str_random(8));
			$password = substr($hashed_random_password, -6);
			$password="A2Z123@123";
			$profile_result = array();
			$dmtOne = ($request->super_res)? $request->super_res : 0;
			$dmtTwo = isset($request->super_p)? $request->super_p : 0;
			//$dmtThree = isset($request->dmt_three)? $request->dmt_three : 0;
			//$restricton_array=array('super_res'=>$super_res);
			DB::beginTransaction();
			try
			{
				$token_api = str_random(60);
				$token_key = str_random(30);
				$user_id = User::insertGetId([
							'name' => trim($request->input('name')),
							'email' => $request->input('email'),
							'password' => bcrypt($password),
							'mobile' => $request->input('mobile'),
							'kyc' => $request->input('kyc'),
							'blocked_amount' => $request->input('blocked_amount'),
							'scheme_id' =>1,
							'upscheme_id' => $request->input('upscheme') ? $request->input('upscheme') :0,
							'role_id' => $role_id,
							'created_at' => $ctime,
							'status_id' => 0,
							'company_id' => $company_id,
							'api_token' => $token_api,
							
							'ip_address' => \Request::ip(),
							'parent_id' => $parent_id,
							'created_by' => Auth::id(),
							'prefix'=>$prefix,
							
						]);	
				$profile_result = array('user_id'=>$user_id);
				if($request->file('profile_picture')) 
				{
					$result = $this->fileSave($request,"profile_picture","profile-picture",$user_id);
					$profile_result = array_merge($profile_result,$result);
				}if($request->file('shop_image')) 
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
					$result =$this->fileSave($request,"aadhaar_card_image","aadhaar-card-image",$user_id);
					$profile_result = array_merge($profile_result,$result);
				}
				if($request->file('aadhaar_img_back')) 
				{
					$result = $this->fileSave($request,"aadhaar_img_back","aadhaar-card-image-back",$user_id);
					$profile_result = array_merge($profile_result,$result);
				}
				if($request->file('cheque_image')) 
				{
					$result = $this->fileSave($request,"cheque_image","cheque-image",$user_id);
					$profile_result = array_merge($profile_result,$result);
				}
				if($request->file('form_image')) 
				{
					$result = $this->fileSave($request,"form_image","form-image",$user_id);
					$profile_result = array_merge($profile_result,$result);
				}
				$profile_result['region']=$request->region;
				$balance = Balance::insertGetId(['user_id' => $user_id, 'user_balance' => 0, 'user_commission' => 0]);
				$profile = Profile::insertGetId($profile_result);
				$scretKey = str_random(30).''.str_random(30);
				$member = Member::insertGetId([
								'user_id' => $user_id, 
								'state_id' => $request->state_id, 
								'pin_code' => $request->pin_code, 
								'address' => $request->address, 
								'office_address' => $request->office_address, 
								'pan_number' => $request->pan_number, 
								'adhar_number' => $request->adhar_number, 
								'company' => $request->company, 
								'created_at'=>$ctime,
								'amount' => 0, 
								'txn_details' => $request->txn_details,
								'dmt_one' => 1,
								'dmt_two' => 0,
								'dmt_three' => 0,
								'secret_key' => $user_id.$scretKey,
								]);
				//Member::where('id',$member)->update($restricton_array);
				/* Txnslab::insertGetId(['user_id' => $user_id, 'ptxn1' =>0,'ptxn2' =>0,'ptxn3' =>0,'ptxn4' =>0,'ptxn5' =>0,'status_id'=>1]);
                Txnfirstslab::insertGetId(['user_id' => $user_id, 'ptxn1' =>0,'ptxn2' =>0,'ptxn3' =>0,'ptxn4' =>0,'ptxn5' =>0,'status_id'=>1]);
                Txnsecondslab::insertGetId(['user_id' => $user_id, 'ptxn1' =>0,'ptxn2' =>0,'ptxn3' =>0,'ptxn4' =>0,'ptxn5' =>0,'status_id'=>1]);
               
                Mdpertxn::insertGetId(['user_id' => $user_id, 'ptxn1' =>0,'ptxn2' =>0,'ptxn3' =>0,'ptxn4' =>0,'ptxn5' =>0,'status_id'=>1]);
                Mdfirstpertxn::insertGetId(['user_id' => $user_id, 'ptxn1' =>0,'ptxn2' =>0,'ptxn3' =>0,'ptxn4' =>0,'ptxn5' =>0,'status_id'=>1]);
                Mdsecondpertxn::insertGetId(['user_id' => $user_id, 'ptxn1' =>0,'ptxn2' =>0,'ptxn3' =>0,'ptxn4' =>0,'ptxn5' =>0,'status_id'=>1]); */
				$usern = User::find($user_id);
				
				$usern->mobile_token = $token_key.''.$user_id;
				$usern->balance_id = $balance;
				$usern->profile_id = $profile;
				$usern->member_id = $member;
				$usern->save();
				$confirmation_code = str_random(30);
				$user = User::find($user_id);
				$data['name'] = $request->input('name');
				$data['mobile'] = $user->mobile;
				$data['id'] = $user_id;
				$data['password'] = $password;
				$message = "Dear Admin, ". $request->input('name') ." is  registered as " . $usern->role->role_title . " By " . Auth::user()->name . ",Thanks!";
				$message = urlencode($message);
				if($amount>0)
					$this->debitUserRegistrationFee($user_id,$amount,1);
				DB::commit();
				try{
					CustomTraits::sendSms(config('constants.ADMIN_MOBILE_NO'),$message,Auth::user()->company_id);
					$message = "Welcome to a2zsuvidhaa.com, We are glab to became a part of A2ZSUVIDHAA Member. Thanks A2ZSUVIDHAA.";
					$message = urlencode($message);
					CustomTraits::sendSms($user->mobile,$message,Auth::user()->company_id); 
				}
				catch(Exception $e)
				{
					
				}
				
				return response()->json(['status' => 'success', 'message' => "Member added Successfully "]);
			}
			catch(Exception $e)
			{
				DB::rollback();
				throw $e;
				return response()->json(['status' => 'failure', 'message' => 'Oops! Something went wrong '.$e->getMessage()]);
			}

		}
	}

    public function createmember(Request $request)
    {

    }
	/* public function generateSecretKey()
	{
		$users = User::selectRaw('id')->get();
		foreach( $users as $key)
		{
			$scretKey = str_random(30).''.str_random(30);
			Member::where('user_id',$key->id)->update(['secret_key'=>$key->id.$scretKey]);
		}
		echo "Updated";
	} */
    public function view(Request $request)
    {
         $id = $request->input('id');
        $provider = User::find($id);
        header('Access-Control-Allow-Origin: *');
        header('content-type: application/json; charset=utf-8');
        
		$doc_list_str=$provider->profile->doc_list;
		$doc_list =explode(",",$doc_list_str);
		return response()->json([
				/* 	'agentcode' => $provider->agentcode,
					'yagentcode' => $provider->yagentcode,  */
					'agreement' => $provider->agreement,
					'voucher_scheme_id' => $provider->voucher_scheme_id, 
					'offline_scheme_id' => $provider->offline_scheme_id, 
					'travel_scheme_id' => $provider->travel_scheme_id, 
					'billpayment_scheme_id' => $provider->billpayment_scheme_id, 	
					'company_id' => $provider->company_id, 
					'email' => $provider->email, 
					'id' => $provider->id, 
					'member_id' => $provider->member_id, 
					'mobile' => $provider->mobile, 
					'name' => $provider->name, 
					'parent_id' => $provider->parent_id, 
					'profile_id' => $provider->profile_id, 
					'role_id' => $provider->role_id, 
					'scheme_id' => $provider->scheme_id, 
					'upscheme' => $provider->upscheme_id, 
					'pan_number' => $provider->member->pan_number, 
                    'aeps_user_name' => $provider->member->aeps_user_name, 
                    'aeps_pwd' => $provider->member->aeps_pwd, 
					'adhar_number' => $provider->member->adhar_number, 
					'address' => $provider->member->address, 
					'office_address'=>$provider->member->office_address, 
					'state_id'=>$provider->member->state_id, 
					'status_id'=>$provider->status_id, 
					'kyc'=>$provider->kyc, 
					'company' => $provider->member->company, 
					'pin_code' => $provider->member->pin_code,
					'amount' => $provider->member->amount,
					'blocked_amount' => $provider->blocked_amount,
					'txn_details' => $provider->member->txn_details,
					'profile_picture'=> $provider->profile->profile_picture,
					'shop_image'=> $provider->profile->shop_image,
					'pan_card_image'=> $provider->profile->pan_card_image,
					'aadhaar_card_image'=> $provider->profile->aadhaar_card_image,
					'aadhaar_img_back'=> $provider->profile->aadhaar_img_back,
					'cheque_image'=> $provider->profile->cheque_image,
					'form_image'=> $provider->profile->form_image,
					'doc_verify'=> $provider->profile->doc_verify,
					'region'=> $provider->profile->region,
					'show_profile'=>isset($doc_list[0])?$doc_list[0] : 0, 
					'show_shop'=>isset($doc_list[1])?$doc_list[1] : 0,
					'show_pan'=>isset($doc_list[2])?$doc_list[2] : 0,
					'show_aadhaar'=>isset($doc_list[3])?$doc_list[3] : 0,
					'show_cheque'=>isset($doc_list[4])?$doc_list[4] : 0,
					'show_form'=>isset($doc_list[5])?$doc_list[5] : 0,
					'show_back_aadhaar'=>isset($doc_list[6])?$doc_list[6] : 0,
					'super_res'=>$provider->member->dmt_one,
					'super_p'=>$provider->member->dmt_two,
					'dmt_three'=>$provider->member->dmt_three,
					'aeps_service'=>$provider->member->aeps_service,
					'blocked_amount'=>$provider->member->blocked_amount,
					'aeps_blocked_amount'=>$provider->member->aeps_blocked_amount,
					'aeps_charge'=>$provider->member->aeps_charge,
					
					]);

    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
   public function update(Request $request, $id)
    {
		$member=User::find($id);
		$rules = array(
			'name' => 'required',
			'email' => 'unique:users,email,'.$id,
			'mobile' => 'required|digits:10|unique:users,mobile,'.$id,
            'company' => 'required',
            'state_id' => 'required',
            //'shop_image' => 'max:500| mimes:jpeg,jpg,png,pdf',
            //'profile_picture' => 'max:200| mimes:jpeg,jpg,png,pdf',
            //'pan_card_image' => 'max:500|mimes:jpeg,jpg,png,pdf',
            'pan_number' => 'required',
            //'aadhaar_card_image' =>'max:500|mimes:jpeg,jpg,png,pdf',
			// 'aadhaar_img_back' =>'max:500|mimes:jpeg,jpg,png,pdf',
            'adhar_number' => 'required',
			//'cheque_image' =>'max:500|mimes:jpeg,jpg,png,pdf',
			//'form_image' =>'max:3072|mimes:jpeg,jpg,png,pdf',
            'address' => 'required|min:5',
            'office_address' => 'required|min:5',
			//'blocked_amount'=>'numeric',
			'pin_code' => 'required|numeric|digits:6');
			
			//print_r($request->all());die;
			
		$validator = Validator::make($request->all(), $rules);
		if ($validator->fails()) {
			return Response::json(array(
				'success' => false,
				'errors' => $validator->getMessageBag()->toArray()
			)); // 400 being the HTTP code for an invalid request.
		} 
		
        if (in_array(Auth::user()->role_id,array(1))) 
		{
		
		//print_r($request->all());die;
		
		$dmtOne = ($request->super_res)? $request->super_res : 0;
		$dmtTwo = isset($request->super_p)? $request->super_p : 0;
		$dmtThree = isset($request->dmt_three)? $request->dmt_three : 0;
		$password = $this->randomPassword();
		$pre_status = $member->status_id;
		$prefix = $this->getPrefix($request->role_id);
			DB::beginTransaction();
            try
            {
				//$member = User::find($id);
				$member->name = trim($request->name);
				if($request->status_id == 1 && $pre_status == 0) 
				$member->password = bcrypt($password);
				$member->mobile = $request->mobile;
				$member->email = $request->email;
				if ($request->parent_id != '') {
					$member->parent_id = $request->parent_id;
				}
				$member->status_id = $request->status_id;
				$member->scheme_id = $request->scheme_id;				
				$member->upscheme_id = 0;//$request->upscheme;
				$member->role_id = $request->role_id;
				$member->kyc = $request->kyc;  
				$member->prefix = $prefix;     				
				$member->aeps_agent_id = $request->agentcode;
				$member->yagentcode = $request->yagentcode;
				$member->ip_address = \Request::ip();
				$member->save();
				$mm = Member::where('user_id', $id)->first();
				$mm->company = $request->company;
				$mm->state_id = $request->state_id;
				$mm->pan_number = $request->pan_number;
                $mm->aeps_pwd = $request->aeps_pwd;
                $mm->aeps_user_name = $request->aeps_user_name;
				$mm->adhar_number = $request->adhar_number;
				$mm->pin_code = $request->pin_code;
				$mm->address = $request->address;
				$mm->office_address = $request->office_address;
				$mm->amount = $request->amount;
				$mm->txn_details = $request->txn_details;
				$mm->dmt_one = $dmtOne;
				$mm->dmt_two = $dmtTwo;				
				$mm->dmt_three = $dmtThree;				
				$mm->blocked_amount = $request->blocked_amount;				
				$mm->aeps_charge = $request->aeps_charge;				
				$mm->aeps_blocked_amount = $request->aeps_blocked_amount;				
				$mm->aeps_service = isset($request->aeps_service)? $request->aeps_service : 0;				
				$mm->save();
			
				$profile_result = array('user_id'=>$id);
				if($request->file('profile_picture')) 
				{
						$result = $this->fileSave($request,"profile_picture","profile-picture",$id);
						$profile_result = array_merge($profile_result,$result);
				}if($request->file('shop_image')) 
				{
							$result = $this->fileSave($request,"shop_image","shop-image",$id);
							$profile_result = array_merge($profile_result,$result);
				}
				if($request->file('pan_card_image')) 
				{
							$result = $this->fileSave($request,"pan_card_image","pan-card-image",$id);
							$profile_result = array_merge($profile_result,$result);
				}
				if($request->file('aadhaar_card_image')) 
				{
							$result = $this->fileSave($request,"aadhaar_card_image","aadhaar-card-image",$id);
							$profile_result = array_merge($profile_result,$result);
				}
				if($request->file('aadhaar_img_back')) 
				{			
							/* if($member->profile->aadhaar_img_back)
								$this->removeImage($member->profile->aadhaar_img_back,$id); */
							$result = $this->fileSave($request,"aadhaar_img_back","aadhaar-card-image-back",$id);
							$profile_result = array_merge($profile_result,$result);
				}
				if($request->file('cheque_image')) 
				{
							$result = $this->fileSave($request,"cheque_image","cheque-image",$id);
							$profile_result = array_merge($profile_result,$result);
				}
				if($request->file('form_image')) 
				{
							$result = $this->fileSave($request,"form_image","form-image",$id);
							$profile_result = array_merge($profile_result,$result);
				}
				$profile_result['region']=$request->region;
				 $profile=Profile::where('user_id', $id)
                                ->update($profile_result);
				$ResponseMessage = "Record Successfully Updated";
				if($request->status_id == 1 && $pre_status == 0 ) 
				{
					$nam= $member->name; 
					$mob = $member->mobile;
					$message = "Dear $nam your application has been approved. Login with given Credential. Mobile No: ".$mob." , Password: $password Thanks A2ZSUVIDHAA.";
					$message = urlencode($message);
					CustomTraits::sendSms($mob,$message,Auth::user()->company_id);
					$ResponseMessage = $ResponseMessage." and password has been sent to registered mobile numebr";
					//$ResponseMessage = $ResponseMessage." and password has been sent to registered mobile numebr";
				}
				DB::commit();
				
				return Response::json(array('status' => 'success', 'message' => $ResponseMessage));
			}
            catch(Exception $e)
            {
                DB::rollback();
				return response()->json(['status' => 'failure', 'message' => 'Something went wrong. Please try again...'.$e->getMessage()]);
            }
        } 
		
        $ResponseMessage = "You do not have permission";
        return Response::json(array('status' => 'success', 'message' => $ResponseMessage));
    }

    public function updated(Request $request, $id)
    {
        $member = User::find($id);
        /* $member->status_id = 1; */
        $member->upscheme_id = $request->upscheme;
        $member->save();
        $ResponseMessage = "Record Successfully Updated";
        return Response::json(array('status' => 0, 'message' => $ResponseMessage));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        /* $id = $request->input('id');
        $members = Member::find($id);
        $members->delete();
        return redirect::to('/member'); */
    }
	
	public function generateTransactionpin(Request $request)
	{
		//print_r($request->all());die;
		
			$transactionpin= $request->txn_pin;
			$confirmation_transactionpin=$request->confirm_txn_pin;
			$transactionpin=$confirmation_transactionpin;
			$userPinRecord=Profile::where('user_id',Auth::id())->first();
			if($request->otp != $userPinRecord->txn_otp)
			{
				return back()->with('statusfail', "OTP Wrong");
			}
			if($transactionpin==$confirmation_transactionpin)
			{
				
				$userPinRecord->txn_pin=$transactionpin;
				$userPinRecord->txn_otp='';
				$userPinRecord->save();
				return back()->with('status', "PIN Successfully Generated");
				}
			else{
				return back()->with('statusfail', "Pin did not matched");
			}
		
	}
	 public function generateSchemepin(Request $request)
	{
		//print_r($request->all());die;
      $Schemepin= $request->scheme_pin;
      $Confirm_Schemepin=$request->confirm_scheme_pin;
      $Schemepin=$Confirm_Schemepin;
      $userPinRecord=Profile::where('user_id',Auth::id())->first();
      if($request->otp != $userPinRecord->txn_otp)
      {
        return back()->with('statusfail', "OTP Wrong");
      }
      if($Schemepin==$Confirm_Schemepin)
      {
        $userPinRecord->scheme_pin=$Schemepin;
        $userPinRecord->txn_otp='';
        $userPinRecord->save();
        return back()->with('status', "PIN Successfully Generated");
        }
      else{
        return back()->with('statusfail', "Pin did not matched");
      }
	}
   public function change_password(Request $request)
    {
		
        $old_password = $request->old_password;
        $new_password = $request->password;
        $userdetail = User::find(Auth::id());
		
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
            return back()->with('status', $message);
        } else {
            $message = "Password Not Changed, Try with correct Deatil, Thanks";
            return back()->with('statusfail', $message);
        }
    }


    public function updatePassword(Request $request, $user_id)
    {
		if(in_array(Auth::user()->role_id,array(1)) && $request->ajax())
		{
			$member = User::selectRaw('id,password')->find($request->user_id);
			$password = $request->new_password;
			$member->password = bcrypt($password);; 
			if($member->save()) 
			{ 
				//$this->sendsms($member->mobile, $message,Auth::user()->company_id);
				$msg = "Password has been changed successfully.";
				return Response::json(array('status' => 1, 'message' => $msg));
				
			}
			return Response::json(array('status' => 0, 'message' => "Oops Something went wrong. Please try again."));
		}
		return "Youd do not have permission";
    }

    public function export(Request $request)
    {
		if(in_array(Auth::user()->role_id,array(1,3,4)))
		{
			return Excel::download(new MemberExport(), 'Member Export.xlsx');
		}
    }
    public function view_pertxn(Request $request)
    {
             if($request->api=='api1')
        {
            $data = Txnslab::where('user_id',$request->id)->first();
            if(@$data->user_id!='')
            {
            return response()->json(['ptxn1'=>$data->ptxn1,'ptxn2'=>$data->ptxn2,'ptxn3'=>$data->ptxn3,'ptxn4'=>$data->ptxn4,'ptxn5'=>$data->ptxn5]);
            }
            else
            {
                return response()->json(['status'=>0]);
            }
            
        }
        elseif($request->api=='api2')
        {
            $data = Txnfirstslab::where('user_id',$request->id)->first();
            if(@$data->user_id!='')
            {
            return response()->json(['ptxn1'=>$data->ptxn1,'ptxn2'=>$data->ptxn2,'ptxn3'=>$data->ptxn3,'ptxn4'=>$data->ptxn4,'ptxn5'=>$data->ptxn5]);
            }
            else
            {
                return response()->json(['status'=>0]);
            }
            
        }
        elseif($request->api=='api3')
        {
            $data = Txnsecondslab::where('user_id',$request->id)->first();
            if(@$data->user_id!='')
            {
            return response()->json(['ptxn1'=>$data->ptxn1,'ptxn2'=>$data->ptxn2,'ptxn3'=>$data->ptxn3,'ptxn4'=>$data->ptxn4,'ptxn5'=>$data->ptxn5]);
            }
            else
            {
                return response()->json(['status'=>0]);
            }
            
    }
    elseif($request->api=='api4')
        {
            $data = Txnthiredslab::where('user_id',$request->id)->first();
            if(@$data->user_id!='')
            {
            return response()->json(['ptxn1'=>$data->ptxn1,'ptxn2'=>$data->ptxn2,'ptxn3'=>$data->ptxn3,'ptxn4'=>$data->ptxn4,'ptxn5'=>$data->ptxn5,'ptxn6'=>$data->ptxn6,'ptxn7'=>$data->ptxn7,'ptxn8'=>$data->ptxn8,'ptxn9'=>$data->ptxn9]);
            }
            else
            {
                return response()->json(['status'=>0]);
            }
            
    }
    else
    {
        $data = Txnslab::where('user_id',$request->id)->first();
            if(@$data->user_id!='')
            {
            return response()->json(['ptxn1'=>$data->ptxn1,'ptxn2'=>$data->ptxn2,'ptxn3'=>$data->ptxn3,'ptxn4'=>$data->ptxn4,'ptxn5'=>$data->ptxn5]);
            }
            else
            {
                return response()->json(['status'=>0]);
            }

    }       
        
    }


public function view_pertxn_nkyc(Request $request)
    {

      if($request->id!='')
        {
            if($request->MdAgent==5)
            {
                $data = Txnthiredslab::where('user_id',$request->id)->first();
            }
            elseif($request->MdAgent==3)
            {
                $data = Mdthiredpertxn::where('user_id',$request->id)->first();
            }
            else
            {
                $data = Txnthiredslab::where('user_id',$request->id)->first();
            }
            
            return response()->json(['ptxn10'=>$data->ptxn10,'ptxn11'=>$data->ptxn11,'ptxn12'=>$data->ptxn12,'ptxn13'=>$data->ptxn13,'ptxn14'=>$data->ptxn14,'ptxn15'=>$data->ptxn15,'ptxn16'=>$data->ptxn16,'ptxn17'=>$data->ptxn17]);
            
            
    }
}

public function memberpertxn_nkyc(Request $request)
    {
        $MdAgent = $request->MdAgent;
          $ptxn10 = $request->ptxn10;
          $ptxn11 = $request->ptxn11;
          $ptxn12 = $request->ptxn12;
          $ptxn13 = $request->ptxn13;
          $ptxn14 = $request->ptxn14;
          $ptxn15 = $request->ptxn15;
          $ptxn16 = $request->ptxn16;
          $ptxn17 = $request->ptxn17;
        $user_id = $request->id;
        if($user_id!='')
        {

            if($MdAgent==5)
            {

                Txnthiredslab::where('user_id',$user_id)
            ->update(['ptxn10'=>$ptxn10,'ptxn11'=>$ptxn11,'ptxn12'=>$ptxn12,'ptxn13'=>$ptxn13,'ptxn14'=>$ptxn14,'ptxn15'=>$ptxn15,'ptxn16'=>$ptxn16,'ptxn17'=>$ptxn17,'status_id'=>1]);

            Mdthiredpertxn::where('user_id',$user_id)
             ->update(['ptxn10'=>0,'ptxn11'=>0,'ptxn12'=>0,'ptxn13'=>0,'ptxn14'=>0,'ptxn15'=>0,'ptxn16'=>0,'ptxn17'=>0,'status_id'=>1]);
            }
            else
            {
                Mdthiredpertxn::where('user_id',$user_id)
             ->update(['ptxn10'=>$ptxn10,'ptxn11'=>$ptxn11,'ptxn12'=>$ptxn12,'ptxn13'=>$ptxn13,'ptxn14'=>$ptxn14,'ptxn15'=>$ptxn15,'ptxn16'=>$ptxn16,'ptxn17'=>$ptxn17,'status_id'=>1]);

              Txnthiredslab::where('user_id',$user_id)
            ->update(['ptxn10'=>0,'ptxn11'=>0,'ptxn12'=>0,'ptxn13'=>0,'ptxn14'=>0,'ptxn15'=>0,'ptxn16'=>0,'ptxn17'=>0,'status_id'=>1]);
            }
    
        return "Record Updated Succesfully";
        }
        else
        {
            return "Something went wrong";
        }
    }

   public function memberpertxn(Request $request)
    {
        $ptxn1 = $request->ptxn1;
        $ptxn2 = $request->ptxn2;
        $ptxn3 = $request->ptxn3;
        $ptxn4 = $request->ptxn4;
        $ptxn5 = $request->ptxn5;
         $ptxn6 = $request->ptxn6;
          $ptxn7 = $request->ptxn7;
           $ptxn8 = $request->ptxn8;
            $ptxn9 = $request->ptxn9;
        $ptxna = $request->ptxna;
        $user_id = $request->id;
        if($request->select_api=='api1')
        {
        Txnslab::where('user_id',$user_id)
        ->update(['ptxn1'=>$ptxn1,'ptxn2'=>$ptxn2,'ptxn3'=>$ptxn3,'ptxn4'=>$ptxn4,'ptxn5'=>$ptxn5,'status_id'=>1]);
        return "Record Updated Succesfully";
        }
        elseif($request->select_api=='api2')
        {
            Txnfirstslab::where('user_id',$user_id)
        ->update(['ptxn1'=>$ptxn1,'ptxn2'=>$ptxn2,'ptxn3'=>$ptxn3,'ptxn4'=>$ptxn4,'ptxn5'=>$ptxn5,'status_id'=>1]);
        return "Record Updated Succesfully";
        }
        elseif($request->select_api=='api3')
        {
        Txnsecondslab::where('user_id',$user_id)
        ->update(['ptxn1'=>$ptxn1,'ptxn2'=>$ptxn2,'ptxn3'=>$ptxn3,'ptxn4'=>$ptxn4,'ptxn5'=>$ptxn5,'status_id'=>1]);
        return "Record Updated Succesfully";
        }
        elseif($request->select_api=='api4')
        {
        Txnthiredslab::where('user_id',$user_id)
        ->update(['ptxn1'=>$ptxn1,'ptxn2'=>$ptxn2,'ptxn3'=>$ptxn3,'ptxn4'=>$ptxn4,'ptxn5'=>$ptxn5,'ptxn6'=>$ptxn6,'ptxn7'=>$ptxn7,'ptxn8'=>$ptxn8,'ptxn9'=>$ptxn9,'status_id'=>1]);
        return "Record Updated Succesfully";
        }
        else
        {
            return "Something went wrong";
        }
    }

    public function memberpertxn_m(Request $request)
    {
        $ptxn1 = $request->ptxn1;
        $ptxn2 = $request->ptxn2;
        $ptxn3 = $request->ptxn3;
        $ptxn4 = $request->ptxn4;
        $ptxn5 = $request->ptxn5;
        $ptxn6 = $request->ptxn6;
        $ptxn7 = $request->ptxn7;
        $ptxn8 = $request->ptxn8;
        $ptxn9 = $request->ptxn9;
        $ptxna = $request->ptxna;
        $user_id = $request->id;
        if($ptxn1 ==0){ $md=0; }elseif($ptxn2 ==0){ $md=0; } elseif($ptxn3 ==0){ $md=0; } elseif($ptxn4 ==0){ $md=0; }elseif($ptxn5 ==0){ $md=0; } else{ $md=0; }
        if($request->select_api=='api1')
        {
        Mdpertxn::where('user_id',$user_id)
        ->update(['ptxn1'=>$ptxn1,'ptxn2'=>$ptxn2,'ptxn3'=>$ptxn3,'ptxn4'=>$ptxn4,'ptxn5'=>$ptxn5,'status_id'=>1]);
        return "Record Updated Succesfully";
        }
        elseif($request->select_api=='api2')
        {
            Mdfirstpertxn::where('user_id',$user_id)
        ->update(['ptxn1'=>$ptxn1,'ptxn2'=>$ptxn2,'ptxn3'=>$ptxn3,'ptxn4'=>$ptxn4,'ptxn5'=>$ptxn5,'status_id'=>1]);
        return "Record Updated Succesfully";
        }
        elseif($request->select_api=='api3')
        {
        Mdsecondpertxn::where('user_id',$user_id)
        ->update(['ptxn1'=>$ptxn1,'ptxn2'=>$ptxn2,'ptxn3'=>$ptxn3,'ptxn4'=>$ptxn4,'ptxn5'=>$ptxn5,'status_id'=>1]);
        return "Record Updated Succesfully";
        }
         elseif($request->select_api=='api4')
        {
        Mdthiredpertxn::where('user_id',$user_id)
        ->update(['ptxn1'=>$ptxn1,'ptxn2'=>$ptxn2,'ptxn3'=>$ptxn3,'ptxn4'=>$ptxn4,'ptxn5'=>$ptxn5,'ptxn6'=>$ptxn6,'ptxn7'=>$ptxn7,'ptxn8'=>$ptxn8,'ptxn9'=>$ptxn9,'status_id'=>1]);
        return "Record Updated Succesfully";
        }
        else
        {
            return "Something went wrong";
        }
    }
    
    public function view_pertxn_m(Request $request)
    {
        
             if($request->api=='api1')
        {
            $data = Mdpertxn::where('user_id',$request->id)->first();
            if(@$data->user_id!='')
            {
            return response()->json(['ptxn1'=>$data->ptxn1,'ptxn2'=>$data->ptxn2,'ptxn3'=>$data->ptxn3,'ptxn4'=>$data->ptxn4,'ptxn5'=>$data->ptxn5]);
            }
            else
            {
                return response()->json(['status'=>0]);
            }
            
        }
        elseif($request->api=='api2')
        {
            $data = Mdfirstpertxn::where('user_id',$request->id)->first();
            if(@$data->user_id!='')
            {
            return response()->json(['ptxn1'=>$data->ptxn1,'ptxn2'=>$data->ptxn2,'ptxn3'=>$data->ptxn3,'ptxn4'=>$data->ptxn4,'ptxn5'=>$data->ptxn5]);
            }
            else
            {
                return response()->json(['status'=>0]);
            }
            
        }
        elseif($request->api=='api3')
        {
            $data = Mdsecondpertxn::where('user_id',$request->id)->first();
            if(@$data->user_id!='')
            {
            return response()->json(['ptxn1'=>$data->ptxn1,'ptxn2'=>$data->ptxn2,'ptxn3'=>$data->ptxn3,'ptxn4'=>$data->ptxn4,'ptxn5'=>$data->ptxn5]);
            }
            else
            {
                return response()->json(['status'=>0]);
            }
            
    }
        elseif($request->api=='api4')
        {
            $data = Mdthiredpertxn::where('user_id',$request->id)->first();
            if(@$data->user_id!='')
            {
            return response()->json(['ptxn1'=>$data->ptxn1,'ptxn2'=>$data->ptxn2,'ptxn3'=>$data->ptxn3,'ptxn4'=>$data->ptxn4,'ptxn5'=>$data->ptxn5,'ptxn6'=>$data->ptxn6,'ptxn7'=>$data->ptxn7,'ptxn8'=>$data->ptxn8,'ptxn9'=>$data->ptxn9]);
            }
            else
            {
                return response()->json(['status'=>0]);
            }
            
    }
    else
    {
        $data = Mdpertxn::where('user_id',$request->id)->first();
            if(@$data->user_id!='')
            {
            return response()->json(['ptxn1'=>$data->ptxn1,'ptxn2'=>$data->ptxn2,'ptxn3'=>$data->ptxn3,'ptxn4'=>$data->ptxn4,'ptxn5'=>$data->ptxn5]);
            }
            else
            {
                return response()->json(['status'=>0]);
            }

    }       
        
    }

    public function emp_member()
    {
        if(Auth::user()->role_id==1)
            {
                if (!empty($_SERVER['HTTP_HOST'])) {
                        $host = $_SERVER['HTTP_HOST'];
                    } else {
                        $host = "localhost:8888";
                    }
                $upfront = Upscheme::lists('scheme', 'id');
                $users = User::whereIn('role_id', [8,9])
                ->orderBy('id', 'DESC')->get();
                $parent_id = User::where('role_id', 1)
                ->orderBy('id', 'DESC')->get();
                $roles = Role::lists('role_title', 'id');
                $company = Company::where('company_website', $host)
            ->where('status', 1)
            ->first();
             if ($company) {
                 $company_id = $company->id;
                } else {
                    $company_id = 0;
                }
                $schemes = Scheme::select('id', 'scheme_name')->where('company_id', '=', $company_id)->lists('scheme_name', 'id')->toArray();
				$otpVerifications=ActionOtpVerification::orderBy('id','ASC')->get();
                return view('admin.member', compact('users', 'roles', 'upfront', 'parent_id','otpVerifications'))->with('schemes', $schemes);
            }
    }
	
    public function onhold_update(Request $request)
    {
        if(Auth::user()->role_id==1)
            {


                $hold_api = $request->hold_api;
                if($request->hold_on=='on')
                {
                     $onhold_save = Company::where('txn_hold',0)->update(['txn_hold'=>$hold_api]);
                     $status = 'on';
                }
               elseif($request->hold_on=='off')
               {
                    $onhold_save = Company::where('txn_hold','!=',0)->update(['txn_hold'=>0]);
                     $status = 'off';
               }
                if($onhold_save)
                {
                    return Response()->json(['message'=>'Succesfully Updated!','status'=>$status]);

                }
                else
                {
                    return "Something Wrong to update. Try again!";
                }
        }
        else
        {
             return "Not Permission";
        }    
         
    }

     public function purchaseBalanceReports(Request $request)
    {
        if(Auth::user()->role_id==1 && Auth::id()==1)
            {
                $start_date = ($request->fromdate) ? $request->fromdate ." 00:00:00" : date("Y-m-d") . " 00:00:00";
                $end_date = ($request->todate) ? $request->todate ." 23:59:59" : date("Y-m-d H:i:s");
                $start_date = date("Y-m-d H:i:s", strtotime($start_date));
                $end_date = date("Y-m-d H:i:s", strtotime($end_date));
                 $reportQuery = Report::where('txnid','PB')->orderBy('id', 'DESC');
                $reportQuery->whereBetween('created_at', [$start_date,$end_date]);

               $purchageBalanceReports=$reportQuery->paginate(40);
                return view('admin.purchasebal', compact('purchageBalanceReports')); 
            }
            else
            {
                return "You have not permission to access this page";
            }
    }

    public function purchaseBalance(Request $request)
    {
            if(Auth::user()->role_id==1 && Auth::id()==1)
                {
              $balance = $request->balance;
              if($balance!='')
              {
				  try{
					$id=Auth::id();
					Balance::where('user_id', 1)->increment('user_balance', $balance);
					$now = new \DateTime();
					$datetime = $now->getTimestamp();
					$ctime = $now->format('Y-m-d H:i:s');
					$user_detail = User::find($id);
					$insert_id = Report::insertGetId([
						'number' => $user_detail->mobile,
						'provider_id' => 0,
						'amount' => $balance,
						'api_id' => 0,
						'description' => 'Wallet Reffil',
						'status_id' => 19,
						'opening_balance' => ($user_detail->balance->user_balance - $balance),
						'pay_id' => $datetime,
						'txnid' => 'PB',
						'profit' => 0,
						'payment_id' => 0,
						'total_balance' => $user_detail->balance->user_balance,
						'created_at' => $ctime,
						'user_id' => Auth::id(),
					]);
				  }
				  catch(\Exception $e)
				  {
					   return response()->json(['message'=>"Whoops! Something Went wrong","status"=>0]);
				  }
            return response()->json(['message'=>"Balance purcheged successfully","status"=>1]);
        }
        else
        {
            return response()->json(['message'=>"Please Enter purchage balance","status"=>0]);
        }
    }
    else
    {
        return response()->json(['message'=>"SOORY! You do not have permission","status"=>0]);
    }
    }
    

        // public function all_bank_bankupdown(Request $request)
        // {
        //     Masterbank::where('smart',0)
        //     ->update(['bank_status'=>1,'smart'=>1]);
        //     return view('admin.bankupdown');
        // }
		public function userLists(Request $request)
	{
		
		if(Auth::user()->role_id && Auth::id() == 1)
		{
			$remark_lists = Remark::whereIn('id',[15,16,17,18])->pluck('remark','id')->toArray();
			$remark_lists[0]="-- SELECT --";
			$remark_lists[1]="Restricted";
			
			ksort($remark_lists);
			$query = User::join('profiles', 'users.id', '=', 'profiles.user_id')
					->orderBy('users.id','desc')
					->select('users.id','users.name','users.company_id','users.mobile','users.profile_id','users.role_id');
			if(isset($request->lm_id))
			{
				if($request->lm_id !='')
				{
					$lm_id=$request->lm_id;
					$query->where('users.id',$lm_id);
				}
				if($request->remark !=0)
				{
					$remark_id=$request->remark;
					if($request->remark == 1)
						$query->where('profiles.res_agent',$remark_id);
					else
						$query->where('profiles.remark_id',$remark_id);
				}
			}
			
			$users = $query->paginate(40);
			
			return view('admin.user-list', compact('users','remark_lists'));
		}
		return view('errors.page-not-found');
	}
	private function removeImage($file_name,$user_id)
	{
		$image_path = config('constants.IMAGE_PATH')."/$user_id/".$file_name;
		
		if(file_exists($image_path)){
			@unlink($image_path);
		}
	}
	private function removeAllImages($file_names)
	{
		
		$user_id=$file_names['user_id'];
		unset($file_names['user_id']);
		foreach($file_names as $key => $value)
		{
			$image_path = config('constants.IMAGE_PATH')."/$user_id/".$value;
			if(file_exists($image_path)){
				@unlink($image_path);
			}
		}
	}
	/* public function updateProfile()
	{
		$profiles = Profile::all();
		foreach($profiles as $profile)
		{
			
			$profile->doc_list = $profile->doc_list.',0';
			$profile->save();
			
		}
	} */
	public function agentRestrictedToUploadDocs(Request $request, $user_id)
	{
		if($request->ajax() && Auth::user()->role_id== 1)
		{
			$req_user_id = $request->user_id;
			$res_value = $request->res_value;
			$mess=($res_value)?"Restricted":"Failed";
			$messge = $request->message;
			if(Profile::where('user_id',$req_user_id)->update(['res_agent'=>$res_value,'message'=>$messge]))
			{
				$mess=($res_value)?"Restricted":"Un-restricted";
				return response()->json(['status' => 1, 'message' => $mess]);
			}
			return response()->json(['status' => 0, 'message' => $mess]);
		}
	}
	public function agentUpdate(Request $request)
	{
			$rules = array(
			'shop_image' => 'max:500| mimes:jpeg,jpg,png,pdf',
			'profile_picture' => 'max:200| mimes:jpeg,jpg,png,pdf',
			'pan_card_image' => 'max:500|mimes:jpeg,jpg,png,pdf',
			'aadhaar_card_image' =>'max:500|mimes:jpeg,jpg,png,pdf',
			'aadhaar_img_back' =>'max:500|mimes:jpeg,jpg,png,pdf',
			'cheque_image' =>'max:500|mimes:jpeg,jpg,png,pdf',
			'form_image' =>'max:3072|mimes:jpeg,jpg,png,pdf',
			);
			$validator = Validator::make($request->all(), $rules);
			if ($validator->fails()) {
				return Response::json(array(
					'success' => false,
					'errors' => $validator->getMessageBag()->toArray()
				)); // 400 being the HTTP code for an invalid request.
			}
		/* 	print_r($request->all());die; */
			//print_r($request->all());die;
			$user_id=Auth::id();
			$profile_result = array('user_id'=>Auth::id());
			DB::beginTransaction();
            try
            {
				if($request->file('profile_picture')) 
				{
							$result = $this->fileSave($request,"profile_picture","profile-picture",$user_id);
							$profile_result = array_merge($profile_result,$result);
				}if($request->file('shop_image')) 
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
							$result = $this->fileSave($request,"aadhaar_img_back","aadhaar-img-back",$user_id);
							$profile_result = array_merge($profile_result,$result);
				}
				if($request->file('cheque_image')) 
				{
							$result = $this->fileSave($request,"cheque_image","cheque-image",$user_id);
							$profile_result = array_merge($profile_result,$result);
				}
				if($request->file('form_image')) 
				{
							$result = $this->fileSave($request,"form_image","form-image",$user_id);
							$profile_result = array_merge($profile_result,$result);
				}
				$profile=Profile::where('user_id', $user_id)->update($profile_result);
				DB::commit();
				$ResponseMessage = "Record Successfully Updated";
				return Response::json(array('status' => 'success', 'message' => $ResponseMessage));
			}
			catch(Exception $e)
            {
                DB::rollback();
				return response()->json(['status' => 'failure', 'message' => 'Something went wrong. Please try again...']);
            }
			
	}
	public function rejectUserDocuments(Request $request,$id)
	{
		if($request->ajax() && Auth::user()->role_id== 1)
		{
			$user_id= $request->id;
			$remark=$request->remark;
			$profile_modal= Profile::where('user_id',$user_id)->first();
			$profile_modal->doc_verify = 0;
			$profile_modal->message = $remark;
			$profile_modal->remark_id = 16;
			$profile_modal->doc_list = "0,0,0,0,0,0,0";
			$profile_modal->save();
			return Response()->json(['message'=>'Document has been rejected','status'=>"Success"]);
		}
		return view('admin.page-not-found');
	}
	public function approveUserDocuments(Request $request,$id)
	{
		if($request->ajax() && Auth::user()->role_id== 1)
		{
			//print_r($request->all());die;
			$user_id= $request->id;
			$remark= $request->remark;
			$profile_modal= Profile::where('user_id',$user_id)->first();
			$profile_modal->doc_verify = 1;
			$profile_modal->remark_id = 17;
			$profile_modal->message = $remark;
			$profile_modal->doc_list = "1,2,3,4,5,6,7";
			$profile_modal->save();
			return Response()->json(['message'=>'Document has been approved','status'=>"Success"]);
		}
		return view('admin.page-not-found');
	}
	public function incompleteUserDocuments(Request $request,$id)
	{
		if($request->ajax() && Auth::user()->role_id== 1)
		{
			//print_r($request->all());die;
			$user_id= $request->id;
			$doc_list= $request->doc_list;
			$remark= $request->remark;
			$profile_modal= Profile::where('user_id',$user_id)->first();
			$profile_modal->doc_verify = 0;
			$profile_modal->remark_id = 18;
			$profile_modal->message = $remark;
			$profile_modal->doc_list = $doc_list;
			$profile_modal->save();
			return Response()->json(['message'=>'Status has been changed','status'=>"Success"]);
		}
		return view('admin.page-not-found');
	}
	public function addImportantNotice(Request $request)
	{
		if($request->ajax() && Auth::user()->role_id== 1)
		{
			$today_notice_id= $request->today_notice_id;
			$notice= $request->notice;
			$written_by= $request->written_by;
			if($today_notice_id !='')
			{
				PaymentRequestADNotice::where('id',$today_notice_id)->update(['notice'=>$notice,'written_by'=>$written_by]);
				return Response()->json(['message'=>'Notice has been updated successfuly','status'=>"Success"]);
			}
			else
			{
				PaymentRequestADNotice::create(['notice'=>$notice,'written_by'=>$written_by]);
				return Response()->json(['message'=>'Notice has been created Successfuly','status'=>"Success"]);
			}
		}
		return view('admin.page-not-found');
	}
	private function fileSave($request,$image_name,$file_name,$user_id)
	{
		
		if (\Input::hasFile("$image_name")) {
			$file = \Input::file("$image_name");
			$filename = $user_id.'-'.$file_name.'-'.time(). '.' . $file->getClientOriginalExtension();
			$file->move("user-uploaded-files/$user_id", $filename);
			return (array($image_name=>$filename));
		}
	}
	public function master_distributor()
    {
		$state = new State();
		$state_list = $state->stateList();
        $export_users=array();
			if (in_array(Auth::user()->role_id,array(1,12,14,11))) 
			{
				$export_users = User::whereIn('role_id', [3])->pluck('name', 'id')->toArray();
				$users = User::whereIn('role_id', [3])->orderBy('id', 'DESC')->paginate();
			}
			else 
			{
				return view('errors.page-not-found');
			}
			$parent_id = User::where('role_id', 2)->orderBy('id', 'DESC')->paginate();
			
			$roles = Role::where('id',3)->pluck('role_title', 'id');
			if (!empty($_SERVER['HTTP_HOST'])) {
				$host = $_SERVER['HTTP_HOST'];
			} else {
				$host = "localhost:8888";
			}
			
			$schemes = Scheme::select('id', 'scheme_name')->pluck('scheme_name', 'id')->toArray();  
			$upfront = Upscheme::pluck('scheme', 'id');
			$moneySchemes = Moneyscheme::select('id','scheme_name','scheme_for')->get();
			$walletSchemes = WalletScheme::whereIn('scheme_for',[3,1,6,5,7])->get();
			$otpVerifications=ActionOtpVerification::orderBy('id','ASC')->get();
			return view('admin.member', compact('users', 'roles', 'upfront', 'parent_id','state_list','export_users','moneySchemes','walletSchemes','otpVerifications'))->with('schemes', $schemes);
		}
		public function distributor()
		{
			$export_users=array();
			if (in_array(Auth::user()->role_id,array(1,12,14,11))) 
			{
				$export_users = User::where('role_id', 4)->pluck('name', 'id')->toArray();
				$users = User::where('role_id', 4)->orderBy('id', 'DESC')->simplePaginate();
				$parent_id = User::where('role_id', 3)->orderBy('id', 'DESC')->simplePaginate();
			} 
			elseif (Auth::user()->role_id == 3) 
			{
			   $users = User::where('parent_id', Auth::id())->where('role_id', 4)->orderBy('id', 'DESC')->simplePaginate();
				$parent_id = User::where('parent_id', Auth::id())->where('role_id', 4)->orderBy('id', 'DESC')->get();
			}
			else 
			{
				return view('errors.page-not-found');
			}
		  
		   $roles = Role::where('id',4)->pluck('role_title', 'id');
			$upfront = Upscheme::pluck('scheme', 'id');
			if (!empty($_SERVER['HTTP_HOST'])) {
				$host = $_SERVER['HTTP_HOST'];
			} else {
				$host = "localhost:8888";
			}
			
			$schemes = Scheme::select('id', 'scheme_name')->pluck('scheme_name', 'id')->toArray();
			$state = new State();
			$state_list = $state->stateList();
			$moneySchemes = Moneyscheme::select('id','scheme_name','scheme_for')->get();
			$walletSchemes = WalletScheme::whereIn('scheme_for',[3,1,6,5,7])->get();
			$otpVerifications=ActionOtpVerification::orderBy('id','ASC')->get();
			return view('admin.member', compact('users', 'roles', 'upfront', 'parent_id','state_list','export_users','moneySchemes','walletSchemes','otpVerifications'))->with('schemes', $schemes);
		}

    public function provider_manage(Request $request)
	{
        if (Auth::user()->role_id == 1)
         {
            $provider_manage = Provider::orderBy('id', 'ASC')->where('id','!=',0)->select('id','api_id','provider_name')->get();
    
          return view('admin.providermanage', compact('provider_manage'));

          }
		else
		{
         return "Not permission,kindly contact to Admin!";
		}

    }

    public function gst_commission_view(Request $request)
    {
        try
        {
            $user_id = $request->user_id;

             $save_comm = Gstcommission::where('user_id',$user_id)->select('commission')->first();
             
            if($save_comm)
            {
            return Response::json(['status_id'=>1,'commission'=>$save_comm->commission]);
            }
        }
        catch(\Exception $e)
        {
             return Response::json(['status_id'=>1,'message'=>'Something went wrong,try again!']);
        }
        
    }
    public function savecommission(Request $request)
    {
        $user_id  = $request->id;
        $comm = $request->comm;
        $save_comm = Gstcommission::where('user_id',$user_id)->update(['commission'=> $comm]);
        if($save_comm)
        {
            return Response::json(['status_id'=>1,'message'=>'successfully updated!']);
        }
    }
	public function getDmtScheme(Request $request)
	{
		$userId = $request->user_id;
		$walletSchemes = Member::selectRaw('imps_wallet_scheme,dmt_two_wallet_scheme,verification_scheme,bill_scheme,aeps_scheme')->where('user_id',$userId)->first();
		if($walletSchemes)
			return response()->json(['status'=>1,'message'=>$walletSchemes]);
		else
			return response()->json(['status'=>0,'message'=>"No Record Found"]);
		
	}public function showDmtScheme(Request $request)
	{
		print_r($request->all());die;
		$userId = $request->user_id;
		$walletSchemes = Member::selectRaw('imps_wallet_scheme')->where('user_id',$userId)->first();
		if($walletSchemes)
			return response()->json(['status'=>1,'message'=>$walletSchemes]);
		else
			return response()->json(['status'=>0,'message'=>"No Record Found"]);
		
	}
	public function setDmtScheme(Request $request,$user_id)
	{
		
		if(in_array(Auth::user()->role_id,[1]))
		{
			if(Member::where('user_id',$user_id)->update(['imps_wallet_scheme'=>$request->dmtOneScheme,'dmt_two_wallet_scheme'=>$request->dmtTwoScheme,'verification_scheme'=>$request->verificationScheme,'bill_scheme'=>$request->bill_scheme,'aeps_scheme'=>$request->aepsScheme]))
				return response()->json(['status'=>1,'message'=>"Scheme has been updated"]);
			else
				return response()->json(['status_id'=>0,'message'=>"Failed"]);
		}
		return response()->json(['status_id'=>0,'message'=>"Access Denied"]);
	}
	public function getWalletScheme(Request $request)
	{
		$userId = $request->user_id;
		$walletSchemes = Member::selectRaw('wallet_scheme,kyc_scheme')->where('user_id',$userId)->first();
		if($walletSchemes)
			return response()->json(['status'=>1,'message'=>$walletSchemes]);
		else
			return response()->json(['status'=>0,'message'=>"No Record Found"]);
	}
	public function setWalletScheme(Request $request,$user_id)
	{
		
		if(in_array(Auth::user()->role_id,[1,3,4]))
			if(Member::where('user_id',$user_id)->update(['wallet_scheme'=>$request->premiumWalletScheme,'kyc_scheme'=>$request->premiumKycScheme]))
				return response()->json(['status'=>1,'message'=>"Wallet Scheme pdation successful"]);
			else
				return response()->json(['status_id'=>0,'message'=>"Wallet Scheme not updated"]);
	}
	public function generateOTP()
	{
		
		$digits = 4;
		$otp = rand(pow(10, $digits-1), pow(10, $digits)-1);
		Profile::where("user_id",Auth::id())->update(['txn_otp'=>$otp]);
		$msg = "OTP $otp for Generate new pin";
		$message = urlencode($msg);
		CustomTraits::sendSMS(Auth::user()->mobile, $message,1);
		return response()->json(['status_id'=>0,'message'=>"OTP has been sent registered mobile number"]);
	}
	 public function passwordGenerateOTP(Request $request)
    {

        //dd($request->all);die();
        $mobile=$request->mobileNumber;
        $Id=$request->userId;
        $digits = 6;
        $otp = rand(pow(10, $digits-1), pow(10, $digits)-1);
        //$otp=$this->randomPassword();
		 $otp=trim($otp);
        $value=ActionOtpVerification::where('mobile',$mobile)->update(['password_otp'=>$otp,'user_id'=>$Id]);
        $msg = "Dear Admin Use this otp $otp to change the paswsword of userId $Id";
        $message = urlencode($msg);
        CustomTraits::sendSMS($mobile, $message,1);
        return response()->json(['status_id'=>1,'message'=>"OTP has been sent registered mobile number"]);
    }

    public function passwordVerifyOTP(Request $request)
    {
      $pwdOtp=$request->otp;
      $mobile=$request->mobileNumber;
      $id=$request->userId;
      $verifyOTP=ActionOtpVerification::where('mobile',$mobile)->where('user_id',$id)->first();
        if($request->otp != $verifyOTP->password_otp)
            {
                return response()->json(['status_id'=>3, 'message'=> "OTP Wrong"]);
            }
        else{
            $verifyOTP->password_otp=$pwdOtp;
            $verifyOTP->password_otp='';
            $verifyOTP->user_id=$id;
            $verifyOTP->save();
            return response()->json(['status_id'=>1,'message'=> "OTP Verified Successfully"]);
        }
    }
	public function refreshBalance()
	{
		return response()->json(['status'=>1,'message'=>number_format(Auth::user()->balance->user_balance,2)]);
	}
	private function randomPassword() 
	{
		$alphabet = "abcdefghijkmnopqrstuwxyzABCDEFGHJKLMNPQRSTUWXYZ123456789";
		$pass = array(); //remember to declare $pass as an array
		$alphaLength = strlen($alphabet) - 1; //put the length -1 in cache
		for ($i = 0; $i < 8; $i++) 
		{
			$n = rand(0, $alphaLength);
			$pass[] = $alphabet[$n];
		}
		return implode($pass); //turn the array into a string
	}
	public function memberCustomSearch(Request $request)
	{
		$userQuery = User::selectRaw('id,name,mobile,role_id,parent_id,status_id,created_at,prefix,is_aeps_onboard');
		if($request->searchType == "NAME")
					$userQuery->where('name','like',trim($request->content) .'%');
				elseif($request->searchType == "MOB")
					$userQuery->where('mobile','like',trim($request->content) .'%');
				elseif($request->searchType == "ID")
					$userQuery->where('id','like',trim($request->content) .'%');
		if(Auth::user()->role_id ==4)
			$userQuery->where('parent_id',Auth::id());
		if(Auth::user()->role_id ==3)
			return "No Permission";
		if($request->urlName == "index")
		{
			$userQuery->where('role_id','!=',1);
		}
    elseif($request->urlName == "master distributor")
      $userQuery->where('role_id','=',3);
		elseif($request->urlName == "distributor")
			$userQuery->where('role_id','=',4);
		elseif($request->urlName == "retailer")
			$userQuery->where('role_id','=',5);
		elseif($request->urlName == "api_retailer")
			$userQuery->where('role_id','=',7);
		$userQuery->orderBy('id','desc');
		$users = $userQuery->get();
		$totalCount = count($users);
		$data=array();
		if($totalCount)
		{
			$data = $users->map(function ($user) {
				return [
					'created_at'=>date_format($user->created_at,"Y-m-d H:i:s"),
					'userPrefix'=>$user->prefix,
					'user_id'=>$user->id,
					'cashDepositeCharge'=>$user->balance->cash_deposit_charge,
					'cashDepositeMinCharge'=>$user->balance->cash_deposit_min_charge,
					'parentPrefix'=>$user->parent->prefix,
					'name'=>$user->name,
					'mobile'=>$user->mobile,
					'user_balance'=>number_format($user->balance->user_balance,3),
					'role_title'=>$user->role->role_title,
					'status'=>($user->status_id) ?"Active" :"In-active",
					'parentName'=> $user->parent->name ,
					'parentId'=> $user->parent_id ,
					'isAepsOnboard'=> $user->is_aeps_onboard ,
				];
			});
		}
			return response()->json(['totalCount'=>$totalCount,'data'=>$data]);
			
			
	}
	public function makeUserActiveDeactive(Request $request,$id) 
	{
		if($request->ajax()){
			
			$user_id=Auth::id();
			$update_user_id=$request->user_id;
			$user_details = User::find($update_user_id);
			
			if($user_details->parent_id == $user_id)
			{
				$user_details->status_id=($user_details->status_id)?0:0;
				//print_r($user_details->status_id);die;
				if($user_details->save())
				return response()->json(['status'=>'success','message'=>'Status has been changed','current_status'=>$user_details->status_id]);
			}
			return Response()->json(['status'=>"failure",'message'=>'Something went worng...']);
		}
		return view('errors.page-not-found');
	}
	public function userLoginOtp()
	{
		if(Auth::user()->role_id==1)
		{
			$usersOtp = User::where('total_logins','>',1)->orderBy('otp_number','desc')->get();
			return view('admin.user-otp-list',compact('usersOtp'));
		}
		return view('errors.permission-denied');
	}
	function getFundRequestScheme(Request $request,$userId)
	{
		if(Auth::user()->role_id==1)
		{
			$balance = Balance::selectRaw('user_id as id,cash_deposit_charge,cash_deposit_min_charge')->where('user_id',$userId)->first();
			return response()->json(['status'=>1,'message'=>"Record Found",'details'=>$balance]);
		}
		return view('errors.permission-denied');
	}
	function getAllUpscheme()
	{
		  $upfront = Upscheme::pluck('scheme', 'id');
		  return response()->json(['status'=>1,'message'=>'','details'=>$upfront]);
	}
	function updateFundRequestScheme(Request $request)
	{
		if(Auth::user()->role_id==1)
		{
			if(Balance::where('user_id',$request->user_id)->update(['cash_deposit_charge'=>$request->cashDepositeCharge,'cash_deposit_min_charge'=>$request->cashDepositMinCharge]))
				return response()->json(['status'=>1,'message'=>"Successfully update",'details'=>'']);
			else
				return response()->json(['status'=>0,'message'=>"Updation Failed",'details'=>'']);
		}
		return view('errors.permission-denied');
	}
}
