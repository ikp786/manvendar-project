<?php

namespace App\Http\Controllers\Mobile;

use App\Http\Controllers\Controller;
use App\Provider;
use App\Bankdetail;
use App\Company;
use App\Traits\CustomAuthTraits;
use App\Traits\CustomTraits;
use App\Upscheme;
use App\Pmethod;
use App\Http\Requests;
use App\User;
use App\Report;
use App\Loan;
use Illuminate\Support\Facades\DB;
use App\Loadcash;
use App\Circle;
use App\Netbank;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Validator;
use Response;
use App\Masterbank;
use App\Complain;
use App\Enquiryview;
use App\Enquiry;

class ComplainController extends Controller
{
    use CustomAuthTraits,CustomTraits;
    public function complainRequestView(Request $request)
    {

        $rules = array(
            'token' => 'required',
            'userId' => 'required',
        );
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return Response::json(array(
                'success' => false,
                'errors' => $validator->getMessageBag()->toArray()
            ));
        }
        //print_r($request->all());die;
        $authentication = $this->checkLoginAuthentication($request);
        if ($authentication['status'] == 1) {
            $userDetail = $authentication['userDetails'];


            $complainDetailsQuery = Complain::orderBy('id', 'asc');
            if ($request->fromdate != '') {
                $start_date = ($request->fromdate) ? $request->fromdate . " 00:00:00" : date("Y-m-d") . " 00:00:00";
                $end_date = ($request->todate) ? $request->todate . " 23:59:59" : date("Y-m-d H:i:s");
                $start_date = date("Y-m-d H:i:s", strtotime($start_date));
                $end_date = date("Y-m-d H:i:s", strtotime($end_date));
                $complainDetailsQuery->whereBetween('created_at', [$start_date, $end_date]);
            }
            if ($request->searchOf != '')
                $complainDetailsQuery->where('status_id', $request->searchOf);
            if ($userDetail->role_id == 1) {
                $complainDetails = $complainDetailsQuery->simplePaginate(40);
            } elseif ($userDetail->role_id == 3) {
                $members = $this->getMdMember();
                $complainDetailsQuery->whereIn('user_id', $members);
                $complainDetails = $complainDetailsQuery->simplePaginate(40);

            } elseif ($userDetail->role_id == 4) {
                $members = $this->getDistMember();
                $complainDetailsQuery->whereIn('user_id', $members);
                $complainDetails = $complainDetailsQuery->simplePaginate(40);
            } else {
                $members = array($userDetail->id);
                $complainDetailsQuery->whereIn('user_id', $members);
                $complainDetails = $complainDetailsQuery->simplePaginate(40);
            }

            if ($request->page)
                $pageNo = $request->page + 1;
            else
                $pageNo = 2;

            $complains = $complainDetails->map(function ($complain){
                return [
                    'id'=>$complain->id,
                    'user_id'=>$complain->user_id,
                    'created_at'=>$complain->created_at->format('d-m-Y H:i:s'),
                    'updated_at'=>$complain->updated_at->format('d-m-Y H:i:s'),
                    'issue_type'=>$complain->issue_type,
                    'txn_id'=>$complain->txn_id,
                    'looking_by'=>$complain->looking_by,
                    'approved_by'=>$complain->approved_by,
                    'status_id'=>$complain->status_id,
                    'status'=>$complain->status->status,
                    'approved_date'=>$complain->approved_date,
                    'current_status_remark'=>($complain->current_status_remark)? $complain->current_status_remark:"N/A",
                    'remark'=>$complain->remark,
                ];
            });
            return response()->json([
                'status'=>'1',
                'message'=>'success',
                'page'=>"page=" . $pageNo,
                'count' => count($complains),
                'complains'=>$complains,
            ]);
        } else return $authentication;
    }

    public function complain()
    {
        if (Auth::user()->role_id == 5) {
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
            $l_user = Auth::user()->name;
            $complain = Complain::orderBy('com_id', 'DESC')->where('name', $l_user)->get();
            return view('reports.complain', compact('complain'));
        }
        return view('errors.premission-denied');
    }

    public function complain_req(Request $request)
    {
        $rules = array('amount' => 'required',
            'product' => 'required',
            'issue_type' => 'required',
            'txn_id' => 'required',
            'bank_ac' => 'required',
            'remark' => 'required'
        );
        $validator = Validator::make($request->all(), $rules);

        $now = new \DateTime();
        $datetime = date('d-m-y');
        $amount = $request->input('amount');
        $date = $request->input('dod');
        $newDate = date("Y-m-d", strtotime($date));
        $product = $request->input('product');
        $issue = $request->input('issue_type');
        $txn_id = $request->input('txn');
        $acount = $request->input('acno');
        $remark = $request->input('remark');
        $complain = ['user_id' => Auth::getUser()->id, 'name' => Auth::user()->name, 'created_at' => $datetime, 'product' => $product, 'issue_type' => $issue, 'date_txn' => $newDate, 'txn_id' => $txn_id, 'bank_ac' => $acount, 'amount' => $amount, 'remark' => $remark, 'status' => 'Pending'];
        Complain::create($complain);

        return Redirect::action('ComplainController@complain');

    }


    public function store_complain_req(Request $request)
    {//dd($request->all());die();
        $c_detail = Complain::where('txn_id', $request->complainTxnId)->whereIn('status_id', [3, 9, 31])->first();
        if ($c_detail) {
            return Response::json(['status' => 0, 'message' => 'Company alreday raised for this txn. Please Check Complain Report']);
        }
        $complainTxnId = $request->complainTxnId;
        $issueType = $request->issueType;
        $complainRemark = $request->complainRemark;
        $complain = ['user_id' => Auth::id(), 'issue_type' => $issueType, 'txn_id' => $complainTxnId, 'remark' => $complainRemark,];
        Complain::create($complain);
        if ($complain) {
            return Response::json(['status' => 1, 'message' => 'Complain Succesfully Submited']);
        }
    }

    public function update(Request $request)
    {
        $complain = Complain::find($request->complainId);
        $complain->current_status_remark = $request->current_status_remark;
        $complain->status_id = $request->status_id;
        if ($request->status_id == 30) {
            $complain->approved_by = Auth::id();
            $complain->approved_date = date("Y-m-d H:i:s");
        }
        $complain->looking_by = Auth::id();
        $complain->save();
        return response()->json(['status' => 1, 'message' => "Complin Updated Succesfully"]);
    }

    public function delete_req(Request $request)
    {
        $id = $request->del_id;
        $delete = DB::table('complains')
            ->where('com_id', $id)
            ->delete();
        if ($delete) {
            return "Record Deleted Succesfully";

        }
    }

    public function agentUpdate(Request $request)
    {
        $a_remark = $request->a_rem;
        $co_id = $request->co_id;
        $u_record = DB::table('complains')
            ->where('com_id', $co_id)
            ->update(array('remark' => $a_remark, 'status' => 'Pending'));
        if ($u_record) {
            return "successfully Updated!";
        } else {
            return "Somthing is Wrong!";
        }
    }

    public function agentDelete(Request $request)
    {

        $co_id = $request->co_id;
        $delete = DB::table('complains')
            ->where('com_id', $co_id)
            ->delete();
        if ($delete) {
            return "Record Deleted Succesfully";

        }
    }

    public function enquiries()
    {
        $enquiry = Enquiry::orderBy('id', 'DESC')->get();
        return view('admin.enquiry', compact('enquiry'));

    }

    function conversation_view(Request $request)
    {
        $conversation_view = Enquiryview::where('conv_id', $request->id)->get();
        $response["data"] = array();
        foreach ($conversation_view as $conversation_views) {
            $data['message'] = $conversation_views['message'];
            $data['name'] = $conversation_views['name'];
            $data['created_at'] = $conversation_views['created_at'];

            array_push($response["data"], $data);
        }
        header('Access-Control-Allow-Origin: *');
        header('content-type: application/json; charset=utf-8');
        return json_encode($response);
    }

    public function bank_acc_digit(Request $request)
    {
        $bank_digit = DB::table('masterbanks')->where('bank_name', $request->bankname)->first();
        return $bank_digit->account_digit;
    }

    public function update_enquiries(Request $requiest)
    {
        DB::table('enquiries')
            ->where('id', $requiest->id)
            ->update(array('message' => $requiest->message, 'status' => $requiest->status, 'assigned' => $requiest->assign));
        return $requiest->status;
    }

    public function view_comission()
    {
        $view_commision = DB::table('recharge_comissions')->paginate(40);
        return view('report.view_comission', compact('view_commision'));
    }

    public function conversation_update(Request $request)
    {
        $conv_id = $request->conv_id;

        $msg = $request->msg;

        $name = $request->assign;

        $insert_conv = Enquiryview::create(['conv_id' => $conv_id, 'name' => $name, 'message' => $msg, 'status' => 1]);

        if ($insert_conv) {
            return array('conv_id' => $conv_id, 'name' => '', 'message' => $msg, 'status' => 1);
        } else return "Somthing is wrong to insert data!";
    }

    public function view_loan()
    {
        $view_loan = Loan::all();
        return view('admin.viewloan', compact('view_loan'));

    }
}