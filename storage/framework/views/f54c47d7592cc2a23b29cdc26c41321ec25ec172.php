<?php $__env->startSection('content'); ?>
<?php echo $__env->make('search.date-search-export', \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>
<div class="box">
	<table id="example2" class="table table-bordered">
	    <thead>
			<tr>
				 <th >Date/Time </th>
				<th>ID</th>
				<th>User </th>
				<th>Product </th>
				<th>Bank Name </th>
				<th>Name</th>
				<th>Acc/Mobile Number</th>
				<th>Tx ID</th>
				<th>Description</th>
				<th>Opening bal</th>
				<th>Amount</th>
				<th>Credit</th>
				<th>Debit</th>
				<th>Balance</th>
				<th>Remark</th>
				<th>Status</th>
			</tr>
		</thead>

		<tbody>
		<?php $__currentLoopData = $reports; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $report): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
			<tr class="<?php echo e($report->status->status); ?>-text">
				<td><?php echo e(date("d/m/Y H:i:s",strtotime($report->created_at))); ?><br></td>
				
				<td><?php echo e($report->id); ?></td>
				<td><?php echo e($report->user->name); ?>(<?php echo e($report->user->prefix); ?><?php echo e($report->user->id); ?> <?php echo e($report->user->mobile); ?>)</td>
				<td><?php echo e(@$report->api->api_name); ?>

				<td>
					<?php if($report->recharge_type ==1): ?>
						<?php echo e(@$report->provider->provider_name); ?>

					<?php else: ?>
					<?php echo e(@$report->beneficiary->bank_name); ?>

					<?php endif; ?>
				</td>
				
				 <td><?php if(is_numeric($report->credit_by)): ?>
							<?php echo e(@$report->creditBy->name); ?> (<?php echo e((@$report->creditBy->role_id == 4) ? "D - " : "R - "); ?> <?php echo e($report->credit_by); ?>)
						<?php else: ?>
							<?php echo e(@$report->credit_by); ?>

						<?php endif; ?> </td>
		  
				<td><?php echo e($report->number); ?></td>
				<td><?php echo e($report->txnid); ?></td>
				<td> <?php echo e($report->description); ?></td>
				<td><?php echo e(number_format($report->opening_balance,2)); ?></td>
				<td><?php echo e(number_format($report->amount,2)); ?></td>
				<td><?php echo e(number_format($report->credit_charge,2)); ?></td>
				<td><?php echo e(number_format($report->debit_charge,2)); ?></td>
				<td><?php echo e(number_format($report->total_balance,2)); ?></td>
				<td> <?php echo e($report->remark); ?></td>
				<td><?php echo e(@$report->status->status); ?></td>
				
			</tr>
		<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

		</tbody>
	</table>
	<?php echo $reports->links(); ?>

</div>
        
    <meta name="_token" content="<?php echo csrf_token(); ?>"/>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('admin.layouts.templatetable', \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>