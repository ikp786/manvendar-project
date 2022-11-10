<?php

namespace App\Http\Middleware;

use Closure;
use Auth;
class LogoutAfterUserInactive
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {

        if (Auth::check())
        {
            if(Auth::user()->status_id == 0)
            {
                    if($request->ajax()){
                        Auth::logout();
                        return response()->json(['status'=>'logout']);
                    }

                Auth::logout();
                return redirect('/login')->with('alert-success', 'You are de-activated . Please Contact with Admin');
            }
        }
        return $next($request);
    }
}
