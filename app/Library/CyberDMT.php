<?php

namespace App\library {
use App\Apiresponse;
use Auth;
    class CyberDMT
    {
        var $CERT = '79A59006B3CCEEB64A72E74D55DB6D8AF7FE4DC0';
        var $PASSWORD = 'a2z@123';
        function get_query_result($qs, $url)
        {
            $opts = array(
                'http' => array(
                    'method' => "POST",
                    'header' => array("Content-type: application/x-www-form-urlencoded\r\n" .
                        "X-CyberPlat-Proto: SHA1RSA\r\n"),
                    'content' => "inputmessage=" . urlencode($qs)
                )
            );
            $context = stream_context_create($opts);
            return file_get_contents($url, false, $context);
        }

        function check_signature($response, $serverCert)
        {
            $fields = preg_split("/END\r\nBEGIN SIGNATURE\r\n|END SIGNATURE\r\n|BEGIN\r\n/", $response, NULL, PREG_SPLIT_NO_EMPTY);
            if (count($fields) != 2) {
                print "Bad response\n";
                return;
            }

            $pubkeyid = openssl_pkey_get_public($serverCert);
            $ok = openssl_verify(trim($fields[0]), base64_decode($fields[1]), $pubkeyid);
            print "Signature is ";
            if ($ok == 1) {
                print "good";
            } elseif ($ok == 0) {
                print "bad";
            } else {
                print "ugly, error checking signature";
            }
            print "\n";
            openssl_free_key($pubkeyid);
        }

        public function is_ok($id)
        {
            return $id;
        }

        public function balance()
        {
            define('CERT', '79A59006B3CCEEB64A72E74D55DB6D8AF7FE4DC0');
            define('CP_PASSWORD', 'a2z@123');


            $check_url = "https://in.cyberplat.com/cgi-bin/mts_espp/mtspay_rest.cgi";
            $pay_url = "https://in.cyberplat.com/cgi-bin/id/id_pay.cgi";
            $verify_url = "https://in.cyberplat.com/cgi-bin/status/get_status.cgi";

            $SD = 347382;
            $AP = 348816;
            $OP = 348817;

            $phNbr = "7210500777";
            $amount = "10.00";

            $secKey = file_get_contents("private.key");
            $passwd = CP_PASSWORD;
            $serverCert = file_get_contents("mycert.pem");

            $sessPrefix = rand(100, 300);
            $sess = $sessPrefix . $phNbr . time();
            $sess = substr($sess, -20);
            $querString = "CERT=" . CERT . "\r\nSESSION=$sess\r\nSD=$SD\r\nAP=$AP\r\nOP=$OP\r\nNUMBER=$phNbr\r\nAMOUNT=$amount\r\nAMOUNT_ALL=$amount\r\nCOMMENT=Test recharge";

            $pkeyid = openssl_pkey_get_private($secKey, $passwd);
            openssl_sign($querString, $signature, $pkeyid, OPENSSL_ALGO_SHA1);
            openssl_free_key($pkeyid);

            $encoded = base64_encode($signature);
            $encoded = chunk_split($encoded, 76, "\r\n");

            $signInMsg = "BEGIN\r\n" . $querString . "\r\nEND\r\nBEGIN SIGNATURE\r\n" . $encoded . "END SIGNATURE\r\n";
            //print "Signed request:\n$signInMsg\n";


            $response = $this->get_query_result($signInMsg, $check_url);

            if ($response) {
                $httpResponseAr = explode('REST=', $response);
                $balf = explode('END', $httpResponseAr[1]);
                echo $balf[0];
            } else {
                print "Bad response\n";
            }


        }

       private function getRequestTypeUrl($type)
       {
           return array('verification' => 'https://in.cyberplat.com/cgi-bin/instp/instp_pay_check.cgi',
                'payment' => 'https://in.cyberplat.com/cgi-bin/instp/instp_pay.cgi',
                'status' => 'https://in.cyberplat.com/cgi-bin/instp/instp_pay_status.cgi',
                'isSpecial' => 'false'
            );
           
       }

       // public function makeDMTTransaction($account_no, $session_id, $number, $amount, $user_id, $account, $cycle)
        public function makeDMTTransaction($type,$number, $amount, $f_name=null, $l_name=null, $rem_id=null, $bene_account=null,$bene_ifsc=null,$beni_id=null,$pin=null,$bank_city=null,$oct=null)
        {
            
            define('CERT', '79A59006B3CCEEB64A72E74D55DB6D8AF7FE4DC0');
            define('CP_PASSWORD', 'a2z@123');
            $ALL_URL = $this->getRequestTypeUrl($type);
            if ($ALL_URL['isSpecial'] == 'true') {
                $isSpecial = 'true';
            } else {
                $isSpecial = 'false';
            }
            $id = $number;
            $verification = $ALL_URL['verification'];
            $payment = $ALL_URL['payment'];
            $status = $ALL_URL['status'];
            $SD = 347382;
            $AP = 348816;
            $OP = 348817;

            $secKey = file_get_contents("private.key");
            $passwd = CP_PASSWORD;
            $serverCert = file_get_contents("mycert.pem");

            $sessPrefix = rand(100, 300);
            $sess = $sessPrefix . $number . time();
            $sess = substr($sess, -20);
            if($type == 5)
                $querString = "CERT=" . CERT . "\r\nSESSION=$sess\r\nSD=$SD\r\nAP=$AP\r\nOP=$OP\r\nNUMBER=$number\r\nAMOUNT=$amount\r\nAMOUNT_ALL=$amount\r\Type=$type";
            if (!empty($account)) {
                $querString = "CERT=" . CERT . "\r\nSESSION=$sess\r\nSD=$SD\r\nAP=$AP\r\nOP=$OP\r\nNUMBER=$number\r\nACCOUNT=$account\r\nAMOUNT=$amount\r\nAMOUNT_ALL=$amount\r\nAuthenticator3=$cycle\r\nCOMMENT=Test recharge";
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
            Apiresponse::create(['message' => $response, 'api_type' => 333,'report_id'=>$number,'request_message'=>$signInMsg]);
            $lines = preg_split('/\n|\r\n?/', $response);
            $error = explode('=', trim($lines[4]));
            if (strpos($response, 'ERROR=0') == true) {
                $response = $this->get_query_result($signInMsg, $payment);
               // Apiresponse::create(['message' => $response, 'api_type' => 100,'report_id'=>$session_id]);
                if (strpos($response, 'ERROR=0') == false) {
                    return array('status' => 2, 'txnid' => 2, 'message' => '', 'response' => $response, 'ref_id' => '');
                    //echo $session_id . '#Failure#.#'.$balance;
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

	function verification($number)
	{
        /*  define('CERT', $this->CERT);
         define('CP_PASSWORD', $this->PASSWORD);  */
         $url = "https://in.cyberplat.com/cgi-bin/instp/instp_pay_check.cgi";
			$SD = 347382;
            $AP = 348816;
            $OP = 348817;
         $secKey = file_get_contents("private.key");
         $passwd = $this->PASSWORD;
         $serverCert = file_get_contents("mycert.pem");
         $sessPrefix = rand(100, 300);
         $sess = $sessPrefix . $number . time();
         $sess = substr($sess, -20);
         $amount = "1";
         $type = 5;
         $querString = "CERT=" . $this->CERT . "\r\nSESSION=$sess\r\nSD=$SD\r\nAP=$AP\r\nOP=$OP\r\nNUMBER=$number\r\nAMOUNT=$amount\r\nAMOUNT_ALL=$amount\r\nType=5";
         $pkeyid = openssl_pkey_get_private($secKey, $passwd);
         openssl_sign($querString, $signature, $pkeyid, OPENSSL_ALGO_SHA1);
         openssl_free_key($pkeyid);
         $encoded = base64_encode($signature);
         $encoded = chunk_split($encoded, 76, "\r\n");

         $errCode = null;
         $rsCode = null;
         $signInMsg = "BEGIN\r\n" . $querString . "\r\nEND\r\nBEGIN SIGNATURE\r\n" . $encoded . "END SIGNATURE\r\n";
         $response = $this->get_query_result($signInMsg, $url);
         //print_r($response);
		  Apiresponse::create(['message'=>$response,'api_id'=>Auth::id(),'api_type'=>"REMITTER VERIFY",'report_id'=>1001,'request_message'=>$signInMsg]);
         $lines = preg_split('/\n|\r\n?/', $response);
         $error = explode('=', trim($lines[4]));
         if (strpos($response, 'ERROR=0') == true) 
		 {
			 $payment = "https://in.cyberplat.com/cgi-bin/instp/instp_pay.cgi";
			 $response = $this->get_query_result($signInMsg, $payment);
			  Apiresponse::create(['message'=>$response,'api_id'=>Auth::id(),'api_type'=>"REMITTER VERIFY",'report_id'=>1002,'request_message'=>$signInMsg]);
			 $Split_auth = explode('AUTHCODE=', $response);
			 $OPT_Code = explode('=', $Split_auth[0]);
			 $error = $OPT_Code[2];
			 $errorcode = explode('RESULT', $error);
			 $myerror =  $errorcode[0];
			 if ($myerror == 0) 
			 {
				 $data =  urldecode($OPT_Code[4]);
				 $data =  explode('DATE', $data);
				 return $data[0]; 
			 }
			 else
			 {
				return '{"statuscode":"RNF","status":"Remitter Not Found","data":""}'; 
			 }
         }else
		 {
			return '{"statuscode":"RNF","status":"Remitter Not Found","data":""}';
         }
    }
         
        function verifySenderOtp ($parameter_cyber){
         $NUMBER = $parameter_cyber[0];
         $remitterOTP = $parameter_cyber[1];
         $remitterVerifyId = $parameter_cyber[2];
         $url = "https://in.cyberplat.com/cgi-bin/instp/instp_pay_check.cgi";
			$SD = 347382;
            $AP = 348816;
            $OP = 348817;
         $secKey = file_get_contents("private.key");
         $passwd = $this->PASSWORD;
         $serverCert = file_get_contents("mycert.pem");
         $sessPrefix = rand(100, 300);
         $sess = $sessPrefix . $NUMBER . time();
         $sess = substr($sess, -20);
         $amount = "1";
         $querString = "CERT=" . $this->CERT . "\r\nSESSION=$sess\r\nSD=$SD\r\nAP=$AP\r\nOP=$OP\r\nNUMBER=$NUMBER\r\nremId=$remitterVerifyId\r\notc=$remitterOTP\r\nAMOUNT=$amount\r\nAMOUNT_ALL=$amount\r\nType=24";

         $pkeyid = openssl_pkey_get_private($secKey, $passwd);
         openssl_sign($querString, $signature, $pkeyid, OPENSSL_ALGO_SHA1);
         openssl_free_key($pkeyid);
         $encoded = base64_encode($signature);
         $encoded = chunk_split($encoded, 76, "\r\n");
         $errCode = null;
         $rsCode = null;
         $signInMsg = "BEGIN\r\n" . $querString . "\r\nEND\r\nBEGIN SIGNATURE\r\n" . $encoded . "END SIGNATURE\r\n";
         $response = $this->get_query_result($signInMsg, $url);
         $lines = preg_split('/\n|\r\n?/', $response);
         $error = explode('=', trim($lines[4]));
		 //Apiresponse::create(['message'=>$response,'api_id'=>Auth::id(),'api_type'=>"VeriyOTP",'report_id'=>10001,'request_message'=>$signInMsg]);
         if (strpos($response, 'ERROR=0') == true) 
		 {
         $payment = "https://in.cyberplat.com/cgi-bin/instp/instp_pay.cgi";
         $response = $this->get_query_result($signInMsg, $payment);
		 //Apiresponse::create(['message'=>$response,'api_id'=>Auth::id(),'api_type'=>"VeriyOTP",'report_id'=>10001,'request_message'=>$signInMsg]);
         $Split_auth = explode('AUTHCODE=', $response);
         $OPT_Code = explode('=', $Split_auth[0]);
         $exload = explode('RESULT', $OPT_Code[2]);
         $myerror = $exload[0];
         if ($myerror == 24) 
		 {
			return '{"statuscode":"ERR","status":"Error From Server Side","data":""}';
         }else
		 {
			 $data =  urldecode($OPT_Code[4]);
			 $data =  explode('DATE', $data);
			 return $data[0];   
         }        
         }else{
			return '{"statuscode":"ERR","status":"Error From Server Side","data":""}';
         }
         }
		 function add_sender ($parameter_cyber)
		 {
			 
         $NUMBER = $parameter_cyber[0];
         $fName = $parameter_cyber[1];
         $lName = $parameter_cyber[2];
         $Pin = $parameter_cyber[3];
        /*  define('CERT', $this->CERT);
         define('CP_PASSWORD', $this->PASSWORD);  */
         $url = "https://in.cyberplat.com/cgi-bin/instp/instp_pay_check.cgi";
			$SD = 347382;
            $AP = 348816;
            $OP = 348817;
         $secKey = file_get_contents("private.key");
         $passwd = $this->PASSWORD;
         $serverCert = file_get_contents("mycert.pem");
         $sessPrefix = rand(100, 300);
         $sess = $sessPrefix . $NUMBER . time();
         $sess = substr($sess, -20);
         $amount = "1";
         $querString = "CERT=" . $this->CERT . "\r\nSESSION=$sess\r\nSD=$SD\r\nAP=$AP\r\nOP=$OP\r\nNUMBER=$NUMBER\r\nfName=$fName\r\nlName=$lName\r\nAMOUNT=$amount\r\nAMOUNT_ALL=$amount\r\nPin=$Pin\r\nType=0";

         $pkeyid = openssl_pkey_get_private($secKey, $passwd);
         openssl_sign($querString, $signature, $pkeyid, OPENSSL_ALGO_SHA1);
         openssl_free_key($pkeyid);
         $encoded = base64_encode($signature);
         $encoded = chunk_split($encoded, 76, "\r\n");
         $errCode = null;
         $rsCode = null;
         $signInMsg = "BEGIN\r\n" . $querString . "\r\nEND\r\nBEGIN SIGNATURE\r\n" . $encoded . "END SIGNATURE\r\n";
         $response = $this->get_query_result($signInMsg, $url);
         $lines = preg_split('/\n|\r\n?/', $response);
         $error = explode('=', trim($lines[4]));
		 Apiresponse::create(['message'=>$response,'api_id'=>Auth::id(),'api_type'=>"REMITTER_ADD",'report_id'=>1001,'request_message'=>$signInMsg]);
         if (strpos($response, 'ERROR=0') == true) 
		 {
         $payment = "https://in.cyberplat.com/cgi-bin/instp/instp_pay.cgi";
         $response = $this->get_query_result($signInMsg, $payment);
		 Apiresponse::create(['message'=>$response,'api_id'=>Auth::id(),'api_type'=>"REMITTER_ADD",'report_id'=>1002,'request_message'=>$signInMsg]);
         $Split_auth = explode('AUTHCODE=', $response);
         $OPT_Code = explode('=', $Split_auth[0]);
         $exload = explode('RESULT', $OPT_Code[2]);
         $myerror = $exload[0];
         if ($myerror == 24) 
		 {
			return '{"statuscode":"ERR","status":"Error From Server Side","data":""}';
         }
		 else
		 {
			 $data =  urldecode($OPT_Code[4]);
			 $data =  explode('DATE', $data);
			 return $data[0];   
         }        
         }
		 else{
			 return $OPT_Code;
			return '{"statuscode":"ERR","status":"Error From Server Side","data":""}';
         }
         }


         function account_name_info ($parameter_cyber){
         $benAccount = $parameter_cyber[0];
         $benIFSC = $parameter_cyber[1];
         $NUMBER = $parameter_cyber[2];
         /* define('CERT', $this->CERT);
         define('CP_PASSWORD', $this->PASSWORD);  */
         $url = "https://in.cyberplat.com/cgi-bin/instp/instp_pay_check.cgi";
			$SD = 347382;
            $AP = 348816;
            $OP = 348817;
         $secKey = file_get_contents("private.key");
         $passwd = $this->PASSWORD;
         $serverCert = file_get_contents("mycert.pem");
         $sessPrefix = rand(100, 300);
         $sess = $sessPrefix . $NUMBER . time();
         $sess = substr($sess, -20);
         $amount = "1";
         $type = 10;
         $AMOUNT_ALL = "2.50";
         $querString = "CERT=" . $this->CERT . "\r\nSESSION=$sess\r\nSD=$SD\r\nAP=$AP\r\nOP=$OP\r\nNUMBER=$NUMBER\r\nbenAccount=$benAccount\r\nbenIFSC=$benIFSC\r\nAMOUNT=$amount\r\nAMOUNT_ALL=$amount\r\nType=10";
         $pkeyid = openssl_pkey_get_private($secKey, $passwd);
         openssl_sign($querString, $signature, $pkeyid, OPENSSL_ALGO_SHA1);
         openssl_free_key($pkeyid);
         $encoded = base64_encode($signature);
         $encoded = chunk_split($encoded, 76, "\r\n");
         $errCode = null;
         $rsCode = null;
         $signInMsg = "BEGIN\r\n" . $querString . "\r\nEND\r\nBEGIN SIGNATURE\r\n" . $encoded . "END SIGNATURE\r\n";
         $response = $this->get_query_result($signInMsg, $url);
         $lines = preg_split('/\n|\r\n?/', $response);
         $error = explode('=', trim($lines[4]));
         if (strpos($response, 'ERROR=0') == true) {
         $payment = "https://in.cyberplat.com/cgi-bin/instp/instp_pay.cgi";
         $response = $this->get_query_result($signInMsg, $payment);
         $Split_auth = explode('AUTHCODE=', $response);
         $OPT_Code = explode('=', $Split_auth[0]);
         return $data =  urldecode($OPT_Code[7]);
         }
         }

         function add_beneficiary ($parameter_cyber){
         $remId = $parameter_cyber[0];
         $fName = $parameter_cyber[1];
         $lName = $parameter_cyber[2];
         $NUMBER = $parameter_cyber[3];
         $benAccount = $parameter_cyber[4];
         $benIFSC = $parameter_cyber[5];
         /* define('CERT', $this->CERT);
         define('CP_PASSWORD', $this->PASSWORD);  */
         $url = "https://in.cyberplat.com/cgi-bin/instp/instp_pay_check.cgi";
			$SD = 347382;
            $AP = 348816;
            $OP = 348817;
         $secKey = file_get_contents("private.key");
         $passwd = $this->PASSWORD;
         $serverCert = file_get_contents("mycert.pem");
         $sessPrefix = rand(100, 300);
         $sess = $sessPrefix . $NUMBER . time();
         $sess = substr($sess, -20);
         $amount = "1";
         $querString = "CERT=" . $this->CERT . "\r\nSESSION=$sess\r\nSD=$SD\r\nAP=$AP\r\nOP=$OP\r\nremId=$remId\r\nfName=$fName\r\nAMOUNT_ALL=$amount\r\nNUMBER=$NUMBER\r\nbenAccount=$benAccount\r\nbenIFSC=$benIFSC\r\nAMOUNT=$amount\r\nlName=$lName\r\nType=4";
        // echo $querString; exit();
         $pkeyid = openssl_pkey_get_private($secKey, $passwd);
         openssl_sign($querString, $signature, $pkeyid, OPENSSL_ALGO_SHA1);
         openssl_free_key($pkeyid);
         $encoded = base64_encode($signature);
         $encoded = chunk_split($encoded, 76, "\r\n");

         $errCode = null;
         $rsCode = null;
         $signInMsg = "BEGIN\r\n" . $querString . "\r\nEND\r\nBEGIN SIGNATURE\r\n" . $encoded . "END SIGNATURE\r\n";
         $response = $this->get_query_result($signInMsg, $url);
         $lines = preg_split('/\n|\r\n?/', $response);
         $error = explode('=', trim($lines[4]));
         if (strpos($response, 'ERROR=0') == true) {
         $payment = "https://in.cyberplat.com/cgi-bin/instp/instp_pay.cgi";
         $response = $this->get_query_result($signInMsg, $payment);
         $Split_auth = explode('AUTHCODE=', $response);
         $OPT_Code = explode('=', $Split_auth[0]);
         $data =  urldecode($OPT_Code[4]);
         $data =  explode('DATE', $data);
         return $data[0];
         }
         }

         function beneconform_resend_otp ($parameter_cyber){
         $remId = $parameter_cyber[0];
         $NUMBER = $parameter_cyber[1];
         $fName = $parameter_cyber[2];
         $lName = $parameter_cyber[3];
         $Pin = $parameter_cyber[4];
         $benAccount = $parameter_cyber[5];
         $benIFSC = $parameter_cyber[6];
		 $benId = $parameter_cyber[7];
		 /* define('CERT', $this->CERT);
         define('CP_PASSWORD', $this->PASSWORD);  */
         $url = "https://in.cyberplat.com/cgi-bin/instp/instp_pay_check.cgi";
			$SD = 347382;
            $AP = 348816;
            $OP = 348817;
         $secKey = file_get_contents("private.key");
         $passwd = $this->PASSWORD;
         $serverCert = file_get_contents("mycert.pem");
         $sessPrefix = rand(100, 300);
         $sess = $sessPrefix . $NUMBER . time();
         $sess = substr($sess, -20);
         $AMOUNT = "1.00";
         $fName = urlencode($fName);
         $remId = $remId;
         $NUMBER = $NUMBER;
         $fName = $fName;
         $lName = $lName;
         $Pin = $Pin;
         $benAccount = $benAccount;
         $benIFSC = $benIFSC;
         $querString = "CERT=" . $this->CERT . "\r\nSESSION=$sess\r\nSD=$SD\r\nAP=$AP\r\nOP=$OP\r\nremId=$remId\r\nNUMBER=$NUMBER\r\nfName=$fName\r\nlName=$lName\r\nPin=$Pin\r\nbenAccount=$benAccount\r\nbenIFSC=$benIFSC\r\nbenId=$benId\r\nAMOUNT=$AMOUNT\r\nAMOUNT_ALL=$AMOUNT\r\nType=4";
       		 
         $pkeyid = openssl_pkey_get_private($secKey, $passwd);
         openssl_sign($querString, $signature, $pkeyid, OPENSSL_ALGO_SHA1);
         openssl_free_key($pkeyid);
         $encoded = base64_encode($signature);
         $encoded = chunk_split($encoded, 76, "\r\n");

         $errCode = null;
         $rsCode = null;
         $signInMsg = "BEGIN\r\n" . $querString . "\r\nEND\r\nBEGIN SIGNATURE\r\n" . $encoded . "END SIGNATURE\r\n";
         $response = $this->get_query_result($signInMsg, $url);
         $lines = preg_split('/\n|\r\n?/', $response);
         $error = explode('=', trim($lines[4]));
         if (strpos($response, 'ERROR=0') == true) {
         $payment = "https://in.cyberplat.com/cgi-bin/instp/instp_pay.cgi";
         $response = $this->get_query_result($signInMsg, $payment);
         $Split_auth = explode('AUTHCODE=', $response);
         $OPT_Code = explode('=', $Split_auth[0]);
         $data =  urldecode($OPT_Code[4]);
         $data =  explode('DATE', $data);
		 return $data[0];
         }
        }


         function bene_confirm ($parameter_cyber){
         $remId = $parameter_cyber[0];
         $benId = $parameter_cyber[1];
         $otc = $parameter_cyber[2];
        // define('CERT', $this->CERT);
         //define('CP_PASSWORD', $this->PASSWORD); 
         $url = "https://in.cyberplat.com/cgi-bin/instp/instp_pay_check.cgi";
			$SD = 347382;
            $AP = 348816;
            $OP = 348817;
         $secKey = file_get_contents("private.key");
         $passwd = $this->PASSWORD;
         $serverCert = file_get_contents("mycert.pem");
         $sessPrefix = rand(100, 300);
         $sess = $sessPrefix . $remId . time();
         $sess = substr($sess, -20);
         $AMOUNT = "1.00";
         $querString = "CERT=" . $this->CERT . "\r\nSESSION=$sess\r\nSD=$SD\r\nAP=$AP\r\nOP=$OP\r\nremId=$remId\r\nbenId=$benId\r\notc=$otc\r\nAMOUNT=$AMOUNT\r\nAMOUNT_ALL=$AMOUNT\r\nType=2";
        // echo $querString; exit();
         $pkeyid = openssl_pkey_get_private($secKey, $passwd);
         openssl_sign($querString, $signature, $pkeyid, OPENSSL_ALGO_SHA1);
         openssl_free_key($pkeyid);
         $encoded = base64_encode($signature);
         $encoded = chunk_split($encoded, 76, "\r\n");

         $errCode = null;
         $rsCode = null;
         $signInMsg = "BEGIN\r\n" . $querString . "\r\nEND\r\nBEGIN SIGNATURE\r\n" . $encoded . "END SIGNATURE\r\n";
         $response = $this->get_query_result($signInMsg, $url);
         $lines = preg_split('/\n|\r\n?/', $response);
         $error = explode('=', trim($lines[4]));
         if (strpos($response, 'ERROR=0') == true) {
         $payment = "https://in.cyberplat.com/cgi-bin/instp/instp_pay.cgi";
         $response = $this->get_query_result($signInMsg, $payment);
         $Split_auth = explode('AUTHCODE=', $response);
         $OPT_Code = explode('=', $Split_auth[0]);
         $exload = explode('RESULT', $OPT_Code[2]);
         $myerror = $exload[0];
         if ($myerror == 24) {
         return '{"statuscode":"BNF","status":"error from server side","data":""}';
         }else{
         $data =  urldecode($OPT_Code[4]);
         $data =  explode('DATE', $data);
         return $data[0];
         }
         
         }
         }


         function delete_beneficiary ($parameter_cyber){
         $remId = $parameter_cyber[0];
         $benId = $parameter_cyber[1];
        //define('CERT', $this->CERT);
        // define('CP_PASSWORD', $this->PASSWORD); 
         $url = "https://in.cyberplat.com/cgi-bin/instp/instp_pay_check.cgi";
			$SD = 347382;
            $AP = 348816;
            $OP = 348817;
         $secKey = file_get_contents("private.key");
         $passwd = $this->PASSWORD;
         $serverCert = file_get_contents("mycert.pem");
         $sessPrefix = rand(100, 300);
         $sess = $sessPrefix . $remId . time();
         $sess = substr($sess, -20);
         $AMOUNT = "1.00";
         $querString = "CERT=" . $this->CERT . "\r\nSESSION=$sess\r\nSD=$SD\r\nAP=$AP\r\nOP=$OP\r\nremId=$remId\r\nbenId=$benId\r\nAMOUNT=$AMOUNT\r\nAMOUNT_ALL=$AMOUNT\r\nType=6";
         $pkeyid = openssl_pkey_get_private($secKey, $passwd);
         openssl_sign($querString, $signature, $pkeyid, OPENSSL_ALGO_SHA1);
         openssl_free_key($pkeyid);
         $encoded = base64_encode($signature);
         $encoded = chunk_split($encoded, 76, "\r\n");

         $errCode = null;
         $rsCode = null;
         $signInMsg = "BEGIN\r\n" . $querString . "\r\nEND\r\nBEGIN SIGNATURE\r\n" . $encoded . "END SIGNATURE\r\n";
         $response = $this->get_query_result($signInMsg, $url);
         $lines = preg_split('/\n|\r\n?/', $response);
         $error = explode('=', trim($lines[4]));
         if (strpos($response, 'ERROR=0') == true) {
         $payment = "https://in.cyberplat.com/cgi-bin/instp/instp_pay.cgi";
         $response = $this->get_query_result($signInMsg, $payment);
         $Split_auth = explode('AUTHCODE=', $response);
         $OPT_Code = explode('=', $Split_auth[0]);
         $exload = explode('RESULT', $OPT_Code[2]);
         $myerror = $exload[0];
         if ($myerror == 0) {
         $data =  urldecode($OPT_Code[4]);
         $data =  explode('DATE', $data);
         return $data[0];
         }else{
         return '{"statuscode":"BNF","status":"Bene Not Found","data":""}';
         }
         
         }
         }

         function bene_confirm_delete ($parameter_cyber){
         $remId = $parameter_cyber[0];
         $benId = $parameter_cyber[1];
         $otc = $parameter_cyber[2];
         /* define('CERT', $this->CERT);
         define('CP_PASSWORD', $this->PASSWORD);  */
         $url = "https://in.cyberplat.com/cgi-bin/instp/instp_pay_check.cgi";
			$SD = 347382;
            $AP = 348816;
            $OP = 348817;
         $secKey = file_get_contents("private.key");
         $passwd = $this->PASSWORD;
         $serverCert = file_get_contents("mycert.pem");
         $sessPrefix = rand(100, 300);
         $sess = $sessPrefix . $remId . time();
         $sess = substr($sess, -20);
         $AMOUNT = "1";
         $querString = "CERT=" . $this->CERT . "\r\nSESSION=$sess\r\nSD=$SD\r\nAP=$AP\r\nOP=$OP\r\notc=$otc\r\nremId=$remId\r\nbenId=$benId\r\nAMOUNT=$AMOUNT\r\nAMOUNT_ALL=$AMOUNT\r\nType=23";
         $pkeyid = openssl_pkey_get_private($secKey, $passwd);
         openssl_sign($querString, $signature, $pkeyid, OPENSSL_ALGO_SHA1);
         openssl_free_key($pkeyid);
         $encoded = base64_encode($signature);
         $encoded = chunk_split($encoded, 76, "\r\n");

         $errCode = null;
         $rsCode = null;
         $signInMsg = "BEGIN\r\n" . $querString . "\r\nEND\r\nBEGIN SIGNATURE\r\n" . $encoded . "END SIGNATURE\r\n";
         $response = $this->get_query_result($signInMsg, $url);
         $lines = preg_split('/\n|\r\n?/', $response);
         $error = explode('=', trim($lines[4]));
         if (strpos($response, 'ERROR=0') == true) {
         $payment = "https://in.cyberplat.com/cgi-bin/instp/instp_pay.cgi";
         $response = $this->get_query_result($signInMsg, $payment);
         $Split_auth = explode('AUTHCODE=', $response);
         $OPT_Code = explode('=', $Split_auth[0]);
         $exload = explode('RESULT', $OPT_Code[2]);
         $myerror = $exload[0];
         if ($myerror == 24) {
         return '{"statuscode":"BNF","status":"Wrong Otp","data":""}';
         }elseif($myerror == 224){
			 return '{"statuscode":"BNF","status":"Wrong Otp","data":""}';
		 }else{
         
		 $data =  urldecode($OPT_Code[4]);
         $data =  explode('DATE', $data);
		 
		 
         return $data[0];
         }
         
         }
         }
         function transaction ($NUMBER, $routingType, $benId, $AMOUNT, $insert_id){
         /* define('CERT', $this->CERT);
         define('CP_PASSWORD', $this->PASSWORD);  */
         $url = "https://in.cyberplat.com/cgi-bin/instp/instp_pay_check.cgi";
			$SD = 347382;
            $AP = 348816;
            $OP = 348817;
         $secKey = file_get_contents("private.key");
         $passwd = $this->PASSWORD;
         $serverCert = file_get_contents("mycert.pem");
         $sess = $insert_id;
         $AMOUNT_ALL = $AMOUNT + 5; 
         $querString = "CERT=" . $this->CERT . "\r\nSESSION=$sess\r\nSD=$SD\r\nAP=$AP\r\nOP=$OP\r\nNUMBER=$NUMBER\r\nroutingType=$routingType\r\nAMOUNT=$AMOUNT\r\nAMOUNT_ALL=$AMOUNT\r\nbenId=$benId\r\nType=3";
         $pkeyid = openssl_pkey_get_private($secKey, $passwd);
         openssl_sign($querString, $signature, $pkeyid, OPENSSL_ALGO_SHA1);
         openssl_free_key($pkeyid);
         $encoded = base64_encode($signature);
         $encoded = chunk_split($encoded, 76, "\r\n");

         $errCode = null;
         $rsCode = null;
         $signInMsg = "BEGIN\r\n" . $querString . "\r\nEND\r\nBEGIN SIGNATURE\r\n" . $encoded . "END SIGNATURE\r\n";
		 $resp = Apiresponse::create(['api_id' => 4,'api_type'=>'Cyber Check Api', 'report_id' => $insert_id,'request_message'=>$signInMsg]);
         $response = $this->get_query_result($signInMsg, $url);
		 $resp->message = $response;
		 $resp->save();
         $lines = preg_split('/\n|\r\n?/', $response);
         $error = explode('=', trim($lines[4]));
         if (strpos($response, 'ERROR=0') == true) {
         $payment = "https://in.cyberplat.com/cgi-bin/instp/instp_pay.cgi";
         $response = $this->get_query_result($signInMsg, $payment);
         Apiresponse::create(['message' => $response, 'api_id' => 4,'api_type'=>'Cyber Payment','report_id' => $insert_id]);
         $Split_auth = explode('AUTHCODE=', $response);
         $OPT_Code = explode('=', $Split_auth[0]);
         $exload = explode('RESULT', $OPT_Code[4]);
         $myerror = $exload[0];
         if ($myerror == 224) {
         return '{"statuscode":"ERR","status":"Error From Server Side","data":""}';
         }else{
          return urldecode($OPT_Code[7]);
         }
         }else{
         return '{"statuscode":"ERR","status":"Error From Server Side","data":""}';
         }
         }
		function getCyberBalance ()
		{
			$url = "https://in.cyberplat.com/cgi-bin/mts_espp/mtspay_rest.cgi";
			$SD = 347382;
            $AP = 348816;
            $OP = 348817;
			$secKey = file_get_contents("private.key");
			$passwd = $this->PASSWORD;
			$serverCert = file_get_contents("mycert.pem");
			$sess = time();

			$querString = "CERT=" . $this->CERT . "\r\nSESSION=$sess\r\nSD=$SD\r\nAP=$AP\r\nOP=$OP";
			$pkeyid = openssl_pkey_get_private($secKey, $passwd);
			openssl_sign($querString, $signature, $pkeyid, OPENSSL_ALGO_SHA1);
			openssl_free_key($pkeyid);
			$encoded = base64_encode($signature);
			$encoded = chunk_split($encoded, 76, "\r\n");

			$errCode = null;
			$rsCode = null;
			$signInMsg = "BEGIN\r\n" . $querString . "\r\nEND\r\nBEGIN SIGNATURE\r\n" . $encoded . "END SIGNATURE\r\n";
			$response = $this->get_query_result($signInMsg, $url);
			//return $response;
			$lines = preg_split('/\n|\r\n?/', $response);
			$error = explode('=', trim($lines[4]));
			if (strpos($response, 'ERROR=0') == true) 
			{
				try
				{
					$Split_auth = explode('REST=', $response);
					$OPT_Code = explode('END', $Split_auth[1]);
					$balance = str_replace("\r\n","",$OPT_Code[0]);
					return $balance;
				}
				catch(\Exception $e)
				{
					return "Server is busy";
				}
			}
			else
			  return "Server is busy";
		}
		function checkStatus($recordId)
	{
		$url = "https://in.cyberplat.com/cgi-bin/status/get_status.cgi";
		$SD = 347382;
		$AP = 348816;
		$OP = 348817;
		$secKey = file_get_contents("private.key");
		$passwd = $this->PASSWORD;
		$serverCert = file_get_contents("mycert.pem");
		$sess = $recordId;
		$querString = "CERT=" . $this->CERT . "\r\nSESSION=$sess\r\nSD=$SD\r\nAP=$AP\r\nOP=$OP";
		$pkeyid = openssl_pkey_get_private($secKey, $passwd);
		openssl_sign($querString, $signature, $pkeyid, OPENSSL_ALGO_SHA1);
		openssl_free_key($pkeyid);
		$encoded = base64_encode($signature);
		$encoded = chunk_split($encoded, 76, "\r\n");
		$errCode = null;
		$rsCode = null;
		$signInMsg = "BEGIN\r\n" . $querString . "\r\nEND\r\nBEGIN SIGNATURE\r\n" . $encoded . "END SIGNATURE\r\n";
		$response = $this->get_query_result($signInMsg, $url);
		$lines = preg_split('/\n|\r\n?/', $response);
		$error = explode('=', trim($lines[4]));
		if (strpos($response, 'ERROR=0') == true) 
		{
			$result = explode('=', trim($lines[5]));
			if($result[1]==7)
			return response()->json(['status'=>1,'msg'=>"Success"]);
			elseif($result[1]==3)
			return response()->json(['status'=>3,'msg'=>"Pending"]);
			elseif($result[1]==1)
			return response()->json(['status'=>2,'msg'=>"Failed"]);
		}else{
			return response()->json(['status'=>2,'msg'=>"try again"]);
		}
	}
		



    }

}