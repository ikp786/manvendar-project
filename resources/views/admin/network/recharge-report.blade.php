@extends('admin.layouts.templatetable')

@section('content')

    </script>

<div class="col-sm-12">
	<div class="col-lg-6 col-md-6">
		<h4 class="page-title" style="color: black; ">{{'Recharge Network'  }}</h4>
	</div>
</div>

@include('search.re-search-with-type-status-export')<br>
              
<div >	
	<table id="tableTypeThree" class="table table-bordered table-hover">
                    <thead>
                    <tr>
                     <th>Select</th>
					<th>Date/Time</th>
                    <th>ID</th> 
                    <th>User </th> 
                    <th>Counsumer No</th>
                   <th> Provider</th>
                    <th>Amount</th>
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
                        <tr>
                            <td><input type="checkbox" name = "checkbox[]"  value="{{@$report->id}}"></td>
						<td>{{ $dt->format('d/m/Y') }}<br>{{ $dt->format('H:i:s') }}</td>
						<td>{{ $report->id }}</td>
						<td>{{ $report->user->name}}({{ $report->user_id }})</td>
						<td>{{ @$report->number }}</td>	
						 <td>{{ @$report->provider->provider_name }}</td>
						<td>{{ $report->amount }} </td>
						<td>{{ $report->txn_type }} </td>
						@if(Auth::user()->role_id==1)
						<td>{{ @$report->api->username }} </td>
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