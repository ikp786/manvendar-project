{{-- <ul class="nav nav-tabs" >
             @if(Auth::user()->role_id == 1) 
            <li class="{{(Request::is('admin/all-members') ? 'active' : '')}}"><a href="{{ url('admin/all-members') }}"><span>All Members</span></a></li>
         
            @endif
            @if(in_array(Auth::user()->role_id,array(1,3)))
            <li class="{{(Request::is('admin/distributor') ? 'active' : '')}}"><a href="{{ url('admin/distributor') }}"><span>Distributor</span></a></li> 
            @endif
            @if(in_array(Auth::user()->role_id,array(1,3,4)))
            <li class="{{(Request::is('admin/retailer') ? 'active' : '')}}"><a href="{{ url('admin/retailer') }}"><span>Retailer</span></a></li> 
            @endif 
            @if(Auth::user()->role_id == 1 && Auth::user()->company_id == 1)        
            <!--<li class="{{(Request::is('admin/otp') ? 'active' : '')}}"><a href="#"><span>White Label</span></a></li>-->
            <li class="{{(Request::is('admin/api-member') ? 'active' : '')}}"><a href="{{ url('admin/api-member') }}"><span>Api user</span></a></li>
            <li class="{{(Request::is('admin/sales-enquiry') ? 'active' : '')}}"><a href="{{ url('admin/sales-enquiry') }}"><span>Sales</span></a></li>
            <li class="{{(Request::is('member/export') ? 'active' : '')}}"><a href="{{ url('member/export') }}"><span>Export member</span></a></li>
            <!--<li><a href="#"><span>Api Document</span></a></li>
            <li><a href="{{url('admin/APImanage')}}"><span>Api Manage</span></a></li>-->
            @endif
</ul>--}}