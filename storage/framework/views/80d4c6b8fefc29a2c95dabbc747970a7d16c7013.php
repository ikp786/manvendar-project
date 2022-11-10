<?php $__env->startSection('content'); ?>
<?php echo $__env->make('agent.fund.fund-type', \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>
<script type="text/javascript">
    $(document).ready(function () {
        $.noConflict();
        $('.customDatepicker').datepicker({
              autoclose: true,  
            format: "dd-mm-yyyy"
        });
    }); 
</script>
<br>
 <div class="col-md-12">
    <form method="get" action="<?php echo e(Request::url()); ?>" class="form-inline" role="form">    
		<?php echo e(Form::select('noOfRecord',['1' =>'20','2' =>'40','3' =>'60'],app('request')->input('noOfRecord'), ['class'=>'form-control','placeholder'=>"--All--"])); ?>

        <input name="fromdate" class="form-control customDatepicker" type="text" placeholder="From date" value="<?php echo e((app('request')->input('fromdate')) ? app('request')->input('fromdate') : date('d-m-Y')); ?>">		<input name="todate" class="form-control customDatepicker" type="text" placeholder="To date" value="<?php echo e((app('request')->input('todate')) ? app('request')->input('todate') : date('d-m-Y')); ?>">
		<?php echo e(Form::select('searchOf',['6' => 'Debit','7' =>'Credit'],app('request')->input('searchOf'), ['class'=>'form-control','placeholder'=>"--Select--"])); ?>

        <button name="SEARCH" value="SEARCH" type="submit" class="btn btn-primary btn-md">
        <i class="fa fa-search"></i></button>
        <a href="<?php echo e(Request::url()); ?>" class="btn btn-info btn-md"><i class="fa fa-refresh"></i></a>
		<button name="export" value="EXPORT" type="submit" class="btn btn-basic"><i class="fa fa-file-excel-o" aria-hidden="true"></i></button>
		
    </form>
</div><br><br>
<div class="box">
    <table id="example2" class="table table-bordered">
        <thead>
            <tr>
    		   <th>Date/Time</th>
                <th>Order ID</th>
                <th>Wallet</th>
                <th>User</th>
                <th>Transfer To/From</th>
                <th>Firm Name</th>
                <th>Ref Id</th>
                <th>Description</th>
                <th>Opening Bal</th>
                <th>Credit Amount</th>
                <th>Closing Bal</th>
                <th>Bank Charge</th>
    			<th>Remark</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
		<?php $totalAmount=$count=0;?>
        <?php $__currentLoopData = $reports; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
		<?php $s = $value->created_at;
			$dt = new DateTime($s);?>
            <tr>
			<td><?php echo e($dt->format('d/m/Y')); ?><br><?php echo e($dt->format('H:i:s')); ?></td>
                <td><?php echo e($value->id); ?>

				</td>
                <td><?php echo e(($value->recharge_type == 1) ? 'Recharge' : 'Money'); ?></td>
                <td><?php echo e($value->user->name); ?> (<?php echo e(@$value->user->prefix); ?>-<?php echo e($value->user->id); ?>)</td>
                <td><?php if(is_numeric($value->credit_by)): ?>							<?php echo e(@$value->creditBy->name); ?>(<?php echo e(@$value->creditBy->prefix); ?>-<?php echo e(@$value->creditBy->id); ?>)<br>
    			<?php else: ?>
    				<?php echo e(@$value->credit_by); ?>

    			<?php endif; ?> 
				</td>
                <td><?php if(is_numeric($value->credit_by)): ?><?php echo e(@$value->creditBy->member->company); ?><?php endif; ?></td>
                <td><?php echo e($value->txnid); ?></td>
                <td><?php echo e($value->description); ?></td>
                <td><?php echo e(number_format($value->opening_balance,2)); ?></td>
                <td><?php echo e(number_format($value->amount,2)); ?>

                <td><?php echo e(number_format($value->total_balance,2)); ?></td>
                <td><?php echo e($value->bank_charge); ?></td>
				<td><?php echo e($value->remark); ?> </td>
                <td><?php echo e(@$value->status->status); ?></td>	
            </tr>
			 <?php 
			$totalAmount +=$value->amount;
			$count++;
			 ?>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </tbody>
		 <h4 style="color:red">Total Amount(<?php echo e($count); ?>) : <?php echo e(number_format($totalAmount,2)); ?></h4>
    </table>
  <?php echo e($reports->appends(\Input::except('page'))->render()); ?>

</td>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>