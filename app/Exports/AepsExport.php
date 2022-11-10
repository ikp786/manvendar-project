<?php
namespace App\Exports;
use App\Report;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\FromQuery;
use Auth;
use DateTime;
class AepsExport implements WithHeadings,WithMapping,FromQuery
{
	public  $condition;
	public  $start_date;
	public  $end_date;	
	public function __construct($condition,$start_date,$end_date,$user_id)
    {
		$this->condition = $condition;
		$this->start_date = $start_date;
		$this->end_date = $end_date;
		$this->user_id = $user_id;
    }
  	public function query()
  	{
	  	if(Auth::user()->role_id==1)
			if($this->user_id!=''){
	  			return Report::query()->where('user_id',$this->user_id )->whereIn('api_id',[10])->where('api_id','!=',4)->where('api_id','!=',5)->whereBetween('created_at',[$this->start_date,$this->end_date])->orderBy('id','desc');
	  		}
	  		else{
	  			return Report::query()->whereIn('api_id',[10])->where('api_id','!=',4)->where('api_id','!=',5)->whereBetween('created_at',[$this->start_date,$this->end_date])->orderBy('id','desc');
	  		}
		else
			return Report::query()->whereIn('api_id',[10])->where('api_id','!=',4)->where('api_id','!=',5)->whereIn('user_id',$this->condition)->whereBetween('created_at',[$this->start_date,$this->end_date])->orderBy('id','desc');
  	}
  	public function headings() : array
    {
        return [
          'Date', 'Time','Id', 'Firm Name','Pan Card','Member Type','Parent Name','Remitter Number','Acc/Mobile/k Number','Bank Name','IFSC','Operator Txn Id','Remark','Amount','Status','Bank RR Number/Check','Description','Credit/Debit','Opening Balance','Credit Amount','Debit Amount','TDS','Service Tax','Balance','Txn Type','Transfer R2R',(Auth::user()->role_id==1) ? 'Admin Comm':''
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
            $value->user->member->company .'('. $value->user->prefix . ' - ' . $value->user_id .')',
			@$value->user->member->pan_number,
            @$value->user->role->role_title,
            @$value->user->parent->name.'('.@$value->user->parent->prefix .'-'.@$value->user->parent_id .')',
			@$value->customer_number,
			(@$value->api_id == 2) ? @$value->biller_name : @$value->number,
			in_array($value->api_id,array(2,10)) ?  @$value->description : @$value->beneficiary->bank_name,
			@$value->beneficiary->ifsc,
			@$value->txnid,
			@$value->remark,
			number_format(@$value->amount,2),
			@$value->status->status,
			(@$value->status_id !=4) ? @$value->bank_ref : '',
			(@$value->recharge_type== 1) ? @$value->provider->provider_name : @$value->api->api_name,
			@$value->type,
			@$value->opening_balance, 
			@$value->credit_charge,
			@$value->debit_charge,
			@$value->tds,
			@$value->gst,
			@$value->total_balance,
			@$value->txn_type,
			(@$value->txnid=="DT") ? @$value->description :'',
			(Auth::user()->role_id==1) ? $value->admin_com_bal:''		
        ];
    }
}