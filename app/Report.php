<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Report extends Model
{

    protected $fillable = ['number', 'amount','ip_address','description','customer_number','channel','user_id', 'provider_id', 'api_id', 'total_balance', 'profit', 'status_id', 'detail' ,'recharge_type','total_balance2','d_user_balance','m_user_balance','a_user_balance','tds','offline_services_id','txnid','paytm_txn_id','qty','category_id','brand_id','credit_charge','debit_charge','tds','gst','beneficiary_id','bank_ref','pay_id','biller_name','type','txn_type','dist_commission','md_commission','admin_commission','opening_balance','admin_com_bal','is_offline','bill_due_date','mode','client_ackno','client_id','bulk_amount','aeps_sattelment_id','txn_initiated_date','txn_status_type']; 

    public function provider(){
        return $this->belongsTo('App\Provider');
    }
    public function rechargeprovider(){
        return $this->belongsTo('App\Rechargeprovider');
    }
    public function user(){
        return $this->belongsTo('App\User');
    }
    public function status(){
        return $this->belongsTo('App\Status');
    }
    public function beneficiary(){
        return $this->belongsTo('App\Beneficiary');
    }
    public function api(){
        return $this->belongsTo('App\Api');
    }
    public function creditBy(){
        return $this->belongsTo('App\User','credit_by');
    }
    public function payment(){
        return $this->belongsTo('App\Loadcash');
    }
	public function yesBankResponses()
    {
        return $this->hasMany('App\YesBankResponse');
    }
	public function offlineservices(){
        return $this->belongsTo('App\OfflineService','offline_services_id');
    }
	public function voucherbrand(){
        return $this->belongsTo('App\VoucherBrand','brand_id');
    }
	public function vouchercategory(){
        return $this->belongsTo('App\VoucherCategory','category_id');
    }
	public function aepssettlements(){
        return $this->belongsTo('App\AepsSettlement','aeps_sattelment_id');
    }
	/* Below function and descrpiton had been added by rajat(For Account details) at 2-DEC_2017 */
	
	/* Description of getTotalrequestedAmountToUser method
	 it will return sum of amount which is requested to user(Logined User) as approved OR Transfer amount as DT from its child of Betweent two dates.
	*@ param $id integer
	*@ param start_date dateTime (Y-m-d H:i:s)
	*@ param end_date dateTime (Y-m-d H:i:s)
	*@ return object
	
	*/
	public function getTotalrequestedAmountToUser($id,$start_date,$end_date)
	{
			return $this->selectRaw('sum(amount) as total_amount, sum(profit) as total_profit, sum(bank_charge) as total_bank_charge')->where(['user_id'=>$id,'status_id'=>7,'api_id'=>0,'provider_id'=>0])->whereBetween('created_at',[$start_date,$end_date])->get();
	}
	
	
	/*	Description of getTotalapprovedAmountFromUser method
		it will return sum of amount which is approved(requested amount) to Its child OR Transfer amount as DT to its child of Betweent two dates.
	*	@ param child_id array
	*	@ param start_date dateTime (Y-m-d H:i:s)
	*	@ param end_date dateTime (Y-m-d H:i:s)
	*	@ return object */
	public function getTotalapprovedAmountFromUser($child_id,$start_date,$end_date)
	{
			return $this->selectRaw('sum(amount) as total_amount, sum(profit) as total_profit, sum(bank_charge) as total_bank_charge')->whereIn('user_id',$child_id)->where(['status_id'=>6,'api_id'=>0,'provider_id'=>0])->whereBetween('created_at',[$start_date,$end_date])->get();
	}
	public function getTotalapprovedAmountAndDTFromUser($id,$start_date,$end_date)
	{
			return $this->selectRaw('sum(amount) as total_amount, sum(profit) as total_profit, sum(bank_charge) as total_bank_charge')->where('user_id',$id)->where(['status_id'=>7,'api_id'=>0,'provider_id'=>0])->whereBetween('created_at',[$start_date,$end_date])->get();
	}
	
	
	/* Description of getTotalrequestedAmountToParent method
		 it will return sum of amount which is requested to its parent by user(Logined User) or Transfer amount as DT from its parent of Betweent two dates.
		*@ param $id integer
		*@ param start_date dateTime (Y-m-d H:i:s)
		*@ param end_date dateTime (Y-m-d H:i:s)
		*@ return object
		
		*/
	public function getTotalRequestedAmountToParentWithDT($id,$start_date,$end_date)
	{
		
			return $this->selectRaw('sum(amount) as total_amount, sum(profit) as total_profit, sum(bank_charge) as total_bank_charge')->where(['user_id'=>$id,'status_id'=>6,'api_id'=>0,'provider_id'=>0])->whereBetween('created_at',[$start_date,$end_date])->get();
	}
	/* Description of getTotalrequestedAmountToParent method
		 it will return sum of amount which is requested to its parent by user(Logined User) or Transfer amount as DT from its parent of Betweent two dates.
		*@ param $id integer
		*@ param start_date dateTime (Y-m-d H:i:s)
		*@ param end_date dateTime (Y-m-d H:i:s)
		*@ return object
		
		*/
	public function getTotalrequestedAmountToParent($id,$start_date,$end_date)
	{
		
			return $this->selectRaw('sum(amount) as total_amount, sum(profit) as total_profit, sum(bank_charge) as total_bank_charge')->where(['user_id'=>$id,'status_id'=>6,'api_id'=>0])->whereBetween('created_at',[$start_date,$end_date])->get();
	}
	/*
	|
	| this function return all approved amount and total charges to a perticular user that is requested to it's parent and | | AD from his parents.
	|
	|*/
	public static function getUpfrontDtAmountByUserId($id,$start_date,$end_date)
	{
		return Report::selectRaw('sum(amount) as total_amount, sum(profit) as total_profit, sum(bank_charge) as total_bank_charge')->where('user_id',$id)->where(['status_id'=>6,'api_id'=>0,'provider_id'=>0])->whereBetween('created_at',[$start_date,$end_date])->first();
	}

}
