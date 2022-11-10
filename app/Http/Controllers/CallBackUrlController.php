<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests;
use App\Report;
use App\Apiresponse;
use App\Traits\CustomTraits;
use DB;
use Log;
use Exception;
class CallBackUrlController extends Controller
{
	use CustomTraits;
	
	public function AmazonResponseCallBack(Request $request)
	{ 
	      $bbps_no ='';
	      $k_number = $request['mobile_no'];
	      $txn = $request['txn'];
	      if(@$txn!=''){
    	      $bbps_data =explode("\\",$txn);
    	      $bbps_no = str_replace(')','',$bbps_data)[1];
    	      
	      }
	      
	      $call_back_param=array(
						'k_number'=>$request['mobile_no'], 
						'amount'=>$request['amount'],
						'txn'=>$request['txn'],
						'status'=>$request['status'],
						'company_id'=>$request['company_id'],
						'client_id'=>$request['client_id'],
						'my_bbps_no'=>$bbps_no,
						);
		  
		  $apiResp = Apiresponse::create(['api_id'=>'26','user_id'=>'0','api_type'=>"AmazonCallBack",'report_id'=>'0','message'=>json_encode($call_back_param)]);
        /*
        
        {"k_number":null,"amount":"1025.00","txn":"BBPS Reference Number\nBD01CAA3EMZ8","status":"success","company_id":"1235","client_id":"100"}
        */
        
       if($request['status']=='success'){
               $ReportData = array("status_id" => '1',"txnid" => $bbps_no);
               Report::where('number', $k_number)->where('status_id', '34')->update($ReportData);
       }else if($request['status']=='pending'){
               $ReportData = array("status_id" => '3',"txnid" => $bbps_no);
               Report::where('number', $k_number)->where('status_id', '34')->update($ReportData);
       }   
	    
	}
	
	public function PayTMResponseCallBack(Request $request)
	{
	    // echo " sdfsdfsf PayTMResponseCallBack"; die;
	    
	    // dd($request['response']);
	   // Log::info($request['response']);
	    try{
	        $apiResp = Apiresponse::create(['api_id'=>25,'user_id'=>'16','api_type'=>"PaytmCallBack",'report_id'=>'00','request_message'=>$request['response']]);
	         
	        $username = urlencode("r604");
            $msg_token = urlencode("yhibBi");
            $sender_id = urlencode("ATZSVD"); // optional (compulsory in transactional sms)
            $message_content = "Dear Customer, Your Txn is successful to Acc No. 00011111 with Amount Rs 2 at PaytmCallBack Thanks";
            $message = rawurlencode($message_content);
            $mobile = urlencode('9619418206');
             
            $content=array('username'=> $username, 'msg_token'=> $msg_token,'sender_id'=>$sender_id,'message'=>$message_content,'mobile'=>$mobile);
        	$url = "http://manage.hivemsg.com/api/send_transactional_sms.php";
        	 
        	$api_response =  $this->getCurlPostMethod($url,$content);
    	
	    }catch(Exception $e){
			  
    	    $username = urlencode("r604");
            $msg_token = urlencode("yhibBi");
            $sender_id = urlencode("ATZSVD"); // optional (compulsory in transactional sms)
            $message_content = "Dear Customer, Your Txn is successful to Acc No. 00011111 with Amount Rs 2 at PaytmCallBackCatchCall Thanks";
            $message = rawurlencode($message_content);
            $mobile = urlencode('9619418206');
             
            $content=array('username'=> $username, 'msg_token'=> $msg_token,'sender_id'=>$sender_id,'message'=>$message_content,'mobile'=>$mobile);
        	$url = "http://manage.hivemsg.com/api/send_transactional_sms.php";
        	 
        	$api_response =  $this->getCurlPostMethod($url,$content);
        	Log::info($request['response']);
		}
	}
	
	public function mroboticsCallBackUrl(Request $request)
	{
		$apiResp = Apiresponse::create(['message'=>json_encode($request->all()),'api_id'=>14,'api_type'=>"MROBOTICS_CALL_BACK"]);
		$STATUS = $request->status;
		$txnid = $request->tnx_id;
		$opid = $request->id;
		$refid = $request->order_id;//our report id
		try
		{
			$mRoboticsRechargeReport = Report::findOrFail($refid);
		}
		catch(Exception $e)
		{
			$resp = ['status'=>0,'message'=>"Failed"];
			$apiResp->request_message =json_encode($resp);
			$apiResp->save();
			return response()->json($resp);
		}
		if($mRoboticsRechargeReport->api_id != 14 || $mRoboticsRechargeReport->txnid !=$txnid)
		{
			$resp = ['status'=>0,'message'=>"Failed"];
			$apiResp->request_message =json_encode($resp);
			$apiResp->save();
			return response()->json($resp);
		}
		elseif(!in_array($mRoboticsRechargeReport->status_id,array(1,3)))
		{
			$resp = ['status'=>0,"message"=>"Failed",'message'=>"Transaction is not in success or pending stage"];
			$apiResp->request_message =json_encode($resp);
			$apiResp->save();
			unset($resp['message']);
			return response()->json($resp);
		}
		$apiResp->user_id =$mRoboticsRechargeReport->user_id;
		$apiResp->report_id =$mRoboticsRechargeReport->id;
		$apiResp->save();
		
		if($STATUS == "success")
		{
			$mRoboticsRechargeReport->status_id = 1;
			$mRoboticsRechargeReport->txnid = $txnid;
			$mRoboticsRechargeReport->save();
			$resp = ['status'=>1,"message"=>"success"];
			$apiResp->request_message =json_encode($resp);
			$apiResp->save();
			return response()->json($resp);
		}
		elseif($STATUS=="failed")
		{
			if(in_array($mRoboticsRechargeReport->status_id,array(1,3))){
				DB::beginTransaction();
				try
				{
					$lastInsertId = $this->doFailTranaction($mRoboticsRechargeReport,"RECHARGE");
					$this->reverseCommission($mRoboticsRechargeReport,"RECHARGE",$lastInsertId);
					$resp = ['status'=>1,"message"=>"success"];
					$apiResp->request_message =json_encode($resp);
					$apiResp->save();
					DB::commit();
					return response()->json($resp);
				}
				catch(Exception $e)
				{
					DB::rollback();
					$resp = ['status'=>0,'message'=>"Failed"];
					$apiResp->request_message =json_encode($resp);
					$apiResp->save();
					return response()->json($resp);
				}
			}	
		}
	}
	
	public function redpayCallBackUrl(Request $request)
	{
		$apiResp = Apiresponse::create(['message'=>json_encode($request->all()),'api_id'=>8,'api_type'=>"REDPAY_CALL_BACK"]);
		$STATUS = $request->status;
		$txnid = $request->txnid;
		$opid = $request->opid;
		$refid = $request->refid;
		$remainbalance=$request->remainbalance;
		try
		{
			//$redpayRechargeReport = Report::where(['api_id'=>8,'txnid'=>$txnid])->firstOrFail();
			$redpayRechargeReport = Report::findOrFail($refid);
		}
		catch(Exception $e)
		{
			$resp = ['status'=>0,'message'=>"Failed"];
			$apiResp->request_message =json_encode($resp);
			$apiResp->save();
			return response()->json($resp);
		}
		if($redpayRechargeReport->api_id != 8 || $redpayRechargeReport->txnid !=$txnid)
		{
			$resp = ['status'=>0,'message'=>"Failed"];
			$apiResp->request_message =json_encode($resp);
			$apiResp->save();
			return response()->json($resp);
		}
		elseif(!in_array($redpayRechargeReport->status_id,array(1,3)))
		{
			$resp = ['status'=>0,"message"=>"Failed",'message'=>"Transaction is not in stuccess or pending stage"];
			$apiResp->request_message =json_encode($resp);
			$apiResp->save();
			unset($resp['message']);
			return response()->json($resp);
		}
		$apiResp->user_id =$redpayRechargeReport->user_id;
		$apiResp->report_id =$redpayRechargeReport->id;
		$apiResp->save();
		
		if($STATUS == "SUCCESS")
		{
			$redpayRechargeReport->status_id = 1;
			$redpayRechargeReport->txnid = $opid;
			$redpayRechargeReport->save();
			$resp = ['status'=>1,"message"=>"success"];
			$apiResp->request_message =json_encode($resp);
			$apiResp->save();
			return response()->json($resp);
		}
		elseif($STATUS=="FAILED")
		{
			if(in_array($redpayRechargeReport->status_id,array(1,3))){
				DB::beginTransaction();
				try
				{
					$lastInsertId = $this->doFailTranaction($redpayRechargeReport,"RECHARGE");
					$this->reverseCommission($redpayRechargeReport,"RECHARGE",$lastInsertId);
					$resp = ['status'=>1,"message"=>"success"];
					$apiResp->request_message =json_encode($resp);
					$apiResp->save();
					DB::commit();
					return response()->json($resp);
				}
				catch(Exception $e)
				{
					DB::rollback();
					$resp = ['status'=>0,'message'=>"Failed"];
					$apiResp->request_message =json_encode($resp);
					$apiResp->save();
					return response()->json($resp);
				}
			}	
		}
	}
	public function aToZRechargeCallBackUrl(Request $request)
	{
		$apiResp = Apiresponse::create(['message'=>json_encode($request->all()),'api_id'=>13,'api_type'=>"A2Z_CALL_BACK"]);
		$STATUS = $request->transtype;
		$txnid = $request->txid;
		$opid = $request->prvdr;
		$refid = $request->rid;
		$amount=$request->amt;
		$remainbalance=$request->bal;
		try
		{
			$a2zRechargeReport = Report::findOrFail($refid);
			if($a2zRechargeReport->api_id != 13)
			{
				$resp = ['status'=>0,'message'=>"Failed"];
				$apiResp->request_message =json_encode($resp);
				$apiResp->save();
				return response()->json($resp);
			}
			elseif(!in_array($a2zRechargeReport->status_id,array(1,3)))
			{
				$resp = ['status'=>0,'message'=>"Failed"];
				$apiResp->request_message =json_encode($resp);
				$apiResp->save();
				return response()->json($resp);
			}
			$apiResp->user_id =$a2zRechargeReport->user_id;
			$apiResp->report_id =$a2zRechargeReport->id;
			$apiResp->save();
		}
		catch(Exception $e)
		{
			$resp = ['status'=>0,'message'=>"Failed"];
			$apiResp->request_message =json_encode($resp);
			$apiResp->save();
			return response()->json($resp);
		}
		if($STATUS == "SUCCESS")
		{
			$resp = ['status'=>1,'message'=>"SUCCESS"];
			$a2zRechargeReport->status_id = 1;
			$a2zRechargeReport->txnid = $txnid;
			$a2zRechargeReport->save();
			$apiResp->request_message =json_encode($resp);
			$apiResp->save();
			return response()->json($resp);
		}
		elseif($STATUS=="FAILED")
		{
			DB::beginTransaction();
			try
			{
				$lastInsertId = $this->doFailTranaction($a2zRechargeReport,"RECHARGE");
				$this->reverseCommission($a2zRechargeReport,"RECHARGE",$lastInsertId);
				$resp = ['status'=>1,"message"=>"success"];
				$apiResp->request_message =json_encode($resp);
				$apiResp->save();
				DB::commit();
				return response()->json($resp);
			}
			catch(Exception $e)
			{
				DB::rollback();
				$resp = ['status'=>0,'message'=>"Failed"];
				$apiResp->request_message =json_encode($resp);
				$apiResp->save();
				return response()->json($resp);
			}
		}
	}
	public function cyberCallback(Request $request)
	{
		$apiResp = Apiresponse::create(['message'=>json_encode($request->all()),'api_id'=>4,'api_type'=>"Cyber_CALL_BACK"]);
		$recordId = $request->DealerTransID;
		$report = Report::find($recordId);
		$successMessge=['status'=>1,'message'=>"Success"];
		$failMessge=['status'=>0,'message'=>"Failed"];
		if($report)
		{
			$apiResp->report_id =$report->id;
			$apiResp->save();
			if(in_array($report->status_id,array(1,3)))
			{
				if($report->api_id == 4 || $report->api_id == 1)
				{
					if($report->api_id == 4)
					{
						if($request->ErrorDesc == "Success")
						{
							$report->status_id = 1;
							$report->txnid = $request->CIPLTransID;
							$report->save();
							$apiResp->request_message=json_encode(['status'=>1,'message'=>'success']);
							$apiResp->save();
							return response()->json($successMessge);
						}
						elseif($request->ErrorDesc == "Cancelled")
						{
							$msg = @$report->api->api_name." txn of Rs." .$report->amount ." and Account No ".$report->number.", Name ".@$report->beneficiary->name ." Bank Name ".@$report->beneficiary->bank_name ." is failed.";
							DB::beginTransaction();
							try
							{
								if(in_array(Report::find($report->id)->status_id,array(1,2,18)))
								{
									$lastInsertId = $this->doFailTranaction($report,"TRANSACTION");
									$this->reverseCommission($report,"DMT",$lastInsertId);
									
									$message = ['status'=>1,'message'=>'Transation is Failed through CyberCallback'];
									$apiResp->request_message=json_encode($message);
									$apiResp->save();
									DB::commit();
									$message = urlencode($msg);
									try{
										$this->sendSMS($report->customer_number, $message,1);
									}
									catch(Exception $e)
									{
									}
									return response()->json($successMessge);
								}
								else
								{
									return response()->json($successMessge);
								}
							}
							catch(Exception $e)
							{
								DB::rollback();
								$message = ['status'=>0,'message'=>'Error'.$e->getMessage()];
								$apiResp->request_message=json_encode($message);
								$apiResp->save();
								return response()->json($failMessge);
							}
						}
					}
					elseif($report->api_id == 1)
					{	
						if($request->ErrorDesc == "Success")
						{
							$report->status_id = 1;
							$report->txnid = $request->CIPLTransID;
							$report->save();
							$apiResp->request_message=json_encode(['status'=>1,'message'=>'success']);
							$apiResp->save();
							return response()->json($successMessge);
						}
						elseif($request->ErrorDesc == "Cancelled")
						{
							DB::beginTransaction();
							try
							{
								$report->txnid = $request->CIPLTransID;
								$report->save();
								$lastInsertId = $this->doFailTranaction($report,"RECHARGE");
								$this->reverseCommission($report,"RECHARGE",$lastInsertId);
								DB::commit();
								$resp = ['status'=>1,"message"=>"success"];
								$apiResp->request_message =json_encode($resp);
								$apiResp->save();
								DB::commit();
							}
							catch(Exception $e)
							{
								DB::rollback();
								$resp = ['status'=>0,'message'=>"FailedE".$e->getMessage()];
								$apiResp->request_message =json_encode($resp);
								$apiResp->save();
							}
							return response()->json($resp);
						}
					}
				}
				else
				{
					$apiResp->request_message=json_encode(['status'=>0,'message'=>'Txn was initiated by other']);
					$apiResp->save();
					return response()->json(['status'=>0,'message'=>'Txn was initiated by other']);
				}
			}
			else
			{
				$apiResp->request_message=json_encode(['status'=>0,'message'=>'Transaction not in Pending or success stage']);
				$apiResp->save();
				return response()->json($failMessge);
			}
		}
		else{
			$apiResp->request_message=json_encode(['status'=>0,'message'=>'No Transaction Found']);
			$apiResp->save();
			return response()->json($failMessge);
		}
	}
	public function aTwoZWalletCallBack(Request $request)
	{
		$apiResp = Apiresponse::create(['message'=>json_encode($request->all()),'api_id'=>5,'api_type'=>"TRAMO_CALL_BACK"]);
		
		$report = Report::where(['txnid'=>$request->txnId,'api_id'=>5])->whereIn('status_id',[1,3,18])->first();
		if($report !='')
		{
			$apiResp->report_id = $report->id;
			$apiResp->save();
			if($report->number != $request->accountNumber)
			{
				$message = ['status'=>2,'message'=>'Account Number is not Matched'];
				$apiResp->request_message=json_encode($message);
				$apiResp->save();
				return response()->json($message);
			}
			else if($report->amount != $request->amount)
			{
				$message = ['status'=>2,'message'=>'Amount Missmatched'];
				$apiResp->request_message=json_encode($message);
				$apiResp->save();
				return response()->json($message);
			}
				$msg = @$report->api->api_name." txn of Rs." .$report->amount ." and Account No ".$report->number.", Name ".@$report->beneficiary->name ." Bank Name ".@$report->beneficiary->bank_name ." is failed";
				DB::beginTransaction();
				try
				{
					$lastInsertId = $this->doFailTranaction($report,"TRANSACTION");
					$this->reverseCommission($report,"DMT",$lastInsertId);
					DB::commit();					
					try{
						$message = urlencode($msg);
						$this->sendSMS($report->customer_number, $message,1);
					}
					catch(Exception $e)
					{
					}
					$message = ['status'=>1,'message'=>'Transation is Failed through tramo callback'];
					$apiResp->request_message=json_encode($message);
					$apiResp->save();
					DB::commit();
					return response()->json($message);
				}
				catch(Exception $e)
				{
					DB::rollback();
					$message = ['status'=>2,'message'=>'Error'.$e->getMessage()];
					$apiResp->request_message=json_encode($message);
					$apiResp->save();
					return response()->json($message);
				}
		}
		else{
			$message = ['status'=>2,'message'=>'No Transaction Found'];
			$apiResp->request_message=json_encode($message);
			$apiResp->save();
			return response()->json($message);
		}
	}
}
