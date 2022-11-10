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

	<div class="search" >
		

		<!-- Search Contents -->
		
		<div class="container fill_height">
			<div class="row fill_height">
				<div class="col fill_height">

					<!-- Search Tabs -->
		               @include('partials.tab')
		               @include('agent.recharge.recharge-type')
						
					  <br>

					 <div class="col-md-4" >
							<!-- Recharge Panel -->
						<div class="form-group">
							<label class="label" style="color:black">Operator</label>
							
							<select class="form-control" id="mobile_provider" style="height:45px !important;">
								<option value="2">----Select----</option>
								@foreach($provider as $providers)
								<option value="{{$providers->id}}">{{$providers->provider_name}}</option>
								@endforeach
							
							</select>
						</div>
						<div class="form-group">
						  <label class="label" style="color:black">Mobile Number</label>
			              <input class="form-control" type="text" value="" name="number" id="mobile_number" placeholder="Enter Valid Number" maxlength="10">
						</div>
						<div class="form-group">
			              <label class="label" style="color:black">Amount</label>
			              <input type="number" value="" class="form-control" name="amount" id="mobile_amount" placeholder="Enter valid Amount">
						 </div>
						
						 <button style="width:25%;" onclick="this.disabled=true;recharge_pay();" type="button" class="btn btn-success">Submit</button>
						 
					</div>
						
					
					
				</div>
			</div>
		</div>		
	</div>
	<br>

	@include('layouts.footer')

 <meta name="_token" content="{!! csrf_token() !!}"/>
@endsection
