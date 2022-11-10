<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\ActionOtpVerification;
use Auth;
use Validator;
use Response;

class ActionOtpVerificationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if(Auth::user()->role_id==1){

            $values=ActionOtpVerification::orderBy('id', 'ASC')->get();
                return view('admin.otp-verify.index',compact('values'));
        }
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
        $rules = array(
            'mobile' =>'required|numeric|regex:/^[0-9]+$/|unique:action_otp_verifications',
            );
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return Response::json(array(
                'success' => false,
                'errors' => $validator->getMessageBag()->toArray()
            )); // 400 being the HTTP code for an invalid request.
        }
        ActionOtpVerification::create($request->all());
        return response()->json([
        'status' => 'success',
        'message' => 'Number added successfully!'
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return response()->json(['status'=>1,'details'=>ActionOtpVerification::find($id)]);
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
    public function update(Request $request)
    {
       
        $rules = array(
            'mobile' =>'required|numeric|unique:action_otp_verifications,mobile,'.$request->id,
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
        ActionOtpVerification::where('id',$request->id)->update($request->all());
        return Response::json(array(
                'status' => 'success',
                'message' => "Update Successfully",
            )); 
    }
    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
      $res = ActionOtpVerification::find($id)->delete();
        if ($res){
            $data=['status'=>'1', 'message'=>'Delete Successfully' ];
        }
        else{
            $data=['status'=>'0','message'=>'fail'];
        }
          return response()->json($data);
    }
}
