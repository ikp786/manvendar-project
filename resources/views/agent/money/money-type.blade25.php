 @if(Auth::user()->role_id==5)  
<ul class="nav nav-tabs">
	<li><a href="{{route('premium-wallet')}}" class="{{(Request::is('premium-wallet') ? 'active' : '')}}">A2Z Wallet</a></li>
	<li><a href="{{route('money')}}" class="{{(Request::is('money') ? 'active' : '')}}">DMT 1</a></li>
	@if(Auth::user()->member->dmt_three)
   <li><a href="{{route('dmt-two')}}" class="{{(Request::is('dmt-two') ? 'active' : '')}}">DMT 2</a></li>@endif
   <li><a href="{{url('account/search')}}" class="{{(Request::is('account/search') ? 'active' : '')}}">Account Search</a></li>
</ul>
@endif
<br>