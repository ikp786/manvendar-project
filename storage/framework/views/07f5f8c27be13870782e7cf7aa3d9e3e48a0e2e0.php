<?php $__env->startSection('content'); ?>
    <script>
        function add_record() {
            $('#btn-save').val("add");
            $('#frmTasks').trigger("reset");
            $("#con-close-modal").modal("toggle");
        }
        //create new task / update existing task
        function savedata() {
            if(confirm('Are you sure to transfer?'))
        {
         
            var btn = $("#btn").text();
            $("#btn-save").prop("disabled", true);
            var url = "payment-request-view";
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                }
            })
            var formData = {
                status_id: $('#status_id').val(),
                //amount: $('#amount').val(),
                //wallet: $('#wallet').val(),
                //rer_id: $('#ref_id').val(),
                id: $('#id').val(),
                //user_id: $('#user_id').val(),
                //pay_type: $('#pay_type').val(),
               // bank_nm: $('#bnk_nm').val(),
                b_charge_id: $('#b_charge_id').val(),
				remark: $('#remark_id').val(),
				//payment_mode: $('#payment_mode').val(),
				adminRemark: $('#adminRemark').val()
            }
            //used to determine the http verb to use [add=POST], [update=PUT]
            var state = $('#btn-save').val();
            var type = "POST"; //for creating new resource
            var task_id = $('#id').val();
            var my_url = url;
            if (state == "update") {
                type = "PUT"; //for updating existing resource
                my_url += '/' + task_id;
            }
            $.ajax({
                type: type,
                url: my_url,
                data: formData,
                dataType: "json",
                 beforeSend: function() {
                        $("#btn-save").hide();
                        $('#imgr').show();
						/* $.LoadingOverlay("show", {
                            image       : "",
                            fontawesome : "fa fa-spinner fa-spin"
                        }); */
                    },
                success: function (data) 
				{
					//$.LoadingOverlay("hide");
                       $("#btn-save").show();
                        $('#imgr').hide();
                    if (data.status == "success") {
                        $("#con-close-modal").modal("hide");
                        /* swal("Success", data.message, "success"); */
                        $("#btn-save").prop("disabled", false);
                        location.reload();
                    } 
                    else if(data.status == "rejected" || data.status == "failure")
                    {
                        alert(data.message);
                        location.reload();
                    }
                    else if(data.status == "approved")
                    {
                        alert(data.message);
                        location.reload();
                    }
                    else 
					{
						alert(data.message)
                       
                    }
                } 

            });
         }
		 else
		 {
			 $("#btn-save").show();
                  $("#btn-save").prop("disabled", false);
                        $('#imgr').hide();
		 }
        }
        function updateRecord(id) {
            var token = $("input[name=_token]").val();
            var dataString = 'id=' + id ;
            var aname = $('#u_a_'+id).val();
			var pay_type = $('#pay_type_'+id).val(); 
            var bnk_nm = $('#bnk_nm_'+id).val();
			$.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                }
            })
            $.ajax({
                type: "post",
                url: "<?php echo e(url('payment-request-view/view')); ?>",
                data: dataString,
				beforeSend:function()
				{
					 $('#adminRemark').val('');
				},
                success: function (res) {
					data = res[0];
                    $('#id').val(data.id);
					$('#bankName').val(data.bankName);
					$('#amount').val(data.amount);
					$('#agentDetails').val(data.agentDetails);
                    $('#ref_id').val(data.bankRef);
                    $('#customerRemark').val(data.requestRemark);
                    $('#locBatchCode').val(data.locBatchCode);
                    $('#modeOfPayment').val(data.paymentMode);
                    $('#requestDepositeDate').val(data.depositDate);
                    /* 
                    $('#user_id').val(data.user_id);
                    $('#pay_type').val(pay_type);
                    $('#bnk_nm').val(bnk_nm); */
                    $('#btn-save').val("update");
                    $("#con-close-modal").modal("toggle");
                }
            })

        }
		
	function loadlink(){
    //$('#links').load('https://partners.levinm.com',function () {
        // location.reload();
   // });
}

function getRecords(user_id)
		{
			var u_a = $('#u_a_'+user_id).val();
			$('#u_a_name').html(u_a);
			var dataString = 'user_id=' + user_id;
			$.ajax({
                type: "get",
                url: "<?php echo e(url('payment-request-view/getRecords')); ?>",
                data: dataString,
                success: function (result) {
                   
						var obj2 = result.data;
						 var html = "";
						for (var key in obj2) {
							console.log(obj2);
							 html +='<tr>';
							 html +='<td>' + obj2[key].id + '</td>';
							 html +='<td>' + obj2[key].amount + '</td>';
							 html +='<td>' + obj2[key].netbank + '</td>';
							 html +='<td>' + obj2[key].bankref + '</td>';
							 html +='<td>' + obj2[key].created_at.date + '</td></tr>';
						}
						$("#myModal").modal("toggle");
					$("#response").html(html);
					
					
                }
            });
			
		}
		function getLastTenApprovedAmount(balance)
        {
            var dataString = 'requested_amount=' + balance;
            $.ajax({
                type: "get",
                url: "<?php echo e(url('payment-request-view/get-last-ten-approved-amount')); ?>",
                data: dataString,
                success: function (result) {
                    if(result.status == 1){
                        var html ='<thead><tr><th>Name</th><th>Mobile</th><th>Order Id</th><th>Amount</th><th>Bank Name</th><th>Reference</th><th style="width: 19%;">Crated_at</th></tr></thead><tbody>';
                        $.each(result.last_ten_approved_amount, function (key, val) {
                             html +='<tr>';
                             html +='<td>' + val.name + '</td>';
                             html +='<td>' + val.mobile + '</td>';
                             html +='<td>' + val.id + '</td>';
                             html +='<td>' + val.amount + '</td>';
                             html +='<td>' + val.bank_name + '</td>';
                             html +='<td>' + val.bankref + '</td>';
                             html +='<td>' + val.created_at + '</td>';

                        });
                        html +='</tbody>';
                    }
                    $("#last-ten-result").html('Last Ten Approved Requested Amounts');
                    $("#result_table").html(html);
                    $("#approved_requested_amount").modal("toggle");
                    
                }
            })
            
        }
		function addNewRemark()
        {
			
			
                var remark = prompt("Enter New Remark");
                if(remark)
                {
                    dataString = 'new_remark=' + remark;
                    $.ajaxSetup({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                        }
                    })
                    $.ajax({
                    type: "POST",
                    url: "<?php echo e(url('payment-request-view/addNewRemark')); ?>",
                    data: dataString,
                    success: function (result) {
                        if(result.status == 1){
                             alert(result.message)
                             console.log(result)
                             $("#remark_id option[value='" + result.contents_id + "']").attr('selected',result.contents)
                            $("#remark_id").append('<option value="'+result.contents_id+'">'+result.contents+'</option>');
                        }
                        else
                            alert(result.message)
                        }
                })
                }
               

        }
        function editRemark()
        {
			
            var remark = prompt("Enter New Remark to update");
                if(remark)
                {
                     var remark_id = $('#remark_id').val(); 
                     var dataString = 'new_remark=' + remark + '&remark_id=' + remark_id;
                     $.ajaxSetup({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                        }
                    }),
                     $.ajax({
                            type: "put",
                            url: "<?php echo e(url('payment-request-view/edit-remark')); ?>"+'/'+remark_id,
                            data: dataString,
                            success: function (data) 
                            {
                               alert(data.message);
                               //$('#remark_id option[value=2]').text();
                               $("#remark_id option[value='" + data.contents_id + "']").text(data.contents)
                            }
                        })
                }
        }
        function deleteRemark()
        {
			
            if(confirm("Are you sure to delete selected remark"))
            {
                     var remark_id = $('#remark_id').val(); 
                     var dataString = 'remark_id=' + remark_id;
                     $.ajaxSetup({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                        }
                    })
                     $.ajax({
                            type: "post",
                            url: "<?php echo e(url('payment-request-view/delete-remark')); ?>",
                            data: dataString,
                            success: function (data) {
                                alert(data.message);
                               //$('#remark_id option[value=2]').text();
                               $("#remark_id option[value='" + data.contents_id + "']").remove();
                            }
                })
            }

        }
		 function showAmountInWords(amount,id)
        {

           if($("#word_"+id).text() =='')
           {
                var dataString = 'amount=' + amount;
                    $.ajax({
                        type: "get",
                        url: "<?php echo e(url('payment-request-view/show-amount-in-words')); ?>",
                        data: dataString,
                        success: function (result) {
                            if(result.status == 1){
                            $("#word_"+id).text(result.word) 
                            }

                    }
                })
           }
        }

loadlink(); // This will run on page load
setInterval(function(){
    loadlink() // this will run after every 5 seconds
}, 2000);
		
    </script>

    
        <div class="panel panel-default">
			<div class="panel-body">
				<h4 class="page-title" style="color: black; font-size: 20px;"><?php echo e('Agent REQUEST VIEW'); ?></h4>
            </div>
        </div>
    <!--Basic Columns-->
    <!--===================================================-->


    <!--===================================================-->
    <div class="row">
        <div class="col-sm-12">
            <div class="table-responsive">
				<div class="col-lg-12">
				
                <marquee onmouseover="this.stop();" onmouseout="this.start();"> <span class="marquee-span-tag"> Bank Down :</span> <span style="color:red"><?php echo e($down_bank_list); ?> </span></marquee>

                </div>
                <table id="mytable" class="table table-bordered">
                    <thead>
						<tr>
							<th>Date</th>
							<th>Time</th>
							<th>Order ID</th>
							<th>User Name</th>
							<th>Firm Name</th>
							<th>Role</th>
							<th>Mobile</th>
							<th>Mode</th>
							<th>Branch Code</th>
							<th>Online Payment Mode</th>
							<th>Deposit Date</th>
							<th data-field="bank_name">Bank Name</th>
							<th>Remark</th>
							<th>Slip</th>
							<th>Ref Id</th> 
							<th>Amount</th>
							
							<th>Status</th>
							<th data-field="action" data-align="center">Action
							</th>
							 <?php if(Auth::user()->role_id == 1): ?>
							 <th data-field="Last_ten" data-align="center" >Lt-15
							</th>
							<?php endif; ?>
						</tr>
                    </thead>
                    <tbody>
                    <?php $__currentLoopData = $loadcashs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr>
							<?php $s = $value->created_at;
							$dt = new DateTime($s);?>
							<td><?php echo e($dt->format('d-m-Y')); ?></td>
                            <td><?php echo e($dt->format('H:i:s')); ?></td>
                            <td><?php echo e($value->id); ?></td>
                            
                            <td><?php echo e($value->user->name); ?> ( <?php echo e($value->user->id); ?> )
                            	<input type="hidden" id="u_a_<?php echo e($value->id); ?>" value="<?php echo e($value->user->name); ?> : <?php echo e($value->user->role->role_title); ?> : <?php echo e($value->user->member->company); ?>">
                            </td>
                            <td><?php echo e(@$value->user->member->company); ?></td>
                            <td><?php echo e(@$value->user->role->role_title); ?></td>
                            <td><?php echo e($value->user->mobile); ?></td>
                            <td><input type="hidden" id="pay_type_<?php echo e($value->id); ?>" value="<?php echo e(@$value->pmethod->payment_type); ?>"><?php echo e(@$value->payment_mode); ?></td>
							<td><?php echo e($value->loc_batch_code); ?></td>
							<td><?php echo e($value->c_online_mode); ?></td>
							<td><?php echo e($value->deposit_date); ?></td>
                           <!-- <td><input type="hidden" id="bnk_nm_<?php echo e($value->id); ?>" value="<?php echo e(@$value->netbank->bank_name); ?>"><?php echo e(@$value->netbank->bank_name); ?></td>-->
						    <td><?php echo e(($value->request_to==1)?$value->bank_name:@$value->netbank->bank_name); ?></td>
                           
                            <td><?php echo e($value->request_remark); ?></td>
							<td>
							<?php if($value->d_picture): ?>
							 <a target="_blank" href='deposit_slip/images/<?php echo e($value->d_picture); ?>'><img src="deposit_slip/images/<?php echo e($value->d_picture); ?>" height="60px" width="60px"></a>
							
							<?php else: ?> <?php echo e('No Slip'); ?>

							
							<!--<?php echo e(Form::open(array('url' => 'slip-update', 'method' => 'post', 'class' => 'form-light','files' => true))); ?>

							<?php echo e(Form::file('d_slip', array('class' => 'form-control','id' => 'd_slip'))); ?>

							
							<div class="form-group">
							<input type="hidden" name="d_id" value="<?php echo e($value->id); ?>">
                            <input type="hidden" id="a_name_<?php echo e($value->id); ?>" value="<?php echo e(@$value->user->name); ?>">
							</div>
							<button type="submit">update</button>>-->
							<?php endif; ?>
							 </td>
							  <td><?php echo e($value->bankref); ?></td>
							<?php if(Auth::user()->role_id ==1): ?>
								<td><a onclick="getLastTenApprovedAmount(<?php echo e($value->amount); ?>)"   href="javascript:void(0)" ><?php echo e($value->amount); ?></a></td>
							<?php else: ?>
								<td><?php echo e($value->amount); ?></td>
							<?php endif; ?>
							<!--<td><span id="word_<?php echo e($value->id); ?>"></span></td> -->
                            <td><?php echo e($value->status->status); ?></td>
							<td>
							<?php if($value->status_id == 3): ?>
                            <a onclick="updateRecord(<?php echo e($value->id); ?>)" href="javascript:void(0)" class="table-action-btn"><i class="fa fa-pencil-square-o"></i></a>
							<?php endif; ?>
                            </td>
                            <?php if(Auth::user()->role_id == 1): ?>
                            <td>
                               <a onclick="getRecords(<?php echo e($value->user_id); ?>)"  href="#" class="table-action-btn"  title="Show Last 5 Successful Txn"><i class="glyphicon glyphicon-th-list"></i></a>
						
                            </td>
                            <?php endif; ?>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                    </tbody>
                </table>
            </div>
        </div>
    </div>


    </div> <!-- container -->

    </div> <!-- content -->

  
    </div>
    <!-- ============================================================== -->
    <!-- End Right content here -->
    <!-- ============================================================== -->

    </div>
    <div id="con-close-modal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
         aria-hidden="true" style="display: none;">
        <div class="modal-dialog modal-md">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                    <h4 class="modal-title">Balance Update</h4>
                </div>
                <div class="modal-body">
                    <div style="display:none" id="name-error"></div>

                    <form id="frmTasks" name="frmTasks" class="form-horizontal" novalidate="">
                        <?php echo csrf_field(); ?>

                        <input type="hidden" name="user_id" id="user_id">
                        <input type="hidden" name="wallet" id="wallet">
                        <input type="hidden" name="id" id="id">
						<div class="form-group row">
                            <label class="col-sm-3 col-form-label text-right" for="amount">Bank name:</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control" id="bankName" name="bankName"
                                       placeholder="Bank Name" value="" readonly>
                            </div>
                        </div>
                        <div class="form-group row">
                           <label class="col-sm-3 col-form-label text-right" for="amount">Amount:</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control" id="amount" name="amount"
                                       placeholder="Amount" value="" readonly>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-sm-3 col-form-label text-right" for="amount">Agent Name:</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control" id="agentDetails" name="agentDetails"
                                       placeholder="Agent ID" value="" readonly>
                            </div>
                        </div>
						

                        <div class="form-group row">
                            <label class="col-sm-3 col-form-label text-right" for="amount">Ref Id:</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control" id="ref_id" name="ref_id"
                                       placeholder="Refernece ID" value="" readonly>
                            </div>
                        </div>  
						<div class="form-group row">
                            <label class="col-sm-3 col-form-label text-right" for="amount">Mode Of Payment:</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control" id="modeOfPayment" name="modeOfPayment"
                                       placeholder="Mode OF Payment" value="" readonly>
                            </div>
                        </div>
						<div class="form-group row">
                            <label class="col-sm-3 col-form-label text-right" for="amount">Customer Remark:</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control" id="customerRemark" name="customerRemark"
                                       placeholder="Customer Remark" value="" readonly>
                            </div>
                        </div>
						<div class="form-group row">
                            <label class="col-sm-3 col-form-label text-right" for="branchCode">BranchCode/N:</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control" id="locBatchCode" name="locBatchCode"
                                       placeholder="N/A" value="" readonly>
                            </div>
                        </div>
						<div class="form-group row">
							<label class="col-sm-3 col-form-label text-right" for="paymentDate">Deposit/Approved Date:</label>
								<div class="col-sm-4">
									<input type="text" class="form-control customDatepicker" id="requestDepositeDate" name="requestDepositeDate" readonly >
								</div>
								<div class="col-sm-5">
									<input type="text" class="form-control customDatepicker" id="approveDate" name="approveDate" value="<?php echo e(date('d-m-Y H:i:s')); ?>" readonly >
								</div>
						</div>
                        <div class="form-group row">
                            <label for="inputEmail3" class="col-sm-3 control-label">Remark</label>
							 <?php if(Auth::user()->role_id ==1): ?>
                            <div class="col-sm-5">
							<?php else: ?>
								<div class="col-sm-9">
							<?php endif; ?>
                                <!-- <input type="text" class="form-control" id="remark" name="remark"
                                       placeholder="Remark" value=""> -->
                             <?php echo Form::select('remark', ($remarks), null, ['class' => 'form-control','id'=>'remark_id']); ?>

                            </div>
							 <?php if(Auth::user()->role_id ==1): ?>
                           <div class="form-group row col-sm-4">
                                
                              
                                <span class="fa fa-plus btn btn-info btn-sm" onclick="addNewRemark()" title="Add new remark"></span> 
                                <a href="#">
                                    <span class="fa fa-pencil btn btn-warning btn-sm"onclick="editRemark()" title="Edit remark"></span>
                                </a>
                              
                                  <span class="fa fa-trash-o btn btn-danger btn-sm"onclick="deleteRemark()" title="Delete remark"></span>
                              
                             </div>
							  <?php endif; ?>
							 
                        </div>
						<div class="form-group row">
                            <label class="col-sm-3 col-form-label text-right" for="amount">Bank Charge:</label>
                            <div class="col-sm-9">
                                <select class="form-control" name="b_charge_id" id="b_charge_id">
                                    <option value="0.00">0.00</option>
                                   <!-- <option value="0.02">0.02</option>
                                    <option value="0.03">0.03</option>
                                    <option value="0.05">0.05</option>-->
                                    
                                </select>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-sm-3 col-form-label text-right" for="amount">Status:</label>
                            <div class="col-sm-9">
                                <select class="form-control" name="status_id" id="status_id">
                                    <option value="3">Pending</option>
                                    <option value="2">Reject</option>
                                    <option value="1">Approve</option>
                                </select>
                            </div>
                        </div>
						
						
						
						<div class="form-group row">
                            <label class="col-sm-3 col-form-label text-right" for="remark"> Remark:</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control" id="adminRemark" name="adminRemark"
                                       placeholder="Write Remark" value="">
                            </div>
                        </div>


                    </form>
                </div>
                <div class="modal-footer">
                <center><span id="imgr" style="display:none;"><img src="images/load2.gif" height="40px" width="40px"></span></center>
                    <button onclick="this.disabled=true;savedata()" type="button" class="btn btn-info waves-effect waves-light"
                            id="btn-save"
                            value="add">Send Now
                    </button>
                    <button type="button" class="btn btn-default waves-effect" data-dismiss="modal">Close</button>
                    <input type="hidden" id="id" name="id" value="0">
                </div>
            </div>
        </div>
    </div><!-- /.modal -->
    <!-- END wrapper -->

    <div class="modal fade" id="myModal" role="dialog">
			<div class="modal-dialog">
			
			  <!-- Modal content-->
				<div class="modal-content" style="width: 900px;">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal">&times;</button>
							<h4 class="modal-title"><strong>Last 15 Fund Approvel ( <span id='u_a_name'></span>) : <span id ="txn_user_name"></span></strong></h4>
					</div>
					<div class="modal-body">
						<table style="font-size: 14px;" class="table table-responsive">
                                                                <thead>
                                                                <tr>
                                                                    <th>ID</th>
                                                                    <th>Amount</th>
                                                                    <th>Bank Name</th>
                                                                    <th>Bankref</th>
                                                                    <th>Created_at</th>
                                                                </tr>
                                                                </thead>
                                                               <tbody id="response" style="font-family: sans-serif;">

                                                                </tbody>
                                                            </table>
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
					</div>
				</div>
			  
			</div>
		 </div>
		<!-- ===========================================================================================
		<!--			Show last 10 approved requested amount 
		<!-- =========================================================================================== -->

		<div class="modal fade" id="approved_requested_amount" role="dialog">
			<div class="modal-dialog">
			
			  <!-- Modal content-->
				<div class="modal-content" style="width: 900px;">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal">&times;</button>
							<h4 class="modal-title"><strong ><span id ="last-ten-result"></span></strong></h4>
					</div>
					<div class="modal-body">
						<table class="table" id="result_table">
						</table>
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
					</div>
				</div>
			  
			</div>
		 </div>
	<!-- ===========================================================================================
    <!--            Show last five successful transaction of users
    <!-- =========================================================================================== -->
     <meta name="_token" content="<?php echo csrf_token(); ?>"/>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('admin.layouts.templatetable', \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>