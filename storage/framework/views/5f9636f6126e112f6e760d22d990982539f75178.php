<?php $__env->startSection('content'); ?>
<?php echo $__env->make('layouts.submenuheader', \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>
<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.8.3/jquery.min.js"></script>
<script type="text/javascript">
    $(function () {
        $("#btnPrint").click(function () {
            var contents = $("#dvContents").html();
            var frame1 = $('<iframe />');
            frame1[0].name = "frame1";
            frame1.css({ "position": "absolute", "top": "-1000000px" });
            $("body").append(frame1);
            var frameDoc = frame1[0].contentWindow ? frame1[0].contentWindow : frame1[0].contentDocument.document ? frame1[0].contentDocument.document : frame1[0].contentDocument;
            frameDoc.document.open();
            //Create a new HTML document.
            frameDoc.document.write('<html><head><meta http-equiv="Content-Type" content="text/html; charset=utf-8"><title>DIV Contents</title>');
            frameDoc.document.write('</head><body>');
            //Append the external CSS file.
            frameDoc.document.write('<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.0/css/bootstrap.min.css">');
            frameDoc.document.write('<link href="style.css" rel="stylesheet" type="text/css" />');
            //Append the DIV contents.
            frameDoc.document.write(contents);
            frameDoc.document.write('</body></html>');
            frameDoc.document.close();
            setTimeout(function () {
                window.frames["frame1"].focus();
                window.frames["frame1"].print();
                frame1.remove();
            }, 500);
        });
    });
</script>
<script type="text/javascript">
function updateFee()
	{
		var billAmount=$("#printAmount").text();
		var billChargeAmount= $("#billChargeAmount").val();
		var finalAmount = Number(billChargeAmount) + Number(billAmount)
		$("#printAmount").text(finalAmount);
		$.ajax({
                type: "get",
                url: "<?php echo e(url('amount-in-words')); ?>",
                data: "amount="+finalAmount,
				dataType:"json",
				beforeSend:function(){
					
					$("#updageFeeSpan").hide();
					
				},
                success: function (msg) 
				{
					console.log(msg);
					$("#printAmountInWord").text(msg);
					
				}
            });
		
	}
	function getProvider()
	{
		var state_id  = $("#state_id").val();
		var dataString = "state_id="+state_id
		$.ajax({
					type: "get",
					url: "<?php echo e(url('/get-provider-name')); ?>",
					data: dataString,
					success: function (data) {
					 var combo = '';
						$.each(data.message, function (i, el) {
							console.log(i)
					
					   combo += "<img src='"+el.provider_image+"' id='providerId_"+el.id+"'class='table-action-btn' name='provider_image' onClick='openRechargeModel("+el.id+")' style=' width:12%;height: 6%;padding-right:9px' data-toggle='tooltip' data-placement='top' title='"+el.provider_name+"'>"
								
							});
							 var comboOne = $("<select class = 'form-control' id ='provider' name='provider' readonly='' disabled='disabled'>");
							comboOne.append();
							$.each(data.message, function (i, el) {
							comboOne.append("<option value="+ el.id +" >" + el.provider_name + "</option>"); 
							
							});
							comboOne.append("</select>");
							$("#provider_list").html(comboOne);
							
							$("#ImageId").html(combo); 
					}
				});
	}
function fetchBillAmount()
{
	var ca_number  = $("#ca_number").val();
	var provider  = $("#provider").val();
	if(ca_number == '')
	{
		alert("Please Enter valid CA Number");
		return false
	}
	
	var dataString = "ca_number="+ca_number+"&provider="+provider
	$.ajax({
                type: "get",
                url: "<?php echo e(url('fetch-bill-amount')); ?>",
                data: dataString,
				dataType:"json",
				beforeSend:function(){
					
					$("#offer_div").html('')
					$("#vbil").hide()
					$("#viewBillLoader").show();
					$("#billerName").val('')
					$("#mobile_amount").val('')
					
				},
                success: function (msg) 
				{
					$("#vbil").show()
					$("#viewBillLoader").hide()
					var content=  '<table class="table table-striped"><tbody>';
					if(msg.status==1)
					{
						content +="<tr><td>Name</td><td>"+msg.content.customerName+"</td></tr>";
						content +="<tr><td>Bill Number</td><td>"+msg.content.billNumber+"</td></tr>";
						content +="<tr><td>Amount</td><td>"+msg.content.dueAmount+"</td></tr>";
						content +="<tr><td>Bill Date</td><td>"+msg.content.billDate+"</td></tr>";
						content +="<tr><td>Due Date</td><td>"+msg.content.dueDate+"</td></tr>";
							$("#billerName").val(msg.content.customerName)
							$("#mobile_amount").val(msg.content.dueAmount); 
							$("#bill_due_date").val(msg.content.dueDate); 
							$("#mobile_amount").prop('readonly','readonly');
							$("#bill_due_date").prop('readonly','readonly');
							$("#ca_number").prop('readonly','readonly');
							$("#billerName").prop('readonly','readonly');
							content +='</tbody></table>';
							$('#offer_div').html(content);
							
					}
					
					else if(msg.status == 0){
						content +='</tbody></table><div style = "background-color:white;text-align: center;" > <h4 style="    font-family: time;color:red;">'+msg.content+'</h4></div>';
						$('#offer_div').html(content);
						
					}
				}
            });
}
	function recharge_pay() {
		var ca_number = $("#ca_number").val();
		var provider = $("#provider").val();
		var mobile_amount = $("#mobile_amount").val();
		var billerName  = $("#billerName").val();
		var consumerNumber  = $("#consumerNumber").val();
		var bill_due_date  = $("#bill_due_date").val();
		var mobilePattern = /^[6789]\d{9}$/;
		var amountPattern = /^[0-9]+$/;
			if(ca_number =='')
			{
				alert("Please enter valid K Number");
				return false;
			}
			else if(mobile_amount =='')
			{
				alert("Please enter amount");
				return false;
			}
			
			else if(billerName =='')
			{
				alert("Biller Name can not be null");
				return false;
			}
			else if(consumerNumber =='')
			{
				alert("Please enter counsumer mob number");
				$('#consumerNumber').focus();
				return false;
			}
			else if(consumerNumber.length != 10)
			{
				alert("Please enter 10 digits consumer number");
				$('#consumerNumber').focus();
				return false;
				
			}
			else if(!consumerNumber.match(mobilePattern))
			{
				alert('Invalid Consumer  Number');
				$('#consumerNumber').focus();
				return false;
			}
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                }
            })
             if (confirm('Are you sure you want to Pay Electricity Amount '+ mobile_amount +'?')) {
            var dataString = 'number=' + ca_number + '&provider=' + provider + '&amount=' + mobile_amount +'&billerName='+billerName+'&consumerNumber='+ consumerNumber +'&bill_due_date='+ bill_due_date;
             $.ajax({
                type: "POST",
                url: "<?php echo e(url('store-bbps')); ?>",
                data: dataString,
				beforeSend:function()
				{
					$("#recharge_button").prop('disabled',true);
					$("#recharge_button").text('Bill Submitted');
				    //	$("#recharge_button").hide();
					//$("#billPayLoader").show();
				},
                success: function (msg) 
				{
				    
					//$("#recharge_button").show();
					//$("#billPayLoader").hide();
				    $("#recharge_button").text('Submit');
               		$('#recharge_button').prop('disabled',false);
                    //$("#ca_number").val('');
                   // $("#provider").val('');
                   
                    $("#trbutto").prop("disabled", false);
                    
                    $("#mobilbtn").text("Pay Now");
					
                    if (msg.status == "ACCEPTED" ||msg.status == "SUCCESS" || msg.status =="PENDING"|| msg.status =="SUCCESSFULLY SUBMITTED") 
					{
						refreshBalance();
						providerName = $("#provider option:selected").text();
						$("#printBillerName").text(billerName);
						$("#printCustomerNumber").text(consumerNumber);
						$("#printBillerName").text(billerName);
						$("#printAmount").text(mobile_amount);
						$("#printConsumerNumber").text(ca_number);
						$("#printTBody").html("<tr><td>"+msg.txnTime+"</td><td>"+providerName+"</td><td>"+msg.operator_ref+"</td><td>"+msg.payid+"</td><td>"+mobile_amount+"</td><td>"+msg.status+"</td></tr>");
						$("#mobile_amount").val('');
						$("#myModalbill").modal("toggle");
						$("#myReciept").modal("toggle");						                     
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


	function viewBill (){
	$("#vbil").hide();
    $("#vbilltext").show();
    var token = $("input[name=_token]").val();
    var ca_number = $("#ca_number").val();
    var provider = $("#provider").val();
    var mobile_amount = $("#mobile_amount").val();
    var op1 = $("#optional1").val();
    var op2 = $("#optional2").val();
    var cycle = $("#cycle").val();
    var dataString = 'number=' + ca_number + '&provider=' + provider + '&amount=' + mobile_amount + '&optional1=' + op1 + '&optional2=' + op2 + '&_token=' + token;

            $.ajax({
                type: "GET",
                url: "<?php echo e(url('check-bbps-bill')); ?>",
                data: dataString,
                dataType: "json",
                success: function (msg) {
                $("#vbil").show();
                $("#vbilltext").hide();
                if (msg.status == 1) {
                alert(msg.message);
                $("#mobile_amount").val(msg.amount);
                } else {
                alert(msg.message);
                }

                }
            }); 
	}
	function removeReadonly()
	{
		$('#mobile_amount').prop('readonly',false);
	}
	
	function openRechargeModel(provider_id) {
		$("#offer_div").html('');
		$("#mobile_amount").attr("readonly", false); 
		$("#ca_number").attr("readonly", false); 
		$("#billerName").attr("readonly", false); 
		$("#myBbpsForm").trigger('reset');
		$("#provider").val(provider_id);
		var providerName = $("#provider option:selected").text();
		$("#preRchHeader").html(providerName+ "Bill");
		$("#myModalbill").modal("toggle");
		//$("#myModalbill").trigger('reset');
	}     
</script>
<?php echo $__env->make('agent.bbps.bbps-type', \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>
<div class="form-inline col-md-12">
	<div class="form-group col-md-3">
		<label class="label" style="color:black">Circle</label>
		<?php echo e(Form::select('state_id', $state_list, 8, array('class' => 'form-control','id' => 'state_id','onChange'=>'getProvider()'))); ?>	
	</div>
	<div class="form-group col-md-9" id="ImageId">
		<?php $__currentLoopData = $provider; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $prov): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
		   <img src="<?php echo e(url('/')); ?>/<?php echo e($prov->provider_image); ?>" id="providerId_<?php echo e($prov->id); ?>" class="providerImage" onClick="openRechargeModel(<?php echo e($prov->id); ?>)" style=" width: 12%;height: 6%;padding-right:9px" name="provider_image" data-toggle="tooltip" data-placement="top" title="<?php echo e($prov->provider_name); ?>">
		 <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
	</div> 
</div>
<div class="modal fade" id="myModalbill" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog">
      <!-- Modal content-->
      <div class="modal-content">
        <div class="modal-header">
             <h4 class="modal-title" id="preRchHeader">Postpaid Bill</h4>
          <button type="button" class="close" data-dismiss="modal">&times;</button>
        </div>
        <div class="modal-body col-md-12"> 
			<div class="col-md-7">
				<?php echo $__env->make('partials.message_error', \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>
				<?php echo Form::open(array('url' =>'bbps','id'=>'myBbpsForm','files'=>true,)); ?>

				<!--<div class="form-group">
				<label class="label" style="color:black">Circle</label>
				<?php echo e(Form::select('state_id', $state_list, 7, array('class' => 'form-control','id' => 'state_id','onChange'=>'getProvider()'))); ?>	
				</div>-->
				<div class="form-group">
					<label class="label" style="color:black">Operator</label>
					<div id="provider_list">
					 <select id="provider" class='form-control' name="provider" readonly="" disabled="disabled">
						<?php $__currentLoopData = $provider; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $prov): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
							<option value="<?php echo e($prov->id); ?>"><?php echo e($prov->provider_name); ?></option>
						<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
					</div>
				</div>
				 <div class="form-group">
					<label class="label" style="color:black">K Number</label>
					  <input value="" class="form-control" name="ca_number" id="ca_number" placeholder="Enter Consumer Number">
					  <span style="position: relative;top: -30px;right: 10px;float: right;"><a style="cursor: pointer;" onclick="fetchBillAmount()" id="vbil">View Bill</a><img src="<?php echo e(url('/loader/loader.gif')); ?>" id="viewBillLoader" class="loaderImg" style="display:none"/></span>
					
				</div>
				<div class="form-group">
					<label class="label" style="color:black">Amount</label>
					 <input value="" class="form-control" name="amount" placeholder="Enter valid Amount" id="mobile_amount">
					 <!--<span  style="position: relative;top: -37px;float: right;"><a style="cursor: pointer;" id="editable" class="btn btn-primary" onClick="removeReadonly()">Edit</a></span>-->
					 
				</div>
				<div class="form-group">
					<label class="label" style="color:black">Biller Name</label>
					 <input value="" class="form-control" name="billerName" placeholder="Biller Name" id="billerName">	
				</div>
				<div class="form-group">
					<label class="label" style="color:black">Bill Due Date</label>
					 <input value="" class="form-control" name="bill_due_date" placeholder="Bill Due Date" id="bill_due_date">	
				</div>
				<div class="form-group">
					<label class="label" style="color:black">Consumer Mob Number</label>
					 <input value="" class="form-control" name="consumerNumber" placeholder="Enter Consumer Number" id="consumerNumber" maxlength="10">	
				</div>
				<div class="form-group col-md-12" >
				  <button id="recharge_button" onclick="recharge_pay()"  type="button" class="btn btn-success">Submit</button>
				  <img src="<?php echo e(url('/loader/loader.gif')); ?>" id="billPayLoader" class="loaderImg" style="display:none"/>	 
				</div>
					<?php echo Form::close(); ?>

			</div>	
			<div id="offer_div" class="col-md-5">
			</div>
		</div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        </div>
      </div>
    </div>
  </div>
					
			
<div id="myReciept" class="modal fade" role="dialog">
    <div class="modal-dialog"> 
     <!-- Modal content-->
        <div class="modal-content" style="width: 150%;">
            <div class="modal-body" style="padding: 10px; border-radius: 0px;">
                <div class="col-md-12">
                    <button type="button" class="btn" data-dismiss="modal" style="padding: 6px ! important; top: -8px; right: -35px; background-color: rgb(255, 255, 255) ! important; position: absolute;">&times;</button>
                </div>
                <div class="containers" style="">
                    <div class="panel panel-primary">
                        <div class="panel-heading" style="margin-bottom: 3px; padding: 7px;">Print / Download Receipt of Transaction ID :<span id="prt_hdtranid"></span></div>
                        <div class="panel-body" style="padding: 0% 4%">
                            <div class="clearfix"></div>
                            <div class="row">
                                <div class="col-md-9"></div>
                                <div class="col-md-3">
                                    <button class="btn btn-primary fullbtn" style="color: #FFF !important; float: right; padding: 5px 8px; text-shadow: none;" id="btnPrint"><i class="fa fa-print" style="margin-right: 5px;"></i>PRINT</button>
                                </div>
                            </div>
                            <div class="clearfix"></div>
                            <div id="reciept" style="margin-top: 5px; margin-bottom: 7px;">

                                <div class="row">
                                    <div class="col-md-12" id="dvContents">
                                        <style>
                                            td {
                                                padding: 5px;
                                            }
                                        </style>
                                        <table style="width: 100%; border: 1px solid #888;">
                                            <tr>
                                                <th colspan="3">
                                                    <div class="col-md-2 col-sm-2 col-xs-2" style="padding:10px;">
                                                        <img src="<?php echo e(asset('newlog/images/Logo168.png')); ?>" style="width:70px;margin-right: 650px" />
                                                    </div>
                                                    <div class="col-md-6 col-sm-6 col-xs-6 text-left" style="padding:10px;">
                                                        <div style="float: middle;">
                                                            <b>Outlet Name: &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo e(Auth::user()->member->company); ?></b>
                                                        </div>
                                                        <br />
                                                        <div style="float: middle;">
                                                            <b>Contact Number: &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo e(Auth::user()->mobile); ?></b>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4 col-sm-4 col-xs-4 text-right" style="padding:10px;" id="trandetailbyheadbps">
                                                        <img src="<?php echo e(asset('newlog/images/bbps_print.png')); ?>" style="width:170px;">
                                                    </div>
                                                </th>
                                            </tr>
                                            <tr style="border-top:1px solid #ddd;" id="trandetailbybps">
                                                <td>
                                                    <b>Biller Name : </b>
													<span id="printBillerName"></span><br /> 
                                                    <b>Customer Mobile No :</b><span id="printCustomerNumber"></span><br />
                                                    <b>Payment channel :Agent </b><span id="prt_tranchannel"></span><br />
                                                </td>
                                                <td>
                                                    <b>Consumer ID/Number : </b><span id="printConsumerNumber"></span><br />
                                                    <b>Payment Mode : Cash</b><br />
                                                    <b>Date & Time : </b><span id="printDate"><?php echo e(date("d-m-Y H:i:s")); ?></span>
													
                                                </td>
                                                <td></td>
                                            </tr>
											<tr>
                                                <td colspan="3" style="border-bottom: 1px solid #ccc; border-top: 1px solid #ccc;"><b>Transaction Details</b></td>
                                            </tr>
                                            <tr>
                                                <td colspan="3" class="nospace1">
                                                    <table class="table table-bordered">
														<thead>
															<tr style="background:#ddd;">
																<td class="phead"><b>Date</b></td>
																<td class="phead"><b>Service Provider</b></td>
																<td class="phead"><b>BBPS Transaction ID</b></td>
																<td class="phead"><b>Transaction ID </b></td>
																<td class="phead"><b>Amount </b></td>
																<td class="phead"><b>Status </b></td>
															</tr>
                                                        </thead>
                                                        <tbody id="printTBody">
                                                        </tbody>
                                                    </table>
                                                </td>
                                            </tr>
                                          <tr>
                                                <td colspan="3">                                                   
                                                    <div class="col-md-6 col-sm-6 col-xs-6">
                                                        <b>Total Amount Rs. : </b>
                                                        <label id="printAmount"></label>&nbsp;&nbsp;&nbsp;
                                                    </div>
                                                </td>
                                            </tr> 
                                          <tr>
                                                <td colspan="3">
                                                    <div class="col-md-12 col-sm-12 col-xs-12">
                                                        <b>Amount in Words :</b>
                                                        <label id="printAmountInWord"></label><span id="updageFeeSpan"><input type="text" id="billChargeAmount" placeholder="Enter Chage Amount"/><button class="btn btn-basic" onClick="updateFee()" >Save</button></span>
                                                    </div>
                                                </td>
                                            </tr> 
                                            <tr>
                                                <td colspan="3" class="modalfoot" style="text-align: center;">
                                                    <p>&copy; 2018 All Rights Reserved</p>
                                                    <p style="font-size: 12px">This is a system generated Receipt. Hence no seal or signature required.</p>
                                                </td>
                                            </tr>
                                        </table> 
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
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
              <?php if($recharge_reports->api_id=='27'): ?>
                  <td> <?php if(in_array($recharge_reports->status_id,array(1,3,9,34))): ?><button id="re_check_<?php echo e($recharge_reports->id); ?>"  disabled class="btn btn-primary">Check</button> <?php endif; ?></td>
              <?php else: ?>
                <td> <?php if(in_array($recharge_reports->status_id,array(1,3,9,34))): ?><button id="re_check_<?php echo e($recharge_reports->id); ?>"onclick="this.disabled=true;recharge_status(<?php echo e($recharge_reports->id); ?>);" class="btn btn-primary">Check</button> <?php endif; ?></td>
              <?php endif; ?>
              
              <td style="text-align:center">
                  <?php if(in_array($recharge_reports->status_id,array(1,3,9,34))): ?>
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