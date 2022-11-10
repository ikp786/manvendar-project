 <ul class="nav nav-tabs" id="TravelTab" style="margin-left:58px">
                        <li class="{{Request::is('*travel') ? 'active' : ''}}">
                           <a href="{{route('agent.travel')}}"><i class="fa fa-plane"></i><span>&nbsp; Flight</span></a>
                        </li>

                        <li class="{{Request::is('*travel-hotels') ? 'active' : ''}}">
                            <a href="{{route('travel-hotels')}}"><i class="fa fa-hotel"></i><span>&nbsp; Hotels</span></a>
                        </li>

                         <li class="{{Request::is('*travel-cars') ? 'active' : ''}}">
                            <a href="{{route('travel-cars')}}"><i class="fa fa-car"></i><span>&nbsp; Cars</span></a>
                        </li>

                        <li class="{{Request::is('*travel-cruises') ? 'active' : ''}}">
                            <a href="{{route('travel-cruises')}}"><i class="fa fa-ship" aria-hidden="true"></i><span>&nbsp; Cruises</span></a>
                        </li>

						<li class="{{Request::is('*travel-homestay') ? 'active' : ''}}">
                            <a href="{{route('travel-homestay')}}"><i class="fa fa-bed"></i><span>&nbsp; Home Stay</span></a>
                        </li>

						<li class="{{Request::is('*travel-honeymoon') ? 'active' : ''}}">
                            <a href="{{route('travel-honeymoon')}}"><i class="fa fa-heart"></i><span>&nbsp; Honey Moon</span></a>
                        </li>
						
                         
                   </ul> 