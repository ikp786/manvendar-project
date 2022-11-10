<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Report;
use App\User;
use App\VoucherBrand;
use App\VoucherCategory;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class BusinessController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function recharge()
    { 
			$datefrom = date('Y-m-d', time());
            $dateto = date('Y-m-d', time());
        
        //return $dateto;
        if (in_array(Auth::user()->role_id,array(1,11,12,14))) 
		{
            $reports = Report::whereIn('api_id', [1,8,13])
                    ->orderBy('id', 'DESC')
                    ->select('id','pay_id','user_id','created_at','api_id','amount','profit','beneficiary_id','recharge_type','status_id','description','total_balance','provider_id','number','customer_number','bank_ref','ackno','txnid','refund','credit_by')
                    ->paginate(40);
        } elseif (Auth::user()->role_id == 3) {
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
            $mmember = array_merge($member_id, $member_id_new, $myid);

            $reports = Report::orderBy('id', 'DESC')
                    ->whereIn('api_id', [1,8,13])
                    ->whereIn('user_id', $mmember)
                    ->select('id','pay_id','user_id','created_at','api_id','amount','profit','beneficiary_id','recharge_type','status_id','description','total_balance','provider_id','number','customer_number','bank_ref','ackno','txnid','refund','credit_by')
                    ->paginate(40);
        } elseif (Auth::user()->role_id == 4) {
            $members = User::where('parent_id', Auth::id())->get();
            $member_id = array();
            foreach ($members as $member) {
                $member_id[] = $member->id;
            }
            $myid = array(Auth::id());
            $mmember = array_merge($member_id, $myid);

            $reports = Report::orderBy('id', 'DESC')
                    ->where('api_id', 1)
                    //->where('api_id', 1)
                    ->whereDate('created_at', '>=', $datefrom)
                    ->whereDate('created_at', '<=', $dateto)
                    ->whereIn('user_id', $mmember)
                    ->select('id','pay_id','user_id','created_at','api_id','amount','profit','beneficiary_id','recharge_type','status_id','description','total_balance','provider_id','number','customer_number','bank_ref','ackno','txnid','refund','credit_by')
                    ->paginate(40);
        } else {
            $reports = Report::orderBy('id', 'DESC')
                    ->where('user_id', Auth::id())
                    ->where('api_id', 1)
                    ->whereDate('created_at', '>=', $datefrom)
                    ->whereDate('created_at', '<=', $dateto)
                    //->whereBetween('created_at', array($start, $end))
                    ->orderBy('id', 'DESC')
                    ->select('id','pay_id','user_id','created_at','api_id','amount','profit','beneficiary_id','recharge_type','status_id','description','total_balance','provider_id','number','customer_number','bank_ref','ackno','txnid','refund','credit_by')
                    ->paginate(40);
            //return $reports;
        }      
        return view('admin.business.recharge', compact('reports'));
		exit;
    }
	public function pancard()
    {
			$datefrom = date('Y-m-d', time());
            $dateto = date('Y-m-d', time());
			if (in_array(Auth::user()->role_id,array(1,11,12,14))) 
		{
            $reports = Report::whereIn('api_id', [1,8,13])
                    ->orderBy('id', 'DESC')
                    ->select('id','pay_id','user_id','created_at','api_id','amount','profit','beneficiary_id','recharge_type','status_id','ip_address','description','total_balance','provider_id','number','customer_number','bank_ref','ackno','txnid','refund','credit_by')
                    ->paginate(40);
        } elseif (Auth::user()->role_id == 3) {
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
            $mmember = array_merge($member_id, $member_id_new, $myid);

            $reports = Report::orderBy('id', 'DESC')
                    ->whereIn('api_id', [1,8,13])
                    ->whereIn('user_id', $mmember)
                    ->select('id','pay_id','user_id','created_at','api_id','amount','profit','beneficiary_id','recharge_type','status_id','ip_address','description','total_balance','provider_id','number','customer_number','bank_ref','ackno','txnid','refund','credit_by')
                    ->paginate(40);
        } elseif (Auth::user()->role_id == 4) {
            $members = User::where('parent_id', Auth::id())->get();
            $member_id = array();
            foreach ($members as $member) {
                $member_id[] = $member->id;
            }
            $myid = array(Auth::id());
            $mmember = array_merge($member_id, $myid);

            $reports = Report::orderBy('id', 'DESC')
                    ->where('api_id', 1)
                    //->where('api_id', 1)
                    ->whereDate('created_at', '>=', $datefrom)
                    ->whereDate('created_at', '<=', $dateto)
                    ->whereIn('user_id', $mmember)
                    ->select('id','pay_id','user_id','created_at','api_id','amount','profit','beneficiary_id','recharge_type','status_id','ip_address','description','total_balance','provider_id','number','customer_number','bank_ref','ackno','txnid','refund','credit_by')
                    ->paginate(40);
        } else {
            $reports = Report::orderBy('id', 'DESC')
                    ->where('user_id', Auth::id())
                    ->where('api_id', 1)
                    ->whereDate('created_at', '>=', $datefrom)
                    ->whereDate('created_at', '<=', $dateto)
                    //->whereBetween('created_at', array($start, $end))
                    ->orderBy('id', 'DESC')
                    ->select('id','pay_id','user_id','created_at','api_id','amount','profit','beneficiary_id','recharge_type','status_id','ip_address','description','total_balance','provider_id','number','customer_number','bank_ref','ackno','txnid','refund','credit_by')
                    ->paginate(40);
            //return $reports;
        }
        return view('admin.business.pancard', compact('reports'));
		exit;
    }
    public function dmt()
    {
         $SessionID = '';
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
            $mmember = array_merge($member_id, $member_id_new, $myid);
            $reports = Report::where('provider_id', 41)
                    ->orderBy('id', 'DESC')
                     ->Where('status_id','!=',14)
                     ->Where('recharge_type','=',0)// added by rajat 0=> Only Money Transaction and 1=>recharge
                     ->Where('status_id','!=',14)
                    ->whereIn('user_id', $mmember)
                    ->whereDate('created_at','>=', Carbon::now()->subWeek()) 
                    ->select('id','user_id','created_at','api_id','amount','profit','beneficiary_id','recharge_type','status_id','description','total_balance','provider_id','number','customer_number','bank_ref','ackno','txnid','refund','credit_by','channel','remark')
                    ->paginate(40);
        }
        elseif(Auth::user()->role_id == 4) {
            $members = User::where('parent_id', Auth::id())->get();
            $member_id = array();
            foreach ($members as $member) {
                $member_id[] = $member->id;
            }
            $reports = Report::where('provider_id', 41)
                    ->orderBy('id', 'DESC')
                     ->Where('status_id','!=',14)
                      ->Where('recharge_type','=',0)// added by rajat
                    ->whereIn('user_id', $member_id)
                    ->whereDate('created_at','>=', Carbon::now()->subWeek())
                    ->select('id','user_id','created_at','api_id','amount','profit','beneficiary_id','recharge_type','status_id','description','total_balance','provider_id','number','customer_number','bank_ref','ackno','txnid','refund','credit_by','channel','remark')
                    ->paginate(40);
        }
        elseif (in_array(Auth::user()->role_id,array(1,11,12,14))) {
            $reports = Report::whereIn('provider_id', [0,41])
                    ->where('status_id','!=',6)
                    ->Where('status_id','!=',7)
                    ->Where('status_id','!=',14)
                     ->Where('recharge_type','=',0)// added by rajat
                    ->whereDate('created_at','>=', Carbon::now()->subWeek())
                    ->orderBy('id', 'DESC')
                    ->select('id','user_id','created_at','api_id','amount','profit','beneficiary_id','recharge_type','status_id','description','total_balance','provider_id','number','customer_number','bank_ref','ackno','txnid','refund','credit_by','channel','remark')
                    ->paginate(40);
        }
        elseif (Auth::user()->role_id == 5) {
            $reports = Report::orderBy('id', 'DESC')
                      ->where('provider_id', 41)
                      ->Where('status_id','!=',14)
                      ->where('user_id', Auth::id())
                      ->whereDate('created_at','>=', Carbon::now()->subWeek())
                      ->select('id','user_id','created_at','api_id','amount','profit','beneficiary_id','recharge_type','status_id','description','total_balance','provider_id','number','customer_number','bank_ref','ackno','txnid','refund','credit_by','channel','remark')
                    ->paginate(40);
        }
        else
        {
            return "You are not permission to access this page!";
        }
        return view('admin.business.dmt', compact('reports'))->with('sessionid', $SessionID);
    
    }
    public function travel()
    {
        return view('admin.business.travel');
    }
    public function aeps()
    {
        return view('admin.business.aeps');
    }
   
    public function mpos()
    {
        return view('admin.business.mpos');
    }
    public function irctc()
    {
        return view('admin.business.irctc');
    }
    public function giftvoucher()
    {
		if(Auth::user()->role_id==1)
		{
			$voucherbrand=VoucherBrand::All();
			
			$voucherycategory=VoucherCategory::where('name')->get();
			
			$voucherlist=Report::where('api_id',22)->orderBy('id','Desc')->paginate(30);
			return view('admin.business.giftvoucher',compact('voucherlist','voucherbrand','voucherycategory'));
		}
		else{
			$voucherbrand=VoucherBrand::All();
			$voucherycategory=VoucherCategory::All();
			$voucherlist=Report::where('api_id',22)->where('user_id',Auth::id())->orderBy('id','Desc')->paginate(30);
			return view('admin.business.giftvoucher',compact('voucherlist','voucherbrand','voucherycategory'));
		}
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
