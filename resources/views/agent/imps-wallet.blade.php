@extends('layouts.app')

@section('content')
 @include('layouts.submenuheader')

<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.8.3/jquery.min.js"></script>

<script type="text/javascript">
    $(function () {
        $("#btnPrint").click(function () {
            var contents = $("#dvContents").html();
            var frame1 = $('<iframe />');
            frame1[0].name = "frame1";
            frame1.css({ "position": "absolute", "top": "-1000000px" });
            $("body").append(frame1);
            var frameDoc = frame1[0].contentWindow ? frame1[0].contentWindow : frame1[0].contentDocument.document ? frame1[0].contentDocument.document : frame1[0].contentDocument;
            frameDoc.document.open();
            //Create a new HTML document.
            frameDoc.document.write('<html><head><title>DIV Contents</title>');
            frameDoc.document.write('</head><body>');
            //Append the external CSS file.
            frameDoc.document.write('<link rel="stylesheet" href="../../Content/css/bootstrap.min.css" type="text/css" media="print" />');
            frameDoc.document.write('<link rel="stylesheet" href="../../Content/css/printReceipt.css" type="text/css" media="print" />');
            frameDoc.document.write('<link href="style.css" rel="stylesheet" type="text/css" />');
            //Append the DIV contents.
            frameDoc.document.write(contents);
            frameDoc.document.write('</body></html>');
            frameDoc.document.close();
            setTimeout(function () {
                window.frames["frame1"].focus();
                window.frames["frame1"].print();
                frame1.remove();
            }, 500);
        });
    });
	
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
function varification() 
{

	$(".loader").show();
	var username = $("input#number").val();
	var sessionid = $("input#sessionid").val();
	if (username.length == 10 && $.isNumeric(username)) {
	var token = $("input[name=_token]").val();
	var dataString = 'sessionid=' + sessionid + '&mobile_number=' + username + '&_token=' + token;
	$.ajax({
		type: "GET",
		url: "{{url('imps-mobile-verification')}}",
		data: dataString,
		dataType: "json",
		beforeSend: function () {
			$("#number").attr('readonly','readonly')
			$('#messagedata').hide();
			$('#senderName').text("");
			$("#verificationBtn").hide();
			$("#remainingLimits").text('');
			$("#name-error").html('');
			$("#loaderImg").show();
		},
		success: function (msg) 
		{
			$("#verificationBtn").show();
			$("#loaderImg").hide();
			$('#messagedata').show();
			if(msg.status==13)
			{
				$('#senderName').html(msg.senderName);
				/* $('#remainingLimits').html(msg.message.rem_bal); */
				sender_limit();
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
				$("#transactionId").val(msg.transaction_id)
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
			else if(msg.status == 10){
				var errorString = '<div class="alert alert-danger"><ul>';
								$.each(msg.message, function (key, value) {
									errorString += '<li>' + value + '</li>';
								});
								errorString += '</ul></div>';
								$("#name-error").show();
								$('#name-error').html(errorString); //appending to a <div id="form-errors"></div> inside form
								$('#name-error').focus();
			}
			else
				alert(msg.message)
		}
		});
	}
	else{
		alert("Enter Correct Mobile Number")
	}
}
function sender_limit()
    {
        var number = $('#number').val();
        var dataString = 'mobile_number=' + number;
        $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                }
            })
        $.ajax({
                    type: "get",
                    url: "{{url('/imps-sender-limit')}}",
                    data: dataString,
                    dataType: "json",
                    beforeSend: function () {
                        
                    },
                    success: function (msg) {
                            if(msg.status_id==15)
                            {
                                $('#limit_msg').show();
								x = msg.limit.split('.');
								y = msg.limit.split('.');
                                $('#remainingLimits').text(x[0]);
                                //$('#get_bal_remaining').text('Remaining Limit: '+ y[0]);
							}
                            else
                            {
                                alert(msg.message);
                                return false;
                            }
                           

                        }
                });
    }
function get_bene()
	{
		   var mobile_number = $("input#number").val();
            if (mobile_number!='') 
			{
                var dataString = 'mobile_number=' + mobile_number;
                
                $.ajax({
                    type: "get",
                    url: "{{url('/get-bene-list')}}",
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
							  
								var jsonContent = msg.message;
								var obj2 = jQuery.parseJSON(jsonContent).data;
								console.log(obj2);
								var html = "";
								for (var key in obj2) 
								{
									
									var rname = obj2[key].beneName;
									//alert(rname);
									var rrid = obj2[key].beneId;
									var ifsc = obj2[key].ifscCode;
									var raccount = obj2[key].accountNumber;
									var bank_name = obj2[key].bankName;
									var status = obj2[key].status_id;
									//alert(obj[key]["receiverid"]);
									html += "<tr>"
									html += '<input type="hidden" value="'+rname+'" id="rrid_'+rrid+'"><input type="hidden" value="'+ifsc+'" id="ifsc_'+rrid+'"><input type="hidden" value="'+bank_name+'" id="bank_'+rrid+'">';
									html += "<tr>";
									html += "<td><div class='d-flex align-items-center'><div class='mg-l-0'> <div class='tx-inverse'> "+rname+" </div> <div class='tx-inverse'> "+raccount+" </div> <div class='tx-inverse'> "+bank_name+" </div> </div></div></td>";
									if (status == 1) {
									html += '<td><div class="btn-group btn-sm right" role="group"> <button class="btn btn-sm tx-12 btn-danger" onclick="pending_resend_top(\'' + rrid + '\')"> De Active </button> </div></td>';
									}else{
									html += "<td><div class='btn-group btn-sm right' role='group'> <button class='btn btn-sm tx-12 btn-danger' onclick='Deletebene("+rrid +")'> <i class='fa fa-trash'></i> </button> </div> <div class='btn-group btn-sm right' role='group'> <button class='btn btn-sm btn-success tx-12' onclick='transfermodal(\""+rrid+"\",\""+raccount+"\",\""+rname+"\")'>TRANSFER</button> </div></td>";
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
		url: "{{url('imps-bene-add')}}",
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
			url: "{{url('remitter-register')}}",
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
		var transactionId = $('#transactionId').val();
		var number_pat=/^[0-9]+$/;
		if(otp =='')
		{
			alert("Please Enter OTP");
			$("#remitterOtp").focus();
			return false;
		}
		else if(otp.length !=6)
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
			
		var dataString = 'mobile=' + number +'&otp='+otp+'&transactionId='+transactionId;
		 $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                }
            })
			 $.ajax({
                    type: "PUT",
                    url: "{{url('imps-mobile-verification-with-otp')}}/"+number,
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
							else 
							{
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
			url: "{{url('cyber-api/account-name-info')}}",
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
				if (msg.status == 1) 
				{
					alert(msg.message);
					/* $("#otp_frm").hide();
					$("#ben_frm").show(); */
					/* $("#bene_mobile").val(number); */
					/* $("#bn").show();
					$("#bm").show();
					$("#ic").hide(); */
					$("#first_name").val(msg.beneName);
					$("#fn").show();
				} 
				if (msg.statuscode == 'TXN') 
				{
					/* $("#otp_frm").hide();
					$("#ben_frm").show();
					$("#bene_mobile").val(number);
					$("#bn").show();
					$("#bm").show();
					$("#ic").hide(); */
					$("#first_name").val(msg.data.benename);
					$("#fn").show();
				}
				else 
				{
					alert(msg.message);
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
			url: "{{url('bene-delete-request')}}/"+beneId,
			data: dataString,
			dataType:"json",
			success: function (msg) {
				console.log(msg);
				
				if (msg.status == 37) {
					alert(msg.message);
					$("#bendelete").modal('show');
					$("#del_beneid").val(beneId);
				} else {
					alert(msg.message);
				}
			}
		});
	}
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
		url: "{{url('confirm-bene-delete')}}",
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
				alert(mst.message)
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
	if(amount=='')
	{
		alert("Please Enter Transaction Amount");
		return false;
	}
	else if(!amount.match(number_pat))
	{
		alert("Amount should be number");
		return false;
	}else if(txn_pin=='')
	{
		alert("Please Transaction Pin Amount");
		return false;
	}
	var amount = $("#amount").val()
	if(amount >50000){
		$("#amount").focus();
		alert("Amount can not be greather than 50000");
	}
	dataString="amount="+ amount +"&txnChargeApiName=TRAMO";
	$.ajax({
		type: "GET",
		url: "{{url('get-agent-charge-amt')}}",
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
						content +='<tr><td>Total Amount</td><td></td><td>'+msg.totalAmount+'</td></tr>';
						content +='<tr><td>Transfer Amount</td><td></td><td><button class="btn btn-primary" disabled>Rs. '+amount+'</button></td></tr>';
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
		});
	//$("#preTxnSlip").modal('show')
	
}

function transferamount (){

	var amount = $("#amount").val();
	if(amount <10 && amount >50000)
		{
		alert("Amount Should be between Rs. 10 - 50000");
		return false();
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
		url: "{{url('tramo-transaction')}}",
		data: dataString,
		dataType: "json",
		beforeSend: function () {
			
			$("#payAndConfirmBtn").attr('disabled',true)
			$("#payAndConfirmBtn").hide();
			$("#txnLoaderImg").show();
		},
		
		success: function (msg) {
			console.log(msg);
			
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
			$("#slipSenderName").text(senderName);
			$("#amount").val()
			varification();
			if (msg.status == 'failure') 
			{
				alert(msg.message);
			} 
			if (msg.status == 2) 
			{
				alert(msg.message);
			}
			else if (msg.status == 1||msg.status == 3) 
			{
			
				if(msg.status == 1)
					$("#slipTxnSataus").html("SUCCESS");
				if(msg.status == 3)
					$("#slipTxnSataus").html("PENDING");
				$("#myReciept").modal('show');
				/* $("#successMessage").html(det);
				$("#successAlert").modal('show'); */
			}else {
				alert(msg.message);
			}
			
		}
	});

}

// last
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
			url: "{{url('cyber-api/get_bank_detail')}}",
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
function callAutoFuncion()
{
	var username = $("input#number").val();
	if (username.length == 10 && $.isNumeric(username)) 
	{
		varification();
	}
}

</script>




@include('agent.money.wallet-type')
<div class="text-right">
	Pending &nbsp;|&nbsp;
	Refund Pending &nbsp;|&nbsp;
	<a href="{{route('txn-history-impswallet')}}?product=3" class="textheader"> Transaction History</a>
</div>                          
<div class="row row-sm">
          
          <div class="col-md-4 mg-t-20">
              <div class="card bd-0 shadow-base">
                <div class="card-header tx-medium bd-0 tx-white bg-info">
                  Enter Mobile Number
                </div><!-- card-header -->
                <div class="card-body">
                 <div class="d-flex">
            <div class="form-group mg-b-0">
           
                <input type="text" name="number" id="number" class="form-control"  placeholder="Enter Mobile Number" required maxlength="10" onkeyup="callAutoFuncion()">
            </div><!-- form-group -->
            <div class="mg-l-10 mg-t-25 pd-t-4">
                <button type="submit" class="btn btn-success" onclick="varification()" id="verificationBtn"><i class="fa fa-search"></i></button> <img src="{{url('/loader/loader.gif')}}" id="loaderImg" class="loaderImg" style="display:none"/> 
				<a href="{{Request::url('')}}" class="btn btn-primary" ><i class="fa fa-refresh"></i></a>
                <!--<button type="submit" class="btn btn-info" onclick="printTxnSlip()" id="verificationBtn"style="margin-top: 25px; margin-left: 10px;">SEARCH</button>-->
				
            </div>
			<div class="" id="name-error">
             
				
            </div> 
        </div>
        <div id="messagedata" style="display:none">
				<span style="font-weight: bold;"> Sender Name : </span><span id="senderName"></span><br>
				<span style="font-weight: bold;"> Remaining Limit : </span><span id="remainingLimits"></span><br>
				<span id="maxLimits" style="font-weight: bold;">Max Limit : 25000</span>
		</div>    
                </div><!-- card-body -->
              </div><!-- card -->

              <!-- <div class="col-md-4 mg-t-20" id="ben_frm"> -->
                    
                    <div class="card bd-0 shadow-base mg-t-20" id="ben_frm" style="display: none;">
                         <input type="hidden" value="" id="senderidn"/>
                  
                  <div class="card-body" style="margin-top: 10px;">
                    <div class="card-title">Add Beneficiary</div>
                    
                    <div class="form-group">
                        <label id="bbank">Bank Name</label>
                        <input list="browsers" id="service_id" class="form-control" onchange="getIfsc()">
                        <datalist id="browsers">

                            @foreach($netbanks as $netbank)
								<option value="{{ $netbank->bank_name }}">
                            @endforeach
                        </datalist>
                    </div><!-- form-group -->
					<div class="form-group row" id="an">
						<div class="col-md-8">
							<input type="text" class="form-control isEmptyFields" id="bank_account" placeholder="Bank Account Number">
						</div>
						<div class="col-md-2">
							<span class="input-group-btn right">
							<button id="bnv" class="btn btn-primary mg-t-40-min" type="button" onclick="verifynow();" style="float: right;">Verify</button>
							<img src="{{url('/loader/loader.gif')}}" id="accVerifyLoaderImg" class="loaderImg" style="margin-top: 16px;display:none"/>
							</span>
						</div>
					</div><!-- form-group -->

                    <div class="form-group" id="fn">
                      <input type="text" class="form-control isEmptyFields" id="first_name" placeholder="Beneficiary Name">
                    </div><!-- form-group -->

                    <!--<div class="form-group" id="bm">
                       <input type="text" class="form-control"
                                                       id="bene_mobile"
                                                       placeholder="Mobile Number">
                    </div><!-- form-group -->

                    <div class="form-group" id="ic">
                    <input type="text" class="form-control isEmptyFields" id="ifsc" placeholder="IFS Code">
                        <span class="input-group-btn right">
                            <!--<button id="ifscbtn" class="btn cursor btn-primary mg-t-40-min" type="button" onclick="getIfsc();" style="float: right;top: -34px;">get ifsc</button>-->
                        </span>
                    </div><!-- form-group -->
					<div class="form-group" id="beneficiaryOtpDiv" style="display:none">
						<input type="text" class="form-control isEmptyFields" id="beneficiaryOtp" placeholder="OTP">
                    </div>
                  </div><!-- card-body -->
                 <div class="card-footer mg-t-auto" id="submitDiv">
                    
					 <button class="btn btn-info bd-0 btn-oblong" onclick="addbene();" id="addBeneBtn">Submit</button>
					
					 <img src="{{url('/loader/loader.gif')}}" id="submitLoaderImg" class="beneLoaderImg" style="display:none"/>
                  </div>
                  <div class="card-footer mg-t-auto" id="addBeneDiv" style="display:none">
				   <button class="btn btn-info bd-0 btn-oblong" onclick="resendOtp();" id="resendOtpBtn">Resend OTP</button>
                    <button class="btn btn-info bd-0 btn-oblong" onclick="addbene();" id="addBeneBtn">Add Bene</button>
					<input type="hidden" value="PreAddBene" id="PreAddBene"/>
					<input type="hidden" value="" id="beneficiaryId"/>
					 <img src="{{url('/loader/loader.gif')}}" id="beneLoaderImg" class="beneLoaderImg" style="display:none"/>
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
                        <label>First Name</label>
                        <input type="text" id="fname" class="form-control">
                    </div>
					<div class="form-group">
                        <label>Last Name</label>
                        <input type="text" id="lname" class="form-control">
                    </div><!-- form-group -->
					 

                    
                  </div><!-- card-body -->
                  <div class="card-footer mg-t-auto">
                    <button class="btn btn-info bd-0 btn-oblong" id ="registerBtn"onclick="registration();">Next Step</button>
					  <img src="{{url('/loader/loader.gif')}}" id="registerLoaderImg" class="loaderImg" style="margin-top: 16px;display:none"/>
                  </div><!-- card-footer -->
                </div>
				<div class="card bd-0 shadow-base mg-t-20" id="otpVerificationDiv" style="display: none;">
					<div class="card-body">
						<div class="form-group" >
							<label>Enter Otp</label>
							<input type="text" id="remitterOtp" maxlength="6" class="form-control" placeholder="Enter 4 digit OTP">
							<input type="hidden" id="transactionId">
						</div>
					</div>
                  <div class="card-footer mg-t-auto">
                    <button id="verifyOtpBtn" class="btn btn-primary" type="button" onclick="otpVerification();">Verify </button>
					 <img src="{{url('/loader/loader.gif')}}" id="verifyLoaderImg" class="loaderImg" style="margin-top: 16px;display:none"/>
                   <button id="resendOtp" class="btn btn-success" type="button" onclick="resendOTP();">Resend OTP </button>
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
                
                <th class="tx-10-force tx-mont tx-medium">Name / Account Number / Bank Name</th>
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
            <div class="modal-dialog modal-lg" role="document">
              <div class="modal-content tx-size-sm" >
                <div class="modal-header pd-x-20">
                  <h6 class="tx-14 mg-b-0 tx-uppercase tx-inverse tx-bold"><span class="benename"></span> - <span class="account_number"><</span></h6>
                  <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                  </button>
                </div>
                {!!csrf_field()!!}
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
                    <input type="password" id="txn_pin" class="form-control pd-y-12" placeholder="txn_pin">
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
                  <button type="button" id="deleteBeneCnfrmBtn" class="btn btn-primary tx-size-xs" onclick="Deletebene_confirm()">Delete</button>
				  <img src="{{url('/loader/loader.gif')}}" id="deleteBeneCnfrmLoadeImg" class="loaderImg" style="margin-top: 16px;display:none"/>
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
                </div>
            </div>
        </div>
    </div>
<div id="preTxnSlip" class="modal fade">
            <div class="modal-dialog modal-lg" role="document">
              <div class="modal-content tx-size-sm">
                <div class="modal-header pd-x-20">
                
					<h4 class="modal-title">Transaction Details</h4>
                </div>
                {!!csrf_field()!!}
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
		</form>
              </div>
			  <div class="modal-footer">
	   <button onclick="transferamount()" type="button" class="btn btn-info" value="add" id="payAndConfirmBtn">Pay & Confirm</button>
	    <img src="{{url('/loader/loader.gif')}}" id="txnLoaderImg" class="loaderImg" style="display:none"/>
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
                {!!csrf_field()!!}
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
					<tbody>
					  <tr>
						<td><span id="pTxnAmount"></span></td>
						<td><span id="pTxnCharge"></span></td>
						<td><span id="pTotal"></span></td>
						
					  </tr>
					 </tbody>
				</table>
			</div>
		</form>
              </div>
			  <div class="modal-footer">
	   <button onclick="transferamount()" type="button" class="btn btn-info" value="add" id="payAndConfirmBtn">Pay & Confirm</button>
	    <img src="{{url('/loader/loader.gif')}}" id="txnLoaderImg" class="loaderImg" style="display:none"/>
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
      </div>
            </div><!-- modal-dialog -->
          </div><!-- modal --> 
    </div>
<div id="myReciept" class="modal fade" role="dialog" data-backdrop="static" data-keyboard="false">
	<div class="modal-dialog modal-lg" role="document">
		<div class="modal-content tx-size-sm" style="width: 130%;margin-left: -11%;margin-top: 19%;">
				<div class="modal-body" style="padding: 10px; border-radius: 0px;">
					<div class="col-md-12">
						<button type="button" class="btn" data-dismiss="modal" style="padding: 6px ! important; top: -8px; right: -35px; background-color: rgb(255, 255, 255) ! important; position: absolute;">&times;</button>
					</div>
					<div class="containers" style="height: 500px; overflow: auto; width: 100%">
						<div class="panel panel-primary">
							<div class="panel-heading" style="margin-bottom: 3px; padding: 7px;width:95%">Print / Download Receipt of Transaction ID : {{@$report->txnid}}<span id="prt_hdtranid"></span>

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
															<img src="{{ asset('newlog/images/Logo168.png') }}" style="width:70px;margin-right: 690px" />
														</div>
														<div class="col-md-6 col-sm-6 col-xs-6 text-left" style="padding:10px;">
															<div style=" float: left:10px;">
																<b>Outlet Name :  {{Auth::user()->member->company}}</b>
															</div>
															<br />
															<div style="margin-top: -20px; float: left:10px;">
																<b>Contact Number: {{Auth::user()->mobile}}</b>
															</div>
														</div>
													   <!--  <div class="col-md-4 col-sm-4 col-xs-4 text-right" style="padding:10px;" id="trandetailbyheadbps">
															<img src="{{asset('newlog/images/bbps_print.png')}}" style="width:170px;margin-top:-90px;margin-left:600px">
														</div> -->
													  <div class="col-md-4 col-sm-4 col-xs-4 text-right" style="padding:10px;float: right" id="trandetailbyheadnormal">
														   <b>Receipt # :</b> R -{{ @$report->id }} <b id="prt_bdtranid"></b>
															<br /> 
															<b>Date : {{ date("d-m-Y H:i:s")}}</b>
														</div> 
														</div>
													</th>


												</tr>

											  
											   <tr></tr>
											   <tr style="border-top:1px solid #ddd;margin-left:60px" id="trandetailbydmt">
													<td>
														<b>Sender Name :<span id="slipSenderName"></span></b><br />
														<b>Account Number : <span id="slipAccountNo"></span></b><br/>
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
																<td ><span id="prt_trandate"></span>{{ date("d-m-Y H:i:s")}}</td>
																<td><span id="prt_tranoperator">{{"MONEY"}}</span></td>
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
														
														<div class="col-md-6 col-sm-6 col-xs-6">
															<b>Total Amount Rs. : </b>
															<label id="slipAmount"></label>
														</div>
													</td>
												</tr> 
												<tr>
													<td colspan="3">
														<div class="col-md-12 col-sm-12 col-xs-12">
														   <!-- <b>Amount in Words :</b>-->
															<label id="slipAmountInWord"></label>
														</div>
													</td>
												</tr> 
												<tr>
													<td colspan="3" class="modalfoot" style="text-align: center;">
														<p>&copy; 2018 All Rights Reserved</p>
														<p style="font-size: 12px">This is a system generated Receipt. Hence no seal or signature required.</p>
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
	

<meta name="_token" content="{!! csrf_token() !!}"/>
@endsection
