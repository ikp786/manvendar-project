<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () { 
    return view('welcome');
});

Route::get('newsignup','SignupController@index')->name('newsignup');
Route::post('storesignup','SignupController@store')->name('storesignup');

Auth::routes();
Route::get('logout', 'Auth\LoginController@logout');
Route::get('/home', 'HomeController@index')->name('home');

Route::get('login/system-verificaiton', 'Auth\LoginController@systemVerification');
Route::get('callback/recharge-service-callback','CallBackUrlController@aToZRechargeCallBackUrl');
Route::any('mrobotics-recharge-callback','CallBackUrlController@mroboticsCallBackUrl');
//for agent
Route::group(['middleware'=>['logout-after-user-inactive','auth']],function() 
{
	
	Route::get('error-log-on','SecurityController@raiseError');  
	Route::get('/dashboard','DashboardController@index')->name('dashboard');
	
	Route::get('/company', 'CompanyController@index');
	Route::get('/recharge-scheme', 'SchemeController@index');
	Route::post('recharge-scheme', 'SchemeController@createRechargeScheme');
	Route::put('recharge-scheme/{schemeId}', 'SchemeController@updateRechargeScheme');
	Route::get('recharge-scheme/view', 'SchemeController@viewRechargeScheme');
	Route::post('recharge-scheme/delete', 'SchemeController@deleteRechargeScheme');
	Route::post('/scheme-manage/', 'SchemeController@moneystore');
	//Route::get('/moneytxn-scheme', 'SchemeController@money_scheme');
	Route::post('/commission-manage/viewupdate', 'CommissionController@viewupdate');
	Route::post('/money-commission-manage/viewupdate', 'CommissionController@moneyviewupdate');
	Route::post('/commission-manage/', 'CommissionController@store');
	Route::post('/money-commission-manage/', 'CommissionController@moneystore');
	Route::get('/recharge-operator', 'MemberController@provider_manage');
	Route::get('/dmt-imps', 'DmtmoneyController@dmt_imps');
		
		
	Route::get('report/r-to-r','ReportController@rToR');
	Route::get('otp/list','MemberController@userLoginOtp');
	Route::post('update-security','SecurityController@systemSecurityUpdate');
	Route::any('account/search','ReportController@accountSearch');
	Route::get('get-mobile-transaction-report','PremiumWalletController@RecordByMobileNumberReport');
	Route::get('get-mobile-transaction-report','MoneyController@RecordByMobileNumber');
	Route::get('service-charged','ServiceController@index');
	Route::post('service-charged','ServiceController@store');
	Route::get('transfer/r-to-r','TransferController@rToRList');
	Route::post('transfer/r-to-r','TransferController@fundTransferRToR');
	Route::get('provider-list','ProviderController@index');
	Route::put('change-service/{providerId}','ProviderController@updateServiceType');
	Route::put('update/operator-code/{providerId}','ProviderController@updateOperatorCode');
	Route::get('get/operator-details','ProviderController@viewProviderDatils');
	Route::post('provider/update/{id}','ProviderController@update');
	Route::post('provider/create','ProviderController@store');
	Route::get('provider/un-categorised','ProviderController@showUncategoryList');
	/* SubUser */
	Route::get('scheme-verification-pin','SecurityController@verificationSecurityPin')->name('verification-pin');
	Route::get('view-sub-agent','SubUserController@viewSubUser');
	/* SubUser */
	Route::get('sub-user-report','ReportController@subUserReport')->name('sub-user-report');
	Route::get('view-sub-agent','SubUserController@viewSubUser');
	/* BBPS FEtCH BIll */
	Route::get('get-agent-charge-amt', 'MoneyController@getAgentChargeAmount');
	Route::get('/get-provider-name','BbpsController@getProdiverName');
	Route::get('fetch-bill-amount','BbpsController@fetchBillAmount')->name('fetch-bill-amount');
	/* End */
	Route::get('/bbps','BbpsController@show')->name('bbps');
	Route::post('/bbps-recharge','BbpsController@recharge')->name('bbps-recharge');
	Route::get('bbps-landline','BbpsController@landline')->name('bbps-landline');
	Route::get('bbps-water','BbpsController@water')->name('bbps-water');
	Route::get('bbps-gas','BbpsController@gas')->name('bbps-gas');
	Route::get('bbps-insurance','BbpsController@insurance')->name('bbps-insurance');
	Route::get('/loanrepayment','BbpsController@loanrepayment')->name('bbps-loanrepayment');

	Route::get('/check-bbps-bill/', 'ElectricityController@check_bill');
	Route::post('/store-bbps/', 'ElectricityController@store');
	
	//Route::get('/money','MoneyController@index')->name('agent.money')->name('money');
	Route::get('/money','MoneyController@index')->name('money');
	Route::get('/money-neft','MoneyController@neft')->name('money-neft');
	Route::get('/money-kyc','MoneyController@kyc')->name('money-kyc');
	Route::get('/money-credit-card','MoneyController@credit_card')->name('money-credit-card');
	Route::get('/money-aeps','MoneyController@aeps')->name('money-aeps');
	Route::get('/money-loan','MoneyController@loan')->name('money-loan');
	Route::get('/money-payment','MoneyController@payment')->name('money-payment');
	Route::get('/money-wallet-load','MoneyController@wallet_load')->name('money-wallet-load');
	Route::get('/money-cabel','MoneyController@cabel')->name('money-cabel');
	Route::post('/moneyloanstore','MoneyLoanController@store')->name('moneyloanstore');
	Route::post('/money-insurance-payment','MoneyLoanController@paymentStore')->name('money-insurance-payment');
	Route::post('/money-cabelstore','MoneyLoanController@cabelStore')->name('money-cabelstore');
	Route::get('/money-dmt2','MoneyDmt2Controller@index')->name('money-dmt2');
	Route::post('/validate-mobiledmt2','MoneyDmt2Controller@validateMobile');

	Route::get('recharge','RechargeController@index')->name('recharge');
	Route::post('/recharge','RechargeController@cyberRecharge')->name('make_recharge');
	Route::get('/recharge-prepaid2','RechargeController@prepaid2')->name('recharge-prepaid2');
	Route::get('bbps-postpaid','RechargeController@postpad')->name('bbps-postpaid');
	Route::get('dth-recharge','RechargeController@dth')->name('dth-recharge');
	Route::get('datacard-recharge','RechargeController@datacard')->name('datacard-recharge');
	Route::get('bbps-broadband','RechargeController@broadband')->name('bbps-broadband');
	Route::get('/recharge-landline','RechargeController@landline')->name('recharge-landline');
	Route::post('/re-check-status','PayjstrechargeController@rech_check_status');
	Route::get('/dth_offers','DTHController@index')->name('dth_offers');
	
	Route::get('/yesbank','YesBankController@index')->name('agent.yesbank');
	Route::get('/imt','IMTController@index')->name('agent.imt');
	Route::get('/pancard','PanCardController@index')->name('agent-pancard');
	Route::get('/pancard-cardload','PanCardController@cardload')->name('pancard-cardload');
	Route::post('/pancard_store','PanCardController@store')->name('pancard_store');
	Route::post('/pan-card-activation','PanCardController@activePanService')->name('pan-card-activation');
	Route::get('/agent-dashboard','DashboardController@agentDashboard')->name('agent-dashboard');
	Route::get('/addmoneyredirect','RedirectBankUrlController@index')->name('addmoneyredirect');
	/*reports for agent*/
	Route::get('txn-history','ReportController@moneyTxnHistory')->name('txn-history');
	Route::get('txn-history-impswallet','ReportController@moneyTxnHistoryImpswallet')->name('txn-history-impswallet');
	Route::get('txn-history-premiumwallet','ReportController@moneyTxnHistoryPremiumwallet')->name('txn-history-premiumwallet');
	Route::get('dt-report','ReportController@directTransferReport')->name('dt-report');
	Route::get('error-log-on','SecurityController@raiseError');  
	/* end */
	/*business report-fundrelated*/
	Route::get('get-operator','SummaryReportController@getOperator');
	Route::get('get-sub-category','SummaryReportController@getSubCategory'); 
	Route::get('/summary_report','SummaryReportController@summary_report')->name('summary_report');
	Route::get('rechrge-report-export','ReportController@export_by_date');
	Route::get('rechrge-report-filter','ReportController@search_by_date');
	Route::get('searchall-all','ReportController@searchall_all');
	Route::get('searchall-all-moneyreport','ReportController@searchall_all_money');
	Route::get('/load-cash','HomeController@load_cash')->name('load-cash');
	
	Route::get('/payment-request-report','HomeController@paymentRequestReport')->name('payment-request-report');
	Route::post('/payment-request','HomeController@paymentRequestSave');
	Route::post('/payment-request-company','HomeController@paymentRequestCompanySave');
	Route::get('/payment-request-company','HomeController@paymentRequestCompany')->name('payment-request-company');
	Route::post('cancel-fund-request','HomeController@cancelFundRequest');
	
		/*funds*/
    Route::get('/bank-fund','HomeController@paymentRequest')->name('bank-fund');
    Route::get('/credit-fund','HomeController@creditFund')->name('credit-fund');
	
	Route::get('/bank-cash','HomeController@bankcash')->name('bank-cash');
	Route::get('/fund-req-report','ReportController@fund_req_report')->name('fund-req-report');
	Route::get('/report','ReportController@report')->name('agent-report');
	Route::get('/invoice/{id}','FrontController@invoice');
	
	Route::get('dmt-scheme', 'WalletSchemeController@getAllDMTScheme');
	Route::get('dmt-imps-scheme', 'WalletSchemeController@getAllImpsScheme');
	Route::post('/wallet-commission-update', 'CommissionController@dmtViewScheme');
	Route::post('/verification-update', 'CommissionController@verificationViewScheme');
	Route::post('/imps-commission-update', 'CommissionController@impsViewScheme');// new
	/* Aeps */
	Route::group(['prefix' => 'member'], function () {
		Route::put('update-fund-request-scheme','MemberController@updateFundRequestScheme');
		Route::get('get-fund-request-scheme/{userId}','MemberController@getFundRequestScheme');
		Route::get('get-upschme-list','MemberController@getAllUpscheme');
	});
	Route::group(['prefix' => 'aeps'], function () {
		
		Route::get('/', 'AepsController@aepsLogin')->name('aeps');
		Route::get('bank-details','AepsSettlementController@index')->name('aeps-bank-details');
		Route::get('bank-details-list','AepsSettlementController@banklist')->name('aeps-bank-details-list');
		Route::get('bank-details-fund','AepsSettlementController@bankfund')->name('aeps-bank-details-fund');
		
		Route::get('bank-details-view/{id}','AepsSettlementController@view')->name('aeps-bank-details-view');
		Route::post('bank-details','AepsSettlementController@store');
		Route::post('delete-bank-details','AepsSettlementController@deleteDetails')->name('aeps-delete-bank-details');
		Route::post('bank-details/{id}','AepsSettlementController@update')->name('aeps-update-bank-details');
		Route::post('settlement-request','AepsSettlementController@AepsSettlementAmount')->name('aepsSettlementAmount');
		Route::get('report','ReportController@aepsReport')->name('aeps-report');
	});
	Route::post('aeps-onboard','AepsRegistrationController@aepsAgentOnboard')->name('aeps-agent-onboard');
	Route::get('aeps-agent','AepsRegistrationController@index')->name('aeps-agent');
	Route::get('morpho-aeps', 'AepsController@aepsMorphoLogin'); 
	Route::post('send-aeps-request', 'AepsController@sendRequest')->name('send-aeps-request');
	Route::get('aeps_driver', 'AepsController@aepsDriver')->name('aeps_driver');
	
	Route::post('/aeps-commission-update', 'CommissionController@aepsViewScheme');// aeps
	Route::get('aeps-scheme', 'WalletSchemeController@getAllAepsScheme');
	Route::post('add-new-aeps-scheme', 'WalletSchemeController@createAepsScheme')->name('add-new-aeps-scheme');
	Route::post('create-new-aeps-row','WalletSchemeController@createAepsOneRow')->name('create-new-aeps-row');
	Route::post('updateAepsScheme','WalletSchemeController@updateAepsScheme');
	/* End */
	Route::post('add-new-wallet-scheme', 'WalletSchemeController@createWalletScheme')->name('add-new-wallet-scheme');
	Route::put('add-new-wallet-scheme/{id}', 'WalletSchemeController@updateWalletSchemeName');
	Route::post('updateWalletScheme','WalletSchemeController@updateWalletScheme');
	Route::post('updateImpsScheme','WalletSchemeController@updateImpsScheme');
	Route::get('getWalletSchemeName', 'WalletSchemeController@getWalletSchemeName')->name('getWalletSchemeName');
	Route::get('get-wallet-scheme', 'MemberController@getWalletScheme')->name('get-wallet-scheme');
	Route::put('set-wallet-scheme/{user_id}', 'MemberController@setWalletScheme')->name('set-wallet-scheme');
	
	Route::get('get-wallet-scheme', 'MemberController@getWalletScheme')->name('get-wallet-scheme');
	Route::put('set-wallet-scheme/{user_id}', 'MemberController@setWalletScheme')->name('set-wallet-scheme');
	Route::get('get-dmt-scheme', 'MemberController@getDmtScheme')->name('get-dmt-scheme');
	Route::get('show-dmt-scheme', 'MemberController@showDmtScheme')->name('show-dmt-scheme');
	Route::put('set-dmt-scheme/{user_id}', 'MemberController@setDmtScheme')->name('set-dmt-scheme');
	
	Route::get('/provider/save', 'MemberController@provider_update');
	 //reset password of Agent
	Route::post('change_password','MemberController@change_password');
	Route::get('changepassword','MemberController@changepassword');
	Route::post('generateTransactionpin','MemberController@generateTransactionpin');
	Route::post('generateSchemepin','MemberController@generateSchemepin');
	Route::post('generate-otp','MemberController@generateOTP');
	
	Route::get('view-profile','ProfileController@viewProfile')->name('view-profile');
	Route::get('update-profile','ProfileController@updateProfile')->name('update-profile');
	Route::post('opt-out-otp','ProfileController@optOutOtp')->name('opt-out-otp');
	Route::get('download-certificate','ProfileController@download');
	
	Route::get('business-report','AnalyticController@businessReport');
	Route::get('amount-in-words','FrontController@amountInWords');
	Route::get('refresh-balance','MemberController@refreshBalance');
	
	Route::get('txn/get-report', 'ReportController@getTransationDetails');
	Route::get('txn/txn-details', 'ReportController@getTxnDetails');
	Route::post('report/update', 'ReportController@update');
	Route::get('view-commission', 'AnalyticController@viewTxnCommission');
	Route::get('member-custom-serach', 'MemberController@memberCustomSearch');
	Route::get('get-sub-category', 'SummaryReportController@getSubCategory');
	Route::get('account-summary', 'ReportController@accountSummary')->name('account-summary');
	Route::post('store_complain_req','ComplainController@store_complain_req');
	Route::get('complain','ComplainController@complain');
    Route::post('complain','ComplainController@complain_req');
	Route::get('/payment-load','ReportController@paymentLoad');
	Route::get('get-cyber-balance', 'MoneyController@getCyberBalance');
	/* Premium Wallet  */
	Route::get('premium-wallet', 'PremiumWalletController@index')->name('premium-wallet');
	Route::get('get-txn-by-field', 'CommonController@getTransactionByField');
	
	Route::get('mobile-verification','PremiumWalletController@mobileVerificaton');
	Route::get('imps-mobile-verification','ImpsWalletController@mobileVerificaton');
	Route::get('imps-sender-limit','ImpsWalletController@senderLimit');
	Route::get('get-bene-list','PremiumWalletController@getBeniList');
	//Route::get('get-bene-list','ImpsWalletController@getBeniList');
	Route::get('remitter-register','PremiumWalletController@remitterRegister');
	Route::put('mobile-verification-with-otp/{mobile}','PremiumWalletController@mobileVerifiedWithOTP');
	Route::put('imps-mobile-verification-with-otp/{mobile}','ImpsWalletController@mobileVerifiedWithOTP');
	Route::post('verify-account-number','PremiumWalletController@verifyAccountNumber');
	Route::post('tramo-transaction','PremiumWalletController@transaction');
	Route::post('bene-add','PremiumWalletController@beneAdd');
	Route::post('imps-bene-add','ImpsWalletController@beneAdd');
	Route::get('dmt-two-imps-scheme', 'WalletSchemeController@getAllDMTTwoScheme');
	Route::get('verification-scheme', 'WalletSchemeController@getVerificationScheme');
	Route::put('bene-delete-request/{beneId}','PremiumWalletController@deleteBeneficiaryRequest');
	Route::put('confirm-bene-delete','PremiumWalletController@deleteBeneficiaryThroughOtp');
	Route::get('/flush_otp', 'MemberController@flush_otp');
	
	Route::post('check-txn-status','CheckStatusController@checkTransactionCurrentStatus');
	
	Route::put('switch/operator/{providerId}','OperatorController@switchRechargeOperator');
	 Route::get('recharge-operator-list','OperatorController@rechargeOperatorList');
	 Route::put('switch/onoff/{providerId}','OperatorController@onOffService');
	 
	Route::get('recharge-txn-history','RechargeController@rechargeTxnHistory')->name('recharge-txn-history'); 
	//Route::get('getPrepedRechargeOffer','ROfferController@getOffer')->name('r-offer');//Route::get('get-dth-plans','ROfferController@getDTHPlans')->name('get-dth-plans'); 
	Route::get('getPrepedRechargeOffer','ROfferController@getPrepedRechargeOffer'); 
	Route::get('special-number-offer','ROfferController@getSpecialNumberOffer'); 
	Route::get('getDTHOffer','ROfferController@getSpecialNumberOffer'); 
	
	Route::get('get-dth-customer-info','ROfferController@getDTHCustomerInfo')->name('get-dth-customer-info'); 
	Route::get('fund-request-summary','HomeController@fundSummary')->name('fund-request-summary'); 

	//Route::get('generateSecretKey','MemberController@generateSecretKey');
	/*for admin members*/
	Route::get('/admin/master-distributor', 'MemberController@master_distributor');
	Route::get('/admin/distributor', 'MemberController@distributor');
	Route::get('/admin/retailer', 'MemberController@retailer');
	Route::get('admin/api-member', 'MemberController@api_retailer');
	Route::get('/admin/sales-enquiry', 'MemberController@api_retailer');
	Route::get('/member/export','MemberController@export');
	/*End members*/
	
	/*otp generate for change Password from Admin*/
	Route::post('passward-generate-otp','MemberController@passwordGenerateOTP')->name('passward-generate-otp');
	Route::post('Verify-password-otp','MemberController@passwordVerifyOTP')->name('Verify-password-otp');
		/*otp generate End*/
		
	Route::get('dmt-reports','ReportController@admin_money_transfer_report')->name('dmt-reports');
	Route::get('account-statement', 'ReportController@account_statement');
	Route::get('daily-balance-reports', 'ReportController@dailyMaintainBalance')->name('daily-balance-reports');
	Route::get('all-recharge-report', 'ReportController@all_recharge_transaction')->name('all-recharge-report');
	Route::get('money_transfer_report', 'ReportController@money_transfer_report')->name('money_transfer_report');
	Route::get('/transaction-report','ReportController@all_recharge_report')->name('transaction-report'); 
	Route::post('txn-generate-otp','ReportController@generateOTP')->name('txn-generate-otp');
	Route::post('Verify-txn-otp','ReportController@verifyTxnOTP')->name('Verify-txn-otp');
	
	/*for Action otp verification*/
	Route::get('action-otp-verification','ActionOtpVerificationController@index')->name('action-otp-verification');
	Route::post('action-otp-verify-details','ActionOtpVerificationController@store');
	Route::get('action-otp-verify-details-view/{id}','ActionOtpVerificationController@show');
	Route::post('action-otp-verify-details/{id}','ActionOtpVerificationController@update');
	Route::post('delete-otp-verify-details/{id}','ActionOtpVerificationController@destroy');
	/*end*/
	
	Route::get('/money_transfer', 'TransferController@money_transfer')->name('Money Transfer');
	Route::get('validate-mobile', 'MoneyController@fetchCustomerDetails');
	Route::get('cyber-api/verify-otp', 'MoneyController@verifySenderOtp');
	Route::get('cyber-api/add-sender', 'MoneyController@add_sender');
	Route::get('cyber-api/add_beneficiary','MoneyController@add_beneficiary');
	Route::get('cyber-api/bene_confirm', 'MoneyController@bene_confirm');
	Route::get('cyber-api/beneconform_resend_otp', 'MoneyController@beneconform_resend_otp');
	Route::get('cyber-api/get_bank_detail',  'MoneyController@get_bank_detail');
	Route::get('cyber-api/account-name-info', 'MoneyController@account_name_info');
	Route::get('cyber-api/delete_beneficiary', 'MoneyController@delete_beneficiary');
	Route::get('cyber-api/bene_confirm_delete','MoneyController@bene_confirm_delete');
	Route::get('api-response','ReportController@apiResponse')->name('api-response');
	Route::get('get-txn-sale','HomeController@getTransactionSale')->name('get-txn-sale');
	Route::get('get-po-txn-count-volume','DashboardController@getPendingOfflineTxnCountVolume')->name('get-po-txn-count-volume');
	Route::get('get-pending-complain','DashboardController@getPendingComplain')->name('get-pending-complain');
	Route::get('txn-with-commission','ReportController@txnWithCommission')->name('txn-with-commission');
	Route::get('api-report','ReportController@apiReport')->name('api-report');
	Route::get('operator-report','ReportController@operatorReport')->name('operator-report');
	Route::get('count-txn-with-volume', 'DashboardController@getSPFTxnCountVolume');
	Route::get('get-balance-request','DashboardController@getBalanceTxnCountVolume')->name('get-balance-request');
	Route::get('get-role-balance','DashboardController@getRoleWiseBalance')->name('get-role-balance');
	Route::get('get-api-balance','DashboardController@getApiBalance')->name('get-api-balance');
	
	/*bank detail*/
	Route::get('master-bank-detail','MasterBankController@index')->name('master-bank-detail');
	Route::post('master-bank-detail','MasterBankController@store')->name('master-bank-detail');
	Route::get('master-bank-view/{id}','MasterBankController@view')->name('master-bank-view');
	Route::post('master-bank-detail/{id}','MasterBankController@update')->name('update-master-detail');
	Route::post('delete-masterbank-details','MasterBankController@deleteDetails')->name('delete-masterbank-details');

	Route::get('company-bank-details','CompanyBankDetailController@index')->name('company-bank-details');
	Route::get('bank-details-view/{id}','CompanyBankDetailController@view')->name('bank-details-view');
	Route::post('company-bank-details','CompanyBankDetailController@store')->name('save-bank-details');
	Route::post('delete-bank-details','CompanyBankDetailController@deleteDetails')->name('delete-bank-details');
	Route::put('company-bank-details/{id}','CompanyBankDetailController@update')->name('update-bank-details');
	
	Route::get('lower-level-bank-details','CompanyBankDetailController@lowerLevelBankDetails')->name('lower-level-bank-details');
	Route::get('upper-level-bank-details','CompanyBankDetailController@upperLevelBankDetails')->name('upper-level-bank-details');
	/*end*/
	
	/*holiday*/
    Route::get('holiday','HolidayController@index')->name('holiday');
    Route::get('holiday/{id}','HolidayController@view')->name('holiday-view');
    Route::put('holiday/{id}','HolidayController@update')->name('holiday-update');
    Route::post('holiday','HolidayController@store')->name('holiday-store');
    /*end*/ 

	Route::get('bill-scheme-list', 'WalletSchemeController@getBillScheme')->name('bill-scheme-list');
	Route::post('bill-pay-commission-update', 'CommissionController@billViewScheme')->name('bill-view-scheme');
	Route::post('updateBillScheme','WalletSchemeController@updateBillScheme');
	Route::post('updateVerificationScheme','WalletSchemeController@updateVerificationScheme');
	
	Route::post('create-new-row','WalletSchemeController@createNewDGOneRow')->name('create-new-row');
	Route::post('create-impsone-row','WalletSchemeController@createNewIMPSOneRow')->name('create-impsone-row');
	Route::post('create-instant-pay-row','WalletSchemeController@createInstantPayOneRow')->name('create-instant-pay-row');
	Route::post('create-bill-pay-row','WalletSchemeController@createBillOneRow')->name('create-bill-pay-row');
	Route::post('create-verification-row','WalletSchemeController@createVerificationOneRow')->name('create-verification-row');
	Route::post('delete-row','WalletSchemeController@deleteRow')->name('delete-row');
	/*payments*/
	Route::get('/fund-transfer','TransferController@index')->name('fund-transfer');
	Route::get('/dist-fund-transfer','TransferController@distFundList')->name('dist-fund-transfer');
	Route::get('/api-user-fund-transfer','TransferController@apiUserFundList')->name('api-user-fund-transfer');
	Route::get('/md-fund-transfer','TransferController@mdFundList')->name('md-fund-transfer');
	Route::post('/fund-transfer/','TransferController@store');
	Route::get('/fund-return','TransferController@distFundReturnList')->name('fund-return');
	Route::get('/dist-fund-return','TransferController@distFundReturnList')->name('dist-fund-return');
	Route::get('/md-fund-return','TransferController@distFundReturnList')->name('md-fund-return');
	Route::get('/api-fund-return','TransferController@distFundReturnList')->name('api-fund-return');
	Route::post('/fund-transfer-return', 'TransferController@fund_return_account');
	Route::get('/admin/purchase-balance','MemberController@purchaseBalanceReports');
	Route::post('/admin/purchase-balance','MemberController@purchaseBalance');
	Route::get('/payment-request-view','HomeController@payment_request_view');
	Route::get('/fund-request-report','ReportController@fund_request_report');
	Route::get('/dowline-request-report','ReportController@downLineRequestReport');
	Route::get('/payment-report','ReportController@payment_report');
	Route::get('/md-payment','ReportController@paymentReportRoleWise');
	Route::get('/dist-payment','ReportController@paymentReportRoleWise');
	Route::get('/retailer-payment','ReportController@paymentReportRoleWise');
	Route::get('/api-payment','ReportController@paymentReportRoleWise');
	Route::get('/payment_report_search','ReportController@payment_report_search');
	Route::get('/payment-transfer-report','TransferController@paymentTransferReport')->name('payment-transfer-report');
	/*end*/
	
	/*Accounting*/
	Route::get('businessview','UserReportController@tranactionView')->name('businessview');
	Route::get('account/operator-report','UserReportController@operatorReport')->name('account/operator-report');
	/*end*/
	
	/*send Sms to all member*/
	Route::get('send-sms','SendSmsController@index')->name('send-sms');
	Route::post('send-sms-memberwise','SendSmsController@sendSmsMemberWise')->name('send-sms-memberwise');
	Route::post('send-sms-rolewise','SendSmsController@sendSmsRoleWise')->name('send-sms-rolewise');
	/*end Sms*/
	
	/*Bank Down*/
	Route::group(['prefix' => 'tools'], function () {
	Route::get('bankupdown',  'MemberController@bnk_updown');
	Route::get('check-bank-status',  'MasterBankController@checkBankStatus');
	Route::get('update-txn-allowed',  'MasterBankController@updateImpsService');
	});
	Route::get('check-is-bank-down','InstantPayController@isBankDownOrNot');
	Route::post('make-bank-updown', 'MemberController@makeBankUpDown');
	/*end Bank Down*/
	
	/*API User*/
	Route::get('/admin/APImanage','SecurityController@api_manage');
	Route::get('get-user-detail/{apiUserId}','SecurityController@getApiUserIps');
	Route::post('save-get-user-detail','SecurityController@saveUserDetail');
	/*End Api User*/
	
	/* Member */
	Route::get('admin/api-member','MemberController@api_retailer');
	Route::get('/member/view', 'MemberController@view');
	Route::post('/member/{id}', 'MemberController@update');
	Route::post('/member/','MemberController@store');
	Route::get('/admin/otp', 'MemberController@member_otp')->name('admin/otp');
	Route::get('/admin/bankupdown',  'MemberController@bnk_updown');
	Route::patch('company/{company_id}', 'CompanyController@update');
	Route::put('memberdp/{user_id}', 'MemberController@updatePassword');
	Route::get('admin/all-members', 'MemberController@index');
	Route::put('admin/make-disable-agent/{user_id}', 'MemberController@makeUserActiveDeactive');
	Route::get('accountSetting','MemberController@accountSetting')->name('accountSetting');
	Route::get('gstinfo', 'MemberController@gstInfo')->name('gstinfo');
	/* NEw */
	Route::get('payment-request-view/getRecords','HomeController@getRecords');
	Route::post('/payment-request-view/view','HomeController@view');
	Route::put('/payment-request-view/{id}','HomeController@update');
	Route::get('user-recharge-report/{id}','ReportController@user_recharge_report')->name('user-reports');
	
	/*report*/
	Route::get('tds-reports','ReportController@tdsReport')->name('tds-reports');
	Route::get('export_data','ReportController@exportUserRecord');
	Route::get('recharge-nework','ReportController@all_recharge_report')->name('all-transaction-reports');
	Route::get('searchall_money','ReportController@searchall_money');
	Route::get('recharge-searchall','ReportController@recharge_searchall');
	Route::get('agent-report','ReportController@agent_report');
	Route::get('searchall_agent','ReportController@searchall_agent');
	Route::get('search-account-statement','ReportController@search_account_statement');
	Route::get('export-account-statement','ReportController@export_account_statement');
	 Route::get('daily-balance-reports','ReportController@dailyMaintainBalance')->name('daily-balance-reports');
	/*end*/
	/*for business*/
	Route::get('/business-recharge','BusinessController@recharge')->name('business-recharge');
	Route::get('/business-dmt','BusinessController@dmt')->name('business-dmt');
	Route::get('/business-travel','BusinessController@travel')->name('business-travel');
	Route::get('/business-aeps','BusinessController@aeps')->name('business-aeps');
	Route::get('/business-pancard','BusinessController@pancard')->name('business-pancard');
	Route::get('/business-mpos','BusinessController@mpos')->name('business-mpos');
	Route::get('/business-irctc','BusinessController@irctc')->name('business-irctc');
	Route::get('/business-giftvoucher','BusinessController@giftvoucher')->name('business-giftvoucher');
	/*end business*/
	/*for complain*/
	Route::get('view-complain','ComplainController@complain_request_view')->name('view-complain');
    Route::get('recharge-complain-request-view','ComplainController@recharge_complain_request_view');
    Route::get('complain-request-update','ComplainController@update');
    Route::get('complain-request-delete','ComplainController@delete_req');
    Route::get('agent-request-update','ComplainController@agentUpdate');
    Route::get('agent-request-delete','ComplainController@agentDelete');
	/*end complain*/
	/*accounting*/
	Route::get('api_balance','ApiController@api_balance');
	Route::get('show-revenue-expenses','UserReportController@showRevenueExpenses');
	Route::get('/admin/user-lists','MemberController@userLists');
	/*end*/
	//Route::get('offline','OfflineRecordController@index');
	Route::get('admin/offline/record','ReportController@offlineRecord')->name('offline-record');
	Route::get('admin/offline/updated-record','ReportController@offlineUpdatedRecord')->name('offline-updated');
	//Route::get('/offine-report', 'OfflineRecordController@view')->name('offine-report');
	//Route::put('/offine-report-update/{id}', 'OfflineRecordController@update')->name('offine-report-update');
	
	Route::post('loadcash','HomeController@requestloadcash');
	Route::get('dist-business-report','AnalyticController@distBusinessReport');
	/* Service */
	Route::post('/add-service','ActiveServiceController@addServices')->name('add-service');
	Route::put('edit-services/{apiId}', 'ActiveServiceController@makeActiveInactiveServices');
	Route::get('service-management','ActiveServiceController@index')->name('servicemanagement');
	Route::post('update-services/{id}', 'ActiveServiceController@update');
	
	Route::get('dmt-one-report','ReportController@DMTOneTransactionReport')->name('dmt-one-report');
	Route::get('dmt-two-report','ReportController@DMTTwoTransactionReport')->name('dmt-two-report');
	Route::get('recharge-reports', 'ReportController@rechargeReport')->name('recharge-reports');

	Route::get('network-chain','MemberController@networkChain')->name('network-chain'); 
	Route::get('network-search','MemberController@networkSearch')->name('network-search'); 
	Route::get('network-chain/{parent_id}','MemberController@networkViewChain')->name('network-view-chain'); 
	Route::get('network-admin-dist-chain/{parent_id}','MemberController@networkViewChain')->name('network-admin-dist-chain'); 
	Route::get('network-admin-agent-chain/{parent_id}','MemberController@networkViewChain')->name('network-admin-agent-chain'); 
	Route::get('view-network','ReportController@viewNetwork')->name('view-network'); 
	
	/* Route::get('instant-mobile-verification','InstantPayController@mobileVerificaton')->name('instant-mobile-verification'); 
	Route::get('instant/add-sender','InstantPayController@remitterRegister')->name('instant-add-sender'); 
	Route::get('instant/sender-verification','InstantPayController@remitterVerification')->name('instant-sender-verification');
	Route::post('instant/account-verification','InstantPayController@verifyAccountNumber')->name('instant-account-verification');
	Route::post('instant/add-beneficiary','InstantPayController@beneAdd')->name('instant-add-beneficiary'); */
	Route::get('dmt-two','InstantPayController@index')->name('dmt-two'); 
	Route::get('instant-mobile-verification','InstantPayController@mobileVerificaton')->name('instant-mobile-verification'); 
	Route::get('instant/add-sender','InstantPayController@remitterRegister')->name('instant-add-sender'); 
	Route::get('instant/sender-verification','InstantPayController@remitterVerification')->name('instant-sender-verification');
	Route::post('instant/account-verification','InstantPayController@verifyAccountNumber')->name('instant-account-verification');
	Route::post('instant/add-beneficiary','InstantPayController@beneAdd')->name('instant-add-beneficiary');
	Route::post('instant/bene_confirm','InstantPayController@beneVerification')->name('instant-beneficiary-verification');
	Route::get('instant/resend-bene-otp','InstantPayController@resendBeneVerificationOtp')->name('instant/resend-bene-otp');
	Route::get('instant/delete_beneficiary','InstantPayController@deleteBeneficiary');
	Route::get('instant/confirm-bene-delete','InstantPayController@confirmBeneDelete');
	Route::post('instant-api/transaction','InstantPayController@transaction');
	Route::get('admin/login-history','UserLoggedInHistoryController@getLoggedInHistory')->name('admin/login-history');
	Route::post('cyber-api/transaction', 'MoneyController@transaction');
	
});
Route::get('privacy-policy', function () {
    return view('privacy-policy');
});


/*reforts*/
/*Route::get('all-transaction-reports', 'ReportController@all_transaction_report'])->name('all-transaction-reports');*/

/*End*/

/*------------------Transaction-------------------*/


/*mobile app api*/





Route::group(['prefix' => 'mobileapp/api'], function () {
    //login
	Route::post('verify-device', 'Mobile\LoginController@verifyNewMobileDevice');
    Route::post('resend-device-otp', 'Mobile\LoginController@resendOTP');
    Route::post('agentLogin', 'Mobile\LoginController@agentLogin');
	Route::post('login/system-verificaiton', 'Mobile\LoginController@systemVerification');
    Route::post('fund-return', 'Mobile\MoneyController@fundReturn');
    Route::get('distfundlist', 'Mobile\MoneyController@distFundList');
    Route::post('fund-transafer', 'Mobile\MoneyController@fundTransafer');
    Route::get('agent-request-view', 'Mobile\MoneyController@agentRequestView');
    Route::get('get-loadcash-record', 'Mobile\MoneyController@getLoadCashRecord');
    Route::get('retailer-fund-transfer', 'Mobile\MoneyController@retailerFundTransfer');
    Route::get('get-imps-status', 'Mobile\CheckStatusController@checkTransactionCurrentStatus');
    Route::get('admin-dashboard', 'Mobile\DashboardController@adminDashboard');
    Route::get('get-remaining-balance', 'Mobile\CheckStatusController@getRemainingBalance');
    Route::get('get-news', 'Mobile\CheckStatusController@getNews');
    Route::post('update-news', 'Mobile\CheckStatusController@updateNew');
    Route::post('api-balance', 'Mobile\ApiController@api_balance');
    Route::post('get-active-services', 'Mobile\ActiveServiceController@getActiveServices');
    Route::post('make-active-inactive-services', 'Mobile\ActiveServiceController@makeActiveInactiveServices');


    Route::get('page-refresh', 'Mobile\CommanController@refreshPage'); 
    Route::post('money-transfer-a2z-wallet', 'Mobile\PremiumWalletController@transaction');
    Route::post('money-transfer-dmt1', 'Mobile\MoneyController@transaction');
    Route::group(['prefix' => 'premium'], function () {

        Route::get('mobile-verification', 'Mobile\PremiumWalletController@mobileVerificaton');
        Route::post('mobile-verification-otp', 'Mobile\PremiumWalletController@mobileVerifiedWithOTP');
        Route::post('remitter-registration', 'Mobile\PremiumWalletController@remitterRegister');
        Route::get('get-beniList', 'Mobile\PremiumWalletController@getBeniList');
        Route::post('delete-beneficiary', 'Mobile\PremiumWalletController@deleteBeneficiaryRequest');
        Route::post('delete-beneficiary-otp', 'Mobile\PremiumWalletController@deleteBeneficiaryThroughOtp');
        Route::post('get-bank-list', 'Mobile\PremiumWalletController@getBankList');
        Route::post('verify-account-number', 'Mobile\PremiumWalletController@verifyAccountNumber');
        Route::post('bene-add', 'Mobile\PremiumWalletController@beneAdd');

    });
    Route::group(['prefix' => 'money'], function () {
        Route::get('index', 'Mobile\MoneyController@index');
        Route::get('mobile-verification', 'Mobile\MoneyController@fetchCustomerDetails');
        Route::post('sender-registration', 'Mobile\MoneyController@senderRegistration');
        Route::post('sender-verification', 'Mobile\MoneyController@senderVerification');
        Route::post('get-bankList', 'Mobile\MoneyController@getBankList');
        Route::post('add-beneficiary', 'Mobile\MoneyController@add_beneficiary');
        Route::post('account-name-info', 'Mobile\MoneyController@account_name_info');
        Route::post('add_beneficiary', 'Mobile\MoneyController@add_beneficiary');
        Route::post('delete-beneficiary', 'Mobile\MoneyController@beneficiaryDelete');
        Route::post('delete-beneficiary-otp', 'Mobile\MoneyController@beneficiaryDeleteOtp');
        Route::get('get-agent-charge-amt', 'Mobile\MoneyController@getAgentChargeAmount');
        Route::get('check-bank-down', 'Mobile\MoneyController@isBankDownOrNot');

    });
    Route::group(['prefix' => 'instant'], function () {
        Route::get('mobile-verification', 'Mobile\InstantPayController@mobileVerification');
        Route::post('register-remitter', 'Mobile\InstantPayController@remitterRegister');
        Route::post('remitter-verification', 'Mobile\InstantPayController@remitterVerification');
		Route::post('otp-remitter-verification-resend','Mobile\InstantPayController@mobileVerificaton');
        Route::post('add-beneficiary', 'Mobile\InstantPayController@beneAdd');
        Route::get('get-bank-ifsc', 'Mobile\InstantPayController@getBankNameIfscCode');
        Route::post('delete_beneficiary', 'Mobile\InstantPayController@deleteBeneficiary');
        Route::post('confirm-bene-delete', 'Mobile\InstantPayController@confirmBeneDelete');
        Route::post('transaction', 'Mobile\InstantPayController@transaction');
    });

    //Aeps
    Route::get('get-aeps-bank-list', 'Mobile\AepsController@aepsBankList');
	Route::post('aeps-transaction', 'Mobile\AepsController@aepsTransaction');

    //recharge and electricity
    Route::post('make-recharge', 'Mobile\RechargeController@cyberRecharge');
    Route::get('get-recharge-provider', 'Mobile\RechargeController@getRechargeProvider');
    Route::post('make-electricity-recharge', 'Mobile\ElectricityController@store');
    Route::get('fetch-bill-amount', 'Mobile\ElectricityController@fetchBillAmount');


    //reports
    Route::get('fund-report', 'Mobile\ReportController@fundReport');
    Route::get('ledger-report', 'Mobile\ReportController@ladgerReport');
    Route::get('recharge-report-ad', 'Mobile\ReportController@all_recharge_report');
    Route::get('all_recharge_report', 'Mobile\ReportController@allRechargeReport');
    Route::get('summary-report', 'Mobile\ReportController@summaryReport');
    Route::get('get-transation-details', 'Mobile\ReportController@getTransationDetails');
    Route::get('get-account-statement', 'Mobile\ReportController@accountStatement');
    Route::get('get-recharge-report', 'Mobile\ReportController@rechargeReportDistributor');
    Route::post('update-transaction', 'Mobile\ReportController@updateTransaction');
	Route::get('report-slip', 'Mobile\ReportController@reportSlip');
    //(commission view)
    Route::get('view-retailer-commission', 'Mobile\ReportController@getRetailerCommission');
    Route::get('view-distributor-commission', 'Mobile\ReportController@getDistributorCommission');
    Route::get('view-admin-commission', 'Mobile\ReportController@getAdminCommission');
	Route::get('get-direct-fund-transfer', 'Mobile\ReportController@directTransferReport');


    //payments
    Route::post('request-fund-payment', 'Mobile\PaymentController@requestloadcash');
    Route::get('get-paymentmethodbanklist', 'Mobile\PaymentController@getPaymentMethodBankList');
    Route::get('get-bank-list', 'Mobile\PaymentController@getBankList');
    Route::post('fund-request-save', 'Mobile\PaymentController@fundRequestSave');
    Route::get('get-retailer-detail', 'Mobile\PaymentController@getRetailerDetail');
    Route::get('verify-pin', 'Mobile\PaymentController@verifyPin');
    Route::post('fund-transfer_r2r', 'Mobile\PaymentController@fundTransferR2r');
    Route::get('payment-report', 'Mobile\PaymentController@paymentReport');
	Route::get('fund-transfer-report', 'Mobile\PaymentController@fundTransferReport');
    Route::get('agent-request-approve', 'Mobile\PaymentController@agentRequestApprove');

    //member
    Route::get('get-members', 'Mobile\MemberController@getMembers');
    Route::post('user-deactivate', 'Mobile\MemberController@makeUserActiveDeactivate');
    Route::post('purchase-balance', 'Mobile\MemberController@purchaseBalance');
    Route::get('get-state-list', 'Mobile\MemberController@getStateList');
    Route::post('create-retailer', 'Mobile\MemberController@store');


    //password
    Route::post('change-password', 'Mobile\PasswordController@change_password');//
    Route::post('generate-new-pin', 'Mobile\PasswordController@generateOtpForPin');
    Route::post('get-generated-pin', 'Mobile\PasswordController@generateTransactionpin');
    Route::post('forget-password', 'Mobile\PasswordController@passwordReset');
    Route::post('store-password', 'Mobile\PasswordController@store');

    Route::get('complain-request-view', 'Mobile\ComplainController@complainRequestView');
    Route::get('get-bank-details', 'Mobile\CompanyBankDetailController@getBankDetails');
    Route::get('get-retailer-dashboard', 'Mobile\DashboardController@retailerDashboard');

 //offers
    Route::get('get-special-offer','Mobile\ROfferController@getSpecialNumberOffer');
	
 

});



