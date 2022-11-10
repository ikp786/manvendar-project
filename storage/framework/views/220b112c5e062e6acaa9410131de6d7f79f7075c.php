<?php $__env->startSection('content'); ?>
 <?php echo $__env->make('layouts.submenuheader', \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>
 <style>
/* .table td, .table th {
    padding: 0px;
    vertical-align: top;
    border-top: 1px solid #dee2e6;
} */
.errorMessage{
	color:red;
}</style>


<script type="text/javascript">
 
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
<script type="text/javascript">
function printTxnSlip()
{
	$("#myReciept").modal('show');
}
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
					console.log(msg);
					$('#slipAmountInWord').css('color','black');
					//$("#spanChargeAmount").text(txnChargeAmout);
					$("#slipAmountInWord").text(msg);
					$("#spanChargeAmount").text(finalAmount);
					
				}
            });
		
	}
function varification() {

	$(".loader").show();
	var username = $("input#number").val();
	var sessionid = $("input#sessionid").val();
	if (username.length == 10 && $.isNumeric(username)) {
	var token = $("input[name=_token]").val();
	var dataString = 'sessionid=' + sessionid + '&mobile_number=' + username + '&_token=' + token;
	$.ajax({
		type: "GET",
		url: "<?php echo e(url('mobile-verification')); ?>",
		data: dataString,
		dataType: "json",
		beforeSend: function () {
			$("#number").attr('readonly','readonly')
			$('#messagedata').hide();
			$('#senderName').text("");
			$("#verificationBtn").hide();
			$("#remainingLimits").text('');
			$("#loaderImg").show();
		},
		success: function (msg) 
		{
			$("#verificationBtn").show();
			$("#reportButton").show();
			$("#loaderImg").hide();
			$('#messagedata').show();
			if(msg.status==13)
			{
				$('#senderName').html(msg.message.fname +' '+msg.message.lname);
				$('#remainingLimits').html(msg.message.rem_bal);
				get_bene();
			}
			else if(msg.status==11){
				$("#tbl").hide();
				$("#ben_frm").hide();
				$("#registration").show();
			}
			else if(msg.status == 12)
			{
				alert(msg.message);
				$("#otpVerificationDiv").show();				
			}
			else if (msg.status == 18 || msg.status == 19) 
			{
				alert(msg.message)
			} 
			else if (msg.message == "Verification pending") 
			{
				//alert("Verification pending");
				$("#tbl").hide();
				$("#registration").hide();
				$("#ben_frm").hide();
				$("#otp_frm").show();
			}
			else{
				alert(msg.message)
			}
		}
		});
	}
	else{
		alert("Enter Correct Mobile Number")
	}
}

function get_bene()
	{
		   var mobile_number = $("input#number").val();
            if (mobile_number!='') 
			{
                var dataString = 'mobile_number=' + mobile_number;
                
                $.ajax({
                    type: "get",
                    url: "<?php echo e(url('/get-bene-list')); ?>",
                    data: dataString,
                    dataType: "json",
                    beforeSend: function () {
                        $("#verificationBtn").hide();
						$("#loaderImg").show();
                    },
                    success: function (msg) {
                      /*   $("#tbl").show(); */
                           $("#verificationBtn").show();
							$("#loaderImg").hide();
							$("#tbl").show();
						   if(msg.status == 22)
						   {
							  
								var obj2 = msg.message.data;
								var html = "";
								var ACCOUNT_NUMBER="number";
								var MOBILE_NUMBER="MOBILE_NUMBER";
								for (var key in obj2) 
								{
									
									var rname = obj2[key].name;
									//alert(rname);
									var rrid = obj2[key].beneId;
									var ifsc = obj2[key].ifsc;
									var raccount = obj2[key].account_number;
									var bank_name = obj2[key].bank_name;
									var status = obj2[key].status_id;
									//alert(obj[key]["receiverid"]);
									html += "<tr>"
									html += '<input type="hidden" value="'+rname+'" id="rrid_'+rrid+'"><input type="hidden" value="'+ifsc+'" id="ifsc_'+rrid+'"><input type="hidden" value="'+bank_name+'" id="bank_'+rrid+'">';
									html += "<tr>";
									html += "<td>"+rname+"</td><td style='cursor: pointer; color:blue' onclick='getTranactionByField(\""+raccount+"\",\""+ACCOUNT_NUMBER+"\")'>"+raccount+"</td><td>"+bank_name+"</td><td>"+ifsc+"</td>";
									if (status == 1) {
									html += '<td><div class="btn-group btn-sm right" role="group"> <button class="btn btn-sm tx-12 btn-danger" onclick="pending_resend_top(\'' + rrid + '\')"> De Active </button> </div></td>';
									}else{
									html += "<td><button class='btn btn-sm btn-primary' onclick='transfermodal(\""+rrid+"\",\""+raccount+"\",\""+rname+"\")'>Pay</button><button class='btn btn-sm tx-12 btn-danger' onclick='Deletebene("+rrid +")'> <i class='fa fa-trash'></i> </button> </td>";
									}
									html += "</tr>";

								}
							}
								//$("#senderidn").val(msg["senderid"]);
								$("#adbenname").show();
								$("#response").html(html);
								$("#ben_frm").show();
								$("#registration").hide();
								$("#otp_frm").hide();
								$("#an").show();
								$("#bn").show();
								$("#bm").show();
								$("#ic").show();
								$("#fn").show();
								$("#bankname").show();
												                        
                    }
                });
            }
	}


function addbene() {
	$(".loader").show();
	var checkname = '';
	var token = $("input[name=_token]").val();
	var senderid = $("#senderidn").val();
	var username = $("input#number").val();
	var sessionid = $("input#sessionid").val();
	var bname = $("#first_name").val();
	var bbank = $("#bank_name").val();
	var baccount = $("#bank_account").val();
	var bifsc = $("#ifsc").val();
	var service_id = $("#service_id").val();
	var bmobile = $("#bene_mobile").val();
	var senderId = $("#senderId").val();
	var caseType=$("#PreAddBene").val();
	var beneficiaryId=$("#beneficiaryId").val();
	var beneficiaryOtp=$("#beneficiaryOtp").val();
	
	var dataString = 'mobile_number=' + username + '&beneName=' + bname  + '&accountNumber=' + baccount + '&ifscCode=' + bifsc + '&bankName=' + service_id + '&_token=' + token+'&caseType='+caseType+'&beneficiaryId='+beneficiaryId+'&beneficiaryOtp='+beneficiaryOtp;
		$.ajax({
		type: "POST",
		url: "<?php echo e(url('bene-add')); ?>",
		data: dataString,
		dataType: "json",
		beforeSend:function(){
			$("addBeneBtn").hide()
			$("beneLoaderImg").show()
			
		},
		success: function (msg) {
			$("addBeneBtn").show()
			$("beneLoaderImg").hide()
			alert(msg.message);
			if(msg.status == 1)
			{
				$("#PreAddBene").val("VerifyOtp")
				$("#beneficiaryId").val(msg.beneId);
				$("#beneficiaryOtpDiv").show();
				$("#submitDiv").hide();
				$("#addBeneDiv").show();
			}
			if(msg.status==35)
			{
				$("#PreAddBene").val("PreAddBene");
				$("#beneficiaryId").val('');
				$(".isEmptyFields").val('');
				$("#ben_frm").hide();
				varification();
			}
		}
	});
}




function registration() {
	$("#newnextstep").text("Loading....");
	$("#newnextstep").attr('disabled', true);
	var token = $("input[name=_token]").val();
	fname = ($("#fname").val()).trim();
	lname = ($("#lname")).val().trim();
	var namePattern=/^[a-zA-Z ]+$/;
	if(fname =='')
	{
		alert("Please Enter First Name");
		$('#fname').focus();
		return false;
	}
	else if(!fname.match(namePattern))
	{
		alert("Only Number Character allowed");
		$('#fname').focus();
		return false;
		
	}
	else if(lname == '')
	{
		alert("Please Enter Last Name");
		$('#lname').focus();
		return false;
	}
	else if(!lname.match(namePattern))
	{
		alert("Only Number Character allowed");
		$('#lname').focus();
		return false;
		
	}
	if (fname != '' && lname != '') 
	{
		var dataString = 'fname=' + fname + '&lname=' + lname +  '&mobile_number=' + $("input#number").val() + '&_token=' + token;
			$.ajax({
			type: "GET",
			url: "<?php echo e(url('remitter-register')); ?>",
			data: dataString,
			dataType: "json",
			beforeSend: function () {
                       // $("#registerBtn").hide();
						//$("#registerLoaderImg").show();
            },
			success: function (msg) 
			{
				if(msg.status == 10)
				{
					var errorString = '<div class="alert alert-danger"><ul>';
					$.each(msg.errors, function (key, value) {
						errorString += '<li>' + value + '</li>';
					});
					errorString += '</ul></div>';
					$("#registerMissingr").show();
					$('#registerMissing').html(errorString); //appending to a <div 
				}
				else if(msg.status == 12)
				{
					$("#registration").hide();
					$("#otpVerificationDiv").show();
					
				}
				else{
					var errorString = '<div class="alert alert-danger"><ul>';
								$.each(msg.errors, function (key, value) {
									errorString += '<li>' + value + '</li>';
								});
								errorString += '</ul></div>';
								$("#registerMissingr").show();
								$('#registerMissing').html(errorString); //appending to a <div id="form-errors"></div> 
				}
				$("#newnextstep").text("Next Step");
				$("#newnextstep").attr('disabled', false);
			}		
		});
	} else {
	$("#newnextstep").text("Next Step");
	$("#newnextstep").attr('disabled', false);
	alert("First Name Last and pincode Required");
	}
}
	function otpVerification()
	{
		var number = $('#number').val();
		var otp = $('#remitterOtp').val();
		var number_pat=/^[0-9]+$/;
		if(otp =='')
		{
			alert("Please Enter OTP");
			$("#remitterOtp").focus();
			return false;
		}
		else if(otp.length !=4)
		{
			alert("Only 4 digits allowed");
			$("#remitterOtp").focus();
			return false;
			
		}
		else if(!otp.match(number_pat))
		{
			alert("Only Number allowed in otp field");
			$('#remitterOtp').focus();
			return false;
			
		}
			
		var dataString = 'mobile=' + number +'&otp='+otp;
		 $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                }
            })
			 $.ajax({
                    type: "PUT",
                    url: "<?php echo e(url('mobile-verification-with-otp')); ?>/"+number,
                    data: dataString,
                    dataType: "json",
                    beforeSend: function () {
                        $("#verifyOtpBtn").hide();
						$("#verifyLoaderImg").show();
                    },
                    success: function (msg) 
					{
							$("#verifyOtpBtn").show();
							$("#verifyLoaderImg").hide();
							if(msg.status == 17)
							{
								$("#otpVerificationDiv").hide();
								alert(msg.message)
								varification();
								//get_bene();
							}
							else if(msg.status == 16)
							{
								alert(msg.message)
							}
							else if(msg.status == 11 || msg.status == "ERR" )
							{
								$("#otpVerificationDiv").hide();
								alert(msg.message)
								$("#tbl").hide();
								$("#ben_frm").hide();
								$("#registration").show();
							}
							else {
								var errorString = '<div class="alert alert-danger"><ul>';
								$.each(msg.errors, function (key, value) {
									errorString += '<li>' + value + '</li>';
								});
								errorString += '</ul></div>';
								$("#name-error").show();
								$('#name-error').html(errorString); //appending to a <div id="form-errors"></div> 
							}
                        }
                });
		
	}


	function verifynow() 
	{
		var token = $("input[name=_token]").val();
		var ifsc = $("#service_id").val();
		var bifsc = $("#ifsc").val();
		var number = $("#number").val();
		var bank_account = $("#bank_account").val();
		if (bank_account != '') {
		if(bifsc == '')
		{
			$("#ifsc").focus();
		}
		else
		{
			var dataString = 'bankcode=' + ifsc + '&ifsc=' + bifsc + '&mobile_number=' + number + '&bank_account=' + bank_account + '&_token=' + token;
			$(".loader").show();
			$.ajax({
			type: "GET",
			url: "<?php echo e(url('cyber-api/account-name-info')); ?>",
			data: dataString,
			dataType: "json",
			beforeSend:function(){
				$("#bnv").hide();
				$("#accverifyLoaderImg").show();				
			},
			success: function (msg) 
			{
				$("#bnv").show();
				$("#accverifyLoaderImg").hide();
				if (msg.statuscode == 1) 
				{
					alert(msg.message);
					/* $("#otp_frm").hide();
					$("#ben_frm").show(); */
					/* $("#bene_mobile").val(number); */
					/* $("#bn").show();
					$("#bm").show();
					$("#ic").hide(); */
					$("#first_name").val(msg.message);
					$("#fn").show();
				} 
				else if (msg.statuscode == 'TXN') 
				{
					/* $("#otp_frm").hide();
					$("#ben_frm").show();
					$("#bene_mobile").val(number);
					$("#bn").show();
					$("#bm").show();
					$("#ic").hide(); */
					$("#first_name").val(msg.data.benename);
					$("#fn").show();
					alert(msg.status);
				}
				else 
				{
					alert(msg.status);
				}
			}
			});
		}
	} else {
		$(".loader").hide();
		alert("OPT Required, Please enter OTP");
	}
}

	function Deletebene(beneId) {
	if(confirm("Are you sure want to delete beneficiary"))
	{
		var token = $("input[name=_token]").val();
		var number = $("#number").val();
		var senderId = $("#senderId").val();
		var sessionid = $("input#sessionid").val();
		var dataString = 'senderid=' + senderId + '&sessionid=' + sessionid + '&mobile_number=' + number + '&beneId=' + beneId;
		 $.ajaxSetup({
					headers: {
						'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
					}
				})
		$.ajax({
			type: "put",
			url: "<?php echo e(url('bene-delete-request')); ?>/"+beneId,
			data: dataString,
			dataType:"json",
			success: function (msg) {
				console.log(msg);
				
				if (msg.status == 37) {
					alert(msg.message);
					$("#del_otp").val('');
					$("#bendelete").modal('show');
					$("#del_beneid").val(beneId);
				} else {
					alert(msg.message);
				}
			}
		});
	}
}function getTranactionByField(searchNumber,searchType) {
		var token = $("input[name=_token]").val();
		
		var dataString = 'searchNumber=' + searchNumber + '&searchType=' + searchType;
		 $.ajaxSetup({
					headers: {
						'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
					}
				})
		$.ajax({
			type: "get",
			url: "<?php echo e(url('get-txn-by-field')); ?>",
			data: dataString,
			dataType:"json",
			success: function (res) 
			{
				 var html ='<thead><tr><th>Record id</th><th>Customer Number</th><th>amount</th><th>Product</th><th>Txn Time</th><th>Status</th></tr></thead><tbody>';
                        $.each(res.details, function (key, val) {
                             html +='<tr>';
                             html +='<td>' + val.reportId + '</td>';
                             html +='<td>' + val.customerNumber + '</td>';
                             html +='<td>' + val.amount + '</td>';
                             html +='<td>' + val.apiName + '</td>';
                             html +='<td>' + val.txnTime + '</td>';
                             html +='<td>' + val.status + '</td>';
                             html +='</tr>';

                        });
                        html +='</tbody>';
					
						$("#result_table").html(html);
						$("#lastTrasnactionDetils").modal('show');
			}
		});
	}

function Deletebene_confirm(){
	$(".loader").show();
	var beneid = $("#del_beneid").val();
	var otp = $("#del_otp").val();
	var dataString = '&beneId=' + beneid + '&otp=' + otp;
	 $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                }
            })
	$.ajax({
		type: "put",
		url: "<?php echo e(url('confirm-bene-delete')); ?>",
		data: dataString,
		dataType: "json",
		beforeSend:function(){
			$("#deleteBeneCnfrmBtn").hide();
			$("#deleteBeneCnfrmLoadeImg").show();
		},
		success: function (msg) {
			$("#deleteBeneCnfrmBtn").show();
			$("#deleteBeneCnfrmLoadeImg").hide();
			if (msg.status == 38) {
				alert(msg.message)
				$("#bendelete").modal('hide');
				varification();
			} else {
				alert(msg.message);
			}
		}
	});
}


function transfermodal(id, account,beneName)
{
	$("#amount").val('');
	$("#amountInWords").val('');
	$("#txn_pin").val('');
var benename = $('#rrid_'+id).val();
var ifsc = $('#ifsc_'+id).val();
var bank = $('#bank_'+id).val();
$(".account_number").html(account);
$(".benename").html(benename);
$(".beneid").html(id);
$(".bank").html(bank);

$("#transferBank").val(bank);
$("#transferIfsc").val(ifsc);
$("#transferBenename").val(benename);
$("#transferId").val(id);
$("#transferAccount").val(account);


$('.modal-backdrop').remove();
/* -------------------- */
	$("#pSenderMobile").text($("#number").val());
	$("#slipSenderNumber").text($("#number").val());
	$("#pBeneName").text(benename);
	$("#slipbeneName").text(benename);
	$("#pBeneIFSC").text(ifsc);
	$("#pBeneAccountNo").text(account);
	
	
/* ---------------- */
$("#transfermodal").modal('show');

}
function preTxnSlip()
{
	var amount = $("#amount").val();
	var txn_pin = $("#txn_pin").val();
	var number_pat = /^[0-9]+$/;
	var remainingLimits=parseInt($('#remainingLimits').text());
	if(amount=='')
	{
		alert("Please Enter Transaction Amount");
		return false;
	}
	else if(!amount.match(number_pat))
	{
		alert("Amount should be number");
		return false;
	}
	else if(txn_pin=='')
	{
		alert("Please Enter Transaction Pin")
		return false;
	}
	var amount = $("#amount").val()
	if(amount >50000){
		$("#amount").focus();
		alert("Amount can not be greather than 50000");
		return false;
	}
	if(amount>remainingLimits){
		$("#amount").focus();
		alert("Amount can not be greather than Your Remaining Limit : " +  remainingLimits);
		return false;
	}
	dataString="amount="+ amount +"&txnChargeApiName=TRAMO";
	$.ajax({
		type: "GET",
		url: "<?php echo e(url('get-agent-charge-amt')); ?>",
		data: dataString,
		dataType: "json",
		beforeSend:function()
		{
			//loaderTrnsImg
			$("#txnBtn").attr('disabled',true)
			$("#txnBtn").hide();
			$("#loaderTrnsImg").show();
		},
		success: function (msg) 
			{
				$("#txnBtn").attr('disabled',false)
				$("#txnBtn").show();
				$("#loaderTrnsImg").hide();
				if(msg.status == 0)
					alert("Low Balance");
				else if(msg.status == 1){
					if(txn_pin == msg.txn_pin)
					{
					var content='';
						$.each(msg.result, function (key, val) 
						{
							content +='<tr><td>'+val.txnAmount+'</td><td>'+val.charge+'</td><td>'+val.total+'</td></tr>';
						});
						var AMTINWords=convertAmountInWords(amount,'displayyAmountinwords');
						content +='<tr><td id="displayyAmountinwords">'+AMTINWords+'</td></tr>';
						content +='<tr><td>Confirm Amount</td><td><input type="text" class="form-control col-md-5" id="confirmAmount"/></td></tr>';
						$("#pSlipTBody").html(content);
						$("#preTxnSlip").modal('show')
						}
					else{
						alert('Transaction pin is invalid');
					}
				}
				else{
					alert("Whoops Something went wrong");
				}
			}
		});//$("#preTxnSlip").modal('show')
	}
	function convertAmountInWords(amount,displayAtTag)
	{
		var amountFormat = /^[0-9]+$/;
		$.ajax({
                type: "get",
                url: "<?php echo e(url('amount-in-words')); ?>",
                data: "amount="+amount,
				dataType:"json",
				beforeSend:function(){	
				},
                success: function (msg) 
				{
					$('#'+displayAtTag).css('color','#3db92e').text(msg);
					//$("#displayAtTag").text(msg);
				}
            });
	}

function transferamount (){

	var amount = $("#amount").val();
	if(amount <10 && amount >50000)
		{
		alert("Amount Should be between Rs. 10 - 50000");
		return false();
}
var confirmAmount = $("#confirmAmount").val();
	if(amount != confirmAmount){
		alert("Enter amount and confirm amount missmatched");
		return false;
	}
	var beneName = $("#pBeneName").text();
	var mode = $("#mode").val();
	var token = $("input[name=_token]").val();
	var senderid = $("#number").val();
	var mobile_number = $("#number").val();
	var beneid = $("#transferId").val();
	var ifsc = $("#transferIfsc").val();
	var channel = $("#channel").val();
	var bank_account = $("#transferAccount").val();
	var amount = amount;
	var bank_name = $("#transferBank").val();
	var senderName = $("#senderName").text();
	var dataString = 'beneName=' + beneName + '&bank_name=' + bank_name + '&ifsc=' + ifsc + '&channel=' + channel + '&bank_account=' + bank_account + '&mobile_number=' + mobile_number + '&beneficiary_id=' + beneid + '&amount=' + amount + '&_token=' + token+ '&senderName=' + senderName; 
	$.ajax({
		type: "POST",
		url: "<?php echo e(url('tramo-transaction')); ?>",
		data: dataString,
		dataType: "json",
		beforeSend: function () {
			
			$("#payAndConfirmBtn").attr('disabled',true)
			$("#payAndConfirmBtn").hide();
			$("#txnLoaderImg").show();
		},
		
		success: function (msg) {
			//console.log(msg);
			
			$("#preTxnSlip").modal('hide')
			$("#payAndConfirmBtn").show();
			$("#payAndConfirmBtn").attr('disabled',false)
			$("#txnLoaderImg").hide();
			$("#transfermodal").modal('hide');
			$("#slipTxnRefId").html(msg.refNo);
			$("#slipTxnSataus").html(msg.status);
			$("#slipTxnId").html(msg.txnId);
			$("#slipTxnAmount").html(amount);
			$("#slipAmount").html(amount);
			$("#slipAccountNo").html(bank_account);
			$("#slipIFSC").html(ifsc);
			$("#slipBankName").html($("#transferBank").val());
			$("#slipSenderName").text(senderName);
			$("#amount").val()
			varification();
			if (msg.status == 1||msg.status == 3||msg.status == 18) 
			{
				refreshBalance();
				if(msg.status == 1)
					$("#slipTxnSataus").html("SUCCESS");
				
				if(msg.status == 3)
					$("#slipTxnSataus").html("PENDING");
				if(msg.status == 18)
					$("#slipTxnSataus").html("INPROCESS");
				$("#myReciept").modal('show');
			}
			else
			alert(msg.message);
		}
	});
}

// last
function getIfsc() {
	var bankcode = $("#service_id").val();
	var token = $("input[name=_token]").val();
	var number = $("#number").val();
	if (number != '') 
	{
		var dataString = 'bankcode=' + bankcode + '&mobile_number=' + $("#number").val() + '&_token=' + token;
			$.ajax({
			type: "GET",
			url: "<?php echo e(url('cyber-api/get_bank_detail')); ?>",
			data: dataString,
			success: function (msg) {
			var obj = $.parseJSON(msg);
			if(obj.is_imps_txn_allow==0)
			{
				alert(obj.message);
			}
			else
			{
				if(obj.status == 0)
				{
					$("#ifsc").val("");
					alert('Ifsc code not found.');
					}else{
					$("#ifsc").val(obj.ifsc);
				}
			}

			}
		});
	} 
	else 
	{
		//alert("OTP Required, Please enter OTP");
	}
}

function callAutoFuncion()
{
	var username = $("input#number").val();
	if (username.length == 10 && $.isNumeric(username)) 
	{
		varification();
	}
}

function checkIsBankDownOrNot()
{
	var ifscCode = $("#pBeneIFSC").text();
	var bankName = $("#transferBank").val();
	var accountNumber = $("#transferAccount").val();
	var amount = $("#amount").val();
	var confirmAmount = $("#confirmAmount").val();
	$("#errorMessageAmountMissmatch").text('');
	if(amount != confirmAmount){
		//alert("Enter amount and confirm amount missmatched");
		$("#errorMessageAmountMissmatch").text("Enter amount and confirm amount missmatched");
		return false;
	}
	
	dataString="ifscCode="+ ifscCode+"&bankName="+ bankName+"&accountNumber="+ accountNumber;
	$.ajax({
		type: "GET",
		url: "<?php echo e(url('check-is-bank-down')); ?>",
		data: dataString,
		dataType: "json",
		beforeSend:function()
		{
			$("#payAndConfirmBtn").attr('disabled',true)
			$("#payAndConfirmBtn").hide();
			$("#txnLoaderImg").show();
			$("#errorMessageAmountMissmatch").text('');
		},
		success: function (msg) 
			{
				
				$("#txnLoaderImg").hide();
				if(msg.status == 1)
				{
					$("#payAndConfirmBtn").attr('disabled',false)
					$("#payAndConfirmBtn").show();
					$("#errorMessageAmountMissmatch").text(msg.message);					
				}
				else if(msg.status == 0){
						transferamount()
				}
				else{
					$("#errorMessageAmountMissmatch").text("Whoops Something went wrong");			
				}
			}
		});
}

$( document ).ready(function() {
    $('input').attr('autocomplete','off');
});

$(document).ready(function(){
  $("#number").click(function(){
    $("#number").attr("readonly", false);
  });
  $("#number").focus(function(){
     $("#number").attr("readonly", false);
  });
  $("#number").hover(function(){
    $("#number").attr("readonly", false);
  });
});

</script>

<?php echo $__env->make('agent.money.money-type', \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>

<div class="text-right">
	<a href="<?php echo e(route('transaction-report')); ?>?product=5&searchOf=3" class="textheader">Pending</a>&nbsp;|&nbsp;
	<a href="<?php echo e(route('transaction-report')); ?>?product=5&searchOf=4" class="textheader">Refunded</a>&nbsp;|&nbsp;
	<a href="<?php echo e(route('transaction-report')); ?>?product=5&searchOf=20" class="textheader">Refund Pending</a>&nbsp;|&nbsp;
	<a href="<?php echo e(route('transaction-report')); ?>?product=5&searchOf=1" class="textheader"> Transaction History</a>&nbsp;&nbsp;
</div>     
<div class="row row-sm" autocomplete="off">
          
          <div class="col-md-4 mg-t-20" autocomplete="off">
              <div class="card bd-0 shadow-base">
                
                <div class="" autocomplete="off">
				 <a href="#" class="pull-right"  onClick="getTransactionOfMobile()" style="display:none" id="reportButton">Report</a>
            	<?php echo $__env->make('partials.mobile-number-report', \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>
            	  <img src="<?php echo e(url('/loader/loader.gif')); ?>" id="mobileTxnLoader" class="loaderImg">
                 <div class="d-flex" style="padding: 2%;" autocomplete="off">
            <div class="form-group mg-b-0" autocomplete="off">
           
                <input type="text" name="number" id="number" class="form-control" style="font-weight: bold;font-size:20px;color:darkslategrey"  placeholder="Enter Mobile Number" required maxlength="10" autocomplete="off" readonly="" onkeyup ="callAutoFuncion()">
            </div><!-- form-group -->
            <div class="mg-l-10 mg-t-25 pd-t-4">
                <button type="submit" class="btn btn-success" onclick="varification()" id="verificationBtn"><i class="fa fa-search"></i></button> <img src="<?php echo e(url('/loader/loader.gif')); ?>" id="loaderImg" class="loaderImg" style="display:none"/> 
				<a href="<?php echo e(Request::url('')); ?>" class="btn btn-primary" ><i class="fa fa-refresh"></i></a>
                <!--<button type="submit" class="btn btn-info" onclick="printTxnSlip()" id="verificationBtn"style="margin-top: 25px; margin-left: 10px;">SEARCH</button>-->
				
            </div>
			<div class="mg-l-10 mg-t-25 pd-t-4">
             	
            </div>
        </div>
        <div id="messagedata" style="display:none">
			<span style="font-weight: bold;"> Sender Name : </span><span id="senderName"></span>
			<span style="font-weight: bold;" class="full-right"> <span>Remaining Limit : </span><span id="remainingLimits"></span></span>	
		</div>    
                </div><!-- card-body -->
              </div><!-- card -->

              <!-- <div class="col-md-4 mg-t-20" id="ben_frm"> -->
                    
                    <div id="ben_frm" style="display: none;">
                         <input type="hidden" value="" id="senderidn"/>
                  
                  <div class="" style="">
                  Add Beneficiary
				  		
				  <div><label id="bbank">Bank Name</label></div>
                    
                    <div class="" style="padding-bottom: 1%;" autocomplete="off">
                        <input list="browsers" id="service_id" class="form-control" onchange="getIfsc()">
                        <datalist id="browsers">

                             <?php $__currentLoopData = $netbanks; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $netbank): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($netbank->bank_name); ?>">
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </datalist>
                    </div><!-- form-group -->
					
					<div class="" id="ic"style="padding-bottom: 1%;">
                    <input type="text" class="form-control isEmptyFields" id="ifsc" placeholder="IFS Code">
                        <span class="input-group-btn right">
                            <!--<button id="ifscbtn" class="btn cursor btn-primary mg-t-40-min" type="button" onclick="getIfsc();" style="float: right;top: -34px;">get ifsc</button>-->
                        </span>
                    </div><!-- form-group -->
                <div class="" id="an" style="padding-bottom: 1%;">
                       <input type="text" class="form-control isEmptyFields" id="bank_account" placeholder="Bank Account Number"/>
					</div>	
						<span class="input-group-btn right">
						<button id="bnv" class="btn btn-primary mg-t-40-min" type="button"
						 onclick="verifynow();" style="float: right;">Verify</button>
						<img src="<?php echo e(url('/loader/loader.gif')); ?>" id="accverifyLoaderImg" class="loaderImg" style="margin-top: 16px;display:none"/></span>
										
                    <!-- form-group -->

                    <div class="" id="fn" style="padding-bottom: 1%;">
                      <input type="text" class="form-control isEmptyFields" id="first_name" placeholder="Beneficiary Name">
                    </div><!-- form-group -->

                    <!--<div class="form-group" id="bm">
                       <input type="text" class="form-control"
                                                       id="bene_mobile"
                                                       placeholder="Mobile Number">
                    </div><!-- form-group -->

                    
					<div class="" id="beneficiaryOtpDiv" style="display:none;padding-bottom: 1%;">
						<input type="text" class="form-control isEmptyFields" id="beneficiaryOtp" placeholder="OTP">
                    </div>
					<div class="col-md-12">
						<div class="col-md-6">
						<button class="btn btn-info bd-0 btn-oblong" onclick="addbene();" id="addBeneBtn">Submit</button>
					
						<img src="<?php echo e(url('/loader/loader.gif')); ?>" id="submitLoaderImg" class="beneLoaderImg" style="display:none"/>
						</div>
						
					 </div>
                  </div><!-- card-body -->
				  
                 
                  <div class="card-footer mg-t-auto" id="addBeneDiv" style="display:none">
				   <button class="btn btn-info bd-0 btn-oblong" onclick="resendOtp();" id="resendOtpBtn">Resend OTP</button>
                    <button class="btn btn-info bd-0 btn-oblong" onclick="addbene();" id="addBeneBtn">Add Bene</button>
					<input type="hidden" value="PreAddBene" id="PreAddBene"/>
					<input type="hidden" value="" id="beneficiaryId"/>
					 <img src="<?php echo e(url('/loader/loader.gif')); ?>" id="beneLoaderImg" class="beneLoaderImg" style="display:none"/>
                  </div><!-- card-footer -->
                </div>

                    <!-- </div> -->


                    <div class="card bd-0 shadow-base mg-t-20" id="otp_frmb" style="display: none;">
                        <input type="hidden" id="senderIdb"/>
                  <div class="card-body">
                    <div class="card-title">Enter otp</div>
                        
                         <div class="form-group">
                             <input type="hidden" id="BeneficiaryCode"
                                                   name="BeneficiaryCode">
                        <label>One Time Password</label>
                        <input type="text" id="otpvalb" class="form-control">
                    </div><!-- form-group -->

                     <div class="form-group">
                     <span class="help-block success"
                                                  id="success_msgb"
                                                  style="display: none;">Please Enter Otp (OTP will be Expired in 60 Min)</span>
                                                  </div><!-- form-group -->


                  </div><!-- card-body -->
                  <div class="card-footer mg-t-auto">
                    <button class="btn btn-info bd-0 btn-oblong" onclick="otpb()">Confirm</button>
                    <button class="btn btn-info bd-0 btn-oblong" onclick="bene_resend_otp()">Resend Otp</button>
                  </div><!-- card-footer -->
                </div>


                 <div class="card bd-0 shadow-base mg-t-20" id="registration" style="display: none;">
                  <div class="card-body">
				  <div id="name-error"></div>
                    <div class="card-title">Register Customer</div>
                   
                    
                    <div class="form-group"id="registerMissing"> 
                    </div>
					<div class="form-group">
                        <input type="text" id="fname" class="form-control" placeholder="First Name">
                    </div>
					<div class="form-group">
                        <input type="text" id="lname" class="form-control" placeholder="Last Name">
                    </div><!-- form-group -->
					 

                    
                  </div><!-- card-body -->
                  <div class="card-footer mg-t-auto">
                    <button class="btn btn-info bd-0 btn-oblong" id ="registerBtn"onclick="registration();">Next Step</button>
					  <img src="<?php echo e(url('/loader/loader.gif')); ?>" id="registerLoaderImg" class="loaderImg" style="margin-top: 16px;display:none"/>
                  </div><!-- card-footer -->
                </div>
				<div class="card bd-0 shadow-base mg-t-20" id="otpVerificationDiv" style="display: none;">
					<div class="card-body">
						<div class="form-group" >
							<label>Enter Otp</label>
							<input type="text" id="remitterOtp" maxlength="4" class="form-control" placeholder="Enter 4 digit OTP">
						</div>
					</div>
                  <div class="card-footer mg-t-auto">
                    <button id="verifyOtpBtn" class="btn btn-primary" type="button" onclick="otpVerification();">Verify </button>
					 <img src="<?php echo e(url('/loader/loader.gif')); ?>" id="verifyLoaderImg" class="loaderImg" style="margin-top: 16px;display:none"/>
                   <button id="resendOtp" class="btn btn-success" type="button" onclick="varification();">Resend OTP </button>
                  </div><!-- card-footer -->
                </div>


                

            </div>

            <div class="col-md-8 mg-t-20">
                <div class="card bd-0 shadow-base" id="tbl">
                <div class="card-header bg-info bd-0 d-flex align-items-center justify-content-between pd-y-5">
                  <div class="card-header tx-medium bd-0 tx-white bg-info">
                 Beneficiary List
                </div>
                  
                  <div class="card-option tx-24">
                    <a class="tx-white-8 clr-white cursor hover-white mg-l-10">
                        <!-- <i class="icon ion-ios-refresh-empty lh-0"></i> -->
                    </a>
                    <!-- <a href="" class="tx-white-8 hover-white mg-l-10"><i class="icon ion-ios-arrow-down lh-0"></i></a>
                    <a href="" class="tx-white-8 hover-white mg-l-10"><i class="icon ion-android-more-vertical lh-0"></i></a> -->
                  </div>
                  <!-- card-option -->
                </div><!-- card-header -->
                <div class="card-body bd-t-0 rounded-bottom-0">
                   <div class="table-responsive"style="max-height:500px">
                  <table class="table mg-b-0 table-contact">
            <thead>
              <tr>
                
               <th>Name</th>
					<th>Account Number </th><th> Bank Name</th><th>IFSC</th><th>Action</th>
                <th style="display: none;" id="benLoad">Loading....</th>
                <!-- <th class="right">Action</th> -->
              </tr>
            </thead>
            <tbody id="response">
             
            </tbody>
          </table>
      </div>
                </div><!-- card-body -->
                
              </div>
            </div>

                
          
        </div><!-- row -->


         <!-- LARGE MODAL -->
          <div id="transfermodal" class="modal fade">
            <div class="modal-dialog modal-md" role="document">
              <div class="modal-content tx-size-sm" >
                <div class="modal-header pd-x-20">
                  <h6 class="tx-14 mg-b-0 tx-uppercase tx-inverse tx-bold"><span class="benename"></span> - <span class="account_number"><</span></h6>
                  <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                  </button>
                </div>
                <?php echo csrf_field(); ?>

                <div class="modal-body pd-20">
                  <h4 class=" lh-3 mg-b-20 bank"></h4>
                  <input type="hidden" id="transferBank"> <input type="hidden" id="transferIfsc"> <input type="hidden" id="transferBenename">
                  <input type="hidden" id="transferId"> <input type="hidden" id="transferAccount">

                  <div class="form-group" id="mode">
                    <select class="form-control" id="channel">
                        <option value="2">IMPS</option>
                        </select>
                </div>

                <div class="form-group">
                    <input type="text" id="amount" class="form-control pd-y-12" placeholder="Amount">
                </div>
				<div class="form-group">
                    <input type="password" id="txn_pin" class="form-control" placeholder="Enter Transaction Pin">
                </div>
                
                <div class="modal-footer">
                  <button type="button" class="btn btn-primary tx-size-xs" onclick="preTxnSlip()" id="txnBtn">Transfer Now</button>
				  
                  <button type="button" class="btn btn-secondary tx-size-xs" data-dismiss="modal">Close</button>
                </div>
              </div>
            </div><!-- modal-dialog -->
          </div><!-- modal --> 
    </div>

    <div id="state-change-model" class="modal fade">
            <div class="modal-dialog modal-lg" role="document">
              <div class="modal-content tx-size-sm">
                <div class="modal-header pd-x-20">
                  <h6 class="tx-14 mg-b-0 tx-uppercase tx-inverse tx-bold"><span>Active Beneficiary</span></h6>
                  <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                  </button>
                </div>
                <div class="modal-body pd-20">
                   <input type="hidden" id="bene3">

                <div class="form-group">
                    <label>Otp</label>
                    <input type="number" id="state_otp" class="form-control pd-y-12" placeholder="Enter Otp">
                </div>
                
                <div class="modal-footer">
                  <button type="button" class="btn btn-primary tx-size-xs" onclick="bene_confirm_pending()">Active</button>
                  <button type="button" class="btn btn-secondary tx-size-xs" onclick="bene_resend_otp()">Resend Otp</button>
				  <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
              </div>
            </div><!-- modal-dialog -->
          </div><!-- modal --> 
    </div>


    <div id="bendelete" class="modal fade">
            <div class="modal-dialog modal-md" role="document">
              <div class="modal-content tx-size-sm">
                <div class="modal-header pd-x-20">
                  <h6 class="tx-14 mg-b-0 tx-uppercase tx-inverse tx-bold"><span>Delete Beneficiary</span></h6>
                  <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                  </button>
                </div>
                <div class="modal-body pd-20">
                   <input type="hidden" id="del_beneid">
                   <input type="hidden" id="del_senderid">

                <div class="form-group">
                    <label>Otp</label>
                    <input type="number" id="del_otp" class="form-control pd-y-12" placeholder="Enter Otp">
                </div>
                
                <div class="modal-footer">
                  <button type="button" id="deleteBeneCnfrmBtn" class="btn btn-primary tx-size-xs" onclick="Deletebene_confirm()">Delete</button>
				  <img src="<?php echo e(url('/loader/loader.gif')); ?>" id="deleteBeneCnfrmLoadeImg" class="loaderImg" style="margin-top: 16px;display:none"/>
				  <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
              </div>
            </div><!-- modal-dialog -->
          </div><!-- modal --> 
    </div>
                   
    <?php echo $__env->make('common.transactionbyField', \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>
    
                    
<div class="modal fade" id="bene_del_otp" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <!-- <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                aria-hidden="true">&times;</span></button> -->
                    <h4 class="modal-title" id="myModalLabel">Delete beneficiarty</h4>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="otp_bene_id">
                    <form class="form-horizontal">
                        <div class="form-group">
                            <label for="bank_account" class="control-label col-sm-4">
                                OTP </label>
                            <div class="col-sm-6">
                                <input type="text" class="form-control" id="deleteBeneOtp"
                                       placeholder="Entrer OTP">
                            </div>
                        </div>

                    </form>
                </div>
                <div class="modal-footer">
                   <button id="benedelete_success" type="button" onclick="this.disabled=true;benedelete_success()" class="btn btn-primary">Confirm
                    </button>
					<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
<div id="preTxnSlip" class="modal fade">
            <div class="modal-dialog modal-md" role="document">
              <div class="modal-content tx-size-sm">
                <div class="modal-header pd-x-20">
                
					<h4 class="modal-title">Transaction Details</h4>
                </div>
                <?php echo csrf_field(); ?>

                <div class="modal-body pd-20">
                  <form class="form-horizontal">
			<div class="form-group">
			<div>
			<!--<label for="Remaining" class="col-sm-3 control-label">Remaining Limits :<span id="remainingLimits"></span></label>
			<label for="Remaining" class="col-sm-3 control-label">Remaining Limits :<span id="remainingLimits"></span></label>-->
			</div>
				<table class="table">
					<thead class="thead-light">
					  <tr>
						<th>Sender Mobile </th>
						<th>Beneficiary Name</th>
						<th>IFSC</th>
						<th>Account No</th>
					  </tr>
					</thead>
					<tbody>
					  <tr>
						<td><span id="pSenderMobile"></span></td>
						<td><span id="pBeneName"></span></td>
						<td><span id="pBeneIFSC"></span></td>
						<td><span id="pBeneAccountNo"></span></td>
						
					  </tr>
					 </tbody>
				</table>
			</div>
			<div class="form-group">
				<table class="table">
					<thead class="thead-light">
					  <tr>
						<th>Transfer Amount </th>
						<th>Txn Charge</th>
						<th>Total</th>
						
					  </tr>
					</thead>
					<tbody id="pSlipTBody">
					  <!--<tr>
						<td><span id="pTxnAmount"></span></td>
						<td><span id="pTxnCharge"></span></td>
						<td><span id="pTotal"></span></td>
						
					  </tr>-->
					 </tbody>
				</table>
			</div>
			<span id="errorMessageAmountMissmatch" class="errorMessage"></span>
		</form>
              </div>
			  <div class="modal-footer">
	   <!--<button onclick="transferamount()" type="button" class="btn btn-info" value="add" id="payAndConfirmBtn">Pay & Confirm</button>-->
	    <button onclick="checkIsBankDownOrNot()" type="button" class="btn btn-info" value="add" id="payAndConfirmBtn">Pay & Confirm</button>
	    <img src="<?php echo e(url('/loader/loader.gif')); ?>" id="txnLoaderImg" class="loaderImg" style="display:none"/>
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
      </div>
            </div><!-- modal-dialog -->
          </div><!-- modal --> 
    </div>
	<div id="printTxnSlip" class="modal fade">
            <div class="modal-dialog modal-lg" role="document">
              <div class="modal-content tx-size-sm" style="width: 89%;margin-left: 10%; margin-top: 25%;">
                <div class="modal-header pd-x-20">
                
					<h4 class="modal-title">Transaction Details</h4>
                </div>
                <?php echo csrf_field(); ?>

                <div class="modal-body pd-20">
                  <form class="form-horizontal">
			<div class="form-group">
			<div>
			<!--<label for="Remaining" class="col-sm-3 control-label">Remaining Limits :<span id="remainingLimits"></span></label>
			<label for="Remaining" class="col-sm-3 control-label">Remaining Limits :<span id="remainingLimits"></span></label>-->
			</div>
				<table class="table">
					<thead class="thead-light">
					  <tr>
						<th>Sender Mobile </th>
						<th>Beneficiary Name</th>
						<th>IFSC</th>
						<th>Account No</th>
					  </tr>
					</thead>
					<tbody>
					  <tr>
						<td><span id="pSenderMobile"></span></td>
						<td><span id="pBeneName"></span></td>
						<td><span id="pBeneIFSC"></span></td>
						<td><span id="pBeneAccountNo"></span></td>
						
					  </tr>
					 </tbody>
				</table>
			</div>
			<div class="form-group">
				<table class="table">
					<thead class="thead-light">
					  <tr>
						<th>Transfer Amount </th>
						<th>Total</th>
						
					  </tr>
					</thead>
					<tbody>
					  <tr>
						<td><span id="pTxnAmount"></span></td>
						<td><span id="pTxnCharge"></span></td>
					
						
					  </tr>
					 </tbody>
				</table>
			</div>
		</form>
              </div>
			  <div class="modal-footer">
	   <button onclick="transferamount()" type="button" class="btn btn-info" value="add" id="payAndConfirmBtn">Pay & Confirm</button>
	    <img src="<?php echo e(url('/loader/loader.gif')); ?>" id="txnLoaderImg" class="loaderImg" style="display:none"/>
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
      </div>
            </div><!-- modal-dialog -->
          </div><!-- modal --> 
    </div>
	<div id="myReciept" class="modal fade" role="dialog" data-backdrop="static" data-keyboard="false">
               <div class="modal-dialog modal-lg" role="document">
                   

                    <!-- Modal content-->
                  <div class="modal-content tx-size-sm">
                        <div class="modal-body" style="padding: 10px; border-radius: 0px;">
                            <div class="col-md-12">
                                <button type="button" class="btn" data-dismiss="modal" style="padding: 6px ! important; top: -8px; right: -35px; background-color: rgb(255, 255, 255) ! important; position: absolute;">&times;</button>
                            </div>
                            <div class="containers" style="height: 500px; overflow: auto; width: 100%">
                                <div class="panel panel-primary">
                                    <div class="panel-heading" style="margin-bottom: 3px; padding: 7px;width:95%">Print / Download Receipt of Transaction ID : <?php echo e(@$report->txnid); ?><span id="prt_hdtranid"></span>

                                     <button class="btn btn-basic fullbtn" style="color:black !important; float: right; padding: 5px 8px; text-shadow: none;" onClick="printDiv()" id="printDiv"><i class="fa fa-print" style="margin-right: 4px;color: green"></i>PRINT</button></div>
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
                                                                        <b>Outlet Name :  <?php echo e(Auth::user()->member->company); ?></b>
                                                                    </div>
                                                                    <br />
                                                                    <div style="margin-top: -20px; float: left:10px;">
                                                                        <b>Contact Number: <?php echo e(Auth::user()->mobile); ?></b>
                                                                    </div>
                                                                </div>
                                                               <!--  <div class="col-md-4 col-sm-4 col-xs-4 text-right" style="padding:10px;" id="trandetailbyheadbps">
                                                                    <img src="<?php echo e(asset('newlog/images/bbps_print.png')); ?>" style="width:170px;margin-top:-90px;margin-left:600px">
                                                                </div> -->
                                                              <div class="col-md-4 col-sm-4 col-xs-4 text-right" style="padding:10px;float: right" id="trandetailbyheadnormal">
                                                                   <b>Receipt # :</b> R -<?php echo e(@$report->id); ?> <b id="prt_bdtranid"></b>
                                                                    <br /> 
                                                                    <b>Date : <?php echo e(date("d-m-Y H:i:s")); ?></b>
                                                                </div> 
																</div>
                                                            </th>


                                                        </tr>

                                                      
                                                       <tr></tr>
                                                       <tr style="border-top:1px solid #ddd;margin-left:60px" id="trandetailbydmt">
                                                            <td>
                                                                <b>Sender Name :<span id="slipSenderName"></span></b><br />
                                                                <b>Account Number : <span id="slipAccountNo"></span></b><br/>
                                                                <b>Bank Name : <span id="slipBankName"></span></b></br>
                                                                <b>IFSC Code : <span id="slipIFSC"></span></b>
                                                            </td>
                                                            <td>
                                                                <b>Sender Number : <span id="slipSenderNumber"></span></b><br />
                                                                <b>Beneficiary Name : <span id="slipbeneName"></span></b><br />
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
                                                                    <tr style="background:#ddd;">
                                                                        <td class="phead"><b>Date</b></td>
                                                                        <td class="phead"><b>Service Provider</b></td>
                                                                        <td class="phead"><b>Transaction ID </b></td>
                                                                        <td class="phead"><b>IMPS/UTR No.</b></td>
                                                                        <td class="phead"><b>Amount </b></td>
                                                                        <td class="phead"><b>Status </b></td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td ><span id="prt_trandate"></span><?php echo e(date("d-m-Y H:i:s")); ?></td>
                                                                        <td><span id="prt_tranoperator"><?php echo e("A2Z Wallet"); ?></span></td>
                                                                        <td><span id="slipTxnId"></span></td>
                                                                        <td><span id="slipTxnRefId"></span></td>
                                                                        <td><span id="slipTxnAmount"></span></td>
                                                                        <td><span id="slipTxnSataus">Success</span></td>
                                                                    </tr>
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
<meta name="_token" content="<?php echo csrf_token(); ?>"/>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>