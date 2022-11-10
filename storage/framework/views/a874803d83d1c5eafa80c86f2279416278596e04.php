<?php $__env->startSection('content'); ?>

<script>
	function add_record() {
		$('#btn-save').val("add");
		$('#frmTasks').trigger("reset");
		$('#message_content').val('');
		$('#sendBtn').val("Create");
		$("#addRemitterModel").modal("toggle");
	} 
    
	function submitRemitterMessage() {
	    var form=$("#TeamForm");
	    $.ajax({
            type:"POST",
            url:form.attr("action"),
            data:form.serialize(),
    
            success: function(response){ 
                
                if(response.success == true) {
                    $("#name-error").show();
                    $("#name-error").html("<div class='alert alert-success'>" + response.message + "</div>");
                    setInterval(function(){ location.reload();},2000);
				    
                }else{
                    $("#name-error").show();
                    $("#name-error").html("<div class='alert alert-danger'>" + response.message + "</div>"); 
                }
                
            }
        }); 
    }

    
	    function updateRecord(id){
	    	/*var token = $("input[name=_token]").val();
	    	var dataString = 'id=' + id + '&_token=' + token;
	    	$.ajax({
	    		type: "get",
	    		url: "<?php echo e(url('recharge-scheme/view')); ?>",
	    		data: dataString,
	    		success: function (data) {
	    			$('#sendId').val(id);
	    			$('#message_content').val(data.message.scheme_name);
	    			$('#sendBtn').val("update");
	    			$("#addRemitterModel").modal("toggle");
	    		}
	    	})*/
	    	$("#addRemitterModel").modal("toggle");
	    	$('#sendId').val(id);
			//$('#message_content').val(data.message.scheme_name);
			//$('#sendBtn').val("update");
			//$("#addRemitterModel").modal("toggle");
	    } 
    
    
    
      
    
</script>
<?php echo $__env->make('admin.admin-subtab.companymaster-type', \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>
<!-- Page-Title -->
<div class="row">
	<div class="col-sm-12">
		<div class="col-lg-6 col-md-6">
			<h3 class="page-title" style="color: black;"><?php echo e('Remitter List'); ?></h3>

		</div>
		<div class="col-lg-6 col-md-6">
			<div class="pull-right">
				
					<button onclick="add_record()" id="demo-add-row" class="btn btn-success"><i
						class="fa fa-plus m-r-5"></i>Send to Selected
					</button>
			
			</div>
		</div>
	</div>
</div><br>

<!--Basic Columns-->
<!--===================================================-->


<!--===================================================-->
<div class="panel-body">
	<?php if(session('error')): ?>
	<div class="alert alert-danger">
		<?php echo e(session('error')); ?>

	</div>
	<?php endif; ?>
	<?php if(session('success')): ?>
	<div class="alert alert-success">
		<?php echo e(session('success')); ?>

	</div>
	<?php endif; ?>
	<div class="table table-responsive">
		<table border='1' class="table table-striped table-bordered table-hover" id="dataTables-example">
			<thead>
				<tr>
					<th><input data-id="all"  type="checkbox" id="message_to_all" value="all" ></th> 
					<th>Remitter Name</th>
					<th>Mobile</th> 
					<th>Is Verified</th>
					<th>Action</th> 
				</tr>
			</thead>
			<tbody>
				<?php $__currentLoopData = $GetRemitterData; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
				<tr class="odd gradeX">
					<td> 
						<input data-id="<?php echo e($value->id); ?>"  type="checkbox" class="opc1" value="<?php echo e($value->id); ?>" >
					</td>
					<td><?php echo e($value->fname); ?> <?php echo e($value->lname); ?></td>
					<td><?php echo e($value->mobile); ?></td>
					<td> 
						<?php if(@$value->verify=='1'): ?>
							<i class="fa fa-check-circle btn btn-success btn-xs" ></i> Verified
						<?php elseif(@$value->verify=='0'): ?> 
							<i class="fa fa-check-circle btn btn-warning btn-xs" ></i> Un-Verified
						<?php endif; ?>
					</td> 
					<td>
						<button onclick="updateRecord('<?php echo e($value->id); ?>')" class="btn btn-info btn-xs">Send</button> 
					</td> 
				</tr>
				<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>  
			</tbody>
		</table> 
	</div>
</div>


<div class="modal fade" id="addRemitterModel" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
	<div class="modal-dialog" role="document">
		<!--<form class="cmxform form-horizontal user_form"  enctype="multipart/form-data"  action="<?php echo e(url('/admin/remitters-send')); ?>" role="form" method="post">
		    -->
		<form class="form-horizontal cmxform" action="<?php echo e(url('/admin/remitters-send')); ?>" role="form" method="post" id="TeamForm" >
        <input type="hidden" name="_token" value="<?php echo e(csrf_token()); ?>">
        	<div class="modal-content">
				<div class="modal-header">
					<h4 class="modal-title" id="myModalLabel">Add Message</h4>
				</div>
				<div class="modal-body">
					<div id="name-error"></div> 
						<div class="form-group">
							<label for="saral_ref_id" class="control-label col-sm-4"> Message </label>
							<div class="col-sm-6"> 
								<textarea class="form-control" name="message_content" required></textarea>
								<input type="hidden" class="form-control" id="sendId" name="sendId">
							</div>
						</div>
					</form>
				</div>

				<div class="modal-footer"> 
					<input id="schemeBtn" type="button" onclick="submitRemitterMessage();" class="btn btn-primary" value="Submit">
				</input>
				<button type="button" class="btn btn-default" data-dismiss="modal">Close</button> 
			</div>
		</form>
	</div>
</div>
</div>
<meta name="_token" content="<?php echo csrf_token(); ?>"/>

<script>

function printChecked() { 
    var checkedIds = [];

    // for each checked checkbox, add it's id to the array of checked ids
    $('.opc1').each(function() {   
        if($(this).is(':checked')) {
            checkedIds.push($(this).val());
        }
    }); 
    $('#sendId').val(checkedIds); 
}

$(document).ready(function(){
    
	$('#message_to_all').on("click",function()
	{  
        if($(this).prop('checked')) {  
            $('.opc1').prop("checked", true); 
        }else{   
            $('.opc1').prop("checked", false); 
        }
        displayCheckbox();
    }); 
    
    $(".opc1").change(function(){ 
        displayCheckbox();
    });
    
     $('#dataTables-example').DataTable( {
        "order": [[ 0, "desc" ]],
        "targets": "no-sort",
        "bSort": false,
    });
    
});

function displayCheckbox() 
{  
    var checkboxes = $('.opc1');
    var results = $("#sendId");
    var checkedIds = [];
    $.each(checkboxes, function(){ 
        if($(this).is(':checked')){
            checkedIds.push($(this).val());
        }
    })
    results.val(checkedIds);
}  
 
</script>
<?php $__env->stopSection(); ?>


<?php echo $__env->make('admin.layouts.templatetable', \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>