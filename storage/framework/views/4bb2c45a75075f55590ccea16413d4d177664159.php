<?php $__env->startSection('content'); ?>
<style>
.bootstrap-table{
    margin-top: -28px;
}
</style>
<script type="text/javascript">
	$(document).ready(function () {
        $.noConflict();
        $('.customDatepicker').datepicker({
              autoclose: true,  
            format: "dd-mm-yyyy"
        });

    }); 
</script>
<div class="col-sm-12">
    <div class="col-lg-6 col-md-6">
        <h4 class="page-title" style="color: black; "><?php echo e('Api Wise Report'); ?></h4>  
    </div>
</div>		
<div class="panel panel-default">
	<div class="panel-body">	
		<div class="col-lg-9 col-md-9">
			<form method="get" action="<?php echo e(Request::url()); ?>" onSubmit="return validateExportForm()" class="form-inline">
				<div class="form-group">
					<input name="fromdate" class="form-control customDatepicker" type="text" value="<?php echo e((app('request')->input('fromdate')) ? app('request')->input('fromdate') : date('d-m-Y')); ?>">
				</div>
				<div class="form-group">
					<input name="todate" class="form-control customDatepicker" type="text" value="<?php echo e((app('request')->input('todate')) ? app('request')->input('todate') : date('d-m-Y')); ?>">
				</div>
				<button type="submit" value="SEARCH" name="export" class="btn btn-primary btn-md"><span class="glyphicon glyphicon-find"></span>Search</button> 
				
				<a href="<?php echo e(Request::url()); ?>"/ class="btn btn-info  btn-md">Reset
				</a>
			</form>
		</div>
	</div>
</div>
<br>
<div class="row">
    <div class="">
       <div class="" style="overflow-x:auto; width:100%">
			<div class="panel-body">
  				 	<div class="col-md-12 faq-desc-item" style="font-size: 16px; font-family: time;">
						<?php $__currentLoopData = $all_reports; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $values): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
							<div class="col-md-12 flip-container text-center" style="padding: 1%;"> 
								<p style=" background: #5bc0de;color: black;font-size: 16px;font-family: time;"><?php echo e($key); ?></p>
									<?php $__currentLoopData = $values; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $d_key=>$d_value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
										<div class="col-md-2 front" style="background: #ececec;padding: 2%;border: 1px solid;border-radius: 23%;">
										<?php $style='';?>
											<?php if($d_key=="Pending"): ?>
												<?php $style="background: yellow;color: black;";?>
											<?php elseif($d_key=="Success"): ?>
												<?php $style="background: green;color: white;";?>
											<?php elseif($d_key=="Failure"): ?>
												<?php $style="background: red;color: white;";?>
											<?php elseif($d_key=="PtxnCredit"): ?>
												<?php $style="background: blue;color: white;";?>
											<?php elseif($d_key=="RefundSuc"): ?>
												<?php $style="background: #EE82EE;color: white;";?>
											<?php elseif($d_key=="RefundAvailable"): ?>
												<?php $style="background: #800000;color: white;";?>
											<?php elseif($d_key=="Refunded"): ?>
												<?php $style="background: #F20056;color: white;";?>
											<?php elseif($d_key=="Successfull"): ?>
												<?php $style="background:green;color:white";?>	
											<?php endif; ?>
											<p style="<?php echo e($style); ?>" class="pStyle"><?php echo e($d_key); ?></p>
											<?php $__currentLoopData = $d_value; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p_key=>$p_value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
												<p><span><?php echo e($p_key); ?></span> : <?php echo e($p_value); ?></p>
											<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
										</div>	
									<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
							</div>
                    
						<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
					</div>
                </div>
        </div>
    </div>
</div>
   

     
<?php $__env->stopSection(); ?>
<?php echo $__env->make('admin.layouts.templatetable', \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>