<?php

namespace App\Http\Controllers;

use App\Balance;
use App\Company;
use App\User;
use App\Report;
use App\Masterbank;
use Carbon\Carbon;
use App\Loadcash;
use App\Member;
use App\Complain;
use App\Holiday;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Traits\CustomTraits;
use Exception;
class DashboardController extends Controller
{
	use CustomTraits;
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
		$down_bank_list = $this->getBankDownList();
		 
		
        if (Auth::user()->role_id == 5) 
		{
			$reports = Report::selectRaw('id,api_id,number,customer_number,status_id,amount,recharge_type,provider_id,debit_charge,created_at')->orderBy('id','desc')->where('user_id',Auth::id())->whereIn('status_id',[1,3,2,4,21,20,24,34])->take(10)->get();
            //print_r($reports);die;

               $holidays = Holiday::where('active_holiday',1)->whereDate('holiday_date','>=', date('Y-m-d'))->orderBy('holiday_date','ASC')->take(3)->get();

            return view('home', compact('reports','holidays','down_bank_list'));      
			 
        }
		else if(Auth::user()->role_id == 13)
		{
			return redirect('admin/otp');
		}
		else if(Auth::user()->role_id == 12)
		{
			return redirect('admin/bankupdown');
		}
		elseif(in_array(Auth::user()->role_id,array(4)))
		{
			
			$first_day_this_month = date('Y-m-01');
			$startDate = date("Y-m-d")." 00:00:00"; 
			$endDate = date("Y-m-d")." 23:23:23";
			$creditDebitBalance = Report::selectRaw('sum(amount) as amount,status_id')->whereIn('status_id',[6,7])->where('user_id',Auth::id())->whereBetween('created_at',[$startDate,$endDate])->groupBy('status_id')->get();
			$receivedBalance = $transferedBalance=0;
			foreach($creditDebitBalance as $crdb)
			{
				if($crdb->status_id == 7)
					 $receivedBalance = $crdb->amount;
				elseif($crdb->status_id == 6)
					 $transferedBalance = $crdb->amount;
			}
			
			if(Auth::user()->role_id ==4)
				$retailerList = $this->getDistAgent(Auth::id());
			$todayNetworkDetails = Report::selectRaw('sum(amount) as amount')->whereIn('user_id',$retailerList)->whereIn('status_id',[1,3])->whereBetween('created_at',[$startDate,$endDate])->get();
			$todayNetworkAmount = $todayNetworkDetails[0]->amount;
			//DB::enableQueryLog();
			$monthNetworkDetails = Report::selectRaw('sum(amount) as amount')->whereIn('user_id',$retailerList)->whereIn('status_id',[1,3])->whereBetween('created_at',[$first_day_this_month,$endDate])->get();
			//print_r(DB::getQueryLog());die;
			$monthNetworkAmount = $monthNetworkDetails[0]->amount;
			//print_r($monthNetworkDetails);die;   
			$data = ['receivedBalance' => $receivedBalance, 'transferedBalance' => $transferedBalance,'todayNetworkAmount'=>$todayNetworkAmount, 'monthNetworkAmount' => $monthNetworkAmount,'recharge_balance' => 0,'paytm_balance'=>0,'paytm_acc'=>0,'paytm_txnid'=>0,'apis_down'=>'','down_bank_list'=>$down_bank_list,'purchase_balance'=>0,'total_profit'=>0,'success_transaction'=>0];
            return view('admin.dashboard')->with($data);
		}
		elseif (in_array(Auth::user()->role_id,array(1,3,4,10,11,14,19)))
		{
			
           return view('admin.dashboard');
        }
		return view('errors.page-not-found');
    }
	private function getTramoBalance()
	{
		
		$content='api_token='.config('constants.TRAMO_API_KEY').'&userId='.config('constants.TRAMO_USER_ID');
		$url = config('constants.TRAMO_DMT_URL') ."/check-balance?".$content;//die;
		$tramoBalance = $this->getCurlGetMethod($url);
		try{
			$balance = json_decode($tramoBalance);
			return  number_format($balance->message->balance,2);
		}
		catch(Exception $e)
		{
			return "Error";
		}
	}
	private function getCyberBalance()
	{
		$cyber = new \App\Library\CyberDMT;
		return $cyber->getCyberBalance();
	}
	
	private function getMroboticsBalance()
	{
		$url = config('constants.MROBOTICS_RECHARGE_URL')."operator_balance";
		$content="api_token=".config('constants.MROBOTICS_API_TOKEN');
		$response = $this->getCurlPostMethod($url,$content);
		try{
			$balance = json_decode($response);
			return  $balance->data->Jio;
		}
		catch(Exception $e)
		{
			//throw $e;
			return "Error";
		}
	}
	
	private function getA2zRechargeBalance()
	{
		$url=config('constants.A2ZSuvidhaa_URL')."/balchk.aspx?uid=".config('constants.A2ZSuvidhaa_USER');
		return $a2zBalance = $this->getCurlGetMethod($url);
	}
	public function agentDashboard()
	{
		 $report = Report::where('user_id', Auth::id())
                ->orderBy('id', 'DESC')
                ->orderBy('created_at', 'DESC')
                ->Paginate(5);
		return view('agent.dashboard', compact('report'));
	}
	public function getSPFTxnCountVolume(Request $request)
	{
		$reportQuery = Report::selectRaw('count(id) as txnCount,sum(amount) as totalVolume, status_id')->whereDate('created_at', date('Y-m-d'));
		//$reportQuery->where('is_aeps','=',0);
		if($request->type=="SFRTXN")
			$reportQuery->whereIn('status_id',[1,2,4]);
		elseif($request->type=="PTXN")
			$reportQuery->where('status_id',3);
		elseif($request->type=="CASH_LOAD")
			$reportQuery->whereIn('status_id',[6,7]);
		if (in_array(Auth::user()->role_id,array(1))) 
        {
			$members = User::pluck('id','id')->where('role_id',5)->toArray();            
        } 
        elseif (Auth::user()->role_id == 3) 
        {
			if($request->type=="CASH_LOAD")
				$members=array(Auth::id());
			else{
                $parent_details = User::where(['parent_id'=>Auth::id(),'role_id'=>4])->pluck('id','id')->toArray();
                $members=User::whereIn('parent_id',$parent_details)->orWhere('parent_id',Auth::id())->orWhere('id',Auth::id())->pluck('id','id')->toArray();
			}
			$reportQuery->whereIn('user_id', $members);
        } 
        elseif (Auth::user()->role_id == 4) 
        { 
			if($request->type=="CASH_LOAD")
				$members=array(Auth::id());
			else
				$members = User::where('parent_id', Auth::id())->orWhere('id',Auth::id())->pluck('id','id')->toArray();
			$reportQuery->whereIn('user_id', $members);
        } 
        else
        { 
			$reportQuery->where('user_id', Auth::id());
                    
        }
		
			$reports =$reportQuery->groupBy('status_id')->get();
			$result=array();
			foreach($reports as $report)
			{
				if($report->status_id == 1){
					$result['successTxnCount']=$report->txnCount;
					$result['successVolume']=$report->totalVolume;
				}
				elseif($report->status_id == 2)
				{
					$result['failTxnCount']=$report->txnCount;
					$result['failVolume']=$report->totalVolume;
				}
				elseif($report->status_id == 4)
				{
					$result['refundedTxnCount']=$report->txnCount;
					$result['refundedVolume']=$report->totalVolume;
				}
				elseif($report->status_id == 7)
				{
					$result['receiveAmount']=$report->totalVolume;
					$result['receiveCount']=$report->txnCount;
				}
				elseif($report->status_id == 3)
				{
					$result['pendingTxnCount']=$report->txnCount;
					$result['pendingVolume']=$report->totalVolume;
				}
				elseif($report->status_id == 6)
				{
					$result['transferAmount']=$report->totalVolume;
					$result['transferCount']=$report->txnCount;
				}
					
			}
			return response()->json(['status'=>(count($result))?1:0,'report'=>$result]);
	}
	public function getPendingOfflineTxnCountVolume(Request $request)
	{
		if(Auth::user()->role_id == 1)
		{
			$reports = Report::selectRaw('count(id) as txnCount,sum(amount) as totalVolume, status_id')->whereIn('status_id',[3,24])->groupBy('status_id')->get();
			$result=array();
			foreach($reports as $report)
			{
				if($report->status_id == 3){
					$result['pendingTxnCount']=$report->txnCount;
					$result['pendingVolume']=$report->totalVolume;
				}
				elseif($report->status_id == 24)
				{
					$result['offLineTxnCount']=$report->txnCount;
					$result['offLineVolume']=$report->totalVolume;
				}					
			}
			return response()->json(['status'=>(count($result))?1:0,'report'=>$result]);
		}
	}
	public function getPendingComplain(Request $request)
	{
		if(Auth::user()->role_id == 1)
		{
			$reports = Complain::selectRaw('count(id) as txnCount,status_id')->where('status_id',3)->groupBy('status_id')->get();
			$result=array();
			foreach($reports as $report)
			{
				if($report->status_id == 3)
					$result['complainTxnCount']=$report->txnCount;				
			}
			return response()->json(['status'=>(count($result))?1:0,'report'=>$result]);
		}
	}
	public function getBalanceTxnCountVolume(Request $request)
	{
		if(Auth::user()->role_id == 1)
		{
			$reports = Loadcash::selectRaw('count(id) as txnCount,sum(amount) as totalVolume, status_id')->groupBy('status_id')->where('request_to',2)->get();
			if($request->type=="BALANCE_REQUEST")
			   $reports->where('status_id',3);
			$result=array();
			foreach($reports as $report)
			{
				if($report->status_id == 3){
					$result['balanceTxnCount']=$report->txnCount;
					$result['balanceVolume']=$report->totalVolume;
				}				
			}
			return response()->json(['status'=>(count($result))?1:0,'report'=>$result]);
		}
	}
	public function getRoleWiseBalance(Request $request)
	{
		if(Auth::user()->role_id == 1)
		{
			$balances = User::join('balances', 'users.id', '=', 'balances.user_id')->selectRaw('users.role_id,sum(balances.user_balance) as user_balance, count(users.id) as userCount')->groupBy('role_id')->get();
			$balanceData=array();
			
	        foreach($balances as $key=>$balance)
	        {
	        	$balanceData[$key]['roleId']=$balance->role_id;
	        	$balanceData[$key]['availableBalance']=number_format($balance->user_balance,2);
	        	$balanceData[$key]['userCount']=$balance->userCount;
	        }
			return response()->json(['status'=>(count($balanceData))?1:0,'details'=>$balanceData,'message'=>"Balance Available"]);
		}
	}
	public function getApiBalance(Request $request)
	{
		if(Auth::user()->role_id == 1)
		{
			if($request->getBalanceOf =="TRAMO")
				return response()->json(['status'=>1,'details'=>array('TRAMOBalance'=>$this->getTramoBalance())]);	
			elseif($request->getBalanceOf =="CYBER")
				return response()->json(['status'=>1,'details'=>array('CYBERBalance'=>$this->getCyberBalance())]);
			elseif($request->getBalanceOf =="MROBOTICS")
				return response()->json(['status'=>1,'details'=>array('MROBOTICSBalance'=>$this->getMroboticsBalance())]);
			elseif($request->getBalanceOf =="A2Z")
				return response()->json(['status'=>1,'details'=>array('A2ZBalance'=>$this->getA2zRechargeBalance())]);			
		}
	}
}
