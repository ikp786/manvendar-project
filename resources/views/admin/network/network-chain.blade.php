@extends('admin.layouts.templatetable')

@section('content')
<div class="table-responsive"  style="overflow-y: scroll; max-height:430px">
            <table class="table table-bordered table-hover" id="example2">
                    <thead style="color: black">
                        <tr>
                            <th>Name</th>
                            <th>Mobile Number</th>
							<th>Email</th>
							<th>Role</th>
							<th>Status</th>
							<th>Active</th>
                            
                            
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($users as $user)
                        <tr>
                        	<td>{{$user->name}}</td>
                        	<td>{{$user->mobile}}</td>
							<td>{{$user->email}}</td>
							<td>{{$user->role->role_title}}</td>
                        	<td>{{($user->status_id) ? "Active" : "In-active"}}</td>
							<td>
								@if(in_array(Auth::user()->role_id,array(1,3)))
								<a href="{{route('network-chain')}}/{{$user->id}}" class="btn btn-outline-secondary">View Chain</a>
								@endif
							</td>
                        	
                        </tr>
                        @endforeach
                    </tbody>
            </table>
</div>            



@endsection
