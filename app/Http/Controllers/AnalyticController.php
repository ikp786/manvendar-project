<?php

namespace App\Http\Controllers;

use App\User;
use Excel;
use Illuminate\Http\Request;
use App\Report;
use App\Api;

use App\Http\Requests;


use Response;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class AnalyticController extends Controller
{
	
	public function businessReport(Request $request)
	{
		if(Auth::user()->role_id==5)
		{
		//echo "hell";die;
			set_time_limit(200000);
				/*  Today Reports Query   */
				$start_date = ($request->fromdate) ? $request->fromdate. " 00:00:00" : date('Y-m-d').' 00:00:00';
				$end_date = ($request->todate) ? $request->todate. " 23:59:59" : date('Y-m-d').' 23:59:59';
				
				$today_reports_query = Report::orderBy('created_at', 'DESC');
					$today_reports_query->whereIn('api_id',[1,2,3,4,5]);
					$today_reports_query->whereIn('status_id', [1,2,3,9]);
					$today_reports_query->whereBetween('created_at',[$start_date,$end_date]);
					$today_reports_query->selectRaw('status_id,api_id ,sum(amount) as today_sales');
					$today_reports_query->groupBy('api_id');
					$today_reports_query->groupBy('status_id');
					$today_reports_query->where('user_id',Auth::id());
				$today_reports=$today_reports_query->get();
				
			$all_reports=array();
			foreach($today_reports as $report)
			{
				//$all_reports[$report->api_id][$report->status]= $report->today_sales;
				$all_reports[$report->api->api_name][$report->status->status]= $report->today_sales;	
			}
			$content=array();
			$index =0;
			foreach($all_reports as$key=> $report)
			{
				$content[$key]['PRODUCT']=$key;
				foreach($report as $skey=>$data)
				{
					$content[strtoupper($key)][strtoupper($skey)]=$data;
				}
			}
			$content=json_encode($content);
			$content = json_decode($content);
			//print_r($content);die;
			//print_r($all_reports);die;
			return view('reports.business_report',compact('content'));
		}
		return view('errors.premission-denied');
			
    }
	public function distBusinessReport(Request $request)
	{
		if(Auth::user()->role_id== 4){
		$products = Api::getActiveProdut();
		$products['']="All Product";
		$start_date = ($request->fromdate) ? $request->fromdate. " 00:00:00" : date('Y-m-d').' 00:00:00';
		$end_date = ($request->todate) ? $request->todate. " 23:59:59" : date('Y-m-d').' 23:59:59';
		$members = User::where('parent_id', Auth::id())->pluck('name','id')->toArray();
		$today_reports_query = Report::orderBy('created_at', 'DESC');
		if($request->all())
		{
			//echo "hello";die;
			if($request->product == -1 && $request->agent !=''){
				//echo "hello";die;
				$today_reports_query->whereIn('status_id', [1,2,3]);
				$today_reports_query->whereIn('api_id', [1,4,5]);
			}
			else if($request->product !='')
				$today_reports_query->where('api_id',$request->product);
			if($request->agent !='')
			{
				
				$today_reports_query->whereIn('status_id',[1,2,3]);
				$today_reports_query->whereIn('api_id', [1,4,5]);
				$today_reports_query->where('user_id',$request->agent);
			}else{
				$today_reports_query->whereIn('status_id',[22]);
				$today_reports_query->whereIn('api_id', [1,4,5]);
				$today_reports_query->where('user_id',Auth::id());
			}
		}
		else{
			
			$today_reports_query->whereIn('status_id',[22]);
			$today_reports_query->whereIn('api_id', [1,4,5]);
			$today_reports_query->where('user_id',Auth::id());
		}
		$today_reports_query->whereBetween('created_at',[$start_date,$end_date]);
		$today_reports_query->selectRaw('api_id ,amount, sum(amount) as Total_Sale,sum(credit_charge) as commission, count(id) as txn_count, status_id,user_id');
		$today_reports_query->groupBy('api_id');
		$today_reports_query->groupBy('status_id');
		$today_reports=$today_reports_query->get();
		//print_r($today_reports);die;
		$all_reports=array();
		$today_sales=0;
		
		foreach($today_reports as $report)
		{
			$all_reports[$report->api->api_name][$report->status->status]['Total Sale']= $report->Total_Sale;
			$all_reports[$report->api->api_name][$report->status->status]['Commission']= $report->commission;
			$all_reports[$report->api->api_name][$report->status->status]['Total Txn']= $report->txn_count;
			$today_sales += $report->Total_Sale;
		}
		
		return view('admin.business-report',compact('all_reports','members','products'));
		}
		return view('errors.access-denied');
		
	}
	public function viewTxnCommission(Request $request)
	{
		if(Auth::user()->role_id==5)
		{
			$start_date = ($request->fromdate) ? $request->fromdate. " 00:00:00" : date('Y-m-d').' 00:00:00';
			$end_date = ($request->todate) ? $request->todate. " 23:59:59" : date('Y-m-d').' 23:59:59';
			if($request->export=="EXPORT")
			{
				$reports = Report::selectRaw('id,user_id,txnid,created_at,provider_id,amount,pay_id,tds,commission, beneficiary_id,description,credit_charge,abs(debit_charge) as debit_charge,api_id,status_id,profit')->where('user_id',Auth::id())->whereIn('status_id',[1,3,9])->whereBetween('created_at',[$start_date,$end_date])->get();
				$this->exportAccountStatement($reports);
				
				
			}
			$reports = Report::selectRaw('count(id) as txnCount,sum(amount) as txnAmount, sum(credit_charge) as txnCommission,sum(abs(debit_charge)) as debitCharge,api_id,status_id')->where('user_id',Auth::id())->whereIn('status_id',[1,3,9])->whereBetween('created_at',[$start_date,$end_date])->groupBy('api_id')->groupBy('status_id')->get();
			//print_r($reports);die;
			$all_reports =array();
			foreach($reports as $report)
			{
				$all_reports[$report->api->api_name][$report->status->status]['Total Sale']= $report->txnAmount;
				$all_reports[$report->api->api_name][$report->status->status]['Commission']= number_format($report->txnCommission,2);
				$all_reports[$report->api->api_name][$report->status->status]['Charge']= number_format($report->debitCharge,2);
				$all_reports[$report->api->api_name][$report->status->status]['Txn Count']= $report->txnCount;
				//$today_sales += $report->Total_Sale;
			}
			return view('reports.view-commission',compact('all_reports'));
		}
		return view('errors.premission-denied');
	}
	private function exportAccountStatement($reports)
	{
		ini_set("memory_limit", "10056M");
		header("Content-Type: application/vnd.ms-excel");
		header("Cache-control: private");
		header("Pragma: public");
		Excel::create('Account Statement-'.date("d-m-Y"), function ($excel) use ($reports) {
			$excel->sheet('Sheet1', function ($sheet) use ($reports) {
			   
				$arr = array();
				foreach ($reports as $value) 
				{
					
					$data = array(
						$value->created_at, 
						$value->id, 
						$value->user->name .'('. @$value->user_id .')',
						@$value->api->api_name, 
						@$value->provider->provider_name, 
						@(in_array($value->api_id,array(3,4,5))) ? @$value->beneficiary->bank_name :  $value->description, 
						@$value->number,
						@$value->txnid,
						@$value->amount,
						//@$value->profit,
						@$value->credit_charge,
						@$value->debit_charge,
						@$value->total_balance,
						@$value->status->status
						);
					array_push($arr, $data);
				}
				//set the titles
				$sheet->fromArray($arr, null, 'A1', true, false)->prependRow(array('Date & Time','Id','User Name','Product','Provider','Bank Name/Description','Acc No/ Mob No','Txn Id','Amount','credit Amt','Debit Amt','Remaining Bal','Status')
				);
			});
		})->export('csv');
	}
	
	

}