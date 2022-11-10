<?php
namespace App\Exports;
use App\Report;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\FromQuery;
use Auth;
use DateTime;
class AepsSettlementExport implements WithHeadings,WithMapping,FromQuery
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
	  	return Report::query()->select('id','user_id','created_at','api_id','amount','profit','beneficiary_id','recharge_type','status_id','description','total_balance','total_balance2','provider_id','number','customer_number','bank_ref','ackno','txnid','refund','credit_by','channel','rechargeprovider_id','credit_charge','tds','gst','debit_charge','aeps_sattelment_id','txn_type','api_id')->where($this->condition)->whereBetween('created_at',[$this->start_date,$this->end_date])->orderBy('id','desc');
  	}

  	public function headings() : array
    {
        return [
          'Date', 'Time','Id', 'User Name','Provider','Bank Name','IFSC','Acc/Number','Txn Id','Bank Ref','Description ','Amount','Credit Amt','Debit Amt','GST','TDS','Balance','Status'
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
            $value->user->name .'('. $value->user->prefix . ' - ' . $value->user_id .')',
			@$value->api->api_name,
            @$value->aepssettlements->bank_name,
            @$value->aepssettlements->ifsc,
			@$value->aepssettlements->account_number,
			$value->txnid,
			$value->bank_ref,
			$value->description,
			@$value->amount,
			@$value->credit_charge,
			@$value->debit_charge,
			@$value->gst,
			@$value->tds,
			@$value->total_balance,
			@$value->txn_type,	
        ];
    }
}