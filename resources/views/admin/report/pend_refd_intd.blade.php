@extends('admin.layouts.templatetable')


@section('content')
<title>Shighrapay Pening Refund</title>
    <script>
        function checkStatus(id) {
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
                    if (data.data.txstatus_desc == 'Failed') {
                        //swal("Failed", data.data.txstatus_desc, "error");
                    } else {

                        //swal("Success", data.data.txstatus_desc, "success");
                    }
                    //location.reload();
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
            var number = $("#number").val();
            var dataString = 'txnid=' + id + '&_token=' + token;
            $.ajax({
                type: "POST",
                url: "{{url('epay/transaction-refund-otp')}}",
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
                        $("#sotps").show();
                    } else {
                        $("#sotsmart").hide();
                        $("#sotpsmart").show();
                        $("#eresend").show();
                        $("#sotps").hide();
                    }
                    $("#payid").val(msg.id);
                    $("#myModalrefund").modal("toggle");
                }
            });
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
            var sessionid = $("#sessionid").val();
            var mobile_number = $("#customer_number").val();
            var dataString = 'sessionid=' + sessionid + '&api=' + api + '&mobile_number=' + mobile_number + '&txnid=' + txnid + '&otp=' + otp + '&payid=' + payid + '&_token=' + token;
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
                    location.reload();
                    $("#c_sender_id").val(mobile_number);
                    $("#c_bene_name").val(name);
                    $("#c_bene_id").val(id);
                    $("#customer_number").val(mobile_number);
                    $("#c_bank_account").val(account);
                    $("#sotp").hide();
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
            var sessionid = $("#sessionid").val();
            var mobile_number = $("#customer_number").val();
            var dataString = 'sessionid=' + sessionid + '&api=' + api + '&mobile_number=' + mobile_number + '&txnid=' + txnid + '&otp=' + otp + '&payid=' + payid + '&_token=' + token;
            $.ajax({
                type: "GET",
                url: "{{url('/spay/refund-otp-eko')}}",
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

        function chektxnstatus()
        {
           
            var ch = $("input[type=checkbox]:checked").length;
           
            $("input[type=checkbox]:checked").each(function(){
                var v =$(this).val();

                    $("#multitxn_"+v).click();
            }); 

        }
    </script>
    
        <div class="panel-body">
		<div class="col-md-12"><span style="    font-size: 20px;
    color: white;
    font-family: time;
    font-weight: bold;">Pending Refund and Initiated Reports:</span></div>
            <div class="col-md-12">
                <div class="col-md-4">
                   <form method="get" action="{{ url('pend-refd-intd-search') }}" class="form-inline" role="form">
                        {!! csrf_field() !!}
					<div class="form-group col-md-6">
                           <select class="form-control" name="search_pri">
						   <option value="0">Select Your Status</option>
							   <option value="p">Pending</option>
							   <option value="r">Refund Pending</option>
                               <option value="i">Initiated</option>
							    <option value="dr">Refunded</option>
							    <!-- <option value="b">Blank TID</option>  -->
                                <option value="d_tid">Double Refunded</option>
                          </select>
                    </div>
					<div class="form-group col-md-6">
					 <button onclick="tekdeail()" type="submit"
                                class=" form-control btn btn-success btn-sm"><span
                                    class="glyphicon glyphicon-find"></span>Search
                        </button>
						<button type="submit" name='export' value='export' target="_blank"
                                class="btn btn-primary"><span class="glyphicon glyphicon-export"></span> Export
                        </button>
						</div>
						</form>
                        
						
                </div>
                <div class="col-md-8">
                    <form method="get" action="{{ url('export_pri') }}">
                        <div class="form-group col-md-4">
                            <input name="fromdate" class="form-control" type="date">
                        </div>
                        <div class="form-group col-md-4">
                            <input name="todate" class="form-control" type="date">
                        </div>
                        <div class="form-group col-md-2">
                            <button name="export" value="export" type="submit"
                                    class=" form-control btn btn-success btn-sm"><span
                                        class="glyphicon glyphicon-find"></span>Export
                            </button>
                        </div>
                        <div class="form-group col-md-2">
                            <button value="search" name="search" type="submit"
                                    class="form-control btn btn-success btn-sm"><span
                                        class="glyphicon glyphicon-find"></span>Search
                            </button>
                        </div>
						
						
                    </form>

                </div>

                <script>

                </script>
            </div>

       
    </div>
    <div class="row">
        <input type="hidden" value="{{ $sessionid }}" id="sessionid">
        <!-- Cart table -->
        <div class="card-box">
            <table data-toggle="table"
                   data-search="true"
                   data-page-list="[10, 10, 20]"
                   data-page-size="40">
                <thead>
                <tr>
                <th data-field="state" data-checkbox="true" class="txncheckbox"></th>

                   <th data-field="date" data-sortable="true">&nbsp&nbsp&nbsp&nbspDate &nbsp&nbsp&nbsp&nbsp&nbsp&nbsp </th>
					  <th data-field="time" data-sortable="true">Time</th>
					<th data-field="ids" data-sortable="true">ID
                    </th>
                    <th data-field="id" data-sortable="true">Tx ID
                    </th>
                    <th data-field="api" data-sortable="true">Product
                    </th>
                    <th data-field="mobilenumber" data-sortable="true">Mobile Number</th>
                    <th data-field="name" data-sortable="true">Name</th>
                    <th data-field="number" data-sortable="true">Account Number</th>
                    <th data-field="profit" data-align="center" data-sortable="true"
                        data-sorter="priceSorter">Bank Name
                    </th>
                    <th data-field="ifsc" data-align="center" data-sortable="true"
                        data-sorter="priceSorter">IFSC
                    </th>
                    <th data-field="utr" data-align="center" data-sortable="true"
                        data-sorter="priceSorter">UTR Number
                    </th>
                    <th data-field="amount" data-align="center" data-sortable="true"
                        data-sorter="priceSorter">Amount
                    </th>
                    <th data-field="mode" data-align="center" data-sortable="true"
                        data-sorter="priceSorter">Mode
                    </th>
                    <th data-field="status" data-align="center" data-sortable="true"
                        data-formatter="statusFormatter">Status
                    </th>
                    <th data-field="action" data-align="center" data-sortable="true">Action
                    </th>
                    <th data-field="refund" data-align="center" data-sortable="true">Refund
                    </th>
                </tr>
                </thead>
                <tbody>
                <?php $i=1; ?>
                @foreach($reports as $report)
				<?php $s = $report->created_at;
						$dt = new DateTime($s);?>
                    <tr>
					<td>{{ $report->id }}</td>
                        <td>{{ $dt->format('d-m-Y') }}</td>

                            <td>{{ $dt->format('H:i:s') }}</td>
							
						<td>{{ $report->id }}</td>
                        <td>{{ $report->txnid }}</td>
                        <td>{{ $report->api->api_name }}</td>
                        <td>{{ $report->customer_number }}</td>
                        <td>{{ @$report->beneficiary->name }}</td>
                        <td>{{ $report->number }}</td>
                        <td>{{ @$report->beneficiary->bank_name }}</td>
                        <td>{{  @$report->beneficiary->ifsc }}</td>
                        <td>{{  $report->bank_ref }}</td>
                        <td>{{ $report->amount }}</td>
                        <td>@if($report->channel == 1){{ "NEFT" }}@else {{ "IMPS" }} @endif</td>
                        <td>{{ $report->status->status }}</td>
                         @if($report->status_id == 2 || $report->status_id == 4)
                                        <td></td>
                                        @elseif($report->amount>5000 && $report->remark=="NON-KYC")
                                          <td>
                                                <!-- @if($report->api->api_name=='SHINE')
                                                  <a onclick="checktransStatus ({{ $report->bank_ref }})" href="#"
                                                    class="table-action-btn btn btn-primary"><i
                                                        class="md md-visibility"></i>Check Status</a>
                                               @else -->
                                                   <a onclick="multi_txn_view({{ $report->id }})" href="#"
                                                    class="table-action-btn btn btn-primary">ViewTxns</a>
                                                        <!-- // @endif -->
                                           
                                        </td>
                                        @else
                        <td>
						@if(in_array($report->status_id,array(1,3,9)))
                        <a onclick="checkStatus({{ $report->id }})" id="multitxn_<?php echo $i; ?>" href="#"
                               class="table-action-btn btn btn-primary" value="{{ $report->id }}"><i
                                        class="md md-visibility"></i>Check Status</a>
						@endif
                            
                        </td>
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
                    <?php $i++; ?>
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
                                       placeholder="Ender OTP">
                            </div>
                        </div>

                    </form>
                </div>
                <div class="modal-footer">
                    <button id="sotsmart" type="button" onclick="refundReqotp()" class="btn btn-primary">Resend OTP
                    </button>
                    <span id="imgr" style="display:none;"><img src="images/load2.gif" height="40px" width="40px"></span>
                    <button id="sotpsmart" type="button" onclick="this.disabled=true;refundReq()" class="btn btn-primary">Confirm Refund
                    </button>

                    <button id="eresend" type="button" onclick="refundOtp()" class="btn btn-success">resend Otp</button>
                    <button id="sotps" type="button" onclick="this.disabled=true;refundReqnew()" class="btn btn-success">Refund Now
                    </button>
                </div>
            </div>
        </div>
    </div>






@endsection