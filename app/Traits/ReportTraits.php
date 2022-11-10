<?php
namespace App\Traits;
use App\Report;
use App\Balance;
use Auth;
use Exception;
use DB;
trait ReportTraits
{
	public function getName()
	{ 
		return "rajat";
	}
	public function getRecordByMobileNumber($mobileNumber)
	{
		$reportDetails = Report::selectRaw('id,number,amount,beneficiary_id,status_id,txnid,api_id')->where(['customer_number'=>$mobileNumber])->whereIn('status_id',[1,2,3,18,21])->orderBy('id','desc')->whereIn('api_id',[4,5,25])->where('user_id',Auth::id())->take(10)->get();
		if(count($reportDetails))
		{
			
			$record =  $reportDetails->map(function($data){
				
				return
				[
					'id'=>$data->id,
					'accountNumber'=>$data->number,
					'bankName'=>@$data->beneficiary->bank_name,
					'ifscCode'=>@$data->beneficiary->ifsc,
					'txnId'=>$data->txnid,
					'amount'=>$data->amount,
					'description'=>@$data->api->api_name,
					'beneName'=>@$data->beneficiary->name,
					'status'=>@$data->status->status,
				];
			});
			return response()->json(['status'=>1,'message'=>$record]);
		}
		else
		{
			return response()->json(['status'=>0,'message'=>'No Transacton found']);
		}
	}
	public function isExistClienId($userId,$clientId)
	{
		try
		{
			Report::where(['user_id'=>$userId,'client_ackno'=>$clientId])->firstOrFail();
			return ['status'=>73,"message"=>"Duplicate Client Id  found"];
		}
		catch(Exception $e)
		{
			return ['status'=>1,"message"=>"Duplicate Client Id not found"];
		}
	}
	private function checkDuplicateTransaction($accountNo,$amount,$userId,$apiId)
	{
		$formatted_date = date("Y-m-d H:i:s");
		$start_time = date('Y-m-d H:i:s',strtotime('-300 seconds',strtotime($formatted_date)));
        $result = DB::table('reports')->select('number', 'amount', 'user_id', 'created_at')->where(['number'=>$accountNo,'bulk_amount'=> $amount,'user_id'=>$userId,'api_id'=>$apiId])->whereIn('status_id',[1,3,18])->where('created_at', '>=', $start_time)->orderBy('created_at', 'desc')->first();
        if ($result) {
            return array('status' => 31, 'message' => 'Same Amount, same account and same mobile transaction is found, Try again after 5 Minutes');
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
	public function getTDS($amount)
	{
		return ($amount * 5)/100;
	}
	public function getGST($commissionAmount)
	{
		return ($commissionAmount - ($commissionAmount/1.18));
	}
	private function createVerificationEntry($user_id,$mobile_number,$bene_id,$channel,$insert_id,$userData,$bank_account)
	{
		if($user_id !='' && $userData['credit_charge'] >0)
		{
			$amount=$userData['credit_charge'];
			if($amount>0)
			{
				if($user_id ==1)
				{
					$tds =  0;
					$netCommission = $amount;
					Balance::where('user_id', $user_id)->increment('admin_com_bal', $netCommission);
					$openingBal = Balance::where('user_id', $user_id)->first();
					$userData['opening_balance']=$openingBal->user_balance;
					$userData['total_balance']=$openingBal->user_balance;
					$userData['admin_com_bal']=$openingBal->admin_com_bal-$netCommission;
				}
				else
				{
					$tds =  (($amount)* 5)/100;
					$netCommission = $amount - ($tds);
					Balance::where('user_id', $user_id)->increment('user_balance', $netCommission);
					$cloasinBal =Balance::select('user_balance')->where('user_id', $user_id)->first()->user_balance;
					$userData['opening_balance']=$cloasinBal - $amount;
					$userData['total_balance']=$cloasinBal;
					
				}
				//$userData['gst'] = $gst;
				$userData['tds'] = $tds;
				
				$userData['number']=$bank_account;
				$userData['amount']=0;
				$userData['created_by']=Auth::id();
				$userData['provider_id']=41;
				$userData['profit']=0;
				$userData['api_id']=2;
				//$userData['credit_charge']=$amount;
				$userData['status_id']=22;
				$userData['debit_charge']=0;
				$userData['type'] = 'CR';
				$userData['recharge_type'] = 0;
				$userData['description']="VERI_COMMISSION";
				$userData['txn_type']="COMMISSION";
				$userData['pay_id']=time();
				$userData['user_id']=$user_id;
				$userData['txnid']=$insert_id;
				$userData['customer_number']=$mobile_number;
				$userData['channel']=$channel;
				Report::create($userData);
			}
		}
		
	}
	private function createVEntry($user_id,$mobile_number,$bene_id,$channel,$insert_id,$userData,$bank_account,$apiId)
	{
		if($user_id !='' && $userData['credit_charge'] >0)
		{
			$amount=$userData['credit_charge'];
			if($amount>0)
			{
				if($user_id ==1)
				{
					$tds =  0;
					$netCommission = $amount;
					Balance::where('user_id', $user_id)->increment('admin_com_bal', $netCommission);
					$openingBal = Balance::where('user_id', $user_id)->first();
					$userData['opening_balance']=$openingBal->user_balance;
					$userData['total_balance']=$openingBal->user_balance;
					$userData['admin_com_bal']=$openingBal->admin_com_bal-$netCommission;
				}
				else
				{
					$tds =  (($amount)* 5)/100;
					$netCommission = $amount - ($tds);
					Balance::where('user_id', $user_id)->increment('user_balance', $netCommission);
					$cloasinBal =Balance::select('user_balance')->where('user_id', $user_id)->first()->user_balance;
					$userData['opening_balance']=$cloasinBal - $amount;
					$userData['total_balance']=$cloasinBal;
					
				}
				//$userData['gst'] = $gst;
				$userData['tds'] = $tds;
				
				$userData['number']=$bank_account;
				$userData['amount']=0; 
				$userData['created_by']=Auth::id();
				$userData['provider_id']=41;
				$userData['profit']=0;
				$userData['api_id']=2;
				//$userData['credit_charge']=$amount;
				$userData['status_id']=$apiId;
				$userData['debit_charge']=0;
				$userData['type'] = 'CR';
				$userData['recharge_type'] = 0;
				$userData['description']="VERIFICATION_COMMISSION";
				$userData['txn_type']="VERIFICATION_COMMISSION";
				$userData['pay_id']=time();
				$userData['user_id']=$user_id;
				$userData['txnid']=$insert_id;
				$userData['customer_number']=$mobile_number;
				$userData['channel']=$channel;
				Report::create($userData);
			}
		}
		
	}

}