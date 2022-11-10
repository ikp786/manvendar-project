
<?php $__env->startSection('content'); ?>

    <script type="text/javascript">
	
        function add_record() {
			$('#mobileButton').val("ADD");
			$('#mobileForm').trigger("reset");
			$('#name-error').text('');
			$("#myModal").modal("toggle");
        } 
		
		function saveRecord() 
		{ 
			var type = "POST";
			var actionType = $("#mobileButton").val();
			var task_id = $('#id').val();
			var url = "<?php echo e(url('action-otp-verify-details')); ?>";
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
					
					var uploadfile = new FormData($("#mobileForm")[0]);
					$.ajax({
						type: type,
						url: my_url,
						data: $('#mobileForm').serialize(),
						dataType: "json",
						beforeSend: function() {
                           
                           
                        },
						success: function (data) {
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
								$('#name-error').focus();

							}
						}

					});				
            }
            else
            {  
				var uploadfile = new FormData($("#mobileForm")[0]);
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
							$("#mobileButton").hide()
							$("#name-error").text('');
                            
                        },
						success: function (data) {
							 $("#submitLoaderImg").hide()
							$("#mobileButton").show()
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
                url: "<?php echo e(url('action-otp-verify-details-view')); ?>/"+id,
               dataType: "json",
                success: function (data) 
				{
                    $('#id').val(data.details.id);
                    $('#mobile').val(data.details.mobile);
                    $('#status_id').val(data.details.status_id);
                    $('#mobileButton').val("UPDATE");
					$("#myModal").modal("toggle");
                }
            })

        }
		function deleteRow(id)
		{
			if(confirm('Are your sure want to update'))
			{
				$.ajaxSetup({
						headers: {
						'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
						}
					})
				$.ajax({
					type: "POST",
					url:"<?php echo e(url('delete-otp-verify-details')); ?>/"+id,
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
<?php ini_set('memory_limit', '-1'); ?>
<div class="col-sm-12">
    <div class="col-lg-2 col-md-2">
        <h4 class="page-title" style="color:black;"><?php echo e('Number Details'); ?></h4>
    </div>
	<?php if(Auth::user()->role_id == 0): ?>
    <div class="col-lg-10 col-md-10">
		 <button type="button" class="btn btn-outline-primary btn-sm pull-right" data-toggle="modal" onClick="add_record()">Add Mobile Number</button>
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
                    <th>Mobile Number</th>
                    <th>Status</th>
                    <th>Action</th>	
                </tr>
                </thead>
                <tbody>
                <?php $__currentLoopData = $values; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>	
                    <tr>
                        <td><?php echo e($value->id); ?></td>
                        <td><?php echo e($value->mobile); ?></td>
                       
                        <td><?php echo e(($value->status_id)? "Active" :"De-Active"); ?></td>
						 <?php if(Auth::user()->role_id == 0): ?>
                        <td>
							<button type="button" class="btn btn-outline-info btn-sm" onclick="updateRecord(<?php echo e($value->id); ?>)"><i class="fa fa-edit " aria-hidden="true"></i></button>
							<button type="button" class="btn btn-outline-danger btn-sm" onclick="deleteRow(<?php echo e($value->id); ?>)"><i class="fa fa-trash " ></i></button>
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
        <h4 class="modal-title">Mobile Number For Fail Txn, OTP Verify</h4>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>

      <!-- Modal body -->
      <div class="modal-body">
	  <div id="name-error" name="name-error"></div>
	   <?php echo Form::open(array('url' =>'#','id'=>'mobileForm','files'=>true,)); ?>

		<div class="form-group">
			<input type="text" class="form-control" id="mobile" name="mobile" placeholder = "Mobile Number" maxlength="10">
			<input type="hidden" class="form-control" id="id" name="id" >
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
	   <input type="button" id="mobileButton" class="btn btn-outline-success btn-sm" onClick="saveRecord()" value="ADD"/>
	    <img src="<?php echo e(url('/loader/loader.gif')); ?>" id="submitLoaderImg" class="beneLoaderImg" style="display:none;width:7%">
        <button type="button" class="btn btn-outline-danger btn-sm" data-dismiss="modal">Close</button>
      </div>

    </div>
  </div>
</div>


    <meta name="_token" content="<?php echo csrf_token(); ?>"/>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('admin.layouts.templatetable', \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>