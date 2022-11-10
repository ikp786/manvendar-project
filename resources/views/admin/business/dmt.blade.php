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
        function refundReqnew() {
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
            var dataString = 'sessionid=' + sessionid + '&api=' + api + '&mobile_number=' + mobile_number + '&txnid=' + txnid + '&otp=' + otp + '&payid=' + payid + '&_token=' + token;
            $.ajax({
                type: "POST",
                url: "{{url('/spay/transaction_refund_success')}}",
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
                    if(obj2[key].status==2)
                    {
                        $('#nonkyc_check_'+txn_id).hide();
                        $('#nkyc_refund_'+txn_id).show();
                    }
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

        function nonkyc_check(txnid,amount) {
            var token = $("input[name=_token]").val();
            var dataString = 'txnid=' + txnid + '&_token=' + token;
            $.ajax({
                type: "GET",
                url: "{{url('ypay/nkyc_transaction_status')}}",
                data: dataString,
                dataType: "json",
                success: function (data) {
                    //console.log(data);
                    alert(data.message);
                    $('#nkyc_success_'+txnid).html(data.message);
                    if(data.message=='FAILED')
                    {
                        $('#nkyc_refund').show();
                        $('#nonkyc_check_'+txnid).hide();
                    }
                    
                   // location.reload();
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

        function nonkyc_refund_view(id)
        {

            var token = $("input[name=_token]").val();
            var number = $("#number").val();
            var dataString = 'txnid=' + id + '&_token=' + token;
            $.ajax({
                type: "POST",
                url: "{{url('ypay/transaction-refund-otp-nonkyc')}}",
                data: dataString,
                datatype: "json",
                success: function (msg) {
                    $("#payid").val(msg.id);
                    $("#amount").val(msg.amount);
                    $("#customer_number").val(msg.mobile_number);
                    $("#txnid").val(msg.txid);
                    $("#yrefund").hide();
                    $("#yotp").hide();
                    $("#nkycotp").show();
                    $("#nkycrefund").show();
                    $('#sotsmart').hide();
                    $('#sotpsmart').hide();
                    $('#eresend').hide();
                    $('#sotps').hide();
                    $("#con-close-multtxn-nkyc").modal("hide");
                    $("#myModalrefund").modal("toggle");
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
    </script>
    <style type="text/css">
        .label-undefined
        {
            background: blue;
        }
    </style>
	@include('admin.admin-subtab.business-type')
    <div class="">
      
                <div class="col-md-6">
                    <h4 class="page-title" style="color: black; ">{{ $page_title or 'DMT' }}</h4>
                </div>
          
    </div>
    <div class="panel panel-default">
        <div class="panel-body">
            <div class="col-md-12">
                <div class="col-md-4">
                    <form method="get" action="{{ url('searchall_money') }}" class="form-inline" role="form">
                         {!! csrf_field() !!}
                        <div class="form-group">
                           <input name="number" type="text" class="form-control" id="exampleInputEmail2"
                                   placeholder="Number">
                        </div>
                        <button type="submit" name="export" value="search"
                                class="btn btn-success"><span
                                    class="glyphicon glyphicon-find"></span>Search
                        </button>
                    </form>
                </div>
                 @if (in_array(Auth::user()->role_id,array(1,11,12,14)))
                <div class="col-md-8">
               
                    <form method="get" action="{{ url('searchall_money') }}">
                        <div class="form-group col-md-4">
                            <input name="fromdate" class="form-control" type="date">
                        </div>
                        <div class="form-group col-md-4">
                            <input name="todate" class="form-control" type="date">
                        </div>
						<div class="form-group col-md-3">
                        {{ Form::select('product', ['16' => 'SHIGHR F','17'=>'SHIGHR P'], null, ['placeholder' => '--Select--','class'=>'form-control']) }}
                        </div>
                        <div class="form-group col-md-1">
                            <button name="export" value="DMT Reports" type="submit"
                                    class="btn btn-primary"><span
                                        class="glyphicon glyphicon-find"></span>Export
                            </button>
                        </div>
                       <!-- <div class="form-group col-md-2">
                            <button value="search" name="search" type="submit"
                                    class="btn btn-success waves-effect waves-light m-l-10 btn-md"><span
                                        class="glyphicon glyphicon-find"></span>Search
                            </button>
                        </div> -->
                    </form>
                  
                </div>
                  @endif
                <script>

                </script>
            </div>

        </div>
    </div>
    <div class="row">
        
        <input type="hidden" id="yres_code">
        <!-- Cart table -->
        <div class="table">
		<table id="mytable"  class="table table-bordered " >
                <thead>
                <tr>
                   <th data-field="date" data-sortable="true">&nbsp&nbsp&nbsp&nbspDate/Time &nbsp&nbsp&nbsp&nbsp&nbsp&nbsp </th>
					  <!--<th data-field="time" data-sortable="true" data-formatter="dateFormatter"> </th>-->
					<th data-field="ids" data-sortable="true">ID
                    </th>
                    @if(Auth::user()->role_id==1)
						<th data-field="user-name" data-sortable="true">User Name/UserId </th>
                    @endif
                    <th data-field="txnid" data-sortable="true">Txn ID</th>
                    
                    <th data-field="api" data-sortable="true">Product
                    </th>
                    <th data-field="mobilenumber" data-sortable="true">Mobile No</th>
                    <th data-field="name" data-sortable="true">Name</th>
                    <th data-field="account-number" data-sortable="true">Account No</th>
                    <th data-field="bank-name" data-align="center" data-sortable="true"
                       >Bank Name/IFSC
                    </th>
                 <!--<th data-field="ifsc" data-align="center" data-sortable="true">IFSC</th>-->
                    <th data-field="utr" data-align="center" data-sortable="true">UTR Number</th>
					
                    <th data-field="amount" data-align="center" data-sortable="true">Amount</th>
                    <th data-field="mode" data-align="center" data-sortable="true">Mode</th>
                    <th data-field="status" data-align="center" data-sortable="true" data-formatter="statusFormatter">Status
                    </th>
                    <th data-field="action" data-align="center" data-sortable="true">Action
                    </th>
                    <th data-field="refund" data-align="center" data-sortable="true">Refund
                    </th>
                </tr>
                </thead>
                <tbody>
                @foreach($reports as $report)
				<?php $s = $report->created_at;
						$dt = new DateTime($s);?>
                    <tr>
                         <td>{{ $dt->format('d-m-Y') }}<br>{{ $dt->format('H:i:s') }}</td>
                            
						<td>{{ $report->id }}</td>
						
						
							@if(Auth::user()->role_id == 1)
								<td>
								<a href="javascript::voide(0)" onclick="getUserDetails({{ $report->user_id }})">{{ @$report->user->name }}({{@$report->user_id}})</a></td>
								
							@endif
						
                        <td>{{ $report->txnid  }}</td>
                        <td>{{ $report->api->api_name }}</td>
                        <td><input type="hidden" id="c_mob_{{ $report->id }}" value="{{ $report->customer_number }}">{{ $report->customer_number }}</td>
                        
                        <td>{{ @$report->beneficiary->name }}</td>
                        
                        <td>{{ $report->number }}</td>
                        
                         
                        
                        <td>{{ @$report->beneficiary->bank_name }}<br>{{  @$report->beneficiary->ifsc }}</td>
                        
                        
                        @if($report->status_id==3)
                        <td>--</td>
                        @else
                         <td><?php 
												$bank_ref_no = @$report->bank_ref;
												if(!is_numeric ($bank_ref_no))
												{
													$content= explode("/",$bank_ref_no);
													
													if($content)
													{
														if(isset($content[1]))
															echo $word = $content[1];
														else
															echo $exact_word = $bank_ref_no;
													}
													else
														echo $exact_word = $bank_ref_no;
												}
												else
													echo $exact_word = @$report->bank_ref;
											
											?></td>
                        
                        @endif
                        
                        <td>{{ $report->amount}}</td>
                        <td>@if($report->channel == 1){{ "NEFT" }}@else{{ "IMPS" }} @endif</td>
                        <td>{{ @$report->status->status }}</td>
                        @if($report->api_id==16 || $report->api_id == 2 ) 
							@if($report->status_id==3 || $report->status_id==9 || $report->status_id==1)
								<td>
									<a onclick="checkStatus({{ $report->id }})" href="#"
									   class="table-action-btn btn btn-primary btn-xs"><i
												class="md md-visibility"></i>Check</a>
									<a href="#" class="table-action-btn"><i class="md md-close"></i></a>
								</td>
								
							@else
                        <td>--</td>
                        @endif
                        @elseif($report->api_id==17)
                        @if($report->status_id==3 || $report->status_id==9 || $report->status_id==1)
                                <td>
                                    <a onclick="PayTmCheckStatus({{ $report->id }})" href="#"
                                       class="table-action-btn btn btn-primary btn-xs"><i
                                                class="md md-visibility"></i>Check</a>
                                    <a href="#" class="table-action-btn"><i class="md md-close"></i></a>
                                </td>
                                
                            @else
                            <td>--</td>
                        @endif
                        @endif
        
                        <td>
                            @if($report->refund == 1)
                                <a onclick="Calculate({{ $report->id }})" href="#"
                                   class="table-action-btn"><i
                                            class="md md-visibility"></i>Refund</a>
                                <a href="#" class="table-action-btn"><i class="md md-close"></i></a>
                            @endif
                        </td>
                     
                    </tr>
            
                   
                 
                   
                @endforeach
                </tbody>
            </table>
            {!! $reports->links() !!}
        </div>
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
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel" style="color:black">NON-KYC Transaction View</h4>
                </div>
                <div class="modal-body">
                    <table style="font-size: 14px; color:black;"
                           class="table table-responsive">
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