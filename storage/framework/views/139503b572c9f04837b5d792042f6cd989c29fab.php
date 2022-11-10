<?php $__env->startSection('content'); ?>

    <script type="text/javascript">
	
	 
	
		
    </script>

					  
	<form method="get" action="<?php echo e(Request::url()); ?>" onSubmit="return checkValidation();" class="form-inline">
		<div class="form-group">
			<?php echo e(Form::select('searchType', ['MOB' => 'Mobile','AgentId'=>'AgentId'], app('request')->input('searchType'), ['class'=>'form-control','id'=>'searchType','placeholder'=>'--Select Search Creteria--','required'=>'required'])); ?>

		</div>
		<div class="form-group">
			<input type="text" id="content" value="<?php echo e(app('request')->input('content')); ?>" class="form-control" placeholder="Search Here" name="content" required>
		</div>
		<div class="form-group">
			<button type="submit" value="Submit" class="btn btn-primary">Submit</button>
		</div>
		<div class="form-group">
			<a href="<?php echo e(Request::url()); ?>" class="btn btn-info  btn-md">
				<i class="fa fa-refresh"></i>
			</a>  
		</div>
	</form>
	<div style="overflow-y: scroll; max-height:430px">
        <h3 id="totalCount" style="text-align: center;font-family: time;"></h3>
            <table id="mytable" class="table table-bordered ">
                <thead>
                    <tr>
						<th>Date/Time </th>
						<th>ID </th>
                        <th>Parent Name</th>
                        <th>Agent id</th>
                        <th>Agent Name </th>
                        <th>Mobile</th>                      
                        <th>Pan Number</th>
                        <th>Aadhaar Number</th>
                        <th>Merchange Login Id </th>
                        <th>Merchange Login Pin </th>
                        <th>Status </th>
					</tr>
                </thead>
                    <tbody id="memberTbody">
					
                    <?php $__currentLoopData = $apiAepsUser; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
						
                        <tr>
							<td><?php echo e(date("d-m-Y",strtotime($user->created_at))); ?></td>
							<td><?php echo e($user->id); ?></td>
							<td><?php echo e(@$user->user->name); ?></td>
							<td><?php echo e(@$user->agent_id); ?></td>
							<td><?php echo e(@$user->agent_name); ?></td>
							<td><?php echo e(@$user->mobile); ?></td>
							<td><?php echo e(@$user->pan_number); ?></td>
							<td><?php echo e(@$user->aadhaar_number); ?></td>
							<td><?php echo e(@$user->merchant_login_id); ?></td>
							<td><?php echo e(@$user->merchant_login_pin); ?></td>
							<td><?php echo e(($user->status_id==1) ? "Active" :"Pending"); ?></td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>
                <?php echo e($apiAepsUser->links()); ?> 
	</div>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('admin.layouts.templatetable', \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>