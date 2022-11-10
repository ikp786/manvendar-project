<?php

namespace App\Http\Controllers;

use App\User;
use Excel;
use Illuminate\Http\Request;
use App\Report;
use DB;
use App\Company;
use App\Balance;
use Carbon\Carbon;
use App\Http\Requests;
use App\Loadcash;
use App\Pmethod;
use App\Netbank;
use Response;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class PriController extends Controller {

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {
        $reports = Report::where('user_id', Auth::id())
                ->orderBy('id', 'DESC')
                ->where('recharge_type', '!=' , 1)
                ->paginate(40);
        
        return view('report.index', compact('reports'));
    }
	
	public function pend_refd_intd()
	{
		$SessionID = Company::find(3)->sessionid;
		if (in_array(Auth::user()->role_id,array(1,11,12,14)))
		{
            $reports = Report::where('provider_id', 41)
                    ->orderBy('id', 'DESC')
					->where('status_id',9)
					->take(500)
                    ->paginate(40);
					return view('admin.report.pend_refd_intd', compact('reports'))->with('sessionid', $SessionID);
		}
		if (Auth::user()->role_id == 5) {
            $reports = Report::where('user_id', Auth::id())
                    ->where('provider_id', 41)
                    ->orderBy('id', 'DESC')
                    ->where('status_id',2)
                    ->where('refund',1)
                    ->where('user_id', Auth::id())
					->take(500)
                    ->paginate(40);
		return view('report.pend_refd_intd', compact('reports'))->with('sessionid', $SessionID);
        }
        if (Auth::user()->role_id == 7) {
            $reports = Report::where('user_id', Auth::id())
                    ->where('provider_id', 41)
                    ->orderBy('id', 'DESC')
                    ->where('status_id',9)
                    ->where('user_id', Auth::id())
                    ->take(500)
                    ->paginate(40);
        return view('report.pend_refd_intd', compact('reports'))->with('sessionid', $SessionID);
        }
		
    }
	
	public function pend_refd_intd_search(Request $request)
	{
		if(in_array(Auth::user()->role_id,array(1,11,12,14)))
		{
			$SessionID = Company::find(3)->sessionid;
			if($request->export =="export")
			{
				$query = Report::select('id','user_id','profit','bank_charge','number','status_id','refund','amount','total_balance','api_id','pay_id','created_at','txnid','payment_id','beneficiary_id','channel');
				
				$export_type='';
				$search_type = $request->search_pri;
				if($search_type == 'p')
				{
					$export_type="Pending Records";
					$query->where('status_id',3);
					
				}
				elseif($search_type == 'r')//refund pending
				{
					$export_type="Refund Pending Records";
					$query->whereIn('status_id',[2,20]);
					$query->where(['refund'=>1]);
					
				}
				elseif($search_type == 'i')// initiated
				{
					$export_type="Initiated Records";
					$query->where(['status_id'=>9]);
					
				}
				elseif($search_type == 'dr')//Refunded
				{
					$export_type="Refunded";
					$query->where(['status_id'=>4,'refund'=>0]);
					
					
				}
				 elseif($search_type == 'b')//blank TID 
				{
					$export_type="Blank TID";
					$query->where(['txnid'=>null]);
					$query->where(['status_id'=>3]);
					
					
				}
				elseif($search_type =='d_tid')
				{
					$export_type="Double Refunded";
					$query->where('status_id',4);
					$query->groupBy('txnid');
					$query->havingRaw('count(*) > 1');
					
					
					
				}
				if($search_type =='d_tid')
					$query->where('provider_id',0);
				else
					$query->where('provider_id',41);
				if($search_type =='d_tid')
					$query->whereIn('api_id',[6]);
				else
					$query->whereIn('api_id',[2,16,17]);
				$query->orderBy('created_at','desc');
				$query->orderBy('user_id','desc');
				$reports = $query->get();
				
				Excel::create($export_type, function ($excel) use ($reports,$export_type) 
					{
						$excel->sheet('Sheet1', function ($sheet) use ($reports,$export_type) 
						{
							$arr = array();
							foreach ($reports as $report) 
							{
								$mode='';
								if($report->channel ==1)
									$mode = "NEFT";
								elseif($report->channel ==2)
									$mode = "IMPS";
								$data = array(
											$report->created_at,
											$report->id,
											$report->user->name.'('.$report->user_id.')',
											$report->profit,
											$report->bank_charge,
											$report->number,
											@$report->beneficiary->customer_number,
											@$report->api->api_name,
											$report->status->status,
											$report->refund,
											$report->amount,
											$report->api->api_name,
											$mode,
											$report->pay_id,
											$report->txnid,
											$report->payment_id,
											$report->total_balance,
											
											
										);
							array_push($arr, $data);
							}
							
							
							 $sheet->fromArray($arr, null, 'A1', false, false)->prependRow(array(
								'Created At', 'Id','user id','Profit','bank Charge','Number','Customer Number','Product','Status','1=>Refund Pending','Amount','product','Channel','Pay Id','Txn Id','Payment Id','Total Balance') );
								
								$sheet->row(1, function($row) {
								// call cell manipulation methods
								$row->setBackground('#A9A9A9');
								$row->setFontFamily('Times New Roman');
						});
						});
					})->export('xls');
			
			}
		 if ($request->search_pri == 'p') 
		 {
                $reports = Report::where('provider_id', 41)
                    ->orderBy('id', 'DESC')
					->where('status_id',3)
					->take(500)
                    ->paginate(500);
                $r_count= $reports->count();
            }
		elseif($request->search_pri == 'r')
		{
		$reports = Report::where('provider_id', 41)
                    ->orderBy('id', 'DESC')
					->whereIn('status_id',[2,20,16,17])
					->where('refund',1)
                    ->take(500)
                    ->paginate(500);
			$r_count= $reports->count();
		}
		elseif($request->search_pri == 'b')
		{
			$reports = Report::where('provider_id', 41)
                    ->orderBy('id', 'DESC')
					->where('txnid',null)
					->whereIn('status_id',[3,8,9])
					->take(500)
                    ->paginate(500);
                 $r_count= $reports->count();
		}

		elseif($request->search_pri == 'i')
		{
			$reports = Report::where('provider_id', 41)
                    ->orderBy('id', 'DESC')
					->where('status_id',9)
					->take(500)
                    ->paginate(500);
                    $r_count= $reports->count();
		}
		elseif($request->search_pri == 'dr')
		{
			$reports = Report::orderBy('id', 'DESC')
                    ->whereIn('api_id',[16,17])
                   // ->where('amount','>=',100)
                    ->where('status_id',4)
                    ->where('refund',0)
                    ->paginate(500);

                    $r_count= $reports->count();
					
		}
        elseif($request->search_pri =='d_tid')
        {
            
            $reports = Report::orderBy('id', 'DESC')
                    ->whereIn('api_id',[3,4,5,6])
                    ->where('status_id',4)
                    //->where('amount','>=',100)
                    ->groupBy('txnid')->havingRaw('count(*) > 1')
                    ->take(500)
                    ->paginate(500);
                    $r_count= $reports->count();
        }
		else{
			$reports = Report::orderBy('id', 'DESC')
                    ->whereIn('api_id',[3,4,5])
                    ->where('status_id',4)
                    ->where('refund',0)
                    ->take(5000)
                    ->paginate(5000);

                    $r_count= $reports->count();
		
		}
		return view('admin.report.pend_refd_intd', compact('reports','r_count'))->with('sessionid', $SessionID);
	}
	if(Auth::user()->role_id == 5)
	{
			$SessionID = Company::find(3)->sessionid;
		 	if ($request->search_pri == 'p') {
                $reports = Report::where('provider_id', 41)
                    ->orderBy('id', 'DESC')
					->where('user_id', Auth::id())
					->where('status_id',3)
					->take(500)
                    ->paginate(500);
                
            }
		elseif($request->search_pri == 'r')
		{
		$reports = Report::where('provider_id', 41)
                    ->orderBy('id', 'DESC')
					->where('user_id', Auth::id())
					->whereIn('status_id',[2,20])
					->where('refund',1)
					->take(500)
                    ->paginate(500);
			
		}

		elseif($request->search_pri == 'i')
		{
		$reports = Report::where('provider_id', 41)
                    ->orderBy('id', 'DESC')
					->where('user_id', Auth::id())
					->where('status_id',9)
					->take(500)
                    ->paginate(500);
			
		}
		elseif($request->search_pri == 'b')
		{
			$reports = Report::where('provider_id', 41)
                    ->orderBy('id', 'DESC')
					->where('user_id', Auth::id())
					->where('txnid',null)
                    ->where('created_at','>=',Carbon::now()->subMonth())
					->whereIn('status_id',[3,8,9])
					->take(500)
                    ->paginate(500);
		}
		elseif($request->search_pri == 'dr')
		{
			$reports = Report::orderBy('id', 'DESC')
                    ->whereIn('api_id',[16,17])
                    ->where('status_id',4)
                    ->where('user_id', Auth::id())
                    ->groupBy('txnid')->havingRaw('count(*) > 1')
                    ->take(500)
                    ->paginate(500);
                    $r_count= $reports->count();
		}
		else{
			$reports = Report::where('provider_id', 41)
                    ->orderBy('id', 'DESC')
					->where('user_id', Auth::id())
					->where('status_id',9)
					->take(500)
                    ->paginate(40);
		
		}
		
		
		return view('report.pend_refd_intd', compact('reports'))->with('sessionid', $SessionID);
		
	}
	
}
        public function export_pri(Request $request)
            {
            if (Auth::user()->role_id == 1) {

            $SessionID = Company::find(3)->sessionid;

            if ($request->export == 'export') {
                set_time_limit(60000);
                ini_set("memory_limit", "256M");
                header("Content-Type: application/vnd.ms-excel");
                header("Cache-control: private");
                header("Pragma: public");
                Excel::create('records', function ($excel) use ($request) {
                    $excel->sheet('Sheet1', function ($sheet) use ($request) {
                    $report = Report::orderBy('id', 'DESC')
                     ->whereDate('created_at', '>=', $request->fromdate)
                     ->whereDate('created_at', '<=', $request->todate)
                    ->whereIn('api_id',[3,4,5,14])
                    ->where('refund',1)
                    ->where('status_id',2)
                    ->where('amount','>=',100)
                    ->get();
                    $arr = array();
                        foreach ($report as $reports) {
                            $data = array($reports->created_at, $reports->txnid, $reports->api->api_name, $reports->beneficiary->customer_number, $reports->beneficiary->name, $reports->number, $reports->bank_ref, $reports->amount, $reports->channel, $reports->status->status,$reports->refund);
                            array_push($arr, $data);
                        }
                        //set the titles
                        $sheet->fromArray($arr, null, 'A1', false, false)->prependRow(array(
                            'Date', 'tx id', 'Product', 'Mobile', 'Name', 'Account Number',
                             'UTR Number', 'Amount', 'Channel', 'Status','refund'
                                )
                        );
                    });
                    
                })->export('csv');
            }
                
            }
}


		

	/* Below function added by Rajat*/
	public function refundReports(Request $request)
	{
		if(Auth::user()->role_id ==1)
		{
			$SessionID = Company::find(3)->sessionid;
			if($request->all())
			{
				$refunded_reports=array();
				if($request->export == 'export')
				{
					$getdateFromat=$this->getDateFromat($request);
					$start_date= $getdateFromat['start_date'];
					$end_date = $getdateFromat['end_date'];
					$refunded_reports = Report::orderBy('id', 'DESC')
								->select('created_at','id','txnid','api_id','customer_number','beneficiary_id','number','bank_ref','amount','channel','status_id')
								->where('status_id',4)
								->whereBetween('created_at', [$start_date,$end_date])
								->orderBy('created_at','desc')
								->get();
								$arr=array();
					// echo "<pre>";
					// print_r($refunded_reports);die;
					foreach($refunded_reports as $report)
					{
						 $data = array(
									$report->created_at, 
									$report->id,
									$report->txnid,
									$report->api->api_name,
									$report->customer_number,
									$report->beneficiary->name,
									$report->number,
									$report->beneficiary->bank_name,
									$report->beneficiary->ifsc,
									$report->bank_ref,
									$report->ackno,
									$report->amount,
									($report->channel == 1)? "NEFT" :"IMPS",
									$report->status->status,
									);

                        array_push($arr, $data);
					}
					
					Excel::create('Refunded Reports', function ($excel) use ($arr,$start_date,$end_date) {
					$excel->setTitle('Refunded Reports');
					$excel->setCreator('Levinm Fintech')->setCompany('Levinm Fintech Pvt. Ltd.');
					$excel->setDescription("Refunded Reports from $start_date to $end_date");
					
					$excel->sheet('Sheet1', function ($sheet) use ($arr,$start_date,$end_date) {
					$sheet->fromArray($arr, null, 'A1', false, false)->prependRow(array(
								'Order Date', 'ID', 'Txn ID', 'PRODUCT','Mobile Number','Name','Account Number','Bank Name','IFSC','UTR Number','ACK No','Amount','Mode','Status')
							);
					$sheet->prependRow( array('','To Date' ,"$end_date"));
					$sheet->prependRow( array('','From Date' ,"$start_date"));
					$sheet->prependRow( array('','','Refunded Reports'));
						});
					})->export('xls');
				}
				else if($request->search == 'search')
				{
					$getdateFromat=$this->getDateFromat($request);
					$start_date= $getdateFromat['start_date'];
					$end_date = $getdateFromat['end_date'];
					$refunded_reports = Report::orderBy('id', 'DESC')
								->select('created_at','id','txnid','api_id','customer_number','beneficiary_id','number','bank_ref','ackno','amount','channel','status_id')
								->where('status_id',4)
								->whereBetween('created_at', [$start_date,$end_date])
								->orderBy('created_at','desc')
								
								//->take(500)
								->paginate(100);
					return view('admin.report.refunded_reports', compact('refunded_reports'))->with('sessionid', $SessionID);
					
				}
				
			}
			
				$refunded_reports = Report::orderBy('id', 'DESC')
					->where('status_id',4)
					->take(500)
					->paginate(40);
			
			return view('admin.report.refunded_reports', compact('refunded_reports'))->with('sessionid', $SessionID);
				
			
			
		}
		else
			echo "Permission Denied";
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
	public function recharge_pri(Request $request)
	{
		if (in_array(Auth::user()->role_id,array(1,11,12,14)))
		{
			$query = Report::where('api_id', 1);
			$search_type = isset($request->search_pri) ? $request->search_pri:'';
			$export_type='';
			if($search_type == 'p')
			{
				$export_type="Pending Records";
				$query->where('status_id',3);
				
			}
			elseif($search_type == 'r')//refund pending
			{
				$export_type="Refund Pending Records";
				$query->whereIn('status_id',[2,20]);
				$query->where(['refund'=>1]);
				
			}
			elseif($search_type == 'i')// initiated
			{
				$export_type="Initiated Records";
				$query->where(['status_id'=>9]);
				
			}
			elseif($search_type == 'dr')//Refunded
			{
				$export_type="Refunded";
				$query->where(['status_id'=>4,'refund'=>0]);
				
				
			}
			 elseif($search_type == 'b')//blank TID 
			{
				$export_type="Blank TID";
				$query->where(['txnid'=>null]);
				$query->where(['status_id'=>3]);
				
				
			}
			elseif($search_type =='d_tid')
			{
				$export_type="Double Refunded";
				$query->where('status_id',4);
				$query->groupBy('txnid');
				$query->havingRaw('count(*) > 1');
			}
			else
			{
				$export_type="Pending Records";
				$query->where(['status_id'=>3]);
				
			}
			if($request->export == "export")
			{
				
				$reports = $query->get();
				Excel::create($export_type, function ($excel) use ($reports,$export_type) 
					{
						$excel->sheet('Sheet1', function ($sheet) use ($reports,$export_type) 
						{
							$arr = array();
							if($reports=='')
								array_push($arr, array('','','',"No Record Found"));
							foreach ($reports as $report) 
							{
								
								$data = array(
											$report->id,
											$report->pay_id,
											$report->user->name.'('.$report->user_id.')',
											date("d-m-Y", strtotime($report->date_txn)),
											$report->api->api_name,
											$report->provider->provider_name,
											$report->number,
											@$report->txnid,
											number_format($report->amount,2),
											number_format($report->profit,2),
											number_format($report->total_balance2,2),
											$report->status->status,
										);
							array_push($arr, $data);
							}
							
							
							 $sheet->fromArray($arr, null, 'A1', false, false)->prependRow(array(
								'Id','Pay ID','User','Date Time','Product','Provider','Number','Ref Id','Amount','S Charge','Total','Status') );
								
								$sheet->row(1, function($row) {
								// call cell manipulation methods
								$row->setBackground('#A9A9A9');
								$row->setFontFamily('Times New Roman');
						});
						});
					})->export('xls');
				
			}
			$query->orderBy('created_at','desc');
			$query->where('recharge_type',1);
			$reports = $query->paginate(40);
			
			return view('admin.report.recharge_pri', compact('reports','export_type'));
		}
	}

}