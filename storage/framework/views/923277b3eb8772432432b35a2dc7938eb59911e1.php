<?php $__env->startSection('content'); ?>
<style>
.loaderImage {
    width: 50px;
    height: 50px;
}
</style>
<script type="text/javascript">
	function recharge_pay() {
		var ca_number = $("#ca_number").val();
		var provider = $("#provider").val();
		var amount = $("#amount").val();
		
		if(ca_number =='')
			{
				alert("Please enter valid CA Number");
				return false;
			}
			else if(amount =='')
			{
				alert("Please enter amount");
				return false;
			}

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                }
            })
            if (confirm('Are you sure you want to Recharge Amount '+ amount +'?')) {
            var dataString = 'number=' + ca_number + '&provider=' + provider + '&amount=' + amount;
            $.ajax({
                type: "POST",
                url: "<?php echo e(url('store-bbps')); ?>",
                data: dataString,
				beforeSend:function()
				{
					$("#recharge_button").prop('disabled',true);
					$("#recharge_button").hide();
					$("#billPayLoader").show();
				},
                success: function (msg) {
					$("#recharge_button").show();
					$("#billPayLoader").hide();
               		$('#recharge_button').prop('disabled',false);
                    $("#ca_number").val('');
                    $("#provider").val('');
                    $("#amount").val('');
                    $("#trbutto").prop("disabled", false);
                    $("#amount").val();
                    $("#mobilbtn").text("Pay Now");
                    if (msg.status == 'success') {
                     alert(msg.message);
					 refreshBalance();
                    } else {
                        alert(msg.message);
                        //window.location.reload();
                    }
                }
            });
            }
            else
            {
                $("#trbutto").attr("disabled", false);
                $("#trbutto").text("Pay Now");
            }
        }
</script>
                   
<?php echo $__env->make('agent.bbps.bbps-type', \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>


<div class="form-group col-md-3 ">
	<?php echo $__env->make('partials.message_error', \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>
	<div class="form-group">
		<label class="label" style="color:black">Operator</label>
		  <?php echo e(Form::select('provider', $provider, old('provider'), array('class' => 'form-control','id'=>'provider'))); ?>

	</div>
    <div class="form-group">
		<label class="label" style="color:black">CA Number</label>
		<input value="" class="form-control" name="ca_number" placeholder="Enter valid CA Number" id ="ca_number">	
	</div>
	<div class="form-group">
		<label class="label" style="color:black">Amount</label>
		<input value="" class="form-control" name="amount" placeholder="Enter valid Amount" id="amount"></div>
	<div class="form-group">
		<button type="button" class="btn btn-success" id="recharge_button" onclick="recharge_pay()"
           value="add">Pay Now
		</button>
		 <img src="<?php echo e(url('/img/loader.gif')); ?>" id="billPayLoader" class="loaderImage" style="display:none"/>
	</div>
</div>

	
<div class="text-right">
	<a href="<?php echo e(route('recharge-txn-history')); ?>?service_id=<?php echo e(@$serviceId); ?>" > Transaction History</a>
 </div>
<div class="ex1" style="overflow-y: scroll">  
    <table id="tableTypeThree" class="table table-bordered table-hover">
        <thead>

            <tr>
			  <th align="center">Date/Time</th>
			  <th>ID </th>
			  <th>User</th>
			  <th>Txn ID </th>
			  <th >Provider</th>
			  <th>Number</th>
			  <th>Amount</th>
			  <th>Commission</th>
			   <th>Status</th>
			   <th>Action</th>
			   <th>Report</th>
            </tr>
        </thead>
        <tbody>
            <?php $__currentLoopData = $report; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $recharge_reports): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php $s = $recharge_reports->created_at;
                $dt = new DateTime($s);?>
            <tr class="odd gradeX" style="background-color:white">
              <td align="center"><?php echo e($dt->format('d/m/Y')); ?><br><?php echo e($dt->format('H:i:s')); ?></td>
              <td><?php echo e($recharge_reports->id); ?></td>
              <td><?php echo e($recharge_reports->user->name); ?></td>
              <td><?php echo e($recharge_reports->txnid); ?></td>
              <td><?php echo e(@$recharge_reports->provider->provider_name); ?></td>
              <td><?php echo e($recharge_reports->number); ?></td>
              <td><?php echo e($recharge_reports->amount); ?></td>
              <td><?php echo e($recharge_reports->profit); ?></td>
              <td> <?php echo e($recharge_reports->status->status); ?></td>
              <td><button id="re_check_<?php echo e($recharge_reports->id); ?>"onclick="this.disabled=true;recharge_status(<?php echo e($recharge_reports->id); ?>);" class="btn btn-primary">Check</button></td>
                <td style="text-align:center">
                  <?php if(in_array($recharge_reports->status_id,array(1,3,9))): ?>
                    <a target="_blank" href="<?php echo e(url('invoice')); ?>/<?php echo e($recharge_reports->id); ?>">
                        <span class="btn btn-success btn-xs" style="font-size: 14px;"><i class="md md-visibility"></i>Receipt</span>
                    </a>
                <?php endif; ?>
                </td>  
            </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </tbody>
    </table>
</div>
 <meta name="_token" content="<?php echo csrf_token(); ?>"/>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>