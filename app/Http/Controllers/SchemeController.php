<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Scheme;
use App\Moneyscheme;
use App\Commission;
use App\Http\Requests;
use Validator;
use Response;
use DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class SchemeController extends Controller
{

  public function index(){
		if(Auth::user()->role_id == 1 || Auth::user()->role_id == 19)
		{
			$schemes = Scheme::all();
			//return $schemes;
			return view('admin.scheme', compact('schemes'));
		}
		return view('errors.page-not-found');
    }
     public function money_scheme(){
        if(Auth::user()->role_id == 1 || Auth::user()->role_id == 19)
        {
            $schemes = Moneyscheme::all();
            return view('admin.moneyscheme', compact('schemes'));
        }
        return view('errors.page-not-found');
    }
	public function createRechargeScheme(Request $request)
    {
		
        $rules = array('scheme_name' => 'required|unique:schemes');
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return Response::json(array(
                'success' => false,
                'errors' => $validator->getMessageBag()->toArray()
            ));
        } else {
            Scheme::create(['scheme_name' => $request->scheme_name, 'user_id' => Auth::id(), 'company_id' => Auth::user()->company_id]);
			return response()->json(["message"=>"Recharge Scheme Created Successfully"]);

        }
    }
    public function store(Request $request)
    {
        $rules = array('scheme_name' => 'required');
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return Response::json(array(
                'success' => false,
                'errors' => $validator->getMessageBag()->toArray()
            ));
        } else {
            return Scheme::create(['scheme_name' => $request->scheme_name, 'user_id' => Auth::id(), 'company_id' => Auth::user()->company_id]);

        }
    }
    public function moneystore(Request $request)
    {
        $rules = array('scheme_name' => 'required');
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return Response::json(array(
                'success' => false,
                'errors' => $validator->getMessageBag()->toArray()
            ));
        } else {
            return Moneyscheme::create(['scheme_name' => $request->scheme_name, 'user_id' => Auth::id(), 'company_id' => Auth::user()->company_id]);

        }
    }
    public function view(Request $request){
        $id = $request->input('id');
        $scheme = Scheme::find($id);
        return $scheme;
    }

    public function update(Request $request, $id)
    {
        $scheme = Scheme::find($id);
        $scheme->scheme_name = $request->scheme_name;
        $scheme->save();
        return $scheme;
    }
	public function viewRechargeScheme(Request $request)
    {
			$rechargeScheme = Scheme::select('scheme_name')->find($request->id);
			return response()->json(['status'=>1,"message"=>$rechargeScheme]);

        
    }public function updateRechargeScheme(Request $request,$schemeId)
    {
		//print_r($schemeId);die;
        $rules = array('scheme_name' => 'required');
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return Response::json(array(
                'success' => false,
                'errors' => $validator->getMessageBag()->toArray()
            ));
        } else {
            Scheme::where('id',$schemeId)->update(['scheme_name' => $request->scheme_name]);
			return response()->json(["Recharge Scheme Updated Successfully"]);

        }
    }public function deleteRechargeScheme(Request $request)
    {
		if($request->schemeId == 1)
			return back()->withError("Sorry!, You can not delete Default Scheme");
		DB::beginTransaction();
		try{
			Scheme::where('id',$request->schemeId)->delete();
			Commission::where('scheme_id',$request->schemeId)->delete();
			DB::commit();
            return back()->with('success', "Recharge Scheme Deleted successfully");
		}
		catch(Exception $e)
		{
			
			 return back()->withError("Something went wrong");
		}

    }

}
