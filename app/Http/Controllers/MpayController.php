<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Masterbank;
use App\Yesmasterbank;
use App\Company;
use SimpleXMLElement;
use GuzzleHttp\Client;
use SoapClient;
use DOMDocument;
use App\User;
use App\Api;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class MpayController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
   

    public function __construct()
    {
        $this->middleware('auth');
    }
    public function index()
    {
		if(Auth::user()->role_id == 5 && Auth::user()->company->money_one==1 )
		{
			$verify_code = Api::select('api_name')->where('acc_verify_id',1)->first();
			$ifsc = Masterbank::where(['bank_status'=>0,'saral'=>0])->get();
			$down_bank_lists='';
			foreach($ifsc as $ifs)
			{
				$down_bank_lists = $down_bank_lists.$ifs->bank_name.", ";
			}
			$c_id = Auth::user()->company_id;
			$updown = Company::where('id',$c_id)->first();
			 $netbanks = Masterbank::lists('bank_name','bank_code');
			return view('mpay.super', compact('netbanks','updown','ifsc','verify_code','down_bank_lists'))->with('user_id',Auth::id())->with('show', 1);
			
			//return view('mpay.epay', compact('netbanks','updown','ifsc','verify_code','down_bank_lists'))->with('user_id',Auth::id())->with('show', 1);
        }
        
        else
        {
         return "Not Permission";   
        }
    }
    public function payTest()
    {
        if(Auth::user()->role_id == 5 && Auth::user()->company->money_one==1){
            $verify_code = Api::select('api_name')->where('acc_verify_id',1)->first();
            $ifsc = Masterbank::where(['bank_status'=>0,'saral'=>0])->get();
            $down_bank_lists='';
            foreach($ifsc as $ifs)
            {
                $down_bank_lists = $down_bank_lists.$ifs->bank_name.", ";
            }
            $c_id = Auth::user()->company_id;
            $updown = Company::where('id',$c_id)->first();
            $netbanks = Masterbank::select('bank_name','bank_code')->orderBy('bank_name','asc')->get();
            return view('mpay.paytest', compact('netbanks','updown','ifsc','verify_code','down_bank_lists'))->with('user_id',Auth::id())->with('show', 1);
        }
        else
        {
         return "Not Permission";   
        }
    }
    public function check_money_status(){
        return view('check_money_status');
    }
    public function refund_request(){
        return view('mpay.refund_request');
    }
   
}
