<?php
namespace App\Exports;
use App\Loadcash;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\FromQuery;
use Auth;
use DateTime;
class FundRequestExport implements WithHeadings,WithMapping,FromQuery
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
		 return Loadcash::query()->where('user_id',Auth::id())->whereBetween('created_at',[$this->start_date,$this->end_date])->orderby('id','DESC');
	}
  	public function headings():array
	{
	   return ['Date','Time','ID','User','Deposite Date','Bank Name','Wallet Amount','Request For','Bank Ref','Payment Mode','Branch Name','Request remark','Approval remark','Updated remark','Status'
	    ];
	}
	public function map($value):array
    {
		$s = $value->created_at;
		$dt = new DateTime($s);
        return [
		
        	$dt->format('d/m/Y'),
        	$dt->format('H:i:s'),
        	$value->id,
        	$value->user->name,
        	$value->deposit_date,
        	($value->request_to ==2)?@$value->netbank->bank_name:@$value->netbank->account_number,
        	$value->amount,
        	($value->request_to == 3 && $value->borrow_type == 1)? "Take Borrow" :(($value->request_to == 3 && $value->borrow_type == 2)? "Pay Off":''),
        	$value->bankref,
        	$value->payment_mode,
        	$value->loc_batch_code,
        	$value->request_remark,
        	@$value->remark->remark,
        	@$value->report->remark,
        	@$value->status->status           
        ];
    }
}