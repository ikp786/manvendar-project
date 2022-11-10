<?php
namespace App\Traits;
use App\User;
use App\Report;
use App\Balance;
trait UserTraits
{
 
	public function debitUserRegistrationFee($registerUserId,$amount,$loginedUserId)
	{
		$user = User::find($registerUserId);
		$loggedUser = User::find($loginedUserId);
		$adminUserDetails = 
		$data=[
			'number'=>$user->mobile,
			'provider_id' => 41,
			'ip_address'=>\Request::ip(),
			'api_id'=>0,
			'status_id'=>6,
			'user_id'=>$user->id,
			'amount'=>0,
			'profit'=>0,
			'txnid'=>"REGISTRATION_FEE",
			'description'=>"REGISTRATION_FEE",
			'type' => 'DR',
			'created_by'=>$loginedUserId,
			'opening_balance' => $user->balance->user_balance,
			'total_balance' => -$amount,
			'debit_charge' => $amount,
			'credit_charge' => 0,
			'channel' => 2,
			'pay_id'=>time(),
		];
		$openingBal = Balance::where('user_id', 1)->first();
		Balance::where('user_id', 1)->increment('admin_com_bal', $amount);
		Balance::where('user_id', $registerUserId)->decrement('user_balance', $amount);
		$adminData = $data;
		$adminData['status_id']=7;
		$adminData['credit_charge']=$amount;
		$adminData['debit_charge']=0;
		$adminData['user_id']=1;
		$adminData['description']="Registration fee of ".$user->name .'(' . $user->role->role_title .') by ' . $loggedUser->name .'( '. $loggedUser->mobile .')';
		$adminData['total_balance']=$openingBal->user_balance;
		$adminData['admin_com_bal']=$openingBal->admin_com_bal+$amount;
		Report::create($data);
		Report::create($adminData);
		
	}
}