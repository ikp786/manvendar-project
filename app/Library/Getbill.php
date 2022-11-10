<?php

namespace App\library {
use App\Apiresponse;
    class getbill
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
            define('CERT', 'BD56F8702D2D65876314E88B90A06095366CF788');
            define('CP_PASSWORD', 'Castrol@77');


            $check_url = "https://in.cyberplat.com/cgi-bin/mts_espp/mtspay_rest.cgi";
            $pay_url = "https://in.cyberplat.com/cgi-bin/id/id_pay.cgi";
            $verify_url = "https://in.cyberplat.com/cgi-bin/status/get_status.cgi";

            $SD = 347587;
            $AP = 347619;
            $OP = 347620;

            $phNbr = "7210500777";
            $amount = "10.00";

            $secKey = file_get_contents("client.key");
            $passwd = CP_PASSWORD;
            $serverCert = file_get_contents("client_cert.pem");

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
                case 2:
                    return $this->vodafone_url();
                case 3:
                    return $this->idea_url();
                case 4:
                    return $this->tataindicom_url();
                case 5:
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
                case 11:
                    return $this->mts_url();
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
                    return $this->mts_url();
                case 20:
                    return $this->reliance_url();
                case 21:
                    return $this->tataindicom_url();
                case 22:
                    return $this->tataindicom_url();
                case 23:
                    return $this->postpaid_airtel_url();
                case 24:
                    return $this->postpaid_idea_url();
                case 25:
                    return $this->postpaid_vodafone_url();
                case 26:
                    return $this->postpaid_reliance_url();
                case 27:
                    return $this->postpaid_reliance_url();
                case 28:
                    return $this->postpaid_docomo_url();
                case 29:
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
                case 35:
                    return $this->bses_url();
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
                case 85:
                    return $this->MGL();
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
                case 116:
                    return $this->southernpower();
                case 117:
                    return $this->bharatpurelectricity();
                case 118:
                    return $this->bikanerelectricity();
                case 119:
                    return $this->damandiuelectricity();
                case 120:
                    return $this->dakshingujarat();
                case 121:
                    return $this->dnhpower();
                case 122:
                    return $this->easternpower();
                case 123:
                    return $this->kotaelectricity();
                case 124:
                    return $this->meghalayapower();
                case 125:
                    return $this->madhyagujarat();
                case 126:
                    return $this->paschimgujarat();
                case 127:
                    return $this->tatapowermumbai();
                case 128:
                    return $this->uttargujarat();
                case 132:
                    return $this->haryanacitygas();
                case 133:
                    return $this->sitienergy();
                case 134:
                    return $this->tripuranatural();
                case 136:
                    return $this->uttarakhandPower();
                case 137:
                    return $this->MuzaffarpurVidyut();
                case 138:
                    return $this->UttarPradeshPower();
                case 139:
                    return $this->NorthBiharPower();
                case 140:
                    return $this->SouthBiharPower();
                case 141:
                    return $this->AssamPower();
                case 142:
                    return $this->UITBhiwadi();
                case 143:
                    return $this->UJSB2B();
                case 144:
                    return $this->UJSOB2C();
                 case 145:
                   return $this->DelhiJal();
                 case 146:
                   return $this->Municipal();
                default:
                    return FALSE;
            }
        }
    

        public function CallRecharge($operator, $session_id, $number, $amount, $user_id, $account, $cycle)
        {
            define('CERT', '79E90F95FFC291C4E79A76015266BCA75B00BB6E');
            define('CP_PASSWORD', 'vijay@123');
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
            $SD = 331055;
            $AP = 331312;
            $OP = 331313;

            $secKey = file_get_contents("client.key");
            $passwd = CP_PASSWORD;
            $serverCert = file_get_contents("client_cert.pem");

            $sessPrefix = rand(100, 300);
            $sess = $sessPrefix . $number . time();
            $sess = substr($sess, -20);
            $querString = "CERT=" . CERT . "\r\nSESSION=$sess\r\nSD=$SD\r\nAP=$AP\r\nOP=$OP\r\nNUMBER=$number\r\nAMOUNT=10\r\nAMOUNT_ALL=10\r\nCOMMENT=Test recharge";
            if (!empty($account)) {
                $querString = "CERT=" . CERT . "\r\nSESSION=$sess\r\nSD=$SD\r\nAP=$AP\r\nOP=$OP\r\nNUMBER=$number\r\nACCOUNT=$account\r\nAMOUNT=10\r\nAMOUNT_ALL=10\r\nAuthenticator3=$cycle\r\nCOMMENT=Test recharge";

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
             Apiresponse::create(['message' => $response, 'api_type' => 150,'report_id'=>$number,'request_message'=>$signInMsg]);
            $lines = preg_split('/\n|\r\n?/', $response);
            $error = explode('=', trim($lines[8]));
            $bill_error = explode('=', trim($lines[7]));
        if(trim($error[0]=='END'))
             {
            @$ercn = $bill_error[1];
         return array('status' => 2, 'txnid' => $ercn, 'message' => 'Sorry,Please try again after sometime.', 'response' => 'Billcheck', 'ref_id' => '');  
            }
         else
            {
                $bill_addinfo = explode('=', trim($lines[7]));
               
                @$erc = trim($error[1]); 
                if($erc)
                return array('status' => 2, 'txnid' => $erc, 'message' => $bill_addinfo[1], 'response' => 'Billcheck', 'ref_id' => '');
                else
                    return array('status' => 2, 'txnid' => 'Sorry,not able to get bill', 'message' => 'Sorry,not able to get bill', 'response' => 'Billcheck', 'ref_id' => '');

            }
                
               
                //echo $session_id . '#Failure#' . $erc.'#'.$balance;
            

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

        public function mts_url()
        {

            return array('verification' => 'https://in.cyberplat.com/cgi-bin/mt/mt_pay_check.cgi',
                'payment' => 'https://in.cyberplat.com/cgi-bin/mt/mt_pay.cgi',
                'status' => 'https://in.cyberplat.com/cgi-bin/mt/mt_pay_status.cgi',
                'isSpecial' => 'false'
            );
        }

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

        public function docomo_special_url()
        {
            return array('verification' => 'https://in.cyberplat.com/cgi-bin/dc/dc_pay_check.cgi',
                'payment' => 'https://in.cyberplat.com/cgi-bin/dc/dc_pay.cgi',
                'status' => 'https://in.cyberplat.com/cgi-bin/dc/dc_pay_status.cgi',
                'isSpecial' => 'true'
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
        public function southernpower()
        {
             return array('verification' => 'https://in.cyberplat.com/cgi-bin/bu/bu_pay_check.cgi/331',
                'payment' => 'https://in.cyberplat.com/cgi-bin/bu/bu_pay.cgi/331',
                'status' => 'https://in.cyberplat.com/cgi-bin/bu/bu_pay_status.cgi',
                'isSpecial' => 'false'
            );
        }

        public function bharatpurelectricity()
        {
             return array('verification' => 'https://in.cyberplat.com/cgi-bin/bu/bu_pay_check.cgi/476',
                'payment' => 'https://in.cyberplat.com/cgi-bin/bu/bu_pay.cgi/476',
                'status' => 'https://in.cyberplat.com/cgi-bin/status/get_status.cgi',
                'isSpecial' => 'false'
            );
        }

         public function bikanerelectricity()
        {
             return array('verification' => 'https://in.cyberplat.com/cgi-bin/bu/bu_pay_check.cgi/477',
                'payment' => 'https://in.cyberplat.com/cgi-bin/bu/bu_pay.cgi/477',
                'status' => 'https://in.cyberplat.com/cgi-bin/status/get_status.cgi',
                'isSpecial' => 'false'
            );
        }

         public function damandiuelectricity()
        {
             return array('verification' => 'https://in.cyberplat.com/cgi-bin/bu/bu_pay_check.cgi/480',
                'payment' => 'https://in.cyberplat.com/cgi-bin/bu/bu_pay.cgi/480',
                'status' => 'https://in.cyberplat.com/cgi-bin/status/get_status.cgi',
                'isSpecial' => 'false'
            );
        }

         public function  dakshingujarat()
        {
             return array('verification' => 'https://in.cyberplat.com/cgi-bin/bu/bu_pay_check.cgi/481',
                'payment' => 'https://in.cyberplat.com/cgi-bin/bu/bu_pay.cgi/481',
                'status' => 'https://in.cyberplat.com/cgi-bin/status/get_status.cgi',
                'isSpecial' => 'false'
            );
        }

         public function  dnhpower()
        {
             return array('verification' => 'https://in.cyberplat.com/cgi-bin/bu/bu_pay_check.cgi/482',
                'payment' => 'https://in.cyberplat.com/cgi-bin/bu/bu_pay.cgi/482',
                'status' => 'https://in.cyberplat.com/cgi-bin/status/get_status.cgi',
                'isSpecial' => 'false'
            );
        }
       
        public function  easternpower()
        {
             return array('verification' => 'https://in.cyberplat.com/cgi-bin/bu/bu_pay_check.cgi/483',
                'payment' => 'https://in.cyberplat.com/cgi-bin/bu/bu_pay.cgi/483',
                'status' => 'https://in.cyberplat.com/cgi-bin/status/get_status.cgi',
                'isSpecial' => 'false'
            );
        }

        public function  kotaelectricity ()
        {
             return array('verification' => 'https://in.cyberplat.com/cgi-bin/bu/bu_pay_check.cgi/485',
                'payment' => 'https://in.cyberplat.com/cgi-bin/bu/bu_pay.cgi/485',
                'status' => 'https://in.cyberplat.com/cgi-bin/status/get_status.cgi',
                'isSpecial' => 'false'
            );
        }

        public function  meghalayapower()
        {
             return array('verification' => 'https://in.cyberplat.com/cgi-bin/bu/bu_pay_check.cgi/486',
                'payment' => 'https://in.cyberplat.com/cgi-bin/bu/bu_pay.cgi/486',
                'status' => 'https://in.cyberplat.com/cgi-bin/status/get_status.cgi',
                'isSpecial' => 'false'
            );
        }

        public function  madhyagujarat()
        {
             return array('verification' => 'https://in.cyberplat.com/cgi-bin/bu/bu_pay_check.cgi/487',
                'payment' => 'https://in.cyberplat.com/cgi-bin/bu/bu_pay.cgi/487',
                'status' => 'https://in.cyberplat.com/cgi-bin/status/get_status.cgi',
                'isSpecial' => 'false'
            );
        }

         public function  paschimgujarat()
        {
             return array('verification' => 'https://in.cyberplat.com/cgi-bin/bu/bu_pay_check.cgi/488',
                'payment' => 'https://in.cyberplat.com/cgi-bin/bu/bu_pay.cgi/488',
                'status' => 'https://in.cyberplat.com/cgi-bin/status/get_status.cgi',
                'isSpecial' => 'false'
            );
        }

         public function  tatapowermumbai()
        {
             return array('verification' => 'https://in.cyberplat.com/cgi-bin/bu/bu_pay_check.cgi/491',
                'payment' => 'https://in.cyberplat.com/cgi-bin/bu/bu_pay.cgi/491',
                'status' => 'https://in.cyberplat.com/cgi-bin/status/get_status.cgi',
                'isSpecial' => 'false'
            );
        }
      
      public function  uttargujarat()
        {
             return array('verification' => 'https://in.cyberplat.com/cgi-bin/bu/bu_pay_check.cgi/492',
                'payment' => 'https://in.cyberplat.com/cgi-bin/bu/bu_pay.cgi/492',
                'status' => 'https://in.cyberplat.com/cgi-bin/status/get_status.cgi',
                'isSpecial' => 'false'
            );
        }
         public function  haryanacitygas()
        {
             return array('verification' => 'https://in.cyberplat.com/cgi-bin/bu/bu_pay_check.cgi/484',
                'payment' => 'https://in.cyberplat.com/cgi-bin/bu/bu_pay.cgi/484',
                'status' => 'https://in.cyberplat.com/cgi-bin/status/get_status.cgi',
                'isSpecial' => 'false'
            );
        }

          public function  sitienergy()
        {
             return array('verification' => 'https://in.cyberplat.com/cgi-bin/bu/bu_pay_check.cgi/489',
                'payment' => 'https://in.cyberplat.com/cgi-bin/bu/bu_pay.cgi/489',
                'status' => 'https://in.cyberplat.com/cgi-bin/status/get_status.cgi',
                'isSpecial' => 'false'
            );
        }
          public function  tripuranatural()
        {
             return array('verification' => 'https://in.cyberplat.com/cgi-bin/bu/bu_pay_check.cgi/490',
                'payment' => 'https://in.cyberplat.com/cgi-bin/bu/bu_pay.cgi/490',
                'status' => 'https://in.cyberplat.com/cgi-bin/status/get_status.cgi',
                'isSpecial' => 'false'
            );
        }

        public function uttarakhandPower()
        {
            return array('verification' => 'https://in.cyberplat.com/cgi-bin/bu/bu_pay_check.cgi/496',
                'payment' => 'https://in.cyberplat.com/cgi-bin/bu/bu_pay.cgi/496',
                'status' => 'https://in.cyberplat.com/cgi-bin/bu/bu_pay_status.cgi',
                'isSpecial' => 'false'
            );
        }
        public function MuzaffarpurVidyut()
        {
            return array('verification' => 'https://in.cyberplat.com/cgi-bin/bu/bu_pay_check.cgi/497',
                'payment' => 'https://in.cyberplat.com/cgi-bin/bu/bu_pay.cgi/497',
                'status' => 'https://in.cyberplat.com/cgi-bin/bu/bu_pay_status.cgi',
                'isSpecial' => 'false'
            );
        }

        public function UttarPradeshPower()
        {
            return array('verification' => 'https://in.cyberplat.com/cgi-bin/bu/bu_pay_check.cgi/499',
                'payment' => 'https://in.cyberplat.com/cgi-bin/bu/bu_pay.cgi/499',
                'status' => 'https://in.cyberplat.com/cgi-bin/bu/bu_pay_status.cgi',
                'isSpecial' => 'false'
            );
        }

        public function  NorthBiharPower()
        {
            return array('verification' => 'https://in.cyberplat.com/cgi-bin/bu/bu_pay_check.cgi/501',
                'payment' => 'https://in.cyberplat.com/cgi-bin/bu/bu_pay.cgi/501',
                'status' => 'https://in.cyberplat.com/cgi-bin/bu/bu_pay_status.cgi',
                'isSpecial' => 'false'
            );
        }

        public function  SouthBiharPower()
        {
            return array('verification' => 'https://in.cyberplat.com/cgi-bin/bu/bu_pay_check.cgi/502',
                'payment' => 'https://in.cyberplat.com/cgi-bin/bu/bu_pay.cgi/502',
                'status' => 'https://in.cyberplat.com/cgi-bin/bu/bu_pay_status.cgi',
                'isSpecial' => 'false'
            );
        }
        public function  AssamPower()
        {
            return array('verification' => 'https://in.cyberplat.com/cgi-bin/bu/bu_pay_check.cgi/313',
                'payment' => 'https://in.cyberplat.com/cgi-bin/bu/bu_pay.cgi/313',
                'status' => 'https://in.cyberplat.com/cgi-bin/bu/bu_pay_status.cgi',
                'isSpecial' => 'false'
            );
        }
        public function UITBhiwadi()
        {
             return array('verification' => 'https://in.cyberplat.com/cgi-bin/bu/bu_pay_check.cgi/493',
                'payment' => 'https://in.cyberplat.com/cgi-bin/bu/bu_pay.cgi/493',
                'status' => 'https://in.cyberplat.com/cgi-bin/bu/bu_pay_status.cgi',
                'isSpecial' => 'false'
            );
        }
        public function UJSB2B()
        {
             return array('verification' => 'https://in.cyberplat.com/cgi-bin/bu/bu_pay_check.cgi/507',
                'payment' => 'https://in.cyberplat.com/cgi-bin/bu/bu_pay.cgi/507',
                'status' => 'https://in.cyberplat.com/cgi-bin/bu/bu_pay_status.cg',
                'isSpecial' => 'false'
            );
        }
        public function UJSOB2C()
        {
             return array('verification' => 'https://in.cyberplat.com/cgi-bin/bu/bu_pay_check.cgi/508',
                'payment' => 'https://in.cyberplat.com/cgi-bin/bu/bu_pay.cgi/508',
                'status' => 'https://in.cyberplat.com/cgi-bin/bu/bu_pay_status.cgi',
                'isSpecial' => 'false'
            );
        }
        public function DelhiJal()
        {
             return array('verification' => 'https://in.cyberplat.com/cgi-bin/bu/bu_pay_check.cgi/494',
                'payment' => 'https://in.cyberplat.com/cgi-bin/bu/bu_pay.cgi/494',
                'status' => 'https://in.cyberplat.com/cgi-bin/bu/bu_pay_status.cgi',
                'isSpecial' => 'false'
            );
        }
         public function Municipal()
        {
             return array('verification' => 'https://in.cyberplat.com/cgi-bin/bu/bu_pay_check.cgi/498',
                'payment' => 'https://in.cyberplat.com/cgi-bin/bu/bu_pay.cgi/498',
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
        public function MGL()
        {
             return array('verification' => 'https://in.cyberplat.com/cgi-bin/bu/bu_pay_check.cgi/241',
                'payment' => 'https://in.cyberplat.com/cgi-bin/bu/bu_pay.cgi/241',
                'status' => 'https://in.cyberplat.com/cgi-bin/bu/bu_pay_status.cgi',
                'isSpecial' => 'false'
            );
        }

    }

}