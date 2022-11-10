<script type="text/javascript">
  
   function refreshBalance()
   {
     $.ajax({
      type: "get",
      url: "<?php echo e(url('refresh-balance')); ?>",
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
          <!-- <?php if(Auth::user()->profile->profile_picture): ?>
          <img src="<?php echo e(url('/user-uploaded-files')); ?>/<?php echo e(Auth::id()); ?>/<?php echo e(Auth::user()->profile->profile_picture); ?>" class="img-circle" >
            <?php endif; ?>-->
          <img src="<?php echo e(url('newlog/images/Logo168.png')); ?>" style="max-width:85% !important; height: 50%; !important">
          
        </div>
        <div class="pull-left info">
         <!-- <p><?php echo e(Auth::user()->name); ?></p>
        <p > Balance:<span id="agentTopBalance"><?php echo e(number_format(Auth::user()->balance->user_balance,2)); ?>

        <span> <a href="javascript::void(0)" onClick="refreshBalance()"><i class="fa fa-spin fa-refresh"></i></a></span></p>-->
     
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
          <a href="<?php echo e(route('dashboard')); ?>">
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
              <i class="fa fa-table"></i>
            <span>Member</span>
            <span class="pull-right-container">
            <i class="fa fa-angle-left pull-right"></i>
            </span>
          </a>
          <ul class="treeview-menu">
         
             <!-- <li>
                <a href="<?php echo e(url('changepassword')); ?>"><i class="fa fa-circle-o"></i>
                  view</a>
              </li>-->
              <li>
                 <a href="<?php echo e(url('admin/retailer')); ?>"><i class="fa fa-circle-o"></i>
                 Create Retailer</a>
              </li>
			   <li><a href="<?php echo e(url('member/export')); ?>"><i class="fa fa-circle-o"></i>Export member</a></li>
             <!-- <li>
                <a href="<?php echo e(route('accountSetting')); ?>"><i class="fa fa-circle-o"></i>
                  Account Setting</a>
              </li>-->
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
              <a href="<?php echo e(route('all-transaction-reports')); ?>"><i class="fa fa-circle-o "></i>
                  Network Ledger</a>
            </li>
            <li>
              <a href="<?php echo e(route('dmt-reports')); ?>"><i class="fa fa-circle-o"></i>
                Network Usage</a>
            </li>
           
            
            <li>
              <a href="<?php echo e(route('dmt-one-report')); ?>"><i class="fa fa-circle-o"></i>
                A2Zwallet</a>
            </li>
            <li>
              <a href="<?php echo e(route('dmt-two-report')); ?>"><i class="fa fa-circle-o"></i>
                DMT1</a>
            </li>
            <li>
              <a href="<?php echo e(route('recharge-reports')); ?>"><i class="fa fa-circle-o"></i>
                Network Recharge</a>
            </li>
            <li>
              <a href="<?php echo e(url('account-statement')); ?>"><i class="fa fa-circle-o"></i>
                Account Statement</a>
            </li>
          <!-- <li>
              <a href="<?php echo e(route('all-transaction-reports')); ?>"><i class="fa fa-circle-o "></i>
                  Network Ledger Report</a>
              </li>-->
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
              <li><a href="<?php echo e(Route('fund-transfer')); ?>"><i class="fa fa-circle-o"></i>
               Fund  Transfer</a>
              </li>
			  <li>
                <a href="<?php echo e(url('payment-report')); ?>"><i class="fa fa-circle-o"></i>
                  Fund Transfer Report</a>
              </li>
              <li>
                <a href="<?php echo e(Route('bank-fund')); ?>"><i class="fa fa-circle-o"></i>
                 Payment Request</a>
              </li>
			  <li>
                <a href="<?php echo e(Route('payment-request-report')); ?>"><i class="fa fa-circle-o"></i>
                 Payment Request Report</a>
              </li>
			   <li>
                <a href="<?php echo e(url('payment-request-view')); ?>"><i class="fa fa-circle-o"></i>
                  Request View</a>
              </li>
			  <li>
                <a href="<?php echo e(url('fund-request-report')); ?>"><i class="fa fa-circle-o"></i>Request Report</a>
                <!--<a href="<?php echo e(url('retailer-payment')); ?>"><i class="fa fa-circle-o"></i>Request Report</a>-->
              </li>
          </ul>
        </li>
         <li class="treeview">
          <a href="#">
             <i class="fa fa-bullseye"></i>
            <span>Network Details</span>
            <span class="pull-right-container">
            <i class="fa fa-angle-left pull-right"></i>
            </span>
          </a>
          <ul class="treeview-menu">
              <li><a href="<?php echo e(Route('network-chain')); ?>"><i class="fa fa-circle-o"></i>
               Network Chain</a>
              </li>
              <li>
                <a href="<?php echo e(Route('network-search')); ?>"><i class="fa fa-circle-o"></i>
                Search Retailer</a>
              </li>
             <!--  <li>
                <a href="<?php echo e(route('view-network')); ?>"><i class="fa fa-circle-o"></i>
               View Network</a>
              </li> -->
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
			  <a href="<?php echo e(route('company-bank-details')); ?>">
			   <i class="fa fa-circle-o"></i>
			 Company 
			  </a>
            </li> 
            <li>
              <a href="<?php echo e(route('upper-level-bank-details')); ?>">
               <i class="fa fa-circle-o"></i>
                MD
              </a>
            </li>
            <li>
              <a href="<?php echo e(route('lower-level-bank-details')); ?>">
               <i class="fa fa-circle-o"></i>
                Add Bank 
              </a>
            </li>
              
          </ul>
        </li>      
      </ul>
    </section>
  </aside>