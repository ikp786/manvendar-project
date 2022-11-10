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
class DmtExport implements WithHeadings,WithMapping,FromQuery
{
	use CustomTraits;
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
		if(Auth::user()->role_id==1)
			return Report::query()->whereBetween('created_at',[$this->start_date,$this->end_date])->where('recharge_type',0)->orderBy('id','desc');
		else
			return Report::query()->whereIn('user_id',$this->condition)->whereBetween('created_at',[$this->start_date,$this->end_date])->where('recharge_type',0)->orderBy('id','desc');
	}
	public function headings() : array
    {
        return [
			'Date' ,'Time',
			'Id',
			'user',
			'Sender No',
			'Bene Name',
			'Bene Account',
			'Ifsc',
			'Bank Name',
			'Amount',
			'Type',
			'Txn Type',
			'Route',
			'Txn ID',
			'Bank Ref',
			'Mode',
			'Status'
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
			$value->user->name .'('. $value->user->prefix .' - '.$value->user->id .')',
			($value->recharge_type==0) ? $value->customer_number : $value->number,
			@$value->beneficiary->name , 
			@$value->number, 
			@$value->beneficiary->ifsc, 
			@$value->beneficiary->bank_name, 
			@$value->amount,
			@$value->type,
			@$value->txn_type,
			($value->recharge_type== 1) ? @$value->provider->provider_name  : @$value->api->api_name,
			@$value->txnid,
			@$value->bank_ref,
			($value->channel==2) ? "IMPS":(($value->channel==1) ? "NEFT" : ""),
			@$value->status->status		
        ];
    }
}