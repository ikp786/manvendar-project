<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Apiresponse;
use App\User;
use Auth;
use App\ApiAepsAgentRegistration;
use App\AepsUserRegistrationResponse;
use Validator;
use Exception;
use App\Traits\CustomTraits;
use Response;
class AepsRegistrationController extends Controller
{
    public function index(Request $request)
	{
		if(Auth::user()->role_id==1)
		{
			$apiAepsUserQuery = ApiAepsAgentRegistration::orderBy('id','desc');
			if($request->searchType)
			{
				if($request->searchType=="MOB")
					$apiAepsUserQuery->where('mobile',$request->content );
				elseif($request->searchType=="AgentId")
					$apiAepsUserQuery->where('agent_id',$request->content );
			}
			$apiAepsUser=$apiAepsUserQuery->simplePaginate(30);
			return view('admin.aeps.agent',compact('apiAepsUser'));
		}
		else{
			return view('errors.permission-denied');
		}
	}
	public function aepsAgentOnboard(Request $request)
	{
		if(Auth::user()->role_id==1)
		{
			try
			{
				$userDetails = User::findOrFail($request->userId);
			}
			catch(Exception $e)
			{
				return response()->json(['status'=>0,'message'=>"User does not exist",'details'=>'']);
			}
			try
			{
				$apiRegistration = ApiAepsAgentRegistration::create([
							'user_id'=>1,
							'agent_id'=>$userDetails->id,
							'pan_number'=>$userDetails->member->pan_number,
							'aadhaar_number'=>$userDetails->member->adhar_number,
							'mobile'=>$userDetails->mobile,
							'agent_name'=>$userDetails->name,
						]);
			}
			catch(Exception $e)
			{
				$respToApi = [
					'status'=>500,	
					'message' => "Internal Server error, please try again",
				];
				throw $e;
				return response()->json($respToApi);
			}
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
			}
			catch(Exception $e)
			{
				$respToApi = [ 
				'status'=>79,	
				'message' => "Error in finding GEO Location",
				];
				
			}
			$merchantLoginId = "AEPS".time().$userDetails->id;
			$merchantLoginPin=time().$userDetails->id;
			$apiRegistration->merchant_login_id = $merchantLoginId;
			$apiRegistration->merchant_login_pin = $merchantLoginPin;
			$apiRegistration->save();
			$contentToPost = array(
						'username'=>'excelonestopd',
						'password'=>md5("1234d"),
						'supermerchantId'=>223,
						'ipAddress'=>$ip,
						'latitude'=>$location_data['geoplugin_latitude'],
						'longitude'=>$location_data['geoplugin_longitude'],
						'merchants'=>array(array(
									'merchantLoginId'=>$merchantLoginId,
									'merchantLoginPin'=>$merchantLoginPin,
									'merchantName'=>$userDetails->name,
									'merchantPhoneNumber'=>$userDetails->mobile,
									'companyLegalName'=>$userDetails->member->company,
									'companyMarketingName'=>$userDetails->member->company,
									'merchantBranch'=>$userDetails->member->company,
									'emailId'=>$userDetails->email,
									'merchantPinCode'=>$userDetails->member->pin_code,
									'tan'=>"",
									'cancellationCheckImages'=>"",
									'merchantAddress'=>array(
												'merchantAddress'=>$userDetails->member->office_address,
												'merchantState'=>22,
									),
									'kyc'=>array(
												'userPan'=>$userDetails->member->pan_number,
												'aadhaarNumber'=>$userDetails->member->adhar_number,
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
				$storeApiResp = AepsUserRegistrationResponse::create(['request_from_api'=>'','user_id'=>$userDetails->id,'mobile'=>$userDetails->mobile,'pan_number'=>$userDetails->member->pan_number,'aadhaar_number'=>$userDetails->member->adhar_number,'request_message'=>json_encode($contentToPost),'aeps_registration_id'=>$apiRegistration->id]);
				
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
							$userDetails->is_aeps_onboard = 1;
							$userDetails->save();
							$apiRegistration->save();
							$respToApi = [
								'status'=>1,	
								'message' => "Registration Successful",
							];
							
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
							'message' => "Failed : " . $apiJsonContent->message,
						];
						$apiRegistration->delete();
						
						$storeApiResp->status="FAILED";
						$storeApiResp->reason="Failed";
						$storeApiResp->save();
						return response()->json($respToApi);
					}
				}
				catch(Exception $e)
				{
					$respToApi = [
							'status'=>3,	
							'message' => "PENDING",
					];
					
					$storeApiResp->status="PENDING";
					$storeApiResp->reason="PENDING";
					$storeApiResp->save();
					return response()->json($respToApi);
				}
		}
	}
}
