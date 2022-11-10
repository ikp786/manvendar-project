@extends('admin.layouts.templatetable')

@section('content')
    <script>
        function flush_record() {
			if(confirm("Are you want to flush OTP for Distributor and Retailer"))
			{
            var dataString = 'case=otp_flush';
			$.ajaxSetup({
					headers: {
					'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
					}
				})
             $.ajax({
                type: "POST",
                url: "{{url('flush_otp')}}",
                data: dataString,
                success: function (data) {
                    alert(data);
                }
            })
		}
        }
    </script>

    <!-- Page-Title -->
    
        <div class="col-md-12">
            <div class="col-md-6">
                <h3 class="page-title" style="color:black; ">{{'Logged In Users' }}</h3>
               
            </div>
            <div class="col-md-6 pull-right">
                <div class="pull-right">
                    <button onclick="flush_record()" id="demo-add-row" class="btn btn-success">Flush OTP
                    </button>
				</div>
            </div>
        </div>
    

    <!--Basic Columns-->
    <!--===================================================-->


    <!--===================================================-->
   
             <div class="">
        
                <table id="mytable" class="table table-bordered hover">
                    <thead>
                    <tr style="color:#115798;">
                        <th>ID</th>
                        <th>Member Name</th>
                        <th>Mobile</th>
						<th>Email</th>
                        <th>Member Otp</th>
                        <th>Total Logins</th>
                        <th>Last Login Time</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($users_otp as $user)
                        <tr>
                            <td>@if($user->role_id == 4){{ "D ".$user->id }}@elseif($user->role_id==5) {{ "R ".$user->id }}@elseif($user->role_id == 1) {{ "A ".$user->id }}@elseif($user->role_id==3) {{ "M ".$user->id }} @endif</td>
                            <td>{{ $user->name }}</td>
                            <td>{{ $user->mobile }}</td>
                            <td>{{ $user->email }}</td>
                            <td>{{ $user->otp_number }}</td>
                            <td>{{ $user->total_logins }}</td>
                            <td>{{ $user->updated_at }}</td>
							
                           
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
     
    
    <meta name="_token" content="{!! csrf_token() !!}"/>
@endsection