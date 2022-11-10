@extends('admin.layouts.templatetable')
@section('content')

    <script type="text/javascript">
	
	 
	
		
    </script>

					  
	<form method="get" action="{{ Request::url() }}" onSubmit="return checkValidation();" class="form-inline">
		<div class="form-group">
			{{ Form::select('searchType', ['MOB' => 'Mobile','AgentId'=>'AgentId'], app('request')->input('searchType'), ['class'=>'form-control','id'=>'searchType','placeholder'=>'--Select Search Creteria--','required'=>'required']) }}
		</div>
		<div class="form-group">
			<input type="text" id="content" value="{{ app('request')->input('content') }}" class="form-control" placeholder="Search Here" name="content" required>
		</div>
		<div class="form-group">
			<button type="submit" value="Submit" class="btn btn-primary">Submit</button>
		</div>
		<div class="form-group">
			<a href="{{ Request::url()}}" class="btn btn-info  btn-md">
				<i class="fa fa-refresh"></i>
			</a>  
		</div>
	</form>
	<div style="overflow-y: scroll; max-height:430px">
        <h3 id="totalCount" style="text-align: center;font-family: time;"></h3>
            <table id="mytable" class="table table-bordered ">
                <thead>
                    <tr>
						<th>Date/Time </th>
						<th>ID </th>
                        <th>Parent Name</th>
                        <th>Agent id</th>
                        <th>Agent Name </th>
                        <th>Mobile</th>                      
                        <th>Pan Number</th>
                        <th>Aadhaar Number</th>
                        <th>Merchange Login Id </th>
                        <th>Merchange Login Pin </th>
                        <th>Status </th>
					</tr>
                </thead>
                    <tbody id="memberTbody">
					
                    @foreach($apiAepsUser as $user)
						
                        <tr>
							<td>{{ date("d-m-Y",strtotime($user->created_at))}}</td>
							<td>{{ $user->id }}</td>
							<td>{{ @$user->user->name }}</td>
							<td>{{ @$user->agent_id }}</td>
							<td>{{ @$user->agent_name }}</td>
							<td>{{ @$user->mobile }}</td>
							<td>{{ @$user->pan_number }}</td>
							<td>{{ @$user->aadhaar_number }}</td>
							<td>{{ @$user->merchant_login_id }}</td>
							<td>{{ @$user->merchant_login_pin }}</td>
							<td>{{ ($user->status_id==1) ? "Active" :"Pending" }}</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
                {{$apiAepsUser->links()}} 
	</div>

@endsection
