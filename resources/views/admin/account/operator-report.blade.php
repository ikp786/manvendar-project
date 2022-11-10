@extends('admin.layouts.templatetable')

@section('content')
<style>
.bootstrap-table{
    margin-top: -28px;
}
</style>
<script type="text/javascript">
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
        <h4 class="page-title" style="color: black; ">{{'Operator Wise Report'}}</h4>  
    </div>
</div>		
<div class="panel panel-default">
	<div class="panel-body">	
		<div class="col-lg-9 col-md-9">
			<form method="get" action="{{ Request::url() }}" onSubmit="return validateExportForm()" class="form-inline">
				<div class="form-group">
					<input name="fromdate" class="form-control customDatepicker" type="text" value="{{ (app('request')->input('fromdate')) ? app('request')->input('fromdate') : date('d-m-Y')}}">
				</div>
				<div class="form-group">
					<input name="todate" class="form-control customDatepicker" type="text" value="{{ (app('request')->input('todate')) ? app('request')->input('todate') : date('d-m-Y')}}">
				</div>
				<button type="submit" value="SEARCH" name="export" class="btn btn-primary btn-md"><i class="fa fa-search"></i></button> 
				
				<a href="{{ Request::url() }}"/ class="btn btn-info  btn-md"><i class="fa fa-refresh"></i>
				</a>
			</form>
		</div>
	</div>
</div>
<div class="box">
	<table class="table table-bordered">
		<thead>
			<th>Operator Name</th>
			<th>Txn Count</th>
			<th>Amount</th>
			<th>Txn Charge</th>
			<th>Txn Commission</th>
		</thead>
		<tbody>
			@foreach($reports as $key => $value)
			   <tr>
			   		<td>{{$value->provider->provider_name}}</td>
			   		<td>{{$value->txnCount}}</td>
			   		<td>{{$value->txnAmount}}</td>
			   		<td>{{$value->debitCharge}}</td>
			   		<td>{{$value->txnCommission}}</td>
			   </tr>
			@endforeach
		</tbody>
		
	</table>
</div>


     
@endsection