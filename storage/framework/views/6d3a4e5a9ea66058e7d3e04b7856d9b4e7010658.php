<?php $__env->startSection('content'); ?>
<div class="table-responsive"  style="overflow-y: scroll; max-height:430px">
            <table class="table table-bordered table-hover" id="example2">
                    <thead style="color: black">
                        <tr>
                            <th>Name</th>
                            <th>Mobile Number</th>
							<th>Email</th>
							<th>Role</th>
							<th>Status</th>
							<th>Active</th>
                            
                            
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr>
                        	<td><?php echo e($user->name); ?></td>
                        	<td><?php echo e($user->mobile); ?></td>
							<td><?php echo e($user->email); ?></td>
							<td><?php echo e($user->role->role_title); ?></td>
                        	<td><?php echo e(($user->status_id) ? "Active" : "In-active"); ?></td>
							<td>
								<?php if(in_array(Auth::user()->role_id,array(1,3))): ?>
								<a href="<?php echo e(route('network-chain')); ?>/<?php echo e($user->id); ?>" class="btn btn-outline-secondary">View Chain</a>
								<?php endif; ?>
							</td>
                        	
                        </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
            </table>
</div>            



<?php $__env->stopSection(); ?>

<?php echo $__env->make('admin.layouts.templatetable', \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>