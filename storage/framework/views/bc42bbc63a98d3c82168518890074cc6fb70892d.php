<?php $__env->startSection('content'); ?>
 <?php echo $__env->make('layouts.submenuheader', \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>
<script>
function recharge_pay() {
         
            $("#mobilbtn").text("Processing...");
            var mobile_number = $("#mobile_number").val();
            var mobile_provider = $("#mobile_provider").val();
            var mobile_amount = $("#mobile_amount").val();
			if(mobile_provider=='')
			{
				alert("Please select Operator");
				return false;
			}
			else if(mobile_amount=='')
			{
				alert("Please enter amount");
				return false;
			}
			else if(mobile_number=='')
				return false;
			{
				alert("Please enter DaraCardNumber");
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
		function getOffer()
		{
			var provider =$("#mobile_provider option:selected").text();
			var circle_id =$("#circle_id").val();
			var recharge_type =$("#recharge_type").val();
			if(circle_id =='')
			{
				alert("Please Select Circle");
				return false;
			}
			else if(recharge_type =='')
			{
				alert("Please Select Recharge type");
				return false;
			}
			else if(provider =='')
			{
				alert("Please Select Operator");
				return false;
			}
			var dataString = 'provider=' + provider + '&circle_id=' + circle_id+ '&recharge_type=' + recharge_type ;
			 $.ajax({
                type: "GET",
                url: "<?php echo e(url('get-data-card-plans')); ?>",
                data: dataString,
                dataType: 'json',
               beforeSend:function(){
					$("#getOffer").text("Processing...");
				},
                success: function (result) 
				{
					$("#getOffer").text("Get Offers");
					if(result.error)
					{
						content = "<p style='color:white;font-size: 22px;font-family: time;'>Offers is not available right now. <br>Please try again after sometime</p>";
						$('#offer_div').html(content);
						console.log(result.error);
					}
					else{
					var content=  '<table class="table table-hover table-dark" ><thead><tr><th>Operator Id</th><th>circleid</th><th>Recharge Amount</th><th>recharge_longdesc</th><th>recharge_shortdesc</th><th>recharge_type</th><th>recharge_validity</th></tr></thead> <tbody>';
						 $.each(result.data, function(key,val) {             
							content +='<tr><td>'+ val.operatorid +'</td><td>'+ val.circleid +'</td><td>'+ val.recharge_amount +'</td><td>'+ val.recharge_longdesc +'</td><td>'+ val.recharge_shortdesc +'</td><td>'+ val.recharge_type +'</td><td>' + val.recharge_validity + '</td></tr>';
						});  
						content +=' </tbody></table>';
						$('#offer_div').html(content);
					}
				}
            });
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
    $("#preRchHeader").html(providerName+ " Recharge");
    //$("#myModal").model(sh);
    $("#myModaldth").modal("toggle");
}     					
		
</script>
<?php echo $__env->make('agent.recharge.recharge-type', \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>
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
             <h4 class="modal-title" id="preRchHeader">Recharge</h4>
          <button type="button" class="close" data-dismiss="modal">&times;</button>
        </div>
        <div class="modal-body">
	<div class="col-md-8">
	<!-- Recharge Panel -->
	 <div class="form-group">
		  <label class="label" style="color:black">DataCard Number</label>
		  <input class="form-control" type="text" value="" name="number" id="mobile_number" placeholder="Enter Valid Number" maxlength="10">
		</div>
		<div class="form-group">
			<label class="label" style="color:black">Operator</label>
				<select id="mobile_provider" class='form-control' name="provider" readonly="" disabled="disabled" >
					<?php $__currentLoopData = $provider; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $prov): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
					<option value="<?php echo e($prov->id); ?>"><?php echo e($prov->provider_name); ?></option>
					<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
				</select>
			<!-- <select id="mobile_provider"class="form-control" name="provider">
				
				<option value="AIRTEL">AIRTEL</option>
				<option value="IDEA">IDEA</option>
				<option value="AIRCEL">AIRCEL</option>
				<option value="BSNL">BSNL</option>
				<option value="MTNL">MTNL</option>
				<option value="MTS MBLAZE">MTS MBLAZE</option>
				<option value="MTS MBROWSE">MTS MBROWSE</option>
				<option value="TATA PHOTON PLUS">TATA PHOTON PLUS</option>
				<option value="TATA PHOTON WHIZ">TATA PHOTON WHIZ</option>
				<option value="VODAFONE">VODAFONE</option>
				
			</select> -->
		</div>
		<div class="form-group">
		  <label class="label" style="color:black">Amount</label>
		  <input type="number" value="" class="form-control" name="amount" id="mobile_amount" placeholder="Enter valid Amount">
		</div>
		<!--  <div class="form-group">
		<label class="label" style="color:black">Circle</label>
		<select id="circle_id"class="form-control" name="circle_id">
		<option value=""> -- Select Circle --</option>
		<option value="Andhra Pradesh">Andhra Pradesh</option>
		<option value="Assam">Assam</option>
		<option value=" Bihar Jharkhand"> Bihar Jharkhand</option>
		<option value="Chennai">Chennai</option>
		<option value="Delhi NCR">Delhi NCR</option>
		<option value=" Gujarat"> Gujarat</option>
		<option value="Haryana">Haryana</option>
		<option value="Himachal Pradesh">Himachal Pradesh</option>
		<option value="Jammu Kashmir">Jammu Kashmir</option>
		<option value="Karnataka">Karnataka</option>
		<option value="Kerala">Kerala</option>
		<option value="Kolkata">Kolkata</option>
		<option value="Madhya Pradesh Chhattisgarh">Madhya Pradesh Chhattisgarh</option>
		<option value="Maharashtra">Maharashtra</option>
		<option value="Mumbai">Mumbai</option>
		<option value="North East">North East</option>
		<option value="Odisha">Odisha</option>
		<option value="Punjab">Punjab</option>
		<option value="Rajasthan">Rajasthan</option>
		<option value="Tamil Nadu">Tamil Nadu</option>
		<option value="UP East">UP East</option>
		<option value="UP West">UP West</option>
		<option value=" West Bengal"> West Bengal</option>
	</select>
		
 </div> -->
<!--  <div class="form-group">
	<label class="label" style="color:black">Recharge Type</label>
	 <select id="recharge_type" class="form-control">
		<option value=""> -- Select Recharge Type --</option>
		
		<option value="2g">2G Data </option>
		<option value="3g">3G Data</option>
		
	</select>
		
 </div> -->
		
		
		
		 <button  onclick="this.disabled=true;recharge_pay();" type="button" class="btn btn-success">Submit</button>
		  <!-- <button type="button" id="getOffer" class="btn btn-success" onClick="getOffer()">Get Offers</button> -->
		 
		</div>
	</div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        </div>
      </div>
    </div>
  </div>	
	
	<div class="col-md-8 table-responsive" id="offer_div" >
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
			  <td><?php if(in_array($recharge_reports->status_id,array(1,3,9))): ?><button id="re_check_<?php echo e($recharge_reports->id); ?>"onclick="this.disabled=true;recharge_status(<?php echo e($recharge_reports->id); ?>);" class="btn btn-primary">Check</button><?php endif; ?></td>
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