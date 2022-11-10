<?php

namespace App\Http\Controllers;

use App\Holiday;
use Auth;
use Validator;
use Response;
use Illuminate\Http\Request;

class HolidayController extends Controller
{
    //
    public function index(){
        $title ='Holiday List';
        $holidayList = Holiday::orderBy('id','desc')->simplePaginate(30);
        return view('admin.holidays.holiday',compact('holidayList','title'));
    }

    public function store(Request $request){

        $rules = array(
            'name' => 'required',
            'holiday_date' => 'required',
            'message_first' => 'required',
            'message_second' => 'required',
        );
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return Response::json(array(
                'success' => false,
                'errors' => $validator->getMessageBag()->toArray()
            )); // 400 being the HTTP code for an invalid request.
        }
        Holiday::create($request->all());
        return response()->json([
            'status' => 'success',
            'message' => 'Holiday added successfully!'
        ]);

    }
    public function update(Request $request,$holidayId){


        if(Auth::user()->role_id == 1){


            $existCompany=Holiday::find($request->id);
            $rules = array(
                'name' => 'required',
                'holiday_date' => 'required',
                'message_first' => 'required',
                'message_second' => 'required',
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
            Holiday::where('id',$holidayId)->update($request->all());
            return Response::json(array(
                'status' => 'success',
                'message' => "Update Successfully",
            ));

        }
        else{
            return response()->json(
                array(
                    'status'=>'0',
                    'message'=>'Access Denied'
                )
            );
        }

    }
    public function view(Request $request,$id)
    {
        //print_r($id);exit;
        return response()->json(['status'=>1,'details'=>Holiday::find($id)]);
    }
}
