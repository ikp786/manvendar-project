<?php $__env->startSection('content'); ?>
<style>
.bootstrap-table{
    margin-top: -28px;
}
</style>
    <script>
	$(document).ready(function () {
		$('[data-toggle="tooltip"]').tooltip();
        $.noConflict();
        $('.customDatepicker').datepicker({
              autoclose: true,  
            format: "dd-mm-yyyy"
        });
		
    }); 
        function updateRecord(id) {
            var token = $("input[name=_token]").val();
            var dataString = 'id=' + id + '&_token=' + token;
            
			$.ajax({
                type: "GET",
                url: "<?php echo e(url('txn/get-report')); ?>",
                data: dataString,
                datatype: "json",
				beforeSend:function()
				{
					
					
				},
                success: function (data) {
					
                    $('#recordId').val(data.message.id);
                    $('#recordNumber').val(data.message.id);
                    $('#txnStatus').val(data.message.statusId);
                    $('#txnNumber').val(data.message.accountNumber);
                    $('#bankName').val(data.message.bankName);
                    $('#branchName').val(data.message.branchName);
                    $('#refId').val(data.message.bank_ref);
                    $('#custMobNumber').val(data.message.customerNumber);
                    $('#ifsc').val(data.message.ifsc);
                    $('#txnId').val(data.message.txnid);
                    $('#userDetails').val(data.message.userDetails);
                    $('#txnAmount').val(data.message.amount);
                    $('#btn-save').val("Update");
                   // $("#transactionDetailsModel").modal("toggle");
                }
            })

        }
		function validateExportForm()
		{
			if($("#SEARCHTYPE").val()=='')
			{
				alert("Please Select Search Criteria")
				return false;
			}
		}
        
        
		 /* Bellow function added by rajat */
		function refundTranaction()
        {
            if(confirm('Are you sure to transfer?'))
            {
                var id = $("#recordId").val();
                var number = $("#txnNumber").val();
                var txnid = $("#refId").val();
                var status = $("#txnStatus").val();
				if(txnid==''){
					alert("Enter Reference Id");
					$("#refId").focus()
					return false;
				}
                var token = $("input[name=_token]").val();
                var dataString = 'id=' + id +'&number='+ number + '&txnid=' + txnid +'&status='+ status;
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
                            //$('#btn-save').hide();
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
		
		function showApiResp(id) {
            var token = $("input[name=_token]").val();
            var dataString = 'number=' + id + '&export=search';
           $.ajax({
                type: "GET",
                url: "<?php echo e(url('api-response')); ?>",
                data: dataString,
                datatype: "json",
                beforeSend:function()
                {
                    $('#apiResponseContent').html('');
                },
                success: function (data) {
                    //alert('hi');
                    var content ="<table class='table'><thead><td>id</td><td>Type</td><td>Api Resp</td><td>Api Req</td><td>Time</td></tr></thead><tbody>";
                    $.each( data.message, function( key, value ) {
                    content +="<tr><td>"+value.id+"</td><td>"+value.api_type+"</td><td>"+value.message+"</td><td>"+value.request_message+"</td><td>"+value.created_at+"</td></tr>";
                    });
                    content +='</tbody></table>';
                    $('#apiResponseContent').html(content);
                }
            })
        }
    </script>

 <?php echo $__env->make('admin.admin-subtab.report-type', \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>

    <!-- Page-Title -->
    <div class="">
        <div class="col-sm-12">
            <div class="col-lg-6 col-md-6">
                <h4 class="page-title" style="color: black; "><?php echo e('AEPS REPORT'); ?></h4>
                
            </div>
        </div>
		
		

    <!--Basic Columns-->
    <!--===================================================-->
<div class="panel panel-default">
        <div class="panel-body">
		
           <?php if(in_array(Auth::user()->role_id,array(1,11,12,14,4))): ?>
          
              <form method="get" action="<?php echo e(Request::url()); ?>" onSubmit="return validateExportForm()" class="form-inline">
                 
               <input name="fromdate" class="form-control customDatepicker" type="text" value="<?php echo e((app('request')->input('fromdate')) ? app('request')->input('fromdate') : date('d-m-Y')); ?>" autocomplete="off"> 
            
            <input name="todate" class="form-control customDatepicker" type="text" value="<?php echo e((app('request')->input('todate')) ? app('request')->input('todate') : date('d-m-Y')); ?>" autocomplete="off">
                   
                      <?php echo e(Form::select('SEARCHTYPE', ['BALANCE_ENQUIRY'=>'BALANCE ENQUIRY','AEPS_COMMISSION'=>'AEPS COMMISSION','BALANCE_WITHDRAW'=>'BALANCE WITHDRAW','AEPS_SETTELMENT_APPROVED'=>'SETTELMENT APPROVED','AEPS_SETTELMENT_PENDING'=>'SETTELMENT PENDING'], null, ['class'=>'form-control','placeholder'=>"--Select Criteria--",'id'=>'SEARCHTYPE'])); ?>

                      <button name="export" value="SEARCH" type="submit" class="btn btn-primary btn-md" data-toggle="tooltip" title="Search" data-placement="bottom"><i class="fa fa-search" aria-hidden="true"></i>
                      </button>
					  <button name="export" value="EXPORT" type="submit" class="btn btn-basic btn-md" data-toggle="tooltip" title="Export" data-placement="bottom"><i class="fa fa-file-excel-o" aria-hidden="true"></i></button>
                    <a href="<?php echo e(Request::url()); ?>" class="btn btn-info  btn-md" data-toggle="tooltip" title="Reset" data-placement="bottom"><i class="fa fa-refresh" aria-hidden="true"></i>

                    </a>
                 
              </form>
         
           <?php endif; ?>
                            
    </div>
</div>
<br>
    <!--===================================================-->
    <div class="row">
        <div class="">
           <div class="" style="overflow-y: scroll; max-height:350px">
		<table id="mytable"  class="table table-bordered">
		<thead>
                    <tr>
					  <th >Date/Time </th>
					 <th >Id</th>
                       
                        <th>User</th>
                      
                        <th>Provider</th>
						<th>Bank Name<br>IFSC</th>
						 <th>Acc/Number</th>
                        <th>Txn Id</th>
                        <th>Description</th>
                        <!-- <th data-field="ackno" data-sortable="true">C_ref_Id</th> -->
                        <th>Amount</th>
                        <th>Credit </th>
						<th >Debit</th>
						<th >GST</th>
						<th >TDS</th>
						<!--<th >Commission</th>-->
                        <th data-field="total" data-align="center" data-sortable="true"
                            data-sorter="priceSorter">Balance
                        </th>
                        <th data-field="status" data-align="center" data-sortable="true"
                            data-formatter="statusFormatter">Status
                        </th>
                        <?php if(Auth::user()->role_id == 1): ?>
                        <th data-field="action" data-align="center" data-sortable="true">Action
                        </th>
                        <?php endif; ?>
                    </tr>
                    </thead>

                    <tbody>
                    <?php $__currentLoopData = $reports; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
					<?php $s = $value->created_at;
						$dt = new DateTime($s);?>
                    <?php if($value->status_id!=14): ?>
                        <tr>
						 <td><?php echo e($dt->format('d/m/Y')); ?><br><?php echo e($dt->format('H:i:s')); ?></td>
                           
                            <td><?php echo e($value->id); ?></td>
                           <td><a href="<?php echo e(url('user-recharge-report')); ?>/<?php echo e(@$value->user_id); ?>"><?php echo e(@$value->user->name); ?> ( <?php echo e(@$value->user->prefix); ?> - <?php echo e(@$value->user->id); ?>)</a>
                            </td>
                          
                            <td><?php echo e($value->api->api_name); ?></td>
							<!-- </td>-->
                            <td><?php echo e(@$value->aepssettlements->bank_name); ?><br><?php echo e(@$value->aepssettlements->ifsc); ?></td>
							<td><?php echo e(@$value->aepssettlements->account_number); ?></td>
							<td><?php echo e($value->txnid); ?></td>
							<td><?php echo e($value->description); ?></td>
                            <td><?php echo e(number_format($value->amount,2)); ?></td>
							<td><?php echo e(number_format($value->credit_charge,2)); ?></td>
							<td>
								<?php echo e(number_format(abs($value->debit_charge),2)); ?>

								
							</td>
                            <td><?php echo e(number_format($value->gst,2)); ?></td>
                            <td><?php echo e(number_format($value->tds,3)); ?></td>
                            <td><?php echo e(number_format($value->total_balance,2)); ?></td>
                            <td><?php echo e(@$value->status->status); ?></td>
							<td>
								<?php if(Auth::user()->role_id == 1): ?>
									<?php if(in_array($value->status_id,array(1,3,9)) && !in_array($value->api_id,array(10))): ?>
									<!--<a data-toggle="modal" href="#example" class="table-action-btn" onclick="updateRecord(<?php echo e($value->id); ?>)">Action</a>-->
								<?php elseif($value->api_id == 10 && $value->description=="AEPS_SETTELMENT_PENDING"): ?>
								<a data-toggle="modal" href="#example" class="table-action-btn" onclick="updateRecord(<?php echo e($value->id); ?>)">Action</a>						
								   <?php endif; ?>
								<?php if(in_array($value->status_id,array(1,2,3,9,20,21,24))): ?>
                                    <a data-toggle="modal" href="#showApiResponse" class="table-action-btn" onclick="showApiResp(<?php echo e($value->id); ?>)">Response</a>
                                <?php endif; ?>   
                            <?php endif; ?> 
							</td>
                        </tr>
                        <?php endif; ?>

                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                    </tbody>
                </table>
                <?php echo e($reports->appends(\Input::except('page'))->render()); ?> 
            </div>
        </div>
    </div>
<div class="container" id="showApiResponseModal">
    <div id="showApiResponse" class="modal fade" style="display: none;">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Api Resp</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button> 
                </div>
                <form id="frmTasks" name="frmTasks" class="form-horizontal" >
                   <div class="modal-body">
                        <div id="apiResponseContent" style="overflow: auto">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default waves-effect" data-dismiss="modal">Close</button>
                    </div>
                </form>
            </div>
        </div>
    </div>  
</div>
   

     <div class="container" id="transactionDetailsModel">
    <div id="example" class="modal fade" style="display: none;">
        <div class="modal-dialog modal-lg">
            <div class="modal-content modal-lg">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                    <h4 class="modal-title">Transaction Details</h4>
                </div>
				 <div class="modal-body">
              <form id="frmTasks" name="frmTasks" class="form-horizontal" novalidate="">
                    <?php echo csrf_field(); ?>


                   
                        <div style="display:none" id="name-error"></div>
                        <input type="hidden" name="recordId" value="" id="recordId">
						<div class="col-md-12">
						<div class="form-group col-md-6">
                            <label for="inputTask" class="col-sm-5 control-label">Record Number</label>
                            <div class="col-sm-7">
                                <input type="text" class="form-control has-error" id="recordNumber" name="recordNumber"
                                       placeholder="" value="" readonly>
                            </div>
                        </div>
                        <div class="form-group col-md-6">
                            <label for="inputTask" class="col-sm-5 control-label">Customer A/c Number</label>
                            <div class="col-sm-7">
                                <input type="text" class="form-control has-error" id="txnNumber" name="txnNumber"
                                       placeholder="Provider Name" value="" readonly>
                            </div>
                        </div> 
					</div>
					<div class="col-md-12">
						<div class="form-group col-md-6">
                            <label for="inputTask" class="col-sm-5 control-label">Customer Mob Number</label>
                            <div class="col-sm-7">
                                <input type="text" class="form-control has-error" id="custMobNumber" name="custMobNumber"
                                       placeholder="Customer Mobile Number" value="" readonly>
                            </div>
                        </div>
												<div class="form-group col-md-6">
                            <label for="inputTask" class="col-sm-5 control-label">Txn Amount</label>
                            <div class="col-sm-7">
                                <input type="text" class="form-control has-error" id="txnAmount" name="txnAmount"
                                       placeholder="Provider Name" value="" readonly>
                            </div>
                        </div>
					</div>
					<div class="col-md-12">
												<div class="form-group col-md-6">
                            <label for="inputEmail3" class="col-sm-5 control-label">Transaction ID</label>
                            <div class="col-sm-7">
                                <input type="text" class="form-control" id="txnId" name="txnId"
                                       placeholder="Provider Code" value="">
                            </div>
                        </div>
												<div class="form-group col-md-6">
                            <label for="inputEmail3" class="col-sm-5 control-label">RR Number</label>
                            <div class="col-sm-7">
                                <input type="text" class="form-control" id="refId" name="refId" placeholder="RR Number" value="">
                            </div>
                        </div>
					</div>
					<div class="col-md-12">
												<div class="form-group col-md-6">
                            <label for="inputEmail3" class="col-sm-5 control-label">Bank Name</label>
                             <div class="col-sm-7">
                                <input type="text" class="form-control" id="bankName" name="bankName" placeholder="Bank Name" value="">
                            </div>
                        </div>
												<div class="form-group col-md-6">
                            <label for="inputEmail3" class="col-sm-5 control-label">IFSC CODE</label>
                            <div class="col-sm-7">
                                <input type="text" class="form-control" id="ifsc" name="ifsc" placeholder="IFSC Code" value="">
                            </div>
                        </div>
					</div>
					<div class="col-md-12">
						
												<div class="form-group col-md-6">
                            <label for="inputEmail3" class="col-sm-5 control-label">Branch Name</label>
                            <div class="col-sm-7">
                                <input type="text" class="form-control" id="branchName" name="branchName" placeholder="Branch Name" value="">
                            </div>
                        </div>
                       <div class="form-group col-md-6">
                            <label for="inputEmail3" class="col-sm-5 control-label">Status</label>
                            <div class="col-sm-7">
                                <select class="form-control" name="txnStatus" id="txnStatus">
                                    <option value="1">Success</option>
                                    <option value="2">Failure</option>
                                    <option value="3">Pending</option>
                                    <!--<option value="20">Refund Pending</option>-->
                                </select>
                            </div>
                        </div>
					</div>


                    </div>
                    <div class="modal-footer">
                       
                        <button type="button" class="btn btn-info waves-effect waves-light" id="btn-save" value="add" onclick="refundTranaction()">Update</button>
						<img src="<?php echo e(url('/img')); ?>/loader.gif" id="loaderImg" class="loaderImg" style="display:none">
						
                        <button type="button" class="btn btn-default waves-effect" data-dismiss="modal">Close</button>
                    </div>
                </form>
            </div>
        </div>
    </div></div>
    <meta name="_token" content="<?php echo csrf_token(); ?>"/>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('admin.layouts.templatetable', \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>