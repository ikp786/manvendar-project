<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\User;
use App\Company;
use App\Balance;
use App\Report;
use App\Beneficiary;
use App\Mdpertxn;
use App\Mdfirstpertxn;
use App\Mdsecondpertxn;
use DB;
use Excel;
use App\Http\Requests;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Encryption;
use App\UserReport;
use App\YesBankResponse;
use App\ApiCharge;
use App\State;
use App\Traits\CustomTraits;// New file name space used concept of Inheritence

use Illuminate\Support\Facades\Input;
use App\Traits\AmountInWords;
class UserReportController extends Controller
{
	//use CustomTraits;
	//use AmountInWords;
    public function userBalanceDetails()
	{
		if(Auth::user()->role->id ==1 || Auth::user()->role_id ==19)
		{
			$users = User::select('id','name','mobile','parent_id','balance_id','company_id')
					->take(40)
					->get();
			return view('admin.user-balance-detail',compact('users'));
		}
		else
			echo"No Permission";
		
	}
	public function transactionMissmathched(Request $request)
	{
		$getdateFromat=$this->getDateFromat($request);
		$start_date= $getdateFromat['start_date'];
		$end_date = $getdateFromat['end_date'];
		DB::enableQueryLog();
		$reports = Report::select('id','user_id','profit','bank_charge','number','status_id','amount','api_id','created_at','txnid','total_balance','total_balance2')
							->whereIn('status_id',[1,6])
							->whereIn('api_id',[0,1,2,3,4,5,8,12,13])
							->whereBetween('created_at', [$start_date,$end_date])
							->orderBy('user_id','desc')
							->orderBy('created_at','asc')
							->paginate(200);
							//->get();
		 //echo ($reports[$key] - ($value->total_balance + $value->profit + $value->bank_charge));
		//dd(DB::getQueryLog());
		//dd(DB::getQueryLog());
		return view('admin.transaction-missmatched',compact('reports','results'));
	}
	public function accountSummary(Request $request)
	{
		if(Auth::user()->role->id ==1 || Auth::user()->role_id ==19)
		{
			// echo"<pre>";
			// print_r($request->all());die;
			$account_report =array();
			set_time_limit(2000000);
			ini_set("memory_limit", "200056M");
			
			if($request->all())
			{
				$start_date= $this->getStartDateFormat($request->from_date);
				$end_date = $this->getEndDateFromat($request->from_date,$request->to_date);
			}
			else
			{
				$start_date= $this->getStartDateFormat();
				$end_date = $this->getEndDateFromat();
			}
			$from_date=explode(" ",$start_date);
			$to_date=explode(" ",$end_date);
			$results['start_date']=$from_date[0];		
			$results['end_date']=$to_date[0];
			if($request->export=='export'){
				$account_report=Report::selectRaw('user_id, number, count(amount) no_of_txn, sum(amount) as total_amount')
							->groupBy('number')
							->where('status_id',1)
							->whereIn('api_id',[3,4,5,12])
							->whereBetween('created_at', [$start_date,$end_date])
							->orderBy('no_of_txn','desc')
							->get();
				Excel::create('account_report', function ($excel) use ($account_report,$start_date,$end_date) {
				$excel->setTitle('Monthly Account Summary');
				$excel->setCreator('Levinm Fintech')->setCompany('Levinm Fintech Pvt. Ltd.');
				$excel->setDescription("Report from $start_date to $end_date");
				
				$excel->sheet('Sheet1', function ($sheet) use ($account_report,$start_date,$end_date) {
				$sheet->fromArray($account_report, null, 'A1', false, false)->prependRow(array(
							'User Id', 'Number', 'No Of Txn', 'Total Amount')
						);
				$sheet->prependRow( array('','To Date' ,"$end_date"));
				$sheet->prependRow( array('','From Date' ,"$start_date"));
				$sheet->prependRow( array('','','Account Summary'));
					});
				})->export('xls');
			}
			$start_date='2017-10-01 00:00:00';
			$end_date ='2017-10-02 00:00:00';
				$account_report=Report::selectRaw('sum(amount) as total_amount, count(amount) no_of_txn, number,user_id')
							->groupBy('number')
							->where('status_id',1)
							->whereIn('api_id',[3,4,5,12])
							->whereBetween('created_at', [$start_date,$end_date])
							->orderBy('no_of_txn','desc')
							->paginate(40);
							//->get();
			
			
			return view('admin.account-summary',compact('account_report','results'));
		}
		echo "No Premission";die;
	}
	
	public function accountMonthlySummary(Request $request)
	{
		if(Auth::user()->role->id ==1 || Auth::user()->role_id ==19)
		{
			set_time_limit(5000);
			ini_set("memory_limit", "200056M");
			if($request->all())
			{
				$start_date= $this->getStartDateFormat($request->from_date);
				$end_date = $this->getEndDateFromat($request->from_date,$request->to_date);
			}
			else
			{
				$start_date= $this->getStartDateFormat();
				$end_date = $this->getEndDateFromat();
			}
			$from_date=explode(" ",$start_date);
			$to_date=explode(" ",$end_date);
			$results['start_date']=$from_date[0];		
			$results['end_date']=$to_date[0];	
			if($request->export=='export'){
				$account_monthly_report = Report::selectRaw('user_id, number, count(DISTINCT DATE_FORMAT(created_at, "%Y-%m-%d")) as no_of_days, count(amount) as no_of_txn, sum(amount) as total_amount')
								->where('status_id',1)
								->whereIn('api_id',[3,4,5,12])
								->whereBetween('created_at', [$start_date,$end_date])
								->groupBy('number')
								->havingRaw('count(DISTINCT DATE_FORMAT(created_at, "%Y-%m-%d")) > 1')
								->orderBy('no_of_days','desc')
								->get();
				Excel::create('Account Monthly Report', function ($excel) use ($account_monthly_report,$start_date,$end_date) {
				$excel->setTitle('Monthly Account Summary');
				$excel->setCreator('Levinm Fintech')->setCompany('Levinm Fintech Pvt. Ltd.');
				$excel->setDescription("Report from $start_date to $end_date");
				
				$excel->sheet('Sheet1', function ($sheet) use ($account_monthly_report,$start_date,$end_date) {
								
				$sheet->fromArray($account_monthly_report, null, 'A1', false, false)->prependRow(array(
							'User Id', 'Account Number','No Of Days', 'No Of Txn', 'Total Amount')
						);
				$sheet->prependRow( array('','To Date' ,"$end_date"));
				$sheet->prependRow( array('','From Date' ,"$start_date"));
				$sheet->prependRow( array('','','Account Monthly Summary'));
					});
				})->export('xls');
			}
			else
			{
				//DB::enableQueryLog();
				$account_report=Report::selectRaw('count(DISTINCT DATE_FORMAT(created_at, "%Y-%m-%d"))as no_of_days, sum(amount) as total_amount, number,created_at,user_id,count(amount) as no_of_txn')
								->where('status_id',1)
								->whereIn('api_id',[3,4,5,12])
								->whereBetween('created_at', [$start_date,$end_date])
								->groupBy('number')
								->havingRaw('no_of_days > 1')
								->orderBy('no_of_days','desc')
								->get();
				$requested_date=$request->all();				
			//dd(DB::getQueryLog());die;
				// $account_report = DB::select('select count(DISTINCT DATE_FORMAT(created_at, "%Y-%m-%d")) as no_of_days, number, sum(amount) as total_amount, count(amount) as tnx, user_id, number FROM `reports` where `status_id` = 1 AND `api_id` IN (3,4,5,12) AND `created_at` BETWEEN "'.$start_date.'" AND "'.$end_date.'" GROUP BY number limit 40');
				
				return view('admin.account-monthly-summary',compact('account_report','results','requested_date'));
			}
		}
		else
			echo "No Permission";die;
	}
	public function getUserDetils(Request $request)
	{
		if(Auth::user()->role->id ==1 || Auth::user()->role_id ==19)
		{
			$user_relation =User::join('users as distributor', function($join)
								{
									$join->on('users.parent_id', '=', 'distributor.id');
								})
							->join('users as md', function($join)
								{
									$join->on('distributor.parent_id', '=', 'md.id');
										//->where('md.role_id','=', 3);
								})
							->join('users as guardian',function($join)
								{
									$join->on('md.parent_id', '=', 'guardian.id');
										//->where('guardian.role_id', '=',1);
								})
								->select('users.id as re_id','users.name as re_name','users.role_id as re_roleId',
										'distributor.id as d_id','distributor.name as d_name','distributor.role_id as d_roleId',
										'md.id as m_id','md.name as m_name','md.role_id as m_roleId',
										'guardian.id as guardian_id','guardian.name as guardian_name','guardian.role_id as g_roleId'
							)
							->where('users.id',$request->id)
							->first();
			$results[$user_relation->re_roleId]=$user_relation->re_name.' ('.$user_relation->re_id.')';
			$results[$user_relation->d_roleId]=$user_relation->d_name.' ('.$user_relation->d_id.')';
			$results[$user_relation->m_roleId]=$user_relation->m_name.' ('.$user_relation->m_id.')';
			$results[$user_relation->g_roleId]=$user_relation->guardian_name.' ('.$user_relation->guardian_id.')';
			$userType['api_user'] = $userType['retailer'] = $userType['distributor'] = $userType['md'] = $userType['guardian'] = '';
			foreach($results as $key =>$result)
			{
				if($key == 7)
				{
					$roleType='api_user';
				}
				elseif($key == 5)
				{
					$roleType='retailer';
				}
				elseif($key == 4)
				{
					$roleType='distributor';
				}
				elseif($key == 3)
				{
					$roleType='md';
				}
				elseif($key == 1)
				{
					$roleType='guardian';
				}
				$userType[$roleType] = $result;
			}
			if($request->ajax()){
				return response()->json(['status'=>'success','result' =>$userType]);
			}
		}
		else
			echo "No Permission";die;
			
	}
	public function showRevenueExpenses(Request $request) {
		if(Auth::user()->role->id ==1 || Auth::user()->role_id ==19)
		{
			set_time_limit(2000000);
			ini_set("memory_limit", "200056M");
				
			if($request->all())
			{
				//print_r(DB::select('id')->from('api_charges')->orderBy('id','DESC')->get()->toArray());die;
				/* $year = date('Y', strtotime($request->start_date));
				$month = (int)date('m', strtotime($request->start_date));
				DB::enableQueryLog();
				$api_charges = ApiCharge::where(['year'=>$year,'month'=>$month])
											->orWhereIn('id',function($query)
												{
													$query->select('id')
														  ->from('api_charges')
															->orderBy('id','DESC');
														 
												})->orderBy('id','DESC')->first();
				dd(DB::getQueryLog());
				print_r($api_charges);die;
				$api_charges = ApiCharge::where(['year'=>$year,'month'=>$month])->orderBy('id','DESC')->first();
				print_r($api_charges);die; */
				$start_date= $this->getStartDateFormat($request->start_date);
				$end_date = $this->getEndDateFromat($request->start_date,$request->end_date);
				
				
			}
			else{
				
				$start_date= $this->getStartDateFormat();
				$end_date = $this->getEndDateFromat();
			}
				$year = (int)date('Y', strtotime($start_date));
				$month = (int)date('m', strtotime($start_date));
				$api_charges = ApiCharge::where(['year'=>$year,'month'=>$month])->orderBy('id','DESC')->first();
				if(!$api_charges)
					$api_charges = ApiCharge::orderBy('id','DESC')->first();
				
			$results['end_date']=$end_date;
			$results['start_date']=$start_date;
			
			$reports = Report::select('id','profit','bank_charge','status_id','api_id','amount','user_id','remark')
					->whereIn('api_id', [0,2,3,4,5])
					->whereIn('status_id', [1])
					->whereBetween('created_at', [$start_date,$end_date])
					->orderBy('created_at','Desc')
					->get();
			//print_r($reports);die;		
			$bank_charge=$tatal_amount = 0;
			
			$shighra_p_txn=0;
			$number_of_txn=array();
			$index =0;
			
			$upfront_txn = $upfront_profit = $bank_charge=$shighra_f_amount=$shighra_f_profit=$shighra_f_txn=$upfront_amount=$varificaiton_amount=$varification_txn=$varificaiton_profit=0;
			$shighra_p_amount = $shighra_p_txn = $shighra_p_profit =0;
			foreach($reports as $report)
			{
				$index++;
				if($report->status_id == 7 && $report->api_id == 0 && $report->user_id == 110)
				{
					$upfront_txn++;
					$upfront_profit += $report->profit;
					$upfront_amount += $report->amount;
					$bank_charge += $report->bank_charge;
				}
				else if($report->status_id == 1 && $report->api_id == 3) //Shighra F || Super
				{
					
					$shighra_f_amount += $report->amount;
					$shighra_f_profit += $report->profit;
					$shighra_f_txn++;
					
				}
				else if($report->status_id == 1 && $report->api_id == 4) //Shighra P || PayTM
				{
					//$shighra_p_amount = $shighra_p_txn = $shighra_p_profit =0;
					$shighra_p_amount += $report->amount;
					$shighra_p_profit += $report->profit;
					$shighra_p_txn++;
					
				}
				
			
				else if($report->status_id == 1 && $report->api_id == 2)
				{
					$varification_txn ++;
					$varificaiton_profit += $report->profit;
					$varificaiton_amount += $report->amount;
				}
				$tatal_amount +=$report->amount;
				//$bank_charge += $report->bank_charge;
			}
			
			$results['upfront_txn']	 = $upfront_txn;
			$results['upfront_profit']	 = $upfront_profit;
			$results['upfront_amount']	 = $upfront_amount;
			
			$results['shighra_f_txn']	 = $shighra_f_txn;
			$results['shighra_f_profit']	 = $shighra_f_profit;
			$results['shighra_f_amount']	 = $shighra_f_amount;
			
			$results['shighra_p_txn']	 = $shighra_p_txn;
			$results['shighra_p_profit']	 = $shighra_p_profit;
			$results['shighra_p_amount']	 = $shighra_p_amount;
			
			$results['varification_txn']	 = $varification_txn;
			
			$results['varificaiton_profit']	 = $varificaiton_profit;
			$results['varificaiton_amount']	 = $varificaiton_amount;
			
			$results['total_revenue']	 = $shighra_p_profit+$varificaiton_amount +$shighra_f_profit;
			$results['total_expenses']	 = $tatal_amount;
			$results['total_revenue_txn']	 = $upfront_txn+ $shighra_p_txn+$varification_txn;
			$results['exp_varification_txn']	 = $results['total_revenue_txn'];
			
			
			$results['exp_shighra_f_txn']	 = $shighra_f_txn;
			$results['exp_shighra_f_profit']	 = $shighra_f_profit;
			$results['exp_shighra_f_amount']	 = $shighra_f_amount;
			
			$results['exp_shighra_p_txn']	 = $shighra_p_txn;
			$results['exp_shighra_p_profit']	 = $shighra_p_profit;
			$results['exp_shighra_p_amount']	 = $shighra_p_amount;
			
			$results['expenses_varification']	 = $varificaiton_amount;
			$results['total_exp_txn']	 = $shighra_p_txn+$varification_txn;
			$results['expenses_varification_txn']	 = $varification_txn;
		
			$result_json =json_encode($results);
			
			if($request->ajax()){
				return response()->json(['status'=>'success','result' =>$results]);
			}
			$results = json_decode($result_json);
			
			return view('admin.showRevenueExpenses',compact('results'));
		}
		else
			echo "No Permission";
    }
	public function showRevenueExpenses_bkp(Request $request) {
		if(Auth::user()->role->id ==1)
		{
			set_time_limit(2000000);
			ini_set("memory_limit", "200056M");
				
			if($request->all())
			{
				$start_date= $this->getStartDateFormat($request->start_date);
				$end_date = $this->getEndDateFromat($request->start_date,$request->end_date);
			}
			else{
				$start_date= $this->getStartDateFormat();
				$end_date = $this->getEndDateFromat();
			}
			$results['end_date']=$end_date;
			$results['start_date']=$start_date;
			$reports = Report::select('id','profit','bank_charge','status_id','api_id','amount','user_id','remark')
								->whereIn('api_id', [0,2,3,4,5,12,14,16])
								->whereBetween('created_at', [$start_date,$end_date])
								->orderBy('created_at','Desc')
								->get();
			$results['upfront'] = $results['pretxn'] = $results['bank_charge'] = $results['varification'] =0;
			$eko_expenses_f = $eko_expenses_s = $itz_expenses = $eko_expenses_f_c = $eko_expenses_s_c = $smart_expenses_c = $itz_expenses_c =  $smart_expenses = $varification_c = $upfront = $bank_charge =$upfront_c=$pertxn_c=$tatal_amount = 0;
			$yes_expenses_non_kyc_c=$yes_expenses_non_kyc=$yes_expenses_kyc_c=$yes_expenses_kyc=0;
			$upfront_seq = $pretxn_seq=$varification_seq='';
			foreach($reports as $report)
			{
				if($report->status_id == 7 && $report->api_id == 0 && $report->provider_id == 0 && $report->user_id == 110)// UpFront and DT
				{
					//$tatal_amount +=$report->amount;
					$upfront_c++;
					$upfront_seq = $upfront_seq.''.$report->profit.' + ';
					$upfront += $report->profit;
					$bank_charge += $report->bank_charge;
				}
				else if(($report->status_id == 1 )&& $report->api_id == 3) //SARAL
				{
					$tatal_amount +=$report->amount;
					$pertxn_c++;
					$pretxn_seq = $pretxn_seq.''.$report->profit.' + ';
					$results['pretxn'] += $report->profit;
					if( $report->amount <= 3500)
					{
						$eko_expenses_f_c++;
						$eko_expenses_f = $eko_expenses_f + 4.42; //fixed charges
					}
					else
					{
						$eko_expenses_s_c ++;
						$variable_charges = (($report->amount * 0.1)/100);
						$gst = ($variable_charges*18)/100;
						$eko_expenses_s = $eko_expenses_s + ($variable_charges + $gst);
					}
				}
				else if(($report->status_id == 1 ) && $report->api_id == 4)// SMART 
				{
					$tatal_amount +=$report->amount;
					$pertxn_c++;
					$smart_expenses_c ++;
					$results['pretxn'] += $report->profit;
					$smart_expenses = $smart_expenses + 5.30;
				}
				else if(($report->status_id == 1 )&& $report->api_id == 5) // ITZ
				{
					$tatal_amount +=$report->amount;
					$pertxn_c++;
					$itz_expenses_c ++;
					$results['pretxn'] += $report->profit;
					$itz_expenses += 4.13;
				}
				else if(($report->status_id == 1 ) && $report->api_id == 12)
				{
					$tatal_amount +=$report->amount;
					$pertxn_c++;
					$results['pretxn'] += $report->profit;
				}
				else if(($report->status_id == 1 ) && $report->api_id == 16)
				{
					$tatal_amount +=$report->amount;
					$pertxn_c++;
					$results['pretxn'] += $report->profit;
				}
				else if((in_array($report->status_id,array(1,15)))&& $report->api_id == 14)
				{
					
					$yesBRM = new YesBankResponse();
					if($report->status_id == 15)
					{
						
						$res_amount = $yesBRM->getSuccessResponseAmount(11);
						$tatal_amount += $res_amount[0]->total_amount;
					}
					else
						$tatal_amount +=$report->amount;
					if($report->remark == "KYC"){
						$yes_expenses_kyc_c++;
						$yes_expenses_kyc +=$yesBRM->getKycChargeAmount($report->amount);
						
					}
					else if($report->remark == "NON-KYC"){
						$yes_expenses_non_kyc_c++;
						$yes_expenses_non_kyc = $yes_expenses_non_kyc + 5.13;
					}
					
					$pertxn_c++;
					$results['pretxn'] += $report->profit;
				}
				// else if(($report->status_id == 1 || $report->status_id == 7)&& $report->api_id == 14)//yes bank
				// {
					// $tatal_amount +=$report->amount;
					// $pertxn_c++;
					// $results['pretxn'] += $report->profit;
					// $yes_expenses_non_kyc_c++;
					// $yes_expenses_non_kyc = $yes_expenses_non_kyc + 5.13;
				// }
				else if($report->status_id == 1 && $report->api_id == 2)
				{
					$tatal_amount +=$report->amount;
					$varification_seq = $varification_seq.''.$report->profit.' + ';
					$varification_c ++;
					$results['varification'] += $report->profit;
				}
				//$bank_charge += $report->bank_charge;
			}
			$results['eko_expenses_f_c'] = $eko_expenses_f_c;
			
			$results['eko_expenses_s_c'] = $eko_expenses_s_c;
			$results['smart_expenses_c'] = $smart_expenses_c;
			$results['itz_expenses_c']	 = $itz_expenses_c;
			$results['yes_expenses_non_kyc_c'] = $yes_expenses_non_kyc_c; 
			$results['yes_expenses_kyc_c'] = $yes_expenses_kyc_c; 
			
			$results['upfront_c']	 = $upfront_c;
			$results['pertxn_c']	 = $pertxn_c;
			$results['varification_c']	 = $varification_c;
			
			$results['eko_expenses_f']	 = round($eko_expenses_f,2);
			$results['eko_expenses_s']	 = round($eko_expenses_s,2);
			$results['smart_expenses']	 = round($smart_expenses,2);
			$results['itz_expenses'] 	 = 	round($itz_expenses,2);
			$results['yes_expenses_non_kyc'] 	 = 	round($yes_expenses_non_kyc,2);
			$results['yes_expenses_kyc'] 	 = 	round($yes_expenses_kyc,2);
			
			$results['upfront'] 		 = round($upfront,2);
			$results['bank_charge'] 		 = round($bank_charge,2);
			$results['expenses_varification'] = 2 * $varification_c;
			$results['upfront_seq'] = $upfront_seq;
			$results['pretxn_seq'] = $pretxn_seq;
			$results['varification_seq'] = $varification_seq; 
			$results['total_revenue']= $results['upfront'] + $results['pretxn'] + $results['varification'] + $results['bank_charge'];
			$results['total_expenses']= $results['eko_expenses_f'] + $results['eko_expenses_s'] + $results['smart_expenses'] + $results['itz_expenses']+$results['yes_expenses_non_kyc'];
			$results['profit'] = $results['total_revenue'] - $results['total_expenses'];
			$results['tatal_amount'] = round($tatal_amount,2);
			// echo"<pre>";
			// print_r($results);die;
			if($request->ajax()){
				return response()->json(['status'=>'success','result' =>$results]);
			}
			return view('admin.showRevenueExpenses',compact('results'));
		}
		else
			echo "No Permission";
    }
	public function downloadRevenueExpenses(Request $request)
	{
		if(Auth::user()->role->id ==1 || Auth::user()->role_id ==19)
		{

			// echo"<pre>";
			// print_r($request->all());die;
			set_time_limit(2000000);
			ini_set("memory_limit", "200056M");
			$date=date_create($request->from_date);
			$month_name = date_format($date,"M-Y");
			  $user_report = new UserReport();
				$reports = $user_report->exportUserReport($request);
			/* $reports = Report::select('id','user_id','profit','bank_charge','number','status_id','amount','api_id','pay_id','created_at','txnid')
							->whereIn('api_id', [0,2,3,4,5,12])
							->whereBetween('created_at', [$start_date,$end_date])
							->orderBy('created_at','Desc')
							->get(); */
				if($request->export =='excel'){
				Excel::create('records', function ($excel) use ($reports,$request) {
					$excel->sheet('Sheet1', function ($sheet) use ($reports,$request) {
						$arr = array();
						foreach ($reports as $report) 
						{
							if($report->status_id == 6 && $report->api_id == 0)
							{
								$data = array($report->id, $report->user->name.'('.$report->user_id.')',$report->credit_by, $report->profit, $report->bank_charge, $report->number, $report->status->status, round($report->amount,2),$report->total_balance,$report->api->api_name,  $report->pay_id, $report->created_at, $report->txnid,$report->payment_id);
								array_push($arr, $data);
							}
							else if($report->status_id == 1 && in_array($report->api_id,array(3,4,5,12))) // SARAL
							{
								$data = array($report->id, $report->user->name.'('.$report->user_id.')',$report->credit_by, $report->profit, $report->bank_charge, $report->number, $report->status->status, round($report->amount,2),$report->total_balance,$report->api->api_name,  $report->pay_id, $report->created_at, $report->txnid,$report->payment_id);
								array_push($arr, $data);
							}
							// else if($report->status_id == 1 && $report->api_id == 4) // SMART 
							// {
								// $data = array($report->id, $report->user_id, $report->profit, $report->bank_charge, $report->number, $report->status->status, $report->amount,$report->api->api_name,  $report->pay_id, $report->created_at, $report->txnid);
								// array_push($arr, $data);
							// }
							// else if($report->status_id == 1 && $report->api_id == 5) // EKO 
							// {
								// $data = array($report->id, $report->user_id, $report->profit, $report->bank_charge, $report->number, $report->status->status, $report->amount,$report->api->api_name,  $report->pay_id, $report->created_at, $report->txnid);
								// array_push($arr, $data);
							// }
							else if($report->status_id == 1 && $report->api_id == 2)
							{
								$data = array(
											$report->id, 
											$report->user->name.'('.$report->user_id.')',
											$report->credit_by,
											$report->profit, 
											$report->bank_charge, 
											$report->number, 
											$report->status->status, 
											$report->amount,
											$report->api->api_name,  
											$report->pay_id, 
											$report->created_at, 
											$report->txnid,
											$report->payment_id
											);
								array_push($arr, $data);
							}
						}
						$sheet->prependRow( array('','To Date' ,$request->to_date));
						$sheet->prependRow( array('','From Date' ,$request->from_date));
								
						$sheet->row(3, function($row) {
								// call cell manipulation methods
								$row->setBackground('#A9A9A9');
								$row->setFontFamily('Algerian');
						});
						
						$sheet->fromArray($arr, null, 'A1', false, false)->prependRow(array(
							'Id', 'Credited By', 'Credited To','Profit', 'Bank Charge','Number', 'Status',
							'Amount','User Balance', 'Api Name', 'Pay Id', 'Crated At', 'Txn Id','Payment Id'
								)
						);
					});
				})->export('xls');
				}
				else if($request->export =='pertxn_a_summary' || $request->export =='pertxn_m_summary' || $request->export =='varification_summary')
				{
					
					Excel::create($request->export, function ($excel) use ($reports,$month_name,$request) 
					{
						$excel->sheet('Sheet1', function ($sheet) use ($reports,$month_name,$request) 
						{
							$arr = array();
							foreach ($reports as $report) 
							{
								// $cgst =0;
								// $sgst =0;
								// $igst =0;
								$total_revenue =  ($report->total_profit*100)/118;
								// if(State::find($report->user->member->state_id)->id !=35)
								// {
									// $igst=(($total_revenue/100)*18);
									// $total_st=$igst;
								// }
								// else
								// {
									// $cgst=(($total_revenue/100)*9);
									// $sgst=(($total_revenue/100)*9);
									// $total_st=$cgst + $sgst;
								// }
								$data = array(
											$month_name, 
											$report->user->company_id,
											$report->user->parent_id,
											$report->user->name.'('.$report->user_id.')',
											$report->api->api_name,
											$report->total_amount,
											
											$report->total_txn, 
											$report->profit,
											$report->total_profit,
											$total_revenue,
											// round($cgst,2),
											// round($sgst,2),
											// round($igst,2),
											// round($total_st,2),
										);
							array_push($arr, $data);
							}
							
							 $sheet->fromArray($arr, null, 'A1', false, false)->prependRow(array(
								'Month','Company_id','Parent Id', 'Name','Product','Total Amount','Total Txn','Profit Charge','Total Profit','Total Revenue Without ST', 'CGST 9%','SGST 9%','IGST 18%','Total GST') );
								$sheet->prependRow( array('','To Date' ,$request->to_date));
								$sheet->prependRow( array('','From Date' ,$request->from_date));
								$sheet->row(3, function($row) {
								// call cell manipulation methods
								$row->setBackground('#A9A9A9');
								$row->setFontFamily('Times New Roman');
						});
						});
					})->export('xls');
				
				}
				else if($request->export =='new_pertxn_a_summary')
				{
					
					
					Excel::create($request->export, function ($excel) use ($reports,$month_name,$request) 
					{
						$excel->sheet('Sheet1', function ($sheet) use ($reports,$month_name,$request) 
						{
							$arr = array();
							$result=array();
							$pendint_txn=array();
							foreach ($reports as $key=>$report) 
							{
								/* print_r($report);
								continue;die; */
							// if($key == 'S')
							// { 
								foreach($report as $value)
								{
									//print_r($value);die;
									if($key == 'S')
									{
										/* $result[$key][$value->user->company_id.'-'.$value->user_id][$value->api_id][$value->status_id]['total_amount']=$value->total_amount;
										$result[$key][$value->user_id][$value->api_id][$value->status_id]['total_profit']=$value->total_profit;
										$result[$key][$value->user_id][$value->api_id][$value->status_id]['total_txn']=$value->total_txn;
										$result[$key][$value->user_id][$value->api_id][$value->status_ id]['profit']=$value->profit; */
										/*$result[$value->user_id][$value->api->api_name][$value->profit][$value->total_txn]=array($month_name,
														$value->user->company_id,
														$value->user->parent_id,
														$value->user->name.'('.$value->user_id.')',
														$value->api->api_name,
														$value->total_amount,
														$value->total_txn,
														$value->profit,
														$value->total_profit,
														); */
														$data=array($month_name,
														$value->user->company_id,
														$value->user->parent_id,
														$value->user->name.'('.$value->user_id.')',
														$value->api->api_name,
														$value->total_amount,
														$value->total_txn,
														$value->profit,
														$value->total_profit,
														);
										array_push($result, $data);
									}
									else
									{
										 $count=0;
										// print_r($value);//die;
										$all_yes_amt = 0;
									$yes_success_amts = $value->yesBankResponses()->where('status',1)->get();
									if(count($yes_success_amts)>0)
									{
										foreach($yes_success_amts as $success_amt)
										{
											$all_yes_amt += $success_amt->amount;
											$count +=1;
										}
										$value->total_amount = $all_yes_amt;
										
										$data=array($month_name,
															$value->user->company_id,
															$value->user->parent_id,
															$value->user->name.'('.$value->user_id.')',
															$value->api->api_name,
															$value->total_amount,
															$count,
															$value->profit,
															$value->total_profit,
															);
															array_push($result, $data);
									}
									else
									{
										$pendint_txn_id[]=$value->id;
										$data=array($month_name,
															$value->user->company_id,
															$value->user->parent_id,
															$value->id,
															$value->user->name.'('.$value->user_id.')',
															$value->api->api_name,
															$value->total_amount,
															1,
															$value->profit,
															$value->total_profit,
															);
										array_push($pendint_txn, $data);
									}
									//$value->total_amount = $all_yes_amt;
									//print_r($result);die;
									/*$result[$value->user_id][$value->api->api_name][$value->profit][$value->total_txn]=array($month_name,
														$value->user->company_id,
														$value->user->parent_id,
														$value->user->name.'('.$value->user_id.')',
														$value->api->api_name,
														$value->total_amount,
														$value->total_txn,
														$value->profit,
														$value->total_profit,
														); */
									//print_r($value);die;
									//print_r(YesBankResponse::getSuccessRecord($value->id));die;
										/* $result[$value->user_id]['Secure'][$value->report_id][]=array($month_name,'','','','',$value->total_amount,'','','','',$value->report_id); */
										//print_r(
										//$result[$key][$value->user_id][14][15]['total_amount']=$value->total_amount;
									}
									
								}
							//}
							// else{
								
									// print_r($report);die;
									// print_r(YesBankResponse::getSuccessRecord($report->$id));die;
								// }
								// print_r($report);
								// continue;die;
							}
							// die;
							//print_r($result);die;
							//print_r($result);//die;
							// foreach($result as $key=>$user_value)
							// {
								// foreach($user_value as $api_key=>$api_value)
								// {
									// if($api_key =="Secure/15")
									// {
										// /* foreach($api_value as $report_id =>$profit_value)
										// {
											// foreach($profit_value as $txn_key=>$txn_value)
											// {
												
												// $final_result[] =$txn_value;
												// print_r($final_result);die;
											// }
										// } */
									// }
									// else
									// {
										// foreach($api_value as $profit_key =>$profit_value)
										// {
											// foreach($profit_value as $txn_key=>$txn_value)
											// {
												
												// $final_result[] =$txn_value;
												// //print_r($final_result);die;
											// }
										// }
									// }
								// }
							// } 
							//print_r($final_result);die;
							 $sheet->fromArray($result, null, 'A1', false, false)->prependRow(array(
								'Month','Company_id','Parent Id', 'Name','Product','Total Amount','Total Txn','Profit Charge','Total Profit','Total Revenue Without ST', 'CGST 9%','SGST 9%','IGST 18%','Total GST') );
								$sheet->prependRow( array('','To Date' ,$request->to_date));
								$sheet->prependRow( array('','From Date' ,$request->from_date));
								$sheet->row(3, function($row) {
								// call cell manipulation methods
								$row->setBackground('#A9A9A9');
								$row->setFontFamily('Times New Roman');
						});
						});
					})->export('xls');
				
				}
				else if($request->export =='upfront_summary')
				{					
					Excel::create($request->export, function ($excel) use ($reports,$month_name,$request) 
					{
						$excel->sheet('Sheet1', function ($sheet) use ($reports,$month_name,$request) 
						{
							$arr = array();
							foreach ($reports as $report) 
							{
								// $cgst =0;
								// $sgst =0;
								// $igst =0;
								$total_revenue =  ($report->total_profit*100)/118;
								// if(State::find($report->user->member->state_id)->id !=35)
								// {
									// $igst=(($total_revenue/100)*18);
									// $total_st=$igst;
								// }
								// else
								// {
									// $cgst=(($total_revenue/100)*9);
									// $sgst=(($total_revenue/100)*9);
									// $total_st=$cgst + $sgst;
								// }
								$user_id=User::where('name',$report->credit_by)->select('id')->first();
								$data = array(
											$month_name, 
											$report->credit_by.'('.@$user_id->id.')',
											$report->total_amount,
											$report->total_profit,
											$report->total_bank_charge,
											$total_revenue,
											// round($cgst,2),
											// round($sgst,2),
											// round($igst,2),
											// round($total_st,2),
										);
							array_push($arr, $data);
							}
							
							 $sheet->fromArray($arr, null, 'A1', false, false)->prependRow(array(
								'Month', 'Name','Total Amount','Total Profit','Bank Charge','Total Revenue Without ST', 'CGST 9%','SGST 9%','IGST 18%','Total GST') );
								
								$sheet->prependRow( array('','To Date' ,$request->to_date));
								$sheet->prependRow( array('','From Date' ,$request->from_date));
								$sheet->row(3, function($row) {
								// call cell manipulation methods
								$row->setBackground('#A9A9A9');
								$row->setFontFamily('Times New Roman');
						});
						});
					})->export('xls');
				
				}
				else
				{
					$excel_name=$request->export."-".$request->from_date;
					$_SESSION['reports']=$reports;
					Excel::create($excel_name, function ($excel) use ($reports,$request) 
					{
						$excel->setCreator('Levinm Fintech')->setCompany('Levinm Fintech Pvt. Ltd.');
						$excel->setDescription("Report from ".$request->from_date." to ".$request->to_date);
						for($i=1; $i<=5; $i++)
						{
							$reports =$_SESSION['reports'];
							if(empty($reports))
								break;
							$excel->sheet("Sheet_$i", function ($sheet) use ($reports,$request) 
							{
								$arr = array();
								foreach($reports as $key=>$report)
								{
									$index=$key;
									if(in_array($index,array(9999,19999,29999,39999)))
									{
										$data = array(
													$report->id, 
													($request->export=='upfront')?($report->user->name.'('.$report->user_id.')'):'', 
													($request->export=='upfront')?($report->credit_by):($report->user->name.'('.$report->user_id.')'),
													($request->export=='upfront')?'':($report->user->company_id),
													//$report->credit_by,
													$report->profit, 
													$report->bank_charge, 
													$report->number, 
													$report->status->status, 
													round($report->amount,2),
													$report->total_balance,
													// $report->total_balance,
													$report->api->api_name,
													$report->created_at, 
													$report->txnid
													);
										array_push($arr, $data);
										
										unset($reports[$key]);
										break;
									}
									else
									{
										$data = array(
												$report->id, 
												//$report->user->name.'('.$report->user_id.')', 
												($request->export=='upfront')?($report->user->name.'('.$report->user_id.')'):'',
												($request->export=='upfront')?($report->credit_by):($report->user->name.'('.$report->user_id.')'),
												($request->export=='upfront')?'':($report->user->company_id),
												//$report->credit_by,
												$report->profit, 
												$report->bank_charge, 
												$report->number, 
												$report->status->status, 
												round($report->amount,2),
												$report->total_balance,
												$report->api->api_name, 
												$report->created_at, 
												$report->txnid,
											);
										array_push($arr, $data);
										unset($reports[$key]);
									}
									 
								}
							
							$sheet->fromArray($arr, null, 'A1', false, false)->prependRow(array(
							'Id', 'Credited By','Credited TO', 'Company ID','Profit', 'Bank Charge','Number / A.C No ', 'Status',
							'Amount','User Balance', 'Api Name', 'Crated At', 'Txn Id'
								)
									);
							$sheet->prependRow( array('','From Date' ,$request->from_date));
							$sheet->prependRow( array('','To Date' ,$request->to_date));
								
							$_SESSION['reports']=$reports;
							$sheet->row(3, function($row) {
								// call cell manipulation methods
							$row->setBackground('#A9A9A9');
							$row->setFontFamily('Algerian');
						});
								
							});
						}
					})->export('xls');
				}
		}
		else
			echo "No Permission";
	}
	public function getAccountDetails(Request $request)
	{
		$account_number=$request->account_no;
		$details= Beneficiary::select('account_number','bank_name','customer_number as customer_mobile_no','user_id','name as customer_name')
								->where('account_number',$account_number)->first();
		
		if($request->ajax()){
				return response()->json(['status'=>'success','result' =>$details]);
			}
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
	
  public function exportUserReport(Request $request)
  {
	  $user_report = new UserReport();
	  $reports = $user_report->exportUserReport($request);
	  echo "<pre>";
	  print_r($reports);die;
	  
	  Excel::create('records', function ($excel) use ($reports,$request) 
				{
						$excel->setCreator('Levinm Fintech')->setCompany('Levinm Fintech Pvt. Ltd.');
						$excel->setDescription("Report from ".$request->fromdate." to ".$request->todate);
						for($i=1; $i<=5; $i++)
						{
							$reports =$_SESSION['reports'];
							if(empty($reports))
								break;
							$excel->sheet("Sheet_$i", function ($sheet) use ($reports,$request) 
							{
								$arr = array();
								foreach($reports as $key=>$report)
								{
									$index=$key;
									if(in_array($index,array(9999,19999,29999,39999)))
									{
										$data = array($report->created_at, $report->txnid,$report->ackno, $report->api->api_name, @$report->beneficiary->customer_number, @$report->beneficiary->name, $report->number, @$report->beneficiary->bank_name, @$report->beneficiary->ifsc, $report->bank_ref, $report->amount, $report->channel, @$report->status->status);
										array_push($arr, $data);
										unset($reports[$key]);
										break;
									}
									else
									{
										$data = array($report->created_at, $report->txnid,$report->ackno, $report->api->api_name, @$report->beneficiary->customer_number, @$report->beneficiary->name, $report->number, @$report->beneficiary->bank_name, @$report->beneficiary->ifsc, $report->bank_ref, $report->amount, $report->channel, @$report->status->status);
										array_push($arr, $data);
										unset($reports[$key]);
									}
									 
								}
								$sheet->fromArray($arr, null, 'A1', false, false)->prependRow(array(
										'Date', 'tx id', 'Ack No','Product', 'Mobile', 'Name', 'Account Number',
										'Bank Name', 'IFSC', 'UTR Number', 'Amount', 'Channel', 'Status'
											)
									);
								$sheet->prependRow( array('','To Date' ,$request->fromdate));
								$sheet->prependRow( array('','From Date' ,$request->fromdate));
								$_SESSION['reports']=$reports;
								
							});
						}
						})->export('xls');
  }
  /*
		@
		@get the details of user by id
		@
		@
		@*/
  public function getUserByID(Request $request) 
  {
	
	$user=new User();
	$id=$request->user_id;
	$exist_user=$user->getUserById($id);
	$user_details=array(
					'name' => $exist_user->name,
					'email' => $exist_user->email,
					'mobile' => $exist_user->mobile,
					'role' => $exist_user->role->role_title,
					'parent_name' => $exist_user->parent->name,
					'balance' => round($exist_user->balance->user_balance,3),
					'lm_code' => 'LM '.$id,
						);
	return response()->json(['status' => 1, 'user_details' => $user_details]);
	  
  }
  public function allMdList() 
	{
		if(in_array(Auth::user()->role_id,array(1)))
		{
			$md_lists= Company::MDLists();
			return view('admin.md-list')->with(['md_lists'=>$md_lists]);
		}
		else
		{
			return view('errors.page-not-found');
		}
	}
	public function getAllMdAgentsRecord_live_bkp_13Jan($id)
	{
		if(in_array(Auth::user()->role_id,array(1,19)))
		{
			$retailer_lists = UserReport::getAllAgentOfMd($id);
			$reports = Report::whereIn('user_id',$retailer_lists)->whereIn('api_id',[3,4,5,12,14])->orderBy('created_at','DESC')->paginate(30);
			return view('admin.md-agent-records')->with(['reports'=>$reports,'company_id'=>$id]);
		}
		else
		{
			echo "You do not have permission";
		}
	}
	public function getAllMdAgentsRecord($id)
	{
		if( Auth::user()->role_id == 1 || Auth::user()->role_id ==19)
		{
			$retailer_lists = CustomTraits::getAllAgentOfMd($id);
			$end_time=date('Y-m-d H:i:s');
			$start_time = date('Y-m-d H:i:s',strtotime('-5 days',strtotime($end_time)));
			 //$reports = Report::whereIn('user_id',$retailer_lists)->orderBy('created_at','DESC')->paginate(30); 
			$reports = Report::select('id','user_id','created_at','api_id','number','provider_id','description','beneficiary_id','txnid','amount','profit','recharge_type','total_balance','status_id')->whereIn('api_id',[3,4,5,12,14])->whereIn('user_id',$retailer_lists)->whereBetween('created_at',[$start_time,$end_time])->orderBy('created_at','DESC')->paginate(30); 
			
			return view('admin.md-agent-records')->with(['reports'=>$reports,'company_id'=>$id,'page_type'=>'md','user_id'=>'']);
		}
		return view('errors.page-not-found');
		//return view('admin.md-agent-records')->with(['reports'=>$reports,'company_id'=>$id]);
	}
	public function generateMdInvoice_bkp(Request $request)
	{
		
		$start_date= $this->getStartDateFormat($request->from_date);
		$end_date = $this->getEndDateFromat($request->from_date,$request->to_date);
		$invoice = $this->generateInvoiceFormat($request->company_id,$start_date,$end_date);
		return $invoice;
	}
	public function generateMdInvoice(Request $request)
	{
		if($request->ajax()){
			if( Auth::user()->role_id == 1 || Auth::user()->role_id ==19)
			{
			$start_date= $this->getStartDateFormat($request->from_date);
			$end_date = $this->getEndDateFromat($request->from_date,$request->to_date);
			// $retailer_reports = CustomTraits::getMdInvoice($request->company_id,$start_date,$end_date);
			$invoice = $this->generateInvoiceFormat($request->company_id,$start_date,$end_date,null,$request->invoice_of);
			
			return $invoice;
			}
			return view('errors.page-not-found');
		}
		return view('errors.page-not-found');
	}
	private function generateInvoiceFormat_bkp($company_id,$start_date,$end_date)
	{
		$total_amount  = $sgst = $cgst =$igst = 0;
		$report_model =new Report();
		$st_date=date_create($start_date);
		$ed_date=date_create($end_date);
		$st_month_name = date_format($st_date,"d-M-Y");
		$ed_month_name = date_format($ed_date,"d-M-Y");
		if(in_array(Auth::user()->role_id,array(1,3,19)))
		{
			if(Auth::user()->role_id==1 || Auth::user()->role_id ==19)
			{
				$isMdChargeSet=0;
				$logined_user_id=Auth::id();
				$user_details = User::select('id','name','email','mobile','member_id','company_id')->find($logined_user_id);
				$com_lm_id = $user_details->id;
				$comp_name = @$user_details->member->company;
				$comp_address = @$user_details->member->office_address;
				$comp_email=$user_details->email;
				$comp_cin_no="U74140DL2016PTC290796";
				$comp_mobile=$user_details->mobile.' ,Tel: +91 '.$user_details->company->company_phone;
				$comp_pancard = @$user_details->member->pan_number;
				$comp_gstno = @$user_details->member->gst_number;
				$comp_state = State::find($user_details->member->state_id)->name;
				$comp_state_code = $user_details->member->state_id;;
				$comp_logo = @$user_details->company->company_logo;
				
				$company_details=Company::select('user_id')->find($company_id);
				$invoice_users = User::select('id','name','mobile','member_id','company_id')->find($company_details->user_id);
				if($invoice_users->mdpertxn->ptxn1 >0 || $invoice_users->mdpertxn->ptxn2 >0 || $invoice_users->mdpertxn->ptxn3 >0 || $invoice_users->mdpertxn->ptxn4 >0 || $invoice_users->mdpertxn->ptxn5 >0)
			$isMdChargeSet=1;
				$invoice_lm_id = $invoice_users->id;
				$invoice_name = @$invoice_users->member->company;
				$invoice_comp_address = @$invoice_users->member->office_address;
				$invoice_comp_pancard = @$invoice_users->member->pan_number;
				$invoice_comp_gstno= @$invoice_users->member->gst_number;
				$invoice_state= State::find($user_details->member->state_id)->name;;
				$invoice_state_code = @$invoice_users->member->state_id;
				
			}
			else{
				$user_details = User::where(['company_id'=>$company_id,'role_id'=>3]);
				$logined_company_name=Auth::user()->company->company_name;
				$logined_company_address=@Auth::user()->company->company_address;
				$logined_company_email=@Auth::user()->company->company_email;
				$logined_user_pancard="Not available";
				$logined_user_gst="Not available";
			}
		}
		$invoice_form=array(
							'com_lm_id'=>$com_lm_id,
							'comp_name'=>$comp_name,
							'comp_address'=>$comp_address,
							'comp_cin_no'=>$comp_cin_no,
							'comp_email'=>$comp_email,
							'comp_mobile'=>$comp_mobile,
							'comp_pancard'=>$comp_pancard,
							'comp_gstno'=>$comp_gstno,
							'comp_state'=>$comp_state,
							'comp_state_code'=>$comp_state_code,
							);
		$invoice_to=array(
							'invoice_lm_id'=>$invoice_lm_id,
							'invoice_name'=>$invoice_name,
							'address'=>$invoice_comp_address,
							'pan_card'=>$invoice_comp_pancard,
							'gstno'=>$invoice_comp_gstno,
							'state'=>$invoice_state,
							'state_code'=>$invoice_state_code,
							
							);
			
		$result=array();
		//print_r($retailer_reports);die;
		$up_ad_charges=Report::getUpfrontDtAmountByUserId($company_details->user_id,$start_date,$end_date);
		$total_value = $up_ad_charges->total_amount;
		$total_amount = $charges = $up_ad_charges->total_profit + $up_ad_charges->total_bank_charge;
		$retailer_reports = UserReport::getMdInvoice($company_id,$start_date,$end_date,$invoice_lm_id,$isMdChargeSet); 
		if(count($retailer_reports) >0)
		{
			// foreach($retailer_reports as $key => $reports)
			// {
						// $result[$key]['api_id'] = $reports->api->api_name;
						// $result[$key]['total_txn'] = $reports->total_txn;
						// $result[$key]['profit_charge'] = $reports->profit_charge;
						// $result[$key]['total_profit'] = $reports->total_profit;
						// $total_amount += $reports->total_profit;
				
			// }
			$index=0;
			foreach($retailer_reports as $key => $reports)
			{
				
				foreach($reports as $key => $report)
				{
							$result[$index]['api_id'] = $report->api->api_name;
							$result[$index]['total_txn'] = $report->total_txn;
							$result[$index]['profit_charge'] = $report->profit_charge;
							$result[$index]['total_profit'] = $report->total_profit;
							$total_amount += $report->total_profit;
					$index++;
				}
			}
			 $result =array('status' => 1, 'result' => $result,'start_date'=>$st_month_name,'end_date'=>$ed_month_name,'invoice_to'=>$invoice_to,'invoice_form'=>$invoice_form);
		}
		else
		{
			$result=['status' => 0, 'result' => "No Invoice of selected date"];
		}
		$taxbale_amount=round((($total_amount*100)/118),2);
		if($invoice_state_code == 7){
			$sgst = $cgst = round((($taxbale_amount*9)/100),2);
		}else
			$igst =round((($taxbale_amount*18)/100),2);
		
		return response()->json(['data' =>$result,'total_value' =>$total_value,'charges'=>round($charges,2),'total_amount'=>round($total_amount,2),'taxbale_amount'=>$taxbale_amount,'cgst' =>$cgst, 'sgst'=>$sgst,'igst'=>$igst]);	
	}
	private function generateInvoiceFormat($company_id=null,$start_date,$end_date,$user_id=null,$invoice_of=null)
	{	
		$total_amount  = $sgst = $cgst =$igst = 0;
		$report_model =new Report();
		$st_date=date_create($start_date);
		$ed_date=date_create($end_date);
		$st_month_name = date_format($st_date,"d-M-Y");
		$ed_month_name = date_format($ed_date,"d-M-Y");
		if(in_array(Auth::user()->role_id,array(1,3)))
		{
			$isMdChargeSet=0;
			if(Auth::user()->role_id==1 || Auth::user()->role_id ==19)
			{
				// echo "<br>company_id ".$company_id;
				// echo "<br>start_date ".$start_date;
				// echo "<br>end_date ".$end_date;
				// echo "<br>user_id ".$user_id;
				// echo "<br>invoice_of ".$invoice_of;//die;  
				// die;
				$logined_user_id=Auth::id();
				$user_details = User::select('id','name','email','mobile','member_id','company_id')->find($logined_user_id);
				$com_lm_id = $user_details->id;
				$comp_name = @$user_details->member->company;
				$comp_address = @$user_details->member->office_address;
				$comp_email=$user_details->email;
				$comp_cin_no="U74140DL2016PTC290796";
				$comp_mobile=$user_details->mobile.' ,Tel: +91 '.$user_details->company->company_phone;
				$comp_pancard = @$user_details->member->pan_number;
				$comp_gstno = @$user_details->member->gst_number;
				$comp_state = State::find($user_details->member->state_id)->name;
				$comp_state_code = $user_details->member->state_id;;
				if($invoice_of == "md")
				{
					$company_details=Company::select('user_id')->find($company_id);
					$invoice_users = User::select('id','name','mobile','member_id','company_id')->find($company_details->user_id);
					//print_r($invoice_users);die;
					$invoice_userID = $company_details->user_id;
					if($invoice_users->mdpertxn->ptxn1 >0 || $invoice_users->mdpertxn->ptxn2 >0 || $invoice_users->mdpertxn->ptxn3 >0 || $invoice_users->mdpertxn->ptxn4 >0 || $invoice_users->mdpertxn->ptxn5 >0)
					$isMdChargeSet=1;
				}
				else if($invoice_of == "distributor" || $invoice_of == "retailer")
				{
					//echo "retailse";die;
						$invoice_users = User::select('id','name','mobile','member_id','company_id','role_id')->find($user_id);
					$invoice_userID = $user_id;
				}
			
				$invoice_lm_id = $invoice_users->id;
				$invoice_name = @$invoice_users->member->company;
				$invoice_comp_address = @$invoice_users->member->office_address;
				$invoice_comp_pancard = @$invoice_users->member->pan_number;
				$invoice_comp_gstno= @$invoice_users->member->gst_number;
				$invoice_state= State::find($invoice_users->member->state_id)->name;;
				$invoice_state_code = @$invoice_users->member->state_id;
				
			}
			else{
				$user_details = User::where(['company_id'=>$company_id,'role_id'=>3]);
				$logined_company_name=Auth::user()->company->company_name;
				$logined_company_address=@Auth::user()->company->company_address;
				$logined_company_email=@Auth::user()->company->company_email;
				$logined_user_pancard="Not available";
				$logined_user_gst="Not available";
			}
		}
		$invoice_form=array(
							'com_lm_id'=>$com_lm_id,
							'comp_name'=>$comp_name,
							'comp_address'=>$comp_address,
							'comp_cin_no'=>$comp_cin_no,
							'comp_email'=>$comp_email,
							'comp_mobile'=>$comp_mobile,
							'comp_pancard'=>$comp_pancard,
							'comp_gstno'=>$comp_gstno,
							'comp_state'=>$comp_state,
							'comp_state_code'=>$comp_state_code,
							);
		$invoice_to=array(
							'invoice_lm_id'=>$invoice_lm_id,
							'invoice_name'=>$invoice_name,
							'address'=>$invoice_comp_address,
							'pan_card'=>$invoice_comp_pancard,
							'gstno'=>$invoice_comp_gstno,
							'state'=>$invoice_state,
							'state_code'=>$invoice_state_code,
							
							);
			
		//print_r($retailer_reports);die;
		$up_ad_charges=Report::getUpfrontDtAmountByUserId($invoice_userID,$start_date,$end_date);
		$total_value = $up_ad_charges->total_amount;
		$total_amount = $charges = $up_ad_charges->total_profit + $up_ad_charges->total_bank_charge;
		
		if($invoice_of == "md")
		{
			$retailer_reports = CustomTraits::getMdInvoice($company_id,$start_date,$end_date,$invoice_lm_id,$isMdChargeSet);
		}
		else
		{
			$retailer_reports = CustomTraits::getDistributorInvoice($invoice_lm_id,$start_date,$end_date,$invoice_of);
		}
		
		$result=array();
		//print_r($retailer_reports);die;
		if(count($retailer_reports) >0)
		{
			$index=0;
			foreach($retailer_reports as $key => $reports)
			{
				
				foreach($reports as $key => $report)
				{
							$result[$index]['api_id'] = $report->api->api_name;
							$result[$index]['total_txn'] = $report->total_txn;
							$result[$index]['profit_charge'] = $report->profit_charge;
							$result[$index]['total_profit'] = $report->total_profit;
							$total_amount += $report->total_profit;
					$index++;
				}
			}
			 $result =array('status' => 1, 'result' => $result,'start_date'=>$st_month_name,'end_date'=>$ed_month_name,'invoice_to'=>$invoice_to,'invoice_form'=>$invoice_form);
		}
		else
		{
			$result=['status' => 0, 'result' => "No Invoice of selected date"];
		}
		$taxbale_amount=round((($total_amount*100)/118),2);
		if($invoice_state_code == 7){
			$sgst = $cgst = round((($taxbale_amount*9)/100),2);
		}else
			$igst =round((($taxbale_amount*18)/100),2);
		
		return response()->json(['data' =>$result,'total_value' =>$total_value,'charges'=>round($charges,2),'total_amount'=>round($total_amount,2),'taxbale_amount'=>$taxbale_amount,'cgst' =>$cgst, 'sgst'=>$sgst,'igst'=>$igst]);	
	}
	public function getAllDistributorLists($company_id)
	{
		if(in_array(Auth::user()->role_id,array(1,19)))
		{
			$role_id=4;
			$company_details = Company::getUserIdOfCompanyId($company_id);
			$parent_id = $company_details->user_id;
			$users = CustomTraits::getDistributorOfMd($company_id,$role_id,$parent_id);
		//print_r($users);die;
			return view('admin.distributor-lists')->with(['users'=>$users,'company_id'=>$company_id]);
		}else{
			return view('errors.page-not-found');
		}
	}
	public function getAllDistributorReports($user_id)
	{
		if(in_array(Auth::user()->role_id,array(1,19)))
		{
			$user_list=User::where('parent_id',$user_id)->pluck('id','id')->toArray();
			$reports = Report::select('id','user_id','created_at','api_id','number','provider_id','description','beneficiary_id','txnid','amount','profit','recharge_type','total_balance','status_id')->whereIn('user_id',$user_list)->orderBy('created_at','DESC')->paginate(30);
			return view('admin.md-agent-records')->with(['reports'=>$reports,'company_id'=>'','user_id'=>$user_id,'page_type'=>'distributor']);
		}
		return view('errors.page-not-found');
	}
	public function generateDistributorInvoice(Request $request)
	{
		
		if($request->ajax()){
			//print_r($request->all());die;
            $start_date= $this->getStartDateFormat($request->from_date);
			$end_date = $this->getEndDateFromat($request->from_date,$request->to_date);
			$invoice = $this->generateInvoiceFormat(null,$start_date,$end_date,$request->user_id,$request->invoice_of);
			return $invoice;
		}
		return view('errors.page-not-found');
	}
	public function getAllRetailerLists($company_id)
	{
		if(in_array(Auth::user()->role_id,array(1,19)))
		{
			$role_id=5;
			$company_details = Company::getUserIdOfCompanyId($company_id);
			$parent_id = $company_details->user_id;
			/*
			| Notice : Below function return all agent of Md, no need to think about function name...
			|
			|*/
			
			$users = CustomTraits::getDistributorOfMd($company_id,$role_id,$parent_id);
			return view('admin.retailer-lists')->with(['users'=>$users,'company_id'=>$company_id]);
		}
		return view('errors.page-not-found');
	}
	public function getRetailerReports($user_id)
	{
		if(in_array(Auth::user()->role_id,array(1)))
		{
			$user_list=array($user_id);
			$reports = Report::select('id','user_id','created_at','api_id','number','provider_id','description','beneficiary_id','txnid','amount','profit','recharge_type','total_balance','status_id')->whereIn('user_id',$user_list)->orderBy('created_at','DESC')->paginate(30);
			return view('admin.md-agent-records')->with(['reports'=>$reports,'company_id'=>'','user_id'=>$user_id,'page_type'=>'retailer']);
		}
		return view('errors.page-not-found');
	}
	public function showAmountInWords(Request $request)
	{
		if($request->ajax()){
			$amount = $request->amount;
			$word = $this->displayAmountInWords($amount);
			return response()->json(['status'=>1,'word'=>$word]);
		}
		return view('errors.page-not-found');
	}
	public function tranactionView(Request $request)
	{
		$start_date = ($request->fromdate) ? $request->fromdate. " 00:00:00" : date('Y-m-d').' 00:00:00';
		$end_date = ($request->todate) ? $request->todate. " 23:59:59" : date('Y-m-d').' 23:59:59';
		$start_date = date("Y-m-d H:i:s", strtotime($start_date));
		$end_date = date("Y-m-d H:i:s", strtotime($end_date));
		$reportsQuery = Report::selectRaw('count(id) as txnCount,sum(amount) as txnAmount, sum(credit_charge) as txnCommission,sum(abs(debit_charge)) as debitCharge,api_id,status_id')->whereIn('status_id',[1,2,3,4,20,21,24])->whereBetween('created_at',[$start_date,$end_date])->groupBy('api_id')->groupBy('status_id')->orderBy('status_id','asc');
		//print_r($reports);die;
		if(Auth::user()->role_id ==1 || Auth::user()->role_id ==19)
		{
			$reports = $reportsQuery->get();
		}elseif(in_array(Auth::user()->role_id,array(5))){
			$reports = $reportsQuery->where('user_id',Auth::id())->get();
		}
		else{
			return view('errors.page-not-found');
		}
		
		$all_reports =array();
	 
		foreach($reports as $report)
		{  
			$all_reports[@$report->api->api_name][@$report->status->status]['Total Sale']= @$report->txnAmount;
			$all_reports[@$report->api->api_name][@$report->status->status]['Txn Count']= @$report->txnCount;
			if(Auth::user()->role_id ==1){
				$all_reports[@$report->api->api_name][@$report->status->status]['Charge']= number_format($report->debitCharge,2);
				$all_reports[@$report->api->api_name][@$report->status->status]['Commission']= number_format($report->txnCommission,2);
			}
		}
	
		if(Auth::user()->role_id==5)
		return view('reports.businessview',compact('all_reports'));
		return view('admin.account.txn-view',compact('all_reports'));
	}
	public function operatorReport(Request $request){

		if(Auth::user()->role_id ==1 || Auth::user()->role_id ==19)
		{
			$start_date = ($request->fromdate) ? $request->fromdate. " 00:00:00" : date('Y-m-d').' 00:00:00';
			$end_date = ($request->todate) ? $request->todate. " 23:59:59" : date('Y-m-d').' 23:59:59';
			$start_date = date("Y-m-d H:i:s", strtotime($start_date));
			$end_date = date("Y-m-d H:i:s", strtotime($end_date));

			if($request->export=="EXPORT")
			{
				$reports = Report::selectRaw('id,user_id,txnid,created_at,provider_id,amount,pay_id,tds,commission, beneficiary_id,description,credit_charge,abs(debit_charge) as debit_charge,api_id,status_id,profit')->where('user_id',Auth::id())->whereIn('status_id',[1,2,3,20,21,4])->whereBetween('created_at',[$start_date,$end_date])->where('provider_id','!=',41)->get();
			}	
				$reports = Report::selectRaw('count(id) as txnCount,sum(amount) as txnAmount, sum(credit_charge) as txnCommission,sum(abs(debit_charge)) as debitCharge,status_id,provider_id')->whereIn('status_id',[1])->whereBetween('created_at',[$start_date,$end_date])->groupBy('provider_id')->where('provider_id','!=',41)->groupBy('status_id')->orderBy('status_id','asc')->get();
			return view('admin.account.operator-report',compact('reports'));
		}
		return view('errors.permission-denied');

	}
	
}


