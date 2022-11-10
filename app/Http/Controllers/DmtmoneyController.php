<?php

namespace App\Http\Controllers;

use App\Api;
use App\Company;
use Validator;
use App\Balance;
use App\Beneficiary;
use App\User;
use Illuminate\Http\Request;
use GuzzleHttp\Client;
use App\Netbank;
use Response;
use App\Txnslab;
use App\Verification;
use App\Clientrequest;
use App\Mdpertxn;
use App\Mdfirstpertxn;
use App\Mdsecondpertxn;
use App\Remitteregister;
use App\Http\Requests;
use App\Txnthiredslab;
use App\Distpertxn;
use App\Mdthiredpertxn;
use Carbon\Carbon;
use DB;
use App\Apiresponse;
use App\Refundrequest;
use App\Report;
use App\Commission;

use App\SendSMS;
use App\Masterbank;
use Auth;
use Exception;
use App\Http\Controllers\Controller;

class DmtmoneyController extends Controller {

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
   public function dmt_imps(Request $request)
   {
       return view('dmtimps');
   }
    public function varification(Request $request) {

        $rules = array('mobile_number' => 'required');
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return Response::json($validator->errors()->getMessages(), 400);
        } else {
            
            $mobile_number = $request->mobile_number;
            $user_id = $request->user_id;
            if($mobile_number!='')
            {
                $get_customer =  Remitteregister::where('mobile',$mobile_number)->first();
                if($get_customer)
                {
                    $remain_bal = $get_customer->total_limit-$get_customer->used_limit;
                    $paycash_remain_bal = $get_customer->paycash_limit-$get_customer->paycash_used;
                if($get_customer->status==1 && $get_customer->f_name!='' && $get_customer->l_name!='')
                {
                    $txnmanage = DB::table('txnmanages')->where('id',1)->first();
                    if($txnmanage->shighra_on){ $wallet1 = 'Shighra-F = '.$remain_bal;}else{$wallet1 = '';}
                    if($txnmanage->paytm_on){ $wallet2 = 'Shighra-P = '.$paycash_remain_bal;}else{$wallet2 = '';}
                    return Response::json(['status_id'=>20,'message'=>'customer id active','f_name'=>@$get_customer->f_name,'l_name'=>@$get_customer->l_name,'limit'=>50000,'wallet1'=>$wallet1,'wallet2'=>$wallet2,'currency'=>'INR']);
                }
                elseif($get_customer->status==1 && $get_customer->f_name=='' && $get_customer->l_name=='')
                {
                    return Response::json(['status_id'=>37,'message'=>'User(First_name,Last_name) varification pending!']);
                }
                elseif($get_customer->f_name!='' && $get_customer->l_name!='' && $get_customer->mobile!='' &&  $get_customer->status==0)
                {
                    return Response::json(['status_id'=>34,'message'=>'User varification pending!']);
                }
                else
                {
                    return Response::json(['status_id'=>31,'message'=>'There is no datas for this number']);
                }
            }
            else
            {
                return Response::json(['status_id'=>31,'message'=>'There is no datas for this number']);
            }
                    
            
            }
            else
            {
             return Response::json(['status_id'=>24,'message'=>'Bad request or parameter missing(s)!']);
            }
            }
        }
    
    

    public function add_sender(Request $request) {
        $rules = array('mobile_number' => 'required', 'fname' => 'required','fname' => 'required');
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return Response::json($validator->errors()->getMessages(), 400);
        } else {
            try
            {
            $mobile_number = $request->mobile_number;
            $fname = $request->fname;
            $lname = $request->lname;
            $user_id = Auth::id();
            if($mobile_number!='' && $fname!='' && $lname!='' && $user_id!='')
            {
             $exitsuser =  Remitteregister::where('mobile',$mobile_number)->select('mobile','f_name','l_name','status')->first();
             if($exitsuser)
             {
             if($exitsuser->mobile!='' && $exitsuser->f_name!='' && $exitsuser->l_name!='' && $exitsuser->status_id==1)
             {
                
                return Response::json(['status_id'=>33,'message'=>'User allready exits, you can get benelist using API!']);
                exit;
             }
             elseif($exitsuser->status==1 || $exitsuser->f_name=='' || $exitsuser->l_name=='')
             {
                $u_balance = Balance::where('user_id',$user_id)->select('user_balance')->first();
                 $updatename = Remitteregister::where('mobile',$mobile_number)
                 ->update(['f_name'=>@$fname,'l_name'=>@$lname,'total_limit'=>25000,'used_limit'=>'','remaining_limit'=>0]);
                 if($updatename)
                 {
                     return Response::json(['status_id'=>38,'message'=>'Mobile Number Registration successful!']);
                     exit;
                 }
                 else
                 {
                     return Response::json(['status_id'=>33,'message'=>'User allready exits, you can get benelist using API!']);
                     exit;
                 }
               
             }
         }
         else
         {
            $digits = 4;
           $otp = rand(pow(10, $digits-1), pow(10, $digits)-1);
            $insertuser = Remitteregister::insertGetId(['f_name'=>$fname,'l_name'=>$lname,'total_limit'=>25000,'mobile'=>$mobile_number,'otp'=>'','user_id'=>$user_id,'status'=>1]);
            if( $insertuser)
            {
               $message = "Dear Customer, Your Registration OTP is :" .$otp.", Thanks";
                $message = urlencode($message);
                $msgdata = $this->sendsms($request->mobile_number, $message);
                if($msgdata)
                {
                     return Response::json(['status_id'=>19,'displaymessage'=>'remmiter varification request','f_name'=>$fname,'l_name'=>$lname,'message'=>'OTP has successfully sent on your register mobile']);
                }
            }
            else
            {
                return Response::json(['status_id'=>32,'message'=>'Something went wrong,try agian!']);
            }
           }
            
            }
            else
            {
              Response::json(['status_id'=>24,'message'=>'Bad request or parameter(s) missing!']);  
            }
        }
        catch(\Exception $e)
        {
            return $e;
            return Response::json(['status_id'=>400,'message'=>'Something is wrong, Please try after sometime!']);
            exit;
        }
    }
}

public function sendsms($number, $message)
    {
        $sms_auth = '198029AfIlUskWlk5a82d240';
        $sms_sender = 'SHIGHR';
    $url = "https://control.msg91.com/api/sendhttp.php?authkey=$sms_auth&mobiles=$number&message=$message&sender=$sms_sender&route=4&country=91";
        //$url = "http://www.sms21.co.in/sms/api?username=8527735551&password=12345&senderid=LEVINM&number=$number&message=$message";
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $data = curl_exec($curl);
        curl_close($curl);
        return $data;
    }

    public function sender_verificaiton(Request $request) 
    {
        
        $rules = array('mobile_number' => 'required', 'otp' => 'required');
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return Response::json($validator->errors()->getMessages(), 400);
        } else {
            try
            {
                $otp = $request->otp;
                $mobile_number = $request->mobile_number;
                $user_id = Auth::id();
                $b_account = $request->account;
                if($otp!='' &&  $mobile_number!='' && $user_id!='')
                {
                $sender_varify = Beneficiary::where('mobile_number',$mobile_number)->where('account_number',$b_account)->first();
                
                try
                {
                    if($otp == $sender_varify->vener_id)
                    {
                       $update_remiit =  Beneficiary::where('mobile_number',$mobile_number)
                         ->where('account_number',$b_account)
                         ->update(['status_id'=>1]);
                         if($update_remiit)
                         {
                            return Response::json(['status_id'=>20,'message'=>'OTP varification successfully done!','benelist'=>0]);
                         }
                         else
                         {
                            return Response::json(['status_id'=>'Filure','message'=>'OTP varification wrong!','benelist'=>0]);
                         }
                    }
                    else
                    {
                        return Response::json(['status_id'=>36,'message'=>'Invalid OTP,try again!','benelist'=>0]);
                    }
                }
                catch(\Exception $e)
                {
                    throw $e;
                }
            }
            else
            {
               return Response::json(['status_id'=>24,'message'=>'Bad request or parameter(s) missing!']);
            }

         }
        catch(\Exception $e)
        {
            return $e;
            return Response::json(['status_id'=>400,'message'=>'Something is wrong, Please try after sometime!']);
            exit;
        }

    }
    
}

    public function resend_otp(Request $request) {
        $rules = array('mobile_number' => 'required');
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return Response::json($validator->errors()->getMessages(), 400);
        } else {
            try{
            $mobile_number = $request->mobile_number;
            $user_id = $request->user_id;
            if($mobile_number!='' && $user_id!='')
            {
            // $otp_resent = Remitteregister::where('mobile',$mobile_number)
            //     ->where('user_id',$user_id)
            //     ->select('otp','mobile')
            //     ->first();
            //     if($otp_resent)
            //     {
                    $digits = 4;
                    $otp = rand(pow(10, $digits-1), pow(10, $digits)-1);
                    $message = "Dear Customer, Your Beneficiary Registration OTP is :" .$otp.", Thanks Shighra Pay";
                    $message = urlencode($message);
                    $msgdata = SendSMS::sendsms($request->mobile_number, $message,Auth::user()->company_id);
                    if($msgdata)
                    {
                        $updateotp = Remitteregister::where('mobile',$mobile_number)
                        ->update(['otp'=>$otp]);
                        if($updateotp)
                        {
                         return Response::json(['status_id'=>21,'message'=>'OTP successfully resent on your register mobile!']);
                        }
                        else
                        {
                          return Response::json(['status_id'=>'failed','message'=>'Something went wrong,Please resend otp']);
                        }
                    }
                    else
                    {
                        return Response::json(['status_id'=>35,'message'=>'OTP Not sent, please try again!']);
                    }
                // }
                // else
                // {
                //     return Response::json(['status_id'=>2,'message'=>'Please try again after somrtime!']);
                // }
           
        }
        else
        {
            Response::json(['status_id'=>24,'message'=>'Bad request or parameter(s) missing!']);
        }
    }
    catch(\Exception $e)
        {
            return Response::json(['status_id'=>400,'message'=>'Something is wrong, Please try after sometime!']);
            exit;
        }
        }
    }

  public function get_bank_detail(Request $request)
    {
        $bankcode = $request->bankcode;
        $bank_detail = Masterbank::where('bank_code', $bankcode)->select('ifsc')->first();
        if ($bank_detail->ifsc) {
            return Response::json(array('status' => 0, 'ifsc' => $bank_detail->ifsc));
        } else {
            return Response::json(array('status' => 1, 'ifsc' => ''));
        }
    }
        public function sharp_bank_detail(Request $request)
        {
            $bankcode = $request->bankcode;
        $bank_detail = Masterbank::where('bank_code', $bankcode)->first();
        if ($bank_detail->itz) {
            return Response::json(array('status' => 0, 'itz' => $bank_detail->itz,'b_name' => $bank_detail->bank_name));
        } else {
            return Response::json(array('status' => 1, 'itz' => ''));
        }

        }

        public function get_eko_ifsc(Request $request)
        {
           $bankcode = $request->bankcode;
           $bank_detail = Masterbank::where('bank_code', $bankcode)->select('bank_code','ifsc')->first();
          if ($bank_detail) {
            return Response::json(array('status' => 1, 'ifsc' => $bank_detail->ifsc));
           } else {
            return Response::json(array('status' => 0, 'ifsc' => ''));
          }
 
        }
   public function account_name_info(Request $request) {
       $rules = array('mobile_number' => 'required');
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return Response::json($validator->errors()->getMessages(), 400);
        } else {
            $now = new \DateTime();
            $datetime = $now->getTimestamp();
            $ctime = $now->format('Y-m-d H:i:s');
            $ClientUniqueID = $request->ClientUniqueID;
            $CustomerMobileNo = $request->mobile_number;
            $BeneIFSCCode = $request->ifsc;
            $BeneAccountNo = $request->bank_account;
            $BeneName = 'shighrpay';
            $CustomerName = 'shighrpay';
            $b_code = $request->bankcode;
            $bank =  Masterbank::where('bank_code',$b_code)->select('bank_name')->first();
            $bbank_name = $bank->bank_name;
            $channel = 2;
            $user_id = Auth::id();
            $Amount = 3;
            $f_amount = 1;
            if($CustomerMobileNo!='' && $BeneIFSCCode!='' && $BeneAccountNo!='' && $BeneName!='' && $CustomerName!='' && $Amount!='' && $channel!='')
            {

            $u_balance = Balance::where('user_id',$user_id)->select('user_balance')->first();
           if($u_balance->user_balance>=$Amount && $Amount>=1)
            {
                $check_limit = Remitteregister::where('mobile',$CustomerMobileNo)->select('used_limit','total_limit')->first();
                if($check_limit)
                {
                    $month_limit = $check_limit->used_limit+$Amount;
                    if($month_limit > $check_limit->total_limit)
                    {
                        return Response::json(['status_id'=>43,'message'=>'Monthly balance limit exceeded!']);
                        exit;
                    }
                }
                $finalamount = $Amount;
              Balance::where('user_id',$user_id)->decrement('user_balance',$finalamount);
              $userbal = Balance::where('user_id',$user_id)->select('user_balance','user_id')->first();
             
              $insert_id = Report::insertGetId([
                            'number' => $BeneAccountNo,
                            'provider_id' => 41,
                            'amount' => $Amount,
                            'profit' => 0,
                            'api_id' => 2,
                            'ip_address'=>\Request::ip(),
                            'status_id' => 3,
                            'description' =>'Acc Verify('.$bbank_name.')',
                            'pay_id' => $datetime,
                            'created_at' => $ctime,
                            'user_id' => $user_id,
                            'customer_number' => $CustomerMobileNo,
                            'total_balance' => $userbal->user_balance,
                            'ackno'=> $ClientUniqueID,
                            'channel' => $channel,
                        ]);
              

              $ref_uniq_id = 'shigh'.$insert_id;
              Report::where('id',$insert_id)->update(['txnid'=>$ref_uniq_id]);
        $res = new \App\Library\GibberishAES;
        $body =  '{"ClientUniqueID":"'.$ref_uniq_id.'","CustomerMobileNo":"'.$CustomerMobileNo.'","BeneIFSCCode":"'.$BeneIFSCCode.'","BeneAccountNo":"'.$BeneAccountNo.'","BeneName":"'.$BeneName.'","Amount":"'.$f_amount.'","CustomerName":"shighrpay","RFU1":"null","RFU2":"String content","RFU3":"String content","ProductCode":"String content"}';
        $header =  '{"ClientId":25,"AuthKey":"edd885a0-2497-4387-ac6e-62b019430ee3"}';
        $headerdata = $res->enc($header,'982b0d01-b262-4ece-a2a2-45be82212ba1');
        $bodydata = $res->enc($body,'9c32f19a-ee15-4e19-865a-bba3d137afd0');
        Apiresponse::create(['report_id'=>$insert_id,'request_message'=>$body]);
        $bank_code = substr($BeneIFSCCode, 0, 4);
        $get_acc_name = Verification::where('accountNumber',$BeneAccountNo)->where('bankCode',$bank_code)->where('name','!=','')->first();
              if($get_acc_name)
              {
                $userdetail = Balance::where('user_id',$user_id)->select('user_balance')->first();
                Report::where('id', $insert_id)
              ->update(['total_balance'=>$userdetail->user_balance,'status_id' => 1, 'bank_ref' => $ref_uniq_id,'txnid'=>$ref_uniq_id ]);
                return Response::json(['status_id'=>1,'message'=>'Varification Succesful','AmountRequested'=>$Amount,'BeneName'=>$get_acc_name->name,'TxnDescription'=>'IMPS','TxnID' => $insert_id,'utr'=>'']);
              }
              else
              {
                    $insert_acco = Verification::insertGetId(['accountNumber'=>$BeneAccountNo,'bankCode'=>$bank_code,'ifsc'=>$BeneIFSCCode,'benificiaryId'=>'','customerNumber'=>$CustomerMobileNo,'bankname'=>$bbank_name]);
              }

               $endpoint = 'https://fpbservices.finopaymentbank.in/FinoMoneyTransferService/UIService.svc/FinoMoneyTransactionApi/IMPSRequest';
            

            $headers = array(
                "Content-Type:application/json",
                "Authentication:".$headerdata."",
                "Accept-Language:en-US,en;q=0.5"
            );
           $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $endpoint);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_TIMEOUT, 1000);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, "\"".$bodydata."\"");
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            $response = curl_exec($ch);
            curl_close($ch);
            
            $result = json_decode($response);
            $decriptdata =  $res->dec($result->ResponseData,'9c32f19a-ee15-4e19-865a-bba3d137afd0');
           Apiresponse::where('report_id',$insert_id)->update(['message'=>$decriptdata,'api_type'=>$result->ResponseCode.'(Acc_verify)']);
            $res_data = json_decode($decriptdata);
            if($response)
            {
                if($result->ResponseCode==0)
                {
                    if($res_data->ActCode == 0 && $res_data->TxnDescription=='IMPS')
                    {          
                        $userdetail = Balance::where('user_id',$user_id)->select('user_balance')->first();
                        Report::where('id', $insert_id)
                        ->update(['total_balance'=>$userdetail->user_balance,'status_id' => 1, 'bank_ref' => $res_data->TxnID,'txnid'=>$ref_uniq_id ]);
                        Verification::where('id',$insert_acco)->update(['name'=>$res_data->BeneName]);
                        return Response::json(['status_id'=>1,'message'=>$result->DisplayMessage,'AmountRequested'=>$res_data->AmountRequested,'BeneName'=>$res_data->BeneName,'TxnDescription'=>$res_data->TxnDescription,'TxnID' => $insert_id,'client_req_no'=>$ClientUniqueID,'utr'=>$res_data->TxnID]);
                    }
                    elseif($res_data->ActCode == 0 && $res_data->TxnDescription=='NEFT')
                    {
                        $txndetail =  Report::where('id',$insert_id)->select('amount','profit','user_id')->first();
                        $final_amount = $txndetail->amount+$txndetail->profit;
                        Balance::where('user_id',$txndetail->user_id)->increment('user_balance',$final_amount);

                        $userdetail = Balance::where('user_id',$user_id)->select('user_balance')->first();
                        Report::where('id', $insert_id)
                        ->update(['total_balance'=>$userdetail->user_balance,'status_id' => 2, 'bank_ref' => $res_data->TxnID,'txnid'=>$ref_uniq_id ]);

                        return Response::json(['status_id'=>2,'message'=>'The bank did not share name this time!','AmountRequested'=>$res_data->AmountRequested,'BeneName'=>'','TxnDescription'=>'The bank did not share name this time!','TxnID' => $insert_id,'client_req_no'=>$ClientUniqueID,'utr'=>'']);
                    }
                    elseif($res_data->ActCode=="08" || $res_data->ActCode=="401" ||$res_data->ActCode=="500"||$res_data->ActCode=="51"||$res_data->ActCode=="54"||$res_data->ActCode=="61"||$res_data->ActCode=="65"||$res_data->ActCode=="700292"||$res_data->ActCode=="91"||$res_data->ActCode=="99"||$res_data->ActCode=="998"||$res_data->ActCode=="999"||$res_data->ActCode=="M0"||$res_data->ActCode=="5001"||$res_data->ActCode=="504" ||$res_data->ActCode=="57"||$res_data->ActCode=="56"||$res_data->ActCode=="62"||$res_data->ActCode=="96"||$res_data->ActCode=="Z4"||$res_data->ActCode=="97"||$res_data->ActCode=="NO" || $res_data->ActCode=="401" || $res_data->ActCode=="500" || $res_data->ActCode=="998" || $res_data->ActCode=="5001" || $res_data->ActCode=="504" || $res_data->ActCode=="-1")
                    {
                        $txndetail =  Report::where('id',$insert_id)->select('amount','profit','user_id')->first();
                        $final_amount = $txndetail->amount+$txndetail->profit;
                        Balance::where('user_id',$txndetail->user_id)->increment('user_balance',$final_amount);

                        $userdetail = Balance::where('user_id',$user_id)->select('user_balance')->first();
                        Report::where('id', $insert_id)
                        ->update(['total_balance'=>$userdetail->user_balance,'status_id' => 2, 'bank_ref' => $res_data->ActCode,'txnid'=>$ref_uniq_id]);
                        Verification::where('id',$insert_acco)->delete();
                        return Response::json(['status_id'=>2,'message'=>'The bank did not share name this time!','AmountRequested'=>$res_data->AmountRequested,'BeneName'=>'','TxnDescription'=>'The bank did not share name this time!','TxnID' => $insert_id,'client_req_no'=>$ClientUniqueID,'utr'=>'']);


                    }
                    else
                    {
                        $txndetail =  Report::where('id',$insert_id)->select('amount','profit','user_id')->first();
                        $final_amount = $txndetail->amount+$txndetail->profit;
                        Balance::where('user_id',$txndetail->user_id)->increment('user_balance',$final_amount);

                        $userdetail = Balance::where('user_id',$user_id)->select('user_balance')->first();
                        Report::where('id', $insert_id)
                        ->update(['total_balance'=>$userdetail->user_balance,'status_id' => 2, 'bank_ref' => $res_data->ActCode,'txnid'=>$ref_uniq_id]);
                        Verification::where('id',$insert_acco)->delete();
                        return Response::json(['status_id'=>2,'message'=>'The bank did not share name this time!','AmountRequested'=>$res_data->AmountRequested,'BeneName'=>'','TxnDescription'=>'The bank did not share name this time!','TxnID' => $insert_id,'client_req_no'=>$ClientUniqueID,'utr'=>'']);
                    }
                }
                elseif($result->ResponseCode==1)
                {
        
                    if($res_data->ActCode=="05" || $res_data->ActCode=="13" || $res_data->ActCode=="1"||$res_data->ActCode=="12"||$res_data->ActCode=="14"||$res_data->ActCode=="1515"||$res_data->ActCode=="20"||$res_data->ActCode=="22"||$res_data->ActCode=="2388"||$res_data->ActCode=="30"||$res_data->ActCode=="3247"||$res_data->ActCode=="52"||$res_data->ActCode=="62"||$res_data->ActCode=="64"||$res_data->ActCode=="700322"||$res_data->ActCode=="700420"||$res_data->ActCode=="700511"||$res_data->ActCode=="92"||$res_data->ActCode=="94"||$res_data->ActCode=="96"||$res_data->ActCode=="M1"||$res_data->ActCode=="M2"||$res_data->ActCode=="M3"||$res_data->ActCode=="M4"||$res_data->ActCode=="M5"||$res_data->ActCode=="MP"||$res_data->ActCode=="9999"||$res_data->ActCode=="100030"||$res_data->ActCode=="100050")
                    {
                        $txndetail =  Report::where('id',$insert_id)->select('amount','profit','user_id')->first();
                        $final_amount = $txndetail->amount+$txndetail->profit;
                        Balance::where('user_id',$txndetail->user_id)->increment('user_balance',$final_amount);
                        $userdetail = Balance::where('user_id',$user_id)->select('user_balance')->first();
                        Report::where('id', $insert_id)
                        ->update(['total_balance'=>$userdetail->user_balance,'status_id' => 2, 'bank_ref' => $res_data->ActCode,'txnid'=>$ref_uniq_id]);
                        Verification::where('id',$insert_acco)->delete();
                        return Response::json(['status_id'=>2,'message'=>'The bank did not share name this time!','AmountRequested'=>$res_data->AmountRequested,'BeneName'=>'','TxnDescription'=>'The bank did not share name this time!','TxnID' => $insert_id,'client_req_no'=>$ClientUniqueID,'utr'=>'']);
                    }
                }
                else
                {
                    $txndetail =  Report::where('id',$insert_id)->select('amount','profit','user_id')->first();
                    $final_amount = $txndetail->amount+$txndetail->profit;
                    Balance::where('user_id',$txndetail->user_id)->increment('user_balance',$final_amount);

                    $userdetail = Balance::where('user_id',$user_id)->select('user_balance')->first();
                    Report::where('id', $insert_id)
                    ->update(['total_balance'=>$userdetail->user_balance,'status_id' => 2, 'bank_ref' => $res_data->TxnID,'txnid'=>$ref_uniq_id]);
                    Verification::where('id',$insert_acco)->delete();
                    return Response::json(['status_id'=>2,'message'=>'The bank did not share name this time!','AmountRequested'=>$res_data->AmountRequested,'BeneName'=>'','TxnDescription'=>'The bank did not share name this time!','TxnID' => $insert_id,'client_req_no'=>$ClientUniqueID,'utr'=>'']);
                }
            }
            else
            {
                return Response::json(['status'=>2,'message'=>'Response not found']); 
            }
           
        }

        else
        {
            return Response::json(['status_id'=>40,'message'=>'Insufficient balance!']);
        }
            
        }
        else
        {
            return Response::json(['status_id'=>24,'message'=>'Invalid Request or paramitter missing(s)']);
        }

    }
}

public function transaction(Request $request)  
      {
        
        $rules = array('mobile_number' => 'required|numeric|digits:10');
        $validator = Validator::make($request->all(), $rules);
        $response_result=array();
        if ($validator->fails()) {
            $response_result[1]=['status_id'=>'Failure','message'=>'Please enter valid mobile number'];
            return Response::json($validator->errors()->getMessages(), 400);
        } 
        else 
        {
            //print_r($request->all());die;
            $now = new \DateTime();
            $datetime = $now->getTimestamp();
            $ctime = $now->format('Y-m-d H:i:s');
            //$ClientUniqueID = $request->ClientUniqueID;
            $CustomerMobileNo = $request->mobile_number;
            $BeneIFSCCode = $request->ifsc;
            $BeneAccountNo = $request->bank_account;
            $BeneName = $request->bene_name;
            $bene_id = $request->beneficiary_id;
            $CustomerName = $request->mobile_number;
            $super_amount = $Amount = $request->amount;
            $channel = $request->channel;
            $user_pan = Auth::user()->member->pan_number;
            $user_pin = Auth::user()->member->pin_code;
            $user_id = Auth::id();
            if($user_pan=='' && $user_pin=='')
            {
                $response_result[1]=['status_id'=>'Failure','message'=>'Please enter valid PAN number and Pin code!'];
                exit;
            }
        if($super_amount!='' && $BeneIFSCCode!='' && $BeneAccountNo!='' && $CustomerMobileNo!='' && $channel!='' && $BeneName!='' && $CustomerName!='')  
            {

            if($super_amount <=25000&& $super_amount>=1)
            {
                $bulk_amount = $req_amount = $super_amount;
                $no_of_ite = ceil($super_amount/5000);
                $response_result=array();
                if( $no_of_ite >10 || $no_of_ite <1)
                {
                    $response_result[1]=array('status_id' => 'Failure', 'message' => "You are doing something wrong..Please confirm entered balance");
                    return response()->json(['result' => $response_result]);
                    exit;
                }
                $check_limit = Remitteregister::where('mobile',$CustomerMobileNo)->select('used_limit','total_limit','paycash_used')->first();
                if($check_limit)
                {
                	 $txnmanage = DB::table('txnmanages')->where('id',1)->first();
                     if($txnmanage)
                     {
                    	if($txnmanage->PaytmActive==1 && $channel==2 && $check_limit->paycash_used+$super_amount<25000)
                    	{
                    		$response_result= $this->paytm_transfer($CustomerMobileNo,$BeneIFSCCode,$BeneAccountNo,$BeneName,$bene_id,$CustomerName,$Amount,$channel,$user_pan,$user_pin,$user_id);
                                 return $response_result;
                    	}
                    	if($txnmanage->shighra_on==0 && $channel==2)
                    	{
                    		$response_result= $this->paytm_transfer($CustomerMobileNo,$BeneIFSCCode,$BeneAccountNo,$BeneName,$bene_id,$CustomerName,$Amount,$channel,$user_pan,$user_pin,$user_id);
                                 return $response_result;
                    	}

                        
                     }
                    $month_limit = $check_limit->used_limit+$super_amount;
                    if($month_limit > $check_limit->total_limit)
                    {
                         if($channel==2)
                        {
                          
                            $response_result= $this->paytm_transfer($CustomerMobileNo,$BeneIFSCCode,$BeneAccountNo,$BeneName,$bene_id,$CustomerName,$Amount,$channel,$user_pan,$user_pin,$user_id);
                             return $response_result;
                            
                          
                        }
                        else
                        {

                           $response_result[1] = array('status_id'=>2,'TxnID'=>'NA','message'=>'Failed,NEFT transaction not allow','AmountRequested'=>$Amount,'BeneName'=>'','TxnDescription'=>'Failed,NEFT transaction not allow more then 25k!','client_req_no'=>'','utr'=>'');
                            return response()->json(['result' => $response_result]);
                             exit;
                            
                        }
                        
                    }
                }
                $u_scheme_id = Auth::user()->scheme_id;
                $scheme_charge = $this->getRetailerChargeAmout($u_scheme_id);
                $total_charge_txn = ceil($super_amount/1000);
                $total_charge_amt = ($total_charge_txn * $scheme_charge->r);
                $finalamount = $super_amount+$total_charge_amt;
                $u_balance = Balance::where('user_id',$user_id)->select('user_balance')->first();
                if($u_balance->user_balance<$finalamount)
                {
                     $response_result[1]=['status_id'=>'Failure','message'=>'Insufficient balance!'];
                    return Response()->json(['result'=>$response_result]);
                }
                DB::beginTransaction();
                try
                {
                    Balance::where('user_id',$user_id)->decrement('user_balance',$finalamount);
                    $userbal = Balance::where('user_id',$user_id)->select('user_balance','user_id')->first();
                    if($no_of_ite > 1)
                    {
                    $ref_insert_id = Report::insertGetId([
                                'number' => $BeneAccountNo,
                                'provider_id' => 41,
                                'amount' => $super_amount,
                                'bulk_amount' => $super_amount,
                                'profit' => $total_charge_amt,
                                'api_id' => 16,
                                'ip_address'=>\Request::ip(),
                                'status_id' => 23,
                                'description' => 'DMT',
                                'pay_id' => $datetime,
                                'created_at' => $ctime,
                                'user_id' => $user_id,
                                'customer_number' => $CustomerMobileNo,
                                'total_balance' => $userbal->user_balance,
                                'beneficiary_id' => $bene_id,
                                'channel' => $channel,
                            ]);
                    }
                    Remitteregister::where('mobile',$CustomerMobileNo)->increment('used_limit',$super_amount);
                    DB::commit();
                }
                catch(Exception $e)
                {
                    DB::rollback();
                    $response_result[1]=array('status' => 'Failure', 'message' => "Whoops Something went wrong. Please try again.");
                    return response()->json(['result' => $response_result]);
                    exit;
                    
                }
                $remaining_balance=0;   
                for($i=1;$i<=$no_of_ite;$i++)
                {
                    if($req_amount > 5000)
                    {
                        $Amount = 5000;
                        $req_amount = $req_amount-5000;
                        
                    }
                    else
                    {
                        $Amount = $req_amount;
                    }
                
                    $now = new \DateTime();
                    $datetime = $now->getTimestamp();
                    $ctime = $now->format('Y-m-d H:i:s');
                    $u_scheme_id = Auth::user()->scheme_id;
                    $scheme_charge = $this->getRetailerChargeAmout($u_scheme_id);
                    if($Amount<=1000)
                    {
                        $chargeamount = $scheme_charge->r;
                    }
                    elseif($Amount>1000 && $Amount<=2000)
                    {
                        $chargeamount = $scheme_charge->r*2;
                    }
                    elseif($Amount>2000 && $Amount<=3000)
                    {
                        $chargeamount = $scheme_charge->r*3;
                    }
                    elseif($Amount>3000 && $Amount<=4000)
                    {
                        $chargeamount = $scheme_charge->r*4;
                    }
                    elseif($Amount>4000 && $Amount<=5000)
                    {
                        $chargeamount = $scheme_charge->r*5;
                    }
                    else
                    {
                       $chargeamount = $scheme_charge->r; 
                    }
                           
                    $remaining_balance = $Amount+$chargeamount;
                    $insert_record = Report::insertGetId([
                                        'number' => $BeneAccountNo,
                                        'provider_id' => 41,
                                        'amount' => $Amount,
                                        'bulk_amount' => $super_amount,
                                        'profit' => $chargeamount,
                                        'api_id' => 16,
                                        'ip_address'=>\Request::ip(),
                                        'status_id' => 3,
                                        'description' => 'DMT',
                                        'ref_id' =>isset($ref_insert_id)?$ref_insert_id:'',
                                        'pay_id' => $datetime,
                                        'created_at' => $ctime,
                                        'user_id' => $user_id,
                                        'customer_number' => $CustomerMobileNo,
                                        'total_balance' => $userbal->user_balance,
                                        'beneficiary_id' => $bene_id,
                                        'channel' => $channel,
                                        ]);
                    $insert_id =$insert_record;
                    $ref_uniq_id = 'shigh'.$insert_id;
                    Report::where('id',$insert_record)->update(['txnid'=>$ref_uniq_id]);
                    $res = new \App\Library\GibberishAES;
                    $body =  '{"ClientUniqueID":"'.$ref_uniq_id.'","CustomerMobileNo":"'.$CustomerMobileNo.'","BeneIFSCCode":"'.$BeneIFSCCode.'","BeneAccountNo":"'.$BeneAccountNo.'","BeneName":"'.$BeneName.'","Amount":"'.$Amount.'","CustomerName":"shighr","RFU1":"null","RFU2":"String content","RFU3":"String content","ProductCode":"String content"}';
                    $header =  '{"ClientId":25,"AuthKey":"edd885a0-2497-4387-ac6e-62b019430ee3"}';
                    $headerdata = $res->enc($header,'982b0d01-b262-4ece-a2a2-45be82212ba1');
                    $bodydata = $res->enc($body,'9c32f19a-ee15-4e19-865a-bba3d137afd0');
                    Apiresponse::create(['report_id'=>$insert_id,'request_message'=>$body]);
                    if($request->channel==1)
                    {

                    $endpoint = 'https://fpbservices.finopaymentbank.in/FinoMoneyTransferService/UIService.svc/FinoMoneyTransactionApi/NEFTRequest';

                    }
                    else
                    {
                    $endpoint = 'https://fpbservices.finopaymentbank.in/FinoMoneyTransferService/UIService.svc/FinoMoneyTransactionApi/IMPSRequest';
                    }

                    $headers = array(
                    "Content-Type:application/json",
                    "Authentication:".$headerdata."",
                    "Accept-Language:en-US,en;q=0.5"
                    );
                    $ch = curl_init();
                    curl_setopt($ch, CURLOPT_URL, $endpoint);
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                    curl_setopt($ch, CURLOPT_TIMEOUT, 1000);
                    curl_setopt($ch, CURLOPT_POST, true);
                    curl_setopt($ch, CURLOPT_POSTFIELDS, "\"".$bodydata."\"");
                    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
                    $response = curl_exec($ch);
                    curl_close($ch);
                    $result = json_decode($response);
					try{
                    Apiresponse::where('report_id',$insert_id)->update(['bank_enc_response'=>$response]);
                    $decriptdata =  $res->dec($result->ResponseData,'9c32f19a-ee15-4e19-865a-bba3d137afd0');
                    $res_data = json_decode($decriptdata);
                    Apiresponse::where('report_id',$insert_id)->update(['message'=>$decriptdata,'api_type'=>$result->ResponseCode.'(money_txn)','url'=>$endpoint]);
                    if($response)
                    {
                        if($result->ResponseCode==0)
                        {
                            if($res_data->ActCode == 0 && $res_data->TxnDescription=='IMPS')
                            {           
                                $message = "Dear Customer your transaction is successful TID:".$res_data->TxnID.",AMOUNT: ".$Amount.",AC NO:".$BeneAccountNo.",Time".$res_data->TransactionDatetime.", Thanks Shighra Pay";
                                $message = urlencode($message);
                                SendSMS::sendsms($request->mobile_number, $message,Auth::user()->company_id);
                                $userdetail = Balance::where('user_id',$user_id)->select('user_balance')->first();
                                Report::where('id', $insert_id)
                                ->update(['total_balance'=>$userdetail->user_balance,'status_id' => 1, 'bank_ref' => $res_data->TxnID,'txnid'=>$ref_uniq_id ]);
                                
                                $response_result[$i]=['status_id'=>1,'message'=>$result->DisplayMessage,'ActCode'=>$res_data->ActCode,'AmountRequested'=>$res_data->AmountRequested,'BeneName'=>$res_data->BeneName,'TxnDescription'=>$res_data->TxnDescription,'TxnID' => $insert_id,'client_req_no'=>$ref_uniq_id,'utr'=>$res_data->TxnID];
                            }
                            elseif($res_data->ActCode == 0 && $res_data->TxnDescription=='NEFT')
                            {
                                $message = "Dear Customer your transaction is successful TID :".$res_data->TxnID.",AMOUNT: ".$Amount.",AC NO:".$BeneAccountNo.",Time".$res_data->TransactionDatetime.", Thanks Shighra Pay";
                                $message = urlencode($message);
                                SendSMS::sendsms($request->mobile_number, $message,Auth::user()->company_id);
                                $userdetail = Balance::where('user_id',$user_id)->select('user_balance')->first();
                                Report::where('id', $insert_id)
                                ->update(['total_balance'=>$userdetail->user_balance,'status_id' => 9, 'bank_ref' => $res_data->TxnID,'txnid'=>$ref_uniq_id ]);
                                
                                $response_result[$i]=['status_id'=>9,'message'=>'Transaction Initiated!','ActCode'=>$res_data->ActCode,'AmountRequested'=>$res_data->AmountRequested,'BeneName'=>$res_data->BeneName,'TxnDescription'=>$res_data->TxnDescription,'TxnID' => $insert_id,'client_req_no'=>$ref_uniq_id,'utr'=>''];
                            }
                            elseif($res_data->ActCode=="08" || $res_data->ActCode=="401" ||$res_data->ActCode=="500"||$res_data->ActCode=="51"||$res_data->ActCode=="54"||$res_data->ActCode=="61"||$res_data->ActCode=="65"||$res_data->ActCode=="700292"||$res_data->ActCode=="91"||$res_data->ActCode=="99"||$res_data->ActCode=="998"||$res_data->ActCode=="999"||$res_data->ActCode=="M0"||$res_data->ActCode=="5001"||$res_data->ActCode=="504" ||$res_data->ActCode=="57"||$res_data->ActCode=="56"||$res_data->ActCode=="62"||$res_data->ActCode=="96"||$res_data->ActCode=="Z4"||$res_data->ActCode=="97"||$res_data->ActCode=="NO" || $res_data->ActCode=="401" || $res_data->ActCode=="500" || $res_data->ActCode=="998" || $res_data->ActCode=="5001" ||  $res_data->ActCode=="504" || $res_data->ActCode=="-1")
                            {
                                $message = "Dear Customer your transaction is successful TID:".$insert_id.",AMOUNT: ".$Amount.",AC NO:".$BeneAccountNo.",Time".$res_data->TransactionDatetime.", Thanks Shighra Pay";
                                $message = urlencode($message);
                                SendSMS::sendsms($request->mobile_number, $message,Auth::user()->company_id);
                                $userdetail = Balance::where('user_id',$user_id)->select('user_balance')->first();
                                Report::where('id', $insert_id)
                                ->update(['total_balance'=>$userdetail->user_balance,'status_id' => 3, 'bank_ref' =>'','txnid'=>$ref_uniq_id]);
                                
                                $response_result[$i]=['status_id'=>3,'TxnID'=>$insert_id,'message'=>$result->DisplayMessage,'AmountRequested'=>$res_data->AmountRequested,'BeneName'=>'','TxnDescription'=>$res_data->TxnDescription,'client_req_no'=>$ref_uniq_id,'utr'=>''];
                            }
                            else
                            {
                                $message = "Dear Customer your transaction is successful TID:".$insert_id.",AMOUNT: ".$Amount.",AC NO:".$BeneAccountNo.",Time".$res_data->TransactionDatetime.", Thanks Shighra Pay";
                                $message = urlencode($message);
                                SendSMS::sendsms($request->mobile_number, $message,Auth::user()->company_id);
                                $userdetail = Balance::where('user_id',$user_id)->select('user_balance')->first();
                                Report::where('id', $insert_id)
                                ->update(['total_balance'=>$userdetail->user_balance,'status_id' => 3, 'bank_ref' =>'','txnid'=>$ref_uniq_id]);
                                
                                $response_result[$i]=['status_id'=>3,'TxnID'=>$insert_id,'message'=>$result->DisplayMessage,'AmountRequested'=>$res_data->AmountRequested,'BeneName'=>'','TxnDescription'=>$res_data->TxnDescription,'client_req_no'=>$ref_uniq_id,'utr'=>''];
                            }
                        }
                        elseif($result->ResponseCode==1)
                        {

                            if($res_data->ActCode=="05" || $res_data->ActCode=="13" || $res_data->ActCode=="1"||$res_data->ActCode=="12"||$res_data->ActCode=="14"||$res_data->ActCode=="1515"||$res_data->ActCode=="20"||$res_data->ActCode=="22"||$res_data->ActCode=="2388"||$res_data->ActCode=="30"||$res_data->ActCode=="3247"||$res_data->ActCode=="52"||$res_data->ActCode=="62"||$res_data->ActCode=="64"||$res_data->ActCode=="700322"||$res_data->ActCode=="700420"||$res_data->ActCode=="700511"||$res_data->ActCode=="92"||$res_data->ActCode=="94"||$res_data->ActCode=="96"||$res_data->ActCode=="M1"||$res_data->ActCode=="M2"||$res_data->ActCode=="M3"||$res_data->ActCode=="M4"||$res_data->ActCode=="M5"||$res_data->ActCode=="MP"||$res_data->ActCode=="9999"||$res_data->ActCode=="100030"||$res_data->ActCode=="100050" || $res_data->ActCode=="100000" || $res_data->ActCode=="1003" || $res_data->ActCode=="1010" || $res_data->ActCode=="503")
                            {
                                $txndetail =  Report::where('id',$insert_id)->select('amount','profit','user_id')->first();
                                $final_amount = $txndetail->amount+$txndetail->profit;
                                Balance::where('user_id',$txndetail->user_id)->increment('user_balance',$final_amount);
                                $userdetail = Balance::where('user_id',$user_id)->select('user_balance')->first();
                                Report::where('id', $insert_id)
                                ->update(['total_balance'=>$userdetail->user_balance,'status_id' => 2, 'bank_ref' => $result->DisplayMessage,'txnid'=>$ref_uniq_id]);
                                Remitteregister::where('mobile',$CustomerMobileNo)->decrement('used_limit',$Amount);
                                $response_result[$i]=['status_id'=>2,'TxnID'=>$insert_id,'message'=>$result->DisplayMessage,'AmountRequested'=>$res_data->AmountRequested,'BeneName'=>$res_data->BeneName,'TxnDescription'=>$res_data->TxnDescription,'client_req_no'=>$ref_uniq_id,'utr'=>$res_data->ActCode];
                            }
                            else
                            {
                                $message = "Dear Customer your transaction is successful TID:".$insert_id.",AMOUNT: ".$Amount.",AC NO:".$BeneAccountNo.",Time".$res_data->TransactionDatetime.", Thanks Shighra Pay";
                                $message = urlencode($message);
                                SendSMS::sendsms($request->mobile_number, $message,Auth::user()->company_id);
                                $userdetail = Balance::where('user_id',$user_id)->select('user_balance')->first();
                                Report::where('id', $insert_id)
                                ->update(['total_balance'=>$userdetail->user_balance,'status_id' => 3, 'bank_ref' =>'','txnid'=>$ref_uniq_id]);
                               
                                $response_result[$i]=['status_id'=>3,'TxnID'=>$insert_id,'message'=>$result->DisplayMessage,'AmountRequested'=>$res_data->AmountRequested,'BeneName'=>'','TxnDescription'=>$res_data->TxnDescription,'client_req_no'=>$ref_uniq_id,'utr'=>''];
                            }
                        }
                        else
                        {
                            $message = "Dear Customer your transaction is successful TID:".$res_data->TxnID.",AMOUNT: ".$Amount.",AC NO:".$BeneAccountNo.",Time".$res_data->TransactionDatetime.", Thanks Shighra Pay";
                            $message = urlencode($message);
                            SendSMS::sendsms($request->mobile_number, $message,Auth::user()->company_id);
                            $userdetail = Balance::where('user_id',$user_id)->select('user_balance')->first();
                            Report::where('id', $insert_id)
                            ->update(['total_balance'=>$userdetail->user_balance,'status_id' => 3, 'bank_ref' => $res_data->TxnID,'txnid'=>$ref_uniq_id]);
                            $response_result[$i]=['status_id'=>3,'TxnID'=>$insert_id,'message'=>$result->DisplayMessage,'AmountRequested'=>$res_data->AmountRequested,'BeneName'=>'','TxnDescription'=>$res_data->TxnDescription,'client_req_no'=>$ref_uniq_id,'utr'=>''];
                        }

                    }
                    else
                    {
                        $userdetail = Balance::where('user_id',$user_id)->select('user_balance')->first();
                        Report::where('id', $insert_id)
                        ->update(['total_balance'=>$userdetail->user_balance,'status_id' => 3,'txnid'=>$ref_uniq_id]);
                         $response_result[$i]=['status_id'=>'Unknown','message'=>'Check Status for this tranaction','TxnID'=>$insert_id,'AmountRequested'=>$Amount]; 
                    }
					}
					catch(Exception $e)
					{
						$response_result[$i]=['status_id'=>'Unknown','message'=>'Check Status for this tranaction','TxnID'=>$insert_id,'AmountRequested'=>$Amount];
					}
                }// End Loop
                return Response()->json(['result'=>$response_result]);
           
            }
            else
            {
                $response_result[1]=['status_id'=>'Failure','message'=>'Amount Should be between 1 to 25000'];
                return Response()->json(['result'=>$response_result]);
                
            }
        }
        else
        {
            $response_result[1]=['status_id'=>'Failure','message'=>'Invalid Request or paramitter missing(s)'];
            return Response()->json(['result'=>$response_result]);
        }

    }
}
public function paytm_transfer($CustomerMobileNo,$BeneIFSCCode,$BeneAccountNo,$BeneName,$bene_id,$CustomerName,$Amount,$channel,$user_pan,$user_pin,$user_id) 
      {
		if($CustomerMobileNo!='')
        {
            
            if($user_pan=='' && $user_pin=='')
            {
                return Response::json(['status_id'=>2,'message'=>'Please enter valid PAN number and Pin code!']);
                exit;
            }
            
            if($CustomerMobileNo!='' && preg_match('/^\d{10}$/', $CustomerMobileNo) && $BeneIFSCCode!='' && $BeneAccountNo!='' && $BeneName!='' && $CustomerName!='' && $Amount!='' && $channel!='' && $user_pan!='' && $user_pin!='' && $user_id!='' && is_numeric($BeneAccountNo))
            {
				$now = new \DateTime();
				$datetime = $now->getTimestamp();
				$ctime = $now->format('Y-m-d H:i:s');
				$u_balance = Balance::where('user_id',$user_id)->select('user_balance')->first();
				$paycash_amount = $Amount;
				if($u_balance->user_balance>=$Amount && $Amount>=1)
				{
					$bulk_amount = $req_amount = $Amount;
					$no_of_ite = ceil($Amount/5000);
					 if( $no_of_ite >5 || $no_of_ite <1)
					{
						$response_result[1]=array('status_id' => 'Failure', 'message' => "You are doing something wrong..Please confirm entered balance");
						return response()->json(['result' => $response_result]);
						exit;
					}
					$response_result=array();
					$u_scheme_id = Auth::user()->scheme_id;
					$scheme_charge = $this->getRetailerChargeAmout($u_scheme_id);
					$total_charge_txn = ceil($bulk_amount/1000);
					$total_charge_amt = ($total_charge_txn * $scheme_charge->r);
					$finalamount = $bulk_amount+$total_charge_amt;
					
					
					$check_limit = Remitteregister::where('mobile',$CustomerMobileNo)->select('used_limit','total_limit','paycash_limit','paycash_used','paycash_remaining')->first();
							if($check_limit)
							{
								$txnmanage = DB::table('txnmanages')->find(1);
								if($txnmanage->paytm_on==0)
								{
									$response_result[1] = ['status_id'=>2,'TxnID'=>'','message'=>'Service is down,please try after sometimes!','AmountRequested'=>'','BeneName'=>'','TxnDescription'=>'Service is down,please try after sometimes!','client_req_no'=>'','utr'=>''];
									return Response()->json(['result'=>$response_result]);
									
								}
								$month_limit = $check_limit->paycash_used+$bulk_amount;
								if($month_limit > $check_limit->paycash_limit)
								{
									$response_result[1]=['status_id'=>43,'message'=>'Monthly balance limit exceeded!'];
									return Response()->json($response_result);
									exit;
								}

							}
							else
							{
								$response_result[1]=['status_id'=>31,'message'=>'There is no datas for this number'];
								return Response()->json(['result'=>$response_result]);
								exit;
							}
					
					
					
					$u_balance = Balance::where('user_id',$user_id)->select('user_balance')->first();
					if($u_balance->user_balance<$finalamount)
					{
						 $response_result[1]=['status_id'=>'Failure','message'=>'Insufficient balance!'];
						return Response()->json(['result'=>$response_result]);
					}
					DB::beginTransaction();
					try
					{
						Balance::where('user_id',$user_id)->decrement('user_balance',$finalamount);
						$userbal = Balance::where('user_id',$user_id)->select('user_balance','user_id')->first();
						if($no_of_ite > 1)
						{
							$ref_insert_id = Report::insertGetId([
									'number' => $BeneAccountNo,
									'provider_id' => 41,
									'amount' => $bulk_amount,
									'bulk_amount' => $bulk_amount,
									'profit' => $total_charge_amt,
									'api_id' => 17,
									'ip_address'=>\Request::ip(),
									'status_id' => 23,
									'description' => 'DMT',
									'pay_id' => $datetime,
									'created_at' => $ctime,
									'user_id' => $user_id,
									'customer_number' => $CustomerMobileNo,
									'total_balance' => $userbal->user_balance,
									'beneficiary_id' => $bene_id,
									'channel' => $channel,
									]);
						}
						Remitteregister::where('mobile',$CustomerMobileNo)->increment('paycash_used',$bulk_amount);
						DB::commit();
					}
					catch(Exception $e)
					{
						DB::rollback();
						$response_result[1]=array('status_id' => 2, 'message' => "Whoops Something went wrong. Please try again.");
						return response()->json(['result' => $response_result]);
						exit;
						
					}
               
                for($i=1;$i<=$no_of_ite;$i++)
                {

                    if($req_amount > 5000)
                    {
                        $Amount = 5000;
                        $req_amount = $req_amount-5000;
                        
                    }
                    else
                    {
                        $Amount = $req_amount;
                    }
                   
                    $now = new \DateTime();
                    $datetime = $now->getTimestamp();
                    $ctime = $now->format('Y-m-d H:i:s');
                    $u_scheme_id = Auth::user()->scheme_id;
                    $scheme_charge = $this->getRetailerChargeAmout($u_scheme_id);
                            if($Amount<=1000)
                            {
                                $chargeamount = $scheme_charge->r;
                            }
                            elseif($Amount>1000 && $Amount<=2000)
                            {
                                $chargeamount = $scheme_charge->r*2;
                            }
                            elseif($Amount>2000 && $Amount<=3000)
                            {
                                $chargeamount = $scheme_charge->r*3;
                            }
                            elseif($Amount>3000 && $Amount<=4000)
                            {
                                $chargeamount = $scheme_charge->r*4;
                            }
                            elseif($Amount>4000 && $Amount<=5000)
                            {
                                $chargeamount = $scheme_charge->r*5;
                            }
                            else
                            {
                               $chargeamount = $scheme_charge->r; 
                            }
							
                
					/* $u_balance = Balance::where('user_id',$user_id)->select('user_balance')->first();
                $finalamount = $Amount+$chargeamount;
                if($u_balance->user_balance<$finalamount)
                {
                     return Response::json(['status_id'=>40,'message'=>'You do not have sufficient balance. For this transaction']);
                        exit;
                } */
              

						$insert_id = Report::insertGetId([
                            'number' => $BeneAccountNo,
                            'provider_id' => 41,
                            'amount' => $Amount,
                            'bulk_amount' => $bulk_amount,
                            'profit' => $chargeamount,
                            'api_id' => 17,
                            'ip_address'=>\Request::ip(),
                            'status_id' => 3,
                            'description' => 'PayCash DMT',
							'ref_id' =>isset($ref_insert_id)?$ref_insert_id:'',
                            'pay_id' => $datetime,
                            'created_at' => $ctime,
                            'user_id' => $user_id,
                            'customer_number' => $CustomerMobileNo,
                            'total_balance' => $userbal->user_balance,
                            'beneficiary_id' => $bene_id,
                            'channel' => $channel,
                        ]);
						Report::where('id',$insert_id)->update(['total_balance'=>$userbal->user_balance,'ackno'=>'payshigh'.$insert_id]);
						
           
                        $ref_uniq_id = 'payshigh'.$insert_id;
                        $checksum_lib = new \App\Library\Paycash;
                        $checkSum = "";
                        $paramList = array();
                        $paramList["BANK_ACC_NO"] = $BeneAccountNo;
                        $paramList["IFSC_CODE"] = $BeneIFSCCode;
                        $paramList["ACC_TYPE"] = "NA";
                        $paramList["MOBILE_NO"] = $CustomerMobileNo;
                        $paramList["SENDER_NAME"] = urldecode($BeneName);
                        $paramList["AMOUNT"] = $Amount;
                        $paramList["CURRENCY"] = "INR";
                        $paramList["MID"] = "Shighr98191998365442";
                        $paramList["ORDER_ID"] = $ref_uniq_id;
                        $paramList["REQUEST_TYPE"] = "P2B_S2S"; 
                        $paramList["REMARKS"] = "SRSMND";
                        $checkSum = $checksum_lib->getChecksumFromArray($paramList,"DDN8_dvamYVHTCt1");
                       // $checkSum ='';
                        $paramList["CHECKSUM"] = $checkSum;
                        $data_string = "JsonData=".json_encode($paramList);
                         $paytm_api_reqest = "{'BANK_ACC_NO':'".$BeneAccountNo."',
                                            'IFSC_CODE':'".$BeneIFSCCode."',
                                            'ACC_TYPE':'NA',
                                            'MOBILE_NO':'".$CustomerMobileNo."',
                                            'SENDER_NAME':'".$BeneName."',
                                            'AMOUNT':'".$Amount."',
                                            'CURRENCY':'INR',
                                            'MID':'Shighr98191998365442',
                                            'ORDER_ID':'payshigh".$insert_id."',
                                            'REQUEST_TYPE':'P2B_S2S',
                                            'REMARKS':'SRSMND',
                                            'CHECKSUM':'".$checkSum."',
                                            }";
                       $url = 'https://secure.paytm.in/oltp/P2B';
                        $ch = curl_init();                    
                        curl_setopt($ch, CURLOPT_URL,$url);
                        curl_setopt($ch, CURLOPT_POST, 1); 
                        curl_setopt($ch, CURLOPT_POSTFIELDS,$data_string); 
                        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); 
                        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);     
                        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);    
                        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
                        $output = curl_exec ($ch); 
                        $info = curl_getinfo($ch); 
						try{
							
                        $result = json_decode($output);
                        Apiresponse::create(['message'=>$output,'api_type'=>'PayTmTxn','report_id'=>$insert_id,'request_message'=>$paytm_api_reqest]);
                    if($result)
                    { 
                        if($result->RESPCODE==01)
                        { 
                         Report::where('id', $insert_id)->update(['status_id' => 1, 'bank_ref' => $result->BANK_TXN_ID,'txnid'=>$result->ORDER_ID]);
                         
                          $message = "Dear Customer your transaction is successful TID:".$result->ORDER_ID.",AMOUNT: ".$Amount.",AC NO:".$BeneAccountNo.", Thanks Shighra Pay";
                           $message = urlencode($message);
							$this->sendsms($CustomerMobileNo, $message);
							
                           $response_result[$i]  =['status_id'=>1,'message'=>'Transaction Succesful','AmountRequested'=>$result->AMOUNT,'BeneName'=>$result->BENEFICIARY_NAME,'TxnDescription'=>'IMPS','TxnID' => $insert_id,'client_req_no'=>$ref_uniq_id,'utr'=>$result->BANK_TXN_ID];
                        }
                         elseif($result->RESPCODE=="8007" || $result->RESPCODE=="8016" ||$result->RESPCODE=="8311"||$result->RESPCODE=="8314"||$result->RESPCODE=="8330"||$result->RESPCODE=="8352" || $result->RESPCODE=="400")
                        {

                        Report::where('id', $insert_id)
                        ->update(['status_id' => 3, 'bank_ref' => @$result->BANK_TXN_ID,'txnid'=>$result->ORDER_ID]);
                         
                        $message = "Dear Customer your transaction is successful TID:".$result->result.",AMOUNT: ".$Amount.",AC NO:".$BeneAccountNo.", Thanks Shighra Pay";
                           $message = urlencode($message);
                         $this->sendsms($CustomerMobileNo, $message);
                         $response_result[$i] =['status_id'=>3,'TxnID'=>$insert_id,'message'=>'Transaction Pending','AmountRequested'=>$result->AMOUNT,'BeneName'=>'','TxnDescription'=>'Transaction Pending','client_req_no'=>$ref_uniq_id,'utr'=>''];

                         }
                         elseif($result->RESPCODE=="226" ||$result->RESPCODE=="348" ||$result->RESPCODE=="402"||$result->RESPCODE=="403"||$result->RESPCODE=="901"||$result->RESPCODE=="902"||$result->RESPCODE=="8000"||$result->RESPCODE=="8001"||$result->RESPCODE=="8002"||$result->RESPCODE=="8003"||$result->RESPCODE=="8005"||$result->RESPCODE=="8006"||$result->RESPCODE=="8008"||$result->RESPCODE=="8009"||$result->RESPCODE=="8010"||$result->RESPCODE=="8011"||$result->RESPCODE=="8012"||$result->RESPCODE=="8013"||$result->RESPCODE=="8014"||$result->RESPCODE=="8015"||$result->RESPCODE=="8017"||$result->RESPCODE=="8018"||$result->RESPCODE=="8019"||$result->RESPCODE=="8020"||$result->RESPCODE=="8021"||$result->RESPCODE=="8022"||$result->RESPCODE=="8023"||$result->RESPCODE=="8024"||$result->RESPCODE=="8025"||$result->RESPCODE=="8026"||$result->RESPCODE=="8027"||$result->RESPCODE=="8028"||$result->RESPCODE=="8029"||$result->RESPCODE=="8030"||$result->RESPCODE=="8031"||$result->RESPCODE=="8032"||$result->RESPCODE=="8032"||$result->RESPCODE=="8032"||$result->RESPCODE=="8032"||$result->RESPCODE=="8033"||$result->RESPCODE=="8034"||$result->RESPCODE=="8035"||$result->RESPCODE=="8036"||$result->RESPCODE=="8037"||$result->RESPCODE=="8038"||$result->RESPCODE=="8039"||$result->RESPCODE=="8040"||$result->RESPCODE=="8041"||$result->RESPCODE=="8042"||$result->RESPCODE=="8042"||$result->RESPCODE=="8042"||$result->RESPCODE=="8042"||$result->RESPCODE=="8042"||$result->RESPCODE=="8043"||$result->RESPCODE=="8044"||$result->RESPCODE=="8045"||$result->RESPCODE=="8046"||$result->RESPCODE=="8047"||$result->RESPCODE=="8048"||$result->RESPCODE=="8049"||$result->RESPCODE=="8050"||$result->RESPCODE=="8051"||$result->RESPCODE=="8052"||$result->RESPCODE=="8053"||$result->RESPCODE=="8054"||$result->RESPCODE=="8055"||$result->RESPCODE=="8056"||$result->RESPCODE=="8057"||$result->RESPCODE=="8058"||$result->RESPCODE=="8059"||$result->RESPCODE=="8060"||$result->RESPCODE=="8061"||$result->RESPCODE=="8062"||$result->RESPCODE=="8063"||$result->RESPCODE=="8064"||$result->RESPCODE=="8065"||$result->RESPCODE=="8066"||$result->RESPCODE=="8067"||$result->RESPCODE=="8068"||$result->RESPCODE=="8069" || $result->RESPCODE=="501")
                             {
                                $refund_amount = $Amount+$chargeamount;
                                $refund_total_bal = Balance::where('user_id',$user_id)->increment('user_balance',$refund_amount);
                                $final_total_bal = Balance::where('user_id',$user_id)->select('user_balance')->first();
                                Report::where('id', $insert_id)
                                ->update(['total_balance'=>$final_total_bal->user_balance,'status_id' =>2, 'bank_ref' =>'','txnid'=>$result->TXN_MSG]);
                                Remitteregister::where('mobile',$CustomerMobileNo)->decrement('paycash_used',$Amount);
                                $response_result[$i] = ['status_id'=>2,'TxnID'=>$insert_id,'message'=>$result->TXN_MSG,'AmountRequested'=>$result->AMOUNT,'BeneName'=>'','TxnDescription'=>$result->TXN_MSG,'client_req_no'=>$ref_uniq_id,'utr'=>''];
                             }
                         elseif($result->RESPCODE=="8301" ||$result->RESPCODE=="8302" ||$result->RESPCODE=="8303"||$result->RESPCODE=="8304"||$result->RESPCODE=="8305"||$result->RESPCODE=="8306"||$result->RESPCODE=="8307"||$result->RESPCODE=="8308"||$result->RESPCODE=="8309"||$result->RESPCODE=="8310"||$result->RESPCODE=="8312"||$result->RESPCODE=="8313"||$result->RESPCODE=="8315"||$result->RESPCODE=="8316"||$result->RESPCODE=="8317"||$result->RESPCODE=="8318"||$result->RESPCODE=="8319"||$result->RESPCODE=="8321"||$result->RESPCODE=="8331"||$result->RESPCODE=="8332"||$result->RESPCODE=="8341" || $result->RESPCODE=="8351" || $result->RESPCODE=="8352"|| $result->RESPCODE=="8353" || $result->RESPCODE=="8354" || $result->RESPCODE=="402" || $result->RESPCODE=="330" || $result->RESPCODE=="501") 
                             {
                                $refund_amount = $Amount+$chargeamount;
                                $refund_total_bal = Balance::where('user_id',$user_id)->increment('user_balance',$refund_amount);
                                $final_total_bal = Balance::where('user_id',$user_id)->select('user_balance')->first();
                                Report::where('id', $insert_id)
                                ->update(['total_balance'=>$final_total_bal->user_balance,'status_id' =>2, 'bank_ref' =>'','txnid'=>$result->ORDER_ID]);
                                Remitteregister::where('mobile',$CustomerMobileNo)->decrement('paycash_used',$Amount);
                                  $response_result[$i] =['status_id'=>2,'TxnID'=>$insert_id,'message'=>$result->TXN_MSG,'AmountRequested'=>$result->AMOUNT,'BeneName'=>'','TxnDescription'=>$result->TXN_MSG,'client_req_no'=>$ref_uniq_id,'utr'=>''];
                             }
                         else
                             {
                                Report::where('id', $insert_id)
                                 ->update(['status_id' => 3, 'bank_ref' =>'','txnid'=>$result->ORDER_ID]);
                                
                                $message = "Dear Customer your transaction is successful TID:".$result->ORDER_ID.",AMOUNT: ".$Amount.",AC NO:".$BeneAccountNo.", Thanks Shighra Pay";
                                 $message = urlencode($message);
                                 $this->sendsms($request->mobile_number, $message);
                                $response_result[$i]  = ['status_id'=>3,'TxnID'=>$insert_id,'message'=>$result->TXN_MSG,'AmountRequested'=>$result->AMOUNT,'BeneName'=>'','TxnDescription'=>$result->TXN_MSG,'client_req_no'=>$ref_uniq_id,'utr'=>''];
                             }

                     }
                    else
                      {
                        Report::where('id', $insert_id)->update(['status_id' => 3, 'bank_ref' =>'','txnid'=>$ref_uniq_id]);
                        
                        $message = "Dear Customer your transaction is successful TID:".$insert_id .",AMOUNT: ".$Amount.",AC NO:".$BeneAccountNo.", Thanks Shighra Pay";
                           $message = urlencode($message);
                         $this->sendsms($CustomerMobileNo, $message);
                        $response_result[$i] = ['status_id'=>3,'TxnID'=>$insert_id,'message'=>"Check Status for this tranaction",'AmountRequested'=>$Amount,'BeneName'=>'','TxnDescription'=>'Check Status for this tranaction','client_req_no'=>$ref_uniq_id,'utr'=>''];
                      }
				}
				catch(Exception $e)
				{
					$response_result[$i]=['status_id'=>'Unknown','message'=>'Check Status for this tranaction','TxnID'=>$insert_id,'AmountRequested'=>$Amount];
				}
                    
                  }//FOR LOOP END
                  return Response()->json(['result'=>$response_result]);
                }
                else
                {
                    
                     $response_result[1]=['status_id'=>'Failure','message'=>'Insufficient balance!'];
                    return Response()->json(['result'=>$response_result]);
                }
            }
            else
            {
                $response_result[1]=['status_id'=>24,'message'=>'Invalid Request or parameters missing'];
				return Response()->json(['result'=>$response_result]);
            }
    }
}
public function paytm_transfer_bkp($CustomerMobileNo,$BeneIFSCCode,$BeneAccountNo,$BeneName,$bene_id,$CustomerName,$Amount,$channel,$user_pan,$user_pin,$user_id) 
      {
        if($CustomerMobileNo!='')
         {
            
            if($user_pan=='' && $user_pin=='')
            {
                return Response::json(['status_id'=>2,'message'=>'Please enter valid PAN number and Pin code!']);
                exit;
            }
            
            if($CustomerMobileNo!='' && preg_match('/^\d{10}$/', $CustomerMobileNo) && $BeneIFSCCode!='' && $BeneAccountNo!='' && $BeneName!='' && $CustomerName!='' && $Amount!='' && $channel!='' && $user_pan!='' && $user_pin!='' && $user_id!='' && is_numeric($BeneAccountNo))
            {

              $u_balance = Balance::where('user_id',$user_id)->select('user_balance')->first();
              if($u_balance->user_balance>=$Amount && $Amount>=1)
              {
                $bulk_amount = $req_amount = $Amount;
                $no_of_ite = ceil($Amount/5000);
                $response_result=array();
                if( $no_of_ite >5 || $no_of_ite <1)
                {
                    $response_result[1]=array('status_id' => 'Failure', 'message' => "You are doing something wrong..Please confirm entered balance");
                    return response()->json(['result' => $response_result]);
                    exit;
                }
                for($i=1;$i<=$no_of_ite;$i++)
                {

                    if($req_amount > 5000)
                    {
                        $Amount = 5000;
                        $req_amount = $req_amount-5000;
                        
                    }
                    else
                    {
                        $Amount = $req_amount;
                    }
                    $loop_balance = Balance::where('user_id',$user_id)->select('user_balance')->first();
                    if($loop_balance->user_balance>=$req_amount)
                    {
                    $now = new \DateTime();
                    $datetime = $now->getTimestamp();
                    $ctime = $now->format('Y-m-d H:i:s');
                    $u_scheme_id = Auth::user()->scheme_id;
                    $scheme_charge = $this->getRetailerChargeAmout($u_scheme_id);
                            if($Amount<=1000)
                            {
                                $chargeamount = $scheme_charge->r;
                            }
                            elseif($Amount>1000 && $Amount<=2000)
                            {
                                $chargeamount = $scheme_charge->r*2;
                            }
                            elseif($Amount>2000 && $Amount<=3000)
                            {
                                $chargeamount = $scheme_charge->r*3;
                            }
                            elseif($Amount>3000 && $Amount<=4000)
                            {
                                $chargeamount = $scheme_charge->r*4;
                            }
                            elseif($Amount>4000 && $Amount<=5000)
                            {
                                $chargeamount = $scheme_charge->r*5;
                            }
                            else
                            {
                               $chargeamount = $scheme_charge->r; 
                            }
                $check_limit = Remitteregister::where('mobile',$CustomerMobileNo)->select('used_limit','total_limit','paycash_limit','paycash_used','paycash_remaining')->first();
                if($check_limit)
                {
                	$txnmanage = DB::table('txnmanages')->find(1);
                    if($txnmanage->paytm_on==0)
                    	{
                    		 $response_result[1] = ['status_id'=>2,'TxnID'=>'','message'=>'Service is down,please try after sometimes!','AmountRequested'=>$AMOUNT,'BeneName'=>'','TxnDescription'=>'Service is down,please try after sometimes!','client_req_no'=>'','utr'=>''];
                    		 return Response()->json(['result'=>$response_result]);
                    		 exit;
                    	}
                    $month_limit = $check_limit->paycash_used+$Amount;
                    if($month_limit > $check_limit->paycash_limit)
                    {
                        return Response::json(['status_id'=>43,'message'=>'Monthly balance limit exceeded!']);
                        exit;
                    }

                }
                else
                {
                    return Response::json(['status_id'=>31,'message'=>'There is no datas for this number']);
                        exit;
                }
                
             $u_balance = Balance::where('user_id',$user_id)->select('user_balance')->first();
                $finalamount = $Amount+$chargeamount;
                if($u_balance->user_balance<$finalamount)
                {
                     return Response::json(['status_id'=>40,'message'=>'You do not have sufficient balance. For this transaction']);
                        exit;
                }
              
             $deduct_amount = $u_balance->user_balance-$finalamount;
              $insert_id = Report::insertGetId([
                            'number' => $BeneAccountNo,
                            'provider_id' => 41,
                            'amount' => $Amount,
                            'profit' => $chargeamount,
                            'api_id' => 17,
                            'ip_address'=>\Request::ip(),
                            'status_id' => 3,
                            'description' => 'PayCash DMT',
                            'pay_id' => $datetime,
                            'created_at' => $ctime,
                            'user_id' => $user_id,
                            'customer_number' => $CustomerMobileNo,
                            'total_balance' => $deduct_amount,
                            'beneficiary_id' => $bene_id,
                            'channel' => $channel,
                        ]);
            if($insert_id)
            {
                Balance::where('user_id',$user_id)->decrement('user_balance',$finalamount);
                $userbal = Balance::where('user_id',$user_id)->select('user_balance','user_id')->first();
                Report::where('id',$insert_id)->update(['total_balance'=>$userbal->user_balance,'ackno'=>'payshigh'.$insert_id]);
                Remitteregister::where('mobile',$CustomerMobileNo)->increment('paycash_used',$Amount);
            }
            else
            {
                $response_result[1] = ['status_id'=>2,'TxnID'=>$insert_id,'message'=>$result->TXN_MSG,'AmountRequested'=>$result->AMOUNT,'BeneName'=>'','TxnDescription'=>$result->TXN_MSG,'client_req_no'=>$ref_uniq_id,'utr'=>''];
              return Response::json(['result'=>$response_result]);
                
            }
                        $ref_uniq_id = 'payshigh'.$insert_id;
                        $checksum_lib = new \App\Library\Paycash;
                        $checkSum = "";
                        $paramList = array();
                        $paramList["BANK_ACC_NO"] = $BeneAccountNo;
                        $paramList["IFSC_CODE"] = $BeneIFSCCode;
                        $paramList["ACC_TYPE"] = "NA";
                        $paramList["MOBILE_NO"] = $CustomerMobileNo;
                        $paramList["SENDER_NAME"] = urldecode($BeneName);
                        $paramList["AMOUNT"] = $Amount;
                        $paramList["CURRENCY"] = "INR";
                        $paramList["MID"] = "Shighr98191998365442";
                        $paramList["ORDER_ID"] = $ref_uniq_id;
                        $paramList["REQUEST_TYPE"] = "P2B_S2S"; 
                        $paramList["REMARKS"] = "SRSMND";
                        $checkSum = $checksum_lib->getChecksumFromArray($paramList,"DDN8_dvamYVHTCt1");
                        $paramList["CHECKSUM"] = $checkSum;
                        $data_string = "JsonData=".json_encode($paramList);
                         $paytm_api_reqest = "{'BANK_ACC_NO':'".$BeneAccountNo."',
                                            'IFSC_CODE':'".$BeneIFSCCode."',
                                            'ACC_TYPE':'NA',
                                            'MOBILE_NO':'".$CustomerMobileNo."',
                                            'SENDER_NAME':'".$BeneName."',
                                            'AMOUNT':'".$Amount."',
                                            'CURRENCY':'INR',
                                            'MID':'Shighr98191998365442',
                                            'ORDER_ID':'payshigh".$insert_id."',
                                            'REQUEST_TYPE':'P2B_S2S',
                                            'REMARKS':'SRSMND',
                                            'CHECKSUM':'".$checkSum."',
                                            }";
                        $url = 'https://secure.paytm.in/oltp/P2B';
                        $ch = curl_init();                    
                        curl_setopt($ch, CURLOPT_URL,$url);
                        curl_setopt($ch, CURLOPT_POST, 1); 
                        curl_setopt($ch, CURLOPT_POSTFIELDS,$data_string); 
                        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); 
                        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);     
                        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);    
                        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
                        $output = curl_exec ($ch); 
                        $info = curl_getinfo($ch);
                        $result = json_decode($output);
                        Apiresponse::create(['message'=>$output,'api_type'=>'PayTmTxn','report_id'=>$insert_id,'request_message'=>$paytm_api_reqest]);
                    if($result)
                    {
                        if($result->RESPCODE==01)
                        { 
                         Report::where('id', $insert_id)
                        ->update(['status_id' => 1, 'bank_ref' => $result->BANK_TXN_ID,'txnid'=>$result->ORDER_ID]);
                         
                          $message = "Dear Customer your transaction is successful TID:".$result->ORDER_ID.",AMOUNT: ".$Amount.",AC NO:".$BeneAccountNo.", Thanks Shighra Pay";
                           $message = urlencode($message);
                         $this->sendsms($CustomerMobileNo, $message);

                           $response_result[$i]  =['status_id'=>1,'message'=>'Transaction Succesful','AmountRequested'=>$result->AMOUNT,'BeneName'=>$result->BENEFICIARY_NAME,'TxnDescription'=>'IMPS','TxnID' => $insert_id,'client_req_no'=>$ref_uniq_id,'utr'=>$result->BANK_TXN_ID];
                        }
                         elseif($result->RESPCODE=="8007" || $result->RESPCODE=="8016" ||$result->RESPCODE=="8311"||$result->RESPCODE=="8314"||$result->RESPCODE=="8330"||$result->RESPCODE=="8352" || $result->RESPCODE=="400")
                        {

                        Report::where('id', $insert_id)
                        ->update(['status_id' => 3, 'bank_ref' => $result->BANK_TXN_ID,'txnid'=>$result->ORDER_ID]);
                         
                        $message = "Dear Customer your transaction is successful TID:".$result->result.",AMOUNT: ".$Amount.",AC NO:".$BeneAccountNo.", Thanks Shighra Pay";
                           $message = urlencode($message);
                         $this->sendsms($CustomerMobileNo, $message);
                         $response_result[$i] =['status_id'=>3,'TxnID'=>$insert_id,'message'=>'Transaction Pending','AmountRequested'=>$result->AMOUNT,'BeneName'=>'','TxnDescription'=>'Transaction Pending','client_req_no'=>$ref_uniq_id,'utr'=>''];

                         }
                         elseif($result->RESPCODE=="226" ||$result->RESPCODE=="348" ||$result->RESPCODE=="402"||$result->RESPCODE=="403"||$result->RESPCODE=="901"||$result->RESPCODE=="902"||$result->RESPCODE=="8000"||$result->RESPCODE=="8001"||$result->RESPCODE=="8002"||$result->RESPCODE=="8003"||$result->RESPCODE=="8005"||$result->RESPCODE=="8006"||$result->RESPCODE=="8008"||$result->RESPCODE=="8009"||$result->RESPCODE=="8010"||$result->RESPCODE=="8011"||$result->RESPCODE=="8012"||$result->RESPCODE=="8013"||$result->RESPCODE=="8014"||$result->RESPCODE=="8015"||$result->RESPCODE=="8017"||$result->RESPCODE=="8018"||$result->RESPCODE=="8019"||$result->RESPCODE=="8020"||$result->RESPCODE=="8021"||$result->RESPCODE=="8022"||$result->RESPCODE=="8023"||$result->RESPCODE=="8024"||$result->RESPCODE=="8025"||$result->RESPCODE=="8026"||$result->RESPCODE=="8027"||$result->RESPCODE=="8028"||$result->RESPCODE=="8029"||$result->RESPCODE=="8030"||$result->RESPCODE=="8031"||$result->RESPCODE=="8032"||$result->RESPCODE=="8032"||$result->RESPCODE=="8032"||$result->RESPCODE=="8032"||$result->RESPCODE=="8033"||$result->RESPCODE=="8034"||$result->RESPCODE=="8035"||$result->RESPCODE=="8036"||$result->RESPCODE=="8037"||$result->RESPCODE=="8038"||$result->RESPCODE=="8039"||$result->RESPCODE=="8040"||$result->RESPCODE=="8041"||$result->RESPCODE=="8042"||$result->RESPCODE=="8042"||$result->RESPCODE=="8042"||$result->RESPCODE=="8042"||$result->RESPCODE=="8042"||$result->RESPCODE=="8043"||$result->RESPCODE=="8044"||$result->RESPCODE=="8045"||$result->RESPCODE=="8046"||$result->RESPCODE=="8047"||$result->RESPCODE=="8048"||$result->RESPCODE=="8049"||$result->RESPCODE=="8050"||$result->RESPCODE=="8051"||$result->RESPCODE=="8052"||$result->RESPCODE=="8053"||$result->RESPCODE=="8054"||$result->RESPCODE=="8055"||$result->RESPCODE=="8056"||$result->RESPCODE=="8057"||$result->RESPCODE=="8058"||$result->RESPCODE=="8059"||$result->RESPCODE=="8060"||$result->RESPCODE=="8061"||$result->RESPCODE=="8062"||$result->RESPCODE=="8063"||$result->RESPCODE=="8064"||$result->RESPCODE=="8065"||$result->RESPCODE=="8066"||$result->RESPCODE=="8067"||$result->RESPCODE=="8068"||$result->RESPCODE=="8069")
                             {
                                $refund_amount = $Amount+$chargeamount;
                                $refund_total_bal = Balance::where('user_id',$user_id)->increment('user_balance',$refund_amount);
                                $final_total_bal = Balance::where('user_id',$user_id)->select('user_balance')->first();
                                Report::where('id', $insert_id)
                                ->update(['total_balance'=>$final_total_bal->user_balance,'status_id' =>2, 'bank_ref' =>'','txnid'=>$result->TXN_MSG]);
                                Remitteregister::where('mobile',$CustomerMobileNo)->decrement('paycash_used',$Amount);
                                $response_result[$i] = ['status_id'=>2,'TxnID'=>$insert_id,'message'=>$result->TXN_MSG,'AmountRequested'=>$result->AMOUNT,'BeneName'=>'','TxnDescription'=>$result->TXN_MSG,'client_req_no'=>$ref_uniq_id,'utr'=>''];
                             }
                         elseif($result->RESPCODE=="8301" ||$result->RESPCODE=="8302" ||$result->RESPCODE=="8303"||$result->RESPCODE=="8304"||$result->RESPCODE=="8305"||$result->RESPCODE=="8306"||$result->RESPCODE=="8307"||$result->RESPCODE=="8308"||$result->RESPCODE=="8309"||$result->RESPCODE=="8310"||$result->RESPCODE=="8312"||$result->RESPCODE=="8313"||$result->RESPCODE=="8315"||$result->RESPCODE=="8316"||$result->RESPCODE=="8317"||$result->RESPCODE=="8318"||$result->RESPCODE=="8319"||$result->RESPCODE=="8321"||$result->RESPCODE=="8331"||$result->RESPCODE=="8332"||$result->RESPCODE=="8341" || $result->RESPCODE=="8351" || $result->RESPCODE=="8352"|| $result->RESPCODE=="8353" || $result->RESPCODE=="8354" || $result->RESPCODE=="402" || $result->RESPCODE=="330") 
                             {
                                $refund_amount = $Amount+$chargeamount;
                                $refund_total_bal = Balance::where('user_id',$user_id)->increment('user_balance',$refund_amount);
                                $final_total_bal = Balance::where('user_id',$user_id)->select('user_balance')->first();
                                Report::where('id', $insert_id)
                                ->update(['total_balance'=>$final_total_bal->user_balance,'status_id' =>2, 'bank_ref' =>'','txnid'=>$result->ORDER_ID]);
                                Remitteregister::where('mobile',$CustomerMobileNo)->decrement('paycash_used',$Amount);
                                  $response_result[$i] =['status_id'=>2,'TxnID'=>$insert_id,'message'=>$result->TXN_MSG,'AmountRequested'=>$result->AMOUNT,'BeneName'=>'','TxnDescription'=>$result->TXN_MSG,'client_req_no'=>$ref_uniq_id,'utr'=>''];
                             }
                         else
                             {
                                Report::where('id', $insert_id)
                                 ->update(['status_id' => 3, 'bank_ref' =>'','txnid'=>$result->TXN_MSG]);
                                
                                $message = "Dear Customer your transaction is successful TID:".$result->ORDER_ID.",AMOUNT: ".$Amount.",AC NO:".$BeneAccountNo.", Thanks Shighra Pay";
                                 $message = urlencode($message);
                                 $this->sendsms($request->mobile_number, $message);
                                $response_result[$i]  = ['status_id'=>3,'TxnID'=>$insert_id,'message'=>$result->TXN_MSG,'AmountRequested'=>$result->AMOUNT,'BeneName'=>'','TxnDescription'=>$result->TXN_MSG,'client_req_no'=>$ref_uniq_id,'utr'=>''];
                             }

                     }
                    else
                      {
                        Report::where('id', $insert_id)
                        ->update(['status_id' => 3, 'bank_ref' =>'','txnid'=>$result->TXN_MSG]);
                        
                        $message = "Dear Customer your transaction is successful TID:".$result->ORDER_ID.",AMOUNT: ".$Amount.",AC NO:".$BeneAccountNo.", Thanks Shighra Pay";
                           $message = urlencode($message);
                         $this->sendsms($CustomerMobileNo, $message);
                        $response_result[$i] = ['status_id'=>3,'TxnID'=>$insert_id,'message'=>$result->TXN_MSG,'AmountRequested'=>$result->AMOUNT,'BeneName'=>'','TxnDescription'=>$result->TXN_MSG,'client_req_no'=>$ref_uniq_id,'utr'=>''];
                      }
                    }
                    else
                    {
                        
                        $response_result[1] = ['status_id'=>40,'message'=>'Insufficient balance or transaction amount should be more then Rs.1'];
                        return Response::json(['result'=>$response_result]);
                    }
                  }
                  return Response()->json(['result'=>$response_result]);
                }
                else
                {
                    
                    return Response::json(['status_id'=>40,'message'=>'Insufficient balance or transaction amount should be more then Rs.1']);
                }
            }
            else
            {
                
                return Response::json(['status_id'=>24,'message'=>'Invalid Request or parameters missing']);
            }
    }
}
protected function getRetailerChargeAmout($scheme_id)
    {
         $provider_detail = Commission::where('scheme_id',$scheme_id)->first();
         return $provider_detail;
    }
    protected function getDistributorChargeAmout($amount,$dis_id)
    {
        $chargeamount = 0;
        $txnslab = Distpertxn::where('user_id',$dis_id)->where('status_id',1)->first();
        if(count($txnslab)>0){
            if($amount<=1000){ $chargeamount  = $txnslab->ptxn1;}    
            elseif($amount>1000 && $amount<=2000){ $chargeamount  = $txnslab->ptxn2; }
            elseif($amount>2000 && $amount<=3000){ $chargeamount  = $txnslab->ptxn3; }
            elseif($amount>3000 && $amount<=4000){ $chargeamount  = $txnslab->ptxn4; }
            elseif($amount>4000 && $amount<=5000){ $chargeamount  = $txnslab->ptxn5; }
        }
        return $chargeamount;
    }

    protected function getMdChargeAmout($amount,$dis_id)
    {
        $chargeamount = 0;
        $txnslab = Mdthiredpertxn::where('user_id',$dis_id)->where('status_id',1)->first();
        if(count($txnslab)>0){
            if($amount<=1000){ $chargeamount  = $txnslab->ptxn1;}    
            elseif($amount>1000 && $amount<=2000){ $chargeamount  = $txnslab->ptxn2; }
            elseif($amount>2000 && $amount<=3000){ $chargeamount  = $txnslab->ptxn3; }
            elseif($amount>3000 && $amount<=4000){ $chargeamount  = $txnslab->ptxn4; }
            elseif($amount>4000 && $amount<=5000){ $chargeamount  = $txnslab->ptxn5; }
        }
        return $chargeamount;
    }

     public function ptxn_transaction($d_id,$m_id,$a_id,$user_id,$mobile_number,$bene_id,$channel,$insert_id,$amount,$chargeamount,$dchargeamount,$mchargeamount,$bank_account)
    {
            
            $now = new \DateTime();
            $datetime = $now->getTimestamp();
            $ctime = $now->format('Y-m-d H:i:s');
                        
                                        if($d_id!='')
                                        {
                                            
                                                //$commission_charge = $chargeamount - $dchargeamount;
                                                Balance::where('user_id', $d_id)->increment('user_commission', $dchargeamount);
                                                //Balance::where('user_id', $d_id)->decrement('user_balance', $dchargeamount);
                                                $dist_details_cr = Balance::where('user_id', $d_id)->select('user_balance','user_commission')->first();
                                                Report::insertGetId([
                                                        'number' => $bank_account,
                                                        'provider_id' => 41,
                                                        'profit'=>0,
                                                        'api_id' => 16,
                                                        'status_id' => 22,
                                                        'description' => 'PtxnDist',
                                                        'pay_id' => $datetime,
                                                        'created_at' => $ctime,
                                                        'user_id' => $d_id,
                                                        'credit_by'=>$user_id,
                                                        'txnid'=>$insert_id,
                                                        'credit_charge' =>$dchargeamount,
                                                        'debit_charge' =>0,
                                                        'customer_number' => $mobile_number,
                                                        'total_balance' => $dist_details_cr->user_commission,// Update
                                                        'beneficiary_id' => $bene_id,
                                                        'channel' => $channel,
                                                    ]); 
                                            }
                                         
                                            if($m_id!='')
                                            {
                                            
                                                Balance::where('user_id', $m_id)->increment('user_commission', $mchargeamount);
                                               // Balance::where('user_id', $m_id)->decrement('user_balance', $mchargeamount);
                                                $admin_details_cr = Balance::select('user_balance','user_commission')->where('user_id', $m_id)->first();
                                                            Report::insertGetId([
                                                            'number' => $bank_account,
                                                            'provider_id' => 41,
                                                            
                                                            'profit'=>0,//update
                                                            'api_id' => 16,
                                                            'status_id' => 22,//update
                                                            'description' => 'PertxnMd',
                                                            'pay_id' => $datetime,
                                                            'created_at' => $ctime,
                                                            'user_id' => $m_id,//update
                                                            'credit_by'=>$d_id,//update
                                                            'credit_charge' =>$mchargeamount,
                                                            'debit_charge' =>0,
                                                            'txnid'=>$insert_id,
                                                            'customer_number' => $mobile_number,
                                                            'total_balance' => $admin_details_cr->user_commission,              
                                                            'beneficiary_id' => $bene_id,
                                                            'channel' => $channel,
                                                        ]); 
                                            }

                                            // if($a_id!='')
                                            // {
                                            //     if($m_id=='' || $d_id==''){ $m_id=$user_id; }
                                            //     Balance::where('user_id', $a_id)->increment('user_balance', $mchargeamount);
                                            //     $admin_details_cr = Balance::select('user_balance')->where('user_id', $a_id)->first();
                                            //                 Reportmodel::insertGetId([
                                            //                 'number' => $bank_account,
                                            //                 'provider_id' => 41,
                                            //                 'amount'=>$amount,
                                            //                 'profit'=>0,//update
                                            //                 'api_id' => 16,
                                            //                 'status_id' => 22,//update
                                            //                 'description' => 'PertxnMd',
                                            //                 'pay_id' => $datetime,
                                            //                 'created_at' => $ctime,
                                            //                 'user_id' => $a_id,//update
                                            //                 'credit_by'=>$m_id,//update
                                            //                 'credit_charge' =>$mchargeamount,
                                            //                 'debit_charge' =>0,
                                            //                 'txnid'=>$insert_id,
                                            //                 'customer_number' => $mobile_number,
                                            //                 'total_balance' => $admin_details_cr->user_balance,              
                                            //                 'beneficiary_id' => $bene_id,
                                            //                 'channel' => $channel,
                                            //             ]); 
                                            // }
                                    
    }
    public function searchsender(Request $request) {
        $number = $request->mobile_number;
        $client = new Client;
        $res = $client->get("http://smsalertbox.com/api/mt/get-sender-id.php?uid=$this->authkey&pin=$this->pin&sender_mobile=$number&format=json");
        //echo $res->getStatusCode();
        $response = $res->getBody();
        //return $response;
        $json = json_decode($response);
        $data['senderid'] = $json->senderid;
        header('Access-Control-Allow-Origin: *');
        header('content-type: application/json; charset=utf-8');
        return $data;
    }

    public function add_beneficiary(Request $request) {
        
        $rules = array('mobile_number' => 'required');
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return Response::json($validator->errors()->getMessages(), 400);
        } else {
            try
            {
            $mobile_number = $request->mobile_number;
            $name = $request->name;
            $bname = $request->name;
            $number = $request->number;
            $bank_account = $request->bank_account;
            $bank_name = $request->bbank;
            $ifsc = $request->ifsc;
            $user_id = $request->user_id;
                if($mobile_number!='' &&  $name!='' && $number!='' && $bank_account!='' && $bank_name!='' && $ifsc!='')
                {
                    //$have_bene_ten =  Beneficiary::where('mobile_number',$mobile_number)->select('mobile_number')->get();
                   $have_bene =  Beneficiary::where(['mobile_number'=>$mobile_number,'account_number'=>$bank_account])->select('account_number','id','status_id')->first();
                  
                   if($have_bene)
                    {
                       if($have_bene->account_number == $bank_account)
                       {
                         return Response::json(['status_id'=>39,'message'=>'Beneficiary allready added!','bene_id'=>$have_bene->id]);
                         exit;
                       }
                    }
                    
                $max_have_bene =  Beneficiary::where('mobile_number',$mobile_number)->select('id')->get();
                 if($max_have_bene)
                  {
                  $total_bene =  $max_have_bene->count();
                  if($total_bene >= 20)
                  {

                    return Response::json(['status_id'=>58,'message'=>'Maximum add of beneficiaries exceeded!']);

                  }
                 }
                   $digits = 4;
                   $otp = rand(pow(10, $digits-1), pow(10, $digits)-1);
                   if($bank_name_d=Masterbank::select('bank_name')->where('bank_code',$bank_name)->first())
                   {
                       $full_bank_name = $bank_name_d->bank_name;
                   }
                               
                   $add_bene =  Beneficiary::insertGetId(['benficiary_id'=>'','account_number'=>$bank_account,'ifsc'=>$ifsc,'bank_name'=>$full_bank_name,'mobile_number'=>$mobile_number,'vener_id'=>$otp,'status_id'=>0,'user_id'=>Auth::id(),'created_at'=>date("Y-m-d H:i:s"),'sender_id'=>'','customer_number'=>$mobile_number,'name'=>$bname,'api_id'=>1]);
                   if($add_bene)
                   {
                    $data = json_encode(array(['bank_name'=>$full_bank_name,'mobile_number'=>$mobile_number,'account_number'=>$bank_account,'ifsc'=>$ifsc,'bene_res_id'=>1,'name'=>$name]));
                    $message = "Dear Customer, Your Beneficiary verification OTP is :" .$otp.", Thanks";
                    $message = urlencode($message);
                   $msgdata =SendSMS::sendsms($mobile_number, $message,Auth::user()->company_id);
                  if($msgdata)
                  {
                    Remitteregister::where('mobile',$mobile_number)->update(['otp'=>$otp]);
                    return Response::json(['status_id'=>25,'message'=>'OTP has been sent on your register mobile number,Please verify beneficiary!','bene_id'=>$add_bene,'data'=>$data]);
                  }
                 }
                   else
                   {
                    return Response::json(['status_id'=>26,'message'=>'Beneficiary not added!','bene_id'=>0]);
                   }
                }
                else
                {
                    return Response::json(['status_id'=>24,'message'=>'Invalid request, or missing Paramitter(s)']);
                    exit;
                }
            }
            catch(\Exception $e)
            {
                return $e;
                return Response::json(['status_id'=>400,'message'=>'Something is wrong, Please try after sometime!']);
                exit;
            }
        }
    }

    public function get_all_beneficiary(Request $request) {
        $rules = array('mobile_number' => 'required');
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return Response::json($validator->errors()->getMessages(), 400);
        } else {
            try{
            $mobile_number = $request->mobile_number;
        if($mobile_number)
        {
           $bene_list = Beneficiary::orderBy('id','DESC')->where('mobile_number',$mobile_number)->where('api_id',1)->select('id','benficiary_id','account_number','ifsc','bank_name','name')->take(20)->get();
           if($bene_list)
           {
            try{
             $bene_list_result["data"] = array();
           foreach ($bene_list as $key => $value) {
            $data = array();
             $data['bene_id'] = $value->id;
             $data['recipient_name'] = $value->name;
             $data['account'] = $value->account_number;
             $data['ifsc'] = $value->ifsc;
             $data['bank_name'] = $value->bank_name;
              array_push($bene_list_result["data"], $data);
           }
           $data = $bene_list_result;
            return Response::json(['status_id'=>26,'bene_list'=>$data]);
            }
            catch(\Exception $e)
            {
                 return Response::json(['status_id'=>400,'message'=>'Something is wrong, Please try after sometime!']);
                 exit;
            }
           }
           else
           {
                Response::json(['status_id'=>26,'message'=>'Beneficiary not listing, try after sometime!']);
           }
       }
       else
       {
        return Response::json(['status_id'=>24,'message'=>'Invalid request, or missing Paramitter(s)']);
                    exit;
       }
           
   }
   catch(\Exception $e)
   {
        return Response::json(['status_id'=>400,'message'=>'Something is wrong, Please try after sometime!']);
       exit;
  }
           
    }

}
   
    public function delete_beneficiary(Request $request) {
        $rules = array('mobile_number' => 'required');
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return Response::json($validator->errors()->getMessages(), 400);
        } else {
            try
            {
            $beneficiary_id = $request->beneficiary_id;
            $mobile_number = $request->mobile_number;
            if($beneficiary_id!='' &&  $mobile_number!='')
            {
            $bene_delete = Beneficiary::find($beneficiary_id);    
            $bene_delete->delete();
            if($bene_delete){
            return Response::json(['status_id'=>28,'message'=>'Beneficiary deleted successfully!','deleted_bene_id'=>$beneficiary_id]);
            }
            else
            {
                return Response::json(['status_id'=>29,'message'=>'Beneficiary not deleted,try after sometime!']);
            }
          }
           else
           {
            return Response::json(['status_id'=>24,'message'=>'Bad request or parameter(s) missing!']);
           }
       }
       catch(\Exception $e)
       {
            return Response::json(['status_id'=>400,'message'=>'Something is wrong, Please try after sometime!']);
            exit;
       }
    }
}
    
    public function check_duplicate($number, $amount, $user_id)
    {
    
        $date = new \DateTime;
        $date->modify('-1 minutes');
        $formatted_date = $date->format('Y-m-d H:i:s');
        $formatted_date = Carbon::now()->subSeconds(20)->toDateTimeString();
        $result = DB::table('reports')->select('number', 'amount', 'user_id', 'created_at')->where('number', $number)->where('amount', $amount)->where('user_id', $user_id)->where('created_at', '>=', $formatted_date)->orderBy('created_at', 'desc')->get();
        if ($result) {
            return array('status' => 1, 'message' => 'Same Number Same Amount, try after 15 minutes to 1 Hr');
        } else {
            return array('status' => 0, 'message' => 'not found');
        }
    }



    // public function sendsms($number, $message)
    // {
    //     $c_id = Company::where('id',$company_id)->first();
    //     $sms_auth = '155642AHf14giDR593a71db';
    //     $sms_sender = $c_id->sms_sender;
    // $url = "https://control.msg91.com/api/sendhttp.php?authkey=$sms_auth&mobiles=$number&message=$message&sender=$sms_sender&route=4&country=91";
    //     //$url = "http://www.sms21.co.in/sms/api?username=8527735551&password=12345&senderid=LEVINM&number=$number&message=$message";
    //     $curl = curl_init();
    //     curl_setopt($curl, CURLOPT_URL, $url);
    //     curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    //     $data = curl_exec($curl);
    //     curl_close($curl);
    //     return $data;
    // }

    public function txn_status(Request $request)
    {
         $reports = Report::where('ackno',$request->txnid)->select('api_id','id')->first();
               if($reports->api_id == 16 ) {
                $mobile_number = $request->mobile_number;
                $txnid = $request->txnid;
                $txn_details = Report::where('ackno',$txnid)->select('id','user_id','status_id','amount','channel','bank_ref')->first();
                if($txn_details->channel==1)
                {
                    $txn_mode = 'NEFT';
                }
                else
                {
                    $txn_mode = 'IMPS';
                }
                if($txn_details->status_id==1 && $txn_details->channel==2)
                {
                    return Response::json(['status_id'=>42,'message'=>'Transaction succes!','AmountRequested'=>$txn_details->amount,'TxnDescription'=>$txn_mode,'utr'=> $txn_details->bank_ref]);
                }
                elseif($txn_details->status_id==1 && $txn_details->channel==1)
                {
                    return Response::json(['status_id'=>42,'message'=>'Transaction succes!','AmountRequested'=>$txn_details->amount,'TxnDescription'=>$txn_mode,'utr'=>substr($txn_details->bank_ref,10,11)]);
                }
                elseif($txn_details->status_id==3)
                {
                     return Response::json(['status_id'=>49,'message'=>'Transaction Pending!','AmountRequested'=>$txn_details->amount,'TxnDescription'=>$txn_mode,'utr'=>'','TxnID'=>$txn_details->id]);
                }
                 elseif($txn_details->status_id==9)
                {
                     return Response::json(['status_id'=>53,'message'=>'Transaction Initiated!','AmountRequested'=>$txn_details->amount,'TxnDescription'=>$txn_mode,'utr'=>'','TxnID'=>$txn_details->id]);
                }
                elseif($txn_details->status_id==2)
                {
                    return Response::json(['status_id'=>50,'message'=>'Transaction Failed!','AmountRequested'=>$txn_details->amount,'TxnDescription'=>$txn_mode,'utr'=>'NA','TxnID'=>$txn_details->id]);
                }
                elseif($txn_details->status_id==20)
                {
                    return Response::json(['status_id'=>51,'message'=>'Refund Pending!','AmountRequested'=>$txn_details->amount,'TxnDescription'=>$txn_mode,'utr'=>'NA','TxnID'=>$txn_details->id]);
                }
                elseif($txn_details->status_id==21)
                {
                    return Response::json(['status_id'=>52,'message'=>'Txn Allreaady Refunded!','AmountRequested'=>$txn_details->amount,'TxnDescription'=>$txn_mode,'utr'=>'','TxnID'=>$txn_details->id]);
                }
                else
                {
                     return Response::json(['message'=>'Transaction Failed!','AmountRequested'=>$txn_details->amount,'TxnDescription'=>$txn_mode,'utr'=>'NA']);
                }

            }
    }

     public function transaction_status(Request $request)
{
     $reports = Report::find($request->txnid);
               if($reports->api_id == 17 ) {
                $mobile_number = $request->mobile_number;
                $txnid = $reports->txnid;
                        $checksum_lib = new \App\Library\Paycash;
                        $checkSum = "";
                        $paramList = array();
                        $paramList["MID"] = "Shighr98191998365442";
                        $paramList["ORDER_ID"] = $reports->txnid;
                        $checkSum = $checksum_lib->getChecksumFromArray($paramList,"DDN8_dvamYVHTCt1");
                        $paramList["CHECKSUM"] = $checkSum;
                        $data_string = "JsonData=".json_encode($paramList);
                        $paycash_api_reqest = "{
                                            'MID':'Shighr98191998365442',
                                            'ORDER_ID':'".$txnid."',
                                            }";
                        $url = 'https://securegw.paytm.in/merchant-status/getTxnStatus';
                        $ch = curl_init();                    
                        curl_setopt($ch, CURLOPT_URL,$url);
                        curl_setopt($ch, CURLOPT_POST, 1); 
                        curl_setopt($ch, CURLOPT_POSTFIELDS,$data_string); 
                        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); 
                        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);     
                        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);    
                        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
                        $output = curl_exec ($ch); 
                        $info = curl_getinfo($ch);
                        $result = json_decode($output);
                        Apiresponse::create(['message' => $output, 'api_type' => 'PaytmCheckStatus','user_id'=>Auth::id(),'report_id'=>$request->txnid,'request_message'=>$paycash_api_reqest]);
                        if($result->RESPCODE==01 || $result->RESPCODE==1)
                       {
                        $company = Report::find($request->txnid);
                        $company->status_id = 1;
                        $company->bank_ref = $result->BANKTXNID;
                        $company->save();
                        return Response::json(['status_id'=>42,'message'=>'Transaction Succesful','AmountRequested'=>$result->TXNAMOUNT,'TxnDescription'=>$result->RESPMSG,'utr'=> $result->BANKTXNID]);
                    
                       }
                		/* elseif($result->RESPCODE==8065)
                        {
                            $company = Report::find($request->txnid);
                            $company->status_id = 20;
                            $company->refund = 1;
                            $company->save();
                            return Response::json(['status_id'=>2,'message'=>$result->RESPMSG,'AmountRequested'=>$result->TXNAMOUNT,'TxnDescription'=>$result->RESPMSG]);
                        } */
                        elseif($result->RESPCODE==400 || $result->RESPCODE==501)
                        {
                             $company = Report::find($request->txnid);
                             $company->status_id = 3;
                             $company->save();
                            return Response::json(['status_id'=>49,'message'=>'Transaction Pending!','AmountRequested'=>$result->TXNAMOUNT,'TxnDescription'=>$result->RESPMSG]);
                        }
                        
                        elseif($result->RESPCODE==334 || $result->RESPCODE==02) {
                        
                    return Response::json(['status_id'=>50,'message'=>$result->RESPMSG,'AmountRequested'=>$result->TXNAMOUNT,'TxnDescription'=>$result->RESPMSG]);
                }
                else
                {
                  return Response::json(['message'=>$result->RESPMSG,'AmountRequested'=>$result->TXNAMOUNT,'TxnDescription'=>$result->RESPMSG]);
                }
                
                # JSON-encode the response
                header('Access-Control-Allow-Origin: *');
                header('content-type: application/json; charset=utf-8');
                
                curl_close($ch);
            }
            else
            {
                return Response::json(['status_id'=>'Failed','message'=>'Not able to CheckStatus!']);
                exit;
            }  

}

        public function refund_otp(Request $request)
        {
           $txnid = $request->txnid;
           if ($txnid) {
            $reportdetail = Report::find($txnid);
            $api = $reportdetail->api_id;
            $amount = $reportdetail->amount;
            $txid = $reportdetail->txnid;
            $beneficiayrid = $reportdetail->beneficiary_id;
            $ackno = $reportdetail->ackno;
            $channel = $reportdetail->channel;
            $amount = $reportdetail->amount;
            $mobile_number = $request->mobile_number;
            if ($channel == 1) {
                $channel = "NEFT";
            } else {
                $channel = "IMPS";
            }
            return Response::json(['id' => $txnid, 'amount' => $amount, 'txid' => $txid, 'mobile_number' => $mobile_number, 'api' => $api]);
        } else {
            return Response::json(array('id' => '', 'amount' => '', 'txid' => '', 'mobile_number' => '', 'api' => ''));
        }
    }
    
    public function refund_otp_resend(Request $request)
    {
        try
        {
        $mobile_number = $request->mobile_number;
        $id = $request->payid;
        $txnid = $request->txnid;
        $amount = $request->re_amount;
        if($mobile_number!='' && $txnid!='')
        {
            $ref_req = Refundrequest::where('txnid',$txnid)->select('otp','txnid')->first();
            if($ref_req)
            {
                $digits = 4;
                $otp = rand(pow(10, $digits-1), pow(10, $digits)-1);
                $otp_msg = urlencode('Dear Customer,Your OTP for refund is:'.$otp);
                $update_otp = Refundrequest::where('txnid',$txnid)
                ->update(['otp'=>$otp]);
                if( $update_otp)
                {
                    $sendsms = SendSMS::sendsms($mobile_number,$otp_msg,Auth::user()->company_id);
                    return Response::json(['status_id'=>45,'message'=>'OTP successfully sent on your number!','txnid'=>$txnid]);
                }
                else
                {
                    return Response::json(['status_id'=>44,'message'=>'OTP successfully sent on your number!','txnid'=>$txnid]);
                }

            }
            else
            {
                     $digits = 4;
                     $otp = rand(pow(10, $digits-1), pow(10, $digits)-1);
                    $otp_msg = urlencode('Dear Customer,Your OTP for refund is:'.$otp);
                     $generate_otp = Refundrequest::create(['ref_id'=>$id,'number'=>$mobile_number,'txnid'=>$txnid,'amount'=>$amount,'otp'=>$otp,'status'=>1]);
                if($generate_otp)
                {
                     $sendsms = SendSMS::sendsms($mobile_number,$otp_msg,Auth::user()->company_id);
                     return Response::json(['status_id'=>45,'message'=>'OTP successfully sent on your number!','txnid'=>$txnid]);
                }
                else
                {
                      return Response::json(['status_id'=>44,'message'=>'OTP successfully sent on your number!','txnid'=>$txnid]);
                }
            }

        }
        else
        {
            return Response::json(['status_id'=>24,'message'=>'Invalid request or missing paramitter(s)!']);
        }
    }
    catch(\Exception $e)
    {
        return Response::json(['status_id'=>400,'message'=>'Something went wrong, please try after sometime!']);
        exit;
    }
}

public function refund_success(Request $request)
{
    try
    {
     $mobile_number = $request->mobile_number;
     $txnid = $request->txnid;
     $otp = $request->otp;
     $id = $request->payid;
     if($mobile_number!='' && $txnid!='' && $otp!='' && $id!='')
     {
        $re_details = Refundrequest::where('txnid',$txnid)->select('otp','txnid','status')->first();
        if($re_details)
        {
            if($otp == $re_details->otp && $re_details->status==1)
            {
              $reportcheck = Report::find($id);
              if($reportcheck->status_id==20 && $reportcheck->refund==1)
              {
            $now = new \DateTime();
            $datetime = $now->getTimestamp();
            $ctime = $now->format('Y-m-d H:i:s');
            $report = Report::find($id);
            $amount = $report->amount;
            $user_id = $report->user_id;
            $userbal = Balance::where('user_id',$user_id)->select('user_balance')->first();
            $total_amount = $userbal->user_balance+$amount;
                $report->status_id = 21;
                $report->refund = 0;
                $report->save();
            $insert_id = Report::insertGetId([
                    'number' => $report->number,
                    'provider_id' => 0,
                    'amount' => $amount,
                    'api_id' => 16,
                    'status_id' => 4,
                    'description' => 'Txn Refunded',
                    'txnid' => $id,
                    'pay_id' => $datetime,
                    'created_at' => $ctime,
                    'user_id' => $user_id,
                    'channel' => 2,
                    'total_balance' =>$total_amount,
                ]); 
             Balance::where('user_id', $user_id)->increment('user_balance', $amount);
            $userdetail = Balance::where('user_id', $user_id)->select('user_balance')->first();
            Refundrequest::where('txnid',$txnid)->update(['status'=>0]);
            return Response::json(['status_id'=>46,'message'=>'Refund successfully done!','txnid'=>$insert_id,'ref_id'=>$id]);
        }
        else
        {
             return Response::json(['status_id'=>48,'message'=>'Allready refunded!','request_id'=>$id]);
        }

        }
        else
        {
            return Response::json(['status_id'=>47,'message'=>'Wrong OTP,Try Again!','request_id'=>$id]);
        }
    }
    else
    {
        return Response::json(['status_id'=>47,'message'=>'Invalid refund request!','request_id'=>$id]);
    }
  }
  else
  {
     return Response::json(['status_id'=>24,'message'=>'Invalid request or missing paramitter(s)!']);
  }
 }
    catch(\Exception $e)
    {
        return $e;
        return Response::json(['status_id'=>400,'message'=>'Something went wrong, please try after sometime!']);
        exit;
    }

}

public function refundview(Request $request)
    {
        $txnid = $request->txnid;
        if ($txnid) {
            $reportdetail = Report::find($txnid);
            $api = $reportdetail->api;
            $amount = $reportdetail->amount;
            $txid = $reportdetail->txnid;
            $beneficiayrid = $reportdetail->beneficiary_id;
            $ackno = $reportdetail->ackno;
            $channel = $reportdetail->channel;
            $amount = $reportdetail->amount;
            $mobile_number = $reportdetail->customer_number;
            if ($channel == 1) {
                $channel = "NEFT";
            } else {
                $channel = "IMPS";
            }
 return Response::json(array('id' => $txnid, 'amount' => $amount, 'txid' => $txid, 'mobile_number' => $mobile_number, 'api' => $api));
        } else {
            return Response::json(array('id' => '', 'amount' => '', 'txid' => '', 'mobile_number' => '', 'api' => ''));
        }
    }
    public function getBulkTxnReport(Request $request)
    {
        $record_id = $request->record_id;
        $reports = Report::find($record_id);
        // print_r($reports);
        $reports=$reports->bulktransactions;
       // $reports = BulkTransaction::where('report_id',$ref_id)->get();
        if($reports)
        {
             return Response::json(['status'=>1,'list'=>$reports]);
        }
        return Response::json(['status'=>0]);
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
    public function show($id) {
        //
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
