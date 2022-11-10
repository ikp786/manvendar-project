@extends('admin.layouts.templatetable')

@section('content')
<style>
.bootstrap-table{
    margin-top: -28px;
}
</style>
<!-- for popup -->
  
    <script>
        function updateRecord(id) {
            var token = $("input[name=_token]").val();
            var dataString = 'id=' + id + '&_token=' + token;
            
			$.ajax({
                type: "GET",
                url: "{{url('txn/get-report')}}",
                data: dataString,
                datatype: "json",
				beforeSend:function()
				{
				 $("#txn_otp").hide();	
					
				},
                success: function (data) {
					$("#txn_otp").hide();
                    $('#recordId').val(data.message.id);
                    $('#recordNumber').val(data.message.id);
                    $('#txnStatus').val(data.message.status_id);
                    $('#txnNumber').val(data.message.number);
                    $('#custMobNumber').val(data.message.customer_number);
                    $('#description').val(data.message.description);
                    $('#api_id').val(data.message.api_id);
                    $('#txnId').val(data.message.txnid);
                    $('#rrNumber').val(data.message.bank_ref);
                    $('#txnAmount').val(data.message.amount);
                    $('#apiName').val(data.message.apiName);
                    $('#userDetails').val(data.message.userDetails);
                    $('#btn-save').val("Update");
                    $("#transactionDetailsModel").modal("toggle");
                }
            })
        }
		 /* Bellow function added by rajat */
		function refundTranaction()
        {
            if(confirm('Are you sure to transfer?'))
            {
                var id = $("#recordId").val();
                var number = $("#txnNumber").val();
                var txnid = $("#txnId").val();
                var rrNumber = $("#rrNumber").val();
                var status = $("#txnStatus").val();
                var token = $("input[name=_token]").val();
                var dataString = 'id=' + id +'&number='+ number + '&txnid=' + txnid+ '&bank_ref=' + rrNumber +'&status='+ status;
				$.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                }
            })
                $.ajax({
                    type: "POST",
                    url: "{{ url('report/update') }}",
                    data: dataString,
                    datatype: "json",
                    beforeSend: function() {
                            $('#btn-save').hide();
							$('#loaderImg').show();
                    },
                    success: function (res) 
                    {
						$('#btn-save').show();
						$('#loaderImg').hide();
                       $('#btn-save').show();
                       if(res.status == 1)
                       {
                            alert(res.message);
							location.reload();
							//$("#transactionDetailsModel").modal("toggle");	
                       }
                       else
                       {
                            alert(res.message);
                       }
                    }
                });
            }
            else
            {
                $("#btn-save").prop("disabled", false);
            }
        }
		function showApiResp(id) {
            var token = $("input[name=_token]").val();
            var dataString = 'number=' + id + '&export=search';
           $.ajax({
                type: "GET",
                url: "{{url('api-response')}}",
                data: dataString,
                datatype: "json",
                beforeSend:function()
                {
                    $('#apiResponseContent').html('');
                },
                success: function (data) {
                    //alert('hi');
                    var content ="<table class='table'><thead><td>id</td><td>Type</td><td>Api Resp</td><td>Api Req</td><td>Time</td></tr></thead><tbody>";
                    $.each( data.message, function( key, value ) {
                    content +="<tr><td>"+value.id+"</td><td>"+value.api_type+"</td><td>"+value.message+"</td><td>"+value.request_message+"</td><td>"+value.created_at+"</td></tr>";
                    });
                    content +='</tbody></table>';
                    $('#apiResponseContent').html(content);
                }
            })
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
                    if(data.status !=4)
                        $("#statusId_"+id).text(data.msg);
                    alert(data.msg); 
                }
            })
        }
	$(document).ready(function () {
        $.noConflict();
        $('.customDatepicker').datepicker({
              autoclose: true,  
            format: "dd-mm-yyyy"
        });
    }); 
		
	$(document).ready(function()
    {
        $("#txnStatus").change(function() 
		{
			var status_id = $('#txnStatus').val();
			var description = $('#description').val();
            if(($(this).val() == "2" && (description != "BALANCE_INQUIRY" && description != "CASH_WITHDRAWAL")) || (status_id == 1 && description=="CASH_WITHDRAWAL")) 
			{
                $("#txn_otp").show();
                $("#btn-save").hide();
				$("#VerifyOtp").hide();
            }
            else {
                $("#txn_otp").hide();
                $("#btn-save").show();
            }
        });
    });

    function generateOTP()
    {  
        var mobile = $("#mobile").val();
        var recordId = $("#recordId").val();
        var dataString = 'mobile=' + mobile+ '&recordId='+recordId;
        $.ajaxSetup({
                headers: {
                'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                }
            })
             $.ajax({
                type: "post",
                url: "{{url('txn-generate-otp')}}",
                data: dataString,
                dataType: "JSON",
                beforeSend:function(){
                    $("#otpBtn").hide();
                    $("#resendOtp").hide();
                    $("#otpLoaderImg").show()
                    $("#VerifyOtp").hide();
                    $("#btn-save").hide();
                },
                success: function (data) {
                    $("#otpBtn").hide();
                    $("#resendOtp").show();
                    $("#VerifyOtp").show();
                    $("#btn-save").hide();
                    $("#otpLoaderImg").hide();
                    alert(data.message);
                }
            })
    }   
 
    function verifyOtp()
    {  
        var mobile = $("#mobile").val();
        var recordId = $("#recordId").val();
        var otp = $("#otp").val();
        var dataString = 'mobile=' + mobile + '&recordId=' +recordId + '&otp=' + otp;
        $.ajaxSetup({
                headers: {
                'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                }
            })
             $.ajax({
                type: "post",
                url: "{{url('Verify-txn-otp')}}",
                data: dataString,
                dataType: "JSON",
                beforeSend:function(){
                    $("#otpBtn").hide();
                    $("#VerifyOtp").hide();
                    $("#resendOtp").hide();
                    $("#otpLoaderImg").show()   
                },
                success: function (data) {
                    $("#otpBtn").hide();
                    $("#resendOtp").show();
                    $("#otpLoaderImg").hide();
                    $("#btn-save").show();
                    alert(data.message);

                    if(data.status_id==1){
                        $("#btn-save").show();
                        $("#txn_otp").hide();
                        $("#VerifyOtp").hide();
                    }
                    else{
                        $("#btn-save").hide();
                        $("#txn_otp").show();
                        $("#VerifyOtp").show();
                    } 
                }
            })
    }                      	
    </script>
    <!-- Page-Title -->
	<div class="col-sm-12">
		<div class="col-lg-6 col-md-6">
			<h4 class="page-title" style="color: black; ">{{'Ledger Report' }}</h4>
		</div>
	</div>
	<div class="panel panel-default">
    <div class="panel-body">
        <form method="get" action="{{ Request::url() }}" class="form-inline" role="form" >
            <input name="number" type="text" class="form-control" id="number" value="{{app('request')->input('number')}}" placeholder="Search Text">
            
            <input name="amount" type="text" class="form-control" id="amount" value="{{app('request')->input('amount')}}" placeholder="Search Amount">
            
            <input name="fromdate" class="form-control customDatepicker" type="text" value="{{ (app('request')->input('fromdate')) ? app('request')->input('fromdate') : date('d-m-Y')}}" autocomplete="off"> 
            
            <input name="todate" class="form-control customDatepicker" type="text" value="{{ (app('request')->input('todate')) ? app('request')->input('todate') : date('d-m-Y')}}" autocomplete="off">
        
			@if(Auth::user()->role_id == 1) 
				{{ Form::select('searchOf', ['1' => 'Success', '2' => 'Failed','3'=>"Pending",'4'=>"Refunded",'21' => 'Refund Success', '28' => 'Manual Failed','18'=>'InProcess','6'=>'Debit','7'=>'Credit','100'=>'Manual Success','101'=>'Manula Failed'], app('request')->input('searchOf'), ['class'=>'form-control','placeholder'=>'--Select Status--']) }}

				{{ Form::select('user', @$users, app('request')->input('user'), ['class'=>'form-control', 'placeholder'=>"----Select User----"]) }}
				{{ Form::select('product', ['4' => 'DMT1', '25' => 'A2Z Plus Wallet', '5' => 'A2Z wallet', '16' => 'DMT 2','10' => 'AEPS','1' => 'BBPS','13'=>'Suvidhaa','2'=>"INS Verify",'17'=>"A2Z Verify",'14'=>'MROBOTICS'], app('request')->input('product'), ['class'=>'form-control','placeholder'=>'--Select Product--']) }}                         

			@else   
				{{ Form::select('searchOf',['1'=>'Success','2'=> 'Failed','3'=>"Pending",'4'=>"Refunded",'21'=>'Refund Success','6'=>'Debit','7'=>'Credit'], app('request')->input('searchOf'),['class'=>'form-control','placeholder'=>'--Select Status--']) }}
				{{ Form::select('product', ['4' => 'DMT1', '5' => 'A2Z wallet', '16' => 'DMT 2','10' => 'AEPS','1' => 'BBPS','13'=>'Suvidhaa'], app('request')->input('product'), ['class'=>'form-control','placeholder'=>'--Select Product--']) }}                         
			@endif
			
			
			<button name="export" value="SEARCH" type="submit" class="btn btn-primary btn-md"><i class="fa fa-search"></i></button>
			<button name="export" value="EXPORT" type="submit" class="btn btn-basic btn-md"><i class="fa fa-file-excel-o" aria-hidden="true"></i></button>
			@if(Auth::user()->role_id == 1)
			<button name="aeps" value="EXPORT" type="submit" class="btn btn-basic btn-md"><i class="fa fa-file-excel-o" aria-hidden="true"></i>Aeps Excel</button>
		   @endif
		   <a href="{{ Request::url() }}"/ class="btn btn-info  btn-md"><i class="fa fa-refresh"></i></a>  
        </form> 
    </div>
</div> 	

	<div class="box" style="overflow-x: scroll;"><!--style="overflow-x: scroll;"-->
		    <table  class="table table-bordered" id="example2">
		        <thead>
                    <tr>
                      <th>Date/Time</th>
					  <th>ID</th>
						<th>Firm Name</th>
						<th>Remitter No</th>
						<th>Acc/mobile/k number<br>Bene Name</th>
                        <th style="min-width: 212px;font-size: 12px;">Bank Name<br>IFSC</th>
                        <th>Txn ID</th>
                        <th>Amount</th>
                        <th>Web/App</th>
                        <th>Status</th>
                        <th>Bank RR Number</th>
                        <th>Description</th>
						<th>Credit/Debit</th>
						<th>Opening Bal</th>
						<th>Credit</th>
						<th>Debit</th>
						<th>TDS</th>
						<th>Service Tax</th>
                        @if(Auth::user()->role_id == 1)
                             <th>Admin Com Bal</th>
                        @endif
						<th>Balance</th>
						<th>Txn Type/ClientId</th>
                        <th>Operator Txn Id/Remark</th>
						<th>Fund Transfer</th>
						<th>Check</th>
						<th>Action</th>
                    </tr>
                </thead>
  
                                
                <tbody>
                    @foreach($reports as $key => $value)
					<?php $s = $value->created_at;
						$dt = new DateTime($s);?>
						<tr class="{{@$value->status->status}}-text">
                            <td>{{ $dt->format('d/m/y')}}<br>{{ $dt->format('H:i:s') }}</td>
							<td>{{ $value->id }}</td>
							
							 <td style="min-width: 180px;"><a href="{{ url('user-recharge-report')}}/{{ @$value->user_id }}">{{ @$value->user->member->company }} ( {{ @$value->user->prefix }} - {{ @$value->user->id }})</a><br>{{@$value->user->mobile}}</td>
							<td>{{ $value->customer_number }}</td>
							<td>
							{{ $value->number }}<br>
                                <span style="font-style: bold;">{{@$value->beneficiary->name}}</span>
								<p>{{ @$value->biller_name}}</p></td>
                            <td>
							 @if(in_array($value->api_id,array(2,10)) && in_array(@$value->status_id,array(1,2,3,4,21)))
								<p>{{ @$value->description}}</p>
							 <p>{{-- @$value->biller_name --}}</p>
							   
					       @else
                                @if(@$value->payment_id>0 )
                                {{@$value->payment->netbank->bank_name}}
                                @else
                                <p style="font-weight: bold">{{ @$value->beneficiary->bank_name }}</p>
                                <p style="font-style: italic;">{{ @$value->beneficiary->ifsc }}</p>
                                @endif
                               @endif
                            </td>
							<td>{{$value->txnid}}
                             @if(@$value->payment_id>0)
                             <p>Fund request</p>
                             @endif
                            </td>
                            <td>{{$value->amount}}</td>
                            <td>{{$value->mode}}</td>
                            <td>{{@$value->status->status}}<br>{{$value->txn_initiated_date}}
							<p>@if($value->recharge_type==0 && $value->txnid !="DT" && $value->api_id !=2&& $value->api_id !=10) 
                            {{($value->channel==2)?"IMPS":"NEFT"}}</p>@endif
							</td>
                            <td>{{$value->bank_ref}}</td>
                            <td style="min-width: 160px;text-align:center;">			   	 @if($value->recharge_type== 1) 		
                                {{ @$value->provider->provider_name}}  
                                @else
                                {{ @$value->api->api_name}} 
                                @endif
                            </td>
							<td>{{$value->type}}</td>
							<td>{{number_format($value->opening_balance,2)}}</td>
							
							<td>{{number_format($value->credit_charge,3)}}</td>
							<td>{{number_format($value->debit_charge,2)}}</td>
							<td>{{number_format($value->tds,3)}}</td>
							<td>{{number_format($value->gst,2)}}</td>
                            @if(Auth::user()->role_id == 1)
                             <td>{{ number_format($value->admin_com_bal,2) }}</td>
                            @endif
							<td>{{ number_format($value->total_balance,2) }}</td>
							
							

							<td>{{ $value->txn_type }}<br>{{ $value->client_ackno }}</td>
                            <td><p>{{ $value->paytm_txn_id }}</p>
                                <p>{{$value->remark}}
                            </td>
							
							<td>@if($value->txnid=="DT")
								{{$value->description}}
								@endif
							</td> 
							<td>
								@if(in_array(@$value->status_id,array(1,3,9,18)))
                                @if($value->api_id==10)
                                
                                @else
                                <a onclick="TramocheckStatus({{ $value->id }},{{$value->api_id}})" href="javascript::void(0)" class="btn btn-outline-info btn-sm" id="checkBtn_{{$value->id}}">
                                Check</a> 
                                <img src="{{url('loader/loader.gif')}}" id="checkImg_{{$value->id}}" class="loaderImg" style="display: none;">@endif
								@endif
                            </td> 
							<td>
							@if(Auth::user()->role_id == 1)
								@if(in_array($value->status_id,array(1,3,9,18)))
								<a data-toggle="modal" href="#example" class="table-action-btn" onclick="updateRecord({{$value->id}})">Action</a>
								@endif
								@if(in_array($value->status_id,array(1,2,3,9,20,21,24,32)))
                                   <a data-toggle="modal" href="#showApiResponse" class="table-action-btn" onclick="showApiResp({{$value->id}})">Response</a>
                                @endif
							@endif
							</td>
						</tr>
                    @endforeach
                </tbody>
            </table>
                {{$reports->appends(\Input::except('page'))->render() }} 
    </div>
   <div class="container" id="transactionDetailsModel">
    <div id="example" class="modal fade" style="display: none;">
        
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
				<h4 class="modal-title">Transaction Details</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                    
                </div>
               <!-- <form id="frmTasks" action="{{ url('report/update') }}" method="post" name="frmTasks" class="form-horizontal" novalidate=""> -->
                <form id="frmTasks" name="frmTasks" class="form-horizontal" novalidate="">
                    {!! csrf_field() !!}

                    <div class="modal-body">
                        <div style="display:none" id="name-error"></div>
                        <input type="hidden" name="recordId" value="" id="recordId">
						<div class="form-group">
							<div class="col-md-6">
								<label for="inputTask" class="col-sm-3 control-label">Record Number</label>
								<div class="col-sm-9">
									<input type="text" class="form-control has-error" id="recordNumber" name="recordNumber" placeholder="" value="" readonly>
								</div>
                            </div>
							<div class="col-md-6">
								<label for="inputTask" class="col-sm-3 control-label">User Detils</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control has-error" id="userDetails" name="userDetails" placeholder="" value="" readonly>
                            </div>
							</div>
                        </div>
						<div class="form-group">
							<div class="col-md-6">
								<label for="inputTask" class="col-sm-3 control-label">A/c Number</label>
								<div class="col-sm-9">
									<input type="text" class="form-control has-error" id="txnNumber" name="txnNumber" placeholder="Provider Name" value="" readonly>
								</div>
							</div>
							<div class="col-md-6">
								<label for="inputTask" class="col-sm-3 control-label">Customer Mob Number</label>
								<div class="col-sm-9">
                                <input type="text" class="form-control has-error" id="custMobNumber" name="custMobNumber" placeholder="Customer Mobile Number" value="" readonly>
                            </div>
							</div> 
                        </div> 
						<div class="form-group">
							<div class="col-md-6">
								<label for="inputTask" class="col-sm-3 control-label">Txn Amount</label>
									<div class="col-sm-9">
										<input type="text" class="form-control has-error" id="txnAmount" name="txnAmount" placeholder="Provider Name" value="" readonly>
									</div>
							</div>
							<div class="col-md-6">
								 <label for="inputEmail3" class="col-sm-3 control-label">Transaction ID</label>
								<div class="col-sm-9">
									<input type="text" class="form-control" id="txnId" name="txnId" placeholder="Provider Code" value="">
								</div>
							</div>
                        </div>
						<div class="form-group">
							
							<div class="col-md-6">
								 <label for="inputEmail3" class="col-sm-3 control-label">RR Number</label>
								<div class="col-sm-9">
									<input type="text" class="form-control" id="rrNumber" name="rrNumber" placeholder="Bank Ref Number" value="">
								</div>
							</div>
							<div class="col-md-6">
								 <label for="inputEmail3" class="col-sm-3 control-label">Description</label>
								<div class="col-sm-9">
									<input type="text" class="form-control" id="description" name="description" placeholder="Description" value="">
								</div>
							</div>
                        </div>
                       
						 <div class="form-group">
							<div class="col-md-6">
								<label for="inputEmail3" class="col-sm-3 control-label">Product/Service</label>
								<div class="col-sm-9">
									<input type="text" class="form-control" id="apiName" name="apiName" placeholder="Product Name" value="" readonly>
								</div>
							</div>
                        <div class="col-md-6">
                            <label for="inputEmail3" class="col-sm-3 control-label">Status</label>
                            <div class="col-sm-9">
                                <select class="form-control" name="txnStatus" id="txnStatus">
                                    <option value="1">Success</option>
                                    <option value="2">Failure</option>
                                    <option value="3">Pending</option>
                                    <!--<option value="20">Refund Pending</option>-->
                                </select>
                            </div>
                        </div>
                        </div>
						<div style="display: none" id="txn_otp">
                            <div class="form-group">
									<div class="col-md-6">
										<label for="inputEmail3" class="col-sm-3 control-label">Mobile</label>
										<div class="col-sm-9">
											<select  class="form-control" name="mobile" id="mobile">
												@foreach($otpVerifications as $verification)
												<option>{{$verification->mobile}}</option>
												@endforeach
											</select>
										</div>
									</div>
                                <div class="col-md-6">
                                    <label for="inputEmail3" class="col-sm-3 control-label">OTP</label>
                                    <div class="col-sm-4">
                                        <input type="text" class="form-control" name="otp" id="otp" placeholder="Enter OTP" required>
                                    </div> 
                                    <a onClick="generateOTP()" id="otpBtn" class="btn btn-basic" style="background:khaki;">Send OTP</a><img src="{{url('/loader/loader.gif')}}" id="otpLoaderImg" class="loaderImg">
                                    <a onClick="verifyOtp()" class="btn btn-primary" id="VerifyOtp">Verify</a>
                                    <a onClick="generateOTP()" class="btn btn-success" id="resendOtp" style="display: none">Resend</a>
                                </div> 
							</div>
                        </div>
						

                    </div>
                    <div class="modal-footer">
                        <!--<button type="button" class="btn btn-info waves-effect waves-light" id="btn-save" value="add" onclick="this.disabled=true;refundTranaction()">Update</button>-->
                        <button type="button" class="btn btn-info waves-effect waves-light" id="btn-save" value="add" onclick="refundTranaction()">Update</button>
						<img src="{{url('/loader/loader.gif')}}" id="loaderImg" class="loaderImg" style="display:none">
						
                        <button type="button" class="btn btn-default waves-effect" data-dismiss="modal">Close</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<div class="container" id="showApiResponseModal">
    <div id="showApiResponse" class="modal fade" style="display: none;">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Api Resp</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button> 
                </div>
                <form id="frmTasks" name="frmTasks" class="form-horizontal" >
                   <div class="modal-body">
                        <div id="apiResponseContent" style="overflow: auto">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default waves-effect" data-dismiss="modal">Close</button>
                    </div>
                </form>
            </div>
        </div>
    </div>  
</div>	
    <meta name="_token" content="{!! csrf_token() !!}"/>
@endsection