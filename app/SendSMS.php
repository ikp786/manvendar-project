<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SendSMS extends Model
{
    //
	public static function sendsms($number, $message, $company_id)
    {
        $c_id = Company::where('id',$company_id)->first();
        $sms_auth = $c_id->smskey;
        $sms_sender = $c_id->sms_sender;
		$url = "https://control.msg91.com/api/sendhttp.php?authkey=$sms_auth&mobiles=$number&message=$message&sender=$sms_sender&route=4&country=91";
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $data = curl_exec($curl);
        curl_close($curl);
        return $data;
    }
}
