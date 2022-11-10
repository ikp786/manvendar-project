
<?php $__env->startSection('content'); ?>

    <script type="text/javascript">
	
        function add_record() {
			$('#companyButton').val("ADD");
			$('#myCompanyBankForm').trigger("reset");
			$("#myModal").modal("toggle");
        } 
		
        //create new task / update existing task
		function saveRecord() 
		{ 
				var type = "POST";
				var actionType = $("#companyButton").val();
				var task_id = $('#id').val();
				var url = "<?php echo e(url('company-bank-details')); ?>";
				var my_url = url;
				$.ajaxSetup({
					headers: {
					'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
					}
				})
			if (actionType == "UPDATE") 
            {
				type = "PUT"; //for updating existing resource
					my_url += '/' + task_id;
					//$('#myImageForm').attr('method','PUT');
					var uploadfile = new FormData($("#myCompanyBankForm")[0]);
					$.ajax({
						type: type,
						url: my_url,
						data: $('#myCompanyBankForm').serialize(),
						dataType: "json",
						beforeSend: function() {
                           
                           
                        },
						success: function (data) {
							if (data.status == 'success') {
								alert(data.message);
								location.reload();
							}
							else if (data.status == 'failure') {
								alert(data.message);
								
							} else {
								var errorString = '<div class="alert alert-danger"><ul>';
								$.each(data.errors, function (key, value) {
									errorString += '<li>' + value + '</li>';
								});
								errorString += '</ul></div>';
								$("#name-error").show();
								$('#name-error').html(errorString); //appending to a <div id="form-errors"></div> inside form
								$('#name-error').focus();

							}
						}

					});				
            }
            else
            {  
				var uploadfile = new FormData($("#myCompanyBankForm")[0]);
					$.ajax({
						type: type,
						url: my_url,
						data: uploadfile,
						// data: formData,
						//enctype: 'multipart/form-data',
						processData: false,  // Important!
						contentType: false,
						cache: false,
						dataType: "json",
						beforeSend: function() {
							$("#submitLoaderImg").show()
							$("#companyButton").hide()
							$("#name-error").text('');
                            
                        },
						success: function (data) {
							 $("#submitLoaderImg").hide()
							$("#companyButton").show()
							if (data.status == 'success') {
								alert(data.message);
								location.reload();
							}
							if (data.status == 'failure') {
								alert(data.message);
							} else {
								var errorString = '<div class="alert alert-danger"><ul>';
								$.each(data.errors, function (key, value) {
									errorString += '<li>' + value + '</li>';
								});
								errorString += '</ul></div>';
								$("#name-error").show();
								$('#name-error').html(errorString); //appending to a <div id="form-errors"></div> inside form
								$('#name-error').focus(); //appending to a <div id="form-errors"></div> inside form
								$('#name-error').focus();

							} 
						}
					});
			}
		}
        
		function updateRecord(id) {

            $.ajax({
                type: "get",
                url: "<?php echo e(url('bank-details-view')); ?>/"+id,
               dataType: "json",
                success: function (data) 
				{
                    $('#id').val(data.details.id);
					$('#user_id').val(data.details.user_id);
                    $('#bank_name').val(data.details.bank_name);
                    $('#account_number').val(data.details.account_number);
                    $('#ifsc_code').val(data.details.ifsc_code);
                    $('#branch_name').val(data.details.branch_name);
                    $('#message_one').val(data.details.message_one);
                    $('#message_two').val(data.details.message_two);
                    $('#status_id').val(data.details.status_id);
                    $('#companyButton').val("UPDATE");
					$("#myModal").modal("toggle");
                }
            })

        }
		function deleteRow(id)
		{
			$.ajaxSetup({
					headers: {
					'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
					}
				})
			$.ajax({
				type: "POST",
				url:"<?php echo e(url('delete-bank-details')); ?>",
				data: 'id='+id,
				dataType: "json",
				success: function (data) 
				{
					alert(data.message)
					location.reload();
				}
			});
		}
        
    </script>
<?php ini_set('memory_limit', '-1'); ?>
        <div class="col-sm-12">
            <div class="col-lg-2 col-md-2">
                <h4 class="page-title" style="color:black;"><?php echo e('Bank Details'); ?></h4>
            </div>
			<?php if(Auth::user()->role_id == 1): ?>
            <div class="col-lg-10 col-md-10">
				 <button type="button" class="btn btn-outline-primary btn-sm" data-toggle="modal" onClick="add_record()">Add bank Details</button>
            </div>
			<?php endif; ?>
        </div>
	<div class="row">
        <div class="col-sm-12">
            <div class="card-box">
                <table class="table">
                    <thead>
                    <tr>
						<th data-field="id" data-sortable="true">ID </th>
                        <th>Bank Name</th>
                        <th>Account Number</th>
                        <th>IFSC</th>
                        <th>Branch</th>
                        <th>Message One</th>
                        <th>Message Two</th>
                        <?php if(Auth::user()->role_id == 1): ?>
                        <th>Status</th>
                        <th>Action</th>	
                        <?php endif; ?>	
                    </tr>
                    </thead>
                    <tbody>
                    <?php $__currentLoopData = $bankDetails; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $bankDetail): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>	
                        <tr>
                            <td><?php echo e($bankDetail->id); ?></td>
                            <td><?php echo e($bankDetail->bank_name); ?></td>
                            <td><?php echo e($bankDetail->account_number); ?></td>
                            <td><?php echo e($bankDetail->ifsc_code); ?></td>
                            <td><?php echo e($bankDetail->branch_name); ?></td>
                            <td><?php echo e($bankDetail->message_one); ?></td>
                            <td><?php echo e($bankDetail->message_two); ?></td>
                            <?php if(Auth::user()->role_id == 1): ?>
                            <td><?php echo e(($bankDetail->status_id)? "Active" :"De-Active"); ?></td>
                            <td>
								<button type="button" class="btn btn-outline-info btn-sm" onclick="updateRecord(<?php echo e($bankDetail->id); ?>)"><i class="fa fa-edit " aria-hidden="true"></i></button>
								<button type="button" class="btn btn-outline-danger btn-sm" onclick="deleteRow(<?php echo e($bankDetail->id); ?>)"><i class="fa fa-trash " ></i></button>
                            </td>
                            <?php endif; ?>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table> 
            </div>
        </div>
    </div>
<div class="modal" id="myModal">
  <div class="modal-dialog">
    <div class="modal-content">
      <!-- Modal Header -->
      <div class="modal-header">	 
        <h4 class="modal-title">Bank Details</h4>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>

      <!-- Modal body -->
      <div class="modal-body">
	  <div id="name-error" name="name-error"></div>
	   <?php echo Form::open(array('url' =>'#','id'=>'myCompanyBankForm','files'=>true,)); ?>

		<div class="form-group">
			<input type="text" class="form-control" id="bank_name" name="bank_name" placeholder = "Bank Name">
			<input type="hidden" class="form-control" id="id" name="id" >
			<input type="hidden" class="form-control" id="user_id" name="user_id" value="<?php echo e($user->id); ?>">
		</div>
		<div class="form-group">
			<input type="text" class="form-control" id="account_number" name="account_number" placeholder = "Account Number">
		</div>
		<div class="form-group">
			<input type="text" class="form-control" id="ifsc_code" name="ifsc_code" placeholder = "IFSC Code">
		</div><div class="form-group">
			<input type="text" class="form-control" id="branch_name" name="branch_name" placeholder = "Branch Name">
		</div><div class="form-group">
			<input type="text" class="form-control" id="message_one" name="message_one" placeholder = "Message First">
		</div>
		<div class="form-group">
			<input type="text" class="form-control" id="message_two" name="message_two" placeholder = "Message Second">
		</div>
		<div class="form-group">
		<select name="status_id" id="status_id" class="form-control">
			<option value="1">Active</option>
			<option value="0">Deactive</option>
		</select>
		 </div>
		<?php echo Form::close(); ?>

      </div>

      <!-- Modal footer -->
      <div class="modal-footer">
	   <input type="button" id="companyButton" class="btn btn-outline-success btn-sm" onClick="saveRecord()" value="ADD"/>
	    <img src="<?php echo e(url('/loader/loader.gif')); ?>" id="submitLoaderImg" class="beneLoaderImg" style="display:none;width:7%"/>
        <button type="button" class="btn btn-outline-danger btn-sm" data-dismiss="modal">Close</button>
      </div>

    </div>
  </div>
</div>


    <meta name="_token" content="<?php echo csrf_token(); ?>"/>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('admin.layouts.templatetable', \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>