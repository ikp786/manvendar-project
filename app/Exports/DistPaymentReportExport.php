<?php
namespace App\Exports;
use App\Report;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\FromQuery;
use Auth;
use DB;
class DistPaymentReportExport implements WithHeadings,WithMapping,FromQuery
{
	public $start_date;
	public $end_date;
	public function __construct($start_date,$end_date)
    {
		$this->start_date = $start_date;
		$this->end_date = $end_date;
    }
  	public function query()
  	{
  		if(in_array(Auth::user()->role_id,array(1,11,14)))
			$logined_user_id = User::select('id')->where('role_id',1)->first()->id;
		else
			$logined_user_id=Auth::id();
		return Report::query()->where('user_id',$logined_user_id)->where('provider_id',0)->where('status_id',6)->whereBetween('created_at',[$this->start_date,$this->end_date])->orderBy('id','DESC');
  	}
  	public function headings():array
	{
	   return [
	        'Date/Time','Order id','Wallet','User','TransferTo/From','Firm Name','Ref Id','Description','Opening Balance','Credit Amount','Closing Bal','Bank Charge','Remark','Status'
	    ];
	}
	public function map($report):array
    {
		//print_r($report);die;
        return [
                @$report->created_at,
				($report->wallet) ? "Recharge" : 'Money',				
				@$report->id,
				@$report->user->name .' '.@$report->user->prefix.' '.@$report->user->id, 
				(is_numeric($report->credit_by)) ? @$report->creditBy->name : $report->credit_by,
				(is_numeric($report->credit_by)) ? $report->creditBy->member->company : '',
				$report->txnid,
				@$report->description,
				number_format($report->opening_balance,2),
				number_format($report->amount,2),
				number_format($report->total_balance,2),
				$report->bank_charge,
				$report->remark,
				$report->status->status     
        ];
    }
}