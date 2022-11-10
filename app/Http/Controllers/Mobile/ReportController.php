<?php

namespace App\Http\Controllers\Mobile;

use App\Api;
use App\User;
use Illuminate\Http\Request;
use App\Report;
use App\Http\Requests;
use App\Loadcash;
use Response;
use Validator;
use App\Http\Controllers\Controller;
use App\Traits\CustomTraits;
use App\Traits\CustomAuthTraits;
use App\MemberActiveBank;

class ReportController extends Controller
{
    use CustomAuthTraits, CustomTraits;

    //-----------------------WORKED BY AKASH KUMAR DAS ON DATE : 15-07-2019-------------------//


    public function allRechargeReport(Request $request)
    {

        $rules = array(
            'userId' => 'required',
            'token' => 'required',
        );


        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return Response::json(array(
                'success' => 10,
                'errors' => $validator->getMessageBag()->toArray()
            ));
        }
        $authentication = $this->checkLoginAuthentication($request);
        if ($authentication['status'] != 1)
            return $authentication;

        $userDetails = $authentication['userDetails'];
        $isDateWithProductStatus = false;

        if (isset($request->start_date) && $request->end_date != '') {
            $start_date = $request->start_date;
            $end_date = $request->end_date;
            $isDateWithProductStatus = true;
        }


        $reportQuery = Report::orderBy('id', 'DESC');
        if ($userDetails->role_id == 4) //distributor
        {
            $members = User::where('parent_id', $userDetails->id)->orWhere('id', $userDetails->id)->pluck('id', 'id')->toArray();
            $reportQuery->whereIn('user_id', $members);
        } else//retailer role id 5
        {
            $reportQuery->where('user_id', $userDetails->id);
        }

        if ($isDateWithProductStatus) {

            $dbFormatDate = $this->getDbFromatDate($start_date, $end_date);
            $reportQuery->whereBetween('created_at', [$dbFormatDate['start_date'], $dbFormatDate['end_date']]);

            if (isset($request->statusOf) && $request->statusOf != '') {
                $reportQuery->where('status_id', $request->statusOf);
            }
            if (isset($request->productOf) && $request->productOf != '') {
                $reportQuery->where('api_id', $request->productOf);
            }

        } else {
            if ($request->SEARCH_TYPE == "RECORD_ID")
                $reportQuery->where('id', $request->SEARCH_INPUT);
            elseif ($request->SEARCH_TYPE == "TXN_ID") {
                $reportQuery->where('txnid', $request->SEARCH_INPUT);
                if ($userDetails->role_id == 1)
                    $reportQuery->orWhere('id', $request->SEARCH_INPUT);
            } elseif ($request->SEARCH_TYPE == "ACCOUNT_NO")
                $reportQuery->where('number', $request->SEARCH_INPUT);
        }

        $finalReportQuery = $reportQuery->simplePaginate(30);
        if ($request->page) $pageNo = $request->page + 1;
        else $pageNo = 2;
        $reports = $this->getLedgerReportDetails($finalReportQuery, $userDetails->role_id);


        return response()->json(['status' => 1, 'reports' => $reports,
            'count' => count($reports), 'page' => "page=" . $pageNo]);
    }

    public function summaryReport(Request $request)
    {

        $rules = array(
            'userId' => 'required',
            'token' => 'required',
        );
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return Response::json(array(
                'success' => 10,
                'errors' => $validator->getMessageBag()->toArray()
            ));
        }
        $authentication = $this->checkLoginAuthentication($request);
        if ($authentication['status'] != 1)
            return $authentication;
        $userDetails = $authentication['userDetails'];
        $isDateWithProductStatus = false;

        if (isset($request->start_date) && $request->end_date != '') {
            $start_date = $request->start_date;
            $end_date = $request->end_date;
            $isDateWithProductStatus = true;
        }

        if (in_array($userDetails->role_id, array(5, 15))) {
            $userId = $userDetails->id;
            $reportQuery = Report::orderBy('created_at', 'desc');
            $reportQuery->where('user_id', $userId);
            $reportQuery->whereIn('status_id', [1, 2, 3, 4, 20, 21, 24]);

            if ($isDateWithProductStatus) {
                $dbFormatDate = $this->getDbFromatDate($start_date, $end_date);
                $reportQuery->whereBetween('created_at', [$dbFormatDate['start_date'],
                    $dbFormatDate['end_date']]);
                if (isset($request->statusOf) && $request->statusOf != '') {
                    $reportQuery->where('status_id', $request->statusOf);
                }

            } else {
                if ($request->SEARCH_TYPE == "RECORD_ID")
                    $reportQuery->where('id', $request->SEARCH_INPUT);
                elseif ($request->SEARCH_TYPE == "TXN_ID") {
                    $reportQuery->where('txnid', $request->SEARCH_INPUT);
                    if ($userDetails->role_id == 1)
                        $reportQuery->orWhere('id', $request->SEARCH_INPUT);
                } elseif ($request->SEARCH_TYPE == "ACCOUNT_NO")
                    $reportQuery->where('number', $request->SEARCH_INPUT);
            }


            $reportDetails = $reportQuery->simplePaginate(50);
            if ($request->page)
                $pageNo = $request->page + 1;
            else
                $pageNo = 2;
            $reports = $reportDetails->map(function ($report) {
                return [
                    'created_at' => $report->created_at->format('d-m-Y H:i:s'),
                    'id' => $report->id,
                    'rmn_no' => $report->user->mobile,
                    'bene_name' => (@!$report->beneficiary->name) ? "N/A" : $report->beneficiary->name,
                    'bene_accont' => (@$report->recharge_type == 0) ?
                        $report->number : 'N/A',
                    'ifsc_code' => (@!$report->beneficiary->ifsc) ? "N/A" : $report->beneficiary->ifsc,
                    'bank_name' => (@!$report->beneficiary->bank_name) ? "N/A" : $report->beneficiary->bank_name,
                    'remiter_number' => ($report->recharge_type == 0) ?
                        $report->customer_number : $report->number,
                    'amount' => $report->amount,
                    'type' => $report->type,
                    'number' => $report->number,
                    'per_name' => $report->client_id,
                    'txn_type' => $report->txn_type,
                    'operator' => ($report->recharge_type == 1) ?
                        $report->provider->provider_name : $report->api->api_name,
                    'op_id' => (!$report->txnid) ? "N/A" : $report->txnid,
                    'statusId' => $report->status_id,
                    'status' => $report->status->status,
                    'mode' => $report->mode,

                ];
            });
            return response()->json(['status' => 1, 'reports' => $reports,
                'count' => count($reports), 'page' => "page=" . $pageNo]);
        }
        return response()->json(['status' => 1, 'message' => 'You are not authenticate!']);
    }

    public function rechargeReportDistributor(Request $request)
    {
        $rules = array(
            'userId' => 'required',
            'token' => 'required',
        );
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return Response::json(array(
                'success' => 10,
                'errors' => $validator->getMessageBag()->toArray()
            ));
        }
        $authentication = $this->checkLoginAuthentication($request);
        if ($authentication['status'] != 1) return $authentication;
        $userDetails = $authentication['userDetails'];
        $isDateWithProductStatus = false;

        if($userDetails->role_id !=4)
            return response()->json(['status'=>'2','message'=>'you are not authenticate']);

        if (isset($request->start_date) && $request->end_date != '') {
            $start_date = $request->start_date;
            $end_date = $request->end_date;
            $isDateWithProductStatus = true;
        }

        $rechargeReports = Report::orderBy('id', 'desc');
        $rechargeReports->where('recharge_type', 1);
        $members = $this->getDistMember($userDetails->id);
        $rechargeReports->whereIn('user_id', $members);


        if ($isDateWithProductStatus) {
            $dbFormatDate = $this->getDbFromatDate($start_date, $end_date);
            $rechargeReports->whereBetween('created_at', [$dbFormatDate['start_date'],
                $dbFormatDate['end_date']]);
            if (isset($request->statusOf) && $request->statusOf != '') {
                $rechargeReports->where('status_id', $request->statusOf);
            }

        } else {
            if ($request->SEARCH_TYPE == "RECORD_ID")
                $rechargeReports->where('id', $request->SEARCH_INPUT);
            elseif ($request->SEARCH_TYPE == "TXN_ID") {
                $rechargeReports->where('txnid', $request->SEARCH_INPUT);
                if ($userDetails->role_id == 1)
                    $rechargeReports->orWhere('id', $request->SEARCH_INPUT);
            } elseif ($request->SEARCH_TYPE == "ACCOUNT_NO")
                $rechargeReports->where('number', $request->SEARCH_INPUT);
        }

        $reportDetails = $rechargeReports->simplePaginate(50);
        if ($request->page)
            $pageNo = $request->page + 1;
        else
            $pageNo = 2;
        $reports = $reportDetails->map(function ($report) {
            return [
                'created_at' => $report->created_at->format('d-m-Y H:i:s'),
                'id' => $report->id,
                'user_name' => $report->user->name,
                'number' => $report->number,
                'mobile_number' => $report->user->mobile,
                'provider' => (@$report->provider->provider_name)? @$report->provider->provider_name : "N/A",
                'amount' => number_format($report->amount, 2),
                'gst' =>  $report->gst,
                'tds' =>   $report->tds,
                'credit_amount' =>  number_format($report->credit_charge,2) ,
                'debit_amount' =>   number_format( $report->debit_charge,2),
                'txn_type' =>   $report->txn_type,
                'txn_id' => $report->txnid,
                'op_id' =>  $report->ref_id ,
                'statusId' => $report->status_id,
                'status' => $report->status->status,
                'mode' => ($report->mode) ? $report->mode : "WEB",
            ];
        });
        return response()->json(['status' => 1, 'reports' => $reports,
            'count' => count($reports), 'page' => "page=" . $pageNo]);
    }

    public function accountStatement(Request $request)
    {
        $rules = array(
            'userId' => 'required',
            'token' => 'required',
        );
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return Response::json(array(
                'success' => 10,
                'errors' => $validator->getMessageBag()->toArray()
            ));
        }
        $authentication = $this->checkLoginAuthentication($request);
        if ($authentication['status'] != 1) return $authentication;
        $userDetails = $authentication['userDetails'];


        $start_date = ($request->start_date) ? $request->start_date . " 00:00:00" : date("Y-m-d") . " 00:00:00";
        $end_date = ($request->end_date) ? $request->end_date . " 23:23:59" : date("Y-m-d H:i:s");//die;
        $start_date = date("Y-m-d H:i:s", strtotime($start_date));
        $end_date = date("Y-m-d H:i:s", strtotime($end_date));
        /* if(in_array($userDetails->role_id,array(1,11,12,14)))
             $logined_user_id = User::select('id')->where('role_id',1)->first()->id;
         else*/
        $logined_user_id = $userDetails->id;
        $reports = Report::where('user_id', $logined_user_id)->whereBetween('created_at',
            [$start_date, $end_date])->orderBy('id', 'DESC')->simplePaginate(50);

        if ($request->page)
            $pageNo = $request->page + 1;
        else
            $pageNo = 2;
        $reportDetails = $reports->map(function ($report) {
            return [
                'created_at' => $report->created_at->format('d-m-Y H:i:s'),
                'id' => $report->id,
                'user_name' => $report->user->name,
                'mobile_number' => $report->user->mobile,
                'product' => (@$report->api->api_name) ? @$report->api->api_name : "N/A",
                'bank_name' => (($report->recharge_type == 1) ? @$report->provider->provider_name : @$report->beneficiary->bank_name)
                    ? ($report->recharge_type == 1) ? @$report->provider->provider_name : @$report->beneficiary->bank_name : "N/A",
                'name' => (is_numeric($report->credit_by) ? (@$report->creditBy->name . ' ' . @$report->creditBy->prefix . ' ' . $report->credit_by) : @$report->credit_by)
                    ? is_numeric($report->credit_by) ? (@$report->creditBy->name . ' ' . @$report->creditBy->prefix . ' ' . $report->credit_by) : @$report->credit_by : "N/A",
                'number' => $report->number,
                'txn_id' => $report->txnid,
                'description' => ($report->description) ? $report->description : "N/A",
                'opening_bal' => number_format($report->opening_balance, 2),
                'amount' => number_format($report->amount, 2),
                'credit_charge' => number_format($report->credit_charge, 2),
                'debit_charge' => number_format($report->debit_charge, 2),
                'total_bal' => number_format($report->total_balance, 2),
                'remark' => ($report->remark) ? $report->remark : "N/A",
                'statusId' => $report->status_id,
                'status' => $report->status->status,
                'mode' => ($report->mode) ? $report->mode : "WEB",

            ];
        });
        return response()->json(['status' => 1, 'reports' => $reportDetails,
            'count' => count($reports), 'page' => "page=" . $pageNo]);

    }


    public function fundReport(Request $request)
    {

        $rules = array(
            'userId' => 'required',
            'token' => 'required',
        );
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return Response::json(array(
                'success' => 10,
                'errors' => $validator->getMessageBag()->toArray()
            )); // 400 being the HTTP code for an invalid request.
        }
        $authentication = $this->checkLoginAuthentication($request);
        if ($authentication['status'] != 1) return $authentication;
        $userDetails = $authentication['userDetails'];

        if (in_array($userDetails->role_id, array(5))) {
            $start_date = ($request->start_date) ? $request->start_date . " 00:00:00"
                : date("Y-m-d") . " 00:00:00";
            $end_date = ($request->end_date) ? $request->end_date . " 23:59:59"
                : date("Y-m-d H:i:s");

            $start_date = date("Y-m-d H:i:s", strtotime($start_date));
            $end_date = date("Y-m-d H:i:s", strtotime($end_date));
            $reports = Loadcash::where('user_id', $userDetails->id)
                ->whereBetween('created_at', [$start_date, $end_date])
                ->orderby('status_id')
                ->orderby('id', 'DESC')
                ->paginate(30);

            if ($request->page)
                $pageNo = $request->page + 1;
            else $pageNo = 2;

            $details = $reports->map(function ($report) {
                return [
                    'id' => $report->id,
                    'created_at' => $report->created_at->format('d-m-Y H:i:s'),
                    'username' => $report->user->name,
                    'deposit_date' => $report->deposit_date,
                    'bank_name' => (in_array($report->request_to, array(2)))
                        ? @$report->netbank->bank_name : $report->bank_name,
                    'account_number' => @$report->netbank->account_number,
                    'wallet_amount' => $report->amount,
                    'request_for' => ($report->request_to == 3 && $report->borrow_type == 1) ? "Take Borrow"
                        : (($report->request_to == 3 && $report->borrow_type == 2) ? "Pay Off" : ''),
                    'bank_ref' => $report->bankref,
                    'payment_mode' => $report->payment_mode,
                    'branch_name' => $report->loc_batch_code,
                    'request_remark' => (@$report->request_remark) ? $report->request_remark : "N/A",
                    'approval_remark' => (@$report->remark->remark) ? $report->remark->remark : "N/A",
                    'update_remark' => (@$report->report->remark) ? $report->report->remark : "N/A",
                    'status' => $report->status->status,
                    'status_id' => $report->status_id,
                    'mode' => $report->mode,
                ];
            });
            return response()->json(['status' => 1, 'reports' => $details, 'count' => count($details),
                'page' => "page=" . $pageNo,]);
        } else return response(['status' => '2', 'message' => 'Access denied! you are not authenticate']);

    }


    public function getRetailerCommission(Request $request)
    {

        $rules = array(
            'userId' => 'required',
            'token' => 'required',
        );
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return Response::json(array(
                'status' => 10,
                'message' => "Missing Param",
                'errors' => $validator->getMessageBag()->toArray()
            )); // 400 being the HTTP code for an invalid request.
        }
        $authentication = $this->checkLoginAuthentication($request);
        if ($authentication['status'] == 1) {
            $userDetails = $authentication['userDetails'];

            if ($userDetails->role_id == 5) {
                $start_date = ($request->fromdate) ? $request->fromdate . " 00:00:00" : date('Y-m-d') . ' 00:00:00';
                $end_date = ($request->todate) ? $request->todate . " 23:59:59" : date('Y-m-d') . ' 23:59:59';
                $reports = Report::selectRaw('count(id) as txnCount,sum(amount) as txnAmount, sum(credit_charge) as txnCommission,sum(abs(debit_charge)) as debitCharge,api_id,status_id')->where('user_id', $userDetails->id)->whereIn('status_id', [1, 2, 3, 20, 21, 4, 18])->whereBetween('created_at', [$start_date, $end_date])->groupBy('api_id')->groupBy('status_id')->get();
                $all_reports = array();
                foreach ($reports as $report) {
                    $all_reports[$report->api->api_name][$report->status->status]['Total Sale'] = $report->txnAmount;
                    $all_reports[$report->api->api_name][$report->status->status]['Commission'] = number_format($report->txnCommission, 2);
                    $all_reports[$report->api->api_name][$report->status->status]['Charge'] = number_format($report->debitCharge, 2);
                    $all_reports[$report->api->api_name][$report->status->status]['Txn Count'] = $report->txnCount;
                }


                return $all_reports;
            }
            return response()->json(['status' => 2, "message" => "you are not authenticate"]);

        } else return $authentication;
    }

    public function getDistributorCommission(Request $request)
    {
        $rules = array(
            'userId' => 'required',
            'token' => 'required',
        );
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return Response::json(array(
                'status' => 10,
                'message' => "Missing Param",
                'errors' => $validator->getMessageBag()->toArray()
            )); // 400 being the HTTP code for an invalid request.
        }
        $authentication = $this->checkLoginAuthentication($request);
        if ($authentication['status'] == 1) {
            $userDetails = $authentication['userDetails'];

            if ($userDetails->role_id == 4) {
                $products = Api::getActiveProdut();
                $products[''] = "All Product";
                $start_date = ($request->fromdate) ? $request->fromdate . " 00:00:00" : date('Y-m-d') . ' 00:00:00';
                $end_date = ($request->todate) ? $request->todate . " 23:59:59" : date('Y-m-d') . ' 23:59:59';
                $members = User::where('parent_id', $userDetails->id)->pluck('name', 'id')->toArray();
                $today_reports_query = Report::orderBy('created_at', 'DESC');
                if ($request->all()) {
                    //echo "hello";die;
                    if ($request->product == -1 && $request->agent != '') {
                        //echo "hello";die;
                        $today_reports_query->whereIn('status_id', [1, 2, 3]);
                        $today_reports_query->whereIn('api_id', [1, 4, 5, 10]);
                    } else if ($request->product != '')
                        $today_reports_query->where('api_id', $request->product);
                    if ($request->agent != '') {

                        $today_reports_query->whereIn('status_id', [1, 2, 3]);
                        $today_reports_query->whereIn('api_id', [1, 4, 5, 10]);
                        $today_reports_query->where('user_id', $request->agent);
                    } else {
                        $today_reports_query->whereIn('status_id', [22]);
                        $today_reports_query->whereIn('api_id', [1, 4, 5, 10]);
                        $today_reports_query->where('user_id', $userDetails->id);
                    }
                } else {

                    $today_reports_query->whereIn('status_id', [22]);
                    $today_reports_query->whereIn('api_id', [1, 4, 5, 10]);
                    $today_reports_query->where('user_id', $userDetails->id);
                }
                $today_reports_query->whereBetween('created_at', [$start_date, $end_date]);
                $today_reports_query->selectRaw('api_id ,amount, sum(amount) as Total_Sale,sum(credit_charge) as commission, count(id) as txn_count, status_id,user_id');
                $today_reports_query->groupBy('api_id');
                $today_reports_query->groupBy('status_id');
                $today_reports = $today_reports_query->get();
                //print_r($today_reports);die;
                $all_reports = array();
                $today_sales = 0;

                foreach ($today_reports as $report) {
                    $all_reports[$report->api->api_name][$report->status->status]['Total Sale'] = $report->Total_Sale;
                    $all_reports[$report->api->api_name][$report->status->status]['Commission'] = $report->commission;
                    $all_reports[$report->api->api_name][$report->status->status]['Txn Count'] = $report->txn_count;
                    $today_sales += $report->Total_Sale;
                }

                return $all_reports;
            }
            return response()->json(['status' => 2, "message" => "you are not authenticate"]);

        }
        return $authentication;
    }

    public function getAdminCommission(Request $request)
    {
        $rules = array(
            'userId' => 'required',
            'token' => 'required',
        );
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return Response::json(array(
                'status' => 10,
                'message' => "Missing Param",
                'errors' => $validator->getMessageBag()->toArray()
            )); // 400 being the HTTP code for an invalid request.
        }
        $authentication = $this->checkLoginAuthentication($request);
        if ($authentication['status'] == 1) {
            $userDetails = $authentication['userDetails'];
            if ($userDetails->role_id == 1) {
                $start_date = ($request->fromdate) ? $request->fromdate . " 00:00:00" : '2019-03-03 00:00:00';
                $end_date = ($request->todate) ? $request->todate . " 23:59:59" : date('Y-m-d') . ' 23:59:59';
                if ($request->export == "EXPORT") {
                    $reports = Report::selectRaw('id,user_id,txnid,created_at,provider_id,amount,pay_id,tds,commission, 
				beneficiary_id,description,credit_charge,abs(debit_charge) as debit_charge,api_id,status_id,profit')
                        ->where('user_id', $userDetails->id)->whereIn('status_id', [1, 2, 3, 20, 21, 4])->whereBetween('created_at', [$start_date, $end_date])->get();
                    $this->exportAccountStatement($reports);


                }
                $reports = Report::selectRaw('count(id) as txnCount,sum(amount) as txnAmount, sum(credit_charge)
			 as txnCommission,sum(abs(debit_charge)) as debitCharge,api_id,status_id')->whereIn('status_id'
                    , [1, 2, 3, 20, 21, 4, 18])->whereBetween('created_at', [$start_date, $end_date])->groupBy('api_id')
                    ->groupBy('status_id')->orderBy('status_id', 'asc')->get();
                //print_r($reports);die;
                $all_reports = array();
                foreach ($reports as $report) {
                    $all_reports[$report->api->api_name][$report->status->status]['Total Sale'] = $report->txnAmount;
                    $all_reports[$report->api->api_name][$report->status->status]['Txn Count'] = $report->txnCount;
                    $all_reports[$report->api->api_name][$report->status->status]['Charge'] = number_format($report->debitCharge, 2);
                    $all_reports[$report->api->api_name][$report->status->status]['Commission'] = number_format($report->txnCommission, 2);


                    //$today_sales += $report->Total_Sale;
                }
                return $all_reports;
            }
            return response()->json(['status' => 2, "message" => "you are not authenticate"]);
        } else return $authentication;
    }


    /**
     * @param $finalReportQuery
     * @return mixed
     */
    private function getLedgerReportDetails($finalReportQuery, $roleId)
    {
        if ($roleId == 5) {
            return $finalReportQuery->map(function ($report) {
                return [
                    'created_at' => $report->created_at->format('d-m-Y H:i:s'),
                    'id' => $report->id,
                    'rRNo' => ($report->recharge_type) ? $report->ref_id : @$report->bank_ref,
                    'remitter_number' => ($report->customer_number) ? $report->customer_number : "N/A",
                    'number' => $report->number,
                    'api_id' => $report->api_id,
                    'description' => ($report->description) ? $report->description : "N/A",
                    'bene_name' => (in_array($report->api_id,array(2,17))) ? $report->biller_name : (($report->recharge_type) ? "N/A" : ((@$report->beneficiary->name) ? @$report->beneficiary->name : "N/A")),
                    'bene_accont' => (@$report->recharge_type == 0) ?
                        $report->number : 'N/A',
                    'ifsc_code' => (@!$report->beneficiary->ifsc) ? "N/A" : $report->beneficiary->ifsc,
                    'bank_name' => (@!$report->beneficiary->bank_name) ? "N/A" : $report->beneficiary->bank_name,
                    'remark' => ($report->remark) ? $report->remar : "N/A",
                    'userName' => $report->user->name . '(' . $report->user_id . ')',
                    'product' => $report->api->api_name,
                    'txnId' => $report->txnid . ((in_array($report->status_id, array(6, 7))) ? $report->remark : "N/A"),
                    'providerName' => ($report->recharge_type) ? $report->provider->provider_name : (@$report->beneficiary->bank_name . '*' . @$report->beneficiary->ifsc . '*' . @$report->beneficiary->name),
                    'amount' => $report->amount,
                    'billerName' => ($report->biller_name) ? $report->biller_name : "N/A",
                    'credit_debit' => ($report->type) ? $report->type : "N/A",
                    'opening_balance' => number_format($report->opening_balance, 2),
                    'credit_amount' => $report->credit_charge,
                    'debit_amount' => $report->debit_charge,
                    'tds' => number_format($report->tds, 3),
                    'service_tax' => number_format($report->gst, 2),
                    'balance' => number_format($report->total_balance, 2),
                    'txn_type' => ($report->txn_type) ? $report->txn_type : "N/A",
                    'r2r_transfer' => ($report->txnid == 'DT') ? $report->description : "N/A",
                    'operator' => ($report->recharge_type == 1) ?
                        $report->provider->provider_name : $report->api->api_name,
                    'op_id' => (!$report->paytm_txn_id) ? "N/A" : $report->paytm_txn_id,
                    'status' => $report->status->status,
                    'statusId' => $report->status_id,
                    'mode' => $report->mode,
                ];
            });
        } elseif ($roleId == 4) {
            return $finalReportQuery->map(function ($report) {
                return [
                    'created_at' => $report->created_at->format('d-m-Y H:i:s'),
                    'id' => $report->id,
                    'rRNo' => ($report->recharge_type) ? $report->ref_id : @$report->bank_ref,
                    'remitter_number' => ($report->customer_number) ? $report->customer_number : "N/A",
                    'number' => $report->number,
                    'api_id' => $report->api_id,
                    'description' => ($report->description) ? $report->description : "N/A",
                    'bene_name' => (@!$report->beneficiary->name) ? "N/A" : $report->beneficiary->name,
                    'bene_accont' => (@$report->recharge_type == 0) ?
                        $report->number : 'N/A',
                    'ifsc_code' => (@!$report->beneficiary->ifsc) ? "N/A" : $report->beneficiary->ifsc,
                    'bank_name' => (@!$report->beneficiary->bank_name) ? "N/A" : $report->beneficiary->bank_name,
                    'remark' => ($report->remark) ? $report->remar : "N/A",
                    'userName' => $report->user->name . '(' . $report->user_id . ')',
                    'product' => $report->api->api_name,
                    'txnId' => $report->txnid . ((in_array($report->status_id, array(6, 7))) ? $report->remark : "N/A"),
                    'providerName' => ($report->recharge_type) ? $report->provider->provider_name : (@$report->beneficiary->bank_name . '*' . @$report->beneficiary->ifsc . '*' . @$report->beneficiary->name),
                    'amount' => $report->amount,
                    'billerName' => ($report->biller_name) ? $report->biller_name : "N/A",
                    'credit_debit' => ($report->type) ? $report->type : "N/A",
                    'opening_balance' => number_format($report->opening_balance, 2),
                    'credit_amount' => $report->credit_charge,
                    'debit_amount' => $report->debit_charge,
                    'tds' => number_format($report->tds, 3),
                    'service_tax' => number_format($report->gst, 2),
                    'balance' => number_format($report->total_balance, 2),
                    'txn_type' => ($report->txn_type) ? $report->txn_type : "N/A",
                    'r2r_transfer' => ($report->txnid == 'DT') ? $report->description : "N/A",
                    'operator' => ($report->recharge_type == 1) ?
                        $report->provider->provider_name : $report->api->api_name,
                    'op_id' => (!$report->txnid) ? "N/A" : $report->txnid,
                    'status' => $report->status->status,
                    'statusId' => $report->status_id,
                    'mode' => $report->mode,
                ];
            });
        }
    }
	
	
	public function directTransferReport(Request $request)
    {
		$rules = array(
            'userId' => 'required',
            'token' => 'required',
        );
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return Response::json(array(
                'status' => 10,
                'message' => "Missing Param",
                'errors' => $validator->getMessageBag()->toArray()
            )); // 400 being the HTTP code for an invalid request.
        }
        $authentication = $this->checkLoginAuthentication($request);
        if ($authentication['status'] != 1) return $authentication;
		
		$userDetails= $authentication['userDetails'];
		$userId = $userDetails->id;
		
		
		if($userDetails->role_id ==5)
		{
			$start_date = ($request->start_date) ? $request->start_date . " 00:00:00": date("Y-m-d") . " 00:00:00";
            $end_date = ($request->end_date) ? $request->end_date . " 23:59:59": date("Y-m-d H:i:s");

            $start_date = date("Y-m-d H:i:s", strtotime($start_date));
            $end_date = date("Y-m-d H:i:s", strtotime($end_date));
			
			$reportQuery = Report::orderBy('id','DESC')->whereIn('status_id',[6,7]);
			$reportQuery->where('user_id', $userId);    	
			/*$start_date=($request->fromdate) ? $request->fromdate ." 00:00:00" : date("Y-m-d") . " 00:00:00";
			$end_date = ($request->todate) ? $request->todate ." 23:59:59" : date("Y-m-d H:i:s");
			$start_date = date("Y-m-d H:i:s",strtotime($start_date));
			$end_date = date("Y-m-d H:i:s",strtotime($end_date));*/
			if($request->searchOf !='')
			$reportQuery->where('status_id',$request->searchOf);
			$reportQuery->whereBetween('created_at',[$start_date,$end_date]);
			$reports = $reportQuery->Paginate(40);
			
			if ($request->page)
              $pageNo = $request->page + 1;
            else $pageNo = 2;
			
			 $details = $reports->map(function ($report) {
                return [
                 
                    'created_at' => $report->created_at->format('d-m-Y H:i:s'),
					'order_id' => $report->id,
					'wallet' => ($report->recharge_type == 1) ? 'Recharge' : 'Money',
                    'username' => $report->user->name,
					'user_id'=> $report->user->id,
					'number'=>$report->number,
					'transfer_to_from' => (is_numeric($report->credit_by)) ? @$report->creditBy->name .' ('. @$report->creditBy->prefix . '-'. @$report->creditBy->id .')': @$value->credit_by,
					'firm_name' => (is_numeric($report->credit_by)) ? @$report->creditBy->member->company :'N/A',
					'ref_id' => $report->txnid ,
				    'description' => $report->description,
				    'opening_bal' => number_format($report->opening_balance,2) ,
					'credit_amount' => number_format($report->amount,2),
					'closing_bal' => number_format($report->total_balance,2),
					'bank_charge' => $report->bank_charge,
					'remark' => $report->remark,
					'status' => $report->status->status,
                ];
            });
			           return response()->json(['status' => 1, 'reports' => $details, 'count' => count($details),
                'page' => "page=" . $pageNo,]);
			
		}
    }


	
    public function reportSlip(Request $request)
    {

        $rules = array(
            'userId' => 'required',
            'token' => 'required',
            'id' => 'required',
            'type' => 'required',
        );
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return Response::json(array(
                'status' => 10,
                'message' => "Missing Param",
                'errors' => $validator->getMessageBag()->toArray()
            ));
        }

        $authentication = $this->checkLoginAuthentication($request);
        if ($authentication['status'] != 1) return $authentication;

        $reports = Report::where('id', $request->id)->get();
        $slipDetails = $this->getInvoiceSlipData($reports,$request->type);

        $slipObject = json_decode($slipDetails,true)[0];
        return response()->json([
            'status'=>'1',
            'data'=>$slipObject,
        ]);

    }

    /**
     * @param $reports
     * @return mixed
     */
    private function getInvoiceSlipData($reports,$type)
    {
        if($type == 'DMT'){
            $slipDetails = $reports->map(function ($report) {
                return [
                    'beneficiaryName' => $report->beneficiary->name,
                    'accountNumber' => $report->number,
                    'shopContact' => $report->user->mobile,
                    'prefix' => $report->user->prefix,
                    'shopName' => $report->user->member->company,
                    'billerName' => (@$report->biller_name) ? @$report->biller_name : "N/A",
                    'amount' => $report->amount,
                    'txnId' => $report->txnid,
                    'impsUtrNo' => $report->bank_ref,
                    'serviceProvider' => @$report->provider->provider_name,
                    'status' => $report->status->status,
                    'ifscCode' => $report->beneficiary->ifsc,
                    'senderMobile' => @$report->beneficiary->customer_number,
                    'recieptNo' => $report->id,
                    'createdAt' => $report->created_at->format('Y-m-d H:i:s'),
                    'transactionType' => @$report->api->api_name,
                ];
            });
        }
        else {
            $slipDetails = $reports->map(function ($report) {
                return [

                    'createdAt' => $report->created_at->format('Y-m-d H:i:s'),
                    'shopContact' => $report->user->mobile,
                    'prefix' => $report->user->prefix,
                    'shopName' => $report->user->member->company,
                    'billerName' => (@$report->biller_name) ? @$report->biller_name : "N/A",
                    'amount' => $report->amount,
                    'customerMobNO' =>(@$report->customer_number)?@$report->customer_number :"N/A",
                    'consumerIdNo' => @$report->number,
                    'paymentMode' => 'Cash',
                    'paymentChannel' => 'Agent',
                    'serviceProvider' => @$report->provider->provider_name,
                    'recieptNo' => $report->id,
                    'txnId' => $report->txnid,
                    'payId' => $report->pay_id,
                    'transactionType' => @$report->api->api_name,
                    'status'=> (@$report->status->status)?@$report->status->status:"N/A"
                ];
            });
        }

        return $slipDetails;
    }
}
