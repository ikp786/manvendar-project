<?php
namespace App\Traits;

trait AepsTraits 
{
 
	public function agentAepsCharge($amount,$charge,$chargeType)
	{
		if($chargeType == 0)
			return ($amount * $charge)/100;
		else
			return $charge;
		
	}
	public function getAepsCommission($amount,$commission,$comm_type)
	{
		if($comm_type==0)
			return ($amount * $commission )/100;
		else
			return $commission;
	}
		
 
}