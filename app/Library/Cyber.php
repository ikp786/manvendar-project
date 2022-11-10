<?php

namespace App\library {
use	DB;
use App\Apiresponse;
use Auth;
use Carbon\Carbon;
    class cyber
    {

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

        public function getURLByOperator($operator_code)
        {
			
            switch ($operator_code) {
                 case 1:
                    return $this->airtel_url(); 
                case 2://mts
                    return $this->vodafone_url();
                case 3://doco
                    return $this->idea_url();
                case 4:
                    return $this->tataindicom_url();
                case 5://idea
                    return $this->docomo_url();
                case 6:
                    return $this->uninor_url();
                case 7:
                    return $this->mtnl_url();
                case 8:
                    return $this->bsnl_url();
                case 9:
                    return $this->aircel_url();
                case 10:
                    return $this->videocon_url();
               // case 11:
                   // return $this->mts_url();
                case 12:
                    return $this->dishtv_url();
                case 13:
                    return $this->tataskytv_url();
                case 14:
                    return $this->suntv_url();
                case 15:
                    return $this->videocontv_url();
                case 16:
                    return $this->bigtv_url();
                case 17:
                    return $this->airtel_dth_url();
                case 18:
                    return $this->mts_url();
                case 19:
                    return $this->reliance_datacard_url(); 
                case 20:
                    return $this->reliance_url();
                 case 21:
                    return $this->docomo_url();
                case 22:
                    return $this->docomo_url(); 
                case 24:
                    return $this->postpaid_idea_url();
                case 23:
                    return $this->postpaid_airtel_url();
                case 25:
                    return $this->postpaid_vodafone_url();
                case 26:
                    return $this->postpaid_reliance_url();
                case 27:
                    return $this->postpaid_reliance_url();
                case 28:
                    return $this->postpaid_docomo_url();
                case 0:
                    return $this->postpaid_aircel_url();
                case 30:
                    return $this->postpaid_docomo_url();
                case 31:
                    return $this->mdl_url();
                case 32:
                    return $this->bl_url();
                case 33:
                    return $this->al_url();
                case 34:
                    return $this->bsesy_url();
                /*case 35:wrong code
                    return $this->bses_url();*/
                case 36:
                    return $this->ndpl_url();
                case 63:
                    return $this->ndpl_url();
                case 39:
                    return $this->reliance_url();
                case 40:
                    return $this->reliance_url();
                case 41:
                    return $this->reliance_url();
                case 42:
                    return $this->bsnl_validity_url();
                case 43:
                    return $this->docomo_special_url();
                case 44:
                    return $this->uninor_special_url();
                case 46:
                    return $this->videocon_special_url();
                case 47:
                    return $this->mtnl_validity_url();
                case 58:
                    return $this->postpaid_tikona_url();
                case 82:
                    return $this->tataindicom_url();
                case 84:
                    return $this->postpaid_tata_url();
                case 87:
                    return $this->bescom_url();
                case 73:
                    return $this->postpaid_bsnl_url();
                case 50:
                    return $this->cg_electricity();
                case 52:
                    return $this->torrent_power();
                case 93:
                    return $this->APSPDCL();
                case 94:
                    return $this->TSSPDCL();
                case 83:
                    return $this->CESC();
                case 80:
                    return $this->GGCL();
                case 81:
                    return $this->IGL();
                case 96:
                    return $this->MPMKVVC();
                case 97:
                    return $this->RVVNL();
                case 98:
                    return $this->MSEDC();
                case 99:
                    return $this->IPCL();
                case 100:
                    return $this->JUSCL();
                case 101:
                    return $this->NPCL();
                case 103:
                    return $this->MPI();
                case 104:
                    return $this->BMESTU();
                case 112:
                    return $this->reliance_jio_url();
				case 214:
					return $this->airtel_datacard_url();
				case 215:
					return $this->aircel_datacard_url();	
				case 216:
					return $this->idea_datacard_url();
				case 217:
					return $this->bsnl_datacard_url();
				case 218:
					return $this->vodafone_datacard_url();
				case 221:
					return $this->hathway_retail_url();
				case 222:
					return $this->hathway_online_url();
				case 220:
					return $this->broadband_online_url();		
				case 219:
					return $this->broadband_retail_url();
				case 223:
					return $this->broadband_tikona_url();
				case 225:
                    return $this->tatasky_b2c_url();
                case 234:
                    return $this->docomo_url();//docomo and docomo special both same
                default:
                    return FALSE;
            }
        }

		public function CallRecharge($operator, $session_id, $number, $amount, $user_id, $account, $cycle){
            define('CERT', '79A59006B3CCEEB64A72E74D55DB6D8AF7FE4DC0');
            define('CP_PASSWORD', 'a2z@123');
            $ALL_URL = $this->getURLByOperator($operator);
            if ($ALL_URL['isSpecial'] == 'true') {
                $isSpecial = 'true';
            } else {
                $isSpecial = 'false';
            }
            $id = $session_id;
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
            $querString = "CERT=" . CERT . "\r\nSESSION=$id\r\nSD=$SD\r\nAP=$AP\r\nOP=$OP\r\nNUMBER=$number\r\nAMOUNT=$amount\r\nAMOUNT_ALL=$amount\r\nCOMMENT=Test recharge";
            if (!empty($optional1)) {
                $querString = "CERT=" . CERT . "\r\nSESSION=$id\r\nSD=$SD\r\nAP=$AP\r\nOP=$OP\r\nNUMBER=$number\r\nAMOUNT=$amount\r\nAMOUNT_ALL=$amount\r\nCOMMENT=Test recharge";
            }
			
			
            $pkeyid = openssl_pkey_get_private($secKey, $passwd);
            openssl_sign($querString, $signature, $pkeyid, OPENSSL_ALGO_SHA1);
            openssl_free_key($pkeyid);

            $encoded = base64_encode($signature);
            $encoded = chunk_split($encoded, 76, "\r\n");

            $errCode = null;
            $rsCode = null;

            $signInMsg = "BEGIN\r\n" . $querString . "\r\nEND\r\nBEGIN SIGNATURE\r\n" . $encoded . "END SIGNATURE\r\n";
			$apiResp = Apiresponse::create(['report_id'=>$id,'api_type'=>"CYBER_RECHARGE",'api_id'=>2,'request_message'=>$signInMsg,'user_id'=>Auth::id()]);
            $response = $this->get_query_result($signInMsg, $verification);
			$apiResp->message=$response;
			$apiResp->stage="VERIFY";
			$apiResp->save();
            $lines = preg_split('/\n|\r\n?/', $response);
            $error = explode('=', trim($lines[4]));
            if (strpos($response, 'ERROR=0') == true) 
			{
                $response = $this->get_query_result($signInMsg, $payment);
				$apiResp->stage="VERIFY-PAYMENT";
				$apiResp->message=$response;
				$apiResp->save();
                if (strpos($response, 'ERROR=0') == false) {
                    return array('status' => 2, 'txnid' => 2, 'message' => '', 'response' => $response);
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

        function check_bill ($provider, $number){
        echo $provider; exit();
        define('CERT', '79A59006B3CCEEB64A72E74D55DB6D8AF7FE4DC0');
        define('CP_PASSWORD', 'a2z@123');
        $ALL_URL = $this->getURLByOperator($provider);

        if ($ALL_URL['isSpecial'] == 'true') {
        $isSpecial = 'true';
        } else {
        $isSpecial = 'false';
        }
        $id =  Carbon::now()->timestamp;
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
		
        public function CallRecharges($operator, $session_id, $number, $amount, $user_id, $account, $cycle)
        {
			
            define('CERT', '79A59006B3CCEEB64A72E74D55DB6D8AF7FE4DC0');
            define('CP_PASSWORD', 'a2z@123');
            $ALL_URL = $this->getURLByOperator($operator);
            if ($ALL_URL['isSpecial'] == 'true') {
                $isSpecial = 'true';
            } else {
                $isSpecial = 'false';
            }
            $id = $session_id;
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
            $querString = "CERT=" . CERT . "\r\nSESSION=$sess\r\nSD=$SD\r\nAP=$AP\r\nOP=$OP\r\nNUMBER=$number\r\nAMOUNT=$amount\r\nAMOUNT_ALL=$amount\r\nCOMMENT=Test recharge";
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
            Apiresponse::create(['message' => $response, 'api_type' => 150,'report_id'=>$session_id,'request_message'=>$signInMsg]);
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
		public function broadband_tikona_url()
		{
			return array('verification' =>
			'https://in.cyberplat.com/cgi-bin/tk/tk_pay_check.cgi/261',
				'payment' => 'https://in.cyberplat.com/cgi-bin/tk/tk_pay.cgi/261',
				'status' => 'https://in.cyberplat.com/cgi-bin/tk/tk_pay_status.cgi',
				'isSpecial' => 'false');
			
		}
		public function hathway_online_url()
		{
			return array('verification' =>
			'https://in.cyberplat.com/cgi-bin/bu/bu_pay_check.cgi/535',
				'payment' => 'https://in.cyberplat.com/cgi-bin/bu/bu_pay.cgi/535',
				'status' => 'https://in.cyberplat.com/cgi-bin/bu/bu_pay_status.cgi',
				'isSpecial' => 'false');
		}
		public function hathway_retail_url()
		{
			return array('verification' =>
			'https://in.cyberplat.com/cgi-bin/bu/bu_pay_check.cgi/534',
				'payment' => 'https://in.cyberplat.com/cgi-bin/bu/bu_pay.cgi/534',
				'status' => 'https://in.cyberplat.com/cgi-bin/bu/bu_pay_status.cgi',
				'isSpecial' => 'false');
		}
		public function broadband_online_url()
		{
			return array('verification' =>
			'https://in.cyberplat.com/cgi-bin/bu/bu_pay_check.cgi/525',
				'payment' => 'https://in.cyberplat.com/cgi-bin/bu/bu_pay.cgi/525',
				'status' => 'https://in.cyberplat.com/cgi-bin/bu/bu_pay_status.cgi',
				'isSpecial' => 'false');
		}
		public function broadband_retail_url()
		{
			return array('verification' =>
			'https://in.cyberplat.com/cgi-bin/bu/bu_pay_check.cgi/479',
				'payment' => 'https://in.cyberplat.com/cgi-bin/bu/bu_pay.cgi/479',
				'status' => 'https://in.cyberplat.com/cgi-bin/bu/bu_pay_status.cgi',
				'isSpecial' => 'false');
		}
        public function aircel_url()
        {
            return array('verification' => 'https://in.cyberplat.com/cgi-bin/ac/ac_pay_check.cgi/1',
                'payment' => 'https://in.cyberplat.com/cgi-bin/ac/ac_pay.cgi/1',
                'status' => 'https://in.cyberplat.com/cgi-bin/ac/ac_pay_status.cgi',
                'isSpecial' => 'false');
        }

        public function airtel_url()
        {
            return array('verification' => 'https://in.cyberplat.com/cgi-bin/at/at_pay_check.cgi',
                'payment' => 'https://in.cyberplat.com/cgi-bin/at/at_pay.cgi',
                'status' => 'https://in.cyberplat.com/cgi-bin/at/at_pay_status.cgi',
                'isSpecial' => 'false');
        }

        public function airtel_dth_url()
        {
            return array('verification' => 'https://in.cyberplat.com/cgi-bin/ad/ad_pay_check.cgi',
                'payment' => 'https://in.cyberplat.com/cgi-bin/ad/ad_pay.cgi',
                'status' => 'https://in.cyberplat.com/cgi-bin/ad/ad_pay_status.cgi',
                'isSpecial' => 'false'
            );
        }

        public function bigtv_dth_url()
        {
            return array('verification' => 'https://in.cyberplat.com/cgi-bin/bt/bt_pay_check.cgi',
                'payment' => 'https://in.cyberplat.com/cgi-bin/bt/bt_pay.cgi',
                'status' => 'https://in.cyberplat.com/cgi-bin/bt/bt_pay_status.cgi',
                'isSpecial' => 'false'
            );
        }

        public function idea_url()
        {
            return array('verification' => 'https://in.cyberplat.com/cgi-bin/id/id_pay_check.cgi',
                'payment' => 'https://in.cyberplat.com/cgi-bin/id/id_pay.cgi',
                'status' => 'https://in.cyberplat.com/cgi-bin/id/id_pay_status.cgi',
                'isSpecial' => 'false'
            );
        }

        public function reliance_url()
        {
            return array('verification' => 'https://in.cyberplat.com/cgi-bin/rl/rl_pay_check.cgi',
                'payment' => 'https://in.cyberplat.com/cgi-bin/rl/rl_pay.cgi',
                'status' => 'https://in.cyberplat.com/cgi-bin/rl/rl_pay_status.cgi',
                'isSpecial' => 'false'
            );
        }

         public function reliance_jio_url()
        {
            return array('verification' => 'https://in.cyberplat.com/cgi-bin/rjio/rjio_pay_check.cgi',
                'payment' => 'https://in.cyberplat.com/cgi-bin/rjio/rjio_pay.cgi',
                'status' => 'https://in.cyberplat.com/cgi-bin/rjio/rjio_pay_status.cgi',
                'isSpecial' => 'false'
            );
        }

        public function loopmobile_url()
        {
            return array('verification' => 'https://in.cyberplat.com/cgi-bin/lm/lm_pay_check.cgi',
                'payment' => 'https://in.cyberplat.com/cgi-bin/lm/lm_pay.cgi',
                'status' => 'https://in.cyberplat.com/cgi-bin/lm/lm_pay_status.cgi',
                'isSpecial' => 'false'
            );
        }

         /*datacard*/
        public function mts_url()
        {

            return array('verification' => 'https://in.cyberplat.com/cgi-bin/mt/mt_pay_check.cgi',
                'payment' => 'https://in.cyberplat.com/cgi-bin/mt/mt_pay.cgi',
                'status' => 'https://in.cyberplat.com/cgi-bin/mt/mt_pay_status.cgi',
                'isSpecial' => 'false'
            );
        }
        public function reliance_datacard_url()
        {
            return array('verification' => 'https://in.cyberplat.com/cgi-bin/rl/rl_pay_check.cgi',
                'payment' => 'https://in.cyberplat.com/cgi-bin/rl/rl_pay.cgi',
                'status' => 'https://in.cyberplat.com/cgi-bin/rl/rl_pay_status.cgi',
                'isSpecial' => 'false'
            );

        }
		public function tataphoton_datacard_url()
        {
            return array('verification' => 'https://in.cyberplat.com/cgi-bin/dc/dc_pay_check.cgi',
                'payment' => 'https://in.cyberplat.com/cgi-bin/dc/dc_pay.cgi',
                'status' => 'https://in.cyberplat.com/cgi-bin/dc/dc_pay_status.cgi',
                'isSpecial' => 'false'
            );

        }
        public function tataindicom_datacard_url()
        {
             return array('verification' => 'https://in.cyberplat.com/cgi-bin/tt/tt_pay_check.cgi',
                'payment' =>'https://in.cyberplat.com/cgi-bin/tt/tt_pay.cgi',
                'status' =>'https://in.cyberplat.com/cgi-bin/tt/tt_pay_status.cgi',
                'isSpecial' => 'false'
            );

        }
		 public function airtel_datacard_url()
        {
            return array('verification' => 'https://in.cyberplat.com/cgi-bin/at/at_pay_check.cgi',
                'payment' =>'https://in.cyberplat.com/cgi-bin/at/at_pay.cgi',
                'status' =>'https://in.cyberplat.com/cgi-bin/at/at_pay_status.cgi',
                'isSpecial' => 'false'
            );
        }
        public function aircel_datacard_url()
        {
            return array('verification' => 'https://in.cyberplat.com/cgi-bin/ac/ac_pay_check.cgi/1',
                'payment' =>'https://in.cyberplat.com/cgi-bin/ac/ac_pay.cgi/1',
                'status' =>'https://in.cyberplat.com/cgi-bin/ac/ac_pay_status.cgi',
                'isSpecial' => 'false'
            );
        }
        public function idea_datacard_url()
        {
            return array('verification' => 'https://in.cyberplat.com/cgi-bin/id/id_pay_check.cgi',
                'payment' =>'https://in.cyberplat.com/cgi-bin/id/id_pay.cgi',
                'status' =>'https://in.cyberplat.com/cgi-bin/id/id_pay_status.cgi',
                'isSpecial' => 'false'
            );
        }
        public function bsnl_datacard_url()
        {
            return array('verification' => 'https://in.cyberplat.com/cgi-bin/mm/mm_pay_check.cgi/219',
                'payment' =>'https://in.cyberplat.com/cgi-bin/mm/mm_pay.cgi/219',
                'status' =>'https://in.cyberplat.com/cgi-bin/mm/mm_pay_status.cgi',
                'isSpecial' => 'false'
            );
        }
        public function vodafone_datacard_url()
        {
            return array('verification' => 'https://in.cyberplat.com/cgi-bin/vd/vd_pay_check.cgi',
                'payment' =>'https://in.cyberplat.com/cgi-bin/vd/vd_pay.cgi',
                'status' =>'https://in.cyberplat.com/cgi-bin/vd/vd_pay_status.cgi',
                'isSpecial' => 'false'
            ); 
        }
       /*enddatacard*/

        public function tataindicom_url()
        {
            return array('verification' => 'https://in.cyberplat.com/cgi-bin/tt/tt_pay_check.cgi',
                'payment' => 'https://in.cyberplat.com/cgi-bin/tt/tt_pay.cgi',
                'status' => 'https://in.cyberplat.com/cgi-bin/tt/tt_pay_status.cgi',
                'isSpecial' => 'false'
            );
        }

        public function docomo_url()
        {
            return array('verification' => 'https://in.cyberplat.com/cgi-bin/dc/dc_pay_check.cgi',
                'payment' => 'https://in.cyberplat.com/cgi-bin/dc/dc_pay.cgi',
                'status' => 'https://in.cyberplat.com/cgi-bin/dc/dc_pay_status.cgi',
                'isSpecial' => 'false'
            );
        }

       

        public function bsnl_url()
        {
            return array('verification' => 'https://in.cyberplat.com/cgi-bin/mm/mm_pay_check.cgi/205',
                'payment' => 'https://in.cyberplat.com/cgi-bin/mm/mm_pay.cgi/205',
                'status' => 'https://in.cyberplat.com/cgi-bin/mm/mm_pay_status.cgi',
                'isSpecial' => 'false'
            );
        }

        public function bsnl_validity_url()
        {
            return array('verification' => 'https://in.cyberplat.com/cgi-bin/mm/mm_pay_check.cgi/219',
                'payment' => 'https://in.cyberplat.com/cgi-bin/mm/mm_pay.cgi/219',
                'status' => 'https://in.cyberplat.com/cgi-bin/mm/mm_pay_status.cgi',
                'isSpecial' => 'false'
            );
        }

        public function vodafone_url()
        {
            return array('verification' => 'https://in.cyberplat.com/cgi-bin/vd/vd_pay_check.cgi',
                'payment' => 'https://in.cyberplat.com/cgi-bin/vd/vd_pay.cgi',
                'status' => 'https://in.cyberplat.com/cgi-bin/vd/vd_pay_status.cgi',
                'isSpecial' => 'false'
            );
        }

        public function uninor_url()
        {
            return array('verification' => 'https://in.cyberplat.com/cgi-bin/un/un_pay_check.cgi',
                'payment' => 'https://in.cyberplat.com/cgi-bin/un/un_pay.cgi',
                'status' => 'https://in.cyberplat.com/cgi-bin/un/un_pay_status.cgi',
                'isSpecial' => 'false'
            );
        }

        public function uninor_special_url()
        {
            return array('verification' => 'https://in.cyberplat.com/cgi-bin/un/un_pay_check.cgi',
                'payment' => 'https://in.cyberplat.com/cgi-bin/un/un_pay.cgi',
                'status' => 'https://in.cyberplat.com/cgi-bin/un/un_pay_status.cgi',
                'isSpecial' => 'true'
            );
        }

        public function mtnl_url()
        {
            return array('verification' => 'https://in.cyberplat.com/cgi-bin/mm/mm_pay_check.cgi/212',
                'payment' => 'https://in.cyberplat.com/cgi-bin/mm/mm_pay.cgi/212',
                'status' => 'https://in.cyberplat.com/cgi-bin/mm/mm_pay_status.cgi',
                'isSpecial' => 'false'
            );
        }

        public function mtnl_validity_url()
        {
            return array('verification' => 'https://in.cyberplat.com/cgi-bin/mm/mm_pay_check.cgi/215',
                'payment' => 'https://in.cyberplat.com/cgi-bin/mm/mm_pay.cgi/215',
                'status' => 'https://in.cyberplat.com/cgi-bin/mm/mm_pay_status.cgi',
                'isSpecial' => 'true'
            );
        }

        public function virgin_cdma_url()
        {
            return array('verification' => 'https://in.cyberplat.com/cgi-bin/tt/tt_pay_check.cgi',
                'payment' => 'https://in.cyberplat.com/cgi-bin/tt/tt_pay.cgi',
                'status' => 'https://in.cyberplat.com/cgi-bin/tt/tt_pay_status.cgi',
                'isSpecial' => 'false'
            );
        }

        public function videocon_url()
        {
            return array('verification' => 'https://in.cyberplat.com/cgi-bin/vm/vm_pay_check.cgi',
                'payment' => 'https://in.cyberplat.com/cgi-bin/vm/vm_pay.cgi',
                'status' => 'https://in.cyberplat.com/cgi-bin/vm/vm_pay_status.cgi',
                'isSpecial' => 'false'
            );
        }

        public function videocon_special_url()
        {
            return array('verification' => 'https://in.cyberplat.com/cgi-bin/vm/vm_pay_check.cgi',
                'payment' => 'https://in.cyberplat.com/cgi-bin/vm/vm_pay.cgi',
                'status' => 'https://in.cyberplat.com/cgi-bin/vm/vm_pay_status.cgi',
                'isSpecial' => 'true'
            );
        }
		public function docomo_datacard_url()
		{
			return array('verification' =>
			'https://in.cyberplat.com/cgi-bin/dc/dc_pay_check.cgi',
			'payment' => 'https://in.cyberplat.com/cgi-bin/dc/dc_pay.cgi',
			'status' => 'https://in.cyberplat.com/cgi-bin/dc/dc_pay_status.cgi',
			'isSpecial' => 'true'
			);
		}
        public function bigtv_url()
        {
            return array('verification' => 'https://in.cyberplat.com/cgi-bin/bt/bt_pay_check.cgi',
                'payment' => 'https://in.cyberplat.com/cgi-bin/bt/bt_pay.cgi',
                'status' => 'https://in.cyberplat.com/cgi-bin/bt/bt_pay_status.cgi',
                'isSpecial' => 'false'
            );
        }

        public function dishtv_url()
        {
            return array('verification' => 'https://in.cyberplat.com/cgi-bin/dt/dt_pay_check.cgi',
                'payment' => 'https://in.cyberplat.com/cgi-bin/dt/dt_pay.cgi',
                'status' => 'https://in.cyberplat.com/cgi-bin/dt/dt_pay_status.cgi',
                'isSpecial' => 'false'
            );
        }

        public function suntv_url()
        {
            return array('verification' => 'https://in.cyberplat.com/cgi-bin/mm/mm_pay_check.cgi/213',
                'payment' => 'https://in.cyberplat.com/cgi-bin/mm/mm_pay.cgi/213',
                'status' => 'https://in.cyberplat.com/cgi-bin/mm/mm_pay_status.cgi',
                'isSpecial' => 'false'
            );
        }

        public function videocontv_url()
        {
            return array('verification' => 'https://in.cyberplat.com/cgi-bin/vc/vc_pay_check.cgi',
                'payment' => 'https://in.cyberplat.com/cgi-bin/vc/vc_pay.cgi',
                'status' => 'https://in.cyberplat.com/cgi-bin/vc/vc_pay_status.cgi',
                'isSpecial' => 'false'
            );
        }

        public function tataskytv_url()
        {
            return array('verification' => 'https://in.cyberplat.com/cgi-bin/ts/ts_pay_check.cgi',
                'payment' => 'https://in.cyberplat.com/cgi-bin/ts/ts_pay.cgi',
                'status' => 'https://in.cyberplat.com/cgi-bin/ts/ts_pay_status.cgi',
                'isSpecial' => 'false'
            );
        }
		public function tatasky_b2c_url()
        {
            return array('verification' => 'https://in.cyberplat.com/cgi-bin/ts/ts_pay_check.cgi/t=249',
                'payment' => 'https://in.cyberplat.com/cgi-bin/ts/ts_pay.cgi/t=249',
                'status' => 'https://in.cyberplat.com/cgi-bin/ts/ts_pay_status.cgi',
                'isSpecial' => 'false'
            );
        }

        //Postpaid Bill
        public function postpaid_airtel_url()
        {
            return array('verification' => 'https://in.cyberplat.com/cgi-bin/ad/ad_pay_check.cgi/225',
                'payment' => 'https://in.cyberplat.com/cgi-bin/ad/ad_pay.cgi/225',
                'status' => 'https://in.cyberplat.com/cgi-bin/ad/ad_pay_status.cgi/225',
                'isSpecial' => 'false'
            );
        }

        public function postpaid_aircel_url()
        {
            return array('verification' => 'https://in.cyberplat.com/cgi-bin/bu/bu_pay_check.cgi/288',
                'payment' => 'https://in.cyberplat.com/cgi-bin/bu/bu_pay.cgi/288',
                'status' => 'https://in.cyberplat.com/cgi-bin/bu/bu_pay_status.cgi',
                'isSpecial' => 'false'
            );
        }

        public function postpaid_idea_url()
        {
            return array('verification' => 'https://in.cyberplat.com/cgi-bin/bu/bu_pay_check.cgi/232',
                'payment' => 'https://in.cyberplat.com/cgi-bin/bu/bu_pay.cgi/232',
                'status' => 'https://in.cyberplat.com/cgi-bin/bu/bu_pay_status.cgi',
                'isSpecial' => 'false'
            );
        }

        public function postpaid_docomo_url()
        {
            return array('verification' => 'https://in.cyberplat.com/cgi-bin/bu/bu_pay_check.cgi/228',
                'payment' => 'https://in.cyberplat.com/cgi-bin/bu/bu_pay.cgi/228',
                'status' => 'https://in.cyberplat.com/cgi-bin/bu/bu_pay_status.cgi',
                'isSpecial' => 'false'
            );
        }

        public function postpaid_bsnl_url()
        {
            return array('verification' => 'https://in.cyberplat.com/cgi-bin/bu/bu_pay_check.cgi/231',
                'payment' => 'https://in.cyberplat.com/cgi-bin/bu/bu_pay.cgi/231',
                'status' => 'https://in.cyberplat.com/cgi-bin/bu/bu_pay_status.cgi',
                'isSpecial' => 'false'
            );
        }

        public function postpaid_loop_url()
        {
            return array('verification' => 'https://in.cyberplat.com/cgi-bin/bu/bu_pay_check.cgi/230',
                'payment' => 'https://in.cyberplat.com/cgi-bin/bu/bu_pay.cgi/230',
                'status' => 'https://in.cyberplat.com/cgi-bin/bu/bu_pay_status.cgi',
                'isSpecial' => 'false'
            );
        }

        public function postpaid_tata_url()
        {
            return array('verification' => 'https://in.cyberplat.com/cgi-bin/bu/bu_pay_check.cgi/233',
                'payment' => 'https://in.cyberplat.com/cgi-bin/bu/bu_pay.cgi/233',
                'status' => 'https://in.cyberplat.com/cgi-bin/bu/bu_pay_status.cgi',
                'isSpecial' => 'false'
            );
        }

        public function postpaid_vodafone_url()
        {
            return array('verification' => 'https://in.cyberplat.com/cgi-bin/bu/bu_pay_check.cgi/234',
                'payment' => 'https://in.cyberplat.com/cgi-bin/bu/bu_pay.cgi/234',
                'status' => 'https://in.cyberplat.com/cgi-bin/bu/bu_pay_status.cgi',
                'isSpecial' => 'false'
            );
        }

        public function postpaid_reliance_url()
        {
            return array('verification' => 'https://in.cyberplat.com/cgi-bin/rl/rl_pay_check.cgi/251',
                'payment' => 'https://in.cyberplat.com/cgi-bin/rl/rl_pay.cgi/251',
                'status' => 'https://in.cyberplat.com/cgi-bin/rl/rl_pay_status.cgi',
                'isSpecial' => 'false'
            );
        }

        public function postpaid_reliance_energy_url()
        {
            return array('verification' => 'https://in.cyberplat.com/cgi-bin/bu/bu_pay_check.cgi/232',
                'payment' => 'https://in.cyberplat.com/cgi-bin/bu/bu_pay.cgi/232',
                'status' => 'https://in.cyberplat.com/cgi-bin/bu/bu_pay_status.cgi',
                'isSpecial' => 'false'
            );
        }

        public function postpaid_mtnl_delhi_url()
        {
            return array('verification' => 'https://in.cyberplat.com/cgi-bin/bu/bu_pay_check.cgi/232',
                'payment' => 'https://in.cyberplat.com/cgi-bin/bu/bu_pay.cgi/232',
                'status' => 'https://in.cyberplat.com/cgi-bin/bu/bu_pay_status.cgi',
                'isSpecial' => 'false'
            );
        }

        public function postpaid_mahanagar_gas_url()
        {
            return array('verification' => 'https://in.cyberplat.com/cgi-bin/bu/bu_pay_check.cgi/232',
                'payment' => 'https://in.cyberplat.com/cgi-bin/bu/bu_pay.cgi/232',
                'status' => 'https://in.cyberplat.com/cgi-bin/bu/bu_pay_status.cgi',
                'isSpecial' => 'false'
            );
        }

        public function postpaid_tata_aig_url()
        {
            return array('verification' => 'https://in.cyberplat.com/cgi-bin/bu/bu_pay_check.cgi/232',
                'payment' => 'https://in.cyberplat.com/cgi-bin/bu/bu_pay.cgi/232',
                'status' => 'https://in.cyberplat.com/cgi-bin/bu/bu_pay_status.cgi',
                'isSpecial' => 'false'
            );
        }

        public function postpaid_icici_pru_url()
        {
            return array('verification' => 'https://in.cyberplat.com/cgi-bin/bu/bu_pay_check.cgi/232',
                'payment' => 'https://in.cyberplat.com/cgi-bin/bu/bu_pay.cgi/232',
                'status' => 'https://in.cyberplat.com/cgi-bin/bu/bu_pay_status.cgi',
                'isSpecial' => 'false'
            );
        }

        public function postpaid_tikona_url()
        {
            return array('verification' => 'https://in.cyberplat.com/cgi-bin/bu/bu_pay_check.cgi/232',
                'payment' => 'https://in.cyberplat.com/cgi-bin/bu/bu_pay.cgi/232',
                'status' => 'https://in.cyberplat.com/cgi-bin/bu/bu_pay_status.cgi',
                'isSpecial' => 'false'
            );
        }

        public function bses_url()
        {
            return array('verification' => 'https://in.cyberplat.com/cgi-bin/bu/bu_pay_check.cgi/236',
                'payment' => 'https://in.cyberplat.com/cgi-bin/bu/bu_pay.cgi/236',
                'status' => 'https://in.cyberplat.com/cgi-bin/bu/bu_pay_status.cgi',
                'isSpecial' => 'false'
            );
        }

        public function bsesy_url()
        {
            return array('verification' => 'https://in.cyberplat.com/cgi-bin/bu/bu_pay_check.cgi/237',
                'payment' => 'https://in.cyberplat.com/cgi-bin/bu/bu_pay.cgi/237',
                'status' => 'https://in.cyberplat.com/cgi-bin/bu/bu_pay_status.cgi',
                'isSpecial' => 'false'
            );
        }

        public function ndpl_url()
        {
            return array('verification' => 'https://in.cyberplat.com/cgi-bin/bu/bu_pay_check.cgi/238',
                'payment' => 'https://in.cyberplat.com/cgi-bin/bu/bu_pay.cgi/238',
                'status' => 'https://in.cyberplat.com/cgi-bin/bu/bu_pay_status.cgi',
                'isSpecial' => 'false'
            );
        }

        public function mdl_url()
        {
            return array('verification' => 'https://in.cyberplat.com/cgi-bin/bu/bu_pay_check.cgi/240',
                'payment' => 'https://in.cyberplat.com/cgi-bin/bu/bu_pay.cgi/240',
                'status' => 'https://in.cyberplat.com/cgi-bin/bu/bu_pay_status.cgi',
                'isSpecial' => 'false'
            );
        }

        public function bl_url()
        {
            return array('verification' => 'https://in.cyberplat.com/cgi-bin/bu/bu_pay_check.cgi/344',
                'payment' => 'https://in.cyberplat.com/cgi-bin/bu/bu_pay.cgi/344',
                'status' => 'https://in.cyberplat.com/cgi-bin/bu/bu_pay_status.cgi',
                'isSpecial' => 'false'
            );
        }

        public function al_url()
        {
            return array('verification' => 'https://in.cyberplat.com/cgi-bin/ad/ad_pay_check.cgi/225',
                'payment' => 'https://in.cyberplat.com/cgi-bin/ad/ad_pay.cgi/225',
                'status' => 'https://in.cyberplat.com/cgi-bin/ad/ad_pay_status.cgi/225',
                'isSpecial' => 'false'
            );
        }

        // Bill Payments
        public function cg_electricity()
        {
            return array('verification' => 'https://in.cyberplat.com/cgi-bin/bu/bu_pay_check.cgi/318',
                'payment' => 'https://in.cyberplat.com/cgi-bin/bu/bu_pay.cgi/318',
                'status' => 'https://in.cyberplat.com/cgi-bin/bu/bu_pay_status.cgi',
                'isSpecial' => 'false'
            );
        }

        public function APSPDCL()
        {
            return array('verification' => 'https://in.cyberplat.com/cgi-bin/bu/bu_pay_check.cgi/331',
                'payment' => 'https://in.cyberplat.com/cgi-bin/bu/bu_pay.cgi/331',
                'status' => 'https://in.cyberplat.com/cgi-bin/bu/bu_pay_status.cgi',
                'isSpecial' => 'false'
            );
        }

        public function TSSPDCL()
        {
            return array('verification' => 'https://in.cyberplat.com/cgi-bin/bu/bu_pay_check.cgi/312',
                'payment' => 'https://in.cyberplat.com/cgi-bin/bu/bu_pay.cgi/312',
                'status' => 'https://in.cyberplat.com/cgi-bin/bu/bu_pay_status.cgi',
                'isSpecial' => 'false'
            );
        }

        public function CESC()
        {
            return array('verification' => 'https://in.cyberplat.com/cgi-bin/bu/bu_pay_check.cgi/317',
                'payment' => 'https://in.cyberplat.com/cgi-bin/bu/bu_pay.cgi/317',
                'status' => 'https://in.cyberplat.com/cgi-bin/bu/bu_pay_status.cgi',
                'isSpecial' => 'false'
            );
        }

        public function GGCL()
        {
            return array('verification' => 'https://in.cyberplat.com/cgi-bin/bu/bu_pay_check.cgi/321',
                'payment' => 'https://in.cyberplat.com/cgi-bin/bu/bu_pay.cgi/321',
                'status' => 'https://in.cyberplat.com/cgi-bin/bu/bu_pay_status.cgi',
                'isSpecial' => 'false'
            );
        }

        public function IGL()
        {
            return array('verification' => 'https://in.cyberplat.com/cgi-bin/bu/bu_pay_check.cgi/310',
                'payment' => 'https://in.cyberplat.com/cgi-bin/bu/bu_pay.cgi/310',
                'status' => 'https://in.cyberplat.com/cgi-bin/bu/bu_pay_status.cgi',
                'isSpecial' => 'false'
            );
        }

        public function torrent_power()
        {
            return array('verification' => 'https://in.cyberplat.com/cgi-bin/bu/bu_pay_check.cgi/332',
                'payment' => 'https://in.cyberplat.com/cgi-bin/bu/bu_pay.cgi/332',
                'status' => 'https://in.cyberplat.com/cgi-bin/bu/bu_pay_status.cgi',
                'isSpecial' => 'false'
            );
        }

        public function bescom_url()
        {
            return array('verification' => 'https://in.cyberplat.com/cgi-bin/bu/bu_pay_check.cgi/315',
                'payment' => 'https://in.cyberplat.com/cgi-bin/bu/bu_pay.cgi/315',
                'status' => 'https://in.cyberplat.com/cgi-bin/bu/bu_pay_status.cgi',
                'isSpecial' => 'false'
            );
        }

        public function MPMKVVC()
        {
            return array('verification' => 'https://in.cyberplat.com/cgi-bin/bu/bu_pay_check.cgi/326',
                'payment' => 'https://in.cyberplat.com/cgi-bin/bu/bu_pay.cgi/326',
                'status' => 'https://in.cyberplat.com/cgi-bin/bu/bu_pay_status.cgi',
                'isSpecial' => 'false'
            );
        }

        public function RVVNL()
        {
            return array('verification' => 'https://in.cyberplat.com/cgi-bin/bu/bu_pay_check.cgi/330',
                'payment' => 'https://in.cyberplat.com/cgi-bin/bu/bu_pay.cgi/330',
                'status' => 'https://in.cyberplat.com/cgi-bin/bu/bu_pay_status.cgi',
                'isSpecial' => 'false'
            );
        }

        public function MSEDC()
        {
            return array('verification' => 'https://in.cyberplat.com/cgi-bin/bu/bu_pay_check.cgi/342',
                'payment' => 'https://in.cyberplat.com/cgi-bin/bu/bu_pay.cgi/342',
                'status' => 'https://in.cyberplat.com/cgi-bin/bu/bu_pay_status.cgi',
                'isSpecial' => 'false'
            );
        }

        public function IPCL()
        {
            return array('verification' => 'https://in.cyberplat.com/cgi-bin/bu/bu_pay_check.cgi/324',
                'payment' => 'https://in.cyberplat.com/cgi-bin/bu/bu_pay.cgi/324',
                'status' => 'https://in.cyberplat.com/cgi-bin/bu/bu_pay_status.cgi',
                'isSpecial' => 'false'
            );
        }

        public function JUSCL()
        {
            return array('verification' => 'https://in.cyberplat.com/cgi-bin/bu/bu_pay_check.cgi/325',
                'payment' => 'https://in.cyberplat.com/cgi-bin/bu/bu_pay.cgi/325',
                'status' => 'https://in.cyberplat.com/cgi-bin/bu/bu_pay_status.cgi',
                'isSpecial' => 'false'
            );
        }

        public function NPCL()
        {
            return array('verification' => 'https://in.cyberplat.com/cgi-bin/bu/bu_pay_check.cgi/335',
                'payment' => 'https://in.cyberplat.com/cgi-bin/bu/bu_pay.cgi/335',
                'status' => 'https://in.cyberplat.com/cgi-bin/bu/bu_pay_status.cgi',
                'isSpecial' => 'false'
            );
        }

        public function BMESTU()
        {
            return array('verification' => 'https://in.cyberplat.com/cgi-bin/bu/bu_pay_check.cgi/340',
                'payment' => 'https://in.cyberplat.com/cgi-bin/bu/bu_pay.cgi/340',
                'status' => 'https://in.cyberplat.com/cgi-bin/bu/bu_pay_status.cgi',
                'isSpecial' => 'false'
            );
        }

        public function MPI()
        {
            return array('verification' => 'https://in.cyberplat.com/cgi-bin/bu/bu_pay_check.cgi/326',
                'payment' => 'https://in.cyberplat.com/cgi-bin/bu/bu_pay.cgi/326',
                'status' => 'https://in.cyberplat.com/cgi-bin/bu/bu_pay_status.cgi',
                'isSpecial' => 'false'
            );
        }

    }

}