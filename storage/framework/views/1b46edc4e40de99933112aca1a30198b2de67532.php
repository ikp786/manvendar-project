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
			var user_id = $('#user_id').val();
			var url = "<?php echo e(url('aeps/bank-details')); ?>";
			var my_url = url;
			$.ajaxSetup({
				headers: {
				'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
				}
			})
			if (actionType == "UPDATE") 
            {
				type = "POST"; //for updating existing resource
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
							if (data.status == 1) {
								alert(data.message);
								location.reload();
							}
							else if (data.status == 0) {
								alert(data.message);
								
							} else if (data.status == 10) {
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
							if (data.status == 1) 
							{
									var errorString = '<div class="alert alert-success"><ul><li>' + data.message + '</li></ul></div>';
								
							}
							else if (data.status == 0) {
								var errorString = '<div class="alert alert-danger"><ul><li>' + data.message + '</li></ul></div>';
								
							}else if (data.status == 10) {
								var errorString = '<div class="alert alert-danger"><ul>';
								$.each(data.errors, function (key, value) {
								errorString += '<li>' + value + '</li>';
								});
								errorString += '</ul></div>';
								
							}
							$("#name-error").show();
								$('#name-error').html(errorString); //appending to a <div id="form-errors"></div> inside form
								$('#name-error').focus();
						}
					});
			}
		}
        
		function updateRecord(id) {
            $.ajax({
                type: "get",
                url: "<?php echo e(url('aeps/bank-details-view')); ?>/"+id,
               dataType: "json",
                success: function (data) 
				{
                    $('#id').val(data.details.id);
                    $('#bank_name').val(data.details.bank_name);
                    $('#account_number').val(data.details.account_number);
                    $('#accountHolderName').val(data.details.accountHolderName);
                    $('#ifsc_code').val(data.details.ifsc);
                    $('#branch_name').val(data.details.branch_name);
                  
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
			if(confirm('Are you sure you want to Delete this')){
				$.ajax({
					type: "POST",
					url:"<?php echo e(url('aeps/delete-bank-details')); ?>",
					data: 'id='+id,
					dataType: "json",
					success: function (data) 
					{
						alert(data.message)
						location.reload();
					}
				});
			}
		}      
    </script>
<div class="super_container">
	<div class="home">
	</div>
	<div class="search">					
           <?php echo $__env->make('agent.aepsSettlement.aepsSettlement-type', \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>
            <br>
		<div class="row pull-right" style="">
			<button type="button" class="btn btn-primary" data-toggle="modal" onClick="add_record()">Add bank for Settlement</button>
        </div>
        	
		<br>	
		<div class="row">
		    <div class="col-sm-12">
		        <div class="">
		            <table class="table table-bordered">
		                <thead >
		                    <tr>
								<th>ID </th>
								<th>Account Holder Name</th>
		                        <th>Bank Name</th>
		                        <th>Account Number</th>
		                        <th>IFSC Code</th>
		                        <th>Branch Name</th>
		                        <th>Status</th>
		                       <?php if(Auth::user()->role_id == 1): ?>
		                        <th>Action</th>	
		                       <?php endif; ?>
		                    </tr>
		                </thead>
		                <tbody>
		                <?php $__currentLoopData = $bankDetails; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $bankDetail): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>		
		                    <tr style="background-color:white">
		                        <td><?php echo e($bankDetail->id); ?></td>
		                        <td><?php echo e($bankDetail->name); ?></td>
		                        <td><?php echo e($bankDetail->bank_name); ?></td>
		                        <td><?php echo e($bankDetail->account_number); ?></td>
		                        <td><?php echo e($bankDetail->ifsc); ?></td>
		                        <td><?php echo e($bankDetail->branch_name); ?></td>
		                        <td><?php echo e($bankDetail->status->status); ?></td>
		                    </tr>
		                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
		                </tbody>
		            </table>
		        </div>
		    </div>
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
	  <div id="name-error" name="name-error" tabindex="-1"></div>
	   <?php echo Form::open(array('url' =>'#','id'=>'myCompanyBankForm','files'=>true,)); ?>

		<div class="form-group">
			<input type="text" class="form-control" id="bank_name" name="bank_name" placeholder = "Bank Name">
			<input type="hidden" class="form-control" id="id" name="id">
			<input type="hidden" class="form-control" id="user_id" name="user_id" value="<?php echo e(Auth::id()); ?>">
			
		</div>
		<div class="form-group">
			<input type="text" class="form-control" id="account_number" name="account_number" placeholder = "Account Number">
		</div>
		<div class="form-group">
			<input type="text" class="form-control" id="accountHolderName" name="name" placeholder = "Account Holder Name">
		</div>
		<div class="form-group">
			<input type="text" class="form-control" id="ifsc_code" name="ifsc" placeholder = "IFSC Code">
		</div><div class="form-group">
			<input type="text" class="form-control" id="branch_name" name="branch_name" placeholder = "Branch Name">
		</div>

		<?php echo Form::close(); ?>

      </div>

      <!-- Modal footer -->
      <div class="modal-footer">
	   <input type="button" id="companyButton" class="btn btn-outline-success btn-sm" onClick="saveRecord()" value="ADD"/>
	    <img src="<?php echo e(url('/loader/loader.gif')); ?>" id="submitLoaderImg" class="beneLoaderImg" style="display:none;width:20%;height: 20%"/>
        <button type="button" class="btn btn-outline-danger btn-sm" data-dismiss="modal">Close</button>
      </div>

    </div>
  </div>
</div>
 <meta name="_token" content="<?php echo csrf_token(); ?>"/>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>