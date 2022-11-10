<?php $__env->startSection('content'); ?>
<?php if(in_array(Auth::user()->role_id,array(1,3,4))): ?>
<div class="col-md-8">

	<form method="get" action="<?php echo e(Request::url()); ?>"  class="form-inline col-md-12">
		<div class="form-group row">
			<input type="text" name="mobile" placeholder="Mobile Number" class='form-control'/>
			<button name="export" value="SEARCH" type="submit" class="btn btn-primary btn-md"></span>Search</button>
			<a href="<?php echo e(Request::url()); ?>" class="btn btn-info btn-md">Reset</a>
		</div> 
		<?php if(Auth::user()->role_id == 3): ?>
		<div class="form-group row">
			<?php echo e(Form::select('searchOf', ['4' => 'Distributor', '5' => 'Retailer'], null, ['class'=>'form-control','placeholder'=>"--Select--"])); ?>

			<?php elseif(Auth::user()->role_id==1): ?>
			<?php echo e(Form::select('searchOf', ['3' => 'Master Dist','4' => 'Distributor', '5' => 'Retailer'], null, ['class'=>'form-control','placeholder'=>"--Select--"])); ?>

			<?php else: ?>
			<input type="hidden" name="searchOf" value="5"/>
		</div>
		<?php endif; ?>
		
	</form>
</div>
<?php endif; ?>
<div class="table-responsive"  style="overflow-y: scroll; max-height:900px">
            <table class="table table-bordered table-hover" id="example2">
                    <thead style="color: black">
                        <tr>
                            <th>Name</th>
                            <th>Mobile Number</th>
							<th>Email</th>
							<th>Status</th>
							<th>Role</th>
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