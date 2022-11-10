<?php $__env->startSection('content'); ?>

    
<div class="row">
        <div class="col-sm-12">
            <div class="card-box">
                <table class="table">
                    <thead>
                    <tr>						
						<th data-field="id" data-sortable="true">ID </th>
						<th>Bank Owner Name</th>                       
                        <th>Bank Name</th>
                        <th>Account Number</th>
                        <th>IFSC</th>
                        <th>Branch</th>
                        <th>OUT-LET NAME</th>
                        <th>TYPE</th>                       
                    </tr>
                    </thead>
                    <tbody>
					
                    <?php $__currentLoopData = $bankDetails; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $bankDetail): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>		
                        <tr>
                            <td><?php echo e($bankDetail->id); ?></td>
							 <td><?php echo e($bankDetail->user->name); ?>(<?php echo e($bankDetail->user->mobile); ?>)</td>
                            
                            <td><?php echo e($bankDetail->bank_name); ?></td>
                            <td><?php echo e($bankDetail->account_number); ?></td>
                            <td><?php echo e($bankDetail->ifsc_code); ?></td>
                            <td><?php echo e($bankDetail->branch_name); ?></td>
                            <td><?php echo e($bankDetail->message_one); ?></td>
                            <td><?php echo e($bankDetail->message_two); ?></td>
                           
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>
                
            </div>
        </div>
    </div>
<h4 style="color:red">Note:Before deposit the money to his Distributor account once confirm him. Company is not responsible for any wrong details. </h4>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>