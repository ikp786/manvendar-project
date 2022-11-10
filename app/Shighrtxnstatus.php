<?php

namespace App\Console\Commands;

use App\Apiresponse;
use Illuminate\Console\Command;
use App\Company;
use App\Report;
use App\User;
use App\Balance;
use App\Refundrequest;
use App\Closing_balance;
use Response;

class Shighrtxnstatus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'know:Shighrtxnstatus';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Know the Shighrtxnstatus';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
     
                $report_detail = Report::where('status_id',3)->where('api_id',16)->take(500)->get();
              foreach($report_detail as $report_details)
              {
                $mobile_number = $report_details->customer_number;
                $txnid = $report_details->txnid;
                $enclib = new \App\Library\GibberishAES;
                $body =  '{"ClientUniqueID":"'.$txnid.'"}';
                $header =  '{"ClientId":25,"AuthKey":"edd885a0-2497-4387-ac6e-62b019430ee3"}';
                $headerdata = $res->enc($header,'982b0d01-b262-4ece-a2a2-45be82212ba1');
                $bodydata = $res->enc($body,'9c32f19a-ee15-4e19-865a-bba3d137afd0');
                $endpoint = 'https://fpbservices.finopaymentbank.in/FinoMoneyTransferService/UIService.svc/FinoMoneyTransactionApi/TxnStatusRequest';
             
                $headers = array(
                "content-type:application/json",
                "authentication:".$headerdata."",
                    );
                   $ch = curl_init();
                    curl_setopt($ch, CURLOPT_URL, $endpoint);
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                    curl_setopt($ch, CURLOPT_TIMEOUT, 1000);
                    curl_setopt($ch, CURLOPT_POST, true);
                    curl_setopt($ch, CURLOPT_POSTFIELDS, "\"".$bodydata."\"");
                    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
                    $res = curl_exec($ch);
                    curl_close($ch);
                    $data = json_decode($res);
                    $decriptdata =  $enclib->dec($data->ResponseData,'9c32f19a-ee15-4e19-865a-bba3d137afd0');
                    $result = json_decode($decriptdata);
                   Apiresponse::create(['message' => $decriptdata, 'api_type' => 'CronCheckStatus','user_id'=>$report_details->user_id,'report_id'=>$report_details->id,'request_message'=>$body]);
                    if($result->ActCode=='S')
                    {
                        //IMPS SUCCESS
                    $company = Report::find($report_details->id);
                    $company->status_id = 1;
                    $company->bank_ref = $result->TxnID;
                    $company->refund = 0;
                    $company->save();

                }
                elseif($result->ActCode==26||$result->ActCode==11)
                {
                     //NEFT SUCCESS
                    $company = Report::find($report_details->id);
                    $company->status_id = 1;
                    $company->refund = 0;
                    $company->save();
                }
                elseif($result->ActCode == "1012" || $result->ActCode == "1009" ||$result->ActCode == "100"||$result->ActCode == "200"||$result->ActCode == "504" ||$result->ActCode == "503"||$result->ActCode == "9999" ||$result->ActCode == "1011"||$result->ActCode == "999"||$result->ActCode == "1004"||$result->ActCode == "1002" ||$result->ActCode == "1010" ||$result->ActCode =="401"||$result->ActCode =="5001") {

                    $company = Report::find($report_details->id);
                    $company->status_id = 3;
                    $company->refund = 0;
                    $company->txnid = $data->ClientUniqueID;
                    $company->save();

                }

                elseif($result->ActCode=='P')
                {
                     $company = Report::find($report_details->id);
                     $company->status_id = 3;
                     $company->bank_ref = $result->TxnID;
                     $company->refund = 0;
                     $company->save();
                  
                }
                elseif($result->ActCode == 20 || $result->ActCode == 9 || $result->ActCode==7 || $result->ActCode == 1 || $result->ActCode == 2)
                {
                    $company = Report::find($report_details->id);
                    $company->status_id = 3;
                    $company->refund = 0;
                    $company->save();
                   
                }
                 elseif($result->ActCode=='R'|| $result->ActCode== 23 || $result->ActCode==21 || $result->ActCode==12) {
                    $company = Report::find($report_details->id);
                    $company->status_id = 20;
                    $company->refund = 1;
                    $company->save();
                    $digits = 4;
                    $otp = rand(pow(10, $digits-1), pow(10, $digits)-1);
                    $ref_pend_detatil = Refundrequest::where('ref_id',$report_details->id)->first();
                    if($ref_pend_detatil)
                    {
                        
                    }
                    else
                    {
                        Refundrequest::create(['ref_id'=>$report_details->id,'number'=>$report_details->customer_number,'txnid'=>$report_details->txnid,'amount'=>$report_details->amount,'otp'=>$otp,'status'=>1]);
                    }
                }
                else
                {
                  
                }

        }  
      
    }
}
