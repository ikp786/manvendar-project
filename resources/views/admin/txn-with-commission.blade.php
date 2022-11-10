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
					
					
				},
                success: function (data) {
					
                    $('#recordId').val(data.message.id);
                    $('#recordNumber').val(data.message.id);
                    $('#txnStatus').val(data.message.status_id);
                    $('#txnNumber').val(data.message.number);
                    $('#custMobNumber').val(data.message.customer_number);
                    $('#api_id').val(data.message.api_id);
                    $('#txnId').val(data.message.txnid);
                    $('#txnAmount').val(data.message.amount);
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
                var status = $("#txnStatus").val();
                var token = $("input[name=_token]").val();
                var dataString = 'id=' + id +'&number='+ number + '&txnid=' + txnid +'&status='+ status;
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
                            //$('#btn-save').hide();
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
							 $("#transactionDetailsModel").modal("toggle");	
                            location.reload();
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
		
	$(document).ready(function () {
        $.noConflict();
        $('.customDatepicker').datepicker({
              autoclose: true,  
            format: "dd-mm-yyyy"
        });

    });	
		
    </script>

	<div class="col-sm-12">
		<div class="col-lg-6 col-md-6">
			<h4 class="page-title" style="color: black; ">{{'Txn With Commission' }}</h4>  
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
			{{ Form::select('searchOf', ['1' => 'Success', '2' => 'Failed','3'=>"Pending",'4'=>"Refunded",'21' => 'Manual Success', '28' => 'Manual Failed','18'=>'InProcess'], app('request')->input('searchOf'), ['class'=>'form-control','placeholder'=>'--Select--']) }}
			@else   
			{{ Form::select('searchOf', ['1' => 'Success', '2' => 'Failed','3'=>"Pending",'4'=>"Refunded",'21' => 'Refund Success'], app('request')->input('searchOf'), ['class'=>'form-control','placeholder'=>'--Select--']) }}
			@endif

			{{ Form::select('product', ['4' => 'DMT1', '5' => 'A2Z wallet','10' => 'AEPS'], app('request')->input('product'), ['class'=>'form-control','placeholder'=>'--Select--']) }}     
							   
            <button name="export" value="SEARCH" type="submit" class="btn btn-primary btn-md"><i class="fa fa-search"></i></button>
			
            <button name="export" value="EXPORT" type="submit" class="btn btn-basic btn-md"><i class="fa fa-file-excel-o" aria-hidden="true"></i></button>
               
            <a href="{{ Request::url() }}"/ class="btn btn-info  btn-md"><i class="fa fa-refresh"></i></a>  
        </form> 
    </div>
</div> 	


        <div class="" >
		    <table class="table table-bordered" id="example2">
		        <thead>
                    <tr>
					<th>Date/Time</th>
						<th>ID</th>
						<th>Txn ID</th>
						
						<th>User</th>
						<th>Consumer No</th>
						<th>Acc/mobile/k number</th>
						<th>Credit/Debit</th>
						<th>Opening Bal</th>
						<th>Amount</th>
						<th>Credit</th>
						<th>Debit</th>
						<th>TDS</th>
						<th>Service Tax</th>
						<th>Balance</th>
						<th>Md Comm</th>
						<th>Dist Comm</th>
						<th>Admin Comm</th>
						<th>Description</th>
						<th>Txn Type</th>
						<th>Status</th>
						<th>Action</th>
                    </tr>
                </thead>

                <tbody>
                    @foreach($reports as $key => $value)
					<?php $s = $value->created_at;
						$dt = new DateTime($s);?>
						<tr>
							<td align="center">{{date("d-m-Y H:i:s",strtotime($value->created_at))}}</td>
							<td>{{ $value->id }}</td>
							<td>{{ $value->txnid }}</td>
							 <td><a href="{{ url('user-recharge-report')}}/{{ @$value->user_id }}">{{ @$value->user->name }} ( {{ @$value->user->prefix }} - {{ @$value->user->id }})</a></td>
							<td>{{ $value->customer_number }}</td>
							<td>{{ $value->number }}</td>
							<td>{{ $value->type }}</td>
							<td>{{ number_format($value->opening_balance,2) }}</td>
							<td>{{ $value->amount }}</td>
							<td>{{ number_format($value->credit_charge,3) }}</td>
							<td>{{ number_format($value->debit_charge,2) }}</td>
							<td>{{ number_format($value->tds,3) }}</td>
							<td>{{ number_format($value->gst,2) }}</td>
							<td>{{ number_format($value->total_balance,2) }}</td>
							<td>{{ number_format($value->md_commission,2) }}</td>
							<td>{{ number_format($value->dist_commission,2) }}</td>
							<td>{{ number_format($value->admin_commission,2) }}</td>
							
							
							<td>@if($value->recharge_type== 1)
								{{ @$value->provider->provider_name }}  
								@else
								{{ @$value->api->api_name }} 
								@endif
							</td>

							<td>{{ $value->txn_type }}</td>
							<td>{{ $value->status->status }}</td>
							<td>
							@if(Auth::user()->role_id == 1)
								@if(in_array($value->status_id,array(1,3,9)))
									 <a data-toggle="modal" href="#example" class="table-action-btn" onclick="updateRecord({{$value->id}})">Action</a>

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
        
        <div class="modal-dialog">
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
                            <label for="inputTask" class="col-sm-3 control-label">Record Number</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control has-error" id="recordNumber" name="recordNumber"
                                       placeholder="" value="" readonly>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="inputTask" class="col-sm-3 control-label">A/c Number</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control has-error" id="txnNumber" name="txnNumber"
                                       placeholder="Provider Name" value="" readonly>
                            </div>
                        </div> 
						<div class="form-group">
                            <label for="inputTask" class="col-sm-3 control-label">Customer Mob Number</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control has-error" id="custMobNumber" name="custMobNumber"
                                       placeholder="Customer Mobile Number" value="" readonly>
                            </div>
                        </div>
						<div class="form-group">
                            <label for="inputTask" class="col-sm-3 control-label">Txn Amount</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control has-error" id="txnAmount" name="txnAmount"
                                       placeholder="Provider Name" value="" readonly>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="inputEmail3" class="col-sm-3 control-label">Transaction ID</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control" id="txnId" name="txnId"
                                       placeholder="Provider Code" value="">
                            </div>
                        </div>
						
                        <div class="form-group">
                            <label for="inputEmail3" class="col-sm-3 control-label">Status</label>
                            <div class="col-sm-9">
                                <select class="form-control" name="txnStatus" id="txnStatus">
                                    <option value="1">Success</option>
                                    <option value="2">Failure</option>
                                    <option value="3">Pending</option>
                                    <option value="24">OffLine</option>
                                    <!--<option value="20">Refund Pending</option>-->
                                </select>
                            </div>
                        </div>

                    </div>
                    <div class="modal-footer">
                        <!--<button type="button" class="btn btn-info waves-effect waves-light" id="btn-save" value="add" onclick="this.disabled=true;refundTranaction()">Update</button>-->
                        <button type="button" class="btn btn-info waves-effect waves-light" id="btn-save" value="add" onclick="refundTranaction()">Update</button>
						<img src="{{url('/img')}}/loader.gif" id="loaderImg" class="loaderImg" style="display:none">
						
                        <button type="button" class="btn btn-default waves-effect" data-dismiss="modal">Close</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>	
    <meta name="_token" content="{!! csrf_token() !!}"/>
@endsection