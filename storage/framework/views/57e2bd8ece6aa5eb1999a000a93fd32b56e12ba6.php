<?php $__env->startSection('content'); ?>
<?php echo $__env->make('layouts.submenuheader', \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>
<script>
function recharge_pay() {
         
            $("#mobilbtn").text("Processing...");
            var mobile_number = $("#mobile_number").val();
            var mobile_provider = $("#mobile_provider").val();
            var mobile_amount = $("#mobile_amount").val();
			if(mobile_number =='')
			{
				alert("Please enter valid mobile number");
				return false;
			}
			else if(mobile_amount =='')
			{
				alert("Please enter amount");
				return false;
			}
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                }
            })
             if (confirm('Are you sure you want to Recharge Amount '+ mobile_amount +'?')) {
            var dataString = 'number=' + mobile_number + '&provider=' + mobile_provider + '&amount=' + mobile_amount;
            $.ajax({
                type: "POST",
                url: "<?php echo e(url('recharge')); ?>",
                data: dataString,
                success: function (msg) {
               
                    $('#recharge_button').prop('disabled',false);
                    $("#mobile_number").val('');
                    $("#mobile_provider").val('');
                    $("#mobile_amount").val('');
                    $("#trbutto").prop("disabled", false);
                    $("#mobile_amount").val();
                    $("#mobilbtn").text("Pay Now");
                    if (msg.status == 'success') {
						    refreshBalance();
                            $("#showaccountnumber").html(mobile_number);
                            $("#showcustomernumber").html(mobile_provider);
                            $("#showamount").html(mobile_amount);
                            $("#showid").html(msg.operator_ref);
                            $("#customer_name").html(status);
                            $("#myModal").modal("toggle");
                        //swal("Success", msg.message, "success");
                        //window.location.reload();
                    } else {
                        alert(msg.message);
                        //window.location.reload();
                    }
                }
            });
            }
            else
            {
                $("#trbutto").attr("disabled", false);
                $("#trbutto").text("Pay Now");
            }
        }
function recharge_status(id)
    {
        var token = $("input[name=_token]").val();
            var dataString = 'id=' + id;
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                }
            })
            $.ajax({
                type: "POST",
                url: "<?php echo e(url('/re-check-status')); ?>",
                data: dataString,
                datatype: "json",
                success: function (data) {
                    $('#re_check_'+id).prop('disabled',false);
                    alert(data.message);
                }
        }); 
    } 


function openRechargeModel(provider_id) {
    $("#mobile_provider").val(provider_id);
    var providerName = $("#mobile_provider option:selected").text();
    $("#preRchHeader").html(providerName+ " Bill");
    //$("#myModal").model(sh);
    $("#myModaldth").modal("toggle");
}     
</script>

<?php echo $__env->make('agent.bbps.bbps-type', \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>

<div class="row col-md-12">
    <?php $__currentLoopData = $provider; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $prov): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <img src="<?php echo e(url('/')); ?>/<?php echo e($prov->provider_image); ?>" id="providerId_<?php echo e($prov->id); ?>" class="providerImage" onClick="openRechargeModel(<?php echo e($prov->id); ?>)"  style=" width: 7%;height: 1%;padding-right:8px" data-toggle="tooltip" data-placement="top" title="<?php echo e($prov->provider_name); ?>">
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
</div>

<div class="modal fade" id="myModaldth" role="dialog">
    <div class="modal-dialog">
      <!-- Modal content-->
      <div class="modal-content">
        <div class="modal-header">
             <h4 class="modal-title" id="preRchHeader">Postpaid Bill</h4>
          <button type="button" class="close" data-dismiss="modal">&times;</button>
        </div>
        <div class="modal-body">
            <div class="col-md-8" >
                <?php echo $__env->make('partials.message_error', \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>
                <!-- Recharge Panel -->
                <div class="form-group">
                  <label class="label" style="color:black">Mobile Number</label>
                  <input class="form-control" type="text" value="" name="number" id="mobile_number" placeholder="Enter Valid Number" maxlength="10">
                </div>
                <div class="form-group">
                    <label class="label" style="color:black">Operator</label>
                        <select id="mobile_provider" class='form-control' name="provider" readonly="" disabled="disabled" >
                            <?php $__currentLoopData = $provider; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $prov): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($prov->id); ?>"><?php echo e($prov->provider_name); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                </div>
                <div class="form-group">
                    <label class="label" style="color:black">Amount</label>
                    <input type="number" value="" class="form-control" name="amount" id="mobile_amount" placeholder="Enter valid Amount">
                </div>
                <button  onclick="recharge_pay();" type="button" class="btn btn-success">Submit</button>
            </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        </div>
      </div>
    </div>
  </div>
			
<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header warning">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">Recharge Detail</h4>
            </div>
            <div class="modal-body">
                <div id="dvContents" style="border: 1px dotted black; padding: 5px; width: 100%">
                    <form class="form-horizontal">
                        <input type="hidden" value="" id="user_id" name="user_id">
                        <input id="c_bene_id" type="hidden">
                        <input id="c_sender_id" type="hidden">
                        <div class="form-group">
                            <label for="bank_account" class="control-label col-sm-4">
                                Agent Name : </label>

                            <label for="bank_account" class="control-label col-sm-4">
                                <?php echo e(Auth::user()->name); ?></label>

                        </div>
                        <!--<div class="form-group">
                            <label for="bank_account" class="control-label col-sm-4">
                                Customer Name : </label>

                            <label for="bank_account" id="customer_name" class="control-label col-sm-4">
                            </label>

                        </div>-->
                       <!-- <div class="form-group">
                            <label for="bank_account" class="control-label col-sm-4">
                                Customer Number : </label>
                            <label id="showcustomernumber" for="bank_account" class="control-label col-sm-4">
                            </label>

                        </div>-->
                        <div class="form-group">
                            <label for="bank_account" class="control-label col-sm-4">
                                Mobile Number : </label>

                            <label id="showaccountnumber" for="bank_account" class="control-label col-sm-4">
                            </label>

                        </div>
                        <div class="form-group">
                            <label for="bank_account" class="control-label col-sm-4">
                                Amount : </label>

                            <label id="showamount" for="bank_account" class="control-label col-sm-4">
                            </label>

                        </div>
                       <!-- <div class="form-group">
                            <label for="bank_account" class="control-label col-sm-4">
                                Fee :</label>

                            <label id="showfee" for="bank_account" class="control-label col-sm-4">
                                2%
                            </label>

                        </div>-->
                        <div class="form-group">
                            <label for="bank_account" class="control-label col-sm-4">
                                Txid : </label>

                            <label id="showid" for="bank_account" class="control-label col-sm-4">
                            </label>
                        </div>
                        <div class="form-group">
                            <label for="bank_account" class="control-label col-sm-4">
                                Status : </label>

                            <label id="statusnew" for="bank_account" class="control-label col-sm-4">
                                Success</label>
                        </div>
                        <div class="form-group">
                            <label for="bank_account" class="control-label col-sm-4">
                                Date &amp; Time : </label>

                            <label for="bank_account" class="control-label col-sm-4">

                                <?php echo e($mytime = Carbon\Carbon::now()); ?>



                            </label>
                        </div>
                        <div class="form-group">
                            <label for="bank_account" class="control-label col-sm-4">
                                Thanks! <?php echo e(Auth::user()->company->company_name); ?> </label>

                            <label for="bank_account" class="control-label col-sm-4">

                                <?php echo e(Auth::user()->mobile); ?>


                            </label>
                        </div>

                    </form>
                </div>
            </div>
            <div class="modal-footer">
                <input type="button" onclick="PrintDiv();" value="Print"/>
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
<div class="text-right">
	<a href="<?php echo e(route('recharge-txn-history')); ?>?service_id=<?php echo e(@$serviceId); ?>" > Transaction History</a>
 </div>
<div class="ex1" style="overflow-y: scroll">  
    <table id="tableTypeThree" class="table table-bordered table-hover">
        <thead>

            <tr>
                 <th align="center">Date/Time</th>
              <th>ID </th>
              <th>User</th>
              <th>Txn ID </th>
              <th >Provider</th>
              <th>Number</th>
              <th>Amount</th>
              <th>Commission</th>
               <th>Status</th>
               <th>Action</th>
               <th>Report</th>
            </tr>
        </thead>
        <tbody>
            <?php $__currentLoopData = $report; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $recharge_reports): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php $s = $recharge_reports->created_at;
                $dt = new DateTime($s);?>
            <tr class="odd gradeX" style="background-color:white">
              <td align="center"><?php echo e($dt->format('d/m/Y')); ?><br><?php echo e($dt->format('H:i:s')); ?></td>
              <td><?php echo e($recharge_reports->id); ?></td>
              <td><?php echo e($recharge_reports->user->name); ?></td>
              <td><?php echo e($recharge_reports->txnid); ?></td>
              <td><?php echo e(@$recharge_reports->provider->provider_name); ?></td>
              <td><?php echo e($recharge_reports->number); ?></td>
              <td><?php echo e($recharge_reports->amount); ?></td>
              <td><?php echo e($recharge_reports->profit); ?></td>
              <td> <?php echo e($recharge_reports->status->status); ?></td>
              <td><?php if(in_array($recharge_reports->status_id,array(1,3,9))): ?><button id="re_check_<?php echo e($recharge_reports->id); ?>"onclick="this.disabled=true;recharge_status(<?php echo e($recharge_reports->id); ?>);" class="btn btn-primary">Check</button> <?php endif; ?></td>
                <td style="text-align:center">
                  <?php if(in_array($recharge_reports->status_id,array(1,3,9))): ?>
                    <a target="_blank" href="<?php echo e(url('invoice')); ?>/<?php echo e($recharge_reports->id); ?>">
                        <span class="btn btn-success btn-xs" style="font-size: 14px;"><i class="md md-visibility"></i>Receipt</span>
                    </a>
                <?php endif; ?>
                </td>  
            </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </tbody>
    </table>
</div>    
 <meta name="_token" content="<?php echo csrf_token(); ?>"/>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>