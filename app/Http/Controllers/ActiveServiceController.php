<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests;
use App\ActiveService;

class ActiveServiceController extends Controller
{
   public function index(Request $request)
   {
	   	if(Auth::user()->role_id == 1 || Auth::user()->role_id == 19)
		{
		   	$services=ActiveService::all();
			return view('admin.servicemanage',compact('services'));
		}
	   	return view('errors.page-not-found');
   	}

   	public function makeActiveInactiveServices(Request $request)
	{
		if(Auth::user()->role_id ==1 || Auth::user()->role_id ==19){
			$serviceVar = $request->serviceVar;
			$Provider = ActiveService::find($request->apiId);
			$Provider->$serviceVar = $request->newStatus;
			if($Provider->save())
				return response()->json(['status'=>1,"message"=>"Service Updated"]);
				return response()->json(['status'=>2,"message"=>"Failed"]);
			return view('errors.permission-denied');
		}
	}
	public function update(Request $request)
    {
    	if(Auth::user()->role_id ==1){
			$services=ActiveService::find($request->id);
			$services->message = $request->message;
			if($services->save())
				return response()->json(['status'=>1,"message"=>"Service Updated"]);
		}		return response()->json(['status'=>2,"message"=>"Failed"]);
    }
	

}
