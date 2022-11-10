<?php $__env->startSection('content'); ?>
<div class="col-sm-12">
	<div class="col-lg-6 col-md-6">
		<h4 class="page-title" style="color: black;"><?php echo e('Recharge Reports'); ?></h4>
	</div>
</div>

<?php echo $__env->make('search.re-search-with-type-status-export', \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>
              
<div class="box" style="overflow-y:scroll;max-height:450px">
	<table class="table table-bordered" id="example2">
	    <thead>
			<tr>
				<th>Date/Time</th>
				<th>ID</th> 
				<th>User </th> 
				<th>Counsumer No</th>
				<th> Provider</th>
				<th>Amount</th>
				<th>GST</th>
				<th>TDS</th>
				<th>Credit Amount</th>
				<th>Debit Amount</th>
				<th >Txn Type</th>
				<?php if(Auth::user()->role_id==1): ?>
				<th>Operator</th>
				<?php endif; ?>
				<th>Txn Id</th>
				<th>Op Id</th>
				<th>Status</th>
				<th>slip</th>
			</tr>
		</thead>
		<tbody>
			<?php $__currentLoopData = $reports; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $report): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
			<?php $s = $report->created_at;
				$dt = new DateTime($s);?>
				<tr class="<?php echo e(@$report->status->status); ?>-text">
					<td><?php echo e($dt->format('d/m/Y')); ?><br><?php echo e($dt->format('H:i:s')); ?></td>
					<td><?php echo e($report->id); ?></td>
					<td><?php echo e($report->user->name); ?>(<?php echo e($report->user_id); ?>)</td>
					<td><?php echo e(@$report->number); ?></td>	
					<td><?php echo e(@$report->provider->provider_name); ?></td>
					<td><?php echo e($report->amount); ?></td>
					<td><?php echo e($report->gst); ?></td>
					<td><?php echo e($report->tds); ?></td>
					<td><?php echo e($report->credit_charge); ?></td>
					<td><?php echo e($report->debit_charge); ?></td>
					<td><?php echo e($report->txn_type); ?></td>
					<?php if(Auth::user()->role_id==1): ?>
					<td><?php echo e(@$report->api->username); ?></td>
					<?php endif; ?>
					<td><?php echo e($report->txnid); ?></td>	
					<td><?php echo e($report->ref_id); ?></td>	
					<td><?php echo e(@$report->status->status); ?></td>
				</tr>
			<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
		</tbody>
	</table>
	<?php echo $reports->links(); ?>

</div>

<?php $__env->stopSection(); ?>
<?php echo $__env->make('admin.layouts.templatetable', \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>