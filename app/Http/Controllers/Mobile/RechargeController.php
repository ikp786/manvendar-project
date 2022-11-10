<?php
namespace App\Http\Controllers\Mobile;
use App\Apiresponse;
use App\ApiResponseToApp;
use App\Balance;
use App\Commission;
use App\Http\Controllers\Controller;
use App\Report;
use App\State;
use App\Traits\CustomAuthTraits;
use App\Provider;
use Auth;
use Exception;
use Illuminate\Http\Request;
use Validator;
use Response;
use DB;
use App\Traits\CustomTraits;
class RechargeController extends Controller
{
    var $baseUrl = "https://www.comrade.tramo.in";
    use CustomAuthTraits, CustomTraits;
    var $mode = "APP";
    public function getRechargeProvider(Request $request)
    {
        $rules = array(
            'userId' => 'required',
            'token' => 'required',
            'requestType' => 'required',
        );
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return Response::json(array(
                'status' => 10,
                'errors' => $validator->getMessageBag()->toArray()
            ));
        }
        $authentication = $this->checkLoginAuthentication($request);
        if ($authentication['status'] == 1) {
            $userDetail = $authentication['userDetails'];
            if ($userDetail->role_id == 5) {
                $provider = null;
                $serviceId = 0;
                $requestType = $request->requestType;
                if ($requestType == 'DTH') {
                    $provider = Provider::where('service_id', '=', '2')->selectRaw('provider_image,provider_name, id')->get();
                    $serviceId = 2;
                } else if ($requestType == 'MOBILE_PREPAID') {
                    $provider = Provider::where('service_id', '=', '1')->selectRaw('provider_name,id,provider_image')->get();
                    $serviceId = 1;
                } else if ($requestType == 'DATA_CARD') {
                    $provider = Provider::where('service_id', '=', '3')
                        ->selectRaw('provider_image,provider_name,id')->get();
                    $serviceId = 3;
                } else if ($requestType == 'POSTPAID') {
                    $serviceId = 4;
                    $provider = Provider::where('service_id', '=', '4')
                        ->selectRaw('provider_image,provider_name,id')->get();

                } else if ($requestType == 'BROADBAND') {
                    $serviceId = 7;
                    $provider = Provider::where('service_id', '=', '7')
                        ->selectRaw('provider_image,provider_name,id')->get();

                } else if ($requestType == 'LANDLINE') {
                    $serviceId = 5;
                    $provider = Provider::where('service_id', '=', '5')
                        ->selectRaw('provider_image,provider_name,id')->get();

                } else if ($requestType == 'WATER') {
                    $serviceId = 12;
                    $provider = Provider::where('service_id', '=', '12')
                        ->selectRaw('provider_image,provider_name,id')->get();
                } else if ($requestType == 'GAS') {
                    $serviceId = 8;;
                    $provider = Provider::where('service_id', '=', '8')
                        ->selectRaw('provider_image,provider_name,id')->get();
                } else if ($requestType == 'ELECTRICITY') {
                    $serviceId = 6;
                    if ($request->state_id == '') {
                        $state_list = State::where('active_state', 1)->orderBy('name', 'asc')->pluck('name', 'id')->toArray();
                        $provider = Provider::where('state_id', '=', 8)->selectRaw('provider_image,provider_name,id')->get();
                        return response()->json([
                            'status' => 1,
                            'message' => 'success',
                            'stateList' => $state_list,
                            'provider' => $provider,
                            'baseUrl' => url('/') . '/',
                            'serviceId' => $serviceId,
                        ]);
                    } else $provider = Provider::where('state_id', '=', $request->state_id)->selectRaw('provider_image,provider_name,id')->get();


                } else if ($requestType == 'ELECTRICITY_PROVIDER') {
                    $serviceId = 6;
                    $provider = Provider::where('state_id', '=', $request->stateId)
                        ->selectRaw('provider_image,provider_name,id')->get();
                }

                return response()->json([
                    'status' => 1,
                    'message' => 'success',
                    'provider' => $provider,
                    'baseUrl' => url('/') . '/',
                    'serviceId' => $serviceId,
                ]);
            } else return response()->json(['status' => 2, 'message' => 'access denied',]);

        } else return $authentication;
    }


    //prepaid,d2h,datacard,postpaid,broadband
    //@Route
    public function cyberRecharge(Request $request)
    {
        $rules = array(
            'number' => 'required|numeric|min:10',
            'provider' => 'required|numeric',
            'amount' => 'required|numeric',
            'userId' => 'required',
            'token' => 'required',
        );
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            $missing_parameters = "Missing Parameter(s)";
            return Response::json(['status_id' => 2, 'message' => $missing_parameters]);
        }

        $appResp = ApiResponseToApp::create([
            'response_from_app' => json_encode($request->all()),
            'request_type' => 'RECHARGE',
            'user_id'=>$request->userId,
            'api_id' => 1,
        ]);

        $authentication = $this->checkLoginAuthentication($request);
     
        if ($authentication['status'] != 1) 
		{
            $appResp->response_to_app = json_encode($authentication);
            $appResp->save();
            return $authentication;
        }
        $userDetails = $authentication['userDetails'];
		$userId = $userDetails->id;


        if ($userId != 4) {
            $isAcitveService = $this->isActiveService(1);
            if ($isAcitveService){
                $response =['status' => 0, 'message' => $isAcitveService->message];
                $appResp->response_to_app = json_encode($response);
                $appResp->save();
                return response()->json($response);
            }
        }
        if ($userDetails->role_id == 5) {
            $now = new \DateTime();
            $datetime = $now->getTimestamp();
            $ctime = $now->format('Y-m-d H:i:s');
            //$userDetails = $this->getUserDetails();
            $balance = Balance::where('user_id', $userId)->first();
            $user_balance = $balance->user_balance;
            $amount = $request->amount;
            $provider = $request->provider;
            $number = $request->number;
            $response = $this->makeRecharge($userDetails, $number, $provider, $amount, $user_balance, $userId);
            $appResp->response_to_app = $response;
            $appResp->save();
            return $response;
        } else {
            $response = ['status' => '2', 'message' => 'you are not authenticate'];
            $appResp->response_to_app = json_encode($response);
            $appResp->save();
            return response()->json($response);
        }
    }

    private function makeRecharge($userDetails, $number, $provider, $amount, $user_balance, $user_id)
    {
        $now = new \DateTime();
        $datetime = $now->getTimestamp();
        $ctime = $now->format('Y-m-d H:i:s');
        if ($user_balance >= $amount && $amount >= 1) {
            $myd = $this->api_route($number, $provider, $amount, $user_id);
            if ($myd) {
                $startTime = date("Y-m-d H:i:s");
                $end_time = date('Y-m-d H:i:s', strtotime('+12 minutes', strtotime($startTime)));
                if ($existRecord = Report::where(['api_id' => $myd->api_id])->whereBetween('created_at', [$startTime,
                    $end_time])->first())
                    return Response::json(
                        array('status' => '2', 'message' => "Recharge has been initiated. Please wait for 3 Min"));
                $statusId = 3;
                $isOffline = 0;
                if (!in_array($myd->service_id, array(6))) {
                    if (!$myd->is_service_active)
                        return Response::json(array('status' => '2', 'message' => $myd->provider_name . " is down"));
                    elseif (!$myd->is_service_online) {
                        $statusId = 24;
                        $isOffline = 1;
                    } elseif (($myd->hold_txn_couter < $myd->max_hold_txn) && $myd->hold_txn_couter > 0 &&
                        ($myd->min_pass_amt_txn < $amount)) {
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
                if ($commission > $max_commission && $max_commission != 0)
                    $commission = $max_commission;
				if($distt_comm > $dist_max_commission)
					$distt_comm = $dist_max_commission;
				if($md_comm > $md_max_commission)
					$md_comm = $md_max_commission;
                if ($mc->is_error)
                    return Response::json(array('status' => '2', 'message' => 'Rechage Slab Error'));
                if ($commission < 0) {
                    $tds = 0;
                    $finalAmount = $amount - $commission;
                    $cr_amt = $commission;
                } else {
                    $tds = 0;//(($commission) * 5) / 100;
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
                        'type' => 'DB',
                        'tds' => $tds,
                        'gst' => 0,
                        'description' => "RECHARGE",
                        'recharge_type' => 1,
                        'dist_commission' => ($d_id != '') ? $this->calCommission($distt_comm) : 0,
                        'md_commission' => ($m_id != '') ? $this->calCommission($md_comm) : 0,
                        'admin_commission' => ($a_id != '') ? $admin_comm : 0,
                        'user_id' => $user_id,
                        'opening_balance' => $user_balance,
                        'credit_charge' => ($cr_amt > 0) ? $cr_amt : 0,
                        'debit_charge' => ($cr_amt < 0) ? abs($cr_amt) : 0,
                        'created_by' => $user_id,
                        'txn_type' => 'RECHARGE',
                        'profit' => 0,
                        'is_offline' => $isOffline,
                        'mode' => 'APP',
                        'total_balance' => Balance::where('user_id', $user_id)->select('user_balance')->first()->user_balance,
                    ]);
                    $insert_id = $rechargeReport->id;
                    DB::commit();
                } catch (Exception $e) {
                    DB::rollback();
                    return Response::json(array('status' => '2', 'message' => 'Whoops! Something went wrong. Please try again after somethime'));
                }
                $provider_code = $myd->provider_code;
                $provider_api = $myd->api_id;
                $vender_code = $myd->vender_code;
                $account = '';
                $cycle = '';


                if ($provider_api >= 1) {
                    if ($statusId == 3) {
                        switch ($provider_api) {
                            case 8:
                                $vendorCode = $myd->redpay;
                                $result = $this->redPayRecharge($user_id, $number, $vendorCode, $amount, $insert_id);
                                break;
                            case 13:
                                $vendorCode = $myd->suvidhaa;
                                $result = $this->aToZSuvidhaa($user_id, $number, $vendorCode,$amount, $insert_id);
                                break;							case 14:								$vendorCode = $myd->provider_code2;								$result=$this->mrobotics($user_id,$number,$vendorCode,$amount, $insert_id, $provider);								break;	
                            case 1:
                                $result = $this->cyber($user_id, $number, $provider, $amount, $insert_id, $account, $cycle);
                                break;
                            default;
                                $company = $provider;
                                $provider_api = 1;
                                $result = $this->cyber($user_id, $number, $provider, $amount, $insert_id, $account, $cycle);
                        }
                        $status = $result['status'];
                        //$status = 1;
                         $txnid = $result['txnid'];
                        //$txnid = 10;
                        $ref_id = $result['ref_id'];
                        //$ref_id = 123;
                        if ($status == 1) {
                            $status = '1';
                            $message = "Transaction Successfully Done, Operator Transaction ID : $txnid, Thanks";
                            $this->creditCommissionR($d_id, $m_id, $distt_comm, $md_comm, $insert_id, $number, $provider,
                                $provider_api, $a_id, $admin_comm, $amount, $user_id, $commission);
                            $rechargeReport->status_id = 1;
                            $rechargeReport->txnid = $txnid;
                            $rechargeReport->ref_id = $ref_id;
                            $rechargeReport->save();

                        } elseif ($status == 2 || $status == 'failure') {
                            $status = '2';
                            $message = "Transaction Failed, Please check Detail or Try After some time, Thanks";
                            Balance::where('user_id', $user_id)->increment('user_balance', $finalAmount);
                            $final_bal = Balance::where('user_id', $user_id)->select('user_balance')->first();
                            $rechargeReport->status_id = 2;
                            $rechargeReport->txnid = $txnid;
                            $rechargeReport->ref_id = $ref_id;
                            $rechargeReport->total_balance = $final_bal->user_balance;
                            $rechargeReport->save();

                        } else {
                            $this->creditCommissionR($d_id, $m_id, $distt_comm, $md_comm, $insert_id, $number, $provider, $provider_api, $a_id, $admin_comm, $amount, $user_id, $commission);
                            $rechargeReport->status_id = 3;
                            $rechargeReport->txnid = $txnid;
                            $rechargeReport->ref_id = $ref_id;
                            $rechargeReport->save();
                            $status = '1';
                            $message = "Transaction Submitted Successfully Done, Check Status in Transaction Report, Thanks";
                        }
                        return Response::json(array('payid' => $insert_id, 'operator_ref' => $txnid, 'status' => $status, 'message' => $message));
                    } else {
                        $this->creditCommissionR($d_id, $m_id, $distt_comm, $md_comm, $insert_id, $number, $provider, $provider_api, $a_id, $admin_comm, $amount, $user_id, $commission);
                        $rechargeReport->save();
                        $status = '1';
                        $message = "Transaction Submitted Successfully Thanks";
                        return Response::json(array('payid' => $insert_id, 'operator_ref' => '', 'status' => $status, 'message' => $message));
                    }
                } else {
                    return Response::json(array('payid' => $insert_id, 'operator_ref' => '', 'status' => '2', 'message' => 'Server Not Connected, Contact Customer Care'));
                }
            } else
                return Response::json(array('status' => '2', 'message' => 'No Route id defined. Please Contact with admin'));
        } else {
            return response()->json((array('status' => '2', 'message' => 'Low Balance or Minimun Recharge amount is 1')));
        }
    }

    public function creditCommissionR($d_id, $m_id, $distt_comm, $md_comm, $insert_id, $number, $provider, $api_id, $a_id, $admin_comm, $amount, $user_id, $commission)
    {
        $now = new \DateTime();
        $datetime = $now->getTimestamp();
        $ctime = $now->format('Y-m-d H:i:s');
        DB::beginTransaction();
        try {
            if ($d_id != '' && $distt_comm > 0) {
                $tds = (($distt_comm) * 5) / 100;
                $cr_amt = $distt_comm - ($tds);
                $user_balance = Balance::where('user_id', $d_id)->select('user_balance')->first();
                $total_balance_dist = $user_balance->user_balance + $cr_amt;
                $insert_dist_ptxn = Report::insertGetId([
                    'number' => $number,
                    'provider_id' => $provider,
                    'amount' => $amount,
                    'api_id' => $api_id,
                    'status_id' => 16,
                    'pay_id' => $datetime,
                    'created_by' => $user_id,
                    'txnid' => $insert_id,
                    'created_at' => $ctime,
                    'description' => "RECHARGE_COMMISSION",
                    'recharge_type' => 1,
                    'tds' => $tds,
                    'gst' => 0,
                    'user_id' => $d_id,
                    'profit' => 0,
                    'debit_charge' => 0,
                    'credit_charge' => $distt_comm,
                    'opening_balance' => $user_balance->user_balance,
                    'total_balance' => $total_balance_dist,
                    'mode' => 'APP',
                ]);
                Balance::where('user_id', $d_id)->increment('user_balance', $cr_amt);

            }
            if ($m_id != '' && $md_comm > 0) {
                $tds = (($md_comm) * 5) / 100;
                $cr_amt = $md_comm - ($tds);
                $user_balance = Balance::where('user_id', $m_id)->select('user_balance')->first();
                Balance::where('user_id', $m_id)->increment('user_balance', $cr_amt);
                $total_balance_md = $user_balance->user_balance;
                $insert_md_ptxn = Report::insertGetId([
                    'number' => $number,
                    'provider_id' => $provider,
                    'amount' => $amount,
                    'api_id' => $api_id,
                    'status_id' => 16,
                    'description' => "RECHARGE_COMMISSION",
                    'pay_id' => $datetime,
                    'txnid' => $insert_id,
                    'created_at' => $ctime,
                    'recharge_type' => 1,
                    'tds' => $tds,
                    'gst' => 0,
                    'user_id' => $m_id,
                    'profit' => 0,
                    'credit_charge' => $md_comm,
                    'debit_charge' => 0,
                    'opening_balance' => $user_balance->user_balance,
                    'total_balance' => $total_balance_md,
                    'created_by' => $user_id,
                    'mode' => 'APP',
                ]);
            }
            if ($a_id != '' && $admin_comm > 0) {
                $tds = 0;
                $cr_amt = $admin_comm - ($tds);
                $user_balance = Balance::where('user_id', $a_id)->first();
                $adminBalance = $user_balance->user_balance;
                Balance::where('user_id', $a_id)->increment('admin_com_bal', $cr_amt);
                $insert_md_ptxn = Report::insertGetId([
                    'number' => $number,
                    'provider_id' => $provider,
                    'amount' => $amount,
                    'api_id' => $api_id,
                    'status_id' => 16,
                    'description' => "RECHARGE_COMMISSION",
                    'pay_id' => $datetime,
                    'txnid' => $insert_id,
                    'created_at' => $ctime,
                    'type' => 'CR',
                    'recharge_type' => 1,
                    'tds' => $tds,
                    'gst' => 0,
                    'user_id' => $a_id,
                    'profit' => 0,
                    'debit_charge' => 0,
                    'credit_charge' => $admin_comm,
                    'opening_balance' => $adminBalance,
                    'admin_com_bal' => $user_balance->admin_com_bal + $admin_comm,
                    'total_balance' => $adminBalance,
                    'created_by' => $user_id,
                    'mode' => 'APP',
                ]);
            }
            DB::commit();
        } catch (Exception $e) {
            DB::rollback();
            return Response::json(['status_id' => 2, 'message' => 'Failed,something went wrong!']);
        }
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

    public function aToZSuvidhaa($user_id, $number, $vendorCode, $amount, $insert_id)
    {
        $url = config('constants.AToZSuvidhaa_URL') . "/Recharge_req.aspx?uid=" . config('constants.AToZSuvidhaa_USER') . "&pass=" . config('constants.AToZSuvidhaa_PSWD') . "&mno=" . $number . "&op=" . $vendorCode . "&amt=" . $amount . "&refid=" . $insert_id . "&format=json";
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $response = curl_exec($curl);
        curl_close($curl);
        Apiresponse::create(['message' => @$response, 'user_id' => @$user_id, 'report_id' => $insert_id, 'request_message' => $url, 'api_id' => 13, 'api_type' => 'A2Z_RECHARGE_TXN']);
        if ($response) {
            try {
                $resp_arr = json_decode($response);
                $txnid = $resp_arr->Operatorid;
                $REFNO = $resp_arr->RID;

                if (strtoupper($resp_arr->status) == "SUCCESS")
                    return array('status' => 1, 'txnid' => $txnid, 'ref_id' => $REFNO);
                elseif (strtoupper($resp_arr->status) == "FAILED")
                    return array('status' => 2, 'txnid' => "Recharge Unavailable", 'ref_id' => $REFNO);
                elseif (strtoupper($resp_arr->status) == "PENDING" || strtoupper($resp_arr->status) == "PROCESS")
                    return array('status' => 3, 'txnid' => $txnid, 'ref_id' => $REFNO);
                else
                    return array('status' => 3, 'txnid' => $txnid, 'ref_id' => $REFNO);
            } catch (\Exception $e) {
                return array('status' => 3, 'txnid' => '', 'ref_id' => '');
            }
        } else {
            return array('status' => 3, 'txnid' => '', 'ref_id' => '');
        }
    }

    public function redPayRecharge($user_id, $number, $vendorCode, $amount, $insert_id)
    {
        $url = config('constants.REDPAY_RECH_URL') . "/Recharge_req.php?token=" . config('constants.REDPAY_APITOKEN') . "&mobileno=" . $number . "&amount=" . $amount . "&operatorcode=" . $vendorCode . "&refid=" . $insert_id . "&format=json";
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $response = curl_exec($curl);
        curl_close($curl);
        // print_r($response);
        Apiresponse::create(['message' => @$response, 'user_id' => @$user_id, 'report_id' => $insert_id, 'request_message' => $url, 'api_id' => 8, 'api_type' => 'RECHARGE_TXN']);
        if ($response) {
            try {
                $resp_arr = json_decode($response);
                $txnid = $resp_arr->txnid;
                $REFNO = $resp_arr->refid;

                if ($resp_arr->status == "SUCCESS")
                    return array('status' => 1, 'txnid' => $txnid, 'ref_id' => $REFNO);
                elseif ($resp_arr->status == "FAILED")
                    return array('status' => 2, 'txnid' => $txnid, 'ref_id' => $REFNO);
                elseif ($resp_arr->status == "PENDING")
                    return array('status' => 3, 'txnid' => $txnid, 'ref_id' => $REFNO);
                else
                    return array('status' => 3, 'txnid' => $txnid, 'ref_id' => $REFNO);
            } catch (\Exception $e) {
                return array('status' => 3, 'txnid' => '', 'ref_id' => '');
            }
        } else {
            return array('status' => 3, 'txnid' => '', 'ref_id' => '');
        }
    }		public function mrobotics($user_id,$number,$vendorCode,$amount,$insert_id,$provider)	{		if($provider==112||$provider==42)		{			$content="api_token=".config('constants.MROBOTICS_API_TOKEN')."&mobile_no=".$number."&amount=".$amount."&company_id=".$vendorCode."&order_id=".$insert_id."&is_stv=".'true';		}			else{			$content="api_token=".config('constants.MROBOTICS_API_TOKEN')."&mobile_no=".$number."&amount=".$amount."&company_id=".$vendorCode."&order_id=".$insert_id."&is_stv=".'false';		}		$url = config('constants.MROBOTICS_RECHARGE_URL')."recharge";		$apiResponse = $this->getCurlPostMethod($url,$content);		Apiresponse::create(['message'=>$apiResponse,'user_id'=>$user_id,'report_id'=>$insert_id,'request_message'=>$url,'api_id'=>14,'api_type'=>'MROBOTICS_RECH_TXN']);		if($apiResponse)		{			try			{				$response=json_decode($apiResponse);				$txnid=$response->tnx_id;				$refNo=$response->id;				if($response->status=='success')					return array('status'=>1,'txnid'=>$txnid,'ref_id'=>$refNo);				elseif($response->status=='failure')					return array('status'=>2,'txnid'=>$txnid,'ref_id'=>$refNo);				elseif($response->status=='pending')					return array('status'=>3,'txnid'=>$txnid,'ref_id'=>$refNo);			}			catch(\Exception $e){				return array('status'=>3,'txnid'=>'','ref_id'=>'');			}		}		else		{			return array('status'=>3,'txnid'=>'','ref_id'=>'');		}	}

    function api_route($number, $provider, $amount, $user_id)
    {
        $provider_detail = Provider::find($provider);
        return $provider_detail;
    }


}
