<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Provider;
use Auth;
class OperatorController extends Controller
{
    public function switchRechargeOperator(Request $request)
    {
		if(Auth::user()->role_id == 1 || Auth::user()->role_id == 19)
		{
			//print_r($request->all());die;
			$id = $request->id;
			$api_id = $request->api_id;		
			 
				$rechargeProvider = Provider::find($id);
				
				if($api_id == 8)
				{
					if($rechargeProvider->redpay == '')
					  return response()->json(['status'=>0,"message"=>"Red Pay Service is not available for this"]);
				}
				elseif($api_id == 13)
				{
					if($rechargeProvider->suvidhaa == '')
					  return response()->json(['status'=>0,"message"=>"A2ZSuvidhaa Service is not available for this"]);
				}
				elseif($api_id == 14)
				{
					if($rechargeProvider->provider_code2 == 'OIL')
					  return response()->json(['status'=>0,"message"=>"MRobotics Service is not available for this"]);
				}
				$rechargeProvider->api_id = $api_id;
				$rechargeProvider->save();
				return response()->json(['status'=>1,"message"=>"Recharge Operator applied Successfully"]);
		}
		return response()->json(['status'=>503,"message"=>"Access Denied"]);
	}
	public function rechargeOperatorList(Request $request)
    {		
        if (Auth::user()->role_id == 1 || Auth::user()->role_id == 19)
        {
            $provider_manage = Provider::orderBy('is_service_active', 'asc')->orderBy('is_service_online', 'asc')->where('id','!=',0)->where('service_id','!=',0)->select('id','api_id','provider_name','service_id','cyber','redpay','is_service_active','is_service_online','suvidhaa','provider_code2')->get();    
          return view('admin.providermanage', compact('provider_manage'));
        }
		else
		{
			return response()->json(['status'=>503,"message"=>"Access Denied"]);
		}
    }
	public function onOffService(Request $request)
	{
		//print_r($request->all());die;
		$serviceVar = $request->serviceVar;
		$Provider = Provider::find($request->providerId);
		$Provider->$serviceVar = $request->newStatus;
		if($Provider->save())
			return response()->json(['status'=>1,"message"=>"Service Updated"]);
			return response()->json(['status'=>2,"message"=>"Failed"]);
	}
}
