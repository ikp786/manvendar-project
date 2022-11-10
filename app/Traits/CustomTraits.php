<?php 

//namespace App\Http\Controllers\Auth;

namespace App\Traits;
use App\User;
use App\Report;
use App\Apiresponse;
use App\Balance;
use App\ActiveService;
use App\Masterbank;
use App\TransactionReportException;
use App\RemitterRegistration; 
use DB;
use Redirect;
 use Auth;
use Request;
/*****************/
/*
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\Crypt; 
use Auth;
use Illuminate\Http\Request;
*/
/****************/

use Validator;
use Response;
use Exception;
trait CustomTraits
{
	public static function getAllAgentOfMd($company_id)
	{
		return User::where(['company_id'=>$company_id,'role_id'=>5])->pluck('id','id')->toArray();
	}
	
	public function isActiveService($apiId) 
	{
	    /*
	    $q_data = ActiveService::where(['api_id'=>25,'status_id'=>0])->first();
         
        if($q_data->api_id=='25' && $q_data->status_id=='0')
        {
             
    		Request::session()->flush();
    		Request::session()->regenerate();
    		Request::session()->flash('message', 'Portal is down for sometime. Please wait...');
    		return redirect('logout');
    		
        }else{ */
           return ActiveService::where(['api_id'=>$apiId,'status_id'=>0])->first(); 
        // } 
	}
	
	public static function sendSMSbkp($mobile,$message,$company_id=null,$senderType=null)
	{
		$senderType="a2zsuvidhaa";
		
		if($senderType)
		{
			$url = "http://103.238.223.66:8080/bulksms/bulksms?username=".\Config::get('constants.SMS_USER_NAME')."&password=".\Config::get('constants.SMS_PASSWORD')."&type=0&dlr=1&destination=". $mobile ."&source=".$senderType."&message=".$message;
			$curl = curl_init();
			curl_setopt_array($curl, array(
			CURLOPT_URL =>$url,
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_ENCODING => "",
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_TIMEOUT => 1,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST => "GET",
			
			));
			$response = curl_exec($curl);
			$err = curl_error($curl);
			curl_close($curl);
		}
	}
	public static function sendSMS($mobile,$message,$company_id=null,$senderType=null)
	{
		$senderType="A2ZSUVIDHAA";
		if($senderType)
		{
			$url = "http://103.238.223.66/api/send_transactional_sms.php?username=".\Config::get('constants.USER_NAME')."&msg_token=".\Config::get('constants.msg_token')."&sender_id=".\Config::get('constants.SENDER_ID')."&message=".$message."&mobile=".$mobile;
			//$url = \Config::get('constants.SMS_BASE_URL_SECOND')."?username=".\Config::get('constants.USER_NAME')."&msg_token=".\Config::get('constants.msg_token')."&sender_id=AMONEY&message=".$message."&mobile=".$mobile."&lang=HN&&adv=1";
			
			$curl = curl_init();
			curl_setopt_array($curl, array(
			CURLOPT_URL =>$url,
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_ENCODING => "",
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_TIMEOUT => 1,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST => "GET",
			));
			$response = curl_exec($curl);
			
			//	echo "nsdvbfnsdfvmsafmsa";
			//  dd($response);
			 
			$err = curl_error($curl);
			curl_close($curl);	
		}
	}
	
	public static function sendRechargeByAmazon($insert_id, $number, $amount, $user_id)
	{ 
	    $inserted_report_id = $insert_id;
	    $k_number = $number;
	    
        $url = "http://203.100.74.163:11000/billpayment?mobile_no=".$k_number."&amount=".$amount."&company_id=1235&client_id=100&key=123456";
         
            $curl = curl_init();
			curl_setopt_array($curl, array(
			CURLOPT_URL =>$url,
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_ENCODING => "",
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_TIMEOUT => 2,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST => "GET",
			));
			$response = curl_exec($curl);
			
			$err = curl_error($curl);
			
		//	echo "<br/> Api response ss= <pre>"; print_r($response);
		//	echo "<br/> Api response err= <pre>"; print_r($err);
			 
			$result =json_decode($response);
			
		//	echo "<br/> api call ==".json_encode($result);
			
			$ReportData = array("status_id" => '34');
            Report::where('id', $inserted_report_id)->update($ReportData); 
                
			if($response==true){
			  //  echo "<br/>  if call success";
			    
			    $apiResp = Apiresponse::create(['api_id'=>'26','user_id'=>'0','api_type'=>"AmazonBillPay",'report_id'=>$inserted_report_id,'message'=>json_encode($result)]);
			     
                return array('status' => 1, 'txnid' => '', 'message' => 'Transaction Successfull', 'response' => '', 'ref_id' => '');
			}else{
			    //echo "<br/> else call error exist =";
			    // {"payid":3180,"operator_ref":"","status":"failure","message":"Transaction Failed, Please check Detail or Try After some time, Thanks"} 
			     
			     
			     $apiResp = Apiresponse::create(['api_id'=>'26','user_id'=>'0','api_type'=>"AmazonBillPay",'report_id'=>'','message'=>json_encode($result)]);
			     
			     return array('status' => 2, 'txnid' => '', 'message' => $result->message, 'response' => '', 'ref_id' => '');
			}
			 
			curl_close($curl);	
        
	}
	
	public static function sendByA2ZPlusSms($mobile_no,$message_content)
	{ 
	    // = urlencode("r604");
        //$msg_token = urlencode("yhibBi");
        //$sender_id = urlencode("ATZSVD");  
        //$message_content = "Dear Customer, Your Txn is successful to Acc No." .$bank_account ." with Amount Rs " .$amount." at ".$bank_name." Thanks";
       
        $mobile = urlencode($mobile_no);
        $message = $message_content;
         
        // $content=array('username'=> $username, 'msg_token'=> $msg_token,'sender_id'=>$sender_id,'message'=>$message_content,'mobile'=>$mobile);
        
     
        $url = "http://manage.hivemsg.com/api/send_transactional_sms.php?username=".\Config::get('constants.USER_NAME')."&msg_token=".\Config::get('constants.msg_token')."&sender_id=".\Config::get('constants.SENDER_ID')."&message=".$message."&mobile=".$mobile;
        	
        //$apiResponse = $this->getCurlPostMethod($url,$content);	
        //dd($api_response);
        
            $curl = curl_init();
			curl_setopt_array($curl, array(
			CURLOPT_URL =>$url,
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_ENCODING => "",
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_TIMEOUT => 1,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST => "GET",
			));
			$response = curl_exec($curl);
	 
			// $result =json_decode($response);
			
			$err = curl_error($curl);
			curl_close($curl);	
        
	}
	

	
	public function getCurlPostMethod($url,$content)
	{
		$curl = curl_init();
		curl_setopt_array($curl, array(
		CURLOPT_URL =>$url,
		CURLOPT_RETURNTRANSFER => true,
		CURLOPT_ENCODING => "",
		CURLOPT_MAXREDIRS => 10,
		CURLOPT_TIMEOUT => 100,
		CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		CURLOPT_CUSTOMREQUEST => "POST",
		CURLOPT_POSTFIELDS=>$content,
		CURLOPT_HTTPHEADER => array(
					 "Accept: application/json",
    //"Content-Type: application/json",
					//"Content-Type: multipart/form-data; boundary=MultipartBoundry",
					),
		));
		$response = curl_exec($curl);
		$err = curl_error($curl);
		curl_close($curl);

		return $response;
	}
	public function getCurlPostIMethod($url,$content)
	{
		$curl = curl_init();
		curl_setopt_array($curl, array(
		CURLOPT_URL =>$url,
		CURLOPT_RETURNTRANSFER => true,
		CURLOPT_ENCODING => "",
		CURLOPT_MAXREDIRS => 10,
		CURLOPT_TIMEOUT => 100,
		CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		CURLOPT_CUSTOMREQUEST => "POST",
		CURLOPT_POSTFIELDS=>$content,
		CURLOPT_HTTPHEADER => array(
					 "Accept: application/json",
    "Content-Type: application/json",
					//"Content-Type: multipart/form-data; boundary=MultipartBoundry",
					),
		));
		$response = curl_exec($curl);
		$err = curl_error($curl);
		curl_close($curl);

		return $response;
	}
	
	public static function getCurlGetMethod($url)
	{
		
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_HEADER, false);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLINFO_HEADER_OUT, 1);
		curl_setopt($ch, CURLOPT_BINARYTRANSFER, 1);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array(
			"Cache-Control: no-cache",
			"Accept: application/json",
		));
		curl_setopt($ch, CURLOPT_TIMEOUT,125 );
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_URL, $url);
		$response = curl_exec($ch); 
		$err = curl_error($ch);
		curl_close($ch);
		
		return $response;
	}
	 	public function ApicheckPaytmStatus($report,$apiResp=null)
	{
		if(in_array($report->status_id,array(1,3,18,34)))
		{
			$paytm_return = new \App\Library\paytm\lib\PaytmResponce; 
            $apiResponse = $paytm_return->getPaytmResponse($report->txnid); 
            $apiResp->message=$apiResponse;
    			$apiResp->save();
		    $content=array('txnId'=>$report->txnid);
		    
		   
    			if($apiResp!='')
    			{
    				$apiResp->request_message=json_encode($content);
    				$apiResp->save();
    			}
    			else
    			{
    				$apiResp = Apiresponse::create(['message'=>'','api_id'=>$report->api_id,'user_id'=>$report->user_id,'api_type'=>"TRANSACTION_CHECK",'report_id'=>$report->id,'request_message'=>json_encode($content)]);
    			}
			
				try{
					$result =json_decode($apiResponse);
					 
				}
				catch(Exception $e)
				{
					return response()->json(['status'=>3,'msg'=>"Pending"]);
				}
			
				if($result->status=="SUCCESS")
				{   
					$report->status_id=1;
					$report->paytm_txn_id=$result->result->paytmOrderId;
					$report->bank_ref=$result->result->rrn;
					$report->save();
					$value = array('status'=>1,'msg'=>"Success",'bankRefNo'=>$result->result->rrn);
                                    $json = json_encode($value); 
                                    return $json;
				//	return response()->json(['status'=>1,'msg'=>"Success",'bankRefNo'=>$result->result->rrn]);
				    
				}
				elseif($result->status=="FAILURE")
				{
					
					DB::beginTransaction();
				    
					try
					{
					    $report->paytm_txn_id=$result->result->paytmOrderId;
					    
					    // if($result->statusCode=="DE_010" || $result->statusCode=="DE_039" || $result->statusCode=="DE_602"){
					    if($result->statusCode=='DE_010' || $result->statusCode=='DE_039' || $result->statusCode=='DE_040' || $result->statusCode=='DE_602' || $result->statusCode=='DE_634' || $result->statusCode=='DE_641'){
					        
					        $report->status_id=3;  
    					    $r_message = ' Pending ';
    					    
    					    $report->save();
        					DB::commit();
					            
					        //return array('status'=>3,'txnid'=>'','ref_id'=>'','msg'=>"Transaction Pending");
					    }
					    else{
					       if(in_array(Report::find($report->id)->status_id,array(1,3,18,34)))
        						{
        							$lastInsertId = $this->doFailTranaction($report,"TRANSACTION");
        							$this->reverseCommission($report,"DMT",$lastInsertId);
        							
        							//$report->status_id=2;  
    					            $r_message = ' Failure ';
    					    
        							DB::commit();
        							
        							$this->updateRemitterAmount($report->id);
        							
        							// return response()->json(['status'=>2,"msg"=>"Transaction Failed, Amount credited"]);
        						}
        					//	return response()->json(['status'=>2,"msg"=>"Transaction has been update, Please try again"]); 
					    }
						
					}
					catch(Exception $e)
					{
						DB::rollback();
						$value = array('status'=>3,'txnid'=>'','ref_id'=>'','msg'=>"Transaction Pending");
                                    $json = json_encode($value); 
                                    return $json;
					//	return array('status'=>3,'txnid'=>'','ref_id'=>'','msg'=>"Transaction Pending");
					}
				     
					
					if($result->statusCode=="DE_024" || $result->statusCode=="DE_025" || $result->statusCode=="DE_034" || $result->statusCode=="DE_041" || $result->statusCode=="DE_606" || 
				    $result->statusCode=="DE_607" ||$result->statusCode=="DE_612" ||$result->statusCode=="DE_613" ||$result->statusCode=="DE_614" ||$result->statusCode=="DE_615" 
				    ||$result->statusCode=="DE_616" ||
				    $result->statusCode=="DE_617" ||$result->statusCode=="DE_618" ||$result->statusCode=="DE_619" ||$result->statusCode=="DE_620" 
				    ||$result->statusCode=="DE_621" ||$result->statusCode=="DE_622" || $result->statusCode=="DE_623" || $result->statusCode=="DE_625" || $result->statusCode=="DE_626" 
				    || $result->statusCode=="DE_627" || $result->statusCode=="DE_636" || $result->statusCode=="DE_640" || $result->statusCode=="DE_652" || $result->statusCode=="DE_653" 
				    || $result->statusCode=="DE_656"  || $result->statusCode=="DE_657")
				    {
				        $report->fail_msg = $result->statusMessage;
					    $report->save();
					
					$value = array('status'=>2,'msg'=>$result->statusMessage);
                                    $json = json_encode($value); 
                                    return $json;
                                    
				       // return response()->json(['status'=>2,'msg'=>$result->statusMessage]);
				    }
				    else if($result->statusCode=="DE_704" || $result->statusCode=="DE_705"){
				            
				            ActiveService::where('id',20)->update(['status_id'=>0]);
				            
				            $report->fail_msg = "Internal Server Error";
					        $report->save();
					        
					        $value = array('status'=>2,'msg'=>"Internal Server Error");
                                    $json = json_encode($value); 
                                    return $json;
				          //  return response()->json(['status'=>2,'msg'=>"Internal Server Error"]);
				    }
				    $value = array('status'=>3,'msg'=>"Transaction $r_message");
                                    $json = json_encode($value); 
                                    return $json;
				//	return response()->json(['status'=>2,'msg'=>"Transaction $r_message"]);
				    
				}
				else
				{
				    $value = array('status'=>3,'msg'=>"Pending");
                                    $json = json_encode($value); 
                                    return $json;
					//return response()->json(['status'=>3,'msg'=>"Pending"]);
				}
		}
		$value = array("status"=>"2","msg"=>"Check Status Not Allowed");
                                    $json = json_encode($value); 
                                    return $json;
		//return response()->json(["status"=>"2","msg"=>"Check Status Not Allowed"]);		
	}
	public function checkPayTMStatus($report,$apiResp=null)
	{
	    
		if(in_array($report->status_id,array(1,3,18,34)))
		{   
		 
			$paytm_return = new \App\Library\paytm\lib\PaytmResponce; 
            $apiResponse = $paytm_return->getPaytmResponse($report->txnid); 
            
		    $content=array('txnId'=>$report->txnid);
		    
		   /*
		        $content=array('api_token'=>config('constants.TRAMO_API_KEY'),'userId'=> config('constants.TRAMO_USER_ID'),'txnId'=>$report->txnid);
     
    			if($apiResp!='')
    			{
    				$apiResp->request_message=json_encode($content);
    				$apiResp->save();
    			}
    			else
    			{
    				$apiResp = Apiresponse::create(['message'=>'','api_id'=>$report->api_id,'user_id'=>$report->user_id,'api_type'=>"TRANSACTION_CHECK",'report_id'=>$report->id,'request_message'=>json_encode($content)]);
    			}
    			$url = config('constants.TRAMO_DMT_URL') ."/transaction-check-status";//die;
    			$apiResponse = $this->getCurlPostMethod($url,$content);	
    			$apiResp->message=$apiResponse;
    			$apiResp->save();
		   
		   */
		    $apiResp = Apiresponse::create(['message'=>'','api_id'=>$report->api_id,'user_id'=>$report->user_id,'api_type'=>"TRANSACTION_CHECK",'report_id'=>$report->id,'request_message'=>json_encode($content)]);
    			
			$apiResp->message=$apiResponse;
			$apiResp->save();
			
				try{
					$result =json_decode($apiResponse);
					 
				}
				catch(Exception $e)
				{
					return response()->json(['status'=>3,'msg'=>"Pending"]);
				}
			

			//	echo "<br/><br/> CustomTraits checkPayTMStatus die call ";  die;
				if($result->status=="SUCCESS")
				{   
					$report->status_id=1;
					$report->paytm_txn_id=$result->result->paytmOrderId;
					$report->bank_ref=$result->result->rrn; // OILAB Comment
					$report->save();
					return response()->json(['status'=>1,'msg'=>"Success ".$result->result->rrn]);//'bankRefNo'=>$result->bankRefNo]); OILAB
				 
				}
				elseif($result->status==42)
				{
					$report->status_id=20;
					$report->refund=1;
					$report->save();
					return response()->json(['status'=>20,'msg'=>"Success"]);
				}
				elseif($result->status=="FAILURE")
				{
					
					DB::beginTransaction();
				    
					try
					{
					    $report->paytm_txn_id=$result->result->paytmOrderId;
					    
					    // if($result->statusCode=="DE_010" || $result->statusCode=="DE_039" || $result->statusCode=="DE_602"){
					    if($result->statusCode=='DE_010' || $result->statusCode=='DE_039' || $result->statusCode=='DE_040' || $result->statusCode=='DE_602' || $result->statusCode=='DE_634' || $result->statusCode=='DE_641'){
					        
					        $report->status_id=3;  
    					    $r_message = ' Pending ';
    					    
    					    $report->save();
        					DB::commit();
					            
					        //return array('status'=>3,'txnid'=>'','ref_id'=>'','msg'=>"Transaction Pending");
					    }
					    else{
					       if(in_array(Report::find($report->id)->status_id,array(1,3,18,34)))
        						{
        							$lastInsertId = $this->doFailTranaction($report,"TRANSACTION");
        							$this->reverseCommission($report,"DMT",$lastInsertId);
        							
        							//$report->status_id=2;  
    					            $r_message = ' Failure ';
    					    
        							DB::commit();
        							
        							$this->updateRemitterAmount($report->id);
        							
        							// return response()->json(['status'=>2,"msg"=>"Transaction Failed, Amount credited"]);
        						}
        					//	return response()->json(['status'=>2,"msg"=>"Transaction has been update, Please try again"]); 
					    }
						
					}
					catch(Exception $e)
					{
						DB::rollback();
						return array('status'=>3,'txnid'=>'','ref_id'=>'','msg'=>"Transaction Pending");
					}
				     
					
					if($result->statusCode=="DE_024" || $result->statusCode=="DE_025" || $result->statusCode=="DE_034" || $result->statusCode=="DE_041" || $result->statusCode=="DE_606" || 
				    $result->statusCode=="DE_607" ||$result->statusCode=="DE_612" ||$result->statusCode=="DE_613" ||$result->statusCode=="DE_614" ||$result->statusCode=="DE_615" 
				    ||$result->statusCode=="DE_616" ||
				    $result->statusCode=="DE_617" ||$result->statusCode=="DE_618" ||$result->statusCode=="DE_619" ||$result->statusCode=="DE_620" 
				    ||$result->statusCode=="DE_621" ||$result->statusCode=="DE_622" || $result->statusCode=="DE_623" || $result->statusCode=="DE_625" || $result->statusCode=="DE_626" 
				    || $result->statusCode=="DE_627" || $result->statusCode=="DE_636" || $result->statusCode=="DE_640" || $result->statusCode=="DE_652" || $result->statusCode=="DE_653" 
				    || $result->statusCode=="DE_656"  || $result->statusCode=="DE_657")
				    {
				        $report->fail_msg = $result->statusMessage;
					    $report->save();
					
				        return response()->json(['status'=>2,'msg'=>$result->statusMessage]);
				    }
				    else if($result->statusCode=="DE_704" || $result->statusCode=="DE_705"){
				            
				            ActiveService::where('id',20)->update(['status_id'=>0]);
				            
				            $report->fail_msg = "Internal Server Error";
					        $report->save();
				            return response()->json(['status'=>2,'msg'=>"Internal Server Error"]);
				    }
				    
					return response()->json(['status'=>2,'msg'=>"Transaction $r_message"]);
				    
				}
				elseif($result->status=="PENDING")//($result->status==3)
				{
				    $report->status_id=3;
					$report->save();
                	return response()->json(['status'=>3,'msg'=>"Transaction Pending"]); 
					
				}
				elseif($result->status==59)
				{
					return response()->json(['status'=>18,'msg'=>$result->message]);
				}else
				{
					return response()->json(['status'=>3,'msg'=>$result->message]);
				}
			
				return $apiResponse;
		}
		return response()->json(['status'=>2,'msg'=>"Check Status Not Allowed"]);		
	}
	
	
	public function updateRemitterAmount($report_id=null)
	{
	    $creditDetails = Report::where(['id'=>$report_id])->first(['amount','customer_number']);
	    
	    
	    $checkUpdate = RemitterRegistration::where('mobile', $creditDetails->customer_number)->increment('rem_bal', $creditDetails->amount);
	    if($checkUpdate){
	        return true;
	    }
	   // return $creditDetails->amount;   
	}
	
	public function checkDMTTwoStatus($report,$apiResp=null)
	{
		if(in_array($report->status_id,array(1,3,18)))
		{
			$content=array('api_token'=>config('constants.TRAMO_API_KEY'),'userId'=> config('constants.TRAMO_USER_ID'),'txnId'=>$report->txnid);
			//print_r($content);die;
			if($apiResp!='')
			{
				$apiResp->request_message=json_encode($content);
				$apiResp->save();
			}
			else
			{
				$apiResp = Apiresponse::create(['message'=>'','api_id'=>$report->api_id,'user_id'=>$report->user_id,'api_type'=>"TRANSACTION_CHECK",'report_id'=>$report->id,'request_message'=>json_encode($content)]);
			}
			$url = config('constants.TRAMO_DMT_URL') ."/transaction-check-status";//die;
			$apiResponse = $this->getCurlPostMethod($url,$content);	
			$apiResp->message=$apiResponse;
			$apiResp->save();
				try{
					$result =json_decode($apiResponse);
				}
				catch(Exception $e)
				{
					return response()->json(['status'=>3,'msg'=>"Pending"]);
				}
				if($result->status==43)
				{
					$report->status_id=1;
					$report->bank_ref=$result->bankRefNo;
					$report->save();
					return response()->json(['status'=>43,'msg'=>"Success",'bankRefNo'=>$result->bankRefNo]);
				}
				elseif($result->status==42)
				{
					$report->status_id=20;
					$report->refund=1;
					$report->save();
					return response()->json(['status'=>20,'msg'=>"Success"]);
				}
				elseif($result->status==57)
				{
					
					DB::beginTransaction();
					try
					{
						if(in_array(Report::find($report->id)->status_id,array(1,3,18)))
						{
							$lastInsertId = $this->doFailTranaction($report,"TRANSACTION");
							$this->reverseCommission($report,"DMT",$lastInsertId);
							DB::commit();
							return response()->json(['status'=>2,"msg"=>"Transaction Failed, Amount credited"]);
						}
						return response()->json(['status'=>2,"msg"=>"Transaction has been update, Please try again"]);
					}
					catch(Exception $e)
					{
						DB::rollback();
						return array('status'=>3,'txnid'=>'','ref_id'=>'','msg'=>"Transaction Pending");
					}
				}
				elseif($result->status==59)
				{
					return response()->json(['status'=>18,'msg'=>$result->message]);
				}else
				{
					return response()->json(['status'=>3,'msg'=>$result->message]);
				}
				return $apiResponse;
		}
		return response()->json(['status'=>2,'msg'=>"Check Status Not Allowed"]);		
	}
	
	public function redPayCheckStatus($report)
	{
		if(in_array($report->status_id,array(1,3)))
		{
			//return response()->json(['status'=>2,'msg'=>"Check Status Not Allowed yet"]);
			$url =config('constants.REDPAY_RECH_URL')."/Status_check?token=".config('constants.REDPAY_APITOKEN')."&refid=".$report->id."&RESPTYPE=JSON";
            $curl = curl_init();
            curl_setopt($curl, CURLOPT_URL, $url);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
            $response = curl_exec($curl);
            curl_close($curl);
			Apiresponse::create(['message'=>$response,'user_id'=>$report->user_id,'api_id'=>8,'api_type'=>'RECH_CHECK_STATUS','report_id'=>$report->id,'request_message'=>$url]);
            if($response)
            {
                try
                {
                    $resp_arr = json_decode($response);
                    @$res_message = $resp_arr->STATUSMSG;
					$txnid = $resp_arr->txnid;
                    $REFNO = $resp_arr->operatorid;
                    if($resp_arr->status == "SUCCESS")
						return array('status'=>1,'txnid'=>$txnid,'ref_id'=>$REFNO,'msg'=>$res_message);
                    elseif($resp_arr->status == "FAILED")
						return array('status'=>2,'txnid'=>$txnid,'ref_id'=>$REFNO,'msg'=>$res_message);
                    elseif($resp_arr->status == "PENDING")
						return array('status'=>3,'txnid'=>$txnid,'ref_id'=>$REFNO,'msg'=>$res_message);
                    else
						return array('status'=>3,'txnid'=>$txnid,'ref_id'=>$REFNO,'msg'=>$res_message);
				}
				catch(\Exception $e)
				{
					
					return array('status'=>3,'txnid'=>'','ref_id'=>'','msg'=>$res_message);
				}
			}
			else
			{
			return array('status'=>3,'txnid'=>'','ref_id'=>'');
			}
		}
		return response()->json(['status'=>"NOT_ALLOWED",'msg'=>"Check Status Not Allowed"]);
	}
	
	public function mRoboticsCheckStatus($report)
	{
		if(in_array($report->status_id,array(1,3)))
		{
			/*$url =config('constants.MROBOTICS_RECHARGE_URL')."order_id_status?api_token=".config('constants.MROBOTICS_API_TOKEN')."&order_id=".$report->id;*/
			$url = config('constants.MROBOTICS_RECHARGE_URL')."order_id_status";
			$content="api_token=".config('constants.MROBOTICS_API_TOKEN')."&order_id=".$report->id;
			
			$response = $this->getCurlPostMethod($url,$content);
			Apiresponse::create(['message'=>$response,'user_id'=>$report->user_id,'api_id'=>14,'api_type'=>'MROBOTICS_CHECK_STATUS','report_id'=>$report->id,'request_message'=>$url]);
            if($response)
            {
                try
                {
                    $resp_arr = json_decode($response);
                    $res_message = $resp_arr->data->status;
					$txnid = $resp_arr->data->tnx_id;
                    $REFNO = $resp_arr->data->id;
                    if($resp_arr->data->status == "success")
						return array('status'=>1,'txnid'=>$txnid,'ref_id'=>$REFNO,'msg'=>$res_message);
                    elseif($resp_arr->data->status == "failed")
						return array('status'=>2,'txnid'=>$txnid,'ref_id'=>$REFNO,'msg'=>$res_message);
                    elseif($resp_arr->data->status == "pending")
						return array('status'=>3,'txnid'=>$txnid,'ref_id'=>$REFNO,'msg'=>$res_message);
                    else
						return array('status'=>3,'txnid'=>$txnid,'ref_id'=>$REFNO,'msg'=>$res_message);
				}
				catch(\Exception $e)
				{
					return array('status'=>3,'txnid'=>'','ref_id'=>'','msg'=>$res_message);
				}
			}
			else
			{
				return array('status'=>3,'txnid'=>'','ref_id'=>'');
			}
		}
		return response()->json(['status'=>"NOT_ALLOWED",'msg'=>"Check Status Not Allowed"]);
	}
	
	public function doFailTranaction($report,$type=null)
	{
		$creditAmt = $report->amount + $report->debit_charge + $report->tds - $report->credit_charge;
		$report->status_id = 21;
		$report->save();
		$data=array(
					'number' => $report->number,
					'provider_id' => $report->provider_id,
					'amount' => $report->amount,
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
					'txn_type' => "$type REFUND",
					'bank_ref' => $report->id,
					'credit_charge' => $report->debit_charge,
					'debit_charge' => $report->credit_charge,
					'txnid'=>$report->id,
					'paytm_txn_id'=>$report->id,
					'pay_id' => time(),
					'user_id' => $report->user_id,
					'recharge_type' => $report->recharge_type,
					'channel' => $report->channel,
					'tds' => $report->tds,
					'gst' => $report->gst,
					'total_balance2' => 0,
					'beneficiary_id' => $report->beneficiary_id,
					'txn_initiated_date' => $report->created_at,
					);
		
		//print_r($data);die;
		Balance::where('user_id', $report->user_id)->increment('user_balance', $creditAmt);
		$userdetail = Balance::where('user_id', $report->user_id)->first();
		$data['total_balance']=$userdetail->user_balance;
		$data['opening_balance']=$userdetail->user_balance - $creditAmt ;
		$failReport = Report::create($data);
		return $failReport->id;	 
		
	}
	
	
	
	public function reverseCommission($report,$type,$insert_id=null)
	{
		if($type=="DMT"){
			$chagePrevStatus = 25;
			$newStatus = 26;
			$creditDetails = Report::where(['api_id'=>$report->api_id,'status_id'=>22,'txnid'=>$report->id])->get();
		}
		elseif($type=="RECHARGE"){
			$chagePrevStatus = 17;
			$newStatus = 27;
			$creditDetails = Report::where(['api_id'=>$report->api_id,'status_id'=>16,'txnid'=>$report->id])->get();
		}
		if(count($creditDetails) >3)
		{ 
		}
		//print_r($creditDetails);die;
			foreach($creditDetails as $creditDetail)
			{
				$creditDetail->status_id = $chagePrevStatus;
				$creditDetail->save();
				if($creditDetail->user_id == 1)
					
					Balance::where('user_id', $creditDetail->user_id)->decrement('admin_com_bal', $creditDetail->credit_charge);
				else
					Balance::where('user_id', $creditDetail->user_id)->decrement('user_balance', ($creditDetail->credit_charge-$creditDetail->tds));
				$userdetail = Balance::where('user_id', $creditDetail->user_id)->first();
				Report::create([
								'number' => $creditDetail->number,
								'provider_id' => $creditDetail->provider_id,
								'amount' => $creditDetail->amount,
								'profit'=>$creditDetail->profit,
								'api_id' => $creditDetail->api_id,
								'status_id' => $newStatus,
								'description' => 'Manual Refund',
								'bank_ref' => $report->id,
								'ip_address' => \Request::ip(),
								'txnid'=>$insert_id,
								'pay_id' => time(),
								'user_id' => $creditDetail->user_id,
								'recharge_type' => $creditDetail->recharge_type,
								'channel' => $creditDetail->channel,
								'credit_charge' => 0,
								'type' => 'DR',
								'txn_type' => 'COMMISSION_REVERSED',
								'debit_charge' => $creditDetail->credit_charge,
								'customer_number' => $creditDetail->customer_number,
								'tds' => $creditDetail->tds,
								'gst' => $creditDetail->gst,
								'opening_balance'=>($creditDetail->user_id==1) ? $userdetail->user_balance : ($userdetail->user_balance + $creditDetail->credit_charge),
								'admin_com_bal'=>$userdetail->admin_com_bal,
								'total_balance' => $userdetail->user_balance,
								'beneficiary_id' => $report->beneficiary_id,
								'txn_initiated_date' => $report->created_at,
								]);
			}
		return array('status'=>2,'txnid'=>'','ref_id'=>'','msg'=>"Transaction Failed, Amount credited");
	}
	public function getDistAgent($userId)
	{
		return User::where('parent_id',$userId)->pluck('id','id')->toArray();
	}
	public function getMdAgent($userId)
	{
		 $parent_details = User::where(['parent_id'=>$userId,'role_id'=>4])->pluck('id','id')->toArray();
		return User::whereIn('parent_id',$parent_details)->orWhere('parent_id',$userId)->pluck('id','id')->toArray();
	}
	public function getMdMember($userId)
	{
		 $parent_details = User::where(['parent_id'=>$userId,'role_id'=>4])->pluck('id','id')->toArray();
		return User::whereIn('parent_id',$parent_details)->orWhere('parent_id',$userId)->orWhere('id',Auth::id())->pluck('id','id')->toArray();
	}
	public function getDistributor($userId)
	{
		return User::where(['parent_id'=>$userId,'role_id'=>4])->pluck('id','id')->toArray();
	}
	public function getDistMember($userId)
	{
		return User::where('parent_id',$userId)->orWhere('id',$userId)->pluck('id','id')->toArray();
	}
	public function getUserDetails()
	{
		if(Auth::user()->role_id == 15)
			return User::find(Auth::user()->parent_id);
		else
			return User::find(Auth::id());
	}
	public function getAllMemberIncludingLoggedUser($roleId,$loginUserId)
    {
		if ($roleId == 3)
			return $this->getMdMember($loginUserId);
		elseif($roleId == 4)
			return $this->getDistMember($loginUserId);
		else
			return array($loginUserId);
    }
	public function getDbFromatDate($startDate,$endDate)
	{
		$start_date = date("Y-m-d H:i:s", strtotime($startDate));
		$end_date = date("Y-m-d H:i:s", strtotime($endDate));
		return ['start_date'=>$start_date,'end_date'=>$end_date];
	}
	public function chageDateFormat($date)
	{
		return ate("Y-m-d", strtotime($date));
	}
	public function creditCommission($d_id,$m_id,$a_id,$user_id,$mobile_number,$bene_id,$channel,$insert_id,$amount,$dist_charge_data,$md_charge_data,$admin_charge_data,$bank_account,$reportDetails,$apiId,$bankName=null)
    {
		if($apiId==10)
			$txnType="AEPS_COMMISSION";
		else
			$txnType="DMT_COMMISSION";
			DB::beginTransaction();
			try
			{
				if($d_id!=''&& count($dist_charge_data))
				{
						$commision = $dist_charge_data['credit_charge'];
						if($commision>0){
							$tds = (($commision)* 5)/100;
							$cr_amt = $commision - $tds;
						$openingBal = Balance::select('user_balance')->where('user_id', $d_id)->first()->user_balance;
						//$dist_charge_data['credit_charge'] = $cr_amt;
						$dist_charge_data['gst'] = 0;
						$dist_charge_data['tds'] = $tds;
						$dist_charge_data['type']='CR';
						Balance::where('user_id', $d_id)->increment('user_balance', $cr_amt);
						$dist_charge_data['number']=$bank_account;
						$dist_charge_data['amount']=$amount;
						$dist_charge_data['provider_id']=41;
						$dist_charge_data['profit']=0;
						$dist_charge_data['api_id']=$apiId;
						$dist_charge_data['status_id']=22;
						$dist_charge_data['description']=$bankName;
						$dist_charge_data['txn_type'] = $txnType;
						$dist_charge_data['pay_id']=time();
						$dist_charge_data['user_id']=$d_id;
						$dist_charge_data['txnid']=$insert_id;
						$dist_charge_data['paytm_txn_id']=$insert_id;
						$dist_charge_data['customer_number']=$mobile_number;
						$dist_charge_data['recharge_type']=0;
						$dist_charge_data['opening_balance']=$openingBal;
						$dist_charge_data['total_balance']=$openingBal+$cr_amt;
						if($apiId!=10)
							$dist_charge_data['beneficiary_id']=$bene_id;
						$dist_charge_data['channel']=$channel;
						Report::create($dist_charge_data);
						$reportDetails->dist_commission = $cr_amt;
						$reportDetails->save();
						}
					
						
				}
				if($m_id!='' && count($md_charge_data))
				{
						$commision = $md_charge_data['credit_charge'];
						if($commision>0){
							$tds = (($commision)* 5)/100;
							$mcr_amt = $commision - $tds;
						$openingBal = Balance::select('user_balance')->where('user_id', $m_id)->first()->user_balance;
						//$md_charge_data['credit_charge'] = $mcr_amt;
						$md_charge_data['gst'] = 0;
						$md_charge_data['tds'] = $tds;
						Balance::where('user_id', $m_id)->increment('user_balance', $mcr_amt);
						$md_charge_data['number']=$bank_account;
						$md_charge_data['amount']=$amount;
						$md_charge_data['provider_id']=41;
						$md_charge_data['type']='CR';
						$md_charge_data['txn_type'] = $txnType;
						$md_charge_data['profit']=0;
						$md_charge_data['api_id']=$apiId;
						$md_charge_data['status_id']=22;
						$md_charge_data['description']=$bankName;
						$md_charge_data['pay_id']=time();
						$md_charge_data['user_id']=$m_id;
						$md_charge_data['txnid']=$insert_id;
						$md_charge_data['paytm_txn_id']=$insert_id;
						$md_charge_data['recharge_type']=0;
						$md_charge_data['customer_number']=$mobile_number;
						$md_charge_data['opening_balance']=$openingBal;
						$md_charge_data['total_balance']=$openingBal+$mcr_amt;
						if($apiId!=10)
							$md_charge_data['beneficiary_id']=$bene_id;
						$md_charge_data['channel']=$channel;
						Report::create($md_charge_data);
						$reportDetails->md_commission = $mcr_amt;
						$reportDetails->save();
						}
					
				}
				if($a_id!='' && count($admin_charge_data))
				{
						$commision = $admin_charge_data['credit_charge'];
						if($commision>0){
							$tds =  0;//(($commision)* 5)/100;
							$mcr_amt = $commision - $tds;

 
						$openingBal = Balance::where('user_id', $a_id)->first();
						$admin_charge_data['credit_charge'] = $mcr_amt;
						$admin_charge_data['gst'] = 0;
						$admin_charge_data['tds'] = $tds;
						Balance::where('user_id', $a_id)->increment('admin_com_bal', $mcr_amt);
						$admin_charge_data['number']=$bank_account;
						$admin_charge_data['amount']=$amount;
						$admin_charge_data['provider_id']=41;
						$admin_charge_data['type']='CR';
						
						$admin_charge_data['profit']=0;
						$admin_charge_data['recharge_type']=0;
						$admin_charge_data['api_id']=$apiId;
						$admin_charge_data['status_id']=22;
						$admin_charge_data['description']=$bankName;
						$admin_charge_data['txn_type']=$txnType;
						$admin_charge_data['pay_id']=time();
						$admin_charge_data['user_id']=$a_id;
						$admin_charge_data['txnid']=$insert_id;
						$admin_charge_data['paytm_txn_id']=$insert_id;
						$admin_charge_data['customer_number']=$mobile_number;
						$admin_charge_data['opening_balance']=$openingBal->user_balance;
						$admin_charge_data['total_balance']=$openingBal->user_balance;
						$admin_charge_data['admin_com_bal']=$openingBal->admin_com_bal+$mcr_amt;
						if($apiId!=10)
							$admin_charge_data['beneficiary_id']=$bene_id;
						$admin_charge_data['channel']=$channel;
						Report::create($admin_charge_data);	
						$reportDetails->admin_commission = $mcr_amt;
						$reportDetails->save();	
						}						
				}
				DB::commit();
			}
			catch(Exception $e)
			{
				DB::rollback();
				throw $e;
				TransactionReportException::create(['report_id'=>$insert_id,'exception'=>$e->getMessage(),'exception_type'=>"DURING GIVEN COMMISSION",'dist_data' =>json_encode($dist_charge_data),'md_data' =>json_encode($md_charge_data),'admin_data' =>json_encode($admin_charge_data),'other'=>json_encode(array('dist_id'=>$d_id,'m_id'=>$m_id,'a_id'=>$a_id))]);
			}
                                    
    }
	public function isActiveOnline($apiId) 
	{

		return ActiveService::where(['api_id'=>$apiId])->first();
	}
	public function changeDateFormat($date)
	{
		 return date("d-m-Y H:i:s",strtotime($date));
	}
	public static function getBankDownList() 
	{
		$bankDownDetails = Masterbank::where(['bank_status'=>0])->select('bank_name','updated_at')->get();
		$bankDownLists='';
		foreach($bankDownDetails as $bankName)
			$bankDownLists = $bankName->bank_name.' is down since : '. $bankName->updated_at . ', '.$bankDownLists;
		return $bankDownLists;
	}
	public function calCommission($comission)
	{
		try{
			$tds = (($comission* 5)/100);
			return ($comission-$tds);
		}
		catch(Exception $e)
		{
			return 0;
		}
	}
	public function insTransactionCheckStatus($report)//INSTANTPAY
	{
		if(in_array($report->status_id,array(1,3)))
		{
			$recordId = $report->id;
			$dateTime = date('Y-m-d', strtotime($report->created_at));
			$token = config('constants.INSTANT_KEY');
			$content ="{\"token\":\"$token\",\"request\":{\"external_id\":\"$recordId\",\"transaction_date\":\"$dateTime\"}}";
			$url = config('constants.INSTANT_PAY_URL') ."/ws/status/checkbyexternalid";
			
			$apiResp = Apiresponse::create(['api_id'=>$report->api_id,'user_id'=>$report->user_id,'api_type'=>"TRANSACTION_CHECK",'report_id'=>$report->id,'request_message'=>$content]);
			$apiResponse = $this->getCurlPostIMethod($url,$content);
			$apiResp->message = $apiResponse;
			$apiResp->save();
			try
			{
				$result =json_decode($apiResponse);
				if($result->statuscode=="TXN" && $result->status=="Call Status" )
				{
					if($result->data->external_id == $recordId && $result->data->order_id == $report->txnid)
					{
						if($result->data->transaction_status =="SUCCESS")
						{
							$report->status_id=1;
							$report->bank_ref=$result->data->serviceprovider_id;
							$report->save();
							return response()->json(['status'=>1,'msg'=>"Success"]);
						}
						elseif($result->data->transaction_status =="REFUND")
						{
							if(in_array(Report::find($report->id)->status_id,array(1,3)))
							{
								$lastInsertId = $this->doFailTranaction($report,"TRANSACTION");
								$this->reverseCommission($report,"DMT",$lastInsertId);
								DB::commit();
								return response()->json(['status'=>2,"msg"=>"Transaction Failed, Amount credited"]);
							}
							return response()->json(['status'=>2,"msg"=>"Transaction has been update, Please try again"]);
						}
						else
							return response()->json(['status'=>3,'msg'=>"Pending"]);
					}
					elseif($result->data->transaction_status =="NOTFOUND")
					{
						if(in_array(Report::find($report->id)->status_id,array(1,3)))
							{
								$lastInsertId = $this->doFailTranaction($report,"TRANSACTION");
								$this->reverseCommission($report,"DMT",$lastInsertId);
								DB::commit();
								return response()->json(['status'=>2,"msg"=>"Transaction Failed, Amount credited"]);
							}
					}
				}
				else
				{
					return response()->json(['status'=>3,'msg'=>@$report->status->status]);
				}
			}
			catch(Exception $e)
			{
				return response()->json(['status'=>4,'msg'=>"Error"]);
			}
			
		}
	}
	public function newCheckAgentActivation($userDetails,$remoteIpAddress,$request)
	{
		if($userDetails->role_id !=7)
			return ['status'=>401,'message'=>"Access denied"];
		elseif($userDetails->status_id==0)
			return ['status'=>101,'message'=>"You are not activated yet! Please Contact with Admin"];
		elseif(!in_array($remoteIpAddress,array($userDetails->member->server_ip,$userDetails->member->server_ip_second)))
			return ['status'=>102,'message'=>"Your IP is not configured or Invalid"];
		elseif($userDetails->member->secret_key != $request->secretKey) 
			return ['status'=>103,'message'=>"Your secret key is invalid"];
		if($userDetails->id != $request->userId)
			return ['status'=>100,'message'=>"User Id is invalid"];
		else
			return ['status'=>1,'message'=>"Extra Validation Passed"];
	}
	public function getMyServerIp($request)
	{
		$rules = array(
			'userId' => 'required|numeric|regex:/^[0-9]+$/',
			'secretKey' => 'required|regex:/^[A-Za-z0-9]+$/',
		);
		$validator = Validator::make($request->all(), $rules);
		if ($validator->fails()){
		  return Response::json(['status'=>10,
			'message' => "Missing/Invalid Parameters",
			'errors' => $validator->getMessageBag()->toArray(),
			]);
		}
		return response()->json(['status'=>1,'message'=>"Your ip is ".$_SERVER['REMOTE_ADDR']]);
	}
	public function checkCurrentBankStatus($dumyAccountNumber,$bankSortName)
	{
		$instantPay = new \App\Library\InstantPayDMT;
		$content =array('account'=>$dumyAccountNumber,"outletid"=>1,"bank"=>$bankSortName);
        $apiResp = $instantPay->isBankdDownOrNot($content);
	}
	public function isHaveBlockedAmount($blockedAmount,$availableAmount)
	{
		if($blockedAmount > $availableAmount)
			return ['status'=>91,'message'=>"Your do not have minimum balanced amount Rs. ".$blockedAmount];
		return ['status'=>1,'message'=>"OK"];
	}
	public function getPrefix($roleId)
	{
		if($roleId== 4)
			return "DLM";
		elseif($roleId== 3)
			return "MD";
		elseif($roleId== 7)
			return "API";
		elseif($roleId== 15)
			return "WL";
		elseif($roleId== 16)
			return "AWL";
		else
			return "REM";
	}
	
	
	
    
}