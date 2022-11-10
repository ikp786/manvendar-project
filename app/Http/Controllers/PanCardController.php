<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Balance;
use App\Member;
use App\Report;
use DB;
use Auth;
use Exception;

class PanCardController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('agent.pancard');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function cardload()
    {
        return view('agent.pancard.cardload');
    }
   

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $pancardpayment = $request->pancardpayment;
        $Amount = $request->cost_of_card;
        $TotalCard = $request->total_card;
        $chargeAmount = 5;
        $debitAmount = $Amount * $TotalCard;
        $logginedUserBalance = Balance::select('user_balance')->where('user_id',Auth::id())->first();
        if($logginedUserBalance->user_balance > $debitAmount)
        {
            DB::beginTransaction();
            try
            {
                Balance::where('user_id',Auth::id())->decrement('user_balance',$debitAmount);
                Report::create([
                            'number' => $TotalCard,
                            'provider_id' => 41,
                            'loan_emi_id' => $pancardpayment,
                            'amount' => $Amount,
                            'profit' => $chargeAmount,
                            'api_id' => 23,
                            'ip_address'=> \Request::ip(),
                            'status_id' => 3,
                            'description' => 'PanCard',
                            'user_id' => Auth::id(),
                            'customer_number' => $request->customer_mobile_no,
                            'total_balance' => Balance::where('user_id',Auth::id())->select('user_balance','user_id')->first()->user_balance,
                            'channel' => 3,
                        ]);
                        DB::commit();
                        return redirect()->back()->with('success', 'Request has been submitted');
            }
            catch(Exception $e)
            {
                DB::rollback();
                
                return redirect()->back()->withErrors('Whoops Something went wrong. Please try again.');
                
                
            }
        }
        return redirect()->back()->withErrors('Insufficient Balance');
    }
	public function activePanService(Request $request)
	{
		if(Auth::user()->member->is_pan_active == 0)
		{
			$logginedUserBalance = Balance::select('user_balance')->where('user_id',Auth::id())->first();
			$debitAmount=100;
			if($logginedUserBalance->user_balance >= $debitAmount)
			{
				DB::beginTransaction();
				try
				{
					Balance::where('user_id',Auth::id())->decrement('user_balance',$debitAmount);
					Member::where('user_id',Auth::id())->update(['is_pan_active'=>3]);
					Report::create([
								'number' => Auth::user()->mobile,
								'provider_id' => 41,
								'amount' => $debitAmount,
								'profit' => 0,
								'api_id' => 24,
								'ip_address'=> \Request::ip(),
								'status_id' => 3,
								'description' => 'PAN CARD ACITIVATION',
								'txnid' => 'PAN CARD ACITIVATION',
								'user_id' => Auth::id(),
								'total_balance' => Balance::where('user_id',Auth::id())->select('user_balance','user_id')->first()->user_balance,
							]);
							DB::commit();
							return response()->json('Your Pancard Request Accepted.');
				}
				catch(Exception $e)
				{
					DB::rollback();
					return response()->json('Whoops Something went wrong. Please try again.');
					
				}
			}
			else{
				return response()->json('Insufficient Balance');
			}
        }
		else
		{
			return response()->json('Your request can not be processed');
		}	
		
	}

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
