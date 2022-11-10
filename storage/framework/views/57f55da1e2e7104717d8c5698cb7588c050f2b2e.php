<?php $__env->startSection('content'); ?>

<?php echo $__env->make('partials.tab', \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>

<?php echo $__env->make('agent.fund.fund-type', \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>
<script>
/*for date->Calender*/

    $(document).ready(function () {
        $.noConflict();
        $('.customDatepicker').datepicker({
              autoclose: true,  
            format: "dd-mm-yyyy"
        });

    }); 
</script>
  <br>
    <div class="panel panel-default">
		<div class="panel-body">
			<div class="col-lg-3 col-md-3">
				<h4 class="page-title" style="color: black; "><?php echo e('Fund Request Summary'); ?></h4>
			</div>
			<div class="col-lg-9 col-md-9">
				<form method="get" action="<?php echo e(Request::url()); ?>" class="form-inline">
					<div class="form-group">
						<input name="fromdate" class="form-control customDatepicker" type="text" value="<?php echo e((app('request')->input('fromdate')) ? app('request')->input('fromdate') : date('d-m-Y')); ?>">
					</div>
					<div class="form-group">
						<input name="todate" class="form-control customDatepicker" type="text" value="<?php echo e((app('request')->input('todate')) ? app('request')->input('todate') : date('d-m-Y')); ?>">
					</div>
					<button type="submit" value="SEARCH" name="export" class="btn btn-primary  btn-md"><span class="glyphicon glyphicon-find"></span><i class="fa fa-search"></i>
					</button> 
					<button name="export" value="EXPORT" type="submit" class="btn btn-basic btn-md"><i class="fa fa-file-excel-o" aria-hidden="true"></i></button>
					<a href="<?php echo e(Request::url()); ?>"/ class="btn btn-info  btn-md"><i class="fa fa-refresh"></i>
					</a>
				</form>
			</div>
		</div>
	</div>
		
	<meta name="viewport" content="width=device-width, initial-scale=1">
<style>
table {
  border-collapse: collapse;
  border-spacing: 0;
  width: 100%;
  border: 1px solid #ddd;
}

th, td {
  text-align: left;
  padding: 8px;
}

tr:nth-child(even){background-color: #f2f2f2}
</style>
				<table id="example2"  class="table table-bordered" >
					<thead>	
						<tr>
							<th>Date/Time</th>
							<th>ID</th>
							<th>User</th>
							<th>Deposite Date</th>
							<th>Bank Name</th>
							<th>Wallet Amount</th>
							<th>Request For</th>
							<th>Bank Ref</th>
							<th>Payment Mode</th>
							<th>Branch Name</th>
							<th>Request remark</th>
							<th>Approval remark</th>
							<th>Updated remark</th>
							<th>Status</th>

						</tr>
                    </thead>
                    	<?php $__currentLoopData = $reports; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php $s = $value->created_at;
						$dt = new DateTime($s);?>
						<tr style="background-color:white">
						  <td><?php echo e($dt->format('d-m-y')); ?><br><?php echo e($dt->format('H:i:s')); ?></td>
                        	<td><?php echo e($value->id); ?></td>
							<td><?php echo e(@$value->user->name); ?></td>
							<td><?php echo e(@$value->deposit_date); ?></td>
							<td>
								<?php if(in_array($value->request_to,array(2))): ?>
									<?php echo e(@$value->netbank->bank_name); ?> : <?php echo e(@$value->netbank->account_number); ?>


								<?php else: ?>
									<?php echo e($value->bank_name); ?>

								<?php endif; ?>
							</td>
						
							<td><?php echo e($value->amount); ?></td>
							<td><?php echo e(($value->request_to == 3 && $value->borrow_type == 1)? "Take Borrow" :(($value->request_to == 3 && $value->borrow_type == 2)? "Pay Off":'')); ?></td>
							<td><?php echo e($value->bankref); ?></td>
							<td><?php echo e($value->payment_mode); ?></td>
							<td><?php echo e($value->loc_batch_code); ?></td>
							<td><?php echo e($value->request_remark); ?></td>
							<td><?php echo e(@$value->remark->remark); ?></td>
							<td><?php echo e(@$value->report->remark); ?></td>
							<td><?php echo e(@$value->status->status); ?></td>
							
						</tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                   
                </table>
              <?php echo $reports->appends(Request::all())->links(); ?>

           
   <meta name="_token" content="<?php echo csrf_token(); ?>"/> 
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>