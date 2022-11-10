<?php $__env->startSection('content'); ?>
<?php echo $__env->make('search.date-search-only', \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>

<div class="row">
    <table id="example2" class="table table-bordered ">
        <thead>
			<tr>
				<th>Agent Name/Agent Id</th>
				<th>Member Type</th>
				<th>Txn Count</th>
				<th>Success Txn Amount</th>
				<th>Txn Charge</th>
				<th>Txn Commission</th>                    
			</tr>
        </thead>
        <tbody>
        <?php $__currentLoopData = $reports; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <tr>
                <td><?php echo e(@$value->user->name); ?><br> (R <?php echo e(@$value->user->id); ?>)</td>
                <td><?php echo e($value->user->role->role_title); ?></td>
				<td><?php echo e(@$value->txn_count); ?></td>
                <td><?php echo e(@$value->total_sales); ?></td>
                <td><?php echo e(number_format(@$value->txn_charge,2)); ?></td>
                <td><?php echo e(number_format(@$value->txn_commission,2)); ?></td>
                            
            </tr>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </tbody>
    </table>
    <?php echo $reports->links(); ?>

</div>
   
    <meta name="_token" content="<?php echo csrf_token(); ?>"/>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('admin.layouts.templatetable', \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>