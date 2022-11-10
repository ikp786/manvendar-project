<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\State;
use App\User;
use Auth;
use PDF;
class ProfileController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('profile.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function viewProfile()
    {
		$state = new State();
		$state_list = $state->stateList();
        return view('profile.agent-profile',compact('state_list'));
    }
	public function updateProfile()
    {
		$state = new State();
		$state_list = $state->stateList();
        return view('profile.agent-update-profile',compact('state_list'));
    }
	public function optOutOtp(Request $request)
	{
		if(User::where('id',Auth::id())->update(['opt'=>$request->opt]))
			return "Successfully Updated";
		return "Not updated";
	}

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
	public function download(Request $request)
    {
        $user=new user();
        $pdf = PDF::loadView('profile.download-certificate', $user);
        return $pdf->download('Certificate.pdf');
         //return view('profile.download-certificate');
    }
	
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
