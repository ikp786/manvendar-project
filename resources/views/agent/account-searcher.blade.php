@extends('layouts.app')
@section('content')
@include('agent.money.money-type')
	<div class="card-body">
		{!! Form::open(array('url' =>'account/search','id'=>'account-searcher','class'=>'form-inline')) !!}
		<div class="card-body">
			<div class="form-inline col-md-12">
			   <label for="inputTask" class="control-label ">Account Number<span style="color:red"> *</span></label>
				<input type="text" class="form-control has-error" id="accountNumber" name="accountNumber" placeholder="Enter Account Number" value="{{ (app('request')->input('accountNumber')) ? app('request')->input('accountNumber') : ''}}">
				<button  type="submit" class="btn btn-info" id="btn-save" value="add"><i class="fa fa-search"></i></button>
			</div>
		</div>
		{!! Form::close() !!}
		<table id="mytable" class="table table-bordered ">
			<thead>
				<tr>
					<th>Account Number </th>
					<th>Bene Name</th>
					<th>IFSC CODE</th>
					<th>Mobile Number </th>
					<th>Wallet </th>                       
				</tr>
			</thead>
			<tbody id="memberTbody">
				@forelse($accountDetails as $accountdetail)
				<tr>
					<td>{{$accountdetail->account_number}}</td>
					<td>{{$accountdetail->name}}</td>
					<td>{{$accountdetail->ifsc}}</td>
					<td>{{$accountdetail->mobile_number}}</td>
					<td>{{($accountdetail->api_id==4) ? "DMT1" :(($accountdetail->api_id==5) ? "A2Z Wallet" :'')}}
					</td>						
				</tr>
				@empty
					No Records
				@endforelse
			</tbody>	
		</table>
	</div>
@endsection
