<?php
namespace App\Exports;
use App\Report;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\FromQuery;
use Auth;
use DateTime;
class AccountReportExport implements WithHeadings,WithMapping,FromQuery 
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
            'Date','Time',			'ID',			'Product',			'Bank Name',			'Name',			'Acc/K No/Mobile Number',			'Txn ID',			'Description',			'Opening Balance',			'Amount',			'Credit',			'Debit',			'Balance',			'Remark',			'Status'
        ];
    }
	public function map($value): array
    {
		$s = $value->created_at;
		$dt = new DateTime($s);
        return [
            $dt->format('d/m/Y'),
        	$dt->format('H:i:s'),
            $value->id,
            $value->api->api_name,
			($value->recharge_type ==1) ? $value->provider->provider_name : @$value->beneficiary->bank_name,			
			$value->credit_by ? $value->creditBy->name .'('. @$value->creditBy->role_id == 4 ? "D - " : "R - " .$value->credit_by.')' : @$value->credit_by,
            $value->number,
			@$value->txnid,
            @$value->description,
            @$value->opening_balance,            @$value->amount,
            @$value->credit_charge,
            @$value->debit_charge,
			@$value->total_balance,
			@$value->remark,
			@$value->status->status
            	
        ];
    }
}