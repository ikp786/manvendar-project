<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Http\Requests;
use Auth;
use Validator;
use Response;
use DB;
use App\AepsSettlement;
use App\User;
use App\Balance;
use App\Report;

class AepsSettlementController extends Controller
{
    public function index()
    {
      	$aepsSettelmentQuery = AepsSettlement::orderBy('status_id','desc');
    	if(in_array(Auth::user()->role_id,array(1,19)))
    	{
    		$bankDetails = $aepsSettelmentQuery->get();
			return view('admin.bank-details.aepssettlement',compact('bankDetails'));
    	}
		elseif(in_array(Auth::user()->role_id,array(5,7))){
			$bankDetails = $aepsSettelmentQuery->where('user_id',Auth::id())->get();
    		return view('agent.aepsSettlement.aepssettlement',compact('bankDetails'));
		}
		else
			return view('errors.permission-denied');
    }
   
    public function store(Request $request)
   	{
	   	$rules = array(
            'bank_name' => 'required|regex:/^[a-zA-Z ]+$/',
			'account_number' => 'required|numeric|unique:aeps_settlements',
			'ifsc' => 'required|regex:/^[a-zA-Z0-9]+$/',
			'name' => 'required',
            'branch_name' => 'required',
        );

		$validator = Validator::make($request->all(), $rules);
		if ($validator->fails()) {
			return Response::json(array(
				'status' => 10,
				'errors' => $validator->getMessageBag()->toArray()
			)); // 400 being the HTTP code for an invalid request.
		}
		$isBankAepsSettlement = AepsSettlement::where('user_id',Auth::id())->whereIn('status_id',[1,3])->first();
		if($isBankAepsSettlement){
			$status = ($isBankAepsSettlement->status_id==1) ?"Success":"Pending";
			return response()->json([
				'status' => 0,
		        'message' => 'You have already added bank for aeps satelment,status : '.$status,
		    	]);	
		}
		elseif(in_array(Auth::user()->role_id,array(5,7))){
		
			AepsSettlement::create($request->all());
				return response()->json([
				'status' => 1,
		        'message' => 'Bank account added successfully!'
		    ]);	
		}
		else
			return response()->json(['status'=>0,'message'=>"You do not have permission"]);
   	}
   public function view(Request $request)
   {
	   return response()->json(['status'=>1,'details'=>AepsSettlement::find($request->id)]); 
   }
   public function update(Request $request)
   {
    	//print_r($aeps);die();
		$existCompany=AepsSettlement::find($request->id);
		$rules = array(
			'bank_name' => 'required',
			'account_number' => 'required|numeric|unique:aeps_settlements,account_number,'.$request->id,
			'ifsc' => 'required',
			'name' => 'required',
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
		AepsSettlement::where('id',$request->id)->update($request->all());
		return Response::json(array(
				'status' => 'success',
				'message' => "Update Successfully",
			));	
   }
   public function deleteDetails(Request $request)
   {
	    AepsSettlement::find($request->id)->delete($request->id);
		return response()->json([
		'status' => 'success',
        'message' => 'Record deleted successfully!'
    ]);
   }

    public function banklist()
    {
		//echo "hello";die;
    	
    	if(Auth::user()->role_id==5)
		{
			$bankDetails = AepsSettlement::where('user_id',Auth::id())->get();
    		return view('agent.aepsSettlement.bank',compact('bankDetails'));
		}
		else 
			return view('errors.permission-denied');
    }
    public function bankfund()
    {
    	
    	if(in_array(Auth::user()->role_id,array(5,7))){
		
		$aepsSettelmentQuery = AepsSettlement::orderBy('status_id','desc');
		 $bankDetails = $aepsSettelmentQuery->where(['user_id'=>Auth::id(),'status_id'=>1])->get();
    	 return view('agent.aepsSettlement.bankfund',compact('bankDetails'));
        }
		else 
			return view('errors.permission-denied');
    }
    public function AepsSettlementAmount(Request $request)
	{
		$rules = array(
			'amount' => 'required|numeric|min:10',
			'channel' => 'required',
		);
		if(in_array(Auth::user()->role_id,array(5,7))){
		
			$requestAmt= $request->amount;
			if($request->channel == 2)
				$debitCharge = (ceil(($requestAmt)/25000)*Auth::user()->member->aeps_charge);
			else
				$debitCharge=0;
			$debitAmount = $requestAmt +$debitCharge ;
			$remark=$request->remark;
			$id=$request->id;
			$logginedUserBalance = Balance::select('user_balance')->where('user_id',Auth::id())->first();
			$minimumAvlBal = Auth::user()->member->aeps_blocked_amount;
			if($minimumAvlBal > $logginedUserBalance->user_balance)
			{
				return response()->json([
							'status' => 0,
							'message' => "You do not have minimum available Balance Rs. ".$minimumAvlBal,
						]);
			}
			else if($logginedUserBalance->user_balance < $debitAmount)
			{
				return response()->json([
							'status' => 0,
							'message' => "In-sufficent Balance",
						]);
				
			}
			if($logginedUserBalance->user_balance > 10)
			{
				$aepsSettement = AepsSettlement::find($id);
				if($aepsSettement->user_id == Auth::id())
				{
					
					$data=[
						'user_id'=>Auth::id(),				
						'provider_id'=>41,	
						'amount'=>$requestAmt,	
						'channel'=>$request->channel,	
						'api_id'=>10,	
						'number'=>Auth::user()->mobile,	
						'ip_address'=>\Request::ip(),	
						'debit_charge'=>$debitCharge,	
						'credit_charge'=>0,	
						'profit'=>0,	
						'opening_balance'=>$logginedUserBalance->user_balance,	
						'remark'=>$remark,
						'txnid' => 'AEPS_SETTELMENT',					
						'description' => 'AEPS_SETTELMENT_PENDING',					
						'txn_type' => 'AEPS_SETTELMENT',					
						'status_id' => 3,					
						'aeps_sattelment_id' => $id,					
						'pay_id' => time(),					
					];	
			
					DB::beginTransaction();
					try
					{
						Balance::where('user_id',Auth::id())->decrement('user_balance',$debitAmount);
						$data['total_balance'] = Balance::where('user_id',Auth::id())->select('user_balance','user_id')->first()->user_balance;
						/* Balance::where('user_id',1)->increment('user_balance',$debitAmount);*/
						Report::create($data);
						 DB::commit();
						return response()->json([
							'status' => 1,
							'message' => 'Request has been submitted!'
						]);
						//return redirect()->back()->with('success', 'Request has been submitted');
					}
					catch(Exception $e)
					{
						DB::rollback();
						return response()->json([
							'status' => 'Pending',
							'message' => 'Whoops Something went wrong. Please try again!'
						]);
						//return redirect()->back()->withErrors('Whoops Something went wrong. Please try again.');
					}
				}
			}
			return response()->json([
							'status' => 0,
							'message' => "Mimimum aeps settlement amount Rs. 10",
						]);
		} 
		else 
			return view('errors.permission-denied');
	}
	public function aepsSettelmentApprove(Request $request)
	{
		if(Auth::user()->role_id==1 || Auth::user()->role_id==19)
		{
			print_r($request->all());die;
				$report = Report::find($request->id);
				$report->status_id =6;
				$report->description='AEPS_SETTELMENT_APPROVED';
				if($report->description=="AEPS_SETTELMENT_PENDING" && $report->status_id==3)
				{
					DB::beginTransaction();
					try
					{
					$report->save();
					Balance::where('user_id',Auth::id())->increment('aeps_balance',$report->amount);
					$availableBal = Balance::where('user_id',Auth::id())->select('user_balance','admin_com_bal')->first();
					Report::create([
							'number'=>Auth::user()->mobile,
							'user_id'=>Auth::id(),	
							'amount'=>$report->amount,
							'txnid'=>$report->id,
							'api_id'=>$report->api_id,
							'description'=>"AEPS_SATTELMENT_APPROVED",
							'bank_ref'=>$request->bankRef,
							'remark'=>$report->remark,
							'status_id' => 7,					
							'aeps_sattelment_id' => $report->aeps_sattelment_id,
							'pay_id' => time(),	
							'credit_by' => $report->user_id,	
							'admin_com_bal' => $availableBal->admin_com_bal,	
							'total_balance' => $availableBal->user_balance,	
							]);
							DB::commit();
							return response()->json([
							'status' => 1,
							'message' => 'Aeps Sattelment Appoved'
						]);
					}
					catch(Exception $e)
					{
						DB::rollback();
						return response()->json([
							'status' => 2,
							'message' => 'Error'.$e->getMessage(),
						]);
						
					}
				}
				return response()->json([
							'status' => 1,
							'message' => 'Already Approved'
						]);
			
		}
		return view('errors.permission-denied');
	}
}
