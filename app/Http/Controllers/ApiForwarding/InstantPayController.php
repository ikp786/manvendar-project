<?php

namespace App\Http\Controllers\ApiForwarding;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Masterbank;
use App\Company;
use App\Balance;
use App\Report;
use App\Apiresponse;
use App\Beneficiary;
use App\InstantPayWalletScheme;
use App\ImpsWalletScheme;
use App\TransactionReportException;
use App\Api;
use Auth;
use DB;
use Exception;
use Validator;
use Response;
use App\Traits\CustomTraits;
use App\Traits\ReportTraits;
/*  A2zApi */
class InstantPayController extends Controller
{
   use CustomTraits,ReportTraits;

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
		$mobile = $request->mobile;
		try{
			$token = config('constants.INSTANT_KEY');
			$content ="{\"token\":\"$token\",\"request\":{\"mobile\":\"$mobile\",\"outletid\":1}}";
			$url = config('constants.INSTANT_PAY_URL') ."/ws/dmi/remitter_details";
			return $this->getCurlPostIMethod($url,$content);
		}
		catch(Exception $e)
		{
			return response()->json(['status'=>503,'message'=>'Time Out']);
		}
	}
	
	public function remitterRegister(Request $request)
	{
		$rules = array(
            'fName' => 'required|between:3,10|regex:/^[A-Za-z ]+$/',
            'lName' => 'required|between:3,10|regex:/^[A-Za-z ]+$/',
            'pinCode' => 'required|between:6,6|regex:/^[0-9]+$/',
			'mobile' => 'required|numeric|digits:10',
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
		$token = config('constants.INSTANT_KEY');
		$firstName =$request->fName;
		$lastName =$request->lName;
		$pinCode =$request->pinCode;
		$mobile = $request->mobile;
		$content ="{\"token\":\"$token\",\"request\": {\"mobile\": \"$mobile\",\"name\":\"$firstName\",\"surname\":\"$lastName\",\"pincode\":\"$pinCode\",\"outletid\":1}}";
		$apiResp = Apiresponse::create(['message'=>'','api_id'=>16,'user_id'=>Auth::id(),'api_type'=>"Remitter Addition",'report_id'=>1101,'request_message'=>$content]);
		try
		{
			$url = config('constants.INSTANT_PAY_URL') ."/ws/dmi/remitter";//die;
			$data =  $this->getCurlPostIMethod($url,$content);
			$apiResp->message = $data;
			$apiResp->save();
			return $data;
		}
		catch(Exception $e)
		{
			return response()->json(['status'=>503,'message'=>"Api Response failed. Try again after some time"]);
		}
	}

	public function remitterVerification(Request $request)
	{
		$rules = array(
            'remitterOTP' => 'required|between:6,6',
            'remitterVerifyId' => 'required',
			'mobile' => 'required|numeric|digits:10',
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
		$token = config('constants.INSTANT_KEY');
		$mobileNumber = $request->mobile;
		$remitterOTP = $request->remitterOTP;
		$remitterVerifyId = $request->remitterVerifyId;
		$content ="{\"token\": \"$token\",\"request\": {\"remitterid\": \"$remitterVerifyId\",\"mobile\": \"$mobileNumber\",\"otp\": \"$remitterOTP\",\"outletid\":1}}";
		$apiResp = Apiresponse::create(['message'=>'','api_id'=>16,'user_id'=>Auth::id(),'api_type'=>"Remitter Addition",'report_id'=>1,'request_message'=>$content]);
		try
		{
			$url = config('constants.INSTANT_PAY_URL') ."/ws/dmi/remitter_validate";//die;
			$data =  $this->getCurlPostIMethod($url,$content);
			$apiResp->message = $data;
			$apiResp->save();
			return $data;
		}
		catch(Exception $e)
		{
			return response()->json(['status'=>503,'message'=>"Api Response failed. Try again after some time"]);
		}
	}

	function beneAdd(Request $request)
	{
	    $rules = array(
			'accountNumber' => 'required|numeric|regex:/^[0-9]+$/',
			'senderId' => 'required|numeric|regex:/^[0-9]+$/',
			'beneName'=> 'required|min:3',
			'ifscCode' => 'required',
			'bankName' => 'required',
			'mobile' => 'required|numeric:10|regex:/^[0-9]+$/',
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
		$token = config('constants.INSTANT_KEY');
		$bankName = $request->bankName;
        $ifsc = trim($request->ifscCode);
        $mobile_number = trim($request->mobile);
        $accountNumber = trim($request->accountNumber); 
        $remitterId = trim($request->senderId);  
        $beneName = trim($request->beneName);  
		$user_id = Auth::id();
		
		$content="{\n\t\"token\"\t\t\t: \"$token\",\n\t\"request\"\t\t: \n\t{\n\t\t\"remitterid\"\t: \"$remitterId\",\n\t\t\"name\"\t\t\t: \"$beneName}\",\n\t\t\"mobile\"\t\t: \"$mobile_number\",\n\t\t\"ifsc\"\t\t\t: \"$ifsc\",\n\t\t\"account\"\t\t: \"$accountNumber\"\n\t\t,\"outletid\":1\n\t}\n}";
		$url = config('constants.INSTANT_PAY_URL') ."/ws/dmi/beneficiary_register";
		$response = $this->getCurlPostIMethod($url,$content);
		$apiResponse = Apiresponse::create(['message'=>$response,'api_id'=>16,'user_id'=>Auth::id(),'api_type'=>"ADD BENE",'request_message'=>json_encode($content)]);
		try
		{
			$res = json_decode($response);
			if (!empty($res->statuscode)) 
			{
				if ($res->statuscode =="TXN")  
				{
					$beneName=$request->beneName;
					$beneDetails = Beneficiary::create([
								'benficiary_id'=>0,
								 'account_number' => $accountNumber,
								 'ifsc' => $ifsc,
								 'bank_name' => $bankName,
								 'customer_number' => $mobile_number,
								 'mobile_number' => $mobile_number, 
								 'vener_id' => 9,
								 'api_id' => 16,
								 'user_id' => $user_id, 
								 'name' => $request->beneName,
						 ]);
					$beneDetails->benficiary_id= $res->data->beneficiary->id;
					$beneDetails->status_id= 1;
					$beneDetails->save();
				}
			}
			return $response;
		}
		catch(Exception $e)
		{
			return response()->json(['status'=>503,'message'=>"Api Response failed. try agian after some time"]);
		}
    }

	function beneVerification(Request $request)
	{
        $rules = array(
			'remitterId' => 'required|numeric|regex:/^[0-9]+$/',
			'beneficiaryId' => 'required|numeric|regex:/^[0-9]+$/',
			'otp'=> 'required|regex:/^[0-9]+$/',
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

		$token = config('constants.INSTANT_KEY');
		$beneficiaryId = $request->beneficiaryId;
		$remitterId = $request->remitterId;
        $otp = $request->otp;
		$content ="{\"token\": \"$token\",\"request\": {\"remitterid\": \"$remitterId\",\"beneficiaryid\": \"$beneficiaryId\",\"otp\": \"$otp\",\"outletid\":1}}";
		$url = config('constants.INSTANT_PAY_URL') ."/ws/dmi/beneficiary_register_validate";
		try
		{
			$response = $this->getCurlPostIMethod($url,$content);
			$apiResponse = Apiresponse::create(['message'=>$response,'api_id'=>16,'user_id'=>Auth::id(),'api_type'=>"ADD BENE",'request_message'=>$content]);
			return $response;
		}
		catch(Exception $e)
		{
			return response()->json(['status'=>503,'message'=>"Api Response failed. try agian after some time"]);
		}
    }

	function resendBeneVerificationOtp(Request $request)
	{
        $rules = array(
			'remitterId' => 'required|numeric|regex:/^[0-9]+$/',
			'beneficiaryCode' => 'required',
			'otp'=> 'required|regex:/^[0-9]+$/',
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
		$token = config('constants.INSTANT_KEY');
	    $remitterId = $request->remitterId;
        $benId = $request->beneficiaryCode;
		$beneDetail = Beneficiary::where(['benficiary_id'=> $benId,'api_id'=>16])->first();
        $content ="{\"token\": \"$token\", \"request\": {\"remitterid\": \"$remitterId\",\"beneficiaryid\": \"$benId\",\"outletid\":1}}";
		try{
			$url = config('constants.INSTANT_PAY_URL') ."/ws/dmi/beneficiary_resend_otp";
	        $response = $this->getCurlPostIMethod($url,$content);
	        return $response;
		}
		catch(Exception $e)
		{
			return response()->json(['status'=>503,'message'=>"Api Response failed. try agian after some time"]);
		}
    }

	public function deleteBeneficiary(Request $request)
	{
		$rules = array(
			'remitterId' => 'required|numeric|regex:/^[0-9]+$/',
			'beneficiaryId' => 'required',
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
		$remitterId = $request->remitterId;
        $beneficiaryId = $request->beneficiaryId;
        $token = config('constants.INSTANT_KEY');
        $content ="{\"token\": \"$token\",\"request\": {\"beneficiaryid\": \"$beneficiaryId\",\"remitterid\": \"$remitterId\",\"outletid\":1}}";
		$url = config('constants.INSTANT_PAY_URL') ."/ws/dmi/beneficiary_remove";
		try{
	        $response = $this->getCurlPostIMethod($url,$content);
	        return $response;
		}
		catch(Exception $e)
		{
			return response()->json(['status'=>503,'message'=>"Api Response failed. try agian after some time"]);
		}
	}

	function confirmBeneDelete (Request $request)
	{
		$rules = array(
			'remitterId' => 'required|numeric|regex:/^[0-9]+$/',
			'beneficiaryId' => 'required',
			'otp'=> 'required|regex:/^[0-9]+$/',
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
		$token = config('constants.INSTANT_KEY');
		$remitterId = $request->remitterId;
		$beneficiaryId = $request->beneficiaryId;
		$otp = $request->otp;
		 $content ="{\"token\": \"$token\",\"request\": {\"beneficiaryid\": \"$beneficiaryId\",\"remitterid\": \"$remitterId\",\"otp\": \"$otp\",\"outletid\":1}}";
		$url = config('constants.INSTANT_PAY_URL') ."/ws/dmi/beneficiary_remove_validate";
		try{
			$response = $this->getCurlPostIMethod($url,$content);
			return $response;
		}
		catch(Exception $e)
		{
			return response()->json(['status'=>503,'message'=>"Api Response failed. try agian after some time"]);
		}
	}
	
	function transaction (Request $request)
	{
		$rules = array(
			'ifsc' => 'required|regex:/^[a-zA-Z0-9]+$/',
			'channel' => 'required|regex:/^[1-2]+$/',
			'amount' => 'required|numeric|min:10|max:5000|regex:/^[0-9]+$/',
			'beneficiaryId' => 'required|numeric',
			'accountNumber' => 'required|numeric|regex:/^[0-9]+$/',
			'mobile' => 'required|numeric|digits:10',
			'userId' => 'required|numeric|regex:/^[0-9]+$/',
			'secretKey' => 'required|regex:/^[A-Za-z0-9]+$/',
			'clientId' => 'required|regex:/^[A-Za-z0-9_]+$/',
		);
		$storeApiResp = Apiresponse::create(['request_from_api'=>json_encode($request->all()),'api_id'=>5,'api_type'=>"Txn"]);
		$validator = Validator::make($request->all(), $rules);
		if ($validator->fails()) {
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
		$user_id = $userdetail->id;
		$isApiUserValid = $this->newCheckAgentActivation($userdetail,$_SERVER['REMOTE_ADDR'],$request);
		if($isApiUserValid['status'] !=1)
		{
			$storeApiResp->response_to_api = json_encode($isApiUserValid);
			$storeApiResp->save();
			return Response::json($isApiUserValid);
		}
		$isExistClienId = $this->isExistClienId($user_id,$request->clientId);
		if($isExistClienId['status']==73){
			$storeApiResp->response_to_api = json_encode($isExistClienId);
			$storeApiResp->save();
			return Response::json($isExistClienId);
		}
		
		$mobile_number=$request->mobile;
		$benId = $request->beneficiaryId;
		$amount = $request->amount;
		$bank_account = $request->accountNumber;
		$clientId = $request->clientId;
		$ifsc = $request->ifsc;
		$bankCode = substr($ifsc, 0, 4);
		$result=array();
		
		$isAcitveService = $this->isActiveService(16);
		if($isAcitveService){
			$result= array('status' => 301, 'message' => $isAcitveService->message);
			return response()->json($result);
		}
		
		$isBankDetails = Masterbank::select('bank_status','manual_status')->where('bank_code',$bankCode)->first();
		if($isBankDetails=='')
		{
			$result =  array('status' => 80, 'message' => "Bank is not available, Please contact to admin");
			return response()->json($result);
		}
		if($isBankDetails->bank_status ==0) 
		{
			$result =  array('status' => 81, 'message' => "Bank is down. Please try again after some time");
			return response()->json( $result);
		}
		elseif ($isBankDetails->manual_status==0)
		{
			$result =  array('status' => 81, 'message' => "Bank is down. Please try again after some time");
			return response()->json( $result);
		}
		if($request->channel==2)
			$routingType = "IMPS";
		else
			$routingType = "NEFT";
		$statusId=3;
		if (true) 
		{
			$duplicate = $this->checkDuplicateTransaction($bank_account,$amount,$user_id,$mobile_number,16);
			if($duplicate['status'] == 31){ 
					return response()->json($duplicate);
					$storeApiResp->response_to_api = json_encode($respToApi);
				$storeApiResp->save();
				return Response::json($respToApi);
			}
			$balance = Balance::where('user_id', $user_id)->first();
			$bulk_amount = $req_amount = $amount;
			$beneficiarydetail = Beneficiary::where(['benficiary_id'=>$benId,'api_id'=>16])->first();
			if (!empty($beneficiarydetail)) 
			{
				$beneficiarydetail = $beneficiarydetail->id;
			} 
			else {
				$beneficiarydetail = 0;
			}
			if($beneficiarydetail==0)
			{
				$respToApi=['status' => 77, 'message' => 'Beneficiary details does not exist. Please Contact with admin'];
				$storeApiResp->response_to_api = json_encode($respToApi);
				$storeApiResp->save();
				return Response::json($respToApi);
			}
			$user_balance = $balance->user_balance;
			if($amount<=1000)
				$apxAmount = 10;
			else
				$apxAmount = $amount +($amount/100);
			if($user_balance < $apxAmount)
			{
				$respToApi=['status'=>26,'message'=>"In-sufficient Balance"];
				$storeApiResp->response_to_api = json_encode($respToApi);
				$storeApiResp->save();
				return Response::json($respToApi);
			}
			$walletScheme = ImpsWalletScheme::where('min_amt','<=',$amount)->where('max_amt','>=',$amount)->where('wallet_scheme_id',$userdetail->member->imps_wallet_scheme)->first();
			if($walletScheme=='')
			{
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
			
			
			$agentCharge = $this->agentCharge($amount,$walletScheme->agent_charge,$walletScheme->agent_charge_type);
			$agentComm =  $this->getCommission($amount,$walletScheme->agent_comm,$walletScheme->agent_comm_type);
			$agent_parent_role = $userdetail->parent->role_id;
			$dist_charge_data=$admin_charge_data=$md_charge_data=array();
			if($userdetail->parent_id == 1)
			{
				$d_id=$m_id='';
				$a_id = 1;
				$admin_charge_data['credit_by'] = $user_id;
				$admin_charge_data['credit_charge'] = $this->getCommission($amount,$walletScheme->admin_comm,$walletScheme->admin_comm_type);
				$admin_charge_data['debit_charge'] = 0;
			}
			else
			{
				$respToApi=['status' => 85, 'message' => 'Invalid Role'];
				$storeApiResp->resp_to_api =json_encode($respToApi);
				$storeApiResp->save(); 
				return response()->json($respToApi);
			}
			//$agentData['credit_charge']= $agentComm;
			$user_balance = $balance->user_balance;
			$tds = $this->getTDS($agentComm);
			$txnDebitAmount = $amount+$agentCharge-$agentComm;
			if ($user_balance >= $txnDebitAmount) 
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
							'api_id' => 16,
							'profit' => 0,
							'type' => 'DR',
							'txn_type' => 'TRANSACTION',
							'status_id' => $statusId,
							'pay_id' => time(),
							'user_id' => $user_id,
							'created_by' => $user_id,
							'ip_address' => request()->ip(),
							'customer_number' => $mobile_number,
							'opening_balance' => $user_balance,
							'total_balance' => $userBalance,
							'biller_name'=>'',
							'gst' => 0,
							'tds' => $tds,
							'recharge_type' => 0,
							'credit_charge' => $agentComm,
							'debit_charge' => $agentCharge,
							'beneficiary_id' => $beneficiarydetail,
							'channel' => $request->channel,
							'client_ackno'=>$request->clientId,
					]);
					$insert_id = $reportDetails->id;
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
                if($statusId == 3)
				{
					$storeApiResp->report_id = $insert_id;
					$storeApiResp->save();
					$clientId = $reportDetails->ackno="A2Z_".$insert_id;
					$reportDetails->save();
					$token = config('constants.INSTANT_KEY');
					$content ="{\"token\": \"$token\",\"request\": {\"remittermobile\": \"$mobile_number\",\"beneficiaryid\": \"$benId\",\"agentid\": \"$insert_id\",\"amount\": \"$amount\",\"mode\": \"$routingType\"\t,\"outletid\":1}}";
					$url = config('constants.INSTANT_PAY_URL') ."/ws/dmi/transfer";
					$response = $this->getCurlPostIMethod($url,$content);
					$storeApiResp->message=$response;
					$storeApiResp->save();
					/* $apiResp->message = $response;
					$apiResp->save(); */
					try
					{
						$res = json_decode($response);
						if (!empty($res->statuscode)) 
						{
							$statuscode =  $res->statuscode;
							if ($statuscode == 'TXN') 
							{ 
								$reportDetails->status_id = 1;
								$reportDetails->txnid = $res->data->ipay_id;
								$reportDetails->bank_ref = $res->data->ref_no;
								$reportDetails->save();
								$this->creditCommission($d_id,$m_id,$a_id,$user_id,$mobile_number,$beneficiarydetail,$request->channel,$insert_id,$amount,$dist_charge_data,$md_charge_data,$admin_charge_data,$bank_account,$reportDetails,16);
								$respToApi=['txnId'=>$insert_id,'bankRefNo'=>$res->data->ref_no,'status'=>1,'message'=>"Transaction Success",'clientId'=>$request->clientId];
								$storeApiResp->response_to_api = json_encode($respToApi);
								$storeApiResp->save();
								return Response::json($respToApi);
							}
							elseif ($statuscode == 'TUP') 
							{
								$reportDetails->status_id = 3;
								$reportDetails->txnid = $res->data->ipay_id;
								$reportDetails->save();
								$this->creditCommission($d_id,$m_id,$a_id,$user_id,$mobile_number,$beneficiarydetail,$request->channel,$insert_id,$amount,$dist_charge_data,$md_charge_data,$admin_charge_data,$bank_account,$reportDetails,16);
								$respToApi=['txnId'=>$insert_id,'bankRefNo'=>'','status'=>3,'message'=>"Transaction Pending",'clientId'=>$request->clientId];
								$storeApiResp->response_to_api = json_encode($respToApi);
								$storeApiResp->save();
								return Response::json($respToApi);
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
								$respToApi=['txnId'=>$insert_id,'bankRefNo'=>'','status'=>2,'message'=>"Transaction Failed",'clientId'=>$request->clientId];
								$storeApiResp->response_to_api = json_encode($respToApi);
								$storeApiResp->save();
								return Response::json($respToApi);
							}
							else
							{
								$this->creditCommission($d_id,$m_id,$a_id,$user_id,$mobile_number,$beneficiarydetail,$request->channel,$insert_id,$amount,$dist_charge_data,$md_charge_data,$admin_charge_data,$bank_account,$reportDetails,16);
								$respToApi=['txnId'=>$insert_id,'bankRefNo'=>'','status'=>3,'message'=>"Transaction Pending",'clientId'=>$request->clientId];
								$storeApiResp->response_to_api = json_encode($respToApi);
								$storeApiResp->save();
								return Response::json($respToApi);
							}
						}
						else
						{
							$this->creditCommission($d_id,$m_id,$a_id,$user_id,$mobile_number,$beneficiarydetail,$request->channel,$insert_id,$amount,$dist_charge_data,$md_charge_data,$admin_charge_data,$bank_account,$reportDetails,16);
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
			}
			else
			{
				$cyberResponse=['status'=>26,'amount'=>$amount,'txnTIme'=>$ctime	];
				$result=$cyberResponse;
			}
			//return Response()->json(['result'=>$result]);
		}			
		else
		{
			$cyberResponse=['status' => 'Failure','message' => 'Amount Should be Minimum Rs 100-25000'];
			return Response()->json(['result'=>$result]);
		}
	}
	
	
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
		$response = $this->getCurlPostIMethod($url,$content);
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
		$response = $this->getCurlPostIMethod($url,$content);
		return $response;
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
		$response = $this->getCurlPostIMethod($url,$content);
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
				$userData['tds'] = $tds;
				Balance::where('user_id', $user_id)->increment('user_balance', $netCommission);
				$userData['number']=$bank_account;
				$userData['amount']=$amount;
				$userData['provider_id']=41;

				$userData['profit']=0;
				$userData['api_id']=16;
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
				//$gst = ($amount *18)/118;
				$gst = 0;
				$taxableAmt = 	($amount - $gst); 
				$userData['credit_charge'] = 0;
				$userData['debit_charge']=0;
				Balance::where('user_id', $user_id)->decrement('user_balance', $amount);
				$userData['gst']=$gst;
				$userData['number']=$bank_account;
				$userData['amount']=$taxableAmt;
				$userData['provider_id']=41;
				$userData['profit']=0;
				$userData['txn_type']="SERVICE CHARGE";
				$userData['api_id']=16;
				$userData['status_id']=15;
				$userData['type']= 'DB';
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
	 private function checkDuplicateTransaction($accountNo,$amount,$userId,$mobileNo)
	{
        $startTime = date("Y-m-d H:i:s");
        $formatted_date = date('Y-m-d H:i:s',strtotime('-300 seconds',strtotime($startTime)));
		$result = DB::table('reports')->select('number', 'amount', 'user_id', 'created_at')->where(['number'=>$accountNo,'bulk_amount'=> $amount,'user_id'=>$userId,'customer_number'=>$mobileNo])->whereIn('status_id',[1,3,9])->where('api_id',16)->where('created_at', '>=',$formatted_date)->orderBy('created_at', 'id')->first();
		if ($result) {
            return array('status' => 2, 'message' => 'Same account and same amount. Please Try agian after 6 minute.');
        } else {
            return array('status' => 0, 'message' => 'not found');
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
		$bankSortName = $bankDetails->bank_sort_name;
		$content =array('account'=>$account,"outletid"=>1,"bank"=>$bankSortName);
		$instantPay = new \App\Library\InstantPayDMT; 
        $apiResp = $instantPay->isBankdDownOrNot($content);
		try
		{
			$res = json_decode($apiResp);
			if (!empty($res->statuscode) && $res->statuscode =="TXN") 
			{
				$fistArray = $res->data[0];
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
	public function checkImpsTransactionCurrentStatus(Request $request)
	{
		
		$rules = array(
			'userId' => 'required|numeric|regex:/^[0-9]+$/',
			'clientId' => 'required|regex:/^[A-Za-z0-9_]+$/',
			'secretKey' => 'required|regex:/^[A-Za-z0-9]+$/',
		);
		$storeApiResp = Apiresponse::create(['message'=>'','api_id'=>16,'user_id'=>1,'api_type'=>"TRANSACTION_CHECK",'report_id'=>1,'request_from_api'=>json_encode($request->all())]);
		$validator = Validator::make($request->all(), $rules);
		if ($validator->fails()){
		  return Response::json(['status'=>10,
			'message' => "Missing/Invalid Parametes",
			'errors' => $validator->getMessageBag()->toArray(),
			]);
		}
		$userdetail = Auth::guard('api')->user();
		if($userdetail->id != $request->userId)
			return response()->json(['status'=>10,'message'=>"User Id is invalid"]);
		$isApiUserValid = $this->newCheckAgentActivation($userdetail,$_SERVER['REMOTE_ADDR'],$request);
		if($isApiUserValid['status'] !=1)
			return $isApiUserValid;
		try
		{
			$report=Report::where(['client_ackno'=>$request->clientId,'user_id'=>$request->userId])->firstOrFail();
			$txnId = $report->id;
			
		}
		catch(Exception $e)
		{
			$respToApi = ['status'=>601,'message'=>"No Record Found"];
			$storeApiResp->response_to_api=json_encode($respToApi);
			$storeApiResp->save();
			return Response::json($respToApi);
		}
		if($report->status_id==2)
		{
			$respToApi =['status'=>55,'message'=>"Transaction Failed","bankRefNo"=>"",'txnId'=>$txnId];
			$storeApiResp->response_to_api=json_encode($respToApi);
			$storeApiResp->save();
			return Response::json($respToApi);
		}
		else if($report->status_id==20){
			
			$respToApi =['status'=>56,'message'=>"Refund Available take through otp ","bankRefNo"=>"",'txnId'=>$txnId];
			$storeApiResp->response_to_api=json_encode($respToApi);
			$storeApiResp->save();
			return Response::json($respToApi);
			
		}
		else if($report->status_id==21){
			$respToApi =['status'=>57,'message'=>"Transaction has been refunded successfully","bankRefNo"=>"",'txnId'=>$txnId];
			$storeApiResp->response_to_api=json_encode($respToApi);
			$storeApiResp->save();
			return Response::json($respToApi);
		}
		else if($report->status_id==18)
		{
			$respToApi =['status'=>59,'message'=>"Tranaction is in process","bankRefNo"=>"",'txnId'=>$txnId];
			$storeApiResp->response_to_api=json_encode($respToApi);
			$storeApiResp->save();
			return Response::json($respToApi);
		}
		else if(in_array($report->status_id,array(1,3)) && in_array($report->api_id,array(16)))
		{
			if(in_array($report->status_id,array(1,3)))
			{
				$previousTxnStatus = $report->status_id;
				$report->status_id=33;
				$report->save();
				$recordId = $report->id;
				$dateTime = date('Y-m-d', strtotime($report->created_at));
				$token = config('constants.INSTANT_KEY');
				$content ="{\"token\":\"$token\",\"request\":{\"external_id\":\"$recordId\",\"transaction_date\":\"$dateTime\"}}";
				$url = config('constants.INSTANT_PAY_URL') ."/ws/status/checkbyexternalid";
				$storeApiResp->request_message=$content;
				$storeApiResp->save();
				try
				{
					$apiResponse = $this->getCurlPostIMethod($url,$content);
					$storeApiResp->message = $apiResponse;
					$storeApiResp->save();
					$result =json_decode($apiResponse);
					if($result->statuscode=="TXN" && $result->status=="Call Status" )
					{
						if($result->data->external_id == $recordId)
						{
							if($result->data->transaction_status =="SUCCESS")
							{
								$report->status_id=1;
								$report->bank_ref=$result->data->serviceprovider_id;
								$report->save();
								$respToApi = ['status'=>1,'message'=>"Success","bankRefNo"=>$result->data->serviceprovider_id,'txnId'=>$txnId];
								$storeApiResp->response_to_api=json_encode($respToApi);
								$storeApiResp->save();
								return Response::json($respToApi);
							}
							elseif($result->data->transaction_status =="REFUND")
							{
								/* if(in_array(Report::find($report->id)->status_id,array(1,3)))
								{ */
									DB::beginTransaction();
									try
									{
										$lastInsertId = $this->doFailTranaction($report,"TRANSACTION");
										$this->reverseCommission($report,"DMT",$lastInsertId);
										$respToApi = ['status'=>2,'message'=>"Transaction Failed,Amount Credited to wallet","bankRefNo"=>"",'txnId'=>$txnId];
										$storeApiResp->response_to_api=json_encode($respToApi);
										$storeApiResp->save();
										DB::commit();
										
										return Response::json($respToApi);
									}
									catch(Exception $e)
									{
										DB::rollback();
										$report->status_id=$previousTxnStatus;
										$report->save();
										$respToApi = ['status'=>3,'message'=>"Transaction Pending","bankRefNo"=>"",'txnId'=>$txnId];
										$storeApiResp->response_to_api=json_encode($respToApi);
										$storeApiResp->save();
										return Response::json($respToApi);
									}
								/* } */
							}
							else
							{
								$report->status_id=$previousTxnStatus;
								$report->save();
								$respToApi = ['status'=>503,'message'=>"Api Response Failed","bankRefNo"=>"",'txnId'=>$txnId];
								$storeApiResp->response_to_api=json_encode($respToApi);
								$storeApiResp->save();
								return Response::json($respToApi);
							}
						}
						elseif($result->data->transaction_status =="NOTFOUND")
						{
							/* if(in_array(Report::find($report->id)->status_id,array(1,3)))
							{ */
								DB::beginTransaction();
								try
								{
									$lastInsertId = $this->doFailTranaction($report,"TRANSACTION");
									$this->reverseCommission($report,"DMT",$lastInsertId);
									
									$respToApi = ['status'=>2,'message'=>"Transaction Failed,Amount Credited to wallet","bankRefNo"=>"",'txnId'=>$txnId];
									$storeApiResp->response_to_api=json_encode($respToApi);
									$storeApiResp->save();
									DB::commit();
									return Response::json($respToApi);
									
								}
								catch(Exception $e)
								{
									DB::rollback();
									$report->status_id=$previousTxnStatus;
									$report->save();
									$respToApi = ['status'=>3,'message'=>"Transaction Pending","bankRefNo"=>"",'txnId'=>$txnId];
									$storeApiResp->response_to_api=json_encode($respToApi);
									$storeApiResp->save();
									return Response::json($respToApi);
								}
							/* } */
						}
						else
						{
							$respToApi = ['status'=>3,'message'=>"Transaction Pending","bankRefNo"=>"",'txnId'=>$txnId];
							$report->status_id=$previousTxnStatus;
							$report->save();
							$storeApiResp->response_to_api=json_encode($respToApi);
							$storeApiResp->save();
							return Response::json($respToApi);
						}
					}
					else
					{
						$report->status_id=$previousTxnStatus;
						$report->save();
						$respToApi = ['status'=>3,'message'=>"Transaction Pending","bankRefNo"=>"",'txnId'=>$txnId];
						$storeApiResp->response_to_api=json_encode($respToApi);
						$storeApiResp->save();
						return Response::json($respToApi);
					}
				}
				catch(Exception $e)
				{
					$report->status_id=$previousTxnStatus;
					$report->save();
					$respToApi = ['status'=>3,'message'=>"Transaction Pending","bankRefNo"=>"",'txnId'=>$txnId];
					$storeApiResp->response_to_api=json_encode($respToApi);
					$storeApiResp->save();
					return Response::json($respToApi);
				}
				
			}
		}
		elseif($report->status_id==33)
		{
			$respToApi = ['status'=>333,'message'=>"Transaction Processing,Please wait..","bankRefNo"=>"",'txnId'=>$txnId];
			$storeApiResp->response_to_api=json_encode($respToApi);
			$storeApiResp->save();
			return Response::json($respToApi);
		}
		else
		{
			return response()->json(['status'=>503,'message'=>"Check status not allowed"]);
			$storeApiResp->response_to_api=json_encode($respToApi);
			$storeApiResp->save();
			return Response::json($respToApi);
		}
	}
	
}
