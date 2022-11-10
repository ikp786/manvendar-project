@extends('admin.layouts.templatetable')

@section('content')
<style>
.bootstrap-table{
    margin-top: -28px;
}
</style>
<script>
function customSearch()
		{
			var content = $("#customeSearch").val();
			var searchType = $("#searchType").val();
			$.ajax({
					type: "get",
					url: "{{url('login-custom-serach')}}",
					data: "content="+content +"&searchType="+searchType+"&urlName="+urlName,
					beforeSend:function(){
						$("#totalCount").html('')
					},
					success: function (result) {
					var content='';
					if(result.totalCount >0){
						data = result.data;
						$("#totalCount").html("TOTAL USER :"+result.totalCount)
						for (var key in data) 
						{
							content += "<tr>"
							
							content += "<td>"+data[key].id+"</td>";
							content += "<td>"+data[key].created_at+"</td>";
							content += "<td>"+data[key].userDetails+"</td>";
							content += "<td>"+data[key].ip_address+"</td>";
							content += "<td>"+data[key].browser+"</td>";
							content += "<td>"+data[key].latitude+"</td>";
							content += "<td>"+data[key].longitude+"</td>";
							content += "<td>"+data[key].country_name+"</td>";
							content += "<td>"+data[key].city+"</td>";
						}
					}else content="<div style='color:red'>No Record Found</div>";
						$('#memberTbody').html(content);
					
					}
				});
		}
		 $(document).ready(function () {
        $.noConflict();
        $('.customDatepicker').datepicker({
              autoclose: true,  
            format: "dd-mm-yyyy"
        });

    }); 

		</script>
<!-- for popup -->
  
    
        <div class="col-sm-12">
            <div class="col-lg-6 col-md-6">
                <h4 class="page-title" style="color: black; ">{{'Logged In History' }}</h4>
                
            </div>
            
        </div>
		
		

        <div class="">
		<form method="get" action="{{ Request::url()}}" onSubmit="return checkValidation();" class="form-inline">
							<div class="form-group">
								 {{ Form::select('user_id', $userLists,  app('request')->input('user_id') , array('class' => 'form-control','id' => 'user_id')) }}
								 
                        <input name="fromdate" class="form-control customDatepicker" type="text" value="{{ (app('request')->input('fromdate')) ? app('request')->input('fromdate') : date('d-m-Y')}}">
                   
                        <input name="todate" class="form-control customDatepicker" type="text" value="{{ (app('request')->input('todate')) ? app('request')->input('todate') : date('d-m-Y')}}">
                    
									<button name="export" value="SEARCH" type="submit" class="btn btn-success btn-md">Search
									</button>
									<button name="export" value="EXPORT" type="submit" class="btn btn-basic btn-md">Export
									</button>
									<a href="{{Request::url()}}" class="btn btn-primary btn-md">Reset
									</a>
								</div>
							
						</form>
		    <table  class="table table-striped table-bordered" id="example2">
		        <thead>
                    <tr>
					  <th>ID</th>
						<th align="center">Date/Time</th>
						<th>User</th>
						<th>Ip Address</th>
						<th>Browser Details</th>
						<th>Latitude</th>
						<th>Longitude</th>
						
						<th>Country Name</th>
						<th>Region Name</th>
						<th>City</th>
						<th>At Map</th>
						
                    </tr>
                </thead>

                <tbody>
                    @foreach($historyDetails as $key => $value)
					<?php $s = $value->created_at;
						$dt = new DateTime($s);?>
						<tr>
							<td>{{ $value->id }}</td>
							<td>{{ $value->created_at }}</td>
							<td>{{ @$value->user->name}} ({{$value->user_id}}</td>
							<td>{{ @$value->ip_address }} </td>
							<td>{{ $value->browser }}</td>
							<td>{{ $value->latitude }}</td>
							<td>{{ $value->longitude }}</td>
							<td>{{ $value->country_name }}</td>
							<td>{{ $value->region_name }}</td>
							<td>{{ $value->city }}</td>
							<td><a href="https://www.google.co.in/maps/place/{{ $value->latitude }},{{ $value->longitude }}">show</a></td>
						</tr>
                    @endforeach
                </tbody>
            </table>
                {{$historyDetails->appends(\Input::except('page'))->render() }} 
        </div>

    <meta name="_token" content="{!! csrf_token() !!}"/>
@endsection