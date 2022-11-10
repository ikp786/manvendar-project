<?php $__env->startSection('content'); ?>
<div class="row">
        <div class="col-sm-12">
            <?php echo $__env->make('search.only-search-with-export', \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>
            <div class="table-responsive" >
				<table id="example2"  class="table table-bordered " >
                    <thead>
                    <tr>
					 <th data-field="date" data-sortable="true">&nbsp&nbsp&nbsp&nbspDate/Time &nbsp&nbsp&nbsp&nbsp&nbsp&nbsp </th>
					 
                        <th>Order ID</th>
                        <th>Wallet</th>
                        <th>User</th>
                        <th>Transfer To/From</th>
                        <th>Firm Name</th>
                        <th>txnId</th>
                        <th>Ref Id</th>
                        <th>Description</th>
						<th>Opening Bal</th>
                        <th>Amount</th>
                        <th>Closing Bal</th>
                         <th>Agent Remark</th>
                        <th data-field="status" data-align="center" data-sortable="true" data-formatter="statusFormatter">Status
                        </th>
                    </tr>
                    </thead>
                    <tbody>
					 <?php $count=$totalAmount=0;?>
                    <?php $__currentLoopData = $rtoRReports; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
					<?php $s = @$value->created_at;
						$dt = new DateTime($s);
						$u = @$value->payment->updated_at;
						$du = new DateTime($u);
						$r=@$value->payment->created_at;
						$dr = new DateTime($r);
						?>
                        <tr>
							<td><?php echo e($dt->format('d/m/Y')); ?><br><?php echo e($dt->format('H:i:s')); ?></td>
							
							            
							<td><?php echo e($value->id); ?></td>
                            <td><?php echo e(($value->recharge_type == 1) ? 'Recharge' : 'Money'); ?></td>
                            <td><?php echo e($value->user->name); ?>(<?php echo e($value->user->id); ?>)</td>
                            <td><?php if(is_numeric($value->credit_by)): ?>
								<?php echo e(@$value->creditBy->name); ?> (<?php echo e(@$value->creditBy->prefix); ?>-<?php echo e(@$value->creditBy->id); ?>)<br>
								<?php else: ?>
									<?php echo e(@$value->credit_by); ?>

								<?php endif; ?> 
							</td>                          
                            <td><?php if(is_numeric($value->credit_by)): ?>
							<?php echo e(@$value->creditBy->member->company); ?><?php endif; ?></td>
                            <td><?php echo e($value->txnid); ?></td>
                            <td><?php echo e($value->ref_id); ?></td>
                            <td><?php echo e($value->description); ?></td>
							<td><?php echo e(number_format($value->opening_balance,2)); ?></td>
                            <td><?php echo e(number_format($value->amount,2)); ?></td>
                            <td><?php echo e(number_format($value->total_balance,2)); ?></td>
							<td><?php echo e($value->remark); ?></td>
                            
							<td><?php echo e(@ $value->status->status); ?></td>
							<?php
							    $totalAmount +=	$value->amount;
                                $count++;
                            ?>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
					<h4 style="color:red">Total Amount(<?php echo e($count); ?>) : <?php echo e($totalAmount); ?></h4>
                </table>
              <?php echo e($rtoRReports->appends(\Input::except('page'))->render()); ?>

            </div>
        </div>
    </div>
   
<?php $__env->stopSection(); ?>
<?php echo $__env->make('admin.layouts.templatetable', \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>