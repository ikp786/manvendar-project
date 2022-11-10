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
 <aside class="main-sidebar">
    <!-- sidebar: style can be found in sidebar.less -->
    <section class="sidebar">
      <!-- Sidebar user panel -->
      <div class="user-panel">
        <div class="pull-left image">
          <!--@if(Auth::user()->profile->profile_picture)
          <img src="{{url('/user-uploaded-files')}}/{{Auth::id()}}/{{Auth::user()->profile->profile_picture}}" class="img-circle" >
          @endif-->
          <img src="{{url('newlog/images/Logo168.png')}}" style="max-width:85% !important; height: 50%; !important">
           
        </div>
        <div class="pull-left info">
        
        </div>
      </div>
    

      <ul class="sidebar-menu" data-widget="tree">
    
        <li >
          <a href="{{route('dashboard')}}">
            <i class="fa fa-dashboard"></i><span>Dashboard</span>
          </a>
        </li>
       
      
        <li class="treeview">
          <a href="#">
           <i class="fa fa-edit"></i>
            <span>Member</span>
            <span class="pull-right-container">
            <i class="fa fa-angle-left pull-right"></i>
            </span>
          </a>
          <ul class="treeview-menu">
          
            <li><a href="{{ url('admin/distributor') }}"><i class="fa fa-circle-o"></i>Distributor</a></li>
            <li><a href="{{ url('admin/retailer') }}"><i class="fa fa-circle-o"></i>Retailer</a></li>
			 <li><a href="{{ url('member/export') }}"><i class="fa fa-circle-o"></i>Export member</a></li>
          </ul>
        </li>
        <li class="treeview">
          <a href="#">
          <i class="fa fa-file"></i>
            <span>Report</span>
            <span class="pull-right-container">
            <i class="fa fa-angle-left pull-right"></i>
            </span>
          </a>
          <ul class="treeview-menu">
           <li>
              <a href="{{ route('all-transaction-reports') }}"><i class="fa fa-circle-o"></i>Network Ledger Report</a>
            </li>
            <li><a href="{{ route('dmt-reports') }}"> <i class="fa fa-circle-o"></i>DMT Reports</a></li>
          
            <li>
              <a href="{{ url('account-statement') }}"><i class="fa fa-circle-o"></i>Account Statement</a>
            </li>
          </ul>
        </li>
        <li class="treeview">
          <a href="#">
             <i class="fa fa-paypal"></i>
            <span>Payment</span>
            <span class="pull-right-container">
            <i class="fa fa-angle-left pull-right"></i>
            </span>
          </a>
          <ul class="treeview-menu">
              <li>
                <a href="{{Route('dist-fund-transfer')}}"><i class="fa fa-circle-o"></i>
                Dist Fund Transfer</a>
              </li>
              <li><a href="{{Route('fund-transfer')}}"><i class="fa fa-circle-o"></i>
                Retailer Fund  Transfer</a>
              </li>
              <li>
                <a href="{{url('payment-request-view')}}"><i class="fa fa-circle-o"></i>
                  Request View</a>
              </li>
              <li>
                <a href="{{ url('fund-request-report') }}"><i class="fa fa-circle-o"></i>
                  Request Report</a>
              </li>
              <!--<li>
                <a href="{{url('payment-report')}}"><i class="fa fa-circle-o"></i>
                  Payment Report</a>
              </li>-->
			   <li>
                <a href="{{Route('bank-fund')}}"><i class="fa fa-circle-o"></i>
                 Payment Request</a>
              </li>
			  <li>
                <a href="{{ url('retailer-payment') }}"><i class="fa fa-circle-o"></i>
                  Payment Report</a>
              </li>
          </ul>
        </li>
		<li class="treeview">
          <a href="#">
             <i class="fa fa-bank"></i>
            <span>Bank Detail</span>
            <span class="pull-right-container">
            <i class="fa fa-angle-left pull-right"></i>
            </span>
          </a>
          <ul class="treeview-menu">
            <li>
              <a href="{{route('company-bank-details')}}">
               <i class="fa fa-circle-o"></i>
               Company 
              </a>
            </li> 
            <li>
              <a href="{{route('lower-level-bank-details')}}">
               <i class="fa fa-circle-o"></i>
                Add Bank 
              </a>
            </li>
          </ul>
        </li>      
      </ul>
    </section>
    <!-- /.sidebar -->
  </aside>