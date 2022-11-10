<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Requests\MoneyLoanRequest;
use App\MoneyLoan;
use App\Balance;
use App\Report;
use DB;
use Auth;
use Exception;
class MoneyLoanController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(MoneyLoanRequest $request)
    {
		//print_r($request->all());die;
		$loanEmiList = $request->loanEmiList;
		$loanAmount = $request->loan_amount;
		$loanAccountNumber = $request->loan_acc_no;
		$chargeAmount = 10;
		$debitAmount = $loanAmount + $chargeAmount;
		$logginedUserBalance = Balance::select('user_balance')->where('user_id',Auth::id())->first();
		if($logginedUserBalance->user_balance > $debitAmount)
		{
			DB::beginTransaction();
			try
			{
				Balance::where('user_id',Auth::id())->decrement('user_balance',$debitAmount);
				Report::create([
							'number' => $loanAccountNumber,
							'provider_id' => 41,
							'offline_services_id' => $loanEmiList,
							'amount' => $loanAmount,
							'profit' => $chargeAmount,
							'api_id' => 19,
							'ip_address'=> \Request::ip(),
							'status_id' => 3,
							'description' => 'LOAN EMI',
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
	 public function paymentStore(MoneyLoanRequest $request)
    {
        $InsurancePayment = $request->InsurancePayment;
        $PremiumAmount = $request->loan_amount;
        $PolicyNumber = $request->loan_acc_no;
        $chargeAmount = 10;
        $debitAmount = $PremiumAmount + $chargeAmount;
        $logginedUserBalance = Balance::select('user_balance')->where('user_id',Auth::id())->first();
        if($logginedUserBalance->user_balance > $debitAmount)
        {
            DB::beginTransaction();
            try
            {
                Balance::where('user_id',Auth::id())->decrement('user_balance',$debitAmount);
                Report::create([
                            'number' => $PolicyNumber,
                            'provider_id' => 41,
                            'loan_emi_id' => $InsurancePayment,
                            'amount' => $PremiumAmount,
                            'profit' => $chargeAmount,
                            'api_id' => 20,
                            'ip_address'=> \Request::ip(),
                            'status_id' => 3,
                            'description' => 'LOAN EMI',
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

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function cabelStore(MoneyLoanRequest $request)
    {
        $CabelPayment = $request->CabelPayment;
        $Amount = $request->loan_amount;
        $CustomerId = $request->loan_acc_no;
        $chargeAmount = 10;
        $debitAmount = $Amount + $chargeAmount;
        $logginedUserBalance = Balance::select('user_balance')->where('user_id',Auth::id())->first();
        if($logginedUserBalance->user_balance > $debitAmount)
        {
            DB::beginTransaction();
            try
            {
                Balance::where('user_id',Auth::id())->decrement('user_balance',$debitAmount);
                Report::create([
                            'number' => $CustomerId,
                            'provider_id' => 41,
                            'loan_emi_id' => $CabelPayment,
                            'amount' => $Amount,
                            'profit' => $chargeAmount,
                            'api_id' => 21,
                            'ip_address'=> \Request::ip(),
                            'status_id' => 3,
                            'description' => 'Cabel',
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
