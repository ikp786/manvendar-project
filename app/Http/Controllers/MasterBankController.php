<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Auth;
use Validator;
use Response;
use App\Traits\CustomTraits;
use  App\Masterbank;
use Exception;
class MasterBankController extends Controller
{
	use CustomTraits;
    public function index()
    {
    	
    	$bankDetails = Masterbank::orderBy('id', 'ASC')->get();
		if(in_array(Auth::user()->role_id,array(1)))
		{
			return view('admin.bank-details.masterbank',compact('bankDetails'));
		}
    }
 	public function store(Request $request)
   	{
   		//print_r($request->all());
	   $rules = array(
           'bank_name' => 'required|unique:masterbanks',
			'ifsc' => 'required',
        );
		$validator = Validator::make($request->all(), $rules);
		if ($validator->fails()) {
			return Response::json(array(
				'success' => false,
				'errors' => $validator->getMessageBag()->toArray()
			)); // 400 being the HTTP code for an invalid request.
		}
		Masterbank::create($request->all());
		return response()->json([
		'status' => 'success',
        'message' => 'Bank account added successfully!'
    	]);
		
   }
   public function view(Request $request)
   {
	   return response()->json(['status'=>1,'details'=>Masterbank::find($request->id)]);	   
   }

    public function update(Request $request)
   {
		$existCompany=Masterbank::find($request->id);
		$rules = array(
			'bank_name' => 'required|unique:masterbanks,bank_name,'.$request->id,
			'ifsc' => 'required',
            
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
		Masterbank::where('id',$request->id)->update($request->all());
		return Response::json(array(
				'status' => 'success',
				'message' => "Update Successfully",
			));	
    }

	public function deleteDetails(Request $request)
   	{
		  if (Auth::user()->role_id == 1) {
	    Masterbank::find($request->id)->delete($request->id);
		return response()->json([
		'status' => 'success',
        'message' => 'Record deleted successfully!'
		]);
		  }
	}
	public function checkBankStatus(Request $request)
	{
		
		$bankSortName = $request->bankSortName;
		if($bankSortName!='')
		{
			$content =array('account'=>$request->accountNumber,"outletid"=>1,"bank"=>$bankSortName);
			$instantPay = new \App\Library\InstantPayDMT; 
			$details = $instantPay->isBankdDownOrNot($content);
			try
			{
				$res = json_decode($details);
				if (!empty($res->statuscode) && $res->statuscode =="TXN") 
				{
					$fistArray = $res->data[0];
					return response()->json(['status'=>1,'message'=>"Record Found",'details'=>$fistArray]);
				}
				return response()->json(['status'=>0,'message'=>"Invalid Response",'details'=>$details]);
			}
			catch(Exception $e)
			{
				return response()->json(['status'=>0,'message'=>"Api Exception",'details'=>$details]);
			}
		}
		return response()->json(['status'=>0,'message'=>"Bank Short name is not available",'details'=>'']);
	}
	public function updateImpsService(Request $request)
	{
		if (Auth::user()->role_id == 1) {
			$bankDetails = Masterbank::selectRaw('id,is_imps_txn_allow')->find($request->id);
			$bankDetails->is_imps_txn_allow=$request->status_id;
			if($bankDetails->save())
			return response()->json(['status'=>1,'message'=>"Updation successfully",'details','']);
			return response()->json(['status'=>0,'message'=>"Failed",'details','']);
		}
	}
}
