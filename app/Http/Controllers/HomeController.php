<?php
namespace App\Http\Controllers;
use App\Provider;
use App\Bankdetail;
use App\Company;
use App\Upscheme;
use App\Pmethod;
use App\Http\Requests;
use App\User;
use App\Apiresponse;
use App\Balance;
use App\Report;
use Illuminate\Support\Facades\DB;
use App\Loadcash;
use App\Circle;
use App\Netbank;
use App\Bbp;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use App\Exports\FundRequestExport;
use App\Exports\PaymentRequuestReportExport;
use Intervention\Image\ImageManagerStatic as Image;
use Validator;
use App\Masterbank;
use App\CompanyBankDetail;
use Input;
use App\Remark;
use Exception;
use App\Holiday;
use Excel;
use App\Traits\CustomTraits;
class HomeController extends Controller {

    /**
     * Create a new controller instance.
     *
     * @return void
     */
	 use CustomTraits;
    public function __construct() {
        $this->middleware('auth');
    }
    public function index() {
        if(in_array(Auth::user()->role_id,array(5)))
        {
            $reports = Report::selectRaw('id,api_id,number,customer_number,status_id,amount,recharge_type,provider_id')->orderBy('id','desc')->where('user_id',Auth::id())->whereIn('status_id',[1,3,2,4,21,20,24])->take(10)->get();
            //print_r($reports);die;

               $holidays = Holiday::orderBy('id','desc')->where('active_holiday',1)->whereDate('holiday_date','>=', date('Y-m-d'))->take(3)->get();

            return view('home', compact('reports','holidays'));        
        }
       return view('errors.permission-denied');
    }
	public function getTransactionSale(Request $request)
	{
		DB::enableQueryLog();
		$reportSaleQuery = Report::selectRaw('sum(amount) as totalSale,count(id) as txnCount, status_id');
		$reportSaleQuery->where('status_id',1);
		$reportSaleQuery->groupBy('status_id');
		if($request->type=="LMS")
				$reportSaleQuery->whereMonth('created_at', date('m', strtotime("-1 months")));
		elseif($request->type=="CMS")
				$reportSaleQuery->whereMonth('created_at', date('m'));
		elseif($request->type=="TS")
			$reportSaleQuery->whereDate('created_at', date('Y-m-d'));
            if(Auth::user()->role_id==5)
             $reportSaleQuery->where('user_id',Auth::id());
		$result = $reportSaleQuery->get();
		if(count($result)){
			return response()->json(['status'=>1,'totalVolume'=>$result[0]->totalSale,'txnCount'=>$result[0]->txnCount]);
			
		}
		return response()->json(['status'=>1,'totalVolume'=>0,'txnCount'=>0]);
	}
	public function paymentRequest()
    {
       $companyBanks = CompanyBankDetail::where('status_id', 1)->where(['user_id' => 1])->select(DB::raw("CONCAT(bank_name,' : ',account_number) AS name"),'id')->pluck('name','id')->toArray();
       $ubankBanks=CompanyBankDetail::where('status_id', 1)->where(['user_id' => Auth::user()->parent_id])->where('user_id','!=',1)->select(DB::raw("CONCAT(bank_name,' : ',account_number) AS name"),'id')->pluck('name','id')->toArray();
	    $loadcashes = Loadcash::where(['user_id'=>Auth::id()])->orderBy('id','desc')->take(10)->get();
	    if(in_array(Auth::user()->role_id,array(5,7)))
			return view('agent.fund.payment-request',compact('companyBanks','loadcashes','ubankBanks'));
        return view('admin.payment.payment-request',compact('companyBanks','loadcashes'));
    }
/*	public function paymentRequestSave(Request $request)
    {
		$date = ($request->upaymentDate) ? ($request->upaymentDate) : ($request->cpaymentDate);	
		$depositDate = date("Y-m-d",strtotime($date));
		Loadcash::create([
			'request_to'=>$request->requestTo,
			'netbank_id'=>$request->companyBank,
			'user_id'=>Auth::id(),
			'amount'=>($request->uamount) ? ($request->uamount) : ($request->camount),
			'payment_mode'=>($request->upaymentMode) ? ($request->upaymentMode) : ($request->cpaymentMode),		
			'bank_name'=>($request->ubankName) ? ($request->ubankName) : ($request->cbankName),		
			'bankref'=>($request->urefNumber) ? ($request->urefNumber) : ($request->crefNumber),	
			'deposit_date'=>$depositDate,		
			'request_remark'=>($request->uremark) ? ($request->uremark) : ($request->cremark),
			'loc_batch_code'=>$request->cloc_batch_code,
			]);
			 if(Auth::user()->parent_id == Auth::user()->parent->id)
            {
                $amount=($request->uamount) ? ($request->uamount) : ($request->camount);
                $msg ="A fund request has came from ". Auth::user()->name . " ( ". Auth::user()->role->role_title ." ) of Amount $amount";
                $msg=urlencode($msg);
                $this->sendSMS(Auth::user()->parent->mobile,$msg,1); 
            }
        return redirect()->back()
        ->with('success','Payment Request Sent');
    }*/

	public function paymentRequestSave(Request $request)
    {
        /*
        if( $request->hasFile('d_picture')){       
             
            echo "paymentRequestSave call";
           
            $file = $request->file('d_picture'); 
            $imageName = time().'.'.$file->getClientOriginalExtension(); 
            $img_size = $file->getSize();
            $img = Image::make($file);
            
            if($img_size>1000000 || $img_size>850394){ echo "<br/> call compresson";
                 $img->resize(320, 240)->save('deposit_slip/images/'.$imageName);
            }else{
                 $upload_img = $file->move('deposit_slip/images', $imageName);
            } 
        }
        die;
        */
		$bankRef = ($request->requestTo == 1) ? ($request->urefNumber) : ($request->crefNumber);
		if($bankRef !='')
		{
			if(Loadcash::where('bankref',$bankRef)->exists())//SBI0012541
			{
				 return redirect()->back()
			->with('warning','Duplicate bank reference number');
			}
		}
		$startTime = date("Y-m-d H:i:s");
		$end_time = date('Y-m-d H:i:s',strtotime('+0 minutes',strtotime($startTime)));
		$start_time = date('Y-m-d H:i:s',strtotime('-5 minutes',strtotime($startTime)));//exit;
		$amount = ($request->requestTo == 1) ? ($request->uamount) : ($request->camount);
		if($amount<0)
		{
			 return redirect()->back()
			->with('warning','Amount can not be less than Rs .0');
		}
		$exit_record = Loadcash::whereIn('status_id',[1,3])->where(['amount'=>$amount,'user_id'=>Auth::id()])->whereBetween('created_at', [$start_time,$end_time])->orderBy('created_at','desc')->get();
		
		if(count($exit_record))//SBI0012541
		{
			 return redirect()->back()
			->with('warning','Request can not make withing 5 minutes with Rs. '.$amount);
		}
       $date = ($request->requestTo == 1) ? ($request->upaymentDate) : ($request->cpaymentDate);	
		$depositDate = date("Y-m-d",strtotime($date));
		if($request->requestTo == 2)
		{
			if($request->cpaymentMode == "OnLine")
			{
				$cOnlinePaymentMode = $request->cOnlinePaymentMode;
			}
		}
		if( $request->hasFile('d_picture')){       
            
            $imageName = time().'.'.$request->file('d_picture')->getClientOriginalExtension();
             
            $file = $request->file('d_picture'); 
            $imageName = time().'.'.$file->getClientOriginalExtension(); 
            $img_size = $file->getSize();
            $img = Image::make($file);
            
            if($img_size>1000000){   
                 $img->resize(340, 240)->save('deposit_slip/images/'.$imageName);
            }else{
                 $upload_img = $file->move('deposit_slip/images', $imageName);
            }  
        }
             
		Loadcash::create([
			'request_to'=>$request->requestTo,
			'netbank_id'=>$request->companyBank,
			'user_id'=>Auth::id(),
			'amount'=>$amount,
			'payment_mode'=>($request->requestTo == 1) ? ($request->upaymentMode) : ($request->cpaymentMode),		
			'bank_name'=>($request->requestTo == 1) ? ($request->ubankName) : ($request->cbankName),		
			'bankref'=>($request->requestTo == 1) ? ($request->urefNumber) : ($request->crefNumber),
			'd_picture'=>@$imageName,
			'deposit_date'=>$depositDate,		
			'request_remark'=>($request->requestTo == 1) ? ($request->uremark) : ($request->cremark),
			'loc_batch_code'=>isset($cOnlinePaymentMode) ? '' : $request->cloc_batch_code,
			'c_online_mode'=>isset($cOnlinePaymentMode) ? $cOnlinePaymentMode : '',
			]);
			$msg ="A fund request has came from ". Auth::user()->name . " ( ". Auth::user()->role->role_title ." ) of Amount $amount";
                $msg=urlencode($msg);
				
				if($request->requestTo == 2)
				{
					$this->sendSMS(Auth::user()->company->company_mobile,$msg,1);
					$alternateMobileOne = config('constants.ALTERNET_MOBILE_NO_ONE');
					$alternateMobileTwo = config('constants.ALTERNET_MOBILE_NO_TWO');
					if($alternateMobileOne !='')
						$this->sendSMS($alternateMobileOne,$msg,1);
					if($alternateMobileTwo !='')
						$this->sendSMS($alternateMobileTwo,$msg,1);
				}
                else 
                    $this->sendSMS(Auth::user()->parent->mobile,$msg,1);  
                  return redirect()->back()
        ->with('success','Payment Request Sent');
    }

	public function paymentRequestReport(Request $request)
    {
		$start_date = ($request->fromdate) ? $request->fromdate ." 00:00:00" : date("Y-m-d") . " 00:00:00";
        $end_date = ($request->todate) ? $request->todate ." 23:59:59" : date("Y-m-d H:i:s");
        $start_date = date("Y-m-d H:i:s", strtotime($start_date));
        $end_date = date("Y-m-d H:i:s", strtotime($end_date));
		$loadcashes = Loadcash::where(['user_id'=>Auth::id()])->orderBy('id','desc')->simplePaginate();
		$page_title='Payment Request Report';
        if (in_array(Auth::user()->role_id,array(1,3,4))) {
            if($request->export=="Payment Load"){
              return Excel::download(new PaymentRequuestReportExport($start_date,$end_date), 'Payment Report.xlsx');
            }
			$userDetails = User::where('parent_id',Auth::id())->selectRaw('name,mobile,id')->get();
            $users=array();
            foreach($userDetails as $user)
            {
                $users[$user->id]=$user->member->company.' '.'(' .$user->mobile .')';
            }
			return view('payservice.loadcash', compact('loadcashes','page_title','users'));
           
        }
		return view('agent.fund.loadcash',compact('loadcashes','page_title'));
    }
	
	public function paymentRequestCompany()
    {
		$companyBanks = Netbank::where('paybank', '=', '1')->pluck('bank_name', 'id');
        return view('agent.fund.payment-request-company',compact('companyBanks'));
    }
	public function paymentRequestCompanySave(Request $request)
    {
		
		Loadcash::create([
			'request_to'=>2,
			'netbank_id'=>$request->companyBank,
			'amount'=>$request->amount,
			'deposit_date'=>$request->paymentDate,
			'payment_mode'=>$request->paymentMode,		
			'bankref'=>$request->refNumber,
			'user_id'=>Auth::id(),
			'loc_batch_code'=>$request->loc_batch_code,		
			'request_remark'=>$request->remark,
			]);
       return redirect()->back()
        ->with('success','Payment Request Sent');
    }
	public function cancelFundRequest(Request $request)
	{
		if(in_array(Auth::user()->role_id,array(3,4,5)))
		{
			$requestReport=  Loadcash::find($request->recordId);
			if($requestReport->status_id ==1)
				$message="Fund request allready approved";
			elseif($requestReport->status_id ==2)
				$message="Fund request allready canceled";
			elseif($requestReport->status_id ==3)
			{
					$requestReport->status_id=2;
					$requestReport->save();
					$message = "Request has been canceled";
			}
			return response()->json(['status'=>1,'message'=>$message]);
		}
	}
    public function bankcash()
    {
        return view('agent.fund.cash');
    }
    public function fundSummary(Request $request)
    {
        if(in_array(Auth::user()->role_id,array(5,7)))
        {
            $start_date = ($request->fromdate) ? $request->fromdate ." 00:00:00" : date("Y-m-d"). " 00:00:00";
            $end_date = ($request->todate) ? $request->todate ." 23:59:59" : date("Y-m-d H:i:s");

            $start_date = date("Y-m-d H:i:s", strtotime($start_date));
            $end_date = date("Y-m-d H:i:s", strtotime($end_date));
            $reports = Loadcash::where('user_id',Auth::id())->whereBetween('created_at',[$start_date,$end_date])->orderby('id','DESC')->paginate();
            //echo($reports);
			if($request->export=="EXPORT")
                return Excel::download(new FundRequestExport($start_date,$end_date), 'FundRequestReport.xlsx');
            return view('reports.fundrequestSummary',compact('reports'));
        }
        return view('errors.page-not-found');
    }
    public function mobile() {
     
         if(Auth::user()->role_id==5)
            {
               
        $circle = Circle::pluck('state', 'id');
        $provider = Provider::where('service_id', '=', '1')->pluck('provider_name', 'id');
        $id = Auth::user()->company_id;
        $news = DB::table('companies')->where('id', $id)->select('recharge_news')->get();
        return view('payservice.mobile', compact('provider', 'circle', 'news'));
    }
    else
    {
       
        return "Not Permission";
    }
}

    public function dth() {
        
        if(Auth::user()->role_id==5)
            {
                
        $provider = Provider::where('service_id', '=', '2')->pluck('provider_name', 'id');
        $id = Auth::user()->company_id;
        $news = DB::table('companies')->where('id', $id)->select('recharge_news')->get();
        return view('payservice.dth', compact('provider', 'news'));
    }
    else
    {
        //return "Recharge Is down Please wait..";
        return "Not Permission";
    }
    }

    public function datacard() {
        
        if(Auth::user()->role_id==5)
            {
               
        $provider = Provider::where('service_id', '=', '3')->pluck('provider_name', 'id');
        $id = Auth::user()->company_id;
        $news = DB::table('companies')->where('id', $id)->select('recharge_news')->get();
        return view('payservice.datacard', compact('provider', 'news'));
    }
    else
    {
       // return "Recharge Is down Please wait..";
        return "Not Permission";
    }
        
}

    public function postpaid() {
        
        if(Auth::user()->role_id==5)
            {
               
        $provider = Provider::where('service_id', '=', '4')->pluck('provider_name', 'id');
        $id = Auth::user()->company_id;
        $news = DB::table('companies')->where('id', $id)->select('recharge_news')->get();
        return view('payservice.postpaid', compact('provider', 'news'));
    }
    else
    {
        return "Recharge Is down Please wait..";
        return "Not Permission";
    }
    
    }

    public function electricity() {
      
        if(Auth::user()->role_id==5)
            {
                //return "Recharge service is down";
        $provider = Provider::where('service_id', '=', '6')->pluck('provider_name', 'id');
        $id = Auth::user()->company_id;
        $news = DB::table('companies')->where('id', $id)->select('recharge_news')->get();
        return view('payservice.electricity', compact('provider', 'news'));
    }
    else
    {
       // return "Recharge Is down Please wait..";
        return "Not Permission";
    }
    
    }

    public function re_electricity()
    {
     if(Auth::user()->role_id==5)
        {

        $provider = Provider::where('service_id', '=', '6')->pluck('provider_name', 'id');
        $id = Auth::user()->company_id;
        $news = DB::table('companies')->where('id', $id)->select('recharge_news')->get();
        return view('payservice.re-electricity', compact('provider', 'news'));
        }
        else
        {
           // return "Recharge Is down Please wait..";
            return "Not Permission";
        }
    }
    public function get_bill() {
     
        if(Auth::user()->role_id==5)
            {
                
        $provider = Provider::where('service_id', '=', '6')->pluck('provider_name', 'id');
        $id = Auth::user()->company_id;
        $news = DB::table('companies')->where('id', $id)->select('recharge_news')->get();
        return view('payservice.getbill', compact('provider', 'news'));
    }
    else
    {
       // return "Recharge Is down Please wait..";
        return "Not Permission";
    }
    }

    public function telephone() {
        
        if(Auth::user()->role_id==5)
            {
                
        $provider = Provider::where('service_id', '=', '5')->pluck('provider_name', 'id');
        $id = Auth::user()->company_id;
        $news = DB::table('companies')->where('id', $id)->select('recharge_news')->get();
        return view('payservice.telephone', compact('provider', 'news'));
    }
    else
    {
        return "Not Permission";
    }
    }

    public function gas() {
     
        if(Auth::user()->role_id==5)
            {
                
        $provider = Provider::where('service_id', '=', '8')->pluck('provider_name', 'id');
        $id = Auth::user()->company_id;
        $news = DB::table('companies')->where('id', $id)->select('recharge_news')->get();
        return view('payservice.gas', compact('provider', 'news'));
    }
    else
    {
        return "Not Permission";
    }
    }

    public function bbps_gas() {
     
        if(Auth::user()->role_id==5)
            {
                
            $provider = Provider::where('service_id',13)->pluck('provider_name', 'id');
            $id = Auth::user()->company_id;
            $news = DB::table('companies')->where('id', $id)->select('recharge_news')->get();
            return view('payservice.bbpsgas', compact('provider', 'news'));
            }
            else
            {
                return "Not Permission";
            }
    }

    public function flights() {
        if(Auth::user()->role_id==5)
            {

        $provider = Provider::where('service_id', '=', '7')->pluck('provider_name', 'id');
        return view('payservice.flights', compact('provider'));
    }
    else
    {
        return "Not Permission";
    }
    }

    public function other() {
        if(Auth::user()->role_id==5)
            {
        return view('mpay.other');
    }
    else
    {
        return "Not Permission";
    }
    }

    public function findmember(Request $request) {
        $id = $request->input('number');
        return User::find($id);
    }

    public function load_cash() {
		if(Auth::user()->role_id==5)
		{
			if (!empty($_SERVER['HTTP_HOST'])) {
				$host = $_SERVER['HTTP_HOST'];
			} else {
				$host = "localhost:8888";
			}
			$company = Company::where('company_website', $host)
					->where('status', 1)
					->first();
			if ($company) {
				$company_id = $company->id;
			} else {
				$company_id = 0;
			}
			$banks = Bankdetail::where('company_id', $company_id)->get();
			$loadcashes = Loadcash::where('user_id', Auth::id())
							->orderBy('id', 'DESC')->paginate(20);
			$pmethods = Pmethod::pluck('payment_type', 'id');
			$netbakings = Netbank::pluck('bank_name', 'bank_code');
			$netbankings_pay = Netbank::where('paybank', '=', '1')->pluck('bank_name', 'id');
			return view('payservice.loadcash', compact('loadcashes', 'pmethods', 'netbankings_pay', 'banks'))->with('netbankings', $netbakings);
		}
		return view('errors.page-not-found');
    }
    
    
   
    
    public function requestloadcash(Request $request) 
	{
		
		 $amount=$request->amount;
        if($amount <100 || $amount >1500000)
		{
            return response()->json(['status'=>'failure','msg'=>"Amount should be between 100 - 1500000"]);
		}
        $now = new \DateTime();
        if($request->amount >= 10){
        $datetime = $now->getTimestamp();
            if (!empty($request->loadpage)) {
                $data = [
                    'key' => 'FE3kA7',
                    'salt' => 'h9MBBfmB',
                    'payurl' => 'https://secure.payu.in',
                    'amount' => $request->amount,
                    'mobile' => Auth::user()->mobile,
                    'email' => Auth::user()->email,
                    'productinfo' => 'yesRecharge',
                    'txnid' => $datetime,
                    'firstname' => Auth::user()->name,
                    'surl' => url('load-cash/success'),
                    'furl' => url('load-cash/failure_page'),
                    'curl' => url('load-cash/cancel'),
                    'pg' => 'NB',
                    'bankcode' => $request->bankcode
                ];
                return view('payservice.payment_process')->with('data', $data);
            }
            
        }
        
        
        //return $request->all();
        $now = new \DateTime();
        $datetime = $now->getTimestamp();
        $rules = array('amount' => 'required',
            'bankref' => 'required',
            'pmethod' => 'required');
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            $messages = $validator->messages();
            return json_encode($messages);
            // redirect our user back to the form with the errors from the validators
        } else 
{
			try{
            $loadcash = ['user_id' => Auth::id(), 'netbank_id' => $request->input('payba'), 'wallet' => $request->input('wallet'), 'bankref' => $request->input('bankref'), 'pmethod_id' => $request->input('pmethod'), 'amount' => $request->input('amount'), 'status_id' => 3];
			Loadcash::create($loadcash);
			 return response()->json(['status'=>'success','message'=>"Request has been send successfully"]);
			}
			catch(Exception $e)
			{
				 return response()->json(['status'=>'failure','message'=>"Oops Something went wrong. Please try again."]);
			}
        }
    }

    public function failure_status() {
        return view('layouts.failure_status');
    }

 
     public function payment_request_view() {
        
		$down_api_lists ='';
        $down_bank_list = $this->getBankDownList();
        if(in_array(Auth::user()->role_id,array(11,14,12)))
            $logined_user_id = 1;
        else
            $logined_user_id = Auth::id();
        $members = User::where('parent_id', $logined_user_id)->get();
        //$members = User::where('', Auth::id())->get();
        $member_id = array();
        foreach ($members as $member) 
        {
            $member_id[] = $member->id;
        }
        if(Auth::user()->role_id ==1)
        {
            $loadcashs = Loadcash::where('status_id', 3)
                            ->where(function ($query) use($member_id)
                            {
                                $query->whereIn('user_id', $member_id)
                                    ->OrWhere('request_to', 2)
                                    ->OrWhere('request_to', 3);
                            })
                            ->orderBy('id','desc')->get();
                           // ->whereIn('user_id', $member_id)->OrWhere('request_to', 2)->orderBy('id','desc')->get();
        }
        else
            $loadcashs = Loadcash::where('status_id', 3)->where('request_to', 1)->whereIn('user_id', $member_id)->orderBy('id','desc')->get();
		
        
        $remarks= Remark::where([['id', '!=', 0],['deleted','=',0]])->pluck('remark','id')->toArray();
        //print_r($remarks);die;
        return view('admin.payment_request_view', compact('loadcashs','remarks','down_api_lists','down_bank_list'));
    }

   
    public function view(Request $request) {
        $id = $request->input('id');
        $loadCashes = Loadcash::where('id',$id)->get();
		
		return $loadCashes->map(function($loadCash){
				return [
					'agentDetails'=>$loadCash->user->name .':' . $loadCash->user->role->role_title .' : ' .$loadCash->user->member->company,
					'amount'=>$loadCash->amount,
					'paymentMode'=>$loadCash->payment_mode .' : '.$loadCash->c_online_mode,
					'locBatchCode'=>$loadCash->loc_batch_code,
					'bankName'=>($loadCash->request_to == 2) ? ($loadCash->netbank->bank_name .' : '.$loadCash->netbank->account_number ): $loadCash->bank_name,
					'bankRef'=>$loadCash->bankref,
					'requestRemark'=>$loadCash->request_remark,
					'depositDate'=>date('d-m-Y',strtotime($loadCash->deposit_date)),
					'id'=>$loadCash->id,
					];
		});
        //return $provider;
    }

    public function p_view(Request $request) {
        $id = $request->input('id');
        $payment_view = Loadcash::find($id);
        return $payment_view;
    }

     public function update(Request $request, $id) {
		
      // print_r($request->all());die; 
		$loadCashDetails = Loadcash::find($id);
		$amount = $loadCashDetails->amount;
		/*if($amount >50000000)
		{
			return response()->json(['status' => 'failure', 'message' => 'Sorry! this request can not approve. amount limits 50000000']);
		}*/
        $status_check =  Loadcash::where('id',$id)->select('status_id')->first();
        if($status_check->status_id==3)
        {
			if(in_array(Auth::user()->role_id,array(1,11,12,14)))
				$logined_user_id = User::select('id')->where('role_id',1)->first()->id;
			else
				$logined_user_id=Auth::id();
			if(in_array(Auth::user()->role_id,array(1,11,12,14,3,4)))
			{				
				$wallet_new = 'user_balance';
				$wallet_detail = 'Fund ';				
				$user_id = $loadCashDetails->user_id;
				$payment_id = $loadCashDetails->id;
				$bank_charge = $request->b_charge_id;
				if($request->status_id == 2)
				{
						 
						$user_mobile = $loadCashDetails->user->mobile;
						$user_name =$loadCashDetails->user->name;
						$loadCashDetails->status_id = $request->status_id;
						$loadCashDetails->remark_id = $request->remark;
						$loadCashDetails->approve_remark = $request->adminRemark;
						$loadCashDetails->updated_by =Auth::id();
						$loadCashDetails->save();
						$message = "Dear ". $user_name ." Partner, Your payment requuest has been rejected";		$message = urlencode($message);
						try{
							$this->sendSMS($user_mobile,$message);
						}
						catch(Exception $e)
						{
							
						}
						return response()->json(['status' => 'failure', 'message' => 'Request has been cancelled.']);
				}
				else if($request->status_id == 3)
				{
					return response()->json(['status' => 'failure', 'message' => 'Sorry Your status is still same']);
				}
				else 
				{
					
					$logined_user_details = User::select('id','balance_id')->find($logined_user_id);
					$balanc_par = $logined_user_details->balance->user_balance;
					
				}
				if ($balanc_par >= $amount) 
				{
					$now = new \DateTime();
					$datetime = $now->getTimestamp();
					$ctime = $now->format('Y-m-d H:i:s');
					if ($request->status_id == 1) 
					{
						$startTime = date("Y-m-d H:i:s");
						$end_time = date('Y-m-d H:i:s',strtotime('+12 minutes',strtotime($startTime)));
						$start_time = date('Y-m-d H:i:s',strtotime('-12 hours',strtotime($startTime)));
						$exit_record = Report::where('txnid',$id)->whereIn('status_id',[6,7])->whereBetween('created_at', [$start_time,$end_time])->orderBy('created_at','desc')->get();
						if(count($exit_record) >0)
						{
							return response()->json(['status' => 'failure', 'message' => 'Sorry! this request has been operated']);
							exit;
						}
						$loadCashDetails->status_id = $request->status_id;
						$loadCashDetails->remark_id = $request->remark;
						$loadCashDetails->approve_remark = $request->adminRemark;
						$loadCashDetails->updated_by =Auth::id();
						$bank_charge_amount = 0;
						/* if($bank_charge > 0)
						{
							$bank_charge_amount = ($amount * $bank_charge)/100;
							$amount = $amount - $bank_charge_amount;
						} */
						DB::beginTransaction(); 
						try
						{
							
							$loadCashDetails->save();
							$balanceDetails = $loadCashDetails->user->balance;
							$cashDepositCharge= $balanceDetails->cash_deposit_charge;
							if($loadCashDetails->request_to==2 && $cashDepositCharge>0 &&(in_array($loadCashDetails->payment_mode,array('Cash@Counter','Cash@CDM'))))
							{
								$cashDepositMinCharge= $balanceDetails->cash_deposit_min_charge;
								$pcntCharge = ($amount*$cashDepositCharge)/100;
								if($pcntCharge > $cashDepositMinCharge)
								{
									$bankChargeAmount = ($amount * $cashDepositCharge)/100;
									$creditAmount = $amount - $bankChargeAmount;
								}
								else{
									$bankChargeAmount = $cashDepositMinCharge;
									$creditAmount = $amount - $bankChargeAmount;
								}
							}
							else{
								$bankChargeAmount = 0;
								$creditAmount = $amount - $bankChargeAmount;
							}
							/* echo $bankChargeAmount;
							echo "<br>".$creditAmount;exit; */
							Balance::where('user_id', $user_id)->increment($wallet_new, $creditAmount);
							$user_detail = User::find($user_id);
                            $updatedBalance = $user_detail->balance->user_balance;
							$insert_id = Report::insertGetId([
									'number' => Auth::user()->mobile,
									'provider_id' => 0,
									'amount' => $amount,
									'debit_charge' => $bankChargeAmount,
									'api_id' => 0,
									'description' => "Amount transfer from  ". Auth::user()->name . '( '. Auth::user()->mobile . ')',
									'status_id' => 7,
									'pay_id' => $datetime,
									'txnid' => $id,
									'profit' => 0,
									'type' => 'CR',
									'txn_type' => 'PAYMENT',
									'opening_balance'=>($user_detail->balance->user_balance - $creditAmount),
									'credit_charge' => 0,
									'bank_charge' =>$bankChargeAmount,
									'total_balance' => $updatedBalance,
									'total_balance2' => $user_detail->balance->user_commission,
									'created_at' => $ctime,
									'recharge_type' => 0,
									'user_id' => $user_id,
									'remark'=>$request->adminRemark,
									'payment_id' => $payment_id,
									'credit_by' => $logined_user_id,
									'updated_by' =>Auth::id(),
										
								]);
							if(Auth::user()->role_id==1)
							{
								Balance::where('user_id', $logined_user_id)->decrement($wallet_new, $amount);
								Balance::where('user_id', $logined_user_id)->increment('admin_com_bal', $bankChargeAmount);
							}
							else
								Balance::where('user_id', $logined_user_id)->decrement($wallet_new, $amount);
							$logined_user_balance = User::select('id','balance_id')->find($logined_user_id);
							//$user_detail_p = User::find($user_id);
							$insert_id = Report::insertGetId([
								'number' => $user_detail->mobile,
								'provider_id' => 0,
								'amount' => $amount,
								'api_id' => 0,
								'profit' => 0,
								'bank_charge' =>$bankChargeAmount,
								'description' => "Amount transfer to ". $user_detail->name . '( ' . $user_detail->mobile . ')',
								'status_id' => 6,
								'type' => 'DR',
								'txn_type' => 'PAYMENT',
								'pay_id' => $datetime,
								'recharge_type' => 0,
								'txnid' => $id,
								'opening_balance'=>($logined_user_balance->balance->user_balance + $amount),
								'debit_charge' =>0,
								'credit_charge' => $bankChargeAmount,
								'payment_id' => $payment_id,
								'total_balance' =>$logined_user_balance->balance->user_balance,
								'total_balance2' => $logined_user_balance->balance->user_commission,
								'created_at' => $ctime,
								'user_id' => $logined_user_id,
								'remark'=>$request->adminRemark,
								'credit_by' => $user_detail->id,
								'updated_by' =>Auth::id(),	
							]);
							if(Auth::user()->role_id==1)
							{
								$updatedRecord = Report::find($insert_id);
								$updatedRecord->admin_com_bal = $logined_user_balance->balance->admin_com_bal;
								$updatedRecord->save();
							}
							$message = "Dear ". $user_detail->name ." , Your Wallet has been Credited with Amount : $amount and updated balance is ". number_format($updatedBalance,2) .", Thanks A2zsuvidhaa";
							$number = $user_detail->mobile;
                            $message = urlencode($message);
                            DB::commit();
							try{
								$this->sendSMS($number,$message);
							}
							catch(Exception $e)
							{
								
							}
							return response()->json(['status' => 'success', 'message' => 'Successfully Updated']);
						}
						catch(Exception $e)
						{
							DB::rollback();
							throw $e;
							return response()->json(['status' => 'failure', 'message' => 'Something went wrong. Please try again...']);
						}
					} 
				} else {
						return response()->json(['status' => 'failure', 'message' => 'You do not have sufficient Balance']);
					}
			} else {
				return response()->json(['status' => 'failure', 'message' => 'NO Permission']);
			}

        }
		elseif($loadCashDetails->status_id==2)
		{
			return response()->json(['status' => 'rejected', 'message' => 'Already Rejected']);
		}
		elseif($loadCashDetails->status_id==1)
		{
			return response()->json(['status' => 'approved', 'message' => 'Already Approved!']);
		}
    }



    public function slip_update(Request $request) {
        if (Auth::user()->role_id == 2 || Auth::user()->role_id == 1) {
            $file = Input::file('d_slip');
            $id = $request->input('d_id');
            if (Input::hasFile('d_slip')) {
                $imageName = $id . '.' . $file->getClientOriginalExtension();
                $upload_img = $file->move('deposit_slip/images', $imageName);
            }
            DB::table('loadcashes')
                    ->where('id', $id)
                    ->update(array('d_picture' => $imageName));
        }
        return redirect('payment-request-view');
    }

    public function p_update(Request $request) {
		
        $id = $request->id;
		
        $remark_content = $request->remark_update;
        $r_update = DB::table('reports')
                ->where('id', $id)
                ->update(array('remark' => $remark_content,'txnid'=>$remark_content));
                if($r_update)
                {
                    return "Update Succesfully";
                }
        /* return redirect('payment-request-viewn'); */
    }
    
    
   

    

    public function myprofile() {
        return view('admin.myprofile');
    }

     public function getRecords(Request $request)
    {
         $last_five = Loadcash::orderBy('id','DESC')->where('user_id',$request->user_id)->where('status_id',1)->take(15)->get();

         $response["data"] = array();
         foreach($last_five as $last_five_success)
         {
            $data['id'] = $last_five_success->id;
            $data['amount'] = $last_five_success->amount;
            $data['created_at'] = $last_five_success->created_at;
            $data['bankref'] = $last_five_success->bankref;
            $data['netbank'] = $last_five_success->netbank->bank_name;

               array_push($response["data"], $data);
         }
          return $response;
    }

      public function bbps()
    {
        if(Auth::user()->id==116)
        {
       $circle = Circle::pluck('state', 'id');
            $provider = Bbp::where('status', '=', '1')->pluck('billerCategory', 'id');
            $provider_name = Bbp::where('status', '=', '1')->pluck('billerName', 'id');
            $id = Auth::user()->company_id;
            $news = DB::table('companies')->where('id', $id)->select('recharge_news')->get();
        return view('mpay.bbps', compact('provider', 'provider_name','circle', 'news')); 
        }
        else
        {
            return "no permission";
        }

    }
    public function getLastTenApprovedAmount(Request $request)
    {
        if(Auth::user()->role_id == 1){
            $childrens = User::where('parent_id',Auth::id())->pluck('id','id')->toArray();
         $last_ten_approved_amount = Loadcash::join('netbanks','loadcashes.netbank_id', '=', 'netbanks.id')
                                             ->join('users', 'users.id', '=', 'loadcashes.user_id')
                                             ->select('loadcashes.id as id','loadcashes.amount','netbanks.bank_name','loadcashes.bankref','loadcashes.created_at','users.name','users.mobile')
                                             ->where(['loadcashes.status_id' => 1,'loadcashes.amount' => $request->requested_amount])
                                             ->whereIN('loadcashes.user_id',$childrens)
                                             ->orderBy('loadcashes.created_at','desc')
                                             ->take(10)
                                             ->get();

         return response()->json(['status' => 1, 'last_ten_approved_amount' => $last_ten_approved_amount,'result'=>'last_ten_approved_amount']);
        }
    }
	public function addNewRemark(Request $request)
	{
		if(in_array(Auth::user()->role_id,array(1,11,14)))
		{
			$remark_array = explode(" ",$request->new_remark);
			$result = array_filter($remark_array);    
			$new_remark = implode(" ",$result);
			$final_remark = ucwords($new_remark);
			$exist_remark = Remark::where(['remark'=>$final_remark,'deleted'=>0])->first();
			if(!$exist_remark)
			{
				$remark_result = Remark::create(['created_by'=>Auth::user()->id,'remark'=>$final_remark]);
				return response()->json(['status' => 1, 'message' => 'Remark has been added successfuly','contents'=>$remark_result->remark,'contents_id'=>$remark_result->id]);
			}
			else
				 return response()->json(['status' => 0, 'message' => 'Remark all ready exists']);
		}
		else
		{
			return response()->json(['status' => 0, 'message' => 'Sorry! You do not have permission']);
		}
        
	}
	public function updateSelectedRemark(Request $request, $id)
    {
		if(in_array(Auth::user()->role_id,array(1,11,14)))
		{
			$remark_id = $id;
			if($remark_id == 0)
				return response()->json(['status' => 1, 'message' => 'You can not delete this remark']);
			$remark_array = explode(" ",$request->new_remark);
			$result = array_filter($remark_array);    
			$new_remark = implode(" ",$result);
			$final_remark = ucwords($new_remark);
			$exist_remark = Remark::where(['remark'=>$final_remark,'deleted'=>0])->first();
			if(!$exist_remark)
			{
				
				Remark::where('id',$remark_id)->update(['remark'=>$final_remark,'updated_by'=>Auth::id()]);
				return response()->json(['status' => 1, 'message' => 'Remark has been updated successfuly','contents'=>$final_remark,'contents_id'=>$remark_id]);
			}
			else
				 return response()->json(['status' => 0, 'message' => 'Remark all ready exists']);
		}
		else
		{
			return response()->json(['status' => 0, 'message' => 'Sorry! You do not have permission']);
		}
        
    }
	public function deletedSelectedRemark(Request $request)
    {
		if(in_array(Auth::user()->role_id,array(1,11,14)))
		{
			$remark_id = $request->remark_id;    
			Remark::where('id',$remark_id)->update(['deleted_by'=>Auth::id(),'deleted'=>1]);
			return response()->json(['status' => 1, 'message' => 'Remark has been deleted successfuly','contents_id'=>$remark_id]);
		}
		else
		{
			return response()->json(['status' => 0, 'message' => 'Sorry! You do not have permission']);
		}
        
    }
	private function getApiDownListName($api_lists)
    {
        
        $down_api_lists='';
        if($api_lists)
        {
            if($api_lists->up_down ==0)
                $down_api_lists = $down_api_lists.' Saral,';
             if($api_lists->up_down2 ==0)
                $down_api_lists =$down_api_lists.' Smart, ';
             if($api_lists->up_down3 ==0)
                $down_api_lists =$down_api_lists.' Sharp, ';
             if($api_lists->up_down4 ==0)
                $down_api_lists =$down_api_lists. 'Secure';

        }
        else
         $down_api_lists = "No Api down.";
            return $down_api_lists;
    }
	private function getBankDownListName($bank_lists)
    {
        
        $bank_list_name='';
        if($bank_lists)
        {
            foreach($bank_lists as $bank)
            {
                
                $bank_list_name = $bank_list_name.$bank->bank_name.', ';
            
            }
           
        }
        else
         $bank_list_name = "No Bank down.";
    return $bank_list_name;
    }

    public function bbps_recharge()
    {
       return "server is down now!";
      if(Auth::user()->role_id==5)
        {    
        $provider = Provider::whereIn('service_id',[6,12])->pluck('provider_name', 'id');
        $id = Auth::user()->company_id;
        $news = DB::table('companies')->where('id', $id)->select('recharge_news')->get();
        return view('payservice.re-electricity', compact('provider', 'news'));
        }
        else
        { 
            return "Not Permission";
        }  
    }
	
    public function creditFund()
    {
        $companyBanks = Netbank::where('paybank', '=', '1')->pluck('bank_name', 'id');
        if(Auth::user()->role_id ==5)//1 for admin
            return view('agent.fund.credit-fund',compact('companyBanks'));
        return view('admin.payment.payment-request',compact('companyBanks'));
    }

}
