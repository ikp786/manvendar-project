<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\State;
use App\User;
use Auth;

class SubUserController extends Controller
{
    public function viewSubUser(Request $request)
	{
		if(Auth::user()->role_id ==5)
		{
			$state = new State();
			$state_list = $state->stateList();
			$users=User::where('parent_id',Auth::id())->simplePaginate();
			return view('agent.sub-user',compact('state_list','users'));
		}
		return view('errors.permission-denied');
	}
}
