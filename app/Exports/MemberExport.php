<?php
namespace App\Exports;
use App\User;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\FromQuery;
use Auth;
use DateTime;
use App\Traits\CustomTraits;
class MemberExport implements WithHeadings,WithMapping,FromQuery
{
	use CustomTraits;
	public function __construct()
    {
		
    }
  public function query()
  {
  	if (Auth::user()->role_id != 1){

  		$member=$this->getAllMemberIncludingLoggedUser(Auth::user()->role_id,Auth::id());
  		return User::query()->orderBy('id','desc')->whereIn('id',$member);
  	}
  	return User::query()->orderBy('id','desc');
     
  }
  public function headings() : array
    {
    	if (Auth::user()->role_id == 1)
	        return [
	            'Registration Date','Time','Outlet Name','Parent Details','Id','Name','Mobile','Email','Pan Card','Aadhaar Number','state','Role','Balance','Address','Outlet Address','Kyc','Pin code','Status','DMT ONE Scheme','A2Z Scheme','A2Z Plus Scheme','Verification Scheme','Bill Scheme','Aeps Scheme'
	        ];
        else
	        return[
	        	 'Registration Date','Time','Outlet Name','Parent Details','Id','Name','Mobile','Email','state','Role','Balance','Address','Outlet Address','Pin code','Status'
	        ];
    }
	public function map($value): array
    {
    	$s = $value->created_at;
		$dt = new DateTime($s);
    	if (Auth::user()->role_id == 1){
	        return [
					$dt->format('d/m/Y'),
					$dt->format('H:i:s'),
					@$value->member->company, 
					@$value->parent->name .'('. @$value->parent->prefix .'-'.@$value->parent->id .')', 
					@$value->prefix .' ' .@$value->id,
					@$value->name, 
					@$value->mobile, 
					@$value->email, 
					@$value->member->pan_number,
					@$value->member->adhar_number,
					@$value->member->state->name, 
					@$value->role->role_title, 
					@$value->balance->user_balance, 
					@$value->member->address,
					@$value->member->office_address, 
					@$value->kyc, 
					@$value->member->pin_code, 
					@$value->status->status,
					@$value->member->DmtOneWalletScheme->name,
					@$value->member->DmtTwoWalletScheme->name,
					@$value->member->PaytmWalletScheme->name,
					@$value->member->verificationSchemeName->name,
					@$value->member->AepsWalletScheme->name,
					@$value->member->BillSchemeName->name,
				];
			}
			else{
					return [
						$dt->format('d/m/Y'),
						$dt->format('H:i:s'),
						@$value->member->company, 
						@$value->parent->name .'('. @$value->parent->prefix .'-'.@$value->parent->id .')', 
						@$value->prefix .' ' .@$value->id,
						@$value->name, 
						@$value->mobile, 
						@$value->email, 
						@$value->member->state->name, 
						@$value->role->role_title, 
						@$value->balance->user_balance, 
						@$value->member->address,
						@$value->member->office_address, 
						@$value->member->pin_code, 
						@$value->status->status,
        		];
        	}
    }
}