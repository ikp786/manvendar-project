<?php

namespace App\Http\Controllers;

use App\User;
use Excel;
use PDF;
use Illuminate\Http\Request;
use App\Report;
use DB;
use App\Company;
use App\Balance;
use Carbon\Carbon;
use App\Http\Requests;
use App\Loadcash;
use App\Pmethod;
use App\Netbank;
use App\Refundrequest;
use App\Provider;
use App\YesBankResponse;
use App\Remitteregister;
use App\ActionOtpVerification;
use App\SendSMS;
use Response;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Exports\ReportExport;
use App\Exports\LedgerReportExport;
use App\Exports\DmtExport;
use App\Exports\UserReportExport;
use App\Exports\AccountReportExport;
use App\Exports\TXNWithCommissionExport;
use App\Exports\PaymentReportExport;
use App\Exports\DistPaymentReportExport;
use App\Exports\PaymentRequestReportExport;
use App\Exports\AepsExport;
use App\Exports\OfflineExport;
use App\Exports\OfflineRecordExport;
use App\Exports\TDSReportExport;
use App\Exports\DTReportExport;
use App\Beneficiary;
use App\Apiresponse; 
use Exception;
use App\Traits\CustomTraits;
use App\Exports\AepsSettlementExport;
class ReportController extends Controller {

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
	use CustomTraits;
	
    public function index() 
	{
		if(Auth::user()->role_id==5)
		{
			$recharge_report = Report::where('user_id', Auth::id())
                ->orderBy('id', 'DESC')
                ->orderBy('created_at', 'DESC')
                ->where('status_id','!=',14)
                ->paginate(30);
			return view('reports.account_statement_report', compact('recharge_report'));
		}
		return view('errors.page-not-found');
    }
	public function tdsReport(Request $request) 
    {
		if(Auth::user()->role_id ==1 || Auth::user()->role_id == 19)
		{
			//print_r($request->all());die;
			$reportQuery = Report::selectRaw('user_id, sum(tds)as tds,sum(credit_charge) as commission, sum(debit_charge) as service_charge,sum(amount) as txn_value')->groupBy('user_id')->whereIn('status_id',[1,22,16]);
			if($request->fromdate !='')
			{
				$start_date =($request->fromdate) ? $request->fromdate ." 00:00:00" : date("Y-m-d") . " 00:00:00";
				$end_date = ($request->todate) ? $request->todate ." 23:59:59" : date("Y-m-d H:i:s");
				$start_date = date("Y-m-d H:i:s", strtotime($start_date));
				$end_date = date("Y-m-d H:i:s", strtotime($end_date));
				$reportQuery->whereBetween('created_at', [$start_date,$end_date]);
			}
			if($request->export=="EXPORT")
				return Excel::download(new TDSReportExport($request->user,$start_date,$end_date), 'TDSReport.xlsx');
			if($request->user !='')
				$reportQuery->where('user_id',$request->user);

			if($request->type == 1){
				$reportQuery->groupBy('user_id');
			}
			if($request->type == 2){
				$reportQuery->groupBy('user_id');
				//$reportQuery->groupBy('api_id');
			}
	        
			$reports = $reportQuery->simplePaginate(50);
			$users =User::pluck('name','id')->toArray();
			return view('admin.report.tds_report',compact('reports','users'));
		}
    } 
    public function all_recharge_report(Request $request) 
    {
		/* 
		DB::enableQueryLog(); */
		$reportQuery = Report::orderBy('id', 'DESC');
	 
		if($request->searchOf !='')
		{
			if($request->searchOf==100)
				$reportQuery->where('txn_status_type',"MANUAL_SUCCESS");
			else if($request->searchOf==101)
				$reportQuery->where('txn_status_type',"MANUAL_FAIL");
			else
				$reportQuery->where('status_id',$request->searchOf);
		}
		if($request->mode !='')
		{       $reportQuery->where('mode',$request->mode);
		}
		if($request->product !='')
		{
			if($request->product==1)
			{
				$reportQuery->whereIn('api_id',[1,8,10]);
				$reportQuery->where('recharge_type',1);
			}else if($request->product==25)
			{ 
				$reportQuery->where('api_id',25);
			} else
			{ 
			    $reportQuery->where('api_id',$request->product);
			}
			
		}
	 
		if($request->fromdate !='')
		{
			$start_date = ($request->fromdate) ? $request->fromdate ." 00:00:00" : date("Y-m-d") . " 00:00:00";
			$end_date = ($request->todate) ? $request->todate ." 23:59:59" : date("Y-m-d H:i:s");
			$start_date = date("Y-m-d H:i:s", strtotime($start_date));
			$end_date = date("Y-m-d H:i:s", strtotime($end_date));
			$reportQuery->whereBetween('created_at', [$start_date,$end_date]);
		}
		if(Auth::user()->role_id ==1 || Auth::user()->role_id == 19)
		{
			$members=array();
		}
		elseif (Auth::user()->role_id == 3) 
        {
            $parent_details = User::where(['parent_id'=>Auth::id(),'role_id'=>4])->pluck('id','id')->toArray();
            $members=User::whereIn('parent_id',$parent_details)->orWhere('parent_id',Auth::id())->orWhere('id',Auth::id())->pluck('id','id')->toArray();
			$reportQuery->whereIn('user_id', $members);
        } 
        elseif (Auth::user()->role_id == 4) 
        { 
            $members = User::where('parent_id', Auth::id())->orWhere('id',Auth::id())->pluck('id','id')->toArray();
			$reportQuery->whereIn('user_id', $members);
        } 
        else
        { 
			//$members=array('user_id',Auth::id());
			$members=array(Auth::id());
            $reportQuery->where('user_id', Auth::id());
        }
		if($request->amount !='')
			$reportQuery->where('amount', '=', $request->amount);
		 if($request->user !='')
			$reportQuery->where('user_id',$request->user);
		if($request->number !='')
		{
			$number = $request->number;
			if(Auth::user()->role_id != 1)
			{
				$number = $request->number;
				$reportQuery->where(function ($query) use($members,$number) {
				$query->where('id', '=', $number)
						->orWhere('number', '=', $number)
						->orWhere('txnid', '=', $number)
						->orWhere('customer_number', '=', $number)
						->orWhere('client_ackno', '=', $number);
						
				}); 
			}
			else{
			    /*
    				$reportQuery->where('id', '=', $request->number);
    				$reportQuery->orWhere('id', '=', $request->number);
    				$reportQuery->orWhere('number', '=', $request->number);
    				$reportQuery->orWhere('txnid', '=', $request->number);
    				$reportQuery->orWhere('customer_number', '=', $request->number);
    				$reportQuery->orWhere('client_ackno', '=', $request->number);
				*/
				
				$reportQuery->where(function ($query) use($members,$number) {
				$query->where('id', '=', $number)
						->orWhere('number', '=', $number)
						->orWhere('txnid', '=', $number)
						->orWhere('customer_number', '=', $number)
						->orWhere('client_ackno', '=', $number);
						
				});
			}
		}
		
	    $reports = $reportQuery->simplePaginate(500);
	    	
		if($request->pdf=="PDF"){
		
		       $pdf = PDF::loadView('profile.download-pdf-report', compact('reports'));
    		       
    		       // return $pdf->stream();
                   // return $pdf->download('LedgerReport.pdf');  
                   
               return view('profile.download-pdf-report', compact('reports'));
             
		}
		if($request->csv=="CSV"){
		
            		 $headers = array(
                        "Content-type" => "text/csv",
                        "Content-Disposition" => "attachment; filename=Reports.csv",
                        "Pragma" => "no-cache",
                        "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
                        "Expires" => "0"
                    );
                
                    
                    $columns = array('Date/Time','ID','Remitter Number','Acc/Mob/K Number/Bene Name','Bank Name/IFSC','Operator Txn Id/Remark','Amount',
            				'Web/App','Status','Bank RR Number','description','Credit/Debit','Opening Bal','Credit Amount','Debit Amount','TDS','Service Tax','Balance','Txn Type','fund Transfer');
                
                    $callback = function() use ($reports, $columns)
                    {
                        $file = fopen('php://output', 'w');
                        fputcsv($file, $columns);
                        $arr = array();
                        foreach($reports as $value) {
                            
                                $s = $value->created_at;
                                $dt = new \DateTime($s);
                                
                                $bene ='';
                                if(in_array($value->api_id,array(2,10)))
                                {
                                    $content = explode("(",$value->description);
                                    try{
                                       $bene =$value->description; 
                                    }
                                    catch(\Exception $e)
                                    {
                                        $bene =$value->description;
                                    }
                                }else{
                                 $bene = @$value->beneficiary->bank_name.' / '.@$value->beneficiary->ifsc;
                                } 
                                if($value->api_id=='25'){ $ttxn = $value->paytm_txn_id; }
                                else {  $ttxn = $value->txnid; }
                                 
                               $nft= '';
                               if($value->recharge_type==0 && $value->txnid !="DT" && !in_array($value->api_id,array(2,10))) 
                               { $nft= ($value->channel==2)?"IMPS":"NEFT";
                               }
                                
            					if($value->recharge_type== 1)
                                {   
                                    $provd = @$value->provider->provider_name; 
                                }else
                                { $provd = @$value->api->api_name;
            				    }
            				    
                                $data = array(
                             		$dt->format('d-m-y H:i:s'),
                            	    $value->id, 
                            		$value->customer_number, 
                            		$value->number.' '.@$value->biller_name.' '.@$value->beneficiary->name,
                            		@$bene,
                            		@$ttxn.' '.@$value->remark,
                            		@$value->amount,
                            		@$value->mode,
                            		@$value->status->status.' '.$value->txn_initiated_date.' '.$nft,
                            		@$value->bank_ref,
                            		@$value->description, 
                            		@$value->type,
                            		@$value->opening_balance,
                            		@$value->credit_charge,
                            		@$value->debit_charge,
                            		$value->tds,
                            		@$value->gst,
                            		@$value->total_balance,
                            		@$value->txn_type, 
                            		$value->description
                            );
                            fputcsv($file, $data);
                        }
                        fclose($file);
                    };

                    return Response::stream($callback, 200, $headers);
		}
			
        if($request->export=="EXPORT")
			return Excel::download(new ReportExport($members,$start_date,$end_date,$request->user), 'All-Transaction.xlsx');
		if($request->aeps=="EXPORT")
			return Excel::download(new AepsExport($members,$start_date,$end_date,$request->user),'All-Aeps.xlsx');
		//$users =User::pluck('name','id')->toArray();
		$userDetails = User::selectRaw('name,mobile,id,prefix')->get();
		$users=array();
		foreach($userDetails as $user)
		{
			$users[$user->id]=$user->member->company.' '.$user->prefix.'-'.$user->id.'(' .$user->mobile .')';
			//$users[$user->id]=$user->name.'('.$user->prefix.'-'.$user->id.') '.$user->mobile;
		}
		$otpVerifications=ActionOtpVerification::orderBy('id','ASC')->get();
	
	 
		if(in_array(Auth::user()->role_id,array(1,3,4)))
			return view('admin.all_recharge_report', compact('reports','users','otpVerifications'));
		else
		{
			$recharge_report = $reports;
			return view('reports.account_statement_report', compact('reports'));
		} 
    } 
	
	public function generateOTP(Request $request)
	{
		$mobile=$request->mobile;
		$Id=$request->recordId;
		$digits = 6;
		$otp = rand(pow(10, $digits-1), pow(10, $digits)-1);
		$value=ActionOtpVerification::where('mobile',$mobile)->update(['otp'=>$otp,'txnid'=>$Id,'otp_verified'=>0]);
		
		$msg = "Dear Admin Use this otp $otp to fail Transaction Id $Id";
		$message = urlencode($msg);
		CustomTraits::sendSMS($mobile, $message,1);
		return response()->json(['status_id'=>1,'message'=>"OTP has been sent registered mobile number"]);
	}

	public function verifyTxnOTP(Request $request)
	{
      $txnOtp=$request->otp;
      $mobile=$request->mobile;
      $id=$request->recordId;
      $verifyOTP=ActionOtpVerification::where('mobile',$mobile)->where('txnid',$id)->first();
      if($request->otp != $verifyOTP->otp)
	    {
			
	        return response()->json(['status_id'=>3, 'message'=> "OTP Wrong"]);
	    }
     else
	 {		$verifyOTP->otp_verified = 1;
	        $verifyOTP->otp='';
	        $verifyOTP->txnid=$id;
	        $verifyOTP->save();
	        return response()->json(['status_id'=>1,'message'=> "OTP Verified Successfully"]);
        }
	}
	
	 public function admin_money_transfer_report(Request $request) {
       
        $SessionID = '';
		/* DB::enableQueryLog(); */
		$start_date = ($request->fromdate) ? $request->fromdate ." 00:00:00" : date("Y-m-d") . " 00:00:00";
		$end_date = ($request->todate) ? $request->todate ." 23:59:59" : date("Y-m-d H:i:s");
		$start_date = date("Y-m-d H:i:s", strtotime($start_date));
		$end_date = date("Y-m-d H:i:s", strtotime($end_date));
		$reportQuery = Report::orderBy('id', 'DESC');
		$reportQuery->where('recharge_type',0);
		if($request->searchOf !='')
			$reportQuery->where('status_id', '=', $request->searchOf);
		/* else
			$reportQuery->whereIn('status_id',[1,2,3,4,20,21,24]); */
		$reportQuery->whereBetween('created_at', [$start_date,$end_date]);
        if (Auth::user()->role_id == 3) 
		{
			$members =$this->getMdMember(Auth::id());
			$reportQuery->whereIn('user_id', $members);
        }
        elseif(Auth::user()->role_id == 4) 
		{
            $members = $this->getDistMember(Auth::id());
            $reportQuery->whereIn('user_id', $members);
        }
        elseif (in_array(Auth::user()->role_id,array(1,11,12,14,19))) {
			$members=array();
        }
        else
		{
			$reportQuery->where('user_id', Auth::id());
			$members=array(Auth::id());
           $reports = $reportQuery->Paginate(50);
        }
		if($request->export=="EXPORT")
		{
			return Excel::download(new DmtExport($members,$start_date,$end_date), 'Usage.xlsx');
		}
        if($request->amount !='')
			$reportQuery->where('amount', '=', $request->amount);
		if($request->product !='')
			$reportQuery->where('api_id', '=', $request->product);
		if($request->number !='')
		{
			$number = $request->number;
			if(Auth::user()->role_id != 1)
			{

				$number = $request->number;
				$reportQuery->where(function ($query) use($members,$number) {
				$query->where('id', '=', $number)
						->orWhere('number', '=', $number)
						->orWhere('txnid', '=', $number)
						->orWhere('customer_number', '=', $number);
				});
			}
			else{
			
				$reportQuery->where('id', '=', $request->number);
				$reportQuery->orWhere('number', '=', $request->number);
				$reportQuery->orWhere('txnid', '=', $request->number);
				$reportQuery ->orWhere('customer_number', '=', $request->number);
			}
		}
		
		$reports=$reportQuery->Paginate(50);
		//dd(DB::getQueryLog());
        return view('admin.report.money_transfer_report', compact('reports'))->with('sessionid', $SessionID);
    }
	
	public function rechargeReport(Request $request) {
       
        $rechargeReports = Report::orderBy('id','desc');
		$rechargeReports->where('recharge_type', 1);
        if (in_array(Auth::user()->role_id,array(1,11,12,14,19))) 
		{
           $members=array();
        } 
		elseif (Auth::user()->role_id == 3) 
		{
            $members =$this->getMdMember(Auth::id());
			$rechargeReports->whereIn('user_id', $members);                    
        } 
		elseif (Auth::user()->role_id == 4) 
		{
            $members = $this->getDistMember(Auth::id());
            $rechargeReports->whereIn('user_id', $members);         
        } 
		else 
		{
			$members = array(Auth::id());
			$rechargeReports->where('user_id', Auth::id());
        }
		if($request->number !='')
		{
			$number = $request->number;
			if(Auth::user()->role_id != 1)
			{
				$number = $request->number;
				$rechargeReports->where(function ($query) use($members,$number) {
				$query->where('id', '=', $number)
						->orWhere('number', '=', $number)
						->orWhere('txnid', '=', $number)
						->orWhere('customer_number', '=', $number);

				});
			}
			else{
			
				$rechargeReports->where('id', '=', $request->number);
				$rechargeReports->orWhere('number', '=', $request->number);
				$rechargeReports->orWhere('txnid', '=', $request->number);
				$rechargeReports ->orWhere('customer_number', '=', $request->number);
			}
		}
		if($request->user !='')
			$rechargeReports->where('user_id',$request->user);
		
		if($request->product !='')
			$rechargeReports->where('api_id',$request->product);
		
		$reports =$rechargeReports->simplePaginate(40);
		$users =User::pluck('name','id')->toArray();
		if(in_array(Auth::user()->role_id,array(1,3,4,19)))
			return view('admin.recharge-report', compact('reports','users'));
		else
			return view('report.all-recharge-report',compact('reports'));
        exit;
    }
       public function all_recharge_transaction(Request $request)
       {
            if(Auth::user()->role_id==5)
            {
                 $recharge_report = Report::orderBy('id', 'DESC')
                    //->where('created_at', '>=', Carbon::now()->subWeek())
                    ->where('user_id', Auth::id())
                    ->where('recharge_type',1)
                    ->orderBy('id', 'DESC')
                    ->Paginate(40);
                return view('reports.all-recharge-report',compact('recharge_report'));
            }
            else
            {
                return "Not permission";
            }

       }
	public function apiReport(Request $request)
	{
		if(Auth::user()->role_id == 1 || Auth::user()->role_id == 19)
		{
			$start_date = ($request->fromdate) ? $request->fromdate ." 00:00:00" : date("Y-m-d") . " 00:00:00";
			$end_date = ($request->todate) ? $request->todate ." 23:59:59" : date("Y-m-d H:i:s");
			 /*  $start_date = date("Y-m-d H:i:s", strtotime($start_date));
			$end_date = date("Y-m-d H:i:s", strtotime($end_date)); */
			$dateFormat = $this->getDbFromatDate($start_date, $end_date);
			//print_r($dateFormat);
			$reportsQuery = Report::selectRaw('sum(amount) as totalVolume, status_id,api_id ,count(id) as txnCount');
			$reportsQuery->whereBetween('created_at', [$dateFormat['start_date'],$dateFormat['end_date']]);
			$reportsQuery->whereIn('status_id',[1,2,3]);
			
			$reportsQuery->groupBy('status_id','api_id');
			$reports = $reportsQuery->get();
			$results=array();
			foreach($reports as $report)
			{
				$results[$report->api->username][strtoupper($report->status->status)] = $report->txnCount;
			}
			$newArray=array();
			foreach($results as $key=>$value)
			{
				$newArray[$key]['SUCCESS'] = isset($value['SUCCESS']) ? $value['SUCCESS'] : 0;
				$newArray[$key]['PENDING'] = isset($value['PENDING']) ? $value['PENDING'] : 0;
				$newArray[$key]['FAILURE'] = isset($value['FAILURE']) ? $value['FAILURE'] : 0;
				
			}
			$newArray=json_decode(json_encode($newArray));
			
			return view('admin.api-report',compact('newArray'));
		}
		return view('errors.permission-denied');
	}
	public function apiResponse(Request $request)
    {
		if(Auth::user()->role_id ==1 || Auth::user()->role_id == 19){
			
			$apiReportQuery=Apiresponse::orderBy('id','DESC');
			if($request->number)
				$apiReportQuery->where('report_id', $request->number);
			if($request->ajax())
				return response()->json(['status'=>1,'message'=>$apiReportQuery->get()]);
			$apiReport = $apiReportQuery->simplePaginate(30);
			return view('admin.report.apiresponse',compact('apiReport'));
		}
		return view('errors.permission-denied');
    }
	public function operatorReport(Request $request)
	{
		if(Auth::user()->role_id == 1 || Auth::user()->role_id == 19)
		{
			$start_date = ($request->fromdate) ? $request->fromdate ." 00:00:00" : date("Y-m-d") . " 00:00:00";
			$end_date = ($request->todate) ? $request->todate ." 23:59:59" : date("Y-m-d H:i:s");
			$dateFormat = $this->getDbFromatDate($start_date, $end_date);
			//print_r($dateFormat);
			$reportsQuery = Report::selectRaw('sum(amount) as totalVolume, status_id,provider_id');
			$reportsQuery->whereBetween('created_at', [$dateFormat['start_date'],$dateFormat['end_date']]);
			$reportsQuery->whereIn('status_id',[1,2,3,21]);
			$reportsQuery->whereNotIn('provider_id',[0,41]);
			$reportsQuery->groupBy('status_id','provider_id');
			$reports = $reportsQuery->get();
			$results=array();
			foreach($reports as $report)
			{
				$results[$report->provider->provider_name][strtoupper($report->status->status)] = $report->totalVolume;
			}
			$newArray=array();
			foreach($results as $key=>$value)
			{
				$newArray[$key]['SUCCESS'] = isset($value['SUCCESS']) ? $value['SUCCESS'] : 0;
				$newArray[$key]['PENDING'] = isset($value['PENDING']) ? $value['PENDING'] : 0;
				$newArray[$key]['FAILURE'] = isset($value['FAILURE']) ? $value['FAILURE'] : 0;
				$newArray[$key]['MANUALSUCCESS'] = isset($value['MANUALSUCCESS']) ? $value['MANUALSUCCESS'] : 0;
			}
			$title="Operator Report";
			$newArray=json_decode(json_encode($newArray));
			
			return view('admin.operator-wise-report',compact('newArray','title'));
		}
		return view('errors.permission-denied');
	}
	public function txnWithCommission(Request $request)
	{
		if(Auth::user()->role_id == 1 || Auth::user()->role_id == 19)
		{
			$start_date = ($request->fromdate) ? $request->fromdate ." 00:00:00" : date("Y-m-d") . " 00:00:00";
			$end_date = ($request->todate) ? $request->todate ." 23:59:59" : date("Y-m-d H:i:s");
			$dateFormat = $this->getDbFromatDate($start_date, $end_date);
			$reportsQuery = Report::orderBy('id','desc');
			$reportsQuery->whereBetween('created_at', [$dateFormat['start_date'],$dateFormat['end_date']]);
			if($request->searchOf !='')
				$reportsQuery->where('status_id',$request->searchOf);
			else
				$reportsQuery->whereIn('status_id',[1,2,3,21,4,24,18]);
			if($request->product !='')
				$reportsQuery->where('api_id',$request->product);
			if($request->export=="EXPORT")
				return Excel::download(new TXNWithCommissionExport($dateFormat['start_date'],$dateFormat['end_date']), 'Txn-With-Commistion-Report.xlsx');
			$reports = $reportsQuery->simplePaginate();
			return view('admin.txn-with-commission',compact('reports'));
		}
		return view('errors.permission-denied');
	}
    public function payment_request_report() {
       $reports = Report::orderBy('id', 'DESC')
               ->orderBy('id', 'DESC')
                ->paginate(40);
        return view('admin.all_recharge_report', compact('reports'));
    }

    public function user_recharge_report($user_id) 
	{
        if (Auth::user()->role_id <= 4 || in_array(Auth::user()->role_id,array(11,12,14))) 
		{
            $start = new \DateTime('now');
            //$start->modify('first day of this month');
            $end = new \DateTime('now');
            //$end->modify('last day of this month');
            $reports = Report::orderBy('id', 'DESC')
                    ->where('status_id','!=',14)
                    ->where('user_id', $user_id)
                    ->paginate(40);
			$userDetails = User::selectRaw('name,mobile,id,prefix')->get();
			$users=array();
			foreach($userDetails as $user)
			{
				$users[$user->id]=$user->prefix.'-'.$user->id.' '.$user->name.' '.'(' .$user->mobile .')';
			}
			$otpVerifications=ActionOtpVerification::orderBy('id','ASC')->get();
            return view('admin.all_recharge_report', compact('reports','users','otpVerifications'));
        } else {
            return "Not Permission to check this report";
        }
    }
	
	public function mobile_recharge_report($user_id)
	{
		  if (Auth::user()->role_id <= 4) {
            $start = new \DateTime('now');
            //$start->modify('first day of this month');
            $end = new \DateTime('now');
            //$end->modify('last day of this month');
            $reports = Report::orderBy('id', 'DESC')
                    ->where('user_id', $user_id)
					->where('recharge_type',1)
                    ->paginate(500);
            return view('admin.upper_lavel_transaction', compact('reports'));
        } else {
            return "Not Permission to check this report";
        }
	}

    public function searchall(Request $request) {
        if (in_array(Auth::user()->role_id,array(1,11,12,14))) 
		{
            $SessionID = Company::find(3)->sessionid;
            if ($request->export == 'All Txn Reports') {
				set_time_limit(60000);
                ini_set("memory_limit", "200056M");
                header("Content-Type: application/vnd.ms-excel");
                header("Cache-control: private");
                header("Pragma: public");
                
                Excel::create($request->export."-".date("d-m-Y"), function ($excel) use ($request) 
				{
                    $excel->sheet('Sheet1', function ($sheet) use ($request) 
					{
                        $employees = Report::orderBy('id', 'DESC')
                                ->whereDate('created_at', '>=', $request->fromdate)
                                ->whereDate('created_at', '<=', $request->todate)
                                //->where('recharge_type','!=',1)//commented by rajat
                                ->where('status_id','!=',14)
                                //->where('user_id', Auth::id())
                                // ->whereIn('status_id',[1,2,3,9,15])//commented by rajat
                                ->orderBy('id', 'DESC')
                                ->select('id','created_at','txnid','api_id','user_id','number','beneficiary_id','bank_ref','amount','profit','total_balance','status_id')
                                ->take(8000)->get();
                        $arr = array();
                        foreach ($employees as $employee) 
						{
                            $data = array(date_format($employee->created_at,"d-m-Y"),date_format($employee->created_at,"H:i:s"),@$employee->id, @$employee->user->name . '(' . @$employee->user->id . ')', @$employee->api->api_name, @$employee->number, @$employee->txnid, @$employee->amount, @$employee->profit, @$employee->total_balance, @$employee->status->status);
                            array_push($arr, $data);
                        }
                        //set the titles
                        $sheet->fromArray($arr, null, 'A1', false, false)->prependRow(array(
                            'Date' , 'Time','Id', 'Name', 'Product',
                            'Number', 'Reference ID', 'Amount', 'Profit', 'Total', 'Status'
                                )
                        );
						$sheet->prependRow( array('','Records OF' ,$request->export));
						/* $sheet->prependRow( array('','To Date' ,$request->todate));
						$sheet->prependRow( array('','From Date' ,$request->fromdate)); */
							$sheet->row(2, function($row) {
								// call cell manipulation methods
								$row->setBackground('#A9A9A9');
								$row->setFontFamily('Times New Roman');
						});
						
                    });
					
					$excel->sheet('Sheet2', function ($sheet) use ($request) {
                        $employees = Report::orderBy('id', 'DESC')
                            ->whereDate('created_at', '>=', $request->fromdate)
                            ->whereDate('created_at', '<=', $request->todate)
                           // ->where('recharge_type','!=',1)////commented by rajat
                           ->where('status_id','!=',14)
                            //->where('user_id', Auth::id())
                          //   ->whereIn('status_id',[1,2,3,9,15])////commented by rajat
                            ->orderBy('id', 'DESC')
                             ->select('id','created_at','txnid','api_id','user_id','number','beneficiary_id','bank_ref','amount','profit','total_balance','status_id')
                            ->take(8000)->offset(8000)->get();
							
                        $arr1 = array();
                      foreach ($employees as $employee) {
                            $data = array(date_format($employee->created_at,"d-m-Y"),date_format($employee->created_at,"H:i:s"),@$employee->id,@$employee->user->name . '(' . @$employee->user->id . ')',@$employee->api->api_name,@$employee->number,@$employee->txnid, @$employee->amount,@$employee->profit,@$employee->total_balance, @$employee->status->status);
                            array_push($arr1, $data);
                        }
                        //set the titles
                        $sheet->fromArray($arr1, null, 'A1', false, false)->prependRow(array(
                            'Date & Time','Id','Name','Product','Number','Reference ID','Amount','Profit','Total','Status'
                                )
                        );
						$sheet->prependRow( array('','Records OF' ,$request->export));
						/* $sheet->prependRow( array('','To Date' ,$request->todate));
						$sheet->prependRow( array('','From Date' ,$request->fromdate)); */
														$sheet->row(2, function($row) {
								// call cell manipulation methods
								$row->setBackground('#A9A9A9');
								$row->setFontFamily('Times New Roman');
						});
						
                   
						});
						
						$excel->sheet('Sheet3', function ($sheet) use ($request) {
                        $employees = Report::orderBy('id', 'DESC')
                            ->whereDate('created_at', '>=', $request->fromdate)
                            ->whereDate('created_at', '<=', $request->todate)
                          //  ->where('recharge_type','!=',1)
                            ->where('status_id','!=',14)
                            //->where('user_id', Auth::id())
							
                           // ->whereIn('status_id',[1,2,3,9])
                            ->orderBy('id', 'DESC')
                             ->select('id','created_at','txnid','api_id','user_id','number','beneficiary_id','bank_ref','amount','profit','total_balance','status_id')
                            ->take(8000)->offset(16000)->get();
							
                        $arr1 = array();
                      foreach ($employees as $employee) {
                            $data = array(date_format($employee->created_at,"d-m-Y"),date_format($employee->created_at,"H:i:s"),@$employee->id, @$employee->user->name . '(' . @$employee->user->id . ')', @$employee->api->api_name, @$employee->number, @$employee->txnid, @$employee->amount, @$employee->profit, @$employee->total_balance, @$employee->status->status);
                            array_push($arr1, $data);
                        }
                        //set the titles
                        $sheet->fromArray($arr1, null, 'A1', false, false)->prependRow(array('Date' , 'Time',
                            'Id', 'Name',  'Product',
                            'Number', 'Reference ID', 'Amount', 'Profit', 'Total', 'Status'
                                )
                        );
						$sheet->prependRow( array('','Records OF' ,$request->export));
						/* $sheet->prependRow( array('','To Date' ,$request->todate));
						$sheet->prependRow( array('','From Date' ,$request->fromdate)); */
							$sheet->row(2, function($row) {
								// call cell manipulation methods
							$row->setBackground('#A9A9A9');
							$row->setFontFamily('Times New Roman');
						});

						});

                })->export('csv');
            }
			 else {
                $number = $request->number;
                $start = new \DateTime('now');
                $start->modify('first day of this month');
                $end = new \DateTime('now');
                $end->modify('last day of this month'); 
                $reports = Report::orderBy('id', 'DESC')
                        ->where('txnid', $number)
                        ->orWhere('id', $number)
                        ->orWhere('number', $number)
                        ->orWhere('pay_id', $number)
                        ->orWhere('customer_number', $number)
                        ->where('status_id','!=',14)
                        ->select('id','user_id','created_at','api_id','amount','profit','beneficiary_id','recharge_type','status_id','description','total_balance','provider_id','number','customer_number','bank_ref','ackno','txnid')
                        ->paginate(40);
                return view('admin.all_recharge_report', compact('reports'))->with('sessionid', $SessionID);
            }
        } elseif (Auth::user()->role_id == 3) {
            $members = User::where('parent_id', Auth::id())->get();
            $member_id = array();
            foreach ($members as $member) {
                $member_id[] = $member->id;
            }
            $members = User::whereIn('parent_id', $member_id)->get();
            $member_id_new = array();
            foreach ($members as $member) {
                $member_id_new[] = $member->id;
            }
            $myid = array(Auth::id());
            $mmember = array_merge($member_id, $member_id_new, $myid);
            $SessionID = Company::find(3)->sessionid;
            if ($request->export == 'export') {
                ini_set("memory_limit", "200056M");
                header("Content-Type: application/vnd.ms-excel");
                header("Cache-control: private");
                header("Pragma: public");
                Excel::create('records-'.date("d-m-Y"), function ($excel) use ($request) {
                    $excel->sheet('Sheet1', function ($sheet) use ($request) {
                        $members = User::where('parent_id', Auth::id())->get();
                        $member_id = array();
                        foreach ($members as $member) {
                            $member_id[] = $member->id;
                        }
                        $members = User::whereIn('parent_id', $member_id)->get();
                        $member_id_new = array();
                        foreach ($members as $member) {
                            $member_id_new[] = $member->id;
                        }
                        $myid = array(Auth::id());
                        $mmember = array_merge($member_id, $member_id_new, $myid);
                        $employees = Report::orderBy('id', 'DESC')
                                ->whereDate('created_at', '>=', $request->fromdate)
                                ->whereDate('created_at', '<=', $request->todate)
                                ->whereIn('user_id', $mmember)
                                ->select('id','user_id','amount','created_at','api_id','number','profit','txnid','total_balance','status_id')
                                ->get();
                        $arr = array();
                        foreach ($employees as $employee) {
                            $data = array(date_format($employee->created_at,"d-m-Y"),date_format($employee->created_at,"H:i:s"),$employee->id, $employee->pay_id, $employee->user->name,  $employee->api->api_name, $employee->number, $employee->txnid, $employee->amount, $employee->profit, $employee->total_balance, $employee->status->status);
                            array_push($arr, $data);
                        }
                        //set the titles
                        $sheet->fromArray($arr, null, 'A1', false, false)->prependRow(array( 'Date ',' Time',
                            'Id', 'Pay Id', 'Name', 'Product',
                            'Number', 'Reference ID', 'Amount', 'Profit', 'Total', 'Status'
                                )
                        );
                    });
                })->export('csv');
            }
            if ($request->search == 'search') {
                return "Not Available";
                $reports = Report::orderBy('id', 'DESC')
                        ->whereDate('created_at', '>=', $request->fromdate)
                        ->whereDate('created_at', '<=', $request->todate)
                        ->whereIn('user_id', $mmember)
                        ->where('status_id','!=',14)
                        ->orderBy('id', 'DESC')
                        ->paginate(40);
                return view('admin.all_recharge_report', compact('reports'));
            } else {
                $number = $request->number;
                $start = new \DateTime('now');
                $start->modify('first day of this month');
                $end = new \DateTime('now');
                $end->modify('last day of this month');
                $reports = Report::orderBy('id', 'DESC')
                        ->where('txnid', $number)
                        ->orWhere('pay_id', $number)
                        ->orWhere('customer_number', $number)
                        ->whereIn('user_id', $mmember)
                        ->where('status_id','!=',14)
                        ->select('id','user_id','created_at','api_id','amount','profit','beneficiary_id','recharge_type','status_id','description','total_balance','provider_id','number','customer_number','bank_ref','ackno','txnid')
                        ->paginate(40);
                return view('admin.all_recharge_report', compact('reports'))->with('sessionid', $SessionID);
            }
        }  
		else 
		{
            $SessionID = Company::find(3)->sessionid;
            if ($request->export == 'All Txn Reports') 
			{
                ini_set("memory_limit", "10056M");
                header("Content-Type: application/vnd.ms-excel");
                header("Cache-control: private");
                header("Pragma: public");
                Excel::create($request->export."-".date("d-m-Y"), function ($excel) use ($request) 
				{
                    $excel->sheet('Sheet1', function ($sheet) use ($request) 
					{
                        $employees = Report::orderBy('id', 'DESC')
                                ->whereDate('created_at', '>=', $request->fromdate)
                                ->whereDate('created_at', '<=', $request->todate)
                                ->where('user_id', Auth::id())
                                ->orderBy('id', 'DESC')
                                ->get();
                        $arr = array();
                        foreach ($employees as $employee) {
                            $data = array(date_format($employee->created_at,"d-m-Y"),date_format($employee->created_at,"H:i:s"),$employee->id, $employee->pay_id, $employee->user->name,  $employee->api->api_name, $employee->number, $employee->txnid, $employee->amount, $employee->profit, $employee->total_balance, $employee->status->status);
                            array_push($arr, $data);
                        }
                        //set the titles
                        $sheet->fromArray($arr, null, 'A1', false, false)->prependRow(array('Date ',' Time',
                            'Id', 'Pay Id', 'Name',  'Product',
                            'Number', 'Reference ID', 'Amount', 'Profit', 'Total', 'Status'
                                )
                        );
						/* $sheet->prependRow( array('','To Date' ,$request->fromdate));
						$sheet->prependRow( array('','From Date' ,$request->fromdate)); */
						$row->setBackground('#A9A9A9');
						$row->setFontFamily('Algerian');
                    });
                })->export('csv');
            }
            if ($request->search == 'search') {
                return "Not Available";
                $reports = Report::orderBy('id', 'DESC')
                        ->whereDate('created_at', '>=', $request->fromdate)
                        ->whereDate('created_at', '<=', $request->todate)
                        ->where('user_id', Auth::id())
                        ->orderBy('id', 'DESC')
                        ->where('status_id','!=',14)
                        ->paginate(40);
                return view('admin.all_recharge_report', compact('reports'));
            } else {
                $number = $request->number;
                $start = new \DateTime('now');
                $start->modify('first day of this month');
                $end = new \DateTime('now');
                $end->modify('last day of this month');
                $reports = Report::orderBy('id', 'DESC')
                        ->where('txnid', $number)
                        ->orWhere('pay_id', $number)
                        ->orWhere('customer_number', $number)
                        ->where('user_id', Auth::id())
                        ->where('status_id','!=',14)
                        ->select('id','user_id','created_at','api_id','amount','profit','beneficiary_id','recharge_type','status_id','description','total_balance','provider_id','number','customer_number','bank_ref','ackno','txnid')
                        ->paginate(40);
                return view('admin.all_recharge_report', compact('reports'))->with('sessionid', $SessionID);
            }
        }
    }
	

	public function recharge_searchall(Request $request)
	{
		if (in_array(Auth::user()->role_id,array(1,11,12,14))) {
            $SessionID = Company::find(1);//->sessionid;
            if ($request->export == 'Recharge Txn Report') {
                set_time_limit(60000);
                ini_set("memory_limit", "200056M");
                header("Content-Type: application/vnd.ms-excel");
                header("Cache-control: private");
                header("Pragma: public");
                Excel::create($request->export.'-'.date("d-m-Y"), function ($excel) use ($request) {
                    $excel->sheet('Sheet1', function ($sheet) use ($request) {
                        $employees = Report::orderBy('id', 'DESC')
									->whereDate('created_at', '>=', $request->fromdate)
									->whereDate('created_at', '<=', $request->todate)
									->whereIn('api_id', [1,8,13])
									->get();
                        $arr = array();
                        foreach ($employees as $employee) {
                            $data = array(date_format($employee->created_at,"d-m-Y"),date_format($employee->created_at,"H:i:s"),$employee->id, $employee->pay_id, $employee->user->name . '(' . $employee->user->id . ')', $employee->api->api_name,@$employee->provider->provider_name, $employee->number, $employee->txnid,  number_format($employee->amount,16), $employee->profit, $employee->total_balance, $employee->status->status);
                            array_push($arr, $data);
                        }
                        //set the titles 
                        $sheet->fromArray($arr, null, 'A1', true, false)->prependRow(array('Date ',' Time',
                            'Id', 'Pay Id','User', 'Product',  'Provider',
                            'Number', 'Reference ID','Amount', 'Charges', 'Balance', 'Status'
                                )
                        );
                    });
                })->export('csv');
            }
            if ($request->search == 'search') {
                return "Not Available";
                $reports = Report::orderBy('id', 'DESC')
									->whereDate('created_at', '>=', $request->fromdate)
									->whereDate('created_at', '<=', $request->todate)
									->where('api_id',1)
									->paginate(40);
                        
                return view('admin.all_recharge_recharge_report', compact('reports'));
            } else {
                $number = $request->number;
                $start = new \DateTime('now');
                $start->modify('first day of this month');
                $end = new \DateTime('now');
                $end->modify('last day of this month');
                $reports = Report::orderBy('id', 'DESC')
                        ->where('number', $number)
                        ->orWhere('id', $number)
                        ->orWhere('txnid', $number)
                        ->orWhere('pay_id', $number)
                        ->orWhere('customer_number', $number)
                        //->where('user_id', Auth::id())
                        ->orderBy('id', 'DESC')
                        ->paginate(40);
                return view('admin.all_recharge_recharge_report', compact('reports'))->with('sessionid', $SessionID);
            }
        } elseif (Auth::user()->role_id == 3) {
            $members = User::where('parent_id', Auth::id())->get();
            $member_id = array();
            foreach ($members as $member) {
                $member_id[] = $member->id;
            }
            $members = User::whereIn('parent_id', $member_id)->get();
            $member_id_new = array();
            foreach ($members as $member) {
                $member_id_new[] = $member->id;
            }
            $myid = array(Auth::id());
            $mmember = array_merge($member_id, $member_id_new, $myid);
            $SessionID = Company::find(1);//->sessionid;
            if ($request->export == 'export') {
                ini_set("memory_limit", "200056M");
                header("Content-Type: application/vnd.ms-excel");
                header("Cache-control: private");
                header("Pragma: public");
                Excel::create('records-'.date("d-m-Y"), function ($excel) use ($request) {
                    $excel->sheet('Sheet1', function ($sheet) use ($request) {
                        $members = User::where('parent_id', Auth::id())->get();
                        $member_id = array();
                        foreach ($members as $member) {
                            $member_id[] = $member->id;
                        }
                        $members = User::whereIn('parent_id', $member_id)->get();
                        $member_id_new = array();
                        foreach ($members as $member) {
                            $member_id_new[] = $member->id;
                        }
                        $myid = array(Auth::id());
                        $mmember = array_merge($member_id, $member_id_new, $myid);
                        $employees = Report::orderBy('id', 'DESC')
									->whereDate('created_at', '>=', $request->fromdate)
									->whereDate('created_at', '<=', $request->todate)
									->whereIn('user_id', $mmember)
									->where('api_id',1)
									->get();

                        $arr = array();
                        foreach ($employees as $employee) {
                            $data = array(date_format($employee->created_at,"d-m-Y"),date_format($employee->created_at,"H:i:s"),$employee->id, $employee->pay_id, $employee->user->name, $employee->api->api_name, $employee->number, $employee->txnid, $employee->amount, $employee->profit, $employee->total_balance, $employee->status->status);
                            array_push($arr, $data);
                        }
                        //set the titles
                        $sheet->fromArray($arr, null, 'A1', false, false)->prependRow(array('Date ',' Time',
                            'Id', 'Pay Id', 'Name',  'Product',
                            'Number', 'Reference ID', 'Amount', 'Profit', 'Total', 'Status'
                                )
                        );
                    });
                })->export('csv');
            }
            if ($request->search == 'search') {
                return "Not Available";
                $reports = Report::orderBy('id', 'DESC')
									->whereDate('created_at', '>=', $request->fromdate)
									->whereDate('created_at', '<=', $request->todate)
									->where('api_id', 1)
									->whereIn('user_id', $mmember)
									->paginate(40);
                return view('admin.all_recharge_recharge_report', compact('reports'));
            } else {
                $number = $request->number;
                $start = new \DateTime('now');
                $start->modify('first day of this month');
                $end = new \DateTime('now');
                $end->modify('last day of this month');
                $reports = Report::orderBy('id', 'DESC')
                        ->where('number', $number)
                        ->orWhere('id', $number)
                        ->orWhere('txnid', $number)
                        ->orWhere('customer_number', $number)
                        ->whereIn('user_id', $mmember)
                        ->orderBy('id', 'DESC')
                        ->paginate(40);
                return view('admin.all_recharge_recharge_report', compact('reports'))->with('sessionid', $SessionID);
            }
        }  else {
            $SessionID = Company::find(1);//->sessionid;
            if ($request->export == 'export') {
                ini_set("memory_limit", "10056M");
                header("Content-Type: application/vnd.ms-excel");
                header("Cache-control: private");
                header("Pragma: public");
                Excel::create('records-'.date("d-m-Y"), function ($excel) use ($request) {
                    $excel->sheet('Sheet1', function ($sheet) use ($request) {
                        $employees =Report::orderBy('id', 'DESC')
									->whereDate('created_at', '>=', $request->fromdate)
									->whereDate('created_at', '<=', $request->todate)
									->where('api_id', 1)
									->where('user_id', Auth::id())
									->get();
                        $arr = array();
                        foreach ($employees as $employee) {
                            $data = array(date_format($employee->created_at,"d-m-Y"),date_format($employee->created_at,"H:i:s"),$employee->id, $employee->pay_id, $employee->user->name, $employee->api->api_name, $employee->number, $employee->txnid, $employee->amount, $employee->profit, $employee->total_balance, $employee->status->status);
                            array_push($arr, $data);
                        }
                        //set the titles
                        $sheet->fromArray($arr, null, 'A1', false, false)->prependRow(array('Date ',' Time',
                            'Id', 'Pay Id', 'Name',  'Product',
                            'Number', 'Reference ID', 'Amount', 'Profit', 'Total', 'Status'
                                )
                        );
                    });
                })->export('csv');
            }
            if ($request->search == 'search') {
                return "Not Available";
                $reports = Report::orderBy('id', 'DESC')
                        ->whereDate('created_at', '>=', $request->fromdate)
                        ->whereDate('created_at', '<=', $request->todate)
                        ->where('api_id', 1)
						->where('user_id', Auth::id())
                        ->paginate(40);
                return view('admin.all_recharge_recharge_report', compact('reports'));
            } else {
                $number = $request->number;
                $start = new \DateTime('now');
                $start->modify('first day of this month');
                $end = new \DateTime('now');
                $end->modify('last day of this month');
                $reports = Report::orderBy('id', 'DESC')
                        ->where('id', $number)
                        ->orWhere('txnid', $number)
                        ->orWhere('pay_id', $number)
                        ->orWhere('customer_number', $number)
                        ->where('user_id', Auth::id())
                        ->orderBy('id', 'DESC')
                        ->paginate(40);
                return view('admin.all_recharge_recharge_report', compact('reports'))->with('sessionid', $SessionID);
            }
        }
	
	}
    public function searchall_money(Request $request) {
        if (in_array(Auth::user()->role_id,array(1,11,12,14))) {
			
            $SessionID = Company::find(1);//->sessionid;
            if ($request->export == 'DMT Reports') {
			    set_time_limit(60000);
                ini_set("memory_limit", "256M");
                header("Content-Type: application/vnd.ms-excel");
                header("Cache-control: private");
                header("Pragma: public");
				$query = Report::select('id','created_at','txnid','ackno','api_id','user_id','number','beneficiary_id','bank_ref','amount','channel','status_id','remark','customer_number');
				$query->whereDate('created_at', '>=', $request->fromdate);
				$query->whereDate('created_at', '<=', $request->todate);
				$query->where('recharge_type','!=',1);
				$query->whereIn('status_id',[1,2,3,9,4,20,21,23]);
				if($request->product !='')
				{
					$query->whereIn('api_id',[(int)$request->product,2]);
				}
				$reports = $query->get();
				$_SESSION['reports']=array();
				$_SESSION['reports']=$reports;
				Excel::create($request->export."-".date("d-m-Y"), function ($excel) use ($reports,$request) 
				{
						
						$excel->setDescription("Report from ".$request->fromdate." to ".$request->todate);
						for($i=1; $i<=5; $i++)
						{
							$reports =$_SESSION['reports'];
							if(empty($reports))
								break;
							$excel->sheet("Sheet_$i", function ($sheet) use ($reports,$request) 
							{
								$arr = array();
								foreach($reports as $key=>$report)
								{
									/* $date=date_create($report->created_at);
									$created_at = date_format($date,"d-m-Y H:i:s"); */
									$index=$key;
									if($report->api_id == 2)
										$mobile_no=$report->customer_number;
									else
										$mobile_no = @$report->beneficiary->customer_number;
									if(in_array($index,array(9999,19999,29999,39999)))
									{
										$data = array(
												date_format($report->created_at,"d-m-Y"),
												date_format($report->created_at,"H:i:s"), 
												@$report->user->name.'('.$report->user_id.')',
												$report->id, 
												$report->txnid,
												//$report->ackno, 
												$report->api->api_name,
												//$mobile_no,
                                                $report->customer_number, 
                                                 ($report->api_id==2) ? $report->remark : $report->beneficiary->name,
												//@$report->beneficiary->name, 
												$report->number, 
                                                 ($report->api_id==2) ? $report->description : $report->beneficiary->bank_name,
												//@$report->beneficiary->bank_name,
												@$report->beneficiary->ifsc, 
												$report->bank_ref, 
												$report->amount, 
												$report->channel, 
												@$report->status->status);
										array_push($arr, $data);
										unset($reports[$key]);
										break;
									}
									else
									{
										$data = array(
												date_format($report->created_at,"d-m-Y"),
												date_format($report->created_at,"H:i:s"), 
												@$report->user->name.'('.$report->user_id.')', 
												$report->id,
												$report->txnid,
												$report->ackno, 
												$report->api->api_name,
												$mobile_no, 
												@$report->beneficiary->name, 
												$report->number, @$report->beneficiary->bank_name, @$report->beneficiary->ifsc, $report->bank_ref, $report->amount, $report->channel, @$report->status->status);
										array_push($arr, $data);
										unset($reports[$key]);
									}
									 
								}
								$sheet->fromArray($arr, null, 'A1', false, false)->prependRow(array(
										'Date','Time', 'User Name','Report Id','txn id', 'Ack No','Product', 'Mobile', 'Name', 'Account Number',
										'Bank Name', 'IFSC', 'UTR Number', 'Amount', 'Channel', 'Status'
											)
									);
									$sheet->row(1, function($row) 
									{
										$row->setBackground('#A9A9A9');
										$row->setFontFamily('Times New Roman');
										$row->setFontSize(14);
									});
								
								$_SESSION['reports']=$reports;
								
							});
						}
						})->export('csv');
            }
            if ($request->export == 'search') {
				
                $reports = Report::orderBy('id', 'DESC')
                       /*  ->whereDate('created_at', '>=', $request->fromdate)
                        ->whereDate('created_at', '<=', $request->todate) */
                        ->where('number', $request->number)
                        ->orderBy('id', 'DESC')
                        ->paginate(40);
                return view('admin.report.money_transfer_report', compact('reports'))->with('sessionid', $SessionID);
            } else {
                $number = $request->number;
                $start = new \DateTime('now');
                $start->modify('first day of this month');
                $end = new \DateTime('now');
                $end->modify('last day of this month');
                $reports = Report::orderBy('id', 'DESC')
                        ->where('id', $number)
                        ->orWhere('txnid', $number)
                        ->orWhere('customer_number', $number)
                        ->orWhere('bank_ref', $number)
                        ->where('status_id','!=',14)
                        ->select('id','user_id','created_at','api_id','amount','profit','beneficiary_id','recharge_type','status_id','description','total_balance','provider_id','number','customer_number','bank_ref','ackno','txnid','refund','credit_by','channel','remark')
                        ->paginate(40);
                return view('admin.report.money_transfer_report', compact('reports'))->with('sessionid', $SessionID);
            }
        } elseif (Auth::user()->role_id == 3) {
            $members = User::where('parent_id', Auth::id())->get();
            $member_id = array();
            foreach ($members as $member) {
                $member_id[] = $member->id;
            }
            $members = User::whereIn('parent_id', $member_id)->get();
            $member_id_new = array();
            foreach ($members as $member) {
                $member_id_new[] = $member->id;
            }
            $myid = array(Auth::id());
            $mmember = array_merge($member_id, $member_id_new, $myid);
            $SessionID = Company::find(1)->sessionid;
            if ($request->export == 'export') {
                ini_set("memory_limit", "10056M");
                header("Content-Type: application/vnd.ms-excel");
                header("Cache-control: private");
                header("Pragma: public");
                Excel::create('records-'.date("d-m-Y"), function ($excel) use ($request) {
                    $excel->sheet('Sheet1', function ($sheet) use ($request) {
                        $members = User::where('parent_id', Auth::id())->get();
                        $member_id = array();
                        foreach ($members as $member) {
                            $member_id[] = $member->id;
                        }
                        $members = User::whereIn('parent_id', $member_id)->get();
                        $member_id_new = array();
                        foreach ($members as $member) {
                            $member_id_new[] = $member->id;
                        }
                        $myid = array(Auth::id());
                        $mmember = array_merge($member_id, $member_id_new, $myid);
                        $employees = Report::orderBy('id', 'DESC')
                                ->whereDate('created_at', '>=', $request->fromdate)
                                ->whereDate('created_at', '<=', $request->todate)
                                ->whereIn('user_id', $mmember)
                               ->select('created_at','txnid','ackno','api_id','user_id','number','beneficiary_id','bank_ref','amount','channel','status_id')
                                ->get();
                        $arr = array();
                        foreach ($employees as $employee) {
                            $data = array(date_format($employee->created_at,"d-m-Y"),date_format($employee->created_at,"H:i:s"), $employee->txnid, $employee->provider->provider_name, $employee->beneficiary->customer_number, $employee->beneficiary->name, $employee->number, $employee->beneficiary->bank_name, $employee->beneficiary->ifsc, $employee->amount, $employee->channel, $employee->status->status);
                            array_push($arr, $data);
                        }
                        //set the titles
                        $sheet->fromArray($arr, null, 'A1', false, false)->prependRow(array(
                            'Date','Time', 'tx id', 'Product', 'Mobile', 'Name', 'Account Number',
                            'Bank Name', 'IFSC', 'Amount', 'Channel', 'Status'
                                )
                        );
                    });
                })->export('csv');
            }
            if ($request->search == 'search') {
                return "Not Available";
                $reports = Report::orderBy('id', 'DESC')
                        ->whereDate('created_at', '>=', $request->fromdate)
                        ->whereDate('created_at', '<=', $request->todate)
                        ->whereIn('user_id', $mmember)
                        ->orderBy('id', 'DESC')
                        ->paginate(40);
                return view('admin.report.money_transfer_report', compact('reports'));
            } else {
                $number = $request->number;
                $start = new \DateTime('now');
                $start->modify('first day of this month');
                $end = new \DateTime('now');
                $end->modify('last day of this month');
                $reports = Report::orderBy('id', 'DESC')
                        ->orWhere('txnid', $number)
                        ->orWhere('customer_number', $number)
                         ->orWhere('id', $number)
                        ->whereIn('user_id', $mmember)
                        ->where('status_id','!=',14)
                        ->select('id','user_id','created_at','api_id','amount','profit','beneficiary_id','recharge_type','status_id','description','total_balance','provider_id','number','customer_number','bank_ref','ackno','txnid','refund','channel','credit_by')
                        ->paginate(40);
                return view('admin.report.money_transfer_report', compact('reports'))->with('sessionid', $SessionID);
            }
        }
        elseif(Auth::user()->role_id==4)
        {
            $members = User::where('parent_id', Auth::id())->get();
            $member_id = array();
            foreach ($members as $member) {
                $member_id[] = $member->id;
            }
            
            $myid = array(Auth::id());
            $mmember = array_merge($member_id,$myid);
            $SessionID = Company::find(1)->sessionid;
            if ($request->search == 'search') {
                $reports = Report::orderBy('id', 'DESC')
                        ->whereDate('created_at', '>=', $request->fromdate)
                        ->whereDate('created_at', '<=', $request->todate)
                        ->whereIn('user_id', $mmember)
                        ->orderBy('id', 'DESC')
                        ->paginate(40);
                return view('admin.report.money_transfer_report', compact('reports'));
            } else {
                $number = $request->number;
                $start = new \DateTime('now');
                $start->modify('first day of this month');
                $end = new \DateTime('now');
                $end->modify('last day of this month');
                $reports = Report::orderBy('id', 'DESC')
                        ->orWhere('txnid', $number)
                        ->orWhere('pay_id', $number)
                        ->orWhere('bank_ref', $number)
                        ->orWhere('customer_number', $number)
                        ->whereIn('user_id', $mmember)
                         ->where('status_id','!=',14)
                        ->select('id','user_id','created_at','api_id','amount','profit','beneficiary_id','recharge_type','status_id','description','total_balance','provider_id','number','customer_number','bank_ref','channel','ackno','txnid')
                        ->paginate(40);
                return view('admin.report.money_transfer_report', compact('reports'))->with('sessionid', $SessionID);
            }
        }
    }

   public function searchall_all(Request $request) 
	{
		//print_r($request->all());die;
        ini_set("memory_limit", "200056M");
        header("Content-Type: application/vnd.ms-excel");
        header("Cache-control: private");
        header("Pragma: public");
        $SessionID = Company::find(1);//->sessionid;

        if ($request->export == 'Account Statements') 
		{
			$dateFormat = $this->getDbFromatDate($request->fromdate, $request->todate);
			$userDetails = $this->getUserDetails();
			
			$condition=array('user_id'=>$userDetails->id);
			return Excel::download(new LedgerReportExport($condition,$dateFormat['start_date'],$dateFormat['end_date']), 'Ledger Report.xlsx');
        }
        if ($request->search == 'search') {
            return "Not Available";
            $recharge_report = Report::orderBy('id', 'DESC')
                    ->whereDate('created_at', '>=', $request->fromdate)
                    ->whereDate('created_at', '<=', $request->todate)
                    ->where('user_id', Auth::id())
                    ->orderBy('id', 'DESC')
                    ->where('status_id','!=',14)
                    ->paginate(40);
            return view('reports.account_statement_report', compact('recharge_report'))->with('sessionid', $SessionID);
        }
		if($request->SEARCH =="SEARCH")
           {
              $start_date = ($request->fromdate) ? $request->fromdate ." 00:00:00" : date("Y-m-d") . " 00:00:00";
              $end_date = ($request->todate) ? $request->todate ." 23:59:59" : date("Y-m-d H:i:s");
              $start_date = date("Y-m-d H:i:s", strtotime($start_date));
              $end_date = date("Y-m-d H:i:s", strtotime($end_date));
              $recharge_report = Report::orderBy('id', 'DESC') ->where('user_id', Auth::id())->whereBetween('created_at', [$start_date,$end_date])->paginate();
              return view('reports.account_statement_report', compact('recharge_report'))->with('sessionid', $SessionID);
           }
        else
        {

        $SessionID = Company::find(1);//->sessionid;
        $number = $request->search_number;
        if($number!='')
        {
        $start = new \DateTime('now');
        $start->modify('first day of this month');
        $end = new \DateTime('now');
        $end->modify('last day of this month');
        $recharge_report  = Report::orderBy('id', 'DESC')
                ->where('txnid', $number)
                ->orWhere('id', $number)
                ->orWhere('number', $number)
                ->orderBy('id', 'DESC')
                ->where('user_id', Auth::id())
                ->where('status_id','!=',14)
                ->paginate(40);
        return view('reports.account_statement_report', compact('recharge_report'))->with('sessionid', $SessionID);
		
    }
    else
    {
        return "Please enter requied detail to be search";
    }
    }
    }
   

    public function searchall_all_money(Request $request) 
	{
		if(Auth::user()->role_id==5)
		{
        ini_set("memory_limit", "25600M");
        header("Content-Type: application/vnd.ms-excel");
        header("Cache-control: private");
        header("Pragma: public");
        $SessionID = Company::find(1);//->sessionid;
        if ($request->export == 'DMT Reports') 
		{
				Excel::create($request->export.'-'.date("d-m-Y"), function ($excel) use ($request) {
                $excel->sheet('Sheet1', function ($sheet) use ($request) {

                    $employees = Report::orderBy('id', 'DESC')
                            ->whereDate('created_at', '>=', $request->fromdate)
                            ->whereDate('created_at', '<=', $request->todate)
                            ->where('user_id', Auth::id())
                            ->whereIn('api_id', [4,5])
                            ->orderBy('id', 'DESC')
                            ->get();
                    $arr = array();
                    foreach ($employees as $employee) 
					{
						$s = $employee->created_at;
							$dt = new \DateTime($s);
                        $data = array($dt->format('d-m-Y'),$dt->format('H:i:s'), 
								@$employee->txnid, 
								@$employee->api->api_name, 
								@$employee->beneficiary->customer_number, 
								@$employee->beneficiary->name, 
								@$employee->beneficiary->account_number, 
								@$employee->beneficiary->bank_name, 
								@$employee->beneficiary->ifsc, 
								@$employee->amount, 
								($employee->channel == 2)?"IMPS":(($employee->channel == 1)?"NEFT":''), 
								@$employee->status->status, 
								@$employee->refund);
                        array_push($arr, $data);
                    }

                    //set the titles
                    $sheet->fromArray($arr, null, 'A1', false, false)->prependRow(array(
                        'Date','Time', 'Tx Id', 'Product', 'Mobile Number',
                        'Name', 'Account', 'Bank Name', 'IFSC', 'Amount', 'Mode', 'Status', 'Refund'
                            )
                    );
						/* 	$sheet->prependRow( array('','To Date' ,$request->todate));
							$sheet->prependRow( array('','From Date' ,$request->fromdate)); */
							$sheet->row(1, function($row) {
							// call cell manipulation methods
							$row->setBackground('#A9A9A9');
							$row->setFontFamily('Times New Roman');
							});
                });
            })->export('csv');
        }
        if ($request->search == 'search') 
		{
            return "Not Available";
            $reports = Report::orderBy('id', 'DESC')
                    ->whereDate('created_at', '>=', $request->fromdate)
                    ->whereDate('created_at', '<=', $request->todate)
                    ->where('user_id', Auth::id())
                    ->orderBy('id', 'DESC')
                    ->paginate(40);
            return view('reports.money_transfer_report', compact('reports'))->with('sessionid', $SessionID);
        }
			$SessionID = Company::find(1);//->sessionid;
			$number = $request->number;
			$start = new \DateTime('now');
			$start->modify('first day of this month');
			$end = new \DateTime('now');
			$end->modify('last day of this month');
			$reports = Report::orderBy('id', 'DESC');
			if($request->SEARCH_TYPE=="ID")
				$reports->where('id', $number);
			elseif($request->SEARCH_TYPE=="TXN_ID")
				$reports->where('txnid', $number);
			elseif($request->SEARCH_TYPE=="ACC")
				$reports->where('number', $number);
			elseif($request->SEARCH_TYPE=="MOB_NO")
				$reports->orWhere('customer_number', $number);
			$reports->where('user_id', Auth::id());
			$reports->where('status_id','!=',14);
			$reports = $reports->simplePaginate(40);
        return view('reports.money_transfer_report', compact('reports'))->with('sessionid', $SessionID);
    }
	return "Access Denied";
	}

    public function operator_wise_report() {
        $start = new \DateTime('now');
        //$start->modify('first day of this month');
        $end = new \DateTime('now');
        //$end->modify('last day of this month');
        $reports = Report::groupBy('provider_id')
                ->selectRaw('*, sum(amount) as total_sales')
                ->orderBy('id', 'DESC')
                ->whereBetween('created_at', array($start, $end))
                ->get();
        //return $users;
        return view('admin.report.operator_wise_report', compact('reports'));
    }

    public function payment_report(Request $request) 
	{	
		$start_date = ($request->fromdate) ? $request->fromdate. " 00:00:00" : date('Y-m-d').' 00:00:00';
		$end_date = ($request->todate) ? $request->todate. " 23:59:59" : date('Y-m-d').' 23:59:59';
		$start_date = date("Y-m-d H:i:s", strtotime($start_date));
		$end_date = date("Y-m-d H:i:s", strtotime($end_date));
		if(in_array(Auth::user()->role_id,array(1,11,14)))
			$logined_user_id = User::select('id')->where('role_id',1)->first()->id;
		else
			$logined_user_id=Auth::id();
			$reportQuery = Report::where('provider_id',0);
			$reportQuery->whereIn('status_id',[6,7]);
			$reportQuery->where('user_id', $logined_user_id);
			$reportQuery->whereBetween('created_at',[$start_date,$end_date]);
			$reportQuery->orderBy('id', 'DESC');
		if($request->orderId !='')
			$reportQuery->where('id',$request->orderId);
		if($request->user !='')
			$reportQuery->where('reports.credit_by',$request->user);
		if($request->export == "Payment Load")
		{
			return Excel::download(new DistPaymentReportExport($start_date,$end_date),"Payment Report.xlsx");
		}
		/*if($request->export=="PAYMENT_REQUEST"){
			$reports = $reportQuery->get();
			$this->exportPaymentLoad($reports,"PAYMENT REQUEST REPORT");
		}*/
		else
			$reports = $reportQuery->simplePaginate(40);
			$page_title = "Payment Report";
			$userDetails = User::where('parent_id',Auth::id())->selectRaw('name,mobile,id')->get();
			$users=array();
			foreach($userDetails as $user)
			{
				$users[$user->id]=$user->member->company.' '.'(' .$user->mobile .')';
			}
        return view('admin.report.payment_report_new', compact('reports','page_title','users'));
    }
	public function paymentReportRoleWise(Request $request)
	{
		$urlPath = \Request::path();
		$start_date = ($request->fromdate) ? $request->fromdate. " 00:00:00" : date('Y-m-d').' 00:00:00';
		$end_date = ($request->todate) ? $request->todate. " 23:59:59" : date('Y-m-d').' 23:59:59';
		$dbFormatDate = $this->getDbFromatDate($start_date,$end_date);
		if($urlPath == 'md-payment')
		{
			$roleId = 3;
            $page_title= Auth::user()->role->role_title . " to Md payment";
		}
		else if($urlPath == 'dist-payment')
		{
			$roleId = 4;
            $page_title=Auth::user()->role->role_title ." to Dist payment";
		}
		else if($urlPath == 'retailer-payment')
		{
			$roleId = 5;
            $page_title= Auth::user()->role->role_title ." to Retailer payment";
		}
		else if($urlPath == 'api-payment')
		{
			$roleId = 7;
            $page_title= Auth::user()->role->role_title ." to Api payment";
		}
		if($request->export == "Payment Load")
		{
			return Excel::download(new PaymentReportExport($roleId,$dbFormatDate['start_date'],$dbFormatDate['end_date'],$request->user), "Payment Report.xlsx");
		}
		$reportQuery=Report::join('users',function ($join)
			{
				$join->on('users.id', '=', 'reports.credit_by');
			});
		$reportQuery->select('reports.id','reports.recharge_type','reports.user_id','reports.credit_by','reports.txnid','reports.amount','reports.bank_charge','reports.remark','reports.status_id','reports.created_at','reports.description','reports.opening_balance','reports.total_balance','reports.payment_id');
		$reportQuery->whereIn('reports.status_id',[6,7]);
		$reportQuery->orderBy('reports.id','desc');
		$reportQuery->where('reports.user_id',Auth::id());
		$reportQuery->whereBetween('reports.created_at',[$dbFormatDate['start_date'],$dbFormatDate['end_date']]);
		/* if($request->searchOf !='')
			$reportQuery->where('reports.credit_by',$request->searchOf); */
		/* if(Auth::user()->role_id==3)
		{
			$distbutorLists = $this->getDistributor(Auth::id());
			$reportQuery->where('users.parent_id',Auth::id());
			$reportQuery->orWhere('users.parent_id',$distbutorLists);
		}
		elseif(Auth::user()->role_id==4)
		{
			$reportQuery->where('users.parent_id',Auth::id());
		} */
		
			
		if($request->user !='')
			$reportQuery->where('reports.credit_by',$request->user);
		
		$reportQuery->where('users.role_id',$roleId);
		$reports  = $reportQuery->paginate(30);
		//$users =User::where('role_id',$roleId)->pluck('name','id')->toArray();
		$userDetails = User::where('role_id',$roleId)->selectRaw('name,mobile,id')->get();
		$users=array();
		foreach($userDetails as $user)
		{
			$users[$user->id]=$user->member->company.' '.'(' .$user->mobile .')';
		}
		return view('admin.report.payment_report_new', compact('reports','page_title','users'));
			
	}
	public function paymentLoad(Request $request) 
	{
		$start_date = ($request->fromdate) ? $request->fromdate. " 00:00:00" : date('Y-m-d').' 00:00:00';
		$end_date = ($request->todate) ? $request->todate. " 23:59:59" : date('Y-m-d').' 23:59:59';
		if(in_array(Auth::user()->role_id,array(1,11,14)))
				$logined_user_id = User::select('id')->where('role_id',1)->first()->id;
			else
				$logined_user_id=Auth::id();
		$reportQuery = Report::where('provider_id',0);
                $reportQuery->whereIn('status_id',[7]);
                $reportQuery->where('user_id', $logined_user_id);
				$reportQuery->whereBetween('created_at',[$start_date,$end_date]);
                $reportQuery->orderBy('id', 'DESC');
		if($request->number !='')
			$reportQuery->where('id',$request->number);
		if($request->export=="PAYMENT_LOAD"){
				$reports = $reportQuery->get();
			$this->exportPaymentLoad($reports,"PAYMENT LOAD REPORT");
		}
		else
			$reports = $reportQuery->simplePaginate(40);
        $page_title = "Payment Report";
        return view('admin.report.payment-load', compact('reports','page_title'));
    }
	private function exportPaymentLoad($reports,$type)
	{
		 Excel::create($type.'-'.date('d-m-Y'), function ($excel) use ($reports) {
                $excel->sheet('Sheet1', function ($sheet) use ($reports) {
                	$arr = array();
                    foreach ($reports as $employee) {
                       $wallet = 'Money'; 
						$data = array(
									date_format($employee->created_at,"d-m-Y"),date_format($employee->created_at,"H:i:s"),
										$employee->id,
										$wallet,
										$employee->user->name.'('.$employee->user->id .')', 
									  @User::select('name')->find($employee->credit_by)->name, 
									/*  ($employee->payment_id)?$employee->payment->user_id:'',// credited id */
									($employee->payment_id)?(@$employee->payment->netbank->bank_name):'', 
									$employee->txnid, 
									$employee->amount, 
									$employee->profit, 
									$employee->bank_charge, 
									($employee->payment_id)?(@$employee->payment->bankref):'', 
									$employee->remark, 
									 /*  @$employee->payment->pmethod_id, */
									$employee->status->status);
                        array_push($arr, $data);
                    } 
                    $sheet->fromArray($arr, null, 'A1', false, false)->prependRow(array('Date',
                        'Time','Id','Wallet', 'Credited By', 'Credited To','Bank','Txnid',
                        'amount', 'profit','Bank Charge','Remark','DT Remark', 'Status'
                        )
                    );
                });
            })->export('csv');
        }
	public function accountSummary(Request $request) 
	{
		$start_date = ($request->fromdate) ? $request->fromdate. " 00:00:00" : date('Y-m-d').' 00:00:00';
		$end_date = ($request->todate) ? $request->todate. " 23:59:59" : date('Y-m-d').' 23:59:59';
		if(in_array(Auth::user()->role_id,array(1,11,14)))
			$logined_user_id = User::select('id')->where('role_id',1)->first()->id;
		else
			$logined_user_id=Auth::id();
			
        $paymetsReports = Report::where('provider_id',0)->selectRaw('sum(amount) as amount,Date(created_at) as dates')->whereIn('status_id',[6,7])->where('user_id', $logined_user_id)->groupBy('status_id')->groupBy('dates')->whereBetween('created_at',[$start_date,$end_date])->orderBy('id', 'DESC')->get();
		//print_r($paymetsReports);die;
		$loadAmount = $transferAmaount = 0;
		$userData=array();
		foreach($paymetsReports as $report)
		{
			/* if($report->status_id == 6)
				$transferAmaount +=$report->amount;
			else
				$loadAmount +=$report->amount; */
			if($report->status_id == 6)
			{
				$transferAmaount =$report->amount;
				$userData[$report->dates]['transferAmaount'] = isset($userData[$report->dates]['transferAmaount']) ? ($userData[$report->dates]['transferAmaount'] +$transferAmaount) : $transferAmaount;
			}
			if($report->status_id == 7)
			{
				$loadAmount =$report->amount;
				$userData[$report->dates]['loadAmount'] = isset($userData[$report->dates]['loadAmount']) ? ($userData[$report->dates]['loadAmount'] +$loadAmount) : $loadAmount;
			}
		}
		$userCommissionReports = Report::selectRaw('sum(amount) as amount,sum(credit_charge) as credit_charge,sum(abs(debit_charge)) as debit_charge,Date(created_at) as dates')->groupBy('dates')->whereIn('status_id',[22,16])->where('user_id', $logined_user_id)->whereBetween('created_at',[$start_date,$end_date])->orderBy('id', 'DESC')->get();
		//print_r($userCommissionReports);die;
		foreach($userCommissionReports as $commission)
		{
			$commissionAmount = $commission->credit_charge;
			$surCharge = $commission->debit_charge;
			$userData[$commission->dates]['commission'] = isset($userData[$commission->dates]['commission']) ? ($userData[$commission->dates]['commission'] + $commissionAmount) : $commissionAmount;
			$userData[$commission->dates]['surCharge'] = isset($userData[$commission->dates]['surCharge']) ? ($userData[$commission->dates]['surCharge'] +$surCharge) : $surCharge;
		}
		
		$userReports = Report::join('users',function($join){
								$join->on('reports.user_id','=','users.id');
					
							})
							->selectRaw('sum(reports.amount) as amount,sum(reports.credit_charge) as credit_charge,sum(abs(reports.debit_charge)) as debit_charge, reports.status_id, Date(reports.created_at) as dates')
							->whereIn('reports.status_id',[1,4,3,9,18])
							->where('users.parent_id', $logined_user_id)
							//->orWhere('users.id', $logined_user_id)
							->groupBy('reports.status_id')
							->groupBy('dates')
							->whereBetween('reports.created_at',[$start_date,$end_date])
							->orderBy('reports.id', 'DESC')
							->get(); 
		$AcitiveAgents = Report::join('users',function($join){
								$join->on('reports.user_id','=','users.id');
					
							})
							->selectRaw('count(distinct(user_id)) as active_agent,Date(reports.created_at) as dates')
							->whereIn('reports.status_id',[1,2,4,3,9,18])
							->where('users.parent_id', $logined_user_id)
							//->orWhere('users.id', $logined_user_id)
							
							->groupBy('dates')
							->whereBetween('reports.created_at',[$start_date,$end_date])
							->orderBy('reports.id', 'DESC')
							->get(); 
		$refundAmount = $txnAmount =0;
		foreach($userReports as $userReport)
		{
			if(in_array($userReport->status_id,array(1,3,9,18)))
			{
				$txnAmount =$userReport->amount;
				$userData[$userReport->dates]['txnAmount'] = isset($userData[$userReport->dates]['txnAmount']) ? ($userData[$userReport->dates]['txnAmount'] +$txnAmount) : $txnAmount;	
			}
			elseif($userReport->status_id == 4)
			{
				$refundAmount =$userReport->amount;
				$userData[$userReport->dates]['refundAmount'] = isset($userData[$userReport->dates]['refundAmount']) ? ($userData[$userReport->dates]['refundAmount'] +$refundAmount) : $refundAmount;
			}
			
			/* $userData[$userReport->dates][$userReport->user_id.'/'.$userReport->user->name][$userReport->status_id]['Date']= $userReport->dates;
			$userData[$userReport->dates][$userReport->user_id.'/'.$userReport->user->name][$userReport->status_id]['Amount']= $userReport->amount;
			$userData[$userReport->dates][$userReport->user_id.'/'.$userReport->user->name][$userReport->status_id]['Credit']= $userReport->credit_charge;
			$userData[$userReport->dates][$userReport->user_id.'/'.$userReport->user->name][$userReport->status_id]['Debit']= $userReport->debit_charge; */
		}
		foreach($AcitiveAgents as $activeAgent)
		{
			$loggedUser =$activeAgent->active_agent;
			$userData[$activeAgent->dates]['ActiveAgent'] = isset($userData[$activeAgent->dates]['ActiveAgent']) ? ($userData[$activeAgent->dates]['ActiveAgent'] +$loggedUser) : $loggedUser;
		}
			
		$reports= json_decode(json_encode($userData));
		$data=array();
		/* $index = 0;
		foreach($userData as $key=>$retailerData)
		{
			$data[$index]['Date'] = $key;
			foreach($retailerData as $c_key=>$c_data)
			{
				$name=explode("/",$c_key);
				$data[$index]['Name'] = $name[1] .' (R '.$name[0] .')';
				foreach($c_data as $r_key=>$r_data)
				{
					if($r_key==7)
						$data[$index]['TransferAmount']=$r_data['Amount'];
					elseif($r_key==1)
					{
						$data[$index]['TxnAmount']=$r_data['Amount'];
						$data[$index]['CreditAmount']=$r_data['Credit'];
						$data[$index]['DebitAmmount']=$r_data['Debit'];
					}
					elseif($r_key==4)
						$data[$index]['RefunAmount']=$r_data['Amount'];
					
				}
			}
			$index++;
			
		} 
		$reports= json_decode(json_encode($data));*/
		/* $reports = [
					//'loadAmount'=>$dates,
					'loadAmount'=>$loadAmount,
					'transferAmaount'=>$transferAmaount,
					'commissionAmount'=>$distCommission,
					'txnAmount'=>$txnAmount,
					'refundAmount'=>$refundAmount,
					];  */
					//print_r($reports);die;
					
		$page_title = "Payment Load";
        return view('admin.report.account-summary', compact('reports','page_title'));
    }
     public function payment_report_search(Request $request) {
		
        if ($request->export == 'Payment Report') 
		{
            ini_set("memory_limit", "10056M");
            header("Content-Type: application/vnd.ms-excel");
            header("Cache-control: private");
            header("Pragma: public");
			
			if(in_array(Auth::user()->role_id,array(1,11,14)))
				$logined_user_id = User::select('id')->where('role_id',1)->first()->id;
			else
				$logined_user_id=Auth::id();
			$reports = Report::where('provider_id',0)->whereIn('status_id',[7,6])
                //->whereDate('created_at', '=', date('Y-m-d'))
                ->where('user_id', $logined_user_id)
				 ->whereDate('created_at', '>=', $request->fromdate)
                 ->whereDate('created_at', '<=', $request->todate)
                ->orderBy('id', 'DESC')
                ->get();
          
            Excel::create($request->export.'-'.date('d-m-Y'), function ($excel) use ($request,$reports) {
                $excel->sheet('Sheet1', function ($sheet) use ($request,$reports) {
                	$arr = array();
                    foreach ($reports as $employee) {
                        if($employee->recharge_type==1){ 
							$wallet = 'Recharge'; 
						} else { $wallet = 'Money'; 
						}
								$data = array(
										date_format($employee->created_at,"d-m-Y"),date_format($employee->created_at,"H:i:s"),
											$employee->id,
										$wallet,
										$employee->user->name.'('.$employee->user->id .')', 
										  @User::select('name')->find($employee->credit_by)->name, 
										/*($employee->payment_id)?$employee->payment->user_id:'',// credited id */
										($employee->payment_id)?(@$employee->payment->netbank->bank_name):'', 
										$employee->txnid, 
										$employee->amount, 
										$employee->profit, 
										$employee->bank_charge, 
										($employee->payment_id)?(@$employee->payment->bankref):'', 
										$employee->remark, 
										 /*  @$employee->payment->pmethod_id, */
										$employee->status->status);
                        array_push($arr, $data);
                    } 
                
                    $sheet->fromArray($arr, null, 'A1', false, false)->prependRow(array('Date',
                        'Time','Id','Wallet', 'Credited By', 'Credited To','Bank','Txnid',
                        'amount', 'profit','Bank Charge','Remark','DT Remark', 'Status'
                            )
                    );
                });
            })->export('csv');
        }
		else if ($request->export == 'Search') 
		{
			if(in_array(Auth::user()->role_id,array(1,11,14)))
				$logined_user_id = User::select('id')->where('role_id',1)->first()->id;
			else
				$logined_user_id=Auth::id();
				$reports = Report::
                where('provider_id',0)
               ->where('id',$request->number)
                ->whereIn('status_id',[7,6])
                //->whereDate('created_at', '=', date('Y-m-d'))
                ->where('user_id', $logined_user_id)
                ->orderBy('id', 'DESC')
                ->paginate(40);
			 return view('admin.report.payment_report_new', compact('reports'));
        }
		else
			return "You Do not have permission";
     
    }

    public function money_transfer_report() 
	{
		if(Auth::user()->role_id==5)
		{
			$SessionID = 'uutrutuytytyuy';
			if (Auth::user()->role_id == 3) 
			{
				$members = User::where('parent_id', Auth::id())->get();
				$member_id = array();
				foreach ($members as $member) {
					$member_id[] = $member->id;
				}
				$members = User::whereIn('parent_id', $member_id)->get();
				$member_id = array();
				foreach ($members as $member) {
					$member_id[] = $member->id;
				}
				$reports = Report::where('user_id', Auth::id())
						->where('provider_id', 41)
						->where('status_id','!=',14)
						->orderBy('id', 'DESC')
						->whereIn('user_id', $member_id)
						->paginate(40);
			}
			elseif(Auth::user()->role_id == 4) 
			{
				$members = User::where('parent_id', Auth::id())->get();
				$member_id = array();
				foreach ($members as $member) {
					$member_id[] = $member->id;
				}
				$reports = Report::where('user_id', Auth::id())
						->where('provider_id', 41)
						  ->where('status_id','!=',14)
						->orderBy('id', 'DESC')
						->whereIn('user_id', $member_id)
						->paginate(40);
			}
			elseif (Auth::user()->role_id == 1 || Auth::user()->role_id==19) 
			{
				$members = User::where('parent_id', Auth::id())->get();
				$member_id = array();
				foreach ($members as $member) {
					$member_id[] = $member->id;
				}
				$reports = Report::where('user_id', Auth::id())
						->where('provider_id', 41)
						->where('status_id','!=',14)
						->orderBy('id', 'DESC')
						->paginate(40);
			}
			elseif (Auth::user()->role_id == 5) {
				$reports = Report::orderBy('id', 'DESC')->where([['status_id','!=',14],['api_id','!=',0]])->where('user_id', Auth::id())->paginate(40);
			}
			return view('reports.money_transfer_report', compact('reports'))->with('sessionid', $SessionID);
		}
		return view('errors.page-not-found');
    }
public function update(Request $request) 
	{
        if (Auth::user()->role->id == 1 || Auth::user()->role_id==19) 
		{
			
            $now = new \DateTime();
            $datetime = $now->getTimestamp();
            $ctime = $now->format('Y-m-d H:i:s');
            $report = Report::findOrFail($request->id);
            if($report->recharge_type == 1){
                $wallet_balance = 'user_balance';
                $recharge_type = 1;
            }else{
                $wallet_balance = 'user_balance';
                $recharge_type = 0;
            }
            $user_id = $report->user_id;
            $amount = $report->amount;
            $oldstatus = $report->status_id;
			
			
            if ($request->status == 2) 
			{
				 if($report->description != "BALANCE_INQUIRY" && $report->description != "CASH_WITHDRAWAL") {
					$isTxnVerifidToUpdate=ActionOtpVerification::select('id','otp_verified')->where(['txnid'=>$request->id,'otp_verified'=>1])->first();
					if(!$isTxnVerifidToUpdate)
						return response()->json(['status' => 0, 'message' => "Transaction is not verified through otp"]);
				}
                if (in_array($report->status_id,array(1,3,9,24,18,34))) 
				{
					$report->txn_status_type ="MANUAL_FAIL";
                    $report->txnid = $request->txnid;
                    $report->bank_ref = $request->bank_ref;
                    
                    $rem_txn_id = $request->txnid;
					DB::beginTransaction();
					try
					{
						
						if($report->recharge_type == 1)
						{
							$creditAmt = $report->amount + $report->debit_charge+$report->tds - $report->credit_charge;
							$report->status_id = 21;
							$report->save();
							$data=array(
									    'number' => $report->number,
										'provider_id' => $report->provider_id,
										'amount' => $amount,
										'profit'=>0,
										'api_id' => $report->api_id,
										'dist_commission' => $report->dist_commission,
										'md_commission' => $report->md_commission,
										'admin_commission' => $report->admin_commission,
										'status_id' => 4,
										'dist_commission' => $report->dist_commission,
										'md_commission' => $report->md_commission,
										'admin_commission' => $report->admin_commission,
										'description' => $report->description,
										'customer_number' => $report->customer_number,
										'type' => 'CR',
										'txn_type' => $report->txn_type .' REFUND',
										'biller_name' => $report->biller_name,
										'credit_charge' => $report->debit_charge,
										'debit_charge' => $report->credit_charge,
										'txnid'=>$report->id,
										'pay_id' => time(),
										'user_id' => $user_id,
										'recharge_type' => $recharge_type,
										'channel' => $report->channel,
										'tds' => $report->tds,
										'gst' => $report->gst,
										'txn_initiated_date' => $report->created_at,
										'total_balance2' => 0);
							
							//print_r($data);die;
							Balance::where('user_id', $user_id)->increment($wallet_balance, $creditAmt);
							$userdetail = Balance::where('user_id', $user_id)->first();
							$data['total_balance']=$userdetail->user_balance;
							$data['opening_balance']=$userdetail->user_balance - $creditAmt ;
							$reportsDetails = Report::create($data);
							$message = "Recharge Refunded successfully, ";
							$this->reverseCommission($report,"RECHARGE",$reportsDetails->id);// As cashback
                            if($report->api_id=='25'){
                                $this->updateRemitterAmount($rem_txn_id);
                            }
                        }
						else
						{
							
							if($report->api_id==10)
							{
								$report->txn_status_type = "MANUAL_FAIL";
								if($report->txn_type == "CASH_WITHDRAWAL")
								{
									$report->status_id = 2;
									$report->save();
									$this->reverseCommission($report,"DMT",$report->id);// As 
									$message="Amount Refunded Successful";
								}
								elseif($report->description=="AEPS_SETTELMENT_PENDING")
								{
									$details=$this->aepsSettelmentApprove($report,$request->txnid,2);
									$message=$details['message'];
								}
								else
								{
									$report->status_id = 2;
									$report->save();
									$message="Record Failled Successfully";
								}
								
							}
							else
							{
								$openingBalance = Balance::where('user_id', $user_id)->first();
								$creditAmt = $report->amount + $report->debit_charge+$report->tds - $report->credit_charge;
								Balance::where('user_id', $user_id)->increment('user_balance', $creditAmt);
								$userdetail = Balance::where('user_id', $user_id)->first();
								$report->status_id = 21;
								$report->save();
								$insert_id = Report::insertGetId([
										'number' => $report->number,
										'provider_id' => $report->provider_id,
										'amount' => $amount,
										'profit'=>$report->profit,
										'api_id' => $report->api_id,
										'opening_balance' => $openingBalance->user_balance,
										'status_id' => 4,
										'type' => 'CR',
										'txn_type' => $report->txn_type .' REFUND',
										'description' => $report->description,
										'biller_name' => $report->biller_name,
										'bank_ref' => $report->bank_ref,
										'txnid'=>$report->id,
										'customer_number' => $report->customer_number,
										'pay_id' => $datetime,
										'created_at' => $ctime,
										'dist_commission' => $report->dist_commission,
										'md_commission' => $report->md_commission,
										'admin_commission' => $report->admin_commission,
										'user_id' => $user_id,
										'credit_charge' => $report->debit_charge,
										'debit_charge' => $report->credit_charge,
										'recharge_type' => $report->recharge_type,
										'channel' => $report->channel,
										'tds' => $report->tds,
										'gst' => $report->gst,
										'total_balance2' => 0,
										'total_balance' => $userdetail->user_balance,
										
										'beneficiary_id' => $report->beneficiary_id,
										'txn_initiated_date' => $report->created_at,
								]);
								
								$this->reverseCommission($report,"DMT",$insert_id);// As 
								if($report->api_id=='25'){
                                    $this->updateRemitterAmount($rem_txn_id);
                                }
								$message="Amount Refunded Successful";
							}
						}
						 if($report->description != "BALANCE_INQUIRY" && $report->description != "CASH_WITHDRAWAL") 
						$isTxnVerifidToUpdate->otp_verified=0;
						DB::commit();
						
						return response()->json(['status' => 1, 'message' => $message]);
					} 
					catch(Exception $e)
					{
						DB::rollback();
						throw $e;
						$err_msg = "Something went worng. Please contact with Admin".$e->getMessage();
						return response()->json(['status' => 0, 'message' => $err_msg]);
					}
                } else {
                    $message = "Already Refunded";
                    return response()->json(['status' => 1, 'message' => $message]);
                }
            } 
			else if ($request->status == 1 || $request->status == 3) 
			{
                $report = Report::find($request->id);
                if($request->status == 1 && $report->status_id==24)
				{
					$report->txn_status_type = "MANUAL_SUCCESS";
					if($report->recharge_type == 1)
					{
						$providerDetails =Provider::find($report->provider_id);
						if($providerDetails->max_hold_txn>0 && $providerDetails->hold_txn_couter <= $providerDetails->max_hold_txn && $providerDetails->hold_txn_couter >=0){
							$providerDetails->hold_txn_couter -=1; 
							$providerDetails->save(); 
						}
					}
					$report->txnid = $request->input('txnid');
					$report->bank_ref = $request->input('bank_ref');
					$report->status_id = $request->status;
					$message = "Record Successfully Updated";									
				}
				elseif($request->status == 1 && $report->status_id==3 && $report->api_id==10)
				{
					if($report->status_id==3 && $request->status == 1 && $report->description=="CASH_WITHDRAWAL")
					{
						$isTxnVerifidToUpdate=ActionOtpVerification::select('id','otp_verified')->where(['txnid'=>$request->id,'otp_verified'=>1])->first();
						if(!$isTxnVerifidToUpdate)
							return response()->json(['status' => 0, 'message' => "Transaction is not verified through otp"]);
					}
					if($report->description=="AEPS_SETTELMENT_PENDING")
					{
						$details=$this->aepsSettelmentApprove($report,$request->txnid,1);
						$message=$details['message'];
					}else{
						$report->txn_status_type = "MANUAL_SUCCESS";
						$report->bank_ref = $request->bank_ref;
						$this->creditAepsPendingTxnAmount($report);
						$message = "Record Successfully Updated";
					}
				}
				else
				{
					if(in_array($report->status_id,array(3,18)))
						$report->txn_status_type = "MANUAL_SUCCESS";
					$report->status_id = $request->status;
					$report->txnid = $request->input('txnid');
					$report->bank_ref = $request->bank_ref;
					$message = "Record Successfully Updated";
				}
				
                $report->save();
                
                return response()->json(['status' => 1, 'message' => $message]);
            }
			elseif($request->status==24){
				$report->status_id = $request->status;
                $report->txnid = $request->input('txnid');
                $report->bank_ref = $request->input('bank_ref');
				/* if($report->recharge_type == 1)
					$report->ref_id = $request->input('refId');
				else
					$report->bank_ref = $request->input('refId'); */
                $report->save();
                $message = "Record Successfully Updated";
                return response()->json(['status' => 1, 'message' => $message]);
			}
            
        } else 
		{
            return response()->json(["status" => 0, "message" => "Sorry!. You do not have permission"]);
        }
    }
    public function updateBkp(Request $request) 
	{
        if (Auth::user()->role->id == 1) 
		{
			
            $now = new \DateTime();
            $datetime = $now->getTimestamp();
            $ctime = $now->format('Y-m-d H:i:s');
            $report = Report::findOrFail($request->id);
            if($report->recharge_type == 1){
                $wallet_balance = 'user_balance';
                $recharge_type = 1;
            }else{
                $wallet_balance = 'user_balance';
                $recharge_type = 0;
            }
            $user_id = $report->user_id;
            $amount = $report->amount;
            $oldstatus = $report->status_id;
			
			
            if ($request->status == 2) 
			{
				$isTxnVerifidToUpdate=ActionOtpVerification::select('id','otp_verified')->where(['txnid'=>$request->id,'otp_verified'=>1])->first();
				if(!$isTxnVerifidToUpdate)
					return response()->json(['status' => 0, 'message' => "Transaction is not verified through otp"]);
				
                if (in_array($report->status_id,array(1,3,9,24,18,19))) 
				{
					$report->txn_status_type ="MANUAL_FAIL";
                    $report->txnid = $request->txnid;
                    $report->bank_ref = $request->bank_ref;
					DB::beginTransaction();
					try
					{
						
						if($report->recharge_type == 1)
						{
							$creditAmt = $report->amount + $report->debit_charge+$report->tds - $report->credit_charge;
							$report->status_id = 21;
							$report->save();
							$data=array(
									    'number' => $report->number,
										'provider_id' => $report->provider_id,
										'amount' => $amount,
										'profit'=>0,
										'api_id' => $report->api_id,
										'dist_commission' => $report->dist_commission,
										'md_commission' => $report->md_commission,
										'admin_commission' => $report->admin_commission,
										'status_id' => 4,
										'dist_commission' => $report->dist_commission,
										'md_commission' => $report->md_commission,
										'admin_commission' => $report->admin_commission,
										'description' => $report->description,
										'customer_number' => $report->customer_number,
										'type' => 'CR',
										'txn_type' => $report->txn_type .' REFUND',
										'biller_name' => $report->biller_name,
										'credit_charge' => $report->debit_charge,
										'debit_charge' => $report->credit_charge,
										'txnid'=>$report->id,
										'pay_id' => time(),
										'user_id' => $user_id,
										'recharge_type' => $recharge_type,
										'channel' => $report->channel,
										'tds' => $report->tds,
										'gst' => $report->gst,
										'txn_initiated_date' => $report->created_at,
										'total_balance2' => 0);
							
							//print_r($data);die;
							Balance::where('user_id', $user_id)->increment($wallet_balance, $creditAmt);
							$userdetail = Balance::where('user_id', $user_id)->first();
							$data['total_balance']=$userdetail->user_balance;
							$data['opening_balance']=$userdetail->user_balance - $creditAmt ;
							$reportsDetails = Report::create($data);
							$message = "Recharge Refunded successfully, ";
							 $this->reverseCommission($report,"RECHARGE",$reportsDetails->id);// As cashback
                         
						}
						else
						{
							
							if($report->api_id==10)
							{
								$report->txn_status_type = "MANUAL_FAIL";
								if($report->txn_type == "CASH_WITHDRAWAL")
								{
									$report->status_id = 2;
									$report->save();
									$this->reverseCommission($report,"DMT",$report->id);// As 
									$message="Amount Refunded Successful";
								}
								elseif($report->description=="AEPS_SETTELMENT_PENDING")
								{
									$details=$this->aepsSettelmentApprove($report,$request->txnid,2);
									$message=$details['message'];
								}
								else
								{
									$report->status_id = 2;
									$report->save();
									$message="Record Failled Successfully";
								}
								
							}
							else
							{
								$openingBalance = Balance::where('user_id', $user_id)->first();
								$creditAmt = $report->amount + $report->debit_charge+$report->tds - $report->credit_charge;
								Balance::where('user_id', $user_id)->increment('user_balance', $creditAmt);
								$userdetail = Balance::where('user_id', $user_id)->first();
								$report->status_id = 21;
								$report->save();
								$insert_id = Report::insertGetId([
										'number' => $report->number,
										'provider_id' => $report->provider_id,
										'amount' => $amount,
										'profit'=>$report->profit,
										'api_id' => $report->api_id,
										'opening_balance' => $openingBalance->user_balance,
										'status_id' => 4,
										'type' => 'CR',
										'txn_type' => $report->txn_type .' REFUND',
										'description' => $report->description,
										'biller_name' => $report->biller_name,
										'bank_ref' => $report->bank_ref,
										'txnid'=>$report->id,
										'customer_number' => $report->customer_number,
										'pay_id' => $datetime,
										'created_at' => $ctime,
										'dist_commission' => $report->dist_commission,
										'md_commission' => $report->md_commission,
										'admin_commission' => $report->admin_commission,
										'user_id' => $user_id,
										'credit_charge' => $report->debit_charge,
										'debit_charge' => $report->credit_charge,
										'recharge_type' => $report->recharge_type,
										'channel' => $report->channel,
										'tds' => $report->tds,
										'gst' => $report->gst,
										'total_balance2' => 0,
										'total_balance' => $userdetail->user_balance,
										
										'beneficiary_id' => $report->beneficiary_id,
										'txn_initiated_date' => $report->created_at,
								]);
								
								$this->reverseCommission($report,"DMT",$insert_id);// As 
								$message="Amount Refunded Successful";
							}
						}
						$isTxnVerifidToUpdate->otp_verified=0;
						DB::commit();
						
						return response()->json(['status' => 1, 'message' => $message]);
					} 
					catch(Exception $e)
					{
						DB::rollback();
						$err_msg = "Something went worng. Please contact with Admin".$e->getMessage();
						return response()->json(['status' => 0, 'message' => $err_msg]);
					}
                } else {
                    $message = "Already Refunded";
                    return response()->json(['status' => 1, 'message' => $message]);
                }
            } 
			else if ($request->status == 1 || $request->status == 3) 
			{
                $report = Report::find($request->id);
                if($request->status == 1 && $report->status_id==24)
				{
					$report->txn_status_type = "MANUAL_SUCCESS";
					if($report->recharge_type == 1)
					{
						$providerDetails =Provider::find($report->provider_id);
						if($providerDetails->max_hold_txn>0 && $providerDetails->hold_txn_couter <= $providerDetails->max_hold_txn && $providerDetails->hold_txn_couter >=0){
							$providerDetails->hold_txn_couter -=1; 
							$providerDetails->save(); 
						}
					}
					$report->txnid = $request->input('txnid');
					$report->bank_ref = $request->input('bank_ref');
					$report->status_id = $request->status;
					$message = "Record Successfully Updated";									
				}
				elseif($request->status == 1 && $report->status_id==3 && $report->api_id==10)
				{
					if($report->status_id==3 && $request->status == 1 && $report->description=="CASH_WITHDRAWAL")
					{
						$isTxnVerifidToUpdate=ActionOtpVerification::select('id','otp_verified')->where(['txnid'=>$request->id,'otp_verified'=>1])->first();
						if(!$isTxnVerifidToUpdate)
							return response()->json(['status' => 0, 'message' => "Transaction is not verified through otp"]);
					}
					if($report->description=="AEPS_SETTELMENT_PENDING")
					{
						$details=$this->aepsSettelmentApprove($report,$request->txnid,1);
						$message=$details['message'];
					}else{
						$report->txn_status_type = "MANUAL_SUCCESS";
						$report->bank_ref = $request->bank_ref;
						$this->creditAepsPendingTxnAmount($report);
						$message = "Record Successfully Updated";
					}
				}
				else
				{
					if(in_array($report->status_id,array(3,18)))
						$report->txn_status_type = "MANUAL_SUCCESS";
					$report->status_id = $request->status;
					$report->txnid = $request->input('txnid');
					$report->bank_ref = $request->bank_ref;
					$message = "Record Successfully Updated";
				}
				
                $report->save();
                
                return response()->json(['status' => 1, 'message' => $message]);
            }
			elseif($request->status==24){
				$report->status_id = $request->status;
                $report->txnid = $request->input('txnid');
                $report->bank_ref = $request->input('bank_ref');
				/* if($report->recharge_type == 1)
					$report->ref_id = $request->input('refId');
				else
					$report->bank_ref = $request->input('refId'); */
                $report->save();
                $message = "Record Successfully Updated";
                return response()->json(['status' => 1, 'message' => $message]);
			}
            
        } else 
		{
            return response()->json(["status" => 0, "message" => "Sorry!. You do not have permission"]);
        }
    }
    public function fund_request_report(Request $request)
	{
		
		if(in_array(Auth::user()->role_id,array(1,15)))
			$logined_user_id = User::select('id')->where('role_id',1)->first()->id;
		else
			$logined_user_id=Auth::id();
        $members = User::where('parent_id', $logined_user_id)->pluck('id','id')->toArray(); 
		//DB::enableQueryLog();
		$loadcashesQuery = Loadcash::orderBy('id', 'DESC');
		$start_date=($request->fromdate) ? $request->fromdate. " 00:00:00" : date('Y-m-d').' 00:00:00';
		$end_date=($request->todate) ? $request->todate. " 23:59:59" : date('Y-m-d').' 23:59:59';
		$start_date=date("Y-m-d H:i:s",strtotime($start_date));
		$end_date=date("Y-m-d H:i:s",strtotime($end_date));
		if(Auth::user()->role_id == 1 || Auth::user()->role_id==19)
		{
			 $loadcashesQuery->where(function ($query) use ($members) {
                $query->whereIn('user_id',$members)
					->orWhere('request_to', '=', 2);
                      
            });
		}
		else{
			 $loadcashesQuery->whereIn('user_id',$members);
		}
		$loadcashesQuery->whereBetween('created_at',[$start_date,$end_date]);
		if($request->export=="Search" || $request->export == "Payment Request Report" )
		{
			
			if($request->export == "Payment Request Report")
			{
				return Excel::download(new PaymentRequestReportExport($start_date,$end_date), "Request Report.xlsx");
			}
			
		}
		if($request->recordCount=='All')
			$loadcashes=$loadcashesQuery->get();
		elseif($request->recordCount!='')
			$loadcashes=$loadcashesQuery->take($request->recordCount)->get();
		else
			$loadcashes=$loadcashesQuery->simplePaginate(40);
		//dd(DB::getQueryLog());die;
		return view('admin.report.payment_report', compact('loadcashes'));
    }
	public function fund_request_reportBkpJan10(Request $request)
	{
		
		if(in_array(Auth::user()->role_id,array(1,11,12,14,19)))
			$logined_user_id = User::select('id')->where('role_id',1)->first()->id;
		else
			$logined_user_id=Auth::id();
        $members = User::where('parent_id', $logined_user_id)->pluck('id','id')->toArray(); 
		//$dbFormatDate = $this->getDbFromatDate($start_date,$end_date);
		$loadcashesQuery = Loadcash::whereIn('user_id',$members)->orderBy('id', 'DESC');//whereBetween('created_at',[$dbFormatDate['start_date'],$dbFormatDate['end_date']]);
		if(Auth::user()->role_id == 1)
			$loadcashesQuery->orWhere('request_to',2);
		if($request->export=="Search" || $request->export == "Payment Request Report" )
		{
			$start_date=($request->fromdate) ? $request->fromdate. " 00:00:00" : date('Y-m-d').' 00:00:00';
			$end_date=($request->todate) ? $request->todate. " 23:59:59" : date('Y-m-d').' 23:59:59';
			$start_date=date("Y-m-d H:i:s",strtotime($start_date));
			$end_date=date("Y-m-d H:i:s",strtotime($end_date));
			if($request->export == "Payment Request Report")
			{
				return Excel::download(new PaymentRequestReportExport($start_date,$end_date), "Request Report.xlsx");
			}
			$loadcashesQuery->whereBetween('created_at',[$start_date,$end_date]);
		}
		if($request->recordCount=='All')
			$loadcashes=$loadcashesQuery->get();
		elseif($request->recordCount!='')
			$loadcashes=$loadcashesQuery->take($request->recordCount)->get();
		else
			$loadcashes=$loadcashesQuery->simplePaginate(40);
		return view('admin.report.payment_report', compact('loadcashes'));
    }
    public function fund_request_report_bkp(Request $request) {
		if(in_array(Auth::user()->role_id,array(1,11,12,14,19)))
			$logined_user_id = User::select('id')->where('role_id',1)->first()->id;
		else
			$logined_user_id=Auth::id();
        $members = User::where('parent_id', $logined_user_id)->pluck('id','id')->toArray(); 
       /*  $member_id = array();
        foreach ($members as $member) {
            $member_id[] = $member->id;
        } */
        $pmethods = Pmethod::pluck('payment_type', 'id');
        $pmethods->prepend('Please Select Payment Method');
        $netbakings = Netbank::pluck('bank_name', 'bank_code');
        $netbankings_pay = Netbank::where('paybank', '=', '1')->pluck('bank_name', 'id');
		$loadcashesQuery = Loadcash::whereIn('user_id', $members)->orderBy('created_at', 'DESC')->orderBy('created_at', 'DESC');
		if(Auth::user()->role_id == 1)
			$loadcashesQuery->OrWhere('request_to', 2);
		if($request->export == "Payment_Request_Report")
		{
			$convertedDateF = $this->getDbFromatDate($request->fromdate,$request->todate);
			$start_date  = $convertedDateF['start_date'];
			$end_date  = $convertedDateF['end_date'];
			$loginedRole = Auth::id();
			return Excel::download(new PaymentRequestExport($loginedRole,$members,$start_date,$end_date), 'Payment Request Report.xlsx');
		}
		$loadcashes = $loadcashesQuery->paginate(40);
        //return $loadcashes;
        return view('admin.report.payment_report', compact('loadcashes', 'pmethods', 'netbankings_pay'))->with('netbankings', $netbakings);
    }
	public function agent_report(Request $request) 
    {
        $start_date = ($request->fromdate) ? $request->fromdate ." 00:00:00" :  date("Y-m-d") ." 00:00:00";
        $end_date = ($request->todate) ? $request->todate ." 23:23:59" :  date("Y-m-d H:i:s");//die;
		$start_date = date("Y-m-d H:i:s", strtotime($start_date));
		$end_date = date("Y-m-d H:i:s", strtotime($end_date));
        $reportsQuery = Report::groupBy('user_id')->selectRaw('user_id, sum(amount) as total_sales,count(id) as txn_count,sum(debit_charge) as txn_charge, sum(credit_charge) as txn_commission') ;
        $reportsQuery->orderBy('user_id', 'DESC');
        $reportsQuery->where('status_id', 1);
		
		if($request->product !='')
		$reportsQuery->where('api_id', $request->product);

        $reportsQuery->orderBy('user_id', 'DESC');
        $reportsQuery->where('status_id', 1);
        if($request->user !='')
			$reportsQuery->where('user_id',$request->user);
        if($request->type == 1){
		}
		if($request->type == 2){
			$reportsQuery->groupBy('user_id');
		}
        if (in_array(Auth::user()->role_id,array(1,11,12,14))) 
            $members = User::where(['role_id'=>5])->pluck('id','id')->toArray();
		elseif(Auth::user()->role_id == 3)
		{
			$parent_details = User::where(['parent_id'=>Auth::id(),'role_id'=>4])->pluck('id','id')->toArray();
            $members=User::whereIn('parent_id',$parent_details)->orWhere('parent_id',Auth::id())->pluck('id','id')->toArray();
			$members = User::where(['role_id'=>5])->whereIn('id',$members)->pluck('id','id')->toArray();
		}
        else
            $members = User::where(['role_id'=>5,'parent_id'=>Auth::user()->parent_id])->pluck('id','id')->toArray();
        $reportsQuery->whereBetween('created_at', array($start_date, $end_date));
        $reportsQuery->whereIn('user_id',$members);
        if($request->export=="export")
        {
            $reports = $reportsQuery->get();
            Excel::create($request->export.'-'.date("d-m-Y"), function ($excel) use ($request,$reports) {
                $excel->sheet('Sheet1', function ($sheet) use ($request,$reports) {
                    $arr = array();
                    foreach ($reports as $report) {  
                        $data = array(
                            $report->user->name .'('.$report->prefix .' '.   $report->user_id .')',
                            $report->txn_count,
                            $report->total_sales,
                            $report->txn_charge,
                            $report->txn_commission,
                            );
                        array_push($arr, $data);
                    }
					$sheet->fromArray($arr, null, 'A1', false, false)->prependRow(array('User','Txn Count','Txn Amount','Charge','Commission'));
					$sheet->row(1, function($row) {
					// call cell manipulation methods
					$row->setBackground('#A9A9A9');
					$row->setFontFamily('Times New Roman');
					});
                });
            })->export('csv');
        }
		$title ="Agent Report";
        $reports = $reportsQuery->paginate(40);
		$users =User::pluck('name','id')->toArray();
        return view('admin.report.agent_report', compact('reports','title','users'));
    }
   
    public function account_statement(Request $request) 
	{
		$start_date = ($request->fromdate) ? $request->fromdate ." 00:00:00" :  date("Y-m-d") ." 00:00:00";
        $end_date = ($request->todate) ? $request->todate ." 23:23:59" :  date("Y-m-d H:i:s");//die;
		$start_date = date("Y-m-d H:i:s", strtotime($start_date));
		$end_date = date("Y-m-d H:i:s", strtotime($end_date));
		if(in_array(Auth::user()->role_id,array(1,11,12,14)))
			$logined_user_id = User::select('id')->where('role_id',1)->first()->id;
		else
			$logined_user_id = Auth::id();
		 $reports = Report::where('user_id', $logined_user_id)->whereBetween('created_at',[$start_date,$end_date])->orderBy('id', 'DESC')->simplePaginate(50);
		 $title = "Account Statement";
		if($request->export =="EXPORT")
		{
            $condition=array('user_id'=>$logined_user_id);
            return Excel::download(new AccountReportExport($condition,$start_date,$end_date), 'AccountStatement.xlsx');
		}
		return view('admin.account_statement', compact('reports','title'));
    }

	public function search_account_statement(Request $request)
	{
		/*$from = $request->fromdate;
		$to = $request->todate;
		$reports = Report::where('user_id', Auth::id())
		
		 ->whereDate('created_at', '>=', $request->fromdate)
		 ->whereDate('created_at', '<=', $request->todate)
		->where('recharge_type', 0)->orderBy('id', 'DESC')->paginate(40);*/
        if ($request->export == 'search') {
                
                $reports = Report::orderBy('id', 'DESC')
                       /*  ->whereDate('created_at', '>=', $request->fromdate)
                        ->whereDate('created_at', '<=', $request->todate) */
                        ->where('number', $request->number)
                        ->orderBy('id', 'DESC')
                        ->paginate(40);
			return view('admin.account_statement', compact('reports'));
	    }
	}
    public function account_statement_recharge(Request $request) {
        if ($_GET) 
		{
            $dateto = $request->todate;
            $datefrom = $request->fromdate;
        } else {
            $datefrom = date('Y-m-d', time());
            $dateto = date('Y-m-d', time());
        }
        if (Auth::user()->role_id = 1) {
            $reports = Report::where('user_id', Auth::id())->where('recharge_type', 1)->where('description', '!=', 'Wallet Reffil')->orderBy('id', 'DESC')->paginate(40);
           // return $reports;
        } elseif (Auth::user()->role_id == 3) {
            $members = User::where('parent_id', Auth::id())->get();
            $member_id = array();
            foreach ($members as $member) {
                $member_id[] = $member->id;
            }

            $members = User::whereIn('parent_id', $member_id)->get();
            $member_id_new = array();
            foreach ($members as $member) {
                $member_id_new[] = $member->id;
            }
            $myid = array(Auth::id());
            $mmember = array_merge($member_id, $member_id_new, $myid);
            $reports = Report::whereIn('user_id', $mmember)->where('recharge_type', 1)->orderBy('id', 'DESC')->paginate(40);
        }
        elseif (Auth::user()->role_id == 4) {
            $members = User::where('parent_id', Auth::id())->get();
            $member_id = array();
            foreach ($members as $member) {
                $member_id[] = $member->id;
            }
            $myid = array(Auth::id());
            $mmember = array_merge($member_id, $myid);
            $reports = Report::whereIn('user_id', $mmember)->where('recharge_type', 1)->whereIn('status_id', [6, 7])->whereDate('created_at', '>=', $datefrom)->whereDate('created_at', '<=', $dateto)->orderBy('id', 'DESC')->paginate(40);
        }

        return view('admin.account_statement', compact('reports'));
    }

    public function recharge_account_statement() {
        $reports = Report::where('user_id', Auth::id())->where('recharge_type', 1)->orderBy('id', 'DESC')->paginate(40);
        //return $reports;
        return view('admin.account_statement', compact('reports'));
    }

    public function export_account_statement(Request $request) {
        ini_set("memory_limit", "200056M");
        header("Content-Type: application/vnd.ms-excel");
        header("Cache-control: private");
        header("Pragma: public");
        $SessionID = Company::find(3)->sessionid;
        if ($request->export == 'Account Statement') {
            Excel::create($request->export.'-'.date("d-m-Y"), function ($excel) use ($request) {
                $excel->sheet('Sheet1', function ($sheet) use ($request) {
					if(in_array(Auth::user()->role_id,array(1,11,12,14)))
						$logined_user_id = User::select('id')->where('role_id',1)->first()->id;
					else
						$logined_user_id = Auth::id();
                    $acc_statement = Report::select('id','user_id','amount','number','txnid','description','total_balance','status_id','credit_by','created_at')->where('user_id', $logined_user_id)
					->orderBy('id', 'DESC')
					->where('api_id',0)
					->whereIn('status_id',[6,7])
					 ->whereDate('created_at', '>=', $request->fromdate)
                     ->whereDate('created_at', '<=', $request->todate)
					->get();
                    $arr = array();
                    foreach ($acc_statement as $acc_statements) {
						if($acc_statements->status_id==6)
						{
							@$amo = $acc_statements->amount;
						}
						 else
						{
							@$amo = $acc_statements->amount;
						}
						if(is_numeric($acc_statements->credit_by))
							$name = User::find($acc_statements->credit_by)->name.'('.$acc_statements->credit_by.')';
						else
							$name = $acc_statements->credit_by;
						
                        $data = array(
							date_format($acc_statements->created_at,"d-m-Y H:i:s"),
							$acc_statements->user->name.'('.$acc_statements->user_id.')', 
							$acc_statements->number, 
							$acc_statements->txnid, 
							$acc_statements->description,
							$name,
							@$amo,
							$acc_statements->total_balance, 
							$acc_statements->status->status);
                        array_push($arr, $data);
                    }

                    //set the titles
                    $sheet->fromArray($arr, null, 'A1', false, false)->prependRow(array(
                        'Date', 'User Name', 'Mobile Number', 'Txn ID', 'Description','Debit/Credit',
                        'Amount','Balance', 'Status'
                            )
                    );
								$sheet->row(1, function($row) {
								// call cell manipulation methods
								$row->setBackground('#A9A9A9');
								$row->setFontFamily('Times New Roman');
						});
					
                });
            })->export('csv');
        }
    }

public function export_operator_detail(Request $request)
{
    return "Coming Soon!";
    // ini_set("memory_limit", "200056M");
    //     header("Content-Type: application/vnd.ms-excel");
    //     header("Cache-control: private");
    //     header("Pragma: public");
    //     $SessionID = Company::find(3)->sessionid;
    //     if ($request->export == 'export') {
    //         Excel::create('records', function ($excel) use ($request) {
    //             $excel->sheet('Sheet1', function ($sheet) use ($request) {
    //                 $export_operator_detail = Report::orderBy('id', 'DESC')
    //                  ->whereDate('created_at', '>=', $request->fromdate)
    //                  ->whereDate('created_at', '<=', $request->todate)
    //                 ->get();
    //                 $arr = array();
                    

               
    //                 $sheet->fromArray($arr, null, 'A1', false, false)->prependRow(array(
    //                     'Date', 'Name', 'Mobile Number', 'Tx ID', 'Description','Debit/creadit',
    //                     'Balance', 'Status'
    //                         )
    //                 );
    //             });
    //         })->export('csv');
    //     }
}
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request) {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function report() {
		
        //return view('agent.report');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id) {
        //
    }

    public function destroy($id) {
        //
    }

    public function searchall_all_ca(Request $request) {

        $SessionID = "klklk";
        if ($request->export == 'export') {
            ini_set("memory_limit", "800056M");
            header("Content-Type: application/vnd.ms-excel");
            header("Cache-control: private");
            header("Pragma: public");
            $members = User::where('parent_id', Auth::id())->where('company_id', 3)->get();
            $member_id = array();
            foreach ($members as $member) {
                $member_id[] = $member->id;
            }
            $members = User::whereIn('parent_id', $member_id)->get();
            $member_id = array();
            foreach ($members as $member) {
                $member_id[] = $member->id;
            }
            Excel::create('records-'.date("d-m-Y"), function ($excel) use ($request, $member_id) {
                $excel->sheet('Sheet1', function ($sheet) use ($request, $member_id) {
                    $employees = Report::orderBy('id', 'DESC')
                            ->whereDate('created_at', '>=', $request->fromdate)
                            ->whereDate('created_at', '<=', $request->todate)
                            ->where('provider_id', 41)
                            ->whereIn('user_id', $member_id)
                            ->orderBy('id', 'DESC')
                            ->take(3000)
                            ->get();
                    $arr = array();
                    foreach ($employees as $employee) {
                        $pparent_id = User::find($employee->user->id)->parent_id;
                        $pparent_id_id = User::find($employee->user->parent_id)->parent_id;
                        if ($employee->user->parent_id == Auth::id()) {
                            $upfront = User::find($employee->user->id)->upscheme->scheme;
                        } else {
                            $upfront = User::find($employee->user->parent_id)->upscheme->scheme;
                        }
                        $upfront_p = ($employee->amount * $upfront) / 100;
                        $fee_charge = ($employee->api_id == 3) ? ($employee->amount * 0.1) / 100 : 0;
						if($employee->channel==1 && $employee->api_id == 3)
						{
						 $per_txn_cost_imps=2.5;
						} elseif($employee->channel==2 && $employee->api_id == 3) { $per_txn_cost_imps=1.5; }
						else
						{
                        $per_txn_cost_imps =(($employee->api_id == 4) ? 5.65 : (($employee->api_id == 5) ? 3.5 : 0.85));
                        }
						$sms_charge = ($employee->api_id == 3) ? 0.09 : 0;
                        $st = (($fee_charge + $per_txn_cost_imps + $sms_charge) * 15) / 100;
                        $total_count = $fee_charge + $per_txn_cost_imps + $sms_charge + $st;
                        $earning_trans_cost = $upfront_p + $employee->profit;
                        $data = array(date_format($employee->created_at,"d-m-Y"),date_format($employee->created_at,"H:i:s"),$employee->id, $employee->pay_id, User::find($employee->user->parent_id)->name,
                            $upfront,
                            $employee->user->name,
                           
                            $employee->api->api_name,
                            $employee->number,
                            $employee->txnid,
                            ($employee->channel == 1) ? 'NEFT' : 'IMPS',
                            $employee->amount, $upfront,
                            $employee->profit,
                            $upfront_p + $employee->profit,
                            $fee_charge,
                            $per_txn_cost_imps,
                            $sms_charge,
                            $st,
                            $total_count,
                            $earning_trans_cost - $total_count,
                            (($earning_trans_cost - $total_count) * 15) / 100,
                            ($earning_trans_cost - $total_count) - ((($earning_trans_cost - $total_count) * 15) / 100),
                            $employee->total_balance, $employee->status->status);
                        array_push($arr, $data);
                    }
                    $sheet->fromArray($arr, null, 'A1', false, false)->prependRow(array('Date ',' Time',
                        'Id', 'Pay Id', 'Parent Name', 'Parent Scheme(%)', 'Agent Name',  'Product',
                        'Number', 'Reference ID', 'MODE', 'Amount', 'Upfront', 'Profit', 'Earning per Txn', 'Free Charge', 'Transaction cost', 'SMS COST', 'ST', 'TOTAL API COST', 'SHIGHRA PROFIT', 'SHIGHRA ST', 'NET PROFIT', 'Total', 'Status'
                            )
                    );
                });
            })->export('csv');
        }
    }

    function recharge_report() {
        $id = Auth::id();
        $reports = Report::where('user_id', $id)
                ->where('recharge_type',1)
                // ->whereBetween('created_at',['2016-01-01','2017-09-01'])
                ->where('status_id', '!=', 7)
                ->orderBy('id', 'DESC')
                ->paginate(50);


            

        return view('report.recharge_report', compact('reports'));
    }

    public function search_by_date(Request $request) {
		
        $companyDetail = new \App\Library\Companyid;
        $company_detail = $companyDetail->get_company_detail();
       // $SessionID = $company_detail->sessionid;
        if ($request->export == 'export') 
		{
            Excel::create('Recharge Reports-'.date("d-m-Y"), function ($excel) use ($request) {
                $excel->sheet('Sheet1', function ($sheet) use ($request) {
                    $employees = Report::orderBy('id', 'DESC')
                            ->whereDate('created_at', '>=', $request->fromdate)
                            ->whereDate('created_at', '<=', $request->todate)
                            ->where('user_id', Auth::id())
                            ->orderBy('id', 'DESC')
                            ->get();
                    $arr = array();
                    foreach ($employees as $employee) 
					{
						$s = $employee->created_at;
						$dt = new \DateTime($s);
                        $data = array($dt->format('d-m-Y'),$dt->format('H:i:s'),$employee->id, $employee->pay_id, $employee->user->name,$employee->api, $employee->number, $employee->txnid, $employee->amount, $employee->profit, $employee->total_balance, $employee->status->status);
                        array_push($arr, $data);
                    }

                    //set the titles
                    $sheet->fromArray($arr, null, 'A1', false, false)->prependRow(array(
                        'Date','Time','Id', 'Pay Id', 'Name', 'Product',
                        'Number', 'Reference ID', 'Amount', 'Profit', 'Total', 'Status'
                            )
                    );
                });
            })->export('csv');
        }
        if ($request->export == 'search') 
		{
			
            $recharge_report  = Report::orderBy('id', 'DESC')
                   
                    ->where('number',$request->number)
                    ->where('user_id', Auth::id())
                    ->orderBy('id', 'DESC')
                    ->paginate(40);
            return view('reports.all-recharge-report', compact('recharge_report'));//->with('sessionid', $SessionID);
        } 
		elseif ($request->number == '') 
		{
           /*  $dateto = $request->todate;
            $datefrom = $request->fromdate; */
            $recharge_report  = Report::orderBy('id', 'DESC')
                    ->whereDate('created_at', '>=', date('Y-m-d'))
                    /* ->whereDate('created_at', '<=', $dateto) */
                    ->where('user_id', Auth::id())
                    ->orderBy('id', 'DESC')
                    ->paginate(40);

            return view('reports.all-recharge-report', compact('recharge_report'))->with('sessionid', $SessionID);
        }
        $companyDetail = new \App\Library\Companyid;
        $company_detail = $companyDetail->get_company_detail();
       // $SessionID = $company_detail->sessionid;
        $number = $request->number;
        $start = new \DateTime('now');
        $start->modify('first day of this month');
        $end = new \DateTime('now');
        $end->modify('last day of this month');

        
        $recharge_report  = Report::orderBy('id', 'DESC')
                ->where('number', $number)
            
                ->where('user_id', Auth::id())
                //->where('user_id', Auth::id())
                ->orderBy('id', 'DESC')
                ->paginate(40);
        return view('reports.all-recharge-report', compact('recharge_report'));//->with('sessionid', $SessionID);
    }

   

    public function txn_inprocess_report()
    {
        // $start_date = '2017-12-25 00:00:00';
        // $end_date = date('Y-m-d').' 23:59:56';
        $reports = Report::orderBy('id','DESC')
        ->where('status_id',18)
        //->whereBetween('created_at', [$start_date,$end_date])
        ->paginate(40);
        return view('admin.txninprocess',compact('reports'));
    }

    public function refund_pending(Request $request)
    {
        $refund_pend = Refundrequest::orderBy('id','desc')
        ->where('status',1)
        ->get();
        return view('admin.report.refunded_reports',compact('refund_pend'));
    }
	
	public function searchall_agent(Request $request) {
        if (Auth::user()->role_id == 1 || Auth::user()->role_id==19) {
            $SessionID = "hello";
			  $reports = Report::groupBy('user_id')
                                ->selectRaw('user_id, sum(amount) as total_sales,count(id) as txn_count,sum(profit) as txn_charge, sum(credit_charge) as txn_commission') 
                                ->orderBy('id', 'DESC')
                                ->whereDate('created_at', '=', date('Y-m-d'))
                                ->where('status_id', 1)
                                ->whereIn('api_id',[3,4,5])
                                ->whereDate('created_at', '>=', $request->fromdate)
                                ->whereDate('created_at', '<=', $request->todate)
                                ->get();
			
			if($request->export == 'DATE_SEARCH')
			{
				
				return view('admin.report.agent_report', compact('reports', 'reports_verify'));
			}
            if ($request->export == 'export') 
			{
                ini_set("memory_limit", "10056M");
                header("Content-Type: application/vnd.ms-excel");
                header("Cache-control: private");
                header("Pragma: public");
                Excel::create('Agent Volume Report-'.date("d-m-Y"), function ($excel) use ($request,$reports) {
                    $excel->sheet('Sheet1', function ($sheet) use ($request,$reports) {                     
                        $arr = array();
                        foreach ($reports as $employee) {
                            $data = array(@$employee->user->name,@$employee->user->id,@$employee->txn_count,@$employee->total_sales,@$employee->txn_charge,@$employee->txn_commission);
                            array_push($arr, $data);
                        }
                        //set the titles
                        $sheet->fromArray($arr, null, 'A1', false, false)->prependRow(array(
                           'Agent Name', 'Agent Id', 'Txn Count','Success Txn Amount','Txn charge', 'Txn Commission'
                                )
                        );
                    });
                })->export('csv');
            }
            if ($request->search == 'search') {
                $reports = Report::orderBy('id', 'DESC')
                        ->whereDate('created_at', '>=', $request->fromdate)
                        ->whereDate('created_at', '<=', $request->todate)
                        //->where('user_id', Auth::id())
                        ->orderBy('id', 'DESC')
                        ->paginate(40);
                return view('admin.report.agent_report', compact('reports'));
            } else {
                $number = $request->number;
                $start = new \DateTime('now');
                $start->modify('first day of this month');
                $end = new \DateTime('now');
                $end->modify('last day of this month');
                $reports = Report::groupBy('user_id')
                        ->selectRaw('amount, sum(amount) as total_sales')
                        ->orderBy('id', 'DESC')
                        ->whereDate('created_at', '=', date('Y-m-d'))
                        ->where('status_id', 1)
                        //->whereIn('user_id', $member_id)
                        //->whereBetween('created_at', array($start, $end))
                        ->get();
                $reports = Report::orderBy('id', 'DESC')
                        ->where('number', $number)
                        ->orWhere('txnid', $number)
                        ->orWhere('pay_id', $number)
                        ->orWhere('customer_number', $number)
                        //->where('user_id', Auth::id())
                        ->orderBy('id', 'DESC')
                        ->paginate(40);
                return view('admin.report.agent_report', compact('reports'))->with('sessionid', $SessionID);
            }
        } elseif (Auth::user()->role_id == 3) {
            $members = User::where('parent_id', Auth::id())->get();
            $member_id = array();
            foreach ($members as $member) {
                $member_id[] = $member->id;
            }
            $members = User::whereIn('parent_id', $member_id)->get();
            $member_id_new = array();
            foreach ($members as $member) {
                $member_id_new[] = $member->id;
            }
            $myid = array(Auth::id());
            $mmember = array_merge($member_id, $member_id_new, $myid);
            $SessionID = Company::find(1);//->sessionid;
            if ($request->export == 'export') {
                ini_set("memory_limit", "10056M");
                header("Content-Type: application/vnd.ms-excel");
                header("Cache-control: private");
                header("Pragma: public");
                Excel::create('Agent Volume Report-'.date("d-m-Y"), function ($excel) use ($request) {
                    $excel->sheet('Sheet1', function ($sheet) use ($request) {
                        $members = User::where('parent_id', Auth::id())->get();
                        $member_id = array();
                        foreach ($members as $member) {
                            $member_id[] = $member->id;
                        }
                        $members = User::whereIn('parent_id', $member_id)->get();
                        $member_id_new = array();
                        foreach ($members as $member) {
                            $member_id_new[] = $member->id;
                        }
                        $myid = array(Auth::id());
                        $mmember = array_merge($member_id, $member_id_new, $myid);
                        $employees = Report::groupBy('user_id')
                                ->selectRaw('amount, sum(amount) as total_sales')
                                ->orderBy('id', 'DESC')
                                ->whereIn('user_id', $mmember)
                                //->whereDate('created_at', '=', date('Y-m-d'))
                                ->where('status_id', 1)
                                ->whereDate('created_at', '>=', $request->fromdate)
                                ->whereDate('created_at', '<=', $request->todate)
                                ->get();
                        $arr = array();
                        foreach ($employees as $employee) {
                            $data = array($employee->user->id, $employee->user->name, $employee->total_sales, $employee->user->parent_id);
                            array_push($arr, $data);
                        }
                        //set the titles
                        $sheet->fromArray($arr, null, 'A1', false, false)->prependRow(array(
                            'Id', 'Name', 'Total Sale', 'Parent'
                                )
                        );
                    });
                })->export('csv');
            }
            if ($request->search == 'search') {
                $reports = Report::orderBy('id', 'DESC')
                        ->whereDate('created_at', '>=', $request->fromdate)
                        ->whereDate('created_at', '<=', $request->todate)
                        ->whereIn('user_id', $mmember)
                        ->orderBy('id', 'DESC')
                        ->paginate(40);
                return view('admin.report.agent_report', compact('reports'));
            } else {
                $number = $request->number;
                $start = new \DateTime('now');
                $start->modify('first day of this month');
                $end = new \DateTime('now');
                $end->modify('last day of this month');
                $reports = Report::orderBy('id', 'DESC')
                        ->where('number', $number)
                        ->orWhere('txnid', $number)
                        ->orWhere('pay_id', $number)
                        ->orWhere('customer_number', $number)
                        ->whereIn('user_id', $mmember)
                        ->orderBy('id', 'DESC')
                        ->paginate(40);
                return view('admin.report.agent_report', compact('reports'))->with('sessionid', $SessionID);
            }
        } else {
            $members = User::where('parent_id', Auth::id())->get();
            $member_id_new = array();
            foreach ($members as $member) {
                $member_id_new[] = $member->id;
            }
            $myid = array(Auth::id());
            $mmember = array_merge($member_id_new, $myid);
            $SessionID = Company::find(1);//->sessionid;
            if ($request->export == 'export') {
                ini_set("memory_limit", "10056M");
                header("Content-Type: application/vnd.ms-excel");
                header("Cache-control: private");
                header("Pragma: public");
                Excel::create('Agent Volume Report-'.date("d-m-Y"), function ($excel) use ($request) {
                    $excel->sheet('Sheet1', function ($sheet) use ($request) {
                        $members = User::where('parent_id', Auth::id())->get();
                        $member_id_new = array();
                        foreach ($members as $member) {
                            $member_id_new[] = $member->id;
                        }
                        $myid = array(Auth::id());
                        $mmember = array_merge($member_id_new, $myid);
                        $employees = Report::groupBy('user_id')
                                ->selectRaw('*, sum(amount) as total_sales')
                                ->orderBy('id', 'DESC')
                                ->whereIn('user_id', $mmember)
                                //->whereDate('created_at', '=', date('Y-m-d'))
                                ->where('status_id', 1)
                                ->whereDate('created_at', '>=', $request->fromdate)
                                ->whereDate('created_at', '<=', $request->todate)
                                ->get();
                        $arr = array();
                        foreach ($employees as $employee) {
                            $data = array($employee->user->id, $employee->user->name, $employee->total_sales, $employee->user->parent_id);
                            array_push($arr, $data);
                        }
                        //set the titles
                        $sheet->fromArray($arr, null, 'A1', false, false)->prependRow(array(
                            'Id', 'Name', 'Total Sale', 'Parent'
                                )
                        );
                    });
                })->export('csv');
            }
            if ($request->search == 'search') {
                $reports = Report::orderBy('id', 'DESC')
                        ->whereDate('created_at', '>=', $request->fromdate)
                        ->whereDate('created_at', '<=', $request->todate)
                        ->whereIn('user_id', $mmember)
                        ->orderBy('id', 'DESC')
                        ->paginate(40);
                return view('admin.report.agent_report', compact('reports'));
            } else {
                $number = $request->number;
                $start = new \DateTime('now');
                $start->modify('first day of this month');
                $end = new \DateTime('now');
                $end->modify('last day of this month');
                $reports = Report::orderBy('id', 'DESC')
                        ->where('number', $number)
                        ->orWhere('txnid', $number)
                        ->orWhere('pay_id', $number)
                        ->orWhere('customer_number', $number)
                        ->whereIn('user_id', $mmember)
                        ->orderBy('id', 'DESC')
                        ->paginate(40);
                return view('admin.report.agent_report', compact('reports'))->with('sessionid', $SessionID);
            }
        }
    }
	public function exportUserRecord(Request $request)
		{
			
			if(Auth::user()->role_id ==1 || Auth::user()->role_id==19)
			{
				if ($request->export == 'Agent Report') 
                {
                $dateFormat = $this->getDbFromatDate($request->fromdate, $request->todate);
                $userDetails = $this->getUserDetails();
                $condition=array('user_id'=>$userDetails->id);
                return Excel::download(new UserReportExport($condition,$dateFormat['start_date'],$dateFormat['end_date']), 'Agent Report.xlsx');
                }
				$query = Report::orderBy('id', 'DESC');
				if($request->fromdate !='')
				{
					$query->whereDate('created_at', '>=', $request->fromdate);
				}
				if($request->todate !='')
				{
					$query->whereDate('created_at', '<=', $request->todate);
					
				}
				
				$query->where('status_id','!=',14);
				if($request->export_user_id == 0)
				{
					$export_users=User::whereNotIn('role_id',[1,2,11,12,13,14])->orderBy('name','asc')->pluck('id','id')->toArray();
					$query->whereIn('user_id', $export_users);
				}
				else
					$query->where('user_id', $request->export_user_id);
				$employees = $query->get();
				Excel::create($request->export.'-'.date("d-m-Y"), function ($excel) use ($request,$employees) {
					$excel->sheet('Sheet1', function ($sheet) use ($request,$employees) 
					{
						$arr = array();
						foreach ($employees as $employee) 
						{
							if($employee->api_id==2)
                             $dis = @$employee->description;
						 else
                            $dis = @$employee->beneficiary->bank_name ;
                           
									$s = $employee->created_at;
									$dt = new \DateTime($s);
									$data = array($dt->format('d-m-Y'),$dt->format('H:i:s'), 
									@$employee->id, 
									@$employee->user->name .'('.$employee->user_id .')', 
									@$employee->api->api_name, 
									@$employee->provider->provider_name, 
									@$dis, 
									@$employee->number, 
									@$employee->txnid, 
									number_format(@$employee->amount,2),
									number_format(@$employee->profit,2),
									number_format(@$employee->total_balance,2),
									@$employee->status->status);
									
							array_push($arr, $data);
						}

						//set the titles
						$sheet->fromArray($arr, null, 'A1', false, false)->prependRow(array(
							'Date','Time', 'Id', 'User', 'Product',
							'Provider', 'Bank Name', 'Number', 'Ref Id', 'Amount', 'Charge','Balance', 'Status',
								)
						);
							
								$sheet->row(1, function($row) {
								
								$row->setBackground('#A9A9A9');
								$row->setFontFamily('Times New Roman');
								});
					});
				})->export('csv');
			
		}
		return view('errors.page-not-found');
	}
	public function dailyMaintainBalance(Request $request)
	{
		if(Auth::user()->role_id == 1 || Auth::user()->role_id==19)
		{
			if($request->fromdate)
				$start_date= $request->fromdate ." 00:00:00";
			else
				$start_date= date('Y-m-d') ." 00:00:00";
			if($request->todate)
				$end_date= $request->todate." 23:59:59";
			else
				$end_date= date('Y-m-d') ." 23:59:59";
			
			//User::where(['role_id'=>5])->pluck('parent_id','parent_id')->toArray();
			$reports = User::
				join('reports', function ($join) {
					$join->on('users.id', '=', 'reports.user_id')
						->where('reports.status_id', '=', 1);
				})
				->selectRaw('sum(reports.amount) as txn_amount,users.parent_id as parent_id,count(reports.id) as txn_count,users.role_id')
				->whereIn('reports.api_id',[16,2,17])
				->whereBetween('reports.created_at', [$start_date,$end_date])
				->where('users.role_id','=',5)
				->groupBy('users.parent_id')
				->get();
			if($request->export =="export")
			{
				ini_set("memory_limit", "10056M");
                header("Content-Type: application/vnd.ms-excel");
                header("Cache-control: private");
                header("Pragma: public");
                Excel::create('Daily Balance Reports-'.date("d-m-Y"), function ($excel) use ($reports) {
                    $excel->sheet('Sheet1', function ($sheet) use ($reports) {
                       
                        $arr = array();
                        foreach ($reports as $value) {
                            $data = array(@$value->parent->name .'('. @$value->parent_id .')',
								@$value->txn_count, 
								@$value->txn_amount);
                            array_push($arr, $data);
                        }
                        //set the titles
                        $sheet->fromArray($arr, null, 'A1', false, false)->prependRow(array(
                            'Parent name', 'Txn Count', 'Txn Amount',)
                        );
                    });
                })->export('csv');
			}
			return view('admin.daily-balance-reports',compact('reports'));
				
		}
		return view('errors.page-not-found');
		
	}
	public function fundTransferRequest()
	{
		if(Auth::user()->role_id ==5)
		{			
		$reports = Report::select('id','status_id','amount','created_at','total_balance','description','credit_by')->where(['user_id'=>Auth::id(),'api_id'=>0,'provider_id'=>0,'status_id'=>7,'txnid'=>'DT'])->orderBy('id','DESC')->paginate(20);
		return view('payservice.fund_transfer_request',compact('reports'));
		}
	}
	
private function exportExcelSheet($reports,$request)
    {
        
        ini_set("memory_limit", "10056M");
        header("Content-Type: application/vnd.ms-excel");
        header("Cache-control: private");
        header("Pragma: public");
        Excel::create('All Txn Reports-'.date("d-m-Y"), function ($excel) use ($reports) {
            $excel->sheet('Sheet1', function ($sheet) use ($reports) {
               
                $arr = array();
                foreach ($reports as $value) 
                {
                    $data = array(
                        $value->created_at, 
                        $value->id, 
                        $value->user->name .'('. $value->user->prefix . ' - ' . $value->user_id .')',
                        @$value->user->member->company, 
                        @$value->api->api_name, 
                        @$value->provider->provider_name, 
                        @$value->beneficiary->bank_name, 
                        
						(is_numeric($value->credit_by)) ?  ($value->creditBy->name) : ($value->credit_by),
						@$value->description, 
						@$value->number,
						@$value->customer_number,
                        @$value->txnid,
                        @$value->amount,
                        @$value->credit_charge,
                        @$value->debit_charge,
                        @$value->gst,
                        @$value->tds,
                        @$value->total_balance,
                        @$value->status->status
                        );
                    array_push($arr, $data);
                }
                //set the titles
                $sheet->fromArray($arr, null, 'A1', true, false)->prependRow(array('Date & Time','Id','User Name','Outlet','Product','Provider','Bank','Credit To/ Debit From','Description','Acc No/ Mob No','Remitter Number','Ref Id','Amount','Credit Amount','Debit Amount','GST','TDS','Remaining Balance','Status')
                );
            });
        })->export('csv');
        
    }
	public function getTransationDetails(Request $request)
	{
		$reports = Report::selectRaw('id,user_id,amount,profit,status_id,txnid,refund,number,customer_number,aeps_sattelment_id,description,api_id,bank_ref')->find($request->id);
		if($reports->api_id==10 && $reports->description=="AEPS_SETTELMENT_PENDING")
		{
			$reports = array(
				'id'=>$reports->id,
				'customerNumber'=>$reports->user->mobile,
				'amount'=>$reports->amount,
				'txnid'=>$reports->txnid,
				'statusId'=>$reports->status_id,
				'accountNumber'=>$reports->aepssettlements->account_number,
				'bankName'=>$reports->aepssettlements->bank_name,
				'branchName'=>$reports->aepssettlements->branch_name,
				'ifsc'=>$reports->aepssettlements->ifsc,
				'userDetails'=>$reports->user->name .'('. $reports->user_id .')',
			);
			return response()->json(['status'=>1,'message'=>$reports]);
			
		}
		else
		{
			$reports = array(
				'id'=>$reports->id,
				'amount'=>$reports->amount,
				'apiName'=>$reports->api->api_name,
				'txnid'=>$reports->txnid,
				'status_id'=>$reports->status_id,
				'customer_number'=>$reports->customer_number,
				'description'=>$reports->description,
				'bank_ref'=>$reports->bank_ref ,
				'number'=>$reports->number,
				'userDetails'=>$reports->user->name .'('. $reports->user_id .')',
			);
			return response()->json(['status'=>1,'message'=>$reports]);
		}
		
	}
	public function getTxnDetails(Request $request)
	{
		$reports = Report::selectRaw('id,user_id,amount,profit,status_id,txnid,refund,number,customer_number,provider_id,ref_id')->where(['id'=>$request->id])->get();
		$data = $reports->map(function($report){
			return [
				'id'=>$report->id,
				'status_id'=>$report->status_id,
				'ref_id'=>$report->ref_id,
				'txnid'=>$report->txnid,
				'number'=>$report->number,
				'amount'=>$report->amount,
				'customer_number'=>$report->customer_number,
				'provider_name'=>$report->provider->provider_name,
			];
		});
		return response()->json(['status'=>1,'message'=>$data]);
		
	}
	private function creditRefundAmount($report)
	{
		$creditDetails = Report::where(['api_id'=>$report->api_id,'status_id'=>22,'txnid'=>$report->id])->get();
		/* if(count($creditDetails) <=3)
		{ */
			foreach($creditDetails as $creditDetail)
			{
				$creditDetail->status_id = 25;
				$creditDetail->save();
				Balance::where('user_id', $creditDetail->user_id)->decrement('user_balance', $creditDetail->credit_charge);
				$userdetail = Balance::where('user_id', $creditDetail->user_id)->first();
				Report::create([
								'number' => $creditDetail->number,
								'provider_id' => $creditDetail->provider_id,
								'amount' => $creditDetail->amount,
								'profit'=>$creditDetail->profit,
								'api_id' => $creditDetail->api_id,
								'status_id' => 26,
								'description' => 'Manual Refund',
								'bank_ref' => $report->id,
								'txnid'=>$report->id,
								'pay_id' => time(),
								'user_id' => $creditDetail->user_id,
								'recharge_type' => $creditDetail->recharge_type,
								'channel' => 2,
								'credit_charge' => 0,
								'debit_charge' => $creditDetail->credit_charge,
								'tds' => $report->tds,
								'gst' => $report->gst,
								'total_balance' => $userdetail->user_balance,
								'beneficiary_id' => $creditDetail->beneficiary_id,
								]);
			}
		/* } */
	}
  public function moneyTxnHistory(Request $request)
    {
       if (Auth::user()->role_id == 5)
        {
			$product = $request->product;
			$status_id = $request->status_id;
			$reportsQuery = Report::where('user_id', Auth::id());
							$reportsQuery->orderBy('id', 'DESC')->where('api_id',$request->product);
							$reportsQuery->where('status_id',$request->status_id);
							
             if($request->export =="SEARCH" || $request->export =="EXPORT")
               {
                    $start_date = ($request->fromdate) ? $request->fromdate ." 00:00:00" : date("Y-m-d") . " 00:00:00";
                    $end_date = ($request->todate) ? $request->todate ." 23:59:59" : date("Y-m-d H:i:s");
                    $reportsQuery->whereBetween('created_at', [$start_date,$end_date]);
                }
            if($request->SEARCH_TYPE=="ID")
                    $reportsQuery->where('id', $request->number);
			elseif($request->SEARCH_TYPE=="TXN_ID")
				$reportsQuery->where('txnid', $request->number);
			elseif($request->SEARCH_TYPE=="ACC")
				$reportsQuery->where('number', $request->number);
			elseif($request->SEARCH_TYPE=="MOB_NO")
				$reportsQuery->where('customer_number', $request->number);
             if($request->export=="EXPORT")
            {
                $reports = $reportsQuery->get();
				$dateFormat = $this->getDbFromatDate($request->fromdate, $request->todate);
			$userDetails = $this->getUserDetails();
			$condition=array('user_id'=>$userDetails->id);
                //$this->exportExcelSheetDMT1($reports,$request);  
              return Excel::download(new DmtExport($condition,$dateFormat['start_date'],$dateFormat['end_date']), 'DMT-Transaction.xlsx');  
            }
			$reports = $reportsQuery->simplePaginate(40);                
                return view('reports.money_txn_history',compact('reports','product','status_id'));
        }
    }
    private function exportExcelSheetDMT1($reports,$request)
    { 
        ini_set("memory_limit", "10056M");
        header("Content-Type: application/vnd.ms-excel");
        header("Cache-control: private");
        header("Pragma: public");
        Excel::create('DMT-'.date("d-m-Y"), function ($excel) use ($reports) {
            $excel->sheet('Sheet1', function ($sheet) use ($reports) {
                $arr = array();
                foreach ($reports as $value) 
                {
                    $data = array(
                        $value->created_at, 
                        $value->id, 
                        $value->user->name .'('. $value->user->prefix . ' - ' . $value->user_id .')',
                        @$value->user->member->company, 
                        @$value->api->api_name, 
                        @$value->provider->provider_name, 
                        @$value->beneficiary->bank_name, 
                        (is_numeric($value->credit_by)) ?  ($value->creditBy->name) : ($value->credit_by),
                        @$value->description, 
                        @$value->number,
                        @$value->customer_number,
                        @$value->txnid,
                        @$value->amount,
                        @$value->credit_charge,
                        @$value->debit_charge,
                        @$value->gst,
                        @$value->tds,
                        @$value->total_balance,
                        @$value->status->status
                        );
                    array_push($arr, $data);
                }
                //set the titles
                $sheet->fromArray($arr, null, 'A1', true, false)->prependRow(array('Date & Time','Id','User Name','Outlet','Product','Provider','Bank','Credit To/ Debit From','Description','Acc No/ Mob No','Remitter Number','Ref Id','Amount','Credit Amount','Debit Amount','GST','TDS','Remaining Balance','Status')
                );
            });
        })->export('csv');
        
    }
     public function moneyTxnHistoryImpswallet(Request $request)
    {
        //dd($request->all());
        if (Auth::user()->role_id == 5)
        {
                $reports = Report::where('user_id', Auth::id())
                        ->orderBy('id', 'DESC')->where('api_id',3)
						->whereIn('status_id',[1,2,3,21,18])
                        ->paginate(40);
             if($request->export =="SEARCH" || $request->export =="EXPORT")
               {
                    $start_date = ($request->fromdate) ? $request->fromdate ." 00:00:00" : date("Y-m-d") . " 00:00:00";
                    $end_date = ($request->todate) ? $request->todate ." 23:59:59" : date("Y-m-d H:i:s");
                    $reports->whereBetween('created_at', [$start_date,$end_date]);
                }
                //$reports->select('id','user_id','created_at','api_id','amount');
            $number = $request->number;
            if($request->SEARCH_TYPE=="ID")
                    $reports->where('id', $number);
                elseif($request->SEARCH_TYPE=="TXN_ID")
                    $reports->where('txnid', $number);
                elseif($request->SEARCH_TYPE=="ACC")
                    $reports->where('number', $number);
                elseif($request->SEARCH_TYPE=="MOB_NO")
                    $reports->orWhere('customer_number', $number);
            if($request->export=="EXPORT")
            {
                $reports = $reports->all();
                $this->exportExcelSheetDMT1($reports,$request);   
            }
           // $reports = $reports->simplePaginate(40);                
                return view('reports.money_txn_history',compact('reports'));
        }
    }
     public function moneyTxnHistoryPremiumwallet(Request $request)
    {
        //dd($request->all());
        if (Auth::user()->role_id == 5)
        {
                $reports = Report::where('user_id', Auth::id())
                        ->orderBy('id', 'DESC')->where('api_id',5)
						->whereIn('status_id',[1,2,3,21,18])
                        ->paginate(40);
             if($request->export =="SEARCH" || $request->export =="EXPORT")
               {
                    $start_date = ($request->fromdate) ? $request->fromdate ." 00:00:00" : date("Y-m-d") . " 00:00:00";
                    $end_date = ($request->todate) ? $request->todate ." 23:59:59" : date("Y-m-d H:i:s");
                    $reports->whereBetween('created_at', [$start_date,$end_date]);
                }
                //$reports->select('id','user_id','created_at','api_id','amount');
            $number = $request->number;
            if($request->SEARCH_TYPE=="ID")
                    $reports->where('id', $number);
                elseif($request->SEARCH_TYPE=="TXN_ID")
                    $reports->where('txnid', $number);
                elseif($request->SEARCH_TYPE=="ACC")
                    $reports->where('number', $number);
                elseif($request->SEARCH_TYPE=="MOB_NO")
                    $reports->orWhere('customer_number', $number);
            if($request->export=="EXPORT")
            {
                $reports = $reports->all();
                $this->exportExcelSheetDMT1($reports,$request);   
            }
           // $reports = $reports->simplePaginate(40);                
                return view('reports.money_txn_history',compact('reports'));
        }
    }
	public function DMTOneTransactionReport(Request $request)
	{
		if(in_array(Auth::user()->role_id,array(2,3,1,4)))
		{
			$dmtTxnReport = $this->getDmtTxnHistory($request,4);
			return view('admin.network.dmt-txn-report',compact('dmtTxnReport','apiId'));
			
		}
	}
	public function DMTTwoTransactionReport(Request $request)
	{
		if(in_array(Auth::user()->role_id,array(2,3,1,4)))
		{
			$dmtTxnReport = $this->getDmtTxnHistory($request,5);
			return view('admin.network.dmt-txn-report',compact('dmtTxnReport','apiId'));
			 
		}
	}
	
	private function getDmtTxnHistory($request,$apiId)
	{
		if(Auth::user()->role_id == 4)
				$memberList= $this->getDistMember(Auth::id());
		else if(Auth::user()->role_id == 3)
				$memberList= $this->getMdMember(Auth::id());
		$reportQuery = Report::where('api_id',$apiId);
		if($request->SEARCH_TYPE == "ID")
			$reportQuery->where('id',$request->number); 
        elseif($request->SEARCH_TYPE == "ACC")
            $reportQuery->where('number',$request->number);
        elseif($request->SEARCH_TYPE == "TXN_ID")
            $reportQuery->where('txnid',$request->number);
		elseif($request->SEARCH_TYPE == "MOB")
            $reportQuery->where('customer_number',$request->number);
		if(Auth::user()->role_id != 1 )
			$reportQuery->whereIn('user_id',$memberList);
		$reportQuery->orderBy('id','desc');
		return $reportQuery->simplePaginate(30);
	}
	public function viewNetwork()
	{
			
	}
	public function offlineRecord(Request $request)
	{
		if(Auth::user()->role_id ==1 || Auth::user()->role_id ==19)
		{
			$start_date = ($request->fromdate) ? $request->fromdate ." 00:00:00" : date("Y-m-d") . " 00:00:00";
			$end_date = ($request->todate) ? $request->todate ." 23:59:59" : date("Y-m-d H:i:s");
			$start_date = date("Y-m-d H:i:s", strtotime($start_date));
			$end_date = date("Y-m-d H:i:s", strtotime($end_date));
			
			$offlineRecords= Report::where('status_id',24)->orderBy('id','ASC');
			if($request->fromdate !=''){
            $offlineRecords->whereBetween('created_at', [$start_date,$end_date]);
			}
			if($request->number!='')
				$offlineRecords->where('number','=',$request->number);
			
			if($request->export=="EXPORT")
				return Excel::download(new OfflineRecordExport($start_date,$end_date),'OfflineRecord.xlsx');
			
			if($request->recordCount=='All')
				$offlineRecords=$offlineRecords->get();
			elseif($request->recordCount!='')
				$offlineRecords=$offlineRecords->simplePaginate($request->recordCount);
			else
				$offlineRecords=$offlineRecords->simplePaginate(20);
			$otpVerifications=ActionOtpVerification::orderBy('id','ASC')->get();
			return view('admin.offline.offline-records',compact('offlineRecords','otpVerifications'));
		}
		return view('errors.permission-denied');
	}
	private function creditAepsPendingTxnAmount($report)
	{
		$creditAmount = $report->amount+$report->credit_charge+$report->debit_charge;
		DB::beginTransaction();
		try{
			$report->status_id=32;
			$report->txn_type=$report->txn_type."_FORWARD";
			$report->save();
			Balance::where('user_id',$report->user_id)->increment('user_balance', $creditAmount);
			$availableBalance = Balance::where('user_id',$report->user_id)->select('user_balance')->first()->user_balance;
			Report::create([
				'number'=>$report->number,
				'provider_id'=>$report->provider_id,
				'amount'=>$report->amount,
				'bulk_amount'=>$report->bulk_amount,
				'api_id'=>$report->api_id,
				'profit'=>$report->profit,
				'type'=>$report->type,
				'txn_type'=>"AMOUNT_CREDITED_TO_WALLET",
				'description'=>$report->description,
				'status_id'=>1,
				'txnid'=>$report->id,
				'pay_id'=>$report->pay_id,
				'user_id'=>$report->user_id,
				'created_by'=>Auth::id(),
				'ip_address' => \Request::ip(),
				'customer_number'=>$report->customer_number,
				'opening_balance'=>$availableBalance-$creditAmount,
				'total_balance'=>$availableBalance,
				'biller_name'=>$report->biller_name,
				'gst'=>$report->gst,
				'tds'=>$report->tds,
				'recharge_type'=>$report->recharge_type,
				'credit_charge'=>$report->credit_charge,
				'debit_charge'=>$report->debit_charge,
				'channel'=>$report->channel,
			
			]);
			DB::commit();
			return ['status'=>1,'message'=>"Transaction Success"];
		}
		catch(Exception $e)
		{
			DB::rollback();
			throw $e;
			return ['status'=>3,'message'=>"Transaction Still Pending"];
		}
	}
    
    public function offlineUpdatedRecord(Request $request)
	{
		$title="Offline Updated Record";
		if(Auth::user()->role_id ==1 || Auth::user()->role_id==19)
		{
			$start_date = ($request->fromdate) ? $request->fromdate ." 00:00:00" : date("Y-m-d") . " 00:00:00";
			$end_date = ($request->todate) ? $request->todate ." 23:59:59" : date("Y-m-d H:i:s");
			 /*  $start_date = date("Y-m-d H:i:s", strtotime($start_date));
			$end_date = date("Y-m-d H:i:s", strtotime($end_date)); */
			$dateFormat = $this->getDbFromatDate($start_date, $end_date);
			
			$offlineUpdatedRecord= Report::where([['status_id','!=',24],['is_offline','=',1]])->whereBetween('created_at', [$dateFormat['start_date'],$dateFormat['end_date']])->orderBy('id','desc');
			
			if($request->number!='')
				$offlineUpdatedRecord->where('number','=',$request->number);
			if($request->export=="EXPORT")
            {
				return Excel::download(new OfflineExport($dateFormat['start_date'],$dateFormat['end_date']), 'offline.xlsx');
            }
			if($request->recordCount=='All')
				$offlineUpdatedRecord=$offlineUpdatedRecord->get();
			elseif($request->recordCount!='')
				$offlineUpdatedRecord=$offlineUpdatedRecord->simplePaginate($request->recordCount);
			else
				$offlineUpdatedRecord=$offlineUpdatedRecord->simplePaginate(20);
			 //$offlineUpdatedRecord= $offlineUpdatedRecord->Paginate(20);
			return view('admin.offline.offline-updated-records',compact('offlineUpdatedRecord','title'));
		}
		return view('errors.permission-denied');
	}
	public function accountSearch(Request $request)
	{
		$accountDetails=array();
		if($request->accountNumber !='')
		{
			$beneQuery = Beneficiary::orderBy('id','desc');
			$beneQuery->where('account_number',$request->accountNumber); 
			$accountDetails = $beneQuery->simplePaginate(30);
			return view('agent.account-searcher',compact('accountDetails'));
		}
		return view('agent.account-searcher',compact('accountDetails'));
	}
	
	public function directTransferReport(Request $request)
    {
		if(Auth::user()->role_id ==5)
		{
			$reportQuery = Report::orderBy('id','DESC')->whereIn('status_id',[6,7]);
			$reportQuery->where('user_id', Auth::id());    	
			$start_date=($request->fromdate) ? $request->fromdate ." 00:00:00" : date("Y-m-d") . " 00:00:00";
			$end_date = ($request->todate) ? $request->todate ." 23:59:59" : date("Y-m-d H:i:s");
			$start_date = date("Y-m-d H:i:s",strtotime($start_date));
			$end_date = date("Y-m-d H:i:s",strtotime($end_date));
			if($request->searchOf !='')
				$reportQuery->where('status_id',$request->searchOf);
			$reportQuery->whereBetween('created_at',[$start_date,$end_date]);
			if($request->export=="EXPORT")
				return Excel::download(new DTReportExport($start_date,$end_date),'DT-Report.xlsx');
			if($request->noOfRecord !='')
			{
				$reports = $reportQuery->Paginate($request->noOfRecord*20);
			}
			else
				$reports = $reportQuery->Paginate(30);
			return view('reports.direct-transfer-report',compact('reports'));
		}
    }
	
	public function aepsReport(Request $request)
    {
        if (in_array(Auth::user()->role_id,array(1,19))) 
        {
			$start_date = ($request->fromdate) ? $request->fromdate ." 00:00:00" : date("Y-m-d") . " 00:00:00";
            $end_date = ($request->todate) ? $request->todate ." 23:59:59" : date("Y-m-d H:i:s");
			$dateFormat = $this->getDbFromatDate($start_date, $end_date);
			
			if($request->export=="EXPORT")
			{
				if($request->SEARCHTYPE!='')
					$searchCriteria['description'] = $request->SEARCHTYPE;
				$searchCriteria['api_id'] = 10;
				//print_r($searchCriteria);exit;
				return Excel::download(new AepsSettlementExport($searchCriteria,$dateFormat['start_date'],$dateFormat['end_date']), 'AEPS_SETTELMENT_Report.xlsx');
			}
			$reportQuery = Report::orderBy('id', 'DESC')->where('api_id',10);
			/* if($request->searchOf !=''){
				$reportQuery->where('status_id',$request->searchOf);
				$searchCriteria['status_id'] = $request->searchOf;
			}
			if($request->SEARCHTYPE == "BALANCE_ENQUIRY")
			{
				$reportQuery->where('description', 'LIKE','%'.$request->SEARCHTYPE."%");
				$searchCriteria['description'] = $request->SEARCHTYPE;
			}
			elseif($request->SEARCHTYPE == "AEPS_COMMISSION")
			{
                $reportQuery->where('description', 'LIKE','%'.$request->SEARCHTYPE."%");
				$searchCriteria['description'] = $request->SEARCHTYPE;
			}
            elseif($request->SEARCHTYPE == "BALANCE_WITHDRAW")
			{
                $reportQuery->where('description', 'LIKE','%'.$request->SEARCHTYPE."%");
				$searchCriteria['description'] = $request->SEARCHTYPE;
			}
            elseif($request->SEARCHTYPE == "AEPS_SETTELMENT_APPROVED")
			{
                $reportQuery->where('description', 'LIKE','%'.$request->SEARCHTYPE."%");
				$searchCriteria['description'] = $request->SEARCHTYPE;
			}
			elseif($request->SEARCHTYPE == "AEPS_SETTELMENT_PENDING")
			{
                $reportQuery->where('description', 'LIKE','%'.$request->SEARCHTYPE."%");
				$searchCriteria['description'] = $request->SEARCHTYPE;
			} */
				if($request->SEARCHTYPE!='')
					$reportQuery->where('description', 'LIKE','%'.$request->SEARCHTYPE."%");
				/* else
				$reportQuery->where('description', 'AEPS_SETTELMENT_PENDING'); */
			$reportQuery->whereBetween('created_at', [$dateFormat['start_date'],$dateFormat['end_date']]);
			/* if($request->export =="SEARCH" || $request->export =="EXPORT")
			{
				$reportQuery->whereBetween('created_at', [$dateFormat['start_date'],$dateFormat['end_date']]);
			} */
            $reportQuery->select('id','user_id','created_at','api_id','amount','profit','beneficiary_id','recharge_type','status_id','description','total_balance','total_balance2','provider_id','number','customer_number','bank_ref','ackno','txnid','refund','credit_by','channel','rechargeprovider_id','credit_charge','tds','gst','debit_charge','aeps_sattelment_id');
            
           /*  if($request->searchType == "NUMBER")
                $reportQuery->where('number',$request->number);
            elseif($request->searchType == "TXNID")
                $reportQuery->where('txnid',$request->number);
            elseif($request->searchType == "MOB")
                $reportQuery->where('customer_number',$request->number);
            if($request->userId != "")
               $reportQuery->where('user_id',$request->userId); 
            if (in_array(Auth::user()->role_id,array(1))) 
            {
                if($request->number !='')
                $reportQuery->orWhere('id',$request->number);
            } 
            else
            { 
                $reportQuery->where('user_id', Auth::id());
                $reportQuery->where('status_id','!=',14);
            } */
			$page_title = "Aeps Report";
            $reports = $reportQuery->simplePaginate(40);
            return view('admin.aeps_report', compact('reports','page_title'));
        }
        else{
            return "Do Your Own Work";
        }
    }
	private function aepsSettelmentApprove($report,$bankRef,$type)
	{
		
		if($report->description=="AEPS_SETTELMENT_PENDING" && $report->status_id==3)
		{
			if($type==1)
			{
				$report->status_id =6;
				$description = "AEPS_SETTELMENT_APPROVED";
				$report->description=$description;
				$report->bank_ref=$bankRef;
				$userMobileNum = Auth::user()->mobile;
				$userId = Auth::id();
				//$statusId=7;
				
			}
			else
			{
				$userMobileNum =$report->user->mobile;
				$userId = $report->user_id;
				$report->status_id =29;
				$description = "AEPS_SETTELMENT_REJECT";
				$report->bank_ref=$bankRef;
				$report->description=$description;
				//$statusId=7;
			}
			 
				$reportData=array('number'=>$userMobileNum,
						'user_id'=>$userId,	
						'amount'=>$report->amount,
						'provider_id'=>41,
						'api_id'=>$report->api_id,
						'description'=>$description,
						'bank_ref'=>$bankRef,
						'remark'=>$report->remark,
						'channel'=>$report->channel,
						'txnid'=>$report->id,
						'status_id' => 7,	
						'txn_type' => 'AEPS_SETTELMENT',						
						'debit_charge' => $report->credit_charge,					
						'debit_charge' => $report->credit_charge,					
						'credit_charge' => $report->debit_charge,					
						'aeps_sattelment_id' => $report->aeps_sattelment_id,			
						'pay_id' => time(),	
						'credit_by' => $report->user_id
						);	
				$creditAmount=$report->amount + $report->debit_charge - $report->credit_charge;
				$report->save();
				$data= Balance::where('user_id',$userId)->first();
				if($type==1)
				{
					Balance::where('user_id',$userId)->increment('aeps_balance',$creditAmount);
					$closingBal= $data->user_balance + $creditAmount;
					$reportData['admin_com_bal'] = $data->admin_com_bal;
				}
				else
				{
					Balance::where('user_id',$userId)->increment('user_balance',$creditAmount);
					$closingBal= $data->user_balance + $creditAmount;
				}
				$reportData['opening_balance'] = $data->user_balance;
				$reportData['total_balance'] = $closingBal;
				
				Report::create($reportData);
					return ['status' => 1,
						'message' => "Aeps Settelment updated",
					];
		}	
		return ['status' => 2,
					'message' => "Aeps Settelment failed",
				];	
			
		
	}
	public function rToR(Request $request)
	{ 
		if(Auth::user()->role_id==1 || Auth::user()->role_id==19)
		{
			$rtoRReportQuery = Report::selectRaw('id,user_id,status_id,provider_id,amount,created_at,txnid,total_balance,opening_balance,credit_by,description,ref_id,remark')->where('channel',3)->orderBy('id','desc');
			if($request->user !='')
				$rtoRReportQuery->where('user_id',$request->user);
			
			// echo $rtoRReportQuery->toSql();
		  // die;	
			$rtoRReports = $rtoRReportQuery->simplePaginate(30);
			$page_title="R to R Report";
			$users=User::where('role_id',5)->pluck('name','id')->toArray();
			return view('admin.report.r-to-r-report',compact('rtoRReports','page_title','users'));
		}
	}
}
