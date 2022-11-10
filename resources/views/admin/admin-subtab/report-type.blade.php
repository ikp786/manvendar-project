{{--    <ul class="nav nav-tabs" >
               <li class="{{(Request::is('all-transaction-reports') ? 'active' : '')}}"><a href="{{ route('all-transaction-reports') }}"><span>Transaction Report</span></a></li>
               <li class="{{(Request::is('dmt-reports') ? 'active' : '')}}"><a href="{{ route('dmt-reports') }}"><span>DMT Reports</span></a></li> 
			   @if(Auth::user()->role_id==4)
				<li class="{{(Request::is('account-summary') ? 'active' : '')}}"><a href="{{ route('account-summary') }}"><span>Summary Report</span></a></li>
				@endif
               <li class="{{(Request::is('recharge-transaction-reports') ? 'active' : '')}}"><a href="{{ route('recharge-transaction-reports') }}"><span>Recharge Reports</span></a></li>
               <!--<li class="{{(Request::is('account-summary') ? 'active' : '')}}"><a href="#"><span>Recharge Acc. details</span></a></li> -->
               <li class="{{(Request::is('agent-report') ? 'active' : '')}}"><a href="{{ url('agent-report') }}"><span>Agent Report</span></a></li> 
               <li class="{{(Request::is('account-statement') ? 'active' : '')}}"><a href="{{ url('account-statement') }}"><span>Account Statement</span></a></li>
              <!-- <li><a href="{{ route('daily-balance-reports') }}"><span>Daily Closing Balance</span></a></li> -->
</ul>--}}