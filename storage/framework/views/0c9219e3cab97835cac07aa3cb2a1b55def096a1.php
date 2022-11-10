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
	function TramocheckStatus(id,apiId)
	{
		var token = $("input[name=_token]").val();
		var number = $("#number").val();
		var dataString = 'id=' + id + '&mobile_number='+number;
		$.ajaxSetup({
		headers: {
			'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
		}
		});
		
		url = "<?php echo e(url('check-txn-status')); ?>"
		
		$.ajax({
			type: "post",
			url: url,
			data: dataString,
			dataType: "json",
			beforeSend:function(){
				$("#checkBtn_"+id).hide();
				$("#checkBtn_"+id).attr('disabled',true);
			},
			success: function (data) {
				$("#checkBtn_"+id).show();
				$("#checkBtn_"+id).attr('disabled',false);
				
				if(data.status==2){
				    alert('Transaction Failed : '+data.msg);
				}else
				{
				     alert(data.msg);
				}
				if((apiId==5 && data.status==43) || apiId==25 && data.status==1) 
				{  
					$("#checkstatusMessage_"+id).text(data.bankRefNo)
				}
			}
		})
	}
</script>
<?php echo $__env->make('agent.report.report-type', \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>

	<div class="col-md-12">
        <form method="get" action="<?php echo e(Request::url()); ?>" class="form-inline" role="form">    
			
			<label class="sr-only" for="payid">Number</label>
			<input name="number"  type="text" class="form-control" id="exampleInputEmail2"  value="<?php echo e(app('request')->input('number')); ?>" placeholder="Number">
		
			<input name="fromdate" style="width:140px" class="form-control customDatepicker" type="text" placeholder="From date" value="<?php echo e((app('request')->input('fromdate')) ? app('request')->input('fromdate') : date('d-m-Y')); ?>">
		
			<input name="todate" style="width:140px" class="form-control customDatepicker" type="text" placeholder="To date" value="<?php echo e((app('request')->input('todate')) ? app('request')->input('todate') : date('d-m-Y')); ?>">
			<?php if(in_array(Auth::user()->role_id,array(5))): ?>
			<?php echo e(Form::select('searchOf', ['34' => 'Accepted','1' => 'Success', '2' => 'Failed','3'=>"Pending",'4'=>"Refunded",'24'=>"Successfull",'21'=>"Refund Success",'18'=>"In-Process",'6'=>'DT'], (app('request')->input('searchOf')), ['class'=>'form-control','placeholder'=>"--Select Status--"])); ?>

			
			<?php echo e(Form::select('product', ['1' => 'Recharge/Bill Payment', '2' => 'Verification','4' => 'DMT 1','16' => 'DMT 2','25'=>"A2Z Plus Wallet",'5'=>"A2Z wallet",'10'=>'AEPS'], (app('request')->input('product')), ['class'=>'form-control','placeholder'=>"--Select Product--"])); ?> 
			<?php else: ?>
			<?php echo e(Form::select('searchOf', ['1' => 'Success', '2' => 'Failed','3'=>"Pending",'4'=>"Refunded",'21'=>"Refund Success",'6'=>'DT'], (app('request')->input('searchOf')), ['class'=>'form-control','placeholder'=>"--Select Status--"])); ?>

			<?php endif; ?>
			<?php echo e(Form::select('mode', ['APP' => 'APP', 'WEB' => 'WEB'], (app('request')->input('mode')), ['class'=>'form-control','placeholder'=>"--Select Mode--"])); ?> 
			<button name="SEARCH" value="SEARCH" type="submit" class="btn btn-primary btn-md">
			<i class="fa fa-search"></i></button>
			<button title="Excel"  name="export" value="EXPORT" type="submit" class="btn btn-success waves-effect waves-light m-l-10 btn-md"><span class="glyphicon glyphicon-find"></span><i class="fa fa-file-excel-o" aria-hidden="true"></i>
			</button>
			<button title="PDF"  name="pdf" value="PDF" type="submit" class="btn btn-danger waves-effect waves-light m-l-10 btn-md"><span class="glyphicon glyphicon-find"></span><i class="fa fa-file-pdf-o" aria-hidden="true"></i>
			</button>
			<button title="CSV"  name="csv" value="CSV" type="submit" class="btn btn-info waves-effect waves-light m-l-10 btn-md"><span class="glyphicon glyphicon-find"></span><i class="fa fa-file-text-o" aria-hidden="true"></i>
			</button>
			<a href="<?php echo e(url('transaction-report')); ?>" class="btn btn-primary  btn-md"><i class="fa fa-refresh"></i></a>
        </form>
	</div>
	<br><br><br>
	
<div class="box">
		<table  class="table table-bordered table-striped" id="example_mn2">
			<thead >
				<tr>
					<th>Date/Time</th>
					<th>ID</th>
					<th>Remitter Number</th>
					<th>Acc/Mob/K Number<br>Bene Name</th>
					<th style="min-width: 212px;font-size: 12px;">Bank Name/<br>IFSC</th>
					<th>Operator Txn Id<br>/Remark</th>
					<th>Amount</th>
					<th>Web/App</th>
					<th style="width:100px !important">Status</th>
					<th>Bank RR Number/<br>Check</th>
					<th>Description</th>
					<th>Receipt</th>
					<th>Credit/Debit</th>
					<th>Opening Bal</th>
					<th>Credit Amount</th>
					<th>Debit Amount</th>
					<th>TDS</th>
					<th>Service Tax</th>
					<th>Balance</th>
					<th>Txn Type</th>
					<th>fund Transfer</th>
					<th>Complain</th>
				</tr>
			</thead>
			<tbody>
				<?php $__currentLoopData = $reports; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $recharge_reports): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
				<?php $s = $recharge_reports->created_at;
				$dt = new DateTime($s);?>
				<tr class="<?php echo e(@$recharge_reports->status->status); ?>-text">
					<td><?php echo e($dt->format('d-m-y')); ?><br><?php echo e($dt->format('H:i:s')); ?></td>
					<td><?php echo e($recharge_reports->id); ?></td>
					<td><?php echo e($recharge_reports->customer_number); ?></td>
					<td><?php echo e($recharge_reports->number); ?>

					<p><?php echo e(@$recharge_reports->biller_name); ?></p>
					<p><span style="font-weight:bold;"><?php echo e(@$recharge_reports->beneficiary->name); ?></p></span>
					</td>
				    <td>
				   <?php if(in_array($recharge_reports->api_id,array(2,10))): ?>
					<?php 
					$content = explode("(",$recharge_reports->description);
						try{
							echo $recharge_reports->description;
						//echo $content[0]; echo "<br>";echo  $content[1];
					}
					catch(\Exception $e)
					{
						echo $recharge_reports->description;
					}
					?>
				   
					<?php else: ?>
						<p style="font-weight: bold">
						<?php echo e(@$recharge_reports->beneficiary->bank_name); ?></p>
						<p style="font-style: italic;"><?php echo e(@$recharge_reports->beneficiary->ifsc); ?>

						</p>
					  <?php endif; ?>  
				    </td>
					<td>    
    					<?php if($recharge_reports->api_id=='25'): ?>
    					    <?php echo e($recharge_reports->paytm_txn_id); ?>

    				    <?php else: ?>  
    				        <?php echo e($recharge_reports->txnid); ?>

    					<?php endif; ?>
					<br><?php echo e($recharge_reports->remark); ?></td>
				    <td><?php echo e(number_format($recharge_reports->amount,2)); ?></td>
				    <td><?php echo e($recharge_reports->mode); ?></td>
				    <td><?php echo e(@$recharge_reports->status->status); ?> 
				        <br><?php echo e($recharge_reports->txn_initiated_date); ?>

						<span id="checkstatusMessage_<?php echo e($recharge_reports->id); ?>" style="color:green"></span>
						<p><?php if($recharge_reports->recharge_type==0 && $recharge_reports->txnid !="DT" && !in_array($recharge_reports->api_id,array(2,10))): ?> 
							<?php echo e(($recharge_reports->channel==2)?"IMPS":"NEFT"); ?></p><?php endif; ?> 
					</td>
					<td><?php if(@$recharge_reports->status_id !=4): ?>
						<?php echo e($recharge_reports->bank_ref); ?> 	
						<?php endif; ?>						
						<?php if(in_array(@$recharge_reports->status_id,array(1,3,9,18,34)) && $recharge_reports->api_id !=10 ): ?>
						    <?php if($recharge_reports->api_id=='27'): ?>
						         <input type="button" disabled id ="checkBtn_<?php echo e($recharge_reports->id); ?>" class="btn btn-primary btn-xs" value="Check"/>
					         <?php else: ?>
						         <input type="button" id ="checkBtn_<?php echo e($recharge_reports->id); ?>" onclick="TramocheckStatus(<?php echo e($recharge_reports->id); ?>,<?php echo e($recharge_reports->api_id); ?>)" class="btn btn-primary btn-xs" value="Check"/>
					        <?php endif; ?>
						   	<?php elseif(@$recharge_reports->status_id ==2): ?>
						   <?php echo e(@$recharge_reports->fail_msg); ?>

						<?php endif; ?>
					</td>

					<td><?php if($recharge_reports->recharge_type== 1): ?>
							<?php echo e(@$recharge_reports->provider->provider_name); ?>  
							<?php else: ?>
							<?php echo e(@$recharge_reports->api->api_name); ?> 
						 <?php endif; ?>
					 </td> 
					<td style="text-align:center">
					<?php if(in_array(@$recharge_reports->status_id,array(1,3,9,18,24,34))): ?>
					<a target="_blank" href="<?php echo e(url('invoice')); ?>/<?php echo e($recharge_reports->id); ?>">
					<span class="btn btn-info btn-xs" style="font-size: 14px;">
					<i class="md md-visibility"></i>Receipt</span>
					</a><?php endif; ?>
					</td> 						 
					<td><?php echo e($recharge_reports->type); ?></td>
					<td><?php echo e(number_format($recharge_reports->opening_balance,2)); ?></td>
					<td><?php echo e($recharge_reports->credit_charge); ?></td>
					<td><?php echo e($recharge_reports->debit_charge); ?></td>
					<td><?php echo e(number_format($recharge_reports->tds,3)); ?></td>
					<td><?php echo e(number_format($recharge_reports->gst,2)); ?></td>
					<td><?php echo e(number_format($recharge_reports->total_balance,2)); ?></td>
					<td><?php echo e($recharge_reports->txn_type); ?></td>
					<td><?php if($recharge_reports->txnid=="DT"): ?>
							<?php echo e($recharge_reports->description); ?>

						<?php endif; ?>
					</td> 
					<td>
						<?php if(in_array(@$recharge_reports->status_id,array(1,3,9,18,24,34))): ?>
							<a onclick="Complain(<?php echo e($recharge_reports->id); ?>)" data-toggle="modal" href="#example">Complain</a> 
						<?php endif; ?></td>
				</tr>
				<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
			</tbody>
		</table>
			<!-- <?php echo e($reports->appends(\Input::except('page'))->render()); ?> --> 
	</div>
<div class="container" id="doComplan">
		<div id="example" class="modal fade" style="display: none;">
			<div class="modal-dialog">
				<div class="modal-content">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
						<h4 class="modal-title">Complain</h4>
					</div>
					<div class="modal-body">
					  <form id="frmTasks" name="frmTasks" class="form-horizontal" novalidate="">
							<?php echo csrf_field(); ?>

							
							<input type="hidden" id="complainTxnId">
						   
							  <div class="form-group">
								<label for="inputEmail3" class="col-sm-3 control-label">Select</label>
								<div class="col-sm-9">
								   <select id="issueType" class="form-control" name="issueType">
										<option value="AMOUNT NOT CREDIT">AMOUNT NOT CREDIT</option>
										<option value="RECHARGE NOT CREDIT">RECHARGE NOT CREDIT</option>
										<option value="PENDING TXN">PENDING TXN</option>
										<option value="OTHERS">OTHERS</option>
									</select>
								</div>
							</div>
							<div class="form-group">
								<label for="inputEmail3" class="col-sm-3 ">Remark</label>
								
								<div class="col-sm-9">
									<textarea id="complainRemark" class="form-control" name="complainRemark" value="" placeholder="Remarks...."></textarea>
								</div>
							</div>
						</form>			
					</div>   
					<div class="modal-footer" style="border-top:0px ">
						
						<button type="button" class="btn btn-info waves-effect waves-light"
								id="btn" onclick="saveComplain()" style="dispplay:none;color:white;">Submit
						</button>
						<button type="button" class="btn btn-default waves-effect" data-dismiss="modal">Close</button> 
					</div>
				</div>     
			</div>
		</div>
		</div>

<script>
$(function(){ 
    $('#example_mn2').DataTable({
        'paging'      : true,
        'lengthChange': true,
        'searching'   : true,
        'ordering'    : false,
        'info'        : true,
        'autoWidth'   : true,
        'scrollY'     : 500,
        'scrollX'     : true,
        "lengthMenu": [ [10, 50, 100, 500, -1], [10, 50, 100, 500, "All"] ],
        "pageLength": 50,
        "columnDefs": [
          { "width": "130px", "targets": 7 } 
        ]
    })
});
</script>

<script type="text/javascript">


 function saveComplain()
        {
        	 var token = $("input[name=_token]").val();
        
            var complainTxnId = $('#complainTxnId').val();
            var issueType = $("#issueType").val();
            var complainRemark = $("#complainRemark").val();
            var dataString = 'complainTxnId=' + complainTxnId + '&issueType=' + issueType + '&complainRemark=' + complainRemark+'&_token=' + token;
            $.ajax({
                type: "POST",
                url: "<?php echo e(url('store_complain_req')); ?>",
                data: dataString,
                datatype: "json",
                success: function (msg) {
					alert(msg.message);
					if(msg.status==1){
						location.reload();
					}
                }
            });

        }

		function Complain(id) {
            var token = $("input[name=_token]").val();
			$('#complainTxnId').val(id);
			$('#complainRemark').val('');
        }
</script>


 <meta name="_token" content="<?php echo csrf_token(); ?>"/>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>