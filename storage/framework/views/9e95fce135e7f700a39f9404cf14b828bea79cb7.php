<?php $__env->startSection('content'); ?>
<style>
.bootstrap-table{
    margin-top: -28px;
}
</style>
<script>
$(document).ready(function () {
        $.noConflict();
        $('.customDatepicker').datepicker({
            autoclose: true,  
            format: "dd-mm-yyyy"
        });
    }); 
	</script>
	<div class="panel panel-default">
<div class="panel-body">
            <div class="col-lg-6 col-md-6">
                <h4 class="page-title" style="color: black; "><?php echo e('Api Report'); ?></h4>
                
			</div>
	
			<div class="row col-md-6">
				<form method="get" action="<?php echo e(Request::url()); ?>" onSubmit="return validateExportForm()" class="form-inline">
                    <div class="form-group">
                        <input name="fromdate" class="form-control customDatepicker" type="text" value="<?php echo e((app('request')->input('fromdate')) ? app('request')->input('fromdate') : date('d-m-Y')); ?>" autocomplete="off"> 
                    </div>
                    <div class="form-group">
                        <input name="todate" class="form-control customDatepicker" type="text" value="<?php echo e((app('request')->input('todate')) ? app('request')->input('todate') : date('d-m-Y')); ?>" autocomplete="off">
                    </div>
                    
                  
                        <button name="export" value="SEARCH" type="submit" class="btn btn-primary btn-md"></span><i class="fa fa-search"></i></button>
                        
                      <a href="<?php echo e(Request::url()); ?>"/ class="btn btn-info  btn-md"><i class="fa fa-refresh"></i></a>
                      
                </form>
               
            </div>
            </div> 
			</div>
<div class="">
	<table  class="table table-bordered" >
		        <thead>
                    <tr>
						<th>Api Name</th>
						<th>Success</th>
						<th>Pending</th>
						<th>Failed</th>
                    </tr>
                </thead>
				<?php $successVolume = $pendingVolume = $failVolume =0;?>
                <tbody>
                    <?php $__currentLoopData = $newArray; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
						<tr>
						
							<td><?php echo e($key); ?></td>
							<td><?php echo e(@$value->SUCCESS); ?></td>
							<td><?php echo e(@$value->PENDING); ?></td>
							<td><?php echo e(@$value->FAILURE); ?></td>
							<?php 
								$successVolume += @$value->SUCCESS;
								$pendingVolume += @$value->PENDING;
								$failVolume += @$value->FAILURE;
								?>
						</tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
						<tr>
							<td>Total</td>
							<td><?php echo e(@$successVolume); ?></td>
							<td><?php echo e(@$pendingVolume); ?></td>
							<td><?php echo e(@$failVolume); ?></td>
						</tr>
                </tbody>
            </table>
                
        </div>
   
    <meta name="_token" content="<?php echo csrf_token(); ?>"/>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('admin.layouts.templatetable', \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>