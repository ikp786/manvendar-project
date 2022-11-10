<?php $__env->startSection('content'); ?>
<script>
/*for date->Calender*/

    $(document).ready(function () {
        $.noConflict();
        $('.customDatepicker').datepicker({
              autoclose: true,  
            format: "dd-mm-yyyy"
        });

    }); 
	function cancelFundRequest(recordId)
{
	if(confirm("Are you sure want to cancel fund Request"))
	{
		
		var dataString = 'recordId=' + recordId;
		$.ajaxSetup({
			headers: {
			'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
			}
		})
		$.ajax({
			type: "post",
			url: "<?php echo e(url('cancel-fund-request')); ?>",
			data: dataString,
			success: function (data) {
				alert(data.message);
				location.reload();
			   
			}
		})
	}
}
</script>
<?php echo $__env->make('agent.fund.fund-type', \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>
<br>
<div class="row">

		   <form method="get" action="<?php echo e(route('fund-req-report')); ?>" class="form-inline">
				<div class="form-group">
					<input name="fromdate" class="form-control customDatepicker" type="text" placeholder="From date">
				</div>
				<div class="form-group">
					<input name="todate" class="form-control customDatepicker" type="text" placeholder="To date">
				</div>
				<div class="form-group">
					<button name="export" value="Fund Request Reports" type="submit"
							class="btn btn-success waves-effect waves-light m-l-10 btn-md"><span
								class="glyphicon glyphicon-find"></span><i class="fa fa-file-excel-o" aria-hidden="true"></i>
					</button>
					
				</div>
				
			</form>
</div>
<br>
	<table class="table table-bordered">
					<thead>
						<tr>
							<th> Date</th>
							<th>ID</th>
							<th>Bank Name</th>
							<th>Mode</th>
							<th>Branch Code</th>
							<th>Deposit Date</th>
							<th>Amount</th>
							<th>Customer Remark</th>
							<th>Ref Id</th>
							<th>Status</th>
							<th>Remark</th>
							<th>Action</th>
						</tr>
					</thead>
					<tbody>
				<?php $__currentLoopData = $loadcashes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $loadcash): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
					<tr class="<?php echo e($loadcash->status->status); ?>-text">
						<?php $s = $loadcash->created_at;
						$dt = new DateTime($s);?>
						<td><?php echo e($dt->format('d/m/Y')); ?><br><?php echo e($dt->format('H:i:s')); ?></td>
						<td><?php echo e($loadcash->id); ?></td>
						<td><?php echo e(($loadcash->request_to == 1) ? @$loadcash->bank_name :@$loadcash->netbank->bank_name); ?></td>
						<td><?php echo e($loadcash->payment_mode); ?></td>
						<td><?php echo e($loadcash->loc_batch_code); ?></td>
						<td><?php echo e($loadcash->deposit_date); ?></td>
						<td><?php echo e($loadcash->amount); ?></td>
						<td><?php echo e($loadcash->request_remark); ?></td>
						<td><?php echo e($loadcash->bankref); ?></td>
						<td><?php echo e(@$loadcash->status->status); ?></td>
						<td><?php echo e(@$loadcash->remark->remark); ?></td> 
						<td>
							<?php if($loadcash->status_id == 3): ?>
								<a onclick="cancelFundRequest(<?php echo e($loadcash->id); ?>)" href="javascript:void(0)" class="table-action-btn"><i class="fa fa-close" style="font-size:35px;color:red"></i></a>
							<?php endif; ?>
                        </td>
					</tr>
				<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
			</tbody>
		</table>
		<?php echo $loadcashes->links(); ?>

	                              

 <meta name="_token" content="<?php echo csrf_token(); ?>"/>              
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>