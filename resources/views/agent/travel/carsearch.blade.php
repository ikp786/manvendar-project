@extends('layouts.app')

@section('content')
<div class="super_container">

	
	
	<!-- Home -->

	<div class="home" style="background: #8d4fff;">
		
	
	</div>

	<!-- Search -->

	<div class="search" style="height: auto">
		

		<!-- Search Contents -->
		
		<div class="container fill_height">
			<div class="row fill_height">
				<div class="col fill_height">
						@include('partials.tab')
						<br>
						
 					<div class="card">
			                <table class="table is-narrow ">
			                    <thead>
				                    <tr>
				                        <th>Type Of Car</th>
				                        <th>Sitting Capacity</th>
				                        <th colspan="2">Price</th>
				                      
				                    </tr>
			                    </thead>
			                    <tbody>
                                  
                                    	
				                            <tr> 
				                            <td><img src="cars/compact1.jpg" <h4>&nbsp;&nbsp;Budget</h4><br>Swift Desire or Equivalent</td>
				                            <td>4+ driver</td>
				                            <td>1200</td>
				                            <td><a href="{{route('travel-carbooking')}}"><button>Book Now</button></a></td>
				                            </tr>
				                            <tr>	
                                            <td><img src="cars/mini_car1.jpg" <h4>&nbsp;&nbsp;Family</h4><br>Innova or Equivalent</td>
				                            <td>4+ driver</td>
				                            <td>1100</td>
				                            <td><a href="{{route('travel-carbooking')}}"><button>Book Now</button></a></td>

				                            </tr> 
				                            <tr>	
                                            <td><img src="cars/luxury_car.jpg" "<h4>&nbsp;&nbsp;Luxury Car</h4><br>E Class or Equivalent</td>
				                            <td>4+ driver</td>
				                            <td>1800</td>
				                            <td><a href="{{route('travel-carbooking')}}"><button>Book Now</button></a></td>

				                            </tr> 
				                             <tr>	
                                            <td><img src="cars/suv.jpg" <h4>&nbsp;&nbsp;Business</h4><br>Corolla or Equivalent</td>
				                            <td>4+ driver</td>
				                            <td>2200</td>
				                           <td><a href="{{route('travel-carbooking')}}"><button>Book Now</button></a></td>

				                            </tr> 
				                             <tr>	
                                            <td><img src="cars/economy_car1.jpg" <h4>&nbsp;&nbsp;Premium</h4><br>Accord or Equivalent</td>
				                            <td>4+ driver</td>
				                            <td>2500</td>
				                            <td><a href="{{route('travel-carbooking')}}"><button>Book Now</button></a></td>

				                            </tr> 
				                            <tr>	
                                            <td><img src="cars/standerd_car1.jpg" <h4>&nbsp;&nbsp;Standard</h4><br>City or Equivalent</td>
				                            <td>4+ driver</td>
				                            <td>2800</td>
				                           <td><a href="{{route('travel-carbooking')}}"><button>Book Now</button></a></td>

				                            </tr> 
				                         
				                         
				                          
				                </tbody>
				            </table> 
				                           
								
					</div>
					


				</div>
			</div>
		</div>
	</div>				

 <meta name="_token" content="{!! csrf_token() !!}"/>
@endsection



