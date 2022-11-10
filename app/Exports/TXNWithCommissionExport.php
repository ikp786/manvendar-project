<?php

namespace App\Exports;
use App\Report;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\FromQuery;
use App\Traits\CustomTraits;
use Auth;
use DateTime;
class TXNWithCommissionExport implements WithHeadings,WithMapping,FromQuery

{
	use CustomTraits;
	public  $condition;
	public  $start_date;
	public  $end_date;
	public function __construct($start_date,$end_date)
	{
		$this->start_date = $start_date;
		$this->end_date = $end_date;

	}
public function query()
{
	if(Auth::user()->role_id==1)
		return Report::query()->whereBetween('created_at',[$this->start_date,$this->end_date])->orderBy('id','desc')->whereIn('status_id',[1,2,3,21,4,24])->orderBy('id','desc');
	else
		return Report::query()->whereIn('user_id',$this->condition)->whereBetween('created_at',[$this->start_date,$this->end_date])->where('recharge_type',0)->orderBy('id','desc');
}
public function headings() : array

    {

        return [
			'Date','Time',
			'Id',
			'Txn Id',
			'user',
			'Consumer No',
			'Acc/mobile/k number',
			'Credit/Debit',
			'Opening Bal',
			'Amount',
			'Credit',
			'Debit',
			'TDS',
			'Service Tax',
			'Balance',
			'Md Comm',
			'Dist Comm',
			'Admin Comm',
			'Description',
			'Txn Type',
			'Status',
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
			$value->txnid,
			$value->user->name .'('. $value->user->prefix .' - '.$value->user->id .')',
			$value->customer_number,
			@$value->number, 
			@$value->type, 
			@$value->opening_balance, 
			@$value->amount, 
			@$value->credit_charge, 
			@$value->debit_charge, 
			@$value->tds, 
			@$value->gst, 
			@$value->total_balance, 
			@$value->md_commission, 
			@$value->dist_commission, 
			@$value->admin_commission, 
			($value->recharge_type== 1) ? @$value->provider->provider_name  : @$value->api->api_name,
			@$value->txn_type,
			@$value->status->status		
        ];
    }
}