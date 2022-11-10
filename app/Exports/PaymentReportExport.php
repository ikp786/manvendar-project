<?php
namespace App\Exports;use App\Report;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\FromQuery;
use Auth;
use DB;
use DateTime;
class PaymentReportExport implements WithHeadings,WithMapping,FromQuery
{
	public $role_id;
	public $start_date;
	public $end_date;
	public $user_id;
	public function __construct($role_id,$start_date,$end_date,$user_id)
    {
		$this->role_id = $role_id;
		$this->start_date = $start_date;
		$this->end_date = $end_date;
		$this->user_id = $user_id;
    }
  	public function query()
  	{ 
		//return User::query()->orderBy('id','desc'); 
		if($this->user_id!=''){
			return Report::query()->join('users',function ($join)
				{
					$join->on('users.id', '=', 'reports.credit_by');
				})->select('reports.id','reports.recharge_type','reports.user_id','reports.credit_by','reports.txnid','reports.amount','reports.bank_charge','reports.remark','reports.description','reports.opening_balance','reports.total_balance','reports.status_id','reports.created_at','reports.payment_id')->where('reports.credit_by',$this->user_id )->whereIn('reports.status_id',[6,7])->orderBy('reports.id','desc')->where('reports.user_id',Auth::id())->where('users.role_id',$this->role_id)->whereBetween('reports.created_at',[$this->start_date,$this->end_date]);
		}
	else{
			return Report::query()->join('users',function ($join)
			{
				$join->on('users.id', '=', 'reports.credit_by');
			})->select('reports.id','reports.recharge_type','reports.user_id','reports.credit_by','reports.txnid','reports.amount','reports.bank_charge','reports.remark','reports.description','reports.opening_balance','reports.total_balance','reports.status_id','reports.created_at','reports.payment_id')->whereIn('reports.status_id',[6,7])->orderBy('reports.id','desc')->where('reports.user_id',Auth::id())->where('users.role_id',$this->role_id)->whereBetween('reports.created_at',[$this->start_date,$this->end_date]);
		}
  	}
  	public function headings():array
	{
	   return [
	        'Date','Time','Request Date/Time','Updated Date/Time','Order id','Wallet','User','TransferTo/From','Firm Name','Ref Id','Description','Bank Ref','Agent Remark','Opening Balance','Credit Amount','Closing Bal','Bank Charge','Remark','Status'
	    ];
	}
	public function map($report):array
    {
		$s = $report->created_at;
		$dt = new DateTime($s);
        return [
			$dt->format('d/m/Y'),
			$dt->format('H:i:s'),
			(@$report->payment_id =='')? ' ':@$report->payment->created_at,
			(@$report->payment_id =='')? ' ':@$report->payment->updated_at,
			($report->wallet) ? "Recharge" : 'Money',				
			@$report->id,
			@$report->user->name .' '.@$report->user->prefix.' '.@$report->user->id, 
			(is_numeric($report->credit_by)) ? @$report->creditBy->name : $report->credit_by,
			(is_numeric($report->credit_by)) ? $report->creditBy->member->company : '',
			$report->txnid,
			@$report->description,
			@$report->payment->bankref,
			@$report->payment->request_remark,
			$report->opening_balance,
			$report->amount,
			$report->total_balance,
			$report->bank_charge,
			$report->remark,
			$report->status->status     
        ];
    }
}