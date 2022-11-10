<?php
namespace App\Exports;
use App\Report;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\FromQuery;
use Auth;
class DTReportExport implements WithHeadings,WithMapping,FromQuery
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
	 return Report::query()->orderBy('id','DESC')->whereIn('status_id',[6,7])->where('user_id', Auth::id())->whereBetween('created_at',[$this->start_date,$this->end_date]); 
  }
  public function headings() : array
    {
        return [
            'Date & Time','Order ID','Wallet','User','Transfer To/From','Firm Name','Ref Id','Description','Opening Bal','Credit Amount','Closing Bal','Bank Charge','Remark','Status'
        ];
    }
	public function map($value): array
    {
        return [
            $value->created_at,
            $value->id,
            ($value->recharge_type == 1) ? 'Recharge' : 'Money',
            @$value->user->name .'('. @$value->user->prefix . ' - ' . $value->user_id .')',
            @$value->creditBy->name.'('.@$value->creditBy->prefix.'-'.@$value->creditBy->id.')'.''.@$value->credit_by,
    		@$value->creditBy->member->company,	
            $value->txnid,
            $value->description,
           	$value->opening_balance,
            $value->amount,
            $value->total_balance,
            $value->bank_charge,
            $value->remark,
            @$value->status->status	
        ];
    }
}