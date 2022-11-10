<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Masterbank;
use App\Company;
use App\Balance;
use App\Report;
use App\Apiresponse;
use App\Beneficiary;
use App\PremiumWalletScheme;
use App\TransactionReportException;
use App\Api;
use Auth;
use DB;
use Exception;
use Validator;
use Response;
use App\Traits\CustomTraits;
class CheckStatusController extends Controller
{
    //
	use CustomTraits;
	
	public function checkTransactionCurrentStatus(Request $request)
	{	
		try{
			$report=Report::findOrFail($request->id);
		}
		catch(Exception $e)
		{
			return response()->json(['status'=>1,'msg'=>"No Record Found"]);
		}
		if($report->status_id == 21)
			return response()->json(['status'=>2,"msg"=>"Amount Refunded."]);
		if($report->status_id == 2)
			return response()->json(['status'=>2,"msg"=>"Recharge Failed"]);
		if($report->api_id == 5)
			return $this->checkDMTTwoStatus($report);
		elseif($report->api_id == 4 || $report->api_id == 1){
			$cyber = new \App\Library\CyberDMT;
			return $cyber->checkStatus($report->id);//Cyber Plat
		}
		elseif($report->api_id == 8 || $report->api_id == 14)
		{
			try
			{
				if($report->api_id == 8)
					$content = $this->redPayCheckStatus($report);
				elseif($report->api_id == 14)
					$content = $this->mRoboticsCheckStatus($report);
			}
			catch(Exception $e)
			{
				//throw $e;
				return array('status'=>3,'txnid'=>'','ref_id'=>'','msg'=>"Api Response Fail, Please try again");
			}	
			if($content['status']== 1)
			{
				$report->status_id = 1;
				$report->txnid = $content['txnid'];
				$report->ref_id = $content['ref_id'];
				$report->save();
				return response()->json(['status'=>1,"msg"=>"Success"]);
			}
			elseif($content['status']==2)
			{
				DB::beginTransaction();
				try
				{
					if(in_array(Report::find($report->id)->status_id,array(1,3,18)))
					{
						$lastInsertId = $this->doFailTranaction($report,"RECHARGE");
						$this->reverseCommission($report,"RECHARGE",$lastInsertId);
						DB::commit();
						return response()->json(['status'=>2,"msg"=>"Recharge Failed, Amount credited"]);
					}
					else
					{
						return response()->json(['status'=>2,"msg"=>"Transaction has been update, Please try again"]);
					}
				}
				catch(Exception $e)
				{
					DB::rollback();
					return array('status'=>3,'txnid'=>'','ref_id'=>'','msg'=>"Recharge Pending");
				}
			}
			else{
				return array('status'=>3,'txnid'=>'','ref_id'=>'','msg'=>"Recharge Pending");
			}
		}
		elseif(in_array($report->api_id,array(16,2)))
		{
			return $this->insTransactionCheckStatus($report);
		}
		else
			return response()->json(['status'=>1,'msg'=>"Check Status not allowed"]);		
	}
	
	public function sendRefundTxnOtp(Request $request)
	{
		try{
			$report = Report::findOrFail($request->recordId);
		}
		catch(Exception $e)
		{
			return response()->json(['status'=>0,'message'=>"No Record Found"]);
		}
		$content=array('api_token'=>config('constants.TRAMO_API_KEY'),'userId'=> config('constants.TRAMO_USER_ID'),'txnId'=>$report->txnid);
		$url = config('constants.TRAMO_DMT_URL') ."/send-refund-txn-otp?".$content;//die;
		return $this->getCurlGetMethod($url);
	}
	
	public function transactionRefundRequest(Request $request)
	{
		$report = Report::find($request->recordId);
		$content=array('api_token'=>config('constants.TRAMO_API_KEY'),'userId'=> config('constants.TRAMO_USER_ID'),'txnId'=>$report->txnid,'otp'=>$request->otp);
		$url = config('constants.TRAMO_DMT_URL') ."/txn-refund-request";
		$response = $this->getCurlPostMethod($url,$content);
		DB::beginTransaction();
		try
		{
			CustomTraits::doFailTranaction($report,"OTP_REFUND");
			CustomTraits::creditRefundAmount($report,"OTP_REFUND");
			$report->status_id = 21;
			$report->refund = 0;
			$report->refundrequest()->update(['refund_status'=>0]);
			$report->save();
			DB::commit();
			return $response;
		} 
		catch(Exception $e)
		{
			DB::rollback();
			$err_msg = "Something went worng. Please contact with Admin";
			return response()->json(['status' => 0, 'message' => $err_msg]);
		}
			
		
	}
	
}
