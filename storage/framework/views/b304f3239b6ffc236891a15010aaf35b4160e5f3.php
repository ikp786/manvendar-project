<?php $__env->startSection('content'); ?>
<style>
.bootstrap-table{
    margin-top: -28px;
}
</style>
<!-- for popup -->
  
    <script>
	function addNewProvider()
	{
		$('#frmTasks').trigger("reset");
		$('#btn-save').val("Submit");
		$("#transactionDetailsModel").modal("toggle");
	}
        /* function updateOperatorCode(id)
        {
            if(confirm('Are You sure to Update?'))
            {
				var cyberCode = $('#cyberCode_'+id).val();
				var redPayCode = $('#redPayCode_'+id).val();
				var dataString = 'providerId=' + id + '&cyberCode='+ cyberCode+ '&redPayCode='+ redPayCode;
				 $.ajaxSetup({
					headers: {
						'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
					}
				})
				$.ajax({
					type: "put",
					url: "<?php echo e(url('update/operator-code')); ?>/"+id,
					data: dataString,
					dataType:"json",
					success: function (data) {
						if(data.status == 0)
						{
							$("#codeMessage_"+id).css('color','red');
						}
						else
							$("#codeMessage_"+id).css('color','green');
						$("#codeMessage_"+id).text(data.message);
					}
				});
        }
        else
        {

        }
    } */
	function updateRecord(id) {
            var token = $("input[name=_token]").val();
            var dataString = 'id=' + id + '&_token=' + token;
            
			$.ajax({
                type: "GET",
                url: "<?php echo e(url('get/operator-details')); ?>",
                data: dataString,
                datatype: "json",
				beforeSend:function()
				{	
				},
                success: function (data) {
                    $('#providerId').val(data.message.id);
                    $('#providerName').val(data.message.provider_name);
                    $('#cyberCode').val(data.message.cyber);
                    $('#redPayCode').val(data.message.redpay);
                    $('#suvidhaa').val(data.message.suvidhaa);
                    $('#sp_key').val(data.message.sp_key);
                    $('#m_robo_sp_key').val(data.message.m_robotics_sp_key);
                    $('#serviceId').val(data.message.service_id);
                    $('#maxHoldTxn').val(data.message.max_hold_txn);
                    $('#minPassAmtTxn').val(data.message.min_pass_amt_txn);
                    $('#btn-save').val("Update");
                    $("#transactionDetailsModel").modal("toggle");
                }
            })
        }
		function updateProviderInfo()
        {
            if(confirm('Are you sure want to update?'))
            {
                var providerId = $("#providerId").val();
                var providerName = $("#providerName").val();
                var cyberCode = $("#cyberCode").val();
                var serviceId = $("#serviceId").val();
                var redPayCode = $("#redPayCode").val();
                var suvidhaa = $("#suvidhaa").val();
                var sp_key = $("#sp_key").val();
                var maxHoldTxn = $("#maxHoldTxn").val();
                var minPassAmtTxn = $("#minPassAmtTxn").val();
                var token = $("input[name=_token]").val();
                var dataString = 'providerId=' + providerId +'&provider_name='+ providerName + '&cyberCode=' + cyberCode +'&redPayCode='+ redPayCode+'&serviceId='+ serviceId+'&maxHoldTxn='+ maxHoldTxn+'&minPassAmtTxn='+ minPassAmtTxn+'&sp_key='+ sp_key+'&suvidhaa='+ suvidhaa;
				$.ajaxSetup({
					headers: {
						'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
					}
				})
				var requestType = $("#btn-save").val();
				var url= url= "<?php echo e(url('provider/create')); ?>"
				if(requestType=="Update")
				{
					url= "<?php echo e(url('provider/update')); ?>/"+providerId;
				}
                $.ajax({
                    type: "POST",
                    url: url,
                    data: dataString,
                    datatype: "json",
                    beforeSend: function() {
                            $('#btn-save').hide();
							$('#loaderImg').show();
							$('#name-error').text('');
                    },
                    success: function (res) 
                    {
						$('#btn-save').show();
                       if(res.status == 1)
                       {
                            $("#transactionDetailsModel").modal("toggle");	
							alert(res.message);
                          location.reload();
                       }
					   else if(res.status == 10)
					   {
						   var errorString = '<div class="alert alert-danger"><ul>';
							$.each(res.errors, function (key, value) {
								errorString += '<li>' + value + '</li>';
							});
							errorString += '</ul></div>';
							$("#name-error").show();
							$('#name-error').html(errorString); //appending to a <div id="form-errors"></div> inside form
							$('#name-error').focus(); //appending to a <div id="form-errors"></div> inside form
							$('#name-error').focus();
					   }
                       else
                       {
						   $('#loaderImg').hide();
							$('#btn-save').show();
                            alert(res.message);
                        
                       }
                    }
                });
            }
            else
            {
                 $("#btn-save").prop("disabled", false);
            }
        }
    </script>

<div class="panel panel-default">
	<div class="panel-body">
		<div class="col-md-12">
			<div class="col-md-6">
				<h4 class="page-title" style="color: black; "><?php echo e('Provider List'); ?></h4>
			</div>
			<div class="col-md-6 pull-right">
		   <input type="search" id="search" value="" class="form-control" placeholder="Page Searching">
			<a href="<?php echo e(url('provider/un-categorised')); ?>" id="" class="btn btn-primary" >Un-Categorise Provider</a>
			<a href="<?php echo e(url('provider-list')); ?>" id="" class="btn btn-success" >Working Provider</a>
			<button type="button" id="" class="btn btn-primary" onClick="addNewProvider()">Add New Provider
			</button>
			</div>
		</div>  
	</div>
</div>
		
    <!--Basic Columns-->
    <!--===================================================-->
    <!--===================================================-->
	<div class="">
		<table class="table table-bordered dataTable no-footer" id="example2" role="grid" aria-describedby="example2_info">
			<thead>
				<tr>
					<th>ID</th>
					<th>Provider Name</th>
					<th>Cyber</th>
					<th>RedPay</th>
					<th>Suvidhaa</th>
					<th>Instant Special Key</th>
					<th>Max Hold Txn</th>
					<th>Remaing Hold Txn</th>
					<th>Min Pass Amt Txn</th>
					<th>Service Type</th>
					<th>Action</th>
					<th>MSG</th>
				</tr>
			</thead>
			<tbody>
				<?php $__currentLoopData = $providerList; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $provider): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
				<tr>
					<td><?php echo e($provider->id); ?></td>
					<td><?php echo e($provider->provider_name); ?></td>
					<td><?php echo e($provider->cyber); ?></td>
					<td><?php echo e($provider->redpay); ?></td>
					<td><?php echo e($provider->suvidhaa); ?></td>
					<td><?php echo e($provider->sp_key); ?></td>
					<td><?php echo e($provider->max_hold_txn); ?></td>
					<td><?php echo e($provider->max_hold_txn - $provider->hold_txn_couter); ?></td>
					<td><?php echo e($provider->min_pass_amt_txn); ?></td>
					<td><?php echo e($provider->service->service_name); ?></td>
					<td><button type="button" onclick="updateRecord(<?php echo e($provider->id); ?>)" class="btn btn-primary btn-sm" id="b_save_<?php echo e($provider->id); ?>">Update Code</button></td>
					<td><span id="codeMessage_<?php echo e($provider->id); ?>" style="color:green"></span></td>	
				</tr>
				<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
			</tbody>
		</table>  
	</div>
	<div class="modal fade" id="transactionDetailsModel" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
				<h4 class="modal-title">Transaction Details</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                </div>
               <!-- <form id="frmTasks" action="<?php echo e(url('report/update')); ?>" method="post" name="frmTasks" class="form-horizontal" novalidate=""> -->
                <form id="frmTasks" name="frmTasks" class="form-horizontal" novalidate="">
                    <?php echo csrf_field(); ?>

                    <div class="modal-body">
                        <div style="display:none" id="name-error"></div>
                        <div class="form-group">
                            <label for="inputTask" class="col-sm-3 control-label">Provider Id</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control has-error" id="providerId" name="providerId" placeholder="" value="" readonly>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="inputTask" class="col-sm-3 control-label">Provider Name</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control has-error" id="providerName" name="providerName" placeholder="Provider Name" value="">
                            </div>
                        </div> 
						<div class="form-group">
                            <label for="inputTask" class="col-sm-3 control-label">CyerPlate Code</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control has-error" id="cyberCode" name="cyberCode" placeholder="Ener Cyber Plate Code" value="" >
                            </div>
                        </div>
						<div class="form-group">
                            <label for="inputTask" class="col-sm-3 control-label">Redpay Code</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control has-error" id="redPayCode" name="redPayCode" placeholder="Enter Redpay Code" value="" >
                            </div>
                        </div>
						<div class="form-group">
                            <label for="inputTask" class="col-sm-3 control-label">Suvidhaa</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control has-error" id="suvidhaa" name="suvidhaa" placeholder="Enter Suvidhaa Code" value="" >
                            </div>
                        </div>
						<div class="form-group">
                            <label for="inputTask" class="col-sm-3 control-label">Instant Pay Special Key</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control has-error" id="sp_key" name="sp_key" placeholder="Instant Pay Special Key" value="" >
                            </div> 
                        </div>
                        <div class="form-group">
                            <label for="inputTask" class="col-sm-3 control-label">MRobotics Special Key</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control has-error" id="m_robo_sp_key" name="m_robo_sp_key" placeholder="MRobotics Special Key" value="" >
                            </div> 
                        </div>
						<div class="form-group">
                            <label for="inputTask" class="col-sm-3 control-label">Number Of Hode Txn</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control has-error" id="maxHoldTxn" name="maxHoldTxn" placeholder="Number of hold txn" value="" >
                            </div>
                        </div>
						<div class="form-group">
                            <label for="inputTask" class="col-sm-3 control-label">Min Amount Pass</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control has-error" id="minPassAmtTxn" name="minPassAmtTxn" placeholder="Enter Amount to hold txn" value="" >
                            </div>
                        </div>
						<div class="form-group">
                            <label for="inputTask" class="col-sm-3 control-label">Service Type</label>
                            <div class="col-sm-9">
                              <?php echo e(Form::select('serviceId', $servicelist, $provider->service_id, array('class' => 'form-control','id' => 'serviceId'))); ?>

                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <!--<button type="button" class="btn btn-info waves-effect waves-light" id="btn-save" value="add" onclick="this.disabled=true;refundTranaction()">Update</button>-->
                        <input type="button" class="btn btn-info waves-effect waves-light" id="btn-save" onclick="updateProviderInfo()" value="Update"/>
						<img src="<?php echo e(url('/img')); ?>/loader.gif" id="loaderImg" class="loaderImg" style="display:none"/>
                        <button type="button" class="btn btn-default waves-effect" data-dismiss="modal">Close</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
	
    <meta name="_token" content="<?php echo csrf_token(); ?>"/>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('admin.layouts.templatetable', \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>