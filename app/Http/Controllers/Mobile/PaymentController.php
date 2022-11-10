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
use function PHPSTORM_META\map;
use Validator;
use Response;
use Mail;
use Hash;


class PaymentController extends Controller
{
    use CustomAuthTraits, CustomTraits;
    var $mode = "APP";


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
            $parentId = $userDetails->parent_id;
            $netbankId = $request->payba;
            $memberActBank = MemberActiveBank::where(['netbank_id' => $netbankId, 'user_id' => $parentId])->first();
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
                        'member_active_bank_id' => $memberActBank->id,
                        'request_to' => $parentId,
                        'mode' => "APP"
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
            'status_id' => 'required|numeric',
            'id' => 'required|numeric',
            'adminRemark' => 'required',
            'remark' => 'required',
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
        if ($authentication['status'] != 1)
            return $authentication;

        $userDetail = $authentication['userDetails'];
        $id = $request->id;
        $userId = $userDetail->id;
        $loadCashDetails = Loadcash::find($id);
        $amount = $loadCashDetails->amount;
        if ($amount > 5000000) {
            return response()->json(['status' => '2', 'message' => 'Sorry! this request can not approve. amount limits 5000000']);
        }
        $status_check = Loadcash::where('id', $id)->select('status_id')->first();
        if ($status_check->status_id == 3) {
            if (in_array($userDetail->role_id, array(1, 11, 12, 14)))
                $logined_user_id = User::select('id')->where('role_id', 1)->first()->id;
            else
                $logined_user_id = $userId;
            if (in_array($userDetail->role_id, array(1, 11, 12, 14, 3, 4))) {
                $wallet_new = 'user_balance';
                $wallet_detail = 'Fund ';
                $user_id = $loadCashDetails->user_id;
                $payment_id = $loadCashDetails->id;
                $bank_charge = $request->b_charge_id;
                if ($request->status_id == 2) {

                    $user_mobile = $loadCashDetails->user->mobile;
                    $user_name = $loadCashDetails->user->name;
                    $loadCashDetails->status_id = $request->status_id;
                    $loadCashDetails->remark_id = $request->remark;
                    $loadCashDetails->updated_by = $userId;
                    $loadCashDetails->save();
                    $message = "Dear " . $user_name . " Partner, Your payment requuest has been rejected";
                    $message = urlencode($message);
                    return response()->json(['status' => '1', 'message' => 'Request has been cancelled.']);
                } else if ($request->status_id == 3) {
                    return response()->json(['status' => '2', 'message' => 'Sorry Your status is still same']);
                } else {

                    $logined_user_details = User::select('id', 'balance_id')->find($logined_user_id);
                    $balanc_par = $logined_user_details->balance->user_balance;

                }
                if ($balanc_par >= $amount) {
                    $now = new \DateTime();
                    $datetime = $now->getTimestamp();
                    $ctime = $now->format('Y-m-d H:i:s');
                    if ($request->status_id == 1) {
                        $startTime = date("Y-m-d H:i:s");
                        $end_time = date('Y-m-d H:i:s', strtotime('+12 minutes', strtotime($startTime)));
                        $start_time = date('Y-m-d H:i:s', strtotime('-12 hours', strtotime($startTime)));
                        $exit_record = Report::where('txnid', $id)->whereIn('status_id', [6, 7])->whereBetween('created_at', [$start_time, $end_time])->orderBy('created_at', 'desc')->get();
                        if (count($exit_record) > 0) {
                            return response()->json(['status' => '2', 'message' => 'Sorry! this request has been operated']);
                       
                        }
                        $loadCashDetails->status_id = $request->status_id;
                        $loadCashDetails->remark_id = $request->remark;
                        $loadCashDetails->updated_by = $userId;
                        $bank_charge_amount = 0;
                        if ($bank_charge > 0) {
                            $bank_charge_amount = ($amount * $bank_charge) / 100;
                            $amount = $amount - $bank_charge_amount;
                        }
                        DB::beginTransaction();
                        try {

                            $loadCashDetails->save();
                            $user_detail = User::find($user_id);
                            /* $upscheme = Upscheme::find($user_detail->upscheme_id);
                            if ($request->wallet == 1) {
                                $schemenew = 0;
                            } else {

                                if ($upscheme) {
                                    $schemenew = $upscheme->scheme;
                                } else {
                                    $schemenew = 0;
                                }
                            } */
                            Balance::where('user_id', $user_id)->increment($wallet_new, $amount);
                            $user_detail = User::find($user_id);
                            $updatedBalance = $user_detail->balance->user_balance;
                            $insert_id = Report::insertGetId([
                                'number' => $userDetail->mobile,
                                'provider_id' => 0,
                                'amount' => $amount,
                                'api_id' => 0,
                                'description' => "Amount transfer from  " . $userDetail->name . '( ' . $userDetail->mobile . ')',
                                'status_id' => 7,
                                'pay_id' => $datetime,
                                'txnid' => $id,
                                'profit' => 0,
                                'type' => 'CR',
                                'txn_type' => 'PAYMENT',
                                'opening_balance' => ($user_detail->balance->user_balance - $amount),
                                'debit_charge' => 0,
                                'credit_charge' => 0,
                                'bank_charge' => $bank_charge_amount,
                                'total_balance' => $updatedBalance,
                                'total_balance2' => $user_detail->balance->user_commission,
                                'created_at' => $ctime,
                                'recharge_type' => 0,
                                'user_id' => $user_id,
                                'remark' => $request->adminRemark,
                                'payment_id' => $payment_id,
                                'credit_by' => $logined_user_id,
                                'updated_by' => $userId,
                                'mode' => 'APP'

                            ]);

                            Balance::where('user_id', $logined_user_id)->decrement($wallet_new, $amount);
                            $logined_user_balance = User::select('id', 'balance_id')->find($logined_user_id);
                            //$user_detail_p = User::find($user_id);
                            $insert_id = Report::insertGetId([
                                'number' => $user_detail->mobile,
                                'provider_id' => 0,
                                'amount' => $amount,
                                'api_id' => 0,
                                'profit' => 0,
                                'bank_charge' => $bank_charge_amount,
                                'description' => "Amount transfer to " . $user_detail->name . '( ' . $user_detail->mobile . ')',
                                'status_id' => 6,
                                'type' => 'DR',
                                'txn_type' => 'PAYMENT',
                                'pay_id' => $datetime,
                                'recharge_type' => 0,
                                'txnid' => $id,
                                'opening_balance' => ($logined_user_balance->balance->user_balance + $amount),
                                'debit_charge' => 0,
                                'credit_charge' => 0,
                                'payment_id' => $payment_id,
                                'total_balance' => $logined_user_balance->balance->user_balance,
                                'total_balance2' => $logined_user_balance->balance->user_commission,
                                'created_at' => $ctime,
                                'user_id' => $logined_user_id,
                                'remark' => $request->adminRemark,
                                'credit_by' => $user_detail->id,
                                'updated_by' => $userId,
                                'mode' => 'APP'
                            ]);
                            $message = "Dear " . $user_detail->name . " , Your Wallet has been Credited with Amount : $amount and updated balance is " . number_format($updatedBalance, 2) . ", Thanks A2zsuvidhaa";
                            $number = $user_detail->mobile;
                            $message = urlencode($message);
                            DB::commit();
                            $this->sendSMS($number, $message);
                            return response()->json(['status' => '1', 'message' => 'Successfully Updated']);
                        } catch (Exception $e) {
                            DB::rollback();
                            throw $e;
                            return response()->json(['status' => '2', 'message' => 'Something went wrong. Please try again...']);
                        }
                    }
                } else {
                    return response()->json(['status' => '2', 'message' => 'You do not have sufficient Balance']);
                }
            } else {
                return response()->json(['status' => '2', 'message' => 'NO Permission']);
            }

        } elseif ($loadCashDetails->status_id == 2) {
            return response()->json(['status' => '3', 'message' => 'Already Rejected']);
        } elseif ($loadCashDetails->status_id == 1) {
            return response()->json(['status' => '3', 'message' => 'Already Approved!']);
        }


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
                'message1' => $userDetail->name . ' Rs. ' . number_format($userDetail->balance->user_balance, 2),
                'message2' => $userDetail->company->company_name . ' Email : ' . $userDetail->company->company_email,
            );

            return response()->json([
                'status' => 1,
                'message' => 'success',
                'approvalDetail' => $approvalDetail,
                'banks' => $companyBanks,
            ]);
        } else return $authentication;
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
                try {
                  /*  $image = $request->d_picture;  // your base64 encoded
                    $image = str_replace('data:image/png;base64,', '', $image);
                    $image = str_replace(' ', '+', $image);
                    $imageName = str_random(10) . '.' . 'png';
                    $path = URL::to("/") . '/deposit_slip/images/'.$imageName;
                    \Image::make(file_get_contents($request->base64_image))->save($path);
                    //Storage::disk()->put()
                    \File::put(public_path('deposit_slip/images/') . $imageName, base64_decode($image));*/

                } catch (Exception $e) {
                    return response()->json(['status' => '2', 'message' => 'error in uploading image! please try again  ' . $e->getMessage()]);
                }

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
                'request_remark' => $request->remark,
                'loc_batch_code' => $request->branchCode,
                'c_online_mode' => $request->onlineMode,
            ]);

            return response()->json([
                'status' => '1',
                'message' => 'success',
            ]);

        } else return $authentication;
    }

    public function getRetailerDetail(Request $request)
    {
        $rules = array(
            'token' => 'required',
            'userId' => 'required',
            'SEARCH_TYPE' => 'required',
            'INPUT' => 'required',
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


            $usersQuery = User::selectRaw('id,name,mobile,member_id');
            if (count($request->all())) {
                if ($request->SEARCH_TYPE == "ID") {
                    //$usersQuery->where('id', 'like', '%' . trim($request['INPUT']) . '%');
                    $usersQuery->where('id', $request->INPUT);
                } elseif ($request->SEARCH_TYPE == "NUMBER") {
                    $usersQuery->where('mobile', $request->INPUT);
                }
            }
            $usersQuery->where('role_id', 5);
            $usersQuery->where('id', '!=', $userDetail->id);
            $mapQuery = $usersQuery->get();

            $data = $mapQuery->map(function ($user) {
                return [
                    'id' => $user->id,
                    'name' => $user->name,
                    'mobile' => $user->mobile,
                    'memberId' => $user->id,
                    'shopName' => $user->member->company,
                ];
            });


            return response()->json([
                'status' => '1',
                'hasData' => (count($data)) ? true : false,
                'message' => (count($data)) ? 'success' : "No Retailer Found! with "
                    . $request->SEARCH_TYPE . ' : ' . $request->INPUT,
                'data' => $data,
            ]);
        } else return $authentication;

    }

    public function verifyPin(Request $request)
    {


        $rules = array(
            'token' => 'required',
            'userId' => 'required',
            'pin' => 'required',
            'type' => 'required',
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
            if ($request->type == "TXN" && ($request->pin == $userDetail->profile->txn_pin)) {
                return response()->json(['status' => 1, 'message' => "Txn pin verified"]);
            } else {
                return response()->json(['status' => 2, 'message' => "Incorrect Pin"]);
            }
        } else return $authentication;
    }

    public function fundTransferR2r(Request $request)
    {

        $rules = array(
            'token' => 'required',
            'userId' => 'required',
            'amount' => 'required',
            'remark' => 'required',
            'dt_scheme' => 'required',
            'wallet' => 'required',
            'memberId' => 'required',
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


            $amount = $request->amount;
            if ($amount > 2000000) {
                return Response::json(array('success' => 'failure', 'message' =>
                    'Amount can not be exceed 10 Lakhs'));
            }
            $child_id = $request->memberId;
            try {
                $child_user_details = User::findOrFail($child_id);
            } catch (Exception $e) {
                return Response::json(array('status' => '2', 'message' =>
                    'User does not exist'));
            }
            $remark_content = $request->remark;
            $payment_id = $request->id;
            $dt_scheme = $request->dt_scheme;// Added By rajat
            $id = $userDetail->id;
            if ($request->wallet == 1) {
                $mybalance = $userDetail->balance->user_balance;
            } else {
                $mybalance = $userDetail->balance->user_balance;
            }


            if ($mybalance >= $amount && $amount >= 10) {
                $md = $this->transfernow($userDetail, $id, $child_id, $amount,
                    $payment_id, $remark_content,
                    $request->wallet, $dt_scheme);
                if ($md)
                    return Response::json(array('status' => '1', 'message' =>
                        'Amount Transferred Successfully'));
                else
                    return Response::json(array('status' => '2', 'message' =>
                        'Oops Something went wrong'));

            } else {
                return Response::json(array('status' => '2', 'message' =>
                    'Low Balance, Please refill your Balance,  Or Requested Amount should be greater than 9 Thanks'));
            }
        } else return $authentication;

    }

    function transfernow($userDetail, $id, $child_id, $amount, $payment_id, $remark_content,
                         $wallet, $dt_scheme)
    {

        $authbalance = $userDetail->balance->user_balance;
        if ($authbalance >= $amount && $amount >= 10) {
            $wallet_new = 'user_balance';
            $wallet_detail = 'Fund';
            $now = new \DateTime();
            $datetime = $now->getTimestamp();
            $ctime = $now->format('Y-m-d H:i:s');
            $schemenew = 0;
            $charge = ($amount * $schemenew) / 100;
            $amount = $amount - $charge;
            DB::beginTransaction();
            try {

                Balance::where('user_id', $child_id)->increment($wallet_new, $amount);
                $child_user_details = User::find($child_id);
                $insert_id = Report::insertGetId([
                    'number' => $child_user_details->mobile,
                    'provider_id' => 0,
                    'amount' => $amount,
                    'api_id' => 0,
                    'opening_balance' => $child_user_details->balance->user_balance - $amount,
                    'ip_address' => \Request::ip(),
                    'description' => "Amount transfer from  " . $userDetail->name . '( '
                        . $userDetail->mobile . ') ' . $userDetail->prefix . ' ' . $userDetail->id,
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
                    'credit_by' => $userDetail->id,
                    'remark' => $remark_content,
                    'recharge_type' => $wallet,
                    'mode' => 'APP'
                ]);
                Balance::where('user_id', $id)->decrement($wallet_new, $amount);
                $user_detail_p = User::find($id);
                $insertId = Report::insertGetId([
                    'number' => $user_detail_p->mobile,
                    'provider_id' => 0,
                    'amount' => $amount,
                    'api_id' => 0,
                    'description' => "Amount transfer to " . $child_user_details->name .
                        '( ' . $child_user_details->mobile . ') ' . $child_user_details->prefix . ' '
                        . $child_user_details->id,
                    'status_id' => 6,
                    'pay_id' => $datetime,
                    'txnid' => 'DT',
                    'opening_balance' => $user_detail_p->balance->user_balance + $amount,
                    'ref_id' => $insert_id,
                    'profit' => 0,
                    'type' => 'DR',
                    'txn_type' => 'PAYMENT',
                    'payment_id' => 0,
                    'total_balance' => $user_detail_p->balance->user_balance,
                    'total_balance2' => $user_detail_p->balance->user_commission,
                    'created_at' => $ctime,
                    'user_id' => $userDetail->id,
                    'credit_charge' => $charge,
                    'debit_charge' => 0,
                    'credit_by' => $child_user_details->id,
                    'remark' => $remark_content,
                    'recharge_type' => $wallet,
                    'mode' => 'APP'
                ]);
                $number = $child_user_details->mobile;
                $message = "Dear " . $child_user_details->name . " , Your Wallet has been Credited with Amount : $amount, Thanks " . $userDetail->company->company_name;
                $message = urlencode($message);
                DB::commit();
                $this->sendSMS($number, $message, $userDetail->company_id);
                return true;
            } catch (Exception $e) {
                DB::rollback();
                throw $e;
                return false;
            }

        } else {
            return false;
        }
    }


    public function paymentReport(Request $request)
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
            ));
        }

        $authentication = $this->checkLoginAuthentication($request);
        if ($authentication['status'] == 1) {
            $userDetails = $authentication['userDetails'];


            $start_date = ($request->fromdate) ? $request->fromdate . " 00:00:00" : date('Y-m-d') . ' 00:00:00';
            $end_date = ($request->todate) ? $request->todate . " 23:59:59" : date('Y-m-d') . ' 23:59:59';
            $start_date = date("Y-m-d H:i:s", strtotime($start_date));
            $end_date = date("Y-m-d H:i:s", strtotime($end_date));
            $loadcashes = Loadcash::where(['user_id' => $userDetails->id])->orderBy('id', 'desc')->simplePaginate();


            if ($request->page)
                $pageNo = $request->page + 1;
            else
                $pageNo = 2;


            $details = $loadcashes->map(function ($loadcash) use ($userDetails) {
                return [
                    'date' => $loadcash->created_at->format('d-m-Y H:i:s'),
                    'id' => $loadcash->id,
                    'request_to' => ($loadcash->request_to == 1) ? ($userDetails->parent->name . '( ' . $userDetails->parent->prefix . ' - ' . $userDetails->parent->id . ')') : $userDetails->company->company_name,
                    'bank_name' => ($loadcash->request_to == 1) ? @$loadcash->bank_name : (@$loadcash->netbank->bank_name . ':' . @$loadcash->netbank->bank_name),
                    'mode' => $loadcash->payment_mode,
                    'branch_code' => $loadcash->loc_batch_code,
                    'deposit_date' => $loadcash->deposit_date,
                    'amount' => $loadcash->amount,
                    'deposit_slip' => "No Slip",
                    'customer_remark' => $loadcash->request_remark,
                    'ref_id' => $loadcash->bankref,
                    'updated_remark' => (@$loadcash->report->remark) ? @$loadcash->report->remark : "N/A",
                    'remark' => $loadcash->remark->remark,
                    'status' => $loadcash->status->status,
                    'status_id' => $loadcash->status_id,
                ];
            });


            return response()->json(array(
                'status' => '1',
                'count' => count($details),
                'page' => "page=" . $pageNo,
                'message' => 'success',
                'result' => $details,
            ));


        }
        return $authentication;
    }
public function fundTransferReport(Request $request)
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
            ));
        }

        $authentication = $this->checkLoginAuthentication($request);
        $userDetails = $authentication['userDetails'];
        $userId = $userDetails->id;


        $start_date = ($request->fromdate) ? $request->fromdate . " 00:00:00" : date('Y-m-d') . ' 00:00:00';
        $end_date = ($request->todate) ? $request->todate . " 23:59:59" : date('Y-m-d') . ' 23:59:59';
        $start_date = date("Y-m-d H:i:s", strtotime($start_date));
        $end_date = date("Y-m-d H:i:s", strtotime($end_date));
        if (in_array($userDetails->role_id, array(1, 11, 14)))
            $logined_user_id = User::select('id')->where('role_id', 1)->first()->id;
        else
            $logined_user_id = $userId;
        $reportQuery = Report::where('provider_id', 0);
        $reportQuery->whereIn('status_id', [6, 7]);
        $reportQuery->where('user_id', $logined_user_id);
        $reportQuery->whereBetween('created_at', [$start_date, $end_date]);
        $reportQuery->orderBy('id', 'DESC');
        if ($request->orderId != '')
            $reportQuery->where('id', $request->orderId);
        if ($request->user != '')
            $reportQuery->where('reports.credit_by', $request->user);
        $fundReports = $reportQuery->simplePaginate(40);

        if ($request->page)
            $pageNo = $request->page + 1;
        else
            $pageNo = 2;


        $reports = $fundReports->map(function ($report) use ($userDetails) {
            return [
                'date' => $report->created_at->format('d-m-Y H:i:s'),
                'request_date' => ($report->payment_id == '') ? 'N/A' : $report->payment->created_at->format('d-m-Y H:i:s'),
                'update_date' => ($report->payment_id == '') ? 'N/A' : $report->payment->updated_at->format('d-m-Y H:i:s'),
                'order_id' => $report->id,
                'wallet' => ($report->recharge_type == 1) ? 'Recharge' : 'Money',
                'user' => $report->user->name . ' ' . $report->user->id,
                'transfer_to_from' => (is_numeric($report->credit_by)) ? @$report->creditBy->name . ' ' . @$report->creditBy->prefix . ' ' . @$report->creditBy->id : @$report->credit_by,
                'firm_name' => (is_numeric($report->credit_by)) ? @$report->creditBy->member->company : 'N/A',
                'ref_id' => $report->txnid,
                'description' => $report->description,
                'bank_ref' => (@$report->payment->bankref) ? @$report->payment->bankref : 'N/A',
                'agent_remark' => (@$report->payment->request_remark) ? @$report->payment->request_remark : 'N/A',
                'opening_bal' => number_format($report->opening_balance, 2),
                'credit_amount' => number_format($report->amount, 2),
                'closing_bal' => number_format($report->total_balance, 2),
                'bank_charge' => $report->bank_charge,
                'remark' => $report->remark,
                'status' => $report->status->status,
                'status_id' => $report->status->id,
            ];
        });


        $userDetails = User::where('parent_id', $userId)->selectRaw('name,mobile,id')->get();
        $users = array();
        foreach ($userDetails as $user) {
            $users[$user->id] = $user->member->company . ' ' . '(' . $user->mobile . ')';
        }

        return response()->json(
            [
                'status' => '1',
                'message' => 'success',
                'users' => $users,
                'count' => count($reports),
                'page' => "page=" . $pageNo,
                'result' => $reports,
            ]
        );
    }

}
