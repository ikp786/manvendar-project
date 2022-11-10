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
class aToZWalletController extends Controller
{
	use CustomTraits, ReportTraits;
		
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
		$content=array(
				'api_token'=>config('constants.TRAMO_API_KEY'),
				'secretKey'=>config('constants.TRAMO_SECRET_KEY'),
				'mobile'=> $request->mobile, 
				'userId'=> config('constants.TRAMO_USER_ID'),
				);
		//print_r($content);//die;
		$url = config('constants.TRAMO_BASE_URL') ."/api/v2/wallet/mobile-verification";
		return $this->getCurlPostMethod($url,$content);
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
		$content=array(
				'api_token'=>config('constants.TRAMO_API_KEY'),
				'userId'=> config('constants.TRAMO_USER_ID'),
				'secretKey'=>config('constants.TRAMO_SECRET_KEY'),
				'mobile'=> $mobile, 
				'walletType'=>0,
				'fname'=>$request->fName,
				'lname'=>$request->lName,
				
				);
		$url = config('constants.TRAMO_BASE_URL') ."/api/v2/wallet/remitter-register";//die;
		return $response =  $this->getCurlPostMethod($url,$content);
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
		$content=array(
				'api_token'=>config('constants.TRAMO_API_KEY'),
				'userId'=> config('constants.TRAMO_USER_ID'),
				'secretKey'=>config('constants.TRAMO_SECRET_KEY'),
				'mobile'=> $mobile, 
				'otp'=>$otp
			);
		$url = config('constants.TRAMO_BASE_URL') ."/api/v2/wallet/mobile-verification-with-otp";//die;
		return $this->getCurlPostMethod($url,$content);
	}
	public function addBeneficiary(Request $request)
	{
		$rules = array(
			'accountNumber' => 'required|regex:/^[0-9A-Za-z]+$/',
			'beneName'=> 'required|min:3|regex:/^[A-Za-z \/]+$/',
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
		$content=array(
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
		return $response = $this->getCurlPostMethod($url,$content);
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
		$content=array(
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
		return $apiResponse;
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
		$content=array(
					'api_token'=>config('constants.TRAMO_API_KEY'),
					'userId'=> config('constants.TRAMO_USER_ID'),
					'secretKey'=>config('constants.TRAMO_SECRET_KEY'),
					'beneId'=>$request->beneId,
				);
		$url = config('constants.TRAMO_BASE_URL') ."/api/v2/wallet/bene-delete-request";
		return $response = $this->getCurlPostMethod($url,$content);
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
		$content=array(
			'api_token'=>config('constants.TRAMO_API_KEY'),
			'userId'=> config('constants.TRAMO_USER_ID'),
			'secretKey'=>config('constants.TRAMO_SECRET_KEY'),
			'beneId'=>$request->beneId,
			'otp'=>$request->otp
			);
		$url = config('constants.TRAMO_BASE_URL') ."/api/v2/wallet/bene-delete-confirm-otp";
		$response = $this->getCurlPostMethod($url,$content);
		return $response;
	}
	public function AcheckImpsTransactionCurrentStatus(Request $request)
	{
	    echo "zcdadsd";
	    	try
		{
			$report=Report::where(['client_ackno'=>$request->clientId,'user_id'=>$request->userId])->firstOrFail();
			//$storeApiResp->report_id=$report->id;
			//$storeApiResp->save();
		}
		catch(Exception $e)
		{
			$respToApi =['status'=>404,'message'=>"No Record Found",'txnId'=>'','bankRefNo'=>'','clientId'=>$request->clientId,'description'=>''];
		}
	    $apiResp=$this->ApicheckPaytmStatus($report);
			json_decode($apiResp, true);
			$json_errors = array(
    JSON_ERROR_NONE => 'No error has occurred',
    JSON_ERROR_DEPTH => 'The maximum stack depth has been exceeded',
    JSON_ERROR_CTRL_CHAR => 'Control character error, possibly incorrectly encoded',
    JSON_ERROR_SYNTAX => 'Syntax error',
);
			 echo 'Last error : ', $json_errors[json_last_error()], PHP_EOL, PHP_EOL;
	     var_dump($apiResp);
	}
	public function checkImpsTransactionCurrentStatus(Request $request)
	{
		$rules = array(
			'userId' => 'required|numeric|regex:/^[0-9]+$/',
			'clientId' => 'required|regex:/^[0-9A-Z_]+$/',
			'secretKey' => 'required|regex:/^[A-Za-z0-9]+$/',
		);
		$storeApiResp = Apiresponse::create(['request_from_api'=>json_encode($request->all()),'api_id'=>5,'api_type'=>"Api User Transaction Check Status"]);
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
		else if(in_array($report->status_id,array(1,3)) && in_array($report->api_id,array(5)))
		{
		   
			$apiResp  = $this->checkDMTTwoStatus($report,$storeApiResp);
			//$report = Report::find($report->id);
			$storeApiResp = json_decode($apiResp);
		try{
			if($storeApiResp->status==1) 
			{
				$respToApi =['status'=>43,'message'=>"SUCCESS",'description'=>"Transaction Successful","bankRefNo"=>$report->bank_ref,'clientId'=>$request->clientId,'txnId'=>$report->txnid];
				$storeApiResp->response_to_api = json_encode($respToApi);
				$storeApiResp->save();
				return Response::json($respToApi);
			}
			elseif($storeApiResp->status==2)
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
			elseif($storeApiResp->status==54)
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
		    return response()->json(['status'=>43,'msg'=>"Success",'bankRefNo'=>'']);
		}
		}
	}
	public function getMyIp(Request $request)
	{
		return $this->getMyServerIp($request);
	}
	public function makeTransaction(Request $request)
	{	
		$isAcitveService = $this->isActiveService(5);
		if($isAcitveService)
			$respToApi=['status_id'=>503,'message'=>$isAcitveService->message];
		$rules = array(
			'beneName'=> 'required',
			'ifscCode' => 'required|min:11|max:11|regex:/^[A-Za-z0-9]+$/',
			'accountNumber' => 'required|regex:/^[A-Z0-9]+$/',
			'mobile' => 'required|numeric|digits:10|regex:/^[0-9]+$/',
			'beneId' => 'required|numeric|regex:/^[0-9]+$/',
			'amount' => 'required|numeric|min:10|max:25000|regex:/^[0-9]+$/',
			'walletType' => 'required|numeric|digits:1|regex:/^[0-1]+$/',
			'userId' => 'required|numeric|regex:/^[0-9]+$/',
			'channel' => 'required|regex:/^[1-2]+$/',
			'clientId' => 'required|regex:/^[A-Z0-9_]+$/',
			'secretKey' => 'required|regex:/^[A-Za-z0-9]+$/',
		);
		$storeApiResp = Apiresponse::create(['request_from_api'=>json_encode($request->all()),'api_id'=>5,'api_type'=>"Txn"]);
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
		$beneName = trim($request->beneName);
		$bank_account = $request->accountNumber;
		$mobile_number =$request->mobile;
		$tramoBeneId = $request->beneId;
		$amount = $request->amount;
		$channel=$request->channel;
		$userDetails = $this->getUserDetails();
		$duplicate = $this->checkDuplicateTransaction($bank_account,$amount,$userId,$mobile_number,5);
		if($duplicate['status'] == 31){ 
				return response()->json($duplicate);
				$storeApiResp->response_to_api = json_encode($respToApi);
			$storeApiResp->save();
			return Response::json($respToApi);
		}
		$walletScheme = PremiumWalletScheme::where('min_amt','<=',$amount)->where('max_amt','>=',$amount)->where('wallet_scheme_id',$userDetails->member->dmt_two_wallet_scheme)->first();
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
		$beneficiarydetail = Beneficiary::where(['benficiary_id'=>$tramoBeneId,'api_id'=>18])->first();
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
			$respToApi=['status'=>26,'message'=>"In-sufficient Balance"];
			$storeApiResp->response_to_api = json_encode($respToApi);
			$storeApiResp->save();
			return Response::json($respToApi);
		}
		$tds = $this->getTDS($agentComm);
		$gst  = 0;//$this->getGST($agentComm);
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
						'api_id' => 5,
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
						'secretKey'=>config('constants.TRAMO_SECRET_KEY'),
						);
		$storeApiResp->user_id=$user_id;
		$storeApiResp->api_type="TRANSACTION";
		$storeApiResp->request_message=json_encode($content);
		$storeApiResp->save();
		if($statusId == 3)
		{
			$url = config('constants.TRAMO_DMT_URL') ."/transaction";//die;
			$apiResponse= $this->getCurlPostMethod($url,$content);
			$storeApiResp->message=$apiResponse;
			$storeApiResp->save();
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
					$respToApi=['txnId'=>$insert_id,'bankRefNo'=>'','status'=>1,'message'=>"Transaction Success",'clientId'=>$request->clientId];
					$storeApiResp->response_to_api = json_encode($respToApi);
					$storeApiResp->save();
					return Response::json($respToApi);
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
