<?php

namespace App\Http\Controllers\ApiForwarding;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\AepsUserRegistrationResponse;
use Auth;
use App\ApiAepsAgentRegistration;
use Validator;
use Exception;
use App\Traits\CustomTraits;
use Response;
class AepsRegistrationController extends Controller
{
	use CustomTraits;
    function aepsMemberRegistration(Request $request)
	{
		$rules = array(
			'agentName' => 'required|max:25|regex:/^[a-zA-z \.]+$/',
			'userId' => 'required|numeric|regex:/^[0-9]+$/',
			'panNumber' => 'required|regex:/^[0-9A-Z]+$/|unique:api_aeps_agent_registrations,pan_number',
			'aadhaarNumber' => 'required|digits:12|regex:/^[0-9]+$/|unique:api_aeps_agent_registrations,aadhaar_number',
			'agentId' => 'required|regex:/^[0-9a-zA-Z_]+$/|unique:api_aeps_agent_registrations,user_id',
			'secretKey' => 'required|regex:/^[A-Za-z0-9]+$/',
			'timestamp' => 'required|regex:/^[0-9]+$/',
			'latitude' => 'required|regex:/^[0-9\.]+$/',
			'longitude' => 'required|regex:/^[0-9\.]+$/',
			'address' => 'required',
			'state' => 'required',
			'mobile' => 'required|numeric|digits:10|unique:api_aeps_agent_registrations,mobile',
			'shopName' => 'required|min:5|max:50',
			'pincode' => 'required|numeric|digits:6',
			'ipAddress' => 'required',
		);
		$userdetail = Auth::guard('api')->user();
		$storeApiResp = AepsUserRegistrationResponse::create(['request_from_api'=>json_encode($request->all()),'user_id'=>$userdetail->id,'mobile'=>$request->mobile,'pan_number'=>$request->panNumber,'aadhaar_number'=>$request->aadhaarNumber,]);
		$validator = Validator::make($request->all(), $rules);
		if ($validator->fails()){
			$respToApi = [
				'status'=>10,	
				'message' => "Missing/Invalid Parametes",
				'errors'=>$validator->errors()->getMessages(),
			];
			$storeApiResp->response_to_api = json_encode($respToApi);
			$storeApiResp->status="FAILED";
			$storeApiResp->reason="Validation Failed";
			$storeApiResp->save();
			return Response::json($respToApi);
		}
		
		$isApiUserValid = $this->newCheckAgentActivation($userdetail,$_SERVER['REMOTE_ADDR'],$request);
		if($isApiUserValid['status'] !=1)
		{
			$storeApiResp->response_to_api = json_encode($isApiUserValid);
			$storeApiResp->status="FAILED";
			$storeApiResp->reason="Authentication Failed";
			$storeApiResp->save();
			return Response::json($isApiUserValid);
		}
		$reqeustLoggedId  =  $userdetail->id;
		try
		{
			$apiRegistration = ApiAepsAgentRegistration::create([
						'user_id'=>$userdetail->id,
						'agent_id'=>$request->agentId,
						'pan_number'=>$request->panNumber,
						'aadhaar_number'=>$request->aadhaarNumber,
						'mobile'=>$request->mobile,
						'agent_name'=>$request->agentName,
					]);
		}
		catch(Exception $e)
		{
			$respToApi = [
				'status'=>500,	
				'message' => "Internal Server error, please try again",
			];
			throw $e;
			$storeApiResp->response_to_api = json_encode($respToApi);
			$storeApiResp->status="FAILED";
			$storeApiResp->reason="Internal Server error".$e->getMessage();
			$storeApiResp->save();
			return response()->json($respToApi);
		}
		$merchantLoginId = "AEPS".$request->mobile.$userdetail->id.$apiRegistration->id;
		$merchantLoginPin=$request->mobile.$userdetail->id.$apiRegistration->id;
		$apiRegistration->merchant_login_id=$merchantLoginId; 
		$apiRegistration->merchant_login_pin=$merchantLoginPin;
		$apiRegistration->save();
		$contentToPost = array(
					'username'=>'excelonestopd',
					'password'=>md5("1234d"),
					'supermerchantId'=>223,
					'ipAddress'=>$request->ipAddress,
					'latitude'=>$request->latitude,
					'longitude'=>$request->longitude,
					'merchants'=>array(array(
								'merchantLoginId'=>$merchantLoginId,
								'merchantLoginPin'=>$merchantLoginPin,
								'merchantName'=>$request->agentName,
								'merchantPhoneNumber'=>$request->mobile,
								'companyLegalName'=>$request->shopName,
								'companyMarketingName'=>$request->shopName,
								'merchantBranch'=>$request->shopName,
								'emailId'=>isset($request->email) ? $request->email : "",
								'merchantPinCode'=>$request->pincode,
								'tan'=>"",
								'cancellationCheckImages'=>"",
								'merchantAddress'=>array(
														'merchantAddress'=>$request->address,
														'merchantState'=>$request->state,
													),
								'kyc'=>array(
											'userPan'=>$request->panNumber,
											'aadhaarNumber'=>$request->aadhaarNumber,
											'gstInNumber'=>"",
											"companyOrShopPan"=>"",
										),
								'settlement'=>array(
													'companyBankAccountNumber'=>"",
													'bankIfscCode'=>"",
													'companyBankName'=>"",
													'bankBranchName'=>"",
													'bankAccountName'=>"",
												)
							),
					));
			$storeApiResp->request_message = json_encode($contentToPost);
			$storeApiResp->save();
			$method="POST";
			$key = '';
			$mt_rand = array(0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15);
			foreach ($mt_rand as $chr)
			{             
				$key .= chr($chr);         
			}
			$iv =   '06f2f04cc530364f';
			$ciphertext_raw = openssl_encrypt(json_encode($contentToPost), 'AES-128-CBC', $key, $options=OPENSSL_RAW_DATA, $iv);
			$requestContent = base64_encode($ciphertext_raw);
			$fp=fopen("fingpay_public_production.cer","r");
			$pub_key_string=fread($fp,8192);         
			fclose($fp);         
			openssl_public_encrypt($key,$crypttext,$pub_key_string);
			$header = [
					'Content-Type: text/plain',
					'trnTimestamp:'.date('d/m/Y H:i:s'),
					'hash:'.base64_encode(hash("sha256",json_encode($contentToPost), True)),
					'eskey:'.base64_encode($crypttext)         
			];
			$url = 'https://fingpayap.tapits.in/fpaepsweb/api/onboarding/merchant/creation/php/m1';
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
				CURLOPT_TIMEOUT => 125,
				CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
				CURLOPT_CUSTOMREQUEST => "POST",
				CURLOPT_POSTFIELDS => $requestContent,
				CURLOPT_HTTPHEADER => $header,
			));
			
			$response = curl_exec($curl);
			$err = curl_error($curl);
			curl_close($curl);
			if($err)
				$result =$err;
			else
				$result = $response;
			$storeApiResp->message = $result;
			$storeApiResp->save();
			try
			{
				
				$apiJsonContent = json_decode($result);
				if($apiJsonContent->status)
				{
					if($apiJsonContent->data->merchants[0]->activeFlag==1)
					{
						$apiRegistration->status_id=1;
						$apiRegistration->save();
						$respToApi = [
							'loginId'=>$merchantLoginId,	
							'loginPin'=>$merchantLoginPin,	
							'status'=>1,	
							'message' => "Registration Successful, Please wait for approval",
						];
						$storeApiResp->response_to_api = json_encode($respToApi);
						$storeApiResp->status="SUCCESS";
						$storeApiResp->reason="SUCCESS";
						$storeApiResp->save();
						return response()->json($respToApi);
					}
					else
					{
						$respToApi = [
							'status'=>2,	
							'message' => "Failed : " . $apiJsonContent->data->merchants[0]->remarks,
						];
						$apiRegistration->delete();
						
						$storeApiResp->status="FAILED";
						$storeApiResp->reason="Failed";
						$storeApiResp->save();
						return response()->json($respToApi);
					}
				}
				else
				{
					$respToApi = [
						'status'=>2,	
						'message' => "Failed : ".$apiJsonContent->message,
					];
					$apiRegistration->delete();
					$storeApiResp->response_to_api = json_encode($respToApi);
					$storeApiResp->status="FAILED";
					$storeApiResp->reason="Failed";
					$storeApiResp->save();
					return response()->json($respToApi);
				}
			}
			catch(Exception $e)
			{
				$respToApi = [
						'status'=>2,	
						'message' => "Failed : ",
					];
					$apiRegistration->delete();
					$storeApiResp->response_to_api = json_encode($respToApi);
					$storeApiResp->status="FAILED";
					$storeApiResp->reason="Failed";
					$storeApiResp->save();
					return response()->json($respToApi);
			}
	}
}



