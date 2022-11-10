@extends('layouts.app')


@section('content')

<!-- <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.4.0/css/font-awesome.min.css"/>

<link rel="stylesheet" href="css/dataTables.bootstrap.css"/>
<script src="js/jquery.dataTables.js"></script>
<script src="js/dataTables.bootstrap.js"></script> -->
<script src="{{url('js/jquery-ui.min.js')}}"></script>  
<link rel="stylesheet" href="css/dataTables.bootstrap.css"/>
<script src="js/jquery.dataTables.js"></script>
<script src="js/dataTables.bootstrap.js"></script>
<style>

element.style {
	height:170px;
}
</style>

    <script>
        
        function checkStatus(id) {
            alert('hello');
            var token = $("input[name=_token]").val();
            var dataString = 'txnid=' + id + '&_token=' + token;
            $.ajax({
                type: "GET",
                url: "{{url('supay/transaction_status')}}",
                data: dataString,
                dataType: "json",
                success: function (data) {
                    //console.log(data);
                    alert(data.message);
                    location.reload();
                    if (data.data.txstatus_desc == 'Failed') {
                        swal("Failed", data.data.txstatus_desc, "error");
                    } else {

                        swal("Success", data.data.txstatus_desc, "success");
                    }
                    

                }
            })
        }
function TramocheckStatus(id,apiId)
{
			
			var token = $("input[name=_token]").val();
            var number = $("#number").val();
            var dataString = 'id=' + id + '&mobile_number='+number;
            $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
            }
            });
			if(apiId== 3){
				//url="{{url('tramo/transaction_status')}}";
			}
			else if(apiId == 5 || apiId==5)
				url = "{{url('check-txn-status')}}"
			
            $.ajax({
                type: "post",
                url: url,
                data: dataString,
                dataType: "json",
				beforeSend:function(){
					$("#checkBtn_"+id).val("Processing...");
					$("#checkBtn_"+id).attr('disabled',true);
				},
                success: function (data) {
					$("#checkBtn_"+id).val("Check");
					$("#checkBtn_"+id).attr('disabled',false);
                    alert(data.msg);
                    
                }
            })

}
function sendOTP() 
		{
			var recordId = $("#recordId").val();
			var dataString = 'recordId=' + recordId;
			$.ajax({
				type: "get",
				url: "{{url('send-refund-txn-otp')}}",
				data: dataString,
				datatype: "json",
				 beforeSend: function() {
						$("#sendOtpBtn").hide();
						$('#otpLoader').show();
					},
				success: function (msg) {
						$('#otpLoader').hide();
						$('#sendOtpBtn').show();
						alert(msg.message);
				}
			});
        }
		function takeRefund() 
		{
			var txnOtp = $("#txnOtp").val();
			if(txnOtp =='')
			{
				alert("Please Enter OTP");
				$("#txnOtp").focus();
				return false;
			}
			if(confirm('Are You Sure To Refund?'))
			{
				var token = $("input[name=_token]").val();
				var recordId = $("#recordId").val();
				var refundApiId = $("#refundApiId").val();
				var txnAmount = $("#txnAmount").val();
				var customerNumber = $("#customerNumber").val();
				
				var txnId = $("#txnId").val();
				
				var dataString = 'recordId=' + recordId + '&refundApiId=' + refundApiId + '&txnAmount=' + txnAmount + '&customerNumber=' + customerNumber + '&txnOtp=' + txnOtp + '&txnId='+ txnId + '&_token=' + token;
				$.ajax({
					type: "POST",
					url: "{{url('txn-refund-request')}}",
					data: dataString,
					datatype: "json",
					 beforeSend: function() {
							$("#refundBtn").hide();
							$('#loader').show();
						},
					success: function (msg) {
						if(msg.status==48)
						{
							$("#refundBtn").hide();
							$('#loader').hide();
							$('#myModalrefund').modal('toggle');
							alert(msg.message);
						}
						else
							alert(msg.message);
					}
				});
			}
        }
		function refundRequest(id,apiId)
	{
		var mobile_number = $("#number").val();
		if(mobile_number=='')
		{
			alert('Please select mobile number!');
			return false;
		}
		var dataString = 'mobile_number=' + mobile_number+'&id='+id;
		$.ajaxSetup({
			headers: {
				'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
			}
		})
		  $.ajax({
			type: "POST",
			url: "{{url('refund-request-view')}}",
			data: dataString,
			dataType: "json",
			success: function (msg) {
					$('#recordId').val(msg.record_id);
					$('#refundApiId').val(msg.api_id);
					$('#txnAmount').val(msg.amount);
					$('#customerNumber').val(msg.customer_number);
					$('#txnId').val(msg.txnid);
					$('#myModalrefund').modal('toggle');
				}
			})

	}


        function checktransStatus(id) {
            var token = $("input[name=_token]").val();
            var mobile_number = $("#c_mob_"+id).val();
            var dataString = 'txnid=' + id + '&mobile_number=' + mobile_number + '&_token=' + token;
            $.ajax({
                type: "POST",
                url: "{{url('trans/transaction_status')}}",
                data: dataString,
                success: function (data) {
                    //console.log(data);
                    alert(data);
                    location.reload();
                    if (data.data.txstatus_desc == 'Failed') {
                        swal("Failed", data.data.txstatus_desc, "error");
                    } else {

                        swal("Success", data.data.txstatus_desc, "success");
                    }
                                      
                }
            })
        }
        
        function refundStatus(id) {
            var token = $("input[name=_token]").val();
            var sessionid = $("#sessionid").val();
            var dataString = 'sessionid=' + sessionid + '&txnid=' + id + '&_token=' + token;
            $.ajax({
                type: "GET",
                url: "{{url('spay/transaction_refund')}}",
                data: dataString,
                dataType: "json",
                success: function (data) {
                    //console.log(data);
                    //alert(data.data.txstatus_desc);
                    if (data.status != 0) {
                        swal("Failed", data.message, "error");
                    } else {

                        swal("Success", data.message, "success");
                    }
//                   $('#id').val(data.id);
//                   $('#provider_name').val(data.provider_name);
//                   $('#provider_code').val(data.provider_code);
//                   $('#service_id').val(data.service_id);
//                   $('#api_id').val(data.api_id);
//                   $('#api_code').val(data.api_code);
//                   $('#btn-save').val("update");
//                   $("#con-close-modal").modal("toggle");
                }
            })
        }
         function refund_request(id)
        {
            var mobile_number = $("#number").val();
            if(mobile_number=='')
            {
                alert('Please select mobile number!');
                return false;
            }
            var dataString = 'mobile_number=' + mobile_number+'&id='+id;
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                }
            })
              $.ajax({
                type: "POST",
                url: "{{url('refund-request-view')}}",
                data: dataString,
                dataType: "json",
                success: function (msg) {
                        $('#payid').val(msg.payid);
                        $('#amount').val(msg.amount);
                        $('#customer_number').val(msg.customer_number);
                        $('#txnid').val(msg.txnid);
                        $('#myModalrefund').modal('toggle');
                    }
                })

        }
        function success_refund_request(id) {
            var token = $("input[name=_token]").val();
            var number = $("#number").val();
            var dataString = 'txnid=' + id + '&_token=' + token;
            $.ajax({
                type: "POST",
                url: "{{url('tramo/transaction-refund-view')}}",
                data: dataString,
                datatype: "json",
                success: function (msg) {
                    $("#payid").val(msg.id);
                    $("#amount").val(msg.amount);
                    $("#customer_number").val(msg.mobile_number);
                    $("#txnid").val(msg.txid);
                    $("#api").val(msg.api.id);
                    if (msg.api.id == 16 || msg.api.id ==17 ) {
                        $("#sresend").show();
                        $("#srefund").show();
                    } else {
                       $("#sresend").hide();
                        $("#srefund").hide();
                    }
                    $("#payid").val(msg.id);
                    $("#myModalrefund").modal("toggle");
                }
            });
        }
         function refund_success() {
            if(confirm('Are You Sure To Refund?'))
        {
            $('#sotpsmart').attr("disabled", true);
            $('#sotpsmart').hide();
            var token = $("input[name=_token]").val();
            var number = $("#number").val();
            var payid = $("#payid").val();
            var txnid = $("#txnid").val();
            var api = $("#api").val();
            var otp = $("#otp").val();
            var sessionid = $("#sessionid").val();
            var mobile_number = $("#customer_number").val();
            var dataString = 'sessionid=' + sessionid + '&api=' + api + '&mobile_number=' + mobile_number + '&txnid=' + txnid + '&otp=' + otp + '&payid=' + payid + '&_token=' + token;
            $.ajax({
                type: "POST",
                url: "{{url('/ppay/transaction_refund')}}",
                data: dataString,
                dataType: "json",
                 beforeSend: function() {
                        $("#sotpsmart").hide();
                        $('#imgr').show();
                    },
                success: function (msg) {
                    $('#sotpsmart').attr("disabled", false);
                       $("#sotpsmart").hide();
                        $('#imgr').hide();
                        $("#yotp").hide();
                        $("#yrefund").hide();
                    alert(msg.message);
                    console.log(msg);
                    location.reload();
                    $("#c_sender_id").val(mobile_number);
                    $("#c_bene_name").val(name);
                    $("#c_bene_id").val(id);
                    $("#customer_number").val(mobile_number);
                    $("#c_bank_account").val(account);
                    $("#sotp").hide();
                    $('#sotpsmart').hide();
                    $("#myModal").modal("toggle");
                }
            });
        }
    }
        function refundReq() {
            if(confirm('Are You Sure To Refund?'))
        {
            $('#sotpsmart').attr("disabled", true);
            $('#sotpsmart').hide();
            var token = $("input[name=_token]").val();
            var number = $("#number").val();
            var payid = $("#payid").val();
            var txnid = $("#txnid").val();
            var api = $("#api").val();
            var otp = $("#otp").val();
            var res_code = $("#response_code").val();
            var sessionid = $("#sessionid").val();
            var mobile_number = $("#customer_number").val();
            var dataString = 'sessionid=' + sessionid + '&api=' + api + '&mobile_number=' + mobile_number + '&txnid=' + txnid + '&otp=' + otp + '&res_code='+ res_code  +'&payid=' + payid + '&_token=' + token;
            $.ajax({
                type: "POST",
                url: "{{url('/spay/transaction_refund')}}",
                data: dataString,
                dataType: "json",
                beforeSend: function() {
                        $("#sotpsmart").hide();
                        $('#imgr').show();
                    },
                success: function (msg) {
                    $('#sotpsmart').attr("disabled", false);
                       $("#sotpsmart").hide();
                        $('#imgr').hide();
                    alert(msg.message);
                    console.log(msg);
                    //location.reload();
                    $("#c_sender_id").val(mobile_number);
                    $("#c_bene_name").val(name);
                    $("#c_bene_id").val(id);
                    $("#customer_number").val(mobile_number);
                    $("#c_bank_account").val(account);
                    $("#sotp").hide();
                    $("#yotp").hide();
                    $("#yrefund").hide();
                    $("#nkycotp").hide();
                    $("#nkycrefund").hide();
                    $("#myModal").modal("toggle");
                }
            });
        }
        else
        {
            
        }
        }

        function refundReqotp() {
            var token = $("input[name=_token]").val();
            var number = $("#number").val();
            var payid = $("#payid").val();
            var txnid = $("#txnid").val();
            var api = $("#api").val();
            var otp = $("#otp").val();
            var sessionid = $("#sessionid").val();
            var mobile_number = $("#customer_number").val();
            var dataString = 'sessionid=' + sessionid + '&api=' + api + '&mobile_number=' + mobile_number + '&txnid=' + txnid + '&otp=' + otp + '&payid=' + payid + '&_token=' + token;
            $.ajax({
                type: "POST",
                url: "{{url('/spay/transaction_refund')}}",
                data: dataString,
                dataType: "json",
                success: function (msg) {
                    alert(msg.message);
                    console.log(msg);
                    //location.reload();
                    $("#c_sender_id").val(mobile_number);
                    $("#c_bene_name").val(name);
                    $("#c_bene_id").val(id);
                    $("#customer_number").val(mobile_number);
                    $("#c_bank_account").val(account);
                    $("#sotp").hide();
                    $("#yotp").hide();
                    $("#yrefund").hide();
                    $("#nkycotp").hide();
                    $("#nkycrefund").hide();
                    $("#myModal").modal("toggle");
                }
            });
        
        }
function ypayOtp() {
            var token = $("input[name=_token]").val();
            var number = $("#number").val();
            var payid = $("#payid").val();
            var txnid = $("#txnid").val();
            var api = $("#api").val();
            var otp = $("#otp").val();
            var sessionid = $("#sessionid").val();
            var mobile_number = $("#customer_number").val();
            var dataString = 'sessionid=' + sessionid + '&api=' + api + '&mobile_number=' + mobile_number + '&txnid=' + txnid + '&otp=' + otp + '&payid=' + payid + '&_token=' + token;
            $.ajax({
                type: "GET",
                url: "{{url('/ypay/refund-otp-yesb')}}",
                data: dataString,
                dataType: "json",
                success: function (msg) {
                    alert(msg.message);
                    $('#yres_code').val(msg.res_code);
                }
            });
        }

        function nkycOtp() {
            var token = $("input[name=_token]").val();
            var number = $("#number").val();
            var payid = $("#payid").val();
            var txnid = $("#txnid").val();
            var api = $("#api").val();
            var otp = $("#otp").val();
            var sessionid = $("#sessionid").val();
            var mobile_number = $("#customer_number").val();
            var dataString = 'sessionid=' + sessionid + '&api=' + api + '&mobile_number=' + mobile_number + '&txnid=' + txnid + '&otp=' + otp + '&payid=' + payid + '&_token=' + token;
            $.ajax({
                type: "GET",
                url: "{{url('/ypay/nkyc-refund-otp-yesb')}}",
                data: dataString,
                dataType: "json",
                success: function (msg) {
                    alert(msg.message);
                    $('#yres_code').val(msg.res_code);
                }
            });
        }

        function ypayRefund() {
            if(confirm('Are You Sure To Refund?'))
        {
                $('#sotps').attr("disabled", true);
            $('#sotps').hide();
            var token = $("input[name=_token]").val();
            var number = $("#number").val();
            var payid = $("#payid").val();
            var txnid = $("#txnid").val();
            var api = $("#api").val();
            var otp = $("#otp").val();
            var sessionid = $("#sessionid").val();
            var mobile_number = $("#customer_number").val();
            var yres_code = $('#yres_code').val();
            var dataString = 'sessionid=' + sessionid + '&api=' + api + '&mobile_number=' + mobile_number + '&txnid=' + txnid + '&otp=' + otp + '&payid=' + payid + '&yres_code='+ yres_code + '&_token=' + token;
            $.ajax({
                type: "POST",
                url: "{{url('/yespay/transaction_refund_yesb')}}",
                data: dataString,
                datatype: "json",
                 beforeSend: function() {
                        $("#sotps").hide();
                        $('#imgr').show();
                    },
                success: function (msg) {
                    $('#sotps').attr("disabled", false);
                     $("#sotps").hide();
                        $('#imgr').hide();
                    alert(msg.message);
                    location.reload();
                    console.log(msg);
                    $("#c_sender_id").val(mobile_number);
                    $("#c_bene_name").val(name);
                    $("#c_bene_id").val(id);
                    $("#customer_number").val(mobile_number);
                    $("#c_bank_account").val(account);
                    $("#myModal").modal("toggle");
                }
            });
        }
        }

        function nkycRefund() {
            if(confirm('Are You Sure To Refund?'))
        {
                $('#sotps').attr("disabled", true);
            $('#sotps').hide();
            var token = $("input[name=_token]").val();
            var number = $("#number").val();
            var payid = $("#payid").val();
            var txnid = $("#txnid").val();
            var api = $("#api").val();
            var otp = $("#otp").val();
            var sessionid = $("#sessionid").val();
            var mobile_number = $("#customer_number").val();
            var yres_code = $('#yres_code').val();
            var dataString = 'sessionid=' + sessionid + '&api=' + api + '&mobile_number=' + mobile_number + '&txnid=' + txnid + '&otp=' + otp + '&payid=' + payid + '&yres_code='+ yres_code + '&_token=' + token;
            $.ajax({
                type: "POST",
                url: "{{url('/yespay/nkyc_transaction_refund_yesb')}}",
                data: dataString,
                datatype: "json",
                 beforeSend: function() {
                        $("#sotps").hide();
                        $('#imgr').show();
                    },
                success: function (msg) {
                    $('#sotps').attr("disabled", false);
                     $("#sotps").hide();
                        $('#imgr').hide();
                    alert(msg.message);
                    location.reload();
                    console.log(msg);
                    $("#c_sender_id").val(mobile_number);
                    $("#c_bene_name").val(name);
                    $("#c_bene_id").val(id);
                    $("#customer_number").val(mobile_number);
                    $("#c_bank_account").val(account);
                    $("#myModal").modal("toggle");
                }
            });
        }
        }

        function refundOtp() {
            var token = $("input[name=_token]").val();
            var number = $("#number").val();
            var payid = $("#payid").val();
            var txnid = $("#txnid").val();
            var api = $("#api").val();
            var otp = $("#otp").val();
            var amount = $("#amount").val();
            var sessionid = $("#sessionid").val();
            var mobile_number = $("#customer_number").val();
            var dataString = 'api=' + api + '&amount='+ amount + '&mobile_number=' + mobile_number + '&txnid=' + txnid + '&payid=' + payid + '&_token=' + token;
            $.ajax({
                type: "POST",
                url: "{{url('/ppay/super-refund-otp')}}",
                data: dataString,
                dataType: "json",
                success: function (msg) {
                    alert(msg.message);
                }
            });
        }
        function refundReqnew() {
		if(confirm('Are You Sure To Refund?'))
        {
            var token = $("input[name=_token]").val();
            var number = $("#number").val();
            var payid = $("#payid").val();
            var txnid = $("#txnid").val();
            var api = $("#api").val();
            var otp = $("#otp").val();
            var mobile_number = $("#customer_number").val();
            var yres_code = $('#yres_code').val();
            var dataString = 'api=' + api + '&mobile_number=' + mobile_number + '&txnid=' + txnid + '&otp=' + otp + '&payid=' + payid + '&yres_code='+ yres_code + '&_token=' + token;
            $.ajax({
                type: "POST",
                url: "{{url('/ppay/refund-success')}}",
                data: dataString,
                datatype: "json",
                 beforeSend: function() {
                        $("#sotps").hide();
                        $('#imgr').show();
                    },
                success: function (msg) {
                    $('#sotps').attr("disabled", false);
                    $("#sotps").hide();
                    $('#imgr').hide();
                    alert(msg.message);
                    location.reload();
                    console.log(msg);
                    $("#c_sender_id").val(mobile_number);
                    $("#c_bene_name").val(name);
                    $("#c_bene_id").val(id);
                    $("#customer_number").val(mobile_number);
                    $("#c_bank_account").val(account);
                    $("#myModal").modal("toggle");
                }
            });
        }
        }
         function do_complain(id)
        {
            $('#do_comp_'+id).toggle();
            $('#do_comp_submit_'+id).toggle();
        }
         function stor_complain(id)
        {
        	 var token = $("input[name=_token]").val();
            var product = $('#myid_'+id).val();
            var issue_type = $('#issue_type_'+id).val();
            var issue_date = $('#date_'+id).val();
            var txn_id = $('#txn_'+id).val();
            var account_number = $('#acno_'+id).val();
            var amount = $('#amount_'+id).val();
            var remark = $('#remark_'+id).val();
            if(issue_type!='' && remark!='')
            {
            var dataString = 'product=' + product + '&issue_type=' + issue_type + '&issue_date=' + issue_date + '&account_number=' + account_number + '&txn_id=' + txn_id + '&amount=' + amount +'&remark=' + remark+'&_token=' + token;
            $.ajax({
                type: "POST",
                url: "{{url('store_complain_req')}}",
                data: dataString,
                datatype: "json",
                success: function (msg) {
                    alert(msg.message);
                   $('#do_comp_'+id).hide();
                }
            });
        }
         else { alert('Please Select Issu Type/Remark Required Field !'); }

        }

        function multi_txn_view(id)
        {
            var token = $("input[name=_token]").val();
            var dataString = 'MultiId=' + id + '&_token=' + token;
             $.ajax({
                type: "get",
                url: "{{url('multi_txn_view')}}",
                data: dataString,
                datatype: "json",
                success: function (msg) {
                    console.log(msg);
                    
                    
                    var obj1 = msg.data;
                    var obj2 = obj1.data;
                    var html = "";
                    for(var key in obj2) {
                    console.log(obj2);

                    var txn_id = obj2[key].txnid;
                    var txn_amount = obj2[key].amount;
                    var txn_status = obj2[key].status;
                    var txn_refund = obj2[key].refund;
                    // if(obj2[key].status==2)
                    // {
                    //     $('#nonkyc_check_'+txn_id).hide();
                    //     $('#nkyc_refund_'+txn_id).show();
                    // }
                    html += "<tr>";
                    html += "<td>"+obj2[key].txnid+"</td>";
                    html += "<td>"+obj2[key].amount+"</td>";
                    if(obj2[key].status==1)
                    {
                        html += "<td style='color:green;' id='nkyc_success_"+txn_id +"'>Success</td>";
                    }
                    else if(obj2[key].status==3)
                    {
                         html += "<td style='color:orange;' id='nkyc_success_"+txn_id +"'>Pending</td>";
                    }
                    else if(obj2[key].status==2)
                    {
                         html += "<td style='color:red;' id='nkyc_success_"+txn_id +"'>Failed</td>";
                    }
                    else
                    {

                    }
                    if(obj2[key].status!=2)
                    {
                    html += '<td><button id="nonkyc_check_'+txn_id +'" onclick="nonkyc_check(\'' + txn_id + '\', \'' + txn_amount + '\')">Check Status</button></td>';
                    }
                    else
                    {
                         html += "<td>--</td>";
                    }
                     html += "<td>"+obj2[key].utr+"</td>";
                    if(txn_status==2 && txn_refund==1)
                    {

                    
                    html += '<td id="nkyc_refund_'+txn_id +'"><button onclick="this.disabled=true;nonkyc_refund_view(\'' + txn_id + '\', \'' + txn_amount + '\')">Refund</button></td>';
                  }
                  else if(txn_refund==4)
                  {
                    html += '<td>Refunded</td>';
                  }
                  else
                  {
                     html += '<td>--</td>';
                  }
                    }
                    $("#response").html(html);
                    $('#con-close-multtxn-nkyc').modal("toggle");
                }
            });
        }
    </script>
<div class="super_container"> 
    <div class="home">
    		
    	
    	</div>
    <div class="search" >	
    <div class="">
    			<div class="">
    				<div class="">
 <div class="panel-body">
                                <div class="col-md-12" style="margin-left:60px;">
                                    <div class="col-md-6">
                                        <form method="get" action="{{ url('searchall-all-moneyreport') }}" class="form-inline"
                                              role="form">
                                            {!! csrf_field() !!}
                                            <div class="form-group">
                                                {{ Form::select('SEARCH_TYPE', ['ID' => 'Record Id','TXN_ID' => 'Txn Id','ACC' => 'Account No', 'MOB_NO'=>'Mobile No'], null, ['class'=>'form-control', 'style'=>"height: 10%;"]) }}
                                            </div> <div class="form-group">
                                                <label class="sr-only" for="payid">Number</label>
                                                <input name="number" type="text" class="form-control" required 
                                                       id="exampleInputEmail2" value="{{app('request')->input('number')}}"
                                                       placeholder="Number">
                                            </div>
                                            <button type="submit"
                                                    class="btn btn-success waves-effect waves-light m-l-10 btn-md"><span
                                                        class="glyphicon glyphicon-find"></span>Search
                                            </button>
                                             <a href="{{url('money_transfer_report')}}" class="btn btn-primary  btn-md">Reset
                                            </a>
                                            <!--<button type="submit" name="exportByNumber" value="exportByNumber"
                                                    class="btn btn-success waves-effect waves-light m-l-10 btn-md"><span
                                                        class="glyphicon glyphicon-find"></span>Export
                                            </button>-->
                                        </form>

                                    </div>
                                    <div class="col-md-6">
                                        <form method="get" action="{{ url('searchall-all-moneyreport') }}">
                                            <div class="form-group col-md-4">
                                                <input name="fromdate" class="form-control" type="date">
                                            </div>
                                            <div class="form-group col-md-4">
                                                <input name="todate" class="form-control" type="date">
                                            </div>
                                            <div class="form-group col-md-2">
                                                <button name="export" value="DMT Reports" type="submit"
                                                        class="btn btn-success waves-effect waves-light m-l-10 btn-md"><span
                                                            class="glyphicon glyphicon-find"></span>Export
                                                </button>
                                            </div>
                                            <!--<div class="form-group col-md-2">
                                                <button value="search" name="search" type="submit"
                                                        class="btn btn-success waves-effect waves-light m-l-10 btn-md"><span
                                                            class="glyphicon glyphicon-find"></span>Search
                                                </button>
                                            </div> -->
                                        </form>
                                    </div>
                                    <script>

                                    </script>
                                </div>

                            </div>
                        
                        
          <input type="hidden" value="{{ $sessionid }}" id="sessionid">
		 
<div class="box">	  
    <table id="example2" class="table table-bordered">
                                        <thead style="color: white">
                                            <tr>
                                               <th data-field="date" data-sortable="true" data-formatter="dateFormatter">&nbsp;&nbsp;Date/Time</th>
    					 <!-- <th data-field="time" data-sortable="true" data-formatter="dateFormatter"> </th>-->
                                                <th>ID</th> 
                                               <!-- <th>Complaint</th>-->
                                                <th>Product</th>
                                                <th>Name<br>Acc Number<br>Rem Mobile No</th>
    											<th> TXN ID</th>
                                                <th>Amount</th>
                                                <th>Bank/Mode/UTR</th>
                                                <th>Status</th>
    											<th>Check Status</th>
    											<th>Refund</th>
    											<th>Receipt</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($reports as $report)
                                   <?php $s = $report->created_at;
    						$dt = new DateTime($s);?>
                                            <tr class="odd gradeX">
    											 <td align="center">{{ $dt->format('d/m/Y') }}<br>{{ $dt->format('H:i:s') }}</td>
                                
    											<td>{{ $report->id }}</td>
													{{--<td><a href="javascript::voide(0)" onclick="do_complain({{ $report->id }})" style="color:black">Submit Complain</a></td>--}}
    											<td class="center">{{ @$report->api->api_name }}</td>
    												
    											<td class="text-left" style="text-align:center">
    												{{ @$report->beneficiary->name }}
    												<br>
    												@if($report->recharge_type == 1)
    													{{ @$report->biller_name }}
    													{{ @$report->provider->provider_name }}
    												@else
														{{ $report->number }}
    												@endif
    												<br>
    												{{ $report->customer_number }}
    												
    												
    											</td>
    											<td class="center">{{ $report->txnid }}</td>
    											
    											
    											<td class="center">
    												 
    													{{ $report->amount }} 
    												</td>
    											<td class="center">
    											@if($report->api_id==2)
    													{{ @$report->remark }} 
    												@else 
    												{{ @$report->beneficiary->bank_name }} 
    												@endif
    												<br>
    												@if($report->channel== 8)
    													{{ "NEFT" }}
    												@elseif($report->channel ==2)
    													{{ "IMPS" }} 
    												@endif
    												<br>
    												<?php 
    												$bank_ref_no = @$report->bank_ref;
    												if(!is_numeric ($bank_ref_no))
    												{
    													$content= explode("/",$bank_ref_no);
    													
    													if($content)
    													{
    														if(isset($content[1]))
    														{
    															$word = $content[1];
    															echo $exact_word = substr($word,5);
    														}
    														else
    															echo $exact_word = $bank_ref_no;
    													}
    													else
    														echo $exact_word = $bank_ref_no;
    												}
    												else
    													echo $exact_word = @$report->bank_ref;
    											
    											?>
    												</td>
    											
    											<td class="center">{{ @$report->status->status }}</td>

    											<td> 
    								                @if(in_array($report->status_id,array(1,3,9)))
    													<input type="button" id ="checkBtn_{{$report->id}}" onclick="TramocheckStatus({{ $report->id }},{{$report->api_id}})" class="btn btn-primary btn-xs" value="Check"/>
    													
    												@endif
    											</td>
    											<td> 
    												@if($report->status_id==20 && $report->refund == 1)
    													<a onclick="refund_request({{ $report->id }})" href="javascript::voide(0)" class="table-action-btn btn btn-success btn-xs">
    													<i class="md md-visibility"></i>Refund</a> 
    												@endif
    											</td>
    											
                                                <td style="text-align:center">
    											  @if(in_array($report->status_id,array(1,3,9)))
    												<a target="_blank" href="{{ url('invoice') }}/{{ $report->id }}">
    													<span class="btn btn-info" style="font-size: 14px;"><i class="md md-visibility"></i>Receipt</span>
    												</a>
    											@endif
    											</td>  
                                               
                                            

                                        </tr>
                                           <tr id="do_comp_{{ $report->id }}" style="display: none;">
                    <td colspan="2" class="no-padding" style="border-top:1px solid #fff !important;">
                    <div class="form-group">
                                        
                                        <input type="hidden" style="font-weight:bold; font-size:18px; font-family:sans-serif;" required id="myid_{{ $report->id }}" name="product" value="{{ $report->api->api_name }}">
                                        
                                        

                                    </div>
                    </td>
                        <td colspan="2" class="no-padding" style="border-top:1px solid #fff;">
                                    <div class="form-group">
                                        
                  <select style="font-weight:bold; font-size:18px; font-family:sans-serif;" class="form-control select2me" id="issue_type_{{ $report->id }}" name="issue_type">
                                        <option value="">SELECT ISSUE TYPE</option>
                                        <option value="DOUBLE TXN">DOUBLE TXN</option>
                                        <option value="WRONG TXN">WRONG TXN</option>
                                        <option value="AMOUNT NOT CREDIT">AMOUNT NOT CREDIT</option>
                                        <option value="RECHARGE NOT CREDIT">RECHARGE NOT CREDIT</option>
                                        <option value="PENDING TXN">PENDING TXN</option>
                                        <option value="OTHERS">OTHERS</option>
                                        </select>      
                                    </div>
                        </td>
                        <!--<td colspan="2" class="no-padding" style="border-top:1px solid #fff;">
                        <div class="form-group">
                        <div class="col-md-12 input-group">
                         <input style="font-weight:bold; font-size:18px; font-family:sans-serif;" value="ertet" name="dod" id="date_{{ $report->id }}" placeholder="TXN Date" value="" required class="form-control">
                        </div>
                        </div>
                        </td>-->
                        <td colspan="2" class="no-padding" style="border-top:1px solid #fff;">
                        <div class="form-group">
                             <div class="col-md-12 input-group">
                                <textarea required style="font-weight:bold; font-size:18px; font-family:sans-serif;" id="remark_{{ $report->id }}" rows="1" class="form-control" placeholder="Remark..." name="remark"></textarea>
                            </div>
                        </div>
                        </td>

                        <td><button onclick="this.disabled=true;stor_complain({{ $report->id }})" style="dispplay:none;  color:white;" type="submit" id="btn" class="btn btn-lg btn-success btn-alt btn-icon btn-icon-right btn-icon-go pull-right">
                                <span>Submit</span>

                            </button></td>

                        <td colspan="2" class="no-padding" style="border-top:1px solid #fff;">
                        <div class="form-group">
                        <div class="col-md-12 input-group">
           <input style="font-weight:bold; font-size:18px; font-family:sans-serif;" id="txn_{{ $report->id }}" required name="txn" placeholder="Enter TXN ID" type="hidden" class="form-control" value="{{ $report->txnid }}">
                        </div>
                        </div>
                        </td>
                        <td colspan="2" class="no-padding" style="border-top:1px solid #fff;">
                        <div class="form-group">
                        <div class="col-md-12 input-group">
                            <input style="font-weight:bold; font-size:18px; font-family:sans-serif;" id="acno_{{ $report->id }}" required name="acno" placeholder="Enter Acoount No." type="hidden" class="form-control" value="{{ $report->number }}">
                        </div>
                        </div>
                        </td>
                        <td colspan="2" class="no-padding" style="border-top:1px solid #fff;">
                        <div class="form-group">
                        <div class="col-md-12 input-group">
                            <input style="font-weight:bold; font-size:18px; font-family:sans-serif;" id="amount_{{ $report->id }}" required name="amount" placeholder="Enter Amount." type="hidden" class="form-control" value="{{ $report->amount }}">
                        </div>
                        </div>
                        </td>
                      </tr>
                      @endforeach
                  </tbody>
                     </table>
               {{$report->appends(\Input::except('page'))->render() }} 
               
           </div>
    	   
        
    	
    	</div>
    	</div>
    	</div>
    	</div>
</div>
<div class="modal fade" id="myModalrefund" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" >
			<div class="modal-dialog" role="document" style="    padding-top: 6%;">
				<div class="modal-content">
					<div class="modal-header">
						<h4 class="modal-title" id="myModalLabel">Confirm Transaction</h4>
						<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
									aria-hidden="true">&times;</span></button>
					</div>
					<div class="modal-body" >
						<form class="form-horizontal">
							<input id="c_bene_id" type="hidden">
							<input id="c_sender_id" type="hidden">
							<div class="form-group">
								<label for="bank_account" class="control-label col-sm-4">
									Record ID </label>
								<div class="col-sm-6">
									<input type="text" class="form-control" disabled id="recordId" placeholder="Reference ID">
									<input type="hidden" id="refundApiId" value=""/>
								</div>
							</div>
							<div class="form-group">
								<input type="hidden" name="api" id="api">
								<label for="bank_account" class="control-label col-sm-4">Customer Number</label>
								<div class="col-sm-6">
									<input type="text" class="form-control" disabled id="customerNumber"
										   placeholder="Customer Number">
								</div>
							</div>
							<div class="form-group">
								<label for="bank_account" class="control-label col-sm-4">
									Transaction id </label>
								<div class="col-sm-6">
									<input type="text" class="form-control" disabled id="txnId"
										   placeholder="Transaction id">
								</div>
							</div>
							<div class="form-group">
								<label for="bank_account" class="control-label col-sm-4">
									Amount </label>
								<div class="col-sm-6">
									<input type="text" disabled class="form-control" id="txnAmount"
										   placeholder="Enter Amount">
								</div>
							</div>
							<div class="form-group">
								<label for="bank_account" class="control-label col-sm-4">OTP </label>
								<div class="col-sm-6">
									<input type="text" class="form-control" id="txnOtp"
										   placeholder="Ender OTP">
								</div>
							</div>

						</form>
					</div>
					<div class="modal-footer">
					
						<button id="sendOtpBtn" type="button" onclick="sendOTP()" class="btn btn-basic">Send OTP</button>
						<img src="img/loader.gif" class="loaderImage" style="display:none" id="otpLoader" alt=""> </span>
			  
					<button id="refundBtn" type="button" onclick="takeRefund()" class="btn btn-success">Take Refund
						</button>
						<img src="img/loader.gif" class="loaderImage" style="display:none" id="loader" alt=""> </span>
		   

					</div>
				</div>
			</div>
		</div> 
@include('layouts.footer')        
    <meta name="_token" content="{!! csrf_token() !!}"/>
@endsection