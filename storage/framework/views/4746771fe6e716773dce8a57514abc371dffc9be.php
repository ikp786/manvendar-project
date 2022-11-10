

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
        <h4 class="page-title" style="color: black; "><?php echo e('Operator Wise Report'); ?></h4>  
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
				<button type="submit" value="SEARCH" name="export" class="btn btn-primary btn-md"><i class="fa fa-search"></i></button> 
				
				<a href="<?php echo e(Request::url()); ?>"/ class="btn btn-info  btn-md"><i class="fa fa-refresh"></i>
				</a>
			</form>
		</div>
	</div>
</div>
<div class="box">
	<table class="table table-bordered">
		<thead>
			<th>Operator Name</th>
			<th>Txn Count</th>
			<th>Amount</th>
			<th>Txn Charge</th>
			<th>Txn Commission</th>
		</thead>
		<tbody>
			<?php $__currentLoopData = $reports; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
			   <tr>
			   		<td><?php echo e($value->provider->provider_name); ?></td>
			   		<td><?php echo e($value->txnCount); ?></td>
			   		<td><?php echo e($value->txnAmount); ?></td>
			   		<td><?php echo e($value->debitCharge); ?></td>
			   		<td><?php echo e($value->txnCommission); ?></td>
			   </tr>
			<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
		</tbody>
		
	</table>
</div>


     
<?php $__env->stopSection(); ?>
<?php echo $__env->make('admin.layouts.templatetable', \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>