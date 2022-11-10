<?php
namespace App\Http\Controllers;
use GuzzleHttp\RetryMiddleware;
use Illuminate\Http\Request;
use App\User;
use App\Member;
use App\Balance;
use App\Report;
use App\Http\Requests;
use Validator;
use Response;
use App\Loadcash;
use App\Upscheme;
use App\Company;
use App\Pmethod;
use App\Netbank;
use DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Exception;
use App\Traits\CustomTraits;
class TransferController extends Controller
{
	use CustomTraits;
    public function index(Request $request)
    {
         
        // dd($member_details);
         
		if(in_array(Auth::user()->role_id,array(1,3,4,19))){
		$usersQuery = User::orderBy('id', 'DESC');
		if($request->SEARCH_TYPE=="ID")
			$usersQuery->where('id','like','%'.trim($request->number) .'%');
		elseif($request->SEARCH_TYPE=="NAME")
			$usersQuery->where('name','like','%'.trim($request->number) .'%');
		elseif($request->SEARCH_TYPE=="MOB_NO")
			$usersQuery->where('mobile','like','%'.trim($request->number) .'%');
		elseif($request->SEARCH_TYPE=="COMPANY"){
		    $member_details = Member::where('company','like','%'.trim($request->number) .'%')->get(['id'])->toArray();
		    $users =  $usersQuery->whereIn('member_id', $member_details);
		}	
        if (Auth::user()->role_id == 1) 
		{
          $usersQuery->where('role_id',5);
			
        }
		elseif (Auth::user()->role_id == 3) 
		{
            $members = $this->getMdMember(Auth::id());
            $users =  $usersQuery->whereIn('parent_id', $members)->where('role_id',5);
        } 
		else 
		{
            $users = $usersQuery->where('parent_id', Auth::id());
        }
		$title="Retailer Fund Transfer";
        $users = $usersQuery->simplePaginate(40);
        return view('admin.fund_transfer_two', compact('users','title'));
		}
		return view('errors.permission-denied');
    }
	public function distFundList(Request $request)
	{
		$usersQuery = User::orderBy('id', 'DESC');
		if($request->SEARCH_TYPE=="ID")
				$usersQuery->where('id','like','%'.trim($request->number) .'%');
			elseif($request->SEARCH_TYPE=="NAME")
				$usersQuery->where('name','like','%'.trim($request->number) .'%');
			elseif($request->SEARCH_TYPE=="MOB_NO")
				$usersQuery->where('mobile','like','%'.trim($request->number) .'%');
		if(Auth::user()->role_id == 1 || Auth::user()->role_id == 19)
			$usersQuery->where('role_id',4);
		elseif(Auth::user()->role_id==3)
			$usersQuery->whereIn('id',$this->getDistributor(Auth::id()));
		else
			return view('errors.permission-denied');
		$title="Distributor Fund Transfer";
		$users = $usersQuery->simplePaginate(40);
		return view('admin.fund_transfer_two', compact('users','title'));
	}

	public function mdFundList(Request $request)
	{
		
		if(Auth::user()->role_id == 1 || Auth::user()->role_id == 19)
		{
			$usersQuery = User::orderBy('id', 'DESC');
			if($request->SEARCH_TYPE=="ID")
				$usersQuery->where('id','like','%'.trim($request->number) .'%');
			elseif($request->SEARCH_TYPE=="NAME")
				$usersQuery->where('name','like','%'.trim($request->number) .'%');
			elseif($request->SEARCH_TYPE=="MOB_NO")
				$usersQuery->where('mobile','like','%'.trim($request->number) .'%');
			$usersQuery->where('role_id',3);
			
			$users = $usersQuery->simplePaginate(40);
		}
		else
			return view('errors.permission-denied');
		$title="MD  Fund Transfer";
		return view('admin.fund_transfer_two', compact('users','title'));
	}
	public function apiUserFundList(Request $request)
	{
		
		if(Auth::user()->role_id == 1 || Auth::user()->role_id == 19)
		{
			$usersQuery = User::orderBy('id', 'DESC');
			if($request->SEARCH_TYPE=="ID")
				$usersQuery->where('id','like','%'.trim($request->number) .'%');
			elseif($request->SEARCH_TYPE=="NAME")
				$usersQuery->where('name','like','%'.trim($request->number) .'%');
			elseif($request->SEARCH_TYPE=="MOB_NO")
				$usersQuery->where('mobile','like','%'.trim($request->number) .'%');
			$usersQuery->where('role_id',7);
			
			$users = $usersQuery->simplePaginate(40);
		}
		else
			return view('errors.permission-denied');
		$title="Api User Fund Transfer";
		return view('admin.fund_transfer_two', compact('users','title'));
	}
    public function comm_transfer()
    {
        if(Auth::user()->role_id == 1 || Auth::user()->role_id == 19) 
		{
			$users = User::all();
			return view('admin.commission_transfer', compact('users'));
		}
    }
    public function fund_ret(Request $request)
    {
        if (Auth::user()->role_id == 1 || Auth::user()->role_id == 19) 
		{
			 $usersQuery = User::orderBy('id', 'DESC');
			if($request->SEARCH_TYPE=="ID")
				$usersQuery->where('id','like','%'.trim($request->number) .'%');
			elseif($request->SEARCH_TYPE=="NAME")
				$usersQuery->where('name','like','%'.trim($request->number) .'%');
			elseif($request->SEARCH_TYPE=="MOB_NO")
				$usersQuery->where('mobile','like','%'.trim($request->number) .'%');
				$usersQuery->where('role_id',5);
			$users = $usersQuery->simplePaginate(40);
			return view('admin.fund_transfer_two_ret', compact('users'));
        }
		return view('errors.page-not-found');
		
    } 
	public function distFundReturnList(Request $request)
    {
       
        if (Auth::user()->role_id == 1 || Auth::user()->role_id == 19) 
		{
			$urlName = \Route::current()->getName();
            $usersQuery = User::orderBy('id', 'DESC');
			if($request->SEARCH_TYPE=="ID")
				$usersQuery->where('id','like','%'.trim($request->number) .'%');
			elseif($request->SEARCH_TYPE=="NAME")
				$usersQuery->where('name','like','%'.trim($request->number) .'%');
			elseif($request->SEARCH_TYPE=="MOB_NO")
				$usersQuery->where('mobile','like','%'.trim($request->number) .'%');
			if($urlName == "dist-fund-return")
				$usersQuery->where('role_id',4);
			else if($urlName == "md-fund-return")
				$usersQuery->where('role_id',3);
			else if($urlName == "api-fund-return")
				$usersQuery->where('role_id',7);
				
			
			$users = $usersQuery->simplePaginate(40);
			return view('admin.fund_transfer_two_ret', compact('users'));
        }
		return view('errors.permission-denied');  
		
    }

    public function transfer(Request $request)
    {
       
        if ($request->walletid == 23 || $request->walletid == 24) {
            $walletid = $request->walletid;
            $mobile = $request->id;
            return $this->check_number($mobile, $walletid);
        }
    }

    public function store(Request $request)
    {
		$amount = $request->amount;
		if($amount >2000000)
		{
			return Response::json(array('success' => 'failure', 'message' => 'Amount can not be exceed 10 Lakhs'));
			exit;
		}
        $child_id = $request->user_id;
		$remark_content = $request->remark;
        $child_balance = Balance::find($child_id);
        
        $payment_id = $request->id;
		$dt_scheme = $request->dt_scheme;// Added By rajat
		$id = Auth::id();
        $my_id = Balance::where('user_id', $id)->first();

        if($request->wallet == 1)
		{
			$mybalance = Auth::user()->balance->user_balance;
		}else{
			$mybalance = Auth::user()->balance->user_balance;
		}
        $duplicatae = $this->checkDuplicatePayment($amount,$child_id);
		//dd($duplicatae);die();
        if($duplicatae['status'] == 2){
            $result[1] =  array('status'=>'Failure','message'=>"Duplicate Txn");
           // return response()->json(['result'=>$result]);
            return Response::json(array('status'=>'Failure','message'=>'Same amount already transfer to user. Please Try agian after 3 minute'));
        }
        if ($mybalance >= $amount && $amount >= 10) 
		{
            $md = $this->transfernow($id, $child_id, $amount, $payment_id,$remark_content, $request->wallet,$dt_scheme);
           if($md)
			    return Response::json(array('status' => 'success', 'message' => 'Amount Transfered Successfully'));
			else
				return Response::json(array('status' => 'failuar', 'message' => 'Oops Something went wrong'));
			   
        } else {
            return Response::json(array('status' => 'failure', 'message' => 'Low Balance, Please refill your Balance,  Or Requested Amount should be greated than 9 Thanks'));
        } 
    }
	
	public function checkDuplicatePayment($amount,$child_id)
    {
        $startTime = date("Y-m-d H:i:s");
        $formatted_date = date('Y-m-d H:i:s',strtotime('-180 seconds',strtotime($startTime)));
        $result = Report::select('amount','user_id','status_id','created_at')->where(['amount'=>$amount,'user_id'=>Auth::id(),'credit_by'=>$child_id,'status_id'=>6])->where('created_at','>=',$formatted_date)->orderBy('id','DESC')->first();
       //dd($result);die();
        if ($result) {
            return array('status'=>2,'message'=>'Same amount already transfer to user. Please Try agian after 3 minute.');
        } else {
            return array('status'=>0,'message'=>'not found');
        }
    }

    public function fund_return_account(Request $request)
    {
        $child_id = $request->user_id;
        $child_balance = Balance::find($child_id);
        $amount = $request->amount;
		$remark_content = $request->remark;
        $id = Auth::id();
        $my_id = Balance::where('user_id', $child_id)->first();
        $mybalance = $my_id->user_balance;
        if ($mybalance >= $amount && $amount >= 1) {
            $md = $this->returnnow($id, $child_id, $amount,$remark_content, $request->wallet);
			if($md)
				 return Response::json(array('success' => 'success', 'message' => 'Amount Successfully Reversed'));
			else
				 return Response::json(array('success' => 'failure', 'message' => 'Oops Something went Wrong'));
        } else {
            return Response::json(array('success' => 'failure', 'message' => 'Low Balance, Please refill your Balance, Thanks'));
        }
}

    function returnnow($id, $child_id, $amount,$remark_content, $wallet)
    {
        //update parent balance

        if (Auth::user()->role->id ==1 || Auth::user()->role_id == 19) {

            $authbalance = Auth::user()->balance->user_balance;
           
                $wallet_new = 'user_balance';
                $wallet_detail = 'Fund ';
            $now = new \DateTime();
            $datetime = $now->getTimestamp();
            $ctime = $now->format('Y-m-d H:i:s');
            $user_detail = User::find($child_id);
            // $upscheme = Upscheme::find($user_detail->upscheme);
            // if ($upscheme) {
            //     $schemenew = $upscheme->scheme;
            // }else{
            //     $schemenew = 0;
            // }
            // $amountper = ($amount * $schemenew)/100;
            // $amount = $amount - $amountper;
			DB::beginTransaction();
            try
            {
				Balance::where('user_id', $id)->increment('user_balance', $amount);
				$child_user_details = User::find($child_id);
				$admin_details = User::find($id);
				$insert_id = Report::insertGetId([
					'number' => $admin_details->mobile,
					'provider_id' => 0,
					'amount' => $amount,
					'api_id' => 0,
					'opening_balance' => $admin_details->balance->user_balance-$amount,
					'description' => "Amount widthdrawal From ". $child_user_details->name . '( ' . $child_user_details->mobile . ') '. $child_user_details->prefix .' '. $child_user_details->id,
					'status_id' => 7,
					'pay_id' => $datetime,
					'txnid' => 'DT Reversed',
					'total_balance' => $admin_details->balance->user_balance,
					'total_balance2' => $admin_details->balance->user_commission,
					'created_at' => $ctime,
					 'recharge_type' => $wallet,
					'user_id' => Auth::id(),
					'credit_by' => $child_id,
					'remark' => $remark_content
				]);
				Balance::where('user_id', $child_id)->decrement($wallet_new, $amount);
				$user_detail_p = User::find($child_id);
				$insert_id = Report::insertGetId([
					'number' => $user_detail_p->mobile,
					'provider_id' => 0,
					'amount' => $amount,
					'api_id' => 0,
					'description' => "Amount widthdrawal from  ". Auth::user()->name . '( '. Auth::user()->mobile . ') '. Auth::user()->prefix .' '. Auth::id(),
					'status_id' => 6,
					'pay_id' => $datetime,
					'opening_balance' => $user_detail_p->balance->user_balance+$amount,
					'txnid' => 'DT Reversed',
					'total_balance' => $user_detail_p->balance->user_balance,
					'total_balance2' => $user_detail_p->balance->user_commission,
					'created_at' => $ctime,
					 'recharge_type' => $wallet,
					'user_id' => $child_id,
					'credit_by' => Auth::id(),
					'remark' => $remark_content
				]);
				DB::commit();
				$message = "Dear ".$user_detail_p->name." , Your Wallet has been debited with Amount : $amount, Thanks " .Auth::user()->company->company_name;
				$message = urlencode($message);
				$this->sendSMS($user_detail_p->mobile, $message,Auth::user()->company_id);
				return true;
			}
			catch(Exception $e)
			{
				 DB::rollback();
				 throw $e;
				return false;
			}
           
        } else {
           return false;
        }
    }
    

    function transfernow($id, $child_id, $amount, $payment_id, $remark_content, $wallet,$dt_scheme)
    {

        $authbalance = Auth::user()->balance->user_balance;
		if ($authbalance >= $amount && $amount >= 10) 
		{
			$wallet_new = 'user_balance';
			$wallet_detail = 'Fund';
			$now = new \DateTime();
			$datetime = $now->getTimestamp();
			$ctime = $now->format('Y-m-d H:i:s');
			$schemenew =0;
			$charge = ($amount * $schemenew) / 100;
			$amount = $amount - $charge;
			DB::beginTransaction();
			try
			{
					
				Balance::where('user_id', $child_id)->increment($wallet_new, $amount);
				$child_user_details = User::find($child_id);
				$insert_id = Report::insertGetId([
					'number' => $child_user_details->mobile,
					'provider_id' => 0,
					'amount' => $amount,
					'api_id' => 0,
					'opening_balance' => $child_user_details->balance->user_balance-$amount,
					'ip_address'=>\Request::ip(),
					'description' => "Amount transfer from  ". Auth::user()->name . '( '. Auth::user()->mobile . ') '. Auth::user()->prefix .' '. Auth::id(),
					'status_id' => 7,
					'pay_id' => $datetime,
					'txnid' => 'DT',
					'type' => 'CR',
					'txn_type' => 'PAYMENT',
					'profit' => 0,
					'payment_id' => 0,
					'total_balance' => $child_user_details->balance->user_balance,
					'total_balance2' => $child_user_details->balance->user_commission,
					'created_at' => $ctime,
					'user_id' => $child_id,
					'debit_charge' => $charge,
					'credit_charge' => 0,
					'credit_by' => Auth::id(),
					'remark' =>$remark_content,
					'recharge_type' =>$wallet
				]);
				Balance::where('user_id', $id)->decrement($wallet_new, $amount);
				$user_detail_p = User::find($id);
				$insertId = Report::insertGetId([
					'number' => $user_detail_p->mobile,
					'provider_id' => 0,
					'amount' => $amount,
					'api_id' => 0,
					'description' => "Amount transfer to ". $child_user_details->name . '( ' . $child_user_details->mobile . ') '. $child_user_details->prefix .' '. $child_user_details->id,
					'status_id' => 6,
					'pay_id' => $datetime,
					'txnid' => 'DT',
					'opening_balance' => $user_detail_p->balance->user_balance+$amount,
					'ref_id' => $insert_id ,
					'profit' => 0,
					'type' => 'DR',
					'txn_type' => 'PAYMENT',
					'payment_id' => 0,
					'total_balance' => $user_detail_p->balance->user_balance,
					'total_balance2' => $user_detail_p->balance->user_commission,
					'created_at' => $ctime,
					'user_id' => Auth::id(),
					'credit_charge' => $charge,
					'debit_charge' => 0,
					'credit_by' => $child_user_details->id,
					'remark' =>$remark_content,
					'recharge_type' => $wallet
				]);
				$number = $child_user_details->mobile;
				$message = "Dear ".$child_user_details->name." , Your Wallet has been Credited with Amount : $amount, Thanks " .Auth::user()->company->company_name;
				$message = urlencode($message);
				DB::commit();
				$this->sendSMS($number, $message,Auth::user()->company_id);
				return true;
			}
			catch(Exception $e)
			{
				DB::rollback();
				throw $e;
				return false;
			}
            
        } else {
           return false;
        }
    }

    public function fund_request()
    {
        $loadcashes = Loadcash::where('user_id', Auth::id())->get();
        $pmethods = Pmethod::lists('payment_type', 'id');
        $netbakings = Netbank::lists('bank_name', 'bank_code');
        $netbankings_pay = Netbank::where('paybank', '=', '1')->lists('bank_name', 'id');
        return view('admin.fund_request', compact('loadcashes', 'pmethods', 'netbankings_pay'))->with('netbankings', $netbakings);
    }

    public function fund_return()
    {
        $loadcashes = Loadcash::where('user_id', Auth::id())->get();
        $pmethods = Pmethod::lists('payment_type', 'id');
        $pmethods->prepend('Please Select Payment Method');
        $netbakings = Netbank::lists('bank_name', 'bank_code');
        $netbankings_pay = Netbank::where('paybank', '=', '1')->lists('bank_name', 'id');
        return view('admin.fund_request', compact('loadcashes', 'pmethods', 'netbankings_pay'))->with('netbankings', $netbakings);
    }

    public function fund_request_save(Request $request)
    {
        $rules = array('amount' => 'required',
            'bankref' => 'required',
            'pmethod' => 'required');
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            $messages = $validator->messages();
            return back()
                ->withErrors($validator)
                ->withInput();
            // redirect our user back to the form with the errors from the validators
        } else {
            $user_detail = User::find(Auth::id());
            $upscheme = Upscheme::find($user_detail->upscheme);
            if ($upscheme) {
                $schemenew = $upscheme->scheme;
            } else {
                $schemenew = 0;
            }
			if( $request->hasFile('d_picture')){ 		
			$imageName = date("Y-m-d h:i:sa").'.'.$request->file('d_picture')->getClientOriginalExtension();
			$upload_img = $request->file('d_picture')->move('deposit_slip/images', $imageName);
			}

            $loadcash = ['upscheme' => $schemenew, 'wallet' => $request->input('wallet'), 'user_id' => Auth::id(), 'netbank_id' => $request->input('paybankaccount'), 'bankref' => $request->input('bankref'), 'pmethod_id' => $request->input('pmethod'), 'amount' => $request->input('amount'), 'status_id' => 3,'d_picture' =>@$imageName];
            Loadcash::create($loadcash);
            $name = Auth::user()->name;
            $parent_id = Auth::user()->parent_id;
            $m_number = User::where('id',$parent_id)->first();
            $amount = $request->input('amount');
            $bank_id = $request->input('paybankaccount');
            $message = "Dear Levin Partner Bank id $bank_id, $name Your Wallet Requested Amount : $amount, Thanks Levin Money";
            $number = $m_number->mobile;
            $message = urlencode($message);
           //SendSMS::sendsms($number, $message,Auth::user()->company_id);
            $message = "Fund Request sent";
            //return redirect('home')->with('status', $message);
            return back()->with('status', $message);
        }
    }

    
    public function fund_to_recharge()
    {
        if (Auth::user()->role_id == 3) {
            $members = User::where('parent_id', Auth::id())->get();
            $member_id = array();
            foreach ($members as $member) {
                $member_id[] = $member->id;
            }
            $members = User::whereIn('parent_id', $member_id)->get();
            $member_id_new = array();
            foreach ($members as $member) {
                $member_id_new[] = $member->id;
            }
            $myid = array(Auth::id());
            $mmember = array_merge($myid);
            $users = User::whereIn('id', $mmember)->get();

        } 
        elseif(Auth::user()->role_id == 4)
        {

            $user_c_id = Auth::user()->company_id;
            
    
             $users = User::where('id', Auth::id())->get();   
        }
        else {

            return "Not Permission"; 
            
        }
        return view('admin.money_to_recharge', compact('users'));
    
    }

public function recharge_store(Request $request)
    {
        // get child detail
        $child_id = $request->user_id;
        $child_balance = Balance::find($child_id);
        $amount = $request->amount;
        $payment_id = $request->id;

        // get Parent Detail
        $id = Auth::id(); 
        $my_id = Balance::where('user_id', $id)->first();
        $mybalance = $my_id->user_balance;
        if ($mybalance >= $amount && $amount >= 1) {
            //return ",u";
            $md = $this->rechargenow($id, $child_id, $amount, $payment_id);
            # JSON-encode the response
            header('Access-Control-Allow-Origin: *');
            header('content-type: application/json; charset=utf-8');
            return Response::json(array('success' => 'success', 'message' => 'Successfully Transfered'));
        } else {
            # JSON-encode the response
            header('Access-Control-Allow-Origin: *');
            header('content-type: application/json; charset=utf-8');
            return Response::json(array('success' => 'failure', 'message' => 'Low Balance, Please refill your Balance, Thanks'));
        }
    }

    public function rechargenow($id, $child_id, $amount, $payment_id)
    {
         //update parent balance
        if (Auth::user()->balance->user_balance >= $amount && $amount >= 1) {
            if (Auth::user()->role->id < 5) {
            
                $now = new \DateTime();
                $datetime = $now->getTimestamp();
                $ctime = $now->format('Y-m-d H:i:s');
                $user_detail = User::find($child_id);
               $upscheme = Upscheme::find($user_detail->upscheme_id);
               if($upscheme)
               {
                $schemenew =  $upscheme->scheme;
               }
               else{ $schemenew = 0.2; }
                
                $amountper = ($amount * $schemenew) / 100;
                $amount = $amount + $amountper;
                $main_amount = $amount - $amountper;
                Balance::where('user_id', $child_id)->increment('user_commission', $amount);
                $insert_id = Report::insertGetId([
                    'number' => $user_detail->mobile,
                    'recharge_type' => 1,
                    'amount' => $amount,
                    'api_id' => 9,
                    'description' => 'Wallet Reffil',
                    'status_id' => 6,
                    'pay_id' => $datetime,
                    'txnid' => 'DT for Recharge',
                    'profit' => $amountper,
                    'payment_id' => 0,
                    'total_balance2' => $user_detail->balance->user_commission,
                    'created_at' => $ctime,
                    'user_id' => $child_id,
                    'credit_by' => Auth::user()->name,
                ]);
                Balance::where('user_id', $id)->decrement('user_balance', $main_amount);
                $user_detail_p = User::find($id);
                $insert_id = Report::insertGetId([
                    'number' => $user_detail->mobile,
                    'provider_id' => 0,
                    'amount' => $main_amount,
                    'api_id' => 9,
                    'description' => 'Fund Transfer to Recharge',
                    'status_id' => 7,
                    'pay_id' => $datetime,
                    'txnid' => 'DT for Recharge',
                    'profit' => $amountper,
                    'payment_id' => 0,
                    'total_balance' => $user_detail_p->balance->user_balance,
                    'created_at' => $ctime,
                    'user_id' => Auth::id(),
                    'credit_by' => $user_detail->name
                ]);
                $number = $user_detail->mobile;
                $message = "Dear Shighrapay Partner, Your Recharge Wallet has been Credited with Amount : $amount, Thanks Shighrah Money";
                $message = urlencode($message);
               //SendSMS::sendsms($number, $message,$user_detail->company_id);
                return "Success";
            } else {
                return "Wrong Detail";
            }
        } else {
            return "Low balance";
        }
    }
    public function savecurl($url, $xml)
    {
        $headers = array(
            "Content-type: text/xml",
            "Content-length: " . strlen($xml),
            "Connection: close",
        );
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, 1000);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $xml);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        $data = curl_exec($ch);
        return $data;
    }

    public function commission_transfer(Request $request)
    {
        if(Auth::user()->id==1 && Auth::user()->role_id==1)
            {
                $amount = $request->amount;

                    $child_id = $request->user_id;
                    $remark_content = $request->remark;
                    $child_balance = Balance::find($child_id);    
                    $payment_id = $request->id;
                    $id = Auth::id();
                    $my_id = Balance::where('user_id', $id)->select('user_commission','user_balance')->first();

                    if($request->wallet == 1){
                    $mybalance = Auth::user()->balance->user_commission;
                }else{
                    $mybalance = Auth::user()->balance->user_commission;
                    }
        $md = $this->transfercommission($id, $child_id, $amount, $payment_id,$remark_content);
    
     }
 }
     function transfercommission($id, $child_id, $amount, $payment_id, $remark_content)
     {
        $authbalance = Auth::user()->balance->user_commission;
        if ($authbalance >= $amount && $amount >= 100) {
            if (Auth::user()->role->id ==1) {
                $wallet_new = 'user_commission';
                $now = new \DateTime();
                $datetime = $now->getTimestamp();
                $ctime = $now->format('Y-m-d H:i:s');
                $user_detail = User::find($child_id);

                Balance::where('user_id', $child_id)->increment('user_balance', $amount);
                $insert_id = Report::insertGetId([
                    'number' => Auth::user()->mobile,
                    'provider_id' => 0,
                    'amount' => $amount,
                    'api_id' => 0,
                    'ip_address'=>\Request::ip(),
                    'description' => $wallet_detail . ' Reffil',
                    'status_id' => 6,
                    'pay_id' => $datetime,
                    'txnid' => 'commission_transfer',
                    'payment_id' => 0,
                    'total_balance' => $user_detail->balance->user_balance, 
                    'created_at' => $ctime,
                    'user_id' => $child_id,
                    'credit_by' => Auth::user()->name,
                    'remark' =>$remark_content,
                ]);

                Balance::where('user_id', $child_id)->decrement('user_commission', $amount);
                $user_detail_p = User::find($child_id);
                $insert_id = Report::insertGetId([
                    'number' => $user_detail->mobile,
                    'provider_id' => 0,
                    'amount' => $amount,
                    'api_id' => 0,
                    'description' => 'Fund Transfer to Agent',
                    'status_id' => 7,
                    'pay_id' => $datetime,
                    'txnid' => 'commission_transfer',
                    'payment_id' => 0,
                    'total_balance' => $user_detail_p->balance->user_commission,
                    'created_at' => $ctime,
                    'user_id' => Auth::id(),
                    'credit_by' => $user_detail->name,
                    'remark' =>$remark_content,
                    'recharge_type' => $wallet
                ]);
                // $number = $user_detail->mobile;
                // $message = "Dear".Auth::user()->company->company_name." Partner, Your Wallet has been Debited with Amount : $amount," .Auth::user()->company->company_name;
                // $message = urlencode($message);
                // SendSMS::sendsms($number, $message, $user_detail->company_id);
                return response()->json(['status' => 'success', 'message' => 'Successfully Transfered']);
            } else {
                return response()->json(['status' => 'failure', 'message' => 'Something Wrong, Call Customer Care']);
            }
        } else {
            return response()->json(['status' => 'failure', 'message' => 'Low Balance']);
        }
    }  
   public function paymentTransferReport(Request $request)
	{
		$paymentTrasferReports = Report::where('user_id',Auth::id())->where('status_id',6)->orderBy('id','desc')->simplePaginate();
		return view('admin.payment.payment-transfer-report',compact('paymentTrasferReports'));
	}
	public function rToRList(Request $request)
	{
		if($request->number!='')
			$retilerLists= $this->searchUser($request->all(),5);
		else
			$retilerLists=[];		
		return view('agent.fund.r-to-r-fund-transfer',compact('retilerLists'));
	}
	public function fundTransferRToR(Request $request)
	{
		print_r($request->all());
		die;
	}
	private function searchUser($request=array(),$role)
	{
		$usersQuery = User::selectRaw('id,name,mobile,email,member_id');
		if(count($request))
		{
			if($request['SEARCH_TYPE']=="ID")
			$usersQuery->where('id','like','%'.trim($request['number']) .'%');
			elseif($request['SEARCH_TYPE']=="MOB_NO")
			$usersQuery->where('mobile','like','%'.trim($request['number']) .'%');
		}
		$usersQuery->where('role_id',$role);
		$usersQuery->where('id','!=',Auth::id());
		return $usersQuery->get();
	}		
}
