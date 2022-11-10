<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Commission;
use App\Scheme;
use App\Service;
use App\Provider;
use Auth;
use DB;
use Validator;
use Exception;
class ProviderController extends Controller
{
    //
	public function index()
	{
		if(Auth::user()->role_id == 1 || Auth::user()->role_id == 19)
		{
			$servicelist = Service::pluck('service_name','id')->toArray();
			$providerList = Provider::where('service_id','!=',0)->orderBy('service_id')->get();
			return view('admin.provider.provider-list',compact('providerList','servicelist'));
		}
	}
	public function viewProviderDatils(Request $request)
	{
		if(Auth::user()->role_id == 1 || Auth::user()->role_id == 19)
		{
			return response()->json(['status'=>1,'message'=>Provider::find($request->id)]);

			}
	}
	public function store(Request $request)
	{
		if(Auth::user()->role_id == 1 || Auth::user()->role_id == 19)
		{
			$rules = array(
				'provider_name' => 'required|unique:providers',
			);
			$validator = Validator::make($request->all(), $rules);
			if ($validator->fails()) {
				return response()->json(array(
					'status' => 10,
					'errors' => $validator->getMessageBag()->toArray(),
					'message' => "providerName is missing"
				)); // 400 being the HTTP code for an invalid request.
			}
		DB::beginTransaction();
		try{
				$provider= Provider::create([
					'provider_name'=>$request->provider_name,
					'cyber'=>$request->cyberCode,
					'redpay'=>$request->redPayCode,
					'suvidhaa'=>$request->suvidhaa,
					'service_id'=>$request->serviceId,
					'max_hold_txn'=>$request->maxHoldTxn,
					'min_pass_amt_txn'=>$request->maxHoldTxn,
					'sp_key'=>$request->sp_key,
				]);
			$providerId = $provider->id;
			$scemesIds = Scheme::pluck('id','id')->toArray();
			foreach($scemesIds as $key=>$value)
			{
				Commission::create(['provider_id'=>$providerId,'rechargeprovider_id'=>$providerId,'scheme_id'=>$key]);
			}
			DB::commit();
			return response()->json(['status'=>1,'message'=>"Updation Successfully"]);
			
		}
		catch(Exception $e)
		{
			DB::rollback();
			return response()->json(['status'=>0,'message'=>"Error"]);
		}
		}
	}
	public function deleteProvider(Request $request)
	{
		if(Auth::user()->role_id == 1 || Auth::user()->role_id == 19)
		{
		Commission::where('provider_id',$request->id)->delete();
		}
	}
	public function updateServiceType(Request $request)
	{
		if(Auth::user()->role_id == 1 || Auth::user()->role_id == 19)
		{
		$provider = Provider::find($request->providerId);
		$provider->service_id = $request->serviceId;
		if($provider->save())
			return response()->json(['status'=>1,'message'=>"Service type updated"]);
		return response()->json(['status'=>0,'message'=>"Error"]);
		}
	}
	public function updateOperatorCode(Request $request)
	{
		if(Auth::user()->role_id == 1 || Auth::user()->role_id == 19)
		{
			$provider = Provider::find($request->providerId);
			$provider->redpay = $request->redPayCode;
			$provider->cyber = $request->cyberCode;
			$provider->suvidhaa = $request->suvidhaa;
			if($provider->save())
				return response()->json(['status'=>1,'message'=>"Code Updated Successfully"]);
			return response()->json(['status'=>0,'message'=>"Error"]);
		}
	}
	public function update(Request $request,$id)
	{
		if(Auth::user()->role_id == 1 || Auth::user()->role_id == 19)
		{
			$rules = array(
				'provider_name' => 'required|unique:providers,provider_name,'.$id,
			);
			$validator = Validator::make($request->all(), $rules);
			if ($validator->fails()) {
				return response()->json(array(
					'status' => 10,
					'errors' => $validator->getMessageBag()->toArray(),
					'message' => "providerName is missing"
				)); // 400 being the HTTP code for an invalid request.
			}
			$provider = Provider::find($request->providerId);
			$provider->provider_name = $request->provider_name;
			$provider->redpay = $request->redPayCode;
			$provider->cyber = $request->cyberCode;
			$provider->suvidhaa = $request->suvidhaa;
			$provider->max_hold_txn = $request->maxHoldTxn;
			$provider->min_pass_amt_txn = $request->minPassAmtTxn;
			$provider->service_id = $request->serviceId;
			$provider->sp_key = $request->sp_key;
			if($provider->save())
				return response()->json(['status'=>1,'message'=>"Updation Successfully"]);
			return response()->json(['status'=>0,'message'=>"Error"]);
		}
	}
	public function showUncategoryList()
	{
		$servicelist = Service::pluck('service_name','id')->toArray();
		$providerList = Provider::where('service_id','=',0)->orderBy('service_id')->get();
		return view('admin.provider.provider-list',compact('providerList','servicelist'));
	}
}
