<style>
.dropdown-toggle>a:hover
{
	background:none; !important;
} 
</style>
<div class="topbar-main" style="background:#F0F0E9">
    <div class="container">
        <!-- Logo container-->
        <div class="logo">
            <a href="{{ url('dashboard') }}" class="logo" style="color:black !important;">Welcome to UFX</a>
        </div>
        <!-- End Logo container-->
        <div class="menu-extras">
            <ul class="nav navbar-nav navbar-right pull-right">
            
                <li class="dropdown hidden-xs">
                    
                    <ul style="display: none" class="dropdown-menu dropdown-menu-lg">
                        <li class="notifi-title"><span class="label label-default pull-right">New 3</span>Notification
                        </li>
                        <li class="list-group nicescroll notification-list">
                            <!-- list item-->
                            <a href="javascript:void(0);" class="list-group-item">
                                <div class="media">
                                    <div class="pull-left p-r-10">
                                        <em class="fa fa-diamond fa-2x text-primary"></em>
                                    </div>
                                    <div class="media-body">
                                        <h5 class="media-heading">A new order has been placed A new order has been
                                            placed</h5>
                                        <p class="m-0">
                                            <small>There are new settings available</small>
                                        </p>
                                    </div>
                                </div>
                            </a>
                            <!-- list item-->
                            <a href="javascript:void(0);" class="list-group-item">
                                <div class="media">
                                    <div class="pull-left p-r-10">
                                        <em class="fa fa-cog fa-2x text-custom"></em>
                                    </div>
                                    <div class="media-body">
                                        <h5 class="media-heading">New settings</h5>
                                        <p class="m-0">
                                            <small>There are new settings available</small>
                                        </p>
                                    </div>
                                </div>
                            </a>

                            <!-- list item-->
                            <a href="javascript:void(0);" class="list-group-item">
                                <div class="media">
                                    <div class="pull-left p-r-10">
                                        <em class="fa fa-bell-o fa-2x text-danger"></em>
                                    </div>
                                    <div class="media-body">
                                        <h5 class="media-heading">Updates</h5>
                                        <p class="m-0">
                                            <small>There are <span class="text-primary font-600">2</span> new updates
                                                available
                                            </small>
                                        </p>
                                    </div>
                                </div>
                            </a>

                            <!-- list item-->
                            <a href="javascript:void(0);" class="list-group-item">
                                <div class="media">
                                    <div class="pull-left p-r-10">
                                        <em class="fa fa-user-plus fa-2x text-info"></em>
                                    </div>
                                    <div class="media-body">
                                        <h5 class="media-heading">New user registered</h5>
                                        <p class="m-0">
                                            <small>You have 10 unread messages</small>
                                        </p>
                                    </div>
                                </div>
                            </a>

                            <!-- list item-->
                            <a href="javascript:void(0);" class="list-group-item">
                                <div class="media">
                                    <div class="pull-left p-r-10">
                                        <em class="fa fa-diamond fa-2x text-primary"></em>
                                    </div>
                                    <div class="media-body">
                                        <h5 class="media-heading">A new order has been placed A new order has been
                                            placed</h5>
                                        <p class="m-0">
                                            <small>There are new settings available</small>
                                        </p>
                                    </div>
                                </div>
                            </a>

                            <!-- list item-->
                            <a href="javascript:void(0);" class="list-group-item">
                                <div class="media">
                                    <div class="pull-left p-r-10">
                                        <em class="fa fa-cog fa-2x text-custom"></em>
                                    </div>
                                    <div class="media-body">
                                        <h5 class="media-heading">New settings</h5>
                                        <p class="m-0">
                                            <small>There are new settings available</small>
                                        </p>
                                    </div>
                                </div>
                            </a>
                        </li>
                        <li>
                            <a href="javascript:void(0);" class="list-group-item text-right">
                                <small class="font-600">See all notifications</small>
                            </a>
                        </li>
                    </ul>
                </li>

                <li class="dropdown">
				<div class="col-md-6 col-sm-12 col-lg-12">
                    <a href="" style="background:none !important;"class="dropdown-toggle waves-effect waves-light profile" data-toggle="dropdown"
                       aria-expanded="true"><span style="color:black;">Balance : Money -  <strong
                                id="apibalancenew">{{ number_format(Auth::user()->balance->user_balance, 2) }}</strong>

                                | Recharge - <strong>{{ number_format(Auth::user()->balance->user_commission, 2) }}</strong></span>
                        | <img src="{{ url('profile_picture/') }}/{{ Auth::user()->profile->profile_picture }}"
                               alt="user-img" class="img-circle"> <span
                                style="color: black;">{{ ucwords(Auth::user()->name) }} </span></a>
								
                    <ul class="dropdown-menu">
                        <li><a href="{{ url('myprofile') }}"><i class="ti-user m-r-5"></i> Profile</a></li>
                        <li><a href="{{ url('logout') }}"><i class="ti-power-off m-r-5"></i> Logout</a></li>
                    </ul>
					</div>
                </li>
				
            </ul>
            <div class="menu-item">
                <!-- Mobile menu toggle-->
                <a class="navbar-toggle">
                    <div class="lines">
                        <span></span>
                        <span></span>
                        <span></span>
                    </div>
                </a>
                <!-- End mobile menu toggle-->
            </div>
        </div>

    </div>
</div>