<?php $__env->startSection('content'); ?>
<?php echo $__env->make('search.date-search-export', \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>
<div class="">
	<table  class="table table-bordered" >
		        <thead>
                    <tr>
						<th>Provider Name</th>
						<th>Success</th>
						<th>Pending</th>
						<th>Failed</th>
						<th>Manul Success</th>
                    </tr>
                </thead>
				<?php $successVolume = $pendingVolume = $failVolume =$refundVolume = 0;?>
                <tbody>
                    <?php $__currentLoopData = $newArray; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
						<tr>
						
							<td><?php echo e($key); ?></td>
							<td><?php echo e(@$value->SUCCESS); ?></td>
							<td><?php echo e(@$value->PENDING); ?></td>
							<td><?php echo e(@$value->FAILURE); ?></td>
							<td><?php echo e(@$value->MANUALSUCCESS); ?></td>
							<?php 
								$successVolume += @$value->SUCCESS;
								$pendingVolume += @$value->PENDING;
								$failVolume += @$value->FAILURE;
								$refundVolume += @$value->MANUALSUCCESS;
								?>
						</tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
						<tr>
							<td>Total</td>
							<td><?php echo e(@$successVolume); ?></td>
							<td><?php echo e(@$pendingVolume); ?></td>
							<td><?php echo e(@$failVolume); ?></td>
							<td><?php echo e(@$refundVolume); ?></td>
						</tr>
                </tbody>
            </table>
                
        </div>
   
    <meta name="_token" content="<?php echo csrf_token(); ?>"/>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('admin.layouts.templatetable', \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>