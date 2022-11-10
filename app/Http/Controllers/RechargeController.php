<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Apiresponse;
use App\Provider;
use Auth;
use Validator;
use App\Commission;
use Response;
use App\Balance;
use App\User;
use App\Report;
use DB;
use Exception;
use App\Traits\CustomTraits;
class RechargeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
	 use CustomTraits;
	var $baseDTHUrl = "http://api.datayuge.in";
	
    public function index()
    {
		if(in_array(Auth::user()->role_id ,array(15,5)))
		{
			 $report = Report::where('user_id', Auth::id())
		 		->whereIn('provider_id',[1,2,3,4,5,6,7,8,9,10,47,112])
                ->orderBy('id', 'DESC')
				->whereIn('status_id',[1,2,3,21,24])
                ->take(5)->get();
			//$provider = Provider::where('service_id', '=', '1')->pluck('provider_name', 'id');
			$provider = Provider::where('service_id', '=', '1')->selectRaw('provider_name,id,provider_image')->get();
			$serviceId = 1;
			return view('agent.recharge.prepaid',compact('provider','report','serviceId'));
		}
		return view('errors.permission-denied');
    }
	public function prepaid2()
    {
		if(in_array(Auth::user()->role_id ,array(15,5)))
		{
			return view('agent.recharge.prepaid2');
		}
		return view('errors.page-not-found');
    }
    public function postpad()
    {
		if(in_array(Auth::user()->role_id ,array(15,5)))
		{
			$report = Report::where('user_id', Auth::id())
		 		->whereIn('provider_id',[23,24,25,28,58,73,84])
                ->orderBy('id', 'DESC')
				->whereIn('status_id',[1,2,3,21])
                
                ->take(5)->get();
				$serviceId = 4;
			$provider = Provider::where('service_id', '=', '4')->selectRaw('provider_image,provider_name,id')->get();
			return view('agent.recharge.postpaid',compact('provider','report','serviceId'));
		}
		return view('errors.page-not-found');
    }
    public function dth()
    {
		if(in_array(Auth::user()->role_id ,array(15,5)))
		{
			
			$report = Report::where('user_id', Auth::id())
		 		->whereIn('provider_id',[12,13,14,15,16,17,225])
                ->orderBy('id', 'DESC')
				->whereIn('status_id',[1,2,3,21])
                
                ->take(5)->get();
			//$provider = Provider::where('service_id', '=', '2')->pluck('provider_name', 'id');
			$provider = Provider::where('service_id', '=', '2')->selectRaw('provider_image,provider_name, id')->get();
						$serviceId = 2;
			return view('agent.recharge.dth',compact('provider','report','serviceId'));
		}
		return view('errors.page-not-found');
        
    }
    public function datacard()
    {
		if(in_array(Auth::user()->role_id ,array(15,5)))
		{
			$report = Report::where('user_id', Auth::id())
		 		->whereIn('provider_id',[18,19,21,22,82,214,215,216,217,218])
                ->orderBy('id', 'DESC')
				->whereIn('status_id',[1,2,3,21])
                
                ->take(5)->get();
				$serviceId = 3;
			$provider = Provider::where('service_id', '=', '3')->selectRaw('provider_image,provider_name,id')->get();
			return view('agent.recharge.datacard',compact('provider','report','serviceId'));
		}
		return view('errors.page-not-found');
    }     
   public function broadband()
    { 
		if(in_array(Auth::user()->role_id ,array(15,5)))
		{
			$report = Report::where('user_id', Auth::id())
		 		->whereIn('provider_id',[219,221,223])
		 		->whereIn('status_id',[1,2,3,21])
                ->orderBy('id', 'DESC')
                
                ->take(5)->get();
				$serviceId = 7;
			$provider = Provider::where('service_id', '=', '7')->selectRaw('provider_image,provider_name,id')->get();
			return view('agent.recharge.broadband',compact('provider','report','serviceId'));
		}
		return view('errors.page-not-found');
        
    } 
    public function landline()
    {
		if(in_array(Auth::user()->role_id ,array(15,5)))
		{
			$serviceId = 5;
			$provider = Provider::where('service_id', '=', '5')->pluck('provider_name', 'id');
			return view('agent.recharge.landline',compact('provider','serviceId'));
		}
		return view('errors.page-not-found');
        
    }
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }
    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }
    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }
    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
	public function getDTHOffer(Request $request)
	{
		$operatorid = $request->provider;
		$recharge_type = $request->recharge_type;
		$url = $this->baseDTHUrl ."/V1/dthplans/?apikey=". config('constants.RECHARGE_API_OFFER_KEY') . "&operatorid=".$operatorid . "&recharge_type=".$recharge_type;
		return $this->getCurlMethod($url);
	}
	public function getSpecialNumberRechargeOffer(Request $request)
	{
		$operatorid = $request->provider;
		$mob_number = $request->mobile_number;
		$url = $this->baseDTHUrl ."/v1/plansByNumber/" . $operatorid ."?number".$mob_number."&apikey=". config('constants.RECHARGE_API_OFFER_KEY');
		return $this->getCurlMethod($url);
	}
	public function getPrepedRechargeOffer(Request $request)
	{
		$operatorid = $request->provider;
		$circle_id = $request->circle_id;
		$recharge_type = $request->recharge_type;
		$url = $this->baseDTHUrl ."/v6/rechargeplans/?apikey=". config('constants.RECHARGE_API_OFFER_KEY') . "&operator_id=".$operatorid ."&circle_id".$circle_id. "&recharge_type=".$recharge_type;
		return $this->getCurlMethod($url);
	}
	public function getDataCardPlans(Request $request)
	{
		$operatorid = $request->provider;
		$circle_id = $request->circle_id;
		$recharge_type = $request->recharge_type;
		$url = $this->baseDTHUrl ."/v1/datacardplans/?apikey=". config('constants.RECHARGE_API_OFFER_KEY') . "&operator_id=".$operatorid ."&circleid".$circle_id. "&recharge_type=".$recharge_type;
		return $this->getCurlMethod($url);
	}
	private function getCurlMethod($url)
	{
		$curl = curl_init();
		curl_setopt_array($curl, array(
		CURLOPT_URL =>$url,
		CURLOPT_RETURNTRANSFER => true,
		CURLOPT_ENCODING => "",
		CURLOPT_MAXREDIRS => 10,
		CURLOPT_TIMEOUT => 30,
		CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		CURLOPT_CUSTOMREQUEST => "GET",
		));
		$response = curl_exec($curl);
		$err = curl_error($curl);
		curl_close($curl);
		return $response;
	}
	public function cyberRecharge(Request $request) 
	{
		if(Auth::id() !=4)
		{
				$isAcitveService = $this->isActiveService(1);
				if($isAcitveService)
					return response()->json(['status_id'=>0,'message'=>$isAcitveService->message]);
		}
		if(Auth::user()->role_id==5)
        {
            $rules = array(
			'number' => 'required|numeric',
            'provider' => 'required|numeric',
            'amount' => 'required|numeric|min:0',
           );
	        $validator = Validator::make($request->all(), $rules);
	        if ($validator->fails()) {
	            $missing_parameters = "Missing Parameter(s)"; 
	            return Response::json(['status_id'=>2,'message'=>$missing_parameters]);
	        } 
	        $mode = $request->mode;
	        $now = new \DateTime();
	        $datetime = $now->getTimestamp();
	        $ctime = $now->format('Y-m-d H:i:s');
			//$userDetails = $this->getUserDetails();
			$balance = Balance::where('user_id', Auth::id())->first();
			  $user_id = Auth::id();
	      
	        $user_balance = $balance->user_balance;
	        $amount = $request->amount;
	        $provider = $request->provider;
	        $number = $request->number;
			$availableBalanceWithBlockedAmount = $user_balance - $amount;
			$isHaveBlockedAmount=$this->isHaveBlockedAmount(Auth::user()->member->blocked_amount,$availableBalanceWithBlockedAmount);
			if($isHaveBlockedAmount['status']==91){
				return response()->json(['status'=>0,'message'=>$isHaveBlockedAmount['message']]);
			}
	        $sendrec = $this->makeRecharge($number, $provider, $amount,$user_balance,$user_id);
			if ($mode == 'web') {
	            $message = "Recharge Request Successfully Submited, Check Transaction Report for more detail";
	            return redirect('home')->with('status', $message);
	        } else {
	            return $sendrec;
	        }
   		}
        else
        {
            return "Not Permission";
        }
    }

    private function makeRecharge($number, $provider, $amount,$user_balance,$user_id) {
        $now = new \DateTime();
        $datetime = $now->getTimestamp();
        $ctime = $now->format('Y-m-d H:i:s');
        if ($user_balance >= $amount && $amount >= 1) 
		{
            $myd = $this->api_route($number, $provider, $amount, $user_id);
			if ($myd) 
			{
				$startTime = date("Y-m-d H:i:s");
				$end_time = date('Y-m-d H:i:s',strtotime('-24 minutes',strtotime($startTime)));
				$isPaymetDone = Report::where(['number'=>$number,'amount'=>$amount,'api_id'=>$myd->api_id])->where('created_at','>=',$end_time)->whereIn('status_id',[1,3,24])->first();
				if($isPaymetDone)
					return Response::json(array('success' => 'failure', 'message' => 'This Recharge alreday done, Please Check your statement. Rechage Date '.date_format($isPaymetDone->created_at,"d-m-Y H:i:s")));
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
				$md_max_commission = $mc->md_max_commission;
				$dist_max_commission = $mc->dist_max_commission;
				if($commission >$max_commission && $max_commission !=0)
					$commission = $max_commission;
				if($distt_comm > $dist_max_commission)
					$distt_comm = $dist_max_commission;
				if($md_comm > $md_max_commission)
					$md_comm = $md_max_commission;
				if($mc->is_error)
					return Response::json(array('success' => 'failure', 'message' => 'Rechage Slab Error'));
				if($commission < 0){
					$tds = 0;
					$finalAmount = $amount - $commission;
					$cr_amt = $commission;
				}
				else
				{
					$tds =  0;//(($commission)* 5)/100;
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
                            'type'=>'DB',
                            'tds'=>$tds,
                            'gst'=>0,
                            'description' => "RECHARGE",
                            'recharge_type' => 1,
                            'dist_commission' => ($d_id!='')?$this->calCommission($distt_comm):0,
                            'md_commission' => ($m_id!='') ? $this->calCommission($md_comm): 0,
                            'admin_commission' => ($a_id!='') ? $admin_comm : 0,
                            'user_id' => $user_id,
                            'opening_balance' => $user_balance,
                            'credit_charge'=>($cr_amt>0)? $cr_amt : 0,
							'debit_charge'=>($cr_amt<0)? abs($cr_amt) : 0, 
							'created_by'=>Auth::id(),
							'txn_type'=>'RECHARGE',
							'profit' => 0,
							'is_offline' => $isOffline,
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
                $provider_code = $myd->provider_code;
                $provider_api = $myd->api_id;
                $vender_code = $myd->vender_code;
                $account = '';
                $cycle = '';
                  

                if ($provider_api >= 1) 
				{
					if($statusId == 3)
					{
						
                    switch ($provider_api) 
					{
                       
                        case 8:
                            $vendorCode = $myd->redpay;
							$result = $this->redPayRecharge($user_id, $number, $vendorCode,$amount, $insert_id);
                            break; 
                        case 13: 
                           $vendorCode = $myd->suvidhaa;
							$result = $this->aToZSuvidhaa($user_id, $number, $vendorCode,$amount, $insert_id);   
							break;
						case 14:
	                        $vendorCode = $myd->provider_code2;
	                        $result=$this->mrobotics($user_id,$number,$vendorCode,$amount, $insert_id,$provider);
							break;	
						case 1:
                            $result = $this->cyber($user_id, $number, $provider, $amount, $insert_id, $account, $cycle);
                            break;
                        default;
							$company = $provider;
							$provider_api = 1;
							$result = $this->cyber($user_id, $number, $provider, $amount, $insert_id, $account, $cycle);
                    }
					$status = $result['status'];
                    $txnid = $result['txnid'];
                    $ref_id = $result['ref_id']; 
					 $apiMessage = isset($result['message'])?$result['message']:''; 	
                    if ($status == 1) 
					{
                        $status = 'success';
                        $message = "Transaction Successfully Done, Operator Transaction ID : $txnid, Thanks";
						$this->creditCommissionR($d_id,$m_id,$distt_comm,$md_comm,$insert_id,$number,$provider,$provider_api,$a_id,$admin_comm,$amount,$user_id,$commission);
						$rechargeReport->status_id = 1;
						$rechargeReport->txnid = $txnid;
						$rechargeReport->ref_id = $ref_id;
						$rechargeReport->save();

                    } elseif ($status == 2 ||  $status == 'failure') 
					{
                        $status = 'failure';
						$message = "Transaction Failed : $apiMessage";
                        //$message = "Transaction Failed, Please check Detail or Try After some time, Thanks";
                        Balance::where('user_id', $user_id)->increment('user_balance', $finalAmount);
                        $final_bal = Balance::where('user_id', $user_id)->select('user_balance')->first();
						$rechargeReport->status_id = 2;
						$rechargeReport->txnid = $txnid;
						$rechargeReport->ref_id = $ref_id;
						$rechargeReport->total_balance = $final_bal->user_balance;
						$rechargeReport->save();
                        
                    }
                     else 
					 {
                       $this->creditCommissionR($d_id,$m_id,$distt_comm,$md_comm,$insert_id,$number,$provider,$provider_api,$a_id,$admin_comm,$amount,$user_id,$commission);
						$rechargeReport->status_id = 3;
						$rechargeReport->txnid = $txnid;
						$rechargeReport->ref_id = $ref_id;
						$rechargeReport->save();
						$status = 'success';
                        $message = "Transaction Submitted Successfully Done, Check Status in Transaction Report, Thanks";
                    }
                    return Response::json(array('payid' => $insert_id, 'operator_ref' => $txnid, 'status' => $status, 'message' => $message));
					}
					else{
					 $this->creditCommissionR($d_id,$m_id,$distt_comm,$md_comm,$insert_id,$number,$provider,$provider_api,$a_id,$admin_comm,$amount,$user_id,$commission);
						$rechargeReport->save();
						$status = 'success';
                        $message = "Transaction Submitted Successfully Thanks";	
						return Response::json(array('payid' => $insert_id, 'operator_ref' => '', 'status' => $status,'message'=>$message));
					}
                } else {
                    return Response::json(array('payid' => $insert_id, 'operator_ref' => '', 'status' => 'failure', 'message' => 'Server Not Connected, Contact Customer Care'));
                }
            }else
			return Response::json(array('success' => 'failure', 'message' => 'No Route id defined. Please Contact with admin'));
        } else {
            return Response::json(array('success' => 'failure', 'message' => 'Low Balance or Minimun Recharge amount is 1'));
        }
    }

	public function creditCommissionR($d_id,$m_id,$distt_comm,$md_comm,$insert_id,$number,$provider,$api_id,$a_id,$admin_comm,$amount,$user_id,$commission)
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
								'description' => "RECHARGE_COMMISSION",
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
				$total_balance_md =  $user_balance->user_balance + $cr_amt;
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
	
    function get_commission($scheme_id, $provider) {
        return Commission::where('provider_id', $provider)->where('scheme_id', $scheme_id)->first(); 
    }
	
    public function cyber($user_id, $number, $provider_id, $amount, $insert_id, $account, $cycle)
    {
        $cyber = new \App\Library\Cyber;
		$data = $cyber->CallRecharge($provider_id, $insert_id, $number, $amount, $user_id, $account, $cycle);
		return $data;
    }
	
	public function mrobotics($user_id,$number,$vendorCode,$amount,$insert_id,$provider)
	{
		if($provider==112||$provider==42)
		{
			$content="api_token=".config('constants.MROBOTICS_API_TOKEN')."&mobile_no=".$number."&amount=".$amount."&company_id=".$vendorCode."&order_id=".$insert_id."&is_stv=".'true';
		}	
		else{
			$content="api_token=".config('constants.MROBOTICS_API_TOKEN')."&mobile_no=".$number."&amount=".$amount."&company_id=".$vendorCode."&order_id=".$insert_id."&is_stv=".'false';
		}
		$url = config('constants.MROBOTICS_RECHARGE_URL')."recharge";
		$apiResponse = $this->getCurlPostMethod($url,$content);
		Apiresponse::create(['message'=>$apiResponse,'user_id'=>$user_id,'report_id'=>$insert_id,'request_message'=>$url,'api_id'=>14,'api_type'=>'MROBOTICS_RECH_TXN']);
		if($apiResponse)
		{
			try
			{
				$response=json_decode($apiResponse);
				$txnid=$response->tnx_id;
				$refNo=$response->id;
				$reason=$response->response;
				if($response->status=='success')
					return array('status'=>1,'txnid'=>$txnid,'ref_id'=>$refNo);
				elseif($response->status=='failure')
					return array('status'=>2,'txnid'=>$txnid,'ref_id'=>$refNo,'message'=>$reason);
				elseif($response->status=='pending')
					return array('status'=>3,'txnid'=>$txnid,'ref_id'=>$refNo);
			}
			catch(\Exception $e){
				return array('status'=>3,'txnid'=>'','ref_id'=>'');
			}
		}
		else
		{
			return array('status'=>3,'txnid'=>'','ref_id'=>'');
		}
	}
	
	public function aToZSuvidhaa($user_id, $number, $vendorCode, $amount, $insert_id)
    {
    	$url =config('constants.AToZSuvidhaa_URL')."/Recharge_req.aspx?uid=".config('constants.AToZSuvidhaa_USER')."&pass=".config('constants.AToZSuvidhaa_PSWD')."&mno=".$number."&op=".$vendorCode."&amt=".$amount."&refid=".$insert_id."&format=json";
    	$curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $response = curl_exec($curl);
        curl_close($curl);
        Apiresponse::create(['message'=>@$response,'user_id'=>@$user_id,'report_id'=>$insert_id,'request_message'=>$url,'api_id'=>13,'api_type'=>'A2Z_RECHARGE_TXN']);
        if($response)
        	{
             try
                {
                    $resp_arr = json_decode($response);
                    $txnid = $resp_arr->Operatorid;
                    $REFNO = $resp_arr->RID;

                    if(strtoupper($resp_arr->status) == "SUCCESS")
						return array('status'=>1,'txnid'=>$txnid,'ref_id'=>$REFNO);
                    elseif(strtoupper($resp_arr->status) == "FAILED")
						return array('status'=>2,'txnid'=>"Recharge Unavailable",'ref_id'=>$REFNO);
                    elseif(strtoupper($resp_arr->status) == "PENDING" || strtoupper($resp_arr->status) == "PROCESS")
						return array('status'=>3,'txnid'=>$txnid,'ref_id'=>$REFNO);
                    else
						return array('status'=>3,'txnid'=>$txnid,'ref_id'=>$REFNO);
             	}
             	catch(\Exception $e)
            	{
              		return array('status'=>3,'txnid'=>'','ref_id'=>'');
            	}
        	}
        	else
        	{
           	 return array('status'=>3,'txnid'=>'','ref_id'=>'');
        	}
    }
    public function redPayRecharge($user_id, $number, $vendorCode, $amount, $insert_id)
    {
    	  $url =config('constants.REDPAY_RECH_URL')."/Recharge_req.php?token=".config('constants.REDPAY_APITOKEN')."&mobileno=".$number."&amount=".$amount."&operatorcode=".$vendorCode."&refid=".$insert_id."&format=json";
    	    $curl = curl_init();
            curl_setopt($curl, CURLOPT_URL, $url);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
            $response = curl_exec($curl);
            curl_close($curl);
           // print_r($response);
            Apiresponse::create(['message'=>@$response,'user_id'=>@$user_id,'report_id'=>$insert_id,'request_message'=>$url,'api_id'=>8,'api_type'=>'RECHARGE_TXN']);
        if($response)
            {
             try
                {
                    $resp_arr = json_decode($response);
                    $txnid = $resp_arr->txnid;
                    $REFNO = $resp_arr->refid;

                    if($resp_arr->status == "SUCCESS")
						return array('status'=>1,'txnid'=>$txnid,'ref_id'=>$REFNO);
                    elseif($resp_arr->status == "FAILED")
						return array('status'=>2,'txnid'=>$txnid,'ref_id'=>$REFNO);
                    elseif($resp_arr->status == "PENDING")
						return array('status'=>3,'txnid'=>$txnid,'ref_id'=>$REFNO);
                    else
						return array('status'=>3,'txnid'=>$txnid,'ref_id'=>$REFNO);
             	}
             	catch(\Exception $e)
            	{
              		return array('status'=>3,'txnid'=>'','ref_id'=>'');
            	}
        	}
        	else
        	{
           	 return array('status'=>3,'txnid'=>'','ref_id'=>'');
        	}
    }

	
    function api_route($number, $provider, $amount, $user_id) {
        $provider_detail = Provider::find($provider);
        return $provider_detail;
    }
	public function txnCallbackStatus(Request $request)
	{
		Apiresponse::create(['message' => $request->all(), 'api_type' =>"RECHARGE_CALLBACK",'report_id'=>1002,'request_message'=>'']);
	}
	

	public function rechargeTxnHistory(Request $request)
	{
		//print_r($request->all());die;
		$serviceId = $request->service_id;
		$providersId = Provider::where('service_id',$serviceId)->pluck('id','id')->toArray();
		$reportDetailsQuery = Report::where('user_id', Auth::id());
		$reportDetailsQuery->whereIn('provider_id',$providersId);
		if($request->export == "DATE_SEARCH")
		{
			 $start_date = ($request->fromdate) ? $request->fromdate ." 00:00:00" : date("Y-m-d") . " 00:00:00";
             $end_date = ($request->todate) ? $request->todate ." 23:59:59" : date("Y-m-d H:i:s");
			 $reportDetailsQuery->whereBetween('created_at', [$start_date,$end_date]);
		}
		 
		$reportDetailsQuery->orderBy('id', 'DESC');
		$reportDetailsQuery->whereIn('status_id',[1,2,3,21,24,34]);
		$reportDetails = $reportDetailsQuery->paginate(15);
		return view('reports.recharge-txn-history',compact('reportDetails','serviceId'));
	}
	
}
 