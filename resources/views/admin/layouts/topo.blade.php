<script type="text/javascript">
   function refreshBalance()
   {
     $.ajax({
      type: "get",
      url: "{{url('refresh-balance')}}",
      data: "Type=getCurrentBalance", 
      dataType:"json",
      beforeSend: function () 
      {
        //$('.login_attempt').html('Processing...');
      },
      success: function (msg) {
        if(msg.status == 1)
          $("#agentTopBalance").text(msg.message);
      }
    });
   }
</script>
<header class="main-header"><meta http-equiv="Content-Type" content="text/html; charset=utf-8">

    <!-- Logo -->
    <a href="#" class="logo">
      <!-- mini logo for sidebar mini 50x50 pixels -->
      <span class="logo-mini"><b>A</b>2Z</span>
      <!-- logo for regular state and mobile devices -->
      <span class="logo-lg"><b>A2Z</b>Suvidhaa</span>
    </a>

    <!-- Header Navbar: style can be found in header.less -->
    <nav class="navbar navbar-static-top">
	<a href="#" style="font-size: 22px;color: wheat;font-family: time;">
      {{Auth::user()->role->role_title}}, ({{Auth::user()->mobile}} : {{Auth::user()->member->company}})</a>
      <a href="#" class="sidebar-toggle" data-toggle="push-menu" role="button">
        <span class="sr-only">Toggle navigation</span>
      </a>
      <div class="navbar-custom-menu">
        <ul class="nav navbar-nav">
          <li class="dropdown messages-menu">
			<span class="label label-success" style="font-size:145%">Balance:<span id="agentTopBalance" > {{number_format(Auth::user()->balance->user_balance,2)}}</span>
				<a href="javascript::void(0)" onClick="refreshBalance()"><i class="fa fa-spin fa-refresh" style="color:white"></i></a></span>
			@if(Auth::user()->role_id==1)
				<span class="label label-success" style="font-size:145%">Commission Balance:{{number_format(Auth::user()->balance->admin_com_bal,2)}}</span>
            @endif
			
          </li>
          <!-- User Account: style can be found in dropdown.less -->
          <li class="dropdown user user-menu">
            <a href="#" class="dropdown-toggle" data-toggle="dropdown">
              <img src="{{url('/user-uploaded-files')}}/{{Auth::id()}}/{{Auth::user()->profile->profile_picture}}" class="user-image">
              <span class="hidden-xs">{{Auth::user()->name}}</span>
            </a>
            <ul class="dropdown-menu">
              <li class="user-header" >
                  
                @php 
                	$GetLoggin = App\UserLoggedInDetail::where('user_id', @Auth::id())->orderBy('id', 'desc')->skip(1)->take(1)->first(['created_at','id']);
                @endphp  
                <img src="{{url('/user-uploaded-files')}}/{{Auth::id()}}/{{Auth::user()->profile->profile_picture}}" class="img-circle" alt="User Image">
                <p style="font-size:15px; ">
                  Member since {{ date("d-m-Y", strtotime(Auth::user()->created_at)) }}
                <br> 
				Last IP:{{Auth::user()->last_login_ip}}
				<br>
				<!-- Last login:{{Auth::user()->last_login_at}} -->
				Last login:{{$GetLoggin->created_at}}
				</p>
              </li>
              <li class="user-footer">
                <div class="pull-left">
                  <a href="{{ url('changepassword')}}" class="btn btn-default btn-flat">Profile</a>
                 
                </div>
                <div class="pull-right">
                  <a href="{{url('logout')}}" class="btn btn-default btn-flat">Sign out</a>
                </div>
              </li>
            </ul>
          </li>
          <!-- Control Sidebar Toggle Button -->
         <!--  <li>
            <a href="#" data-toggle="control-sidebar"><i class="fa fa-gears"></i></a>
          </li> -->
        </ul>
      </div>
    </nav>
  </header>
			@if(in_array(Auth::user()->role_id,array(3,4)))
			<font color="RED"><marquee direction="left" style="background:white">{{Auth::user()->company->recharge_news}}</marquee></font>
			@endif