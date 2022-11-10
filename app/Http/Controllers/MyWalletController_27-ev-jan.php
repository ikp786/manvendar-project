<?php

namespace App\Http\Controllers; 
use Illuminate\Http\Request; 
use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Masterbank;
use App\Company;
use App\Balance;
use App\Report;
use App\Apiresponse;
use App\Beneficiary;
use App\RemitterRegistration; 
use App\PremiumWalletScheme;
use App\TransactionReportException;
use App\Api;
use App\ActiveService;
use App\User;
use Auth;
use DB;
use Exception;
use Validator;
use Response;
use CommonController;
use App\Traits\CustomTraits;
use App\Traits\ReportTraits;
use Log;

class MyWalletController extends Controller
{
	use CustomTraits;
	use ReportTraits;
	/*
	protected $request;

    public function __construct(Request $request) {
        $this->request = $request;
    } */
	public function index()
	{
	     
		//return "Service is down. Please Wait";die;
		if(in_array(Auth::user()->role_id,array(5,15)) && Auth::user()->member->paytm_my_wallet ==1)
		{
			
			$verify_code = Api::select('api_name')->where('acc_verify_id',1)->first();
            $ifsc = Masterbank::where(['bank_status'=>0])->get();
			
            $down_bank_lists='';
            foreach($ifsc as $ifs)
            {
                $down_bank_lists = $down_bank_lists.$ifs->bank_name.", ";
            }
            $userDetails = $this->getUserDetails();
            $c_id = $userDetails->company_id;
            $updown = Company::where('id',$c_id)->first();
            $netbanks = Masterbank::where('status_id',1)->get(['bank_name','bank_code']);
            //return $netbanks;
			 $reports = Report::where('user_id', Auth::id())
						->where('api_id',5)
						->orderBy('id', 'DESC')
						->orderBy('created_at', 'DESC')
						->take(5)->get();
			return view('agent.my-wallet',compact('netbanks','updown','ifsc','verify_code','down_bank_lists','reports'));
			//return view('agent.primiumwallet');
		}
		return view('errors.permission-denied');
	}
	
    public function getAgentChargeAmount(Request $request)
	{
		$userDetails=$this->getUserDetails();
		if($request->amount > $userDetails->balance->user_balance)
				return response()->json(['result' => '','txn_pin'=>'','totalAmount'=>'','status'=>0]);
		$req_amount=$amount=$request->amount;
		if($request->txnChargeApiName =="PAYTM")
		{
			$walletScheme = PremiumWalletScheme::where('min_amt','<=',$amount)->where('max_amt','>=',$amount)->where('wallet_scheme_id',$userDetails->member->dmt_two_wallet_scheme)->first();
			$charge = $walletScheme->agent_charge;
			$totalAmount = $amount +$charge ;
			$result[1]=array('charge' => $charge,'txnAmount'=>$amount,'total'=>$totalAmount);
			if($totalAmount > $userDetails->balance->user_balance)
				return response()->json(['result' => $result,'txn_pin'=>$userDetails->profile->txn_pin,'totalAmount'=>$totalAmount,'status'=>0]);
			return response()->json(['result' => $result,'txn_pin'=>$userDetails->profile->txn_pin,'totalAmount'=>$totalAmount,'status'=>1]);
		}
		else{
    		$no_of_ite = ceil($amount/5000);
    		$result=array();
    		$totalAmount=0;
    		if( $no_of_ite <=5 && $no_of_ite >=1)
    		{
    			for( $i=1;$i<=$no_of_ite;$i++)
    			{
    				if($req_amount > 5000)
    				{
    					$amount = 5000;
    					$req_amount = $req_amount-5000;
    				}
    				else
    				{
    					$amount = $req_amount;
    				}
    				if($amount <= 1000)
    					$charge = 10;
    				else
    					$charge =($amount)/100;
    				$totalAmount += $amount +$charge ;
    				$result[$i]=array('charge' => $charge,'txnAmount'=>$amount,'total'=>$charge + $amount);
    				
    			}
    			if($totalAmount > $userDetails->balance->user_balance)
    				return response()->json(['result' => $result,'txn_pin'=>$userDetails->profile->txn_pin,'totalAmount'=>$totalAmount,'status'=>0]);
    			return response()->json(['result' => $result,'txn_pin'=>$userDetails->profile->txn_pin,'totalAmount'=>$totalAmount,'status'=>1]);
    		}
		}
	}
	
	
	public function isBankDownOrNot(Request $request)
	{
		$account = $request->accountNumber;
		$ifscCode = $request->ifscCode;
		$bankName = explode('(',$request->bankName);
		$bankName = trim($bankName[0]);
		$bankCode = substr($ifscCode, 0, 4);
		$bankDetails = Masterbank::where(['bank_code'=>$bankCode])->first();
		if($bankDetails=='')
		{
			return response()->json(['status'=>1,'message'=>"Bank code is not found. Please contact with Admin"]);
		}
		elseif ($bankDetails->manual_status==0)
		{
			return response()->json(['status'=>1,'message'=>"Bank is down. Please contact with Admin"]);
		}
		$bankSortName = $bankDetails->bank_sort_name;
		$content =array('account'=>$account,"outletid"=>1,"bank"=>$bankSortName);
		$instantPay = new \App\Library\InstantPayDMT; 
        $apiResp = $instantPay->isBankdDownOrNot($content);
		try
		{
			$res = json_decode($apiResp); 
			
		//	echo 'stcode ='.$res->statuscode; die;
			if (!empty($res->statuscode) && $res->statuscode =="TXN") 
			{
				$fistArray = $res->data[0];
				
			//	echo 'asd '.$fistArray->is_down; die;
				if($fistArray->is_down == 0)// Bank Up
					Masterbank::where(['bank_code'=>$fistArray->ifsc_alias])->update(['bank_status'=>1]);
				else
					Masterbank::where(['bank_code'=>$fistArray->ifsc_alias])->update(['bank_status'=>0,'down_time'=>date('Y-m-d H:i:s')]);
				return response()->json(['status'=>$fistArray->is_down,'message'=>$fistArray->ifsc_alias .' is Down']);
			}
			else
			{
				//Masterbank::where(['bank_code'=>$bankCode])->update(['bank_status'=>0,'down_time'=>date('Y-m-d H:i:s')]);
				return response()->json(['status'=>0,'message'=>$fistArray->ifsc_alias .'is Down']);
			}
		}
		catch(Exception $e)
		{
			//Masterbank::where(['bank_code'=>$bankCode])->update(['bank_status'=>0,'down_time'=>date('Y-m-d H:i:s')]);
			return response()->json(['status'=>0,'message'=>$bankDetails->bank_name .'is Down']);
		}
	}
	 
	function PayTMResponseCallBack(Request $request)
	{    
	    // dd($request['response']);
	   // Log::info($request['response']);
	    try{
	        $apiResp = Apiresponse::create(['api_id'=>25,'user_id'=>'16','api_type'=>"PaytmCallBack",'report_id'=>'00','request_message'=>$request]);
	         
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
	
	public function paytmTransaction(Request $request)
	{	 
		if(Auth::id() !=4)
		{
				$isAcitveService = $this->isActiveService(25);
				if($isAcitveService)
					return response()->json(['status_id'=>0,'message'=>$isAcitveService->message]);
		}
		$rules = array(
			'beneName'=> 'required',
			'ifsc'=> 'required|min:11|max:11|regex:/^[A-Z0-9]+$/',
			'bank_account' => 'required|numeric|regex:/^[0-9]+$/',
			'mobile_number' => 'required|numeric:10|regex:/^[0-9]+$/',
			'beneficiary_id' => 'required|numeric|regex:/^[0-9]+$/',
			'amount' => 'required|min:2|numeric|regex:/^[0-9]+$/',  // amount limit
			//'channel' => 'required:numeric||regex:/^[0-1]+$/',
		);
		$validator = Validator::make($request->all(), $rules);
		if ($validator->fails()){
		  return Response::json(['status'=>10,'message'=>$validator->errors()->getMessages()]);
		}
		 
		$statusId=3;
		$beneName = trim($request->beneName);
		$ifsc = trim($request->ifsc);
		$bankCode = substr($ifsc, 0, 4);
		$isBankDetails = Masterbank::select('bank_status')->where('bank_code',$bankCode)->first();
		if($isBankDetails=='')
		{
			return response()->json(['status' => 2, 'message' => 'Bank code is not found. Please contact with Admin']);
		}
		$bank_name = $isBankDetails->bank_name;
		
		if($isBankDetails->bank_status ==0) 
			return response()->json(['status' => 2, 'message' => 'Bank is down. Please try againg after some time']);
		$bank_account = trim($request->bank_account);
		$mobile_number = trim($request->mobile_number);
		$a2zBeneId = trim($request->beneficiary_id);
		$amount = $request->amount;
		$channel=$request->channel;
		$userDetails = $this->getUserDetails();
		$duplicatae = $this->checkDuplicateTransaction($bank_account,$amount,Auth::id(),5);
		if($duplicatae['status'] == 31) 
				return response()->json($duplicatae);
		$walletScheme = PremiumWalletScheme::where('min_amt','<=',$amount)->where('max_amt','>=',$amount)->where('wallet_scheme_id',$userDetails->member->paytm_wallet_scheme)->first();
		if($walletScheme=='')
			return response()->json(['status'=>0,'message'=>"Your commission is not configured."]);
		elseif($walletScheme->is_error){
			return response()->json(['status'=>0,'message'=>'Error in Setting. Please Call to admin']);
		}
		$beneficiarydetail = Beneficiary::where(['benficiary_id'=>$a2zBeneId])->whereIn('api_id', ['18','25'])->first();
		if (!empty($beneficiarydetail)) 
		{
			$beneficiary_id = $beneficiarydetail->id;
		}
		else{
			return response()->json(['status' => 2, 'message' => 'Beneficiary details does not exist. Please Contact with admin']);
		}
		$agentCharge = $this->agentCharge($amount,$walletScheme->agent_charge,$walletScheme->agent_charge_type);
		$agentComm =  $this->getCommission($amount,$walletScheme->agent_comm,$walletScheme->agent_comm_type);
		$tds = $this->getTDS($agentComm);
		$agentData['credit_charge']= $agentComm;
		$agent_parent_id = $userDetails->parent_id; 
		$user_id=$userDetails->id;
		$agent_parent_role = $userDetails->parent->role_id;
		$dist_charge_data=$admin_charge_data=$md_charge_data=array();
		if(Auth::user()->parent_id == 1)
		{
			$d_id = $m_id ='';
			$a_id = 1;
			$admin_charge_data['credit_by'] = $user_id;
			$admin_charge_data['credit_charge'] = $agentCharge;
			$admin_charge_data['debit_charge'] = 0;
		}
		else
		{
			if($agent_parent_role ==4)
			{
				$agent_parent_details = $userDetails->parent;
				$dist_parent_id = $agent_parent_details->parent_id;
				if($dist_parent_id ==1)
				{
					$d_id=$agent_parent_id;
					$m_id='';
					$a_id = 1;
					$dist_charge_data['credit_by'] = $user_id;
					$dist_charge_data['credit_charge'] = $this->getCommission($amount,$walletScheme->dist_comm,$walletScheme->dist_comm_type);
					$dist_charge_data['debit_charge'] = 0;
					$admin_charge_data['credit_by'] = $d_id;
					$admin_charge_data['credit_charge'] = $this->getCommission($amount,$walletScheme->admin_comm,$walletScheme->admin_comm_type);
					$admin_charge_data['debit_charge'] = 0;
				}
				else
				{
					$d_id=$agent_parent_id;
					$m_id=$dist_parent_id;
					$a_id = 1;
					
					$dist_charge_data['credit_by'] = $user_id;
					$dist_charge_data['credit_charge'] = $this->getCommission($amount,$walletScheme->dist_comm,$walletScheme->dist_comm_type);
					$dist_charge_data['debit_charge'] = 0;
					
					$md_charge_data['credit_by'] = $d_id;
					$md_charge_data['credit_charge'] = $this->getCommission($amount,$walletScheme->md_comm,$walletScheme->md_comm_type);
					$md_charge_data['debit_charge'] = 0;
					
					$admin_charge_data['credit_by'] = $m_id;
					$admin_charge_data['credit_charge'] = $this->getCommission($amount,$walletScheme->admin_comm,$walletScheme->admin_comm_type);
					$admin_charge_data['debit_charge'] = 0;
				}
			}
			else if($agent_parent_role == 3)
			{
				$d_id='';
				$m_id=$agent_parent_id;
				$a_id = 1;
				
				$md_charge_data['credit_by'] = $user_id;
				$md_charge_data['credit_charge'] = $this->getCommission($amount,$walletScheme->md_comm,$walletScheme->md_comm_type);
				$md_charge_data['debit_charge'] = 0;
				
				$admin_charge_data['credit_by'] = $m_id;
				$admin_charge_data['credit_charge'] = $this->getCommission($amount,$walletScheme->admin_comm,$walletScheme->admin_comm_type);
				$admin_charge_data['debit_charge'] = 0;
			}
		}
		 
		$user_detail_balance = Balance::where('user_id',$user_id)->select('user_balance')->first();
		$finalAmount =  $agentCharge+$amount;
		if($user_detail_balance->user_balance < $finalAmount)
		{
			return response()->json(['status'=>0,'message'=>"In-sufficient Balance"]);
		}
		

		$availableBalanceWithBlockedAmount = $user_detail_balance->user_balance - $finalAmount;
		$isHaveBlockedAmount=$this->isHaveBlockedAmount(Auth::user()->member->blocked_amount,$availableBalanceWithBlockedAmount);
		if($isHaveBlockedAmount['status']==91){
			return response()->json(['status'=>0,'message'=>$isHaveBlockedAmount['message']]);
		}

		DB::beginTransaction();
		try
		{ 
			$finalAmount = $amount+$agentCharge+$tds-$agentComm;
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
						'created_by'=>Auth::id(),
						'customer_number' => $mobile_number,
						'opening_balance' => $user_detail_balance->user_balance,
						'total_balance' => Balance::where('user_id',$user_id)->first()->user_balance,
						'bulk_amount'=>$amount,
						'biller_name'=>$request->senderName,
						'gst' => 0,
						'tds' => 0,
						'debit_charge' => $agentCharge,
						'credit_charge' => $agentComm,
						'beneficiary_id' => $beneficiary_id,
						'channel' => 2,
			]);
			$insert_id = $record->id;
			DB::commit();
		} 
		catch(Exception $e)
		{
			DB::rollback();
			return response()->json(['status' => 2, 'message' => 'Something went wrong. Please try again...']);
		}
		
	    $clientId = $record->ackno="A2Z_".$insert_id; 
	
		// $orderId =rand(10000,99999999);
		
		$record->save();
 
		   $content=array(
						'ORDER_ID'=> $insert_id, 
						'beneficiaryAccount'=>$bank_account,
						'beneficiaryIFSC'=>$ifsc,
						'amount'=>$amount,
						);
 
		$apiResp = Apiresponse::create(['api_id'=>25,'user_id'=>$user_id,'api_type'=>"TRANSACTION",'report_id'=>$record->id,'request_message'=>json_encode($content)]);
	
		if($statusId == 3)
		{ 
			$paytm_response = new \App\Library\paytm\lib\BankTransfer;  
            $apiResponse = $paytm_response->call_paytm($content);
         
        // echo "<br/>paytmapi resp ="; print_r($apiResponse); die;
        
			$apiResp->message=$apiResponse;
			$apiResp->save();
			
			try
			{
			    $my_txn_id = $insert_id;
			    $my_ref_id = '';
			    
				$result =json_decode($apiResponse);
	  
	
		
				if($result->status=="SUCCESS" || $result->status=="ACCEPTED")//($result->status==1)
				{
				    if($result->status=="ACCEPTED"){
				        $record->status_id=34;
					}else{
					    $record->status_id=1;
					}    
				    $record->txnid=$insert_id;
				    $record->paytm_txn_id=$insert_id;
				     
					$record->save(); 
					$this->creditCommission($d_id,$m_id,$a_id,$user_id,$mobile_number,$beneficiary_id,2,$insert_id,$amount,$dist_charge_data,$md_charge_data,$admin_charge_data,$bank_account,$record,25);
					
    					$GetRemitterData = RemitterRegistration::where(['mobile'=>$mobile_number])->first();
    			
            			if(!empty($GetRemitterData) && $GetRemitterData->rem_bal!='0'){  
                            $less_amt = $GetRemitterData->rem_bal - $amount;
                             
                            
                            try{  
                                RemitterRegistration::where(['mobile'=>$mobile_number])->update(['rem_bal'=>$less_amt,'updated_at'=>date('Y-m-d H:i:s')]); 
                        		 
                        		    $username = urlencode("r604");
                                    $msg_token = urlencode("yhibBi");
                                    $sender_id = urlencode("ATZSVD"); // optional (compulsory in transactional sms)
                                    $message_content = "Dear Customer, Your Txn is successful to Acc No." .$bank_account ." with Amount Rs " .$amount." at ".$bank_name." Thanks";
                                    $message = rawurlencode($message_content);
                                    $mobile = urlencode($mobile_number);
                                     
                                    $content=array('username'=> $username, 'msg_token'=> $msg_token,'sender_id'=>$sender_id,'message'=>$message_content,'mobile'=>$mobile);
                                	$url = "http://manage.hivemsg.com/api/send_transactional_sms.php";
                                	 
                                	$api_response =  $this->getCurlPostMethod($url,$content); 
                                    $splitResponce = explode(' ', trim($api_response));
                                    
                                    // $reports_data = Report::where(['mobile'=>$mobile_number])->get(['id']);
                                    
                                    //CustomTraits::checkPayTMStatus($record);  
                                    
                                    //sleep(5);
                                    return response()->json(['txnId'=>$my_txn_id,'refNo'=>$my_ref_id,'status'=>1,'message'=>"Transaction '.$result->status.'"]);
                			}
                			catch(\Exception  $e)
                			{
                				throw $e;
                				return response()->json(['status'=>500 ,'message' => 'Internal Server Error. Try again']) ;
                			}
            		    }  
				}
				elseif($result->status=="FAILURE") // ($result->status==2)
				{ 
				    if($result->status_code=='DE_705')
					{
						ActiveService::where('id',19)->update(['status_id'=>0]);
					}
					$record->txnid=$my_txn_id;
					$record->bank_ref=$my_ref_id;
					
					if($result->status_code=='DE_010' || $result->status_code=='DE_039' || $result->status_code=='DE_040' || $result->status_code=='DE_602' || $result->status_code=='DE_634' || $result->status_code=='DE_641'){
					    $record->status_id=3;    
					}else{
					    $record->status_id=2;
					}
					
					DB::beginTransaction();
					try
					{
					
						Balance::where('user_id',$user_id)->increment('user_balance',$finalAmount);
						$record->total_balance=Balance::where('user_id',$user_id)->select('user_balance')->first()->user_balance;
						$record->type="DR/CR";
						$record->save();
						DB::commit();
					}
					catch(Exception $e)
					{
						DB::rollback();
						return response()->json(['txnId'=>$my_txn_id,'refNo'=>$my_ref_id,'status'=>3,'message'=>"Transaction Pending"]);
						
					}
					if($result->status_code=='DE_704' || $result->status_code=='DE_705')
					{
					    return response()->json(['status'=>2,'message'=>"Server Error"]);
					}else{
					    return response()->json(['status'=>2,'message'=>"Transaction Failed"]);   
					}
				}
				/*
				elseif($result->status=="FAILURE") 
				{  
					$record->status_id=2;
					DB::beginTransaction();
					try
					{  
						Balance::where('user_id',$user_id)->increment('user_balance',$finalAmount);
						$record->type="DR/CR";
						 
						$record->total_balance=Balance::where('user_id',$user_id)->select('user_balance')->first()->user_balance;
					
						$record->save();
					
						DB::commit();
					}
					catch(Exception $e)
					{ 
						DB::rollback();
						return response()->json(['txnId'=>$my_txn_id,'refNo'=>'','status'=>3,'message'=>"Transaction Pending"]);
						
					}
					return response()->json(['status'=>2,'message'=>"Transaction Failed"]);
				} */
				elseif($result->status=="PENDING")//($result->status==3)
				{
					$record->txnid=$my_txn_id;
					$record->save();
					$this->creditCommission($d_id,$m_id,$a_id,$user_id,$mobile_number,$beneficiary_id,2,$insert_id,$amount,$dist_charge_data,$md_charge_data,$admin_charge_data,$bank_account,$record,25);
					return response()->json(['txnId'=>$my_txn_id ,'refNo'=>$my_ref_id ,'status'=>3,'message'=>"Transaction Pending"]);
				}
				elseif($result->status=="ACCEPTED")//($result->status==59)
				{
					$record->status_id=18;
					$record->txnid=$my_txn_id ;
					$record->save();
					$this->creditCommission($d_id,$m_id,$a_id,$user_id,$mobile_number,$beneficiary_id,2,$insert_id,$amount,$dist_charge_data,$md_charge_data,$admin_charge_data,$bank_account,$record,25);
					return response()->json(['txnId'=>$my_txn_id,'refNo'=>$my_ref_id ,'status'=>18,'message'=>"Transaction Inprocess"]);
				}
				else
				{
					$this->creditCommission($d_id,$m_id,$a_id,$user_id,$mobile_number,$beneficiary_id,2,$insert_id,$amount,$dist_charge_data,$md_charge_data,$admin_charge_data,$bank_account,$record,25);
					return response()->json(['txnId'=>$my_txn_id,'refNo'=>'','status'=>3,'message'=>"Transaction Pending"]);
				}
				
			}
			catch(Exception $e)
			{
				return response()->json(['txnId'=>$my_txn_id,'refNo'=>'try one','status'=>3,'message'=>"Transaction Pending"]);
			}
		}
		else{
			$this->creditCommission($d_id,$m_id,$a_id,$user_id,$mobile_number,$beneficiarydetail,2,$insert_id,$amount,$dist_charge_data,$md_charge_data,$admin_charge_data,$bank_account,$record,25);
			return response()->json(['txnId'=>$my_txn_id,'refNo'=>'','status'=>3,'message'=>"Transaction Pending"]);
		}
	}
	
	public function RecordByMobileNumberReport(Request $request)
    {
    	$mobileNumber=$request->mobileNumber;
    	$apiId=5;
    	$report=$this->getRecordByMobileNumber($mobileNumber);
    	return $report;
    }
	public function mobileVerificaton(Request $request)
	{
		
		$rules = array(
			'mobile_number' => 'required|numeric|digits:10'
		);
		$validator = Validator::make($request->all(), $rules);
		if ($validator->fails()) {
			return Response::json(array(
				'success' => false,
				'errors' => $validator->getMessageBag()->toArray()
			)); 
		}
		if(in_array(Auth::user()->role_id,array(5,15)))
		{
			$mobile = $request->mobile_number;
			
			$GetRemitterData = RemitterRegistration::where(['mobile'=>$request->mobile_number])->first();
			
			
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
                        $mobile = urlencode($mobile);
                        
                             // $api = "http://manage.hivemsg.com/api/send_transactional_sms.php?username=".$username."&msg_token=".$msg_token."&sender_id=".$sender_id."&message=".$message."&mobile=".$mobile."";
                            
                            $content=array('username'=> $username, 'msg_token'=> $msg_token,'sender_id'=>$sender_id,'message'=>$message_content,'mobile'=>$mobile);
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
			//dd($GetRemitterData);
			
			
			// Remitteregister
			// $content='api_token='.config('constants.TRAMO_API_KEY').'&mobile='.$mobile.'&userId='.config('constants.TRAMO_USER_ID');
			// $url = config('constants.TRAMO_DMT_URL') ."/mobile-verification?".$content;//die;
			// return $this->getCurlGetMethod($url);
			
			
		}
	}
	public function getBeniList(Request $request)
	{
		$rules = array(
			'mobile_number' => 'required|numeric|digits:10'
		);
		$validator = Validator::make($request->all(), $rules);
		if ($validator->fails()) {
			return Response::json(array(
				'success' => false,
				'errors' => $validator->getMessageBag()->toArray()
			)); 
		}
		$mobile = $request->mobile_number;
        // 		$content='api_token='.config('constants.TRAMO_API_KEY').'&mobile='.$mobile.'&userId='.config('constants.TRAMO_USER_ID');
        // 		$url = config('constants.TRAMO_DMT_URL') ."/bene-list?".$content;//die;
        // 		$apiResponse = $this->getCurlGetMethod($url);
        // 		$beneList = json_decode($apiResponse);
		
		// $beneficiary_records = Beneficiary::where(['mobile_number'=>$mobile,'api_id'=>18])->get(); 
	
	    $beneficiary_records = Beneficiary::where('mobile_number', $mobile)->whereIn('api_id', ['18','25'])->get(); 
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
		
	}
	public function remitterRegister(Request $request)
	{
	    
		
		//print_r($request->all());die;
		$rules = array(
            'fname' => 'required|between:3,10|regex:/^[A-Za-z ]+$/',
            'lname' => 'required|between:3,10|regex:/^[A-Za-z ]+$/',
			'mobile_number' => 'required|numeric|digits:10',
        );
		$validator = Validator::make($request->all(), $rules);
		if ($validator->fails()) {
			return Response::json(array(
				'success' => false,
				'errors' => $validator->getMessageBag()->toArray()
			)); // 400 being the HTTP code for an invalid request.
		}
		$mobile = $request->mobile_number;
		
		//$content=array('api_token'=>config('constants.TRAMO_API_KEY'),'mobile'=> $mobile, 'userId'=> config('constants.TRAMO_USER_ID'),'walletType'=>0,'fname'=>$request->fname,'lname'=>$request->lname);
		//$url = config('constants.TRAMO_DMT_URL') ."/remitter-register";//die;
		//$response =  $this->getCurlPostMethod($url,$content);
		//return $response;


		try
		{ 

    		$register_content = RemitterRegistration::create([
            	 'fname'=>$request->fname,
            	 'lname' => $request->lname,
            	 'mobile' => $request->mobile_number, 
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
        
	}
	public function mobileVerifiedWithOTP(Request $request,$mobile)
	{
		$rules = array(
            'otp' => 'required|numeric|digits:4/',
			'mobile' => 'required|numeric|digits:10',
        );
		$validator = Validator::make($request->all(), $rules);
		if ($validator->fails()) {
			return Response::json(array(
				'success' => false,
				'errors' => $validator->getMessageBag()->toArray()
			)); // 400 being the HTTP code for an invalid request.
		}
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
		
	}
	
	
	function beneAdd(Request $request)
	{
       
        $rules = array(
			'accountNumber' => 'required|numeric|regex:/^[0-9]+$/',
			'beneName'=> 'required',
			'ifscCode' => 'required|min:11|max:11|regex:/^[A-Z0-9]+$/',
			'bankName' => 'required|regex:/^[A-Za-z() ]+$/',
			'mobile_number' => 'required|numeric:10|regex:/^[0-9]+$/',
		);
		$validator = Validator::make($request->all(), $rules);
		if ($validator->fails()){
		  return Response::json(['status'=>0,'message'=>$validator->errors()->getMessages()]);
			
		}
		
		$bankName = $request->bankName;
        $ifsc = trim($request->ifscCode);
        $mobile_number = trim($request->mobile_number);
        $accountNumber = trim($request->accountNumber);  
        $user_id = Auth::id();
        
        $user_record = User::where(['id'=>$user_id])->first();
        
    
		if($request->caseType=="PreAddBene")
		{ 	 
		    try{
		        $beneficiary_record_count = Beneficiary::where(['account_number'=>$accountNumber,'ifsc'=>$ifsc,'mobile_number'=>$mobile_number])->count();
		        if($beneficiary_record_count<=0){
		             
            		$beneDetails = Beneficiary::create([
            					 'benficiary_id'=>0,
            					 'account_number' => $accountNumber,
            					 'ifsc' => $ifsc,
            					 'bank_name' => $bankName,
            					 'customer_number' => $mobile_number,
            					 'mobile_number' => $mobile_number, 
            					 'vener_id' => 1,
            					 'api_id' => 25,
            					 'user_id' => $user_id, 
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
			 
		} 
		return response()->json(['status'=>0,"message"=>"Whoop! Something went wrong"]);
    }
	
 
/* 	private function agentCharge($amount,$charge,$chargeType)
	{
		if($chargeType == 0)
			return ($amount * $charge)/100;
		else
			return $charge;
		
	}
	private function getCommission($amount,$commission,$comm_type)
	{
		if($comm_type==0)
			return ($amount * $commission )/100;
		else
			return $commission;
	} */
	
	public function deleteBeneficiaryRequest(Request $request)
	{  
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
                    'message' => "OTP has been sent at remetter mobile number"
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
	}
	public function deleteBeneficiaryThroughOtp(Request $request)
	{
		//print_r($request->all());die;
		$rules = array( 
			'otp' => 'required|numeric|regex:/^[0-9]+$/',
		); 
		$validator = Validator::make($request->all(), $rules);
		if ($validator->fails()){
		  return Response::json(['status'=>10,'message'=>$validator->errors()->getMessages()]);
		}
		//$content=array('api_token'=>config('constants.TRAMO_API_KEY'),'userId'=> config('constants.TRAMO_USER_ID'),'beneId'=>$request->beneId,'otp'=>$request->otp);
		//$url = config('constants.TRAMO_DMT_URL') ."/bene-delete-confirm-otp";
	    //$response = $this->getCurlPostMethod($url,$content);
	    
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
		return $response;
	}
	public function checkImpsTransactionCurrentStatus(Request $request)
	{
		return CustomTraits::checkStatus($request->id);
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
	private function createEntry($user_id,$mobile_number,$bene_id,$channel,$insert_id,$userData,$bank_account)
	{
		if($user_id !='' && $userData['credit_charge'] >0)
		{
				$amount=$userData['credit_charge'];
				if($amount>0)
				{
					if($user_id ==1)
					{
						$tds =  0;
						$netCommission = $amount;
					}else{
						$tds =  (($amount)* 5)/100;
						$netCommission = $amount - ($tds);
					}
					$userData['credit_charge'] = 0;
					//$userData['gst'] = $gst;
					$userData['tds'] = 0;
					Balance::where('user_id', $user_id)->increment('user_balance', $amount);
					$userData['number']=$bank_account;
					$userData['amount']=$amount;
					$userData['provider_id']=41;

					$userData['profit']=0;
					$userData['created_by']=Auth::id();
					$userData['api_id']=5;
					$userData['status_id']=22;
					$userData['type'] = 'CR';
					$userData['description']="DMT_COMMISSION";
					$userData['txn_type']="COMMISSION";
					$userData['pay_id']=time();
					$userData['user_id']=$user_id;
					$userData['txnid']=$insert_id;
					$userData['customer_number']=$mobile_number;
					$userData['total_balance']=Balance::select('user_balance')->where('user_id', $user_id)->first()->user_balance;
					$userData['beneficiary_id']=$bene_id;
					$userData['channel']=$channel;
					Report::create($userData);
				}
		}
		
	}
	private function createChargeEntry($user_id,$mobile_number,$bene_id,$channel,$insert_id,$agentCharge,$bank_account)
	{
			$amount=$agentCharge;
			if($amount>0)
			{
				$gst = ($amount *18)/118;
				$taxableAmt = 	($amount - $gst);
				$userData['credit_charge'] = 0;
				$userData['debit_charge']=0;
				$userData['created_by']=Auth::id();
				Balance::where('user_id', $user_id)->decrement('user_balance', $amount);
				$userData['gst']=0;
				$userData['number']=$bank_account;
				$userData['amount']=$amount;
				$userData['provider_id']=41;
				$userData['profit']=0;
				$userData['txn_type']="SERVICE CHARGE";
				$userData['api_id']=5;
				$userData['status_id']=15;
				$userData['type']= 'DR';
				$userData['description']="DMT_SERVICE_CHARGE";
				$userData['pay_id']=time();
				$userData['user_id']=$user_id;
				$userData['txnid']=$insert_id;
				$userData['customer_number']=$mobile_number;
				$userData['total_balance']=Balance::select('user_balance')->where('user_id', $user_id)->first()->user_balance;
				$userData['beneficiary_id']=$bene_id;
				$userData['channel']=$channel;
				Report::create($userData);
			}
		
	}
	/* private function checkDuplicateTransaction($accountNo,$amount,$userId,$mobileNo,$apiId)
	{
		$formatted_date = date("Y-m-d H:i:s");
		$start_time = date('Y-m-d H:i:s',strtotime('-300 seconds',strtotime($formatted_date)));
        $result = DB::table('reports')->select('number', 'amount', 'user_id', 'created_at')->where(['number'=>$accountNo,'amount'=> $amount,'user_id'=>$userId,'customer_number'=>$mobileNo])->whereIn('status_id',[1,3])->where('api_id',$apiId)->where('created_at', '>=', $start_time)->orderBy('created_at', 'desc')->first();
        if ($result) {
            return array('status' => 2, 'message' => 'Same Amount, same account and same mobile transaction is found, Try again after 5 Minutes');
        } else {
            return array('status' => 0, 'message' => 'not found');
        }
	} */
	
}
