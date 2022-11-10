<?php

namespace App\Http\Controllers;
use Mail;
use App\User;
use Excel;
use Illuminate\Http\Request;
use App\Report;
use DB;
use App\Company;
use App\Balance;
use App\Member;
use Carbon\Carbon;
use App\Http\Requests;
use App\Loadcash;
use App\Pmethod;
use App\Netbank;
use Response;
use Validator;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class SecurityController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function get_users(Request $request)
    {
		$all_users = User::orderBy('id', 'DESC')->get();
		$date = $request->fromdate;
		if($date!='')
		{
			 $report = Report::groupBy('user_id')
			->orderBy('id', 'DESC')
			->whereDate('created_at','=',$date)
			->selectRaw('*, count(amount) as transection, sum(amount) as t_amount')
			->get();
		}
		else {
		
			$report= Report::groupBy('user_id')
			->orderBy('id', 'DESC')
			->whereIn('status_id',[1,7])
			->selectRaw('*, count(amount) as transection, sum(amount) as t_amount, max(id) as id')
			->get();
		
		$member_id = array();
		
        foreach ($report as $member) {
                $member_id[] = $member->id;
				
            }
			$main_report= Report::orderBy('id', 'DESC')
			->whereIn('id',$member_id)
			->get();
			
			return view('admin.security',compact('main_report','all_users'));
		}
	}
	
	public function closing_balance(Request $request)
	{
		
		/* $report= Report::groupBy('user_id')
					->orderBy('id', 'ASC')
					->whereDate('created_at','=','2016-12-28')
					->whereIn('status_id',[1,2,4])
					->where('api_id','!=',1)
					->where('user_id',921)
					->selectRaw('*, count(amount) as transection, sum(amount) as t_amount, max(id) as id')
					->get();
					return $report; */
							
		if (Auth::user()->role_id == 1) {
            $SessionID = Company::find(3)->sessionid;
			if ($request->export == 'export') {
                ini_set("memory_limit", "200056M");
                header("Content-Type: application/vnd.ms-excel");
                header("Cache-control: private");
                header("Pragma: public");
                Excel::create('records', function ($excel) use ($request) {
                    $excel->sheet('Sheet1', function ($sheet) use ($request) {
					$cl_date = $request->fromdate;
                      	$report= Report::groupBy('user_id')
							->orderBy('id', 'DESC')
							->whereDate('created_at','=',$cl_date)
							->whereIn('status_id',[1,2,4,6,7])
							->where('api_id','!=',1)
							->selectRaw('*, count(amount) as transection, sum(amount) as t_amount, max(id) as id')
							->get();

						$member_id = array();
						
						foreach ($report as $member) {
								$member_id[] = $member->id;
							}
							$main_report= Report::orderBy('id', 'DESC')
							->whereIn('id',$member_id)
							->get();
						 $arr = array();
                        foreach ($main_report as $employee) {
                            $data = array($employee->user->id,$employee->user->name,$employee->created_at,$employee->total_balance);
                            array_push($arr, $data);
                        }
                        //set the titles
                        $sheet->fromArray($arr, null, 'A1', false, false)->prependRow(array(
                                'ID','Name','Date & time','closing balance'
                            )
                        );
                    });
                })->export('csv');
	
			}
		}
		
	}
	
	public function closing_balance_ex_date(Request $request)
	{
		if (Auth::user()->role_id == 1 || Auth::user()->role_id==19) {
            $SessionID = Company::find(3)->sessionid;

			if ($request->export == 'export') {
				 
                ini_set("memory_limit", "200056M");
                header("Content-Type: application/vnd.ms-excel");
                header("Cache-control: private");
                header("Pragma: public");
                Excel::create('records', function ($excel) use ($request) {
                    $excel->sheet('Sheet1', function ($sheet) use ($request) {
					$cl_date = $request->fromdate;
						
                      	$report= Report::groupBy('user_id')
							->orderBy('id', 'DESC')
							->whereDate('created_at','=',$cl_date)
							->selectRaw('*, count(amount) as transection, sum(amount) as t_amount, max(id) as id')
							->get();
							
					
						
						$member_id = array();
						$member_user_id = array();
						
						foreach($report as $member){
								$member_id[] = $member->id;
								$member_user_id[] = $member->user_id;
							}

							$user_table = User::orderBy('id', 'DESC')
							->whereNotin('id',$member_user_id)
							->get();
							
							
							$u_id=array();
							foreach ($user_table as $users_table) {
								$u_id[] = $users_table->id;
							}
							
							$main_report= Report::groupBy('user_id')
							->orderBy('id', 'DESC')
							->whereDate('created_at','<=',$cl_date)
							->whereIn('user_id',$u_id)
							->selectRaw('*, count(amount) as transection, sum(amount) as t_amount, max(id) as id')
							->get();
							
							$new_u_id =array();
							foreach($main_report as $m_repo)
							{
								$new_u_id[] = $m_repo->id;
							}
							$main_report_new = Report::orderBy('id', 'DESC')
							->whereIn('id',$new_u_id)
							->whereIn('status_id',[1,2,4,6,7])
							->whereNotIn('api_id',[1])
							->get();
							
						 $arr = array();
                        foreach ($main_report_new as $employee) {
                            $data = array($employee->user->id,$employee->user->name,$employee->created_at,$employee->total_balance);
                            array_push($arr, $data);
                        }
                        //set the titles
                        $sheet->fromArray($arr, null, 'A1', false, false)->prependRow(array(
                                'ID','Name','Date & time','closing balance'
                            )
                        );
                    });
                })->export('csv');
	
			}
		}
	}
	public function falure_amount(Request $request)
	{
		if (Auth::user()->role_id == 1 || Auth::user()->role_id==19) {
            $SessionID = Company::find(3)->sessionid;
            if ($request->export == 'export') {
			
                ini_set("memory_limit", "200056M");
                header("Content-Type: application/vnd.ms-excel");
                header("Cache-control: private");
                header("Pragma: public");
                Excel::create('records', function ($excel) use ($request) {
                    $excel->sheet('Sheet1', function ($sheet) use ($request) {
				
                      $date = $request->fromdate;
						$employees= Report::groupBy('user_id')
						->orderBy('id', 'DESC')
						->whereDate('created_at','=',$date)
						->where('status_id',2)
						->selectRaw('*, count(amount) as transection, sum(amount) as t_amount')
						->get();	
						 $arr = array();
							foreach ($employees as $employee){
                            $data = array($employee->user->id,$employee->user->name,$employee->created_at,$employee->t_amount);
                            array_push($arr, $data);
                        }
                        //set the titles
                        $sheet->fromArray($arr, null, 'A1', false, false)->prependRow(array(
                                'ID','Name','Date & time','Failure Total'
                            )
                        );
                    });
                })->export('csv');
	
			}
		}
	}
	
	public function admin_to_dist(Request $request)
	{
		if (Auth::user()->role_id == 1 || Auth::user()->role_id==19) {
            $SessionID = Company::find(3)->sessionid;
            if ($request->export == 'export') {
			
                ini_set("memory_limit", "200056M");
                header("Content-Type: application/vnd.ms-excel");
                header("Cache-control: private");
                header("Pragma: public");
                Excel::create('records', function ($excel) use ($request) {
                    $excel->sheet('Sheet1', function ($sheet) use ($request) {
				
                      $date = $request->fromdate;
						$employees= Report::groupBy('credit_by')
						->orderBy('id', 'DESC')
						->whereDate('created_at','=',$date)
						->where('status_id',7)
						->where('user_id','=',110)
						->selectRaw('*, count(amount) as transection, sum(amount) as t_amount')
						->get();
						
						 $arr = array();
							foreach ($employees as $employee){
                            $data = array($employee->user->id,$employee->user->name,$employee->credit_by,$employee->created_at,$employee->t_amount);
                            array_push($arr, $data);
                        }
                        //set the titles
                        $sheet->fromArray($arr, null, 'A1', false, false)->prependRow(array(
                                'ID','Name','Distributor','Date & time',' Total'
                            )
                        );
                    });
                })->export('csv');
	
			}
		}
	}
	
	public function distt_to_agent(Request $request)
	{
		if (Auth::user()->role_id == 1 || Auth::user()->role_id==19) {
            $SessionID = Company::find(3)->sessionid;
            if ($request->export == 'export') {
			
                ini_set("memory_limit", "200056M");
                header("Content-Type: application/vnd.ms-excel");
                header("Cache-control: private");
                header("Pragma: public");
                Excel::create('records', function ($excel) use ($request) {
                    $excel->sheet('Sheet1', function ($sheet) use ($request) {
				
                      $date = $request->fromdate;
						$dist=User::groupBy('id')
						->where('parent_id',110)
						->get();
						$dist_id = array();
						foreach($dist as $val)
						{
							$dist_id[] = $val->id;
						}
						
						$employees= Report::groupBy('user_id')
						->orderBy('id', 'DESC')
						->whereDate('created_at','=',$date)
						->where('txnid','DT')
						->WhereIn('user_id',$dist_id)
						->selectRaw('*, count(amount) as transection, sum(amount) as t_amount')
						->get();
						
						 $arr = array();
							foreach ($employees as $employee){
                            $data = array($employee->user->id,$employee->user->parent_id,$employee->user->name,$employee->created_at,$employee->txnid,$employee->t_amount);
                            array_push($arr, $data);
                        }
                        //set the titles
                        $sheet->fromArray($arr, null, 'A1', false, false)->prependRow(array(
                                'ID','P_ID','Name','Date & time','txn id','Total'
                            )
                        );
                    });
                })->export('csv');
	
	}
	}
	
	}
	
	public function distt_to_all_agents(Request $request)
	{
	
	
	if (Auth::user()->role_id == 1 || Auth::user()->role_id==19) {
            $SessionID = Company::find(3)->sessionid;
            if ($request->export == 'export') {
			
                ini_set("memory_limit", "200056M");
                header("Content-Type: application/vnd.ms-excel");
                header("Cache-control: private");
                header("Pragma: public");
                Excel::create('records', function ($excel) use ($request) {
                    $excel->sheet('Sheet1', function ($sheet) use ($request) {
				
                      $date = $request->fromdate;
						$dist=User::groupBy('id')
						->where('parent_id',110)
						->get();
						$dist_id = array();
						foreach($dist as $val)
						{
							$dist_id[] = $val->id;
						}
						
						$agent=User::groupBy('id')
						->whereIn('parent_id',$dist_id)
						->get();
						
						$agents_id = array();
						foreach($agent as $val)
						{
							$agents_id[] = $val->id;
						}
						
						
						$employees= Report::orderBy('id', 'DESC')
						->whereDate('created_at','=',$date)
						->where('txnid','DT')
						->where('status_id',6)
						->WhereIn('user_id',$agents_id)
						->get();
						
						 $arr = array();
							foreach ($employees as $employee){
                            $data = array($employee->user->parent_id,$employee->user->name,$employee->user->id,$employee->created_at,$employee->txnid,$employee->amount);
                            array_push($arr, $data);
                        }
                        //set the titles
                        $sheet->fromArray($arr, null, 'A1', false, false)->prependRow(array(
                                'Distributor ID','Agent','Agent ID','Date & time','txn id','Amount'
                            )
                        );
                    });
                })->export('csv');
	
	}
	}
		
	
	}
	public function export_op_bln(Request $request)
	{

		if (Auth::user()->role_id == 1 || Auth::user()->role_id==19) {
            $SessionID = Company::find(3)->sessionid;
            if ($request->export == 'export') {
				 
                ini_set("memory_limit", "200056M");
                header("Content-Type: application/vnd.ms-excel");
                header("Cache-control: private");
                header("Pragma: public");
                Excel::create('records', function ($excel) use ($request) {
                    $excel->sheet('Sheet1', function ($sheet) use ($request) {
				
                      $date = $request->fromdate;
						$employees= Report::groupBy('user_id')
						->orderBy('id', 'DESC')
						->whereDate('created_at','=',$date)
						->whereIn('status_id',[1,7])
						->selectRaw('*, count(amount) as transection, sum(amount) as t_amount')
						->get();
						
						 $arr = array();
                        foreach ($employees as $employee) {
						
                            $data = array($employee->user->id,$employee->user->name,$employee->created_at,$employee->total_balance,$employee->transection,$employee->t_amount);
                            array_push($arr, $data);
                        }
                        //set the titles
                        $sheet->fromArray($arr, null, 'A1', false, false)->prependRow(array(
                                'ID','Name','Date & time','Opening Balance','Txn','Total'
                            )
                        );
                    });
                })->export('csv');
	
	}
	}
	}
	
	public function dummy_test(Request $request)
	{ 
						$employees1= Report::groupBy('user_id')
						->orderBy('id', 'DESC')
						->whereDate('created_at','=',date('y-m-d'))
						->where('status_id',1)
						->selectRaw('*, count(amount) as transection, sum(amount) as t_amount')
						->get();
						$total_amount=array();
							foreach($employees1 as $key => $newemployees1)
							{
								$total_amount[] = $newemployees1;
								
							}
					    $employees2= Report::groupBy('user_id')
						->orderBy('id', 'ASC')
						->whereDate('created_at','=','2016-12-26')
						->where('status_id',1)
						->selectRaw('*, count(amount) as transection, sum(amount) as d_amount')
						->get();
						
						$f_total_amount=array();
						
							foreach($employees2 as $newemployees2)
							{
								$f_total_amount[] = $newemployees2;
								
							}

						$employees = array_merge($total_amount, $f_total_amount);
					
						
						return view('admin.dummy_test',compact('employees1','employees2'));
						
	}
	
	
	public function total_agent(Request $request)
	{	
		$date = $request->fromdate;
        $users = User::orderBy('id', 'DESC')
            ->whereDate('updated_at','=',$date)
			->get();	
        return view('admin.security',compact('users'));
	}
	
	public function users_balance(Request $request)
    {
		$date = $request->fromdate;
        $open_close_balance = Report::orderBy('id', 'DESC')
            ->whereDate('created_at','=',date('y-m-d'))
			->get();	
        return view('admin.open_close_balance',compact('open_close_balance'));
    }
	
	public function runing_balance(Request $request)
	{
			if (Auth::user()->role_id == 1) {
            $SessionID = Company::find(3)->sessionid;
            if ($request->export == 'export') {
                ini_set("memory_limit", "200056M");
                header("Content-Type: application/vnd.ms-excel");
                header("Cache-control: private");
                header("Pragma: public");
                Excel::create('records', function ($excel) use ($request) {
                    $excel->sheet('Sheet1', function ($sheet) use ($request) {
                        $employees = Report::orderBy('id', 'DESC')
                                ->whereDate('created_at', '>=', $request->fromdate)
                                ->whereDate('created_at', '<=', $request->todate)
                                ->where('user_id', $request->select_users)
                                ->orderBy('id', 'DESC')
                                ->get();
                        $arr = array();
                        foreach ($employees as $employee) {
                            $data = array($employee->id, $employee->pay_id, $employee->user->name . '(' . $employee->user->id . ')', $employee->created_at, $employee->api->api_name, $employee->number, $employee->txnid, $employee->amount, $employee->profit, $employee->total_balance, $employee->status->status);
                            array_push($arr, $data);
                        }
                        //set the titles
                        $sheet->fromArray($arr, null, 'A1', false, false)->prependRow(array(
                            'Id', 'Pay Id', 'Name', 'Date & Time', 'Product',
                            'Number', 'Reference ID', 'Amount', 'Profit', 'Total', 'Status'
                                )
                        );
                    });
                })->export('csv');
            }
	}
	
	}
	
	public function refunded_balance(Request $request)
	{
		if (Auth::user()->role_id == 1 || Auth::user()->role_id==19) {
            $SessionID = Company::find(3)->sessionid;
            if ($request->export == 'export') {
			
                ini_set("memory_limit", "200056M");
                header("Content-Type: application/vnd.ms-excel");
                header("Cache-control: private");
                header("Pragma: public");
                Excel::create('records', function ($excel) use ($request) {
                    $excel->sheet('Sheet1', function ($sheet) use ($request) {
				
                      $date = $request->fromdate;
						$employees= Report::groupBy('user_id')
						->orderBy('id', 'DESC')
						->whereDate('created_at','=',$date)
						->where('status_id',4)
						->selectRaw('*, count(amount) as transection, sum(amount) as t_amount')
						->get();	
						 $arr = array();
							foreach ($employees as $employee){
                            $data = array($employee->user->id,$employee->user->name,$employee->created_at,$employee->t_amount);
                            array_push($arr, $data);
                        }
                        //set the titles
                        $sheet->fromArray($arr, null, 'A1', false, false)->prependRow(array(
                                'ID','Name','Date & time','Total'
                            )
                        );
                    });
                })->export('csv');
	
	}
	}
	
	}
	public function export_member(Request $request)
	{
	 ini_set("memory_limit", "200056M");
     $e_member =   header("Content-Type: application/vnd.ms-excel");
     header("Cache-control: private");
     header("Pragma: public");
     $e_member =  Excel::create('records', function ($excel) use ($request) {
            $excel->sheet('Sheet1', function ($sheet) use ($request) {
                $employees = User::orderBy('id', 'DESC')
                    ->orderBy('id', 'DESC')
                    ->get();
                $arr = array();
                foreach ($employees as $employee) {
                    $data = array($employee->parent_id, $employee->id, $employee->name.'('.$employee->id.')', $employee->mobile, $employee->email, $employee->member->pan_number, $employee->member->company, $employee->role_id, $employee->balance->user_balance, $employee->balance->user_commission, $employee->agentcode, $employee->upscheme->scheme, $employee->member->address,$employee->member->office_address, $employee->kyc, $employee->member->pin_code, $employee->created_at, $employee->status->status);
                    array_push($arr, $data);
                }

                //set the titles
                $sheet->fromArray($arr, null, 'A1', false, false)->prependRow(array(
                        'Parent_id', 'Id', 'Name', 'Mobile', 'Email', 'Pan Card', 'Company Name', 'Role', 'Balance', 'recharge Balance', 'agent code', 'scheme', 'Address', 'office_address', 'Kyc', 'Pin code', 'Date & Time', 'Status'
                    )

                );

            });
        })->store('csv', storage_path('images'));
	   
	}
	
	public function schedule(Schedule $schedule)
	{
		 $shedule->command('export_member')
		  ->saturdays()
		 ->at('4:03');
	
	}
	public function wallet_manage(Request $request)
    {
        if (Auth::user()->role_id == 1 && Auth::user()->company_id == 3 && Auth::id()==1) {
            $wallet_manage = DB::table('txnmanages')->orderBy('id', 'ASC')->get();
          return view('admin.walletmanage', compact('wallet_manage'));

          }
      else
      {
         return "Not permission,kindly contact to Admin!";
      }

    }
    public function wallets_success(Request $request)
   {
       if(Auth::user()->role_id==1 || Auth::user()->role_id==19)
    	 {
    		$id = $request->id;
    		$api_id = $request->api_id;
    		$up_down = $request->up_down;
    		if($api_id==16 && $up_down==1)
    		{
    			$data = DB::table('txnmanages')->where('id',$id)->update(['ShighrapayActive'=>1,'PaytmActive'=>0]);
    			if($data)
    			{
    				return "Succesfully Updated";
    			}
    			else
    			{
    				return "Not Updated,try again!";
    			}

    		}
    		elseif($api_id==17 && $up_down==1)
    		{
    			$data = DB::table('txnmanages')->where('id',$id)->update(['ShighrapayActive'=>0,'PaytmActive'=>1]);
    			if($data)
    			{
    				return "Succesfully Updated";
    			}
    			else
    			{
    				return "Not Updated,try again!";
    			}
    		}
    		
    		else
    		{
    			return "Something went wrong,Please try again!";
    		}
    		
    	}
    	else
    	{
    	return "You have nor permission to access this page!";
    	}
    }
     public function wallets_on_off(Request $request)
   {
       if(Auth::user()->role_id==1)
    	 {
    		$id = $request->id;
    		$api_id = $request->api_id;
    		$on_off = $request->on_off;
    		if($api_id==16 && $on_off==1)
    		{
    			$data = DB::table('txnmanages')->where('id',1)->update(['shighra_on'=>1]);
    			if($data)
    			{
    				return "Succesfully Updated";
    			}
    			else
    			{
    				return "Not Updated,try again!";
    			}

    		}
    		elseif($api_id==16 && $on_off==0)
    		{
    			$data = DB::table('txnmanages')->where('id',$id)->update(['shighra_on'=>0]);
    			if($data)
    			{
    				return "Succesfully Updated";
    			}
    			else
    			{
    				return "Not Updated,try again!";
    			}

    		}
    		elseif($api_id==17 && $on_off==1)
    		{
    			$data = DB::table('txnmanages')->where('id',$id)->update(['paytm_on'=>1]);
    			if($data)
    			{
    				return "Succesfully Updated";
    			}
    			else
    			{
    				return "Not Updated,try again!";
    			}
    		}
    		elseif($api_id==17 && $on_off==0)
    		{
    			$data = DB::table('txnmanages')->where('id',$id)->update(['paytm_on'=>0]);
    			if($data)
    			{
    				return "Succesfully Updated";
    			}
    			else
    			{
    				return "Not Updated,try again!";
    			}
    		}
    		else
    		{
    			return "Something went wrong,Please try again!";
    		}
    		
    	}
    	else
    	{
    	return "You have nor permission to access this page!";
    	}
    }

    public function api_manage(Request $request)
    {
    	if(Auth::user()->role_id==1 || Auth::user()->role_id==19)
    	{
			$user = User::find(Auth::id());
    		$getalluser  = User::orderBy('id','DESC')->where('role_id',7)->get();
    		return view('admin.apimanage',compact('getalluser','user'));
    	}
    	else
    	{
    		return "You have not permission to access this page";
    	}
    }
	public function getApiUserIps(Request $request,$apiUserId)
    {
		if(Auth::user()->role_id==1 || Auth::user()->role_id==19)
    	{
    		$user_detail=Member::where('user_id',$apiUserId)->select('user_id','server_ip','server_ip_second')->first();
			if($user_detail)
			{
				return Response::json(['status'=>1,'user_id'=>$user_detail->user_id,'server_ip'=>$user_detail->server_ip,'server_ip_second'=>$user_detail->server_ip_second]);
			}
			else
				return Response::json(['status'=>0,'message'=>"No User Found"]);
    	}
    	else
    	{
    		return Response::json(['message'=>'You have not permission to access']);
    	}
    }
    function saveUserDetail(Request $request)
    {
		
		$rules = array( 
			'server_ip' => 'required|regex:/^[0-9.]+$/',
			//'server_ip_second' => 'regex:/^[0-9.]+$/',
		);
		$validator = Validator::make($request->all(), $rules);
		if ($validator->fails()){
		  return Response::json(['status'=>0,'message'=>$validator->errors()->getMessages()]);
		}
    	$id = $request->user_id;
    	$server_ip = $request->server_ip;
    	$server_ip_second = $request->server_ip_second;
    	//$txn_charge = $request->txncharge;
    	//$verify_charge = $request->verfycharge;
    	$updateIP = Member::where('user_id',$id)->update(['server_ip'=>$server_ip,'server_ip_second'=>$server_ip_second]);
    	if($updateIP)
    	{
    		return Response::json(['message'=>'Succesfully updated']);
    	}
    	else
    	{
    		return Response::json(['message'=>'Something went wrong']);
    	}
    }
    public function get_user_detail(Request $request)
    {
		$rules = array(
			'server_ip' => 'required|regex:/^[0-9.]+$/',
			'server_ip_second' => 'required|regex:/^[0-9.]+$/',
		);
		$validator = Validator::make($request->all(), $rules);
		if ($validator->fails()){
		  return Response::json(['status'=>0,'message'=>$validator->errors()->getMessages()]);
		}
    	if(Auth::user()->role_id==1)
    	{
    		$user_detail = User::where('id',$request->id)->select('id','server_ip','txn_charge','verify_charge' ,'server_ip_second')->first();
    		return Response::json(['message'=>'success','user_id'=>$user_detail->id,'server_ip'=>$user_detail->server_ip,'txn_charge'=>$user_detail->txn_charge,'verify_charge'=>$user_detail->verify_charge]);

    	}
    	else
    	{
    		return Response::json(['message'=>'You have not permission to access']);
    	}
    }
    function save_get_user_detail(Request $request)
    {
    	$id = $request->user_id;
    	$server_ip = $request->server_ip;
    	$txn_charge = $request->txncharge;
    	$verify_charge = $request->verfycharge;
    	$update_charge = User::where('id',$id)->update(['server_ip'=>$server_ip,'txn_charge'=>$txn_charge,'verify_charge'=>$verify_charge]);

    	if($update_charge)
    	{
    		return Response::json(['message'=>'Succesfully updated']);
    	}
    	else
    	{
    		return Response::json(['message'=>'Something went wrong']);
    	}
    }
	public function verificationRechargeUpDown(Request $request)
	{
		/* return Response::json(['message'=>'Now, this is under process']);
		die; */
		if(Auth::user()->id==1)
		{
			$status=$request->status;
			if($request->api_type=="FINO")
			{
				$data = DB::table('txnmanages')->where('id',1)->update(['fino_verify_on'=>$status]);
			}
			else if($request->api_type=="PTM")
			{
				$data = DB::table('txnmanages')->where('id',1)->update(['paytm_verify_on'=>$status]);
				
			}
			else if($request->api_type =="RECHARGE")
			{
				$data = DB::table('txnmanages')->where('id',1)->update(['recharge'=>$status]);
			}
			else
			{
				return Response::json(['message'=>'You do not have permission']);
			}
			return Response::json(['message'=>'Updation Succesfully']);
		}
		return Response::json(['message'=>'You do not have permission']);
		
	}
	public function verificationSecurityPin(Request $request)
	{
		
		if($request->type == "SCHEME" && ($request->pin == Auth::user()->profile->scheme_pin))
		{
			return response()->json(['status'=>1,'message'=>"Verification pin verified"]);
		}
		elseif($request->type == "PROFILE_UPDATE" && ($request->pin == Auth::user()->profile->profile_pin))
		{
			return response()->json(['status'=>1,'message'=>"Profile Pin Verified"]);
		}
		elseif($request->type == "DT" && ($request->pin == Auth::user()->profile->txn_pin))
		{
			return response()->json(['status'=>1,'message'=>"Fund Transfer pin verified"]);
		}
		elseif($request->type == "TXN" && ($request->pin == Auth::user()->profile->txn_pin))
		{
			return response()->json(['status'=>1,'message'=>"Txn pin verified"]);
		}
		return response()->json(['status'=>0,'message'=>$request->type ." Invalid Pin"]);
			
	}
	public function systemSecurityUpdate(Request $request)
	{
		
		$userDetails=User::find(Auth::id());
		$fieldName=$request->field_name;
		if($userDetails->profile()->update(["$fieldName"=>$request->status]))
			return response()->json(['status'=>1,'message'=>"Successfully updated"]);
		return response()->json(['status'=>0,'message'=>"Updation Failed"]);
	
	}
	
}