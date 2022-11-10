<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Masterbank;
use App\Yesmasterbank;
use App\Company;
use App\OfflineService;
use App\Companydesign;
use SimpleXMLElement;
use GuzzleHttp\Client;
use SoapClient;
use DOMDocument;
use App\User;
use App\Balance;
use App\Moneycommission;
use App\ImpsWalletScheme;
use App\PremiumWalletScheme;
use App\VerificationScheme;
use App\Report;
use App\Api;
use App\Beneficiary;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Response;
use DB;
use Exception;
use Validator;
use App\Gstcommission;
use App\Apiresponse;
use App\TransactionReportException;
use App\Traits\CustomTraits;
use App\Traits\ReportTraits;

class MoneyController extends Controller
{
	use CustomTraits;
	use ReportTraits;
    public function index()
    {
		if(in_array(Auth::user()->role_id,array(5,15)) && Auth::user()->member->dmt_one == 1)
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
			return view('agent.money',compact('netbanks','updown','ifsc','verify_code','down_bank_lists'));
		}
		return view('errors.permission-denied');
    }
	public function RecordByMobileNumber(Request $request)
    {
    	$mobileNumber=$request->mobileNumber;
    	
    	$report=$this->getRecordByMobileNumber($mobileNumber);
    	return $report;
    }
    public function neft()
    {
		if(Auth::user()->role_id == 5)
		{
			return view('agent.money.neft');
		}
		return view('errors.page-not-found');
    }
     public function kyc()
    {
		if(in_array(Auth::user()->role_id,array(5,15)))
		{
			return view('agent.money.kyc');
		}
		return view('errors.page-not-found');
		
    }
    public function credit_card()
    {
		if(in_array(Auth::user()->role_id,array(5,15)))
		{
			return view('agent.money.creditcard');
		}
		return view('errors.page-not-found');
    }
    public function aeps()
    {
	}
    public function loan()
    {
		if(in_array(Auth::user()->role_id,array(5,15)))
		{
			$loanEmiList = OfflineService::where('service_type',2)->pluck('name','id')->toArray();
			//print_r($loanEmiList);die;
		
			return view('agent.money.loan',compact('loanEmiList'));
		}
		return view('errors.page-not-found');
    }
    public function payment()
    {
		if(in_array(Auth::user()->role_id,array(5,15)))
		{
			$insurancePayments = OfflineService::where('service_type',1)->pluck('name','id')->toArray();
			return view('agent.money.payment',compact('insurancePayments'));
		}
		return view('errors.page-not-found');
    }
	 public function cabel()
    {
		if(in_array(Auth::user()->role_id,array(5,15)))
		{
			$cabels=OfflineService::where('service_type',3)->pluck('name','id')->toArray();
			return view('agent.money.cabel',compact('cabels'));
		}
		return view('errors.page-not-found');
    }
     public function wallet_load()
    {
		if(in_array(Auth::user()->role_id,array(5,15)))
		{
			return view('agent.money.walletload');
		}
		return view('errors.page-not-found');
    }
	public function fetchCustomerDetails(Request $request)
	{
		if(in_array(Auth::user()->role_id,array(5,15)))
		{
			if(Auth::id() !=4)
			{
				$isAcitveService = $this->isActiveService(4);
				if($isAcitveService)
					return response()->json(['status_id'=>201,'message'=>$isAcitveService->message]);
			}
			$mobileNumber = $request->mobile_number;
			//print_r($this->getCyberResponse(5,$mobileNumber,1,'','','','','','','','',''));
			$cyber = new \App\Library\CyberDMT;
			$response = $cyber->verification($mobileNumber);
			$new = json_decode($response);
			if($new->status == 'Transaction Successful')
			{

				foreach ($new->data->beneficiary as $data) {

				if (Beneficiary::where(['benficiary_id'=>$data->id,'api_id' => 0])->exists()) {

				} else {
					Beneficiary::create(['benficiary_id' => $data->id,
					 'account_number' => $data->account,
					 'ifsc' => $data->ifsc,
					 'bank_name' => $data->bank,
					 'customer_number' => $mobileNumber,
					 'mobile_number' => $data->mobile,
					 'vener_id' => 1,
					 'api_id' => 0,
					 'user_id' => Auth::id(),
					 'name' => $data->name
					]);
					}

				}
			}
			elseif($new->status == "OTP sent successfully")
			{
				
			}
			return $response;
		}
	}
	
	function add_sender (Request $request)
	{
			$NUMBER = $request->mobile_number;
			$fName = $request->fname;
			$lName = $request->lname;
			/*foreach($fName as $key=>$value)
			{
				if($key==0)
					$fName = $value;
				else
					$lName = $lName .' '.$value;
			}*/
			
			$Pin = $request->pincode;
			$parameter_cyber = [$NUMBER, $fName,$lName, $Pin];
			$cyber = new \App\Library\CyberDMT;
			$response = $cyber->add_sender($parameter_cyber);
			return $response;
	}
	function verifySenderOtp (Request $request)
	{
		if(Auth::user()->role_id !=5)
			return response()->json(['status_id'=>0,'message'=>"You do not have permission"]);
			$NUMBER = $request->username;
			$remitterOTP = $request->remitterOTP;
			$remitterVerifyId = $request->remitterVerifyId;
			$parameter_cyber = [$NUMBER, $remitterOTP,$remitterVerifyId];
			$cyber = new \App\Library\CyberDMT;
			$response = $cyber->verifySenderOtp($parameter_cyber);
			return $response;
	}
		
		   function add_beneficiary (Request $request)
		   {
			   if(Auth::user()->role_id !=5)
					return response()->json(['status_id'=>0,'message'=>"You do not have permission"]);
			$remId = $request->senderid;
			$fName = $request->name;
			$lName = "";
			$NUMBER = $request->number;
			$benAccount = $request->bank_account;
			$benIFSC = $request->ifsc;
			if ($remId && $fName && $NUMBER && $benAccount && $benIFSC) 
			{
				$parameter_cyber = [$remId, $fName, $lName, $NUMBER, $benAccount, $benIFSC];
				$cyber = new \App\Library\CyberDMT;
				$response = $cyber->add_beneficiary($parameter_cyber);
				return $response;
			}else
			{
				return Response()->json(['status' => 'failure', 'message' => 'All Filed Required']);   
			}
        }
		
		function beneconform_resend_otp (Request $request){
			if(Auth::user()->role_id !=5)
				return response()->json(['status_id'=>0,'message'=>"You do not have permission"]);
       // print_r($request->all()); exit();
        $remId = $request->senderid;
        $NUMBER = $request->mobile_number;
        $fName = $request->name;
        $lName = "";
        $Pin = "201301";
        $benAccount = $request->account_number;
        $benIFSC = $request->ifsc;
        $benId = $request->BeneficiaryCode;
		$beneDetail = Beneficiary::where('benficiary_id', $benId)->first();
        $parameter_cyber = [$remId, $NUMBER, $beneDetail->name, $lName, $Pin, $beneDetail->account_number, $beneDetail->ifsc, $benId];
        $cyber = new \App\Library\CyberDMT;
        $response = $cyber->beneconform_resend_otp($parameter_cyber);
        echo $response;
        }
		
		
		 function bene_confirm (Request $request){
        $remId = $request->senderid;
        $benId = $request->beneficiaryid;
        $otc = $request->otp;
        $parameter_cyber = [$remId, $benId, $otc];
        $cyber = new \App\Library\CyberDMT;
        $response = $cyber->bene_confirm($parameter_cyber);
        return $response;
        }
		
		
		
		 function delete_beneficiary (Request $request){
        $remId = $request->senderid;
        $benId = $request->beneficiary_id;
        $parameter_cyber = [$remId, $benId];
        $cyber = new \App\Library\CyberDMT;
        $response = $cyber->delete_beneficiary($parameter_cyber);
        return $response;
        }

        function bene_confirm_delete (Request $request){
         $remId = $request->senderid;
         $benId = $request->beneficiary_id;
         $otc = $request->otp;
         $parameter_cyber = [$remId, $benId, $otc];
         $cyber = new \App\Library\CyberDMT;
         $response = $cyber->bene_confirm_delete($parameter_cyber);
         return $response;

        }
		
		function get_bank_detail(Request $request){
		    
            $rules = array('mobile_number' => 'required');
            $validator = Validator::make($request->all(), $rules);
            
            if ($validator->fails()) {
                return Response::json($validator->errors()->getMessages(), 400);
            } else {
                $bank_code = $request->bank_code;
                if ($bank_code == '') {
                    $bank_code = $request->bankcode;
                }
    
                $data = Masterbank::selectRaw('ifsc,is_imps_txn_allow,account_digit')->where('bank_name', $bank_code)->first();
                if ($data->ifsc == '') {
                    $status = 0;
                } else {
                    $status = 1;
                }
                $response['ifsc'] = $data->ifsc;
                $response['account_digit'] = $data->account_digit;
                $response['status'] = $status;
                $response['is_imps_txn_allow'] = $data->is_imps_txn_allow;
                $response['message'] = "IMPS Transaciton is not allwed, Please use DMT2 NEFT Mode";
                echo json_encode($response);
            }
    }

	function account_name_info (Request $request)
	{
	    	if(Auth::id() !=4)
			{
			    $service_id = $request->service_id;
				$isAcitveService = $this->isActiveService($service_id);
				if($isAcitveService)
					return response()->json(['status_id'=>201,'message'=>$isAcitveService->message]);
			}
		if(Auth::user()->role_id !=5)
			return response()->json(['statuscode'=>"ERR",'status'=>"You do not have permission"]);
        $bankName = $request->bankcode;
        $ifsc = trim($request->ifsc);
        $mobile_number = $request->mobile_number;
        $bank_account = trim($request->bank_account);
		$userDetails = $this->getUserDetails();
		$user_id = $userDetails->id;
        $balance = Balance::where('user_id', $user_id)->first();
       $walletScheme = VerificationScheme::where('wallet_scheme_id',$userDetails->member->verification_scheme)->first();
		if($walletScheme=='')
		{
			return Response()->json(['statuscode' => "ERR", 'status' => 'Verification Sur charge is not configured']);
		}
		$agentCharge = $walletScheme->agent_charge;
		$agent_parent_id = $userDetails->parent_id;
					//print_r($walletScheme);die;
		$dist_charge_data = $admin_charge_data = $md_charge_data=array();
		$agent_parent_role = $userDetails->parent->role_id;
		if($userDetails->parent_id == 1)
		{
			$d_id = $m_id ='';
			$a_id = 1;
			$admin_charge_data['credit_by'] = $user_id;
			$admin_charge_data['credit_charge'] = $walletScheme->admin_comm;
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
					$dist_charge_data['credit_charge'] = $walletScheme->dist_comm;
					$dist_charge_data['debit_charge'] = 0;
					$admin_charge_data['credit_by'] = $d_id;
					$admin_charge_data['credit_charge'] = $walletScheme->admin_comm;
					$admin_charge_data['debit_charge'] = 0;
				}
				else
				{
					$d_id=$agent_parent_id;
					$m_id=$dist_parent_id;
					$a_id = 1;
					
					$dist_charge_data['credit_by'] = $user_id;
					$dist_charge_data['credit_charge'] =$walletScheme->dist_comm;
					$dist_charge_data['debit_charge'] = 0;
					
					$md_charge_data['credit_by'] = $d_id;
					$md_charge_data['credit_charge'] = $walletScheme->md_comm;
					$md_charge_data['debit_charge'] = 0;
					
					$admin_charge_data['credit_by'] = $m_id;
					$admin_charge_data['credit_charge'] =$walletScheme->admin_comm;
					$admin_charge_data['debit_charge'] = 0;
				}
			}
			else if($agent_parent_role == 3)
			{
				$d_id='';
				$m_id=$agent_parent_id;
				$a_id = 1;
				
				$md_charge_data['credit_by'] = $user_id;
				$md_charge_data['credit_charge'] = $walletScheme->md_comm;
				$md_charge_data['debit_charge'] = 0;
				
				$admin_charge_data['credit_by'] = $m_id;
				$admin_charge_data['credit_charge'] = $walletScheme->admin_comm;
				$admin_charge_data['debit_charge'] = 0;
			}
		}
		
	
		
		$user_balance = $balance->user_balance;
		$balance = Balance::where('user_id', $user_id)->first();
		$user_balance = $balance->user_balance;
        if ($user_balance >= $agentCharge) 
		{	
			DB::beginTransaction();
			try{
			Balance::where('user_id', $user_id)->decrement('user_balance', $agentCharge);
			$report = Report::create([
									'number' => $bank_account,
									'provider_id' => 41,
									'amount' => 0,
									'api_id' => 2,
									'profit' => 0,
									'credit_charge' => 0,
									'txn_type' => 'ACCOUNT_VERIFICATION',
									'type' => 'DR',
									'debit_charge' => 0,
									'status_id' => 3,
									'txnid' => 'Account Verification',
									'tds'=>0,
									'pay_id' => time(),
									'description' => $bankName . ' ( ' . $ifsc .' )', 
									'user_id' => $user_id,
									'created_by' => Auth::id(),
									'channel' => 2,
									'opening_balance' => $user_balance,
									'total_balance' => $user_balance - $agentCharge,
									'customer_number' => $mobile_number,
						]);
						DB::commit();
			}
			catch(Exception $e)
			{
				DB::rollback();
				return Response()->json(['statuscode' => "ERR", 'status' => 'Something went wrong please try again']);
			}
		
		$existBene = Beneficiary::where(['account_number'=>$bank_account,'ifsc'=>$ifsc,'is_bank_verified'=>1])->first();
		
		//dd($existBene);
		$beneficiary_id='';
		$insert_id = $report->id;
		if($existBene)
		{
			$report->api_id = $service_id;
			$report->status_id = 1;
			$report->save();
			$report->biller_name = $existBene->name;
			$apiId=$service_id;
			$this->createVEntry($d_id,$mobile_number,$beneficiary_id,2,$insert_id,$dist_charge_data,$bank_account,$apiId);
			$this->createVEntry($m_id,$mobile_number,$beneficiary_id,2,$insert_id,$md_charge_data,$bank_account,$apiId);
			$this->createVEntry($a_id,$mobile_number,$beneficiary_id,2,$insert_id,$admin_charge_data,$bank_account,$apiId);
		
			Beneficiary::where(['account_number'=>$bank_account,'mobile_number'=>$mobile_number])->update(['is_bank_verified'=>1]);
			 
			return response()->json(['statuscode'=>"TXN","data"=>array('benename'=>$existBene->name),'status'=>"Successful"]);
			
		} 
		
		$apiId=2;		
        $token = config('constants.INSTANT_KEY');
		$content="{\"token\":\"$token\",\"request\":{\"remittermobile\":\"$mobile_number\",\"account\":\"$bank_account\",\"ifsc\":\"$ifsc\",\"agentid\":\"$insert_id\",\"outletid\":1}}";
        $instantPay = new \App\Library\InstantPayDMT; 
        $data = $instantPay->accountNumberVerification($content,Auth::id(),$insert_id);
		
		try
		{
			$res = json_decode($data);
		}
		catch(Exception $e)
		{
			return Response()->json(['statuscode' => "ERR", 'status' => 'Server Not Responding Please Try After Sometime']);
		} 
			if ($res->statuscode =="TXN")
			{ 
				if($res->status =="Transaction Successful" || $res->status =="Successful")
				{ 
					$report->status_id = 1;
					$report->txnid = $res->data->ipay_id;
					$report->bank_ref = $res->data->bankrefno;
					$report->biller_name = $res->data->benename;
					$report->save();
					Beneficiary::where(['account_number'=>$bank_account,'ifsc'=>$ifsc])->update(['name'=>$res->data->benename,'is_bank_verified'=>1]);
					$this->createVEntry($d_id,$mobile_number,$beneficiary_id,2,$insert_id,$dist_charge_data,$bank_account,$apiId);
					$this->createVEntry($m_id,$mobile_number,$beneficiary_id,2,$insert_id,$md_charge_data,$bank_account,$apiId);
					$this->createVEntry($a_id,$mobile_number,$beneficiary_id,2,$insert_id,$admin_charge_data,$bank_account,$apiId);
					return $data;
				}
				else
				{
					return Response()->json(['statuscode' => "ERR", 'status' => 'Server Not Responding Please Try After Sometime']);
				}
			}
			else if ($res->statuscode =="TUP")
			{
				if($res->status =="Transaction Under Process")
				{
					
					$report->txnid = $res->data->ipay_id;
					$report->save();
					$this->createVEntry($d_id,$mobile_number,$beneficiary_id,2,$insert_id,$dist_charge_data,$bank_account,$apiId);
					$this->createVEntry($m_id,$mobile_number,$beneficiary_id,2,$insert_id,$md_charge_data,$bank_account,$apiId);
					$this->createVEntry($a_id,$mobile_number,$beneficiary_id,2,$insert_id,$admin_charge_data,$bank_account,$apiId);
					return $data;
				}
				else
				{
					return Response()->json(['statuscode' => "ERR", 'status' => 'Server Not Responding Please Try After Sometime']);
				}
			}
			elseif($res->statuscode =="RPI"||$res->statuscode =="UAD"||$res->statuscode =="IAC"||$res->statuscode =="IAT"||$res->statuscode =="AAB"||$res->statuscode =="IAB"||$res->statuscode =="ISP"||$res->statuscode =="DID"||$res->statuscode =="DTX"||$res->statuscode =="IAN"||$res->statuscode =="DTB"||$res->statuscode =="RBT"||$res->statuscode =="SPE"||$res->statuscode =="SPD"||$res->statuscode =="UED"||$res->statuscode =="IEC"||$res->statuscode =="IRT"||$res->statuscode =="ITI"||$res->statuscode =="TSU"||$res->statuscode =="IPE"||$res->statuscode =="ISE"||$res->statuscode =="TRP"||$res->statuscode =="ODI"||$res->statuscode =="TDE"||$res->statuscode =="IVC"||$res->statuscode =="IUA"||$res->statuscode =="SNA"||$res->statuscode =="ERR"||$res->statuscode =="FAB"||$res->statuscode =="RAB")
			{
				Balance::where('user_id', $user_id)->increment('user_balance', $agentCharge);
				$report->type="DR/CR";
				$report->status_id=2;
				if($res->statuscode =="IAB")
					$message=$report->txnid = "FAILED";
				else
					$message=$report->txnid = $res->status;
				$report->total_balance= Balance::where('user_id', $user_id)->first()->user_balance;
				$report->save();
				return Response()->json(['statuscode' => "ERR", 'status' => $message]);
			}
		else
		{
			$this->createVEntry($d_id,$mobile_number,$beneficiary_id,2,$insert_id,$dist_charge_data,$bank_account,$apiId);
					$this->createVEntry($m_id,$mobile_number,$beneficiary_id,2,$insert_id,$md_charge_data,$bank_account,$apiId);
					$this->createVEntry($a_id,$mobile_number,$beneficiary_id,2,$insert_id,$admin_charge_data,$bank_account,$apiId);
			return Response()->json(['statuscode' => "ERR", 'status' => 'Verification is pending']); 
		}}
		else{
			return Response()->json(['status' => 'failure', 'message' => 'Your Balance Is Low Please Refill Your Wallet']);
        }
	}	
	function transaction (Request $request){
		
		$result=array();
		if(Auth::user()->role_id !=5)
		{
				$result[1] =  array('status' => 'Failure', 'message' => "You do not have permission");
				return response()->json(['result' => $result]);
		}
			
		$request_ip =  request()->ip(); 
		$mode = "WEB";
		$mobile_number=$NUMBER = $request->mobile_number;
		$routingType = "IMPS";
		$benId = $request->beneficiary_id;
		$amount = $request->amount;
		$bank_account = $request->bank_account;
		$userDetails = $this->getUserDetails();
		$user_id = $userDetails->id;
		$ifsc = $request->ifsc;
		$bankCode = substr($ifsc, 0, 4);
		
		
		if(Auth::id() != 4)
		{
			$isAcitveService = $this->isActiveService(4);
			if($isAcitveService){
				$result[1] =  array('status' => 'Failure', 'message' => $isAcitveService->message);
				return response()->json(['result' => $result]);
			}
		}
		$isBankDetails = Masterbank::select('bank_status')->where('bank_code',$bankCode)->first();
		if($isBankDetails=='')
		{
			$result[1] =  array('status' => 'Failure', 'message' => "Bank code is not found. Please contact with Admin");
			return response()->json(['result' => $result]);
		}
		if($isBankDetails->bank_status ==0) 
		{
			$result[1] =  array('status' => 'Failure', 'message' => "Bank is down. Please try again after some time");
				return response()->json(['result' => $result]);
		}
		$statusId=3;
	/* 	$isAcitveService = $this->isActiveOnline(4);
		if(!$isAcitveService->is_online_service)
			$statusId=24;
		else
			$statusId=3; */
		if ($amount >= 10 && $amount != '' && $NUMBER != '' && $benId != '' && $amount<=25000) 
		{
			$duplicatae = $this->checkDuplicateTransaction($bank_account, $amount,Auth::id(),4);
			if($duplicatae['status'] == 31){
				//return response()->json($duplicatae); 
				$result[1] =  array('status' => 'Failure', 'message' => "Duplicate Txn");
				return response()->json(['result' => $result]);
			}
			
			$balance = Balance::where('user_id', $user_id)->first();
			$bulk_amount = $req_amount = $amount;
			$no_of_ite = ceil($amount/5000);
			$result=array();
			if( $no_of_ite >5 || $no_of_ite <1)
			{
					$result[1]=array('status' => 'Failure', 'message' => "You are doing something wrong..Please confirm entered balance");
					return response()->json(['result' => $result]);
			}
			
			$beneficiarydetail = Beneficiary::where(['benficiary_id'=>$benId,'api_id'=>0])->first();
			if (!empty($beneficiarydetail)) 
			{
				$beneficiarydetail = $beneficiarydetail->id;
			} 
			else {
				$beneficiarydetail = 0;
			}
			if($beneficiarydetail==0)
			{
				$result[1]=['status' => 'Failure', 'message' => 'Beneficiary Does not existn'];
				return response()->json(['result' => $result]);
			}
			$user_balance = $balance->user_balance;
			$apxAmount = $amount +($amount/100);
			if($user_balance < $apxAmount)
			{
				$result[1]=['status' => 'Failure', 'message' => 'Low Balance Please Refill your wallet'];
				return response()->json(['result' => $result]);
			}
			//echo "Hello";die;
			for($i=1;$i<=$no_of_ite;$i++)
			{
				$now = new \DateTime();
				$datetime = $now->getTimestamp();
				$ctime = $now->format('Y-m-d H:i:s');
				if($req_amount > 5000)
				{
					$amount = 5000;
					$req_amount = $req_amount-5000;
				}
				else
				{
					$amount = $req_amount;
				}
				$walletScheme = ImpsWalletScheme::where('min_amt','<=',$amount)->where('max_amt','>=',$amount)->where('wallet_scheme_id',$userDetails->member->imps_wallet_scheme)->first();
				if($walletScheme=='')
				{
					$result[$i]=['status' => 'Failure', 'message' => 'Server is busy Please try again'];
					return response()->json(['result' => $result]);
				}
				elseif($walletScheme->is_error){
					$result[$i]=['status' => 'Failure', 'message' => 'Error in Setting. Please Call to admin'];
					return response()->json(['result' => $result]);
				}
				

				$agent_parent_id = Auth::user()->parent_id;
				$user_id=Auth::id();
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
				$agentCharge = $this->agentCharge($amount,$walletScheme->agent_charge,$walletScheme->agent_charge_type);
				$agentComm =  $this->getCommission($amount,$walletScheme->agent_comm,$walletScheme->agent_comm_type);
				$tds = $this->getTDS($agentComm);
				$agentData['credit_charge']= $agentComm;
				$user_balance = $balance->user_balance;
				$txnDebitAmount = $amount+$agentCharge-$agentComm;
				if ($user_balance >= $txnDebitAmount && $amount >= 10) 
				{
					$txnDebitAmount = $amount+$agentCharge+$tds-$agentComm;
					DB::beginTransaction();
					try
					{
						Balance::where('user_id', $user_id)->decrement('user_balance', $txnDebitAmount);
						$balance = Balance::where('user_id', $user_id)->first();
						$userBalance = $balance->user_balance;
						$reportDetails = Report::create([
								'number' => $bank_account,
								'provider_id' => 41,
								'amount' => $amount,
								'bulk_amount' => $bulk_amount,
								'api_id' => 4,
								'profit' => 0,
								'type' => 'DR',
								'txn_type' => 'TRANSACTION',
								'status_id' => $statusId,
								'pay_id' => $datetime,
								'created_at' => $ctime,
								'user_id' => $user_id,
								'created_by' => Auth::id(),
								'ip_address' => $request_ip,
								'customer_number' => $NUMBER,
								'opening_balance' => $user_balance,
								'total_balance' => $userBalance,
								'biller_name'=>$request->senderName,
								'gst' => 0,
								'tds' => $tds,
								'recharge_type' => 0,
								'credit_charge' => $agentComm,
								'debit_charge' => $agentCharge,
								'beneficiary_id' => $beneficiarydetail,
								'channel' => 2,
						]);
						$insert_id = $reportDetails->id;
						DB::commit();
					}
					catch(Exception $e)
					{
						DB::rollback();
						$cyberResponse=['txnId'=>'','refNo'=>'','status'=>"FAILED",'amount'=>$amount,'txnTIme'=>$ctime,'message'=>"Whoops! Somethig went wrong".$e->getMessage()];
						$result[$i]=$cyberResponse;
						return Response()->json(['result'=>$result]);
					}
					
					
					/* echo "<pre>";
								echo "<br>Agent Id :". $user_id ."<br>";
								print_r($agentData);
								echo "<br>-------------    D   --------------";
								echo "<br>dist_id Id :". $d_id ."<br>";
								print_r($dist_charge_data);
								echo "<br>-------------    M   --------------";
								echo "<br>md_id Id :". $m_id ."<br>";
								print_r($md_charge_data);
								echo "<br>-------------    A   --------------";
								echo "<br>admin_id Id :". $a_id ."<br>";
								print_r($admin_charge_data);

								
								die;    */
					//echo "Hello";die;
					
					/* --------------------------------------------------------- */
					if($statusId == 3)
					{ 
						$cyber = new \App\Library\CyberDMT;
						$apiResp = Apiresponse::create(['api_id'=>4,'api_type'=>"TXN",'report_id'=>$insert_id,'request_message'=>json_encode(['number'=>$NUMBER,'routingType'=>$routingType,'benId'=>$benId,'amount'=>$amount,'insert_id'=>$insert_id])]);
						$response = $cyber->transaction($NUMBER, $routingType, $benId, $amount, $insert_id);
						$apiResp->message = $response; 
						$apiResp->save();
						try
						{
							$res = json_decode($response);
							if (!empty($res->statuscode)) 
							{
								$statuscode =  $res->statuscode;
								if ($statuscode == 'TXN') 
								{ 
									
									$reportDetails->status_id =1;
									$reportDetails->txnid =$res->data->ref_no;
									$reportDetails->bank_ref =$res->data->ref_no;
									$reportDetails->save();
									$this->creditCommission($d_id,$m_id,$a_id,$user_id,$mobile_number,$beneficiarydetail,2,$insert_id,$amount,$dist_charge_data,$md_charge_data,$admin_charge_data,$bank_account,$reportDetails,4);
									$cyberResponse= ['txnId'=>$insert_id,'refNo'=>$res->data->ref_no,'status'=>"SUCCESS",'amount'=>$amount,'txnTIme'=>$ctime];
									$result[$i]=$cyberResponse;
								}
								elseif ($statuscode == 'TUP') 
								{
									$reportDetails->status_id =1;
									$reportDetails->txnid =$res->data->ref_no;
									$reportDetails->bank_ref =$res->data->ref_no;
									$reportDetails->save();
									$this->creditCommission($d_id,$m_id,$a_id,$user_id,$mobile_number,$beneficiarydetail,2,$insert_id,$amount,$dist_charge_data,$md_charge_data,$admin_charge_data,$bank_account,$reportDetails,4);
									$cyberResponse= ['txnId'=>$insert_id,'refNo'=>$res->data->ref_no,'status'=>"SUCCESS",'amount'=>$amount,'txnTIme'=>$ctime];
									$result[$i]=$cyberResponse;
								}
								elseif ($statuscode == 'ERR' || $statuscode == 'IAB' || $statuscode == 'SPD' || $statuscode == 'ISE' || $statuscode == 'IAN')
								{
									Balance::where('user_id', $user_id)->increment('user_balance', $txnDebitAmount);
									$balance = Balance::where('user_id', $user_id)->first();
									$user_balance = $balance->user_balance;
									$reportDetails->status_id =2;
									$reportDetails->type ="DR/CR";
									$reportDetails->total_balance =$user_balance;
									$reportDetails->save();
									$cyberResponse=['txnId'=>$insert_id,'refNo'=>'','status'=>"FAILED",'amount'=>$amount,'txnTIme'=>$ctime,'message'=>"FAILED"];
									$result[$i]=$cyberResponse;
								}
								else
								{
									$this->creditCommission($d_id,$m_id,$a_id,$user_id,$mobile_number,$beneficiarydetail,2,$insert_id,$amount,$dist_charge_data,$md_charge_data,$admin_charge_data,$bank_account,$reportDetails,4);
									$cyberResponse=['txnId'=>$insert_id,'refNo'=>'','status'=>"PENDING",'amount'=>$amount,'txnTIme'=>$ctime];
									$result[$i]=$cyberResponse;
								}
							}
							else
							{
								
								$this->creditCommission($d_id,$m_id,$a_id,$user_id,$mobile_number,$beneficiarydetail,2,$insert_id,$amount,$dist_charge_data,$md_charge_data,$admin_charge_data,$bank_account,$reportDetails,4);
								$cyberResponse=['txnId'=>$insert_id,'refNo'=>'','status'=>"PENDING",'amount'=>$amount,'txnTIme'=>$ctime];
								$result[$i]=$cyberResponse;
							}
						}
						catch(Exception $e)
						{
							$cyberResponse=['txnId'=>$insert_id,'refNo'=>'','status'=>"PENDING",'amount'=>$amount,'txnTIme'=>$ctime];
							$result[$i]=$cyberResponse;
						}
					}
					else{
						$this->creditCommission($d_id,$m_id,$a_id,$user_id,$mobile_number,$beneficiarydetail,2,$insert_id,$amount,$dist_charge_data,$md_charge_data,$admin_charge_data,$bank_account,$reportDetails,4);
						$cyberResponse=['txnId'=>$insert_id,'refNo'=>'','status'=>"Successful Submitted",'amount'=>$amount,'txnTIme'=>$ctime];
						$result[$i]=$cyberResponse;
					}
					
				}
				else{
					$cyberResponse=['txnId'=>'','refNo'=>'','status'=>"LOW BALANCE",'amount'=>$amount,'txnTIme'=>$ctime	];
					$result[$i]=$cyberResponse;
				}
		}
			return Response()->json(['result'=>$result]);
		}
		else
		{
			$cyberResponse=['status' => 'Failure','message' => 'Amount Should be Minimum Rs 10-25000'];
			return Response()->json(['result'=>$result]);
		}
	
	}


	 public function getCyberResponse($type,$number, $amount, $f_name=null, $l_name=null, $rem_id=null, $bene_account=null,$bene_ifsc=null,$beni_id=null,$pin=null,$bank_city=null,$oct=null)
    {
        $cyber = new \App\Library\CyberDMT;
        $data = $cyber->makeDMTTransaction($type,$number, $amount, $f_name, $l_name, $rem_id, $bene_account,$bene_ifsc,$beni_id,$pin,$bank_city,$oct);
        return $data;
    }
	
	/* private function agentCharge($amount,$charge,$chargeType)
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
	/* private function checkDuplicateTransaction($accountNo,$amount,$userId,$mobileNo)
	{
        $startTime = date("Y-m-d H:i:s");
        $formatted_date = date('Y-m-d H:i:s',strtotime('-30 seconds',strtotime($startTime)));
		$result = DB::table('reports')->select('number', 'amount', 'user_id', 'created_at')->where(['number'=>$accountNo,'bulk_amount'=> $amount,'user_id'=>$userId,'customer_number'=>$mobileNo])->whereIn('status_id',[1,3,9])->where('created_at', '>=',$formatted_date)->orderBy('created_at', 'id')->first();
		if ($result) {
            return array('status' => 2, 'message' => 'Same account and same amount. Please Try agian after 30 Seconds.');
        } else {
            return array('status' => 0, 'message' => 'not found');
        }
	} */
	public function getAgentChargeAmount(Request $request)
	{
		$userDetails=$this->getUserDetails();
		if($request->amount > $userDetails->balance->user_balance)
				return response()->json(['result' => '','txn_pin'=>'','totalAmount'=>'','status'=>0]);
		$req_amount=$amount=$request->amount;
		if($request->txnChargeApiName =="TRAMO")
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
	public function getAgentChargeAmountBkp(Request $request)
	{
		if($request->amount <= 1000)
			$charge = 10;
		else
			$charge =($request->amount)/100;
		return response()->json(['charge'=>$charge,'txn_pin'=>Auth::user()->profile->txn_pin]);
	}
	
	 
	
}
