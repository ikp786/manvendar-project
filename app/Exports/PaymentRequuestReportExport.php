<?php
namespace App\Exports;
use App\Loadcash;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\FromQuery;
use Auth;
use DateTime;
class PaymentRequuestReportExport implements WithHeadings,WithMapping,FromQuery
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
		 return Loadcash::query()->where(['user_id'=>Auth::id()])->whereBetween('created_at',[$this->start_date,$this->end_date])->orderby('id','DESC');
	}
  	public function headings():array
	{
	   return ['Date','Time','ID','Request To','Bank Name','Mode','Branch Code','Deposit Date','Amount','Customer Remark','Ref Id','Status','Remark','Updated remark',
	    ];
	}
	public function map($loadcash):array
    {
		$s = $loadcash->created_at;
		$dt = new DateTime($s);
        return [
        	$dt->format('d/m/Y'),
			$dt->format('H:i:s'),
        	$loadcash->id,
        	($loadcash->request_to == 1) ? (Auth::user()->parent->name .'( '.Auth::user()->parent->prefix . ' - ' .Auth::user()->parent->id .')') : Auth::user()->company->company_name,
        	($loadcash->request_to == 1) ? @$loadcash->bank_name :(@$loadcash->netbank->bank_name .':'. @$loadcash->netbank->bank_name),

        	 $loadcash->payment_mode,
             $loadcash->loc_batch_code,
             $loadcash->deposit_date,
             $loadcash->amount,
             $loadcash->request_remark,
             $loadcash->bankref,
             @$loadcash->status->status,
             @$loadcash->remark->remark,
             @$loadcash->report->remark
           
        ];
    }
}