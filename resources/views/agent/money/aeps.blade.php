@extends('layouts.app')

@section('content')
<style>
.form-control {
height:34px !important;
}
.nav-tabs>li>a
{
color:white;
}
</style>
<script>
		function aepsLogin()
		{
			
			var dc_patter = /^[0-9]+$/;
			mobileNumber = $("#mobileNumber").val();
			if(!mobileNumber.match(dc_patter))
			{
				alert("Enter Correct Mobile Number")
				return false;
			}	
			
			var dataString ='mobileNumber='+mobileNumber;
			$.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                }
            })
			 $.ajax({
                type: "get",
                url: "{{url('aeps-login')}}",
                data: dataString,
                success: function (data) {
                
					alert(data.message)
                }
            });
		}
			

</script>

<div class="super_container">

	
	
	<!-- Home -->

	<div class="home">
		
	
	</div>

	<!-- Search -->

	<div class="search">
		

		<!-- Search Contents -->
		
		<div class="">
			<div class="">
				<div class="">

                   	@include('partials.tab')
					@include('agent.money.money-type')
					<br>
			          <h1 style="margin-left: 40%;color:white">Comming Soon........</h1>
						
			      <!--  <div class="col-md-3" style="margin-left:80px;">
			        	
			        	
						<div class="form-group">
						  <label class="label" style="color:black">AEPS Number</label>
			              <input class="form-control" type="text"  name="mobileNumber"  id="mobileNumber"  placeholder="Enter Mobile Number" >
						</div>
						<button style="width:25%;"  type="button" onClick="aepsLogin()" class="btn btn-success">Submit</button>
					</div>	-->
					
				</div>
			</div>
		</div>		
	</div>


	@include('layouts.footer')
 <meta name="_token" content="{!! csrf_token() !!}"/>
@endsection
