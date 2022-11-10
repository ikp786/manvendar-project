@extends('layouts.app')

@section('content')
@include('agent.report.report-type')
<style type="text/css">
	.label{
		color:black; 
	}
	#provider_id{
		max-width: 50%;
	}
</style>
<script type="text/javascript">
 $(document).ready(function(){
 // var $optgroups = $('#subCategory > optgroup');
  $("#categoryType").on("change",function(){
		var category = this.value;
		var dataString = 'category=' + category;
		$.ajax({
			type: "get",
			url: "{{url('get-sub-category')}}",
			data: dataString,
			dataType: "json",
			beforeSend:function()
			{
				$("#subcategoryDiv").html('');
				$("#operatorDiv").html('');
			},
			success: function (res) {
			   if(res.status == 1)
			   {
				  var combo = $("<select></select>").attr("id", "subcategory").attr("name", "subcategory").attr('class','form-control').attr('onChange','getOperators()');
				 	  combo.append("<option value=ALL> ALL</option>");
				    $.each(res.subcategories, function (i, el) {
				        combo.append("<option value="+i+">" + el + "</option>");
				    });
				    $("#subcategoryDiv").html(combo);
			   }
			   else
				   alert(res.subcategories);
			}
		});
 	});  
});

function getOperators()
 {
		var category = $("#categoryType").val();

		if(category == 1 || category == 4 ){
			
		var subcategory_id = $("#subcategory").val();
		var dataString = 'subcategory_id=' + subcategory_id;
			$.ajax({
				type: "get",
				url: "{{url('get-operator')}}",
				data: dataString,
				dataType: "json",
				beforeSend:function()
				{
					$("#operatorDiv").html('');
				},
				success: function (res) {
				   if(res.status == 1)
				   {
					  var combo = $("<select></select>").attr("id", "provider_id").attr("name", "provider_id").attr('class','form-control');
					 	  combo.append("<option value=ALL> ALL</option>");
					    $.each(res.providers, function (i, el) {
					        combo.append("<option value="+i+">" + el + "</option>");
					    });
					    $("#operatorDiv").html(combo);
				   }
				   else
					   alert(res.providers);
				}
			});
		}
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
		var dataString = 'mobile_number=' + mobile_number+'&id='+id+'&apiId='+apiId;
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
	$(document).ready(function () {
        $.noConflict();
        $('.customDatepicker').datepicker({
              autoclose: true,  
            format: "dd-mm-yyyy"
        });

    }); 
</script>

<div class="super_container">  
	<div class="search" >
		<div class="panel-body">
			<div class="panel-body">
	            <form method="get" action="{{Request::url()}}" class="form-inline" role="form">
					<div class="form-group ">
						<input id="myInput" type="text" class="form-control" name="number" value="{{app('request')->input('number')}}" placeholder="Search Text">
						
						<input name="fromdate" class="form-control customDatepicker" type="text" value="{{ (app('request')->input('fromdate')) ? app('request')->input('fromdate') : date('d-m-Y')}}" placeholder="From date">
						
						<input name="todate" class="form-control customDatepicker" type="text" value="{{ (app('request')->input('todate')) ? app('request')->input('todate') : date('d-m-Y')}}" placeholder="To date">
						
						{{ Form::select('searchOf', ['1' => 'Success', '2' => 'Failed','3'=>"Pending",'4'=>"Refunded",'24'=>"Successfull",'21'=>"Refund Success"], (app('request')->input('searchOf')), ['class'=>'form-control','placeholder'=>"--Select Status--"]) }}
					
						<button type="submit" class="btn btn-primary" ><span class="glyphicon glyphicon-find"></span><i class="fa fa-search"></i></button>
						
						<button name="export" value="SUMMARY_REPORT" type="submit"
						 class="btn btn-basic"><span class="glyphicon glyphicon-find"></span><i class="fa fa-file-excel-o"></i></button>
						 
						<a href="{{Request::url()}}" class="btn btn-info  btn-md"><i class="fa fa-refresh"></i></a>
					</div>	
                </form>	
			</div>
					<!--<div>
					<span style="font-size: 16px;color:black ;font-family: time;"> Total :{{isset($data->TOTAL) ? $data->TOTAL : 0}}</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
					<span style="font-size: 16px;color: green;font-family: time;"> Success:{{isset($data->SUCCESS) ? $data->SUCCESS : 0}}</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
					<span style="font-size: 16px;color:#a94c27;font-family: time;"> Pending:{{isset($data->PENDING) ? $data->PENDING : 0}}</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
					<span style="color: red;font-size: 16px;font-family: time;">Failled:{{isset($data->FAILURE) ? $data->FAILURE : 0}}</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
					<span style="color: #cc2fce;font-size: 16px;font-family: time;">Reversal:  {{isset($data->REFUNDED) ? $data->REFUNDED : 0}}</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
					
					</div>-->
				
		</div>	
	</div>
	<div class="box">		
	<div style="">	  
		<table id="example2" class="table table-bordered table-hover">
			<thead >
				<tr>
				<th>S. No</th>
				<th>Date/Time</th>
				<th>ID</th> 
				<th>RMN No</th>
				<th>Bene Name</th>
				<th>Bene Account</th>
				<th>Ifsc</th>
				<th>Bank Name</th>
				<th>Remitter Number</th>
				<th>Amount</th>
				<th>Type</th>
				<th>Per Name</th>
				<th>Txn Type</th>
				<th>Operator</th>
				<th>Op Id</th>
				<th>Status</th>
				<th>Check</th>
				<th>Refund</th>
				<th>slip</th>
				<th>Complain</th>
				</tr>
			</thead>
			<tbody>
				@foreach($reportDetails as $key => $report)
					<?php $s = $report->created_at;
					$dt = new DateTime($s);?>
				<tr class="{{$report->status->status}}-text">
					<td>{{$key + 1}}</td>
					<td>{{ $dt->format('d-m-Y') }}<br>{{ $dt->format('H:i:s') }}</td>
					<td>{{ $report->id }}</td>
					<td>{{ @$report->user->mobile }}</td>	
					<td>{{ @$report->beneficiary->name }}</td>
					<td>{{ ($report->recharge_type==0) ? $report->number : '' }}</td>
					
					<td>{{ @$report->beneficiary->ifsc }}</td>
					<td>{{ @$report->beneficiary->bank_name }} </td>
					<td>{{($report->recharge_type==0) ? $report->customer_number : $report->number}} </td> 
					<td>{{ $report->amount }} </td> 
					<td>{{ $report->type }} </td>
					<td>{{ @$report->client_id }} </td>
					<td>{{ $report->txn_type }} </td>							
					<td>
						@if($report->recharge_type== 1)
							{{ @$report->provider->provider_name }}  
							@else
							{{ @$report->api->api_name }} 
						@endif
					</td>
					<td>{{ $report->txnid }}</td>	
					<td>{{ @$report->status->status }}</td>	
					<td>
					 @if(in_array($report->status_id,array(1,3,9)) && $report->api_id !=10)
						<a onclick="TramocheckStatus({{ $report->id }},{{$report->api_id}})" href="javascript::voide(0)" class="btn btn-outline-info btn-sm" id="checkBtn_{{$report->id}}">Check</a> 
						<img src="{{url('loader/loader.gif')}}" id="checkImg_{{$report->id}}" class="loaderImg" style="display: none;">	 
							@endif
					</td>
					<td> 
						@if($report->refund == 1&& $report->api_id !=10)
							<button  onclick="refundRequest({{ $report->id }},{{$report->api_id}})"  class="btn btn-outline-success btn-sm">
							Refund</button> 
							<img src="{{url('loader/loader.gif')}}" id="refundImg_{{$report->id}}" class="loaderImg" style="display: none;">
						@endif
					</td>
					<td style="text-align:center">
					  @if(in_array($report->status_id,array(1,3,9,18,24)))
						<a target="_blank" href="{{ url('invoice') }}/{{ $report->id }}">
							<span class="btn btn-outline-secondary btn-sm">Receipt</span>
						</a>
						@endif
					</td>  
					<td><a onclick="Complain({{ $report->id }})" data-toggle="modal" href="#example">Complain</a> </td>
				</tr>


		
		  @endforeach
			</tbody>
		</table>
		{!! $reportDetails->appends(Request::all())->links() !!}
	</div>

	</div>

</div>
<!--  <div id="do_comp" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
		        <div class="modal-fade" id="example">  -->
<!-- <div class="container" id="do_comp">
	<div id="example" class="modal fade" style="display: none;">
        <div class="modal-content" style="width: 50%; margin-left: 27%;margin-top: 5%">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button> 
            	<h4 class="modal-title">Complain</h4>
            </div>
            <div class="modal-body">
                <form id="frmTasks" name="frmTasks" class="form-horizontal">
                    {!! csrf_field() !!}
                  	<input type="hidden" id="complainId" value="">
                  	<input type="hidden" id="myid" name="product" value="">
                  	<input id="txn" name="txnid" type="hidden" class="form-control" value="">
                  	<input  id="acno" name="acno" type="hidden" class="form-control" value="">
                  	<input id="amount" name="amount" type="hidden" class="form-control" value="">
                    <div class="form-group">
                        <label for="inputEmail3" class="col-sm-3 control-label">Select</label>
                        <div class="col-sm-9">
                           <select id="issue_type" class="form-control" name="issue_type">
                            	<option value="">SELECT ISSUE TYPE</option>
								<option value="AMOUNT NOT CREDIT">AMOUNT NOT CREDIT</option>
								<option value="RECHARGE NOT CREDIT">RECHARGE NOT CREDIT</option>
								<option value="PENDING TXN">PENDING TXN</option>
								<option value="OTHERS">OTHERS</option>
                            </select>
						</div>
                    </div>
                    <div class="form-group">
                        <label for="inputEmail3" class="col-sm-3 control-label">Remark</label>
                        <div class="col-sm-9">
                            <textarea id="remark" class="form-control" name="remark" placeholder="Remarks...."></textarea>
                        </div>
                    </div>
				</form>			
            </div>   
            <div class="modal-footer">
                <button type="button" class="btn btn-info waves-effect waves-light" id="btn"  onclick="stor_complain()">Submit
                </button>
                <button type="button" class="btn btn-default waves-effect" data-dismiss="modal">Close</button> 
                  <input type="hidden" id="id" name="id" value="">
            </div>
        </div>     
    </div>
</div> -->
<script type="text/javascript">
 function saveComplain()
        {
        	 var token = $("input[name=_token]").val();
        
            var complainTxnId = $('#complainTxnId').val();
            var issueType = $("#issueType").val();
            var complainRemark = $("#complainRemark").val();
            var dataString = 'complainTxnId=' + complainTxnId + '&issueType=' + issueType + '&complainRemark=' + complainRemark+'&_token=' + token;
            $.ajax({
                type: "POST",
                url: "{{url('store_complain_req')}}",
                data: dataString,
                datatype: "json",
                success: function (msg) {
					alert(msg.message);
					if(msg.status==1){
						location.reload();
					}
                }
            });

        }

		function Complain(id) {
            var token = $("input[name=_token]").val();
			$('#complainTxnId').val(id);
			$('#complainRemark').val('');
        }
</script>
 <meta name="_token" content="{!! csrf_token() !!}"/>
@endsection