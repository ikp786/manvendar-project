@extends('layouts.app')

@section('content')

@include('partials.tab')

@include('agent.fund.fund-type')
<script>
/*for date->Calender*/

    $(document).ready(function () {
        $.noConflict();
        $('.customDatepicker').datepicker({
              autoclose: true,  
            format: "dd-mm-yyyy"
        });

    }); 
</script>
  <br>
    <div class="panel panel-default">
		<div class="panel-body">
			<div class="col-lg-3 col-md-3">
				<h4 class="page-title" style="color: black; ">{{'Fund Request Summary' }}</h4>
			</div>
			<div class="col-lg-9 col-md-9">
				<form method="get" action="{{ Request::url() }}" class="form-inline">
					<div class="form-group">
						<input name="fromdate" class="form-control customDatepicker" type="text" value="{{ (app('request')->input('fromdate')) ? app('request')->input('fromdate') : date('d-m-Y')}}">
					</div>
					<div class="form-group">
						<input name="todate" class="form-control customDatepicker" type="text" value="{{ (app('request')->input('todate')) ? app('request')->input('todate') : date('d-m-Y')}}">
					</div>
					<button type="submit" value="SEARCH" name="export" class="btn btn-primary  btn-md"><span class="glyphicon glyphicon-find"></span><i class="fa fa-search"></i>
					</button> 
					<button name="export" value="EXPORT" type="submit" class="btn btn-basic btn-md"><i class="fa fa-file-excel-o" aria-hidden="true"></i></button>
					<a href="{{ Request::url() }}"/ class="btn btn-info  btn-md"><i class="fa fa-refresh"></i>
					</a>
				</form>
			</div>
		</div>
	</div>
		
	<meta name="viewport" content="width=device-width, initial-scale=1">
<style>
table {
  border-collapse: collapse;
  border-spacing: 0;
  width: 100%;
  border: 1px solid #ddd;
}

th, td {
  text-align: left;
  padding: 8px;
}

tr:nth-child(even){background-color: #f2f2f2}
</style>
				<table id="example2"  class="table table-bordered" >
					<thead>	
						<tr>
							<th>Date/Time</th>
							<th>ID</th>
							<th>User</th>
							<th>Deposite Date</th>
							<th>Bank Name</th>
							<th>Wallet Amount</th>
							<th>Request For</th>
							<th>Bank Ref</th>
							<th>Payment Mode</th>
							<th>Branch Name</th>
							<th>Request remark</th>
							<th>Approval remark</th>
							<th>Updated remark</th>
							<th>Status</th>

						</tr>
                    </thead>
                    	@foreach($reports as $value)
                        <?php $s = $value->created_at;
						$dt = new DateTime($s);?>
						<tr style="background-color:white">
						  <td>{{ $dt->format('d-m-y')}}<br>{{ $dt->format('H:i:s') }}</td>
                        	<td>{{$value->id}}</td>
							<td>{{@$value->user->name }}</td>
							<td>{{@$value->deposit_date }}</td>
							<td>
								@if(in_array($value->request_to,array(2)))
									{{@$value->netbank->bank_name}} : {{@$value->netbank->account_number}}

								@else
									{{$value->bank_name}}
								@endif
							</td>
						
							<td>{{ $value->amount }}</td>
							<td>{{ ($value->request_to == 3 && $value->borrow_type == 1)? "Take Borrow" :(($value->request_to == 3 && $value->borrow_type == 2)? "Pay Off":'') }}</td>
							<td>{{$value->bankref}}</td>
							<td>{{$value->payment_mode}}</td>
							<td>{{$value->loc_batch_code}}</td>
							<td>{{$value->request_remark}}</td>
							<td>{{@$value->remark->remark}}</td>
							<td>{{@$value->report->remark}}</td>
							<td>{{@$value->status->status }}</td>
							
						</tr>
                    @endforeach
                   
                </table>
              {!! $reports->appends(Request::all())->links() !!}
           
   <meta name="_token" content="{!! csrf_token() !!}"/> 
@endsection