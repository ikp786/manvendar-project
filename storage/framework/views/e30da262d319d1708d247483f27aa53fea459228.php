<?php $__env->startSection('content'); ?>

    <script>
        function transfernow(id) {
			var amount = $("#amount_"+id).val();
			if(confirm('Are you sure to Return transfer amount :' +amount))
			{
				
				var remark = $("#remark_"+id).val();
							
							var wallet = 0;
				$("#trbutton_"+id).text("Processing");
				$("#trbutton_"+id).prop("disabled", true);
				var token = $("input[name=_token]").val();
				var user_id = id;
				var commission = $("#commission").val();
				var dataString = 'amount=' + amount + '&wallet=' + wallet + '&remark='+ remark + '&user_id=' + user_id + '&commission=' + commission;
				$.ajaxSetup({
					headers: {
						'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
					}
				})
				$.ajax({
					type: "POST",
					url: "<?php echo e(url('fund-transfer-return')); ?>",
					data: dataString,
					dataType: "json",
					beforeSend:function()
					{
						/*  $.LoadingOverlay("show", {
							image       : "",
							fontawesome : "fa fa-spinner fa-spin"
						}); */

					},
					success: function (msg) {
						// $.LoadingOverlay("hide");
						if(msg.success == 'success') {
							$("#trbutton_"+id).prop("disabled", false);
							$("#trbutton_"+id).text("Return Now");
							$("#amount_"+id).val('');
						}else{
							$("#trbutton_"+id).prop("disabled", false);
							$("#trbutton_"+id).text("Return Now");
							$("#amount_"+id).val('');
							
						}
						alert(msg.message);
						location.reload();

					}
				});
			}
			 else
			{
				 $("#trbutton_"+id).prop("disabled", false);
				$("#trbutton_"+id).text("Return Now");
				//$("#amount_"+id).val('');
			}
        }
		function openVerificationPin(userId,type)
		{
			$("#schemeVerificationUser").val(userId);
			$("#verificationPinType").val(type);
			$('#VerificationPinModal').modal("toggle");
			$("#schemeVerificationPin").val('')
			$("#schemeVerificationMessage").val('')
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
    </script>
	
	<script>
    function expand_textarea()
	{
		$('textarea.expand').keyup(function () {
		$(this).animate({ height: "4em", width: "13em" }, 500); 
		});
	}
	
	</script>
	
<div class="panel panel-default">
    <div class="panel-body">
        <div class="col-md-12">
                <h4>Fund Transfer</h4>
            <div class="col-md-6">
                <form method="get" action="<?php echo e(Request::url()); ?>" class="form-inline"
                      role="form">
                    <?php echo csrf_field(); ?>

                    <div class="form-group">
                        <?php echo e(Form::select('SEARCH_TYPE', ['NAME'=>'NAME','ID' => 'Record Id','MOB_NO'=>'Mobile No'], null, ['class'=>'form-control', 'style'=>"height: 10%;"])); ?>

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
</div>
 <br>   
    <div class="row">
        <div class="col-sm-12">
            <div class="card-box">
                <div class="">

                    <?php echo e(Form::open(array('url' => 'fund-transfer'))); ?>


                    <?php echo e(Form::close()); ?>

                     <div class="">
						<table id="mytable"  class="table table-bordered " >
                            <thead>
                            <tr>
                               <!-- <th data-field="state" data-checkbox="true"></th>-->
                                <th data-field="id" data-sortable="true">
                                    ID
                                </th>
                                <th data-field="name" data-sortable="true">Name</th>
                                <th data-field="company" data-sortable="true">Shop Name</th>
                                <th data-field="date" data-sortable="true" data-formatter="dateFormatter">Mobile
                                </th>
                                <th data-field="balance" data-align="center" data-sortable="true"
                                    data-sorter="priceSorter">Money Balance
                                </th>
                               <!-- <th data-field="rechargebalance" data-align="center" data-sortable="true"
                                    data-sorter="priceSorter">Recharge Balance
                                </th> -->
                                <?php if(Auth::user()->role_id == 1): ?>
                                <th data-field="amount" data-align="center" data-sortable="true"
                                    data-sorter="priceSorter">Member Type
                                </th>
                                <?php endif; ?>
                                <!-- <th data-field="type" data-align="center" data-sortable="true"
                                    data-sorter="priceSorter">Wallet Type
                                </th> -->
                                <th data-field="parent_name" data-align="center" data-sortable="true"
                                >Amount
                                </th>
								<th data-field="remark" data-align="center" data-sortable="true"
                                    data-formatter="">Remark
                                </th>
                                <th data-field="action" data-align="center" data-sortable="true"
                                    data-formatter="">Transfer Now
                                </th>
                            </tr>
                            </thead>

                            <tbody>
                            <?php $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr>
                                   <!-- <td><?php echo e($user->id); ?></td>-->
                                    <td><?php echo e(($user->role_id == 4) ? "D " : "R "); ?><?php echo e($user->id); ?></td>
                                    <td><?php echo e($user->name); ?></td>
                                    <td><?php echo e($user->member->company); ?></td>
                                    <td><?php echo e($user->mobile); ?></td>
                                    <td><?php echo e($user->balance->user_balance); ?></td>
                                    
                                    <?php if(Auth::user()->role_id == 1): ?>
                                    <td><?php echo e($user->role->role_title); ?></td>
                                    <?php endif; ?>
									<td><input id="amount_<?php echo e($user->id); ?>" type="text" name="amount"

                                               style="width:60px; text-align:center; margin-right:5px; height:34px;"></td>
											   
									<td><textarea style="color:black;" class="expand" onclick="expand_textarea()" id="remark_<?php echo e($user->id); ?>" rows="2" cols="7" name="remark"></textarea></td>
                                    <?php if(Auth::user()->company->company_id!=13): ?>
                                    <!--<td><a id="trbutton_<?php echo e($user->id); ?>" onclick="transfernow(<?php echo e($user->id); ?>)" class="btn btn-success">Return Now</a> </td>-->
									<td><a id="trbutton_<?php echo e($user->id); ?>" onclick="openVerificationPin(<?php echo e($user->id); ?>,'DT')" class="btn btn-success">Return Now</a> </td>
								
                                    <?php endif; ?>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </tbody>
                        </table>
                    </div>
                   
                </div>
            </div>
        </div>
    </div>
	<div id="VerificationPinModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
        <div class="modal-dialog modal-sm" role="document">
            <div class="modal-content">
                <div class="modal-header">
					<h4 class="modal-title" id="myModalLabel">Enter Verification Pin</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    
                </div>
                <div class="modal-body">
                    <div id="frmTasks" name="frmTasks" class="form-horizontal">
						<div class="form-group">
                           
                                <input type="password" class="form-control" id="schemeVerificationPin" placeholder="Enter Verification Security Pin"/>
                                <input type="hidden" class="form-control" id="schemeVerificationUser"/>
								<input type="hidden" class="form-control" id="verificationPinType"/>
								<span id="schemeVerificationMessage"></span>
                            
                        </div>
						
                    </div>
                </div>
                <div class="modal-footer">
                    <button onclick="pinVerification()" type="button" class="btn btn-info waves-effect waves-light" value="add">Verify
                    </button>
                    <button type="button" class="btn btn-default waves-effect" data-dismiss="modal">Close
                    </button>
                </div>
            </div>
        </div>


    </div>
<meta name="_token" content="<?php echo csrf_token(); ?>"/>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('admin.layouts.templatetable', \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>