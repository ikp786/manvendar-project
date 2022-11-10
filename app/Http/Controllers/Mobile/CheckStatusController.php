<?php

namespace App\Http\Controllers\Mobile;

use App\Library\CyberDMT;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Masterbank;
use App\Company;
use App\Balance;
use App\Report;
use App\Apiresponse;
use App\Beneficiary;
use App\PremiumWalletScheme;
use App\TransactionReportException;
use App\Http\Controllers\Controller;
use App\Api;
use DB;
use Validator;
use Exception;
use Response;
use App\Traits\CustomTraits;
use App\Traits\CustomAuthTraits;

class CheckStatusController extends Controller
{
    //
    use CustomTraits, CustomAuthTraits;

public function checkTransactionCurrentStatus(Request $request)
    {

        $rules = array(
            'userId' => 'required|numeric',
            'token' => 'required',
            'id' => 'required',
        );
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return Response::json(array(
                'status' => 10,
                'message' => $validator->getMessageBag()->toArray()
            ));
        }

        $authentication = $this->checkLoginAuthentication($request);
        if($authentication['status'] !=1) return $authentication;




        try{
            $report=Report::findOrFail($request->id);
        }
        catch(Exception $e)
        {
            return response()->json(['status'=>1,'msg'=>"No Record Found"]);
        }
        if($report->status_id == 21)
            return response()->json(['status'=>2,"msg"=>"Amount Refunded."]);
        if($report->status_id == 2)
            return response()->json(['status'=>2,"msg"=>"Recharge Failed"]);
        if($report->api_id == 5)
            return $this->checkDMTTwoStatus($report);
            elseif($report->api_id == 25){
		     	if($report->txnid == '')
			    return response()->json(['status'=>0,"msg"=>"Error."]);
			return $this->checkPayTMStatus($report);
		}
        elseif($report->api_id == 4 || $report->api_id == 1){
            $cyber = new \App\Library\CyberDMT;
            return $cyber->checkStatus($report->id);//DG 1, Cyber Plat
        }		
		elseif($report->api_id == 8 || $report->api_id == 14)		
		{			
			try			
			{				
				if($report->api_id == 8)
					$content = $this->redPayCheckStatus($report);
				elseif($report->api_id == 14)
					$content = $this->mRoboticsCheckStatus($report);
				}			
				catch(Exception $e)			
				{				
				//throw $e;				
					return array('status'=>3,'txnid'=>'','ref_id'=>'','msg'=>"Api Response Fail, Please try again");		
				}	
        //elseif($report->api_id == 8){//8 redpay apiID
           // $content = $this->redPayCheckStatus($report);
            if($content['status']== 1)
            {
                $report->status_id = 1;
                $report->txnid = $content['txnid'];
                $report->ref_id = $content['ref_id'];
                $report->save();
                return response()->json(['status'=>1,"msg"=>"Success"]);
            }
            elseif($content['status']==2)
            {
                DB::beginTransaction();
                try
                {
                    if(in_array(Report::find($report->id)->status_id,array(1,3,18)))
                    {
                        $lastInsertId = $this->doFailTranaction($report);
                        $this->reverseRechCommission($report,"RECHARGE",$lastInsertId);
                        DB::commit();
                        return response()->json(['status'=>2,"msg"=>"Recharge Failed, Amount credited"]);
                    }
                    else
                    {
                        return response()->json(['status'=>2,"msg"=>"Transaction has been update, Please try again"]);
                    }
                }
                catch(Exception $e)
                {
                    DB::rollback();
                    return array('status'=>3,'txnid'=>'','ref_id'=>'','msg'=>"Recharge Pending");
                }
            }
            else{
                return array('status'=>3,'txnid'=>'','ref_id'=>'','msg'=>"Recharge Pending");
            }
        }
        elseif($report->api_id==16)
        {
            return $this->insTransactionCheckStatus($report);
        }


        else
            return response()->json(['status'=>1,'msg'=>"Check Status not allowed"]);

    }

    public function checkImpsTransactionCurrentStatus(Request $request)
    {

        $rules = array(
            'userId' => 'required|numeric',
            'token' => 'required',
            'id' => 'required',
        );
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return Response::json(array(
                'status' => 10,
                'message' => $validator->getMessageBag()->toArray()
            ));
        }
        $authentication = $this->checkLoginAuthentication($request);
        if ($authentication['status'] != 1) return $authentication;

        try {
            $report = Report::findOrFail($request->id);
        } catch (Exception $e) {
            return response()->json(['status' => 2, 'message' => "No Record Found"]);
        }
        if ($report->status_id == 21)
            return response()->json(['status' => 1, "message" => "Amount Refunded."]);
        if ($report->status_id == 2)
            return response()->json(['status' => 2, "message" => "Recharge Failed"]);
        if ($report->api_id == 5)
            return $this->checkDMTTwoStatus($report);
        elseif ($report->api_id == 4 || $report->api_id == 1) {
            $cyber = new CyberDMT;
            return $cyber->checkStatusApp($report->id);//DG 1, Cyber Plat
        } elseif ($report->api_id == 8) {//8 redpay apiID
            $content = $this->redPayCheckStatus($report);
            if ($content['status'] == 1) {
                $report->status_id = 1;
                $report->txnid = $content['txnid'];
                $report->ref_id = $content['ref_id'];
                $report->save();
                return response()->json(['status' => 1, "message" => "Success"]);
            } elseif ($content['status'] == 2) {
                DB::beginTransaction();
                try {
                    if (in_array(Report::find($report->id)->status_id, array(1, 3, 18))) {
                        $lastInsertId = $this->doFailTranaction($report);
                        $this->reverseRechCommission($report, "RECHARGE", $lastInsertId);
                        DB::commit();
                        return response()->json(['status' => 2, "message" => "Recharge Failed, Amount credited"]);
                    } else {
                        return response()->json(['status' => 2, "message" => "Transaction has been update, Please try again"]);
                    }
                } catch (Exception $e) {
                    DB::rollback();
                    return array('status' => 3, 'txnid' => '', 'ref_id' => '', 'message' => "Recharge Pending");
                }
            } else {
                return array('status' => 3, 'txnid' => '', 'ref_id' => '', 'message' => "Recharge Pending");
            }
        } elseif ($report->api_id == 16) {
            return $this->insTransactionCheckStatusApp($report);
        } else
            return response()->json(['status' => 1, 'message' => "Check Status not allowed"]);
    }


    public function getRemainingBalance(Request $request)
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
            $userDetails = $authentication['userDetails'];
            $user_balance = $userDetails->balance->user_balance;
            return response()->json([
                'status' => 1,
                'balance' => number_format($user_balance, 2)
            ]);
        } else return $authentication;

    }
    public function getNews(Request $request)
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
            $userDetails = $authentication['userDetails'];
            $com = $userDetails->company->news;
            $news = $userDetails->company->news;
            $companyDeatils = Company::find(1);
            return response()->json([
                'status' => 1,
                'companyId' => $companyDeatils->id,
                'distributorNews' => $companyDeatils->recharge_news,
                'retailerNews' => $companyDeatils->news,
            ]);
        } else return $authentication;

    }
    public function updateNew(Request $request)
    {
        $rules = array(
            'userId' => 'required|numeric',
            'token' => 'required',
            'retailerNews' => 'required',
            'companyId' => 'required',
            'distributorNews' => 'required',
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
            $userDetails = $authentication['userDetails'];
            if ($userDetails->role_id == 1) {
                $news = $userDetails->company->news;
                try {
                    $companyDeatils = Company::findOrFail($request->companyId);
                } catch (Exception $e) {
                    return response()->json(['status' => 0, 'message' => "Error."]);
                }
                $companyDeatils->news = $request->retailerNews;
                $companyDeatils->recharge_news = $request->distributorNews;
                if ($companyDeatils->save())
                    return response()->json(['status' => 1, 'message' => "News Successfully Updated"]);
                return response()->json(['status' => 0, 'message' => "News Update failed"]);
            }
        } else return $authentication;

    }
}
