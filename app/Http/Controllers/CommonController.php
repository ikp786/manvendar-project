<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Report;
use Auth;
class CommonController extends Controller
{
	public function getTransactionByField(Request $request)
	{
		$reports = Report::selectRaw('id,amount,number,customer_number,status_id,api_id,created_at')->where(['user_id'=>Auth::id(),$request->searchType =>$request->searchNumber])->whereIn('status_id',[1,2,3,4,21,18])->orderBy('id','desc')->take(10)->get();
		if(count($reports))
		{
			return response()->json(['status'=>1,'message'=>"Record Found",'details'=>$reports->map(function($report)
			{
				return [
					'reportId'=>$report->id,
					'amount'=>$report->amount,
					'customerNumber'=>$report->customer_number,
					'status'=>$report->status->status,
					'apiName'=>$report->api->api_name,
					'txnTime'=>date("Y-m-d",strtotime($report->created_at))
				];
			})]);
		}
		return response()->json(['status'=>0,'message'=>"No Record Available",'details'=>'']);
		
	}
}
