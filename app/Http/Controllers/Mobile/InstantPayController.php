<?php

namespace App\Http\Controllers\Mobile;

use App\ApiResponseToApp;
use App\Http\Controllers\Controller;
use App\Library\InstantPayDMT;
use App\Traits\CustomAuthTraits;
use Illuminate\Http\Request;

use App\Http\Requests;
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

class InstantPayController extends Controller
{
    //
    use CustomTraits;
    use CustomAuthTraits;

    var $mode = "APP";

    public function mobileVerification(Request $request)
    {

        $rules = array(
            'userId' => 'required',
            'token' => 'required',
            'mobileNumber' => 'required|numeric|digits:10',
        );
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return Response::json(array(
                'status' => 10,
                'message' => "Missing Param",
                'errors' => $validator->getMessageBag()->toArray()
            ));
        }
        $authentication = $this->checkLoginAuthentication($request);
        if ($authentication['status'] != 1) return $authentication;
        $userDetails = $authentication['userDetails'];
        $userId = $userDetails->id;


        if ($userId != 5) {
            $isAcitveService = $this->isActiveService(16);
            if ($isAcitveService)
                return response()->json(['status' => 2, 'message' => $isAcitveService->message]);
        }
        if (in_array($userDetails->role_id, array(5, 15)) && $userDetails->member->dmt_three == 1) {
            $mobile = $request->mobileNumber;
            $token = config('constants.INSTANT_KEY');
            $content = "{\"token\":\"$token\",\"request\":{\"mobile\":\"$mobile\",\"outletid\":1}}";
            $url = config('constants.INSTANT_PAY_URL') . "/ws/dmi/remitter_details";
            try {
                $apiResp = $this->getCurlPostIMethod($url, $content);
                $new = json_decode($apiResp);
                if ($new->status == 'Transaction Successful') {

                    foreach ($new->data->beneficiary as $data) {
                        $isExistBene = Beneficiary::where(['benficiary_id' => $data->id, 'api_id' => 16])->first();
                        if ($isExistBene) {
                            $isExistBene->account_number = $data->account;
                            $isExistBene->ifsc = $data->ifsc;
                            $isExistBene->bank_name = $data->bank;
                            $isExistBene->customer_number = $data->mobile;
                            $isExistBene->mobile_number = $data->mobile;
                            $isExistBene->name = $data->name;
                            $isExistBene->save();
                        } else {
                            Beneficiary::create(['benficiary_id' => $data->id,
                                'account_number' => $data->account,
                                'ifsc' => $data->ifsc,
                                'bank_name' => $data->bank,
                                'customer_number' => $mobile,
                                'mobile_number' => $data->mobile,
                                'vener_id' => 3,
                                'api_id' => 16,
                                'user_id' => $userId,
                                'name' => $data->name
                            ]);
                        }

                    }
                } elseif ($new->status == "OTP sent successfully") {

                }
                return $apiResp;
            } catch (Exception $e) {
                throw $e;
                return response()->json(['status' => 0, 'message' => "Api Response failed , Please try again"]);
            }

        } else return response()->json(['status' => 2, 'message' => 'you are not authenticate']);
    }//done

    public function remitterRegister(Request $request)
    {

        $rules = array(
            'fName' => 'required|between:3,10|regex:/^[A-Za-z ]+$/',
            'lName' => 'required|between:3,10|regex:/^[A-Za-z ]+$/',
            'pinCode' => 'required|between:6,6|regex:/^[0-9]+$/',
            'mobileNumber' => 'required|numeric|digits:10',
            'userId' => 'required',
            'token' => 'required'
        );
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return Response::json(array(
                'success' => false,
                'message' => 'Missing params',
                'errors' => $validator->getMessageBag()->toArray()
            )); // 400 being the HTTP code for an invalid request.
        }

        $authentication = $this->checkLoginAuthentication($request);
        if ($authentication['status'] != 1) return $authentication;
        $userDetails = $authentication['userDetails'];
        $userId = $userDetails->id;

        $token = config('constants.INSTANT_KEY');
        $firstName = $request->fName;
        $lastName = $request->lName;
        $pinCode = $request->pinCode;
        $mobile = $request->mobileNumber;
        $content = "{\"token\":\"$token\",\"request\": {\"mobile\": \"$mobile\",\"name\":\"$firstName\",\"surname\":\"$lastName\",\"pincode\":\"$pinCode\",\"outletid\":1}}";
        $apiResp = Apiresponse::create(['message' => '', 'api_id' => 16, 'user_id' => $userId, 'api_type' => "Remitter Addition", 'report_id' => 1101, 'request_message' => $content]);
        try {
            $url = config('constants.INSTANT_PAY_URL') . "/ws/dmi/remitter";//die;
            $data = $this->getCurlPostIMethod($url, $content);
            $apiResp->message = $data;
            $apiResp->save();
            return $data;
        } catch (Exception $e) {
            return response()->json(['status' => 0, 'message' => "Api Response failed. Try again after some time"]);
        }

    }

    public function remitterVerification(Request $request)
    {
        $rules = array(
            'remitterOTP' => 'required|between:6,6',
            'remitterVerifyId' => 'required',
            'mobileNumber' => 'required|numeric|digits:10',
            'userId' => 'required',
            'token' => 'required'
        );
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return Response::json(array(
                'success' => 10,
                'errors' => $validator->getMessageBag()->toArray()
            )); // 400 being the HTTP code for an invalid request.
        }

        $authentication = $this->checkLoginAuthentication($request);
        if($authentication['status'] !=1) return $authentication;
        $userDetails = $authentication['userDetails'];
        $userId = $userDetails->id;

        $token = config('constants.INSTANT_KEY');
        $mobileNumber = $request->mobileNumber;
        $remitterOTP = $request->remitterOTP;
        $remitterVerifyId = $request->remitterVerifyId;
        $content = "{\"token\": \"$token\",\"request\": {\"remitterid\": \"$remitterVerifyId\",\"mobile\": \"$mobileNumber\",\"otp\": \"$remitterOTP\",\"outletid\":1}}";
        $apiResp = Apiresponse::create(['message' => '', 'api_id' => 16, 'user_id' => $userId, 'api_type' => "Remitter Addition", 'report_id' => 1101, 'request_message' => $content]);
        try {
            $url = config('constants.INSTANT_PAY_URL') . "/ws/dmi/remitter_validate";//die;
            $data = $this->getCurlPostIMethod($url, $content);
            $apiResp->message = $data;
            $apiResp->save();
            return $data;
        } catch (Exception $e) {
            return response()->json(['status' => 0, 'message' => "Api Response failed. Try again after some time"]);
        }
    }


public function remitterVerificationBkp(Request $request)
	{
	
		$rules = array(
            'remitterOTP' => 'required|between:6,6',
            'remitterVerifyId' => 'required',
			'mobileNumber' => 'required|numeric|digits:10',
			 'userId' => 'required',
            'token' => 'required'
        );
		$validator = Validator::make($request->all(), $rules);
		if ($validator->fails()) {
			return Response::json(array(
				'success' => 10,
				'message'=>'missing params',
				'errors' => $validator->getMessageBag()->toArray()
			)); // 400 being the HTTP code for an invalid request.
		}
			$token = config('constants.INSTANT_KEY');
			$mobileNumber = $request->mobileNumber;
			$remitterOTP = $request->remitterOTP;
			$remitterVerifyId = $request->remitterVerifyId;
			$content ="{\"token\": \"$token\",\"request\": {\"remitterid\": \"$remitterVerifyId\",\"mobile\": \"$mobileNumber\",\"otp\": \"$remitterOTP\",\"outletid\":1}}";
			$apiResp = Apiresponse::create(['message'=>'','api_id'=>16,'user_id'=>Auth::id(),'api_type'=>"Remitter Addition",'report_id'=>1101,'request_message'=>$content]);
			try{
				$url = config('constants.INSTANT_PAY_URL') ."/ws/dmi/remitter_validate";//die;
				$data =  $this->getCurlPostIMethod($url,$content);
				$apiResp->message = $data;
				$apiResp->save();
				return $data;
			}
			catch(Exception $e)
			{
				return response()->json(['status'=>0,'message'=>"Api Response failed. Try again after some time"]);
			}
	}
	
    function beneAdd(Request $request)
    {

        $rules = array(
            'accountNumber' => 'required|numeric|regex:/^[0-9]+$/',
            'senderId' => 'required|numeric|regex:/^[0-9]+$/',
            'beneName' => 'required',
            'ifscCode' => 'required',
            'bankName' => 'required',
            'mobile_number' => 'required|numeric:10|regex:/^[0-9]+$/',
            'userId' => 'required',
            'token' => 'required'
        );
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return Response::json(['status' => 0, 'message' => $validator->errors()->getMessages()]);

        }

        $authentication = $this->checkLoginAuthentication($request);
        if($authentication['status'] !=1) return $authentication;
        $userDetails = $authentication['userDetails'];
        $userId = $userDetails->id;


        $token = config('constants.INSTANT_KEY');
        $bankName = $request->bankName;
        $ifsc = trim($request->ifscCode);
        $mobile_number = trim($request->mobile_number);
        $accountNumber = trim($request->accountNumber);
        $remitterId = trim($request->senderId);
        $beneName = trim($request->beneName);
        $user_id = $userId;

        $content = "{\n\t\"token\"\t\t\t: \"$token\",\n\t\"request\"\t\t: \n\t{\n\t\t\"remitterid\"\t: \"$remitterId\",\n\t\t\"name\"\t\t\t: \"$beneName}\",\n\t\t\"mobile\"\t\t: \"$mobile_number\",\n\t\t\"ifsc\"\t\t\t: \"$ifsc\",\n\t\t\"account\"\t\t: \"$accountNumber\"\n\t\t,\"outletid\":1\n\t}\n}";
        $url = config('constants.INSTANT_PAY_URL') . "/ws/dmi/beneficiary_register";
        $response = $this->getCurlPostIMethod($url, $content);
        $apiResponse = Apiresponse::create(['message' => $response, 'api_id' => 16, 'user_id' =>$userId, 'api_type' => "ADD BENE", 'request_message' => json_encode($content)]);
        try {
            $res = json_decode($response);
            if (!empty($res->statuscode)) {
                if ($res->statuscode == "TXN") {

                    $beneName = $request->beneName;
                    $beneDetails = Beneficiary::create([
                        'benficiary_id' => 0,
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
                    $beneDetails->benficiary_id = $res->data->beneficiary->id;
                    $beneDetails->status_id = 1;
                    $beneDetails->save();
                }
            }
            return $response;
        } catch (Exception $e) {
            return response()->json(['status' => 0, 'message' => "Api Response failed. try agian after some time"]);
        }
    }

    function beneVerification(Request $request)
    {

        //print_r($request->all());die;
        $rules = array(
            'remitterId' => 'required|numeric|regex:/^[0-9]+$/',
            'beneficiaryId' => 'required|numeric|regex:/^[0-9]+$/',
            'otp' => 'required|regex:/^[0-9]+$/',
            'mobileNumber' => 'required|numeric:10|regex:/^[0-9]+$/',
        );
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return Response::json(['status' => 0, 'message' => $validator->errors()->getMessages()]);

        }
        $token = config('constants.INSTANT_KEY');
        $beneficiaryId = $request->beneficiaryId;
        $remitterId = $request->remitterId;
        $otp = $request->otp;
        $content = "{\"token\": \"$token\",\"request\": {\"remitterid\": \"$remitterId\",\"beneficiaryid\": \"$beneficiaryId\",\"otp\": \"$otp\",\"outletid\":1}}";
        $url = config('constants.INSTANT_PAY_URL') . "/ws/dmi/beneficiary_register_validate";
        try {
            $response = $this->getCurlPostIMethod($url, $content);
            $apiResponse = Apiresponse::create(['message' => $response, 'api_id' => 16, 'user_id' => Auth::id(), 'api_type' => "ADD BENE", 'request_message' => $content]);
            return $response;
        } catch (Exception $e) {
            return response()->json(['status' => 0, 'message' => "Api Response failed. try agian after some time"]);
        }
    }

    function resendBeneVerificationOtp(Request $request)
    {

        $token = config('constants.INSTANT_KEY');
        $remitterId = $request->remitterId;
        $benId = $request->BeneficiaryCode;
        $beneDetail = Beneficiary::where(['benficiary_id' => $benId, 'api_id' => 16])->first();
        $content = "{\"token\": \"$token\", \"request\": {\"remitterid\": \"$remitterId\",\"beneficiaryid\": \"$benId\",\"outletid\":1}}";
        try {
            $url = config('constants.INSTANT_PAY_URL') . "/ws/dmi/beneficiary_resend_otp";
            $response = $this->getCurlPostIMethod($url, $content);
            return $response;
        } catch (Exception $e) {
            return response()->json(['status' => 0, 'message' => "Api Response failed. try agian after some time"]);
        }
    }

    public function deleteBeneficiary(Request $request)
    {
        $remitterId = $request->remitterId;
        $beneficiaryId = $request->beneficiaryId;
        $token = config('constants.INSTANT_KEY');
        $content = "{\"token\": \"$token\",\"request\": {\"beneficiaryid\": \"$beneficiaryId\",\"remitterid\": \"$remitterId\",\"outletid\":1}}";
        $url = config('constants.INSTANT_PAY_URL') . "/ws/dmi/beneficiary_remove";
        try {
            $response = $this->getCurlPostIMethod($url, $content);
            return $response;
        } catch (Exception $e) {
            return response()->json(['status' => 0, 'message' => "Api Response failed. try agian after some time"]);
        }

    }

    function confirmBeneDelete(Request $request)
    {
        $token = config('constants.INSTANT_KEY');
        $remitterId = $request->remitterId;
        $beneficiaryId = $request->beneficiaryId;
        $otp = $request->otp;
        $content = "{\"token\": \"$token\",\"request\": {\"beneficiaryid\": \"$beneficiaryId\",\"remitterid\": \"$remitterId\",\"otp\": \"$otp\",\"outletid\":1}}";
        $url = config('constants.INSTANT_PAY_URL') . "/ws/dmi/beneficiary_remove_validate";
        try {
            $response = $this->getCurlPostIMethod($url, $content);
            return $response;
        } catch (Exception $e) {
            return response()->json(['status' => 0, 'message' => "Api Response failed. try agian after some time"]);
        }

    }

    function transaction(Request $request)
    {

        $rules = array(
            'beneficiary_id' => 'required',
            'ifsc' => 'required|regex:/^[a-zA-Z0-9]+$/',
            'channel' => 'required|regex:/^[1-2]+$/',
            'amount' => 'required|numeric|min:10|max:25000|regex:/^[0-9]+$/',
            'bank_account' => 'required|numeric|regex:/^[0-9]+$/',
            'mobile_number' => 'required|numeric|digits:10',
            'userId' => 'required',
            'token' => 'required',
			'senderName' => 'required|regex:/^[A-Za-z ]+$/',
        );
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
			$result[1] = [
				'status' => 'Failure',
                'message' => $validator->getMessageBag()->toArray(),
				];
           return response()->json(['result' => $result]);
        }

        $authentication = $this->checkLoginAuthentication($request);
        if($authentication['status'] !=1) return $authentication;
        $userDetails = $authentication['userDetails'];
        $userId = $userDetails->id;

        $appResp = ApiResponseToApp::create([
            'response_from_app' => json_encode($request->all()),
            'request_type' => 'DMT2 TRANSACTION',
            'user_id'=>$request->userId,
            'api_id' => 16,
        ]);

        $request_ip = request()->ip();
        $mobile_number = $NUMBER = $request->mobile_number;
        $benId = $request->beneficiary_id;
        $amount = $request->amount;
        $bank_account = $request->bank_account;
        $ifsc = $request->ifsc;
        $bankCode = substr($ifsc, 0, 4);
        $result = array();
        if ($userId != 4) {
            $isAcitveService = $this->isActiveService(4);
            if ($isAcitveService) {

                $result[1] = array('status' => 'Failure', 'message' => $isAcitveService->message);
                $appResp->response_to_app = json_encode($result);
                $appResp->save();
                return response()->json(['result' => $result]);

            }
        }
        $isBankDetails = Masterbank::select('bank_status')->where('bank_code', $bankCode)->first();
        if ($isBankDetails == '') {
            $result[1] = array('status' => 'Failure', 'message' => "Bank code is not found. Please contact with Admin");
            $appResp->response_to_app = json_encode($result);
            $appResp->save();
            return response()->json(['result' => $result]);
        }
        if ($isBankDetails->bank_status == 0) {

            $result[1] = array('status' => 'Failure', 'message' => "Bank is down. Please try again after some time");
            $appResp->response_to_app = json_encode($result);
            $appResp->save();
            return response()->json(['result' => $result]);

        }
        if ($request->channel == 2)
            $routingType = "IMPS";
        else
            $routingType = "NEFT";
        $statusId = 3;
        /* 	$isAcitveService = $this->isActiveOnline(4);
            if(!$isAcitveService->is_online_service)
                $statusId=24;
            else
                $statusId=3; */
        if ($amount >= 10 && $amount != '' && $NUMBER != '' && $benId != '' && $amount <= 25000) {
            $duplicate = $this->checkDuplicateTransaction($bank_account, $amount, $userId, $NUMBER);
            if ($duplicate['status'] == 2) {
                $result[1] = array('status' => 'Failure', 'message' => $duplicate['message']);
                $appResp->response_to_app = json_encode($result);  
                $appResp->save();
                return response()->json(['result' => $result]);
            }

            $balance = Balance::where('user_id', $userId)->first();
            $bulk_amount = $req_amount = $amount;
            $no_of_ite = ceil($amount / 5000);
            $result = array();
            if ($no_of_ite > 5 || $no_of_ite < 1) 
			{
                $result[1] = array('status' => 'Failure', 'message' => "You are doing something wrong..Please confirm entered balance");
                $appResp->response_to_app = json_encode($result);
                $appResp->save();
                return response()->json(['result' => $result]);
            }

            $beneficiarydetail = Beneficiary::where(['benficiary_id' => $benId, 'api_id' => 16])->first();
            if (!empty($beneficiarydetail)) {
                $beneficiarydetail = $beneficiarydetail->id;
            } else {
                $beneficiarydetail = 0;
            }
            if ($beneficiarydetail == 0) {
                $result[1] = ['status' => 'Failure', 'message' => 'Beneficiary Does not exist'];
                $appResp->response_to_app = json_encode($result);
                $appResp->save();
                return response()->json(['result' => $result]);
            }
            $user_balance = $balance->user_balance;
            $apxAmount = $amount + ($amount / 5000);
            if ($user_balance < $apxAmount) 
			{
                $result[1] = ['status' => 'Failure', 'message' => 'Low Balance Please Refill your wallet'];
                $appResp->response_to_app = json_encode($result);
                $appResp->save();
                return response()->json(['result' => $result]);
            }
            //echo "Hello";die;
            for ($i = 1; $i <= $no_of_ite; $i++) 
			{
                $now = new \DateTime();
                $datetime = $now->getTimestamp();
                $ctime = $now->format('Y-m-d H:i:s');
                if ($req_amount > 5000) {
                    $amount = 5000;
                    $req_amount = $req_amount - 5000;
                } else {
                    $amount = $req_amount;
                }
                $walletScheme = ImpsWalletScheme::where('min_amt', '<=', $amount)->where('max_amt', '>=', $amount)->where('wallet_scheme_id', $userDetails->member->imps_wallet_scheme)->first();
                $agentCharge = $this->agentCharge($amount, $walletScheme->agent_charge, $walletScheme->agent_charge_type);
                if ($walletScheme == '') {
                    $result[$i] = ['status' => 'Failure', 'message' => 'Server is busy Please try again'];
                    $appResp->response_to_app = json_encode($result);
                    $appResp->save();
                    return response()->json(['result' => $result]);
                } elseif ($walletScheme->is_error) {
                    $result[$i] = ['status' => 'Failure', 'message' => 'Error in Setting. Please Call to admin'];
                    $appResp->response_to_app = json_encode($result);
                    $appResp->save();
                    return response()->json(['result' => $result]);
                }
                $agent_parent_id = $userDetails->parent_id;
                $user_id = $userId;
                $agent_parent_role = $userDetails->parent->role_id;
                $dist_charge_data = $admin_charge_data = $md_charge_data = array();
                if ($userDetails->parent_id == 1) {
                    $d_id = $m_id = '';
                    $a_id = 1;
                    $admin_charge_data['credit_by'] = $user_id;
                    $admin_charge_data['credit_charge'] = $agentCharge;
                    $admin_charge_data['debit_charge'] = 0;
                } else {
                    if ($agent_parent_role == 4) {
                        $agent_parent_details = $userDetails->parent;
                        $dist_parent_id = $agent_parent_details->parent_id;
                        if ($dist_parent_id == 1) {
                            $d_id = $agent_parent_id;
                            $m_id = '';
                            $a_id = 1;
                            $dist_charge_data['credit_by'] = $user_id;
                            $dist_charge_data['credit_charge'] = $this->getCommission($amount, $walletScheme->dist_comm, $walletScheme->dist_comm_type);
                            $dist_charge_data['debit_charge'] = 0;
                            $admin_charge_data['credit_by'] = $d_id;
                            $admin_charge_data['credit_charge'] = $this->getCommission($amount, $walletScheme->admin_comm, $walletScheme->admin_comm_type);
                            $admin_charge_data['debit_charge'] = 0;
                        } else {
                            $d_id = $agent_parent_id;
                            $m_id = $dist_parent_id;
                            $a_id = 1;

                            $dist_charge_data['credit_by'] = $user_id;
                            $dist_charge_data['credit_charge'] = $this->getCommission($amount, $walletScheme->dist_comm, $walletScheme->dist_comm_type);
                            $dist_charge_data['debit_charge'] = 0;

                            $md_charge_data['credit_by'] = $d_id;
                            $md_charge_data['credit_charge'] = $this->getCommission($amount, $walletScheme->md_comm, $walletScheme->md_comm_type);
                            $md_charge_data['debit_charge'] = 0;

                            $admin_charge_data['credit_by'] = $m_id;
                            $admin_charge_data['credit_charge'] = $this->getCommission($amount, $walletScheme->admin_comm, $walletScheme->admin_comm_type);
                            $admin_charge_data['debit_charge'] = 0;
                        }
                    } else if ($agent_parent_role == 3) {
                        $d_id = '';
                        $m_id = $agent_parent_id;
                        $a_id = 1;

                        $md_charge_data['credit_by'] = $user_id;
                        $md_charge_data['credit_charge'] = $this->getCommission($amount, $walletScheme->md_comm, $walletScheme->md_comm_type);
                        $md_charge_data['debit_charge'] = 0;

                        $admin_charge_data['credit_by'] = $m_id;
                        $admin_charge_data['credit_charge'] = $this->getCommission($amount, $walletScheme->admin_comm, $walletScheme->admin_comm_type);
                        $admin_charge_data['debit_charge'] = 0;
                    }
                }


                $agentComm = $this->getCommission($amount, $walletScheme->agent_comm, $walletScheme->agent_comm_type);
                $agentData['credit_charge'] = $agentComm;
                $user_balance = $balance->user_balance;
                $txnDebitAmount = $amount + $agentCharge - $agentComm;
                if ($user_balance >= $txnDebitAmount && $amount >= 10) 
				{
                    $txnDebitAmount = $amount + $agentCharge - $agentComm;
                    DB::beginTransaction();
                    try {
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
                            'pay_id' => $datetime,
                            'user_id' => $user_id,
                            'created_by' => $userId,
                            'ip_address' => $request_ip,
                            'customer_number' => $NUMBER,
                            'opening_balance' => $user_balance,
                            'total_balance' => $userBalance,
                            'biller_name' => $request->senderName,
                            'gst' => 0,
                            'tds' => 0,
                            'recharge_type' => 0,
                            'credit_charge' => $agentComm,
                            'debit_charge' => $agentCharge,
                            'beneficiary_id' => $beneficiarydetail,
                            'channel' => $request->channel,
                            'mode' => $this->mode,
                        ]);
                        $insert_id = $reportDetails->id;
                        DB::commit();
                    } catch (Exception $e) {
                        DB::rollback();

                        $cyberResponse = ['txnId' => '', 'refNo' => '', 'status' => "FAILED", 'amount' => $amount, 'txnTIme' => $ctime, 'message' => "Whoops! Somethig went wrong" . $e->getMessage()];
                        $result[$i] = $cyberResponse;
                        $appResp->response_to_app = json_encode($result);
                        $appResp->save();
                        return Response()->json(['result' => $result]);
                    }
                    if ($statusId == 3) 
					{
                        $token = config('constants.INSTANT_KEY');
                        $content = "{\"token\": \"$token\",\"request\": {\"remittermobile\": \"$mobile_number\",\"beneficiaryid\": \"$benId\",\"agentid\": \"$insert_id\",\"amount\": \"$amount\",\"mode\": \"$routingType\"\t,\"outletid\":1}}";
                        $apiResp = Apiresponse::create(['api_id' => 16, 'api_type' => "TXN", 'report_id' => $insert_id, 'request_message' => $content]);

                        $url = config('constants.INSTANT_PAY_URL') . "/ws/dmi/transfer";
                        $response = $this->getCurlPostIMethod($url,$content);
                        //$response = "{\"statuscode\":\"TXN\",\"status\":\"Transaction Successful\",\"data\":{\"ipay_id\":\"1190905121604KUTFX\",\"ref_no\":\"924812920152\",\"opr_id\":\"924812920152\",\"name\":\"RAVINA DATIKA\",\"opening_bal\":\"270702.83\",\"amount\":11000,\"charged_amt\":11005.75,\"locked_amt\":0,\"ccf_bank\":10,\"bank_alias\":\"PPBL\"}}";

                        $apiResp->message = $response;
                        $apiResp->save();
                        try {
                            $res = json_decode($response);
                            if (!empty($res->statuscode)) {
                                $statuscode = $res->statuscode;
                                if ($statuscode == 'TXN') {

                                    $reportDetails->status_id = 1;
                                    $reportDetails->txnid = $res->data->ipay_id;
                                    $reportDetails->bank_ref = $res->data->ref_no;
                                    $reportDetails->save();
                                    $this->creditCommission($d_id, $m_id, $a_id, $user_id, $mobile_number, $beneficiarydetail, $request->channel, $insert_id, $amount, $dist_charge_data, $md_charge_data, $admin_charge_data, $bank_account, $reportDetails, 16);
                                    $cyberResponse = ['txnId' => $insert_id, 'refNo' => $res->data->ref_no, 'status' => "SUCCESS", 'amount' => $amount, 'txnTIme' => $ctime];
                                    $result[$i] = $cyberResponse;
                                } elseif ($statuscode == 'TUP') {
                                    $reportDetails->status_id = 1;
                                    $reportDetails->txnid = $res->data->ipay_id;
                                    $reportDetails->save();
                                    $this->creditCommission($d_id, $m_id, $a_id, $user_id, $mobile_number, $beneficiarydetail, $request->channel, $insert_id, $amount, $dist_charge_data, $md_charge_data, $admin_charge_data, $bank_account, $reportDetails, 16);
                                    $cyberResponse = ['txnId' => $insert_id, 'refNo' => $res->data->ref_no, 'status' => "SUCCESS", 'amount' => $amount, 'txnTIme' => $ctime];
                                    $result[$i] = $cyberResponse;
                                } elseif ($statuscode == 'ERR' || $statuscode == 'IAB' || $statuscode == 'SPD' || $statuscode == 'ISE' || $statuscode == 'IAN') {
                                    Balance::where('user_id', $user_id)->increment('user_balance', $txnDebitAmount);
                                    $balance = Balance::where('user_id', $user_id)->first();
                                    $user_balance = $balance->user_balance;
                                    $reportDetails->status_id = 2;
                                    $reportDetails->type = "DR/CR";
                                    $reportDetails->total_balance = $user_balance;
                                    $reportDetails->save();
                                    $cyberResponse = ['txnId' => $insert_id, 'refNo' => '', 'status' => "FAILED", 'amount' => $amount, 'txnTIme' => $ctime, 'message' => "FAILED"];
                                    $result[$i] = $cyberResponse;
                                } else {
                                    $this->creditCommission($d_id, $m_id, $a_id, $user_id, $mobile_number, $beneficiarydetail, $request->channel, $insert_id, $amount, $dist_charge_data, $md_charge_data, $admin_charge_data, $bank_account, $reportDetails, 16);
                                    $cyberResponse = ['txnId' => $insert_id, 'refNo' => '', 'status' => "PENDING", 'amount' => $amount, 'txnTIme' => $ctime];
                                    $result[$i] = $cyberResponse;
                                }
                            } else {

                                $this->creditCommission($d_id, $m_id, $a_id, $user_id, $mobile_number, $beneficiarydetail, $request->channel, $insert_id, $amount, $dist_charge_data, $md_charge_data, $admin_charge_data, $bank_account, $reportDetails, 4);
                                $cyberResponse = ['txnId' => $insert_id, 'refNo' => '', 'status' => "PENDING", 'amount' => $amount, 'txnTIme' => $ctime];
                                $result[$i] = $cyberResponse;
                            }
                        } catch (Exception $e) {
                            $cyberResponse = ['txnId' => $insert_id, 'refNo' => '', 'status' => "PENDING", 'amount' => $amount, 'txnTIme' => $ctime];
                            $result[$i] = $cyberResponse;
                        }
                    } else {
                        $this->creditCommission($d_id, $m_id, $a_id, $user_id, $mobile_number, $beneficiarydetail, $request->channel, $insert_id, $amount, $dist_charge_data, $md_charge_data, $admin_charge_data, $bank_account, $reportDetails, 16);
                        $cyberResponse = ['txnId' => $insert_id, 'refNo' => '', 'status' => "PENDING", 'amount' => $amount, 'txnTIme' => $ctime];
                        $result[$i] = $cyberResponse;
                    }

                } else {
                    $cyberResponse = ['txnId' => '', 'refNo' => '', 'status' => "FAILED", 'amount' => $amount, 'txnTIme' => $ctime];
                    $result[$i] = $cyberResponse;
                }
            }
            $appResp->response_to_app = json_encode($result);
            $appResp->save();
            return Response()->json(['result' => $result]);
        } 
		else 
		{
			$result[1] = ['status' => "Failure", 'message' =>"Low Balance"];
            $appResp->response_to_app = json_encode($result);
            $appResp->save();
            return Response()->json(['result' => $result]);
        }

    }


    private function agentCharge($amount, $charge, $chargeType)
    {
        if ($chargeType == 0)
            return ($amount * $charge) / 100;
        else
            return $charge;

    }

    private function getCommission($amount, $commission, $comm_type)
    {
        if ($comm_type == 0)
            return ($amount * $commission) / 100;
        else
            return $commission;
    }

    public function deleteBeneficiaryRequest(Request $request)
    {
        $rules = array(
            'beneId' => 'required|regex:/^[0-9]+$/',
        );
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return Response::json(['status' => 10, 'message' => $validator->errors()->getMessages()]);
        }
        $content = array('api_token' => config('constants.TRAMO_API_KEY'), 'userId' => config('constants.TRAMO_USER_ID'), 'beneId' => $request->beneId);
        $url = config('constants.TRAMO_DMT_URL') . "/bene-delete-request";
        $response = $this->getCurlPostIMethod($url, $content);
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
        if ($validator->fails()) {
            return Response::json(['status' => 10, 'message' => $validator->errors()->getMessages()]);
        }
        $content = array('api_token' => config('constants.TRAMO_API_KEY'), 'userId' => config('constants.TRAMO_USER_ID'), 'beneId' => $request->beneId, 'otp' => $request->otp);
        $url = config('constants.TRAMO_DMT_URL') . "/bene-delete-confirm-otp";
        $response = $this->getCurlPostIMethod($url, $content);
        return $response;
    }

    public function checkImpsTransactionCurrentStatus(Request $request)
    {
        //return CustomTraits::checkStatus($request->id);
        return $this->checkDgTwoStatus($request->id);
    }

    public function sendRefundTxnOtp(Request $request)
    {
        try {
            $report = Report::findOrFail($request->recordId);
        } catch (Exception $e) {
            return response()->json(['status' => 0, 'message' => "No Record Found"]);
        }
        $content = array('api_token' => config('constants.TRAMO_API_KEY'), 'userId' => config('constants.TRAMO_USER_ID'), 'txnId' => $report->txnid);
        $url = config('constants.TRAMO_DMT_URL') . "/send-refund-txn-otp?" . $content;//die;
        return $this->getCurlGetMethod($url);
    }

    public function transactionRefundRequest(Request $request)
    {
        $report = Report::find($request->recordId);
        $content = array('api_token' => config('constants.TRAMO_API_KEY'), 'userId' => config('constants.TRAMO_USER_ID'), 'txnId' => $report->txnid, 'otp' => $request->otp);
        $url = config('constants.TRAMO_DMT_URL') . "/txn-refund-request";
        $response = $this->getCurlPostIMethod($url, $content);
        DB::beginTransaction();
        try {
            CustomTraits::doFailTranaction($report, "OTP_REFUND");
            CustomTraits::creditRefundAmount($report, "OTP_REFUND");
            $report->status_id = 21;
            $report->refund = 0;
            $report->refundrequest()->update(['refund_status' => 0]);
            $report->save();
            DB::commit();
            return $response;
        } catch (Exception $e) {
            DB::rollback();
            $err_msg = "Something went worng. Please contact with Admin";
            return response()->json(['status' => 0, 'message' => $err_msg]);
        }


    }

    private function createEntry($user_id, $mobile_number, $bene_id, $channel, $insert_id, $userData, $bank_account)
    {
        if ($user_id != '' && $userData['credit_charge'] > 0) {
            $amount = $userData['credit_charge'];
            if ($amount > 0) {
                if ($user_id == 1) {
                    $tds = 0;
                    $netCommission = $amount;
                } else {
                    $tds = (($amount) * 5) / 100;
                    $netCommission = $amount - ($tds);
                }
                $userData['credit_charge'] = 0;
                //$userData['gst'] = $gst;
                $userData['tds'] = $tds;
                Balance::where('user_id', $user_id)->increment('user_balance', $netCommission);
                $userData['number'] = $bank_account;
                $userData['amount'] = $amount;
                $userData['provider_id'] = 41;

                $userData['profit'] = 0;
                $userData['api_id'] = 16;
                $userData['status_id'] = 22;
                $userData['type'] = 'CR';
                $userData['description'] = "DMT_COMMISSION";
                $userData['txn_type'] = "COMMISSION";
                $userData['pay_id'] = time();
                $userData['user_id'] = $user_id;
                $userData['txnid'] = $insert_id;
                $userData['customer_number'] = $mobile_number;
                $userData['total_balance'] = Balance::select('user_balance')->where('user_id', $user_id)->first()->user_balance;
                $userData['beneficiary_id'] = $bene_id;
                $userData['channel'] = $channel;
                $userData['mode'] = $this->mode;
                Report::create($userData);
            }
        }

    }

    private function createChargeEntry($user_id, $mobile_number, $bene_id, $channel, $insert_id, $agentCharge, $bank_account)
    {
        $amount = $agentCharge;
        if ($amount > 0) {
            //$gst = ($amount *18)/118;
            $gst = 0;
            $taxableAmt = ($amount - $gst);
            $userData['credit_charge'] = 0;
            $userData['debit_charge'] = 0;
            Balance::where('user_id', $user_id)->decrement('user_balance', $amount);
            $userData['gst'] = $gst;
            $userData['number'] = $bank_account;
            $userData['amount'] = $taxableAmt;
            $userData['provider_id'] = 41;
            $userData['profit'] = 0;
            $userData['txn_type'] = "SERVICE CHARGE";
            $userData['api_id'] = 16;
            $userData['status_id'] = 15;
            $userData['type'] = 'DB';
            $userData['description'] = "DMT_SERVICE_CHARGE";
            $userData['pay_id'] = time();
            $userData['user_id'] = $user_id;
            $userData['txnid'] = $insert_id;
            $userData['customer_number'] = $mobile_number;
            $userData['total_balance'] = Balance::select('user_balance')->where('user_id', $user_id)->first()->user_balance;
            $userData['beneficiary_id'] = $bene_id;
            $userData['channel'] = $channel;
            $userData['mode'] = $this->mode;
            Report::create($userData);
        }

    }

    private function checkDuplicateTransaction($accountNo, $amount, $userId, $mobileNo)
    {
        $startTime = date("Y-m-d H:i:s");
        $formatted_date = date('Y-m-d H:i:s', strtotime('-30 seconds', strtotime($startTime)));
        $result = DB::table('reports')->select('number', 'amount', 'user_id', 'created_at')->where(['number' => $accountNo, 'bulk_amount' => $amount, 'user_id' => $userId, 'customer_number' => $mobileNo])->whereIn('status_id', [1, 3, 9])->where('api_id', 16)->where('created_at', '>=', $formatted_date)->orderBy('created_at', 'id')->first();
        if ($result) {
            return array('status' => 2, 'message' => 'Same account and same amount. Please Try agian after 30 Seconds.');
        } else {
            return array('status' => 0, 'message' => 'not found');
        }
    }

    public function isBankDownOrNot(Request $request)
    {
        $account = $request->accountNumber;
        $ifscCode = $request->ifscCode;
        explode('(', $request->bankName);
        $bankCode = substr($ifscCode, 0, 4);
        $bankDetails = Masterbank::where(['bank_code' => $bankCode])->first();
        if ($bankDetails == '') {
            return response()->json(['status' => 1, 'message' => "Bank code is not found. Please contact with Admin"]);
        }
        $bankSortName = $bankDetails->bank_sort_name;
        $content = array('account' => $account, "outletid" => 1, "bank" => $bankSortName);
        $instantPay = new InstantPayDMT;
        $apiResp = $instantPay->isBankdDownOrNot($content);
        return response()->json(['status' => 0, 'message' => 'bank up']);
        try {
            $res = json_decode($apiResp);
            $fistArray = $res->data[0];
            if (!empty($res->statuscode) && $res->statuscode == "TXN") {
                if ($fistArray->is_down == 0)// Bank Up
                    Masterbank::where(['bank_code' => $fistArray->ifsc_alias])->update(['bank_status' => 1]);
                else
                    Masterbank::where(['bank_code' => $fistArray->ifsc_alias])->update(['bank_status' => 0, 'down_time' => date('Y-m-d H:i:s')]);
                return response()->json(['status' => $fistArray->is_down, 'message' => $fistArray->ifsc_alias . ' is Down']);
            } else {
                Masterbank::where(['bank_code' => $bankCode])->update(['bank_status' => 0, 'down_time' => date('Y-m-d H:i:s')]);
                return response()->json(['status' => 1, 'message' => $fistArray->ifsc_alias . 'is Down']);
            }
        } catch (Exception $e) {
            Masterbank::where(['bank_code' => $bankCode])->update(['bank_status' => 0, 'down_time' => date('Y-m-d H:i:s')]);
            return response()->json(['status' => 1, 'message' => $bankDetails->bank_name . 'is Down']);
        }
    }

	public function mobileVerificaton(Request $request)
	{
		
		    $rules = array(
            'userId' => 'required',
            'token' => 'required',
			'mobile_number'=>'required',
        );
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return Response::json(array(
                'status' => 10,
                'errors' => $validator->getMessageBag()->toArray(),
                'message' => "Missing param"
            ));
        }

        $authentication = $this->checkLoginAuthentication($request);
        if($authentication['status'] !=1) return $authentication;
        $userDetails = $authentication['userDetails'];
        $userId = $userDetails->id;
		
		
		if($userId !=4)
		{
				$isAcitveService = $this->isActiveService(16);
				if($isAcitveService)
					return response()->json(['status'=>201,'message'=>$isAcitveService->message]);
			
		}
		
		if(in_array($userDetails->role_id,array(5,15)) && $userDetails->member->dmt_three ==1)
		{
			$mobile = $request->mobile_number;
			$token = config('constants.INSTANT_KEY');
			$content ="{\"token\":\"$token\",\"request\":{\"mobile\":\"$mobile\",\"outletid\":1}}";
			$url = config('constants.INSTANT_PAY_URL') ."/ws/dmi/remitter_details";
			try
			{
			$apiResp = $this->getCurlPostIMethod($url,$content);
			$new = json_decode($apiResp);
			if($new->status == 'Transaction Successful')
			{

				foreach ($new->data->beneficiary as $data) {
				$isExistBene = Beneficiary::where(['benficiary_id'=>$data->id,'api_id' => 16])->first();
				if ($isExistBene) 
				{
						$isExistBene->account_number=$data->account;
						$isExistBene->ifsc=$data->ifsc;
						$isExistBene->bank_name=$data->bank;
						$isExistBene->customer_number=$data->mobile;
						$isExistBene->mobile_number=$data->mobile;
						$isExistBene->name=$data->name;
						$isExistBene->save();
				} else { 
					Beneficiary::create(['benficiary_id' => $data->id,
					 'account_number' => $data->account,
					 'ifsc' => $data->ifsc,
					 'bank_name' => $data->bank,
					 'customer_number' => $mobile,
					 'mobile_number' => $data->mobile,
					 'vener_id' => 3,
					 'api_id' => 16,
					 'user_id' => $userId,
					 'name' => $data->name
					]);
					}

				}
			}
			elseif($new->status == "OTP sent successfully")
			{
				
			} 
			return $apiResp;
			}
			catch(Exception $e)
			{
				return response()->json(['status'=>0,'message'=>"Api Response failed , Please try again"]);
			}
	
		}
	}
}
