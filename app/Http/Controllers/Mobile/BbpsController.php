<?php

namespace App\Http\Controllers\Mobile;
use App\Http\Controllers\Controller;
use App\Library\Bbps;
use App\Traits\CustomAuthTraits;
use Validator;
use App\Balance;
use App\Provider;
use Illuminate\Http\Request;
use Response;
use App\Http\Requests;
use DB;
use App\Commission;
use App\Report;
use Auth;
use Exception;
use App\Traits\CustomTraits;

class BbpsController extends Controller {

    use CustomTraits,CustomAuthTraits;
    public function fetchBillAmount(Request $request)
    {
        $rules = array(
            'userId'=>'required',
            'token'=>'required',
            'provider' => 'required|numeric',
            'ca_number' => 'required'
        );
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return Response::json(array(
                'status' => 2,
                'errors' => $validator->getMessageBag()->toArray()
            ));
        }

        $authentication = $this->checkLoginAuthentication($request);
        if($authentication['status']==1){

            $number = $request->ca_number;
            $provider = $request->provider;
            $provider = Provider::where('id', $provider)->first();
            $cybercode = $provider->cyber;
            $cyber = new Bbps;
            return  $data = $cyber->check_bill($cybercode, $number);

        }else return $authentication;
    }


    function store (Request $request){
        $rules = array(
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
        $number = $request->number;
        $currentFistDate = date('Y-m-01'). " 00:00:00";
        if(Auth::id() !=4)
        {
            $isAcitveService = $this->isActiveService(15);
            if($isAcitveService)
                return response()->json(['success'=>"failure",'message'=>$isAcitveService->message]);
        }
        if(Report::where(['number'=>$number,'api_id'=>15])->where('created_at','>=',$currentFistDate)->whereIn('status_id',[1,3,24])->first())
            return Response::json(array('success' => 'failure', 'message' => 'This Payment alreday done, Please Check your statement'));
        $provider = $request->provider;
        $amount = $request->amount;
        $billerName = $request->billerName;
        $consumerNumber = $request->consumerNumber;
        if ($number && $provider  && $amount)
        {
            $userDetails = $this->getUserDetails();
            $balance = Balance::where('user_id', $userDetails->id)->first();
            return $sendrec = $this->makeRecharge($number, $provider, $amount,$billerName,$userDetails,$consumerNumber);
        }
        else{
            return Response::json(array('success' => 'failure', 'message' => 'all field requierd'));
        }
    }


    function makeRecharge ($number, $provider, $amount,$billerName,$userDetails,$consumerNumber)
    {
        $now = new \DateTime();
        $datetime = $now->getTimestamp();
        $ctime = $now->format('Y-m-d H:i:s');
        $user_id  =$userDetails->id;
        $user_balance =Balance::where('user_id', $user_id)->first();
        $user_balance = $user_balance->user_balance;
        if($user_balance < $amount)
        {
            return Response::json(array('success' => 'failure', 'message' => "Low balance"));
        }
        $myd = $this->api_route($provider);
        if ($myd)
        {
            $startTime = date("Y-m-d H:i:s");
            $end_time = date('Y-m-d H:i:s',strtotime('-3 minutes',strtotime($startTime)));
            if($existRecord = Report::where(['number'=>$number])->whereBetween('created_at', [$end_time,$startTime])->first())
                return Response::json(array('success' => 'failure', 'message' => "Bill Payment has been initiated. Please wait for 3 Min"));
            $statusId=3;
            $isOffline=0;
            if(in_array($myd->service_id,array(6)))
            {
                if(!$myd->is_service_active)
                    return Response::json(array('success' => 'failure', 'message' => $myd->provider_name ." is down"));
                elseif(!$myd->is_service_online)
                {
                    $statusId=24;
                    $isOffline=1;
                }
                elseif(($myd->hold_txn_couter <= $myd->max_hold_txn) && $myd->hold_txn_couter >=0 && ($myd->min_pass_amt_txn < $amount))
                {
                    $statusId=24;
                    $isOffline=1;
                    $myd->hold_txn_couter += 1;
                    $myd->save();
                }
            }
            else{
                return Response::json(array('success' => 'failure', 'message' => "Route is not defined"));
            }
            $scheme_id = Auth::user()->scheme_id;
            $mc = $this->get_commission($scheme_id, $provider);
            if($mc =='')
            {
                return Response::json(array('success' => 'failure', 'message' => 'Sar Charge is not configured'));
            }
            if($mc->type==0)
            {
                $max_commission = $mc->max_commission;
                $commission = ($amount * $mc->r) / 100;
                $distt_comm = ($amount * $mc->d) / 100;
                $md_comm = ($amount * $mc->md) / 100;
                $admin_comm = ($amount * $mc->admin) / 100;
            }
            else
            {
                $max_commission = $mc->max_commission;
                $commission = $mc->r;
                $distt_comm = $mc->d;
                $md_comm = $mc->md;
                $admin_comm = $mc->admin;
            }
            if($commission > $max_commission)
                $commission = $max_commission;
            if($mc->is_error)
                return Response::json(array('success' => 'failure', 'message' => 'Slab Error. Please contact to admin'));
            if($commission < 0){
                $tds = 0;
                $finalAmount = $amount - $commission;
                $cr_amt = $commission;
            }
            else
            {
                $tds =  (($commission)* 5)/100;
                $cr_amt = $commission - ($tds);
                $finalAmount = $amount - $cr_amt;
            }
            $d_id = $m_id  = $a_id = '';
            $agent_parent_role = Auth::user()->parent->role_id;
            $agent_parent_id = Auth::user()->parent_id;
            if(Auth::user()->parent_id == 1)
            {
                $d_id = $m_id ='';
                $a_id = 1;

            }
            else
            {
                if($agent_parent_role ==4)
                {

                    $agent_parent_details = Auth::user()->parent;
                    $dist_parent_id = $agent_parent_details->parent_id;// Don't go at variable name
                    if($dist_parent_id ==1)
                    {
                        $d_id=$agent_parent_id;
                        $m_id='';
                        $a_id = 1;
                    }
                    else
                    {
                        $d_id=$agent_parent_id;
                        $m_id=$dist_parent_id;
                        $a_id = 1;

                    }
                }
                else if($agent_parent_role == 3)
                {
                    $d_id='';
                    $m_id=$agent_parent_id;
                    $a_id = 1;
                }
                elseif($agent_parent_role == 1)
                {
                    $d_id='';
                    $m_id='';
                    $a_id = 1;
                }
            }
            DB::beginTransaction();
            try
            {
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
                    'dist_commission' => ($d_id!='')? $this->calCommission($distt_comm):0,
                    'md_commission' => ($m_id!='') ? $this->calCommission($md_comm): 0,
                    'admin_commission' => ($a_id!='') ? $admin_comm : 0,
                    'user_id' => $user_id,
                    'opening_balance' => $user_balance,
                    'credit_charge'=>($cr_amt>0)? $cr_amt : 0,
                    'debit_charge'=>($cr_amt<0)? abs($cr_amt) : 0,
                    'created_by'=>Auth::id(),
                    'txn_type'=>'BILL PAYMENT',
                    'type' => 'DB',
                    'tds' => $tds,
                    'profit' => 0,
                    'is_offline' => $isOffline,
                    'biller_name' => $billerName,
                    'customer_number' => $consumerNumber,
                    'total_balance' => Balance::where('user_id',$user_id)->select('user_balance')->first()->user_balance,
                ]);
                $insert_id = $rechargeReport->id;
                DB::commit();
            }
            catch(Exception $e)
            {
                DB::rollback();
                return Response::json(array('success' => 'failure', 'message' => 'Whoops! Something went wrong. Please try again after somethime'));
            }
            $provider_api = $myd->api_id;
            $cybercode = $myd->cyber;
            $cyber = new Bbps;
            $account = '';
            $cycle = '';
            if($statusId == 3)
            {
                switch ($provider_api)
                {

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
                /* $status = 1;
                $txnid = 1;  */
                if ($status == 1)
                {
                    $status = 'SUCCESS';
                    $message = "Transaction Successfully Done, Operator Transaction ID : $txnid, Thanks";
                    $this->creditCommissionR($d_id,$m_id,$distt_comm,$md_comm,$insert_id,$number,$provider,$provider_api,$a_id,$admin_comm,$amount,$user_id,$commission);
                    $rechargeReport->status_id = 1;
                    $rechargeReport->txnid = $txnid;
                    $rechargeReport->save();

                    if ($service_id==6) {
                        @$amount=$request->amount;
                        @$billerName=$request->biller_name;
                        @$billNumber=$request->number;
                        @$consumerNumber=$request->customer_number;
                        @$msg ="Dear $billerName($billNumber) Your Electricity bill Successfull with amount $amount ";
                        @$msg=urlencode(@$msg);
                        $this->sendSMS($consumerNumber,$msg,1);
                    }

                } elseif ($status == 2)
                {
                    $status = 'failure';
                    $message = "Transaction Failed, Please check Detail or Try After some time, Thanks";
                    Balance::where('user_id', $user_id)->increment('user_balance', $finalAmount);
                    $final_bal = Balance::where('user_id', $user_id)->select('user_balance')->first();
                    $rechargeReport->status_id = 2;
                    $rechargeReport->txnid = $txnid;
                    $rechargeReport->total_balance = $final_bal->user_balance;
                    $rechargeReport->save();

                }
                else
                {
                    $this->creditCommissionR($d_id,$m_id,$distt_comm,$md_comm,$insert_id,
                        $number,$provider,$provider_api,$a_id,$admin_comm,$amount,$user_id,$commission);
                    $rechargeReport->status_id = 3;
                    $rechargeReport->txnid = $txnid;
                    $rechargeReport->ref_id = $ref_id;
                    $rechargeReport->save();
                    $status = 'PENDING';
                    $message = "Transaction Submitted Successfully Done, Check Status in Transaction Report, Thanks";
                }
                return Response::json(array('payid' => $insert_id, 'operator_ref' => $txnid, 'status' => $status, 'message' => $message));
            }
            else{
                $this->creditCommissionR($d_id,$m_id,$distt_comm,$md_comm,$insert_id,$number,$provider,$provider_api,$a_id,$admin_comm,$amount,$user_id,$commission);
                $rechargeReport->save();
                $status = 'SUCCESSFULLY SUBMITTED';
                $message = "Transaction Submitted Successfully Thanks";
                @$msg ="Dear $billerName($number) Your Electricity bill Successfull with amount $amount ";
                @$msg=urlencode(@$msg);
                $this->sendSMS($consumerNumber,$msg,1);
                return Response::json(array('payid' => $insert_id, 'operator_ref' => '', 'status' => "SUCCESSFULLY SUBMITTED",'message'=>$message));
            }
        }
        else
            return Response::json(array('success' => 'failure', 'message' => 'No Route id defined. Please Contact with admin'));


    }
    function get_commission($scheme_id, $provider) {
        return Commission::where('provider_id', $provider)->where('scheme_id', $scheme_id)->first();
    }
    private function api_route($provider) {
        $provider_detail = Provider::find($provider);
        return $provider_detail;
    }
    public function creditCommissionR($d_id,$m_id,$distt_comm,$md_comm,$insert_id,$number,$provider,$api_id,
                                      $a_id,$admin_comm,$amount,$user_id,$commission)
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
                    'created_by'=>Auth::id(),
                    'txnid'=>$insert_id,
                    'created_at' => $ctime,
                    'description' => "BILL_COMMISSION",
                    'recharge_type' => 1,
                    'tds' => $tds,
                    'gst' => 0,
                    'user_id' => $d_id,
                    'profit' => 0,
                    'debit_charge'=>0,
                    'credit_charge'=>$cr_amt,
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
                    'credit_charge'=>$cr_amt,
                    'debit_charge'=>0,
                    'opening_balance'=>$user_balance->user_balance,
                    'total_balance' => $total_balance_md,
                    'created_by'=>Auth::id(),
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
                    'created_by'=>Auth::id(),
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


}
