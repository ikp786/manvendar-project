<?php

namespace App\Http\Controllers;


use App\Http\Requests;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Company;
use Validator;

class CompanyController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
		if(Auth::user()->role_id == 1 || Auth::user()->role_id == 19)
		{
			$company = Company::where('id', 1)->first();
			if ($company) {
				return view('admin.company')->with('company', $company);
			}else{
				return "Not Permission to change";
		}
	 }
		return "Not permission";
    }

    public function update(Request $request,$id)
    {
		
		if(Auth::user()->role_id == 1 || Auth::user()->role_id == 19)
		{
			$company = Company::find($id);
			$company->company_name = $request->company_name;
			$company->company_email = $request->company_email;
			$company->company_address =  $request->company_address;
			$company->company_phone =  $request->company_phone;
			$company->company_mobile =  $request->company_mobile;
			$company->news =  $request->news;
			$company->recharge_news =  $request->recharge_news;
			$company->save();
			$message = "Company Deatail Updated";
			\Session::flash('message', $message);
			return back();
		}
		return view('errors.page-not-found');

    }

    public function store(Request $request)
    {
        $rules = array('company_name' => 'required',
            'company_email' => 'required|email',
            'company_website' => 'required|unique:companies',
        );

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return redirect('/company')
                ->withInput()
                ->withErrors($validator);

        } else {
            $user = Auth::id();
            Company::create(['company_name' => $request->company_name, 'user_id' => $user, 'company_email' => $request->company_email, 'company_phone' => $request->company_phone, 'company_mobile' => $request->company_mobile, 'company_website' => $request->company_website, 'company_address' => $request->company_address]);
            \Session::flash('flash_message', 'successfully saved.');
            return redirect('/company');
        }

    }
}
