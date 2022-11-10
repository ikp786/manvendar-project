<?php $__env->startSection('content'); ?>
<style>
.bootstrap-table{
    margin-top: -28px;
}
</style>
    <script>
	function updateRecord(id) {
		
            var token = $("input[name=_token]").val();
            var dataString = 'id=' + id + '&_token=' + token;
            
			$.ajax({
                type: "GET",
                url: "<?php echo e(url('txn/txn-details')); ?>",
                data: dataString,
                datatype: "json",
				beforeSend:function()
				{
				 $("#txn_otp").hide();	
					
				},
                success: function (res) {
					data=res.message[0];
					console.log(data);
					$("#txn_otp").hide();
                    $('#recordId').val(data.id);
                    $('#recordNumber').val(data.id);
                    $('#txnStatus').val(data.status_id);
                    $('#txnNumber').val(data.number);
                    $('#custMobNumber').val(data.customer_number);
                    $('#api_id').val(data.api_id);
                    $('#txnId').val(data.txnid);
                    $('#refId').val(data.ref_id);
                    $('#txnAmount').val(data.amount);
                    $('#providerName').val(data.provider_name);
                    $('#btn-save').val("Update");
                    $("#transactionDetailsModel").modal("toggle");
                }
            })
        }
		function refundTranaction()
        {
            if(confirm('Are you sure to transfer?'))
            {
                var id = $("#recordId").val();
                var number = $("#txnNumber").val();
                var txnid = $("#txnId").val();
                var refId = $("#refId").val();
                var status = $("#txnStatus").val();
                var token = $("input[name=_token]").val();
                var dataString = 'id=' + id +'&number='+ number + '&txnid=' + txnid +'&status='+ status+'&refId='+ refId;
				$.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                }
            })
                $.ajax({
                    type: "POST",
                    url: "<?php echo e(url('report/update')); ?>",
                    data: dataString,
                    datatype: "json",
                    beforeSend: function() {
                        $('#loaderImg').show();
                    },
                    success: function (res) 
                    {
					   $('#btn-save').show();
					   $('#loaderImg').hide();
                       $('#btn-save').show();
                       if(res.status == 1)
                       {
                            alert(res.message);
							//$("#transactionDetailsModel").modal("toggle");	
                            location.reload();
                        }
                       else
                       {
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
        function sendTxn(id) {
            var token = $("input[name=_token]").val();
            var dataString = 'id=' + id + '&_token=' + token;
            
			$.ajax({
                type: "post",
                url: "<?php echo e(url('re-initiate-txn')); ?>",
                data: dataString,
                //datatype: "json",
				beforeSend:function()
				{
					$("#sendTxnLoaderImg_"+id).show();
					$("#sendTxnBtn_"+id).hide();
				},
                success: function (data) {
					$("#sendTxnLoaderImg_"+id).hide();
                    $('#recordId_'+id).text(data.message);
                 }
            })
        }
 $(document).ready(function()
    {
        $("#txnStatus").change(function() {
            if($(this).val() == "2") {
                $("#txn_otp").show();
                $("#btn-save").hide();
				$("#VerifyOtp").hide();
            }
            else {
                $("#txn_otp").hide();
                $("#btn-save").show();
            }
        });
    });

    function generateOTP()
    {  
        var mobile = $("#mobile").val();
        var recordId = $("#recordId").val();
        var dataString = 'mobile=' + mobile+ '&recordId='+recordId;
        $.ajaxSetup({
                headers: {
                'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                }
            })
             $.ajax({
                type: "post",
                url: "<?php echo e(url('txn-generate-otp')); ?>",
                data: dataString,
                dataType: "JSON",
                beforeSend:function(){
                    $("#otpBtn").hide();
                    $("#resendOtp").hide();
                    $("#otpLoaderImg").show()
                    $("#VerifyOtp").hide();
                    $("#btn-save").hide();
                },
                success: function (data) {
                    $("#otpBtn").hide();
                    $("#resendOtp").show();
                    $("#VerifyOtp").show();
                    $("#btn-save").hide();
                    $("#otpLoaderImg").hide();
                    alert(data.message);
                }
            })
    }   
 
    function verifyOtp()
    {  
        var mobile = $("#mobile").val();
        var recordId = $("#recordId").val();
        var otp = $("#otp").val();
        var dataString = 'mobile=' + mobile + '&recordId=' +recordId + '&otp=' + otp;
        $.ajaxSetup({
                headers: {
                'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                }
            })
             $.ajax({
                type: "post",
                url: "<?php echo e(url('Verify-txn-otp')); ?>",
                data: dataString,
                dataType: "JSON",
                beforeSend:function(){
                    $("#otpBtn").hide();
                    $("#VerifyOtp").hide();
                    $("#resendOtp").hide();
                    $("#otpLoaderImg").show()   
                },
                success: function (data) {
                    $("#otpBtn").hide();
                    $("#resendOtp").show();
                    $("#otpLoaderImg").hide();
                    $("#btn-save").show();
                    alert(data.message);

                    if(data.status_id==1){
                        $("#btn-save").show();
                        $("#txn_otp").hide();
                        $("#VerifyOtp").hide();
                    }
                    else{
                        $("#btn-save").hide();
                        $("#txn_otp").show();
                        $("#VerifyOtp").show();
                    } 
                }
            })
    }                          
     
    $(document).ready(function (){
        $.noConflict();
        $('.customDatepicker').datepicker({
              autoclose: true,  
            format: "dd-mm-yyyy"
        });
    });     
	 
</script>
<?php echo $__env->make('admin.admin-subtab.offlinerecord-type', \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>
<div class="panel panel-default">
    <div class="panel-body">
		<div class="">
			<h4 class="page-title" style="color: black; ">Offline Record</h4>
		</div>
        <div class="">
            <form method="get" action="<?php echo e(Request::url()); ?>" onSubmit="return validateExportForm()" class="form-inline">
				<?php echo e(Form::select('recordCount', ['All'=>'All','10'=> '10','30'=> '30','50'=>'50'],app('request')->input('recordCount'), ['class'=>'form-control','id'=>'recordCount','placeholder'=>'--Select No of Record--'])); ?>

                <input name="number" type="text" class="form-control" id="number" value="<?php echo e(app('request')->input('number')); ?>" placeholder="Enter K Number">
               
				<input name="fromdate" class="form-control customDatepicker" type="text"  autocomplete="off" placeholder="FromDate" value="<?php echo e((app('request')->input('fromdate')) ? app('request')->input('fromdate') : date('d-m-Y')); ?>"> 
                   
                <input name="todate" class="form-control customDatepicker" type="text" autocomplete="off" placeholder="ToDate" value="<?php echo e((app('request')->input('todate')) ? app('request')->input('todate') : date('d-m-Y')); ?>">
                   
                <button name="export" value="SEARCH" type="submit" class="btn btn-primary btn-md"><i class="fa fa-search"></i></button>
                <button name="export" value="EXPORT" type="submit" class="btn btn-basic btn-md"><i class="fa fa-file-excel-o" aria-hidden="true"></i></button>
                <a href="<?php echo e(Request::url()); ?>"/ class="btn btn-info  btn-md"><i class="fa fa-refresh"></i></a>   
            </form>
        </div>
    </div>
</div>

    <div style="overflow-x: scroll;">
		<table id="example2"  class="table table-bordered">
			<thead>
				<tr>
				    <th>ID</th>
					<th>Txn ID</th>
					<th align="center">Date/Time</th>
					<th>User</th>
					<th>Due Date</th>
					<th>K Number</th>
					<th>Amount</th>
					<th>Credit/Debit</th>
					<th>TDS</th>
					<th>Service Tax</th>
					<th>Balance</th>
					<th>Description</th>
					<th>Txn Type</th>
					<th>Status</th>
					<th>Action</th> 
				</tr>
			</thead>

			<tbody>
				<?php  $totalAmount=0;
					   $count=0;
				?>
				<?php $__currentLoopData = $offlineRecords; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
				<?php $s = $value->created_at;
					$dt = new DateTime($s);
				   ?>                    
					<tr>
						<td><?php echo e($value->id); ?></td>
						<td><?php echo e($value->txnid); ?></td>
						<td align="center"><?php echo e($value->created_at); ?></td>
						 <td><a href="<?php echo e(url('user-recharge-report')); ?>/<?php echo e(@$value->user_id); ?>"><?php echo e(@$value->user->name); ?> ( <?php echo e(@$value->user->prefix); ?> - <?php echo e(@$value->user->id); ?>)</a></td>
						<td><?php echo e(@$value->bill_due_date); ?></td>
						<td><?php echo e(@$value->number); ?></td>
						<td><?php echo e($value->amount); ?></td>
						<td><?php echo e($value->type); ?></td>
						<td><?php echo e(number_format($value->tds,3)); ?></td>
						<td><?php echo e(number_format($value->gst,2)); ?></td>
						<td><?php echo e(number_format($value->total_balance,2)); ?></td>
						<td><?php if($value->recharge_type== 1): ?>
							<?php echo e(@$value->provider->provider_name); ?>  
							<?php else: ?>
							<?php echo e(@$value->api->api_name); ?> 
							<?php endif; ?>
						</td>
						<td><?php echo e($value->txn_type); ?></td>
						<td><?php echo e($value->status->status); ?></td>
						<td>
						<?php if(Auth::user()->role_id == 1): ?>
							<?php if(in_array($value->status_id,array(24))): ?>
								<!--<button onclick="sendTxn(<?php echo e($value->id); ?>)" class="btn btn-outline-info btn-sm" id="sendTxnBtn_<?php echo e($value->id); ?>">Send</button>-->
							<img src="<?php echo e(url('/loader/loader.gif')); ?>" id="sendTxnLoaderImg_<?php echo e($value->id); ?>" class="loaderImg" style="display:none"/>
							<span id="recordId_<?php echo e($value->id); ?>"></span>
							<button onclick="updateRecord(<?php echo e($value->id); ?>)" class="btn btn-outline-danger btn-sm" id="rejectBtn_<?php echo e($value->id); ?>" href="#transactionDetailsModel" data-toggle="modal">Update</button>	
							<img src="<?php echo e(url('/loader/loader.gif')); ?>" id="rejectLoaderImg_<?php echo e($value->id); ?>" class="loaderImg" style="display:none"/>
							<?php endif; ?>
						<?php endif; ?>
						<?php 
							$totalAmount +=$value->amount;
							$count++;
						?>
						</td>
					</tr>
				<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
			</tbody>
		</table>
			<h2 style="color:red">Total Amount(<?php echo e($count); ?>) : <?php echo e($totalAmount); ?></h2>
    </div>
    <div id="transactionDetailsModel" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
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
                        <input type="hidden" name="recordId" value="" id="recordId">
						<div class="form-group">
                            <label for="inputTask" class="col-sm-3 control-label">Record Number</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control has-error" id="recordNumber" name="recordNumber" placeholder="" value="" readonly>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="inputTask" class="col-sm-3 control-label">Provider</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control has-error" id="providerName" name="providerName" placeholder="Provider Name" value="" readonly>
                            </div>
                        </div><div class="form-group">
                            <label for="inputTask" class="col-sm-3 control-label">Mob/ A.c Number</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control has-error" id="txnNumber" name="txnNumber" placeholder="Account Number" value="" readonly>
                            </div>
                        </div> 
						<div class="form-group">
                            <label for="inputTask" class="col-sm-3 control-label">Customer Mob Number</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control has-error" id="custMobNumber" name="custMobNumber" placeholder="Customer Mobile Number" value="" readonly>
                            </div>
                        </div>
						<div class="form-group">
                            <label for="inputTask" class="col-sm-3 control-label">Txn Amount</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control has-error" id="txnAmount" name="txnAmount" placeholder="Txn Amount" value="" readonly>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="inputEmail3" class="col-sm-3 control-label">Transaction ID</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control" id="txnId" name="txnId" placeholder="Transaction Id" value="">
                            </div>
                        </div>
						<div class="form-group">
                            <label for="inputEmail3" class="col-sm-3 control-label">Opertor Ref No</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control" id="refId" name="txnId" placeholder="Opertor Ref Od" value="">
                            </div>
                        </div>
						
                        <div class="form-group">
                            <label for="inputEmail3" class="col-sm-3 control-label">Status</label>
                            <div class="col-sm-9">
                                <select class="form-control" name="txnStatus" id="txnStatus">
                                    <option value="1">Success</option>
                                    <option value="2">Failure</option>
                                    <option value="3">Pending</option>
                                    <option value="20">Refund Pending</option>
                                    <option value="24">Successfull</option>
                                </select>
                            </div>
                        </div>
						<div style="display: none" id="txn_otp">
                            <div class="form-group">
                                <label for="inputEmail3" class="col-sm-3 control-label">Mobile</label>
                                <div class="col-sm-9">
                                    <select  class="form-control" name="mobile" id="mobile">
                                        <?php $__currentLoopData = $otpVerifications; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $verification): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option><?php echo e($verification->mobile); ?></option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="inputEmail3" class="col-sm-3 control-label">OTP</label>
                                <div class="col-sm-4">
                                    <input type="text" class="form-control" name="otp" id="otp" placeholder="Enter OTP" required>
                                </div> 
                                <a onClick="generateOTP()" id="otpBtn" class="btn btn-basic" style="background:khaki;">Send OTP</a><img src="<?php echo e(url('/loader/loader.gif')); ?>" id="otpLoaderImg" class="loaderImg">
                                <a onClick="verifyOtp()" class="btn btn-primary" id="VerifyOtp">Verify</a>
                                <a onClick="generateOTP()" class="btn btn-success" id="resendOtp" style="display: none">Resend</a>
                            </div> 
                        </div>

                    </div>
                    <div class="modal-footer">
                        <!--<button type="button" class="btn btn-info waves-effect waves-light" id="btn-save" value="add" onclick="this.disabled=true;refundTranaction()">Update</button>-->
                        <button type="button" class="btn btn-info waves-effect waves-light" id="btn-save" value="add" onclick="refundTranaction()">Update</button>
						<img src="<?php echo e(url('/img')); ?>/loader.gif" id="loaderImg" class="loaderImg" style="display:none">
						
                        <button type="button" class="btn btn-default waves-effect" data-dismiss="modal">Close</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <meta name="_token" content="<?php echo csrf_token(); ?>"/>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('admin.layouts.templatetable', \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>