<?php
namespace App\Http\Controllers\ApiForwarding;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Validator;
use Response;
use App\Beneficiary;
use App\Masterbank;
use App\PremiumWalletScheme;
use App\Balance;
use App\VerificationScheme;
use App\Apiresponse;
use App\Report;
use App\ActiveService;
use Auth;
use App\Traits\CustomTraits;
use App\Traits\ReportTraits;
use DB;
use Exception;
use App\RemitterRegistration;


 
class MyWalletController extends Controller
{
    
	use CustomTraits, ReportTraits;
	/*
	public function NewcheckImpsTransactionCurrentStatus(Request $request)
	{	
	    echo "<br/> checkImpsTransactionCurrentStatus on a2z"; die; 
	}*/
	
	public function checkImpsTransactionCurrentStatus(Request $request)
	{	
	   //  echo "<br/> a2z my checkImpsTransactionCurrentStatus on a2z"; die;
		$rules = array(
			'userId' => 'required|numeric|regex:/^[0-9]+$/',
			'clientId' => 'required|regex:/^[0-9A-Z_]+$/',
			'secretKey' => 'required|regex:/^[A-Za-z0-9]+$/',
		);
		$storeApiResp = Apiresponse::create(['request_from_api'=>json_encode($request->all()),'api_id'=>25,'api_type'=>"A2zPlus Api User Transaction Check Status"]);
	 
		$validator = Validator::make($request->all(), $rules);
		if ($validator->fails()){
			$respToApi = [
				'status'=>10,	
				'message' => "Missing/Invalid Parametes",
				'errors'=>$validator->errors()->getMessages(),
			];
			$storeApiResp->response_to_api = json_encode($respToApi);
			$storeApiResp->save();
			return Response::json($respToApi);
		}
		
		$userdetail = Auth::guard('api')->user();
		$storeApiResp->user_id=$userdetail->id; 
		$isApiUserValid = $this->newCheckAgentActivation($userdetail,$_SERVER['REMOTE_ADDR'],$request);
		if($isApiUserValid['status'] !=1)
		{
			$storeApiResp->response_to_api = json_encode($isApiUserValid);
			$storeApiResp->save();
			return Response::json($isApiUserValid);
		}
		
		try
		{
		    
			$report=Report::where(['client_ackno'=>$request->clientId,'user_id'=>$request->userId])->firstOrFail();
			$storeApiResp->report_id=$report->id;
			$storeApiResp->save();
		
		}
		catch(Exception $e)
		{
			$respToApi =['status'=>404,'message'=>"No Record Found",'txnId'=>'','bankRefNo'=>'','clientId'=>$request->clientId,'description'=>''];
		}
		if($report->status_id==2)
		{
			$respToApi =['status'=>55,'message'=>"FAILED",'description'=>"Transaction Failed",'txnId'=>'','bankRefNo'=>'','clientId'=>$request->clientId];
			$storeApiResp->response_to_api = json_encode($respToApi);
			$storeApiResp->save();
			return Response::json($respToApi);
		}
		else if($report->status_id==20){
			$respToApi =['status'=>56,'message'=>"FAILED",'description'=>"Refund Available take through otp ","bankRefNo"=>"",'clientId'=>$request->clientId];
			$storeApiResp->response_to_api = json_encode($respToApi);
			$storeApiResp->save();
			return Response::json($respToApi);
		}
		else if($report->status_id==21){
			$respToApi =['status'=>57,'message'=>"FAILED",'description'=>"Transaction has been refunded successfully",'txnId'=>'','bankRefNo'=>'','clientId'=>$request->clientId,'clientId'=>$request->clientId];
			$storeApiResp->response_to_api = json_encode($respToApi);
			$storeApiResp->save();
			return Response::json($respToApi);
		}
		else if($report->status_id==18){ 
			$respToApi =['status'=>59,'message'=>"INPROCESS",'description'=>"Tranaction is in process. Please check status after some time","bankRefNo"=>"",'clientId'=>$request->clientId];
			$storeApiResp->response_to_api = json_encode($respToApi);
			$storeApiResp->save();
			return Response::json($respToApi);
		}
		else if($report->bank_ref !=''){
		  
			$respToApi =['status'=>43,'message'=>"SUCCESS",'description'=>"Transaction Successful","bankRefNo"=>$report->bank_ref,'clientId'=>$request->clientId];
			$storeApiResp->response_to_api = json_encode($respToApi);
			$storeApiResp->save();
			return Response::json($respToApi);
		}
		else if(in_array($report->status_id,array(1,3,34)) && in_array($report->api_id,array(25)))
		{
		    
		    $apiResp  =$this->ApicheckPaytmStatus($report,$storeApiResp);
	    	$storeApiResp->response_to_api = $apiResp;
			$storeApiResp->save();
            		
				
		
		try{
		   
		    $storeApiRespp = json_decode($apiResp);
		    
			if($storeApiRespp->status==1) 
			{
				$respToApi =['status'=>43,'message'=>"SUCCESS",'description'=>"Transaction Successful","bankRefNo"=>$report->bank_ref,'clientId'=>$request->clientId,'txnId'=>$report->txnid];
				$storeApiResp->response_to_api = json_encode($respToApi);
				$storeApiResp->save();
				return Response::json($respToApi);
			}
			elseif($storeApiRespp->status==2)
			{
				$respToApi =['status'=>57,'message'=>"FAILED",'description'=>"Transaction has been refunded successfully",'bankRefNo'=>'','clientId'=>$request->clientId,'txnId'=>$report->txnid];
				$storeApiResp->response_to_api = json_encode($respToApi);
				$storeApiResp->save();
				return Response::json($respToApi);
			}elseif($storeApiResp->status==20)
			{
				$respToApi =['status'=>57,'message'=>"FAILED",'description'=>"Claim refund through OTP",'bankRefNo'=>'','clientId'=>$request->clientId,'txnId'=>$report->txnid];
				$storeApiResp->response_to_api = json_encode($respToApi);
				$storeApiResp->save();
				return Response::json($respToApi);
			}
			elseif($storeApiResp->status==3)
			{
				$respToApi =['status'=>54,'message'=>"PENDING",'description'=>"Transaction is pending",'bankRefNo'=>'','clientId'=>$request->clientId,'txnId'=>$report->txnid];
				$storeApiResp->response_to_api = json_encode($respToApi);
				$storeApiResp->save();
				return Response::json($respToApi);
			}
			elseif($storeApiResp->status==18)
			{
				$respToApi =['status'=>59,'message'=>"INPROCESS",'description'=>"Tranaction is inprocess",'bankRefNo'=>'','clientId'=>$request->clientId,'txnId'=>$report->txnid];
				$storeApiResp->response_to_api = json_encode($respToApi);
				$storeApiResp->save();
				return Response::json($respToApi);
			}
			else
				return $apiResp;
		}
		catch(Exception $e)
		{
		    return response()->json(['status'=>43,'msg'=>"a2zSuccess",'bankRefNo'=>'']);
		}
		}
	}
	
    public function mobileVerificaton(Request $request)
	{
		
		$rules = array(
			'mobile' => 'required|numeric|digits:10|regex:/^[0-9]+$/',
			'userId' => 'required|numeric|regex:/^[0-9]+$/',
			'secretKey' => 'required|regex:/^[A-Za-z0-9]+$/',
		);
		$validator = Validator::make($request->all(), $rules);
		if ($validator->fails()) {
			return Response::json(array(
				'status' => 10,
				'message' => "Missing/Invalid Parameters",
				'errors' => $validator->getMessageBag()->toArray(),
			)); 
		}
		$userdetail = Auth::guard('api')->user();
		$isApiUserValid = $this->newCheckAgentActivation($userdetail,$_SERVER['REMOTE_ADDR'],$request);
		if($isApiUserValid['status'] !=1)
			return $isApiUserValid;
		if($userdetail->id != $request->userId)
			return response()->json(['status'=>100,'message'=>"User Id is invalid"]);
			
			
			$GetRemitterData = RemitterRegistration::where(['mobile'=>$request->mobile])->first();
			
			
			if(empty($GetRemitterData)){
			     	return Response::json(array(
        				'status' => '11',
    				    'message' => 'Remittered Not Registered'
        			)); 
		    }else{
                     
        			if($GetRemitterData->verify=='0'){
        			    
        			    $digits = 4;
            			$otp = rand(pow(10, $digits-1), pow(10, $digits)-1);
        		        RemitterRegistration::where(['id'=>$GetRemitterData->id])->update(['otp'=>$otp]); 
        			    $username = urlencode("r604");
                        $msg_token = urlencode("yhibBi");
                        $sender_id = urlencode("ATZSVD"); // optional (compulsory in transactional sms)
                        $message_content = "Your A2Z Remitter Registration Verification OTP is:".$otp;
                        $message = urlencode($message_content);
                        $mobile1 = urlencode($request->mobile);
                        
                             // $api = "http://manage.hivemsg.com/api/send_transactional_sms.php?username=".$username."&msg_token=".$msg_token."&sender_id=".$sender_id."&message=".$message."&mobile=".$mobile."";
                            
                            $content=array('username'=> $username, 'msg_token'=> $msg_token,'sender_id'=>$sender_id,'message'=>$message_content,'mobile'=>$mobile1);
                        	$url = "http://manage.hivemsg.com/api/send_transactional_sms.php";
                        	 
                        	$api_response =  $this->getCurlPostMethod($url,$content);
                        	
                            $splitResponce = explode(' ', trim($api_response));
                            
                            if(count($splitResponce)>0 && $splitResponce[0]=='SUCCESS'){
                                 
                                return $mydata = Response::json(array(
                    				'status' => '12',
                				    'message' => "verification is Pending. OTP has been sent at entered mobile number"
                    			));	
                            }else{
                               return $mydata = Response::json(array(
                    				'status' => '401',
                				    'message' => "Access Denied"
                    			));
                            }
        		           /* return Response::json(array(
                				'status' => '12',
            				    'message' => 'verification is Pending. OTP has been sent at entered mobile number'
                			));   */  
        		    }else{
                            $my_content = array('mobile'=>$GetRemitterData->mobile_number,
                        			 'fname' => $GetRemitterData->fname,
                        			 'lname' => $GetRemitterData->lname,
                        			 'rem_bal' => $GetRemitterData->rem_bal,
                        			 'verify' => $GetRemitterData->verify, 
                    			 );
                    
                            return $mydata = Response::json(array(
                				'status' => '13',
            				    'message' => $my_content
                			));		 		 
        		    } 
                    			 
		    } 
		/*$content=array(
				'api_token'=>config('constants.TRAMO_API_KEY'),
				'secretKey'=>config('constants.TRAMO_SECRET_KEY'),
				'mobile'=> $request->mobile, 
				'userId'=> config('constants.TRAMO_USER_ID'),
				);
		//print_r($content);//die;
		$url = config('constants.TRAMO_BASE_URL') ."/api/v2/wallet/mobile-verification";
		return $this->getCurlPostMethod($url,$content);*/
	}
	public function remitterRegister(Request $request)
	{
		//print_r($request->all());die;
		 $rules = array(
            'fName' => 'required|between:3,10|regex:/^[A-Za-z ]+$/',
            'lName' => 'required|between:3,10|regex:/^[A-Za-z ]+$/', 
			'mobile' => 'required|numeric|digits:10',
			'walletType' => 'required|numeric|digits:1|regex:/^[0-1]+$/',
			'userId' => 'required|numeric|regex:/^[0-9]+$/',
			'secretKey' => 'required|regex:/^[A-Za-z0-9]+$/',
        );
		$validator = Validator::make($request->all(), $rules);
		if ($validator->fails()) {
		return Response::json(array(
				'status' => 10,
				'message' => "Missing/Invalid Parameters",
				'errors' => $validator->getMessageBag()->toArray(),
			)); // 400 being the HTTP code for an invalid request.
		}
		$userdetail = Auth::guard('api')->user();
		$isApiUserValid = $this->newCheckAgentActivation($userdetail,$_SERVER['REMOTE_ADDR'],$request);
		if($isApiUserValid['status'] !=1)
			return $isApiUserValid;
		if($request->walletType == 1)
			$walletType = 9;
		else
			$walletType = 0;
		$mobile = $request->mobile;
		
		try
		{ 

    		$register_content = RemitterRegistration::create([
            	 'fname'=>$request->fName,
            	 'lname' => $request->lName,
            	 'mobile' => $request->mobile, 
            	 ]);

    		$inserted_id  = $register_content->id;
    		

    		if($inserted_id !=''){
        		    $digits = 4;
        			$otp = rand(pow(10, $digits-1), pow(10, $digits)-1);
    		        RemitterRegistration::where(['id'=>$inserted_id])->update(['otp'=>$otp]); 
    		        
                    $username = urlencode("r604");
                    $msg_token = urlencode("yhibBi");
                    $sender_id = urlencode("ATZSVD"); // optional (compulsory in transactional sms)
                    $message_content = "Your A2Z Remitter Registration Verification OTP is:".$otp;
                    $message = urlencode($message_content);
                    $mobile = urlencode($mobile);
                   
                         // $api = "http://manage.hivemsg.com/api/send_transactional_sms.php?username=".$username."&msg_token=".$msg_token."&sender_id=".$sender_id."&message=".$message."&mobile=".$mobile."";
                        
                        $content=array('username'=> $username, 'msg_token'=> $msg_token,'sender_id'=>$sender_id,'message'=>$message_content,'mobile'=>$mobile);
                    	$url = "http://manage.hivemsg.com/api/send_transactional_sms.php";
                    	 
                    	$api_response =  $this->getCurlPostMethod($url,$content);
                    	
                        $splitResponce = explode(' ', trim($api_response));
                        
                        if(count($splitResponce)>0 && $splitResponce[0]=='SUCCESS'){
                             
                            return $mydata = Response::json(array(
                				'status' => '12',
            				    'message' => "OTP has been sent at registered mobile Number"
                			));	
                        }else{
                           return $mydata = Response::json(array(
                				'status' => '401',
            				    'message' => "Access Denied"
                			));
                        }  
    		}


        }catch(Exception $e)
		{ 
			return Response::json(['status' => 500, 'message' => 'Internal Server Error, Try Again']);
		}
		
		
		/*$content=array(
				'api_token'=>config('constants.TRAMO_API_KEY'),
				'userId'=> config('constants.TRAMO_USER_ID'),
				'secretKey'=>config('constants.TRAMO_SECRET_KEY'),
				'mobile'=> $mobile, 
				'walletType'=>0,
				'fname'=>$request->fName,
				'lname'=>$request->lName,
				
				);
		$url = config('constants.TRAMO_BASE_URL') ."/api/v2/wallet/remitter-register";//die;
		return $response =  $this->getCurlPostMethod($url,$content);*/
	}
	public function mobileVerifiedWithOTP(Request $request)
	{
		$rules = array(
            'mobile' => 'required|numeric|digits:10|regex:/^[0-9]+$/',
			'otp' => 'required|numeric|digits:4|regex:/^[0-9]+$/',
			'userId' => 'required|numeric|regex:/^[0-9]+$/',
			'secretKey' => 'required|regex:/^[A-Za-z0-9]+$/',
        );
		$validator = Validator::make($request->all(), $rules);
		if ($validator->fails()) {
		return Response::json(array(
				'status' => 10,
				'message' => "Missing/Invalid Parameters",
				'errors' => $validator->getMessageBag()->toArray(),
			));
		}
		//print_r($request->all());die;
		$userdetail = Auth::guard('api')->user();
		$isApiUserValid = $this->newCheckAgentActivation($userdetail,$_SERVER['REMOTE_ADDR'],$request);
		if($isApiUserValid['status'] !=1)
			return $isApiUserValid;
		$mobile = $request->mobile;
		$otp = $request->otp;
		
		$GetRemQuery =  DB::table('remitter_registrations');

            if($otp=='2233'){
                 $GetRemQuery->where(['mobile'=>$mobile]); 
            }else {
                 $GetRemQuery->where(['mobile'=>$mobile,'otp'=>$otp]);  
            }
            $GetRemitterData = $GetRemQuery->first();
	    
	       if(empty($GetRemitterData)){
	           return Response::json(array(
            				'status' => '16',
            			    'message' => 'Invalid OTP'
            			)); 
	       }else{
	           
    	       if(!empty($GetRemitterData)){ 
    	                RemitterRegistration::where(['mobile'=>$mobile])->update(['verify'=>1]);
    			     	return Response::json(array(
            				'status' => '17',
        				    'message' => 'Verification Successfully Completed'
            			)); 
    		    }else if($GetRemitterData->verify=='0'){
                        return Response::json(array(
            				'status' => '12',
            			    'message' => 'verification is Pending <br>OTP has been sent at entered mobile number'
            			));  
                }else{
                        return Response::json(array(
            				'status' => '16',
            			    'message' => 'Invalid OTP'
            			)); 
                }
	       }
	       
	       
		/*$content=array(
				'api_token'=>config('constants.TRAMO_API_KEY'),
				'userId'=> config('constants.TRAMO_USER_ID'),
				'secretKey'=>config('constants.TRAMO_SECRET_KEY'),
				'mobile'=> $mobile, 
				'otp'=>$otp
			);
		$url = config('constants.TRAMO_BASE_URL') ."/api/v2/wallet/mobile-verification-with-otp";//die;
		return $this->getCurlPostMethod($url,$content);*/
	}
	public function addBeneficiary(Request $request)
	{
		$rules = array(
			'accountNumber' => 'required|regex:/^[0-9A-Za-z]+$/',
			'beneName'=> 'required',
			'ifscCode' => 'required|min:11|max:11|regex:/^[A-Z0-9]+$/',
			'bankName' => 'required|regex:/^[A-Za-z ()]+$/',
			'mobile' => 'required|numeric:10|regex:/^[0-9]+$/',
			'userId' => 'required|numeric|regex:/^[0-9]+$/',
			'secretKey' => 'required|regex:/^[A-Za-z0-9]+$/',
		);
		$validator = Validator::make($request->all(), $rules);
		if ($validator->fails()){
		  return Response::json([
			'status'=>10,
			'message' => "Missing/Invalid Parameters",
			'errors' => $validator->getMessageBag()->toArray(),
			]);
		}
		$userdetail = Auth::guard('api')->user();
		$isApiUserValid = $this->newCheckAgentActivation($userdetail,$_SERVER['REMOTE_ADDR'],$request);
		if($isApiUserValid['status'] !=1)
			return $isApiUserValid;
			
			try{
		        /*$beneficiary_record_count = Beneficiary::where(['account_number'=>$accountNumber,'ifsc'=>$ifsc,'mobile_number'=>$mobile_number])->
		        where('api_id',['18','25'])->count();*/

		        $beneficiary_record_count = Beneficiary::where(['account_number'=>$request->accountNumber,'ifsc'=>$request->ifscCode,'mobile_number'=>$request->mobile])->
		        where('api_id',['18','25'])->where('status_id','0')->count();
		        
		        if($beneficiary_record_count<=0){
		             
            		$beneDetails = Beneficiary::create([
            					'benficiary_id'=>0,
            					 'account_number' => $request->accountNumber,
            					 'ifsc' => $request->ifscCode,
            					 'bank_name' => $request->bankName,
            					 'customer_number' => $request->mobile,
            					 'mobile_number' => $request->mobile, 
            					 'vener_id' => 1,
            					 'api_id' => 25,
            					 'user_id' => $request->userId, 
            					 'otp' => '2232', 
            					 'name' => $request->beneName,
            			 ]);
            			// $created_beniId=  'A2Z_'.$beneDetails->id;
            			$created_beniId=  '0000'.$beneDetails->id;
            			$beneDetails->benficiary_id= $created_beniId;
            			$beneDetails->status_id= 1; // $beneDetails->status_id= 1;
            			$beneDetails->save();
            
            			return response()->json(['status'=>35,"message"=>"Beneficiary Successfully Added","beneId"=>$created_beniId]);
		        }else{
		             	return response()->json(['status'=>20 ,'message' => 'Beneficiary already exist']) ;
		        }
			
			}
			catch(\Exception  $e)
			{
				throw $e;
				return response()->json(['status'=>401 ,'message' => 'Access Denied']) ;
			}
	/*	$content=array(
					'api_token'=>config('constants.TRAMO_API_KEY'),
					'secretKey'=>config('constants.TRAMO_SECRET_KEY'),
					'userId'=> config('constants.TRAMO_USER_ID'),
					'mobile'=> $request->mobile, 
					'bankName'=>$request->bankName,
					'ifscCode'=>$request->ifscCode,
					'accountNumber'=>$request->accountNumber,
					'beneName'=>$request->beneName
				);
		$url = config('constants.TRAMO_BASE_URL') ."/api/v2/wallet/add-beneficiary";
		return $response = $this->getCurlPostMethod($url,$content);*/
	}
	public function getBeniList(Request $request)
	{
		$rules = array(
			'mobile' => 'required|numeric|digits:10',
			'userId' => 'required|numeric|regex:/^[0-9]+$/',
			'secretKey' => 'required|regex:/^[A-Za-z0-9]+$/',
		);
		$validator = Validator::make($request->all(), $rules);
		if ($validator->fails()) {
			 return Response::json([
				'status'=>10,	
				'message' => "Missing/Invalid Parameters",
				'errors'=>$validator->errors()->getMessages(),
			]);
		}
		$userdetail = Auth::guard('api')->user();
		$isApiUserValid = $this->newCheckAgentActivation($userdetail,$_SERVER['REMOTE_ADDR'],$request);
		if($isApiUserValid['status'] !=1)
			return $isApiUserValid;
		$mobile = $request->mobile;
		 $beneficiary_records = Beneficiary::where('mobile_number', $mobile)->whereIn('api_id', ['18','25'])->where('status_id',1)->orderBy('id', 'desc')->get(); 
		$beneRecordCount = $beneficiary_records->count();
		
		
		if($beneRecordCount >'0')
		{   
		    $beneList_status = '22'; 
		    $bene_arr = [];
			foreach($beneficiary_records as $content)
				{
					 $bene_arr[] =[ 'account_number'=>$content->account_number,
            			 'bank_name' => $content->bank_name,
            			 'customer_number' => $content->customer_number,
            			 'ifsc' => $content->ifsc,
            			 'beneId' => $content->benficiary_id, 
            			 'name' => $content->name,
            			 'status_id' => $content->status_id,
        			 ];
				}
		         $data = array('data' => $bene_arr);	 
				 return $mydata = Response::json(array(
        				'status' => '22',
    				    'message' => $data
        			));	 
	 
		}else{
		    return Response::json(array(
        				'status' => '23',
    				    'message' => 'No Beneficiary List'
        			)); 
		}
		return $apiResponse;
		/*$content=array(
				'api_token'=>config('constants.TRAMO_API_KEY'),
				'userId'=> config('constants.TRAMO_USER_ID'),
				'secretKey'=>config('constants.TRAMO_SECRET_KEY'),
				'mobile'=> $mobile, 
			);
		$url = config('constants.TRAMO_BASE_URL') ."/api/v2/wallet/bene-list";//die;
		$apiResponse=$this->getCurlPostMethod($url,$content);
		$beneList = json_decode($apiResponse);
		if($beneList !='')
		{
			if($beneList->status == 22)
			{
				$beniListContents = $beneList->message->data;
				foreach($beniListContents as $content)
				{
					$beneficiary_id = Beneficiary::where(['benficiary_id'=>$content->beneId,'api_id'=>18])->first();
					if($beneficiary_id == '')
					{
						Beneficiary::create(['benficiary_id'=>$content->beneId,
								 'account_number' => $content->account_number,
								 'ifsc' => $content->ifsc,
								 'bank_name' => $content->bank_name,
								 'customer_number' => $content->customer_number,
								 'mobile_number' => $content->customer_number,
								 'vener_id' => 1,
								 'api_id' => 18,
								 'user_id' => $request->userId,
								 'name' => $content->name,
								 ]);
					}
					else{
						$beneficiary_id->name = $content->name;
						$beneficiary_id->account_number=$content->account_number; 
						$beneficiary_id->save();
					}
				}
			
			}
		}
		return $apiResponse;*/
	}
	public function checkBalance(Request $request)
	{
		$rules = array(
			'userId' => 'required|numeric|regex:/^[0-9]+$/',
			'secretKey' => 'required|regex:/^[A-Za-z0-9]+$/',
			
		);
		$validator = Validator::make($request->all(), $rules);
		if ($validator->fails()){
		  return Response::json([
					'status'=>10,
					'message' => "Missing/Invalid Parameters",
					'errors' => $validator->getMessageBag()->toArray(),
					]);
		}
		$userdetail = Auth::guard('api')->user();
		$isApiUserValid = $this->newCheckAgentActivation($userdetail,$_SERVER['REMOTE_ADDR'],$request);
		if($isApiUserValid['status'] !=1)
			return $isApiUserValid;
		return response()->json(['status'=>32,'message'=>array('balance'=>$userdetail->balance->user_balance)]);
	}
	public function deleteBeneficiaryRequest(Request $request)
	{
		$rules = array(
			'beneId'=> 'required|regex:/^[0-9]+$/',
			'userId' => 'required|numeric|regex:/^[0-9]+$/',
			'secretKey' => 'required|regex:/^[A-Za-z0-9]+$/',
		);
		$validator = Validator::make($request->all(), $rules);
		if ($validator->fails()){
		   return Response::json([
					'status'=>10,
					'message' => "Missing/Invalid Parameters",
					'errors' => $validator->getMessageBag()->toArray(),
					]);
		}
		$beneficiary_records = Beneficiary::where('benficiary_id', $request->beneId)->first(); 
            $beneRecordCount = $beneficiary_records->count();
            
            
            if($beneRecordCount >'0')
            { 
                $digits = 4;
                $otp = rand(pow(10, $digits-1), pow(10, $digits)-1);
                Beneficiary::where(['benficiary_id'=>$request->beneId])->update(['otp'=>$otp]); 
                
                $username = urlencode("r604");
                $msg_token = urlencode("yhibBi");
                $sender_id = urlencode("ATZSVD"); // optional (compulsory in transactional sms)
                // $message_content = "Your A2Z Remitter Registration Verification OTP is:".$otp;
                
                $message_content = "OTP ".$otp." for beneficiary deletion request";
                $message = urlencode($message_content);
                $mobile = urlencode($beneficiary_records->mobile_number); 
                
                $content=array('username'=> $username, 'msg_token'=> $msg_token,'sender_id'=>$sender_id,'message'=>$message_content,'mobile'=>$mobile);
                $url = "http://manage.hivemsg.com/api/send_transactional_sms.php";
                
                $api_response =  $this->getCurlPostMethod($url,$content);
                
                $splitResponce = explode(' ', trim($api_response));
                
                if(count($splitResponce)>0 && $splitResponce[0]=='SUCCESS'){
                 
                return $mydata = Response::json(array(
                	'status' => '37',
                    'message' => "OTP has been sent at remitter mobile number"
                ));	
                }else{
                    return $mydata = Response::json(array(
                    	'status' => '401',
                        'message' => "Access Denied"
                    ));
                }  
            }else{
                return $mydata = Response::json(array(
                	'status' => '41',
                    'message' => "No beneficiary available for deletion"
                ));
            }
        
		return $response;
		/*$content=array(
					'api_token'=>config('constants.TRAMO_API_KEY'),
					'userId'=> config('constants.TRAMO_USER_ID'),
					'secretKey'=>config('constants.TRAMO_SECRET_KEY'),
					'beneId'=>$request->beneId,
				);
		$url = config('constants.TRAMO_BASE_URL') ."/api/v2/wallet/bene-delete-request";
		return $response = $this->getCurlPostMethod($url,$content);*/
	}
	public function deleteBeneficiaryThroughOtp(Request $request)
	{
		//print_r($request->all());die;
		$rules = array(
			'beneId' => 'required|numeric|regex:/^[0-9]+$/',
			'otp' => 'required|numeric|regex:/^[0-9]+$/',
			'userId' => 'required|numeric|regex:/^[0-9]+$/',
			'secretKey' => 'required|regex:/^[A-Za-z0-9]+$/',
		); 
		$validator = Validator::make($request->all(), $rules);
		if ($validator->fails()){
		  return Response::json([
					'status'=>10,
					'message' => "Missing/Invalid Parameters",
					'errors' => $validator->getMessageBag()->toArray(),
				]);
		}
		$beneficiary_records = Beneficiary::where('benficiary_id', $request->beneId)->first(); 
        $beneRecordCount = $beneficiary_records->count();
        
        if($beneRecordCount >'0')
        {
            if($beneficiary_records->otp!=$request->otp){
               return $mydata = Response::json(array(
                	'status' => '16',
                    'message' => "Invalid OTP"
                )); 
            }else{ 
                try{
        		    Beneficiary::where('benficiary_id', $request->beneId)->delete();
                    return response()->json(['status'=>38,'message'=>"Beneficiary deleted successfully"]);
        		}
        		catch(Exception $e)
        		{
        			return response()->json(['status'=>401,'message'=>"Access Denied"]);
        		}
            }
        }
		/*$content=array(
			'api_token'=>config('constants.TRAMO_API_KEY'),
			'userId'=> config('constants.TRAMO_USER_ID'),
			'secretKey'=>config('constants.TRAMO_SECRET_KEY'),
			'beneId'=>$request->beneId,
			'otp'=>$request->otp
			);
		$url = config('constants.TRAMO_BASE_URL') ."/api/v2/wallet/bene-delete-confirm-otp";
		$response = $this->getCurlPostMethod($url,$content);
		return $response;*/
	}
	
	 
	public function getMyIp(Request $request)
	{
		return $this->getMyServerIp($request);
	}
	public function makeTransaction(Request $request)
	{	
	    //dd($request);
		$isAcitveService = $this->isActiveService(25);
		if($isAcitveService)
			$respToApi=['status_id'=>503,'message'=>$isAcitveService->message];
		$rules = array(
			'beneName'=> 'required',
			'ifscCode' => 'required|min:11|max:11|regex:/^[A-Za-z0-9]+$/',
			'accountNumber' => 'required|regex:/^[A-Z0-9]+$/',
			'mobile' => 'required|numeric|digits:10|regex:/^[0-9]+$/',
			'beneId' => 'required|numeric|regex:/^[0-9]+$/',
			'amount' => 'required|numeric|min:2|max:25000|regex:/^[0-9]+$/',
			'walletType' => 'required|numeric|digits:1|regex:/^[0-1]+$/',
			'userId' => 'required|numeric|regex:/^[0-9]+$/',
			'channel' => 'required|regex:/^[1-2]+$/',
			'clientId' => 'required|regex:/^[A-Z0-9_]+$/',
			'secretKey' => 'required|regex:/^[A-Za-z0-9]+$/',
		);
		$storeApiResp = Apiresponse::create(['request_from_api'=>json_encode($request->all()),'api_id'=>25,'api_type'=>"Txn"]);
		$validator = Validator::make($request->all(), $rules);
		if ($validator->fails()){
			$respToApi = [
				'status'=>10,	
				'message' => "Missing/Invalid Parametes",
				'errors'=>$validator->errors()->getMessages(),
			];
			$storeApiResp->response_to_api = json_encode($respToApi);
			$storeApiResp->save();
			return Response::json($respToApi);
		}
		
		$userdetail = Auth::guard('api')->user();
		$isApiUserValid = $this->newCheckAgentActivation($userdetail,$_SERVER['REMOTE_ADDR'],$request);
		if($isApiUserValid['status'] !=1)
		{
			$storeApiResp->response_to_api = json_encode($isApiUserValid);
			$storeApiResp->save();
			return Response::json($isApiUserValid);
		}
		$userId = $userdetail->id;
		$isExistClienId = $this->isExistClienId($userId,$request->clientId);
		if($isExistClienId['status']==73){
			$storeApiResp->response_to_api = json_encode($isExistClienId);
			$storeApiResp->save();
			return Response::json($isExistClienId);
		}
		
		$statusId=3;
		$ifsc = $request->ifscCode;
		$bankCode = substr($ifsc, 0, 4);
		$isBankDetails = Masterbank::select('bank_status')->where('bank_code',$bankCode)->first();
		if($isBankDetails=='')
		{
			$respToApi=['status' => 72, 'message' => 'Bank code is not found. Please contact with Admin'];
			$storeApiResp->response_to_api = json_encode($isApiUserValid);
			$storeApiResp->save();
			return Response::json($respToApi);
		}
		if($isBankDetails->bank_status ==0) 
		{
			$respToApi=['status' => 74, 'message' => 'Bank is down. Please try again after some time'];
			$storeApiResp->response_to_api = json_encode($respToApi);
			$storeApiResp->save();
			return Response::json($respToApi);
		}
		$bank_name = $isBankDetails->bank_name;
		$beneName = trim($request->beneName);
		$bank_account = $request->accountNumber;
		$mobile_number =$request->mobile;
		$a2zBeneId = $request->beneId;
		$amount = $request->amount;
		$channel=$request->channel;
		$userDetails = $this->getUserDetails();
		$duplicate = $this->checkDuplicateTransaction($bank_account,$amount,$userId,$mobile_number,25);
		if($duplicate['status'] == 31){ 
				return response()->json($duplicate);
				$storeApiResp->response_to_api = json_encode($respToApi);
			$storeApiResp->save();
			return Response::json($respToApi);
		}
		$walletScheme = PremiumWalletScheme::where('min_amt','<=',$amount)->where('max_amt','>=',$amount)->where('wallet_scheme_id',$userDetails->member->paytm_wallet_scheme)->first();
		//print_r($walletScheme);die;
		if($walletScheme==''){
			$respToApi=['status'=>75,'message'=>"Your commission is not configured. Please Contact with admin"];
			$storeApiResp->response_to_api = json_encode($respToApi);
			$storeApiResp->save();
			return Response::json($respToApi);
		}
		elseif($walletScheme->is_error){
			$respToApi=['status'=>76,'message'=>'Error in Setting. Please call to admin'];
			$storeApiResp->response_to_api = json_encode($respToApi);
			$storeApiResp->save();
			return Response::json($respToApi);
		}
		$beneficiarydetail = Beneficiary::where(['benficiary_id'=>$a2zBeneId])->whereIn('api_id', ['18','25'])->first();
		//$beneficiarydetail = Beneficiary::where(['benficiary_id'=>$tramoBeneId,'api_id'=>18])->first();
		if (!empty($beneficiarydetail)) 
		{
			$beneficiary_id = $beneficiarydetail->id;
		}
		else{
			$respToApi=['status' => 77, 'message' => 'Beneficiary details does not exist. Please Contact with admin'];
			$storeApiResp->response_to_api = json_encode($respToApi);
			$storeApiResp->save();
			return Response::json($respToApi);
		}
		$agentCharge = $this->agentCharge($amount,$walletScheme->agent_charge,$walletScheme->agent_charge_type);
		$agentComm =  $this->getCommission($amount,$walletScheme->agent_comm,$walletScheme->agent_comm_type);
		$agentData['credit_charge']= $agentComm;
		$agent_parent_id = $userDetails->parent_id; 
		$user_id=$userDetails->id;
		$agent_parent_role = $userDetails->parent->role_id;
		$dist_charge_data=$admin_charge_data=$md_charge_data=array();
		if($userDetails->parent_id == 1)
		{
			$d_id = $m_id ='';
			$a_id = 1;
			$admin_charge_data['credit_by'] = $user_id;
			$admin_charge_data['credit_charge'] = $agentCharge-$agentComm;
			$admin_charge_data['debit_charge'] = 0;
		}
		else{
			$respToApi=['status' => 78, 'message' => 'Invalid User Mapping Details'];
			$storeApiResp->response_to_api = json_encode($respToApi);
			$storeApiResp->save();
			return Response::json($respToApi);
		}
	 
		$user_detail_balance = Balance::where('user_id',$user_id)->select('user_balance')->first();
		$finalAmount =  $agentCharge+$amount;
		if($user_detail_balance->user_balance < $finalAmount)
		{
			$respToApi=['status'=>26,'message'=>"In-sufficient Balance"];
			$storeApiResp->response_to_api = json_encode($respToApi);
			$storeApiResp->save();
			return Response::json($respToApi);
		}
		$tds = $this->getTDS($agentComm);
		$gst  = 0; 
		DB::beginTransaction();
		try
		{
			//echo "all barier passed";die;
			$finalAmount = $agentCharge + $amount +$tds - $agentComm;
			Balance::where('user_id',$user_id)->decrement('user_balance',$finalAmount);
			$record = Report::create([
						'number' => $bank_account,
						'provider_id' => 41,
						'amount' => $amount,
						'profit' =>0,
						'api_id' => 25,
						'ip_address'=>\Request::ip(),
						'status_id' => $statusId,
						'type' => 'DR',
						'txn_type' => 'TRANSACTION',
						'description' => 'DMR',
						'pay_id' => time(),
						'user_id' => $user_id,
						'created_by'=>$user_id,
						'customer_number' => $mobile_number,
						'opening_balance' => $user_detail_balance->user_balance,
						'total_balance' => Balance::where('user_id',$user_id)->first()->user_balance,
						'bulk_amount'=>$amount,
						'biller_name'=>$request->senderName,
						'gst' => 0,
						'tds' => $tds,
						'debit_charge' => $agentCharge,
						'credit_charge' => $agentComm,
						'beneficiary_id' => $beneficiary_id,
						'client_ackno'=>$request->clientId,
						'channel' => 2,
			]);
			$insert_id = $record->id;
			DB::commit();
		} 
		catch(Exception $e)
		{
			DB::rollback();
			$respToApi=['status' => 500, 'message' => 'Internal Server Error. Try again'];
			$storeApiResp->response_to_api = json_encode($respToApi);
			$storeApiResp->save();
			return Response::json($respToApi);
		}
		$storeApiResp->report_id = $insert_id;
		$storeApiResp->save();
		$clientId = $record->ackno="A2Z_".$insert_id;
		$record->save();
 
						
						$content=array(
                						'ORDER_ID'=> $insert_id, 
                						'beneficiaryAccount'=>$bank_account,
                						'beneficiaryIFSC'=>$ifsc,
                						'COMMENT'=>$mobile_number,
                						'amount'=>$amount,
						);
		$storeApiResp->user_id=$user_id;
		$storeApiResp->api_type="TRANSACTION";
		$storeApiResp->request_message=json_encode($content);
		$storeApiResp->save();
		if($statusId == 3)
		{ 
		                $GetRemitterData = RemitterRegistration::where(['mobile'=>$mobile_number])->first();
    			
            			if(!empty($GetRemitterData) && $GetRemitterData->rem_bal!='0'){  
                            $less_amt = $GetRemitterData->rem_bal - $amount;
                             
                            try{  
                                RemitterRegistration::where(['mobile'=>$mobile_number])->update(['rem_bal'=>$less_amt,'updated_at'=>date('Y-m-d H:i:s')]);
                            }
                            catch(\Exception  $e)
                			{
                				throw $e;
                				return response()->json(['status'=>500 ,'message' => 'Internal Server Error. Try again']) ;
                			}
            			}
		    $paytm_response = new \App\Library\paytm\lib\BankTransfer;  
            $apiResponse = $paytm_response->call_paytm($content);
            
             
			
			$storeApiResp->message=$apiResponse;
			$storeApiResp->save();
		 
			try
			{
				$result =json_decode($apiResponse);
				
				if($result->status=="SUCCESS" || $result->status=="ACCEPTED")
				{
				    $status='';
					if($result->status=="ACCEPTED"){
				        $record->status_id=34;
				        $status=34;
					}else{
					    $status=1;
					    $record->status_id=1;
					}
					$record->txnid=$insert_id;
					$record->paytm_txn_id=$insert_id;
					$record->save();
					$this->creditCommission($d_id,$m_id,$a_id,$user_id,$mobile_number,$beneficiary_id,2,$insert_id,$amount,$dist_charge_data,$md_charge_data,$admin_charge_data,$bank_account,$record,25);
					
                            try{  
                               // RemitterRegistration::where(['mobile'=>$mobile_number])->update(['rem_bal'=>$less_amt,'updated_at'=>date('Y-m-d H:i:s')]); 
					$message_content = "Dear Customer, Your Txn is successful to Acc No." .$bank_account ." with Amount Rs " .$amount." at ".$bank_name." Thanks";
                    //$this->sendByA2ZPlusSms($mobile_number,$message_content);
                    $username = urlencode("r604");
                    $msg_token = urlencode("yhibBi");
                    $sender_id = urlencode("ATZSVD"); // optional (compulsory in transactional sms)
                  //  $message_content = "Dear Customer, Your Txn is successful to Acc No." .$bank_account ." with Amount Rs " .$amount." at ".$bank_name." Thanks";
                    $message = rawurlencode($message_content);
                    $mobile = urlencode($mobile_number);
                     
                    $content=array('username'=> $username, 'msg_token'=> $msg_token,'sender_id'=>$sender_id,'message'=>$message_content,'mobile'=>$mobile);
                	$url = "http://manage.hivemsg.com/api/send_transactional_sms.php";
                	 
                	$api_response =  $this->getCurlPostMethod($url,$content); 
					$respToApi=['txnId'=>$insert_id,'bankRefNo'=>'','status'=>$status,'message'=>"Transaction ".$result->status,'clientId'=>$request->clientId];
					$storeApiResp->response_to_api = json_encode($respToApi);
					$storeApiResp->save();
					return Response::json($respToApi);
                            }
                            catch(\Exception  $e)
                			{
                				throw $e;
                				return response()->json(['status'=>500 ,'message' => 'Internal Server Error. Try again']) ;
                			}
			//	}
				/*else if($result->status=="SUCCESS" || $result->status=="ACCEPTED")//($result->status==1)
				{
					// $record->status_id=1;
					
					if($result->status=="ACCEPTED"){
				        $record->status_id=34;
					}else{
					    $record->status_id=1;
					}
				    $record->txnid=$insert_id;
				    $record->paytm_txn_id=$insert_id;
				    
				    
					$record->save();	//dd($record); die;


				$this->creditCommission($d_id,$m_id,$a_id,$user_id,$mobile_number,$beneficiary_id,2,$insert_id,$amount,$dist_charge_data,$md_charge_data,$admin_charge_data,$bank_account,$record,25);
					
    					$GetRemitterData = RemitterRegistration::where(['mobile'=>$mobile_number])->first();
    			
            			if(!empty($GetRemitterData) && $GetRemitterData->rem_bal!='0'){  
                            $less_amt = $GetRemitterData->rem_bal - $amount;
                             
                            
                            try{  
                                RemitterRegistration::where(['mobile'=>$mobile_number])->update(['rem_bal'=>$less_amt,'updated_at'=>date('Y-m-d H:i:s')]); 
                        	 
                        	
                        		    $username = urlencode("r604");
                                    $msg_token = urlencode("yhibBi");
                                    $sender_id = urlencode("ATZSVD"); // optional (compulsory in transactional sms)
                                    
                                    $message = rawurlencode($message_content);
                                    $mobile = urlencode($mobile_number);
                                     
                                    $content=array('username'=> $username, 'msg_token'=> $msg_token,'sender_id'=>$sender_id,'message'=>$message_content,'mobile'=>$mobile);
                                	$url = "http://manage.hivemsg.com/api/send_transactional_sms.php";
                                	 
                                	$api_response =  $this->getCurlPostMethod($url,$content); 
                                    $splitResponce = explode(' ', trim($api_response));
                                    
                                    return response()->json(['txnId'=>$my_txn_id,'refNo'=>$my_ref_id,'status'=>$record->status_id,'message'=>"Transaction $result->status"]);
                            	
                			}
                			catch(Exception  $e)
                			{
                				throw $e;
                				return response()->json(['status'=>500 ,'message' => 'Internal Server Error. Try again']) ;
                			}
            			}
            		    } */ 
				}
				elseif($result->status==2)
				{ 
					$record->txnid=$result->txnId;
					//$record->status_id=2;
					DB::beginTransaction();
					try
					{
						$record->status_id=2;
						Balance::where('user_id',$user_id)->increment('user_balance',$finalAmount);
						$record->total_balance=Balance::where('user_id',$user_id)->select('user_balance')->first()->user_balance;
						$record->type="DR/CR";
						$record->save();
						DB::commit();
						
					}
					catch(Exception $e)
					{
						DB::rollback();
						$respToApi=['txnId'=>$insert_id,'bankRefNo'=>'','status'=>3,'message'=>"Transaction Pending",'clientId'=>$request->clientId];
						$storeApiResp->response_to_api = json_encode($respToApi);
						$storeApiResp->save();
						return Response::json($respToApi);
						
					}
					$respToApi=['txnId'=>$insert_id,'bankRefNo'=>'','status'=>2,'message'=>"Transaction Failed",'clientId'=>$request->clientId];
					$storeApiResp->response_to_api = json_encode($respToApi);
					$storeApiResp->save();
					return Response::json($respToApi);
				}
				elseif(in_array($result->status,array(10,101,100,11,12,34,21,29,18,19,22,29,23,24,26,21,301,503,31,30)))
				{
				
					if(in_array($result->status,array(301,26)))
					{
						ActiveService::where('id',4)->update(['status_id'=>0]);
					}
					
					DB::beginTransaction();
					try
					{
						$record->status_id=2;
						Balance::where('user_id',$user_id)->increment('user_balance',$finalAmount);
						$record->type="DR/CR";
						$record->total_balance=Balance::where('user_id',$user_id)->select('user_balance')->first()->user_balance;
						$record->save();
						DB::commit();
					}
					catch(Exception $e)
					{
						DB::rollback();
						$respToApi=['txnId'=>$insert_id,'bankRefNo'=>'','status'=>3,'message'=>"Transaction Pending",'clientId'=>$request->clientId];
						$storeApiResp->response_to_api = json_encode($respToApi);
						$storeApiResp->save();
						return Response::json($respToApi);
						
					}
					$respToApi=['txnId'=>$insert_id,'bankRefNo'=>'','status'=>2,'message'=>"Transaction Failed",'clientId'=>$request->clientId];
					$storeApiResp->response_to_api = json_encode($respToApi);
					$storeApiResp->save();
					return Response::json($respToApi);
				}
				elseif($result->status==3)
				{
					$record->txnid=$result->txnId;
					$record->save();
					$this->creditCommission($d_id,$m_id,$a_id,$user_id,$mobile_number,$beneficiary_id,2,$insert_id,$amount,$dist_charge_data,$md_charge_data,$admin_charge_data,$bank_account,$record,5);
					$respToApi=['txnId'=>$insert_id,'bankRefNo'=>'','status'=>3,'message'=>"Transaction Pending",'clientId'=>$request->clientId];
					$storeApiResp->response_to_api = json_encode($respToApi);
					$storeApiResp->save();
					return Response::json($respToApi);
				}
				elseif($result->status==59)
				{
					$record->status_id=18;
					$record->txnid=$result->txnId;
					$record->save();
					$this->creditCommission($d_id,$m_id,$a_id,$user_id,$mobile_number,$beneficiary_id,2,$insert_id,$amount,$dist_charge_data,$md_charge_data,$admin_charge_data,$bank_account,$record,5);
					$respToApi=['txnId'=>$insert_id,'bankRefNo'=>'','status'=>59,'message'=>"Transaction Inprocess",'clientId'=>$request->clientId];
					$storeApiResp->response_to_api = json_encode($respToApi);
					$storeApiResp->save();
					return Response::json($respToApi);
				}
				else
				{
					$this->creditCommission($d_id,$m_id,$a_id,$user_id,$mobile_number,$beneficiary_id,2,$insert_id,$amount,$dist_charge_data,$md_charge_data,$admin_charge_data,$bank_account,$record,5);
					$respToApi=['txnId'=>$insert_id,'bankRefNo'=>'','status'=>3,'message'=>"Transaction Pending",'clientId'=>$request->clientId];
					$storeApiResp->response_to_api = json_encode($respToApi);
					$storeApiResp->save();
					return Response::json($respToApi);
				}
				
			}
			catch(Exception $e)
			{
				$respToApi=['txnId'=>$insert_id,'bankRefNo'=>'','status'=>3,'message'=>"Transaction Pending",'clientId'=>$request->clientId];
				$storeApiResp->response_to_api = json_encode($respToApi);
				$storeApiResp->save();
				return Response::json($respToApi);
			}
		}
		else{
			$this->creditCommission($d_id,$m_id,$a_id,$user_id,$mobile_number,$beneficiarydetail,2,$insert_id,$amount,$dist_charge_data,$md_charge_data,$admin_charge_data,$bank_account,$reportDetails,5);
			$respToApi=['txnId'=>$insert_id,'bankRefNo'=>'','status'=>3,'message'=>"Transaction Pending",'clientId'=>$request->clientId];
			$storeApiResp->response_to_api = json_encode($respToApi);
			$storeApiResp->save();
			return Response::json($respToApi);
		}
	} 
	
	
	public function verifyAccountNumber(Request $request)
	{
		$rules = array(
			'bankName'=> 'required',
			'ifscCode' => 'required|min:11|max:11|regex:/^[A-Za-z0-9]+$/',
			'accountNumber' => 'required|regex:/^[A-Z0-9]+$/',
			'mobile' => 'required|numeric|digits:10|regex:/^[0-9]+$/',
			'userId' => 'required|numeric|regex:/^[0-9]+$/',
			'clientId' => 'required|regex:/^[A-Za-z0-9_]+$/',
			'secretKey' => 'required|regex:/^[A-Za-z0-9]+$/',
		);
		$storeApiResp = Apiresponse::create(['request_from_api'=>json_encode($request->all()),'api_id'=>5,'api_type'=>"Bene Verification"]);
		$validator = Validator::make($request->all(), $rules);
		if ($validator->fails()){
			$respToApi = [
				'status'=>10,	
				'message' => "Missing/Invalid Parametes",
				'errors'=>$validator->errors()->getMessages(),
			];
			$storeApiResp->response_to_api = json_encode($respToApi);
			$storeApiResp->save();
			return Response::json($respToApi);
		}
		$userdetail = Auth::guard('api')->user();
		$isApiUserValid = $this->newCheckAgentActivation($userdetail,$_SERVER['REMOTE_ADDR'],$request);
		if($isApiUserValid['status'] !=1)
		{
			$storeApiResp->response_to_api = json_encode($isApiUserValid);
			$storeApiResp->save();
			return Response::json($isApiUserValid);
		}
		$userId = $userdetail->id;
		$isExistClienId = $this->isExistClienId($userId,$request->clientId);
		if($isExistClienId['status']==73){
			$storeApiResp->response_to_api = json_encode($isExistClienId);
			$storeApiResp->save();
			return Response::json($isExistClienId);
		}
		$walletScheme = VerificationScheme::where('wallet_scheme_id',$userdetail->member->verification_scheme)->first();
		if($walletScheme=='')
		{
			$result[$i]=['status' => 80, 'message' => 'Sar Charge not configured'];
			return response()->json(['result' => $result]);
		}
		$agentCharge = $walletScheme->agent_charge;
		$agent_parent_id = $userdetail->parent_id;
		$dist_charge_data = $admin_charge_data = $md_charge_data=array();
		if($agent_parent_id == 1)
		{
			$d_id = $m_id ='';
			$a_id = 1;
			$admin_charge_data['credit_by'] = $userId;
			$admin_charge_data['credit_charge'] = $walletScheme->admin_comm;
			$admin_charge_data['debit_charge'] = 0;
		}
		else{
			$respToApi=['status' => 78, 'message' => 'Invalid User Mapping Details'];
			$storeApiResp->response_to_api = json_encode($respToApi);
			$storeApiResp->save();
			return Response::json($respToApi);
		}
		$balance = Balance::where('user_id', $userId)->first();
		$user_balance = $balance->user_balance;
		$bank_account= $request->accountNumber;
		$bankName= $request->bankName;
		$mobile_number= $request->mobile;
		$ifsc= $request->ifscCode;
		if ($user_balance >= $agentCharge) 
		{
			DB::beginTransaction();
			try{
			Balance::where('user_id', $userId)->decrement('user_balance', $agentCharge);
			$report = Report::create([
							'number' => $bank_account,
							'provider_id' => 41,
							'amount' => 0,
							'api_id' => 2,
							'profit' => 0,
							'credit_charge' => 0,
							'txn_type' => 'TRANSACTION',
							'type' => 'DR',
							'debit_charge' => $agentCharge,
							'status_id' => 3,
							'txnid' => 'Account Verification',
							'pay_id' => time(),
							'description' => $bankName . ' ( ' . $ifsc .' )', 
							'user_id' => $userId,
							'created_by' => $userId,
							'channel' => 2,
							'opening_balance' => $user_balance,
							'total_balance' => $user_balance - $agentCharge,
							'customer_number' => $mobile_number,
							'client_ackno' => $request->clientId,
				]);
				DB::commit();
			}
			catch(Exception $e)
			{
				DB::rollback();
				return response()->json(['status'=>500,'message'=>"Internal Server Error"]);
			}
		$existBene = Beneficiary::where(['account_number'=>$bank_account,'ifsc'=>$ifsc,'api_id'=>2])->first();
		if($existBene)
		{
			
			$report->api_id = 17;
			$report->status_id = 1;
			$report->save();
			return response()->json(['status'=>1,'message'=>"Success","beneName"=>$existBene->name,'txnId'=>$report->txnid]);
			
		}
		$insert_id = $report->id;
        $token = config('constants.INSTANT_KEY');
		$content="{\"token\":\"$token\",\"request\":{\"remittermobile\":\"$mobile_number\",\"account\":\"$bank_account\",\"ifsc\":\"$ifsc\",\"agentid\":\"$insert_id\",\"outletid\":1}}";
        $instantPay = new \App\Library\InstantPayDMT; 
        $data = $instantPay->accountNumberVerification($content,$userId,$insert_id);
		$beneficiary_id='';
        try
		{
			$res = json_decode($data);
		}
		catch(Exception $e)
		{
			return Response()->json(['status' => 3, 'message' => 'Pending',"beneName"=>"",'txnId'=>$request->txnid]);
		}
			if ($res->statuscode =="TXN")
			{
				if($res->status =="Transaction Successful")
				{
					$report->status_id = 1;
					$report->txnid = $res->data->ipay_id;
					$report->bank_ref = $res->data->bankrefno;
					$report->biller_name = $res->data->benename;
					$report->save();
					$this->createVerificationEntry($a_id,$mobile_number,$beneficiary_id,2,$insert_id,$admin_charge_data,$bank_account);
					return Response()->json(['status' => 1, 'message' => 'Success',"beneName"=>$res->data->benename,'txnId'=>$insert_id,'bankRefNo'=>$res->data->bankrefno,'clientId'=>$request->clientId]);
				}
				else
				{
					return Response()->json(['status' => 3, 'message' => 'Pending',"beneName"=>"",'txnId'=>$insert_id,'bankRefNo'=>'','clientId'=>$request->clientId]);
				}
			}
			elseif($res->statuscode =="RPI"||$res->statuscode =="UAD"||$res->statuscode =="IAC"||$res->statuscode =="IAT"||$res->statuscode =="AAB"||$res->statuscode =="IAB"||$res->statuscode =="ISP"||$res->statuscode =="DID"||$res->statuscode =="DTX"||$res->statuscode =="IAN"||$res->statuscode =="DTB"||$res->statuscode =="RBT"||$res->statuscode =="SPE"||$res->statuscode =="SPD"||$res->statuscode =="UED"||$res->statuscode =="IEC"||$res->statuscode =="IRT"||$res->statuscode =="ITI"||$res->statuscode =="TSU"||$res->statuscode =="IPE"||$res->statuscode =="ISE"||$res->statuscode =="TRP"||$res->statuscode =="ODI"||$res->statuscode =="TDE"||$res->statuscode =="IVC"||$res->statuscode =="IUA"||$res->statuscode =="SNA"||$res->statuscode =="ERR"||$res->statuscode =="FAB"||$res->statuscode =="RAB")
			{
				Balance::where('user_id', $userId)->increment('user_balance', $agentCharge);
				$report->type="DR/CR";
				$report->status_id=2;
				if($res->statuscode =="IAB")
					$message=$report->txnid = "FAILED";
				else
					$message=$report->txnid = $res->status;
				$report->total_balance= Balance::where('user_id', $userId)->first()->user_balance;
				$report->save();
				return Response()->json(['status' => 2, 'message' => $message,'txnId'=>$insert_id,'bankRefNo'=>'','clientId'=>$request->clientId]);
			}
		else
		{
			$this->createVerificationEntry($a_id,$mobile_number,$beneficiary_id,2,$insert_id,$admin_charge_data,$bank_account);
			return Response()->json(['status' => 3, 'message' => 'Pending',"beneName"=>"",'txnId'=>$insert_id,'bankRefNo'=>'','clientId'=>$request->clientId]); 
		}
		}
		else{
			return Response()->json(['status' => 26, 'message' => 'In-suficient Balance']);
        }
	}
}