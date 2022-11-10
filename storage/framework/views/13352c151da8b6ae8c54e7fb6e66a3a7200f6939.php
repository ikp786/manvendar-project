<?php $__env->startSection('content'); ?>
<style>
.textFormat{
	font-family: time;
}
</style>
<script type="text/javascript">
	function expand_textarea()
	{
		$('textarea.expand').keyup(function () {
		$(this).animate({ height: "4em", width: "13em" }, 500); 
		});
	}

    function transfernow(id) 
		{
			var amount = $("#amount").val();
            var remark = $("#remark").val();
            var channel = $("#channel").val();
			var numberFormat = /^[0-9]+$/;
			if(amount=='')
			{
				alert('Only Number is allowed');
				$('#amount').focus();
				return false;
			}
			else if(!amount.match(numberFormat))
			{
				alert("Please enter valid amount");
				$('#amount').focus();
				return false;
				
			}
			else if(amount<1)
			{
				alert('Miminum Amount should be Rs. 1');
				$('#amount').focus();
				return false;
			}
			
		if(confirm('Are you sure to transfer Amount : ' + amount))
		{          
            var dataString = 'amount=' + amount + '&remark='+ remark+ '&id='+ id+ '&channel='+ channel;
			$.ajaxSetup({
					headers: {
						'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
					}
				})
            $.ajax({
                type: "POST",
                url: "<?php echo e(url('aeps/settlement-request')); ?>",
                data: dataString,
                dataType: "json",
				beforeSend: function() {
						$("#trbutton").attr("disabled", false);
						$("#loader").show();
						$("#Sendnow").hide();
                    },
                success: function (data) 
				{
					$("#loader").hide();
                    $("#Sendnow").show();
					if (data.status == 1) {
						alert(data.message);
						location.reload();
					} 
					else{
						alert(data.message);
						location.reload();
					}
					           
				}
			});
        }
    }
	
</script>
<div class="super_container">
	<div class="home">
	</div>
	<div class="search">					
            
            <?php echo $__env->make('agent.aepsSettlement.aepsSettlement-type', \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>
            <br>
     
		<div class="row">
		    <div class="col-sm-12">
		        <div class="">
		            <table class="table table-bordered">
		                <thead style="">
		                    <tr>
								<th data-field="id" data-sortable="true">ID </th>
								<th>Account Holder Name</th>
		                        <th>Account Number<br>Bank Name</th>
		                        <th>IFSC Code<br>Branch Name</th>
		                        <th>Balance</th>
		                        <th>Minimum Avl Bal.</th>
		                        <th>Charge</th>
		                        <th>Amount</th>
		                         <th>Remark</th>
		                         <th>Payment Type</th>
		                        <th>Action</th>	
		                      
		                    </tr>
		                </thead>
		                <tbody>
		                <?php $__currentLoopData = $bankDetails; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $bankDetail): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>		
		                    <tr style="background-color:white">
		                        <td><?php echo e($bankDetail->id); ?></td>
		                        <td><?php echo e($bankDetail->name); ?></td>
		                        <td><?php echo e($bankDetail->account_number); ?><br><?php echo e($bankDetail->bank_name); ?></td>
		                        <td><?php echo e($bankDetail->ifsc); ?></br><?php echo e($bankDetail->branch_name); ?></td>
		                        <td><?php echo e(number_format($bankDetail->user->balance->user_balance,2)); ?></td>	
		                        <td><?php echo e(number_format(Auth::user()->member->aeps_blocked_amount,2)); ?></td>	
		                        <td><?php echo e(number_format(Auth::user()->member->aeps_charge,2)); ?></td>	
		                        <td>
									<?php if(Auth::user()->member->aeps_blocked_amount <= $bankDetail->user->balance->user_balance): ?>
										<input type="text" name="amount" id="amount">
									<?php else: ?>
										<span style="color:red">Minimum Availabel Bal not available</span>
									<?php endif; ?>
								</td>
		                        <td><textarea onclick="expand_textarea()" class="expand" rows="1" cols="7" name="remark" id="remark"></textarea></td>
								<td>
								
                     <?php echo e(Form::select('channel', ['2' => 'IMPS', '1' => 'NEFT'], null, ['class'=>'form-control','id'=>"channel"])); ?>

								</td>
								
		                        <td>
									<?php if(Auth::user()->member->aeps_blocked_amount <= $bankDetail->user->balance->user_balance): ?><button onclick="transfernow(<?php echo e($bankDetail->id); ?>)" class="btn btn-success" id="Sendnow">Send Now</button> 
 								<img src="<?php echo e(url('/loader/loader.gif')); ?>" id="loader" class="loaderImg" style="display:none"/><?php endif; ?>
		                        </td>
		                    </tr>
		                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
		                </tbody>
		            </table>
		        </div>
				<div> <h2 class="textFormat">Terms and Condition:-</h2>
					<h4 class="textFormat"><b>1. </b>Available between 9am to 4pm.</h4>
					<h4 class="textFormat"><b>2. </b>Please check account number before settlement for imps option. Any transaction to wrong account using imps will not be returend back.</h3>
					<h4 class="textFormat"><b>3. </b>All settlement will be charged <span style="color:red">Rs <?php echo e(number_format(Auth::user()->member->aeps_charge,2)); ?> per Rs 25000.</span> which deducted from wallet amount. </h4>
					
				</div>
		    </div>
			
					
		</div>
	</div>
</div>
 <meta name="_token" content="<?php echo csrf_token(); ?>"/>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>