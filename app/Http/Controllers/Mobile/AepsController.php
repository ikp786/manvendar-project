<?php

namespace App\Http\Controllers\Mobile;

use App\Traits\CustomAuthTraits;
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
use App\AepsScheme;
use App\Report;
use App\Api;
use App\Beneficiary;use App\ApiAepsAgentRegistration;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Response;
use Exception;
use Validator;
use App\Gstcommission;
use App\Apiresponse;
use App\Traits\CustomTraits;
use App\Traits\AepsTraits;
use DB;
use Illuminate\Validation\Rule;

class AepsController extends Controller
{
    use CustomTraits, AepsTraits, CustomAuthTraits;
    public function aepsBankList(Request $request)
    {
        //validation rules
        $rules = ['userId' => 'required', 'token' => 'required'];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) return response()
            ->json(['status' => '10', 'message' => 'missing params', 'error' => $validator->getMessageBag()]);

        //user authentication
        $authentication = $this->checkLoginAuthentication($request);
        if ($authentication['status'] != 1) return $authentication;

        $userDetails = $authentication['userDetails'];
        if ($userDetails->role_id != 5) return response()->json(['status' => '2', 'message' => 'you are not authenticated']);

        $bankDetails = $this->getCurlGetMethod('https://fingpayap.tapits.in/fpaepsservice/api/bankdata/bank/details');
        if ($bankDetails) {
            $bankDetails = json_decode($bankDetails);//print_r($bankDetails);die;
            $bankLists = array();
            foreach ($bankDetails->data as $data) {
                $bankLists[$data->iinno] = $data->bankName;
            }
        }
        return response()->json(['status'=>'1','message'=>'success','data'=>$bankLists]);
    }

	public function aepsTransaction(Request $request)
	{
		
            $rules =  [
                'userId' => 'required',
                'token' => 'required',
                'aadhaarNumber'=>'required|numeric|digits:12',
                'customerNumber'=>'required|numeric|digits:10',
				'txtPidData'=>'required',
                'bankName'=>'required',
				'deviceName'=>'required',
               'transactionType' =>[
									'required',
									Rule::in(['BE', 'CW']),
								],
				'amount' => 'required|numeric|min:0|max:10000',
            ];
		$storeApiResp = Apiresponse::create(['request_from_api'=>json_encode($request->all()),'api_id'=>10,'api_type'=>"USER_API_AEPS_TXN",'user_id'=>4]);
		
		$validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) return response()
            ->json(['status' => '10', 'message' => 'missing params', 'error' => $validator->getMessageBag()]);
			
			
        $authentication = $this->checkLoginAuthentication($request);
        if($authentication['status']!=1) return $authentication;



        $userDetails = $authentication['userDetails'];
        $userId = $userDetails->id;
		
		
		if($userId !=4)
		{
			$isAcitveService = $this->isActiveService(10);
			if($isAcitveService)
				return response()->json(['status_id'=>0,'message'=>$isAcitveService->message]);
		}
		$pidData = $request->txtPidData;
		try{
			$doc = new \DOMDocument();
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
			$serialNuber = $xml->DeviceInfo->additional_info->Param[0]['value'];
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
			if($request->deviceName=="MANTRA")
				$serialNuber = (string)$xml->DeviceInfo->additional_info->Param[0]['value'];
			else
				$serialNuber = (string)$xml->DeviceInfo->additional_info->Param[0]['value'];			$merchantDetails = ApiAepsAgentRegistration::where(['user_id'=>1,'agent_id'=>$userId])->first();			if($merchantDetails=='')				return response()->json(['status' => 2, 'message' => 'Aeps Onboarding is not activated, Please contact with Admin']);			else			{				$merchantLoginId = $merchantDetails->merchant_login_id;				$merchantLoginPin = $merchantDetails->merchant_login_pin;			}
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
							'nationalBankIdentificationNumber'=>$bankName,
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
				'merchantUserName'=>isset($merchantLoginId) ? $merchantLoginId : "excelonestopm",
				'merchantPin'=>isset($merchantLoginPin) ? md5($merchantLoginPin) : md5(1234),
				//'subMerchantId'=>$userId,
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
				//return $post; 
				if($transactionType=="BE")
				{
					$url ='https://fingpayap.tapits.in/fpaepsservice/api/balanceInquiry/merchant/php/getBalance';
					$apiType="AEPS_BAL_INQUIRY";
					$txnType = "BALANCE_INQUIRY";
					$post['merchantTransactionId']=$tranactionId;
					$agentCharge=$agentCommission=$amount=0;
					
				}else if($transactionType=="CW")
				{
					$url ='https://fingpayap.tapits.in/fpaepsservice/api/cashWithdrawal/merchant/php/withdrawal';
					$apiType="AEPS_BAL_CASHWIDRAWAL";
					$txnType = "CASH_WITHDRAWAL";
					$amount=$request->amount;
					$walletScheme = AepsScheme::where('min_amt','<=',$amount)->where('max_amt','>=',$amount)->where('wallet_scheme_id',$userDetails->member->aeps_scheme)->first();
					if($walletScheme=='')
					{
						return response()->json(['status' => 2, 'message' => 'your scheme is not configured, Please contact with admin']);
					}
					
					$agentCharge = $this->agentAepsCharge($amount,$walletScheme->agent_charge,$walletScheme->agent_charge_type);
					$agentCommission =  $this->getAepsCommission($amount,$walletScheme->agent_comm,$walletScheme->agent_comm_type);
				}
				$aepsLib = new \App\Library\Aeps;
				$aepsReportDetails = $aepsLib->insertAepsRecard($request->aadhaarNumber,$customerNumber,$amount,$bankName,$userId,$agentCharge,$agentCommission,$txnType);
				if($aepsReportDetails['status']==1)
				{
					$aepsReport=$aepsReportDetails['details'];
				}
				else{
					return response()->json(['status'=>2,'message'=>$aepsReportDetails['details']]);
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
						'deviceIMEI:'.$serialNuber,        
						'eskey:'.base64_encode($crypttext)         
						];
				$apiResp = Apiresponse::create(['message'=>'','api_id'=>10,'api_type'=>$apiType,'user_id'=>$userId,'request_message'=>json_encode($post),'report_id'=>$aepsReport->id]);
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
					CURLOPT_TIMEOUT => 30,
					CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
					CURLOPT_CUSTOMREQUEST => "POST",
					CURLOPT_POSTFIELDS => $requestContent,
					CURLOPT_HTTPHEADER => $header,
				));
				
				$response = curl_exec($curl);
				$err = curl_error($curl);
				curl_close($curl);
				
				$apiResp->message=$response;
				$apiResp->save();
				$result = json_decode($response);
				
			
				if($result->status)
				{
					
					if($result->data->transactionStatus =="successful")
					{
							$aepsReport->txnid=$result->data->fpTransactionId;
							$aepsReport->description=$request->selectedBankName;
							$aepsReport->bank_ref=$result->data->bankRRN;
							$status="Success";
							
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
								}
								catch(Exception $e)
								{
									DB::rollback();
									$aepsReport->status_id=3;
									$aepsReport->save();
									$status="Pending";
									
								}
								$aepsLib->calculateAepsCommission($userId,$userDetails,$walletScheme,$insert_id,$amount,$aepsReport,$aadhaarNumber,$customerNumber,$request->selectedBankName);
								
								$bankName=$request->selectedBankName;
		            			$msg ="Your Transaction Successfull with $amount from Bank $bankName";
		            			$msg=urlencode($msg);
		            			try{

		            				$this->sendSMS($customerNumber,$msg,1);
		            			}
		            			catch(Exception $e){
		            					
								}
								
							}
							else{
								$aepsReport->status_id=1;
								$aepsReport->save();
							}
							return response()->json(['status'=>$status,'availableBalance'=>number_format($result->data->balanceAmount,2),'transactionAmount'=>$result->data->transactionAmount,'bankRRN'=>$result->data->bankRRN,'transactionType'=>$txnType,'fpTransactionId'=>$result->data->fpTransactionId,'txnTime'=>date("d-m-Y")]);
						
					}
					return response()->json(['status'=>"Pending",'availableBalance'=>'','transactionAmount'=>'','bankRRN'=>'','transactionType'=>'','fpTransactionId'=>'']);
				}
				else
				{
					if($transactionType=="CW")
					{
						$aepsReport->txnid=$result->data->fpTransactionId;
						$aepsReport->description=$request->selectedBankName;
						$aepsReport->bank_ref=$result->data->bankRRN;
						DB::beginTransaction();
						try
						{
							
							
							$aepsReport->type="CR/DB";
							$aepsReport->status_id=2;
							$aepsReport->save();
							DB::commit();
						}
						catch(Exception $e)
						{
							
							DB::rollback();
							return response()->json(['status'=>"Pending",'availableBalance'=>'','transactionAmount'=>'','bankRRN'=>'','transactionType'=>'','fpTransactionId'=>'']);
						}
					}
					else{
						$aepsReport->status_id=2;
						$aepsReport->save();
					}
					return response()->json(['status'=>"Failed",'message'=>$result->message]);
				}		
		}
	}
   
  
}
