<?php

namespace App\library {
use App\Report;
use App\Balance;
use DB;
use Exception;
use App\Traits\CustomTraits;
use Auth;
    class Aeps
    {
		use CustomTraits;
		
		public function insertAepsRecard($aadhaarNumber,$customerNumber,$amount,$bankIINNumber,$userId,$agentCharge,$agentCommission,$txnType,$clientId=null)
		{
			DB::beginTransaction();
			try
			{
				/* $txnCreditAmount=$amount+$agentCommission-$agentCharge;
				Balance::where('user_id', $userId)->increment('user_balance', $txnCreditAmount); */
				$tds =  (($agentCommission)* 5)/100;
				$cr_amt = $agentCommission - ($tds);
				$balance = Balance::where('user_id', $userId)->first();
				$userBalance = $balance->user_balance;
				$reportDetails = Report::create([
						'number' => $aadhaarNumber,
						'provider_id' => 41,
						'amount' => $amount,
						'bulk_amount' => $amount,
						'api_id' => 10,
						'profit' => 0,
						'type' => 'CR',
						'txn_type' => $txnType,
						'description' => $txnType,
						'status_id' => 3,
						'pay_id' => time(),
						'user_id' => $userId,
						'created_by' => $userId,
						'ip_address' => \Request::ip(),
						'customer_number' => $customerNumber,
						'opening_balance' => $userBalance,
						'total_balance' => $userBalance,
						'biller_name'=>'',
						'gst' => 0,
						'tds' => $tds,
						'recharge_type' => 0,
						'credit_charge' => $agentCommission,
						'debit_charge' => $agentCharge,
						'channel' => 2,
						'client_ackno'=>$clientId,
				]);
				
				DB::commit();
				return ['status'=>1,'details'=>$reportDetails];
			}
			catch(Exception $e)
			{
				DB::rollback();
				return ['status'=>0,'details'=>"ERROR_".$e->getMessage()];
			}
		}
		public function calculateAepsCommission($user_id,$userDetails,$walletScheme,$insert_id,$amount,$reportDetails,$aadhaarNumber,$customerNumber,$bankName)
		{
			$agent_parent_id = $userDetails->parent_id;
			$dist_charge_data = $admin_charge_data = $md_charge_data=array();
			$agent_parent_role = $userDetails->parent->role_id;
			if($userDetails->parent_id == 1)
			{
				$d_id = $m_id ='';
				$a_id = 1;
				$admin_charge_data['credit_by'] = $user_id;
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
						$dist_charge_data['credit_by'] = $user_id;
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
						
						$dist_charge_data['credit_by'] = $user_id;
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
					
					$md_charge_data['credit_by'] = $user_id;
					$md_charge_data['credit_charge'] = $walletScheme->md_comm;
					$md_charge_data['debit_charge'] = 0;
					
					$admin_charge_data['credit_by'] = $m_id;
					$admin_charge_data['credit_charge'] = $walletScheme->admin_comm;
					$admin_charge_data['debit_charge'] = 0;
				}
			} 
			$reportDetails->dist_commission = isset($dist_charge_data['credit_charge']) ? $dist_charge_data['credit_charge'] : 0;
			$reportDetails->md_commission= isset($md_charge_data['credit_charge']) ? $md_charge_data['credit_charge'] : 0;
			$reportDetails->admin_commission = isset($admin_charge_data['credit_charge']) ? $admin_charge_data['credit_charge'] : 0;
			$reportDetails->save();
			$beneficiarydetail='';
			$this->creditCommission($d_id,$m_id,$a_id,$user_id,$customerNumber,$beneficiarydetail,2,$insert_id,$amount,$dist_charge_data,$md_charge_data,$admin_charge_data,$aadhaarNumber,$reportDetails,10,$bankName);
		}
	}
}