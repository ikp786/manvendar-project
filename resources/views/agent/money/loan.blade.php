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
						
			       <!-- <form action="{{route('moneyloanstore')}}" method="post" enctype="multipart/form-data">
			        	 {{ csrf_field() }} 
				        <div class="col-md-3" style="margin-left:80px;">
						@include('partials.message_error')
				        	<div class="form-group">
							  <label class="label" style="color:black">Company Name</label>
							 
							
							   {{ Form::select('loanEmiList', $loanEmiList, old('loanEmiList'), array('class' => 'form-control','id' => 'loan_emi_payment')) }}
							</div>
							<div class="form-group">
							  <label class="label" style="color:black">Customer Mobile No</label>
				              <input class="form-control" type="text"  name="customer_mobile_no"  value="{{old('customer_mobile_no')}}" placeholder="Customer Mobile Number">
							</div>
							<div class="form-group">
							  <label class="label" style="color:black">Loan Account Number</label>
				              <input class="form-control" type="text"  name="loan_acc_no"  value="{{old('loan_acc_no')}}" placeholder="loan account number">
							</div>
							
				        	
							<div class="form-group">
							  <label class="label" style="color:black">Loan Amount</label>
				              <input class="form-control" type="text"  name="loan_amount" value="{{old('loan_amount')}}" placeholder="loan amount">
							</div>
							<button style="width:25%;"  type="submit" class="btn btn-success" name="submit" value="Submit">Submit</button>
						</div>
					</form>	-->
					
				</div>
			</div>
		</div>		
	</div>


	@include('layouts.footer')

</div>

 <meta name="_token" content="{!! csrf_token() !!}"/>
@endsection
