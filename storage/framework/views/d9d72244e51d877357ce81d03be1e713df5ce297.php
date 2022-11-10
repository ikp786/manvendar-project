<?php $__env->startSection('content'); ?>
    <script>
        function add_record() {
            $('#btn-save').val("add");
            $('#frmTasks').trigger("reset");
            $("#con-close-modal").modal("toggle");
        }
        //create new task / update existing task
        function savedata() {
            var url = "scheme-manage";
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                }
            })
            var formData = {
                scheme_name: $('#scheme_name').val(),
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
                dataType: 'text',
                success: function (data) {
                    var obj = $.parseJSON(data);
                    if (obj.success == false) {
                        var obj1 = obj.errors;
                        //alert(obj1["provider_name"]);
                        var html = "";
                        for (var key in obj1)
                                //alert(obj1[key]);
                        {
                            html += "<li>" + obj1[key] + "</li>";
                        }
                        $("#name-error").show();
                        $("#name-error").html("<div class='alert alert-danger'><ul>" + html + "</ul></div>");
                    } else {
                        var html = "";
                        for (var key in obj) {
                            html += "<li>" + obj[key] + "</li>";
                        }
                        $("#name-error").show();
                        $("#name-error").html("<div class='alert alert-success'><ul>" + html + "</ul></div>");
                    }
                }

            });
        }
        function updateRecord(id) {
            var token = $("input[name=_token]").val();
            var dataString = 'id=' + id + '&_token=' + token;
            $.ajax({
                type: "post",
                url: "<?php echo e(url('scheme-manage/view')); ?>",
                data: dataString,
                success: function (data) {
                    $('#id').val(data.id);
                    $('#scheme_name').val(data.scheme_name);
                    $('#btn-save').val("update");
                    $("#con-close-modal").modal("toggle");
                }
            })

        }
        function save_record() {

            $("#commfor").click();
            //document.getElementById("commform").submit();
        }
		function makeEditable()
		{
			$('.custom').prop("readonly", false); // Element(s) are now enabled.
		}
    </script>
    <!--Basic Columns-->
    <!--===================================================-->


    <!--===================================================-->
<div class="col-lg-4 col-md-4 pull-right">
	<div class="pull-right">
		<?php echo e(Form::open(array('url' => 'commission-manage/', 'method' => 'POST','id' => 'commform', 'onSubmit'=>"return checkCommissionSettin()"))); ?>

	<input type="hidden" id="scheme_id" value="<?php echo e($scheme_id); ?>" name="scheme_id"/>
	<button type="submit" id="commfor" class="btn btn-success">Save All
	</button>
	<button type="button" id="" class="btn btn-primary" onClick="makeEditable()">Edit
	</button>
	<!--<button onclick="add_record()" id="demo-add-row" class="btn btn-info"><i class="fa fa-plus m-r-5"></i>Add Record
	</button>-->
	</div>
</div><br>
<div class="panel-body">
	 <?php //print_r($errorMessage);die;?>
	  <h4 style="color:black">Scheme Name:<?php echo e(@$scheme_name); ?></h4>
    <div class="box" style="overflow-y: scroll;max-height: 600px">
		<input id="myInput" type="text" placeholder="Search.." class="pull-right">
        <table border='1' class="table table-striped table-bordered table-hover" id="dataTables-example">
				<thead>
					<tr>
						<th>ID</th>
						<th>Operator Name</th>
						<th>Category</th>
						<th>Type</th>
						<th>Purchase Cast</th>
						<th>Admin</th>
						<th>MD</th>
						<th>Distt</th>
						<th>Agent</th>
						<th>Agent Max Comm</th>						
						<th>Dist Max Comm</th>						
						<th>Md Max Comm</th>						
						<!--<th>Show Scheme</th>-->						
					</tr>
				</thead>
				<tbody id="myTable">
				   <?php $__currentLoopData = $inserID; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
					<tr class="odd gradeX">
                                            
                            <td><?php echo e(@$value->provider_id); ?></td>
                            <td><?php echo e(@$value->provider->provider_name); ?></td>
							<td><?php echo e(@$value->provider->service->service_name); ?></td>
                            <td>
                             <select class="form-control custome" name="commission_value[<?php echo e($value->id); ?>][type]" style="width:100px; text-align:center; margin-right:5px; height:34px;">
							<option value="0" <?php echo e(($value->type==0) ?"selected" : ""); ?>>%</option>
							<option value="1" <?php echo e(($value->type==1) ?"selected" : ""); ?>>Rs</option>                                  
                                </select>
                            </td>
                           
                            <td>
                                <input type="text" name="commission_value[<?php echo e($value->id); ?>][purchage_cast]" value="<?php echo e($value->purchage_cast); ?>"
                                       style="width:60px; text-align:center; margin-right:5px; height:34px;" class="form-control custom" readonly>
                            </td>
							<td>
                                <input type="text" name="commission_value[<?php echo e($value->id); ?>][admin]" value="<?php echo e($value->admin); ?>" style="width:60px; text-align:center; margin-right:5px; height:34px;" class="form-control custom" readonly>
                            </td>
                            <td>
                                <input type="text" name="commission_value[<?php echo e($value->id); ?>][md]" value="<?php echo e($value->md); ?>" style="width:60px; text-align:center; margin-right:5px; height:34px;"class="form-control custom" readonly>
                            </td>
                            <td>
                                <input type="text" name="commission_value[<?php echo e($value->id); ?>][d]" value="<?php echo e($value->d); ?>" style="width:60px; text-align:center; margin-right:5px; height:34px;"class="form-control custom" readonly>
                            </td>
                            <td>
                                <input type="text" name="commission_value[<?php echo e($value->id); ?>][r]" value="<?php echo e($value->r); ?>"style="width:60px; text-align:center; margin-right:5px; height:34px;"class="form-control custom" readonly>
                            </td>
							<td>
                                <input type="text" name="commission_value[<?php echo e($value->id); ?>][max_commission]" value="<?php echo e($value->max_commission); ?>"style="width:60px; text-align:center; margin-right:5px; height:34px;"class="form-control custom" readonly>
                            </td>
							<td>
                                <input type="text" name="commission_value[<?php echo e($value->id); ?>][dist_max_commission]" value="<?php echo e($value->dist_max_commission); ?>"style="width:60px; text-align:center; margin-right:5px; height:34px;"class="form-control custom" readonly>
                            </td>
							<td>
                                <input type="text" name="commission_value[<?php echo e($value->id); ?>][md_max_commission]" value="<?php echo e($value->md_max_commission); ?>"style="width:60px; text-align:center; margin-right:5px; height:34px;"class="form-control custom" readonly>
                            </td>
                            <!--<td>
                               <a href="#" onclick="show_scheme();">Show Scheme</a> 
                            </td>-->
                            <td> <span style="color:red"><?php echo e(($value->is_error) ? "Error":""); ?></span></td>
                    </tr>
										
                 <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>  
                </tbody>
        </table>
       <?php echo e(Form::close()); ?>

    </div>
</div>
    
    <div id="con-close-modal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
         aria-hidden="true" style="display: none;">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                    <h4 class="modal-title">Provider Editor</h4>
                </div>
                <div class="modal-body">
                    <div style="display:none" id="name-error"></div>

                    <form id="frmTasks" name="frmTasks" class="form-horizontal" novalidate="">
                        <?php echo csrf_field(); ?>

                        <div class="form-group">
                            <label for="inputTask" class="col-sm-3 control-label">Scheme Name</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control has-error" id="scheme_name" name="scheme_name"
                                       placeholder="Api Name" value="">
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button onclick="savedata()" type="button" class="btn btn-info waves-effect waves-light"
                            id="btn-save"
                            value="add">Save Now
                    </button>
                    <button type="button" class="btn btn-default waves-effect" data-dismiss="modal">Close</button>
                    <input type="hidden" id="id" name="id" value="0">
                </div>
            </div>
        </div>
    </div>
    <meta name="_token" content="<?php echo csrf_token(); ?>"/>
    <!-- END wrapper -->
<?php $__env->stopSection(); ?>
<?php echo $__env->make('admin.layouts.templatetable', \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>