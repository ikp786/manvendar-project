@if(Auth::user()->role_id==5)  
    <ul class="nav nav-tabs">
        @if(Auth::user()->member->paytm_my_wallet ==1)
            <li><a href="{{route('my-wallet')}}" class="{{(Request::is('my-wallet') ? 'active' : '')}}">A2Z Plus Wallet</a></li>
        @endif
        @if(Auth::user()->member->paytm_my_wallet ==0)
    	<li><a href="{{route('premium-wallet')}}" class="{{(Request::is('premium-wallet') ? 'active' : '')}}">A2Z Wallet</a></li>
    	@endif
    	<li><a href="{{route('money')}}" class="{{(Request::is('money') ? 'active' : '')}}">DMT 1</a></li>
    	@if(Auth::user()->member->dmt_three)
       <li><a href="{{route('dmt-two')}}" class="{{(Request::is('dmt-two') ? 'active' : '')}}">DMT 2</a></li>@endif
       <li><a href="{{url('account/search')}}" class="{{(Request::is('account/search') ? 'active' : '')}}">Account Search</a></li>
    </ul>
@endif
<br>