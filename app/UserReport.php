<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Report;
use Illuminate\Support\Facades\Auth;
class UserReport extends Model
{
    //
	public function exportUserReport($request=array())
	{
		
		
		$getdateFromat=$this->getDateFromat($request);
		$start_date= $getdateFromat['start_date'];
		$end_date = $getdateFromat['end_date'];
		$query = Report::select('id','user_id','profit','bank_charge','number','status_id','amount','total_balance','api_id','pay_id','created_at','txnid','payment_id','credit_by');
		$query->whereBetween('created_at', [$start_date,$end_date]);
		
		/* Ptx and varification Summary Query*/
		$summary_query = Report::selectRaw('user_id,api_id, sum(amount) as total_amount, sum(profit) as total_profit, count(id) as total_txn, profit,credit_by');
		$summary_query->whereBetween('created_at', [$start_date,$end_date]);
		/* End */
		/* Up Front Summary Query*/
		$up_front_summary_query = Report::selectRaw('user_id, sum(amount) as total_amount, sum(profit) as total_profit, sum(bank_charge) as total_bank_charge,count(id) as total_txn, profit,count(id) as total_txn,credit_by');
		$up_front_summary_query->whereBetween('created_at', [$start_date,$end_date]);
		/**/
		if($request->export =='upfront')//Done & output correct wihout except Tekedar, Shayam Sundar, Levein Demo
		{
				$query->where('user_id', Auth::id());
				$query->where('provider_id',0);
				$query->where('api_id',0);
				$query->where('status_id',7);
				
				
		}
		else if($request->export =='pretxn-a')// Done and testing is left
		{
				//$query->where('provider_id',41);
				$query->whereIn('api_id', [3,4,5,12,14]);
				$query->where('status_id',1);
		}
		else if($request->export =='pretxn-m')// Done & testing is left
		{
				//$query->where('provider_id',41);
				$query->whereIn('api_id', [3,4,5,12,14]);
				$query->where('status_id',7);
		}
		else if($request->export =='varification')// done & output correct
		{
				//$query->where('user_id', Auth::id());
				$query->where('api_id',2);
				$query->where('status_id',1);
		}
		else if($request->export =='excel')
		{
			
				$query->whereIn('api_id', [0,2,3,4,5,12,14]);
				$query->orderBy('created_at','Desc');
		}
		else if($request->export =='upfront_summary')
		{
			
				$up_front_summary_query->where('user_id', Auth::id());
				$up_front_summary_query->where('provider_id',0);
				$up_front_summary_query->where('api_id',0);
				$up_front_summary_query->where('status_id',7);
				$up_front_summary_query->groupBy('credit_by');
				
				$reports = $up_front_summary_query->get();
				return $reports;
		}
		else if($request->export =='pertxn_a_summary')
		{
			
				$summary_query->where('provider_id',41);
				$summary_query->whereIn('api_id', [3,4,5,12,14]);
				$summary_query->where('status_id',1);
				$summary_query->groupBy('user_id','api_id','profit');
				$reports = $summary_query->get();
				return $reports;
		}
		else if($request->export =='new_pertxn_a_summary')
		{
			$results=array();
				$summary_query = Report::selectRaw('user_id,api_id,status_id, sum(amount) as total_amount, sum(profit) as total_profit, count(id) as total_txn, profit,credit_by');
				$summary_query->whereBetween('created_at', [$start_date,$end_date]);
				$summary_query->where('provider_id',41);
				$summary_query->whereIn('api_id', [3,4,5,12,14]);
				$summary_query->whereIn('status_id',[1]);
				$summary_query->groupBy('user_id','profit','api_id');
				$reports = $summary_query->get();
				$results['S']=$reports;
				
				
				$summary_query_yes = Report::selectRaw('user_id,api_id,id,status_id, amount as total_amount, profit as total_profit, profit,credit_by');
				$summary_query_yes->whereBetween('created_at', [$start_date,$end_date]);
				$summary_query_yes->where('provider_id',41);
				$summary_query_yes->whereIn('api_id', [14]);
				$summary_query_yes->where('status_id',15);
				$reportsY = $summary_query_yes->get();
				$results['Y']=$reportsY;
				return $results;
		}
		else if($request->export =='pertxn_m_summary')/* Need to work on it  total amount */
		{
			
				$summary_query->where('provider_id',41);
				$summary_query->whereIn('api_id', [3,4,5,12,14]);
				$summary_query->where('status_id',7);
				//$summary_query->where('status_id',1);
				$summary_query->groupBy('user_id','api_id','profit');
				$reports = $summary_query->get();
				return $reports;
		}
		else if($request->export =='varification_summary')
		{
			
				$summary_query->where('provider_id',41);
				$summary_query->where('api_id',2);
				$summary_query->where('status_id',1);
				$summary_query->groupBy('user_id','api_id','profit');
				$reports = $summary_query->get();
				return $reports;
		}
		$query->orderBy('created_at','Desc');
		/* $query->take(100); */
		$reports = $query->get();
		return $reports;
	}
	private function getStartDateFormat($start_date=null)
	{
		if(!empty($start_date))
			return $start_date.' 00:00:00';
		return date('Y-m-d').' 00:00:00';
	}
	private function getEndDateFromat($start_date=null,$end_date=null)
	{
		
			if(!empty($end_date))
				return $end_date.' 23:59:59';
			else
				return date('Y-m-d').' 23:59:59';
	}
	private function getDateFromat($request=null)
	{
		$date_format=array();
		if($request->all())
			{
				$date_format['start_date'] = $this->getStartDateFormat($request->from_date);
				$date_format['end_date'] = $this->getEndDateFromat($request->from_date,$request->to_date);
				
				
			}
		else{
			$date_format['start_date'] = $this->getStartDateFormat();
			$date_format['end_date'] = $end_date = $this->getEndDateFromat();
		}
			return $date_format;
	
	}
	public static function getAllAgentOfMd($company_id)
	{
		return User::where(['company_id'=>$company_id,'role_id'=>5])->pluck('id','id')->toArray();
	}
   /*  public static function getMdInvoice($company_id,$start_date,$end_date)
    {
        $agent_list = UserReport::getAllAgentOfMd($company_id);
        $results = UserReport::generateInvoiceCompanyWise($agent_list,$start_date,$end_date);
        return $results;
    } */
	 public static function getMdInvoice($company_id,$start_date,$end_date,$invoice_lm_id,$isMdChargeSet)
    {
        $agent_list = UserReport::getAllAgentOfMd($company_id);
        $results = UserReport::generateInvoiceCompanyWise($agent_list,$start_date,$end_date,$invoice_lm_id,$isMdChargeSet);
        return $results;
    }
	/*  public static function generateInvoiceCompanyWise($agent_list,$start_date,$end_date)
    {
        $summary_query = Report::selectRaw('api_id, sum(amount) as total_amount, count(id) as total_txn,profit as profit_charge, sum(profit) as total_profit');
                $summary_query->whereBetween('created_at', [$start_date,$end_date]);
                $summary_query->where('provider_id',41);
                $summary_query->whereIn('api_id', [2,3,4,5,12,14]);
                $summary_query->whereIn('status_id',[1,15]);
                $summary_query->WhereIn('user_id',$agent_list);
                $summary_query->groupBy('profit','api_id');
                $summary_query->orderBy('api_id','ASC');
                $reports = $summary_query->get();
                return $reports;
                
    } */
	public static function generateInvoiceCompanyWise($agent_list,$start_date,$end_date,$invoice_lm_id,$isMdChargeSet)
    {
        $reports=array();
        if($isMdChargeSet)
        {
            $summary_query = Report::selectRaw('api_id, sum(amount) as total_amount, count(id) as total_txn,profit as profit_charge, sum(profit) as total_profit');
                $summary_query->whereBetween('created_at', [$start_date,$end_date]);
                $summary_query->where('provider_id',41);
                $summary_query->whereIn('api_id', [3,4,5,12,14]);
                $summary_query->where('status_id',7);
                $summary_query->Where('user_id',$invoice_lm_id);
                $summary_query->groupBy('profit','api_id');
                $summary_query->orderBy('api_id','ASC');
                $reports[0] = $summary_query->get();
              
                 $varification_query = Report::selectRaw('api_id, sum(amount) as total_amount, count(id) as total_txn,profit as profit_charge, sum(profit) as total_profit');
                $varification_query->whereBetween('created_at', [$start_date,$end_date]);
                $varification_query->where('provider_id',41);
                $varification_query->where('api_id', 2);
                $varification_query->where('status_id',1);
                $varification_query->WhereIn('user_id',$agent_list);
                $varification_query->groupBy('api_id');
                $reports[1] = $varification_query->get();
               
        }else{
        $summary_query = Report::selectRaw('api_id, sum(amount) as total_amount, count(id) as total_txn,profit as profit_charge, sum(profit) as total_profit');
                $summary_query->whereBetween('created_at', [$start_date,$end_date]);
                $summary_query->where('provider_id',41);
                $summary_query->whereIn('api_id', [3,4,5,12,14,2]);
                $summary_query->whereIn('status_id',[1,15]);
                $summary_query->WhereIn('user_id',$agent_list);
                $summary_query->groupBy('profit','api_id');
                $summary_query->orderBy('api_id','ASC');
                $reports[0] = $summary_query->get();
            }
                return $reports;
                
    }
}
