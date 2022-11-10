<?php

namespace App\Http\Controllers\Mobile;

use App\ActiveService;
use App\ApiResponseToApp;
use App\Http\Controllers\Controller;
use App\Traits\CustomAuthTraits;
use App\User;
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
use Auth;
use DB;
use Exception;
use Validator;
use Response;
use App\Traits\CustomTraits;

class PremiumWalletController extends Controller
{
    use CustomTraits, CustomAuthTraits;

    public function kycMobileVerificaton(Request $request)
    {

        $rules = array(
            'mobile_number' => 'required|numeric|digits:10',
            'userId' => 'required',
            'token' => 'required',
        );
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return Response::json(array(
                'success' => false,
                'errors' => $validator->getMessageBag()->toArray()
            ));
        }

        $authentication = $this->checkLoginAuthentication($request);
        if ($authentication['status'] == 1) {
            $userDetail = $authentication['userDetails'];
            if ($userDetail->role_id == 1) {
                return Response::json(array(
                    'success' => false,
                    'errors' => $validator->getMessageBag()->toArray()
                ));
            } else if ($userDetail->role_id == 5) {
                $mobile = $request->mobile_number;
                $content = 'api_token=' . config('constants.TRAMO_API_KEY') . '&mobile=' . $mobile . '&userId=' . config('constants.TRAMO_USER_ID');
                $url = config('constants.TRAMO_DMT_URL') . "/kyc-mobile-verification?" . $content;//die;
                return $this->getCurlGetMethod($url);
            }
        }
        return $authentication;
    }

    public function mobileVerificaton(Request $request)
    {

        if ($request->userId != 4) {
            $isAcitveService = $this->isActiveService(5);
            if ($isAcitveService)
                return response()->json(['status' => 201, 'message' => $isAcitveService->message]);
        }

        $rules = array(
            'userId' => 'required',
            'token' => 'required',
            'mobile_number' => 'required|numeric|digits:10',
        );
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return Response::json(array(
                'success' => false,
                'errors' => $validator->getMessageBag()->toArray()
            ));
        }


        $authentication = $this->checkLoginAuthentication($request);
        if ($authentication['status'] == 1) {
            if ($authentication['userDetails']->role_id == 5) {
                $mobile = $request->mobile_number;
                $content = 'api_token=' . config('constants.TRAMO_API_KEY') . '&mobile=' . $mobile . '&userId=' . config('constants.TRAMO_USER_ID');
                $url = config('constants.TRAMO_DMT_URL') . "/mobile-verification?" . $content;//die;
                return $this->getCurlGetMethod($url);

            }
        }
        return $authentication;
    }

    public function getBeniList(Request $request)
    {

        $rules = array(
            'mobile_number' => 'required|numeric|digits:10',
            'userId' => 'required',
            'token' => 'required',
        );
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return Response::json(array(
                'success' => false,
                'errors' => $validator->getMessageBag()->toArray()
            ));
        }

        $authentication = $this->checkLoginAuthentication($request);
        if ($authentication['status'] != 1)
            return $authentication;
        $mobile = $request->mobile_number;
        $content = 'api_token=' . config('constants.TRAMO_API_KEY') . '&mobile=' . $mobile . '&userId=' . config('constants.TRAMO_USER_ID');
        $url = config('constants.TRAMO_DMT_URL') . "/bene-list?" . $content;//die;
        $apiResponse = $this->getCurlGetMethod($url);
        $beneList = json_decode($apiResponse);
        if ($beneList != '') {
            if ($beneList->status == 22) {
                $beniListContents = $beneList->message->data;
                foreach ($beniListContents as $content) {
                    $beneficiary_id = Beneficiary::where(['benficiary_id' => $content->beneId, 'api_id' => 18])->first();
                    if ($beneficiary_id == '') {
                        Beneficiary::create(['benficiary_id' => $content->beneId,
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
                    } else {
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
        //print_r($request->all());die;
        $rules = array(
            'fname' => 'required',
            'lname' => 'required',
            'mobile' => 'required|numeric|digits:10',
            'userId' => 'required',
            'token' => 'required',
        );
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return Response::json(array(
                'success' => false,
                'errors' => $validator->getMessageBag()->toArray()
            )); // 400 being the HTTP code for an invalid request.
        }
        $authentication = $this->checkLoginAuthentication($request);
        if ($authentication['status'] != 1)
            return $authentication;
        $mobile = $request->mobile;
        $content = array('api_token' => config('constants.TRAMO_API_KEY'), 'mobile' => $mobile,
            'userId' => config('constants.TRAMO_USER_ID'), 'walletType' => 0, 'fname' => $request->fname,
            'lname' => $request->lname);
        $url = config('constants.TRAMO_DMT_URL') . "/remitter-register";//die;
        $response = $this->getCurlPostMethod($url, $content);
        return $response;

    }

    public function mobileVerifiedWithOTP(Request $request)
    {
        $rules = array(
            'otp' => 'required|numeric|digits:4/',
            'mobile' => 'required|numeric|digits:10',
            'token' => 'required',
            'userId' => 'required',
        );
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return Response::json(array(
                'success' => false,
                'errors' => $validator->getMessageBag()->toArray()
            )); // 400 being the HTTP code for an invalid request.
        }

        $authentication = $this->checkLoginAuthentication($request);
        if ($authentication['status'] != 1)
            return $authentication;

        $mobile = $request->mobile;
        $otp = $request->otp;
        $content = array('api_token' => config('constants.TRAMO_API_KEY'), 'mobile' => $mobile, 'userId' => config('constants.TRAMO_USER_ID'), 'otp' => $otp);
        $url = config('constants.TRAMO_DMT_URL') . "/mobile-verification-with-otp";//die;
        return $this->getCurlPostMethod($url, $content);
        return response()->json(['status' => 0, 'message' => "Invalid OTP"]);

    }

    function verifyAccountNumber(Request $request)
    {
        $rules = array(
            'token' => 'required',
            'userId' => 'required',
            'mobile' => 'required|numeric|digits:10',
            'accountNumber' => 'required',
            'ifscCode' => 'required',
            'bankName' => 'required',

        );
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return Response::json(array(
                'success' => false,
                'errors' => $validator->getMessageBag()->toArray()
            )); // 400 being the HTTP code for an invalid request.
        }

        $authentication = $this->checkLoginAuthentication($request);
        if ($authentication['status'] != 1)
            return $authentication;
        $bankcode = $request->bankcode;
        $ifsc = $request->ifsc;
        $mobile_number = $request->mobile_number;
        $bank_account = $request->bank_account;
        $masterbank = Masterbank::where('bank_name', $bankcode)->first();
        if ($masterbank) {
            $bank_code = $masterbank->bank_code;
            $ifsc = $masterbank->ifsc;
        } else {
            $bank_code = '';
            $ifsc = "";
        }

        $sdistributor = 0;
        $distributor = 0;
        $retailer = 4;

        $user_id = $request->userId;
        $balance = Balance::where('user_id', $user_id)->first();
        $user_balance = $balance->user_balance;
        $limit = 0;
        $sumamount = $retailer + $limit;
        if ($user_balance >= $sumamount && $retailer >= 4) {
            $parameter_cyber = [$bank_account, $ifsc, $mobile_number];
            $cyber = new \App\Library\CyberDMT;
            $response = $cyber->account_name_info($parameter_cyber);
            Apiresponse::create(['message' => $response, 'api_id' => 3, 'api_type' => "VERIFICATION"]);
            $res = json_decode($response);
            if (!empty($res->statuscode)) {
                $status = $res->statuscode;
                if ($status == 'TXN') {
                    Balance::where('user_id', $user_id)->decrement('user_balance', $retailer);
                    $now = new \DateTime();
                    $datetime = $now->getTimestamp();
                    $ctime = $now->format('Y-m-d H:i:s');
                    $balance = Balance::where('user_id', $user_id)->first();
                    $user_balance = $balance->user_balance;
                    $insert_id = Report::insertGetId([
                        'number' => $bank_account,
                        'provider_id' => 41,
                        'amount' => $retailer,
                        'profit' => 0,
                        'credit_charge' => 0,
                        'debit_charge' => 0,
                        'api_id' => 2,
                        'status_id' => 1,
                        'txnid' => 'Account Verification',
                        'description' => $res->data->benename,
                        'pay_id' => $datetime,
                        'created_at' => $ctime,
                        'user_id' => $user_id,
                        'channel' => 2,
                        'beneficiary_id' => 0,
                        'total_balance' => $user_balance,
                        'mode' => "APP"
                    ]);

                    return $response;
                } else {
                    return Response()->json(['status' => 'failure', 'message' => 'Server Not Responding Please Try After Sometime']);
                }
            }
        } else {
            return Response()->json(['status' => 'failure', 'message' => 'Your Balance Is Low Please Refill Your Wallet']);
        }
    }

    function beneAdd(Request $request)
    {
        $rules = array(
            'accountNumber' => 'required',
            'beneName' => 'required',
            'ifscCode' => 'required',
            'bankName' => 'required',
            'mobile_number' => 'required',
            'userId' => 'required',
            'token' => 'required',
        );
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return Response::json(['status' => 0, 'message' => $validator->errors()->getMessages()]);

        }
        $authentication = $this->checkLoginAuthentication($request);
        if ($authentication['status'] != 1)
            return $authentication;
        $bankName = $request->bankName;
        $ifsc = trim($request->ifscCode);
        $mobile_number = trim($request->mobile_number);
        $accountNumber = trim($request->accountNumber);
        if (Beneficiary::where(['ifsc' => $ifsc, 'mobile_number' => $mobile_number, 'account_number' => $accountNumber, 'api_id' => 18, 'status_id' => 1])->first())
            return Response()->json(['status' => 0, 'message' => 'Beneficiary Already Exists']);
        $user_id = $request->userId;
        if ($request->caseType == "PreAddBene") {
            $digits = 4;
            $otp = rand(pow(10, $digits - 1), pow(10, $digits) - 1);
            $beneDetails = Beneficiary::create([
                'benficiary_id' => 0,
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
            /* $msg = "OTP $otp for Beneficiary add confirmation";
            $message = urlencode($msg);
            CustomTraits::sendSMS($mobile_number, $message,1);
            return response()->json(['status'=>1,"message"=>"OTP has been sent at Remitter Mobile","beneId"=>$beneDetails]); */
            $content = array('api_token' => config('constants.TRAMO_API_KEY'), 'mobile' => $mobile_number,
                'userId' => config('constants.TRAMO_USER_ID'), 'bankName' => $bankName, 'ifscCode' => $ifsc,
                'accountNumber' => $accountNumber, 'beneName' => $request->beneName);
            $url = config('constants.TRAMO_DMT_URL') . "/add-beneficiary";
            $response = $this->getCurlPostMethod($url, $content);
            $apiResponse = Apiresponse::create(['message' => $response, 'api_id' => 18, 'user_id' => $request->userId, 'api_type' => "ADD BENE", 'request_message' => json_encode($content)]);
            $res = json_decode($response);
            if (!empty($res->status)) {
                $status = $res->status;
                if ($status == 35) {
                    $beneDetails->benficiary_id = $res->beneId;
                    $beneDetails->status_id = 1;
                    $beneDetails->save();
                }
            }
            return $response;
        }

        return response()->json(['status' => 0, "message" => "Whoop! Something went wrong"]);
    }

    function beneAddBkp(Request $request)
    {

        //print_r($request->all());die;
        $rules = array(
            'accountNumber' => 'required|numeric|regex:/^[0-9]+$/',
            'beneName' => 'required|regex:/^[A-Za-z ]+$/',
            'ifscCode' => 'required|min:11|max:11|regex:/^[A-Z0-9]+$/',
            'bankName' => 'required|regex:/^[A-Za-z() ]+$/',
            'mobile_number' => 'required|numeric:10|regex:/^[0-9]+$/',
            'userId' => 'required',
            'token' => 'required',
        );
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return Response::json(['status' => 0, 'message' => $validator->errors()->getMessages()]);

        }
        $authentication = $this->checkLoginAuthentication($request);
        if ($authentication['status'] != 1)
            return $authentication;
        $bankName = $request->bankName;
        $ifsc = trim($request->ifscCode);
        $mobile_number = trim($request->mobile_number);
        $accountNumber = trim($request->accountNumber);
        if (Beneficiary::where(['ifsc' => $ifsc, 'mobile_number' => $mobile_number, 'account_number' => $accountNumber, 'api_id' => 18])->first())
            return Response()->json(['status' => 0, 'message' => 'Beneficiary Already Exists']);
        $chargeAmount = 4;
        $user_id = $request->userId;
        $balance = Balance::where('user_id', $user_id)->first();
        $user_balance = $balance->user_balance;
        // print_r($request->all());die;
        $sumamount = $chargeAmount;
        $content = array('api_token' => config('constants.TRAMO_API_KEY'), 'mobile' => $mobile_number, 'userId' => config('constants.TRAMO_USER_ID'), 'bankName' => $bankName, 'ifscCode' => $ifsc, 'accountNumber' => $accountNumber, 'beneName' => $request->beneName);
        $url = config('constants.TRAMO_DMT_URL') . "/add-beneficiary";
        $response = $this->getCurlPostMethod($url, $content);
        $apiResponse = Apiresponse::create(['message' => $response, 'api_id' => 18, 'user_id' => $request->userId, 'api_type' => "ADD BENE", 'request_message' => json_encode($content)]);
        $res = json_decode($response);
        if (!empty($res->status)) {
            $status = $res->status;
            if ($status == 35) {

                $beneficiary_id = Beneficiary::insertGetId([
                    'benficiary_id' => $res->beneId,
                    'account_number' => $accountNumber,
                    'ifsc' => $ifsc,
                    'bank_name' => $bankName,
                    'customer_number' => $mobile_number,
                    'mobile_number' => $mobile_number,
                    'vener_id' => 1,
                    'api_id' => 18,
                    'user_id' => $user_id,
                    'name' => $request->beneName,
                ]);

            }
        }
        return $response;
    }


    public function transaction(Request $request)
    {
        $rules = array(
            'beneName'=> 'required',
            'ifsc'=> 'required|min:11|max:11|regex:/^[A-Z0-9]+$/',
            'bank_account' => 'required|numeric|regex:/^[0-9]+$/',
            'mobile_number' => 'required|numeric:10|regex:/^[0-9]+$/',
            'beneficiary_id' => 'required|numeric|regex:/^[0-9]+$/',
            'amount' => 'required|min:10|numeric|regex:/^[0-9]+$/',
            'senderName' => 'required|regex:/^[A-Za-z ]+$/',
            //'channel' => 'required:numeric||regex:/^[0-1]+$/',
        );
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()){
            return Response::json(['status'=>10,'message'=>$validator->errors()->getMessages()]);
        }


        $appResp = ApiResponseToApp::create([
            'response_from_app' => json_encode($request->all()),
            'request_type' => 'A2Z Wallet',
            'usre_id' =>$request->userId,
            'api_id' => 5,
        ]);

        $authentication = $this->checkLoginAuthentication($request);
        if ($authentication['status'] != 1) {
            $appResp->response_to_app = json_encode($authentication);
            $appResp->save();
            return $authentication;
        }
        $userDetails = $authentication['userDetails'];
        $userId = $userDetails->id;


        if($userId !=4)
        {
            $isAcitveService = $this->isActiveService(5);
            if($isAcitveService){
                $response = ['status_id'=>0,'message'=>$isAcitveService->message];
                $appResp->response_to_app = json_encode($response);
                $appResp->save();
                return response()->json($response);
            }

        }

        $statusId=3;
        $beneName = trim($request->beneName);
        $ifsc = trim($request->ifsc);
        $bankCode = substr($ifsc, 0, 4);
        $isBankDetails = Masterbank::select('bank_status')->where('bank_code',$bankCode)->first();
        if($isBankDetails=='')
        {

            $response = ['status' => 2, 'message' => 'Bank code is not found. Please contact with Admin'];
            $appResp->response_to_app = json_encode($response);
            $appResp->save();
            return response()->json($response);

        }
        if($isBankDetails->bank_status ==0){
            $response = ['status' => 2, 'message' => 'Bank is down. Please try againg after some time'];
            $appResp->response_to_app = json_encode($response);
            $appResp->save();
            return response()->json($response);
        }

        $bank_account = trim($request->bank_account);
        $mobile_number = trim($request->mobile_number);
        $tramoBeneId = trim($request->beneficiary_id);
        $amount = $request->amount;
        $duplicatae = $this->checkDuplicateTransaction($bank_account,$amount,$userId,$mobile_number,5);
        if($duplicatae['status'] == 2){
            $response = $duplicatae;
            $appResp->response_to_app = json_encode($response);
            $appResp->save();
            return response()->json($response);
        }

        $walletScheme = PremiumWalletScheme::where('min_amt','<=',$amount)->where('max_amt','>=',$amount)->where('wallet_scheme_id',$userDetails->member->dmt_two_wallet_scheme)->first();
        if($walletScheme==''){
            $response = ['status'=>0,'message'=>"Your commission is not configured."];
            $appResp->response_to_app = json_encode($response);
            $appResp->save();
            return response()->json($response);
        }

        elseif($walletScheme->is_error){
            $response = ['status'=>0,'message'=>'Error in Setting. Please Call to admin'];
            $appResp->response_to_app = json_encode($response);
            $appResp->save();
            return response()->json($response);
        }
        $beneficiarydetail = Beneficiary::where(['benficiary_id'=>$tramoBeneId,'api_id'=>18])->first();
        if (!empty($beneficiarydetail))
        {
            $beneficiary_id = $beneficiarydetail->id;
        }
        else{
            $response = ['status' => 2, 'message' => 'Beneficiary details does not exist. Please Contact with admin'];
            $appResp->response_to_app = json_encode($response);
            $appResp->save();
            return response()->json($response);
        }
        $agentCharge = $this->agentCharge($amount,$walletScheme->agent_charge,$walletScheme->agent_charge_type);
        $agentComm =  $this->getCommission($amount,$walletScheme->agent_comm,$walletScheme->agent_comm_type);
        $agentData['credit_charge']= $agentComm;
        $agent_parent_id = $userDetails->parent_id;
        $agent_parent_role = $userDetails->parent->role_id;
        $dist_charge_data=$admin_charge_data=$md_charge_data=array();
        if($userDetails->parent_id == 1)
        {
            $d_id = $m_id ='';
            $a_id = 1;
            $admin_charge_data['credit_by'] = $userId;
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
                    $dist_charge_data['credit_by'] = $userId;
                    $dist_charge_data['credit_charge'] = $this->getCommission($amount,$walletScheme->dist_comm,
                        $walletScheme->dist_comm_type);
                    $dist_charge_data['debit_charge'] = 0;
                    $admin_charge_data['credit_by'] = $d_id;
                    $admin_charge_data['credit_charge'] = $this->getCommission($amount,$walletScheme->admin_comm,
                        $walletScheme->admin_comm_type);
                    $admin_charge_data['debit_charge'] = 0;
                }
                else
                {
                    $d_id=$agent_parent_id;
                    $m_id=$dist_parent_id;
                    $a_id = 1;

                    $dist_charge_data['credit_by'] = $userId;
                    $dist_charge_data['credit_charge'] = $this->getCommission($amount,$walletScheme->dist_comm,
                        $walletScheme->dist_comm_type);
                    $dist_charge_data['debit_charge'] = 0;

                    $md_charge_data['credit_by'] = $d_id;
                    $md_charge_data['credit_charge'] = $this->getCommission($amount,$walletScheme->md_comm,
                        $walletScheme->md_comm_type);
                    $md_charge_data['debit_charge'] = 0;

                    $admin_charge_data['credit_by'] = $m_id;
                    $admin_charge_data['credit_charge'] = $this->getCommission($amount,$walletScheme->admin_comm,
                        $walletScheme->admin_comm_type);
                    $admin_charge_data['debit_charge'] = 0;
                }
            }
            else if($agent_parent_role == 3)
            {
                $d_id='';
                $m_id=$agent_parent_id;
                $a_id = 1;

                $md_charge_data['credit_by'] = $userId;
                $md_charge_data['credit_charge'] = $this->getCommission($amount,$walletScheme->md_comm,
                    $walletScheme->md_comm_type);
                $md_charge_data['debit_charge'] = 0;

                $admin_charge_data['credit_by'] = $m_id;
                $admin_charge_data['credit_charge'] = $this->getCommission($amount,$walletScheme->admin_comm,
                    $walletScheme->admin_comm_type);
                $admin_charge_data['debit_charge'] = 0;
            }
        }

        $user_detail_balance = Balance::where('user_id',$userId)->select('user_balance')->first();
        $finalAmount =  $agentCharge+$amount;
        if($user_detail_balance->user_balance < $finalAmount)
        {
            $response =['status'=>0,'message'=>"In-sufficient Balance"];
            $appResp->response_to_app = json_encode($response);
            $appResp->save();
            return response()->json($response);
        }

        DB::beginTransaction();
        try
        {
            $finalAmount = $agentCharge + $amount - $agentComm;
            Balance::where('user_id',$userId)->decrement('user_balance',$finalAmount);
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
                'user_id' => $userId,
                'created_by'=>$userId,
                'customer_number' => $mobile_number,
                'opening_balance' => $user_detail_balance->user_balance,
                'total_balance' => Balance::where('user_id',$userId)->first()->user_balance,
                'bulk_amount'=>$amount,
                'biller_name'=>$request->senderName,
                'gst' => 0,
                'tds' => 0,
                'debit_charge' => $agentCharge,
                'credit_charge' => $agentComm,
                'beneficiary_id' => $beneficiary_id,
                'channel' => 2,
                'mode' => 'APP',
            ]);
            $insert_id = $record->id;
            DB::commit();
        }
        catch(Exception $e)
        {
            DB::rollback();
            $response =['status' => 2, 'message' => 'Something went wrong. Please try again...'];
            $appResp->response_to_app = json_encode($response);
            $appResp->save();
            return response()->json($response);
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
        $apiResp = Apiresponse::create(['api_id'=>5,'user_id'=>$userId,'api_type'=>"TRANSACTION",
            'report_id' =>$record->id,'request_message'=>json_encode($content)]);
        if($statusId == 3)
        {
			//dummy response
			//$apiResponse = '{"status":1,"message":"SUCCESS","txnId":40867,"refId":1547208685}';
			
            $url = config('constants.TRAMO_DMT_URL') ."/transaction";//die;
            $apiResponse= $this->getCurlPostMethod($url,$content);
           
            $apiResp->message=$apiResponse;
            $apiResp->save();
            try
            {
                $result =json_decode($apiResponse);
                if($result->status==1)
                {
                    $record->status_id=1;
                    $record->txnid=$result->txnId;
                    $record->save();
                    $this->creditCommission($d_id,$m_id,$a_id,$userId,$mobile_number,$beneficiary_id,2,$insert_id,$amount,$dist_charge_data,$md_charge_data,$admin_charge_data,$bank_account,$record,5);

                    $msg ="Dear Customer, Your Txn is successful to Acc No." .$bank_account ." with Amount Rs " .$amount;
                    $message = urlencode($msg);
                    //$this->sendSMS($mobile_number,$message,1);
                    $response =['txnId'=>$insert_id,'refNo'=>$result->txnId,'status'=>1,'message'=>"Transaction Success"];
                    $appResp->response_to_app = json_encode($response);
                    $appResp->save();
                    return response()->json($response);
                }
                elseif($result->status==2)
                {
                    $record->txnid=$result->txnId;
                    $record->bank_ref=$result->refId;
                    $record->status_id=2;
                    DB::beginTransaction();
                    try
                    {

                        Balance::where('user_id',$userId)->increment('user_balance',$finalAmount);
                        $record->total_balance=Balance::where('user_id',$userId)->select('user_balance')
                            ->first()->user_balance;
                        $record->type="DR/CR";
                        $record->save();
                        DB::commit();
                    }
                    catch(Exception $e)
                    {
                        DB::rollback();
                        $response =['txnId'=>$insert_id,'refNo'=>$result->txnId,'status'=>3,'message'=>"Transaction Pending"];
                        $appResp->response_to_app = json_encode($response);
                        $appResp->save();
                        return response()->json($response);

                    }
                    $response =['status'=>2,'message'=>"Transaction Failed"];
                    $appResp->response_to_app = json_encode($response);
                    $appResp->save();
                    return response()->json($response);

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

                        Balance::where('user_id',$userId)->increment('user_balance',$finalAmount);
                        $record->type="DR/CR";
                        $record->total_balance=Balance::where('user_id',$userId)->select('user_balance')
                            ->first()->user_balance;
                        $record->save();
                        DB::commit();
                    }
                    catch(Exception $e)
                    {
                        DB::rollback();
                        $response =['txnId'=>$insert_id,'refNo'=>'','status'=>3,
                            'message'=>"Transaction Pending"];
                        $appResp->response_to_app = json_encode($response);
                        $appResp->save();
                        return response()->json($response);

                    }
                    $response =['status'=>2,'message'=>"Transaction Failed"];
                    $appResp->response_to_app = json_encode($response);
                    $appResp->save();
                    return response()->json($response);
                }
                elseif($result->status==3)
                {
                    $record->txnid=$result->txnId;
                    $record->save();
                    $this->creditCommission($d_id,$m_id,$a_id,$userId,$mobile_number,$beneficiary_id,2,$insert_id,$amount,$dist_charge_data,$md_charge_data,$admin_charge_data,$bank_account,$record,5);


                    $response =['txnId'=>$insert_id,'refNo'=>$result->txnId,'status'=>3,'message'=>"Transaction Pending"];
                    $appResp->response_to_app = json_encode($response);
                    $appResp->save();
                    return response()->json($response);
                }
                elseif($result->status==59)
                {
                    $record->status_id=18;
                    $record->txnid=$result->txnId;
                    $record->save();
                    $this->creditCommission($d_id,$m_id,$a_id,$userId,$mobile_number,$beneficiary_id,2,$insert_id,$amount,$dist_charge_data,$md_charge_data,$admin_charge_data,$bank_account,$record,5);


                    $response =['txnId'=>$insert_id,'refNo'=>$result->txnId,'status'=>18,'message'=>"Transaction Inprocess"];
                    $appResp->response_to_app = json_encode($response);
                    $appResp->save();
                    return response()->json($response);
                }
                else
                {
                    $this->creditCommission($d_id,$m_id,$a_id,$userId,$mobile_number,$beneficiary_id,2,$insert_id,$amount,$dist_charge_data,$md_charge_data,$admin_charge_data,$bank_account,$record,5);

                    $response =['txnId'=>$insert_id,'refNo'=>'','status'=>3,'message'=>"Transaction Pending"];
                    $appResp->response_to_app = json_encode($response);
                    $appResp->save();
                    return response()->json($response);

                }

            }
            catch(Exception $e)
            {
                $response =['txnId'=>$insert_id,'refNo'=>'','status'=>3,'message'=>"Transaction Pending"];
                $appResp->response_to_app = json_encode($response);
                $appResp->save();
                return response()->json($response);
            }
        }
        else{
            $this->creditCommission($d_id,$m_id,$a_id,$userId,$mobile_number,$beneficiarydetail,2,$insert_id,$amount,$dist_charge_data,$md_charge_data,$admin_charge_data,$bank_account,$reportDetails,5);

            $response =['txnId'=>$insert_id,'refNo'=>'','status'=>3,'message'=>"Transaction Pending"];
            $appResp->response_to_app = json_encode($response);
            $appResp->save();
            return response()->json($response);
        }
    }





    private function checkDuplicateTransaction($accountNo,$amount,$userId,$mobileNo,$apiId)
    {
        $formatted_date = date("Y-m-d H:i:s");
        $start_time = date('Y-m-d H:i:s',strtotime('-300 seconds',strtotime($formatted_date)));
        $result = DB::table('reports')->select('number', 'amount', 'user_id', 'created_at')->where(['number'=>$accountNo,'amount'=> $amount,'user_id'=>$userId,'customer_number'=>$mobileNo])->whereIn('status_id',[1,3])->where('api_id',$apiId)->where('created_at', '>=', $start_time)->orderBy('created_at', 'desc')->first();
        if ($result) {
            return array('status' => 2, 'message' => 'Same Amount, same account and same mobile transaction is found, Try again after 5 Minutes');
        } else {
            return array('status' => 0, 'message' => 'not found');
        }
    }
    private function deniedSameTransactionInDay($accountNo, $amount, $userId, $mobileNo)
    {
        $formatted_date = date("Y-m-d") . " 00:00:00";
        $result = DB::table('reports')->select('number', 'amount', 'user_id',
            'created_at')->where(['number' => $accountNo, 'amount' => $amount, 'user_id' => $userId,
            'customer_number' => $mobileNo, 'api_id' => 5])->whereIn('status_id', [1, 3])->where('created_at', '>=', $formatted_date)->orderBy('created_at', 'desc')->first();
        if ($result) {
            return array('status' => 2,
                'message' => 'Same account and same amount transaction at same day not allowed');
        } else {
            return array('status' => 0, 'message' => 'not found');
        }
    }


    private function agentCharge($amount,$charge,$chargeType)
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
    }

    private function creditCommissionBkb($d_id, $m_id, $a_id, $user_id, $mobile_number, $bene_id, $channel, $insert_id, $amount, $dist_charge_data, $md_charge_data, $admin_charge_data, $bank_account)
    {
        DB::beginTransaction();
        try {
            if ($d_id != '' && count($dist_charge_data)) {
                $cr_amt = $dist_charge_data['credit_charge'];

                $dist_charge_data['debit_charge'] = 0;
                $dist_charge_data['gst'] = 0;
                $dist_charge_data['tds'] = 0;
                Balance::where('user_id', $d_id)->increment('user_balance', $cr_amt);
                $dist_charge_data['number'] = $bank_account;
                $dist_charge_data['amount'] = $amount;
                $dist_charge_data['provider_id'] = 41;
                $dist_charge_data['profit'] = 0;
                $dist_charge_data['api_id'] = 5;
                $dist_charge_data['status_id'] = 22;
                $dist_charge_data['description'] = "DMR_COMMISSION";
                $dist_charge_data['pay_id'] = time();
                $dist_charge_data['user_id'] = $d_id;
                $dist_charge_data['txnid'] = $insert_id;
                $dist_charge_data['customer_number'] = $mobile_number;
                $dist_charge_data['total_balance'] = Balance::where('user_id', $d_id)->select('user_balance')->first()->user_balance;
                $dist_charge_data['beneficiary_id'] = $bene_id;
                $dist_charge_data['channel'] = $channel;
                $dist_charge_data['mode'] = 'APP';
                Report::create($dist_charge_data);


            }
            if ($m_id != '' && count($md_charge_data)) {
                $mcr_amt = $md_charge_data['credit_charge'];

                $md_charge_data['gst'] = 0;
                $md_charge_data['tds'] = 0;
                Balance::where('user_id', $m_id)->increment('user_balance', $mcr_amt);
                $md_charge_data['number'] = $bank_account;
                $md_charge_data['amount'] = $amount;
                $md_charge_data['provider_id'] = 41;
                $md_charge_data['debit_charge'] = 0;

                $md_charge_data['profit'] = 0;
                $md_charge_data['api_id'] = 5;
                $md_charge_data['status_id'] = 22;
                $md_charge_data['description'] = "DMT_COMMISSION";
                $md_charge_data['pay_id'] = time();
                $md_charge_data['user_id'] = $m_id;
                $md_charge_data['txnid'] = $insert_id;
                $md_charge_data['customer_number'] = $mobile_number;
                $md_charge_data['total_balance'] = Balance::select('user_balance')->where('user_id', $m_id)->first()->user_balance;
                $md_charge_data['beneficiary_id'] = $bene_id;
                $md_charge_data['channel'] = $channel;
                $md_charge_data['mode'] = 'APP';
                Report::create($md_charge_data);

            }
            if ($a_id != '' && count($admin_charge_data)) {
                $cr_amt = $admin_charge_data['credit_charge'];
                $admin_charge_data['gst'] = 0;
                $admin_charge_data['tds'] = 0;
                Balance::where('user_id', $a_id)->increment('user_balance', $cr_amt);
                $admin_charge_data['number'] = $bank_account;
                $admin_charge_data['amount'] = $amount;
                $admin_charge_data['provider_id'] = 41;
                $admin_charge_data['debit_charge'] = 0;

                $admin_charge_data['profit'] = 0;
                $admin_charge_data['api_id'] = 5;
                $admin_charge_data['status_id'] = 22;
                $admin_charge_data['description'] = "DMT_COMMISSION";
                $admin_charge_data['pay_id'] = time();
                $admin_charge_data['user_id'] = $a_id;
                $admin_charge_data['txnid'] = $insert_id;
                $admin_charge_data['customer_number'] = $mobile_number;
                $admin_charge_data['total_balance'] = Balance::select('user_balance')->where('user_id', $a_id)->first()->user_balance;
                $admin_charge_data['beneficiary_id'] = $bene_id;
                $admin_charge_data['channel'] = $channel;
                $admin_charge_data['mode'] = 'APP';
                Report::create($admin_charge_data);
            }
            DB::commit();
        } catch (Exception $e) {
            DB::rollback();
            TransactionReportException::create(['report_id' => $insert_id, 'exception' => $e->getMessage(), 'exception_type' => "DURING GIVEN COMMISSION", 'dist_data' => json_encode($dist_charge_data), 'md_data' => json_encode($md_charge_data), 'admin_data' => json_encode($admin_charge_data), 'other' => json_encode(array('dist_id' => $d_id, 'm_id' => $m_id, 'a_id' => $a_id))]);
        }

    }

    public function deleteBeneficiaryRequest(Request $request)
    {
        $rules = array(
            'beneId' => 'required|regex:/^[0-9]+$/',
            'userId' => 'required',
            'token' => 'required',
        );


        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return Response::json(['status' => 10, 'message' => $validator->errors()->getMessages()]);

        }

        $authentication = $this->checkLoginAuthentication($request);
        if ($authentication['status'] != 1)
            return $authentication;


        $content = array('api_token' => config('constants.TRAMO_API_KEY'), 'userId' => config('constants.TRAMO_USER_ID'), 'beneId' => $request->beneId);
        $url = config('constants.TRAMO_DMT_URL') . "/bene-delete-request";
        $response = $this->getCurlPostMethod($url, $content);
        return $response;
    }

    public function deleteBeneficiaryThroughOtp(Request $request)
    {
        //print_r($request->all());die;
        $rules = array(
            'beneId' => 'required|numeric|regex:/^[0-9]+$/',
            'otp' => 'required|numeric|regex:/^[0-9]+$/',
            'userId' => 'required',
            'token' => 'required',
        );
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return Response::json(['status' => 10, 'message' => $validator->errors()->getMessages()]);
        }


        $authentication = $this->checkLoginAuthentication($request);
        if ($authentication['status'] != 1)
            return $authentication;

        $content = array('api_token' => config('constants.TRAMO_API_KEY'), 'userId' => config('constants.TRAMO_USER_ID'), 'beneId' => $request->beneId, 'otp' => $request->otp);
        $url = config('constants.TRAMO_DMT_URL') . "/bene-delete-confirm-otp";
        $response = $this->getCurlPostMethod($url, $content);
        return $response;
    }

    public function checkImpsTransactionCurrentStatus(Request $request)
    {
        return CustomTraits::checkStatus($request->id);
    }

    public function sendRefundTxnOtp(Request $request)
    {
        try {
            $report = Report::findOrFail($request->recordId);
        } catch (Exception $e) {
            return response()->json(['status' => 0, 'message' => "No Record Found"]);
        }

        $authentication = $this->checkLoginAuthentication($request);
        if ($authentication['status'] != 1)
            return $authentication;


        $content = array('api_token' => config('constants.TRAMO_API_KEY'), 'userId' => config('constants.TRAMO_USER_ID'), 'txnId' => $report->txnid);
        $url = config('constants.TRAMO_DMT_URL') . "/send-refund-txn-otp?" . $content;//die;
        return $this->getCurlGetMethod($url);
    }

    public function transactionRefundRequest(Request $request)
    {

        $report = Report::find($request->recordId);
        $content = array('api_token' => config('constants.TRAMO_API_KEY'), 'userId' => config('constants.TRAMO_USER_ID'), 'txnId' => $report->txnid, 'otp' => $request->otp);
        $url = config('constants.TRAMO_DMT_URL') . "/txn-refund-request";
        $response = $this->getCurlPostMethod($url, $content);
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

    /*worked by Akash kumar das*/
    public function getBankList(Request $request)
    {

        $rules = array('token' => 'required', 'userId' => 'required',);
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return Response::json(array(
                'status' => '3',
                'message' => $validator->getMessageBag()->toArray()
            )); // 400 being the HTTP code for an invalid request.
        }


        $loggedInUser = $this->checkLoginAuthentication($request);
        if ($loggedInUser) {
            $bank = Masterbank::pluck('bank_name', 'ifsc')->toArray();
            return Response::json(array('status' => '1', 'bankList' => $bank));
        }
        return $loggedInUser;
    }

}
