<?php
namespace App\Exports;
use App\Report;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\FromQuery;
use Auth;
class TDSReportExport implements WithHeadings,WithMapping,FromQuery
{
	public  $start_date;
	public  $end_date;
	public  $user_id;
	public function __construct($user_id,$start_date,$end_date)
    {
		$this->user_id=$user_id;
		$this->start_date = $start_date;
		$this->end_date = $end_date;
    }
	public function query()
	{
		if($this->user_id!='')
		{
	  		return Report::query()->selectRaw('user_id, sum(tds) as tds, sum(credit_charge) as commission, sum(debit_charge) as service_charge,sum(amount) as txn_value')->groupBy('user_id')->where('user_id',$this->user_id )->whereBetween('created_at',[$this->start_date,$this->end_date])->whereIn('status_id',[1,22,16]);
	  	}
	  	else
		{
	  		return Report::query()->selectRaw('user_id, sum(tds) as tds,sum(credit_charge) as commission, sum(debit_charge) as service_charge,sum(amount) as txn_value')->groupBy('user_id')->whereBetween('created_at',[$this->start_date,$this->end_date])->whereIn('status_id',[1,22,16])->orderBy('user_id','desc'); 
			//return Report::query()->selectRaw('user_id, tds,credit_charge as commission,debit_charge as service_charge,amount as txn_value,status_id')->whereBetween('created_at',[$this->start_date,$this->end_date])->whereIn('status_id',[1,22,16]);
	 	}
	}
	public function headings() : array
    {
        return [
           'User','Firm Name','Pan Card','Mobile','Member Type','Volume','Service Charge','Commission','TDS'
        ];
    }
	public function map($value): array
    {
	  return[
		
			 @$value->user->name .'('. @$value->user->prefix .'-'.@$value->user->id .')',
			 @$value->user->member->company,
			 @$value->user->member->pan_number,
			 @$value->user->mobile,
             @$value->user->role->role_title,
	         $value->txn_value,
			 $value->service_charge,
	         $value->commission,
	         $value->tds,
	        
            ];
    }
}