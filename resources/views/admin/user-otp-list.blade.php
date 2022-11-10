@extends('admin.layouts.templatetable')
@section('content')
<div  class="" style="overflow-y: scroll; max-height:500px">
<div class="box" >
<input id="myInput" type="text" placeholder="Search.." class="pull-right">
	<table class="table table-bordered" id="example2">
		<thead>
		  <tr>
			<th>ID</th>
			<th>Name</th>
			<th>Mobile</th>
			<th>Email</th>
			<th>Member Type</th>
			<th>Login Count</th>
			<th>OTP</th>
			<th>Last Login Ip</th>
		 </tr>
		</thead>
		<tbody id="myTable">
			@foreach($usersOtp as $user)
			<tr>
				<td>{{ $user->prefix}} {{$user->id }} 
				<br><span style="color:darkgoldenrod">{{$user->member->company}}</span></td>
				<td>{{$user->name}}</td>
				<td>{{$user->mobile}}</td>
				<td>{{$user->email}}</td>
				<td>{{@$user->role->role_title}}</td>
				<td>{{$user->total_logins}}</td>
				<td>{{$user->otp_number}}</td>
				<td>{{$user->last_login_ip}}</td>
				
			</tr>
			@endforeach
		</tbody>
	</table>
	
</div>
</div>
@endsection
