<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Masterbank;
use App\Yesmasterbank;
use App\Company;
use App\OfflineService;
use App\Companydesign;
use SimpleXMLElement;
use GuzzleHttp\Client;
use SoapClient;
use DOMDocument;
use App\User;
use App\Balance;
use App\Moneycommission;
use App\ImpsWalletScheme;
use App\PremiumWalletScheme;
use App\VerificationScheme;
use App\Report;
use App\Api;
use App\Beneficiary;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Response;
use DB;
use Exception;
use Validator;
use App\Gstcommission;
use App\Apiresponse;
use App\TransactionReportException;
use App\Traits\CustomTraits;
use App\Traits\ReportTraits;

class PaytmCallBackController extends Controller
{ 
	/*
	protected $request;

    public function __construct(Request $request) {
        $this->request = $request;
    } */
	public function index()
	{
	 
	}
	
	function PayTMResponseCallBack(Request $request)
	{    
	    echo " sdfsdfsf PayTMResponseCallBack"; die;
	    
	    // dd($request['response']);
	   // Log::info($request['response']);
	    try{
	        $apiResp = Apiresponse::create(['api_id'=>25,'user_id'=>'16','api_type'=>"PaytmCallBack",'report_id'=>'00','request_message'=>$request]);
	         
	        $username = urlencode("r604");
            $msg_token = urlencode("yhibBi");
            $sender_id = urlencode("ATZSVD"); // optional (compulsory in transactional sms)
            $message_content = "Dear Customer, Your Txn is successful to Acc No. 00011111 with Amount Rs 2 at PaytmCallBack Thanks";
            $message = rawurlencode($message_content);
            $mobile = urlencode('9619418206');
             
            $content=array('username'=> $username, 'msg_token'=> $msg_token,'sender_id'=>$sender_id,'message'=>$message_content,'mobile'=>$mobile);
        	$url = "http://manage.hivemsg.com/api/send_transactional_sms.php";
        	 
        	$api_response =  $this->getCurlPostMethod($url,$content);
    	
	    }catch(Exception $e){
			  
    	    $username = urlencode("r604");
            $msg_token = urlencode("yhibBi");
            $sender_id = urlencode("ATZSVD"); // optional (compulsory in transactional sms)
            $message_content = "Dear Customer, Your Txn is successful to Acc No. 00011111 with Amount Rs 2 at PaytmCallBackCatchCall Thanks";
            $message = rawurlencode($message_content);
            $mobile = urlencode('9619418206');
             
            $content=array('username'=> $username, 'msg_token'=> $msg_token,'sender_id'=>$sender_id,'message'=>$message_content,'mobile'=>$mobile);
        	$url = "http://manage.hivemsg.com/api/send_transactional_sms.php";
        	 
        	$api_response =  $this->getCurlPostMethod($url,$content);
        	Log::info($request['response']);
		}
	}
	
	
}
