<?php $__env->startSection('content'); ?>

<script type="text/javascript">
	
function TramocheckStatus(id,apiId)
{
	var token = $("input[name=_token]").val();
	var number = $("#number").val();
	var dataString = 'id=' + id;
	$.ajaxSetup({
	headers: {
		'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
	}
	});
	/* if(apiId== 3){
		//url="<?php echo e(url('tramo/transaction_status')); ?>";
	}
	else if(apiId == 5) */
		url = "<?php echo e(url('check-txn-status')); ?>"
	
	$.ajax({
		type: "post",
		url: url,
		data: dataString,
		dataType: "json",
		beforeSend:function(){
			$("#checkBtn_"+id).hide()
			$("#checkImg_"+id).show();
		},
		success: function (data) {
			$("#checkBtn_"+id).show()
			$("#checkImg_"+id).hide();
			alert(data.msg);	
		}
	})

}

/*for date->Calender*/

    $(document).ready(function () {
        $.noConflict();
        $('.customDatepicker').datepicker({
              autoclose: true,  
            format: "dd-mm-yyyy"
        });

    }); 
</script>
<div class="row">
<div class="row col-md-12">
	<div class="col-md-6">
		<form action="<?php echo e(Request('')); ?>" method="get" class="form-inline">
		   <input type="hidden" name="service_id" value="<?php echo e(@$serviceId); ?>"/>
			<div class="form-group">
				<input type="text" name="number" class="form-control" id="number"
					   placeholder="Number" required value="<?php echo e(app('request')->input('number')); ?>">
			</div> 
			<button type="submit" name="export" value="numberSearch" class="btn btn-success waves-effect waves-light m-l-10 btn-md"><span class="glyphicon glyphicon-find"></span><i class="fa fa-search"></i></button>
			<a href="<?php echo e(Request('')); ?>?service_id=<?php echo e(@$serviceId); ?>" class="btn btn-primary btn-md"><i class="fa fa-refresh"></i></a>
		</form>
	</div>
	 <div class="col-md-6">
			<form action="<?php echo e(Request('')); ?>" method="get" class="form-inline">
			<div class="form-group" >
			   <input name="fromdate" class="form-control customDatepicker" type="text" value="<?php echo e(app('request')->input('fromdate')); ?>" placeholder="From date">
				<input type="hidden" name="service_id" value="<?php echo e(@$serviceId); ?>" />
			</div>
			<div class="form-group">
				<input name="todate" class="form-control customDatepicker" type="text" value="<?php echo e(app('request')->input('todate')); ?>" placeholder="To date">
			</div>
			<div class="form-group">
				<button name="export" value="DATE_SEARCH" type="submit" class="btn btn-success "><span class="glyphicon glyphicon-find"></span><i class="fa fa-search"></i></button>
				<button name="export" value="Recharge Reports" type="submit" class="btn btn-basic "><span class="glyphicon glyphicon-find"></span><i class="fa fa-file-excel-o" aria-hidden="true"></i></button>
			</div>
	   </form>
	</div>
</div>
	<br>				
	
<div class="">	  
    <table id="dtBasicExample" class="table table-hover table-bordered" >
						<thead >
							<tr>
								 <th align="center">Date/Time</th>
								<th>ID </th>
								<th>User</th>
								<th>Txn ID </th>
								<th >Provider</th>
								<th>Number</th>
								<th>Amount</th>
								<th>Status</th>
								<th>Action</th>
								<th>Report</th>
							</tr>
						</thead>
					 	<tbody>
						<?php $__currentLoopData = $reportDetails; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $recharge_reports): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                  <?php $s = $recharge_reports->created_at;
                $dt = new DateTime($s);?>
                    <tr class="odd gradeX" style="background-color:white">
                      <td align="center"><?php echo e($dt->format('d-m-Y')); ?><br><?php echo e($dt->format('H:i:s')); ?></td>
                      <td><?php echo e($recharge_reports->id); ?></td>
                      <td><?php echo e($recharge_reports->user->name); ?></td>
                      <td><?php echo e($recharge_reports->txnid); ?></td>
                      <td><?php echo e(@$recharge_reports->provider->provider_name); ?></td>
                      <td><?php echo e($recharge_reports->number); ?></td>
                      <td><?php echo e($recharge_reports->amount); ?></td>
                      <td> <?php echo e($recharge_reports->status->status); ?></td>
              
                    <?php if(in_array($recharge_reports->status_id,array(1,3,9,34))): ?>
					    <td>
				             
				            <img src="<?php echo e(url('loader/loader.gif')); ?>" id="checkImg_<?php echo e($recharge_reports->id); ?>" class="loaderImg" style="display: none;">
				            <?php if($recharge_reports->api_id=='27'): ?>
                                 <a  href="javascript::voide(0)" disabled class="btn btn-outline-info btn-sm" id="checkBtn_<?php echo e($recharge_reports->id); ?>"> Check</a>
                            <?php else: ?>
                                <a onclick="TramocheckStatus(<?php echo e($recharge_reports->id); ?>,<?php echo e($recharge_reports->api_id); ?>)" href="javascript::voide(0)" 
                                class="btn btn-outline-info btn-sm"  id="checkBtn_<?php echo e($recharge_reports->id); ?>"> Check</a>
                            <?php endif; ?>
			            </td>
			            <td style="text-align:center">
                            <a target="_blank" href="<?php echo e(url('invoice')); ?>/<?php echo e($recharge_reports->id); ?>">
                                <span class="btn btn-success btn-xs" style="font-size: 14px;"><i class="md md-visibility"></i>Receipt</span>
                            </a>
					    </td>  
                    <?php else: ?>
    					<td></td>
    					<td></td>
    				<?php endif; ?>
                    </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
					  	</tbody>
					  
				</table>
				 <?php echo $reportDetails->appends(Request::all())->links(); ?>

			</div>


</div>
 <meta name="_token" content="<?php echo csrf_token(); ?>"/>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>