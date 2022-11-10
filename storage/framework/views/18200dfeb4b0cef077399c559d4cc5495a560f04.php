<?php $__env->startSection('content'); ?>
 <style type="text/css">
 .hideTxtField{
	 display:none;
 }
        
    </style>
<head>
		<script src="https://code.jquery.com/jquery-2.1.1.min.js" type="text/javascript"></script>
		<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.1/css/select2.min.css" rel="stylesheet" />
		<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.1/js/select2.min.js"></script>
		<script type="text/javascript">
			$(document).ready(function() {
				$.noConflict();
				var country = [];
				$("#bankName").select2({
				  data: country
				});
			});
		</script>
	</head>



        <div>
		 <?php echo $__env->make('agent.aepsSettlement.aepsSettlement-type', \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>
			<?php echo Form::open(array('url' =>'#','id'=>'myImageForm')); ?>

		<div class="form-group col-md-12 row">
							<div class="col-md-12">
                            <label for="inputTask" class="control-label">Biometric Device</label>
                            <div class="">
								<select name="deviceName" id="deviceName" class="form-control" onChange="loadDevicepage()">
									<option value="MANTRA">MANTRA</option>
									<option value="MORPHO" selected>MORPHO</option>
                                </select>
                            </div>
							</div>
							
                        </div> 
						<div class="form-group col-md-12 row">
							
							<div class="col-md-12">
							<label for="inputTask" class="control-label">Bank Name</label>
                            <div class="">
                                <?php echo e(Form::select('bankName', $bankLists,null, ['id'=>'bankName','class'=>'form-control','placeholder'=>'-- Select Bank--'])); ?>

                            </div>
							</div>
							<input type="hidden" name="selectedBankName" id="selectedBankName"/>
							<input type="hidden" name="serialNumber" id="serialNumber"/>
                        </div>
						<div class="form-group col-md-12 row">
							<div class="col-md-12">
							<label class="control-label" for="email">Service:</label>
							<div class="">
								<select class="browser-default form-control" id="transactionType" name="transactionType" onChange="openBalanceDiv()">
								  <option value="BE">Enquiry</option>
								  <option value="CW">Widthdrawn</option>
							</select>
							</div>
						</div>
						</div>
						<div class="form-group col-md-12 row" style="display:none" id="balanceDiv">
						<div class="col-md-12">
							<label class="control-label" for="email">Amount</label>
							<div class="">
								<input type="text" class="form-control" id="amount" name="amount" placeholder="Enter Min Amount 100" value="0">
							</div>
							</div>
						</div>						
						<div class="form-group col-md-12 row">
							<div class="col-md-12">
                            <label for="inputTask" class="control-label">AadhaarNumber</label>
                            <div class="">
								<?php if(Auth::id()==4): ?>
									<input name="aadhaarNumber" type="text" class="form-control" id="aadhaarNumber" value="448014574212" placeholder="Enter Aadhaar Number" maxlength="12"/>
								<?php else: ?>
									<input name="aadhaarNumber" type="text" class="form-control" id="aadhaarNumber" value="" placeholder="Enter Aadhaar Number" maxlength="12"/>
								<?php endif; ?>
                            </div>
							</div>
							
                        </div>
						<div class="form-group col-md-12 row">
							<div class="col-md-12">
                            <label for="inputTask" class="control-label">Customer Number</label>
                            <div class="">
							<?php if(Auth::id()==4): ?>
								<input name="customerNumber" type="text" class="form-control" id="customerNumber" value="8285540407" placeholder="Enter Customer Number" maxlength="10">
							<?php else: ?>
							
								<input name="customerNumber" type="text" class="form-control" id="customerNumber" value="" placeholder="Enter Customer Number" maxlength="10">
								<?php endif; ?>
                            </div>
							</div>
							
                        </div> 
						 <textarea id="txtPidData" name="txtPidData"style="width: 100%; height: 150px;display:none" class="form-control" > </textarea>
						<div class="form-group col-md-12 row">
							<div class="col-md-12">
							<!-- <input class="btn btn-primary btn-200" onclick="discoverAvdm();" type="button" id="checkDeviceBank" value="Check Your Device">
							 <img src="<?php echo e(url('loader/loader.gif')); ?>" id="checkDeviceImage" class="loaderImg" style="display: none;">-->
							 <div id="scanDeviceDiv" style="display:block">
								<input type="button" class="btn btn-success btn-200" onclick="DeviceInfo();" value="Scan Finger" id="scanDeviceBtn"/>
								<img src="<?php echo e(url('loader/loader.gif')); ?>" id="scanDeviceImage" class="loaderImg" style="display: none;">
								<img src="<?php echo e(url('img/scan_fp.gif')); ?>" class="" style="display: none;width: 15%;
    border-radius: 15%;" id="captureDeviceLoader" alt="Image">
								<img src="<?php echo e(url('img/newLoader.gif')); ?>" class="" style="display: none;width: 10%;
" id="captureDeviceSuccessLoader" alt="Image">
								<a data-toggle="modal" href="#myReciept" class="table-action-btn" id="printAnchorSlip" style="display:none">Action</a>
							</div>
							 <!--<div id="scanDeviceDiv" style="display:none">
								<input type="button" class="btn btn-success btn-200" onclick="CaptureAvdm();" value="Scan Finger" id="scanDeviceBtn"/>
								<img src="<?php echo e(url('loader/loader.gif')); ?>" id="scanDeviceImage" class="loaderImg" style="display: none;">
							</div>-->
							<div class="scanDeviceDiv">
							
                        </div> 						
						</div>
						</div></div>
    <script language="javascript" type="text/javascript">

	$(document).ready(function (){ 	

			$('#closemodal').click(function() {
							location.reload();
				});
			});
		

function amountInWords()
	{
		var amount=$("#amount").val();		
		var txnChargeAmout=$("#txnChargeAmout").val();	
		var amountFormat = /^[0-9]+$/;
		if(!txnChargeAmout.match(amountFormat) || txnChargeAmout =='')
		{
			$('#txnChargeAmout').focus();
			$('#slipAmountInWord').css('color','red');
			$('#slipAmountInWord').text('Please Enter Correct Amount');
			return false;
		}		
		var finalAmount = +amount + +txnChargeAmout;
		$.ajax({
                type: "get",
                url: "<?php echo e(url('amount-in-words')); ?>",
                data: "amount="+finalAmount,
				dataType:"json",
				beforeSend:function(){
					
					$("#updageFeeSpan").hide();
					
				},
                success: function (msg) 
				{
					$('#slipAmountInWord').css('color','black');
					console.log(msg);
					//$("#spanChargeAmount").text(txnChargeAmout);
					$("#slipAmountInWord").text(msg);
					$("#spanChargeAmount").text(finalAmount);
					
				}
            });
		
	}

function openBalanceDiv()
		{
			if($("#transactionType").val() == "BE")
				$("#balanceDiv").hide();
			else
				$("#balanceDiv").show();
		}
		function loadDevicepage()
		{
			if($("#deviceName").val() == "MANTRA")
				window.location.replace("<?php echo e(url('aeps')); ?>");
			else
				window.location.replace("<?php echo e(url('morpho-aeps')); ?>");
		}

		function test()
		{
			alert("I am calling..");
		}

function DeviceInfo()
{

	var url = "http://127.0.0.1:11100/getDeviceInfo";

	var xhr;
	var ua = window.navigator.userAgent;
	var msie = ua.indexOf("MSIE ");
			var bankName = $("#bankName").val();
			var selectedBankName = $("#bankName option:selected").html();
			$("#selectedBankName").val(selectedBankName);
			var deviceName = $("#deviceName").val();
			var aadhaarNumber = $("#aadhaarNumber").val();
			var customerNumber = $("#customerNumber").val();
			var adhar_number_pat = /^[0-9]+$/;
			var mobile_pattern = /^[6789]\d{9}$/;
			var numberPattern = /^[0-9]+$/;
			
			if(bankName=='')
			{
				alert("Please Select Bank");
				return false;
			}else if(aadhaarNumber=='')
			{
				alert("Please Enter Aadhaar Number");
				$('#aadhaarNumber').focus();
				return false;
			}else if(aadhaarNumber.length != 12 )
			{
				alert('Please enter 12 digits aadhaar number ');
				$('#aadhaarNumber').focus();
				return false;
			}else if(!aadhaarNumber.match(adhar_number_pat) )
			{
				alert('Please enter correct aadhaar number ');
				$('#aadhaarNumber').focus();
				return false;
			}
			else if(customerNumber=='')
			{
				alert("Please enter customer mobile number");
				$('#customerNumber').focus();
				return false;
			}
			else if(customerNumber.length != 10)
			{
				alert("Please enter 10 digits mobile number");
				$('#customerNumber').focus();
				return false;
				
			}
			else if(!customerNumber.match(mobile_pattern))
			{
				alert('Invalid Mobile Number');
				$('#customerNumber').focus();
				return false;
			}
			if($("#transactionType").val() == "CW")
			{
					var amount =$('#amount').val();
					$('#spanChargeAmount').html(amount);
					if(amount == '')
					{
						alert("Please Enter amount");
						$('#amount').focus();
						return false;
						
					}
					else if(!amount.match(numberPattern))
					{
						alert("Please Enter valid aadhaar card number");
						$('#adhar_number').focus();
						return false;
						
					}
					else if(amount <1)
					{
						alert("Amount should be greater than 1");
						$('#amount').focus();
						return false;
						
					}
				
			}
	if (msie > 0 || !!navigator.userAgent.match(/Trident.*rv\:11\./)) // If Internet Explorer, return version number
	{
	//IE browser
		xhr = new ActiveXObject("Microsoft.XMLHTTP");
	} else {
	//other browser
		xhr = new XMLHttpRequest();
	}
	$("#scanDeviceBtn").hide();
	$("#captureDeviceLoader").show();
	//
	xhr.open('DEVICEINFO', url, true);
	xhr.onreadystatechange = function () {
		// if(xhr.readyState == 1 && count == 0){
		//	fakeCall();
		//}
		if (xhr.readyState == 4){
			var status = xhr.status;

			if (status == 200) 
			{
				let parser=new DOMParser();
				let xml = parser.parseFromString(xhr.response,"application/xml");
				var deviceInfo=xml.getElementsByTagName('DeviceInfo')[0]; 
				var additional_info = deviceInfo.getElementsByTagName('additional_info')[0];
				var Param = additional_info.getElementsByTagName('Param')[0];
				var name = additional_info.getElementsByTagName('Param')[0].getAttribute("name");
				var value = additional_info.getElementsByTagName('Param')[0].getAttribute("value");
					if(value==''|| value==null)
					{
						$("#scanDeviceBtn").show();
						$("#captureDeviceLoader").hide();
						alert("Device is not ready");
						return false;
					}
					$("#serialNumber").val(value)
					Capture()
				
			} else {

			console.log(xhr.response);
			}
		}

	};

	xhr.send();
}
		
		function Capture()
		{
			var bankName = $("#bankName").val();
			var selectedBankName = $("#bankName option:selected").html();
			$("#selectedBankName").val(selectedBankName);
			var deviceName = $("#deviceName").val();
			var aadhaarNumber = $("#aadhaarNumber").val();
			var customerNumber = $("#customerNumber").val();
			var adhar_number_pat = /^[0-9]+$/;
			var mobile_pattern = /^[6789]\d{9}$/;
			var numberPattern = /^[0-9]+$/;
			
			if(bankName=='')
			{
				alert("Please Select Bank");
				return false;
			}else if(aadhaarNumber=='')
			{
				alert("Please Enter Aadhaar Number");
				$('#aadhaarNumber').focus();
				return false;
			}else if(aadhaarNumber.length != 12 )
			{
				alert('Please enter 12 digits aadhaar number ');
				$('#aadhaarNumber').focus();
				return false;
			}else if(!aadhaarNumber.match(adhar_number_pat) )
			{
				alert('Please enter correct aadhaar number ');
				$('#aadhaarNumber').focus();
				return false;
			}
			else if(customerNumber=='')
			{
				alert("Please enter customer mobile number");
				$('#customerNumber').focus();
				return false;
			}
			else if(customerNumber.length != 10)
			{
				alert("Please enter 10 digits mobile number");
				$('#customerNumber').focus();
				return false;
				
			}
			else if(!customerNumber.match(mobile_pattern))
			{
				alert('Invalid Mobile Number');
				$('#customerNumber').focus();
				return false;
			}
			if($("#transactionType").val() == "CW")
			{
					var amount =$('#amount').val();
					$('#spanChargeAmount').html(amount);
					if(amount == '')
					{
						alert("Please Enter amount");
						$('#amount').focus();
						return false;
						
					}
					else if(!amount.match(numberPattern))
					{
						alert("Please Enter valid aadhaar card number");
						$('#adhar_number').focus();
						return false;
						
					}
					else if(amount <1)
					{
						alert("Amount should be greater than 1");
						$('#amount').focus();
						return false;
						
					}
				
			}
			if(deviceName=="MANTRA")
			{
				var urlnew="http://127.0.0.1:11100/rd/capture";
				var XML='<?php echo '<?xml version="1.0"?>'; ?> <PidOptions version="1.0"> <Opts fCount="1" fType="0" iCount="" pCount="" format="0"   pidVer="0" timeout="10000" wadh="" posh="" env="X" /> <CustOpts><Param name="mantrakey" value="" /></CustOpts> </PidOptions>';
			}
			else if(deviceName=="MORPHO")
			{
				var urlnew="http://127.0.0.1:11100/capture";
				XML='<PidOptions ver=\"1.0\">'+'<Opts fCount=\"1\" fType=\"0\" iCount=\"\" iType=\"\" pCount=\"\" pType=\"\" format=\"0\" pidVer=\"2.0\" timeout=\"10000\" otp=\"\" wadh=\"\" posh=\"\"/>'+'</PidOptions>';
			}
			var xhr;//=createCORSRequest('CAPTURE',url);
            var ua = window.navigator.userAgent; 
            var msie = ua.indexOf("MSIE");
            if (msie > 0 || !!navigator.userAgent.match(/Trident.*rv\:11\./)) // If Internet Explorer, return version number
            {
	//IE browser
                xhr = new ActiveXObject("Microsoft.XMLHTTP");
            } else {
	//other browser
                xhr = new XMLHttpRequest();
            }
			xhr.open('CAPTURE', urlnew, true);
            xhr.setRequestHeader("Content-Type", "text/xml");
            xhr.setRequestHeader("Accept", "text/xml");
            if (!xhr) 
			{
                toastr["success"]('Error for accessing device');
                console.log('CORS not supported');
                return
            }
			xhr.onreadystatechange = function () 
			{
				
				console.log(xhr);
				console.log(xhr.readyState);
                if (xhr.readyState == 4) 
				{
                    var status = xhr.status;
					console.log(status);
                    if (status == 200) 
					{
                        xhr.response
						$('#txtPidData').val(xhr.response);
						console.log("-----")
						console.log( xhr.response);
						let parser=new DOMParser();
						let xml = parser.parseFromString(xhr.response,"application/xml");
						var pidContent=xml.getElementsByTagName('PidData')[0]; 
						var responseCode = pidContent.getElementsByTagName('Resp')[0].getAttribute("errCode");
						var errInfo = pidContent.getElementsByTagName('Resp')[0].getAttribute("errInfo");
						if(responseCode==700 || responseCode=="700")
						{
							$("#scanDeviceBtn").show();
							$("#captureDeviceLoader").hide();
							alert(errInfo +" Please try again");
							//location.reload()
							return false;
						}
						else if(responseCode== -1509 || responseCode=="-1509")
						{
							$("#scanDeviceBtn").show();
							$("#captureDeviceLoader").hide();
							alert(errInfo +" Please try again");
							//location.reload()
							return false;
						} 
						else if(responseCode !=0)
						{
							$("#scanDeviceBtn").show();
							$("#captureDeviceLoader").hide();
							errorMessage = errInfo +" Please try again";
							alert(errorMessage)
							//swal("Failure", errorMessage, "error");
							//setTimeout(function(){ location.reload(); }, 3000);
							return false;
						}
						$.ajaxSetup({
							headers: {
							'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
							}
						})
						var uploadfile = $('#myImageForm').serialize()
						$.ajax({
							type: "post",
							url: "<?php echo e(url('send-aeps-request')); ?>",
							data: uploadfile,
							datatype: "json",
							beforeSend:function()
							{
								$("#captureDeviceLoader").hide();
								$("#captureDeviceSuccessLoader").show();
								
							},
							success: function (res) {
								$("#scanDeviceBtn").show();
								$("#captureDeviceSuccessLoader").hide();
								if(res.status=="Success"||res.status=="Pending")
								{
									$('#printAnchorSlip')[0].click();
									refreshBalance();
									var content = "<tr><td>"+res.txnTime+"</td><td>Aeps</td><td>"+res.fpTransactionId+"</td><td>"+res.bankRRN+"</td><td>"+res.transactionAmount+"</td><td>"+res.availableBalance+"</td><td>"+res.status+"</td></tr>";
									
									$("#slipAadhaarNumber").html(aadhaarNumber);
									$("#slipCustomerNumber").html(customerNumber);
									$("#slipBankName").html(selectedBankName);
									/* $("#slipTxnAmount").html(res.transactionAmount);
									$("#slipAvailableBalance").html(res.availableBalance);
									$("#slipTxnId").html(res.fpTransactionId);
									$("#slipBankRefNumber").html(res.bankRRN); */
									$("#slipTxnType").html(res.transactionType);
									$("#slipStatus").html(res.status);
									$("#txnPrintSlip").html(content);
									//$("#myReciept").modal("toggle");
								}
								else
								{
									alert(res.message);
									$("#scanDeviceImage").hide();
									//location.reload();
								}
							}
						})
						
                    } else {
                        console.log(xhr.response);
                    }
                }
            };
            xhr.onerror = function () 
			{
                alert("Check If Morpho Service/Utility is Running");
				console.log(xhr.response);
				
				$("#checkDeviceLoader").show();
				$("#captureDeviceLoader").hide();
            }
            xhr.send(XML);
		}
		
	function printDiv() 
	{
	  var divToPrint=document.getElementById('dvContents');
	  var newWin=window.open('','Print-Window');
	  newWin.document.open();
	  newWin.document.write('<html><body onload="window.print()">'+divToPrint.innerHTML+'</body></html>');
	  newWin.document.close();
	  setTimeout(function(){newWin.close();},10);
	}	
    </script>
	<div class="modal fade" id="transactionDetailsModel" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"  data-backdrop="static" data-keyboard="false">
			<div class="modal-dialog" role="document" style="">
				<div class="modal-content">
					<div class="modal-header">
						<h4 class="modal-title" id="myModalLabel">Slip</h4>
						<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
					</div>
					<div class="modal-body" >
						<form class="form-horizontal">
							<table class="table">
								<tr>
									<td>
										<div class="col-md-2 col-sm-2 col-xs-2" >
											<img src="<?php echo e(asset('newlog/images/Logo168.png')); ?>"/>
										</div>
									</td>
									<td><div class="pull-right">
											<div style=" float: left:10px;">
												<b>Outlet Name :  <?php echo e(Auth::user()->member->company); ?></b>
											</div>
											
											<div style="">
												<b>Contact Number: <?php echo e(Auth::user()->mobile); ?></b>
											</div>
										</div>
									</td>
								</tr>
														
							
							</table>
							<table class="table">
								<tbody id="balanceEnquirySlip">
									<!--<tr><th> Aadhaar Number</th><td id="slipAadhaarNumber"></td></tr>
									<tr><th> Customer Number</th><td id="slipCustomerNumber"></td></tr>
									<tr><th> Bank Name</th><td id="slipBankName"></td></tr>
									<tr><th> Txn Amount</th><td id="slipTxnAmount"></td></tr>
									<tr><th> Available Balance</th><td id="slipAvailableBalance"></td></tr>
									<tr><th> Txn Id</th><td id="slipTxnId"></td></tr>
									<tr><th> Bank Ref Number</th><td id="slipBankRefNumber"></td></tr>
									<tr><th> Txn Type</th><td id="slipTxnType"></td></tr>
									<tr><th> Status</th><td id="slipStatus"></td></tr>-->

								</tbody>
							</table>
							

						</form>
					</div>
					<!--<div class="modal-footer">
						<button type="button" class="btn btn-danger" data-dismiss="modal" id="closemodal">Close</button>
					</div>-->
				</div>
			</div>
		</div>
	<meta name="_token" content="<?php echo csrf_token(); ?>"/>
	
<div id="myReciept" class="modal fade" role="dialog" data-backdrop="static" data-keyboard="false">
	   <div class="modal-dialog modal-lg" role="document">
		   

			<!-- Modal content-->
		  <div class="modal-content tx-size-sm" >
				<div class="modal-body">
					<div class="col-md-12">
						<button type="button" class="btn" data-dismiss="modal" style="padding: 6px ! important; top: -8px; right: -35px; background-color: rgb(255, 255, 255) ! important; position: absolute;" id="closemodal">&times;</button>
					</div>
					<div class="containers" style="height: 500px; overflow: auto; width: 100%">
						<div class="panel panel-primary">
							<div class="panel-heading" style="margin-bottom: 3px; padding: 7px;width:95%">Print / Download Receipt <span id="prt_hdtranid"></span>

							<button class="btn btn-basic fullbtn" style="color:black !important; float: right; padding: 5px 8px; text-shadow: none;" onClick="printDiv()"><i class="fa fa-print" style="margin-right: 4px;color: green"></i>PRINT</button></div>
							<div class="panel-body" style="padding: 0% 1%">
								<div class="clearfix"></div>
								<div class="row">
									<div class="col-md-9"></div>
									<div class="col-md-3">
									</div>
								</div>
								<div class="clearfix"></div>
								<div id="reciept" style="margin-top: 5px; margin-bottom: 7px;">

									<div class="row">
										<div class="col-md-12" id="dvContents">
											<style>
												td {
													padding: 5px;
												}
											</style>
											<table style="width: 100%; border: 1px solid #888;">
												<tr>
													<th colspan="2">
													<div class="col-md-12">
														<div class="col-md-2 col-sm-2 col-xs-2" style="padding:10px;">
															<img src="<?php echo e(asset('newlog/images/Logo168.png')); ?>" style="width:70px;margin-right: 690px" />
														</div>
														<div class="col-md-6 col-sm-6 col-xs-6 text-left" style="padding:10px;">
															<div style=" float: left:10px;">
																<b>Outlet Name: <?php echo e(Auth::user()->member->company); ?></b>
															</div>
															<br />
															<div style="margin-top: -20px; float: left:10px;">
																<b>Contact Number: <?php echo e(Auth::user()->mobile); ?></b>
															</div>
														</div>
													  
													  <div class="col-md-4 col-sm-4 col-xs-4 text-right" style="padding:10px;float: right" id="trandetailbyheadnormal">
														   <b>Receipt # :</b> <span class="slipTxnId"> <b id="prt_bdtranid"></b>
															<br /> 
															<b>Date : <?php echo e(date("d-m-Y H:i:s")); ?></b>
														</div> 
														</div>
													</th>


												</tr>

											  
											   <tr></tr>
											   <tr style="border-top:1px solid #ddd;margin-left:60px" id="trandetailbydmt">
													<td>
														
														<b>Aadhaar Number : <span id="slipAadhaarNumber"></span><br/>
														<b>Customer Number : <span id="slipCustomerNumber"></span><br/>
														
													</td>
													<td class="pull-right">
														<b>Bank Name : <span id="slipBankName"></span><br/>
														<b>Transaction Type : <span id="slipTxnType">IMPS</span></b><br />
													</td>
													<td></td>
												</tr>
											   
												<tr>
													<td colspan="3" style="border-bottom: 1px solid #ccc; border-top: 1px solid #ccc;"><b>Transaction Details</b></td>
												</tr>
												<tr>
													<td colspan="3" class="nospace1">
														<table class="table table-bordered">
															<thead class="thead-light">
																<tr style="background:#ddd;">
																	<td class="phead"><b>Date</b></td>
																	<td class="phead"><b>Service Provider</b></td>
																	<td class="phead"><b>Transaction ID </b></td>
																	<td class="phead"><b>IMPS/UTR No.</b></td>
																	<td class="phead"><b>Txn Amount </b></td>
																	<td class="phead"><b> Available Balance </b></td>
																	<td class="phead"><b>Status </b></td>
																</tr>
																</thead>
																<tbody id="txnPrintSlip"></tbody>
														   
														</table>
													</td>
												</tr>
											   <tr>
													<td colspan="3">
														<div class="col-md-12 col-sm-12 col-xs-12">
														<b>Total Amount Rs. : </b>
														<span id="spanChargeAmount"></span>
														<span id="updageFeeSpan"><input type="text" id="txnChargeAmout" onfocusout="amountInWords()"></span>
								
														</div>
													</td>
												</tr> 
												<tr>
													<td colspan="3">
														<div class="col-md-12 col-sm-12 col-xs-12">
														   <b>Amount in Words :</b>
															<label id="slipAmountInWord"></label>
														</div>
													</td>
												</tr> 
												<tr>
													<td colspan="3" class="modalfoot" style="">
														<h4>Terms & Conditions / Disclaimer:</h4>
													<p class="pull-left" style="    text-align: left;">
                                                    1. This transaction receipt is only a provisional acknowledgment and is issued to customer mentioned herein for accepting mentioned payment for the above order and as per the details provided by the customer.<br>
                                                    2. The customer is fully responsible for the accuracy of the details as provided by him before the transaction is initiated.<br>
                                                    3. The Merchant shall not charge any fee to the customer directly for services rendered by them. The customer is required to immediately report such additional/excess charges to a2zsuvidhaa.
                                                    <br> 
                                                    4. This is a system generated receipt hence does not require any signature. Is there anything you want to share with us? Feedback, comments, suggestions or compliments - do write to info@a2zsuvidhaa.com </p>
                                                    <p>This is a system generated Receipt. Hence no seal or signature required.</p>
													</td>
												</tr>
											</table>

										</div>
									</div>
								</div>
							</div>

						</div>
					</div>
				</div>
			</div>

		</div>
	</div>

	
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>