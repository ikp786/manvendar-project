<?php

namespace App\Http\Controllers\ApiForwarding;

use Illuminate\Http\Request;

use SimpleXMLElement;
use GuzzleHttp\Client;
use SoapClient;
use DOMDocument;
use App\User;
use App\Balance;
use App\AepsScheme;
use App\Report;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Response;
use Exception;
use Validator;

use App\Apiresponse;
use App\Traits\CustomTraits;
use App\Traits\AepsTraits;
use App\Traits\ReportTraits;
use DB;
use Illuminate\Validation\Rule;
class AepsController extends Controller
{
	use CustomTraits, AepsTraits, ReportTraits;
	public function getIINNo(Request $request) 
    {
		$rules = array(
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
			$bankDetails = $this->getCurlGetMethod('https://fingpayap.tapits.in/fpaepsservice/api/bankdata/bank/details');
			try
			{
				if($bankDetails)
				{
					$bankDetails = json_decode($bankDetails);//print_r($bankDetails);die;
					
					$bankLists = array();
					foreach($bankDetails->data as $data)
					{
						$bankLists[$data->iinno] = $data->bankName;
					}
					return response()->json(['status'=>1,'bankLists'=>$bankLists]);
				}
				return response()->json(['status'=>0,'bankLists'=>"Server is not responding"]);
			}
			catch(Exception $e)
			{
				return response()->json(['status'=>0,'bankLists'=>"Server is not responding."]);
			}
	}

	public function sendRequest(Request $request)
	{
		$rules = array(
			'userId' => 'required|numeric|regex:/^[0-9]+$/',
			'secretKey' => 'required|regex:/^[A-Za-z0-9]+$/',
			'clientId' => 'required|regex:/^[A-Z0-9_]+$/',
			'IIN' => 'required',
			'bankName' => 'required',
			'aadhaarNumber' => 'required|numeric|digits:12',
			'customerNumber' => 'required|numeric|digits:10',
			'transactionType' =>[
									'required',
									Rule::in(['BE', 'CW']),
								],
			'amount' => 'required|numeric|min:0|max:10000',
			'biometricData' => 'required',
			'deviceSerialNumber' => 'required',
		);
		$storeApiResp = Apiresponse::create(['request_from_api'=>json_encode($request->all()),'api_id'=>10,'api_type'=>"USER_API_AEPS_TXN"]);
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
		$isApiUserValid = $this->newCheckAgentActivation($userdetail,$_SERVER['REMOTE_ADDR'],$request);
		$userId = $userdetail->id;
		$storeApiResp->user_id=$userId;
		$storeApiResp->save();
		if($isApiUserValid['status'] !=1)
		{
			$storeApiResp->response_to_api = json_encode($isApiUserValid);
			$storeApiResp->save();
			return Response::json($isApiUserValid);
		}
		$clientId =$request->clientId;
		$isExistClienId = $this->isExistClienId($userId,$clientId);
		if($isExistClienId['status']==73){
			$storeApiResp->response_to_api = json_encode($isApiUserValid);
			$storeApiResp->save();
			return Response::json($isExistClienId);
		}
		$pidData = $request->biometricData;
		$doc = new \DOMDocument();
		try{
        $doc->loadXML($pidData);
		$xml = simplexml_load_string($pidData);
		}
		catch(Exception $e)
		{
			$respToApi = [
			'status'=>81,	
			'message' => "Invalid format of biometricData",
			];
			$storeApiResp->response_to_api = json_encode($respToApi);
			$storeApiResp->save();
			return Response::json($respToApi);
		}
		if ($xml === false) {
			$respToApi = [
			'status'=>82,	
			'message' => "Parsing to failed biometricData",
			];
			$storeApiResp->response_to_api = json_encode($respToApi);
			$storeApiResp->save();
			return Response::json($respToApi);
		}
		else
		{
			$aadhaarNumber=$request->aadhaarNumber;
			$bankName=$request->bankName;
			$transactionType=$request->transactionType;
			$customerNumber=$request->customerNumber;
			$deviceSerialNumber=$request->deviceSerialNumber;
			try
			{
				 if (getenv('HTTP_CLIENT_IP'))
					$ipaddress = getenv('HTTP_CLIENT_IP');
				else if(getenv('HTTP_X_FORWARDED_FOR'))
					$ipaddress = getenv('HTTP_X_FORWARDED_FOR');
				else if(getenv('HTTP_X_FORWARDED'))
					$ipaddress = getenv('HTTP_X_FORWARDED');
				else if(getenv('HTTP_FORWARDED_FOR'))
					$ipaddress = getenv('HTTP_FORWARDED_FOR');
				else if(getenv('HTTP_FORWARDED'))
					$ipaddress = getenv('HTTP_FORWARDED');
				else if(getenv('REMOTE_ADDR'))
					$ipaddress = getenv('REMOTE_ADDR');
				else
					$ipaddress = 'UNKNOWN';
				$ip=$ipaddress;
				$location_data = (unserialize(file_get_contents('http://www.geoplugin.net/php.gp?ip='.$ip)));
				
				//print_r($location_data);die;
			}
			catch(Exception $e)
			{
				$respToApi = [
				'status'=>79,	
				'message' => "Error in finding GEO Location",
				];
				$storeApiResp->response_to_api = json_encode($respToApi);
				$storeApiResp->save();
				return Response::json($respToApi);
			}
			
			$tranactionId=time().''.$userId;
			try
			{
			$post = array('captureResponse'=>
				array(
					'PidDatatype'=>"X",
					"Piddata"=>(string)$xml->Data[0],
					"ci" =>  (string)$xml->Skey[0]['ci'],
					"dc" => (string)$xml->DeviceInfo['dc'],
					"dpID" => (string)$xml->DeviceInfo['dpId'],
					"errCode" => (string)$xml->Resp['errCode'],
					"errInfo" => (string)$xml->Resp['errInfo'],
					"fCount" => (string)$xml->Resp['fCount'],
					"fType" => (string)$xml->Resp['fType'],
					"hmac" => (string)$xml->Hmac,
					"iCount" => 0,
					"mc" => (string)$xml->DeviceInfo['mc'],
					"mi" => (string)$xml->DeviceInfo['mi'],
					"nmPoints" => (string)$xml->Resp['nmPoints'],
					"pCount" => 0,
					"pType" => 1,
					"qScore" => (string)$xml->Resp['qScore'],
					"rdsID" => (string)$xml->DeviceInfo['rdsId'],
					"rdsVer" => (string)$xml->DeviceInfo['rdsVer'],
					"sessionKey" => (string)$xml->Skey,
					),
				'cardnumberORUID'=>
						array(
							'adhaarNumber'=>$aadhaarNumber,
							'indicatorforUID'=>0,
							'nationalBankIdentificationNumber'=>$request->IIN,
							),
				'languageCode'=>"en",
				'latitude'=>$location_data['geoplugin_latitude'],
				'longitude'=>$location_data['geoplugin_longitude'],
				'mobileNumber'=>$customerNumber,
				'paymentType'=>"B",
				'requestRemarks'=>"A2Z".time().$userId,
				'timestamp'=>date("d/m/Y H:i:s"),
				'transactionAmount'=>($transactionType=="BE") ? 0 : $request->amount,
				'transactionType'=>$transactionType,
				'merchantUserName'=>"excelonestopm",
				'merchantPin'=>md5(1234),
				'subMerchantId'=>$userId,
				);
			}
			catch(Exception $e)
			{
				$respToApi = [
				'status'=>80,	
				'message' => "Error during getting device information",
				];
				$storeApiResp->response_to_api = json_encode($respToApi);
				$storeApiResp->save();
				return Response::json($respToApi);
			}
				if($transactionType=="BE")
				{
					$url ='https://fingpayap.tapits.in/fpaepsservice/api/balanceInquiry/merchant/php/getBalance';
					$apiType="AEPS_BAL_INQUIRY";
					$txnType = "BALANCE_INQUIRY";
					$post['merchantTransactionId']=$tranactionId;
					$agentCharge=$agentCommission=$amount=0;
					
				}else if($transactionType=="CW"){
					
					$url ='https://fingpayap.tapits.in/fpaepsservice/api/cashWithdrawal/merchant/php/withdrawal';
					$apiType="AEPS_BAL_CASHWIDRAWAL";
					$txnType = "CASH_WITHDRAWAL";
					$userDetails = $this->getUserDetails();
					$user_id = $userDetails->id;
					$amount=$request->amount;
					if($request->amount <=0)
						return response()->json(['status' => 83, 'message' => 'Minimum withdrawal amount is Rs. 1']);
					$walletScheme = AepsScheme::where('min_amt','<=',$amount)->where('max_amt','>=',$amount)->where('wallet_scheme_id',$userdetail->member->aeps_scheme)->first();
					if($walletScheme=='')
					{
						return response()->json(['status' => 75, 'message' => 'your scheme is not configured, Please contact with admin']);
					}
					
					$agentCharge = $this->agentAepsCharge($amount,$walletScheme->agent_charge,$walletScheme->agent_charge_type);
					$agentCommission =  $this->getAepsCommission($amount,$walletScheme->agent_comm,$walletScheme->agent_comm_type);
				}
				$aepsLib = new \App\Library\Aeps;
				$aepsReportDetails = $aepsLib->insertAepsRecard($request->aadhaarNumber,$customerNumber,$amount,$bankName,$userId,$agentCharge,$agentCommission,$txnType,$clientId);
				if($aepsReportDetails['status']==1)
				{
					$aepsReport=$aepsReportDetails['details'];
				}
				else{
					return response()->json(['status'=>500,'message'=>"Internal Server Error"]);
				}
				$insert_id=$post['merchantTranId']=$aepsReport->id;
				
				$method="POST";
				$key = '';
				$mt_rand = array(0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15);
				foreach ($mt_rand as $chr)
				{             
					$key .= chr($chr);         
				}
				$iv =   '06f2f04cc530364f';
				$ciphertext_raw = openssl_encrypt(json_encode($post), 'AES-128-CBC', $key, $options=OPENSSL_RAW_DATA, $iv);
				$requestContent = base64_encode($ciphertext_raw);
				$fp=fopen("fingpay_public_production.cer","r");
				$pub_key_string=fread($fp,8192);         
				fclose($fp);         
				openssl_public_encrypt($key,$crypttext,$pub_key_string);

				$header = [
						'Content-Type: text/xml',
						'trnTimestamp:'.date('d/m/Y H:i:s'),
						'hash:'.base64_encode(hash("sha256",json_encode($post), True)),
						'deviceIMEI:'.$deviceSerialNumber,        
						'eskey:'.base64_encode($crypttext)         
						];
				$storeApiResp->api_type=$apiType;
				$storeApiResp->user_id=$userId;
				$storeApiResp->request_message=json_encode($post);
				$storeApiResp->report_id=$aepsReport->id;
				$storeApiResp->message="BEFORE API HIT";
				$storeApiResp->save();
				$curl = curl_init();
				curl_setopt_array($curl, array
				(
					CURLOPT_URL =>$url,     
					CURLOPT_RETURNTRANSFER => true,
					CURLOPT_ENCODING => "",
					CURLOPT_SSL_VERIFYPEER => true,  
					CURLOPT_HEADER=> false,   				
					CURLOPT_SSL_VERIFYHOST => 2,
					CURLOPT_MAXREDIRS => 10,
					CURLOPT_TIMEOUT => 90,
					CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
					CURLOPT_CUSTOMREQUEST => "POST",
					CURLOPT_POSTFIELDS => $requestContent,
					CURLOPT_HTTPHEADER => $header,
				));
				
				$response = curl_exec($curl);
				$err = curl_error($curl);
				curl_close($curl);
				if($err)
				{
					
					$respToApi=[
							'status'=>504,
							'message'=>$err,
							'availableBalance'=>0.0,
							'transactionAmount'=>0.0,
							'bankRRN'=>'',
							'transactionType'=>$transactionType,
							'txnId'=>$aepsReport->id,
							'clientId'=>$clientId,
							];
					$storeApiResp->message=$err;
					$storeApiResp->response_to_api=json_encode($respToApi);
					$storeApiResp->save();
					return Response::json($respToApi);
				}
				
				$storeApiResp->message=$response;
				$storeApiResp->save();
				$result = json_decode($response);
				if($result->status)
				{
					if($result->data->transactionStatus =="successful")
					{
							
							$aepsReport->txnid=$result->data->fpTransactionId;
							$aepsReport->description=$request->selectedBankName;
							$aepsReport->bank_ref=$result->data->bankRRN;
							$status=3;
							if($transactionType=="CW")
							{
								$txnCreditAmount=$amount+$aepsReport->credit_charge-$aepsReport->tds -$agentCharge;
								DB::beginTransaction();
								try
								{
									
									$aepsReport->status_id=1;
									Balance::where('user_id', $userId)->increment('user_balance', $txnCreditAmount);
									$aepsReport->total_balance = Balance::where('user_id',$userId)->first()->user_balance;
									$aepsReport->save();
									DB::commit();
									$message="Success";
									$status=1;
								}
								catch(Exception $e)
								{
									DB::rollback();
									$aepsReport->status_id=3;
									$aepsReport->save();
									$message="Pending";
									$status=3;
									
								}
								$aepsLib->calculateAepsCommission($userId,$userdetail,$walletScheme,$insert_id,$amount,$aepsReport,$aadhaarNumber,$customerNumber,$request->selectedBankName);
								
								$bankName=$request->selectedBankName;
		            			$msg ="Your Transaction Successfull with $amount from Bank $bankName";
		            			$msg=urlencode($msg);
		            			try{

		            				$this->sendSMS($customerNumber,$msg,1);
		            			}
		            			catch(Exception $e){
		            					
								}
								
							}
							else
							{
								$message="Success";
								$status=1;
								$aepsReport->status_id=1;
								$aepsReport->save();
							}
						$respToApi = [
							'status'=>$status,
							'availableBalance'=>$result->data->balanceAmount,
							'transactionAmount'=>$result->data->transactionAmount,
							'bankRRN'=>$result->data->bankRRN,
							//'transactionType'=>$result->data->transactionType,
							'transactionType'=>$transactionType,
							'txnId'=>$aepsReport->id,
							'clientId'=>$clientId,
							'message'=>"$message"
						];
							$storeApiResp->response_to_api=json_encode($respToApi);
							$storeApiResp->save();
							return Response::json($respToApi);
					}
					$respToApi=[
							'status'=>3,
							'availableBalance'=>0.0,
							'transactionAmount'=>0.0,
							'bankRRN'=>'',
							'transactionType'=>$transactionType,
							'txnId'=>$aepsReport->id,
							'message'=>"Pending",
							'clientId'=>$clientId,
							];
					$storeApiResp->response_to_api=json_encode($respToApi);
					$storeApiResp->save();
					return Response::json($respToApi);  
				}
				else
				{
					if($transactionType=="CW")
					{
						if(in_array($result->statusCode,array(10027)))
						{
							
							$respToApi = [
							'status'=>2,
							'availableBalance'=>0,
							'transactionAmount'=>0,
							'bankRRN'=>'',
							'transactionType'=>$transactionType, 
							'txnId'=>$aepsReport->id,
							'message'=>@$result->message,
							'clientId'=>$clientId,
							];
							$storeApiResp->response_to_api=json_encode($respToApi);
							$storeApiResp->save();
							return Response::json($respToApi);
						}
						$aepsReport->txnid=$result->data->fpTransactionId;
						$aepsReport->bank_ref=$result->data->bankRRN;
						DB::beginTransaction();
						try
						{
							$aepsReport->type="CR/DB";
							$aepsReport->status_id=2;
							$aepsReport->save();
							
							$respToApi = [
							'status'=>2,
							'availableBalance'=>$result->data->balanceAmount,
							'transactionAmount'=>$result->data->transactionAmount,
							'bankRRN'=>@$result->data->bankRRN,
							//'transactionType'=>@$result->data->transactionType,
							'transactionType'=>$transactionType,
							'txnId'=>$aepsReport->id,
							'message'=>@$result->message,
							'clientId'=>$clientId,
						];
							$storeApiResp->response_to_api=json_encode($respToApi);
							$storeApiResp->save();
							DB::commit();
							return Response::json($respToApi);
						}
						catch(Exception $e)
						{
							DB::rollback();
							$respToApi = [
							'status'=>3,
							'availableBalance'=>$result->data->balanceAmount,
							'transactionAmount'=>$result->data->transactionAmount,
							'bankRRN'=>@$result->data->bankRRN,
							//'transactionType'=>@$result->data->transactionType,
							'transactionType'=>$transactionType,
							'txnId'=>$aepsReport->id,
							'message'=>"Pending",
							'clientId'=>$clientId,
							];
							$storeApiResp->response_to_api=json_encode($respToApi);
							$storeApiResp->save();	
							return Response::json($respToApi);
						}
					}
					else
					{
						$aepsReport->status_id=2;
						$aepsReport->save();
						$respToApi = [
							'status'=>2,
							'availableBalance'=>@$result->data->balanceAmount,
							'transactionAmount'=>@$result->data->transactionAmount,
							'bankRRN'=>@$result->data->bankRRN,
							//'transactionType'=>$result->data->transactionType,
							'transactionType'=>$transactionType,
							'txnId'=>$aepsReport->id,
							'message'=>@$result->message,
							'clientId'=>$clientId,
							];
						$storeApiResp->response_to_api=json_encode($respToApi);
						$storeApiResp->save();
						return Response::json($respToApi);
					}
				}		
		}
	}
	public function getMyIp(Request $request)
	{
		return $this->getMyServerIp($request);
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
	public function checkStatus(Request $request)
	{
		$rules = array(
			'userId' => 'required|numeric|regex:/^[0-9]+$/',
			'clientId' => 'required|regex:/^[0-9A-Z_]+$/',
			'secretKey' => 'required|regex:/^[A-Za-z0-9]+$/',
		);
		$storeApiResp = Apiresponse::create(['request_from_api'=>json_encode($request->all()),'api_id'=>10,'api_type'=>"Api User Transaction Check Status"]);
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
		try
		{
			$report=Report::where(['client_ackno'=>$request->clientId,'user_id'=>$request->userId])->firstOrFail();
			$storeApiResp->report_id=$report->id;
			$storeApiResp->save();
		}
		catch(Exception $e)
		{
			$respToApi =['status'=>404,'message'=>"Failed",'txnId'=>'','bankRRN'=>'','clientId'=>$request->clientId,'description'=>'Record not found at merchant','amount'=>0];
			$storeApiResp->response_to_api = json_encode($respToApi);
			$storeApiResp->save();
			return Response::json($respToApi);
		}
		$amount =$report->amount;
		$clientId = $request->clientId;
		$bankRefNo = $report->bank_ref;
		$txnId = $report->txnid;
		if($report->status_id==1)
		{
			$respToApi =['status'=>1,'message'=>"Success",'description'=>"Transaction Successful",'txnId'=>$txnId,'bankRRN'=>$bankRefNo,'clientId'=>$clientId,'amount'=>$amount];
			$storeApiResp->response_to_api = json_encode($respToApi);
			$storeApiResp->save();
			return Response::json($respToApi);
		}
		else if($report->status_id==2)
		{
			$respToApi =['status'=>2,'message'=>"Failed",'description'=>"Transaction Failed",'txnId'=>$txnId,'bankRRN'=>$bankRefNo,'clientId'=>$clientId,'amount'=>$amount];
			$storeApiResp->response_to_api = json_encode($respToApi);
			$storeApiResp->save();
			return Response::json($respToApi);
		}
		elseif($report->status_id==3)
		{
			$respToApi =['status'=>3,'message'=>"Pending",'description'=>"Transaction is pending",'txnId'=>$txnId,'bankRRN'=>$bankRefNo,'clientId'=>$clientId,'amount'=>$amount];
			$storeApiResp->response_to_api = json_encode($respToApi);
			$storeApiResp->save();
			return Response::json($respToApi);
		}
		else if($report->status_id==21){
			$respToApi =['status'=>57,'message'=>"Failed",'description'=>"Transaction has been refunded successfully",'txnId'=>$txnId,'bankRRN'=>$bankRefNo,'clientId'=>$clientId,'amount'=>$amount];
			$storeApiResp->response_to_api = json_encode($respToApi);
			$storeApiResp->save();
			return Response::json($respToApi);
		}
		
		

	}
}
