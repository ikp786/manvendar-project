<?php

namespace App\Http\Controllers\Mobile;

use App\Http\Controllers\Controller;
use App\Traits\CustomAuthTraits;
use Validator;
use Illuminate\Http\Request;
use App\Provider;

class ROfferController extends Controller
{
    use CustomAuthTraits;

    var $baseUrlSpecialOffer = "http://live.vastwebindia.com/LiveRecharge";

    public function getSpecialNumberOffer(Request $request)
    {
        $rules=[
            'provider'=>'required',
            'mobile_number'=>'required',
            'token'=>'required',
            'userId'=>'required',
        ];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return Response::json(array(
                'status' => '10',
                'message' => 'missing/invalid params',
                'errors' => $validator->getMessageBag()->toArray()
            ));
        }
        $authentication = $this->checkLoginAuthentication($request);
        if($authentication['status'] !=1) return $authentication;


        $number = $request->mobile_number;
        $provider = $request->provider;

        if ($provider == 'VODAFONE') {
            $url = $this->baseUrlSpecialOffer . "/VodaRoffer?Token=" . config('constants.VAST_WEB_API_TOKEN') . "&RechargeNumber=" . $number . "&userid=" . config('constants.VAST_WEB_API_USER_ID');
        } else if ($provider == 'AIRTEL PREPAID') {
            $url = $this->baseUrlSpecialOffer . "/AirtelRoffer?Token=" . config('constants.VAST_WEB_API_TOKEN') . "&RechargeNumber=" . $number . "&userid=" . config('constants.VAST_WEB_API_USER_ID');
        } else if ($provider == 'IDEA') {
            $url = $this->baseUrlSpecialOffer . "/IdeaRoffer?Token=" . config('constants.VAST_WEB_API_TOKEN') . "&RechargeNumber=" . $number . "&userid=" . config('constants.VAST_WEB_API_USER_ID');
        } else if ($provider == 'AIRTEL DEGITAL TV') {
            $url = $this->baseUrlSpecialOffer . "/AirteldthRoffer?Token=" . config('constants.VAST_WEB_API_TOKEN') . "&RechargeNumber=" . $number . "&userid=" . config('constants.VAST_WEB_API_USER_ID');
        } else if ($provider == 'DISH TV') {
            $url = $this->baseUrlSpecialOffer . "/dishtvRoffer?Token=" . config('constants.VAST_WEB_API_TOKEN') . "&RechargeNumber=" . $number . "&userid=" . config('constants.VAST_WEB_API_USER_ID');
        } else if ($provider == 'VIDEOCON DTH') {
            $url = $this->baseUrlSpecialOffer . "/VideoD2hRoffer?Token=" . config('constants.VAST_WEB_API_TOKEN') . "&RechargeNumber=" . $number . "&userid=" . config('constants.VAST_WEB_API_USER_ID');
        }
        try {
            return $this->getCurlMethod($url);
        } catch (Exception $e) {
            return Response::json(array('status' => 'failed', 'message' => 'Please time after some time'));
        }
    }

    private function getCurlMethod($url)
    {
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
        ));
        $response = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);
        return $response;
    }

    public function getOffer(Request $request)
    {
        $provider = Provider::find($request->providerId);
        $r_offerCode = $provider->r_offer_code;
        $url = config('constants.R_OFFER_URL') . "/plans.php?apikey=" . config('constants.R_OFFER_KEY') . "&offer=roffer&tel=" . $request->mobileNumber . "&operator=" . $r_offerCode;
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $response = curl_exec($curl);
        curl_close($curl);
        //return 	'{"tel":"9999999999","operator":"Idea","records":[{"rs":"449","desc":"Rs.449-Unlimited Local+National+Roaming Calls+100 SMS\/D+114 GB @1.4GB\/D DATA-82 Days"},{"rs":"199","desc":"Rs.199-Unlimited Local+National+Roaming Calls+100SMS\/Day+39 GB @1.4GB\/DAY DATA-28 Days "},{"rs":"179","desc":"Rs.179-Unlimited Local+National+Roaming Calls+100SMS\/Day+1GB-28 Days "},{"rs":"120","desc":"Aapka Khaas Offer! Rs 120 ke Rchg pe 120 ka TT"},{"rs":"104","desc":"RC104-300 Local + STD Mobile Mins for 28 Days"},{"rs":"62","desc":"Rs.62-Upto 5GB-15D-"},{"rs":"45","desc":"Rs.45-1GB-28 Days-"},{"rs":"29","desc":"Rs.29-250MB-28 Days-"},{"rs":"0","desc":"Have Recharge karvu ekdam saral.MyIdea App par aakarshak Cashback offer ane discount coupons. Aajej Dial karo 12166"},{"rs":"0","desc":"Aap na number ne Aadhar sathe link karvo jaruri che. Najik na store ni mulakat lo athwa Dial karo toll free 14546"},{"rs":"0","desc":"Idea Ape che Validity addition suvidha jema aap Hal na UL Pack ni baki Validity nava UL ni validity ma Umerai jase."},{"rs":"0","desc":"Unlimited Song Change @2rs\/day"}],"status":1}';
        return $response;
    }

    public function getPrepaidCircleOffer(Request $request)
    {
        $provider = Provider::find($request->providerCode);
        $r_offerCode = $provider->r_offer_code;
        $url = config('constants.R_OFFER_URL') . "/plans.php?apikey=" . config('constants.R_OFFER_KEY') . "&cricle=Delhi NCR&tel=" . $request->mobileNumber . "&operator=" . $r_offerCode;
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $response = curl_exec($curl);
        curl_close($curl);
        return $response;
    }

    public function getDTHCustomerInfo(Request $request)
    {
        $provider = Provider::find($request->providerId);
        $r_offerCode = $provider->r_offer_code2;
        $url = config('constants.R_OFFER_URL') . "/Dthinfo.php?apikey=" . config('constants.R_OFFER_KEY') . "&offer=roffer&tel=" . $request->consumerNumber . "&operator=" . $r_offerCode;
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $response = curl_exec($curl);
        curl_close($curl);
        return $response;
    }

    public function getDTHPlans(Request $request)
    {
        $provider = Provider::find($request->providerCode);
        $dthPlanCode = urlencode($provider->r_offer_code);
        $url = config('constants.R_OFFER_URL') . "/dthplans.php?apikey=" . config('constants.R_OFFER_KEY') . "&operator=" . $dthPlanCode;
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $response = curl_exec($curl);
        curl_close($curl);
        return $response;
    }

    public function getDTHPlansWithChannel(Request $request)
    {

        $operator = $provider->operator;
        $url = config('constants.R_OFFER_URL') . "/dth_plans.phpa?pikey=" . config('constants.R_OFFER_KEY') . "&operator=" . $operator;
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $response = curl_exec($curl);
        curl_close($curl);
        return $response;
    }

    public function getDTHRoffer(Request $request)
    {

        $operator = $provider->operator;
        $url = config('constants.R_OFFER_URL') . "/DthRoffer.php?apikey=" . config('constants.R_OFFER_KEY') . "&operator=" . $operator;
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $response = curl_exec($curl);
        curl_close($curl);
        return $response;
    }

    public function getElectricityCustomerInformation(Request $request)
    {

        $operator = $provider->operator;
        $url = config('constants.R_OFFER_URL') . "/api/electricinfo.php?apikey=" . config('constants.R_OFFER_KEY') . "&offer=roffer&tel=" . $request->consumerNumber . "&operator=" . $operator;
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $response = curl_exec($curl);
        curl_close($curl);
        return $response;


    }

    public function getMobileOperator(Request $request)
    {

        $url = "http://operatorcheck.mplan.in/api/operatorinfo.php?apikey=" . config('constants.R_OFFER_KEY') . "&tel=" . $request->mobile_number;
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $response = curl_exec($curl);
        curl_close($curl);
        return $response;

    }
}
