<?php

namespace App\Http\Controllers\ApiForwarding;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Validator;
use Response;
use Auth;
use App\Traits\CustomTraits;
class ApiController extends Controller
{
	use CustomTraits;
    public function fetchBillFormat(Request $request)
	{
		$rules = array(
			'userId' => 'required|numeric|regex:/^[0-9]+$/',
			'specialKey' => 'required|regex:/^[0-9a-zA-Z]+$/',
		);
		$validator = Validator::make($request->all(), $rules);
		if ($validator->fails()){
		  return Response::json(['status'=>10,'message'=>$validator->errors()->getMessages()]);
		}
		$userdetail = Auth::guard('api')->user();
		if($userdetail->id != $request->userId)
			return response()->json(['status'=>10,'message'=>"User Id is invalid"]);
		//$content ="{\n\"token\":\"$token\",\n\"request\": {\n\"sp_key\": \"$sp_key\"}\n}";
		$content=array('token'=>config('constants.INSTANT_KEY'),'request'=>array('sp_key'=>$request->specialKey));
		//$content ="{\n\"token\":\"$token\",\n\"request\": {\n\"sp_key\": \"$sp_key\"}\n}";
		$url = config('constants.INSTANT_PAY_URL') ."/ws/userresources/bbps_biller";
		$agentid = time();
		return $this->getCurlPostIMethod($url,json_encode($content));
	}
	public function fetchBillDetails(Request $request)
	{
		$rules = array(
			'userId' => 'required|numeric|regex:/^[0-9]+$/',
			'specialKey' => 'required',
			'consumerNumber' => 'required',
			'numberOfParam' => 'required|numeric|regex:/^[1-3]+$/',
		);
		
		$numberOfParam=$request->numberOfParam;
		$customerNumber=$request->customerNumber;
		$validator = Validator::make($request->all(), $rules);
		if ($validator->fails()){
		  return Response::json(['status'=>10,'message'=>$validator->errors()->getMessages()]);
		}
		$userdetail = Auth::guard('api')->user();
		if($userdetail->id != $request->userId)
			return response()->json(['status'=>10,'message'=>"User Id is invalid"]);
		return $this->getCurlMethod($request->consumerNumber,$request->specialKey,$numberOfParam,$customerNumber);
	}
	
	private function getCurlMethod($consumerNumber,$sp_key,$numberOfParam=1,$customerNumer=null)
	{
		$token = config('constants.INSTANT_KEY');//die;
		$mobileNumber = 9829415434;//config('constants.INSTANT_MOB_NUMBER');//die;
		$outletId = 10746;//config('constants.INSTANT_OUTLET_ID');//die;
		$agentId = time();
		$customerMobileNumber= Auth::user()->mobile;
		$serIP = "182.18.157.156";
		$ip=$_SERVER['REMOTE_ADDR'];
		$location_data = (unserialize(file_get_contents('http://www.geoplugin.net/php.gp?ip='.$ip)));
		$latlog = "28.626640,77.384804";
		if($numberOfParam==2)
		{
			$content = "{\n\"token\": \"$token\",\n\"request\": {\n\"sp_key\":\"$sp_key\",\n\"agentid\":\"$agentId\",\n\"customer_mobile\":\"$mobileNumber\",\n    \"customer_params\": [\n\"$consumerNumber\",\n\"$customerNumer\"],\n\"init_channel\": \"AGT\",\n\"endpoint_ip\": \"$serIP\",\n\"mac\": \"AD-fg-12-78-GH\",\n\"payment_mode\":\"Cash\",\n\"payment_info\":\"bill\",\n    \"amount\": \"10\",\n\"reference_id\": \"\",\n\"latlong\": \"$latlog\",\n\"outletid\":\"$outletId\"\n}\n}"; 
		}
		else if($numberOfParam==3)
		{
			$content = "{\n\"token\": \"$token\",\n\"request\": {\n\"sp_key\":\"$sp_key\",\n\"agentid\":\"$agentId\",\n\"customer_mobile\":\"$mobileNumber\",\n    \"customer_params\": [\n\"$consumerNumber\",\n\"$customerNumer\"],\n\"init_channel\": \"AGT\",\n\"endpoint_ip\": \"$serIP\",\n\"mac\": \"AD-fg-12-78-GH\",\n\"payment_mode\":\"Cash\",\n\"payment_info\":\"bill\",\n    \"amount\": \"10\",\n\"reference_id\": \"\",\n\"latlong\": \"$latlog\",\n\"outletid\":\"$outletId\"\n}\n}"; 
		}
		else{
			$content = "{\n\"token\": \"$token\",\n\"request\": {\n\"sp_key\":\"$sp_key\",\n\"agentid\":\"$agentId\",\n\"customer_mobile\":\"$mobileNumber\",\n    \"customer_params\": [\n\"$consumerNumber\"],\n\"init_channel\": \"AGT\",\n\"endpoint_ip\": \"$serIP\",\n\"mac\": \"AD-fg-12-78-GH\",\n\"payment_mode\":\"Cash\",\n\"payment_info\":\"bill\",\n    \"amount\": \"10\",\n\"reference_id\": \"\",\n\"latlong\": \"$latlog\",\n\"outletid\":\"$outletId\"\n}\n}"; 
		}
		
		
		$url = config('constants.INSTANT_PAY_URL')."/ws/bbps/bill_fetch";
		return $this->getCurlPostIMethod($url,$content);
	}
	
	
	public function checkBankDownApi(Request $request)
	{
		$rules = array(
			'accountNumber' => 'required|numeric|regex:/^[0-9]+$/',
			'userId' => 'required|numeric|regex:/^[0-9]+$/',
			'isPerticularBank' => 'required|regex:/^[0-9]+$/',
		);
		$validator = Validator::make($request->all(), $rules);
		if ($validator->fails()){
		  return Response::json(['status'=>10,'message'=>$validator->errors()->getMessages()]);
		}
		$userdetail = Auth::guard('api')->user();
		if($userdetail->id != $request->userId)
			return response()->json(['status'=>10,'message'=>"User Id is invalid"]);
		$account = $request->accountNumber;
		if($request->isPerticularBank==1)
		{
			$bankShortName=$request->bankShortName;
			if($bankShortName!='')
				$content =array('account'=>$account,"outletid"=>1,"bank"=>$request->bankShortName);
			else
				return response()->json(['status'=>10,'message'=>"Bank Short Name is missing"]);
			$content =array('account'=>$account,"outletid"=>1,"bank"=>$bankShortName);
		}
		else
			$content =array('account'=>$account,"outletid"=>1);
		$instantPay = new \App\Library\InstantPayDMT; 
        return $apiResp = $instantPay->isBankdDownOrNot($content);
	}
}
