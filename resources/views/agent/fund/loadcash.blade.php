@extends('layouts.app')
@section('content')
<script>
/*for date->Calender*/

    $(document).ready(function () {
        $.noConflict();
        $('.customDatepicker').datepicker({
              autoclose: true,  
            format: "dd-mm-yyyy"
        });

    }); 
	function cancelFundRequest(recordId)
{
	if(confirm("Are you sure want to cancel fund Request"))
	{
		
		var dataString = 'recordId=' + recordId;
		$.ajaxSetup({
			headers: {
			'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
			}
		})
		$.ajax({
			type: "post",
			url: "{{ url('cancel-fund-request') }}",
			data: dataString,
			success: function (data) {
				alert(data.message);
				location.reload();
			   
			}
		})
	}
}
</script>
@include('agent.fund.fund-type')
<br>
<div class="row">

		   <form method="get" action="{{route('fund-req-report')}}" class="form-inline">
				<div class="form-group">
					<input name="fromdate" class="form-control customDatepicker" type="text" placeholder="From date">
				</div>
				<div class="form-group">
					<input name="todate" class="form-control customDatepicker" type="text" placeholder="To date">
				</div>
				<div class="form-group">
					<button name="export" value="Fund Request Reports" type="submit"
							class="btn btn-success waves-effect waves-light m-l-10 btn-md"><span
								class="glyphicon glyphicon-find"></span><i class="fa fa-file-excel-o" aria-hidden="true"></i>
					</button>
					
				</div>
				
			</form>
</div>
<br>
	<table class="table table-bordered">
					<thead>
						<tr>
							<th> Date</th>
							<th>ID</th>
							<th>Bank Name</th>
							<th>Mode</th>
							<th>Branch Code</th>
							<th>Deposit Date</th>
							<th>Amount</th>
							<th>Customer Remark</th>
							<th>Ref Id</th>
							<th>Status</th>
							<th>Remark</th>
							<th>Action</th>
						</tr>
					</thead>
					<tbody>
				@foreach($loadcashes as $loadcash)
					<tr class="{{$loadcash->status->status}}-text">
						<?php $s = $loadcash->created_at;
						$dt = new DateTime($s);?>
						<td>{{ $dt->format('d/m/Y') }}<br>{{ $dt->format('H:i:s') }}</td>
						<td>{{ $loadcash->id }}</td>
						<td>{{ ($loadcash->request_to == 1) ? @$loadcash->bank_name :@$loadcash->netbank->bank_name }}</td>
						<td>{{ $loadcash->payment_mode }}</td>
						<td>{{ $loadcash->loc_batch_code }}</td>
						<td>{{ $loadcash->deposit_date }}</td>
						<td>{{ $loadcash->amount }}</td>
						<td>{{ $loadcash->request_remark }}</td>
						<td>{{ $loadcash->bankref }}</td>
						<td>{{ @$loadcash->status->status }}</td>
						<td>{{ @$loadcash->remark->remark }}</td> 
						<td>
							@if($loadcash->status_id == 3)
								<a onclick="cancelFundRequest({{ $loadcash->id }})" href="javascript:void(0)" class="table-action-btn"><i class="fa fa-close" style="font-size:35px;color:red"></i></a>
							@endif
                        </td>
					</tr>
				@endforeach
			</tbody>
		</table>
		{!! $loadcashes->links() !!}
	                              

 <meta name="_token" content="{!! csrf_token() !!}"/>              
@endsection