<style> .hide_block{ display:none; } </style>

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
@php
    use App\Http\Controllers\Controller;   
    
@endphp
 <aside class="main-sidebar">
    <!-- sidebar: style can be found in sidebar.less -->
    <section class="sidebar">
      <!-- Sidebar user panel -->
      <div class="user-panel">
        <div class="pull-left image">
		<a href="{{url('dashboard')}}">
          <img src="{{url('newlog/images/Logo168.png')}}" style="max-width:85% !important; height: 50%; !important"></a>
        </div>
        <div class="pull-left info">
         <!-- <p>{{Auth::user()->name}}</p>
         Balance:{{number_format(Auth::user()->balance->user_balance,2)}}
        <span> <a href="javascript::void(0)" onClick="refreshBalance()"><i class="fa fa-spin fa-refresh"></i></a></span>-->
     
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
        <li class="treeview {{ (Controller::is_assigned(Auth::user()->id,'1')=='1') ? ' hide_block ' : '' }}"  >
          <a href="#">
             <i class="fa fa-pie-chart"></i>
            <span> Tools</span>
            <span class="pull-right-container">
            <i class="fa fa-angle-left pull-right"></i>
            </span>
          </a>
          <ul class="treeview-menu">
            <li class="{{ (Controller::is_assigned(Auth::user()->id,'9')=='1') ? ' hide_block ' : '' }}" ><a href="{{route('admin/otp')}}"><i class="fa fa-circle-o"></i>OTP Manager</a></li>
			<li class="{{ (Controller::is_assigned(Auth::user()->id,'10')=='1') ? ' hide_block ' : '' }}" >
                <a href="{{ url('tools/bankupdown') }}">
                  <i class="fa fa-circle-o"></i>
                  Bank Management
                </a>
            </li>
			<li class="{{ (Controller::is_assigned(Auth::user()->id,'11')=='1') ? ' hide_block ' : '' }}"><a href="{{url('admin/offline/record')}}"><i class="fa fa-circle-o"></i>Offline Record</a></li>
			
            
          
            <li class="{{ (Controller::is_assigned(Auth::user()->id,'10')=='12') ? ' hide_block ' : '' }}"><a href="{{route('admin/login-history')}}"><i class="fa fa-circle-o"></i>Login History</a></li>
            
          </ul>
        </li>
        <li class="treeview {{ (Controller::is_assigned(Auth::user()->id,'2')=='1') ? ' hide_block ' : '' }}">
          <a href="#">
            <i class="fa fa-building"></i>
            <span> Company Master</span>
            <span class="pull-right-container">
            <i class="fa fa-angle-left pull-right"></i>
            </span>
          </a>
          <ul class="treeview-menu">
            <li class="{{ (Controller::is_assigned(Auth::user()->id,'13')=='1') ? ' hide_block ' : '' }}"><a href="{{url('company')}}"><i class="fa fa-circle-o"></i>Company</a></li>
            <li class="{{ (Controller::is_assigned(Auth::user()->id,'14')=='1') ? ' hide_block ' : '' }}"><a href="{{url('recharge-scheme')}}"><i class="fa fa-circle-o"></i>Recharge Scheme</a></li>
            <li class="{{ (Controller::is_assigned(Auth::user()->id,'15')=='1') ? ' hide_block ' : '' }}"><a href="{{url('recharge-operator-list')}}"><i class="fa fa-circle-o"></i>Recharge Operators</a></li>
			<li class="{{ (Controller::is_assigned(Auth::user()->id,'16')=='1') ? ' hide_block ' : '' }}"><a href="{{url('provider-list')}}"><i class="fa fa-circle-o"></i>Providers</a></li>
            <li class="{{ (Controller::is_assigned(Auth::user()->id,'17')=='1') ? ' hide_block ' : '' }}"><a href="{{url('dmt-imps-scheme')}}"><i class="fa fa-circle-o"></i>DMT1 Scheme</a></li>
            <li  class="{{ (Controller::is_assigned(Auth::user()->id,'18')=='1') ? ' hide_block ' : '' }}"><a href="{{url('dmt-two-imps-scheme')}}"><i class="fa fa-circle-o"></i>A2Z  Scheme</a></li>
            
            <li class=" {{ (Controller::is_assigned(Auth::user()->id,'59')=='1') ? ' hide_block ' : '' }}"><a href="{{url('a2z-plus-scheme')}}"><i class="fa fa-circle-o"></i>A2Z Plus Scheme</a></li>
            
			<li class="{{ (Controller::is_assigned(Auth::user()->id,'19')=='1') ? ' hide_block ' : '' }}"><a href="{{url('verification-scheme')}}"><i class="fa fa-circle-o"></i>Verification  Scheme</a></li>
			<li class="{{ (Controller::is_assigned(Auth::user()->id,'20')=='1') ? ' hide_block ' : '' }}"><a href="{{url('bill-scheme-list')}}"><i class="fa fa-circle-o"></i>Bill  Scheme</a></li>
			<li class="{{ (Controller::is_assigned(Auth::user()->id,'21')=='1') ? ' hide_block ' : '' }}"><a href="{{url('aeps-scheme')}}"><i class="fa fa-circle-o"></i>Aeps  Scheme</a></li>
			
			<li class="{{ (Controller::is_assigned(Auth::user()->id,'66')=='1') ? ' hide_block ' : '' }}"><a href="{{url('adhaar-pay-scheme')}}"><i class="fa fa-circle-o"></i>Adhaar Pay  Scheme</a></li>
			
            <li class="{{ (Controller::is_assigned(Auth::user()->id,'22')=='1') ? ' hide_block ' : '' }}"><a href="{{route('servicemanagement')}}"><i class="fa fa-circle-o"></i>Service Management</a></li>
			<li class="{{ (Controller::is_assigned(Auth::user()->id,'23')=='1') ? ' hide_block ' : '' }}"><a href="{{url('otp/list')}}"><i class="fa fa-circle-o"></i>OTP List</a></li>
          </ul>
        </li>
        <li class="treeview {{ (Controller::is_assigned(Auth::user()->id,'3')=='1') ? ' hide_block ' : '' }}">
          <a href="#">
           <i class="fa fa-edit"></i>
            <span>Member</span>
            <span class="pull-right-container">
            <i class="fa fa-angle-left pull-right"></i>
            </span>
          </a>
          <ul class="treeview-menu">
            <li class="{{ (Controller::is_assigned(Auth::user()->id,'64')=='1') ? ' hide_block ' : '' }}"><a href="{{ url('admin/all-remitters') }}"><i class="fa fa-circle-o"></i>All Remitters</a></li>  
            <li class="{{ (Controller::is_assigned(Auth::user()->id,'24')=='1') ? ' hide_block ' : '' }}"><a href="{{ url('admin/all-members') }}"><i class="fa fa-circle-o"></i>All Members</a></li>
            <li class="{{ (Controller::is_assigned(Auth::user()->id,'25')=='1') ? ' hide_block ' : '' }}"><a href="{{url('admin/master-distributor')}}"><i class="fa fa-circle-o"></i>MD</a></li>
            <li class="{{ (Controller::is_assigned(Auth::user()->id,'26')=='1') ? ' hide_block ' : '' }}"><a href="{{ url('admin/distributor') }}"><i class="fa fa-circle-o"></i>Distributor</a></li>
            <li class="{{ (Controller::is_assigned(Auth::user()->id,'27')=='1') ? ' hide_block ' : '' }}"><a href="{{ url('admin/retailer') }}"><i class="fa fa-circle-o"></i>Retailer</a></li>
            <li class="{{ (Controller::is_assigned(Auth::user()->id,'28')=='1') ? ' hide_block ' : '' }}"><a href="{{ url('admin/api-member') }}"><i class="fa fa-circle-o"></i>Api user</a></li>
			<li class="{{ (Controller::is_assigned(Auth::user()->id,'29')=='1') ? ' hide_block ' : '' }}"><a href="{{url('admin/APImanage')}}"><i class="fa fa-circle-o"></i>Api Manage</a></li>
            <li class="{{ (Controller::is_assigned(Auth::user()->id,'60')=='1') ? ' hide_block ' : '' }}"><a href="{{ url('aeps/bank-details') }}"><i class="fa fa-circle-o"></i>Aeps Bank Details</a></li>
            <li class="{{ (Controller::is_assigned(Auth::user()->id,'30')=='1') ? ' hide_block ' : '' }}"><a href="{{ url('member/export') }}"><i class="fa fa-circle-o"></i>Export member</a></li>
            
            <li class="{{ (Controller::is_assigned(Auth::user()->id,'65')=='1') ? ' hide_block ' : '' }}"><a href="{{ url('aeps-agent') }}"><i class="fa fa-circle-o"></i>Aeps On-Boarding</a></li>
            
            <li class="{{ (Controller::is_assigned(Auth::user()->id,'61')=='1') ? ' hide_block ' : '' }}"><a href="{{ url('admin/all-members/19') }}"><i class="fa fa-circle-o"></i>Sub Admin</a></li>
          </ul>
        </li>
        <li class="treeview {{ (Controller::is_assigned(Auth::user()->id,'4')=='1') ? ' hide_block ' : '' }}">
          <a href="#">
          <i class="fa fa-file"></i>
            <span>Report</span>
            <span class="pull-right-container">
            <i class="fa fa-angle-left pull-right"></i>
            </span>
          </a>
          <ul class="treeview-menu">
            <li class="{{ (Controller::is_assigned(Auth::user()->id,'31')=='1') ? ' hide_block ' : '' }}">
              <a href="{{ route('all-transaction-reports') }}"><i class="fa fa-circle-o"></i>
                  Network Ledger Report</a>
            </li>
            <li class="{{ (Controller::is_assigned(Auth::user()->id,'32')=='1') ? ' hide_block ' : '' }}"><a href="{{ route('dmt-reports') }}"> <i class="fa fa-circle-o"></i>Uses Reports</a></li>
				<li>
                <a href="{{ route('recharge-reports') }}">
                  <i class="fa fa-circle-o"></i>
                   Recharge Report
                </a>
            </li>
			<li class="{{ (Controller::is_assigned(Auth::user()->id,'33')=='1') ? ' hide_block ' : '' }}">
                <a href="{{ url('account-statement') }}"><i class="fa fa-circle-o"></i>Account Statement</a>
            </li>
			<li class="{{ (Controller::is_assigned(Auth::user()->id,'34')=='1') ? ' hide_block ' : '' }}">
                <a href="{{ route('tds-reports') }}">
                  <i class="fa fa-circle-o"></i>
                   TDS Report
                </a>
            </li>
			<li class="{{ (Controller::is_assigned(Auth::user()->id,'35')=='1') ? ' hide_block ' : '' }}">
                <a href="{{ route('api-report') }}">
                  <i class="fa fa-circle-o"></i>
                  Api Report
                </a>
              </li> 
			<li class="{{ (Controller::is_assigned(Auth::user()->id,'36')=='1') ? ' hide_block ' : '' }}">
                <a href="{{ route('operator-report') }}">
                  <i class="fa fa-circle-o"></i>
                  Operator Report
                </a>
            </li>
            <li class="{{ (Controller::is_assigned(Auth::user()->id,'37')=='1') ? ' hide_block ' : '' }}">
                <a href="{{ url('agent-report') }}"><i class="fa fa-circle-o"></i>Agent Report</a>
              </li>
               
			<li class="{{ (Controller::is_assigned(Auth::user()->id,'38')=='1') ? ' hide_block ' : '' }}">
                <a href="{{ route('txn-with-commission') }}"><i class="fa fa-circle-o"></i>Txn Commission Report</a>
            </li>
           <!--  <li><a href="{{url('complain')}}"><i class="fa fa-circle-o"></i>View Complain</a></li> -->
			<li class="{{ (Controller::is_assigned(Auth::user()->id,'39')=='1') ? ' hide_block ' : '' }}">
                <a href="{{ url('aeps/report') }}"><i class="fa fa-circle-o"></i>Aeps Selltelment</a>
              </li>
             <li class="{{ (Controller::is_assigned(Auth::user()->id,'40')=='1') ? ' hide_block ' : '' }}">
                <a href="{{ url('report/r-to-r') }}"><i class="fa fa-circle-o"></i>R2R Report</a>
              </li>
          </ul>
        </li>
        <li class="treeview {{ (Controller::is_assigned(Auth::user()->id,'5')=='1') ? ' hide_block ' : '' }}">
          <a href="#">
             <i class="fa fa-paypal"></i>
            <span>Payment</span>
            <span class="pull-right-container">
            <i class="fa fa-angle-left pull-right"></i>
            </span>
          </a>
          <ul class="treeview-menu">
			 <li class="{{ (Controller::is_assigned(Auth::user()->id,'41')=='1') ? ' hide_block ' : '' }}">
                <a href="{{url('api-user-fund-transfer')}}"><i class="fa fa-circle-o"></i>
                Api User Fund Transfer</a>
              </li>
			  <li class="{{ (Controller::is_assigned(Auth::user()->id,'42')=='1') ? ' hide_block ' : '' }}">
                <a href="{{Route('md-fund-transfer')}}"><i class="fa fa-circle-o"></i>
                Md Fund Transfer</a>
              </li>
              <li class="{{ (Controller::is_assigned(Auth::user()->id,'43')=='1') ? ' hide_block ' : '' }}">
                <a href="{{Route('dist-fund-transfer')}}"><i class="fa fa-circle-o"></i>
                Dist Fund Transfer</a>
              </li>
              <li class="{{ (Controller::is_assigned(Auth::user()->id,'44')=='1') ? ' hide_block ' : '' }}"><a href="{{Route('fund-transfer')}}"><i class="fa fa-circle-o"></i>
                Retailer Fund  Transfer</a>
              </li>
              <li class="{{ (Controller::is_assigned(Auth::user()->id,'45')=='1') ? ' hide_block ' : '' }}">
                <a href="{{url('md-fund-return')}}"><i class="fa fa-circle-o"></i>
                  MD Fund Return</a>
              </li>
			  <li class="{{ (Controller::is_assigned(Auth::user()->id,'62')=='1') ? ' hide_block ' : '' }}">
                <a href="{{Route('dist-fund-return')}}"><i class="fa fa-circle-o"></i>
                  Dist Fund Return</a>
              </li>
              <li class="{{ (Controller::is_assigned(Auth::user()->id,'63')=='1') ? ' hide_block ' : '' }}">
                <a href="{{Route('fund-return')}}"><i class="fa fa-circle-o"></i>
                  Retailer Fund Return</a>
              </li>
			  <li class="{{ (Controller::is_assigned(Auth::user()->id,'46')=='1') ? ' hide_block ' : '' }}">
                <a href="{{url('api-fund-return')}}"><i class="fa fa-circle-o"></i>
                 Api Fund Return</a>
              </li>
              <li class="{{ (Controller::is_assigned(Auth::user()->id,'47')=='1') ? ' hide_block ' : '' }}">
                <a href="{{url('admin/purchase-balance')}}"><i class="fa fa-circle-o"></i>
                  Purchase Balance</a>
              </li>
              <li class="{{ (Controller::is_assigned(Auth::user()->id,'48')=='1') ? ' hide_block ' : '' }}">
                <a href="{{url('payment-request-view')}}"><i class="fa fa-circle-o"></i>
                  Request View</a>
              </li>
              <li class="{{ (Controller::is_assigned(Auth::user()->id,'49')=='1') ? ' hide_block ' : '' }}">
                <a href="{{ url('fund-request-report') }}"><i class="fa fa-circle-o"></i>
                  Request Report</a>
              </li>
              <li class="{{ (Controller::is_assigned(Auth::user()->id,'50')=='1') ? ' hide_block ' : '' }}">
                <a href="{{url('retailer-payment')}}"><i class="fa fa-circle-o"></i>
                  Payment Report</a>
              </li>
          </ul>
        </li>
        <li class="treeview {{ (Controller::is_assigned(Auth::user()->id,'6')=='1') ? ' hide_block ' : '' }}">
          <a href="#">
          <i class="fa fa-book"></i>
            <span> Accounting</span>
            <span class="pull-right-container">
            <i class="fa fa-angle-left pull-right"></i>
            </span>
          </a>
          <ul class="treeview-menu">
             <li class="{{ (Controller::is_assigned(Auth::user()->id,'51')=='1') ? ' hide_block ' : '' }}">
                <a href="{{url('admin/user-lists')}}">
                  <i class="fa fa-circle-o"></i>
                  Member Document
                </a>
              </li>
			  <li class="{{ (Controller::is_assigned(Auth::user()->id,'52')=='1') ? ' hide_block ' : '' }}">
                <a href="{{url('businessview')}}">
                  <i class="fa fa-circle-o"></i>
                  Api Report
                </a>
              </li>
              <li class="{{ (Controller::is_assigned(Auth::user()->id,'53')=='1') ? ' hide_block ' : '' }}">
                <a href="{{route('account/operator-report')}}">
                  <i class="fa fa-circle-o"></i>
                  Operator Report
                </a>
              </li>
          </ul>
        </li>
        <li class=" {{ (Controller::is_assigned(Auth::user()->id,'7')=='1') ? ' hide_block ' : '' }}">
          <a href="{{url('view-complain')}}">
          <i class="nav-icon fa fa-comment"></i>  
            <span> View Complain</span>
            <btn id="complain_admin_count" class="btn btn-warning btn-xs" style="margin-left: 15px; border-radius: 10px;" > 
            @php 
                echo App\Complain::where('status_id', 3)->count()
            @endphp
            </btn> 
          </a>
        </li>

		<li class="treeview {{ (Controller::is_assigned(Auth::user()->id,'8')=='1') ? ' hide_block ' : '' }}">
              <a href="#">
                  <i class="glyphicon glyphicon-move"></i>
                  <span> More</span>
				<span class="pull-right-container">
					<i class="fa fa-angle-left pull-right"></i>
				</span>
              </a>
              <ul class="treeview-menu">
			    <li class="{{ (Controller::is_assigned(Auth::user()->id,'54')=='1') ? ' hide_block ' : '' }}"><a href="{{route('send-sms')}}"><i class="fa fa-circle-o"></i>Send Sms</a></li>
                <li class="{{ (Controller::is_assigned(Auth::user()->id,'55')=='1') ? ' hide_block ' : '' }}"><a href="{{route('company-bank-details')}}"><i class="fa fa-circle-o"></i>Add Bank</a></li>

                <li class="{{ (Controller::is_assigned(Auth::user()->id,'56')=='1') ? ' hide_block ' : '' }}"><a href="{{route('master-bank-detail')}}"><i class="fa fa-circle-o"></i>Add Master Bank</a></li>

                <li class="{{ (Controller::is_assigned(Auth::user()->id,'57')=='1') ? ' hide_block ' : '' }}"><a href="{{route('holiday')}}"><i class="fa fa-circle-o"></i>Holiday List</a></li>
				
				<li class="{{ (Controller::is_assigned(Auth::user()->id,'58')=='1') ? ' hide_block ' : '' }}"><a href="{{route('action-otp-verification')}}"><i class="fa fa-circle-o"></i>Action OTP Verification</a></li>
				
              </ul>
         </li>
       <!-- <li>
          <a href="{{url('api_balance')}}">
           <i class="fa fa-bank"></i> 
            <span> Api balance</span>
			 <span class="pull-right-container">
            <i class="fa fa-angle-left pull-right"></i>
            </span>
          </a>
        </li>-->
      </ul>
    </section>
    <!-- /.sidebar --> 
 </aside>