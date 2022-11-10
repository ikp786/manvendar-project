<?php

namespace App\Http\Controllers;
use App\Api;
use App\Company;
use Validator;
use App\Balance;
use App\Beneficiary;
use App\User;
use App\Provider;
use Illuminate\Http\Request;
use GuzzleHttp\Client;
use App\Netbank;
use Response;
use App\Http\Requests;
use Carbon\Carbon;
use DB;
use App\Apiresponse;
use App\Commission;
use App\Report;
use App\BillScheme;
use App\Masterbank;
use Auth;
use Exception;
use App\Http\Controllers\Controller;
use App\Traits\CustomTraits;
class ElectricityController extends Controller {

use CustomTraits;
   function check_bill (Request $request){
        $number = $request->number;
        $provider = $request->provider;
        $provider = Provider::where('id', $provider)->first();
        $cybercode = $provider->cyber;
        $cyber = new \App\Library\Bbps;
        return  $data = $cyber->check_bill($cybercode, $number);
        }


    function store(Request $request){
            
		$rules = array(
			'number' => 'required|regex:/^[A-Za-z0-9]+$/',
			'provider' => 'required|numeric|regex:/^[0-9]+$/',
			'amount' => 'required|numeric|min:0',
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
		if(Auth::id() !=4)
		{ 
			$isAcitveService = $this->isActiveService(15);
			if($isAcitveService)
				return response()->json(['success'=>"failure",'message'=>$isAcitveService->message]);
		}
		
        $provider = $request->provider;
        $amount = $request->amount;
        $billerName = $request->billerName;
        $consumerNumber = $request->consumerNumber;
		$bill_due_date = $request->bill_due_date;
		$bill_due_date = date("Y-m-d H:i:s", strtotime($bill_due_date));
		
        if ($number && $provider  && $amount) 
		{
			$userDetails = $this->getUserDetails();
			//$balance = Balance::where('user_id', $userDetails->id)->first();
			return $sendrec = $this->makeRecharge($number, $provider, $amount,$billerName,$userDetails,$consumerNumber,$bill_due_date);
			}
			else{
				return Response::json(array('success' => 'failure', 'message' => 'all field requierd'));
			}
        }

    
    
	function makeRecharge ($number, $provider, $amount,$billerName,$userDetails,$consumerNumber,$bill_due_date=null)
	{
	            
        $now = new \DateTime();
        $datetime = $now->getTimestamp();
        $ctime = $now->format('Y-m-d H:i:s');
		$user_id  =$userDetails->id;
		$user_balance =Balance::where('user_id', $user_id)->first();
		$user_balance = $user_balance->user_balance;
		$availableBalanceWithBlockedAmount = $user_balance - $amount;
		$isHaveBlockedAmount=$this->isHaveBlockedAmount(Auth::user()->member->blocked_amount,$availableBalanceWithBlockedAmount);
		
		// echo "<br/> inst id =".$insert_id ='0';
		// echo "<br/> k number =".$number;
		// echo "<br/> amount =".$amount;
		// echo "<br/> user id =".$user_id; 
		// $result = $this->sendRechargeByAmazon($insert_id, $number, $amount, $user_id); die;
		
		if($isHaveBlockedAmount['status']==91){
			return response()->json(['status'=>'failure','message'=>$isHaveBlockedAmount['message']]);
		}
		if($user_balance < $amount)
		{
			return Response::json(array('success' => 'failure', 'message' => "Low balance"));
		}
		
		$myd = $this->api_route($provider);
	 
		if ($myd) 
		{
			$currentFistDate = date('Y-m-01'). " 00:00:00";
			$isPaymetDone = Report::where(['number'=>$number,'amount'=>$amount,'api_id'=>$myd->api_id])->where('created_at','>=',$currentFistDate)->whereIn('status_id',[1,3,24])->first();
			if($isPaymetDone)
				return Response::json(array('success' => 'failure', 'message' => 'This Payment alreday done, Please Check your statement. Payment Date '.date_format($isPaymetDone->created_at,"d-m-Y H:i:s")));
			$statusId=3;
			$isOffline=0;
			if(in_array($myd->service_id,array(6)))
			{
				if(!$myd->is_service_active){
				    
					return Response::json(array('success' => 'failure', 'message' => $myd->provider_name ." is down"));
				}elseif(!$myd->is_service_online)
				{  // echo " elseif call ";
					$statusId=24;
					$isOffline=1;
				}
				elseif(($myd->hold_txn_couter <= $myd->max_hold_txn) && $myd->hold_txn_couter >=0 && ($myd->min_pass_amt_txn < $amount))
				{;
					$statusId=24;
					$isOffline=1;
					$myd->hold_txn_couter += 1;
					$myd->save();
				}
			}
			else{
				return Response::json(array('success' => 'failure', 'message' => "Route is not defined"));
			}
			
		 
			// echo "<br/>service_id =".$myd->service_id; 
		//	 echo "<br/>is_service_active =".$myd->is_service_active; 
			 
			$scheme_id = Auth::user()->scheme_id; 
			$mc = $this->get_commission($scheme_id, $provider);
			if($mc =='')
			{
				return Response::json(array('success' => 'failure', 'message' => 'Sar Charge is not configured'));
			}
			if($mc->type==0)
			{
				$commission = ($amount * $mc->r) / 100;
				$distt_comm = ($amount * $mc->d) / 100;
				$md_comm = ($amount * $mc->md) / 100;
				$admin_comm = ($amount * $mc->admin) / 100;
			}
			else
			{
				 $commission = $mc->r;
				 $distt_comm = $mc->d;
				 $md_comm = $mc->md;
				 $admin_comm = $mc->admin;
			}
			$max_commission = $mc->max_commission;
			$md_max_commission = $mc->md_max_commission;
			$dist_max_commission = $mc->dist_max_commission;
			if($commission > $max_commission)
				$commission = $max_commission;
			if($distt_comm > $dist_max_commission)
				$distt_comm = $dist_max_commission;
			if($md_comm > $md_max_commission)
				$md_comm = $md_max_commission;
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
			if($myd->api_id=='27'){
			    $statusId='3';
			}
			if($amount < $myd->min_pass_amt_txn)   
			{  
				$statusId=24;
				$isOffline=1;
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
                            'credit_charge'=>($commission>0)? $commission : 0,
							'debit_charge'=>($commission<0)? abs($commission) : 0,
							'created_by'=>Auth::id(),
							'txn_type'=>'BILL PAYMENT',
							'type' => 'DB',
							'tds' => $tds,
							'profit' => 0,
							'is_offline' => $isOffline,
							'biller_name' => $billerName,
							'bill_due_date' => $bill_due_date,
							'customer_number' => $consumerNumber,
                            'total_balance' => Balance::where('user_id',$user_id)->select('user_balance')->first()->user_balance,
                    ]);
    				$insert_id = $rechargeReport->id;
    				DB::commit();
    				
    				 //echo "<br/>report id = =".$insert_id;
				}
				catch(Exception $e)
				{
					DB::rollback();
					return Response::json(array('success' => 'failure', 'message' => 'Whoops! Something went wrong. Please try again after somethime'));
				}
				$provider_api = $myd->api_id; // 1
                $cybercode = $myd->cyber;
				$cyber = new \App\Library\Bbps;
                $account = '';
                $cycle = '';
                /*
                echo "<br/>report id = =".$insert_id;
                echo "<br/>statusId id = =".$statusId;
                echo "<br/>provider_api =".$provider_api;
                echo "<br/>isOffline =".$isOffline;
                die;*/
                
				if($statusId == 3 || ($provider_api=='27' && $isOffline=='0'))
				{
					if(true)
					{
						switch ($provider_api) 
						{
						    case 1:
								$result = $cyber->sendrecharge($cybercode, $insert_id, $number, $amount, $user_id, $account, $cycle);
								break; 
							case 8:
								$vendorCode = $myd->redpay;
								//$result = $this->redPayRecharge($user_id, $number, $vendorCode,$amount, $insert_id);
								break; 
						    case 27:    
								$result = $this->sendRechargeByAmazon($insert_id, $number, $amount, $user_id);
								$status = 1;
								break;	  
							default;
								$company = $provider;
								$provider_api = 1;
								$result = $cyber->sendrecharge($cybercode, $insert_id, $number, $amount, $user_id, $account, $cycle);
						}
						
						$status = @$result['status'];
						$txnid = @$result['txnid']; 
					}
					else{
						$status = 1;
						$txnid = 1; 
					}
					
					// echo "<br/> sttt =".$status; die;
                    if ($status == 1) 
					{
                        $status = 'ACCEPTED';
                        $message = "Transaction Successfully Done, Operator Transaction ID : $txnid, Thanks";
						$this->creditCommissionR($d_id,$m_id,$distt_comm,$md_comm,$insert_id,$number,$provider,$provider_api,$a_id,$admin_comm,$amount,$user_id,$commission);
						if($provider_api!='27'){
						    $rechargeReport->status_id = 1;  
						}
						$rechargeReport->txnid = $txnid;
						$rechargeReport->save();
						
						$message ="Dear $billerName ($number) Your Electricity bill successfull with amount $amount ";
            			$message = urlencode($message);
            			CustomTraits::sendByA2ZPlusSms($consumerNumber,$message);
        						
					 

                    } elseif ($status == 2)  
					{
                        $status = 'failure';
                        $message = "Transaction Failed, Please check Detail or Try After some time, Thanks";
                        Balance::where('user_id', $user_id)->increment('user_balance', $finalAmount);
                        $final_bal = Balance::where('user_id', $user_id)->select('user_balance')->first();
						
						if($provider_api!='27'){
						    $rechargeReport->status_id = 2;  
						}
						$rechargeReport->txnid = $txnid;
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
					//@$msg ="Dear $billerName($number) Your Electricity bill Successfull with amount $amount ";
        			//@$msg=urlencode(@$msg);
        			//$this->sendSMS($consumerNumber,$msg,1);
        			
        			$message ="Dear $billerName ($number) Your Electricity bill successfull with amount $amount ";
        			$message = urlencode($message);
        			CustomTraits::sendByA2ZPlusSms($consumerNumber,$message);
			 
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
	private function agentCharge($amount,$chargeType,$charge)
	{
		if($chargeType ==0)
			return ($amount * $charge)/100;
		else
			return $charge;
		
	}
	private function getCommission($amount,$commType,$comm)
	{
		
		if($commType ==0)
			return ($amount * $comm)/100;
		else
			return $comm;
	} 


}
