<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Report;
use App\Api;
use App\CategoryType;
use App\SubCategory;
use Carbon\Carbon;
use App\Provider;
use Auth;
use Excel;
use DB;
use App\Exports\UsagesReportExport;
use App\Exports\SummaryReportExport;
use App\Traits\CustomTraits;
class SummaryReportController extends Controller
{
    use CustomTraits;
	public function summary_report(Request $request)
    {
		$userDetails = $this->getUserDetails();
		if(in_array(Auth::user()->role_id,array(5,15)))
		{
			$userId = $userDetails->id;
			$start_date= ($request->fromdate) ? $request->fromdate . " 00:00:00" : date("Y-m-d"). " 00:00:00";
			$end_date= ($request->todate) ? $request->todate . " 23:59:59" : date("Y-m-d H:i:s");
			$start_date = date("Y-m-d H:i:s", strtotime($start_date));
			$end_date = date("Y-m-d H:i:s", strtotime($end_date));
			DB::enableQueryLog();
			$repotQuery=Report::orderBy('created_at','desc');
			//$repotsSummaryQuery=Report::selectRaw('sum(amount) as txn_amount,count(id) as txn_count,status_id');
			$repotQuery->where('user_id', $userId);
			//$repotsSummaryQuery->where('user_id', $userId);
			$condition=array('user_id'=>$userId);
			
			$repotQuery->whereIn('status_id',[1,2,3,4,20,21,24]);
			//$repotsSummaryQuery->whereIn('status_id',[1,2,3,4,20,21,24]);
			$repotQuery->whereBetween('created_at', [$start_date,$end_date]);
			//$repotsSummaryQuery->whereBetween('created_at', [$start_date,$end_date]);
			if($request->searchOf !='')
				$repotQuery->where('status_id',$request->searchOf);
			if($request->number !='')
			{

				$number = $request->number;
				$repotQuery->where(function ($query) use($number) {
               		// $query->where('user_id', '=', Auth::id())
                     $query->where('id', '=', $number)
                      ->orWhere('number', '=', $number)
                      ->orWhere('customer_number', '=', $number)
                      ->orWhere('txnid', '=', $number);
            	});
			} 
			if($request->export=="SUMMARY_REPORT")
			{
				
				return Excel::download(new UsagesReportExport($condition,$start_date,$end_date), 'Usage Report.xlsx');
			}
			$reportDetails = $repotQuery->simplePaginate(50);
			//print_r(DB::getQueryLog());die;
			/*$repotsSummaryQuery->groupBy('status_id');
			$repots = $repotsSummaryQuery->get();
			$data=array();
			 $txn_count=$txn_amount=0;
			 foreach($repots as $report)
			 {
					$data[strtoupper($report->status->status)] = $report->txn_amount .' * '.$report->txn_count;

					$txn_count += $report->txn_count;
					$txn_amount += $report->txn_amount;

			 }
			 $data['TOTAL']= $txn_amount .' * '. $txn_count;
			$data=json_decode(json_encode($data));
			if ($request->search == 'search') {
				return "Not Available";
				$report = Report::orderBy('id', 'DESC');
				if($request->SEARCH_TYPE=="ID")
					$report->where('id', $request->search_number);
				elseif($request->SEARCH_TYPE=="TXN_ID")
					$report->where('txnid',$request->search_number);
				elseif($request->SEARCH_TYPE=="ACC")
					$report->where('number',$request->search_number);
				elseif($request->SEARCH_TYPE=="MOB_NO")
					$report->orWhere('customer_number',$request->search_number);
				$report->where('user_id', $userId);
				$report->where('status_id','!=',14);
				$report = $report->paginate(50);
			}*/
				$subCategories = SubCategory::pluck('name','id')->toArray();
				$categoryTypes = CategoryType::where('status_id',1)->pluck('name','id')->toArray();
				$providers = Provider::whereIn('subcategory_id',SubCategory::pluck('id','id')->toArray())->pluck('provider_name','id')->toArray();
				return view('reports.summary_report',compact('reportDetails','categoryTypes','subCategories','providers'));
		}
		return view('errors.page-not-found');
      
    }
	public function getSubCategory(Request $request)
	{
		
			$subCategories = SubCategory::where(['category_id'=>$request->category,'status_id'=>1])->pluck('name','id')->toArray();
		
		if($subCategories)
			return response()->json(['status'=>1,'subcategories'=>$subCategories]);
		return response()->json(['status'=>0,'subcategories'=>"No SubCategory Available"]);
	}
    public function getOperator(Request $request)
    {
        $providers = Provider::where('subcategory_id',$request->subcategory_id)->pluck('provider_name','id')->toArray();
        if($providers)
            return response()->json(['status'=>1,'providers'=>$providers]);
        return response()->json(['status'=>0,'providers'=>"No SubCategory Available"]);
    }
	public function exportSummaryReport($reports)
	{
		Excel::create('Summary Report-'.date("d-m-Y"), function ($excel) use ($reports) {
                $excel->sheet('Sheet1', function ($sheet) use ($reports) {
                    $arr = array();
                    foreach ($reports as $report) 
					{
						$data = array(
							$report->created_at,
							$report->user->name .'('. $report->user->prefix .' '. $report->user->id .')', 
							@$report->api->api_name, 
							$report->biller_name,
							@$report->provider->provider_name,
							@$report->beneficiary->name,
							@$report->beneficiary->bank_name,
							$report->number,
							$report->customer_number,
							$report->txnid,
							$report->remark,
							$report->amount,
							$report->credit_charge,
							abs($report->debit_charge),
							$report->total_balance,
							@$report->status->status,
							);
                        array_push($arr, $data);
                    }

                    //set the titles
                    $sheet->fromArray($arr, null, 'A1', false, false)->prependRow(array(
                        'Date', 'User Name','Product','Biller Name','Provider Name','Bene Name','Bank Name','Acc/Mobile Number','Customer Number', 'Txn ID', 'Remark','Amount','Credit','Debit','Remaining Bal', 'Status')
                    );
								$sheet->row(1, function($row) {
								// call cell manipulation methods
								$row->setBackground('#A9A9A9');
								$row->setFontFamily('Times New Roman');
						});
					
                });
            })->export('csv');
	}
}
