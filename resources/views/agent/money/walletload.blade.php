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
						
			        <!--<div class="col-md-3" style="margin-left:80px;">
			        	
						<div class="form-group">
						  <label class="label" style="color:black">Wallet Amount</label>
			              <input class="form-control" type="text"  name="wallet_amount"  placeholder="Enter wallet amount ">
						</div>
						<button style="width:25%;"  type="button" class="btn btn-success">Submit</button>
					</div>	-->
					
				</div>
			</div>
		</div>		
	</div>

	@include('layouts.footer')

</div>

 <meta name="_token" content="{!! csrf_token() !!}"/>
@endsection
