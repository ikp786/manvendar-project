<?php

namespace App\Http\Controllers\Mobile;
use App\Traits\CustomAuthTraits;
use Illuminate\Http\Request;
use App\Api;
use Validator;
use Response;
use App\Http\Requests;
use App\Traits\CustomTraits;
use App\Http\Controllers\Controller;

class ApiController extends Controller
{
    use CustomTraits, CustomAuthTraits;

    public function api_balance(Request $request)
    {
        $rules = array(
            'userId' => 'required|numeric',
            'token' => 'required',
        );
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return Response::json(array(
                'status' => 10,
                'message' => $validator->getMessageBag()->toArray()
            ));
        }

        $authentication = $this->checkLoginAuthentication($request);
        $status = $authentication['status'];
        if ($status == 1) {
            $userDetails = $authentication['userDetails'];
            if ($userDetails->role_id == 1) {
                $cyber = new \App\Library\CyberDMT;
                $data = $cyber->getCyberBalance();
                $content='api_token='.config('constants.TRAMO_API_KEY').'&userId='.config('constants.TRAMO_USER_ID');
                $url = config('constants.TRAMO_DMT_URL') ."/check-balance?".$content;//die;
                $tramoBalance = $this->getCurlGetMethod($url);
                $balance = json_decode($tramoBalance);
                $tramoBalance = $balance->message->balance;
                return response()->json([
                    'status' => 1,
                    'message' => 'success',
                    'cyberBalance' => $data,
                    'tramoBalance' => $tramoBalance,
                ]);
            }
            return response()->json([
                'status' => 2,
                'message' => 'access denied!'
            ]);
        }
        return $authentication;
    }
}
