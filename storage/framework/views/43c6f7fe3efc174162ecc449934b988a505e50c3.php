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
<div class="row">
	   <?php echo $__env->make('search.only-search-with-export', \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>
</div>
<div class="box" style="overflow-x: scroll;">
	<table class="table table-bordered" id="example2">
			<thead>
				
					<th> Date</th>
					<th>ID</th>
					<th>Request To</th>
					<th>Bank Name</th>
					<th>Mode</th>
					<th>Branch Code</th>
					<th>Deposit Date</th>
					<th>Amount</th>
					<th>Deposit Slip</th>
					<th>Customer Remark</th>
					<th>Ref Id</th>
					<th>Status</th>
					<th>Remark</th>
					<th>Updated Reamrk</th>
					<th>Action</th>
				
			</thead>
			<tbody>
			<?php $__currentLoopData = $loadcashes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $loadcash): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
			<tr>
				<?php $s = $loadcash->created_at;
				$dt = new DateTime($s);?>
				<td><?php echo e($dt->format('d/m/Y')); ?><br><?php echo e($dt->format('H:i:s')); ?></td>
				<td><?php echo e($loadcash->id); ?></td>
				<td><?php echo e(($loadcash->request_to == 1) ? (Auth::user()->parent->name .'( '.Auth::user()->parent->prefix . ' - ' .Auth::user()->parent->id .')'): Auth::user()->company->company_name); ?></td>
				<td><?php echo e(($loadcash->request_to == 1) ? @$loadcash->bank_name :(@$loadcash->netbank->bank_name .':'. @$loadcash->netbank->bank_name)); ?></td>
				<td><?php echo e($loadcash->payment_mode); ?></td>
				<td><?php echo e($loadcash->loc_batch_code); ?></td>
				<td><?php echo e($loadcash->deposit_date); ?></td>
				<td><?php echo e($loadcash->amount); ?></td>
				<td>
					 <?php if( $loadcash->d_picture ): ?>
					 <a target="_blank" href="<?php echo e(url('deposit_slip/images')); ?>/<?php echo e($loadcash->d_picture); ?>"><img src="<?php echo e(url('deposit_slip/images')); ?>/<?php echo e($loadcash->d_picture); ?>" height="60px" width="60px"></a>
					 <?php else: ?> <?php echo e('No Slip'); ?>

					 <?php endif; ?>
				</td>
				<td><?php echo e($loadcash->request_remark); ?></td>
				<td><?php echo e($loadcash->bankref); ?></td>
				<td><?php echo e(@$loadcash->status->status); ?></td>
				<td><?php echo e(@$loadcash->remark->remark); ?></td>
				<td><?php echo e(@$loadcash->report->remark); ?></td>
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

	 </div>                             

 <meta name="_token" content="<?php echo csrf_token(); ?>"/>              
<?php $__env->stopSection(); ?>
<?php echo $__env->make('admin.layouts.templatetable', \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>