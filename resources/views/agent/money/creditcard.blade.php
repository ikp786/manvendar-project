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
		
		<div class="container fill_height">
			<div class="row fill_height">
				<div class="col fill_height">

                   	@include('partials.tab')
					@include('agent.money.money-type')
					<br>
			        
			        <div class="col-md-4" >
			        	
			        	
						<div class="form-group">
						  <label class="label" style="color:black">Credit Card Number</label>
			              <input class="form-control" type="text" name="number"  placeholder="Enter credit Card Number">
						</div>
						<button style="width:25%;"  type="button" class="btn btn-success">Submit</button>
					</div>	
					
				</div>
			</div>
		</div>		
	</div>

	@include('layouts.footer')

</div>

 <meta name="_token" content="{!! csrf_token() !!}"/>
@endsection
