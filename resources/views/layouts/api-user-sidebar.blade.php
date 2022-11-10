 <aside class="main-sidebar">
    <section class="sidebar">
      <div class="user-panel">
        <div class="pull-left image">
           <img src="{{url('newlog/images/Logo168.png')}}" style="max-width:85% !important; height: 50%; !important">
        </div>
        <div class="pull-left info">
        </div>
      </div>
      <ul class="sidebar-menu" data-widget="tree">
        <li class="treeview">
          <a href="#">
            <i class="fa fa-file"></i>
            <span>Report</span>
            <span class="pull-right-container">
              <i class="fa fa-plus pull-right"></i></span>
          </a>
          <ul class="treeview-menu">
			      <li><a href="{{url('recharge-nework')}}"><i class="fa fa-circle-o"></i>Ledger Report</a></li>
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
                 <li><a href="{{route('bank-fund')}}"><i class="fa fa-circle-o"></i>Fund Request</a></li>				 <li><a href="{{url('aeps/bank-details')}}">  <i class="fa fa-money"></i> Aeps</a></li>
              </ul>
          </li>
      </ul>
    </section>
  </aside>