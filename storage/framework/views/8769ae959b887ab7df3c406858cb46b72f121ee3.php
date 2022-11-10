 <aside class="main-sidebar">
    <!-- sidebar: style can be found in sidebar.less -->
    <section class="sidebar">
      <!-- Sidebar user panel -->
      <div class="user-panel">
        <div class="pull-left image">
		<!-- <?php if(Auth::user()->profile->profile_picture): ?>
          <img src="<?php echo e(url('/user-uploaded-files')); ?>/<?php echo e(Auth::id()); ?>/<?php echo e(Auth::user()->profile->profile_picture); ?>" class="img-circle" >
          <?php endif; ?>-->
		  <a href="<?php echo e(url('dashboard')); ?>">
           <img src="<?php echo e(url('newlog/images/Logo168.png')); ?>" style="max-width:85% !important; height: 50%; !important"></a>
           
        </div>
        <div class="pull-left info">
         <!-- <p><?php echo e(Auth::user()->name); ?></p>-->
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
          <a href="<?php echo e(route('home')); ?>">
            <i class="fa fa-dashboard"></i><span>Dashboard</span>
          </a>
        </li> -->
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
             <i class="fa fa-mobile"></i>
            <span>Recharge</span>
            <span class="pull-right-container">
            <i class="fa fa-plus pull-right"></i>
            </span>
          </a>
          <ul class="treeview-menu">
            <li><a href="<?php echo e(route('recharge')); ?>"><i class="fa fa-circle-o"></i>Prepaid</a></li>
            <li><a href="<?php echo e(route('dth-recharge')); ?>"><i class="fa fa-circle-o"></i>DTH</a></li>
            <li><a href="<?php echo e(route('datacard-recharge')); ?>"><i class="fa fa-circle-o"></i>Data Card</a></li>
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
            <?php if(Auth::user()->member->paytm_my_wallet ==1): ?>  
                <li><a href="<?php echo e(route('my-wallet')); ?>"><i class="fa fa-circle-o"></i>A2Z Plus Wallet</a></li>
            <?php endif; ?>
            <?php if(Auth::user()->member->paytm_my_wallet ==0): ?>
            <li><a href="<?php echo e(route('premium-wallet')); ?>"><i class="fa fa-circle-o"></i>A2Z Wallet</a></li>
            <?php endif; ?>
            <li><a href="<?php echo e(route('money')); ?>"><i class="fa fa-circle-o"></i>DMT 1</a></li>
			<?php if(Auth::user()->member->dmt_three): ?>
           <li><a href="<?php echo e(route('dmt-two')); ?>"><i class="fa fa-circle-o"></i>DMT 2</a></li><?php endif; ?>
		   <li><a href="<?php echo e(url('account/search')); ?>"><i class="fa fa-circle-o"></i>Account Search</a></li>
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
            <li><a href="<?php echo e(route('bbps')); ?>"><i class="fa fa-circle-o"></i>Electricity</a></li>
            <li><a href="<?php echo e(route('bbps-postpaid')); ?>"><i class="fa fa-circle-o"></i>PostPaid</a></li>
            <li><a href="<?php echo e(route('bbps-broadband')); ?>"><i class="fa fa-circle-o"></i>BroadBand</a></li>
            <li><a href="<?php echo e(route('bbps-landline')); ?>"><i class="fa fa-circle-o"></i>LandLine</a></li>
            <li><a href="<?php echo e(route('bbps-water')); ?>"><i class="fa fa-circle-o"></i>Water</a></li>
            <li><a href="<?php echo e(route('bbps-gas')); ?>"><i class="fa fa-circle-o"></i>Gas</a></li>
            <li><a href="<?php echo e(route('bbps-insurance')); ?>"><i class="fa fa-circle-o"></i>Insurance</a></li>
			
          </ul>
        </li>


               
        <li class="treeview">
          <a href="#">
      <i class="fa fa-file"></i>
            <span>Report</span>
            <span class="pull-right-container">
        <i class="fa fa-plus pull-right"></i>
            </span>
          </a>
          <ul class="treeview-menu">
			<li><a href="<?php echo e(url('recharge-nework')); ?>"><i class="fa fa-circle-o"></i>Ledger Report</a></li>
            <li><a href="<?php echo e(route('summary_report')); ?>"><i class="fa fa-circle-o"></i>Usage Report</a></li>
			<li><a href="<?php echo e(route('businessview')); ?>"><i class="fa fa-circle-o"></i>Business View</a></li>
            

           <!--  <li><a href="<?php echo e(url('complain')); ?>"><i class="fa fa-circle-o"></i>View Complain</a></li> -->
          </ul>
        </li>

           
           <li class="treeview">
               <a href="#">
                   <i class="fa fa-file"></i>
                   <span>Funds</span>
					<span class="pull-right-container">
						<i class="fa fa-plus pull-right"></i>
					</span>
               </a>
               <ul class="treeview-menu">
                   <li><a href="<?php echo e(route('fund-request-summary')); ?>"><i class="fa fa-circle-o"></i>Fund Report</a></li>
                   <li><a href="<?php echo e(route('bank-fund')); ?>"><i class="fa fa-circle-o"></i>Fund Request</a></li>
                   <li><a href="<?php echo e(url('transfer/r-to-r')); ?>"><i class="fa fa-circle-o"></i>R-to-R Transfer</a></li>  
				   <li><a href="<?php echo e(route('dt-report')); ?>"><i class="fa fa-circle-o"></i>DT Report</a></li>
               </ul>
           </li>
		<li><a href="<?php echo e(url('aeps')); ?>">  <i class="fa fa-money"></i> Aeps</a></li>
		<?php if(Auth::user()->member->adhaar_pay ==1): ?>
		    <li><a href="<?php echo e(url('aeps-ap')); ?>"><i class="fa fa-money"></i> Adhaar Pay</a></li>
		<?php endif; ?>
		<li>
		  <a href="<?php echo e(route('company-bank-details')); ?>">
		   <i class="fa fa-bank"></i> 
			<span>Bank Detail</span>
		  </a>
        </li>
		
		<li>
		  <a href="<?php echo e(route('view-complain')); ?>">
		   <i class="fa fa-bank"></i> 
			<span>Complain List</span>
			
			
			    <btn id="complain_retailer_count" class="btn btn-warning btn-xs" style="margin-left: 15px; border-radius: 10px;" >0</btn>
            
		  </a>
        </li>
		<li>
		  <a href="<?php echo e(route('aeps_driver')); ?>">
		   <i class="fa fa-file-o"></i> 
		  <span>Aeps Kit</span>
		  </a>
		</li>
      </ul>
    </section>
    <!-- /.sidebar -->
  </aside>