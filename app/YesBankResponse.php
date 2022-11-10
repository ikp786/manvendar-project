<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class YesBankResponse extends Model
{
    //
	/*-----------------------------------------------
	|	@ param status integer size 1
	|	@ param resp_code varchar
	*/
    protected $fillable= ['report_id','amount','txn_code','bulk_amount','status','resp_code','refund','transaction_date','user_id','profit','mobile','account','bank_ref'];
	public function getSuccessResponseAmount($table_id)
	{
		return $this->selectRaw('sum(amount) as total_amount,count(id) as total_non_kyc_txn')->where(['report_id' =>$table_id,'status'=>1])->get();
	}
	public function getKycChargeAmount($amount) 
	{
		return 3;
	}
}
