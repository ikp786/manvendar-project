<?php

namespace App\library {

    class Sms
    {
        public function is_ok($id)
        {
            return $id;
        }

        public function sendsms($number, $message)
        {
            $url = "http://www.sms21.co.in/sms/api?username=8527735551&password=12345&senderid=LEVINM&number='.$number.'&message='.$message";
            $curl = curl_init();
            curl_setopt($curl, CURLOPT_URL, $url);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
            $data = curl_exec($curl);
            curl_close($curl);
            return $data;
        }

        public function sendsms_two($number, $message, $sender_id)
        {
            $url = "https://control.msg91.com/api/sendhttp.php?authkey=43466ADvfq19mpb52b33cd2&mobiles=$number&message=$message&sender=$sender_id&route=4&country=91";
            $curl = curl_init();
            curl_setopt($curl, CURLOPT_URL, $url);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
            $data = curl_exec($curl);
            curl_close($curl);
            return $data;
        }

    }
}