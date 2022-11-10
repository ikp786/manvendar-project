<?php

namespace App\Http\Controllers\Mobile;

use App\Balance;
use App\CompanyBankDetail;
use App\Loadcash;
use App\MemberActiveBank;
use App\Pmethod;
use App\User;
use Exception;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use App\Report;
use App\Traits\CustomTraits;
use App\Traits\CustomAuthTraits;
use Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Validator;
use Response;
use Mail;
use Hash;


class FundController extends Controller
{
    use CustomAuthTraits, CustomTraits;
    var  $mode = "APP";


    public function requestloadcash(Request $request)
    {

        $authetication = $this->checkLoginAuthentication($request);
        if ($authetication['status'] == 1) {
            $userDetails = $authetication['userDetails'];
            $rules = array(
                'userId' => 'required',
                'token' => 'required',
                'amount' => 'required|numeric',
                'pmethod' => 'required',
                'payba' => 'required',
                'bankref' => 'required',
                'wallet' => 'required'
            );

            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
                return Response::json(array(
                    'success' => '2',
                    'errors' => $validator->getMessageBag()->toArray()
                ));
            }

            $amount = $request->amount;
            if ($amount < 100 || $amount > 1500000)
                return response()->json(['status' => '2', 'msg' => "Amount should be between 100 - 1500000"]);
            $parentId=$userDetails->parent_id;
            $netbankId = $request->payba;
            $memberActBank = MemberActiveBank::where(['netbank_id'=>$netbankId,'user_id'=>$parentId])->first();
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
            } else {
                try {
                    $loadcash = [
                        'user_id' => $userDetails->id,
                        'netbank_id' => $request->payba,
                        'wallet' => $request->wallet,
                        'bankref' => $request->bankref,
                        'pmethod_id' => $request->pmethod,
                        'amount' => $request->amount,
                        'status_id' => 3,
                        'member_active_bank_id'=>$memberActBank->id,
                        'request_to' => $parentId,
                        'mode'=>"APP"
                    ];
                    Loadcash::create($loadcash);
                    return response()->json(['status' => '1', 'message' => "Request has been send successfully"]);
                } catch (Exception $e) {
                    return response()->json(['status' => '2', 'message' => "Oops Something went wrong. Please try again."]);
                }
            }
        } else return $authetication;

    }
    public function getPaymentMethodBankList(Request $request)
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

        $authentication = $this->checkLoginAuthentication($request);

        if ($authentication['status'] == 1) {
            $userDetail = $authentication['userDetails'];
            if (in_array($userDetail->role_id, array(5, 4))) {


                $netbankings_pay = MemberActiveBank::where(['user_id' => $userDetail->parent_id, 'status_id' => 1])->get();
                $pmethods = Pmethod::where('status', '=', '1')->lists('payment_type', 'id');

                $details = $netbankings_pay->map(function ($netbanking) {
                    return [

                        // $netbanking->netbank->bank_name=>$netbanking->netbank_id,
                        'bank_name' => $netbanking->netbank->bank_name,
                        'netbank_id' => $netbanking->netbank_id,
                    ];
                });

                return response()->json(array(
                    'status' => '1',
                    'message' => 'success',
                    'pmethods' => $pmethods,
                    'netbankings_pay' => $details,
                ));
            } else return response()->json(array(
                'status' => '2',
                'message' => 'Role id missmatched!',
            ));
        } else return $authentication;
    }
    function agentRequestApprove(Request $request)
    {
        $rules = array(
            'userId' => 'required',
            'token' => 'required',
            'statusId' => 'required|numeric',
            'loadCashId' => 'required|numeric',
            'amount' => 'required|numeric',
            'remark' => 'required',
        );
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails())
        {
            return Response::json(array(
                'status' => 10,
                'message' => "Missing Param",
                'errors' => $validator->getMessageBag()->toArray()
            ));
        }
        $authentication = $this->checkLoginAuthentication($request);
        if ($authentication['status'] == 1)
        {
            $userDetails =$authentication['userDetails'];
            $id = $request->loadCashId;
            $amount = $request->amount;
            if($amount >1500000)
            {
                return response()->json(['status' => 2, 'message' => 'Sorry! this request can not approve. amount limits 1500000']);
            }
            elseif($amount < 100) {

                return response()->json(['status' => 2, 'message' => 'Minimum Amount should be of Rs 100']);
            }
            $loadCashDetils =  Loadcash::find($id);
            if($loadCashDetils->status_id==1)
            {
                return response()->json(['status' => 1, 'message' => 'Already Approved!']);
            }
            elseif ($request->statusId == 3) {
                return response()->json(['status' => 3, 'message' => 'Sorry Your status is still same']);
            }
            elseif($loadCashDetils->status_id==2)
            {
                return response()->json(['status' => 27, 'message' => 'Already Rejected']);
            }

            elseif($request->statusId == 2)
            {

                $loadCashDetils->status_id = $request->statusId;
                $loadCashDetils->remark_id = $request->remark;
                $loadCashDetils->updated_by =$userDetails->id;
                $loadCashDetils->save();
                //$message = "Dear ". $user_name ." Partner, Your payment requuest has been rejected";
                //$message = urlencode($message);
                return response()->json(['status' => 27, 'message' => 'Request has been cancelled.']);
            }
            elseif($loadCashDetils->status_id==3)
            {
                $logined_user_id =$userDetails->id;
                if(in_array($userDetails->role_id,array(1,11,12,14,3,4)))
                {
                    $wallet_new = 'user_balance';
                    $wallet_detail = 'Fund ';
                    $user_id = $loadCashDetils->user_id;
                    $amount = $loadCashDetils->amount;
                    if ($request->statusId == 1)
                    {
                        $balanc_par = $userDetails->balance->user_balance;
                        if ($balanc_par >= $amount)
                        {
                            $now = new \DateTime();
                            $datetime = $now->getTimestamp();
                            $ctime = $now->format('Y-m-d H:i:s');
                            $startTime = date("Y-m-d H:i:s");
                            $end_time = date('Y-m-d H:i:s', strtotime('+12 minutes', strtotime($startTime)));
                            $start_time = date('Y-m-d H:i:s', strtotime('-12 hours', strtotime($startTime)));
                            $exit_record = Report::where('txnid', $loadCashDetils->id)->whereIn('status_id', [6, 7])->whereBetween('created_at', [$start_time, $end_time])->orderBy('created_at', 'desc')->get();
                            if (count($exit_record) > 0)
                                return response()->json(['status' => 2, 'message' => 'this request may be duplicate']);
                            $loadCashDetils->status_id = $request->statusId;
                            $loadCashDetils->remark_id = $request->remark;
                            $loadCashDetils->updated_by = $userDetails->id;
                            DB::beginTransaction();
                            try {

                                $loadCashDetils->save();
                                Balance::where('user_id', $user_id)->increment($wallet_new, $amount);
                                $user_detail = User::find($user_id);
                                $insert_id = Report::create([
                                    'number' => $userDetails->mobile,
                                    'provider_id' => 0,
                                    'amount' => $amount,
                                    'api_id' => 0,
                                    'description' => $wallet_detail . ' Refill',
                                    'status_id' => 7,
                                    'pay_id' => time(),
                                    'txnid' => $loadCashDetils->id,
                                    'profit' => 0,
                                    'debit_charge' => 0,
                                    'credit_charge' => 0,
                                    'bank_charge' => 0,
                                    'total_balance' => $user_detail->balance->user_balance,
                                    'total_balance2' => $user_detail->balance->user_commission,
                                    'recharge_type' => 0,
                                    'user_id' => $user_id,
                                    'payment_id' => $loadCashDetils->id,
                                    'credit_by' => $logined_user_id,
                                    'updated_by' => $userDetails->id,
                                    'recharge_type' => 0,
									'mode' => "APP"

                                ]);

                                Balance::where('user_id', $logined_user_id)->decrement($wallet_new, $amount);
                                $logined_user_balance = User::select('id', 'balance_id')->find($logined_user_id);
                                $user_detail_p = User::find($user_id);
                                $insert_id = Report::insertGetId([
                                    'number' => $user_detail->mobile,
                                    'provider_id' => 0,
                                    'amount' => $amount,
                                    'api_id' => 0,
                                    'profit' => 0,
                                    'bank_charge' => 0,
                                    'description' => 'Fund Transfer to ' . $user_detail_p->name,
                                    'status_id' => 6,
                                    'pay_id' => time(),
                                    'recharge_type' => $request->wallet,
                                    'txnid' => $loadCashDetils->id,
                                    'debit_charge' => 0,
                                    'credit_charge' => 0,
                                    'payment_id' => $loadCashDetils->id,
                                    'total_balance' => $logined_user_balance->balance->user_balance,
                                    'total_balance2' => $logined_user_balance->balance->user_commission,
                                    'created_at' => $ctime,
                                    'user_id' => $logined_user_id,
                                    'recharge_type' => 0,
                                    'credit_by' => $user_detail_p->id,
                                    'updated_by' => $userDetails->id,
									'mode' => "APP"


                                ]);
                                $message = "Dear " . $user_detail_p->name . " Partner, Your Wallet has been Credited with Amount : $amount, Thanks PAYJST";
                                $message = urlencode($message);
                                DB::commit();

                                return response()->json(['status' => 1, 'message' => 'Successfully Updated']);
                            } catch (Exception $e) {
                                DB::rollback();
                                return response()->json(['status' => 2, 'message' => 'Something went wrong. Please try again...']);
                            }
                        } else {
                            return response()->json(['status' => 2, 'message' => 'Low Balance or minimum balance should be 100 Rs']);
                        }
                    } else {
                        return response()->json(['status' => 2, 'message' => 'In valid Request']);
                    }
                }
                else {
                    return response()->json(['status' => 2, 'message' => 'NO Permission']);
                }

            }
            return response()->json(['status' => 2, 'message' => 'Invalid Request']);
        }
        return $authentication;
    }

    public function getBankList(Request $request)
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
        $authentication = $this->checkLoginAuthentication($request);
        if ($authentication['status'] == 1) {
            $userDetail = $authentication['userDetails'];
            $companyBanks = CompanyBankDetail::where('status_id', 1)
                ->select(DB::raw("CONCAT(bank_name,' : ',account_number) AS name"), 'id')
                ->pluck('name', 'id')->toArray();

            $approvalDetail = array(
                'message1'=>$userDetail->name . ' Rs. '.number_format($userDetail->balance->user_balance,2),
                'message2'=>$userDetail->company->company_name  . ' Email : '.$userDetail->company->company_email,
            );

            return response()->json([
                'status'=>1,
                'message'=>'success',
                'approvalDetail'=>$approvalDetail,
                'banks'=>$companyBanks,
            ]);
        }else return $authentication;
    }
    public function fundRequestSave(Request $request)
    {


        $rules = array(
            'token' => 'required',
            'userId' => 'required',
            'requestTo' => 'required',
            'paymentDate' => 'required',
            'paymentMode' => 'required',
            'amount' => 'required',
            'remark' => 'required',
        );
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return Response::json(array(
                'success' => false,
                'errors' => $validator->getMessageBag()->toArray()
            ));
        }
        $authentication = $this->checkLoginAuthentication($request);
        if ($authentication['status'] == 1) {
            $userDetail = $authentication['userDetails'];

            $date = $request->paymentDate;
            $depositDate = date("Y-m-d", strtotime($date));
            if ($request->requestTo == 2) {
                if ($request->paymentMode == "OnLine") {
                    $cOnlinePaymentMode = $request->cOnlinePaymentMode;
                }
            }
           if ($request->hasAny('d_picture')) {
                //$imageName = time() . '.' . $request->hasAny('d_picture')->getClientOriginalExtension();
                //$upload_img = $request->file('d_picture')->move('deposit_slip/images', $imageName);

               $image = $request->d_picture;  // your base64 encoded
               $image = str_replace('data:image/png;base64,', '', $image);
               $image = str_replace(' ', '+', $image);
               $imageName = time().'.'.'png';

               //return  public_path().'\deposit_slip'.'\\'.$imageName;
               //Storage::disk('public')->put($imageName, base64_decode($image));
               File::move( public_path( base64_decode($image)),'deposit_slip\images');

            }




            Loadcash::create([
                'request_to' => $request->requestTo,
                'netbank_id' => $request->bankId,
                'user_id' => $userDetail->id,
                'amount' => $request->amount,
                'payment_mode' => $request->paymentMode,
                'bank_name' => $request->bankName,
                'bankref' => $request->refNumber,
                'd_picture' => @$imageName,
                'deposit_date' => $depositDate,
                'request_remark' =>$request->remark,
                'loc_batch_code' => $request->branchCode,
                'c_online_mode' => $request->onlineMode,
            ]);

            return response()->json([
                'status'=>'1',
                'message'=>'success',
            ]);

         /*   if ($userDetail->parent_id == $userDetail->parent->id) {
                $amount = $request->amount;
                $msg = "A fund request has came from " . $userDetail->name . " ( " . $userDetail->role->role_title . " ) of Amount $amount";
                $msg = urlencode($msg);

                if ($request->requestTo != 1)
                    $this->sendSMS($userDetail->company->company_mobile, $msg, 1);
                else
                    $this->sendSMS($userDetail->parent->mobile, $msg, 1);
            }
            return redirect()->back()
                ->with('success', 'Payment Request Sent');*/
        }else return $authentication;
    }

}
