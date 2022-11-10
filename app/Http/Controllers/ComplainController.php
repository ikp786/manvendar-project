<?php

namespace App\Http\Controllers;

use App\Provider;
use App\Bankdetail;
use App\Company;
use App\Upscheme;
use App\Pmethod;
use App\Http\Requests;
use App\User;
use App\Apiresponse;
use App\Balance;
use App\Report;
use App\Loan;
use Illuminate\Support\Facades\DB;
use App\Loadcash;
use App\Circle;
use App\Netbank;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Validator;
use Response;
use App\Masterbank;
use App\Complain;
use App\Enquiryview;
use App\Enquiry;

class ComplainController extends Controller
{
    
    
    public function checkComplainStatus(Request $request)
	{  
	    if(Auth::user()->role_id == 1)
		{
	        $complainCount = Complain::where('status_id',3)->groupBy('status_id')->count();
		}else{ 
		    $complainCount = Complain::where('user_id',Auth::id())->where('status_id', '!=' , 3)->where('read_status_by_retailer', '=' , 0)->count(); 
		}
		
		return $complainCount;
	}
	
 
    public function complain_request_view(Request $request)
    {
		$complainDetailsQuery = Complain::orderBy('id','desc');
		if($request->fromdate  !=''){
		$start_date = ($request->fromdate) ? $request->fromdate ." 00:00:00" : date("Y-m-d") . " 00:00:00";
		$end_date = ($request->todate) ? $request->todate ." 23:59:59" : date("Y-m-d H:i:s");
		$start_date = date("Y-m-d H:i:s", strtotime($start_date));
		$end_date = date("Y-m-d H:i:s", strtotime($end_date));
		$complainDetailsQuery->whereBetween('created_at', [$start_date,$end_date]);
	   }
	   if($request->searchOf !='')
		   $complainDetailsQuery->where('status_id',$request->searchOf);
		if(Auth::user()->role_id == 1)
		{
			$complainDetails = $complainDetailsQuery->simplePaginate(40);
		}
		elseif(Auth::user()->role_id == 3){
			$members = $this->getMdMember();
			$complainDetailsQuery->whereIn('user_id',$members);
			$complainDetails = $complainDetailsQuery->simplePaginate(40);
			
		}
		elseif(Auth::user()->role_id == 4)
		{
			$members = $this->getDistMember();
			$complainDetailsQuery->whereIn('user_id',$members);
			$complainDetails = $complainDetailsQuery->simplePaginate(40);
		}
		else{
			$members = array(Auth::id());
			$complainDetailsQuery->whereIn('user_id',$members);
			$complainDetails = $complainDetailsQuery->simplePaginate(40);
		}
		$title="Company View";
		if(in_array(Auth::user()->role_id,array(1,3,4))){
			return view('admin.complain_request_view', compact('complainDetails','title'));
		}
		else{
		    $complainDetails2 = Complain::where('user_id',Auth::id())->where('status_id', '!=' , 3)->where('looking_by', 1)->get(); 
		    foreach($complainDetails2 as $value)
		    {    
                $Complain = array(
                    "read_status_by_retailer"=> '1',
                );
                Complain::where('id', $value->id)->update($Complain);
		    }
			return view('reports.complain-view', compact('complainDetails','title'));
		}
    }
	
	public function complain()
    {
		if(Auth::user()->role_id==5)
		{
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
		   $l_user = Auth::user()->name;
			$complain = Complain::orderBy('com_id','DESC')->where('name',$l_user)->get();
			return view('reports.complain', compact('complain'));
		}
		return view('errors.premission-denied');
    }
	public function complain_req(Request $request)
	{
			$rules = array('amount' => 'required',
				'product' => 'required',
				'issue_type' => 'required',
				'txn_id' => 'required',
				'bank_ac' => 'required',
				'remark' => 'required'
				); 
			$validator = Validator::make($request->all(),$rules); 
			
			$now = new \DateTime();
			$datetime = date('d-m-y');
			$amount =  $request->input('amount');
			$date =    $request->input('dod');
			$newDate = date("Y-m-d", strtotime($date));
			$product = $request->input('product');
			$issue  =  $request->input('issue_type');
			$txn_id  = $request->input('txn');
			$acount  = $request->input('acno');
			$remark  = $request->input('remark');
			$complain = ['user_id'=> Auth::getUser()->id,'name' => Auth::user()->name,'created_at' =>$datetime,'product'=>$product,'issue_type'=>$issue,'date_txn'=>$newDate,'txn_id'=>$txn_id,'bank_ac'=>$acount,'amount'=>$amount,'remark'=>$remark,'status' =>'Pending'];
			  Complain::create($complain);
		  
			return Redirect::action('ComplainController@complain');  
		
	}
	

	public function store_complain_req(Request $request)
	{ 
	    //dd($request->all());die();
		$c_detail = Complain::where('txn_id',$request->complainTxnId)->whereIn('status_id',[3,9,31])->first();
        if($c_detail)
        {
			return Response::json(['status'=>0,'message'=>'Company alreday raised for this txn. Please Check Complain Report']);
        }
		$complainTxnId  = $request->complainTxnId;
		$issueType  = $request->issueType;
		$complainRemark  = $request->complainRemark;
		$complain = ['user_id'=> Auth::id(),'issue_type'=>$issueType,'txn_id'=>$complainTxnId,'remark'=>$complainRemark,];
		Complain::create($complain);
		  if($complain)
		  {
			return Response::json(['status'=>1,'message'=>'Complain Succesfully Submited']);
		  }  
	}
	
	 public function update(Request $request)
    {
		$complain = Complain::find($request->complainId);
		$complain->current_status_remark = $request->current_status_remark;
		$complain->status_id = $request->status_id;
		if($request->status_id==30){
			$complain->approved_by = Auth::id();
			$complain->approved_date = date("Y-m-d H:i:s");
		}
		$complain->looking_by = Auth::id();
		$complain->save();
		return response()->json(['status'=>1,'message'=>"Complin Updated Succesfully"]);		
    }
	public function delete_req(Request $request)
	{
		  $id =   $request->del_id;
		  $delete = DB::table('complains')
            ->where('com_id', $id)
            ->delete();
            if($delete)
            {
            	return "Record Deleted Succesfully";

            }
	}
	public function agentUpdate(Request $request)
	{
		$a_remark = $request->a_rem;
		$co_id =    $request->co_id;
		$u_record = DB::table('complains')
            ->where('com_id', $co_id)
            ->update(array('remark' => $a_remark,'status'=>'Pending'));
            if($u_record){ return "successfully Updated!"; } else { return "Somthing is Wrong!"; }
	}

	public function agentDelete(Request $request)
	{

		$co_id =    $request->co_id;
		$delete = DB::table('complains')
            ->where('com_id', $co_id)
            ->delete();
            if($delete)
            {
            	return "Record Deleted Succesfully";

            }
	}
	public function enquiries()
	{
		$enquiry = Enquiry::orderBy('id','DESC')->get();
        return view('admin.enquiry',compact('enquiry'));

	}

	function conversation_view(Request $request)
	{
		$conversation_view = Enquiryview::where('conv_id',$request->id)->get();
		 $response["data"] = array();
        foreach($conversation_view as $conversation_views)
        {
        	$data['message'] = $conversation_views['message'];
        	$data['name'] = $conversation_views['name'];
        	$data['created_at'] = $conversation_views['created_at'];
        	 
           array_push($response["data"], $data);
        }
        header('Access-Control-Allow-Origin: *');
            header('content-type: application/json; charset=utf-8');
            return json_encode($response);
	}

	public function bank_acc_digit(Request $request)
    {
     $bank_digit =  DB::table('masterbanks')->where('bank_name',$request->bankname)->first();
        return $bank_digit->account_digit;
    }

    public function update_enquiries(Request $requiest)
    {
    	DB::table('enquiries')
            ->where('id', $requiest->id)
            ->update(array('message' => $requiest->message,'status' => $requiest->status,'assigned'=>$requiest->assign));
            return $requiest->status;
    }

    public function view_comission()
    {
    	$view_commision = DB::table('recharge_comissions')->paginate(40);
         return view('report.view_comission',compact('view_commision'));
    }

    public function conversation_update(Request $request)
    {
        $conv_id = $request->conv_id;

        $msg = $request->msg;

        $name = $request->assign;

        $insert_conv = Enquiryview::create(['conv_id'=>$conv_id,'name'=>$name,'message'=>$msg,'status'=>1]);

        if($insert_conv)
        {
            return array('conv_id'=>$conv_id,'name'=>'','message'=>$msg,'status'=>1);
        }
        else return "Somthing is wrong to insert data!";
    }
    
    public function view_loan()
    {
        $view_loan = Loan::all();
        return view('admin.viewloan',compact('view_loan'));

    }
}