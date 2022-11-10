<?php
namespace App\Exports;
use App\User;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\FromQuery;
use Auth;
use DateTime;
class UserReportExport implements WithHeadings,WithMapping,FromQuery
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
	 return User::query()->orderBy('id','desc'); 
    }
    public function headings():array
	{
	   return [
	    'Date','Time','ID','User Name','Mobile','Balance','Member Type','Parent Name','Status'
	    ];
	}
	public function map($employee):array
    {
		$s = $employee->created_at;
		$dt = new DateTime($s);
        return [
			$dt->format('d/m/Y'),
			$dt->format('H:i:s'), 
			@$employee->prefix.' '.@$employee->id, 
			@$employee->name,
			@$employee->mobile,
			number_format(@$employee->balance->user_balance,2),
			@$employee->role->role_title,
			@$employee->parent->name.'('.@$employee->parent->prefix .'-'.@$employee->parent_id .')',
			@$employee->status_id ? "Active" : "In-active",
			(in_array(Auth::user()->role_id,array(5,15))) ? @$value->createdTxnBy->name :'' 	
        ];
    }
}