 <aside class="main-sidebar">
    <!-- sidebar: style can be found in sidebar.less -->
    <section class="sidebar">
      <!-- Sidebar user panel -->
      <div class="user-panel">
        <div class="pull-left image">
		<!-- @if(Auth::user()->profile->profile_picture)
          <img src="{{url('/user-uploaded-files')}}/{{Auth::id()}}/{{Auth::user()->profile->profile_picture}}" class="img-circle" >
          @endif-->
		  <a href="{{url('dashboard')}}">
           <img src="{{url('newlog/images/Logo168.png')}}" style="max-width:85% !important; height: 50%; !important"></a>
           
        </div>
        <div class="pull-left info">
         <!-- <p>{{Auth::user()->name}}</p>-->
        </div>
      </div>
      <!-- search form -->
      <!-- <form action="#" method="get" class="sidebar-form">
        <div class="input-group">
          <input type="text" name="q" class="form-control" placeholder="Search...">
          <span class="input-group-btn">
                <button type="submit" name="search" id="search-btn" class="btn btn-flat">
                  <i class="fa fa-search"></i>
                </button>
              </span>
        </div>
      </form> -->

      <ul class="sidebar-menu" data-widget="tree">
    
       <!--  <li class="active treeview menu-open">
          <a href="{{route('home')}}">
            <i class="fa fa-dashboard"></i><span>Dashboard</span>
          </a>
        </li> -->
        <li>
          <a href="{{route('dashboard')}}">
            <i class="fa fa-dashboard"></i><span>Dashboard</span>
          </a>
        </li>
        <!-- <li class="treeview">
          <a href="#">
            <i class="fa fa-files-o"></i>
            <span>Layout Options</span>
            <span class="pull-right-container">
              <span class="label label-primary pull-right">4</span>
            </span>
          </a>
          <ul class="treeview-menu">
            <li><a href="pages/layout/top-nav.html"><i class="fa fa-circle-o"></i> Top Navigation</a></li>
            <li><a href="pages/layout/boxed.html"><i class="fa fa-circle-o"></i> Boxed</a></li>
            <li><a href="pages/layout/fixed.html"><i class="fa fa-circle-o"></i> Fixed</a></li>
            <li><a href="pages/layout/collapsed-sidebar.html"><i class="fa fa-circle-o"></i> Collapsed Sidebar</a></li>
          </ul>
        </li> -->
        <li class="treeview">
          <a href="#">
             <i class="fa fa-mobile"></i>
            <span>Recharge</span>
            <span class="pull-right-container">
            <i class="fa fa-plus pull-right"></i>
            </span>
          </a>
          <ul class="treeview-menu">
            <li><a href="{{route('recharge')}}"><i class="fa fa-circle-o"></i>Prepaid</a></li>
            <li><a href="{{route('dth-recharge')}}"><i class="fa fa-circle-o"></i>DTH</a></li>
            <li><a href="{{route('datacard-recharge')}}"><i class="fa fa-circle-o"></i>Data Card</a></li>
          </ul>
        </li>
         <li class="treeview">
          <a href="#">
           <i class="fa fa-money"></i>
            <span>Money</span>
            <span class="pull-right-container">
            <i class="fa fa-plus pull-right"></i>
            </span>
          </a>
          <ul class="treeview-menu">
            <li><a href="{{route('premium-wallet')}}"><i class="fa fa-circle-o"></i>A2Z Wallet</a></li>
            <li><a href="{{route('money')}}"><i class="fa fa-circle-o"></i>DMT 1</a></li>
			@if(Auth::user()->member->dmt_three)
           <li><a href="{{route('dmt-two')}}"><i class="fa fa-circle-o"></i>DMT 2</a></li>@endif
		   <li><a href="{{url('account/search')}}"><i class="fa fa-circle-o"></i>Account Search</a></li>
          </ul>
        </li>
        <li class="treeview">
          <a href="#">
          <i class="fa fa-bolt"></i>
            <span>BBPS</span>
            <span class="pull-right-container">
            <i class="fa fa-plus pull-right"></i>
            </span>
          </a>
          <ul class="treeview-menu">
            <li><a href="{{route('bbps')}}"><i class="fa fa-circle-o"></i>Electricity</a></li>
            <li><a href="{{route('bbps-postpaid')}}"><i class="fa fa-circle-o"></i>PostPaid</a></li>
            <li><a href="{{route('bbps-broadband')}}"><i class="fa fa-circle-o"></i>BroadBand</a></li>
            <li><a href="{{route('bbps-landline')}}"><i class="fa fa-circle-o"></i>LandLine</a></li>
            <li><a href="{{route('bbps-water')}}"><i class="fa fa-circle-o"></i>Water</a></li>
            <li><a href="{{route('bbps-gas')}}"><i class="fa fa-circle-o"></i>Gas</a></li>
            <li><a href="{{route('bbps-insurance')}}"><i class="fa fa-circle-o"></i>Insurance</a></li>
			
          </ul>
        </li>


               {{--reports--}}
        <li class="treeview">
          <a href="#">
      <i class="fa fa-file"></i>
            <span>Report</span>
            <span class="pull-right-container">
        <i class="fa fa-plus pull-right"></i>
            </span>
          </a>
          <ul class="treeview-menu">
			<li><a href="{{url('recharge-nework')}}"><i class="fa fa-circle-o"></i>Ledger Report</a></li>
            <li><a href="{{route('summary_report')}}"><i class="fa fa-circle-o"></i>Usage Report</a></li>
			<li><a href="{{route('businessview')}}"><i class="fa fa-circle-o"></i>Business View</a></li>
            

           <!--  <li><a href="{{url('complain')}}"><i class="fa fa-circle-o"></i>View Complain</a></li> -->
          </ul>
        </li>

           {{--fund--}}
           <li class="treeview">
               <a href="#">
                   <i class="fa fa-file"></i>
                   <span>Funds</span>
					<span class="pull-right-container">
						<i class="fa fa-plus pull-right"></i>
					</span>
               </a>
               <ul class="treeview-menu">
                   <li><a href="{{route('fund-request-summary')}}"><i class="fa fa-circle-o"></i>Fund Report</a></li>
                   <li><a href="{{route('bank-fund')}}"><i class="fa fa-circle-o"></i>Fund Request</a></li>
                   <li><a href="{{url('transfer/r-to-r')}}"><i class="fa fa-circle-o"></i>R-to-R Transfer</a></li>  
				   <li><a href="{{route('dt-report')}}"><i class="fa fa-circle-o"></i>DT Report</a></li>
               </ul>
           </li>
		<li><a href="{{url('aeps')}}">  <i class="fa fa-money"></i> Aeps</a></li>
		<li>
		  <a href="{{route('company-bank-details')}}">
		   <i class="fa fa-bank"></i> 
			<span>Bank Detail</span>
		  </a>
        </li>
		
		<li>
		  <a href="{{route('view-complain')}}">
		   <i class="fa fa-bank"></i> 
			<span>Complain List</span>
		  </a>
        </li>
		<li>
		  <a href="{{route('aeps_driver')}}">
		   <i class="fa fa-file-o"></i> 
		  <span>Aeps Kit</span>
		  </a>
		</li>
      </ul>
    </section>
    <!-- /.sidebar -->
  </aside>