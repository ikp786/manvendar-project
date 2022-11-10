@extends('admin.layouts.templatetable')


@section('content')
    <script>
        function checkStatus(id) {
            var token = $("input[name=_token]").val();
            var dataString = 'txnid=' + id + '&_token=' + token;
            $.ajax({
                type: "GET",
                url: "{{url('/supay/transaction_status')}}",
                data: dataString,
                dataType: "json",
                success: function (data) {
                    console.log(data);
                    //alert(data.message);
					
                    if (data.status_id == 42) {
                        swal("Success", data.message, "success");
                    } 
                    else if(data.status_id == 49)
                    {
                         swal("Pending", data.message, "success");
                    }
                    else {

                        swal("Failure", data.message, "error");
                    }
                    /* location.reload(); */
                }
            })
        }
        function PayTmCheckStatus(id) {
            var token = $("input[name=_token]").val();
            var dataString = 'txnid=' + id + '&_token=' + token;
            $.ajax({
                type: "POST",
                url: "{{url('/paytm/transaction_status')}}",
                data: dataString,
                dataType: "json",
                success: function (data) {
                    console.log(data);
                    //alert(data.message);
                    
                    if (data.status_id == 42) {
                        swal("Success", data.message, "success");
                    } 
                    else if(data.status_id == 49)
                    {
                         swal("Pending", data.message, "success");
                    }
                    else {

                        swal("Failure", data.message, "error");
                    }
                     // location.reload(); 
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
        function Calculate(id) {
            var token = $("input[name=_token]").val();
            var number = $("#c_mob_"+id).val();
            var dataString = 'txnid=' + id + '&mobile_number='+ number +'&_token=' + token;
            $.ajax({
                type: "GET",
                url: "{{url('ppay/transaction-refund-otp')}}",
                data: dataString,
                datatype: "json",
                success: function (msg) {
                    $("#payid").val(msg.id);
                    $("#amount").val(msg.amount);
                    $("#customer_number").val(msg.mobile_number);
                    $("#txnid").val(msg.txid);
                    $("#api").val(msg.api.id);
                    if (msg.api.id == 4) {
                        $("#sotsmart").show();
                        $("#sotpsmart").hide();
                        $("#eresend").hide();
                        $("#yotp").hide();
                        $("#yrefund").hide();
                        $("#nkycotp").hide();
                        $("#nkycrefund").hide();
                        $("#sotps").show();
                    } else {
                        $("#sotsmart").hide();
                        $("#sotpsmart").show();
                        $("#eresend").show();
                        $("#sotps").hide();
                        $("#yotp").hide();
                        $("#yrefund").hide();
                        $("#nkycotp").hide();
                        $("#nkycrefund").hide();
                    }
                    $("#payid").val(msg.id);
                    $("#myModalrefund").modal("toggle");
                }
            });
        }

        function trans_refund(id)
        {
             var token = $("input[name=_token]").val();
            var mobile_number = $("#c_mob_"+id).val();
            var dataString = 'txnid=' + id + '&mobile_number=' + mobile_number + '&_token=' + token;
            $.ajax({
                type: "POST",
                url: "{{url('trans/transaction_refund')}}",
                data: dataString,
                success: function (data) {
                    //console.log(data);
                    alert(data);
                    return false;
                    location.reload();
                    if (data.data.txstatus_desc == 'Failed') {
                        swal("Failed", data.data.txstatus_desc, "error");
                    } else {

                        swal("Success", data.data.txstatus_desc, "success");
                    }
                                      
                }
            })
        }

        function yespay_refund(id)
        {
            var token = $("input[name=_token]").val();
            var number = $("#number").val();
            var dataString = 'txnid=' + id + '&_token=' + token;
            $.ajax({
                type: "POST",
                url: "{{url('ypay/transaction-refund-otp')}}",
                data: dataString,
                datatype: "json",
                success: function (msg) {
                    $("#payid").val(msg.id);
                    $("#amount").val(msg.amount);
                    $("#customer_number").val(msg.mobile_number);
                    $("#txnid").val(msg.txid);
                    $("#api").val(msg.api.id);
                    $("#yrefund").show();
                    $("#yotp").show();
                    $("#nkycrefund").hide();
                    $("#nkycotp").hide();
                    $('#sotsmart').hide();
                    $('#sotpsmart').hide();
                    $('#eresend').hide();
                    $('#sotps').hide();
                    $("#myModalrefund").modal("toggle");
                }
            });
        }
        function refund_success() {
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
                    $('#sotpsmart').hide();
                    $("#yotp").hide();
                    $("#yrefund").hide();
                    $("#myModal").modal("toggle");
                }
            });
    }
        function refundOtp() {
            var token = $("input[name=_token]").val();
            var number = $("#number").val();
            var payid = $("#payid").val();
            var txnid = $("#txnid").val();
            var api = $("#api").val();
            var otp = $("#otp").val();
            var re_amount = $('#amount').val();
            var sessionid = $("#sessionid").val();
            var mobile_number = $("#customer_number").val();
            var dataString = 'api=' + api + '&mobile_number=' + mobile_number + '&txnid=' + txnid + '&re_amount='+ re_amount +'&payid=' + payid + '&_token=' + token;
            $.ajax({
                type: "GET",
                url: "{{url('/ppay/refund-otp')}}",
                data: dataString,
                dataType: "json",
                success: function (msg) {
                    alert(msg.message);
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
        
        
		function getUserDetails(user_id)
		{
			var dataString = 'user_id=' + user_id ;
            $.ajax({
                type: "GET",
                url: "{{url('admin/getUserByID')}}",
                data: dataString,
                datatype: "json",
                success: function (result) {
					if(result.status == 1){
                        var html ='<thead><tr style="font-size: 16px font-family: time;background: cadetblue;font-style: italic;"><th>LM Code</th><th>Name Id</th><th>Email</th><th>Mobile No.</th><th>Parent Name</th><th>Role</th><th>Balance</th></tr></thead><tbody>';
                        
                        var val=result.user_details;
                             html +='<tr>';
                             html +='<td>' + val.lm_code + '</td>';
                             html +='<td>' + val.name + '</td>';
                             html +='<td>' + val.email + '</td>';
                             html +='<td>' + val.mobile + '</td>';
                             html +='<td>' + val.parent_name + '</td>';
                             html +='<td>' + val.role + '</td>';
                             html +='<td>' + val.balance + '</td>';
                             
                        
                        html +='</tbody>';
                        $("#result_table").html(html);
                        $("#user_details").modal("toggle");
                    }
					else
						alert(" OOPS! Something went worng");
                   
                }
            });
			}
 function TramocheckStatus(id,apiId)
{
			
			var token = $("input[name=_token]").val();
            var number = $("#number").val();
            var dataString = 'id=' + id;
            $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
            }
            });
			/* if(apiId== 3){
				//url="{{url('tramo/transaction_status')}}";
			}
			else if(apiId == 5) */
				url = "{{url('check-txn-status')}}"
			
            $.ajax({
                type: "post",
                url: url,
                data: dataString,
                dataType: "json",
				beforeSend:function(){
					$("#checkBtn_"+id).hide()
					$("#checkImg_"+id).show();
				},
                success: function (data) {
					$("#checkBtn_"+id).show()
					$("#checkImg_"+id).hide();
                    alert(data.msg);
                    
                }
            })

}

 </script>
    <div class="col-sm-12">
		<div class="col-lg-6 col-md-6">
			<h4 class="page-title" style="color: black; ">{{'Uses Report'}}</h4>
		</div>
	</div>
      
   
  @include('search.search-with-type-status-export')  
<div class="box">
		<table id="example2" class="table table-bordered">
                <thead>
                <tr>
                   	<th>Date/Time</th>
					<th>ID</th> 
					<th>User</th> 
					<th>Sender No</th>
					<th>Bene Name</th>
					<th>Bene Account</th>
					<th>Ifsc</th>
					<th>Bank Name</th>
					<th>Amount</th>
					<th>Type</th>
					<th>Txn Type</th>
					<th>Route</th>
					<th>Txn Id</th>
					<th>Bank Ref</th>
					<th>Mode</th>
					<th>Status</th>
                    <th>Action</th>
                    <th>Refund</th>
                </tr>
                </thead>
                <tbody>
                @foreach($reports as $report)
                    <tr class="{{@$report->status->status}}-text">
                        <td>{{ date("d/m/Y H:i:s",strtotime($report->created_at))}}</td>
						<td>{{ $report->id }}</td>
						 <td><a href="{{ url('user-recharge-report')}}/{{ @$report->user_id }}">{{ @$report->user->name }} ( {{ @$report->user->prefix }} - {{ @$report->user->id }})</a></td>
						<td>{{ ($report->recharge_type==0) ? $report->customer_number : $report->number }}</td>	
						<td>{{ @$report->beneficiary->name }}</td>
						<td>{{ $report->number }}</td>
						<td>{{ @$report->beneficiary->ifsc }}</td>
						<td>{{ @$report->beneficiary->bank_name }} </td>
						<td>{{ $report->amount }} </td> 
						<td>{{ $report->type }} </td>
						<td>{{ $report->txn_type }} </td>
							
						<td>
							@if($report->recharge_type== 1)
								{{ @$report->provider->provider_name }}  
								@else
								{{ @$report->api->api_name }} 
							@endif
							</td>
						<td>{{ $report->txnid }}</td>	
						<td>{{ $report->bank_ref }}</td>	
						<td>{{ ($report->channel==2) ? "IMPS":"NEFT" }}</td>	
						<td>{{ @$report->status->status }}</td>
                        <td>
						 @if(in_array($report->status_id,array(1,3,9,34)))
							<a onclick="TramocheckStatus({{ $report->id }},{{$report->api_id}})" href="javascript::void(0)" class="btn btn-outline-info btn-sm" id="checkBtn_{{$report->id}}">
									Check</a> 
							<img src="{{url('loader/loader.gif')}}" id="checkImg_{{$report->id}}" class="loaderImg" style="display: none;">
						@endif
						</td>
						<td></td>
                    </tr>
                   
                @endforeach
                </tbody>
            </table>
            {!! $reports->links() !!}
        </div>
  
    <div class="modal fade" id="myModalrefund" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel">Confirm Transaction</h4>
                </div>
                <div class="modal-body">
                    <form class="form-horizontal">
                        <input id="c_bene_id" type="hidden">
                        <input id="c_sender_id" type="hidden">
                        <div class="form-group">
                            <label for="bank_account" class="control-label col-sm-4">
                                Reference ID </label>
                            <div class="col-sm-6">
                                <input type="text" class="form-control" disabled id="payid"
                                       placeholder="Reference ID">
                            </div>
                        </div>
                        <div class="form-group">
                            <input type="hidden" name="api" id="api">
                            <label for="bank_account" class="control-label col-sm-4">
                                Customer Number </label>
                            <div class="col-sm-6">
                                <input type="text" class="form-control" disabled id="customer_number"
                                       placeholder="Customer Number">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="bank_account" class="control-label col-sm-4">
                                Transaction id </label>
                            <div class="col-sm-6">
                                <input type="text" class="form-control" disabled id="txnid"
                                       placeholder="Transaction id">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="bank_account" class="control-label col-sm-4">
                                Amount </label>
                            <div class="col-sm-6">
                                <input type="text" disabled class="form-control" id="amount"
                                       placeholder="Enter Amount">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="bank_account" class="control-label col-sm-4">
                                OTP </label>
                            <div class="col-sm-6">
                                <input type="text" class="form-control" id="otp"
                                       placeholder="Entrer OTP">
                            </div>
                        </div>

                    </form>
                </div>
                <div class="modal-footer">
                    

                    <span id="imgr" style="display:none;"><img src="images/load2.gif" height="40px" width="40px"></span>
                   <button id="sotpsmart" type="button" onclick="this.disabled=true;refund_success()" class="btn btn-primary">Confirm Refund
                    </button>

                    <button id="eresend" type="button" onclick="refundOtp()" class="btn btn-success">resend Otp</button>
                    
                </div>
            </div>
        </div>
    </div>
	<!-- =====================================================================  -->
    <!--            Display the details of user                                 -->     
    <!-- ====================================================================== -->

        <div class="modal fade" id="user_details" role="dialog">
            <div class="modal-dialog">
            
              <!-- Modal content-->
                <div class="modal-content" style="width: 900px;">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                            <h4 class="modal-title"><strong >User Details</h4>
                    </div>
                    <div class="modal-body">
                        <table class="table" id="result_table">
                        </table>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    </div>
                </div>
              
            </div>
         </div>
    <!-- ====================================================================== -->
    <!--            Display the details of user                            		-->
    <!-- ====================================================================== -->


<div id="con-close-multtxn-nkyc" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
         aria-hidden="true" style="display: none;">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel" style="color:black">NON-KYC Transaction View</h4>
                </div>
                <div class="box">
                    <table  class="table table-responsive" id="example2">
                        <thead>
                        <tr>
                            <th>Txn ID</th>
                            <th>Amount</th>
                            <th>Status</th>
                            <th>Check Status</th>
                            <th>UTR</th>
                            <th>Refund</th>
                        </tr>
                        </thead>
                        <tbody id="response" style="font-family: sans-serif;">

                        </tbody>
                    </table>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default waves-effect" data-dismiss="modal">Close
                    </button>
                    <input type="hidden" id="idnew" name="idnew" value="0">


                </div>
            </div>
        </div>


    </div>



@endsection