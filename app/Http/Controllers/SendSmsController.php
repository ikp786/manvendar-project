<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use App\Role;
use Auth;
use App\Traits\CustomTraits;
class SendSmsController extends Controller
{
    use CustomTraits;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if(Auth::user()->role_id==1){
            $roles = Role::whereIN('id', [3,4,5,])->pluck('role_title', 'id');
            $userDetails = User::selectRaw('name,mobile,id,prefix,role_id')->where('status_id',1)->where('role_id','!=',1)->get();
            $users=array();
            foreach($userDetails as $user)
            {
                $users[$user->mobile]=$user->name.' '.'('.$user->role->role_title.')';
            }
           return view('admin.sms.index',compact('users','roles'));
        }  
        return "Do your Own Work";
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function sendSmsMemberWise(Request $request)
    {
        //dd($request->all());die();
        $mobile=$request->user;
        $message=$request->message;
        $message=urlencode($message);

        CustomTraits::sendSMS($mobile,$message,1);
        return redirect()->back()->with('message','Message Sent Successfully');
        /*for Ajax*/
       //return response()->json(['status_id'=>1,'message'=>"Sms Sent Successfully"]);
    }

    public function sendSmsRoleWise(Request $request)
    {
        if(Auth::user()->role_id==1)
        {
            $roleId=$request->role_id;
            $message=$request->message;
            $users=User::where('role_id',$roleId)->where('status_id',1)->pluck('mobile','id')->toArray();
			$message=urlencode($message);
            if(count($users))
            {
                foreach ($users as $key => $value) {
                    CustomTraits::sendSMS($value, $message,1);
                }  
            }
           return redirect()->back()->with('message','Message Sent Successfully');
       }
    }

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
