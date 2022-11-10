<?php

namespace App\Http\Controllers\Mobile;

use App\Http\Controllers\Controller;
use App\Traits\CustomAuthTraits;
use App\Traits\CustomTraits;
use Illuminate\Http\Request;
use Auth;
use Validator;
use Response;
use  App\CompanyBankDetail;
class CompanyBankDetailController extends Controller
{
    use CustomTraits,CustomAuthTraits;
   public function getBankDetails(Request $request)
   {
       $rules = array(
           'token' => 'required',
           'userId' => 'required',
       );
       $validator = Validator::make($request->all(), $rules);
       if ($validator->fails()) {
           return Response::json(array(
               'success' => false,
               'errors' => $validator->getMessageBag()->toArray()
           ));
       }
       $authentication = $this->checkLoginAuthentication($request);
       if ($authentication['status'] == 1) {
           $bankDetails = CompanyBankDetail::all();
           return response()->json([
               'status'=>'1',
               'message'=>'success',
               'banks'=>$bankDetails,
           ]);

       }else return $authentication;
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
}
