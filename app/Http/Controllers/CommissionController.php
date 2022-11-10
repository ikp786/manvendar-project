<?php

namespace App\Http\Controllers;

use App\User;
use App\Upscheme;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Validator;
use Response;
use App\Commission;
use App\Moneycommission;
use App\PremiumWalletScheme;
use App\AepsScheme;
use App\Scheme;
use App\Slab;
use DB;
use App\Provider;
use App\Rechargeprovider;
use App\ImpsWalletScheme;
use App\VerificationScheme;
use App\BillScheme;
use App\Company;
use App\AdhaarpayScheme;
use Illuminate\Support\Facades\Input;
use App\Http\Requests;
use App\Http\Controllers\Controller;

class CommissionController extends Controller
{
    public function index()
    {
        $commissions = Commission::all();
        $providers = Provider::all();
        return view('admin.viewcommission', compact('commissions', 'providers'));
    }

    public function store(Request $request)
    {
		$scheme_id = $request->scheme_id;
		$errorMessage = array();
		$isError=false;
		foreach ($request->input('commission_value') as $key => $value) 
		{
			/* DB::table('commissions')
                ->where('id', $key)
                ->update($value); */
			$value['is_error']=0;
            if((float)$value['purchage_cast'] !=  (float)((float)$value['admin']+  (float)$value['md'] + (float)$value['d'] +(float)$value['r']))
			{
				if($isError==false)
					$isError=true;
				//$errorMessage[$key]="Error". $value['purchage_cast'] .  ($value['admin']+$value['md'] + $value['d'] +$value['r']);
				$errorMessage[$key]="Error";
				$value['is_error']=1;
			}
			DB::table('commissions')
                ->where('id', $key)
                ->update($value);
        }
		if($isError)
		{
			$inserID = Commission::where('scheme_id', $scheme_id)->get();
			return view('admin.viewcommission', compact('errorMessage'))->with('scheme_id', $scheme_id)->with('inserID', $inserID);
		}
        /* foreach ($request->input('commission_value') as $key => $value) {
            DB::table('commissions')
                ->where('id', $key)
                ->update($value);
        } */
        $inserID = Commission::where('scheme_id', $scheme_id)->get();
        return view('admin.viewcommission')->with('scheme_id', $scheme_id)->with('inserID', $inserID);

    }

    public function moneystore(Request $request)
    {
        $scheme_id = $request->scheme_id;
        foreach ($request->input('commission_value') as $key => $value) {
            DB::table('moneycommissions')
                ->where('id', $key)
                ->update($value);
        }
        $inserID = Moneycommission::where('moneyscheme_id', 1)->get();
        return view('admin.moneyssviewcommission')->with('inserID', $inserID);

    }

    public function view(Request $request)
    {
        $id = $request->input('id');
        $scheme = Scheme::find($id);
        return $scheme;
    }
    public function up_front_commission(){
        return view('admin.up_front_commission');
    }
    public function up_commission(Request $request){
        $upscheme = $request->upscheme;
        Upscheme::create(['scheme' => $upscheme]);
        return redirect('up-front-commission');
    }
	
    public function viewupdate(Request $request)
    {
        $scheme_name=$request->input('scheme_name');
        $providers = Provider::orderBy('id','ASC')->where('service_id', '!=',0)->get();
		$id = $request->input('id');
       // $scheme = Scheme::find($id);
        $countCommission = Commission::where('scheme_id', $id)->count();
        $inserID = array();
        if ($countCommission == 0) 
		{
			$commission = Commission::where('scheme_id',1)->get();
			if(count($commission))
			{
				foreach ($commission as $prov) 
				{
					$myid = Commission::create([
									'scheme_id' => $id, 
									'user_id' => Auth::id(), 
									'rechargeprovider_id' => $prov->rechargeprovider_id,
									'provider_id' => $prov->provider_id,
									'max_commission' => 0,
									'purchage_cast' => 0,
									'admin' => 0,
									'type' => 0,
									'md' => 0,
									'd' => 0,
									'r' => 0,
									]);
					
				}
			}
            else
			{
				foreach ($providers as $prov) 
				{
					$myid = Commission::insertGetId(['scheme_id' => $id, 'user_id' => Auth::id(), 'provider_id' => $prov->id]);
					$inserID[] = $myid;
				}
			}
            $inserID = Commission::where('scheme_id', $id)->get();
            return view('admin.viewcommission', compact('providers','scheme_name'))->with('scheme_id', $id)->with('inserID', $inserID);
        } else {
           $inserID = Commission::orderBy('id','ASC')->where('scheme_id', $id)->get();
            return view('admin.viewcommission', compact('providers','scheme_name'))->with('scheme_id', $id)->with('inserID', $inserID);
        }
    }

    public function moneyviewupdate(Request $request)
    {
            $id = $request->input('id');
            $inserID = Moneycommission::orderBy('id','ASC')->where('moneyscheme_id', $id)->get();
            return view('admin.moneyviewcommission')->with('moneyscheme_id', $id)->with('inserID', $inserID);   
    }
    public function updateslab(Request $request){
        $providers = array('1' => 'Slab <1000', '2' => 'Slab 1001-2000', '3' => 'Slab 2001-3000', '4' => 'Slab 3001-4000', '5' => 'Slab 4001-5000', '6' => 'Slab 5001-10000', '7' => 'Slab 10001-25000', '8' => 'Slab 25001-50000');
        $id = $request->input('id');
        $countCommission = Slab::where('scheme_id', $id)->count();
        $inserID = array();
        if ($countCommission == 0) {
            foreach ($providers as $key => $prov) {
                $myid = Slab::insertGetId(['scheme_id' => $id, 'user_id' => Auth::id(), 'slab_id' => $key]);
                $inserID[] = $myid;
            }
            $inserID = Commission::where('scheme_id', $myid)->get();
            return view('admin.slabcommission', compact('providers'))->with('scheme_id', $id)->with('inserID', $inserID);
        } else {
            $inserID = array('1' => 'Slab <1000', '2' => 'Slab 1001-2000', '3' => 'Slab 2001-3000', '4' => 'Slab 3001-4000', '5' => 'Slab 4001-5000', '6' => 'Slab 5001-10000', '7' => 'Slab 10001-25000', '8' => 'Slab 25001-50000');
            return view('admin.slabcommission')->with('scheme_id', $id)->with('inserID', $inserID);
        }
    }
    public function storeslab(Request $request)
    {
		print_r($request->all());die;
        $scheme_id = $request->scheme_id;
        foreach ($request->input('commission_value') as $key => $value) {
            DB::table('slabs')
                ->where('id', $key)
                ->update($value);
        }
        $inserID = Commission::where('scheme_id', $scheme_id)->get();
        return view('admin.viewcommission', compact('providers'))->with('scheme_id', $scheme_id)->with('inserID', $inserID);

    }

    public function update(Request $request, $id)
    {
        $scheme = Scheme::find($id);
        $scheme->scheme_name = $request->scheme_name;
        $scheme->save();
        return $scheme;
    }
    public function my_profit(){
        $id = User::find(Auth::id());
        $scheme_id = $id->scheme_id;
        $inserID = Commission::where('scheme_id', $scheme_id)->get();
        return view('profit.view_commission',compact('inserID'));
    }
	public function commissionShift(Request $request, $user_id)
	{
		if(Auth::user()->role_id == 1)
		{
			
			$commission_records = User::whereIn('role_id',[3,4])->select('id','name','email','mobile','role_id','status_id','parent_id','balance_id')->get();
			 return view('profit.view_commission',compact('inserID'));
			
		}
	}
	public function getCommissionAmount()
	{
		if(Auth::user()->role_id == 1)
		{
			$commission_records = User::whereIn('role_id',[3,4])->select('id','name','email','mobile','role_id','status_id','parent_id','balance_id')->orderBy('id','desc')->get();
			 return view('admin.commission.view-commssion-amount',compact('commission_records'));
		}
		return view('errors.page-not-found');
	}
	public function dmtViewScheme(Request $request)
    {
		//print_r($request->all());die;
            $id = $request->input('id');
            $schemeName = $request->input('schemeName');
			$schemeFor = $request->scheme_for;
			if($schemeFor == 3)
			{
				$walletSchemeDetails = PremiumWalletScheme::orderBy('id','ASC')->where('wallet_scheme_id', $id)->get();
				
			}
			
			//print_r($walletSchemeDetails);die;
			return view('admin.dmt-scheme.wallet-scheme-view',compact('walletSchemeDetails','schemeName','schemeFor'))->with('wallet_scheme_id', $id);
        
    }
    
    public function a2zPlusViewScheme(Request $request)
    {
		//print_r($request->all());die;
            $id = $request->input('id');
            $schemeName = $request->input('schemeName');
			$schemeFor = $request->scheme_for;
			if($schemeFor == 4)
			{
				$walletSchemeDetails = PremiumWalletScheme::orderBy('id','ASC')->where('wallet_scheme_id', $id)->get();
				
			}
			
			//print_r($walletSchemeDetails);die;
			return view('admin.dmt-scheme.a2z-plus-scheme-view',compact('walletSchemeDetails','schemeName','schemeFor'))->with('wallet_scheme_id', $id);
        
    }
    
	public function verificationViewScheme(Request $request)
    {
		
            $id = $request->input('id');
            $schemeName = $request->input('schemeName');
			$schemeFor = $request->scheme_for;
			if($schemeFor == 6)
			{
				$walletSchemeDetails = VerificationScheme::orderBy('id','ASC')->where('wallet_scheme_id', $id)->get();
				return view('admin.verification-scheme.veri-scheme-view',compact('walletSchemeDetails','schemeName','schemeFor'))->with('wallet_scheme_id', $id);
				
			}
			
        
    }
	public function impsViewScheme(Request $request)
    {
		//print_r($request->all());die;
            $id = $request->input('id');
            $schemeName = $request->input('schemeName');
			$schemeFor = $request->scheme_for;
			$walletSchemeDetails = ImpsWalletScheme::orderBy('id','ASC')->where('wallet_scheme_id', $id)->get();
			//print_r($walletSchemeDetails);die;
			return view('admin.imps-scheme.wallet-scheme-view',compact('walletSchemeDetails','schemeName','schemeFor'))->with('wallet_scheme_id', $id);
        
    }
	public function aepsViewScheme(Request $request)// Aeps
    {
		//print_r($request->all());die;
            $id = $request->input('id');
            $schemeName = $request->input('schemeName');
			$schemeFor = $request->scheme_for;
			$walletSchemeDetails = AepsScheme::orderBy('id','ASC')->where('wallet_scheme_id', $id)->get();
			//print_r($walletSchemeDetails);die;
			return view('admin.aeps-scheme.aeps-scheme-view',compact('walletSchemeDetails','schemeName','schemeFor'))->with('wallet_scheme_id', $id);
        
    }
    public function adhaarPayViewScheme(Request $request)// Aeps
    {
		//print_r($request->all());die;
            $id = $request->input('id');
            $schemeName = $request->input('schemeName');
			$schemeFor = $request->scheme_for;
			$walletSchemeDetails = AdhaarpayScheme::orderBy('id','ASC')->where('wallet_scheme_id', $id)->get();
			//print_r($walletSchemeDetails);die;
			return view('admin.aeps-scheme.adhaar-pay-scheme-view',compact('walletSchemeDetails','schemeName','schemeFor'))->with('wallet_scheme_id', $id);
        
    }
	public function billViewScheme(Request $request)
    {
		$id = $request->input('id');
		$schemeName = $request->input('schemeName');
		$schemeFor = $request->scheme_for;
		$walletSchemeDetails = BillScheme::orderBy('id','ASC')->where('wallet_scheme_id', $id)->get();
		return view('admin.bill-scheme.bill-scheme-view',compact('walletSchemeDetails','schemeName','schemeFor'))->with('wallet_scheme_id', $id);
    }
}
