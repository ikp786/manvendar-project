
<div class="col-md-12" style="margin-top: -54px;">
   <nav class="navbar navbar-default agent-header-color" style="margin-top: 0px;">
          <div class="navbar-header">
            <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
              <span class="sr-only">Toggle navigation</span>
              <span class="icon-bar"></span>
              <span class="icon-bar"></span>
              <span class="icon-bar"></span>
            </button>
            <a class="navbar-brand agent-font-color" href="#"></a>
          </div>
          <div id="navbar" class="navbar-collapse collapse">
            <ul class="nav navbar-nav"> 
            <li><a href="{{ url('dashboard') }}">Dashboard</a></li>
            <li class="dropdown">
                <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Member<span class="caret"></span></a>
                <ul class="dropdown-menu" style=" background: #f89923;">
					<li><a href="{{ url('admin/master-distributor') }}">Master Distributor</a></li>
					<li><a href="{{ url('admin/distributor') }}">Distributor</a></li>
					<li><a href="{{ url('admin/retailer') }}">Retailer</a></li>
					<li><a href="{{ url('admin/sales-enquiry') }}">Sales Enquiry</a></li>
					<li><a href="{{ url('admin/all-member') }}">All Members</a></li> 
					<!--<li><a href="{{ url('member/export') }}">Export Members</a></li> -->
				</ul>
              </li> 
			  <li class="dropdown">
                <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Report<span class="caret"></span></a>
                <ul class="dropdown-menu" style=" background: #f89923;">
					<li><a href="{{ route('all-transaction-reports') }}">All Transaction Report</a></li>
					<li><a href="{{ route('dmt-reports') }}">DMT Reports</a></li>
					<li><a href="{{ route('recharge-transaction-reports') }}">Recharge Transaction Reports</a></li>
					<li><a href="{{ url('agent-report') }}">Agent Report</a></li>
					<li><a href="{{ url('account-statement') }}">Account statement</a></li>
                        
                </ul>
              </li>
			  
			  <li class="dropdown">
                <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Payment<span class="caret"></span></a>
                <ul class="dropdown-menu" style=" background: #f89923;">
					<li><a href="{{ url('payment-request-view') }}">Agent Request View</a></li>
					<li><a href="{{ url('fund-request-report') }}">Agent Request Report</a></li>
					<li><a href="{{ url('payment-report') }}">Payment Report</a></li>
                </ul>
              </li>
			  <li class="dropdown">
                <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Pen/Ref/Int<span class="caret"></span></a>
                <ul class="dropdown-menu" style=" background: #f89923;">
                  <li><a href="{{ url('pend-refd-intd') }}">Money(Pend/Refd/Intd)</a></li>
                </ul>
              </li>
			
            </ul>
            
          </div><!--/.nav-collapse -->
       
      </nav>
  </div>


   

</body>
</html>



                   

         
    