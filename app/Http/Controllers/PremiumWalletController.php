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
use App\ActiveService;
use Auth;
use DB;
use Exception;
use Validator;
use Response;
use App\Traits\CustomTraits;
use App\Traits\ReportTraits;
class PremiumWalletController extends Controller
{
	use CustomTraits;
	use ReportTraits;
	public function index()
	{
		//return "Service is down. Please Wait";die;
		if(in_array(Auth::user()->role_id,array(5,15)) && Auth::user()->member->dmt_two ==1)
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
			return view('agent.premium-wallet',compact('netbanks','updown','ifsc','verify_code','down_bank_lists','reports'));
			//return view('agent.primiumwallet');
		}
		return view('errors.permission-denied');
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
		if(Auth::user()->role_id !=5)
			return response()->json(['status_id'=>0,'message'=>"You do not have permission"]);
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
			$content='api_token='.config('constants.TRAMO_API_KEY').'&mobile='.$mobile.'&userId='.config('constants.TRAMO_USER_ID');
			$url = config('constants.TRAMO_DMT_URL') ."/mobile-verification?".$content;//die;
			return $this->getCurlGetMethod($url);
			
		}
	}
	public function getBeniList(Request $request)
	{
		if(Auth::user()->role_id !=5)
			return response()->json(['status_id'=>0,'message'=>"You do not have permission"]);
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
		$content='api_token='.config('constants.TRAMO_API_KEY').'&mobile='.$mobile.'&userId='.config('constants.TRAMO_USER_ID');
		$url = config('constants.TRAMO_DMT_URL') ."/bene-list?".$content;//die;
		$apiResponse = $this->getCurlGetMethod($url);
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
								 'user_id' => Auth::id(),
								 'name' => $content->name,
								 ]);
						}
						else{
							$beneficiary_id->name = $content->name;
							$beneficiary_id->save();
							
						}
				}
			
			}
		}
		return $apiResponse;
		
	}
	public function remitterRegister(Request $request)
	{
		if(Auth::user()->role_id !=5)
			return response()->json(['status_id'=>0,'message'=>"You do not have permission"]);
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
		$content=array('api_token'=>config('constants.TRAMO_API_KEY'),'mobile'=> $mobile, 'userId'=> config('constants.TRAMO_USER_ID'),'walletType'=>0,'fname'=>$request->fname,'lname'=>$request->lname);
		$url = config('constants.TRAMO_DMT_URL') ."/remitter-register";//die;
		$response =  $this->getCurlPostMethod($url,$content);
		return $response;
		
	}
	public function mobileVerifiedWithOTP(Request $request,$mobile)
	{
		if(Auth::user()->role_id !=5)
			return response()->json(['status_id'=>0,'message'=>"You do not have permission"]);
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
		$content=array('api_token'=>config('constants.TRAMO_API_KEY'),'mobile'=> $mobile, 'userId'=> config('constants.TRAMO_USER_ID'),'otp'=>$otp);
		$url = config('constants.TRAMO_DMT_URL') ."/mobile-verification-with-otp";//die;
		return $this->getCurlPostMethod($url,$content);
		return response()->json(['status'=>0,'message'=>"Invalid OTP"]);
		
	}
	
	
	function beneAdd(Request $request)
	{
       
		if(Auth::user()->role_id !=5)
			return response()->json(['status_id'=>0,'message'=>"You do not have permission"]);
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
		/* if(Beneficiary::where(['ifsc'=>$ifsc,'mobile_number'=>$mobile_number,'account_number'=>$accountNumber,'api_id'=>18,'status_id'=>1])->first()) */
			//return Response()->json(['status' => 0, 'message' => 'Beneficiary Already Exists']);
        $user_id = Auth::id();
		if($request->caseType=="PreAddBene")
		{
			$digits = 4;
			$otp = rand(pow(10, $digits-1), pow(10, $digits)-1);
			
			/* $msg = "OTP $otp for Beneficiary add confirmation";
			$message = urlencode($msg);
			CustomTraits::sendSMS($mobile_number, $message,1);
			return response()->json(['status'=>1,"message"=>"OTP has been sent at Remitter Mobile","beneId"=>$beneDetails]); */
			$content=array('api_token'=>config('constants.TRAMO_API_KEY'),'secretKey'=>config('constants.TRAMO_SECRET_KEY'),'mobile'=> $mobile_number, 'userId'=> config('constants.TRAMO_USER_ID'),'bankName'=>$bankName,'ifscCode'=>$ifsc,'accountNumber'=>$accountNumber,'beneName'=>$request->beneName);
			$url = config('constants.TRAMO_DMT_URL') ."/add-beneficiary";
			$response = $this->getCurlPostMethod($url,$content);
			$apiResponse = Apiresponse::create(['message'=>$response,'api_id'=>18,'user_id'=>$user_id,'api_type'=>"ADD BENE",'request_message'=>json_encode($content)]);
			$res = json_decode($response);
			if (!empty($res->status)) 
			{
				$status = $res->status;
				if ($status ==35)  
				{
					 $beneDetails = Beneficiary::create([
					'benficiary_id'=>0,
					 'account_number' => $accountNumber,
					 'ifsc' => $ifsc,
					 'bank_name' => $bankName,
					 'customer_number' => $mobile_number,
					 'mobile_number' => $mobile_number, 
					 'vener_id' => 1,
					 'api_id' => 18,
					 'user_id' => $user_id, 
					 'otp' => $otp, 
					 'name' => $request->beneName,
			 ]);
					$beneDetails->benficiary_id= $res->beneId;
					$beneDetails->status_id= 1;
					$beneDetails->save();
				}
			}
			return $response;
		}
		/* if($request->caseType=="VerifyOtp")
		{
			$beneDetails = Beneficiary::find($request->beneficiaryId);
			if($beneDetails=='')
				return response()->json(['status'=>0,"message"=>"Beneficiary will not added, Please Try Again"]);
			if($request->beneficiaryOtp != $beneDetails->otp)
				return response()->json(['status'=>0,"message"=>"Invalid OTP"]);
			$content=array('api_token'=>config('constants.TRAMO_API_KEY'),'mobile'=> $mobile_number, 'userId'=> config('constants.TRAMO_USER_ID'),'bankName'=>$bankName,'ifscCode'=>$ifsc,'accountNumber'=>$accountNumber,'beneName'=>$request->beneName);
			$url = config('constants.TRAMO_DMT_URL') ."/add-beneficiary";
			$response = $this->getCurlPostMethod($url,$content);
			$apiResponse = Apiresponse::create(['message'=>$response,'api_id'=>18,'user_id'=>Auth::id(),'api_type'=>"ADD BENE",'request_message'=>json_encode($content)]);
			$res = json_decode($response);
			if (!empty($res->status)) 
			{
				$status = $res->status;
				if ($status ==35) 
				{
					$beneDetails->benficiary_id= $res->beneId;
					$beneDetails->status_id= 1;
					//$beneDetails->name= $res->beneId; 
					$beneDetails->save();
					
					
					
				}
			}
			return $response;
		} */
		return response()->json(['status'=>0,"message"=>"Whoop! Something went wrong"]);
    }
	
	public function transaction(Request $request)
	{	
		if(Auth::user()->role_id !=5)
			return response()->json(['status_id'=>0,'message'=>"You do not have permission"]);
		if(Auth::id() !=4)
		{
				$isAcitveService = $this->isActiveService(5);
				if($isAcitveService)
					return response()->json(['status_id'=>0,'message'=>$isAcitveService->message]);
		}
		$rules = array(
			'beneName'=> 'required',
			'ifsc'=> 'required|min:11|max:11|regex:/^[A-Z0-9]+$/',
			'bank_account' => 'required|numeric|regex:/^[0-9]+$/',
			'mobile_number' => 'required|numeric:10|regex:/^[0-9]+$/',
			'beneficiary_id' => 'required|numeric|regex:/^[0-9]+$/',
			'amount' => 'required|min:10|numeric|regex:/^[0-9]+$/',
			//'channel' => 'required:numeric||regex:/^[0-1]+$/',
		);
		$validator = Validator::make($request->all(), $rules);
		if ($validator->fails()){
		  return Response::json(['status'=>10,'message'=>$validator->errors()->getMessages()]);
		}
		/* $isAcitveService = $this->isActiveOnline(5);
		if(!$isAcitveService->is_online_service)
			$statusId=24;
		else */
		$statusId=3;
		$beneName = trim($request->beneName);
		$ifsc = trim($request->ifsc);
		$bankCode = substr($ifsc, 0, 4);
		$isBankDetails = Masterbank::select('bank_status')->where('bank_code',$bankCode)->first();
		if($isBankDetails=='')
		{
			return response()->json(['status' => 2, 'message' => 'Bank code is not found. Please contact with Admin']);
		}
		if($isBankDetails->bank_status ==0) 
			return response()->json(['status' => 2, 'message' => 'Bank is down. Please try againg after some time']);
		$bank_account = trim($request->bank_account);
		$mobile_number = trim($request->mobile_number);
		$tramoBeneId = trim($request->beneficiary_id);
		$amount = $request->amount;
		$channel=$request->channel;
		$userDetails = $this->getUserDetails();
		$duplicatae = $this->checkDuplicateTransaction($bank_account,$amount,Auth::id(),5);
		if($duplicatae['status'] == 31) 
				return response()->json($duplicatae);
		$walletScheme = PremiumWalletScheme::where('min_amt','<=',$amount)->where('max_amt','>=',$amount)->where('wallet_scheme_id',$userDetails->member->dmt_two_wallet_scheme)->first();
		if($walletScheme=='')
			return response()->json(['status'=>0,'message'=>"Your commission is not configured."]);
		elseif($walletScheme->is_error){
			return response()->json(['status'=>0,'message'=>'Error in Setting. Please Call to admin']);
		}
		$beneficiarydetail = Beneficiary::where(['benficiary_id'=>$tramoBeneId,'api_id'=>18])->first();
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
		/* echo "<br>---------------Dist------------<br>";
		print_r($dist_charge_data);
		echo "<br>---------------MD------------<br>";
		print_r($md_charge_data);
		echo "<br>---------------Admin------------<br>";
		print_r($admin_charge_data);
		die; */
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
			//$finalAmount = $agentCharge + $amount - $agentComm;
			$finalAmount = $amount+$agentCharge+$tds-$agentComm;
			Balance::where('user_id',$user_id)->decrement('user_balance',$finalAmount);
			$record = Report::create([
						'number' => $bank_account,
						'provider_id' => 41,
						'amount' => $amount,
						'profit' =>0,
						'api_id' => 5,
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
		$record->save();
		$content=array('api_token'=>config('constants.TRAMO_API_KEY'),
						'mobile'=> $mobile_number, 
						'userId'=> config('constants.TRAMO_USER_ID'),
						'beneName'=>$beneName,
						'ifscCode'=>$ifsc,
						'accountNumber'=>$bank_account,
						'beneficiaryId'=>$tramoBeneId,
						'amount'=>$amount,
						'walletType'=>0,
						'channel'=>2,
						'clientId'=>$clientId,
						);
		$apiResp = Apiresponse::create(['api_id'=>5,'user_id'=>$user_id,'api_type'=>"TRANSACTION",'report_id'=>$record->id,'request_message'=>json_encode($content)]);
		if($statusId == 3)
		{
			$url = config('constants.TRAMO_DMT_URL') ."/transaction";//die;
			$apiResponse= $this->getCurlPostMethod($url,$content);
			$apiResp->message=$apiResponse;
			$apiResp->save();
			//Apiresponse::create(['message'=>$apiResponse,'api_id'=>4,'user_id'=>Auth::id(),'api_type'=>"TRANSACTION",'report_id'=>$record->id,'request_message'=>json_encode($apiContent)]);
			//Apiresponse::create(['message'=>$apiResponse,'api_id'=>5,'user_id'=>$user_id,'api_type'=>"TRANSACTION",'report_id'=>$record->id]);
			try
			{
				$result =json_decode($apiResponse);
				if($result->status==1)
				{
					$record->status_id=1;
					$record->txnid=$result->txnId;
					$record->save();
					$this->creditCommission($d_id,$m_id,$a_id,$user_id,$mobile_number,$beneficiary_id,2,$insert_id,$amount,$dist_charge_data,$md_charge_data,$admin_charge_data,$bank_account,$record,5);
					
					$msg ="Dear Customer, Your Txn is successful to Acc No." .$bank_account ." with Amount Rs " .$amount;
					$message = urlencode($msg);
					//$this->sendSMS($mobile_number,$message,1);
					return response()->json(['txnId'=>$insert_id,'refNo'=>$result->txnId,'status'=>1,'message'=>"Transaction Success"]);
				}
				elseif($result->status==2)
				{ 
					$record->txnid=$result->txnId;
					$record->bank_ref=$result->refId;
					$record->status_id=2;
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
						return response()->json(['txnId'=>$insert_id,'refNo'=>$result->txnId,'status'=>3,'message'=>"Transaction Pending"]);
						
					}
					return response()->json(['status'=>2,'message'=>"Transaction Failed"]);
				}
				elseif(in_array($result->status,array(10,101,100,11,12,34,21,29,18,19,22,29,23,24,26,21,301,503,31,30)))
				{
				
					if(in_array($result->status,array(301,26)))
					{
						ActiveService::where('id',4)->update(['status_id'=>0]);
					}
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
						return response()->json(['txnId'=>$insert_id,'refNo'=>'','status'=>3,'message'=>"Transaction Pending"]);
						
					}
					return response()->json(['status'=>2,'message'=>"Transaction Failed"]);
				}
				elseif($result->status==3)
				{
					$record->txnid=$result->txnId;
					$record->save();
					$this->creditCommission($d_id,$m_id,$a_id,$user_id,$mobile_number,$beneficiary_id,2,$insert_id,$amount,$dist_charge_data,$md_charge_data,$admin_charge_data,$bank_account,$record,5);
					return response()->json(['txnId'=>$insert_id,'refNo'=>$result->txnId,'status'=>3,'message'=>"Transaction Pending"]);
				}
				elseif($result->status==59)
				{
					$record->status_id=18;
					$record->txnid=$result->txnId;
					$record->save();
					$this->creditCommission($d_id,$m_id,$a_id,$user_id,$mobile_number,$beneficiary_id,2,$insert_id,$amount,$dist_charge_data,$md_charge_data,$admin_charge_data,$bank_account,$record,5);
					return response()->json(['txnId'=>$insert_id,'refNo'=>$result->txnId,'status'=>18,'message'=>"Transaction Inprocess"]);
				}
				else
				{
					$this->creditCommission($d_id,$m_id,$a_id,$user_id,$mobile_number,$beneficiary_id,2,$insert_id,$amount,$dist_charge_data,$md_charge_data,$admin_charge_data,$bank_account,$record,5);
					return response()->json(['txnId'=>$insert_id,'refNo'=>'','status'=>3,'message'=>"Transaction Pending"]);
				}
				
			}
			catch(Exception $e)
			{
				return response()->json(['txnId'=>$insert_id,'refNo'=>'','status'=>3,'message'=>"Transaction Pending"]);
			}
		}
		else{
			$this->creditCommission($d_id,$m_id,$a_id,$user_id,$mobile_number,$beneficiarydetail,2,$insert_id,$amount,$dist_charge_data,$md_charge_data,$admin_charge_data,$bank_account,$record,5);
			return response()->json(['txnId'=>$insert_id,'refNo'=>'','status'=>3,'message'=>"Transaction Pending"]);
		}
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
		$rules = array(
			'beneId'=> 'required|regex:/^[0-9]+$/',
		);
		$validator = Validator::make($request->all(), $rules);
		if ($validator->fails()){
		  return Response::json(['status'=>10,'message'=>$validator->errors()->getMessages()]);
		}
		$content=array('api_token'=>config('constants.TRAMO_API_KEY'), 'userId'=> config('constants.TRAMO_USER_ID'),'beneId'=>$request->beneId);
		$url = config('constants.TRAMO_DMT_URL') ."/bene-delete-request";
		$response = $this->getCurlPostMethod($url,$content);
		return $response;
	}
	public function deleteBeneficiaryThroughOtp(Request $request)
	{
		//print_r($request->all());die;
		$rules = array(
			'beneId' => 'required|numeric|regex:/^[0-9]+$/',
			'otp' => 'required|numeric|regex:/^[0-9]+$/',
		); 
		$validator = Validator::make($request->all(), $rules);
		if ($validator->fails()){
		  return Response::json(['status'=>10,'message'=>$validator->errors()->getMessages()]);
		}
		$content=array('api_token'=>config('constants.TRAMO_API_KEY'),'userId'=> config('constants.TRAMO_USER_ID'),'beneId'=>$request->beneId,'otp'=>$request->otp);
		$url = config('constants.TRAMO_DMT_URL') ."/bene-delete-confirm-otp";
		$response = $this->getCurlPostMethod($url,$content);
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
