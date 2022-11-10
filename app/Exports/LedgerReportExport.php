<?php

namespace App\Exports;

use App\Report;

use Maatwebsite\Excel\Concerns\FromCollection;

use Maatwebsite\Excel\Concerns\WithHeadings;

use Maatwebsite\Excel\Concerns\WithMapping;

use Maatwebsite\Excel\Concerns\FromQuery;
use DateTime;
class LedgerReportExport implements WithHeadings,WithMapping,FromQuery

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

            'Date','Time','ID','Txn ID','Amount','Credit/Debit','TDS','Service Tax','Balance','Description','Txn Type','Status','Created By',''

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
			number_format($value->amount,2),

			$value->type,

			number_format($value->tds,3), 

			number_format($value->gst,3),

			number_format($value->total_balance,3),

			($value->recharge_type== 1) ? $value->provider->provider_name : $value->api->api_name,

			$value->txn_type,

			$value->status->status,

			@$value->createdTxnBy->name

            	

        ];

    }

}