<?php

namespace App\Http\Controllers\Mobile;

use App\Traits\CustomAuthTraits;
use App\Traits\CustomTraits;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\ActiveService;
use App\Http\Controllers\Controller;

use Validator;
use Response;


class ActiveServiceController extends Controller
{
    use CustomTraits, CustomAuthTraits;
    public function getActiveServices(Request $request)
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
                $services = ActiveService::all();
                return response()->json([
                    'status' => 1,
                    'message' => 'success',
                    'list' => $services,
                ]);
            }
            return response()->json([
                'status' => 2,
                'message' => 'You are not Authenticate',
            ]);
        }
        return $authentication;
    }

    public function makeActiveInactiveServices(Request $request)
    {
        $rules = array(
            'userId' => 'required|numeric',
            'token' => 'required',
            'id'=>'required',
            'status_id'=>'required'
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
            if ($userDetails->role_id == 1)
            {
                $status_id = $request->status_id;
                $service_detail = ActiveService::find($request->id);
                $service_detail->status_id = $status_id;
              //  $service_detail->message = $request->message;
                if ($service_detail->save())
                    return response()->json(['status' => 1, 'message' => 'Updated']);
            }
            return Response()->json(['status' => "2", 'message' => 'You are not Authenticate']);

        }
        return $authentication;
    }


}
