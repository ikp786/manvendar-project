<?php $__env->startSection('content'); ?>

    <script>
        function transfernow(id) 
		{
			var amount = $("#amount_"+id).val();
			if(amount<10)
            {
                alert('Please Enter minimum 10 Rs');
                $("#trbutton_"+id).attr("disabled", false);
                return false;
            }
		if(confirm('Are you sure to transfer Amount : ' + amount))
		{
           
			var remark = $("#remark_"+id).val();
            var wallet = 0;
			var dt_scheme=0.0;// Added by rajat
            $("#trbutton_"+id).attr("disabled", true);
            var token = $("input[name=_token]").val();
            var user_id = id;
            var commission = $("#commission").val();
            var dataString = 'wallet=' + wallet + '&amount=' + amount + '&remark='+ remark + '&user_id=' + user_id + '&commission=' + commission + '&dt_scheme='+dt_scheme;
			$.ajaxSetup({
					headers: {
						'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
					}
				})
            $.ajax({
                type: "POST",
                url: "<?php echo e(url('fund-transfer')); ?>",
                data: dataString,
                dataType: "json",
                beforeSend: function() {
                        $("#trbutton_" + id).hide();
                        $('#imgr_' +id).show();
						 //$.LoadingOverlay("show");
                    },
                success: function (msg) 
				{
					//$.LoadingOverlay("hide");
                    if(msg.status == 'success') {
                        $("#trbutton_" + id).hide();
                        $('#imgr_' +id).show();
                        //swal("Success", msg.message, "success");
                        $("#trbutton_"+id).attr("disabled", false);
                        $("#trbutton_"+id).text("Transfer Now");
                        $("#amount_"+id).val('');
						alert(msg.message)
						 location.reload();
                    }else{
                        $("#trbutton_"+id).attr("disabled", false);
                        $("#trbutton_"+id).text("Transfer Now");
                        $("#amount_"+id).val('');
						$('#imgr_' +id).hide();
                        //swal("Failure", msg.message, "error");
						 $("#trbutton_" + id).show();
						 alert(msg.message)
                    }

                }
            });
		}
		else { }
        }
		function openVerificationPin(userId,type)
		{
			amount= $("#amountInWords_"+userId).text();
			$("#schemeVerificationUser").val(userId);
			$("#verificationPinType").val(type);
			$("#schemeVerificationMessage").text('');
			$('#VerificationPinModal').modal("toggle");
			$("#schemeVerificationPin").val('')
		}
		function pinVerification()
		{
			var schemeVerificationPin = $("#schemeVerificationPin").val();
			var schemeVerificationUser = $("#schemeVerificationUser").val();
			var verificationPinType = $("#verificationPinType").val();
			var dataString = 'pin=' + schemeVerificationPin+'&type='+verificationPinType;
			$.ajax({
				type: "get",
				url: "<?php echo e(url('scheme-verification-pin')); ?>",
				data: dataString,
				dataType: "json",
				beforeSend:function()
				{
					$("#schemeVerificationMessage").text('');
				},
				success: function (res) {
					$("#schemeVerificationMessage").text(res.message);
				   if(res.status == 1)
				   {
					   $("#schemeVerificationMessage").css('color','green');
					    $("#VerificationPinModal").modal("hide");
						transfernow(schemeVerificationUser)
				   }
				   else
						$("#schemeVerificationMessage").css('color','red');
				}
			});
			
		}

	function amountInWords(recordId)
		{
			$("#amountInWords_"+recordId).text('');
			var amount = $("#amount_"+recordId).val();
			if(amount =='')
				return false;
			else if(amount <0){
				alert("Negative amount not valid")
				return false;
			}
			else
			{
				var finalAmount = amount;
				$.ajax({
					type: "get",
					url: "<?php echo e(url('/')); ?>/amount-in-words",
					data: "amount="+amount,
					dataType:"json",
					beforeSend:function(){
				
					},
					success: function (msg) 
					{
						$("#amountInWords_"+recordId).text(msg);
					}
				});
			}
		}


    </script>
	<script>
    function expand_textarea()
	{
		$('textarea.expand').keyup(function () {
		$(this).animate({ height: "4em", width: "13em" }, 500); 
		});
	}
	
	</script>
	<?php echo $__env->make('agent.fund.fund-type', \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?><br>
<div class="panel panel-default">
    <div class="panel-body">
        <div class="col-md-12">
            <h4> R To R Fund Transfer</h4>
            <div class="col-md-6">
                <form method="get" action="<?php echo e(Request::url()); ?>" class="form-inline"
                      role="form">
                    <?php echo csrf_field(); ?>

                    <div class="form-group">
                        <?php echo e(Form::select('SEARCH_TYPE', ['ID'=>'Id','MOB_NO'=>'Mobile No'], null, ['class'=>'form-control', 'style'=>"height: 10%;"])); ?>

                    </div>
                     <div class="form-group">
                        <label class="sr-only" for="payid">Number</label>
                        <input name="number" type="text" class="form-control" required 
                               value="<?php echo e(app('request')->input('number')); ?>" placeholder="Number">
                    </div>
                    <button type="submit" class="btn btn-success waves-effect waves-light m-l-10 btn-md"><span class="glyphicon glyphicon-find"></span><i class="fa fa-search"></i>
                    </button>
                     <a href="<?php echo e(Request::url()); ?>" class="btn btn-primary"><i class="fa fa-refresh"></i>
                    </a> 
                </form>
            </div>
        </div>
    </div>
</div><br>
<div class="row">
    <div class="col-sm-12">
        <div class="card-box">
            <div class="table-responsive">
				<table id="mytable" class="table table-bordered">
					<thead>
				
						<!--<th data-field="state" data-checkbox="true"></th>-->
						<th data-field="id" data-sortable="true">ID</th>
						<th data-field="name" data-sortable="true">Name</th>
						<th data-field="company" data-sortable="true">Shop Name</th>
						<th data-field="mob" data-sortable="true">Mobile</th>
						<th data-field="mob" data-sortable="true">Amount</th>
						 <th data-field="remark" data-align="center" data-sortable="true">
						 Remark
						 </th>
						<th data-field="action" data-align="center" data-sortable="true">Action
						</th>
				
					</thead>
					<tbody>
						<?php $__currentLoopData = $retilerLists; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
							<tr>
								<td><?php echo e($user->prefix); ?> <?php echo e($user->id); ?></td>
								<td><?php echo e($user->name); ?></td>
								<td><?php echo e($user->member->company); ?></td>
								<td><?php echo e($user->mobile); ?></td>
								
								<?php if(Auth::user()->role_id == 1): ?>
								<td><?php echo e($user->role->role_title); ?></td>
								<?php endif; ?>
								<td><input id="amount_<?php echo e($user->id); ?>" type="text" name="amount" style="width:60px; text-align:center; margin-right:5px; height:34px;" onfocusout="amountInWords(<?php echo e($user->id); ?>)"><br><span id="amountInWords_<?php echo e($user->id); ?>" style="font-size: 11px;font-weight: bold;"></span>
								</td>
								 <td><textarea placeholder=""onclick="expand_textarea()" class="expand" id="remark_<?php echo e($user->id); ?>" rows="1" cols="7" name="remark"></textarea></td>
								<!--<td><center><span id="imgr_<?php echo e($user->id); ?>" style="display:none;"><img src="images/load2.gif" height="40px" width="40px"></span></center>
								<button id="trbutton_<?php echo e($user->id); ?>" onclick="transfernow(<?php echo e($user->id); ?>)" class="btn btn-success">Transfer Now</button> </td>-->
								<td><center><span id="imgr_<?php echo e($user->id); ?>" style="display:none;"><img src="images/load2.gif" height="40px" width="40px"></span></center>
								<button id="trbutton_<?php echo e($user->id); ?>" onclick="openVerificationPin(<?php echo e($user->id); ?>,'TXN')" class="btn btn-success">Transfer Now</button> </td>
								<!--<a onclick="openVerificationPin(<?php echo e($user->id); ?>,'PROFILE_UPDATE')" href="javascript:void(0)" class="table-action-btn"><i class="md md-edit"></i>Update</a>-->
							</tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tbody>
                    </table>
            </div>
            <div class="col-md-6">
			</div>
        </div>
    </div>
</div>

<div id="VerificationPinModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
    <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h4 class="modal-title" id="myModalLabel">Enter Verification Pin</h4>
			</div>
			<div class="modal-body">
				<div class="form-group">
					<input type="password" class="form-control" id="schemeVerificationPin" placeholder="Enter Verification Security Pin"/>
					<input type="hidden" class="form-control" id="schemeVerificationUser"/>
					<input type="hidden" class="form-control" id="verificationPinType"/>
					<span id="schemeVerificationMessage"></span>
				</div>
			</div>
			<div class="form-group">
				<span id="modalAmountInWords"></span>
			</div>
			<div class="modal-footer">
				<button onclick="pinVerification()" type="button" class="btn btn-info waves-effect waves-light" value="add">Verify</button>
				<button type="button" class="btn btn-default waves-effect" data-dismiss="modal">Close</button>
			</div>
        </div>
    </div>
</div>
<meta name="_token" content="<?php echo csrf_token(); ?>"/>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>