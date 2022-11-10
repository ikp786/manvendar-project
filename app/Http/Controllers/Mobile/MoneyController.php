<?php

namespace App\Http\Controllers\Mobile;

use App\ApiResponseToApp;
use App\Http\Controllers\InstantPayController;
use App\Library\CyberDMT;
use App\Loadcash;
use App\Remark;
use Illuminate\Http\Request;
use App\Masterbank;
use App\VerificationScheme;
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
use App\Traits\CustomAuthTraits;
use DateTime;

class MoneyController extends Controller
{
    use CustomTraits, CustomAuthTraits,ReportTraits;

    public function index(Request $request)
    {
        $rules = array(
            'userId' => 'required|numeric',
            'token' => 'required',
        );
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return Response::json(array(
                'status' => 10,
                'message' => $validator->getMessageBag()->toArray()
            ));
        }
        $loggedInUser = $this->checkLoginAuthentication($request);
        if ($loggedInUser) {
            if ($loggedInUser->role_id == 5 && $loggedInUser->member->dmt_one == 1) {
                $masterBank = Masterbank::pluck(['bank_name', 'bank_code'])->toArray();
                return response()->json(['status' => 1, 'bankList' => $masterBank]);
            }
            return response()->json(['status' => 2, 'message' => "DMT 1 Service is not allow"]);
        }
        return $loggedInUser;
    }

    public function fetchCustomerDetails(Request $request)
    {
        $rules = array(
            'mobileNumber' => 'required|numeric|min:10',
            'userId' => 'required|numeric',
            'token' => 'required',
        );
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return Response::json(array(
                'status' => 10,
                'message' => $validator->getMessageBag()->toArray()
            ));
        }
        $authentication = $this->checkLoginAuthentication($request);
        if ($authentication['status'] == 1) {
            $loggedInUser = $authentication['userDetails'];
            if ($loggedInUser->id != 4) {
                $isAcitveService = $this->isActiveService(4);
                if ($isAcitveService)
                    return response()->json(['status' => 201, 'message' => $isAcitveService->message]);
            }
            $mobileNumber = $request->mobileNumber;
            $cyber = new CyberDMT;
            $response = $cyber->verification($mobileNumber);
            $new = json_decode($response);
            if ($new->status == 'Transaction Successful') {

                foreach ($new->data->beneficiary as $data) {

                    if (Beneficiary::where(['benficiary_id' => $data->id, 'api_id' => 0])->exists()) {

                    } else {
                        Beneficiary::create(['benficiary_id' => $data->id,
                            'account_number' => $data->account,
                            'ifsc' => $data->ifsc,
                            'bank_name' => $data->bank,
                            'customer_number' => $mobileNumber,
                            'mobile_number' => $data->mobile,
                            'vener_id' => 1,
                            'api_id' => 0,
                            'user_id' => $loggedInUser->id,
                            'name' => $data->name
                        ]);
                    }

                }
            }
            return $response;
        }
        return $authentication;
    }

    function senderRegistration(Request $request)
    {
        $rules = array(
            'mobileNumber' => 'required|numeric|digits:10|regex:/^[0-9]+$/',
            'firstName' => 'required|regex:/^[A-Za-z ]+$/',
            'lastName' => 'required|regex:/^[A-Za-z ]+$/',
            'pinCode' => 'required|numeric|digits:6|regex:/^[0-9]+$/',
            'userId' => 'required|numeric',
            'token' => 'required',
        );
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return Response::json(array(
                'status' => 10,
                'errors' => $validator->getMessageBag()->toArray(),
                'message' => 'Missing Parameters'
            ));
        }
        $authentication = $this->checkLoginAuthentication($request);
        if ($authentication['status'] == 1) {
            $NUMBER = $request->mobileNumber;
            $fName = $request->firstName;
            $lName = $request->lastName;
            $Pin = $request->pinCode;
            $parameter_cyber = [$NUMBER, $fName, $lName, $Pin];
            $cyber = new CyberDMT;
            $response = $cyber->add_sender($parameter_cyber);
            return $response;
        }
        return $authentication;
    }

    function senderVerification(Request $request)
    {
        $rules = array(
            'mobileNumber' => 'required|numeric|digits:10|regex:/^[0-9]+$/',
            'remitterOTP' => 'required|numeric|regex:/^[0-9]+$/',
            'remitterVerifyId' => 'required|numeric|regex:/^[0-9]+$/',
            'userId' => 'required|numeric',
            'token' => 'required',
        );
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return Response::json(array(
                'status' => 10,
                'message' => $validator->getMessageBag()->toArray()
            ));
        }
        $authentication = $this->checkLoginAuthentication($request);
        if ($authentication['status'] == 1) {
            $NUMBER = $request->mobileNumber;
            $remitterOTP = $request->remitterOTP;
            $remitterVerifyId = $request->remitterVerifyId;
            $parameter_cyber = [$NUMBER, $remitterOTP, $remitterVerifyId];
            $cyber = new CyberDMT;
            $response = $cyber->verifySenderOtp($parameter_cyber);
            return $response;
        }
        return $authentication;
    }

    function getBankList(Request $request)
    {

        $authentication = $this->checkLoginAuthentication($request);
        if ($authentication['status'] == 1) {
            $bank = Masterbank::pluck('bank_name', 'ifsc')->toArray();
            return Response::json(array('success' => 1, 'status' => 'Success', 'bankList' => $bank));
        }
        return $authentication;
    }

    function getIFSCCode(Request $request)
    {
        $rules = array('bankId' => 'request');
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return Response::json(array(
                'status' => 10,
                'message' => $validator->getMessageBag()->toArray()
            ));
        } else {

            $authentication = $this->checkLoginAuthentication($request);
            if ($authentication['status'] == 1) {

                $bank_code = $request->bank_code;
                if ($bank_code == '') {
                    $bank_code = $request->bankcode;
                }

                $data = Masterbank::find($request->bankId)->first();
                if ($data->ifsc == '') {
                    $status = 0;
                } else {
                    $status = 1;
                }
                return response()->json(['status' => $status, 'ifsc' => $data->ifsc]);
            } else return $authentication;
        }
    }




    function beneficiaryDelete(Request $request)
    {

        $rules = array(
            'remitterId' => 'required|numeric|min:10',
            'beneficiaryId' => 'required|numeric',
        );
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return Response::json(array(
                'status' => 10,
                'message' => $validator->getMessageBag()->toArray()
            ));
        }
        $authentication = $this->checkLoginAuthentication($request);
        if ($authentication['status'] == 1) {
                $remId = $request->remitterId;
                $benId = $request->beneficiaryId;
                $parameter_cyber = [$remId, $benId];
                $cyber = new \App\Library\CyberDMT;
                $response = $cyber->delete_beneficiary($parameter_cyber);
                return $response;
        }
        return $authentication;
    }

    function beneficiaryDeleteOtp(Request $request)
    {

        $rules = array(
            'remitterId' => 'required|numeric',
            'beneficiaryId' => 'required|numeric',
            'otp' => 'required|numeric',
        );
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return Response::json(array(
                'status' => 10,
                'message' => $validator->getMessageBag()->toArray()
            ));
        }
        $authentication = $this->checkLoginAuthentication($request);
        if ($authentication['status'] == 1) {

            $remId = $request->remitterId;
            $benId = $request->beneficiaryId;
            $otc = $request->otp;
            $parameter_cyber = [$remId, $benId, $otc];
            $cyber = new \App\Library\CyberDMT;
            $response = $cyber->bene_confirm_delete($parameter_cyber);
            return $response;
        }
        return $authentication;
    }


    function get_commission($scheme_id, $provider)
    {
        return Commission::where('provider_id', $provider)->where('scheme_id', $scheme_id)->first();
    }

    public function cyber($user_id, $number, $provider_id, $amount, $insert_id, $account, $cycle)
    {
        $cyber = new \App\Library\Cyber;
        $data = $cyber->CallRecharge($provider_id, $insert_id, $number, $amount, $user_id, $account, $cycle);

        return $data;
    }

    function api_route($number, $provider, $amount, $user_id)
    {
        $provider_detail = Provider::find($provider);
        return $provider_detail;
    }


    /*worked by akash kumar das ) on date:14-05-2019*/
    function add_beneficiary(Request $request)
    {

        $rules = array(
            'remId' => 'required|numeric',
            'fName' => 'required',
            'number' => 'required|numeric',
            'benAccount' => 'required|numeric',
            'benIFSC' => 'required',
            'userId' => 'required|numeric',
            'token' => 'required',
        );

        $isValidate = Validator::make($request->all(), $rules);
        if ($isValidate->fails()) {
            return Response::json(array(
                'status' => 10,
                'message' => $isValidate->getMessageBag()->toArray()
            ));
        }
        $authentication = $this->checkLoginAuthentication($request);
        if ($authentication['status'] == 1) {

            $remId = $request->remId;
            $fName = $request->fName;
            $lName = "";
            $NUMBER = $request->number;
            $benAccount = $request->benAccount;
            $benIFSC = $request->benIFSC;
            if ($remId && $fName && $NUMBER && $benAccount && $benIFSC) {
                $parameter_cyber = [$remId, $fName, $lName, $NUMBER, $benAccount, $benIFSC];
                $cyber = new CyberDMT;
                $response = $cyber->add_beneficiary($parameter_cyber);
                return $response;
            } else {
                return Response()->json(['status' => 'failure', 'message' => 'All Filed Required']);
            }
        } else return $authentication;
    }

    function account_name_infoBkp(Request $request)
    {

        $rules = array(
            'token' => 'required',
            'userId' => 'required',
            'ifsc' => 'required',
            'bankCode' => 'required',
            'mobile_number' => 'required|numeric',
            'bank_account' => 'required',
        );


        $isValidate = Validator::make($request->all(), $rules);
        if ($isValidate->fails()) {
            return Response::json(array(
                'status' => 10,
                'message' => $isValidate->getMessageBag()->toArray()
            ));
        }


        $authentication = $this->checkLoginAuthentication($request);
        if ($authentication['status'] == 1) {
            $userdetails = $authentication['userDetails'];
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

            $retailer = 4;

            $user_id = $userdetails->id;
            $balance = Balance::where('user_id', $user_id)->first();
            $user_balance = $balance->user_balance;
            $limit = 0;
            $sumamount = $retailer + $limit;
            if ($user_balance >= $sumamount && $retailer >= 4) {
                //return'{"statuscode":"TXN","status":"Transaction Successful","data":{"remarks":"Transaction Successful","bankrefno":913420001336,"ipay_id":"1190514200054BRAGD","benename":"TRAMO TECHNOLAB PRIV","locked_amt":0,"charged_amt":2.18,"verification_status":"VERIFIED"}}';
                $parameter_cyber = [$bank_account, $request->ifsc, $mobile_number];
                $cyber = new CyberDMT;
                $response = $cyber->account_name_info($parameter_cyber);
                $res = json_decode($response);
                if (!empty($res->statuscode)) {
                    $status = $res->statuscode;
                    if ($status == 'TXN') {
                        $beneficiary_id = Beneficiary::insertGetId(['benficiary_id' => 0,
                            'account_number' => $bank_account,
                            'ifsc' => $ifsc,
                            'bank_code' => $bank_code,
                            'bank_name' => $bankcode,
                            'customer_number' => $mobile_number,
                            'vener_id' => 1,
                            'user_id' => $userdetails->id,
                            'name' => $res->data->benename]);
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
                            'api_id' => 2,
                            'profit' => 0,
                            'credit_charge' => 0,
                            'debit_charge' => 0,
                            'status_id' => 1,
                            'txnid' => 'Account Verification',
                            'description' => $res->data->benename,
                            'pay_id' => $datetime,
                            'created_at' => $ctime,
                            'user_id' => $user_id,
                            'channel' => 2,
                            'mode' => "APP",
                            'beneficiary_id' => $beneficiary_id,
                            'total_balance' => $user_balance,
                        ]);

                        return $response;
                    } else {
                        return Response()->json(['status' => 'failure', 'message' => 'Server Not Responding Please Try After Sometime']);
                    }
                } else
                    return Response()->json(['status' => 'failure', 'message' => 'Server Not Responding Please Try After Sometime']);
            } else {
                return Response()->json(['status' => 'failure', 'message' => 'Your Balance Is Low Please Refill Your Wallet']);
            }
        } else return $authentication;
    }

function account_name_info (Request $request)
    {

        $rules = array(
            'token' => 'required',
            'userId' => 'required',
            'ifsc' => 'required',
            'bankCode' => 'required',
            'mobile_number' => 'required|numeric:digits:10',
            'bank_account' => 'required',
        );


        $isValidate = Validator::make($request->all(), $rules);
        if ($isValidate->fails()) {
            return Response::json(array(
                'status' => "failure",
                'message' => $isValidate->getMessageBag()->toArray()
            ));
        }


        $authentication = $this->checkLoginAuthentication($request);
        if ($authentication['status'] != 1) return $authentication;
        $userDetails = $authentication['userDetails'];
        $userId = $userDetails->id;
		$ifsc = $request->ifsc;
        $bankName = $request->bankcode;
        $mobile_number = $request->mobile_number;
        $bank_account = $request->bank_account;
        $balance = Balance::where('user_id', $userId)->first();

        $walletScheme = VerificationScheme::where('wallet_scheme_id',$userDetails->member->verification_scheme)->first();
        if($walletScheme=='')
        {
            $result=['status' => 'failure', 'message' => 'Surcharge is not configured'];
            return response()->json(['result' => $result]);
        }
        $agentCharge = $walletScheme->agent_charge;
        $agent_parent_id = $userDetails->parent_id;
        $dist_charge_data = $admin_charge_data = $md_charge_data=array();
        $agent_parent_role = $userDetails->parent->role_id;
        if($userDetails->parent_id == 1)
        {
            $d_id = $m_id ='';
            $a_id = 1;
            $admin_charge_data['credit_by'] = $userId;
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
                    $dist_charge_data['credit_by'] = $userId;
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

                    $dist_charge_data['credit_by'] = $userId;
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

                $md_charge_data['credit_by'] = $userId;
                $md_charge_data['credit_charge'] = $walletScheme->md_comm;
                $md_charge_data['debit_charge'] = 0;

                $admin_charge_data['credit_by'] = $m_id;
                $admin_charge_data['credit_charge'] = $walletScheme->admin_comm;
                $admin_charge_data['debit_charge'] = 0;
            }
        }
        $balance = Balance::where('user_id', $userId)->first();
        $user_balance = $balance->user_balance;
        if ($user_balance >= $agentCharge)
        {
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
                'tds'=>0,
                'pay_id' => time(),
                'description' => $bankName . ' ( ' . $ifsc .' )',
                'user_id' => $userId,
                'created_by' => $userDetails->id,
                'channel' => 2,
                'opening_balance' => $user_balance,
                'total_balance' => $user_balance - $agentCharge,
                'customer_number' => $mobile_number,
                'mode' => "APP",
            ]);
            $existBene = Beneficiary::where(['account_number'=>$bank_account,'ifsc'=>$ifsc,'is_bank_verified'=>1])->first();
			$insert_id = $report->id;
			$beneficiary_id='';
            if($existBene)
            {

				$report->api_id = 17;
				$report->status_id = 1;
				$report->biller_name = $existBene->name;
				$report->save();
				
				$apiId=17;
				$this->createVEntry($d_id,$mobile_number,$beneficiary_id,2,$insert_id,$dist_charge_data,$bank_account,$apiId,"APP");
				$this->createVEntry($m_id,$mobile_number,$beneficiary_id,2,$insert_id,$md_charge_data,$bank_account,$apiId,"APP");
				$this->createVEntry($a_id,$mobile_number,$beneficiary_id,2,$insert_id,$admin_charge_data,$bank_account,$apiId,"APP");
				 return Response()->json(['status' => "success", 'message' => 'Success','beneName'=>$existBene->name]);

            }
            $apiId=2;
            $token = config('constants.INSTANT_KEY');
            $content="{\"token\":\"$token\",\"request\":{\"remittermobile\":\"$mobile_number\",\"account\":\"$bank_account\",\"ifsc\":\"$ifsc\",\"agentid\":\"$insert_id\",\"outletid\":1}}";
            $instantPay = new \App\Library\InstantPayDMT;
            $data = $instantPay->accountNumberVerification($content,$userId,$insert_id);
            
            try
            {
                $res = json_decode($data);
            }
            catch(Exception $e)
            {
                return Response()->json(['status' => "failure", 'status' => 'Server Not Responding Please Try After Sometime']);
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
					Beneficiary::where(['account_number'=>$bank_account,'ifsc'=>$ifsc])->update(['name'=>$res->data->benename,'is_bank_verified'=>1]);
					$this->createVEntry($d_id,$mobile_number,$beneficiary_id,2,$insert_id,$dist_charge_data,$bank_account,$apiId,"APP");
					$this->createVEntry($m_id,$mobile_number,$beneficiary_id,2,$insert_id,$md_charge_data,$bank_account,$apiId,"APP");
					$this->createVEntry($a_id,$mobile_number,$beneficiary_id,2,$insert_id,$admin_charge_data,$bank_account,$apiId,"APP");
                   return Response()->json(['status' => "success", 'message' => 'Success','beneName'=>$res->data->benename]);
                }
                else
                {
                    return Response()->json(['status' => "pending", 'message' => 'Verification is pending','beneName'=>'']);
                }
            }
			else if ($res->statuscode =="TUP")
			{
				if($res->status =="Transaction Under Process")
				{
					
					$report->txnid = $res->data->ipay_id;
					$report->save();
					$this->createVEntry($d_id,$mobile_number,$beneficiary_id,2,$insert_id,$dist_charge_data,$bank_account,$apiId,"APP");
					$this->createVEntry($m_id,$mobile_number,$beneficiary_id,2,$insert_id,$md_charge_data,$bank_account,$apiId,"APP");
					$this->createVEntry($a_id,$mobile_number,$beneficiary_id,2,$insert_id,$admin_charge_data,$bank_account,$apiId,"APP");
					return Response()->json(['status' => "pending", 'message' => 'Tranaction Under process','beneName'=>'']);
				}
				else
				{
					return Response()->json(['status' => "pending", 'message' => 'Verification is pending','beneName'=>'']);
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
                return Response()->json(['status' => "failure", 'message' => $message,'beneName'=>'']);
            }
            else
            {
				$this->createVEntry($d_id,$mobile_number,$beneficiary_id,2,$insert_id,$dist_charge_data,$bank_account,$apiId,"APP");
				$this->createVEntry($m_id,$mobile_number,$beneficiary_id,2,$insert_id,$md_charge_data,$bank_account,$apiId,"APP");
				$this->createVEntry($a_id,$mobile_number,$beneficiary_id,2,$insert_id,$admin_charge_data,$bank_account,$apiId,"APP");
                return Response()->json(['status' => "pending", 'message' => 'Verification is pending','beneName'=>'']);
            }}
        else{
            return Response()->json(['status' => 'failure', 'message' => 'Your Balance Is Low Please Refill Your Wallet','beneName'=>'']);
        }
    }

    public function getAgentChargeAmount(Request $request)
    {

        $rules = array(
            'token' => 'required',
            'userId' => 'required',
            'amount' => 'required',
            'txnChargeApiName' => 'required',
        );

        $isValidate = Validator::make($request->all(), $rules);
        if ($isValidate->fails()) {
            return Response::json(array(
                'status' => 10,
                'message' => "Missing Param",
                'errors' => $isValidate->getMessageBag()->toArray()
            ));
        }


        $authentication = $this->checkLoginAuthentication($request);
        if ($authentication['status'] != 1)
            return $authentication;
        $userDetail = $authentication['userDetails'];

        if ($request->amount > $userDetail->balance->user_balance)
            return response()->json(['result' => '', 'txn_pin' => '', 'totalAmount' => '', 'status' => 0, 'message' => "Low balance"]);
        $req_amount = $amount = $request->amount;
        if ($request->txnChargeApiName == "TRAMO") {
            $walletScheme = PremiumWalletScheme::where('min_amt', '<=', $amount)->where('max_amt', '>=', $amount)
                ->where('wallet_scheme_id', $userDetail->member->dmt_two_wallet_scheme)->first();
            $charge = $walletScheme->agent_charge;
            $totalAmount = $amount + $charge;
            $result[1] = array('charge' => $charge, 'txnAmount' => $amount, 'total' => $totalAmount);
            if ($totalAmount > $userDetail->balance->user_balance)
                return response()->json(['result' => $result, 'txn_pin' => $userDetail->profile->txn_pin, 'totalAmount' => $totalAmount, 'status' => 0, 'message' => "Low balance"]);
            return response()->json(['result' => $result, 'txn_pin' => $userDetail->profile->txn_pin, 'totalAmount' => $totalAmount, 'status' => 1, 'message' => "Ok"]);
        } else {
            $no_of_ite = ceil($amount / 5000);
            $result = array();
            $totalAmount = 0;
            if ($no_of_ite <= 5 && $no_of_ite >= 1) {
                for ($i = 1; $i <= $no_of_ite; $i++) {
                    if ($req_amount > 5000) {
                        $amount = 5000;
                        $req_amount = $req_amount - 5000;
                    } else {
                        $amount = $req_amount;
                    }
                    if ($amount <= 1000)
                        $charge = 10;
                    else
                        $charge = ($amount) / 100;
                    $totalAmount += $amount + $charge;
                    $result[$i] = array('charge' => $charge, 'txnAmount' => $amount, 'total' => $charge + $amount);

                }
                if ($totalAmount > $userDetail->balance->user_balance)
                    return response()->json(['result' => $result, 'txn_pin' => $userDetail->profile->txn_pin, 'totalAmount' => $totalAmount, 'status' => 0, 'message' => "Low balance"]);
                return response()->json(['result' => $result, 'txn_pin' => $userDetail->profile->txn_pin, 'totalAmount' => $totalAmount, 'status' => 1, 'message' => "ok"]);
            }
        }
    }


    /*admin fund transfer*/
    public function fundReturn(Request $request)
    {
        $rules = array(
            'userId' => 'required',
            'token' => 'required',
            'agentId' => 'required|numeric',
            'amount' => 'required|numeric',
            'remark' => 'required',
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

        $appResp = ApiResponseToApp::create([
            'response_from_app' => json_encode($request->all()),
            'request_type' => 'FUND RETURN',
            'user_id' => $request->userId,
            'api_id' => 0,
        ]);

        if ($authentication['status'] == 1) {
            $wallet = 0;
            $userDetails = $authentication['userDetails'];
            $amount = $request->amount;
            if ($amount > 2000000) {
                $appResp->response_to_app = json_encode(array('status' => 2, 'message' => 'Amount can not be exceed 20 Lakhs'));
                $appResp->save();
                return Response::json(array('status' => 2, 'message' => 'Amount can not be exceed 20 Lakhs'));

            }
            $loggeInUser = $userDetails->id;
            $child_id = $request->agentId;
            $remark_content = $request->remark;
            $child_balance = Balance::find($child_id);
            $payment_id = $request->id;
            $authbalance = $userDetails->balance->user_balance;
            if ($authbalance >= $amount && $amount >= 100) {
                $wallet_new = 'user_balance';
                $wallet_detail = 'Fund';
                $now = new \DateTime();
                $datetime = $now->getTimestamp();
                $ctime = $now->format('Y-m-d H:i:s');
                $user_detail = User::find($child_id);

                $schemenew = 0;
                $charge = ($amount * $schemenew) / 100;
                $amount = $amount - $charge;
                DB::beginTransaction();
                try {

                    Balance::where('user_id', $loggeInUser)->increment('user_balance', $amount);

                    $admin_details = User::find($loggeInUser);
                    $insert_id = Report::insertGetId([
                        'number' => $admin_details->mobile,
                        'provider_id' => 0,
                        'amount' => $amount,
                        'api_id' => 0,
                        'description' => 'Fund Returned',
                        'status_id' => 7,
                        'pay_id' => $datetime,
                        'txnid' => 'DT Reversed',
                        'total_balance' => $admin_details->balance->user_balance,
                        'total_balance2' => $admin_details->balance->user_commission,
                        'recharge_type' => $wallet,
                        'user_id' => $userDetails->id,
                        'credit_by' => $child_id,
                        'remark' => $remark_content,
                        'mode' => "APP"
                    ]);
                    Balance::where('user_id', $child_id)->decrement($wallet_new, $amount);
                    $user_detail_p = User::find($child_id);
                    $insert_id = Report::insertGetId([
                        'number' => $user_detail_p->mobile,
                        'provider_id' => 0,
                        'amount' => $amount,
                        'api_id' => 0,
                        'description' => 'Fund Returned',
                        'status_id' => 6,
                        'pay_id' => $datetime,
                        'txnid' => 'DT Reversed',
                        'total_balance' => $user_detail_p->balance->user_balance,
                        'total_balance2' => $user_detail_p->balance->user_commission,
                        'created_at' => $ctime,
                        'recharge_type' => $wallet,
                        'user_id' => $child_id,
                        'credit_by' => $loggeInUser,
                        'remark' => $remark_content,
                        'mode' => "APP"
                    ]);
                    DB::commit();
                    // if($userDetails->role_id == 1)
                    //   CustomTraits::sendSMS($number, $message,$userDetails->company_id);
                    $response = ['status' => 1, "message" => "Fund Return Successfully"];
                    $appResp->response_to_app = json_encode($response);
                    $appResp->save();
                    return response()->json($response);
                } catch (Exception $e) {
                    DB::rollback();
                    $appResp->response_to_app = json_encode(
                        ['status' => 2, "message" => "Failed. Please contact with admin"]);
                    $appResp->save();
                    return response()->json(['status' => 2, "message" => "Failed. Please contact with admin"]);
                }

            }
            $response = ['status' => 2, "message" => "Low balance or Minimum return balance should be Rs. 100"];
            $appResp->response_to_app = json_encode($response);
            $appResp->save();
            return response()->json($response);
        }
        $appResp->response_to_app = json_encode($authentication);
        $appResp->save();
        return $authentication;
    }


    public function distFundList(Request $request)
    {

        $rules = array(
            'token' => 'required',
            'userId' => 'required',
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
                $usersQuery = User::orderBy('id', 'DESC');
                if ($request->SEARCH_TYPE == "ID")
                    $usersQuery->where('id', 'like', '%' . trim($request->number) . '%');
                elseif ($request->SEARCH_TYPE == "NAME")
                    $usersQuery->where('name', 'like', '%' . trim($request->number) . '%');
                elseif ($request->SEARCH_TYPE == "MOB_NO")
                    $usersQuery->where('mobile', 'like', '%' . trim($request->number) . '%');
                $usersQuery->where('role_id', 4);

                $users = $usersQuery->simplePaginate(40);

                if ($request->page)
                    $pageNo = $request->page + 1;
                else
                    $pageNo = 2;


                $details = $users->map(function ($user) {
                    return [

                        'id' => $user->id,
                        'prefix' => $user->prefix,
                        'name' => $user->name,
                        'shopName' => $user->member->company,
                        'mobile' => $user->mobile,
                        'moneyBalance' => number_format($user->balance->user_balance, 2),
                    ];
                });
                return response()->json(['status' => 1, 'reports' => $details, 'count' => count($details), 'page' => "page=" . $pageNo,]);
            } else
                return response()->json(['status' => 2, 'message' => 'you are not authorized!']);
        }
        return $authentication;
    }

    /*route:agent-request-view*/
    public function agentRequestView(Request $request)
    {
        $rules = array(
            'userId' => 'required',
            'token' => 'required',
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
        if ($authentication['status'] == 1) {
            $userDetails = $authentication['userDetails'];
            $logined_user_id = $userDetails->id;
            $members = User::where('parent_id', $logined_user_id)->pluck('id', 'id')->toArray();
            $loadcashes = Loadcash::where('status_id', 3)->where('request_to', 1)->whereIn('user_id', $members)->orderBy('id', 'desc')->get();

            $remarks = Remark::where([['id', '!=', 0], ['deleted', '=', 0]])->pluck('remark', 'id')->toArray();

            try {
                $result = $loadcashes->map(function ($loadcash) {
                    return [
                        'created_at' => $loadcash->created_at->format('d-m-Y H:i:s'),
                        'id' => $loadcash->id,
                        'user_id' => $loadcash->user->id,
                        'user_name' => $loadcash->user->name . '  ' . $loadcash->user->id,
                        'firm_name' => $loadcash->user->member->company,
                        'role' => @$loadcash->user->role->role_title,
                        'mobile' => @$loadcash->user->mobile,
                        'mode' => @$loadcash->payment_mode,
                        'branch_code' => (@$loadcash->loc_batch_code) ? @$loadcash->loc_batch_code : "N/A",
                        'online_payment_mode' => @$loadcash->c_online_mode,
                        'deposit_date' => @$loadcash->deposit_date,
                        'bank_name' => ($loadcash->request_to == 1) ? $loadcash->bank_name : (@$loadcash->netbank->bank_name) ? @$loadcash->netbank->bank_name : "N/A",
                        'remark' => @$loadcash->request_remark,
                        'slip' => 'No Slip',
                        'ref_id' => (@$loadcash->bankref) ? @$loadcash->bankref : "N/A",
                        'amount' => @$loadcash->amount,
                        'status' => @$loadcash->status->status,
                        'status_id' => @$loadcash->status_id,
                    ];

                });
                return response()->json(['status' => 1, 'count' => count($result),
                    'result' => $result, 'remarks' => $remarks]);
            } catch (Exception $e) {
                return response()->json(['status' => 0, 'count' => 0, 'message' => "Error."]);
            }

        }
        return $authentication;

    }

    /*route:get-loadcash-record*/
    function getLoadCashRecord(Request $request)
    {
        $rules = array(
            'userId' => 'required',
            'token' => 'required',
            'loadCashId' => 'required|numeric',
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
        if ($authentication['status'] == 1) {
            $loadcashes = Loadcash::where('id', $request->loadCashId)->get();
            try {
                $result = $loadcashes->map(function ($loadcash) {
                    return
                        array(
                            'id' => $loadcash->id,
                            'name' => $loadcash->user->name . '(' . $loadcash->prefix . ' - ' . $loadcash->id . ')',
                            'paymentType' => @$loadcash->pmethod->payment_type,
                            'bankRef' => @$loadcash->bankref,
                            'amount' => @$loadcash->amount,
                            'remarkId' => Remark::where([['id', '!=', 0], ['deleted', '=', 0]])->lists('remark', 'id')->toArray(),
                            'statusId' => array(
                                '1' => "Success",
                                '2' => "Reject",
                                '3' => "Pending"
                            ),
                            'paymentMode' => array(
                                '1' => "Cash Deposit",
                                '2' => "Online Payment",
                            ),
                            'bankCharge' => array(
                                '0.00' => "0.00",

                            ),
                        );

                });
                return response()->json(['status' => 1, 'count' => count($result), 'result' => $result]);
            } catch (Exception $e) {
                throw $e;
                return response()->json(['status' => 0, 'count' => 0, 'message' => "Error."]);
            }
        }
        return $authentication;
    }

    public function retailerFundTransfer(Request $request)
    {
        $rules = array(
            'userId' => 'required',
            'token' => 'required',
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
        if ($authentication['status'] == 1) {
            $userDetails = $authentication['userDetails'];


            $usersQuery = User::orderBy('id', 'DESC');
            if ($request->SEARCH_TYPE == "ID")
                $usersQuery->where('id', 'like', '%' . trim($request->number) . '%');
            elseif ($request->SEARCH_TYPE == "NAME")
                $usersQuery->where('name', 'like', '%' . trim($request->number) . '%');
            elseif ($request->SEARCH_TYPE == "MOB_NO")
                $usersQuery->where('mobile', 'like', '%' . trim($request->number) . '%');
            if ($userDetails->role_id == 1) {
                $usersQuery->where('role_id', 5);

            } elseif ($userDetails->role_id == 3) {
                $members = User::where('parent_id', $userDetails->id)->get();
                $member_id = array();
                foreach ($members as $member) {
                    $member_id[] = $member->id;
                }
                $members = User::whereIn('parent_id', $member_id)->get();
                $member_id_new = array();
                foreach ($members as $member) {
                    $member_id_new[] = $member->id;
                }
                $myid = array($userDetails->id);
                $mmember = array_merge($member_id, $member_id_new, $myid);
                $users = $usersQuery->whereIn('parent_id', $mmember);
            } else {
                $users = $usersQuery->where('parent_id', $userDetails->id);
            }

            $users = $usersQuery->simplePaginate(40);

            if ($request->page)
                $pageNo = $request->page + 1;
            else
                $pageNo = 2;


            $details = $users->map(function ($user) {
                return [
                    'id' => $user->id,
                    'name' => $user->name,
                    'shopName' => $user->member->company,
                    'mobile' => $user->mobile,
                    'moneyBalance' => number_format($user->balance->user_balance, 2),
                ];
            });


            return response()->json(array(
                'status' => '1',
                'count' => count($details),
                'page' => "page=" . $pageNo,
                'message' => 'success',
                'reports' => $details,
            ));
        } else return $authentication;
    }


    public function fundTransafer(Request $request)
    {
        $rules = array(
            'userId' => 'required',
            'token' => 'required',
            'agentId' => 'required|numeric',
            'amount' => 'required|numeric',
            'remark' => 'required',
        );
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return Response::json(array(
                'success' => 10,
                'message' => "Missing Param",
                'errors' => $validator->getMessageBag()->toArray()
            ));
        }
        $authentication = $this->checkLoginAuthentication($request);


        $appResp = ApiResponseToApp::create([
            'response_from_app' => json_encode($request->all()),
            'request_type' => 'FUND TRANSFER',
            'api_id' => '0',
            'user_id' => $request->userId,
        ]);

        if ($authentication['status'] == 1) {

            $wallet = 0;
            $userDetails = $authentication['userDetails'];
            $amount = $request->amount;
            if ($amount > 2000000) {
                $response = array('status' => 2, 'message' => 'Amount can not be exceed 10 Lakhs');
                $appResp->response_to_app = json_encode($response);
                $appResp->save();
                return Response::json($response);

            }
            $loggeInUser = $userDetails->id;
            $child_id = $request->agentId;
            $remark_content = $request->remark;
            $child_balance = Balance::find($child_id);
            $payment_id = $request->id;
            $authbalance = $userDetails->balance->user_balance;
            if ($authbalance >= $amount && $amount >= 100) {
                $wallet_new = 'user_balance';
                $wallet_detail = 'Fund';
                $now = new \DateTime();
                $datetime = $now->getTimestamp();
                $ctime = $now->format('Y-m-d H:i:s');
                $user_detail = User::find($child_id);

                $schemenew = 0;
                $charge = ($amount * $schemenew) / 100;
                $amount = $amount - $charge;
                DB::beginTransaction();
                try {

                    Balance::where('user_id', $child_id)->increment($wallet_new, $amount);
                    $child_user_details = User::find($child_id);
                    $reports = Report::create([
                        'number' => $child_user_details->mobile,
                        'provider_id' => 0,
                        'amount' => $amount,
                        'api_id' => 0,
                        'ip_address' => \Request::ip(),
                        'description' => $wallet_detail . ' Refill',
                        'status_id' => 7,
                        'pay_id' => time(),
                        'txnid' => 'DT',
                        'profit' => $charge,
                        'payment_id' => 0,
                        'total_balance' => $child_user_details->balance->user_balance,
                        'total_balance2' => $child_user_details->balance->user_commission,
                        'user_id' => $child_id,
                        'debit_charge' => $charge,
                        'credit_charge' => 0,
                        'credit_by' => $userDetails->id,
                        'remark' => $remark_content,
                        'recharge_type' => $wallet,
                        'mode' => "APP"
                    ]);
                    $lastInsetedId = $reports->id;
                    Balance::where('user_id', $loggeInUser)->decrement($wallet_new, $amount);
                    $user_detail_p = User::find($loggeInUser);
                    $insertId = Report::insertGetId([
                        'number' => $user_detail_p->mobile,
                        'provider_id' => 0,
                        'amount' => $amount,
                        'api_id' => 0,
                        'description' => 'Fund Transfer to ' . $user_detail->name,
                        'status_id' => 6,
                        'pay_id' => time(),
                        'txnid' => 'DT',
                        'ref_id' => $lastInsetedId,
                        'profit' => $charge,
                        'payment_id' => 0,
                        'total_balance' => $user_detail_p->balance->user_balance,
                        'total_balance2' => $user_detail_p->balance->user_commission,
                        'created_at' => $ctime,
                        'user_id' => $userDetails->id,
                        'credit_charge' => $charge,
                        'debit_charge' => 0,
                        'credit_by' => $user_detail->id,
                        'remark' => $remark_content,
                        'recharge_type' => $wallet,
                        'mode' => "APP"
                    ]);
                    $number = $user_detail->mobile;
                    $message = "Dear " . $user_detail->name . " , Your Wallet has been Credited with Amount : $amount, Thanks " . $userDetails->company->company_name;
                    $message = urlencode($message);
                    DB::commit();
                    //if($userDetails->role_id == 1)
                    //   CustomTraits::sendSMS($number, $message,$userDetails->company_id);
                    $response = ['status' => 1, "message" => "Fund Transafer Successfully"];
                    $appResp->response_to_app = json_encode($response);
                    $appResp->save();
                    return response()->json($response);
                } catch (Exception $e) {
                    DB::rollback();
                    $response = ['status' => 2, "message" => "Failed. Please contact with admin"];
                    $appResp->response_to_app = json_encode($response);
                    $appResp->save();
                    return response()->json($response);
                }

            }
            $response = ['status' => 2, "message" => "Low balance or Minimum transafer balance should be Rs. 100"];
            $appResp->response_to_app = json_encode($response);
            $appResp->save();
            return response()->json($response);
        }
        $appResp->response_to_app = json_encode($authentication);
        $appResp->save();
        return $authentication;
    }


    /*-------------transaction-------------------*/

    function isBankDownOrNot(Request $request){

        $rules = array(
            'userId' => 'required',
            'token' => 'required',
            'accountNumber' => 'required',
            'ifscCode' => 'required',
            'bankName' => 'required',
        );
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return Response::json(array(
                'success' => 10,
                'message' => "Missing Param",
                'errors' => $validator->getMessageBag()->toArray()
            ));
        }
        $authentication = $this->checkLoginAuthentication($request);
        if($authentication['status'] !=1) return $authentication;
        $instantPay = new InstantPayController();
        $response = $instantPay->isBankDownOrNot($request);
        return $response;
    }

    function transactionBkp(Request $request)
    {
        $rules = array(
            'senderName' => 'required|regex:/^[A-Za-z ]+$/',
            'ifsc' => 'required|min:11|max:11|regex:/^[A-Z0-9]+$/',
            'bank_account' => 'required|numeric|regex:/^[0-9]+$/',
            'mobile_number' => 'required|numeric:10|regex:/^[0-9]+$/',
            'beneficiary_id' => 'required|numeric|regex:/^[0-9]+$/',
            'amount' => 'required|min:10|numeric|regex:/^[0-9]+$/',


        );
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return Response::json(['status' => 10, 'message' => $validator->errors()->getMessages()]);
        }
        $appResp = ApiResponseToApp::create([
            'response_from_app' => json_encode($request->all()),
            'request_type' => 'DMT1 TRANSACTION',
            'user_id'=>$request->userId,
            'api_id' => 4,
        ]);

        $authentication = $this->checkLoginAuthentication($request);
        if ($authentication['status'] != 1) {
            $appResp->response_to_app = json_encode($authentication);
            $appResp->save();
            return $authentication;
        }
        $userDetails = $authentication['userDetails'];


        $request_ip = request()->ip();
        $mode = "APP";
        $mobile_number = $NUMBER = $request->mobile_number;
        $routingType = "IMPS";
        $benId = $request->beneficiary_id;
        $amount = $request->amount;
        $bank_account = $request->bank_account;
        //$userDetails = $this->getUserDetails();
        $user_id = $userDetails->id;
        $ifsc = $request->ifsc;
        $bankCode = substr($ifsc, 0, 4);
        $result = array();
        if ($user_id != 4) {
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

        $statusId = 3;

        if ($amount >= 10 && $amount != '' && $NUMBER != '' && $benId != '' && $amount <= 25000) {
            $duplicatae = $this->checkDuplicateTransaction($bank_account, $amount, $user_id, $NUMBER);

            if ($duplicatae['status'] == 2) {
                //return response()->json($duplicatae);
                $result[1] = array('status' => 'Failure', 'message' => "Duplicate Txn");
                $appResp->response_to_app = json_encode($result);
                $appResp->save();
                return response()->json(['result' => $result]);
            }

            $balance = Balance::where('user_id', $user_id)->first();
            $bulk_amount = $req_amount = $amount;
            $no_of_ite = ceil($amount / 5000);
            $result = array();


            if ($no_of_ite > 5 || $no_of_ite < 1) {
                $result[1] = array('status' => 'Failure', 'message' => "You are doing something wrong..Please confirm entered balance");
                $appResp->response_to_app = json_encode($result);
                $appResp->save();
                return response()->json(['result' => $result]);
            }

            $beneficiarydetail = Beneficiary::where(['benficiary_id' => $benId, 'api_id' => 0])->first();
            if (!empty($beneficiarydetail)) {
                $beneficiarydetail = $beneficiarydetail->id;
            } else {
                $beneficiarydetail = 0;
            }
            if ($beneficiarydetail == 0) {
                $result[1] = ['status' => 'Failure', 'message' => 'Beneficiary Does not existn'];
                $appResp->response_to_app = json_encode($result);
                $appResp->save();
                return response()->json(['result' => $result]);
            }
            $user_balance = $balance->user_balance;
            $apxAmount = $amount + ($amount / 100);
            if ($user_balance < $apxAmount) {
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
				$balance = Balance::select('user_balance')->where('user_id', $user_id)->first();
                if ($balance->user_balance < $amount) 
				{
					
					
					$result[$i]=['txnId' => '', 'refNo' => '', 'status' => "FAILED", 'amount' => $amount, 'txnTIme' => $ctime, 'message' => "FAILED"];
					$appResp->response_to_app = json_encode($result);
					$appResp->save();
					return response()->json(['result' => $result]);
				}
				$walletScheme = ImpsWalletScheme::where('min_amt', '<=', $amount)->where('max_amt', '>=', $amount)->where('wallet_scheme_id', $userDetails->member->imps_wallet_scheme)->first();
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
                $agentCharge = $this->agentCharge($amount, $walletScheme->agent_charge, $walletScheme->agent_charge_type);
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
                            'api_id' => 4,
                            'profit' => 0,
                            'type' => 'DR',
                            'txn_type' => 'TRANSACTION',
                            'status_id' => $statusId,
                            'pay_id' => $datetime,
                            'created_at' => $ctime,
                            'user_id' => $user_id,
                            'created_by' => $user_id,
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
                            'channel' => 2,
                            'mode' => 'APP',
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


                    /* --------------------------------------------------------- */
                    if ($statusId == 3) {


                        //dummy response
                        /* $apiResp = Apiresponse::create(['api_id' => 4, 'api_type' => "TXN", 'report_id' => $insert_id, 'request_message' => json_encode(['number' => $NUMBER, 'routingType' => $routingType, 'benId' => $benId, 'amount' => $amount, 'insert_id' => $insert_id])]);

                        $response = "{\"status\":\"Transaction Successful\",\"statuscode\":\"TXN\",\"data\":{\"charged_amt\":1004.32,\"ref_no\":\"920808533257\",\"name\":\"HASIBALISOMOHAMMEDKH\",\"amount\":1000,\"ccf_bank\":10,\"bank_alias\":\"PPBL\",\"locked_amt\":0,\"opr_id\":\"920808533257\",\"ipay_id\":\"1190727083340VCDHE\"}}";*/


                        	$cyber = new \App\Library\CyberDMT;
                            $apiResp = Apiresponse::create(['api_id'=>4,'api_type'=>"TXN",'report_id'=>$insert_id,'request_message'=>json_encode(['number'=>$NUMBER,'routingType'=>$routingType,'benId'=>$benId,'amount'=>$amount,'insert_id'=>$insert_id])]);
                            $response = $cyber->transaction($NUMBER, $routingType, $benId, $amount, $insert_id);
                        $apiResp->message = $response;
                        $apiResp->save();
                        try {
                            $res = json_decode($response);
                            if (!empty($res->statuscode)) {
                                $statuscode = $res->statuscode;
                                if ($statuscode == 'TXN') {

                                    $reportDetails->status_id = 1;
                                    $reportDetails->txnid = $res->data->ref_no;
                                    $reportDetails->bank_ref = $res->data->ref_no;
                                    $reportDetails->save();
                                    $this->creditCommission($d_id, $m_id, $a_id, $user_id, $mobile_number, $beneficiarydetail, 2, $insert_id, $amount, $dist_charge_data, $md_charge_data, $admin_charge_data, $bank_account, $reportDetails, 4);
                                    $cyberResponse = ['txnId' => $insert_id, 'refNo' => $res->data->ref_no, 'status' => "SUCCESS", 'amount' => $amount, 'txnTIme' => $ctime];
                                    $result[$i] = $cyberResponse;
                                } elseif ($statuscode == 'TUP') {
                                    $reportDetails->status_id = 1;
                                    $reportDetails->txnid = $res->data->ref_no;
                                    $reportDetails->bank_ref = $res->data->ref_no;
                                    $reportDetails->save();
                                    $this->creditCommission($d_id, $m_id, $a_id, $user_id, $mobile_number, $beneficiarydetail, 2, $insert_id, $amount, $dist_charge_data, $md_charge_data, $admin_charge_data, $bank_account, $reportDetails, 4);
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
                                    $this->creditCommission($d_id, $m_id, $a_id, $user_id, $mobile_number, $beneficiarydetail, 2, $insert_id, $amount, $dist_charge_data, $md_charge_data, $admin_charge_data, $bank_account, $reportDetails, 4);
                                    $cyberResponse = ['txnId' => $insert_id, 'refNo' => '', 'status' => "PENDING", 'amount' => $amount, 'txnTIme' => $ctime];
                                    $result[$i] = $cyberResponse;
                                }
                            } else {

                                $this->creditCommission($d_id, $m_id, $a_id, $user_id, $mobile_number, $beneficiarydetail, 2, $insert_id, $amount, $dist_charge_data, $md_charge_data, $admin_charge_data, $bank_account, $reportDetails, 4);
                                $cyberResponse = ['txnId' => $insert_id, 'refNo' => '', 'status' => "PENDING", 'amount' => $amount, 'txnTIme' => $ctime];
                                $result[$i] = $cyberResponse;
                            }
                        } catch (Exception $e) {
                            $cyberResponse = ['txnId' => $insert_id, 'refNo' => '', 'status' => "PENDING", 'amount' => $amount, 'txnTIme' => $ctime];
                            $result[$i] = $cyberResponse;
                        }
                    } else {
                        $this->creditCommission($d_id, $m_id, $a_id, $user_id, $mobile_number, $beneficiarydetail, 2, $insert_id, $amount, $dist_charge_data, $md_charge_data, $admin_charge_data, $bank_account, $reportDetails, 4);
                        $cyberResponse = ['txnId' => $insert_id, 'refNo' => '', 'status' => "Successful Submitted", 'amount' => $amount, 'txnTIme' => $ctime];
                        $result[$i] = $cyberResponse;
                    }

                } else {
                    $cyberResponse = ['txnId' => '', 'refNo' => '', 'status' => "LOW BALANCE", 'amount' => $amount, 'txnTIme' => $ctime];
                    $result[$i] = $cyberResponse;
                }
            }
            $appResp->response_to_app = json_encode($result);
            $appResp->save();
            return Response()->json(['result' => $result]);
        } else {
            $cyberResponse = ['status' => 'Failure', 'message' => 'Amount Should be Minimum Rs 10-25000'];
            $appResp->response_to_app = json_encode($result);
            $appResp->save();
            return Response()->json(['result' => $result]);
        }
    }

	function transaction (Request $request){

        $rules = array(
            'ifsc' => 'required|min:11|max:11|regex:/^[A-Z0-9]+$/',
            'bank_account' => 'required|numeric|regex:/^[0-9]+$/',
            'mobile_number' => 'required|numeric:10|regex:/^[0-9]+$/',
            'beneficiary_id' => 'required|numeric|regex:/^[0-9]+$/',
            'amount' => 'required|min:10|numeric|regex:/^[0-9]+$/',
			 'senderName' => 'required|regex:/^[A-Za-z ]+$/',
            'userId'=>'required',
            'token'=>'required',


        );
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return Response::json(['status' => 10, 'error' => $validator->errors()->getMessages(),"message"=>"missing params"]);
        }
        $appResp = ApiResponseToApp::create([
            'response_from_app' => json_encode($request->all()),
            'request_type' => 'DMT1 TRANSACTION',
            'user_id'=>$request->userId,
            'api_id' => 4,
        ]);

        $authentication = $this->checkLoginAuthentication($request);
        if ($authentication['status'] != 1) {
            $appResp->response_to_app = json_encode($authentication);
            $appResp->save();
            return $authentication;
        }
        $userDetails = $authentication['userDetails'];
        $userId = $userDetails->id;


        $request_ip =  request()->ip();
        $mobile_number=$NUMBER = $request->mobile_number;
        $routingType = "IMPS";
        $benId = $request->beneficiary_id;
        $amount = $request->amount;
        $bank_account = $request->bank_account;
        $user_id = $userDetails->id;
        $ifsc = $request->ifsc;
        $bankCode = substr($ifsc, 0, 4);
        $result=array();
        if($userId!= 4)
        {
            $isAcitveService = $this->isActiveService(4);
            if ($isAcitveService) {
                $result[1] = array('status' => 'Failure', 'message' => $isAcitveService->message);
                $appResp->response_to_app = json_encode($result);
                $appResp->save();
                return response()->json(['result' => $result]);
            }
        }
        $isBankDetails = Masterbank::select('bank_status')->where('bank_code',$bankCode)->first();
        if($isBankDetails=='')
        {
            $result[1] =  array('status' => 'Failure', 'message' => "Bank code is not found. Please contact with Admin");
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
        $statusId=3;
        if ($amount >= 10 && $amount != '' && $NUMBER != '' && $benId != '' && $amount<=25000)
        {
            $duplicatae = $this->checkDuplicateTransaction($bank_account, $amount,$userId,4);
            if($duplicatae['status'] == 31){
                $result[1] =  array('status' => 'Failure', 'message' => "Duplicate Txn");
                $appResp->response_to_app = json_encode($result);
                $appResp->save();
                return response()->json(['result' => $result]);
            }

            $balance = Balance::where('user_id', $user_id)->first();
            $bulk_amount = $req_amount = $amount;
            $no_of_ite = ceil($amount/5000);
            $result=array();
            if( $no_of_ite >5 || $no_of_ite <1)
            {
                $result[1]=array('status' => 'Failure', 'message' => "You are doing something wrong..Please confirm entered balance");
                $appResp->response_to_app = json_encode($result);
                $appResp->save();
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
                $appResp->response_to_app = json_encode($result);
                $appResp->save();
                return response()->json(['result' => $result]);
            }
            $user_balance = $balance->user_balance;
            $apxAmount = $amount +($amount/100);
            if($user_balance < $apxAmount)
            {
                $result[1]=['status' => 'Failure', 'message' => 'Low Balance Please Refill your wallet'];
                $appResp->response_to_app = json_encode($result);
                $appResp->save();
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
                    $appResp->response_to_app = json_encode($result);
                    $appResp->save();
                    return response()->json(['result' => $result]);
                }
                elseif($walletScheme->is_error){
                    $result[$i]=['status' => 'Failure', 'message' => 'Error in Setting. Please Call to admin'];
                    $appResp->response_to_app = json_encode($result);
                    $appResp->save();
                    return response()->json(['result' => $result]);
                }

                $agentCharge = $this->agentCharge($amount,$walletScheme->agent_charge,$walletScheme->agent_charge_type);
                $agent_parent_id = $userDetails->parent_id;
                $user_id=$userId;
                $agent_parent_role = $userDetails->parent->role_id;
                $dist_charge_data=$admin_charge_data=$md_charge_data=array();
                if($userDetails->parent_id == 1)
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
                            'created_by' => $userId,
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
                        $appResp->response_to_app = json_encode($result);
                        $appResp->save();
                        return response()->json(['result' => $result]);
                    }

                    if($statusId == 3)
                    {
                        $cyber = new \App\Library\CyberDMT;
                        $apiResp = Apiresponse::create(['api_id'=>4,'api_type'=>"TXN",'report_id'=>$insert_id,
                            'request_message'=>json_encode(['number'=>$NUMBER,'routingType'=>$routingType,
                                'benId'=>$benId,'amount'=>$amount,'insert_id'=>$insert_id])]);
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
            $appResp->response_to_app = json_encode($result);
            $appResp->save();
            return response()->json(['result' => $result]);

        }
        else
        {
            $appResp->response_to_app = json_encode($result);
            $appResp->save();
            return response()->json(['result' => $result]);
        }
    }


}
