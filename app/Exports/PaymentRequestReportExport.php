<?php
namespace App\Exports;
use App\User;
use App\Loadcash;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\FromQuery;
use Auth;
use DateTime;
class PaymentRequestReportExport implements WithHeadings,WithMapping,FromQuery
{
	public  $start_date;
	public  $end_date;
	public function __construct($start_date,$end_date)
    {
		$this->start_date = $start_date;
		$this->end_date = $end_date;
    }
	public function query()
	{
		if(in_array(Auth::user()->role_id,array(1,11,12,14)))			$logined_user_id = User::select('id')->where('role_id',1)->first()->id;		else			$logined_user_id=Auth::id();	        $members = User::where('parent_id', $logined_user_id)->pluck('id','id')->toArray(); 		 $loadcashesQuery = Loadcash::orderBy('id', 'DESC');		if(Auth::user()->role_id == 1)		{			 $loadcashesQuery->where(function ($query) use ($members) {                $query->whereIn('user_id',$members)					->orWhere('request_to', '=', 2);                                  });		}		else{			 $loadcashesQuery->whereIn('user_id',$members);		}	    return $loadcashesQuery->whereBetween('created_at',[$this->start_date,$this->end_date]);
	}
  	public function headings():array
	{
	   return [
	        'Date','Time','Order id','Wallet','User','Request To','Payment Mode','Payment Type','Bank Name','Ref Id','Location/Branch Code','Amount','Remark','Request Remark','Updated Remark','Status'
	    ];
	}
	public function map($report):array
    {
		$s = $report->created_at;
		$dt = new DateTime($s);
        return [
            $dt->format('d/m/Y'),
			$dt->format('H:i:s'),
			@$report->id,
			(@$report->wallet) ? "Recharge" : "Money",				
			@$report->user->prefix .' '.@$report->user->name.' '.@$report->user->id, 
			//@$report->request_to . ' '."Company".' '.Auth::user()->parent->role->role_title,
			(@$report->request_to==2) ? "Company" : "Auth::user()->parent->role->role_title",
			@$report->payment_mode,
			@$report->pmethod->payment_type,
			@$report->netbank->bank_name,
			@$report->bankref,
			@$report->loc_batch_code,
			@$report->amount,
			@$report->remark->remark,
			@$report->request_remark,
			($report->status_id==1) ? @$report->report->remark :'',
			@$report->status->status, 
        ];
    }
}