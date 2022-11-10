<ul class="nav nav-tabs" >
				@if(Auth::user()->role_id == 1)
					<li  class="{{(Request::is('md-payment') ? 'active' : '')}}"><a href="{{url('md-payment')}}" ><span>ADM To MD</span></a></li>
					<li  class="{{(Request::is('dist-payment') ? 'active' : '')}}"><a href="{{url('dist-payment')}}" ><span>ADM To Dist</span></a></li>
					<li  class="{{(Request::is('retailer-payment') ? 'active' : '')}}"><a href="{{url('retailer-payment')}}"><span>ADM To Retailer</span></a></li>
					<li  class="{{(Request::is('api-payment') ? 'active' : '')}}"><a href="{{url('api-payment')}}"><span>ADM To ApiUser</span></a></li>
				@elseif(Auth::user()->role_id == 3)
					<li  class="{{(Request::is('dist-payment') ? 'active' : '')}}"><a href="{{url('dist-payment')}}" ><span>ADM To Dist</span></a></li>
					<li class="{{(Request::is('retailer-payment') ? 'active' : '')}}"><a href="{{url('retailer-payment')}}"><span>Retailer Fund Transfer</span></a></li>
				@elseif(Auth::user()->role_id == 4)
					<li class="{{(Request::is('fund-return') ? 'active' : '')}}"><a href="{{url('retailer-payment')}}"><span>Retailer Fund Transfer</span></a></li>
				@endif
				<!--<li class="{{(Request::is('payment-statement') ? 'active' : '')}}"><a href="{{url('payment-statement')}}"><span>Payment Statement</span></a></li>-->
</ul>