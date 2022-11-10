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
.search_panel{
    height: 10%;
    display: none;
}

#from{
	width:30%;
}
.leftSide{
	right: 12%;
}
.check_in{
	width: 55%;
}
.check_out{
	width: 128%;
	padding-right: 20px;
}

</style>

  <script src="https://code.jquery.com/jquery-1.12.4.js"></script> 
   <script>
 

  $(document).ready(function(){
    $("#datepicker").datepicker({

        minDate: 0,

        maxDate: "+60D",

        numberOfMonths: 2,

        onSelect: function(selected) {

          $("#datepicker1").datepicker("option","minDate", selected)

        }

    });

    $("#datepicker1").datepicker({

        minDate: 0,

        maxDate:"+60D",

        numberOfMonths: 2,

        onSelect: function(selected) {

           $("#datepicker").datepicker("option","maxDate", selected)

        }

    }); 

});

  </script>

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

					<!-- Search Tabs -->
                   	@include('partials.tab')
					@include('agent.travel.travel-type')
					
					<br>
			        <div class="search_panel active">
					      
						<form action="#"  class="search_panel_content d-flex flex-lg-row flex-column align-items-lg-center align-items-start justify-content-lg-between justify-content-start">
							
				                <div class="search_item" >
				                    <div>From</div>
				                    <select class="check_in search_input" required="">
										<option value="">Ahmedabad (AMD)</option>
										<option value="">Amritsar (ATQ)</option>
										<option value="">Bengaluru (BLR)</option>
										<option value="">Bhubaneswar (BBI)</option>
										<option value="">Chandigarh (IXC)</option>
										<option value="">Chennai (MAA)</option>
										<option value="">Coimbatore (CJB)</option>
										<option value="">Delhi (DEL)</option>
										<option value="">Goa (GOI)</option>
										<option value="">Gurugram (DEL)</option>
										<option value="">Hyderabad (HYD)</option>
										<option value="">Mangalore (IXE)</option>
										<option value="">Mumbai (BOM)</option>
										<option value="">Mysore (MYQ)</option>
										
										<option value="">Visakhapatnam (VTZ)</option>
									</select>

				                </div>
				             
								<div class="search_item leftSide">
				                  <div>To</div>
				                 	<select class="check_out search_input" required="">
										<option value="">Ahmedabad (AMD)</option>
										<option value="">Amritsar (ATQ)</option>
										<option value="">Bengaluru (BLR)</option>
										<option value="">Bhubaneswar (BBI)</option>
										<option value="">Chandigarh (IXC)</option>
										<option value="">Chennai (MAA)</option>
										<option value="">Coimbatore (CJB)</option>
										<option value="">Delhi (DEL)</option>
										<option value="">Goa (GOI)</option>
										<option value="">Gurugram (DEL)</option>
										<option value="">Hyderabad (HYD)</option>
										<option value="">Mangalore (IXE)</option>
										<option value="">Mumbai (BOM)</option>
										<option value="">Mysore (MYQ)</option>
										
										<option value="">Visakhapatnam (VTZ)</option>
									</select>
				            	</div>  

							<div class="search_item leftSide">
								<div>Travel Date</div>
								<input type="text" class=" search_input" placeholder="MM-DD-YYYY" id="datepicker" required="">
							</div>


							<div class="search_item " style="right:13.5%">
								<div>Return Date</div>
								<input type="text"  class="check_out search_input" placeholder="MM-DD-YYYY" id="datepicker1" required="">
							</div>

							
							<button class="button search_button"><a href="{{route('travel-carsearch')}}">search</a></button>
						</form>
					</div>

				</div>
			</div>
		</div>		
	</div>
	
@include('layouts.footer')

</div>

 <meta name="_token" content="{!! csrf_token() !!}"/>
@endsection



