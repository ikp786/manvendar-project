<?php
namespace App\Exports;
use App\Report;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\FromQuery;
use Auth;
use DateTime;
class OfflineRecordExport implements WithHeadings,WithMapping,FromQuery
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
		return Report::query()->where('status_id',24)->whereBetween('created_at',[$this->start_date,$this->end_date])->orderBy('id','asc'); 
	}
    public function headings():array
	{
	   return [
	        'Date','Time','ID','Txn ID','User','Due Date','K Number','Amount','Credit/Debit','TDS','Service Tax','Balance','Description','Txn Type','Status'
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
                $value->txnid,
                @$value->user->name.'('.$value->user->prefix .'-'.$value->user->id .')',
				@$value->bill_due_date,
                $value->number,
                $value->amount,
                $value->type,
                $value->tds,
                $value->gst,
                $value->total_balance,
               ($value->recharge_type== 1) ? @$value->provider->provider_name:@$value->api->api_name,
               $value->txn_type,
               $value->status->status,
        	];
    }
}