<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\UserLoggedInDetail;
use App\User;
use Auth;
use DB;
use App\Traits\CustomTraits;

class UserLoggedInHistoryController extends Controller
{
    //
	use CustomTraits;
	public function getLoggedInHistory(Request $request)
	{
		$historyDetailsQ = UserLoggedInDetail::orderBy('id','desc');
		$start_date = ($request->fromdate) ? $request->fromdate ." 00:00:00" :  date("Y-m-d") . " 00:00:00";
		$end_date = ($request->todate) ? $request->todate ." 23:59:59" :  date("Y-m-d") ." 23:59:59";
		$dateFormat = $this->getDbFromatDate($start_date, $end_date);
		$historyDetailsQ->whereBetween('created_at', array($dateFormat['start_date'], $dateFormat['end_date']));
		$userLists=array();
		if(Auth::user()->role_id == 1)
		{
			
			$dateFormat = $this->getDbFromatDate($start_date, $end_date);
			$userLists =User::select(
            DB::raw("CONCAT(name,' : ',mobile) AS name"),'id')->pluck('name','id')->toArray();
			if($request->user_id !='')
				$historyDetailsQ->where('user_id',$request->user_id);
		}
		else{
			$historyDetailsQ->where('user_id',Auth::id());
		}
		if($request->export == "SEARCH")
		{
			if($request->export == "EXPORT")
			{
				
			}
		}
		$historyDetails = $historyDetailsQ->simplePaginate(30);
		return view('admin.loggedIn-history',compact('historyDetails','userLists'));
		}
	public function customHistorySearch(Request $request)
	{
			$getLoggedInHistory::where('user_id',$request->id)->get();
			$data = $getLoggedInHistory->map(function ($detail) {
				return [
					'created_at'=>date_format($detail->created_at,"Y-m-d H:i:s"),
					'userDetails'=>$detail->name .'('.$detail->id.')',
					'id'=>$detail->id,
					'ip_address'=>$detail->ip_address,
					'browser'=>$detail->browser,
					'latitude'=>$detail->latitude,
					'longitude'=>$detail->longitude,
					'country_name'=>$detail->country_name,
					'city'=>$detail->city,
				];
			});
		}
	
}
