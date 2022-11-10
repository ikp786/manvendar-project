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


	<div class="home">
		
	
	</div>

	<!-- Search -->

	<div class="search">
		

		<!-- Search Contents -->
		
		<div class="">
			<div class="">
				<div class="">

					<!-- Search Tabs -->
                   	@include('partials.tab')
					@include('agent.bbps.bbps-type')
				
					<br>
			         <h1 style="margin-left: 40%;color:white">Comming Soon........</h1>
						
					<!--<div class="col-md-3" style="margin-left:80px;">
			        		<div class="form-group">
								<label class="label" style="color:black">Operator</label>
								 <input value="" class="form-control" name="amount" placeholder="Enter valid Amount">
								
		         			</div>

		         			<div class="form-group">
								<label class="label" style="color:black">Circle</label>
								 <select id="circle_id"class="form-control" name="circle_id">
									<option value=""> -- Select Circle --</option>
									
								</select>
									
					         </div>
					         <div class="form-group">
								<label class="label" style="color:black">CA Number</label>
								  <input value="" class="form-control" name="amount" placeholder="Enter valid Amount">
								
		         			</div>
							<div class="form-group">
								<label class="label" style="color:black">Amount</label>
								  <input value="" class="form-control" name="amount" placeholder="Enter valid Amount">
								
		         			</div>
		         			<div class="form-group col-md-12" >
							 <button type="button" class="btn btn-success">Submit</button>
							
							 
							</div>
			        </div>-->	
					<div class="col-md-6" style="margin-left: 183px" id="Sidelogo">
                    

                            <div id="carousel-example-generic" class="carousel slide" data-ride="carousel">
                                <div class="carousel-inner">
                                    <div class="item active" data-interval="20000">
                                         <img src="newlog/images/IMAG.jpg" style="height:470px;width:600px">
                                    </div>
                                    <div class="item" data-interval="20000">
                                        <img src="newlog/images/IMAG.jpg" style="height:470px;width:600px">
                                    </div>
                                                            
                                </div>
                                <a class="left carousel-control" href="#carousel-example-generic" data-slide="prev">
                                    <span class="fa fa-angle-left"></span>
                                </a>
                                <a class="right carousel-control" href="#carousel-example-generic" data-slide="next" style="margin-right: 10px">
                                    <span class="fa fa-angle-right"></span>
                                </a>
                            </div>
                               
                    </div>  
					
				</div>
			</div>
		</div>		
	</div>
	
	@include('layouts.footer')

</div>

 <meta name="_token" content="{!! csrf_token() !!}"/>
@endsection
