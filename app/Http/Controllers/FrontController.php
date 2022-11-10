<?php

namespace App\Http\Controllers;

use App\Provider;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Report;
use App\User;
use App\Apiresponse;
use App\Http\Controllers\Controller;
use App\Traits\AmountInWords;
use App\Traits\CustomTraits;

class FrontController extends Controller {

use AmountInWords,CustomTraits;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {
        $provider = Provider::lists('provider_name', 'id');
        return view('welcome', compact('provider'));
    }

    public function invoice($id)
	{
	    $report = Report::find($id);
	    
	    // echo '<br/> api_id ='.$report->api_id;
	    // echo '<br/> service_id ='.$report->provider->service_id;
	     
       // return view('invoice.electricity')->with('report',$report);
// echo $report->id;
		if(in_array($report->api_id,array(1,8)) && $report->provider->service_id!=6)
		{
		    return view('invoice.recharge')->with('report',$report); 
		}
		elseif(in_array($report->api_id,array(1,8,24,27)) && $report->provider->service_id==6)
		{
			return view('invoice.electricity')->with('report',$report);
		}
		elseif(in_array($report->api_id,array(19,20,21,22,23,24,26)))
		{  
			return view('invoice.offline')->with('report', $report);
		}
        else
		{
			if($report->api_id == 5 || $report->api_id == 25)
			{	
				// $this->checkDMTTwoStatus($report);
				$report = Report::find($report->id);
			}
			elseif($report->api_id==10)
				return view('invoice.aeps')->with('report', $report);
			return view('invoice.index')->with('report', $report);
		}
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create() {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request) {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */


    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id) {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id) {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id) {
        //
    }
	public function amountInWords(Request $request)
	{
		$amount = $request->amount;
		return response()->json(array($this->displayAmountInWords($amount)));
	}

}
