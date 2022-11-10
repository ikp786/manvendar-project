@extends('admin.layouts.templatetable')
@section('content')
<div class="col-sm-12">
	<div class="col-lg-6 col-md-6">
		<h4 class="page-title" style="color: black;">{{'Recharge Reports'}}</h4>
	</div>
</div>

@include('search.re-search-with-type-status-export')
              
<div class="box" style="overflow-y:scroll;max-height:450px">
	<table class="table table-bordered" id="example2">
	    <thead>
			<tr>
				<th>Date/Time</th>
				<th>ID</th> 
				<th>User </th> 
				<th>Counsumer No</th>
				<th> Provider</th>
				<th>Amount</th>
				<th>GST</th>
				<th>TDS</th>
				<th>Credit Amount</th>
				<th>Debit Amount</th>
				<th >Txn Type</th>
				@if(Auth::user()->role_id==1)
				<th>Operator</th>
				@endif
				<th>Txn Id</th>
				<th>Op Id</th>
				<th>Status</th>
				<th>slip</th>
			</tr>
		</thead>
		<tbody>
			@foreach($reports as $key => $report)
			<?php $s = $report->created_at;
				$dt = new DateTime($s);?>
				<tr class="{{@$report->status->status}}-text">
					<td>{{ $dt->format('d/m/Y') }}<br>{{ $dt->format('H:i:s') }}</td>
					<td>{{ $report->id }}</td>
					<td>{{ $report->user->name}}({{ $report->user_id }})</td>
					<td>{{ @$report->number }}</td>	
					<td>{{ @$report->provider->provider_name }}</td>
					<td>{{ $report->amount }}</td>
					<td>{{ $report->gst }}</td>
					<td>{{ $report->tds }}</td>
					<td>{{ $report->credit_charge }}</td>
					<td>{{ $report->debit_charge }}</td>
					<td>{{ $report->txn_type }}</td>
					@if(Auth::user()->role_id==1)
					<td>{{ @$report->api->username }}</td>
					@endif
					<td>{{ $report->txnid }}</td>	
					<td>{{ $report->ref_id }}</td>	
					<td>{{ @$report->status->status }}</td>
				</tr>
			@endforeach
		</tbody>
	</table>
	{!! $reports->links() !!}
</div>

@endsection