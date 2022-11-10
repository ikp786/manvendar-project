<?php
namespace App\Library\paytm\lib {
use	DB; 
use App\Apiresponse;
use Auth;
    class PaytmResponce
    {
        public function getPaytmResponse($order_id) {
        	 
         
            $paytmParams = array();
          // echo "cont ="; print_r($order_data);
            $x_mid = "ExcelO34350059108446"; // live
           // $x_mid = "ExcelO15291578428739"; // testing
            $paytmParams["orderId"] = $order_id; 
            
            
            $EncdecPaytm = new \App\Library\paytm\lib\EncdecPaytm;     
            
             
            //$paytmParams["CHECKSUMHASH"] = $checksum; 
            $post_data = json_encode($paytmParams, JSON_UNESCAPED_SLASHES);
            $x_checksum = $EncdecPaytm->getChecksumFromString($post_data, "BTNRm#FsTJrvXNlQ"); //  live
            
             ////$x_checksum = $EncdecPaytm->getChecksumFromString($post_data, "7A4pzcs#t_Q!M4hx"); //testing
     
          //  echo 'getPaytmResponse post data = '.$post_data;  
    
          //$url = "https://staging-dashboard.paytm.com/bpay/api/v1/disburse/order/query"; //testing
            
           $url = "https://dashboard.paytm.com/bpay/api/v1/disburse/order/query"; // live
           $ch = curl_init($url); 
           curl_setopt($ch, CURLOPT_POST, true);
           curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
           curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type: application/json", "x-mid: " . $x_mid, "x-checksum: " . $x_checksum)); 
           curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
           $response = curl_exec($ch);
            
           
            
         //   echo '<br/><br/>getPaytmResponse response = '.$response;  
            
        	return $response;
        }
    }
}