@extends('admin.layouts.templatetable')
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
<div class="row">
	   @include('search.only-search-with-export')
</div>
<div class="box" style="overflow-x: scroll;">
	<table class="table table-bordered" id="example2">
			<thead>
				
					<th> Date</th>
					<th>ID</th>
					<th>Request To</th>
					<th>Bank Name</th>
					<th>Mode</th>
					<th>Branch Code</th>
					<th>Deposit Date</th>
					<th>Amount</th>
					<th>Deposit Slip</th>
					<th>Customer Remark</th>
					<th>Ref Id</th>
					<th>Status</th>
					<th>Remark</th>
					<th>Updated Reamrk</th>
					<th>Action</th>
				
			</thead>
			<tbody>
			@foreach($loadcashes as $loadcash)
			<tr>
				<?php $s = $loadcash->created_at;
				$dt = new DateTime($s);?>
				<td>{{ $dt->format('d/m/Y') }}<br>{{ $dt->format('H:i:s') }}</td>
				<td>{{ $loadcash->id }}</td>
				<td>{{ ($loadcash->request_to == 1) ? (Auth::user()->parent->name .'( '.Auth::user()->parent->prefix . ' - ' .Auth::user()->parent->id .')'): Auth::user()->company->company_name}}</td>
				<td>{{ ($loadcash->request_to == 1) ? @$loadcash->bank_name :(@$loadcash->netbank->bank_name .':'. @$loadcash->netbank->bank_name)}}</td>
				<td>{{ $loadcash->payment_mode }}</td>
				<td>{{ $loadcash->loc_batch_code }}</td>
				<td>{{ $loadcash->deposit_date }}</td>
				<td>{{ $loadcash->amount }}</td>
				<td>
					 @if( $loadcash->d_picture )
					 <a target="_blank" href="{{url('deposit_slip/images')}}/{{ $loadcash->d_picture}}"><img src="{{url('deposit_slip/images')}}/{{ $loadcash->d_picture}}" height="60px" width="60px"></a>
					 @else {{'No Slip'}}
					 @endif
				</td>
				<td>{{ $loadcash->request_remark }}</td>
				<td>{{ $loadcash->bankref }}</td>
				<td>{{ @$loadcash->status->status }}</td>
				<td>{{ @$loadcash->remark->remark }}</td>
				<td>{{ @$loadcash->report->remark }}</td>
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
	 </div>                             

 <meta name="_token" content="{!! csrf_token() !!}"/>              
@endsection