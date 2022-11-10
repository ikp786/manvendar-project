@extends('admin.layouts.templatetable')

@section('content')
<style>
.bootstrap-table{
    margin-top: -28px;
}
</style>
<script>
$(document).ready(function () {
 $.noConflict();
    $('.customDatepicker').datepicker({
          autoclose: true,  
        format: "dd-mm-yyyy"
    });
}); 
</script>
	@include('admin.admin-subtab.offlinerecord-type')
<div class="panel panel-default">
	<div class="panel-body">
		<div class="col-lg-3 col-md-3">
			<h4 class="page-title" style="color: black; ">{{@$title }}</h4> 
		</div>
		<div class="row col-md-9">
			<form method="get" action="{{ Request::url() }}" class="form-inline">
				{{Form::select('recordCount', ['All'=>'All','10'=> '10','30'=> '30','50'=>'50'],app('request')->input('recordCount'), ['class'=>'form-control','id'=>'recordCount','placeholder'=>'--Select No of Record--'])}}
				<input name="number" type="text" class="form-control" id="number" value="{{app('request')->input('number')}}" placeholder="Enter K Number">
				
				<input name="fromdate" class="form-control customDatepicker" type="text" value="{{ (app('request')->input('fromdate')) ? app('request')->input('fromdate'):date('d-m-Y')}}" autocomplete="off"> 
			
				<input name="todate" class="form-control customDatepicker" type="text" value="{{ (app('request')->input('todate')) ? app('request')->input('todate') : date('d-m-Y')}}" autocomplete="off">
				
				<button name="export" value="SEARCH" type="submit" class="btn btn-primary btn-md"><i class="fa fa-search"></i></button>
				<button name="export" value="EXPORT" type="submit" class="btn btn-basic btn-md"><i class="fa fa-file-excel-o" aria-hidden="true"></i></button>
				<a href="{{ Request::url() }}"/ class="btn btn-info  btn-md"><i class="fa fa-refresh"></i></a>   
			</form>
		</div>
	</div> 
</div>
 <div style="overflow-x: scroll;">
	<table id="example2"  class="table table-bordered hover">
		<thead>
			<tr>
				<th>ID</th>
				<th>Txn ID</th>
				<th>RequestDate</th>
				<th>UpdateDate</th>
				<th>User</th>
				<th>Acc/K/M/ Number<br>Customer Number</th>
				<th>Amount</th>
				<th>Credit/Debit</th>
				<th>TDS</th>
				<th>Service Tax</th>
				<th>Balance</th>
				<th>Description</th>
				<th>Txn Type</th>
				<th>Txn Status Type</th>
				<th>Status</th>
			</tr>
		</thead>
		<tbody>
		<?php $totalAmount=$count=0; ?>
			@foreach($offlineUpdatedRecord as $key => $value)
			<?php $s = $value->created_at;
			$dt = new DateTime($s);?>  
			<tr>
				<td>{{$value->id}}</td>
				<td>{{$value->txnid}}</td>
				<td>{{$value->created_at}}</td>
				<td>{{$value->updated_at}}</td>
				<td><a href="{{url('user-recharge-report')}}/{{@$value->user_id}}">{{ @$value->user->name}}({{@$value->user->prefix}} - {{@$value->user->id}})</a></td>
				<td>{{$value->number}}<br>{{$value->customer_number}}</td>
				<td>{{$value->amount}}</td>
				<td>{{$value->type}}</td>
				<td>{{number_format($value->tds,3)}}</td>
				<td>{{number_format($value->gst,2)}}</td>
				<td>{{number_format($value->total_balance,2)}}</td>
				<td>@if($value->recharge_type== 1)
					{{@$value->provider->provider_name}}  
					@else
					{{@$value->api->api_name}} 
					@endif
				</td>
				<td>{{$value->txn_type}}</td>
				<td>{{$value->txn_status_type}}</td>
				<td>{{$value->status->status}}</td>
				<?php $totalAmount +=$value->amount;
						$count++;
				?>
			</tr>
		  @endforeach
		</tbody>
		<h4 style="color:red">Total Amount({{$count}}) : {{$totalAmount}}</h4>
	</table>
	    
</div>
        
 <div id="transactionDetailsModel" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
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
                            <label for="inputTask" class="col-sm-3 control-label">Provider</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control has-error" id="providerName" name="providerName"
                                       placeholder="Provider Name" value="" readonly>
                            </div>
                        </div><div class="form-group">
                            <label for="inputTask" class="col-sm-3 control-label">Mob/ A.c Number</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control has-error" id="txnNumber" name="txnNumber"
                                       placeholder="Account Number" value="" readonly>
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
                                       placeholder="Txn Amount" value="" readonly>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="inputEmail3" class="col-sm-3 control-label">Transaction ID</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control" id="txnId" name="txnId"
                                       placeholder="Transaction Id" value="">
                            </div>
                        </div>
						<div class="form-group">
                            <label for="inputEmail3" class="col-sm-3 control-label">Opertor Ref No</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control" id="refId" name="txnId"
                                       placeholder="Opertor Ref Od" value="">
                            </div>
                        </div>
						<div class="form-group">
                            <label for="inputEmail3" class="col-sm-3 control-label">Date & time</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control" id="dateTime" placeholder="Opertor Ref Od" value="">
                            </div>
                        </div>
						
                        <div class="form-group">
                            <label for="inputEmail3" class="col-sm-3 control-label">Status</label>
                            <div class="col-sm-9">
                                <select class="form-control" name="txnStatus" id="txnStatus">
                                    <option value="1">Success</option>
                                    <option value="2">Failure</option>
                                    <option value="3">Pending</option>
                                    <option value="20">Refund Pending</option>
                                    <option value="24">SuccessfullySubmitted</option>
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
    <meta name="_token" content="{!! csrf_token() !!}"/>
@endsection