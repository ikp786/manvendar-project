<?php

namespace App\Http\Controllers\Mobile;

use App\Holiday;
use App\Report;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Balance;
use App\Company;
use App\Http\Controllers\Controller;
use App\Traits\CustomAuthTraits;
use App\Traits\CustomTraits;
use App\User;
use Illuminate\Http\Response;
use DB;
use Validator;
use App\Masterbank;

class DashboardController extends Controller
{
    use CustomTraits, CustomAuthTraits;

    public function adminDashboard(Request $request)
    {
        $rules = array(
            'userId' => 'required|numeric',
            'token' => 'required',
        );
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return Response::json(array(
                'status' => 10,
                'message' => $validator->getMessageBag()->toArray()
            ));
        }
        $authentication = $this->checkLoginAuthentication($request);
        $status = $authentication['status'];
        if ($status == 1) {
            $userDetail = $authentication['userDetails'];

            $destributed_balance = Balance::sum('user_balance');
            $destributed_balance = $destributed_balance - $userDetail->balance->user_balance;
            $balances = User::where('role_id', '!=', '3')->join('balances', 'users.id', '=',
                'balances.user_id')->selectRaw('users.role_id,sum(balances.user_balance)
                 as user_balance,count(users.id) as userId')->groupBy('role_id')->get();//->sum('user_balance');
            $balanceData = array();
            foreach ($balances as $key => $balance) {
                $balanceData[$key]['roleName'] = $balance->role->role_title;
                $balanceData[$key]['amount'] = number_format($balance->user_balance, 2);
                $balanceData[$key]['id'] = $balance->userId;
            }
            $balanceData = json_decode(json_encode($balanceData));


            //success pending failed refunded
            $reports = Report::selectRaw('count(id) as txnCount,sum(amount) as totalVolume, status_id')
                ->whereDate('created_at', '=', date('Y-m-d'))->whereIn('status_id', [1, 2, 3, 4])
                ->groupBy('status_id')->get();
            $result = array();
            $result['successTxnCount']
                = $result['successVolume']
                = $result['failTxnCount']
                = $result['failVolume']
                = $result['refundedTxnCount']
                = $result['refundedVolume']
                = $result['pendingTxnCountToday']
                = $result['pendingVolumeToday'] = 0;

            foreach ($reports as $report) {
                if ($report->status_id == 1) {
                    $result['successTxnCount'] = $report->txnCount;
                    $result['successVolume'] = $report->totalVolume;
                } elseif ($report->status_id == 2) {
                    $result['failTxnCount'] = $report->txnCount;
                    $result['failVolume'] = $report->totalVolume;
                } elseif ($report->status_id == 4) {
                    $result['refundedTxnCount'] = $report->txnCount;
                    $result['refundedVolume'] = $report->totalVolume;
                } elseif ($report->status_id == 3) {
                    $result['pendingTxnCountToday'] = $report->txnCount;
                    $result['pendingVolumeToday'] = $report->totalVolume;
                }
            }

            return response()->json([
                'status' => 1,
                'distributed_balance' => number_format($destributed_balance, 2),
                'balanceData' => $balanceData,
                'data' => $result]);

        } else return $authentication;
    }

    public function retailerDashboard(Request $request)
    {

        $rules = array(
            'userId' => 'required|numeric',
            'token' => 'required',
        );
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return Response::json(array(
                'status' => 10,
                'message' => $validator->getMessageBag()->toArray()
            ));
        }
        $authentication = $this->checkLoginAuthentication($request);
        $status = $authentication['status'];
        if ($status == 1) {
            $userDetail = $authentication['userDetails'];

            $down_bank_list = $this->getBankDownList();
            if ($userDetail->role_id == 5) {
                $reportDetail = Report::selectRaw('id,api_id,number,customer_number,
            status_id,amount,recharge_type,provider_id,debit_charge,created_at')
                    ->orderBy('id', 'desc')->where('user_id', $userDetail->id)
                    ->whereIn('status_id', [1, 3, 2, 4, 21, 20, 24,34])
                    ->take(10)
                    ->get();

                $reports = $reportDetail->map(function ($report) {
                    return [
                        'created_at' => $report->created_at->format('d-m-Y H:i:s'),
                        'number' => $report->number,
                        'operator' => ($report->recharge_type == 1) ?
                            $report->provider->provider_name : $report->api->api_name,
                        'status' => $report->status->status,
                        'amount' => $report->amount,
                    ];
                });


                $holidays = Holiday::selectRaw('id,holiday_date,name')
                    ->where('active_holiday', 1)
                    ->whereDate('holiday_date', '>=', date('Y-m-d'))
                    ->orderBy('holiday_date', 'ASC')
                    ->take(3)
                    ->get();

                return response()->json([
                    'status' => '1',
                    'hasHoliday' => count($holidays) ? true : false,
                    'hasReport' => count($reports) ? true : false,
                    'message' => 'success',
                    'LMS' => $this->getSaleTxn("LMS", $userDetail),
                    'CMS' => $this->getSaleTxn("CMS", $userDetail),
                    'TS' => $this->getSaleTxn("TS", $userDetail),
                    'holidays' => $holidays,
                    'reports' => $reports,

                    'bankDownList' => $down_bank_list,
                ]);

            } else if ($userDetail->role_id == 13) {
                return redirect('admin/otp');
            } else if ($userDetail->role_id == 12) {
                return redirect('admin/bankupdown');
            } elseif (in_array($userDetail->role_id, array(4))) {

                $first_day_this_month = date('Y-m-01');
                $startDate = date("Y-m-d") . " 00:00:00";
                $endDate = date("Y-m-d") . " 23:23:23";
                $creditDebitBalance = Report::selectRaw('sum(amount) as amount,status_id')
                    ->whereIn('status_id', [6, 7])
                    ->where('user_id', $userDetail->id)
                    ->whereBetween('created_at', [$startDate, $endDate])
                    ->groupBy('status_id')->get();
                $receivedBalance = $transferedBalance = 0;
                foreach ($creditDebitBalance as $crdb) {
                    if ($crdb->status_id == 7)
                        $receivedBalance = $crdb->amount;
                    elseif ($crdb->status_id == 6)
                        $transferedBalance = $crdb->amount;
                }

                if ($userDetail->role_id == 4)
                    $retailerList = $this->getDistAgent($userDetail->id);
                $todayNetworkDetails = Report::selectRaw('sum(amount) as amount')->whereIn('user_id', $retailerList)->whereIn('status_id', [1, 3])->whereBetween('created_at', [$startDate, $endDate])->get();
                $todayNetworkAmount = $todayNetworkDetails[0]->amount;
                //DB::enableQueryLog();
                $monthNetworkDetails = Report::selectRaw('sum(amount) as amount')->whereIn('user_id', $retailerList)->whereIn('status_id', [1, 3])->whereBetween('created_at', [$first_day_this_month, $endDate])->get();
                //print_r(DB::getQueryLog());die;
                $monthNetworkAmount = $monthNetworkDetails[0]->amount;
                //print_r($monthNetworkDetails);die;
                $data = ['receivedBalance' => $receivedBalance, 'transferedBalance' => $transferedBalance, 'todayNetworkAmount' => $todayNetworkAmount, 'monthNetworkAmount' => $monthNetworkAmount, 'recharge_balance' => 0, 'paytm_balance' => 0, 'paytm_acc' => 0, 'paytm_txnid' => 0, 'apis_down' => '', 'down_bank_list' => $down_bank_list, 'purchase_balance' => 0, 'total_profit' => 0, 'success_transaction' => 0];
                return view('admin.dashboard')->with($data);
            } elseif (in_array($userDetail->role_id, array(1, 3, 4, 10, 11, 14))) {
                if ($request->getBalanceOf == "TRMO")
                    return $this->getTramoBalance();
                elseif ($request->getBalanceOf == "CYBER")
                    return $this->getCyberBalance();

                else {
                    $tramoBalance = $this->getTramoBalance();
                    $datas = $this->getCyberBalance();

                }
                $company_name = $user_balance = $user_commission = $destributed_balance = $recharge_balance = $apis_down = $purchase_balance = $total_profit = $success_transaction = $fail_transaction = $pending_transaction = $refunded_transaction = $balance_request = $apiuser_balance = $masterdist_balance = $dist_balance = $retailer_balance = $complain = $success_balance = $fail_balance = $pending_balance = $refunded_balance = $request_amount = 0;
                $master_users = User::where('role_id', '=', '3')->selectRaw('id')->get();
                $masterdist_balance = Balance::whereIn('user_id', $master_users)->sum('user_balance');

                $dist_users = User::where('role_id', '=', '4')->selectRaw('id')->get();
                $dist_balance = Balance::whereIn('user_id', $dist_users)->sum('user_balance');

                $retailer_users = User::where('role_id', '=', '5')->selectRaw('id')->get();
                $retailer_balance = Balance::whereIn('user_id', $retailer_users)->sum('user_balance');

                $data = ['user_balance' => $user_balance, 'user_commission' => $user_commission, 'comp_name' => $company_name, 'distributed_balance' => $destributed_balance, 'recharge_balance' => $recharge_balance, 'paytm_balance' => 0, 'paytm_acc' => 0, 'paytm_txnid' => 0, 'apis_down' => $apis_down, 'down_bank_list' => $down_bank_list, 'purchase_balance' => $purchase_balance, 'total_profit' => $total_profit, 'success_transaction' => $success_transaction, 'fail_transaction' => $fail_transaction, 'pending_transaction' => $pending_transaction, 'refunded_transaction' => $refunded_transaction, 'balance_request' => $balance_request, 'apiuser_balance' => $apiuser_balance, 'masterdist_balance' => $masterdist_balance, 'dist_balance' => $dist_balance, 'retailer_balance' => $retailer_balance, 'complain' => $complain, 'success_balance' => $success_balance, 'fail_balance' => $fail_balance, 'pending_balance' => $pending_balance, 'refunded_balance' => $refunded_balance, 'request_amount' => $request_amount];
                return view('admin.dashboard', compact('tramoBalance', 'datas'))->with($data);
            }
            return response()->json(['status' => 2, 'message' => 'You are not authenticate']);
        } else return $authentication;
    }

    private function getSaleTxn($type, $userDetail)
    {
        $reportSaleQuery = Report::selectRaw('sum(amount) as totalSale,count(id)
         as txnCount, status_id');
        $reportSaleQuery->where('status_id', 1);
        $reportSaleQuery->groupBy('status_id');
        if ($type == "LMS")
            $reportSaleQuery->whereMonth('created_at', date('m', strtotime("-1 months")));
        elseif ($type == "CMS")
            $reportSaleQuery->whereMonth('created_at', date('m'));
        elseif ($type == "TS")
            $reportSaleQuery->whereDate('created_at', date('Y-m-d'));
        if ($userDetail->role_id == 5)
            $reportSaleQuery->where('user_id', $userDetail->id);
        $result = $reportSaleQuery->get();
        if (count($result)) {
            return [
                'totalVolume' => $result[0]->totalSale,
                'txnCount' => $result[0]->txnCount];
        }
        return [
            'totalVolume' => 0,
            'txnCount' => 0];
    }
}
