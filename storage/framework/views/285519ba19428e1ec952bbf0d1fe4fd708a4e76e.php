<?php $__env->startSection('content'); ?>
 <?php echo $__env->make('layouts.submenuheader', \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>
 <style>
.table td, .table th {
    padding: 0px;
    vertical-align: top;
    border-top: 1px solid #dee2e6;
}
.errorMessage{
	color:red;
}</style>
<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.8.3/jquery.min.js"></script>
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
					$('#slipAmountInWord').css('color','black');
					console.log(msg);
					//$("#spanChargeAmount").text(txnChargeAmout);
					$("#slipAmountInWord").text(msg);
					$("#spanChargeAmount").text(finalAmount);
					
				}
            });
		
	}
function varification() {

	$(".loader").show();
	var username = $("input#number").val();
	$("#bene_mobile").val(username);
	var sessionid = $("input#sessionid").val();
	if (username.length == 10 && $.isNumeric(username)) {
	var token = $("input[name=_token]").val();
	var dataString = 'sessionid=' + sessionid + '&mobile_number=' + username + '&_token=' + token;
	$.ajax({
		type: "GET",
		url: "<?php echo e(url('validate-mobile')); ?>",
		data: dataString,
		dataType: "json",
		beforeSend: function () {
			$("#messagedata").hide();
			$("#verificationBtn").hide();
			$("#loaderImg").show();
		},
	success: function (msg) 
		{
			$("#messagedata").show();
			$("#reportButton").show();
			$("#verificationBtn").show();
			$("#loaderImg").hide();
			if (msg.status == "Remitter Not Found") 
			{
				$("#tbl").hide();
				$("#ben_frm").hide();
				$("#registration").show();
			}
			else if (msg.status == 'Transaction Successful') 
			{
				$('#senderName').html(msg.data.remitter.name)
				$('#remainingLimits').html(msg.data.remitter.remaininglimit)
				$("#slipSenderName").text(msg.data.remitter.name);
				$("#senderId").val(msg.data.remitter.id);
				if(msg.data.beneficiary == ''){
				$("#ben_frm").show();
				}
				else
				{
					$("#tbl").show();
					var obj2 = msg.data.beneficiary;
					var html = "";
					var ACCOUNT_NUMBER="number";
					for (var key in obj2) 
					{
						
						var rname = obj2[key].name;
						//alert(rname);
						var rrid = obj2[key].id;
						var ifsc = obj2[key].ifsc;
						var raccount = obj2[key].account;
						var bank_name = obj2[key].bank;
						var status = obj2[key].status;
						//alert(obj[key]["receiverid"]);
						html += "<tr>"
						html += '<input type="hidden" value="'+rname+'" id="rrid_'+rrid+'"><input type="hidden" value="'+ifsc+'" id="ifsc_'+rrid+'"><input type="hidden" value="'+bank_name+'" id="bank_'+rrid+'">';
						html += "<tr>";
						html += "<td>"+rname+"</td><td style='cursor: pointer; color:blue' onclick='getTranactionByField(\""+raccount+"\",\""+ACCOUNT_NUMBER+"\")'>"+raccount+"</td> <td> "+bank_name+"</td><td>"+ifsc+"</td>";
						if (status == 0) {
						html += '<td><div class="btn-group btn-sm right" role="group"> <button class="btn btn-sm tx-12 btn-danger" onclick="pending_resend_top(\'' + rrid + '\')"> De Active </button> </div></td>';
						}else{
						html += "<td> <button class='btn btn-sm btn-primary tx-12' onclick='transfermodal(\""+rrid+"\",\""+raccount+"\")'>Pay</button> <button class='btn btn-sm tx-12 btn-danger' onclick='Deletebene("+rrid +")'> <i class='fa fa-trash'></i> </button> </td>";
						}
						html += "</tr>";

					}
					$("#senderidn").val(msg["senderid"]);
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
			} 
			else if (msg.status == "OTP sent successfully") 
			{
				$("#tbl").hide();
				$("#registration").hide();
				$("#ben_frm").hide();	
				alert(msg.status)
				//$('#remitterOtpModel').modal('toggle');
				var remitter = msg.data.remitter;
				$("#remitterVerifyId").val(remitter.id);
				$('#remitterOtpModel').modal('toggle');
				
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
	var dataString = 'check_name=' + checkname + '&sessionid=' + sessionid + '&senderid=' + senderId + '&mobile_number=' + username + '&name=' + bname + '&bbank=' + bbank + '&bank_account=' + baccount + '&ifsc=' + bifsc + '&number=' + bmobile + '&bankcode=' + service_id + '&_token=' + token;
		$.ajax({
		type: "GET",
		url: "<?php echo e(url('cyber-api/add_beneficiary')); ?>",
		data: dataString,
		dataType: "json",
		beforeSend:function(){
			$("addBeneBtn").hide()
			$("beneLoaderImg").show()
			
		},
		success: function (msg) {
			$("addBeneBtn").show()
			$("beneLoaderImg").hide()
			if (msg.status == 'Transaction Successful') 
			{
				alert("Beneficiary add successfully")
				$("#ben_frm").hide();
				$("#tbl").show();
				$("#an").val('');
				$("#bn").val('');
				$("#bm").val('');
				$("#bank_account").val(baccount);
				$("#ifsc").val(bifsc);
				$("#first_name").val(bname);
				$("#bnv").val('');
				$("#BeneficiaryCode").val(msg.data.beneficiary.id);
				$("#senderIdb").val(senderid);
				$('#service_id').val('BANK NAME');
				$("#bene_mobile").val('');
				$("#fn").val('');
				$("#bankname").val('');
				varification();
				//$("#otp_frmb").show();//commented by Avinash
			//search();
			} else if (msg.status == '355') {
				$("#ben_frm").hide();
				$("#otp_frmb").show();
				$("#tbl").hide();
				resendotp();
			} else {
				alert(msg.status);
				$('#msgs').html("<div class='alert alert-danger'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a>" + obj.message + "</div>");
			}
		}
	});
}

function otpb() {
$(".loader").show();
var token = $("input[name=_token]").val();
var otpb = $("#otpvalb").val();
var sessionid = $("#sessionid").val();
var BeneficiaryCode = $("#BeneficiaryCode").val();
var number = $("#number").val();
var senderId = $("#senderId").val();
var dataString = 'senderid=' + senderId + '&beneficiaryid=' + BeneficiaryCode + '&mobile_number=' + number + '&otp=' + otpb + '&_token=' + token;
$.ajax({
type: "GET",
url: "<?php echo e(url('cyber-api/bene_confirm')); ?>",
data: dataString,
dataType: "json",
success: function (msg) {
$(".loader").hide();
if (msg.status == 'Transaction Successful') {
$("#an").val('');
$("#bn").val('');
$("#bank_account").val('');
$("#ifsc").val('');
$("#first_name").val('');
$("#bnv").val('');
$("#fn").val('');
$("#bankname").val('');
$("#otp_frmb").hide();
varification();
} else {
alert(msg.status);
}
}
});
}

function bene_resend_otp(){
$(".loader").show();
var token = $("input[name=_token]").val();
var senderid = $("#senderId").val();
var BeneficiaryCode = $("#BeneficiaryCode").val();
if(BeneficiaryCode == ''){
var BeneficiaryCode = $("#bene3").val();
}else{
var BeneficiaryCode = BeneficiaryCode;
}
var number = $("#number").val();
var baccount = $("#bank_account").val();
var bifsc = $("#ifsc").val();
var bname = $("#first_name").val();
var dataString = 'senderid=' + senderid + '&BeneficiaryCode=' + BeneficiaryCode + '&mobile_number=' + number + '&account_number=' + baccount + '&ifsc=' + bifsc + '&name=' + bname + '&_token=' + token;
$.ajax({
type: "get",
url: "<?php echo e(url('cyber-api/beneconform_resend_otp')); ?>",
data: dataString,
dataType: "json",
success: function (msg) {
$(".loader").hide();
if (msg.status == 'Transaction Successful') {
alert('Otp sent on your sender number.');
} else {
alert(msg.status);
}
}
});
}

	function registration() {
		$("#newnextstep").text("Loading....");
		$("#newnextstep").attr('disabled', true);
		var token = $("input[name=_token]").val();
		fname = $("#fname").val();
		lname = $("#lname").val();
		pincode = $("#pincode").val();
		if (fname != '' && pincode != '' && lname != '') 
		{
			var dataString = 'fname=' + $("#fname").val() + '&lname=' + $("#lname").val() +  '&pincode=' + $("#pincode").val() + '&mobile_number=' + $("input#number").val() + '&_token=' + token;
			$.ajax({
				type: "GET",
				url: "<?php echo e(url('cyber-api/add-sender')); ?>",
				data: dataString,
				dataType: "json",
				beforeSend:function()
				{
					$("#registrationButton").hide();
					$("#registrationLoader").show();
				},
				success: function (msg) {
				console.log(msg);
				$("#registrationButton").show();
					$("#registrationLoader").hide();
				if (msg.status == 'Transaction Successful') 
				{
					$("#otp_frm").hide();
					$('#msgs').html("<div class='alert alert-success'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a>" + msg['message'] + "</div>");
					$("#ben_frm").show();
					$("#bankname").show();
					$("#lastrpt").hide();
					$("#registration").hide();
					$("#senderId").val(msg.data.remitter.senderid);
					varification();
				}
				else if (msg.status == "OTP sent successfully") 
				{
					$("#tbl").hide();
					$("#registration").hide();
					$("#ben_frm").hide();	
					alert(msg.status)
					//$('#remitterOtpModel').modal('toggle');
					var remitter = msg.data.remitter;
					$("#remitterVerifyId").val(remitter.id);
					$('#remitterOtpModel').modal('toggle');
					
				}
				else
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


function verifynow() {
//alert('hi');
// $(".loader").show();
	var token = $("input[name=_token]").val();
	var ifsc = $("#service_id").val();
	var bifsc = $("#ifsc").val();
	var number = $("#number").val();
	var bank_account = $("#bank_account").val();
	if (bank_account != '') 
	{
		if(bifsc == ''){
			$("#ifsc").focus();
		}else
		{
			var dataString = 'bankcode=' + ifsc + '&ifsc=' + bifsc + '&mobile_number=' + number + '&bank_account=' + bank_account + '&_token=' + token;
			$(".loader").show();
			$.ajax({
			type: "GET",
			url: "<?php echo e(url('cyber-api/account-name-info')); ?>",
			data: dataString,
			dataType: "json",
			beforeSend:function()
			{
				$("#bnv").hide();
				$("#accVerifyLoaderImg").show(); 
			},
			success: function (msg) {
					$("#bnv").show();
					$("#accVerifyLoaderImg").hide(); 
					$(".loader").hide();
					if (msg.statuscode == "TXN") 
					{
						$("#otp_frm").hide();
						$("#ben_frm").show();
						$("#bene_mobile").val(number);
						$("#bn").show();
						$("#bm").show();
						$("#ic").hide();
						$("#first_name").val(msg.data.benename);
						$("#fn").show();
						
						alert(msg.status);
					
					
					}
					else {
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


function Deletebene(beneid) {
$(".loader").show();
var token = $("input[name=_token]").val();
var number = $("#number").val();
var senderId = $("#senderId").val();
var sessionid = $("input#sessionid").val();
var dataString = 'senderid=' + senderId + '&sessionid=' + sessionid + '&mobile_number=' + number + '&beneficiary_id=' + beneid + '&_token=' + token;
$.ajax({
type: "GET",
url: "<?php echo e(url('cyber-api/delete_beneficiary')); ?>",
data: dataString,
dataType: "json",
success: function (msg) {
$(".loader").hide();
if (msg.status == 'Transaction Successful') {
alert('Otp sent on your sender number.');
$("#del_otp").val('');
$("#bendelete").modal('show');
$("#del_beneid").val(beneid);
$("#del_senderid").val(senderId);
} else {
alert(msg.status);
}
}
});
}

function Deletebene_confirm(){
$(".loader").show();
var beneid = $("#del_beneid").val();
var token = $("input[name=_token]").val();
var number = $("#number").val();
var senderId = $("#del_senderid").val();
var otp = $("#del_otp").val();
var dataString = 'senderid=' + senderId + '&mobile_number=' + number + '&beneficiary_id=' + beneid + '&otp=' + otp + '&_token=' + token;
$.ajax({
type: "GET",
url: "<?php echo e(url('cyber-api/bene_confirm_delete')); ?>",
data: dataString,
dataType: "json",
success: function (msg) {
$(".loader").hide();
if (msg.status == 'Transaction Successful') {
alert('Beneficiary Delete Successfully.');
$("#bendelete").modal('hide');
varification();
} else {
alert(msg.status);
}
}
});
}

function transfermodal(id, account){
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
		alert("Please Enter Transaction Pin");
		return false;
	}
	var amount = $("#amount").val()
	if(amount >25000){
		$("#amount").focus();
		alert("Amount can not be greather than 25000");
		return false;
	}
	if(amount>remainingLimits){
		$("#amount").focus();
		alert("Amount can not be greather than Your Remaining Limit  : " +remainingLimits);
		return false;
	}
	dataString="amount="+ amount;
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
	var confirmAmount = $("#confirmAmount").val();
	if(amount != confirmAmount){
		alert("Enter amount and confirm amount missmatched");
		return false;
	}
	if(amount <10 && amount >25000)
		{
		alert("Amount Should be between Rs. 10 - 2500");
		return false();
	}
	var mode = $("#mode").val();
	var token = $("input[name=_token]").val();
	var senderid = $("#number").val();
	var mobile_number = $("#number").val();
	var beneid = $("#transferId").val();
	var ifsc = $("#transferIfsc").val();
	var channel = $("#channel").val();
	var bank_account = $("#transferAccount").val();
	var bank_name = $("#transferBank").val();
	var user_id = $("#user_id").val();
	var senderName = $("#senderName").text();
	var dataString = 'senderid=' + senderid + '&bank_name=' + bank_name + '&ifsc=' + ifsc + '&channel=' + channel + '&bank_account=' + bank_account + '&user_id=' + user_id + '&mobile_number=' + mobile_number + '&beneficiary_id=' + beneid + '&amount=' + amount + '&_token=' + token+ '&senderName=' + senderName;
	$.ajax({
		type: "POST",
		url: "<?php echo e(url('cyber-api/transaction')); ?>",
		data: dataString,
		dataType: "json",
		beforeSend: function () {
			
			$("#payAndConfirmBtn").attr('disabled',true)
			$("#payAndConfirmBtn").hide();
			$("#txnLoaderImg").show();
			$("#spanChargeAmount").text('');
			$("#slipMobNo").text('');
			$("#slipAccountNo").text('');
			$("#slipBankName").text('');
			$("#slipIFSC").text('');
		},
		
		success: function (res) 
		{
			//console.log(res);
			var slipRRId = content='';
			var isModelShown = false;
			$("#spanChargeAmount").html(amount);
			$("#slipMobNo").html(mobile_number);
			$("#slipAccountNo").html(bank_account);
			$("#slipBankName").html($("#transferBank").val());
			$("#slipIFSC").html(ifsc);
			$("#payAndConfirmBtn").show();
			$("#payAndConfirmBtn").attr('disabled',false)
			$("#txnLoaderImg").hide();
			var noOfRecord=0;
			$.each(res.result, function (key, val) 
			{
				noOfRecord++;
			});
			$.each(res.result, function (key, val) 
            {
				
				//console.log(val);
				msg=val;
				//console.log(msg.status);
				if (msg.status == 'Failure') 
				{
					alert(msg.message);
					return false;
				}
				if(msg.status == "FAILED" && noOfRecord == 1)
				{
						alert(msg.message);
				}
				else if(msg.status == "SUCCESS" || msg.status == "FAILED" || msg.status == "PENDING")
				{
					isModelShown=true;
					refreshBalance();
					$("#amount").val()
					$("#preTxnSlip").modal('hide')
					$("#payAndConfirmBtn").show();
					$("#payAndConfirmBtn").attr('disabled',false)
					$("#txnLoaderImg").hide();
					$("#transfermodal").modal('hide');
					content += val.txnId+",";
					slipRRId += val.refNo+", ";
					/* $("#slipTxnRefId").html(msg.refNo);
					$("#slipTxnSataus").html(msg.status);
					$(".slipTxnId").html(msg.txnId);
					$("#slipTxnAmount").html(amount);
					
					 */
					content +='<tr><td>'+val.txnTIme+'</td><td>MONEY</td><td>'+val.txnId+'</td><td>'+val.refNo+'</td><td>'+val.amount+'</td><td>'+val.status+'</td></tr>';
					//$("#myReciept").modal('show');
				}
			});
			if(isModelShown)
			{
				$("#txnPrintSlip").html(content);
				$("#slipRRId").html(slipRRId);
				$("#myReciept").modal('show');
			}	
		}
	});
}

// last


function pending_resend_top(id) {
$(".loader").show();
var token = $("input[name=_token]").val();
var otpb = $("#state_otp").val();
var senderid = $("#senderId").val();
var BeneficiaryCode = id;
// var number = $("#state_mobile").val();
var number = $("#number").val();
var dataString = 'senderid=' + senderid + '&BeneficiaryCode=' + BeneficiaryCode + '&mobile_number=' + number + '&_token=' + token;
$.ajax({
type: "get",
url: "<?php echo e(url('cyber-api/beneconform_resend_otp')); ?>",
data: dataString,
dataType: "json",
success: function (msg) {
$(".loader").hide();
if (msg.status == 'Transaction Successful') {
$("#state-change-model").modal("show");
$("#bene3").val(BeneficiaryCode);;
} else {
alert(msg.status);
}
}
});
}

function bene_confirm_pending() {
$(".loader").show();
var token = $("input[name=_token]").val();
var otpb = $("#state_otp").val();
var senderId = $("#senderId").val();
var BeneficiaryCode = $("#bene3").val();
var number = $("#number").val();
var dataString = 'senderid=' + senderId + '&beneficiaryid=' + BeneficiaryCode + '&mobile_number=' + number + '&otp=' + otpb + '&_token=' + token;
$.ajax({
type: "GET",
url: "<?php echo e(url('cyber-api/bene_confirm')); ?>",
data: dataString,
dataType: "json",
success: function (msg) {
$(".loader").hide();
if (msg.status == 'Transaction Successful') {
alert('Beneficiary Acivate Successfully.');
$("#state-change-model").modal("hide");
$("#otp_frmb").hide();
$("#otpnewa").val('');
varification();
} else {
alert(msg.status);
}
}
});
}

function getIfsc() {
// var bankcode = obj.options[obj.selectedIndex].value;
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
			if(obj.status == 0){
			$("#ifsc").val("");
			alert('Ifsc code not found.');
			}else{
			$("#ifsc").val(obj.ifsc);
			}

			}
		});
	} 
	else 
	{
		alert("OTP Required, Please enter OTP");
	}
}

function remitterOTP()
{
	var username = $("input#number").val();
	var remitterOTP = $("input#remitterOTP").val();
	var remitterVerifyId = $("input#remitterVerifyId").val();
	if (remitterOTP != '') 
	{
		var dataString = 'username=' + username + '&remitterOTP=' + remitterOTP+ '&remitterVerifyId=' + remitterVerifyId;
			$.ajax({
			type: "GET",
			url: "<?php echo e(url('cyber-api/verify-otp')); ?>",
			data: dataString,
			dataType:"json",
			beforeSend:function()
			{
				$("#remitterVerifyLoader").show();
				$("#remitterOTPButton").hide();
				
			},
			success: function (msg) {
				$("#remitterVerifyLoader").hide();
				$("#remitterOTPButton").show();
				if(msg.statuscode=="TXN")
				{
					alert("Remitter verified successfully");
					$("#remitterVerifyId").val('');
					$('#remitterOtpModel').modal('toggle');
					varification();

				}
				else{
					alert(msg.status);
				}

			}
		});
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
				if(msg.status == 1){
					$("#payAndConfirmBtn").attr('disabled',false)
					$("#payAndConfirmBtn").show();
					$("#errorMessageAmountMissmatch").text(msg.message);					
				}
				else if(msg.status == 0){
						transferamount()
				}
				else{
					alert("Whoops Something went wrong");
				}
			}
		});
}

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
function getTranactionByField(searchNumber,searchType) {
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
</script>
<?php echo $__env->make('agent.money.money-type', \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>
<div class="text-right">
	<a href="<?php echo e(route('transaction-report')); ?>?product=4&searchOf=3" class="textheader">Pending </a>&nbsp;|&nbsp;
	<a href="<?php echo e(route('transaction-report')); ?>?product=4&searchOf=4" class="textheader">Refunded</a>&nbsp;|&nbsp;
	<a href="<?php echo e(route('transaction-report')); ?>?product=4&searchOf=20" class="textheader">Refund Pending</a>&nbsp;|&nbsp;
	<a href="<?php echo e(route('transaction-report')); ?>?product=4&searchOf=1" class="textheader"> Transaction History</a>
</div>     
<div class="row row-sm">        
    <div class="col-md-4 mg-t-20">
        <div class="card bd-0 shadow-base">
            <div class="">
			<a href="#" class="pull-right"  onClick="getTransactionOfMobile()" style="display:none" id="reportButton">Report</a>
            	<?php echo $__env->make('partials.mobile-number-report', \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>
            	  <img src="<?php echo e(url('/loader/loader.gif')); ?>" id="mobileTxnLoader" class="loaderImg">
               <div class="d-flex" style="padding: 2%;">
		            <div class="form-group mg-b-0">
						<input type="text" name="number" id="number" class="form-control" placeholder="Enter Mobile Number" style="font-weight: bold;font-size:20px;color:darkslategrey" required  maxlength="10" autocomplete="off" readonly="" onkeyup="callAutoFuncion()">
		            </div><!-- form-group -->
		            <div class="mg-l-10 mg-t-25 pd-t-4">
		                <button type="submit" class="btn btn-success btn-md" onclick="callAutoFuncion()" id="verificationBtn"><i class="fa fa-search"></i></button><img src="<?php echo e(url('/loader/loader.gif')); ?>" id="loaderImg" class="loaderImg"> 
		                <a href="<?php echo e(Request::url('')); ?>" class="btn btn-primary btn-md" ><i class="fa fa-refresh"></i></a>
		            </div>
			    </div>
			   	<p id="messagedata" style="display:none">
					<span style="font-weight: bold;"> Sender Name : </span><span id="senderName"></span>
					<span style="font-weight: bold;"> Remaining Limit : </span><span id="remainingLimits"></span>
				</p>    
            </div><!-- card-body -->
        </div><!-- card -->

              <!-- <div class="col-md-4 mg-t-20" id="ben_frm"> -->
                    
		<div id="ben_frm" style="display: none;">
			<input type="hidden" value="" id="senderidn"/>

			<div class="">
				Add Beneficiary
				
				<div><label id="bbank">Bank Name</label></div>

				<div class="" style="padding-bottom: 1%;">
					
					<input list="browsers" id="service_id" class="form-control" onchange="getIfsc()"/>
					<datalist id="browsers">

					<?php $__currentLoopData = $netbanks; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $netbank): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
						<option value="<?php echo e($netbank->bank_name); ?>">
					<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
					</datalist>
				</div><!-- form-group -->
				
				<div class="" id="ic" style="padding-bottom: 1%;">
					<input type="text" class="form-control" id="ifsc" placeholder="IFSC Code">
					<span class="input-group-btn right">
					<button id="ifscbtn" class="btn cursor btn-primary mg-t-40-min" type="button" onclick="getIfsc();" style="float: right;top: -34px; display:none;">get ifsc</button> 
					</span>
				</div><!-- form-group -->
				<div class="" id="an">
					<input type="text" class="form-control" id="bank_account" placeholder="Bank Account Number"> 
				</div>
			       <button id="bnv" class="btn btn-primary mg-t-40-min" type="button" onclick="verifynow();" style="float: right;">Verify</button>
					<img src="<?php echo e(url('/loader/loader.gif')); ?>" id="accVerifyLoaderImg" class="loaderImg" style="margin-top: 16px;display:none"/>
					

				<!-- form-group -->

				<div class="" id="fn">
					<input type="text" class="form-control" id="first_name" placeholder="Beneficiary Name">
				</div><!-- form-group --> 

				<div class="form-group" id="bm" style="display: none;">
					<input type="hidden" class="form-control" id="bene_mobile" placeholder="Mobile Number" readonly>
				</div><!-- form-group -->
				<div>
				<button class="btn btn-info bd-0 btn-oblong" onclick="addbene();" id="addBeneBtn">Add Bene</button>
				<img src="<?php echo e(url('/loader/loader.gif')); ?>" id="beneLoaderImg" class="loaderImg" style="display:none"/>
				</div>

			</div><!-- card-body -->
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
				<div class="card-title">Register Customer</div>
				
				<div class="form-group">
					<input type="text" id="fname" class="form-control" placeholder="First Name">
				</div>
				<div class="form-group">
                    <input type="text" id="lname" class="form-control" placeholder="Last Name">
                </div>
				<!-- form-group -->

				<div class="form-group">
					<input type="text" id="pincode" class="form-control" placeholder="Pin Code">
				</div><!-- form-group -->


				
			</div><!-- card-body -->
		  	<div class="card-footer mg-t-auto">
				<button id ="registrationButton" class="btn btn-info bd-0 btn-oblong" onclick="registration();">Next Step</button>
				 <img src="<?php echo e(url('/loader/loader.gif')); ?>" id="registrationLoader" class="loaderImg" style="margin-top: 16px;display:none"/>
		  	</div><!-- card-footer -->
		</div>


		<div class="card bd-0 shadow-base mg-t-20" id="otp_frm" style="display: none;">
			<input type="hidden" value="" id="senderId"/>
		  <div class="card-body">
			<div class="card-title">Register Customer</div>
			<span class="success" id="sendercreate"
							  style="display: none;"></span>
			<div class="form-group">
				<label>Enter Otp</label>
				<input type="text" id="otpval" class="form-control">
				<span id="success_msg" style="display: none;">Please Enter Otp (OTP will be Expired in 60 Min)</span>
			</div><!-- form-group -->

			
		  </div><!-- card-body -->
		  <div class="card-footer mg-t-auto">
			<button class="btn btn-info bd-0 btn-oblong" onclick="otp();">Confirm</button>
			<button class="btn btn-info bd-0 btn-oblong" onclick="resendotp();">Resend Otp</button>
		  </div><!-- card-footer -->
		</div>

    </div>

	<div class="col-md-8 mg-t-20">
		<div class="card bd-0 shadow-base" id="tbl">
		<div class="card-header bg-info bd-0 d-flex align-items-center justify-content-between pd-y-5">
		  <div class="card-header tx-medium bd-0 tx-white bg-info">
                  		Beneficiary List
                	</div>
			  <!--<h6 class="mg-b-0 tx-14 tx-white tx-normal">Beneficiary List</h6>-->
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
              <div class="modal-content tx-size-md" style="">
                <div class="modal-header pd-x-20">
                  <h4 class="tx-14 mg-b-0 tx-uppercase tx-inverse tx-bold"><span class="benename"></span> - <span class="account_number"><</span></h4>
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
                    <input type="password" id="txn_pin" class="form-control pd-y-12" placeholder="Transaction Pin">
                </div>
               <!-- <div class="form-group">
                    <input type="hidden" id="txn_pin" class="form-control" value="<?php echo e(Auth::user()->profile->txn_pin); ?>">
                </div>-->
                <div class="modal-footer">
                  <button type="button" class="btn btn-primary tx-size-xs" onclick="preTxnSlip()" id="txnBtn">Sumbit</button>
				  <img src="<?php echo e(url('/loader/loader.gif')); ?>" id="loaderTrnsImg" class="loaderImg" style="margin-top: 16px;display:none"/>
				  
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
                    <input type="text" id="state_otp" class="form-control pd-y-12" placeholder="Enter Otp">
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
            <div class="modal-dialog modal-lg" role="document">
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
                  <button type="button" class="btn btn-primary tx-size-xs" onclick="Deletebene_confirm()">Delete</button>
				  <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
              </div>
            </div><!-- modal-dialog -->
          </div><!-- modal --> 
    </div>
                   
              
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
	<div class="modal fade" id="remitterOtpModel" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                     <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                aria-hidden="true">&times;</span></button> 
                    <h4 class="modal-title" id="myModalLabel">Verify Remitter Mobile Number</h4>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="remitterVerifyId">
                    <form class="form-horizontal">
                        <div class="form-group">
                            <label for="remitterOTP" class="control-label col-sm-4">
                                OTP </label>
                            <div class="col-sm-6">
                                <input type="text" class="form-control" id="remitterOTP"
                                       placeholder="Entrer OTP">
                            </div>
                        </div>

                    </form>
                </div>
                <div class="modal-footer">
                   <button id="remitterOTPButton" type="button" onclick="remitterOTP()" class="btn btn-primary">Confirm</button>
				   <img src="<?php echo e(url('/loader/loader.gif')); ?>" id="remitterVerifyLoader" class="loaderImg" style="margin-top: 16px;display:none"/>
				   <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
<div id="preTxnSlip" class="modal fade">
            <div class="modal-dialog modal-md" role="document">
              <div class="modal-content tx-size-sm" >
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
						<th>Charge</th>
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
	<?php echo $__env->make('common.transactionbyField', \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>
<div id="myReciept" class="modal fade" role="dialog" data-backdrop="static" data-keyboard="false">
	   <div class="modal-dialog modal-lg" role="document">
		   

			<!-- Modal content-->
		  <div class="modal-content tx-size-sm" >
				<div class="modal-body">
					<div class="col-md-12">
						<button type="button" class="btn" data-dismiss="modal" style="padding: 6px ! important; top: -8px; right: -35px; background-color: rgb(255, 255, 255) ! important; position: absolute;">&times;</button>
					</div>
					<div class="containers" style="height: 500px; overflow: auto; width: 100%">
						<div class="panel panel-primary">
							<div class="panel-heading" style="margin-bottom: 3px; padding: 7px;width:95%">Print / Download Receipt of Transaction ID : <?php echo e(@$report->txnid); ?><span id="prt_hdtranid"></span>

							 <!--<button class="btn btn-basic fullbtn" style="color:black !important; float: right; padding: 5px 8px; text-shadow: none;" id="btnPrint"><i class="fa fa-print" style="margin-right: 4px;color: green"></i>PRINT</button>--> <button class="btn btn-basic fullbtn" style="color:black !important; float: right; padding: 5px 8px; text-shadow: none;" onClick="printDiv()"><i class="fa fa-print" style="margin-right: 4px;color: green"></i>PRINT</button></div>
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
													   <!--  <div class="col-md-4 col-sm-4 col-xs-4 text-right" style="padding:10px;" id="trandetailbyheadbps">
															<img src="<?php echo e(asset('newlog/images/bbps_print.png')); ?>" style="width:170px;margin-top:-90px;margin-left:600px">
														</div> -->
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
														<b>Sender Name :<span id="slipSenderName"></span></b><br />
														<b>Account Number : <span id="slipAccountNo"></span></b><br/>
														<b>Bank Name : <span id="slipBankName"></span><br/>
														<b>IFSC Code : <span id="slipIFSC"></span></b>
													</td>
													<td class="pull-right">
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
															<thead class="thead-light">
																<tr style="background:#ddd;">
																	<td class="phead"><b>Date</b></td>
																	<td class="phead"><b>Service Provider</b></td>
																	<td class="phead"><b>Transaction ID </b></td>
																	<td class="phead"><b>IMPS/UTR No.</b></td>
																	<td class="phead"><b>Amount </b></td>
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
	<!--<div id="myReciept" class="modal fade" role="dialog" data-backdrop="static" data-keyboard="false" style="overflow-y: scroll;">
	   <div class="modal-dialog modal-md" role="document">
		<div class="modal-content tx-size-md">
				<div class="modal-body">
					<div class="col-md-12 from-inline">
						<div class="col-md-12"></div>
						<div id="dvContents">
							<table class="table">
								<tr> 
									<td><img src="<?php echo e(asset('newlog/images/Logo168.png')); ?>" class="img-rounded" alt="a2z suvidhaa" width="50" height="50"></td><td class="pull-right"><span>Outlet Name : <?php echo e(Auth::user()->member->company); ?></span><br><span>Retailer Mob : <?php echo e(Auth::user()->mobile); ?></span></td>
								</tr>
							</table>
							<div><b>Payment Confirmation</b><br>
								
							</div>
							<table class="table">
								<tr><td>Sender Name :</td><td><span id="slipSenderName"></span></td></tr>
								<tr><td>Sender Mob No :</td><td><span id="slipMobNo"></span></td></tr>
								<tr><td>Beneficiary Name :</td><td><span id="slipbeneName"></span></td></tr>
								<tr><td>Bene Bank Name :</td><td><span class="bank"></span></td></tr>
								<tr><td>IFSC Code :</td><td><span id="slipIFSC"></span></td></tr>
								<tr><td>Account Number :</td><td><span id="slipAccountNo"></span></td></tr>
								<tr><td> Transaction Value :</td><td><span id="slipTxnValue"></span></td></tr>
								<tr><td> Txn Number :</td><td><span id="slipTxnId"></span></td></tr>
								<tr><td> RR Number :</td><td style="width : 50%"><span id="slipRRId"></span></td></tr>
								<tr><td> Txn Date :</td><td><span id="slipTxnDate"><?php echo e(date("Y-m-d H:i:s")); ?></span></td></tr>
								<tr><td> Transaction Type :</td><td><span id="">DMT 1</span></td></tr>
								<tr><td> Payment Mode :</td><td>IMPS</span></td>
								</tr>
								<tr><td> Txn Charge :</td><td><span id="spanChargeAmount"></span>
								<span id="updageFeeSpan"><input type="text" id="txnChargeAmout" onfocusout="amountInWords()"></span>
							</td></tr>
							<tr><td> Total Amount :</td><td><span id="totalAmount"></span>
								
							</td></tr>
							<tr><td> Amount In words :</td><td><span id="amountInWords"></span>
							</td></tr>
							</table>
						</div>
						<div class="col-md-6"></div>
					</div>
					
				</div>
				<div class="modal-footer">
						<button id="btnPrint"class="btn btn-primary" onClick="printDiv()">PRINT</button>
						<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
				</div>
			</div>
		</div>
	</div>	-->			
<meta name="_token" content="<?php echo csrf_token(); ?>"/>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>