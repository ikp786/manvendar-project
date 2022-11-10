<?php

namespace App\library {
use App\Apiresponse;
use Carbon\Carbon;
class Bbps    {
    function check_bill ($cybercode, $number){
    $allurl = $this->geturlbycompany($cybercode);
    define('CERT', '79A59006B3CCEEB64A72E74D55DB6D8AF7FE4DC0');
    define('CP_PASSWORD', 'a2z@123');
    $isSpecial = 'true';
    $id =  Carbon::now()->timestamp;
    $verification = $allurl['verification'];
    $payment = $allurl['payment'];
    $status = $allurl['status'];
			$SD = 347382;
            $AP = 348816;
            $OP = 348817;
    $secKey = file_get_contents("private.key");
    $passwd = CP_PASSWORD;
    $serverCert = file_get_contents("mycert.pem");
    $sessPrefix = rand(100, 300);
    $sess = $sessPrefix . $number . time();
    $sess = substr($sess, -20);
    $amount = "1";
    $querString = "CERT=" . CERT . "\r\nSESSION=$sess\r\nSD=$SD\r\nAP=$AP\r\nOP=$OP\r\nNUMBER=$number\r\nAMOUNT=$amount\r\nAMOUNT_ALL=$amount\r\nCOMMENT=Test recharge";
    if (!empty($optional1)) {
                $querString = "CERT=" . CERT . "\r\nSESSION=$sess\r\nSD=$SD\r\nAP=$AP\r\nOP=$OP\r\nNUMBER=$number\r\nAMOUNT=$amount\r\nAMOUNT_ALL=$amount\r\nCOMMENT=Test recharge";
    }
            $pkeyid = openssl_pkey_get_private($secKey, $passwd);
            openssl_sign($querString, $signature, $pkeyid, OPENSSL_ALGO_SHA1);
            openssl_free_key($pkeyid);

            $encoded = base64_encode($signature);
            $encoded = chunk_split($encoded, 76, "\r\n");

            $errCode = null;
            $rsCode = null;
            $signInMsg = "BEGIN\r\n" . $querString . "\r\nEND\r\nBEGIN SIGNATURE\r\n" . $encoded . "END SIGNATURE\r\n";
            $response = $this->get_query_result($signInMsg, $verification);
            $lines = preg_split('/\n|\r\n?/', $response);
            $details = urldecode($lines[8]);
            if ($details == 'END') {
            $error = explode('=', trim($lines[7]));
            $message = "Invalid number format";
            $status = 2;
            $amount = "";
            }else{
            $error = explode('=', trim($lines[8]));
            $amount = $error[1];
            $message = "verification Success";
            $status = 1;
            }
            return Response()->json(['status' => $status, 'amount' => $amount, 'message' => $message]);
    }



    function sendrecharge ($cybercode, $insert_id, $number, $amount, $user_id, $account, $cycle){
    define('CERT', '79A59006B3CCEEB64A72E74D55DB6D8AF7FE4DC0');
    define('CP_PASSWORD', 'a2z@123');
    $allurl = $this->geturlbycompany($cybercode);
    $isSpecial = 'true';
    $verification = $allurl['verification'];
    $payment = $allurl['payment'];
    $status = $allurl['status'];
			$SD = 347382;
            $AP = 348816;
            $OP = 348817;
    $secKey = file_get_contents("private.key");
    $passwd = CP_PASSWORD;
    $serverCert = file_get_contents("mycert.pem");
    $sessPrefix = rand(100, 300);
	$sess = $insert_id;
    $querString = "CERT=" . CERT . "\r\nSESSION=$sess\r\nSD=$SD\r\nAP=$AP\r\nOP=$OP\r\nNUMBER=$number\r\nAMOUNT=$amount\r\nAMOUNT_ALL=$amount\r\nCOMMENT=Test recharge";
    if (!empty($optional1)) {
    $querString = "CERT=" . CERT . "\r\nSESSION=$sess\r\nSD=$SD\r\nAP=$AP\r\nOP=$OP\r\nNUMBER=$number\r\nAMOUNT=$amount\r\nAMOUNT_ALL=$amount\r\nCOMMENT=Test recharge";
    }
    $pkeyid = openssl_pkey_get_private($secKey, $passwd);
    openssl_sign($querString, $signature, $pkeyid, OPENSSL_ALGO_SHA1);
    openssl_free_key($pkeyid);
    $encoded = base64_encode($signature);
    $encoded = chunk_split($encoded, 76, "\r\n");
    $errCode = null;
    $rsCode = null;
    $signInMsg = "BEGIN\r\n" . $querString . "\r\nEND\r\nBEGIN SIGNATURE\r\n" . $encoded . "END SIGNATURE\r\n";
    $response = $this->get_query_result($signInMsg, $verification);
	//print_r($response);die;
	 Apiresponse::create(['message' => $response, 'api_type' => 15,'report_id'=>$insert_id,'request_message'=>$signInMsg]);
    $lines = preg_split('/\n|\r\n?/', $response);
    $error = explode('=', trim($lines[4]));
    if (strpos($response, 'ERROR=0') == true) {
    $response = $this->get_query_result($signInMsg, $payment);
    if (strpos($response, 'ERROR=0') == false) {
    return array('status' => 2, 'txnid' => 2, 'message' => '', 'response' => $response);
    } else {
    if (strpos($response, 'AUTHCODE=') == false) {
    $txid = '0';
    } else {
    $Split_auth = explode('AUTHCODE=', $response);
    $OPT_Code = explode('END', $Split_auth[1]);
    if (!empty($OPT_Code[0])) {
    $txid = trim($OPT_Code[0]);
    $lines = preg_split('/\n|\r\n?/', $txid);
    if ($lines) {
    $txid = $lines[0];
    }
    } else {
    $txid = '0';
    }
    }
    return array('status' => 1, 'txnid' => $txid, 'message' => '', 'response' => $response, 'ref_id' => '');
    //echo $session_id . '#Success#' . $txid.'#'.$total_amount;
    }
    } else {
    $erc = trim($error[1]);
     return array('status' => 2, 'txnid' => $erc, 'message' => '', 'response' => $response, 'ref_id' => '');
      //echo $session_id . '#Failure#' . $erc.'#'.$balance;
     }
    }

    function get_query_result($qs, $url){
    $opts = array(
    'http' => array(
    'method' => "POST",
    'header' => array("Content-type: application/x-www-form-urlencoded\r\n" ."X-CyberPlat-Proto: SHA1RSA\r\n"),
    'content' => "inputmessage=" . urlencode($qs)
     )
     );
    $context = stream_context_create($opts);
    return file_get_contents($url, false, $context);
    }


    function geturlbycompany ($cybercode){
    return array(
        'verification' =>  "https://in.cyberplat.com/cgi-bin/bu/bu_pay_check.cgi/$cybercode",
        'payment' => "https://in.cyberplat.com/cgi-bin/bu/bu_pay.cgi/$cybercode",
        'status' => "https://in.cyberplat.com/cgi-bin/bu/bu_pay_status.cgi",
        'isSpecial' => 'false');
    }
}
}