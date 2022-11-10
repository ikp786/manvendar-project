<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\VoucherApiResponse;
use App\VoucherCategory;
use App\VoucherBrand;
use App\VoucherPrice;
use App\Report;
use App\Balance;
use App\Voucher;
use Auth;
use DB;
use Exception;
class VoucherController extends Controller
{
    //
	var $baseUrl = "https://catalog.vouchagram.com/EPService.svc";
	public function purchasedVoucherDetails(Request $request)
	{
		if(Auth::user()->role_id ==1)
		{
			$purchasedVouchers = Report::where('api_id',22)->orderBy('id','desc')->paginate(40);
			//print_r($purchasedVouchers);die;
			return view('admin.voucher.purchased-voucher-list',compact('purchasedVouchers'));
		}
		else
		{
			$purchasedVouchers = Report::where('api_id',22)->where('user_id',Auth::id())->orderBy('id','desc')->paginate(40);
			return view('agent.voucher.purchased-voucher-list',compact('purchasedVouchers'));
		}
	}
	public function getBulkVoucherCode(Request $request)
	{
		if(Auth::user()->role_id ==1)
		{
			$purchasedVouchersCode = Voucher::selectRaw('voucher_name,voucher_no,voucher_value,product_name,id,message')->where('report_id',$request->voucher_bulk_id)->orderBy('id','desc')->get();
		}
		else
		{
			$purchasedVouchersCode = Voucher::selectRaw('voucher_name,voucher_no,voucher_value,product_name,id,message')->where('report_id',$request->voucher_bulk_id)->where('user_id',Auth::id())->orderBy('id','desc')->get();
			
		}
		if($purchasedVouchersCode)
			return response()->json(['status'=>1,'message'=>$purchasedVouchersCode]);
		else
			return response()->json(['status'=>0,'message'=>"No Record Found"]);
	}
	public function purchageVoucherProduct(Request $request)
	{
		/* $result ='{
  "vPullVouchersResult": {
    "ErrorCode": "",
    "ErrorMessage": "",
    "ExternalOrderIdOut": "12346",
    "Message": "Process successfully completed",
    "PullVouchers": [
      {
        "ProductGuid": "f23cd951-f854-49b4-8891-bc2715eb1747",
        "ProductName": "MORE",
        "VoucherName": "MORE INR 250",
        "Vouchers": [
          {
            "EndDate": "30 Apr 2019",
            "Value": "250.00",
            "VoucherGuid": "b5b3c2c6-ec70-413a-b53b-c34bcaf2ec24",
            "VoucherNo": "107426255336",
            "Voucherpin": ""
          },
          {
            "EndDate": "30 Apr 2019",
            "Value": "250.00",
            "VoucherGuid": "937074ff-6c0c-48f6-b49b-d1e82c62a51c",
            "VoucherNo": "106712513495",
            "Voucherpin": ""
          }
        ]
      }
    ],
    "ResultType": "SUCCESS"
  }
}';
$data = json_decode($result);
$fullVoucher = $data->vPullVouchersResult->PullVouchers[0];
foreach($fullVoucher->Vouchers as $vouchers)
						print_r($vouchers);
die;
print_r(json_decode($result));die; */
		if(Auth::user()->role_id == 5)
		{		
			
			
				
			$ExternalOrderId = 12346;
			$categoryId = $request->category_id;
			$brandId = $request->brand_id;
			$quantity = $request->qty;
			$ProductGuidDetails = VoucherPrice::select('product_guid','price')->where(['voucher_brand_id'=>$brandId])->first();
			if($ProductGuidDetails =='')
				return response()->json(['status'=>2,'message'=>"Voucher not available At this time"]);
			else
				$price = $ProductGuidDetails->price;
				$ProductGuid =$ProductGuidDetails->product_guid;
			if($price =='' || $ProductGuid=='')
				return response()->json(['status'=>2,'message'=>"Invalid Voucher."]);
			if($quantity == '')
				$quantity = 1;
			$chargeAmount = 10*$quantity;
			$debitAmount = ($price * $quantity) + ($chargeAmount * $quantity);
			$logginedUserBalance = Balance::select('user_balance')->where('user_id',Auth::id())->first();
			if($logginedUserBalance->user_balance > $debitAmount)
			{
				DB::beginTransaction();
				try
				{
					Balance::where('user_id',Auth::id())->decrement('user_balance',$debitAmount);
					$report = Report::create([
								'number' => Auth::user()->mobile,
								'provider_id' => 41,
								'category_id' => $categoryId,
								'brand_id' => $brandId,
								'profit' => $chargeAmount,
								'amount' => $price * $quantity,
								'qty' => $quantity,
								'api_id' => 22,
								'ip_address'=> \Request::ip(),
								'status_id' => 3,
								'description' => 'Voucher Purchage',
								'user_id' => Auth::id(),
								'total_balance' => Balance::where('user_id',Auth::id())->select('user_balance')->first()->user_balance,
								'channel' => 4,// Voucher
							]);
							DB::commit();
						   // return redirect()->back()->with('success', 'Request has been submitted');
				}
				catch(Exception $e)
				{
					DB::rollback();
					return response()->json(['status'=>2,'message'=>"Whoops something went wrong"]);
				}
			}
			else
				return response()->json(['status'=>2,'message'=>"Insufficient Balance"]);
			
			$url =$this->baseUrl ."/PullVoucher?BuyerGuid=". config('constants.BUYER_GUID') ."&ProductGuid=". $ProductGuid ."&ExternalOrderId=". $ExternalOrderId."&Quantity=". $quantity."&Password=" .config('constants.PASSWORD');
			$voucherApi = VoucherApiResponse::create(['user_id'=>Auth::id(),'report_id'=>$report->id,'request_type'=>"VOUCHER_PURCHAGE",'request_param'=>$url]);
			
			$curl = curl_init();
			curl_setopt_array($curl, array(
			CURLOPT_URL =>$url,
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
			/* $response = '{"vPullVouchersResult":{"ErrorCode":"","ErrorMessage":"","ExternalOrderIdOut":"123456","Message":"Process successfully completed","PullVouchers":[{"ProductGuid":"38c4c1f1-94eb-4666-a001-f4d87e5fd5e7","ProductName":"Baskin Robins","VoucherName":"BR INR 50","Vouchers":[{"EndDate":"04 Apr 2019","Value":"50.00","VoucherGuid":"a05c5458-a12a-4cac-8113-a1fa56843542","VoucherNo":"BV61141325","Voucherpin":""}]}],"ResultType":"SUCCESS"}}'; */
			$voucherApi->report_id = $report->id;
		
			$voucherApi->response=$response;
			$voucherApi->save();
			
			$err = false;;
			if ($err) {
				$voucherApi->reason = "Invalid Respnse";
				$voucherApi->save();
			  return response()->json(['status'=>3,'message'=>"Pending",'TYPE'=>"ERROR"]);
			} 
			else 
			{
				try
				{
					$results= json_decode($response);
					//print_r($results);die;
					if($results->vPullVouchersResult->ResultType =="SUCCESS")
					{
						$fullVoucher = $results->vPullVouchersResult->PullVouchers[0];
						//print_r($fullVoucher);die;
						//$vouchers = $fullVoucher->Vouchers[0];
						$report->status_id = 1;
						$report->txnid = $fullVoucher->Vouchers[0]->VoucherNo;
						DB::beginTransaction();
						try
						{
							$report->save();
							foreach($fullVoucher->Vouchers as $vouchers)
							{
						
								$voucher = Voucher::create(['external_order_id_out'=>$ExternalOrderId,'product_guid'=>$ProductGuid,'report_id'=>$report->id]);
								$voucher->message = $results->vPullVouchersResult->Message;
								$voucher->product_name = $fullVoucher->ProductName;
								$voucher->voucher_name = $fullVoucher->VoucherName;
								$voucher->voucher_enddate = $vouchers->EndDate;
								$voucher->voucher_value = $vouchers->Value;
								$voucher->voucher_guid = $vouchers->VoucherGuid;
								$voucher->voucher_no = $vouchers->VoucherNo;
								$voucher->result_type = "SUCCESS";
								$voucher->reason = "Success";
								//$voucher->report_id = $report->id;
								$voucher->save();
								DB::commit();
							}
							return response()->json(['status'=>1,'message'=>"Success"]);
							
						}
						catch(Exception $e)
						{
							DB::rollback();
							$voucherApi->reason = $e->getMessage();
							$voucherApi->save();
							return response()->json(['status'=>3,'message'=>"'Voucher Puchased Success, Contact with Administration"]);
							
						}
						
					}
					else if(strtoupper($results->vPullVouchersResult->ResultType) =="FAILED")
					{
						$report->status_id =2;
						$report->txnid =$results->vPullVouchersResult->ErrorMessage;
						DB::beginTransaction();
						//$voucher->message = $results->vPullVouchersResult->Message;
						try
						{
							Balance::where('user_id',Auth::id())->increment('user_balance',$debitAmount);
							$report->total_balance = Balance::where('user_id',Auth::id())->select('user_balance')->first()->user_balance;
							$voucherApi->reason = "Voucher Request Failed";
							$voucherApi->save();
							$report->save();
							DB::commit();
							return response()->json(['status'=>2,'message'=>"Failed"]);
						}
						catch(Exception $e)
						{
							DB::rollback();
							$voucherApi->reason = $e->getMessage();
							$voucherApi->save();
							return response()->json(['status'=>3,'message'=>"Voucher Request Pending"]);
						}
					}
					else
					{
						$voucherApi->reason = "New Response Type";
						$voucherApi->save();
						return response()->json(['status'=>3,'message'=>"Voucher Request Pending"]);
					}
				}
				catch(Exception $e)
				{
					//throw $e;
					$voucherApi->reason = "Main ".$e->getMessage();
					$voucherApi->save();
					return response()->json(['status'=>3,'message'=>"Voucher Request Pending",'TYPE'=>"EXCEPTION"]);
				}
			}
		}
	}
	public function getVoucherCategory(Request $request)
	{
		if(Auth::user()->role_id == 5)
		{

		}
	}
	public function getVoucherCategoryBrand(Request $request)
	{
		//print_r($request->all());
		$brands = VoucherBrand::where('voucher_categorie_id',$request->category_id)->pluck('name','id')->toArray();
		if(count($brands))
		{
			return response()->json(['status'=>1,'brands'=>$brands]);
		}
		else
		{
			return response()->json(['status'=>0,'message'=>"No brand available"]);
		}
		if(Auth::user()->role_id == 5)
		{

		}
	}
	public function getVoucherPrice(Request $request)
	{
		$brandsprices = VoucherPrice::where('voucher_brand_id',$request->brand_id)->pluck('price','id')->toArray();
		if(count($brandsprices))
		{
			return response()->json(['status'=>1,'brandsprices'=>$brandsprices]);
		}
		else
		{
			return response()->json(['status'=>0,'message'=>"No Price  available"]);
		}
		if(Auth::user()->role_id == 5)
		{

		}
	}
	public function showVoucherProduct(Request $request)
	{
		$categories=VoucherCategory::pluck('name','id')->toArray();
		//print_r($categories);die;
		return view('admin.voucher.voucher',compact('categories'));
	}
	public function setVoucherCategory(Request $request)
	{
		VoucherCategory::create(['name'=>$request->category_name]);
		return response()->json(['message'=>"Voucher Category Added"]);
	}
	public function setVoucherCategoryBrand(Request $request)
	{
		
		VoucherBrand::create(['name'=>$request->brand_name,'voucher_categorie_id'=>$request->category_id]);
		return response()->json(['message'=>"Voucher Brand Added"]);
	}
	public function setVoucherPrice(Request $request)
	{
		//print_r($request->all());
		VoucherPrice::create(['voucher_brand_id'=>$request->brand_id,'price'=>$request->price]);
		return response()->json(['message'=>"Voucher Price Added"]);
		
	}
	public function setProductGUID(Request $request)
	{
		if(VoucherPrice::where(['id'=>$request->price_table_id])->update(['product_guid'=>$request->product_guid]))
			return response()->json(['status'=>1,'message'=>"Product GUID updated successfully"]);
		return response()->json(['status'=>1,'message'=>"Not updated"]);
	}
	public function getProductGUID(Request $request)
	{
		
		return VoucherPrice::select('product_guid')->where(['id'=>$request->price_table_id])->first();
	}
	/* ----------------------------------------- */
	public function voucherProduct(Request $request)
	{
		$categories=VoucherCategory::pluck('name','id')->toArray();
		return view('agent.voucher.voucher',compact('categories'));
	}
	public function getBrandWithPrice(Request $request)
	{
		//print_r($request->all());
		/* $brands = VoucherBrand::where('voucher_categorie_id',$request->category_id)->select('name','id','voucher_categorie_id')->get();
		$voucherWithPrice = $brands->map(function($data)
		{
			return [
					'name'=>$data->name,
					'id'=>$data->id,
					'voucher_categorie_id'=>$data->voucher_categorie_id,
					'price'=>@$data->price->price,
					];
		}); */
		$voucherWithPrice = VoucherBrand::join('voucher_prices',function($join){
						$join->on('voucher_brands.id','=','voucher_prices.voucher_brand_id');
		})
		->where('voucher_categorie_id',$request->category_id)
		->selectRaw('voucher_brands.id as id,voucher_brands.voucher_categorie_id as voucher_categorie_id,voucher_brands.name as name,voucher_prices.price as price')
		->get();
		if(count($voucherWithPrice))
		{
			return response()->json(['status'=>1,'voucherWithPrice'=>$voucherWithPrice]);
		}
		else
		{
			return response()->json(['status'=>0,'message'=>"No Voucher Falicity Available"]);
		}
		if(Auth::user()->role_id == 5)
		{

		}
	}
	
}
