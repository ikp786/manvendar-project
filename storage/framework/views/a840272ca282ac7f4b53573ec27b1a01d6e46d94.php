	<style type="text/css">
		.col-sm-2 {
		 width: 10%; 
	}
	</style>
<?php if(Auth::user()->role_id==5): ?>
<div class="row">                      
    <div class="col-1 col-sm-2 col-md-2">
        <a href="<?php echo e(route('summary_report')); ?>" data-toggle="tooltip" data-placement="top" title="Usage Report">
        <span class="info-box-text textheader <?php echo e(Request::is('summary_report') ? 'active' : ''); ?>">Usage Report</span>
        </a>
    </div>   
    <div class="col-1 col-sm-2 col-md-2">
        <a href="<?php echo e(url('recharge-nework')); ?>" data-toggle="tooltip" data-placement="top" title="Usage Report">
        <span class="info-box-text textheader <?php echo e(Request::is('recharge-nework') ? 'active' : ''); ?>">Ledger Report</span> 
        </a>
    </div>
	
    <div class="col-1 col-sm-2 col-md-2">
        <a href="<?php echo e(route('businessview')); ?>" data-toggle="tooltip" data-placement="top" title="Business View">
        <span class="info-box-text textheader <?php echo e(Request::is('businessview') ? 'active' : ''); ?>">Business View</span> 
        </a>
    </div>

    <!-- <div class="col-3 col-sm-2 col-md-1">
        <a href="<?php echo e(url('view-commission')); ?>">
        <span class="info-box-text <?php echo e(Request::is('view-commission') ? 'active' : ''); ?>">View Commision</span> 
        </a>
    </div>  --> 
   <!--  <div class="col-3 col-sm-2 col-md-1">
        <a href="<?php echo e(url('complain')); ?>">
        <span class="info-box-text <?php echo e(Request::is('*complain') ? 'active' : ''); ?>">View Complain</span> 
        </a>
    </div>  --> 
        <!--<li class="<?php echo e(Request::is('*money_transfer_report') ? 'active' : ''); ?>">
			   <a href="<?php echo e(route('money_transfer_report')); ?>"><span>&nbsp; Sales Report</span></a>
			</li>-->
			<!--<li class="<?php echo e(Request::is('*all-recharge-report') ? 'active' : ''); ?>">
				<a href="<?php echo e(route('all-recharge-report')); ?>"><span>&nbsp; Recharge Report</span></a>
			</li>-->
			<!--<li class="<?php echo e(Request::is('*summary_report') ? 'active' : ''); ?>">
				<a href="<?php echo e(route('summary_report')); ?>"><span>&nbsp; Summary Report</span></a>
			</li>-->

			<!--<li class="<?php echo e(Request::is('*load-cash') ? 'active' : ''); ?>">
				<a href="<?php echo e(route('load-cash')); ?>"><span>&nbsp; Fund Request</span></a>
			</li>-->

			<!--<li class="<?php echo e(Request::is('*business-report') ? 'active' : ''); ?>">
				<a href="<?php echo e(url('business-report')); ?>"><span>&nbsp; Business View</span></a>
			</li>-->                        
</div><?php endif; ?>
<br>