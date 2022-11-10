{{--   <ul class="nav nav-tabs" >
				@if(Auth::user()->role_id == 1)
					<li  class="{{(Request::is('md-fund-transfer') ? 'active' : '')}}"><a href="{{Route('md-fund-transfer')}}" ><span>MD Fund Transfer</span></a></li>
					<li  class="{{(Request::is('dist-fund-transfer') ? 'active' : '')}}"><a href="{{Route('dist-fund-transfer')}}" ><span>Dist Fund Transfer</span></a></li>
					<li  class="{{(Request::is('fund-transfer') ? 'active' : '')}}"><a href="{{Route('fund-transfer')}}"><span>Retailer Fund Transfer</span></a></li>
					<li  class="{{(Request::is('dist-fund-return') ? 'active' : '')}}"><a href="{{Route('dist-fund-return')}}"><span>Dist Fund Return</span></a></li>
					<li class="{{(Request::is('fund-return') ? 'active' : '')}}"><a href="{{Route('fund-return')}}" ><span>Retailer Fund Return</span></a></li>
					
				@elseif(Auth::user()->role_id == 4)
					<li class="{{(Request::is('fund-return') ? 'active' : '')}}"><a href="{{url('fund-transfer')}}"><span>Retailer Fund Transfer</span></a></li>
				@endif
              
               @if(Auth::id()==1)
               <li class="{{(Request::is('admin/purchase-balance') ? 'active' : '')}}"><a href="{{url('admin/purchase-balance')}}"><span>Purchase Balance</span></a></li>
               @endif
               <li class="{{(Request::is('payment-request-view') ? 'active' : '')}}"><a href="{{url('payment-request-view')}}"><span>Request View</span></a></li> 
               <li class="{{(Request::is('fund-request-report') ? 'active' : '')}}"><a href="{{ url('fund-request-report') }}"><span>Request Report</span></a></li> 
               <li class="{{(Request::is('payment-report') ? 'active' : '')}}"><a href="{{url('payment-report')}}"><span>Payment Report</span></a></li>
				 @if(Auth::user()->role_id == 4)
				<li class="{{(Request::is('payment-load') ? 'active' : '')}}"><a href="{{url('payment-load')}}"><span>Payment Load</span></a></li>
				@endif
</ul>--}}