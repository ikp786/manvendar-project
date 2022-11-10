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
</script>
<button class="btn-basic pull-right" title="Back" data-toggle="tooltip" data-placement="top"><a href="<?php echo e((URL::previous())); ?>" ><i class="fa fa-arrow-circle-left" aria-hidden="true" ></i></a></button>

 <div class="row col-md-12" >
	<div class="col-md-5">
    <form method="get" action="<?php echo e(Request::url()); ?>" class="form-inline" role="form">
        <div class="form-group">
            <?php echo e(Form::select('SEARCH_TYPE', ['ID' => 'Record Id','TXN_ID' => 'Txn Id','ACC' => 'Account No', 'MOB_NO'=>'Mobile No'], app('request')->input('SEARCH_TYPE'), ['class'=>'form-control'])); ?>

        </div>
        <div class="form-group">
            <label class="sr-only" for="payid">Number</label>
            <input name="number" type="text" class="form-control" id="exampleInputEmail2" value="<?php echo e(app('request')->input('number')); ?>" placeholder="Number">
			<input type="hidden" name="product" value="<?php echo e(@$product); ?>"/>
			<input type="hidden" name="status_id" value="<?php echo e(@$status_id); ?>"/>
        </div>
        <button type="submit" name="export" class="btn btn-success btn-md"><span class="glyphicon glyphicon-find" ></span><i class="fa fa-search"></i></button>
         <a href="<?php echo e(Request::url()); ?>" class="btn btn-primary btn-md"><i class="fa fa-refresh"></i>
        </a>
		</form>
		</div>
		<div class="col-md-6">
		<form method="get" action="<?php echo e(Request::url()); ?>" class="form-inline" role="form">
        <div class="form-group">
            <input name="fromdate" class="form-control customDatepicker" type="text" placeholder="From date" value="<?php echo e((app('request')->input('fromdate')) ? app('request')->input('fromdate') : date('d-m-Y')); ?>" autocomplete="false">
        </div>
        <div class="form-group">
            <input name="todate" class="form-control customDatepicker" type="text" placeholder="To date" value="<?php echo e((app('request')->input('todate')) ? app('request')->input('todate') : date('d-m-Y')); ?>" autocomplete="false">
        </div>
        <div class="form-group">
			<input type="hidden" name="product" value="<?php echo e(@$product); ?>"/>
			<input type="hidden" name="status_id" value="<?php echo e(@$status_id); ?>"/>
            <button name="export" value="EXPORT" type="submit" class="btn btn-success waves-effect waves-light m-l-10 btn-md"><span class="glyphicon glyphicon-find"></span><i class="fa fa-file-excel-o" aria-hidden="true"></i></button>
			
			<!--<button class="btn btn-basic"><i class="fa fa-print"></i></button>-->
        </div>
    </form></div>
</div>
<br><br>
<div class="box" style="overflow-y: scroll;max-height: 600px">
	<table id="tableTypeThree" class="table table-bordered table-hover">
            <thead>
                <tr>
				 <!-- <th>Select</th>-->
                   <th>Date/Time</th>
                    <th>ID</th> 
                    <th>Counsumer No</th>
                    <th>Bene Name</th>
					<th>Bene Account</th>
                    <th>Ifsc</th>
                    <th>Bank Name</th>
                    <th>Amount</th>
					<th >Type</th>
					<th >Per Name</th>
					<th >Txn Type</th>
					<th>Operator</th>
					<th>Op Id</th>
					<th>Status</th>
					<th>slip</th>
                </tr>
            </thead>
            <tbody>
                <?php $__currentLoopData = $reports; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $report): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
      			 <?php $s = $report->created_at;
				$dt = new DateTime($s);?>
                <tr>
					<!--<td><input type="checkbox" name = "checkbox[]"  value="<?php echo e(@$report->id); ?>"></td>-->
					<td><?php echo e($dt->format('d/m/Y')); ?><br><?php echo e($dt->format('H:i:s')); ?></td>
					<td><?php echo e($report->id); ?></td>
					<td><?php echo e(@$report->customer_number); ?></td>	
					<td><?php echo e(@$report->beneficiary->name); ?></td>
					<td><?php echo e($report->number); ?></td>
					<td><?php echo e(@$report->beneficiary->ifsc); ?></td>
					<td><?php echo e(@$report->beneficiary->bank_name); ?> </td>
					<td><?php echo e($report->amount); ?> </td>
					<td><?php echo e($report->type); ?> </td>
					<td><?php echo e(@$report->client_id); ?> </td>
					<td><?php echo e($report->txn_type); ?> </td>
					<td><?php echo e(@$report->api->api_name); ?> </td>
					<td><?php echo e($report->txnid); ?></td>	
					<td><?php echo e(@$report->status->status); ?></td>					
                    <td style="text-align:center">
					  <?php if(in_array($report->status_id,array(1,3,9))): ?>
						<a target="_blank" href="<?php echo e(url('invoice')); ?>/<?php echo e($report->id); ?>">
							<span class="btn btn-info" style="font-size: 14px;"><i class="md md-visibility"></i>Receipt</span>
						</a>
					<?php endif; ?>
					</td>  
           		 </tr>
           	</tbody>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </table>
</div>           		 
 <?php $__env->stopSection(); ?>	

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>