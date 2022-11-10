<?php $__env->startSection('content'); ?>
    <script>
        function add_record() {
            $('#btn-save').val("add");
            $('#frmTasks').trigger("reset");
            $("#con-close-modal").modal("toggle");
        }
        //create new task / update existing task
        function savedata() {
            var url = "provider";
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                }
            })
            var formData = {
                provider_name: $('#provider_name').val(),
                provider_code: $('#provider_code').val(),
                service_id: $('#service_id').val(),
                api_code: $('#api_code').val(),
                api_id: $('#api_id').val(),
                provider_picture: $('#provider_picture').val(),
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
                url: "<?php echo e(url('provider/view')); ?>",
                data: dataString,
                success: function (data) {
                    $('#id').val(data.id);
                    $('#provider_name').val(data.provider_name);
                    $('#provider_code').val(data.provider_code);
                    $('#service_id').val(data.service_id);
                    $('#api_id').val(data.api_id);
                    $('#api_code').val(data.api_code);
                    $('#btn-save').val("update");
                    $("#con-close-modal").modal("toggle");
                }
            })
        }
		
$(document).ready(function () {
        $.noConflict();
        $('.customDatepicker').datepicker({
              autoclose: true,  
            format: "dd-mm-yyyy"
        });

    }); 			
		
    </script>

	<div class="row">
		<div class="panel panel-default">
			<div class="panel-body">
				 <div class="col-md-12">
				<div class="col-md-3">
					<h4 class="page-title" style="color: black"><?php echo e('Agent REQUEST REPORT'); ?></h4>
				</div>
					<div class="col-md-9">
						<form method="get" action="<?php echo e(Request::url()); ?>" class="form-inline" autocomplete="off">
							<?php echo e(Form::select('recordCount', ['All'=>'All','10'=> '10','30'=> '30','50'=>'50','60'=>'60','80'=>'80'],app('request')->input('recordCount'), ['class'=>'form-control','id'=>'recordCount','placeholder'=>'--Select No of Record--'])); ?>

							<input name="fromdate" class="form-control customDatepicker" type="text" value="<?php echo e((app('request')->input('fromdate')) ? app('request')->input('fromdate') : date('d-m-Y')); ?>">					
							<input name="todate" class="form-control customDatepicker" type="text" value="<?php echo e((app('request')->input('todate')) ? app('request')->input('todate') : date('d-m-Y')); ?>">							
							<button name="export" value="Search" type="submit" class="btn btn-primary waves-effect waves-light m-l-10 btn-md"><span class="glyphicon glyphicon-find"></span><i class="fa fa-search"></i></button>
							<button name="export" value="Payment Request Report" type="submit" class="btn btn-success waves-effect waves-light m-l-10 btn-md"><span class="glyphicon glyphicon-find"></span><i class="fa fa-file-excel-o" aria-hidden="true"></i></button>
							<a href="<?php echo e(Request::url()); ?>"/ class="btn btn-info  btn-md"><i class="fa fa-refresh"></i></a>
						</form>
					</div>                       
				</div>
			</div>
		</div><br> 
		<div class="box">
			<table id="example2" data-toggle="table" class="table table-bordered" data-search="true">
				<thead>
				<tr>
					<th data-field="date" data-sortable="true" data-formatter="dateFormatter">Date/Time </th>
					<!-- <th data-field="time" data-sortable="true" data-formatter="dateFormatter">Time </th> -->
					<th data-field="id">Order ID</th>
					<th data-field="recharge_type">Wallet</th>
					<th data-field="user" data-sortable="true">User Name</th>
					<th>Request To</th>
					<th>Payment Mode</th>
					<th data-field="name" data-sortable="true">Payment Type</th>
					<th data-field="bank_name" data-sortable="true">Bank Name</th>
					<th data-field="bankref" data-align="center" data-sortable="true" data-sorter="priceSorter">Ref Id</th>
					<th>Location/Branch Code</th>
					<th data-field="deposite_slip" data-align="center" data-sortable="true" data-sorter="priceSorter">Deposit Slip</th>
					<th data-field="amount" data-align="center" data-sortable="true" data-sorter="priceSorter">Amount</th>
					<th data-field="remark" data-align="center" data-sortable="true">Remark</th>
					<th>Request Remark</th>
					<th>Updated Remark</th>
					<th data-field="status" data-align="center" data-sortable="true" data-formatter="statusFormatter">Status
					</th>
					<th data-field="updated_by" data-align="center" data-sortable="true">Approved By 
					</th>
				</tr>
				</thead>

				<tbody>
				<?php $__currentLoopData = $loadcashes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
				<?php $s = $value->created_at;
					$dt = new DateTime($s);
					?>
					<tr>
						<td><?php echo e($dt->format('d/m/Y')); ?><br><?php echo e($dt->format('H:i:s')); ?></td>
						<td><?php echo e($value->id); ?></td>
						<td><?php echo e(($value->wallet == 1) ? 'Recharge' : 'Money'); ?></td>
						<td><?php echo e($value->user->name); ?> ( <?php echo e($value->user->id); ?> )</td>
						<td>
							<?php if($value->request_to==2): ?>
						   <p>Company</p>
						   <?php elseif($value->request_to==1): ?>
							
							<?php echo e(Auth::user()->name); ?></br>
							<?php echo e(Auth::user()->role->role_title); ?>

						   <?php endif; ?>
						</td>
					   <!--  <td><?php echo e(($value->request_to == 2) ? 'Company' : '{{Auth::user()->parent->role->role_title'); ?></td> -->
						<td><?php echo e($value->payment_mode); ?></td>
						<td><?php echo e(@$value->pmethod->payment_type); ?></td>
						<td>
							<?php if($value->request_to==2): ?>
								<?php echo e(@$value->netbank->bank_name); ?>

							<?php elseif($value->request_to==1): ?>
								<?php echo e(@$value->bank_name); ?>

							<?php endif; ?>
							</td>
						<td><?php echo e($value->bankref); ?></td>
						<td><?php echo e(@$value->loc_batch_code); ?></td>
						 <td>
						 <?php if( $value->d_picture ): ?>
						 <a target="_blank" href='deposit_slip/images/<?php echo e($value->d_picture); ?>'><img src="deposit_slip/images/<?php echo e($value->d_picture); ?>" height="60px" width="60px"></a>
						 <?php else: ?> <?php echo e('No Slip'); ?>

						 <?php endif; ?>
						 </td>
						<td><?php echo e($value->amount); ?></td>
						<td><?php echo e($value->remark->remark); ?></td>
						<td><?php echo e($value->request_remark); ?></td>
						<td><?php echo e($value->approve_remark); ?></td>
						<td><?php echo e($value->status->status); ?></td>
						<td>
							<?php if(is_numeric($value->updated_by)): ?>
								<?php echo e(\App\User::find($value->updated_by)->name); ?>(<?php echo e($value->updated_by); ?>)
							<?php else: ?>
								<?php echo e($value->updated_by); ?>

							<?php endif; ?>
						</td>
					</tr>
					<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
				</tbody>
			</table>
               
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
                            <label for="inputTask" class="col-sm-3 control-label">Provider Name</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control has-error" id="provider_name" name="task" placeholder="Provider Name" value="">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="inputEmail3" class="col-sm-3 control-label">Provider Code</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control" id="provider_code" name="provider_code" placeholder="Provider Code" value="">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="inputEmail3" class="col-sm-3 control-label">Provider Picture</label>
                            <div class="col-sm-9">
                                <?php echo e(Form::file('provider_picture', array('class' => 'form-control','id' => 'provider_picture'))); ?>

                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button onclick="savedata()" type="button" class="btn btn-info waves-effect waves-light"
                        id="btn-save" value="add">Save Now
                    </button>
                    <button type="button" class="btn btn-default waves-effect" data-dismiss="modal">Close</button>
                    <input type="hidden" id="id" name="id" value="0">
                </div>
            </div>
        </div>
    </div><!-- /.modal -->
    <!-- END wrapper -->
    <meta name="_token" content="<?php echo csrf_token(); ?>"/>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('admin.layouts.templatetable', \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>