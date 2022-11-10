<?php
namespace App\Exports;
use App\Report;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\FromQuery;
use DateTime;
class SummaryReportExport implements WithHeadings,WithMapping,FromQuery
{
	public  $condition;
	public  $start_date;
	public  $end_date;
	public function __construct($condition,$start_date,$end_date)
    {
		$this->condition = $condition;
		$this->start_date = $start_date;
		$this->end_date = $end_date;
    }
  public function query()
  {
	 return Report::query()->where($this->condition)->whereBetween('created_at',[$this->start_date,$this->end_date])->orderBy('id','desc'); 
  }
  public function headings() : array
    {
        return [
            'Date','Time','User Name','Id','Consumer No','Bene Name','Bene Account','Ifsc','Bank Name','Remitter Number','Amount','Type','Per Name','Txn Type','Operator','Op Id','Status','Created By'
        ];
    }
	public function map($value): array
    {
		$s = $value->created_at;
		$dt = new DateTime($s);
        return [
            $dt->format('d/m/Y'),
			$dt->format('H:i:s'),
			@$value->user->name .'('. @$value->user->prefix . ' - ' . $value->user_id .')',
            $value->id,
            $value->number,
            @$value->beneficiary->name,
            ($value->recharge_type== 0) ? $value->number : '',
			@$value->beneficiary->ifsc,
			@$value->beneficiary->bank_name,
			@$value->customer_number,
			$value->amount,
			$value->type,
			$value->client_id,
			$value->txn_type,
			($value->recharge_type== 1) ? @$value->provider->provider_name : @$value->api->api_name,
			$value->txnid,
			@$value->status->status,
			@$value->createdTxnBy->name
            	
        ];
    }
}