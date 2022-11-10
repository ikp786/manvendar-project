<?php

namespace App\Http\Controllers\apiForwarding;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests;
use App\Report;
use App\Balance;
use App\Provider;
use Auth;
use Validator;
use App\Commission;
use Response;
use App\TransactionReportException;
use App\Apiresponse;
use App\Traits\CustomTraits;
use DB;
use Exception;
class RechargeController extends Controller
{
    use CustomTraits;
	public function getProvider(Request $request)
	{
		$rules = array(
            'userId' => 'required|numeric|regex:/^[0-9]+$/',
            'secretKey' => 'required|regex:/^[A-Za-z0-9]+$/',
        );
		$validator = Validator::make($request->all(), $rules);
		if ($validator->fails()){
			return $respToApi = [
				'status'=>10,	
				'message' => "Missing/Invalid Parameters ",
				'errors'=>$validator->errors()->getMessages(),
			];
		}
		$userdetail = Auth::guard('api')->user(); 
		$isApiUserValid = $this->newCheckAgentActivation($userdetail,$_SERVER['REMOTE_ADDR'],$request);
		if($isApiUserValid['status'] !=1)
        {
            return $isApiUserValid;
        }
		$providerDetails = Provider::selectRaw('provider_name,id,service_id,atoz_code')->orderBy('service_id','asc')->where('service_id','!=',0)->where('atoz_code','!=','')->where('status_id',1)->get();
		if(count($providerDetails))
		{
			$providers = $providerDetails->map(function($provider)
			{
				return [
					"providerKey"=>$provider->atoz_code,
					"serviceType"=>@$provider->service->service_name,
					"serviceId"=>@$provider->service_id,
					"providerName"=>$provider->provider_name,
				];
			});
			return response()->json(['status'=>1,'providerList'=>$providers]);
		}
		return response()->json(['status'=>0,'providerList'=>"No Provider Available"]);
	}

	public function recharge(Request $request)
    {
    	if(Auth::id() !=4)
		{
			$isAcitveService = $this->isActiveService(1);
			if($isAcitveService)
				return response()->json(['status_id'=>0,'message'=>$isAcitveService->message]);
		}
       $rules = array(
            'userId' => 'required|numeric|regex:/^[0-9]+$/',
            'secretKey' => 'required|regex:/^[A-Za-z0-9]+$/',
            'providerKey' => 'required|regex:/^[0-9A-Z_]+$/',
            'amount' => 'required|numeric|min:1|regex:/^[0-9]+$/',
            'number' => 'required|numeric|regex:/^[0-9]+$/',
            'clientId' => 'required|regex:/^[A-Za-z0-9_]+$/',
            );

        $userdetail = Auth::guard('api')->user(); 
		$user_id=$userdetail->id;		
        $api_response = Apiresponse::create(['api_id'=>404,'user_id'=>$user_id,'api_type'=>"RECHARGE",'report_id'=>1,'api_request'=>json_encode($request->all(),'message'=>'Heat by api user $user_id')]);
		$validator = Validator::make($request->all(), $rules);
		if ($validator->fails())
		{
			$respToApi = [
				'status'=>10,	
				'message' => "Missing/Invalid Parameters ",
				'errors'=>$validator->errors()->getMessages(),
			];
			$api_response->resp_to_api = json_encode($respToApi);
			$api_response->save();
			return Response::json($respToApi);
		}
		$isApiUserValid = $this->newCheckAgentActivation($userdetail,$_SERVER['REMOTE_ADDR'],$request);
		if($isApiUserValid['status'] !=1)
        {
            $api_response->resp_to_api=json_encode($isApiUserValid);
            $api_response->save();
            return $isApiUserValid;
        }
        $amount = $request->amount;
		$isAvailableBalance = $this->checkBalance($user_id,$amount);
        if($isAvailableBalance['status']==26)
		{
			$api_response->resp_to_api=json_encode($isAvailableBalance);
			$api_response->save();
			return response()->json($isAvailableBalance);
		}
		$user_balance=$isAvailableBalance['balance'];
		$clientId=$request->clientId;
		$number = $request->number;
		
        $duplicate = $this->checkDuplicateRecharge($number,$amount,$user_id);
        if($duplicate['status'] == 31) 
            return response()->json($duplicate);
		if($this->checkDuplicateClientId($user_id,$clientId))
		{
			$respToApi=
			[
				'status' => 73, 
				'message' => 'Client Id Duplicate',
				'txnId'=>'',
				'operatorId'=>'',
				'clientId'=>$clientId
			];
			$api_response->resp_to_api =json_encode($respToApi);
			$api_response->save(); 
			return response()->json($respToApi);
		}
		$isExistProvider = Provider::where('atoz_code',$request->providerKey)->first();
		if($isExistProvider=='')
		{
			$respToApi = [
				'status'=>60,	
				'message' => "Invalid Provider Key",
				'txnId'=>'',
				'operatorId'=>'',
				'clientId'=>$clientId
			];
			$api_response->resp_to_api = json_encode($respToApi);
			$api_response->save();
			return Response::json($respToApi);
		}
		$provider = $isExistProvider->id;
		 $myd = $this->api_route($number,$provider,$amount,$user_id);
		if($myd =='')
		{
			$respToApi=[
				'status'=>521,
				'message'=>"Server is down",
				'txnId' => '',
				'operatorId'=>'',
				'clientId'=>$clientId
			];
			$api_response->resp_to_api =json_encode($respToApi);
			$api_response->save(); 
			return response()->json($respToApi);
		}
		/*$provider_api = $myd->api_id;
		$jioOfferCode='';
		if($provider == 112 && $provider_api==1)
		{
			$jioOffers = DB::table('jio_recharge_plans')->where('amount',$amount)->first();
			
			if($jioOffers)
			{
				$jioOfferCode = $jioOffers->plan_id;
			}
			else{
				$respToApi=[
				'status' => 74, 
				'message' => 'Rechange Amount is invalid for Jio Rechange',
				'txnId' => '',
				'operatorId'=>'',
				'clientId'=>$clientId
				];
				$api_response->resp_to_api =json_encode($respToApi);
				$api_response->save(); 
				return response()->json($respToApi);
			}
		}*/
		try
        	{	
				$statusId=3;
				$isOffline=0;
				if(!in_array($myd->service_id,array(6)))
				{
					if(!$myd->is_service_active)
						return Response::json(array('success' => 'failure', 'message' => $myd->provider_name ." is down"));
					elseif(!$myd->is_service_online)
					{
						$statusId=24;
						$isOffline=1;
					}
					elseif(($myd->hold_txn_couter < $myd->max_hold_txn) && $myd->hold_txn_couter >0 && ($myd->min_pass_amt_txn < $amount))
					{
						$statusId=24;
						$isOffline=1;
						$myd->hold_txn_couter += 1;
						$myd->save();
					} 
				}
				else{
						$respToApi=[
						'status' => 75, 
						'message' => 'Route is not defined',
						'txnId' => '',
						'operatorId'=>'',
						'clientId'=>$clientId
						];
					$api_response->resp_to_api =json_encode($respToApi);
					$api_response->save(); 
					return response()->json($respToApi);
				}
				$scheme_id = Auth::user()->scheme_id; 
                $mc = $this->get_commission($scheme_id,$provider);
                $commission = $distt_comm = $md_comm = $admin_comm=0;
                if($mc) 
					{
						if($mc->type==0)
						{
							$max_commission = $mc->max_commission;
							$commission = ($amount * $mc->api_user) / 100;
							$admin_comm = ($amount * $mc->admin) / 100;
						}
						else
						{
							$max_commission = $mc->max_commission;
							$commission = $mc->api_user;
							$admin_comm = $mc->admin;
						}

						//$max_commission=$mc->max_commission;
						if($commission >$max_commission && $max_commission !=0)
							$commission = $max_commission;
						if($mc->is_error)
						return Response::json(array('success' => 'failure', 'message' => 'Rechage Slab Error'));
					} 
					else
					{
						$respToApi=[
								'status' => 15, 
								'message' => 'Your Scheme is not configured',
								'txnId' => '',
								'operatorId'=>'',
								'clientId'=>$clientId
							];
						$api_response->resp_to_api =json_encode($respToApi);
						$api_response->save(); 
						return response()->json($respToApi);
					}
					if($commission <=0)
					{
						$tds=0;
						$camount = ($amount - $commission + $tds);
						$netCommission = $commission - $tds;
					}
					else
					{
						$tds = (($commission*5)/100);
						$camount = ($amount - $commission + $tds);
						$netCommission = $commission - $tds;
					}
				
					$provider_code = $myd->provider_code2;
					
					$vender_code = $myd->vender_code;
					$account = '';
					$cycle = '';
			}
            catch(Exception $e)
            {
				$respToApi=['status' => 500, 
					'message' => 'Internal Server Error. Try again',
					'txnId' => '',
					'operatorId'=>'',
					'clientId'=>$clientId
				];
				$api_response->resp_to_api =json_encode($respToApi);
				$api_response->save(); 
				return response()->json($respToApi);
			}
			//$agent_parent_role = $userdetail->parent->role_id;
			//$agent_parent_id = $userdetail->parent_id;
			$comm =$commission;
			DB::beginTransaction();
            try
            {
				Balance::where('user_id',$user_id)->decrement('user_balance',$camount);
				$availableBalance = Balance::where('user_id', $user_id)->first()->user_balance;
                $rechargeReport = Report::create([
                        'number' => $number,
                        'provider_id'=>$provider,
                        'amount' => $amount,
                        'api_id' => $myd->api_id,
						'type'=>'DB',
                        'tds'=>$tds,
                        'gst'=>0,
                        'description' => "RECHARGE",
                        'recharge_type' => 1,
                        'admin_commission' => ($a_id!='') ? $admin_comm : 0,
						'opening_bal'=>$user_balance,
                        'pay_id' => time(),
						'recharge_type' => 1,
                        'user_id' => $user_id,
                        'profit' => 0,
                        'credit_charge' => ($comm > 0) ? $netCommission :0,
                        'debit_charge' => ($comm < 0) ? $netCommission:0,
                        'total_balance' =>$availableBalance,
                        'ackno' =>$request->clientId,
                        'created_by'=>Auth::id(),
						'txn_type'=>'RECHARGE',
						'is_offline' => $isOffline,
                ]);
				$insert_id = $reports->id;
				DB::commit();
            }
            catch(Exception $e)
            {
				$respToApi=[
					'status' => 500, 
					'message' => 'Internal Server Error. Try again',
					'txnId'=>'',
					'operatorId'=>'',
					'clientId'=>$clientId
				];
				$api_response->resp_to_api =json_encode($respToApi);
				$api_response->save(); 
				return response()->json($respToApi);
            }
			$account = '';
			$cycle = '';
			if ($provider_api >= 1) 
			{
				switch ($provider_api) 
				{
					case 8:
                        $vendorCode = $myd->redpay;
						$result = $this->redPayRecharge($user_id,$number,$vendorCode,$amount,$insert_id);
                            break; 
                    case 13: 
                       $vendorCode = $myd->suvidhaa;
					   $result = $this->aToZSuvidhaa($user_id,$number,$vendorCode,$amount, $insert_id);
						break;  
					case 1:
                        $result = $this->cyber($user_id,$number,$provider,$amount,$insert_id,$account,$cycle);
                        break;
                    default;
						$company = $provider;
						$provider_api = 1;
						$result = $this->cyber($user_id,$number,$provider,$amount,$insert_id,$account,$cycle);
				}
                try
                {
					if(true){
	                    $status = $result['status'];
	                    $txnid = $result['txnid'];
						$ref_id = $result['ref_id'];
					}
					else
					{
						$status = 1;
						$txnid = 1;
						$ref_id = 1;
					}
                    if ($status == 1) 
					{
                       
                        $status = 'success';
                        $message = "Transaction Successfully Done, Operator Transaction ID : $txnid, Thanks";
						$this->creditCommissionR($insert_id,$number,$provider,$provider_api,$a_id,$admin_comm,$amount,$user_id,$commission);
						$rechargeReport->status_id = 1;
						$rechargeReport->txnid = $txnid;
						$rechargeReport->ref_id = $ref_id;
						$rechargeReport->save();
                    } 
					 elseif ($status == 2 ||  $status == 'failure') 
					{
                        $status = 'failure';
                        $message = "Transaction Failed, Please check Detail or Try After some time, Thanks";
                        try
						{
	                        Balance::where('user_id', $user_id)->increment('user_balance', $finalAmount);
	                        $final_bal = Balance::where('user_id', $user_id)->select('user_balance')->first();
							$rechargeReport->status_id = 2;
							$rechargeReport->txnid = $txnid;
							$rechargeReport->ref_id = $ref_id;
							$rechargeReport->total_balance = $final_bal->user_balance;
							$rechargeReport->save(); 
						}
						catch(Exception $e)
						{
							DB::rollback();
							$status = 3;
							$message = "Pending";
						}
                    }
                    else 
					{
                       $this->creditCommissionR($insert_id,$number,$provider,$provider_api,$a_id,$admin_comm,$amount,$user_id,$commission);
						$rechargeReport->status_id = 3;
						$rechargeReport->txnid = $txnid;
						$rechargeReport->ref_id = $ref_id;
						$rechargeReport->save();
						$status = 'success';
                        $message = "Transaction Submitted Successfully Done, Check Status in Transaction Report, Thanks";
                    }
					$respToApi=['status'=>$status, 
							'message'=>$message,
							'txnId'=>$insert_id,
							'operatorId'=>$ref_id,
							'clientId'=>$clientId,
							'availableBalance'=>(float)$availableBalance
						];
					$api_response->resp_to_api =json_encode($respToApi);
					$api_response->save(); 
					return response()->json($respToApi);
                }
                catch(Exception $e)
                {
					$respToApi=[
							'status'=>3, 
							'message'=>'Pending',
							'txnId'=>$insert_id,
							'operatorId'=>'',
							'clientId'=>$clientId,
							'availableBalance'=>(float)$availableBalance
					];
					$api_response->resp_to_api =json_encode($respToApi);
					$api_response->save(); 
					return response()->json($respToApi);
                }
			}
			else{
					$respToApi=[
						'status'=>3, 
						'txnId'=>$insert_id, 
						'operatorId'=>'', 
						'message'=>'Provider is not available',
						'clientId'=>$clientId,
						'availableBalance'=>(float)number_format($availableBalance,2)
					];
				$api_response->resp_to_api=json_encode($respToApi);
				$api_response->save(); 
				return response()->json($respToApi);
			}        
    }
    public function creditCommissionR($insert_id,$number,$provider,$api_id,$a_id,$admin_comm,$amount,$user_id,$commission)
	{
		$now = new \DateTime();
		$datetime = $now->getTimestamp();
		$ctime = $now->format('Y-m-d H:i:s');
		DB::beginTransaction();
		try
		{	
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
							'description' => "RECHARGE_COMMISSION",
							'pay_id' => $datetime,
							'txnid'=>$insert_id,
							'created_at' => $ctime,
							'type'=>'CR',
							'recharge_type' => 1,
							'tds' => $tds,
							'gst' => 0,
							'user_id' => $a_id,
							'profit' => 0,
							'debit_charge'=>0,
							'credit_charge'=>$cr_amt,
							'opening_balance'=>$adminBalance-$admin_comm,
							'admin_com_bal'=>$user_balance->admin_com_bal+$cr_amt,
							'total_balance' =>$adminBalance,
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
	public function checkStatus(Request $request)
	{	
		$rules = array(
			'clientId' => 'required|regex:/^[A-Za-z0-9_]+$/',
			'userId' => 'required|numeric|regex:/^[0-9]+$/',
			'secretKey' => 'required|regex:/^[A-Za-z0-9]+$/',
		);
		$userdetail = Auth::guard('api')->user();
		$user_id = $userdetail->id;
		$api_response = Apiresponse::create(['api_id'=>404,'user_id'=>$user_id,'api_type'=>"CheckStatus",'report_id'=>1,'api_request'=>json_encode($request->all())]);
		$validator = Validator::make($request->all(), $rules);
		
		if ($validator->fails())
		{
			$respToApi = [
				'status'=>10,	
				'message' => "Missing/Invalid Parameters ",
				'errors'=>$validator->errors()->getMessages(),
			];
			$api_response->resp_to_api = json_encode($respToApi);
			$api_response->save();
			return Response::json($respToApi);
		}
		//print_r($request->all());die;
		$userdetail = Auth::guard('api')->user();
		$isApiUserValid = $this->newCheckAgentActivation($userdetail,$_SERVER['REMOTE_ADDR'],$request);
		if($isApiUserValid['status'] !=1)
		{
			$api_response->resp_to_api =json_encode($isApiUserValid);
            $api_response->save();
            return $isApiUserValid;
		}
		try
		{
			$report = Report::selectRaw('id,status_id,amount,recharge_type,txnid')->where(['ackno'=>$request->clientId,'user_id'=>$userdetail->id])->firstOrFail();
			$api_response->report_id=$report->id;
			$api_response->save();
			
		}
		catch(Exception $e)
		{
			$respToApi=[
				'status'=>404,
				"message"=>"No Record Found",
				"txnId"=>"",
				'clientId'=>$request->clientId,
				'operatorId' => '', 
				'availableBalance'=>(float)$userdetail->balance->user_balance
				];
			$api_response->resp_to_api =json_encode($respToApi);
			$api_response->save();
			return $respToApi;
		}
		$txnId = $report->id;
		$operatorId = $report->txnid;
		$ackno = $request->clientId;
		if($report->recharge_type !=1)
		{
			$respToApi=[
				'status'=>503,
				'message'=>"check status not allowed",
				'clientId'=>$ackno,
				'txnId' => $txnId, 
				'operatorId' => $operatorId, 
				'availableBalance'=>(float)$userdetail->balance->user_balance
				];
			$api_response->resp_to_api =json_encode($respToApi);
			$api_response->save();
			return $respToApi;
		}
		if($report->status_id==2)
			$respToApi=['status'=>55,'message'=>"Rechange failed","txnId"=>$txnId,'clientId'=>$ackno,'operatorId'=>$operatorId,'availableBalance'=>(float)$userdetail->balance->user_balance];
		else if($report->status_id==20)
			$respToApi=['status'=>56,'message'=>"Refund Available take through otp","txnId"=>$txnId,'clientId'=>$ackno,'operatorId'=>$operatorId,'availableBalance'=>(float)$userdetail->balance->user_balance];
		
		else if($report->status_id==21)
			$respToApi=['status'=>57,'message'=>"Rechange has been refunded successfully","txnId"=>$txnId,'clientId'=>$ackno,'operatorId'=>$operatorId,'availableBalance'=>(float)$userdetail->balance->user_balance];
		elseif(in_array($report->status_id,array(1,3)))
		{
			$respToApi=['status'=>$report->status_id,'message'=>$report->status->status,"txnId"=>$txnId,'clientId'=>$ackno,'operatorId'=>$operatorId,'availableBalance'=>(float)$userdetail->balance->user_balance];
		}
		else{
			$respToApi=['status'=>503,'message'=>"check status not allowed",'txnId' => '', 
				'operatorId' => '', 'availableBalance'=>(float)$userdetail->balance->user_balance];
		}
		$api_response->resp_to_api =json_encode($respToApi);
		$api_response->save();
		return $respToApi;
	}

    function api_route($number, $provider, $amount, $user_id) {
        $provider_detail = Provider::find($provider);
        return $provider_detail;
    }
    function get_commission($scheme_id, $provider) {
        return Commission::where('provider_id', $provider)->where('scheme_id', $scheme_id)->first(); 
    }
    private function checkDuplicateRecharge($number,$amount,$userId)
    {
        $formatted_date = date("Y-m-d H:i:s");
        $start_time = date('Y-m-d H:i:s',strtotime('-300 seconds',strtotime($formatted_date)));
        $result = Report::select('id')->where(['number'=>$number,'amount'=> $amount,'user_id'=>$userId])->whereIn('status_id',[1,3,24])->whereIn('api_id',[1,8,13])->where('created_at', '>=', $start_time)->orderBy('created_at', 'desc')->first();
        if ($result) {
            return array('status' => 31, 'message' => 'Same Amount, same number recharge is found, Try again after 5 Minutes');
        } else {
            return array('status' => 0, 'message' => 'not found');
        }
    }

}
