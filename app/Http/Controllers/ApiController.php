<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Api;
use Validator;
use Response;
use App\Http\Requests;
use Auth;
use App\Traits\CustomTraits;
use App\Http\Controllers\Controller;

class ApiController extends Controller
{
    use CustomTraits;
    public function store(Request $request)
    {
        $rules = array('api_name' => 'required',
            'username' => 'required',
            'api_url' => 'required'
        );
        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return Response::json(array(
                'success' => false,
                'errors' => $validator->getMessageBag()->toArray()

            )); // 400 being the HTTP code for an invalid request.
//            $messages = $validator->messages();
//            return json_encode($messages);
            // redirect our user back to the form with the errors from the validator
        } else {
            return Api::create($request->all());


        }

    }

    public function view(Request $request){
        $id = $request->input('id');
        $provider = Api::find($id);
        return $provider;
    }

    public function update(Request $request, $id)
    {
        $provider = Api::find($id);
        $provider->api_name = $request->api_name;
        $provider->api_url = $request->api_url;
        $provider->username = $request->username;
        $provider->password = $request->password;
        $provider->api_key = $request->api_key;
        $provider->save();
        return $provider;
    }
	public function api_balance(Request $request)
    {
		if(Auth::user()->role_id == 1)
		{
			
			if($request->getBalanceOf =="TRMO")
				return  $this->getTramoBalance();
			elseif($request->getBalanceOf =="CYBER")
				return  $this->getCyberBalance();
			elseif($request->getBalanceOf =="XP")
				return  $this->getXpBalance();
			elseif($request->getBalanceOf =="DIGITAL")
				return $this->getDigitalBalance();
			else
			{
				$redPayBalance = $this->getRedPayBalance();
				$tramoBalance = $this->getTramoBalance();
				$data = $this->getCyberBalance();
				$xpBalance = $this->getXpBalance();
				$digitalBalance = $this->getDigitalBalance();
			}
			return view('admin.api_balance',compact('data','tramoBalance','xpBalance','digitalBalance','redPayBalance'));
		}
		return view('errors.permission-denied');
    }
	private function getTramoBalance()
	{
		$content='api_token='.config('constants.TRAMO_API_KEY').'&userId='.config('constants.TRAMO_USER_ID');
		$url = config('constants.TRAMO_DMT_URL') ."/check-balance?".$content;//die;
		$tramoBalance = $this->getCurlGetMethod($url);
		$balance = json_decode($tramoBalance);
		return  number_format($balance->message->balance,2);
	}
	private function getCyberBalance()
	{
		$cyber = new \App\Library\CyberDMT;
		return $cyber->getCyberBalance();
	}
	private function getXpBalance()
	{
		$xpUrl = config('constants.XP_RECH_URL')."?MobileNo=".config('constants.XP_MOBILE_NUMBER')."&APIKey=".config('constants.XP_API_KEY')."&REQTYPE=BAL&RESPTYPE=JSON";
		 $curl = curl_init();
		curl_setopt($curl, CURLOPT_URL, $xpUrl);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
		$response = curl_exec($curl);
		curl_close($curl);
		$xpResp = json_decode($response);
		$xpBalance='';
		if($xpResp->STATUSCODE == 0)
			return number_format($xpResp->BALANCE,2);
	}
	
	private function getDigitalBalance()
	{
		$xpUrl = config('constants.DIGITAL_RECH_URL')."/GetBalance?LapuID=".config('constants.DIGITAL_USER');
		 $curl = curl_init();
		curl_setopt($curl, CURLOPT_URL, $xpUrl);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
		$response = curl_exec($curl);
		curl_close($curl);
		$doc = new \DOMDocument();
		$doc->loadXML($response);
		$Balance = $doc->getElementsByTagName("Balance")->item(0)->nodeValue;
			return number_format($Balance,2);
	}
	private function getIndoOneBalance()
	{
		
		$indiaOne = config('constants.INDO_ONE_URL')."?mobileno=".config('constants.INDO_USER')."&password=".config('constants.INDO_PSWD')."&message=BAL&Tranref=".time();
		$response =$this->getRechargeCurl($indiaOne);
		$xpResp = json_decode($response);
		$xpBalance='';
		if($xpResp->STATUSCODE == 0)
			return number_format($xpResp->BALANCE,2);
	}
	//REDPAY_RECH_URL
	private function getRedPayBalance()
	{
		$redPay = config('constants.REDPAY_RECH_URL')."/Balance_check?token=".config('constants.REDPAY_APITOKEN');
		$response =$this->getRechargeCurl($redPay);
		try{
			$redPayResp = json_decode($response);
			return number_format($redPayResp->balance,2);
		}
		catch(Exception $e){
			return "Error";
		}
	}
	private function getRechargeCurl($url)
	{
		$curl = curl_init();
		curl_setopt($curl, CURLOPT_URL, $url);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
		$response = curl_exec($curl);
		curl_close($curl);
		return $response;
	}
}
