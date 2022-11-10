<?php $__env->startSection('content'); ?>
<?php echo $__env->make('agent.money.money-type', \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>
	<div class="card-body">
		<?php echo Form::open(array('url' =>'account/search','id'=>'account-searcher','class'=>'form-inline')); ?>

		<div class="card-body">
			<div class="form-inline col-md-12">
			   <label for="inputTask" class="control-label ">Account Number<span style="color:red"> *</span></label>
				<input type="text" class="form-control has-error" id="accountNumber" name="accountNumber" placeholder="Enter Account Number" value="<?php echo e((app('request')->input('accountNumber')) ? app('request')->input('accountNumber') : ''); ?>">
				<button  type="submit" class="btn btn-info" id="btn-save" value="add"><i class="fa fa-search"></i></button>
			</div>
		</div>
		<?php echo Form::close(); ?>

		<table id="mytable" class="table table-bordered ">
			<thead>
				<tr>
					<th>Account Number </th>
					<th>Bene Name</th>
					<th>IFSC CODE</th>
					<th>Mobile Number </th>
					<th>Wallet </th>                       
				</tr>
			</thead>
			<tbody id="memberTbody">
				<?php $__empty_1 = true; $__currentLoopData = $accountDetails; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $accountdetail): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
				<tr>
					<td><?php echo e($accountdetail->account_number); ?></td>
					<td><?php echo e($accountdetail->name); ?></td>
					<td><?php echo e($accountdetail->ifsc); ?></td>
					<td><?php echo e($accountdetail->mobile_number); ?></td>
					<td><?php echo e(($accountdetail->api_id==4) ? "DMT1" :(($accountdetail->api_id==5) ? "A2Z Wallet" :'')); ?>

					</td>						
				</tr>
				<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
					No Records
				<?php endif; ?>
			</tbody>	
		</table>
	</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>