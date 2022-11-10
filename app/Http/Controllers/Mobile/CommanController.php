<?php

namespace App\Http\Controllers\Mobile;
use App\Http\Controllers\Controller;
use App\Traits\CustomAuthTraits;
use Validator;
use App\Company;
use App\Balance;
use Illuminate\Http\Request;
use Response;
use App\Http\Requests;
use DB;
use Auth;
use Exception;
use App\Traits\CustomTraits;

class CommanController extends Controller {

    use CustomTraits,CustomAuthTraits;
    public function refreshPage(Request $request)
    {
        $rules = array(
            'userId'=>'required',
            'token'=>'required',
        );
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return Response::json(array(
                'status' => 2,
                'errors' => $validator->getMessageBag()->toArray()
            ));
        }

        $authentication = $this->checkLoginAuthentication($request);
        if($authentication['status']==1)
		{
			try
			{
				$userDetails = $authentication['userDetails'];
				$remainingBalance =  $balance = Balance::select('user_balance')->where('user_id', $userDetails->id)->first()->user_balance;
				$newDetails= Company::find($userDetails->company_id);
				$bankDownList = CustomTraits::getBankDownList();
				$retailerNews =$newDetails->news;
				$distributorNews =$newDetails->recharge_news;
				return response()->json(['status'=>1,'remainingBalance'=>number_format($remainingBalance,2),'retailerNews'=>$retailerNews,'distributorNews'=>$distributorNews,'bankDownList'=>$bankDownList]);
			}
			catch(Exception $e)
			{
				return response()->json(['status'=>0,'remainingBalance'=>'','retailerNews'=>'','distributorNews'=>'','bankDownList'=>'']);
			}

        }
		else 
			return $authentication;
    }
}
