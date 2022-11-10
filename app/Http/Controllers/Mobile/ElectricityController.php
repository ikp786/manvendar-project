<?php

namespace App\Http\Controllers\Mobile;

use App\ApiResponseToApp;
use App\Traits\CustomAuthTraits;
use Validator;
use App\Balance;
use App\Provider;
use Illuminate\Http\Request;
use Response;
use DB;
use App\Commission;
use App\Report;
use Auth;
use Exception;
use App\Http\Controllers\Controller;
use App\Traits\CustomTraits;

class ElectricityController extends Controller
{

    use CustomTraits;
    use CustomAuthTraits;

    //@Route
    public function fetchBillAmount(Request $request)
    {
        $rules = array(
            'provider' => 'required|numeric',
            'ca_number' => 'required',
            'userId' => 'required',
            'token' => 'required'
        );
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return Response::json(array(
                'status' => false,
                'errors' => $validator->getMessageBag()->toArray()
            ));
        }

        $authentication = $this->checkLoginAuthentication($request);
        if($authentication['status'] !=1) return $authentication;
        $userDetails = $authentication['userDetails'];


        $consumerNumber = $request->ca_number;
        $token = config('constants.INSTANT_KEY');//die;
        $instantPay = Provider::select('sp_key')->find($request->provider);
        if($instantPay)
            $sp_key = $instantPay->sp_key;
        if($sp_key !='')
        {
            $content ="{\n\"token\":\"$token\",\n\"request\": {\n\"sp_key\": \"$sp_key\"}\n}";
            $url = config('constants.INSTANT_PAY_URL') ."/ws/userresources/bbps_biller";
            $agentid = time();
            $output =  $this->getCurlPostIMethod($url,$content);
            try
            {
                $apiResp = json_decode($output);
                $params = $apiResp->data[0]->params;
                try{
                    return $this->getCurlMethod($userDetails,$params,$consumerNumber,$sp_key);
                }
                catch(Exception $e)
                {
                    return response()->json(['status'=>0,'content'=>"Bill fetch service is not currenty possible. Please enter amount and biller name manully"]);
                }
            }
            catch(Exception $e)
            {
                return response()->json(['status'=>0,'content'=>"Currently Service is down"]);
            }
        }
        return response()->json(['status'=>0,'content'=>"Currently Service is down"]);
    }
    private function getCurlMethod($userDetails,$params,$consumerNumber,$sp_key)
    {
        $jsonData = json_decode($params);
        $jsonData[0]->name = $consumerNumber;
        $jsonData=$jsonData[0];
        $paramSecornd = json_encode($jsonData);
        $token = config('constants.INSTANT_KEY');//die;
        $agentid = time();
        $customerMobileNumber= $userDetails->mobile;
        $serIP = "182.18.157.156";
        $outletId = 15814;
        $ip=$_SERVER['REMOTE_ADDR'];
        $location_data = (unserialize(file_get_contents('http://www.geoplugin.net/php.gp?ip='.$ip)));
        $latlog = "28.626640,77.384804";
        $content = "{\n\"token\": \"$token\",\n\"request\": {\n\"sp_key\":\"$sp_key\",\n\"agentid\":\"$agentid\",\n\"customer_mobile\":\"8285540407\",\n    \"customer_params\": [\n\"$consumerNumber\"],\n    \"init_channel\": \"AGT\",\n\"endpoint_ip\": \"$serIP\",\n\"mac\": \"AD-fg-12-78-GH\",\n\"payment_mode\":\"Cash\",\n\"payment_info\":\"bill\",\n    \"amount\": \"10\",\n\"reference_id\": \"\",\n\"latlong\": \"$latlog\",\n\"outletid\":\"$outletId\"\n}\n}";

        $url = config('constants.INSTANT_PAY_URL')."/ws/bbps/bill_fetch";
        $output =  $this->getCurlPostIMethod($url,$content);
        $apiRespContent = json_decode($output);
        if($apiRespContent->statuscode == "ERR")
        {
            return response()->json(['status'=>0,'content'=>$apiRespContent->status]);

        }
        else{
            $displayContent['dueAmount'] = $apiRespContent->data->dueamount;
            $displayContent['dueDate'] = $apiRespContent->data->duedate;
            $displayContent['customerName'] = $apiRespContent->data->customername;
            $displayContent['billNumber'] = $apiRespContent->data->billnumber;
            $displayContent['billDate'] = $apiRespContent->data->billdate;
            return response()->json(['status'=>1,'content'=>$displayContent]);
        }
    }

    //@Route
    function store(Request $request)
    {
        $rules = array(
            'userId' => 'required',
            'token' => 'required',
            'number' => 'required|regex:/^[A-Za-z0-9]+$/',
            'provider' => 'required|numeric|regex:/^[0-9]+$/',
            'amount' => 'required',
            'billerName' => 'required',

        );
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return Response::json(array(
                'status' => 10,
                'errors' => $validator->getMessageBag()->toArray(),
                'message' => "Please enter correct information"
            ));
        }

        $appResp = ApiResponseToApp::create([
            'response_from_app' => json_encode($request->all()),
            'request_type' => 'BBPS/Electricity',
            'user_id'=>$request->userId,
            'api_id' => 15,
        ]);


        $authentication = $this->checkLoginAuthentication($request);
        if ($authentication['status'] != 1) {
            $appResp->response_to_app = json_encode($authentication);
            $appResp->save();
            return $authentication;
        }
        $userDetails = $authentication['userDetails'];
        $userId = $userDetails->id;


        $number = $request->number;
        $currentFistDate = date('Y-m-01') . " 00:00:00";
        if ($userId != 4) {
            $isAcitveService = $this->isActiveService(15);
            if ($isAcitveService){
                $response =['status' => "2", 'message' => $isAcitveService->message];
                $appResp->response_to_app = json_encode($response);
                $appResp->save();
                return response()->json($response);
            }
        }
        if (Report::where(['number' => $number, 'api_id' => 15])->where('created_at', '>=', $currentFistDate)->whereIn('status_id', [1, 3, 24])->first())
        {
            $response = ['status' => '2', 'message' => 'This Payment alreday done, Please Check your statement'];
            $appResp->response_to_app = json_encode($response);
            $appResp->save();
            return response()->json($response);
        }
        $provider = $request->provider;
        $amount = $request->amount;
        $billerName = $request->billerName;
        $consumerNumber = $request->consumerNumber;
        $bill_due_date = $request->bill_due_date;
        $bill_due_date = date("Y-m-d H:i:s", strtotime($bill_due_date));
        if ($number && $provider && $amount) {
           
            $balance = Balance::where('user_id', $userDetails->id)->first();
            $response = $this->makeRecharge($number, $provider, $amount, $billerName, $userDetails, $consumerNumber, $bill_due_date);
            $appResp->response_to_app = $response;
            $appResp->save();
            return $response;
        } else {
            $response = ['status' => '2', 'message' => 'all field requierd'];
            $appResp->response_to_app = json_encode($response);
            $appResp->save();
            return response()->json($response);
        }
    }
    function makeRecharge($number, $provider, $amount, $billerName, $userDetails, $consumerNumber,
                          $bill_due_date = null)
    {
        $now = new \DateTime();
        $datetime = $now->getTimestamp();
        $ctime = $now->format('Y-m-d H:i:s');
        $user_id = $userDetails->id;
        $user_balance = Balance::where('user_id', $user_id)->first();
        $user_balance = $user_balance->user_balance;
        if ($user_balance < $amount) {

            return Response::json(array('status' => '2', 'message' => "Low balance"));
        }
        $myd = $this->api_route($provider);
        if ($myd) {
            $startTime = date("Y-m-d H:i:s");
            $end_time = date('Y-m-d H:i:s', strtotime('-3 minutes', strtotime($startTime)));
            if ($existRecord = Report::where(['number' => $number])->whereBetween('created_at', [$end_time, $startTime])->first())
                return Response::json(array('status' => '2', 'message' => "Bill Payment has been initiated. Please wait for 3 Min"));
            $statusId = 3;
            $isOffline = 0;
            if (in_array($myd->service_id, array(6))) {
                if (!$myd->is_service_active)
                    return Response::json(array('status' => '2', 'message' => $myd->provider_name . " is down"));
                elseif (!$myd->is_service_online) {
                    $statusId = 24;
                    $isOffline = 1;
                } elseif (($myd->hold_txn_couter <= $myd->max_hold_txn) && $myd->hold_txn_couter >= 0 && ($myd->min_pass_amt_txn < $amount)) {
                    $statusId = 24;
                    $isOffline = 1;
                    $myd->hold_txn_couter += 1;
                    $myd->save();
                }
            } else {
                return Response::json(array('status' => '2', 'message' => "Route is not defined"));
            }
            $scheme_id = $userDetails->scheme_id;
            $mc = $this->get_commission($scheme_id, $provider);
            if ($mc == '') {
                return Response::json(array('status' => '2', 'message' => 'Sar Charge is not configured'));
            }
            if ($mc->type == 0) {
                $max_commission = $mc->max_commission;
                $commission = ($amount * $mc->r) / 100;
                $distt_comm = ($amount * $mc->d) / 100;
                $md_comm = ($amount * $mc->md) / 100;
                $admin_comm = ($amount * $mc->admin) / 100;
            } else {
                $max_commission = $mc->max_commission;
                $commission = $mc->r;
                $distt_comm = $mc->d;
                $md_comm = $mc->md;
                $admin_comm = $mc->admin;
            }
			$md_max_commission = $mc->md_max_commission;
			$dist_max_commission = $mc->dist_max_commission;
			if($commission > $max_commission)
				$commission = $max_commission;
			if($distt_comm > $dist_max_commission)
				$distt_comm = $dist_max_commission;
			if($md_comm > $md_max_commission)
				$md_comm = $md_max_commission;
            if ($mc->is_error)
                return Response::json(array('status' => '2', 'message' => 'Slab Error. Please contact to admin'));
            if ($commission < 0) {
                $tds = 0;
                $finalAmount = $amount - $commission;
                $cr_amt = $commission;
            } else {
                $tds = (($commission) * 5) / 100;
                $cr_amt = $commission - ($tds);
                $finalAmount = $amount - $cr_amt;
            }
            $d_id = $m_id = $a_id = '';
            $agent_parent_role = $userDetails->parent->role_id;
            $agent_parent_id = $userDetails->parent_id;
            if ($userDetails->parent_id == 1) {
                $d_id = $m_id = '';
                $a_id = 1;
            } else {
                if ($agent_parent_role == 4) {
                    $agent_parent_details = $userDetails->parent;
                    $dist_parent_id = $agent_parent_details->parent_id;// Don't go at variable name
                    if ($dist_parent_id == 1) {
                        $d_id = $agent_parent_id;
                        $m_id = '';
                        $a_id = 1;
                    } else {
                        $d_id = $agent_parent_id;
                        $m_id = $dist_parent_id;
                        $a_id = 1;
                    }
                } else if ($agent_parent_role == 3) {
                    $d_id = '';
                    $m_id = $agent_parent_id;
                    $a_id = 1;
                } elseif ($agent_parent_role == 1) {
                    $d_id = '';
                    $m_id = '';
                    $a_id = 1;
                }
            }
            DB::beginTransaction();
            try {
                Balance::where('user_id', $user_id)->decrement('user_balance', $finalAmount);
                $rechargeReport = Report::create([
                    'number' => $number,
                    'provider_id' => $provider,
                    'amount' => $amount,
                    'api_id' => $myd->api_id,
                    // 'api_id' => 1,
                    'status_id' => $statusId,
                    'pay_id' => time(),
                    'recharge_type' => 1,
                    'dist_commission' => ($d_id != '') ? $this->calCommission($distt_comm) : 0,
                    'md_commission' => ($m_id != '') ? $this->calCommission($md_comm) : 0,
                    'admin_commission' => ($a_id != '') ? $admin_comm : 0,
                    'user_id' => $user_id,
                    'opening_balance' => $user_balance,
                    'credit_charge' => ($commission > 0) ? $commission : 0,
                    'debit_charge' => ($commission < 0) ? abs($commission) : 0,
                    'created_by' => $userDetails->id,
                    'txn_type' => 'BILL PAYMENT',
                    'type' => 'DB',
                    'tds' => $tds,
                    'profit' => 0,
                    'is_offline' => $isOffline,
                    'biller_name' => $billerName,
                    'bill_due_date' => $bill_due_date,
                    'customer_number' => $consumerNumber,
                    'mode' => 'APP',
                    'total_balance' => Balance::where('user_id', $user_id)->select('user_balance')->first()->user_balance,
                ]);
                $insert_id = $rechargeReport->id;
                DB::commit();
            } catch (Exception $e) {
                DB::rollback();
                return Response::json(array('status' => '2', 'message' => 'Whoops! Something went wrong. Please try again after somethime'));
            }
            $provider_api = $myd->api_id;
            $cybercode = $myd->cyber;
            $cyber = new \App\Library\Bbps;
            $account = '';
            $cycle = '';
            if ($statusId == 3) {
                switch ($provider_api) {

                    case 8:
                        $vendorCode = $myd->redpay;
                        //$result = $this->redPayRecharge($user_id, $number, $vendorCode,$amount, $insert_id);
                        break;

                    case 1:
                        $result = $cyber->sendrecharge($cybercode, $insert_id, $number, $amount, $user_id, $account, $cycle);
                        break;
                    default;
                        $company = $provider;
                        $provider_api = 1;
                        $result = $cyber->sendrecharge($cybercode, $insert_id, $number, $amount, $user_id, $account, $cycle);
                }
                $status = $result['status'];
                $txnid = $result['txnid'];
                //$status = 1;
                //$txnid = 1;
                if ($status == 1) {
                    $status = '1';
                    $message = "Transaction Successfully Done, Operator Transaction ID : $txnid, Thanks";
                    $this->creditCommissionR($userDetails,$d_id, $m_id, $distt_comm, $md_comm, $insert_id, $number, $provider,
                        $provider_api, $a_id, $admin_comm, $amount, $user_id, $commission);
                    $rechargeReport->status_id = 1;
                    $rechargeReport->txnid = $txnid;
                    $rechargeReport->save();

                    //commented by akash kumar das(on date 27-08-2019)
                    if ($service_id == 6) {
						try{
						$msg = "Dear $billerName($number) Your Electricity bill Successfull with amount $amount ";
                        @$msg = urlencode(@$msg);
                        $this->sendSMS($consumerNumber, $msg, 1);
						}
						catch(Exception $e)
						{
							
						}
                    }

                } elseif ($status == 2) {
                    $status = '2';
                    $message = "Transaction Failed, Please check Detail or Try After some time, Thanks";
                    Balance::where('user_id', $user_id)->increment('user_balance', $finalAmount);
                    $final_bal = Balance::where('user_id', $user_id)->select('user_balance')->first();
                    $rechargeReport->status_id = 2;
                    $rechargeReport->txnid = $txnid;
                    $rechargeReport->total_balance = $final_bal->user_balance;
                    $rechargeReport->save();

                } else {
                    $this->creditCommissionR($userDetails,$d_id, $m_id, $distt_comm, $md_comm, $insert_id,
                        $number, $provider, $provider_api, $a_id, $admin_comm, $amount, $user_id, $commission);
                    $rechargeReport->status_id = 3;
                    $rechargeReport->txnid = $txnid;
                    $rechargeReport->ref_id = $ref_id;
                    $rechargeReport->save();
                    $status = '3';
                    $message = "Transaction Submitted Successfully Done, Check Status in Transaction Report, Thanks";
                }
                return Response::json(array('payid' => $insert_id, 'operator_ref' => $txnid, 'status' => $status, 'message' => $message));
            } else {
                $this->creditCommissionR($userDetails,$d_id, $m_id, $distt_comm, $md_comm, $insert_id, $number, $provider, $provider_api, $a_id, $admin_comm, $amount, $user_id, $commission);
                $rechargeReport->save();
                $status = '1';
                $message = "Transaction Submitted Successfully Thanks";
                @$msg = "Dear $billerName($number) Your Electricity bill Successfull with amount $amount ";
                @$msg = urlencode(@$msg);
                $this->sendSMS($consumerNumber, $msg, 1);
                return Response::json(array('payid' => $insert_id, 'operator_ref' => '', 'status' => "1", 'message' => $message));
            }
        } else
            return Response::json(array('status' => '2', 'message' => 'No Route id defined. Please Contact with admin'));


    }
    function get_commission($scheme_id, $provider)
    {
        return Commission::where('provider_id', $provider)->where('scheme_id', $scheme_id)->first();
    }
    private function api_route($provider)
    {
        $provider_detail = Provider::find($provider);
        return $provider_detail;
    }
	
	public function creditCommissionR($userDetails,$d_id,$m_id,$distt_comm,$md_comm,$insert_id,$number,$provider,$api_id,$a_id,$admin_comm,$amount,$user_id,$commission)
	{
		$now = new \DateTime();
		$datetime = $now->getTimestamp();
		$ctime = $now->format('Y-m-d H:i:s');
		DB::beginTransaction();
		try
		{	
			if($d_id!='' && $distt_comm>0)
			{
				$tds =  (($distt_comm)* 5)/100;
				$cr_amt = $distt_comm - ($tds);
				$user_balance = Balance::where('user_id',$d_id)->select('user_balance')->first();
				$total_balance_dist =  $user_balance->user_balance+$cr_amt;
				$insert_dist_ptxn =  Report::insertGetId([
								'number' => $number,
								'provider_id' => $provider,
								'amount' => $amount,
								'api_id' => $api_id,
								'status_id' => 16,
								'pay_id' => $datetime,
								'created_by'=>$user_id,
								'txnid'=>$insert_id,
								'created_at' => $ctime,
								'description' => "BILL_COMMISSION",
								'recharge_type' => 1,
								'tds' => $tds,
								'gst' => 0,
								'user_id' => $d_id,
								'profit' => 0,
								'debit_charge'=>0,
								'credit_charge'=>$distt_comm,
								'opening_balance'=>$user_balance->user_balance,
								'total_balance' => $total_balance_dist,
								]);
					Balance::where('user_id',$d_id)->increment('user_balance',$cr_amt);
				
			}
			if($m_id!=''&& $md_comm>0)
			{
				$tds =  (($md_comm)* 5)/100;
				$cr_amt = $md_comm - ($tds);
				$user_balance = Balance::where('user_id',$m_id)->select('user_balance')->first();
				Balance::where('user_id',$m_id)->increment('user_balance',$cr_amt);
				$total_balance_md =  $user_balance->user_balance;
				$insert_md_ptxn =  Report::insertGetId([
								'number' => $number,
								'provider_id' => $provider,
								'amount' => $amount,
								'api_id' => $api_id,
								'status_id' => 16,
								'description' => "RECHARGE_COMMISSION",
								'pay_id' => $datetime,
								'txnid'=>$insert_id,
								'created_at' => $ctime,
								'recharge_type' => 1,
								'tds' => $tds,
								'gst' => 0,
								'user_id' => $m_id,
								'profit' => 0,
								'credit_charge'=>$md_comm,
								'debit_charge'=>0,
								'opening_balance'=>$user_balance->user_balance,
								'total_balance' => $total_balance_md,
								'created_by'=>$user_id,
								]);
            }
			if($a_id!=''&& $admin_comm>0)
			{
				$tds =  0;
				$cr_amt = $admin_comm - ($tds);
				$user_balance = Balance::where('user_id',$a_id)->first();
				$adminBalance =  $user_balance->user_balance;
				Balance::where('user_id',$a_id)->increment('admin_com_bal',$cr_amt);
				$insert_md_ptxn =  Report::insertGetId([
								'number' => $number,
								'provider_id' => $provider,
								'amount' => $amount,
								'api_id' => $api_id,
								'status_id' => 16,
								'description' => "BILL_PAY_COMMISSION",
								'pay_id' => $datetime,
								'txnid'=>$insert_id,
								'created_at' => $ctime,
								'recharge_type' => 1,
								'tds' => $tds,
								'gst' => 0,
								'user_id' => $a_id,
								'profit' => 0,
								'debit_charge'=>0,
								'credit_charge'=>$cr_amt,
								'opening_balance'=>$adminBalance,
								'admin_com_bal'=>$user_balance->admin_com_bal+$cr_amt,
								'total_balance' => $adminBalance,
								'created_by'=>$user_id,
								]);
            }
			DB::commit();
		}
		catch(Exception $e)
		{
			DB::rollback();
			return Response::json(['status_id'=>2,'message'=>'Failed,something went wrong!']);
		}
	}
	
    
    private function agentCharge($amount, $chargeType, $charge)
    {
        if ($chargeType == 0)
            return ($amount * $charge) / 100;
        else
            return $charge;

    }
    private function getCommission($amount, $commType, $comm)
    {

        if ($commType == 0)
            return ($amount * $comm) / 100;
        else
            return $comm;
    }


}
