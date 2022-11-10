<?php $__env->startSection('content'); ?>
<style>
.bootstrap-table{
    margin-top: -28px;
}
</style>
<script>
$(document).ready(function () {
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
		<div class="col-lg-3 col-md-3">
			<h4 class="page-title" style="color: black; "><?php echo e(@$title); ?></h4> 
		</div>
		<div class="row col-md-9">
			<form method="get" action="<?php echo e(Request::url()); ?>" class="form-inline">
				<?php echo e(Form::select('recordCount', ['All'=>'All','10'=> '10','30'=> '30','50'=>'50'],app('request')->input('recordCount'), ['class'=>'form-control','id'=>'recordCount','placeholder'=>'--Select No of Record--'])); ?>

				<input name="number" type="text" class="form-control" id="number" value="<?php echo e(app('request')->input('number')); ?>" placeholder="Enter K Number">
				
				<input name="fromdate" class="form-control customDatepicker" type="text" value="<?php echo e((app('request')->input('fromdate')) ? app('request')->input('fromdate'):date('d-m-Y')); ?>" autocomplete="off"> 
			
				<input name="todate" class="form-control customDatepicker" type="text" value="<?php echo e((app('request')->input('todate')) ? app('request')->input('todate') : date('d-m-Y')); ?>" autocomplete="off">
				
				<button name="export" value="SEARCH" type="submit" class="btn btn-primary btn-md"><i class="fa fa-search"></i></button>
				<button name="export" value="EXPORT" type="submit" class="btn btn-basic btn-md"><i class="fa fa-file-excel-o" aria-hidden="true"></i></button>
				<a href="<?php echo e(Request::url()); ?>"/ class="btn btn-info  btn-md"><i class="fa fa-refresh"></i></a>   
			</form>
		</div>
	</div> 
</div>
 <div style="overflow-x: scroll;">
	<table id="example2"  class="table table-bordered hover">
		<thead>
			<tr>
				<th>ID</th>
				<th>Txn ID</th>
				<th>RequestDate</th>
				<th>UpdateDate</th>
				<th>User</th>
				<th>Acc/K/M/ Number<br>Customer Number</th>
				<th>Amount</th>
				<th>Credit/Debit</th>
				<th>TDS</th>
				<th>Service Tax</th>
				<th>Balance</th>
				<th>Description</th>
				<th>Txn Type</th>
				<th>Txn Status Type</th>
				<th>Status</th>
			</tr>
		</thead>
		<tbody>
		<?php $totalAmount=$count=0; ?>
			<?php $__currentLoopData = $offlineUpdatedRecord; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
			<?php $s = $value->created_at;
			$dt = new DateTime($s);?>  
			<tr>
				<td><?php echo e($value->id); ?></td>
				<td><?php echo e($value->txnid); ?></td>
				<td><?php echo e($value->created_at); ?></td>
				<td><?php echo e($value->updated_at); ?></td>
				<td><a href="<?php echo e(url('user-recharge-report')); ?>/<?php echo e(@$value->user_id); ?>"><?php echo e(@$value->user->name); ?>(<?php echo e(@$value->user->prefix); ?> - <?php echo e(@$value->user->id); ?>)</a></td>
				<td><?php echo e($value->number); ?><br><?php echo e($value->customer_number); ?></td>
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
				<td><?php echo e($value->txn_status_type); ?></td>
				<td><?php echo e($value->status->status); ?></td>
				<?php $totalAmount +=$value->amount;
						$count++;
				?>
			</tr>
		  <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
		</tbody>
		<h4 style="color:red">Total Amount(<?php echo e($count); ?>) : <?php echo e($totalAmount); ?></h4>
	</table>
	    
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
                                <input type="text" class="form-control has-error" id="recordNumber" name="recordNumber"
                                       placeholder="" value="" readonly>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="inputTask" class="col-sm-3 control-label">Provider</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control has-error" id="providerName" name="providerName"
                                       placeholder="Provider Name" value="" readonly>
                            </div>
                        </div><div class="form-group">
                            <label for="inputTask" class="col-sm-3 control-label">Mob/ A.c Number</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control has-error" id="txnNumber" name="txnNumber"
                                       placeholder="Account Number" value="" readonly>
                            </div>
                        </div> 
						<div class="form-group">
                            <label for="inputTask" class="col-sm-3 control-label">Customer Mob Number</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control has-error" id="custMobNumber" name="custMobNumber"
                                       placeholder="Customer Mobile Number" value="" readonly>
                            </div>
                        </div>
						<div class="form-group">
                            <label for="inputTask" class="col-sm-3 control-label">Txn Amount</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control has-error" id="txnAmount" name="txnAmount"
                                       placeholder="Txn Amount" value="" readonly>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="inputEmail3" class="col-sm-3 control-label">Transaction ID</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control" id="txnId" name="txnId"
                                       placeholder="Transaction Id" value="">
                            </div>
                        </div>
						<div class="form-group">
                            <label for="inputEmail3" class="col-sm-3 control-label">Opertor Ref No</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control" id="refId" name="txnId"
                                       placeholder="Opertor Ref Od" value="">
                            </div>
                        </div>
						<div class="form-group">
                            <label for="inputEmail3" class="col-sm-3 control-label">Date & time</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control" id="dateTime" placeholder="Opertor Ref Od" value="">
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
                                    <option value="24">SuccessfullySubmitted</option>
                                </select>
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