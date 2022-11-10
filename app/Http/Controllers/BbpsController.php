<?php

namespace App\Http\Controllers;
use App\Api;
use App\Company;
use Validator;
use App\Balance;
use App\Beneficiary;
use App\User;
use App\Provider;
use App\Rechargeprovider;
use Illuminate\Http\Request;
use GuzzleHttp\Client;
use App\Netbank;
use Response;
use App\Http\Requests;
use Carbon\Carbon;
use DB;
use App\Apiresponse;
use App\Report;
use App\Masterbank;
use App\State;
use Auth;
use App\Traits\CustomTraits;
use App\Http\Controllers\Controller;

class BbpsController extends Controller {

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
        
		use CustomTraits;


        function check_bill (Request $request){
        $number = $request->number;
        $provider = $request->provider;
        $cyber = new \App\Library\Cyber;
        return  $data = $cyber->check_bill($provider, $number);
        }
	
     public function recharge(Request $request) 
	 {
        if(Auth::user()->role_id==5)
        {
			//print_r($request->all());die;
			$balance = Balance::where('user_id', Auth::id())->first();
			$user_id = Auth::id();
			$user_balance = $balance->user_balance;
			$amount = $request->amount;
			$provider = $request->provider;
			$number = $request->ca_number;
			$sendrec = $this->makeRecharge($number, $provider, $amount, $user_balance, $user_id);
			
		}
        else
        {
            return "Not Permission";
        }
    }
	private function makeRecharge($number, $provider, $amount, $user_balance, $user_id) {
         // return Response::json(array('payid' => '', 'operator_ref' =>$provider, 'status' => 'failure', 'message' =>'Service is down'));

        if ($user_balance >= $amount && $amount >= 4) 
		{
            $myd = $this->api_route($number, $provider, $amount, $user_id); 
            if ($myd) 
			{
                $user_detail = User::findOrFail($user_id);
                $companyDetail = new \App\Library\Companyid;
                $company_detail = $companyDetail->get_company_detail();
                $pidnew = $company_detail->user_id;
                if($user_detail->role_id==7)
                {
                   $scheme_id = User::find($user_detail->id)->scheme_id; 
                }
                else
                {
                   $scheme_id = User::find($pidnew)->scheme_id; 
                }
                /* $mc = $this->get_commission($scheme_id, $provider);
                
                if($myd->service_id != 1 && $myd->service_id != 2 && $myd->service_id != 3)
				{

                    if ($mc) {

                        if($mc->type==0)
                        {
                        $commission = ($amount * $mc->r) / 100;
                        $distt_comm = ($amount * $mc->d) / 100;
                        $md_comm = ($amount * $mc->md) / 100;
                        $admin_comm = ($amount * $mc->a) / 100;
                        }
                        else
                        {
                             $commission = $mc->r;
                             $distt_comm = $mc->d;
                             $md_comm = $mc->md;
                             $admin_comm = $mc->a;
                        }
                        
                        

                    } else {
                        $commission = -5;
                    }
                    
                }else{
                
                    if ($mc) {

                        if($mc->type==0)
                        {
                        $commission = ($amount * $mc->r) / 100;
                        $distt_comm = ($amount * $mc->d) / 100;
                        $md_comm = ($amount * $mc->md) / 100;
                        $admin_comm = ($amount * $mc->a) / 100;
                        }
                        else
                        {
                             $commission = $mc->r;
                             $distt_comm = $mc->d;
                             $md_comm = $mc->md;
                             $admin_comm = $mc->a;
                        }
                       
                    } else {
                        $commission = 0; 
                    }
                    
                }

                if($mc->r >0)
					$tds = ($commission * 5) / 100;
				else
					$tds = 0; */
				$commission = 0;
				$tds = 0;
                $camount = ($amount - $commission + $tds); 
               
               $now = new \DateTime();
        $datetime = $now->getTimestamp();
        $ctime = $now->format('Y-m-d H:i:s');
                
                $total_balance = $user_balance - $camount;
				DB::beginTransaction();
				try
				{
					Balance::where('user_id', $user_id)->decrement('user_balance', $camount);
                $insert_id = Report::create([
                            'number' => $number,
                            'provider_id' => $provider,
                            'amount' => $amount,
                            'api_id' => 15,
                            'status_id' => 3,
                            'pay_id' => $datetime,
                            'created_at' => $ctime,
                            'recharge_type' => 1,
                            'user_id' => $user_id,
                            'profit' => $commission,
                            'tds' => $tds,
                            'total_balance' => $total_balance,
                   ]);
                  DB::commit();
				}
				catch(Exception $e)
				{
					DB::rollback();
					return Response::json(array('payid' => '', 'operator_ref' => '', 'status' => 'failure', 'message' => 'Server Not Connected,try again!'));
				}
                $provider_code = $myd->provider_code;
                $provider_api = $myd->api_id;
                $vender_code = $myd->vender_code;
                $account = '';
                $cycle = '';
                
                    $p_id = Auth::user()->parent_id;
                       $u_role_id = user::where('id',$p_id)->select('role_id')->first();
                        if($u_role_id->role_id==4)
                        {
                            $m_id1 = User::where('id',$p_id)->select('id','parent_id')->first();
                            $m1_id = $m_id1->parent_id;
                            if($m1_id==110)
                            {
                                $d_id=$p_id;
                                $m_id='';
                                $a_id=110;

                            }
                            else
                            {
                                $d_id = $p_id;
                                $m_id=$m1_id;
                                $a_id=110;
                            }                          
                        }
                        if($u_role_id->role_id==3)
                        {
                            $d_id = '';
                            $m_id = $p_id;
                            $a_id = 110;
                        }
                        if($u_role_id->role_id==1)
                        {
                            $d_id = '';
                            $m_id = '';
                            $a_id = 110;
                        }

                if ($provider_api >= 1) {
                    switch ($provider_api) {
                        
                        case 8:
                            $company = $provider;
                            $result = $this->cyber_bbps($user_id, $number, $company, $amount, $insert_id, $account, $cycle);
                            break;
                        default;
                            $company = $provider;
                            $result = $this->cyber_bbps($user_id, $number, $company, $amount, $insert_id, $account, $cycle);
                    }
                    $status = $result['status'];
                    $txnid = $result['txnid'];
                    $ref_id = $result['ref_id'];
                    if ($status == 1) {
                        $status = 'success';
                        $message = "Transaction Successfully Done, Operator Transaction ID : $txnid, Thanks";
                        $ptxn_insert = $this->pertxn_insert($d_id,$m_id,$distt_comm,$md_comm,$insert_id,$number,$provider,$myd->api_id);
                        Report::where('id', $insert_id)->update(['status_id' => 1, 'txnid' => $txnid]);
                    } elseif ($status == 2) {
                        $status = 'failure';
                        $message = "Transaction Failed, Please check Detail or Try After some time, Thanks";
                        Balance::where('user_id', $user_id)->increment('user_balance', $camount);
                          $final_bal = Balance::where('user_id', $user_id)->select('user_balance')->first();
                        Report::where('id', $insert_id)->update(['status_id' => 2, 'txnid' => $txnid, 'total_balance' => $final_bal->user_balance]); 
                    }

                     else {
                       
                         $ptxn_insert = $this->pertxn_insert($d_id,$m_id,$distt_comm,$md_comm,$insert_id,$number,$provider,$myd->api_id);
                        Report::where('id', $insert_id)->update(['status_id' => 3, 'txnid' => $txnid]);
                        $status = 'success';
                        $message = "Transaction Submitted Successfully Done, Check Status in Transaction Report, Thanks";
                    }
                    return Response::json(array('payid' => $insert_id, 'operator_ref' => $txnid, 'status' => $status, 'message' => $message));
                } else {
                    return Response::json(array('payid' => $insert_id, 'operator_ref' => '', 'status' => 'failure', 'message' => 'Server Not Connected, Contact Customer Care'));
                }
            }
        } else {
            return Response::json(array('success' => 'failure', 'message' => 'Low Balance or Not Permission'));
        }
    }
	private function cyber_bbps($user_id, $number, $provider_id, $amount, $insert_id, $account, $cycle)
    {
        $cyber = new \App\Library\Cyberbbps;
        $data = $cyber->CallRecharge($provider_id, $insert_id, $number, $amount, $user_id, $account, $cycle);
        return $data;
    }
	function api_route($number, $provider, $amount, $user_id) {
        $provider_detail = Provider::find($provider);
        return $provider_detail;
    }
	function get_commission($scheme_id, $provider) {
        return Commission::where('provider_id', $provider)
                        ->where('scheme_id', $scheme_id)
                        ->first();
    }
	public function pertxn_insert($d_id,$m_id,$distt_comm,$md_comm,$insert_id,$number,$provider,$api_id)
{
    $now = new \DateTime();
         $datetime = $now->getTimestamp();
         $ctime = $now->format('Y-m-d H:i:s');
         if($d_id!='')
         {
             $d_tds = ($distt_comm * 5)/100;
             $c_commission = $distt_comm ;
             $distt_comm = $distt_comm - $d_tds;
             $user_balance = Balance::where('user_id',$d_id)->select('user_balance')->first();
             $total_balance_dist =  $user_balance->user_balance+$distt_comm;
             $insert_dist_ptxn =  Report::insertGetId([
                            'number' => $number,
                            'provider_id' => $provider,
                            'api_id' => $api_id,
                            'status_id' => 22,
                            'pay_id' => $datetime,
                            'txnid'=>$insert_id,
                            'created_at' => $ctime,
                            'recharge_type' => 1,
                            'tds' => $d_tds,
                            'user_id' => $d_id,
                            'profit' => $c_commission,
                            'credit_charge'=>$c_commission,
                            'total_balance' => $total_balance_dist,
                            ]);
            if($insert_dist_ptxn)
            {
                Balance::where('user_id',$d_id)->increment('user_balance',$distt_comm);
                $distt_final_balance = Balance::where('user_id',$d_id)->select('user_balance')->first();
                Report::where('id',$insert_dist_ptxn)->update(['total_balance'=>$distt_final_balance->user_balance]);
            }
            else
            {
                return Response::json(['status_id'=>2,'message'=>'Failed,something went wrong!']);
            }
        }

       if($m_id!='')
         {
            $m_tds = ($md_comm * 5)/100;
            $m_commission = $md_comm ;
            $md_comm = $md_comm - $m_tds;
            $user_balance = Balance::where('user_id',$m_id)->select('user_balance')->first();
            $total_balance_md =  $user_balance->user_balance+$md_comm;
            $insert_md_ptxn =  Report::insertGetId([
                            'number' => $number,
                            'provider_id' => $provider,
                            'api_id' => $api_id,
                            'status_id' => 22,
                            'pay_id' => $datetime,
                            'txnid'=>$insert_id,
                            'created_at' => $ctime,
                            'recharge_type' => 1,
                            'tds' =>$m_tds,
                            'user_id' => $m_id,
                            'profit' => $m_commission,
                            'credit_charge'=>$m_commission,
                            'total_balance' => $total_balance_md,
                            ]);
            if($insert_md_ptxn)
            {
                Balance::where('user_id',$m_id)->increment('user_balance',$md_comm);
                $md_final_balance = Balance::where('user_id',$m_id)->select('user_balance')->first();
                Report::where('id',$insert_md_ptxn)->update(['total_balance'=>$md_final_balance->user_balance]);
            }
            else
            {
                return Response::json(['status_id'=>2,'message'=>'Failed,something went wrong!']);
            }
        }
        
	}

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create() {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request) {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function showBkp() {
		if(Auth::user()->role_id == 5)
		{
			$provider = Provider::where('service_id', '=', '6')->pluck('provider_name', 'id');
			return view('agent.bbps',compact('provider'));
		}
		return view('errors.page-not-found');
    }
    public function getProdiverName(Request $reqeust)
	{
		//$provider = Rechargeprovider::where('state_id', '=', $reqeust->state_id)->lists('provider_name', 'id');
		$provider = Provider::where('state_id', '=', $reqeust->state_id)->selectraw('provider_name,id,provider_image')->get();
		if(count($provider)){
			
		$providerDetails =  $provider->map(function($pro)
			{
				return [
						'id'=>$pro->id,
						'provider_name'=>$pro->provider_name,
						'provider_image'=>url('/').'/'.$pro->provider_image,
				];
				
			});
			return response()->json(['status'=>1,'message'=>$providerDetails]);
		}
		return response()->json(['status'=>0,'message'=>"NO Provider Available"]);
	}
	public function show(Request $reqeust)
	{
		if(Auth::user()->role_id == 5)
		{
			$serviceId = 6;
			$report=$this->getProviderReport($serviceId);
			$state_list = State::where('active_state',1)->orderBy('name','asc')->pluck('name', 'id')->toArray();
			$provider = Provider::where('state_id', '=', 8)->selectRaw('provider_image,provider_name,id')->get();
				
			return view('agent.bbps',compact('provider','state_list','report','serviceId'));
		}
		return view('errors.page-not-found');
	}
	private function getProviderReport($serviceId)
	{
		
		$providersId = Provider::where('service_id',$serviceId)->pluck('id','id')->toArray();
		$reportDetails = Report::where('user_id', Auth::id())
					->whereIn('provider_id',$providersId)
					->orderBy('id', 'DESC')
					->whereIn('status_id',[1,2,3,21,24,34])
					->paginate(15);
		return $reportDetails;
	}
	public function fetchBillAmount(Request $request)
	{
		$rules = array(
			'provider' => 'required|numeric',
			'ca_number' => 'required'
			);
		$validator = Validator::make($request->all(), $rules);
		if ($validator->fails()) {
			return Response::json(array(
				'status' => false,
				'errors' => $validator->getMessageBag()->toArray()
			)); 
		}
		$consumerNumber = $request->ca_number;
		
		$token = config('constants.INSTANT_KEY');//die;
		$instantPay = Provider::select('sp_key')->find($request->provider);
		if($instantPay)
			$sp_key = $instantPay->sp_key;
		if($sp_key !='')
		{
			$content ="{\n\"token\":\"$token\",\n\"request\": {\n\"sp_key\": \"$sp_key\"}\n}";
			$url = config('constants.INSTANT_PAY_URL') ."/ws/userresources/bbps_biller";
			$agentid = time();
			$output =  $this->getCurlPostIMethod($url,$content);
			try
			{
				$apiResp = json_decode($output);
				if(in_array($apiResp->statuscode,array("SNA")))
					return response()->json(['status'=>0,'content'=>$apiResp->status]);
				$params = $apiResp->data[0]->params;
				try{
					return $this->getCurlMethod($params,$consumerNumber,$sp_key);
				}
				catch(Exception $e)
				{
					return response()->json(['status'=>0,'content'=>"Bill fetch service is not currenty possible. Please enter amount and biller name manully"]);
				}
			}
			catch(Exception $e)
			{
				return response()->json(['status'=>0,'content'=>"Currently Service is down"]);
			}
		}
		return response()->json(['status'=>0,'content'=>"Currently Service is down"]);
	}
	private function getCurlMethod($params,$consumerNumber,$sp_key)
	{
		$jsonData = json_decode($params);
		$jsonData[0]->name = $consumerNumber;
		$jsonData=$jsonData[0];
		$paramSecornd = json_encode($jsonData);
		$token = config('constants.INSTANT_KEY');//die;
		$agentid = time();
		$customerMobileNumber= Auth::user()->mobile;
		$serIP = "182.18.157.156";
		$outletId = 15814;
		$ip=$_SERVER['REMOTE_ADDR'];
		$location_data = (unserialize(file_get_contents('http://www.geoplugin.net/php.gp?ip='.$ip)));
		$latlog = "28.626640,77.384804";
		$content = "{\n\"token\": \"$token\",\n\"request\": {\n\"sp_key\":\"$sp_key\",\n\"agentid\":\"$agentid\",\n\"customer_mobile\":\"8285540407\",\n    \"customer_params\": [\n\"$consumerNumber\"],\n    \"init_channel\": \"AGT\",\n\"endpoint_ip\": \"$serIP\",\n\"mac\": \"AD-fg-12-78-GH\",\n\"payment_mode\":\"Cash\",\n\"payment_info\":\"bill\",\n    \"amount\": \"10\",\n\"reference_id\": \"\",\n\"latlong\": \"$latlog\",\n\"outletid\":\"$outletId\"\n}\n}"; 
		
		$url = config('constants.INSTANT_PAY_URL')."/ws/bbps/bill_fetch";
		$output =  $this->getCurlPostIMethod($url,$content);
		$apiRespContent = json_decode($output);
        if($apiRespContent->statuscode == "ERR")
        {
            return response()->json(['status'=>0,'content'=>$apiRespContent->status]);

        }
        else{
		$displayContent['dueAmount'] = $apiRespContent->data->dueamount;
		$displayContent['dueDate'] = $apiRespContent->data->duedate;
		$displayContent['customerName'] = $apiRespContent->data->customername;
		$displayContent['billNumber'] = $apiRespContent->data->billnumber;
		$displayContent['billDate'] = $apiRespContent->data->billdate;
        return response()->json(['status'=>1,'content'=>$displayContent]);
        }
	}
    public function landline() {
		if(Auth::user()->role_id == 5)
		{	
			$serviceId = 5;
			$report=$this->getProviderReport($serviceId);
			$provider = Provider::where('service_id', '=', '5')->selectRaw('provider_image,provider_name,id')->get();
			
			return view('agent.bbps.landline',compact('provider','report','serviceId'));
		}
		return view('errors.page-not-found');
    }
    public function water() {
		if(Auth::user()->role_id == 5)
		{	
			$serviceId = 12;
			$report=$this->getProviderReport($serviceId);
			$provider = Provider::where('service_id', '=', '12')->selectRaw('provider_image,provider_name,id')->get();
			return view('agent.bbps.water',compact('provider','report','serviceId'));
		}
		return view('errors.page-not-found');
    }
    public function gas() 
	{
		if(Auth::user()->role_id == 5)
		{
			$serviceId = 8;
			$report=$this->getProviderReport($serviceId);
			$provider = Provider::where('service_id', '=', '8')->selectRaw('provider_image,provider_name,id')->get();
			return view('agent.bbps.gas',compact('provider','report','serviceId'));
		}
		return view('errors.page-not-found');
    }
   public function insurance() {
        if(Auth::user()->role_id == 5)
		{
			$serviceId = 9;
			$report=$this->getProviderReport($serviceId);
            $provider=Provider::where('service_id', '=', '9')->pluck('provider_name','id');
			return view('agent.bbps.insurance',compact('provider','report','serviceId'));
		}
		return view('errors.page-not-found');
    }
    public function loanrepayment() {
        if(Auth::user()->role_id == 5)
		{
			return view('agent.bbps.loanrepayment');
		}
		return view('errors.page-not-found');
    }
    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id) {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id) {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id) {
        //
    }

}
