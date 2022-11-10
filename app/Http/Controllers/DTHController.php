<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use Response;

class DTHController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
   public function index(Request $request)
   {
   $url="http://api.datayuge.in/V1/dthplans?apikey=6iCLDRdpZv34lyo56QC4zFrJ12G0hFzJ&operatorid=Dish TV&recharge_type=3 Month Packs&order_by=recharge_type&limit=50&flow=ASC&page=1";
      $curl = curl_init();
            curl_setopt($curl, CURLOPT_URL, $url);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
            //$data = curl_exec($curl);
            // $doc = new \DOMDocument();
            /* $doc->loadXML($data);*/
          /*  $ResponseCode = $doc->getElementsByTagName("NODES")->item(1);
            $message =  $doc->getElementsByTagName("MSG")->item(1);
            $status =  $doc->getElementsByTagName("STATUS")->item(1);
            if($status=='Success')
            {
                Report::where('id',$request->id)->update(['status_id'=>1]);

                return Response::json(['status_id'=>1,'message'=>'Success']);
            }
            elseif($status=='failed')
            {
                $report = Report::find($request->id);
                $report->status_id=2;
                $report->refund=1;
                $report->save();
                return Response::json(['status_id'=>2,'message'=>'Failed']);
            }
            else
            {
                return Response::json(['status_id'=>2,'message'=>$message]);
            }*/
			return $url;
    
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
    public function store(Request $request)
    {
        //
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
