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
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js "></script>
<script type="text/javascript">
	$("#input2,#input1").keyup(function () {

   var v = $('#output').val($('#input1').val() * $('#input2').val());
console.log(v);
});
</script>
<div class="super_container">
	<div class="home">
		
	
	</div>

	<div class="search">
	
		<div class="">
			<div class="">
				<div class="">

                   @include('partials.tab')
                   @include('agent.pancard.pancard-type')
                   <br><br>
				   	<form method="post" action="{{(route('pancard_store'))}}" enctype="multipart/form-data">
						{{ csrf_field() }}
                 	<div class="col-md-3" style="margin-left:80px;">
					<!-- Recharge Panel -->
        				<div class="form-group">
        					<label class="label" style="color:black">Total Card</label>
        						<input type="text" class="form-control" name="total_card" id="input1" value="" placeholder="Enter no of Card">
        					
        		         </div>
        				<div class="form-group">
        				  <label class="label" style="color:black">Cost Of Card</label>
                          <input type="text" class="form-control" name="cost_of_card" id="input2" value="107" readonly> 
        				</div>
        				<div class="form-group">
                          <label class="label" style="color:black">Total Amount</label>
                          <input type="text" class="form-control" name="total_amount" id="output" readonly>
        				</div>
        				
        				 <button id="recharge_button" style="width:25%;" type="submit" class="btn btn-success">Submit</button>
				 
			         </div>
					 </form

				</div>
			</div>
		</div>		
	</div>
	
	

</div>
@include('layouts.footer')
 <meta name="_token" content="{!! csrf_token() !!}"/>
@endsection
