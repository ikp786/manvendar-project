@extends('admin.layouts.templatetable')

@section('content')
@if (in_array(Auth::user()->role_id,array(1,3,4)))
<div class="col-md-8">

	<form method="get" action="{{ Request::url() }}"  class="form-inline col-md-12">
		<div class="form-group row">
			<input type="text" name="mobile" placeholder="Mobile Number" class='form-control'/>
			<button name="export" value="SEARCH" type="submit" class="btn btn-primary btn-md"></span>Search</button>
			<a href="{{ Request::url() }}" class="btn btn-info btn-md">Reset</a>
		</div> 
		@if(Auth::user()->role_id == 3)
		<div class="form-group row">
			{{ Form::select('searchOf', ['4' => 'Distributor', '5' => 'Retailer'], null, ['class'=>'form-control','placeholder'=>"--Select--"]) }}
			@elseif(Auth::user()->role_id==1)
			{{ Form::select('searchOf', ['3' => 'Master Dist','4' => 'Distributor', '5' => 'Retailer'], null, ['class'=>'form-control','placeholder'=>"--Select--"]) }}
			@else
			<input type="hidden" name="searchOf" value="5"/>
		</div>
		@endif
		
	</form>
</div>
@endif
<div class="table-responsive"  style="overflow-y: scroll; max-height:900px">
            <table class="table table-bordered table-hover" id="example2">
                    <thead style="color: black">
                        <tr>
                            <th>Name</th>
                            <th>Mobile Number</th>
							<th>Email</th>
							<th>Status</th>
							<th>Role</th>
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
