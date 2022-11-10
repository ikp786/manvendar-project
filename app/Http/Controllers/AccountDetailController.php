<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\User;
use App\Balance;
use App\Report;
use App\Beneficiary;
use App\Loadcash;
use DB;
use Excel;
use App\UserReport;
use Illuminate\Support\Facades\Auth;
class AccountDetailController extends Controller
{
    //
	/* For Md 
	*
	* To dispay requested amount to admin and requested amount from its Child,
	* To display received amount from admin and approved amount of its child
	*
	*@
	*
	*/
	public function accountDetails()
	{
		/* Requested Amount to Admin*/
		
		
		
		$logined_user_id=Auth::id();
		$role_id = Auth::user()->role_id;
		//$logined_user_id = 121;
		//$role_id = User::find($logined_user_id)->role_id;
		if(in_array($role_id,array(1,3,4)))
		{
			$user = new User();
			$report = new Report();
			$child_id = $user->getChildIdOfLoginedUser($logined_user_id,$role_id);
			if($role_id == 3)
				$child_id = $user->getChildOfChild($child_id);
			$loadcash = new Loadcash();
			$start_date = date('Y-m-d').' 00:00:00';
			$end_date = date('Y-m-d').' 23:59:59';
			
			/* $start_date = '2017-11-30 00:00:00';
			$end_date = '2017-11-30 23:59:59'; */
			
			
			
			 $accounts=array();
			 $accounts['total_req_amt_to_parent'] = 0;
			 $accounts['total_app_amt_fr_parent'] = 0;
			 $accounts['charge'] = 0;
			 $accounts['tatal_req_amt_to_me'] = 0;
			 $accounts['tatal_app_amt_fr_me'] = 0;
			 $accounts['profit'] = 0;
			 $accounts['total_bank_charge'] =0;
			 $accounts['actual_profit'] =0;
			 $accounts['parent_deduction']=0;
			 $accounts['total_bank_charge_parent']=0;
			 
			if( $role_id != 1)
			{
				//$parents=User::find($logined_user_id)->parent->id;
			
				$req_amount_to_admin= $report->getTotalRequestedAmountToParentWithDT($logined_user_id,$start_date,$end_date);
				
				$tatal_req_amout_to_parent = round((($req_amount_to_admin[0]->total_amount) + ($req_amount_to_admin[0]->total_profit)+($req_amount_to_admin[0]->total_bank_charge)),2);
				$tatal_approved_amout_from_parent =  round(($req_amount_to_admin[0]->total_amount),3);
				$total_profit_from_parent =  round(($req_amount_to_admin[0]->total_profit),3);
				$total_bank_charge_parent =  round(($req_amount_to_admin[0]->total_bank_charge),3);
				/* echo "<br>tatal_req_amout_to_admin :".$tatal_req_amout_to_parent;
				echo "<br>tatal_approved_amout_from_admin :".$tatal_approved_amout_from_parent;
				echo "<br>Charges :".round(($tatal_req_amout_to_parent - $tatal_approved_amout_from_parent),2); */
				$accounts['total_req_amt_to_parent']=$tatal_req_amout_to_parent;
				$accounts['total_app_amt_fr_parent']=$tatal_approved_amout_from_parent;
				$accounts['charge']=round(($tatal_req_amout_to_parent - $tatal_approved_amout_from_parent),2);
				$accounts['parent_deduction']=round(($total_profit_from_parent + $total_bank_charge_parent),2);
				$accounts['total_bank_charge_parent']=$total_bank_charge_parent;
			 }
			if( $role_id != 5)
			{
				$req_amount_to_me= $report->getTotalrequestedAmountToUser($logined_user_id,$start_date,$end_date);
				
				$tatal_req_amout_to_me = round((($req_amount_to_me[0]->total_amount) + ($req_amount_to_me[0]->total_profit)+($req_amount_to_me[0]->total_bank_charge)),2);
				
				//$approved_amt_fr_me = $report->getTotalapprovedAmountFromUser($child_id,$start_date,$end_date);
				$approved_amt_fr_me = $report->getTotalapprovedAmountAndDTFromUser($logined_user_id,$start_date,$end_date);

				$tatal_approved_amout_fr_me = round(($approved_amt_fr_me[0]->total_amount),2);
				$tatal_atotal_profit_fr_me = round(($approved_amt_fr_me[0]->total_profit),2);
				$total_bank_charge_fr_me = round(($approved_amt_fr_me[0]->total_bank_charge),2);
				/* echo "<br>tatal_req_amout_to_me :".$tatal_req_amout_to_me;
				echo "<br>tatal_approved_amout_fr_me :".$tatal_approved_amout_fr_me;
				echo "<br>Profit :".round(($tatal_req_amout_to_me - $tatal_approved_amout_fr_me),2); */
				$accounts['tatal_req_amt_to_me']=$tatal_req_amout_to_me;
				$accounts['tatal_app_amt_fr_me']=$tatal_approved_amout_fr_me;
				
				/* $accounts['profit']=round(($tatal_req_amout_to_me - $tatal_approved_amout_fr_me),2); */
				$accounts['deduction']=round(($tatal_atotal_profit_fr_me + $total_bank_charge_fr_me),2);
				$accounts['total_bank_charge']=$total_bank_charge_fr_me;
				/* $accounts['actual_profit'] = round(($accounts['profit'] - $accounts['total_bank_charge']),2); */
				$accounts['actual_profit'] = round($tatal_atotal_profit_fr_me,2);
			}
			$accounts['role_id']=$role_id;
			return view('layouts.account',compact('accounts'));
		}
		else
			echo "No Permission";
		 
	}
	
}
