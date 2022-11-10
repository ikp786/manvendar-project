<?php 
namespace App\Library\paytm\lib {
use	DB; 
use App\Apiresponse;
use Auth;
 
    class BankTransfer
    {
 
    	public function call_paytm($content){
     
         
            $paytmParams = array();

            /* Find Sub Wallet GUID in your Paytm Dashboard at https://dashboard.paytm.com */
           // $paytmParams["subwalletGuid"] = "416b9a5e-0794-11ea-8708-fa163e429e83";  //  testing
            
            $paytmParams["subwalletGuid"] = "ccde7a06-062d-4f7c-a2cf-6cba26797241"; // dev subwalletGuid old
            
            //$paytmParams["subwalletGuid"] = "bb12d67d-c5a5-4824-8a2f-a2adc9eea371"; // live subwalletGuid
            //$paytmParams["comments"] = $content["COMMENT"]; 
            /* Enter your order id which needs to be check disbursal status for */
            $paytmParams["orderId"] = $content["ORDER_ID"];
            
            /* Enter Beneficiary Account Number in which the disbursal needs to be made */
            $paytmParams["beneficiaryAccount"] = $content["beneficiaryAccount"];
            
            /* Enter Beneficiary's Bank IFSC Code */
            $paytmParams["beneficiaryIFSC"] = $content["beneficiaryIFSC"];
            
            /* Amount in INR to transfer */
            $paytmParams["amount"] =$content["amount"];
            
            /* Enter Purpose of transfer, possible values are: SALARY_DISBURSEMENT, REIMBURSEMENT, BONUS, INCENTIVE, OTHER */
            $paytmParams["purpose"] = "OTHERS";
            
            /* Enter the date for which you wants to disburse the amount. Required if purpose is SALARY_DISBURSEMENT or REIMBURSEMENT */
            $paytmParams["date"] = date('Y-m-d'); 
            
            /* Paytm call back URl */
            $paytmParams["callbackUrl"] = "http://dev.excelonestopsolution.com/a2z-paytm-callback-url";
            
            /* prepare JSON string for request body */
            $post_data = json_encode($paytmParams, JSON_UNESCAPED_SLASHES);
            
    
            /**
            * Generate checksum by parameters we have in bod
            * Find your Merchant Key in your Paytm Dashboard at https://dashboard.paytm.com/next/apikeys 
            */
            $EncdecPaytm = new \App\Library\paytm\lib\EncdecPaytm;
    		  
           // $checksum = $EncdecPaytm->getChecksumFromString($post_data,"7A4pzcs#t_Q!M4hx"); //  testing
            
             $checksum = $EncdecPaytm->getChecksumFromString($post_data,"BTNRm#FsTJrvXNlQ"); //live
            //echo $checksum;
            /* Find your MID in your Paytm Dashboard at https://dashboard.paytm.com/next/apikeys */
             $x_mid ="ExcelO34350059108446"; // live
            
             //$x_mid ="ExcelO15291578428739"; // testing
             
            /* put generated checksum value here */
            $x_checksum = $checksum;
           
           
             /* for Staging */
            //$url = "https://staging-dashboard.paytm.com/bpay/api/v1/disburse/order/bank"; // testing
             
            /* for Production */
             $url = "https://dashboard.paytm.com/bpay/api/v1/disburse/order/bank"; // live
 
 // echo 'call_paytm post data = '.$post_data;  
             
            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type: application/json", "x-mid: " . $x_mid, "x-checksum: " . $x_checksum)); 
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); 
            $response = curl_exec($ch);
            
           // echo "<br/>retrn rep=<pre>"; print_r($response);  
            return $response;
            
    	}

   	 
    }
}
?>