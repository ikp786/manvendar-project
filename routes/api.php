<?php
use Illuminate\Http\Request;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
|Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::group(['prefix' => 'callback'], function () {
	Route::any('/redpay','CallBackUrlController@redpayCallBackUrl');
	
	Route::any('/cyber','CallBackUrlController@cyberCallback');
});

Route::group(['prefix' => 'dmtcallback','middleware'=>'auth:api'], function () {
    Route::any('/tramo','CallBackUrlController@aTwoZWalletCallBack');
});

Route::group(['prefix' => 'v1','middleware'=>'auth:api'], function () {
    Route::any('fetch/bill-format','ApiForwarding\ApiController@fetchBillFormat');
    Route::any('fetch/bill-details','ApiForwarding\ApiController@fetchBillDetails');
    Route::any('dmt/bank-down','ApiForwarding\ApiController@checkBankDownApi');
});
 
Route::group(['prefix' => 'v1/dmt', 'middleware' => ['auth:api']], function () 
{
    Route::post('a2z-transaction','ApiForwarding\MyWalletController@makeTransaction');
    Route::post('a2z-mobile-verification','ApiForwarding\MyWalletController@mobileVerificaton');
    Route::post('a2z-remitter-register','ApiForwarding\MyWalletController@remitterRegister');
    Route::post('a2z-mobile-verification-with-otp','ApiForwarding\MyWalletController@mobileVerifiedWithOTP');
    Route::post('a2z-bene-list','ApiForwarding\MyWalletController@getBeniList');
    Route::post('a2z-add-beneficiary','ApiForwarding\MyWalletController@addBeneficiary');
   Route::post('a2z-txn-check-status','ApiForwarding\MyWalletController@checkImpsTransactionCurrentStatus');
   Route::post('a2z-bene-delete-request','ApiForwarding\MyWalletController@deleteBeneficiaryRequest');
   Route::post('a2z-bene-delete-confirm-otp','ApiForwarding\MyWalletController@deleteBeneficiaryThroughOtp');
   
	Route::post('get-bank-list','ApiForwarding\aToZWalletController@getBankList');
	Route::post('kyc-mobile-verification','ApiForwarding\aToZWalletController@kycMobileVerificaton');
	Route::post('mobile-verification','ApiForwarding\aToZWalletController@mobileVerificaton');
	Route::post('bene-list','ApiForwarding\aToZWalletController@getBeniList');
	Route::post('remitter-register','ApiForwarding\aToZWalletController@remitterRegister');
	Route::post('mobile-verification-with-otp','ApiForwarding\aToZWalletController@mobileVerifiedWithOTP');
	Route::post('upload-kyc-doucuments','ApiForwarding\aToZWalletController@uploadKycForm');
	Route::post('add-beneficiary','ApiForwarding\aToZWalletController@addBeneficiary');
	Route::post('transaction','ApiForwarding\aToZWalletController@makeTransaction');
	Route::post('verify-account-number','ApiForwarding\aToZWalletController@verifyAccountNumber');
	Route::post('txn-check-status','ApiForwarding\aToZWalletController@checkImpsTransactionCurrentStatus');
	Route::post('check-balance','ApiForwarding\aToZWalletController@checkBalance');
	Route::post('bene-delete-request','ApiForwarding\aToZWalletController@deleteBeneficiaryRequest');
	Route::post('bene-delete-confirm-otp','ApiForwarding\aToZWalletController@deleteBeneficiaryThroughOtp');
	//Route::post('transaction-check-status','ApiForwarding\aToZWalletController@checkImpsTransactionCurrentStatus'); 
	Route::POST('send-refund-txn-otp','ApiForwarding\aToZWalletController@sendRefundTxnOtp');
	Route::post('txn-refund-request','ApiForwarding\aToZWalletController@transactionRefundRequest');
	Route::post('get-my-ip','ApiForwarding\aToZWalletController@getMyIp');
  
});
Route::group(['prefix' => 'v1/aeps', 'middleware' => ['auth:api']], function () 
{
	Route::post('get-iin-no','ApiForwarding\AepsController@getIINNo');
	Route::post('transaction','ApiForwarding\AepsController@sendRequest');
	Route::post('get-my-ip','ApiForwarding\AepsController@getMyIp');
	Route::post('check-status','ApiForwarding\AepsController@checkStatus'); 
	Route::post('check-balance','ApiForwarding\AepsController@checkBalance'); 
	Route::post('onboarding','ApiForwarding\AepsRegistrationController@aepsMemberRegistration');
 
});
Route::group(['prefix' => 'v2/aeps', 'middleware' => ['auth:api']], function () 
{
	Route::post('get-iin-no','ApiForwarding\AepsV2Controller@getIINNo');
	Route::post('transaction','ApiForwarding\AepsV2Controller@sendRequest');
	Route::post('get-my-ip','ApiForwarding\AepsV2Controller@getMyIp');
	Route::post('check-status','ApiForwarding\AepsV2Controller@checkStatus'); 
	Route::post('check-balance','ApiForwarding\AepsV2Controller@checkBalance'); 
	Route::post('onboarding','ApiForwarding\AepsRegistrationController@aepsMemberRegistration');
 
});
Route::group(['prefix'=>'v1/dmtthree','middleware'=>['auth:api']], function () 
{ 
	Route::post('mobile-verification','ApiForwarding\InstantPayController@mobileVerificaton'); 
	Route::post('add-sender','ApiForwarding\InstantPayController@remitterRegister')->name('add-sender'); 
	Route::post('sender-verification','ApiForwarding\InstantPayController@remitterVerification')->name('sender-verification');
	Route::post('add-beneficiary','ApiForwarding\InstantPayController@beneAdd')->name('add-beneficiary');
	
	Route::post('account-verification','ApiForwarding\InstantPayController@verifyAccountNumber')->name('account-verification');
	Route::post('bene_confirm','ApiForwarding\InstantPayController@beneVerification')->name('beneficiary-verification');
	Route::post('resend-bene-otp','ApiForwarding\InstantPayController@resendBeneVerificationOtp')->name('resend-bene-otp');
	Route::post('delete_beneficiary','ApiForwarding\InstantPayController@deleteBeneficiary');
	Route::post('confirm-bene-delete','ApiForwarding\InstantPayController@confirmBeneDelete');
	Route::post('transaction','ApiForwarding\InstantPayController@transaction');
	Route::post('check-status','ApiForwarding\InstantPayController@checkImpsTransactionCurrentStatus');
});
Route::group(['prefix' => 'api/v1/recharge', 'middleware' => ['auth:api']], function () {
	Route::post('get-provider','Apiforwarding\RechargeController@getProvider');
	Route::post('payment','Apiforwarding\RechargeController@recharge');
	Route::post('check-status','Apiforwarding\RechargeController@checkStatus');
	Route::post('get-balance','Apiforwarding\RechargeController@checkBalance');
	
});