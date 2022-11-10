<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use App\Plan;
use App\UserPlan;
class TragetSettingController extends Controller
{

		public function updateUserPlan(Request $request)
		{
			
			$users = User::selectRaw('id,plan_id')->get();
			$plans = Plan::select('id')->get();
			DB::begintTransaction();
			try{
				foreach($users as $user)
				{
					foreach($plans as $plan)
					{
						$userPlan = UserPlan::create(['user_id'=>$user->id,'plan_id'=>$plan->id]);
						UserPlanTarget::create(['userplan_id'=>$userPlan->id]);
					}
				}
				DB::commit();
			}
			catch(Exception $e)
			{
				DB::rollback();
			}
		}
		public function updateUserTragent(Request $request)
		{
			$rule=array(
				
			
			);
			
		}
}
