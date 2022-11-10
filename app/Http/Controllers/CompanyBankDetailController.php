<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use Validator;
use Response;
use  App\CompanyBankDetail;
use  App\User;
class CompanyBankDetailController extends Controller
{
   public function index()
   {
	   $bankDetails = CompanyBankDetail::where('user_id','=',1)->get();
	   $user = User::find(Auth::id());
	    if(Auth::user()->role_id>1)
	   	 $bankDetails = CompanyBankDetail::where('user_id','=',1)->where('status_id',1)->get();
		if(in_array(Auth::user()->role_id,array(1,2,3,4)))
			return view('admin.bank-details.bank',compact('bankDetails','user'));
		else
		$bankDetails = CompanyBankDetail::where(['user_id' => Auth::user()->parent_id])->orwhere('user_id','=',1)->where('status_id',1)->get();
			return view('reports.bank-details',compact('bankDetails'));
   }
   public function store(Request $request)
   {
	   $rules = array(
            'bank_name' => 'required',
			'account_number' => 'required|numeric|unique:company_bank_details',
			'ifsc_code' => 'required',
            'branch_name' => 'required',
        );
		$validator = Validator::make($request->all(), $rules);
		if ($validator->fails()) {
			return Response::json(array(
				'success' => false,
				'errors' => $validator->getMessageBag()->toArray()
			)); // 400 being the HTTP code for an invalid request.
		}
		CompanyBankDetail::create($request->all());
		return response()->json([
		'status' => 'success',
        'message' => 'Bank account added successfully!'
    ]);
		
   }
   public function view(Request $request)
   {
	   return response()->json(['status'=>1,'details'=>CompanyBankDetail::find($request->id)]);
	   
   }
   public function update(Request $request)
   {
		$existCompany=CompanyBankDetail::find($request->id);
		$rules = array(
			'bank_name' => 'required',
			'account_number' => 'required|numeric|unique:company_bank_details,account_number,'.$request->id,
			'ifsc_code' => 'required',
            'branch_name' => 'required',
		);
		$validator = Validator::make($request->all(), $rules);
		if ($validator->fails()) {
			return Response::json(array(
				'success' => false,
				'errors' => $validator->getMessageBag()->toArray()
			)); // 400 being the HTTP code for an invalid request.
		} 
		unset($request['id']);
		unset($request['_token']);
		//print_r($request->all());die;
		CompanyBankDetail::where('id',$request->id)->update($request->all());
		return Response::json(array(
				'status' => 'success',
				'message' => "Update Successfully",
			));	
   }
   public function deleteDetails(Request $request)
   {
	    CompanyBankDetail::find($request->id)->delete($request->id);
		return response()->json([
			'status' => 'success',
			'message' => 'Record deleted successfully!'
		]);
   }
   
   public function lowerLevelBankDetails()
   {
	  $bankDetails = CompanyBankDetail::where(['user_id' => Auth::id()])->get();
	  $user = User::find(Auth::id());
	   /* if(Auth::user()->role_id==4)
	   	 $bankDetails = CompanyBankDetail::where(['user_id' => Auth::id()])->where('status_id',1)->get();*/
		if(in_array(Auth::user()->role_id,array(3,4)))
			return view('admin.bank-details.lower-level-bank-detail',compact('bankDetails','user'));
    }
   public function upperLevelBankDetails()
   {
	  $bankDetails = CompanyBankDetail::where(['user_id' => Auth::user()->parent_id])->get();
	  $user = User::find(Auth::id());
		if(in_array(Auth::user()->role_id,array(4)))
			return view('admin.bank-details.upper-level-bank-details',compact('bankDetails','user'));
    }
}
