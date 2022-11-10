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
/*by vijay for flight booking*/
.search_panel{
    height: 10%;
    display: none;
}

#from{
	width:47%;
}
#children_2{
	width:190%;
}
.leftSide{
	right: 12%;
}
.check_in{
	width: 95%;
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
			        <div class="search_panel active" id="flightBookingForm">
				
						<!--   <li class="resp-tab-item" style="width:8%"><span>One way</span></li>
						<li class="resp-tab-item" style="width:10%"><span>Round Trip</span></li> -->
						<!--  <li>
                            <a href="#tab-cars1" data-toggle="tab" style="width: 10%"><i></i><span>One way</span></a>
                        </li>
                         <li>
                            <a href="#tab-tour2" data-toggle="tab" style="width: 10%"><i></i><span>Round Trip</span></a>
                        </li> -->
                       
					      
						<form action="#"  class="search_panel_content d-flex flex-lg-row flex-column align-items-lg-center align-items-start justify-content-lg-between justify-content-start">
							
				        <!-- <div class="search_item " style=" margin: -70px 0;" >
				        	 <div class="one-way col-md-4" ><div style=" margin: -85px 0;">One way</div></div>
				            <div class="round-trip col-md-4" ><div style=" margin: -85px 0;right: 50%" >Round trip</div></div>
                            
                         </div> -->
				                <div class="search_item" >
				                    <div>From</div>
				                    <input type="text"  class="check_in search_input" id="from"/>      
				                </div>
				             
								<div class="search_item leftSide">
				                  <div>To</div>
				                  <input type="text" class="check_in search_input"  />
				            	</div>  

							<div class="search_item leftSide">
								<div>Check In</div>
								<input type="text" class="check_in search_input" placeholder="MM-DD-YYYY" id="datepicker">
							</div>

							<div class="search_item " style="right:13.5%">
								<div>Check Out</div>
								<input type="text"  class="check_out search_input" placeholder="MM-DD-YYYY" id="datepicker1">
							</div>

							<div class="search_item leftSide">
								<div>adults</div>
								<select name="adults" id="adults_2" class="dropdown_item_select search_input">
									<option>01</option>
									<option>02</option>
									<option>03</option>
									<option>04</option>
									<option>05</option>
									<option>06</option>
									<option>07</option>
								</select>
							</div>

							<div class="search_item leftSide">
								<div>children</div>
								<select name="children" id="children_2" class="dropdown_item_select search_input">
									<option>0</option>
									<option>01</option>
									<option>02</option>
									<option>03</option>
									<option>04</option>
									<option>05</option>
									<option>06</option>
								</select>
							</div>
						<br><br>
							<button class="button search_button">search</button>
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



