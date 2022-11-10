<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\WalletScheme;
use App\AepsScheme;
use App\AdhaarpayScheme;

use App\PremiumWalletScheme;
use App\PremiumKycScheme;
use App\ImpsWalletScheme;
use App\BillScheme;
use App\InstantPayWalletScheme;
use App\VerificationScheme;
use App\Http\Requests;
use Validator;
use Response;
use Auth;
use DB;
class WalletSchemeController extends Controller
{
    //
	public function getAllImpsScheme()//DG1
	{
		$impsSchemeLists = WalletScheme::where('scheme_for',1)->get();
		return view('admin.imps-scheme.imps-scheme-list',compact('impsSchemeLists'));
		
	}
	
	public function getAllDMTTwoScheme()//IMPS1
	{
		if(Auth::user()->role_id==1 || Auth::user()->role_id==19)
		{
			$dmtSchemeList = WalletScheme::whereIn('scheme_for',[3])->get();
			return view('admin.dmt-scheme.dmt-schem-list',compact('dmtSchemeList'));
		}
	}
	
	public function getAllA2zPlusScheme()//A2Z Plus Scheme
	{
		if(Auth::user()->role_id==1 || Auth::user()->role_id==19)
		{
			$dmtSchemeList = WalletScheme::whereIn('scheme_for',[4])->get();
			return view('admin.dmt-scheme.a2z-plus-scheme-list',compact('dmtSchemeList'));
		}
	}
	
	public function getAllAepsScheme()//AEPS
	{
		if(Auth::user()->role_id==1 || Auth::user()->role_id==19)
		{
			$dmtSchemeList = WalletScheme::whereIn('scheme_for',[7])->get();
			return view('admin.aeps-scheme.aeps-scheme-list',compact('dmtSchemeList'));
		}
	}
	
	public function getAllAdhaarPayScheme()//AEPS
	{
		if(Auth::user()->role_id==1 || Auth::user()->role_id==19)
		{
			$dmtSchemeList = WalletScheme::whereIn('scheme_for',[8])->get();
			return view('admin.aeps-scheme.adhaar-pay-scheme-list',compact('dmtSchemeList'));
		}
	}
	
	public function getInstantPayScheme()//IMPS1
	{
		if(Auth::user()->role_id==1 || Auth::user()->role_id==19)
		{
			$impsSchemeLists = WalletScheme::where('scheme_for',2)->get();
			return view('admin.dg3-scheme.dg3-scheme-list',compact('impsSchemeLists'));
		}
	}public function getBillScheme()//IMPS1
	{
		if(Auth::user()->role_id==1 || Auth::user()->role_id==19)
		{
			$impsSchemeLists = WalletScheme::where('scheme_for',5)->get();
			return view('admin.bill-scheme.bill-scheme-list',compact('impsSchemeLists'));
		}
	}
	public function getVerificationScheme()//Verification
	{
		if(Auth::user()->role_id==1 || Auth::user()->role_id==19)
		{
			$dmtSchemeList = WalletScheme::whereIn('scheme_for',[6])->get();
			return view('admin.verification-scheme.veri-scheme-list',compact('dmtSchemeList'));
		}
	}
	public function createWalletSchemeBkp(Request $request)
	{
		$rules = array(
            'name' => 'required|unique:wallet_schemes',
		);
		$validator = Validator::make($request->all(), $rules);
		if ($validator->fails()) {
			return Response::json(array(
				'success' => false,
				'errors' => $validator->getMessageBag()->toArray()
			));
		}
		print_r($request->all());die;
		if($request->scheme_for == 3)// IMPS1 Tramo
			$walletScheme = PremiumWalletScheme::where('wallet_scheme_id',1)->get();
		else if($request->scheme_for == 1)//DG1
			$walletScheme = ImpsWalletScheme::where('wallet_scheme_id',3)->get();
		
		if(count($walletScheme))
		{
			DB::beginTransaction();
			try
			{
				$newSchemes = WalletScheme::create(['name'=>$request->name,'scheme_for'=>$request->scheme_for]);
				/* foreach($walletScheme as $key =>$value)
				{
					if($request->scheme_for == 1)
					{
						PremiumWalletScheme::create([
											'min_amt'=>$value->min_amt,
											'max_amt'=>$value->max_amt,
											'agent_charge'=>$value->agent_charge,
											'dist_comm'=>$value->dist_comm,
											'md_comm'=>$value->md_comm,
											'admin_comm'=>$value->md_comm,
											'wallet_scheme_id'=>$newSchemes->id,
											]);
					}
					else if($request->scheme_for == 2)
					{
						PremiumKycScheme::create([
											'min_amt'=>$value->min_amt,
											'max_amt'=>$value->max_amt,
											'agent_charge'=>$value->agent_charge,
											'dist_comm'=>$value->dist_comm,
											'md_comm'=>$value->md_comm,
											'admin_comm'=>$value->admin_comm,
											'wallet_scheme_id'=>$newSchemes->id,
											]);
						
					}
					else
					{
						ImpsWalletScheme::create([
											'min_amt'=>$value->min_amt,
											'max_amt'=>$value->max_amt,
											'agent_charge'=>$value->agent_charge,
											'dist_comm'=>$value->dist_comm,
											'md_comm'=>$value->md_comm,
											'admin_comm'=>$value->admin_comm,
											'wallet_scheme_id'=>$newSchemes->id,
											]);
						
					}
				} */
				DB::commit();
				return response()->json(['message'=>"New Scheme Created Successfully"]);
			}
			catch(\Exception $e)
			{
				DB::rollback();
				return response()->json(['message'=>"Whoops! something went worng. Please try again"]);
			}
		}
		else{
			return response()->json(['message'=>"New Scheme Not Created"]);
		}

		
		return response()->json(['success', 'GST Scheme has been created']);
		
	}
	public function createWalletScheme(Request $request)
	{
		if(Auth::user()->role_id==1 || Auth::user()->role_id==19)
		{
			$rules = array(
				'name' => 'required|unique:wallet_schemes',
			);
			$validator = Validator::make($request->all(), $rules);
			if ($validator->fails()) {
				return Response::json(array(
					'success' => false,
					'errors' => $validator->getMessageBag()->toArray()
				));
			}
			DB::beginTransaction();
				try
				{
					$newSchemes = WalletScheme::create(['name'=>$request->name,'scheme_for'=>$request->scheme_for]);
					/* foreach($walletScheme as $key =>$value)
					{
						$data=[
							'min_amt'=>$value->min_amt,
							'max_amt'=>$value->max_amt,
							'charge_type'=>$value->charge_type,
							'agent_charge'=>$value->agent_charge,
							'agent_comm_type'=>$value->agent_comm_type,
							'agent_comm'=>$value->agent_comm,
							'dist_comm_type'=>$value->dist_comm_type,
							'dist_comm'=>$value->dist_comm,
							'md_comm_type'=>$value->md_comm_type,
							'md_comm'=>$value->md_comm,
							'ad_comm_type'=>$value->ad_comm_type,
							'admin_comm'=>$value->admin_comm,
							'wallet_scheme_id'=>$newSchemes->id,
							];
						if($request->scheme_for == 1)
							PremiumWalletScheme::create($data);// IMPS 1 Tramo 
						else if($request->scheme_for == 2)
							PremiumKycScheme::create($data);
						else if($request->scheme_for == 3)
							ImpsWalletScheme::create($data);// DG 1 Cyber Plat
										 }*/
					DB::commit();
					return response()->json(['message'=>"New Scheme Created Successfully"]);
				}
				catch(\Exception $e)
				{
					DB::rollback();
					return response()->json(['message'=>"Whoops! something went worng. Please try again"]);
				}
			}
	}
	public function createAepsScheme(Request $request)//Aeps
	{
		if(Auth::user()->role_id==1 || Auth::user()->role_id==19)
		{
			$rules = array(
				'name' => 'required|unique:wallet_schemes',
			);
			$validator = Validator::make($request->all(), $rules);
			if ($validator->fails()) {
				return Response::json(array(
					'success' => false,
					'errors' => $validator->getMessageBag()->toArray()
				));
			}
			DB::beginTransaction();
				try
				{
					$newSchemes = WalletScheme::create(['name'=>$request->name,'scheme_for'=>$request->scheme_for]);
					
					DB::commit();
					return response()->json(['message'=>"New Scheme Created Successfully"]);
				}
				catch(\Exception $e)
				{
					DB::rollback();
					return response()->json(['message'=>"Whoops! something went worng. Please try again"]);
				}
			}
	}
	public function updateWalletScheme(Request $request)
	{
		
		unset($request['_token']);
		$schemeFor = $request->schemeFor;
		unset($request['schemeFor']);
		$contents = $request->all();
		
		DB::beginTransaction();
		try
		{
		   // dd($contents);
			foreach($contents as $key => $content)
			{
				$isError = 0;
				
				// dd($content); die;
				
				if($key != "wallet_scheme_id")
				{
					$isError = 0;
					if($content['agent_charge'] !=($content['agent_comm']+$content['dist_comm']+$content['md_comm']+@$content['admin_comm']))
					{
						$isError = 1;
					}
					$data=['agent_charge_type'=>$content['agent_charge_type'],
								'agent_charge'=>$content['agent_charge'],
								'agent_comm_type'=>$content['agent_comm_type'],
								'agent_comm'=>$content['agent_comm'],
								'dist_comm_type'=>$content['dist_comm_type'],
								'dist_comm'=>$content['dist_comm'],
								'md_comm_type'=>$content['md_comm_type'],
								'md_comm'=>$content['md_comm'],
								'admin_comm_type'=>@$content['admin_comm_type'],
								'admin_comm'=>@$content['admin_comm'],
								'is_error'=>$isError
								];
								
				    if($schemeFor == 4)
						PremiumWalletScheme::where(['id'=>$key,'wallet_scheme_id'=>$request->wallet_scheme_id])->update($data); 
					else if($schemeFor == 3)
						PremiumWalletScheme::where(['id'=>$key,'wallet_scheme_id'=>$request->wallet_scheme_id])->update($data);
					else if($schemeFor == 2)
						InstantPayWalletScheme::where(['id'=>$key,'wallet_scheme_id'=>$request->wallet_scheme_id])->update($data);
				}
			}
			DB::commit();
			if($schemeFor == 4)
			    return redirect('a2z-plus-scheme')->withSuccess('Scheme Commission updated successfully');
			if($schemeFor == 3)
				return redirect('dmt-two-imps-scheme')->withSuccess('Scheme Commission updated successfully');
			if($schemeFor == 2)
				return redirect('dmt-two-imps-scheme')->withSuccess('DMT Two Scheme Commission updated successfully');
			return redirect('dmt-scheme')->withSuccess('Scheme Commission updated successfully');
		}
		catch(\Exception $e)
		{
			DB::rollback();
			throw $e;
			if($schemeFor == 4)
			    return redirect('a2z-plus-scheme')->withErrors('Scheme Commission updation failed');
			else    
			    return redirect('dmt-two-imps-scheme')->withErrors('Scheme Commission updation failed');
		}
	}
	public function updateImpsScheme(Request $request)
	{
		/* echo "IMPS";
		print_r($request->all());die; */
		unset($request['_token']);
		$schemeFor = $request->schemeFor;
		unset($request['schemeFor']);
		$contents = $request->all();
		DB::beginTransaction();
		try
		{
			foreach($contents as $key => $content)
			{
				$isError = 0;
				
				if($key != "wallet_scheme_id")
				{
					if($content['agent_charge'] !=($content['agent_comm']+$content['dist_comm']+$content['md_comm']+$content['admin_comm']))
				{
					$isError = 1;
				}
						ImpsWalletScheme::where(['id'=>$key,'wallet_scheme_id'=>$request->wallet_scheme_id])->update([
								'agent_charge_type'=>$content['agent_charge_type'],
								'agent_charge'=>$content['agent_charge'],
								'agent_comm_type'=>$content['agent_comm_type'],
								'agent_comm'=>$content['agent_comm'],
								'dist_comm_type'=>$content['dist_comm_type'],
								'dist_comm'=>$content['dist_comm'],
								'md_comm_type'=>$content['md_comm_type'],
								'md_comm'=>$content['md_comm'],
								'admin_comm_type'=>$content['admin_comm_type'],
								'admin_comm'=>$content['admin_comm'],
								'is_error'=>$isError,
								
								]);
					
				}
			}
			DB::commit();
			
			return redirect('dmt-imps-scheme')->withSuccess('Scheme Commission updated successfully');
		}
		catch(\Exception $e)
		{
			DB::rollback();
			throw $e;
			return redirect('dmt-imps-scheme')->withErrors('Scheme Commission updation failed');
		}
	}
	
	public function updateAdhaarPayScheme(Request $request) // AdhaarPay
	{ 
		unset($request['_token']);
		$schemeFor = $request->schemeFor;
		unset($request['schemeFor']);
		$contents = $request->all();
		DB::beginTransaction();
		try
		{
			foreach($contents as $key => $content)
			{
				$isError = 0;
				
				if($key != "wallet_scheme_id")
				{
					if($content['agent_charge'] !=($content['agent_comm']+$content['dist_comm']+$content['md_comm']+$content['admin_comm']))
				{
					$isError = 1;
				}
						AdhaarpayScheme::where(['id'=>$key,'wallet_scheme_id'=>$request->wallet_scheme_id])->update([
								'agent_charge_type'=>$content['agent_charge_type'],
								'agent_charge'=>$content['agent_charge'],
								'agent_comm_type'=>$content['agent_comm_type'],
								'agent_comm'=>$content['agent_comm'],
								'dist_comm_type'=>$content['dist_comm_type'],
								'dist_comm'=>$content['dist_comm'],
								'md_comm_type'=>$content['md_comm_type'],
								'md_comm'=>$content['md_comm'],
								'admin_comm_type'=>$content['admin_comm_type'],
								'admin_comm'=>$content['admin_comm'],
								'is_error'=>$isError,
								
								]);
					
				}
			}
			DB::commit();
			
			return redirect('adhaar-pay-scheme')->withSuccess('Scheme Commission updated successfully');
		}
		catch(\Exception $e)
		{
			DB::rollback();
			throw $e;
			return redirect('adhaar-pay-scheme')->withErrors('Scheme Commission updation failed');
		}
	}
	
	public function updateAepsScheme(Request $request)//Aeps
	{
		/* echo "IMPS";
		print_r($request->all());die; */
		unset($request['_token']);
		$schemeFor = $request->schemeFor;
		unset($request['schemeFor']);
		$contents = $request->all();
		DB::beginTransaction();
		try
		{
			foreach($contents as $key => $content)
			{
				$isError = 0;
				
				if($key != "wallet_scheme_id")
				{
					if($content['agent_charge'] !=($content['agent_comm']+$content['dist_comm']+$content['md_comm']+$content['admin_comm']))
				{
					$isError = 1;
				}
						AepsScheme::where(['id'=>$key,'wallet_scheme_id'=>$request->wallet_scheme_id])->update([
								'agent_charge_type'=>$content['agent_charge_type'],
								'agent_charge'=>$content['agent_charge'],
								'agent_comm_type'=>$content['agent_comm_type'],
								'agent_comm'=>$content['agent_comm'],
								'dist_comm_type'=>$content['dist_comm_type'],
								'dist_comm'=>$content['dist_comm'],
								'md_comm_type'=>$content['md_comm_type'],
								'md_comm'=>$content['md_comm'],
								'admin_comm_type'=>$content['admin_comm_type'],
								'admin_comm'=>$content['admin_comm'],
								'is_error'=>$isError,
								
								]);
					
				}
			}
			DB::commit();
			
			return redirect('aeps-scheme')->withSuccess('Scheme Commission updated successfully');
		}
		catch(\Exception $e)
		{
			DB::rollback();
			throw $e;
			return redirect('aeps-scheme')->withErrors('Scheme Commission updation failed');
		}
	}
	
	public function updateBillScheme(Request $request)
	{
		/* echo "IMPS";
		print_r($request->all());die; */
		unset($request['_token']);
		$schemeFor = $request->schemeFor;
		unset($request['schemeFor']);
		$contents = $request->all();
		DB::beginTransaction();
		try
		{
			foreach($contents as $key => $content)
			{
				if($key != "wallet_scheme_id")
				{
					
						BillScheme::where(['id'=>$key,'wallet_scheme_id'=>$request->wallet_scheme_id])->update([
								'agent_charge_type'=>$content['agent_charge_type'],
								'agent_charge'=>$content['agent_charge'],
								'agent_comm_type'=>$content['agent_comm_type'],
								'agent_comm'=>$content['agent_comm'],
								'dist_comm_type'=>$content['dist_comm_type'],
								'dist_comm'=>$content['dist_comm'],
								'md_comm_type'=>$content['md_comm_type'],
								'md_comm'=>$content['md_comm'],
								'admin_comm_type'=>$content['admin_comm_type'],
								'admin_comm'=>$content['admin_comm'],
								
								]);
					
				}
			}
			DB::commit();
			
			return redirect('bill-scheme-list')->withSuccess('Scheme Commission updated successfully');
		}
		catch(\Exception $e)
		{
			DB::rollback();
			return redirect('bill-scheme-list')->withErrors('Scheme Commission updation failed');
		}
	}public function updateVerificationScheme(Request $request)
	{
		/* echo "IMPS";
		print_r($request->all());die; */
		unset($request['_token']);
		$schemeFor = $request->schemeFor;
		unset($request['schemeFor']);
		$contents = $request->all();
		DB::beginTransaction();
		try
		{
			foreach($contents as $key => $content)
			{
				if($key != "wallet_scheme_id")
				{
					
						VerificationScheme::where(['id'=>$key,'wallet_scheme_id'=>$request->wallet_scheme_id])->update([
								'agent_charge_type'=>$content['agent_charge_type'],
								'agent_charge'=>$content['agent_charge'],
								'dist_comm_type'=>$content['dist_comm_type'],
								'dist_comm'=>$content['dist_comm'],
								'md_comm_type'=>$content['md_comm_type'],
								'md_comm'=>$content['md_comm'],
								'admin_comm_type'=>$content['admin_comm_type'],
								'admin_comm'=>$content['admin_comm'],
								
								]);
					
				}
			}
			DB::commit();
			
			return redirect('verification-scheme')->withSuccess('Verification Commission Scheme updated successfully');
		}
		catch(\Exception $e)
		{
			DB::rollback();
			throw $e;
			return redirect('verification-scheme')->withErrors('Verification Commission Scheme updation failed');
		}
	}
	public function getWalletSchemeName(Request $request)
	{
		$newSchemes = WalletScheme::where(['id'=>$request->id])->first();
		return response()->json(['status'=>1,'message'=>$newSchemes->name]);
	}
	public function updateWalletSchemeName(Request $request,$id)
	{
		WalletScheme::where('id',$request->id)->update(['name'=>$request->name,'scheme_for'=>$request->scheme_for]);
		return response()->json(['message'=>"Scheme Name updated successfully"]);
	}
	public function createNewDGOneRow(Request $request)
	{
		if(Auth::user()->role_id == 1 || Auth::user()->role_id==19)
		{
			ImpsWalletScheme::create($request->all());
			return redirect('dmt-imps-scheme')->withSuccess("New Record Created");
		}
	}
	public function createNewIMPSOneRow(Request $request)
	{
		if(Auth::user()->role_id == 1 || Auth::user()->role_id==19)
		{
			PremiumWalletScheme::create($request->all());
			return redirect('dmt-two-imps-scheme')->withSuccess("New Record Created");
		}
	}
	public function createNewVeriOneRow(Request $request)
	{
		if(Auth::user()->role_id == 1 || Auth::user()->role_id==19)
		{
			VerificationScheme::create($request->all());
			return redirect('verification-scheme')->withSuccess("New Record Created");
		}
	}
	public function createInstantPayOneRow(Request $request)
	{
		if(Auth::user()->role_id == 1 || Auth::user()->role_id==19)
		{
			InstantPayWalletScheme::create($request->all());
			return redirect('dg-three-scheme-list')->withSuccess("New Record Created");
		}
	}public function createBillOneRow(Request $request)
	{
		if(Auth::user()->role_id == 1 || Auth::user()->role_id==19)
		{
			BillScheme::create($request->all());
			return redirect('bill-scheme-list')->withSuccess("New Record Created");
		}
	}
	public function createVerificationOneRow(Request $request)
	{
		if(Auth::user()->role_id == 1 || Auth::user()->role_id==19)
		{
			//print_r($request->all());die;
			VerificationScheme::create($request->all());
			return redirect('verification-scheme')->withSuccess("New Record Created");
		}
	}
	public function createAepsOneRow(Request $request)
	{
		if(Auth::user()->role_id == 1 || Auth::user()->role_id==19)
		{
			//print_r($request->all());die;
			AepsScheme::create($request->all());
			return redirect('aeps-scheme')->withSuccess("New Record Created");
		}
	}
	
	public function createAdhaayPayOneRow(Request $request)
	{
		if(Auth::user()->role_id == 1 || Auth::user()->role_id==19)
		{
			//print_r($request->all());die;
			AdhaarpayScheme::create($request->all());
			return redirect('adhaar-pay-scheme')->withSuccess("New Record Created");
		}
	}
	
	public function deleteRow(Request $request)
	{
		if($request->schemeFor == 1)
			ImpsWalletScheme::destroy($request->recordId);
		elseif($request->schemeFor == 3)
			PremiumWalletScheme::destroy($request->recordId);
		elseif($request->schemeFor == 2)
			InstantPayWalletScheme::destroy($request->recordId);
		elseif($request->schemeFor == 5)
			BillScheme::destroy($request->recordId);
		elseif($request->schemeFor == 6)
			VerificationScheme::destroy($request->recordId);
		elseif($request->schemeFor == 7)
			AepsScheme::destroy($request->recordId);
		elseif($request->schemeFor == 8)
			AdhaarpayScheme::destroy($request->recordId);	
		else
			return response()->json(['status'=>1,'message'=>"Record not deletion failed"]);
		return response()->json(['status'=>1,'message'=>"Record Deleted Successfully"]);
	}
}
