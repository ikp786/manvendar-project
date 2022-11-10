<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Report;
use App\Balance;
use App\Remark;
use Carbon\Carbon;
use Auth;
use DB;
use Exception;
class OfflineRecordController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
		$offlineReports=Report::selectRaw('id,user_id,profit,number,status_id,provider_id,offline_services_id,amount,api_id,created_at,txnid,total_balance')->where('created_at', '>=', Carbon::now()->subWeek())->whereIn('api_id',[19,20,21,23])->where(['status_id'=>3])->orderBy('id','DESC')->get();
		$remarks= Remark::where([['id', '!=', 0],['deleted','=',0]])->lists('remark','id')->toArray();       
	   return view('admin.report.offline',compact('offlineReports','remarks'));
    }

  public function view(Request $request)
  {
	  $record_id = $request->record_id;
	  $offlineReports=Report::selectRaw('id,user_id,profit,number,status_id,provider_id,offline_services_id,amount,api_id,created_at,txnid,total_balance')->where('id',$record_id)->get();
	  //return $offlineReport;
	  return $offlineReports->map(function($report)
	  {
		  return [	
					"id" => $report->id,
					"name"=>$report->user->name,
					"number"=>$report->number,
					"company_name"=>@$report->offlineservices->name,
					"status_id"=>$report->status_id,
					"amount"=>$report->amount,
					"txnid"=>$report->txnid,
					
					];
	  });
  }
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        if(Auth::user()->role_id == 1 && $request->ajax())
		{
			$report = Report::find($request->record_id);
			if($report->status_id == 2)
				return response()->json(['status'=>0, 'message'=>'Transaction Allready Rejected']);
			elseif($report->status_id == 1)
				return response()->json(['status'=>0, 'message'=>'Transaction Allready Success']);
			else if($report->status_id == 3 && $request->status_id == 1)
			{
				$report->txnid = $request->txnid;
				$report->remark = $request->remark;
				$report->status_id = $request->status_id;
				if($report->save())
					return response()->json(['status'=>1, 'message'=>'Transaction updated successfully']);
				else
					return response()->json(['status'=>0, 'message'=>'Transaction not updated']);
			}
			else if($request->status_id == 2)
			{
				if($this->refundTransactionFailed($report,$request->txnid))
					return response()->json(['status'=>2, 'message'=>'Transaction has been failed']);
				return response()->json(['status'=>2, 'message'=>'Something went wrong please try again']);
			}
			else
				return response()->json(['status'=>0, 'message'=>'Sorry! Transaction could not updated']);
		}
		return response()->json(['status'=>0, 'message'=>'You do not have permission']);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
	private function refundTransactionFailed($report, $txnId)
	{
	
		$report->status_id = 21;
        $report->txnid = $txnId;
		DB::beginTransaction();
		try
		{
			$report->save();
			$creditAmt = $report->profit + $report->amount;
			Balance::where('user_id', $report->user_id)->increment('user_balance', $creditAmt);
			$insert_id = Report::create([
						'number' => $report->number,
						'amount' => $report->amount,
						'profit'=>$report->profit,
						'api_id' => $report->api_id,
						'status_id' => 4,
						'description' => 'ManualRefund',
						'txnid' => $report->id,
						'user_id' => $report->user_id,
						'recharge_type' => $report->recharge_type,
						'channel' => $report->channel,
						'total_balance' => Balance::where('user_id', $report->user_id)->first()->user_balance,
			]);
			DB::commit();
			return true;
		} 
		catch(Exception $e)
		{
			DB::rollback();
			throw $e;
			return false;
		}
	}
}
