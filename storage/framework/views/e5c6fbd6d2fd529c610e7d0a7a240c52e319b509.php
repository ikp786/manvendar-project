<?php $__env->startSection('content'); ?>
<div  class="" style="overflow-y: scroll; max-height:500px">
<div class="box" >
<input id="myInput" type="text" placeholder="Search.." class="pull-right">
	<table class="table table-bordered" id="example2">
		<thead>
		  <tr>
			<th>ID</th>
			<th>Name</th>
			<th>Mobile</th>
			<th>Email</th>
			<th>Member Type</th>
			<th>Login Count</th>
			<th>OTP</th>
			<th>Last Login Ip</th>
		 </tr>
		</thead>
		<tbody id="myTable">
			<?php $__currentLoopData = $usersOtp; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
			<tr>
				<td><?php echo e($user->prefix); ?> <?php echo e($user->id); ?> 
				<br><span style="color:darkgoldenrod"><?php echo e($user->member->company); ?></span></td>
				<td><?php echo e($user->name); ?></td>
				<td><?php echo e($user->mobile); ?></td>
				<td><?php echo e($user->email); ?></td>
				<td><?php echo e(@$user->role->role_title); ?></td>
				<td><?php echo e($user->total_logins); ?></td>
				<td><?php echo e($user->otp_number); ?></td>
				<td><?php echo e($user->last_login_ip); ?></td>
				
			</tr>
			<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
		</tbody>
	</table>
	
</div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('admin.layouts.templatetable', \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>