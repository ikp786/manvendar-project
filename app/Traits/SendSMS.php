<?php
namespace App\Traits;

trait SendSMS {
 
	public static function sendSMS($mobile,$message,$company_id=null,$senderType=null)
	{
		$senderType="PAYJST";
		
		if($senderType)
		{
			$url = "http://103.16.101.52:8080/bulksms/bulksms?username=".\Config::get('constants.SMS_USER_NAME')."&password=".\Config::get('constants.SMS_PASSWORD')."&type=0&dlr=1&destination=". $mobile ."&source=".$senderType."&message=".$message;
			$curl = curl_init();
			curl_setopt_array($curl, array(
			CURLOPT_URL =>$url,
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_ENCODING => "",
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_TIMEOUT => 1,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST => "GET",
			
			));
			$response = curl_exec($curl);
			$err = curl_error($curl);
			curl_close($curl);
		}
	}
		
 
}