<?php $__env->startSection('content'); ?> 
<script type="text/javascript">
    function getApiUserIp(id){
        //alert(id);
        var dataString = 'id='+id;
        $.ajax({
            type:"get",
            url:"<?php echo e(url('get-user-detail')); ?>/"+id,
            data: dataString,
            datatype:"json",
            success:function (data) {
                $('#id').val(data.user_id);
                $('#server_ip').val(data.server_ip);
                $('#server_ip_second').val(data.server_ip_second);
                //$('#txncharge').val(data.txn_charge);
                //$('#verfycharge').val(data.verify_charge);
                $("#con-close-modal").modal("toggle");
            }
        });
    }
    function savedata()
    {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        var token =$("input[name=csrf-token]").val();
        var user_id =$('#id').val();
      //  alert(user_id);
        var server_ip =$('#server_ip').val();
        var server_ip_second =$('#server_ip_second').val();
        //var txncharge =$('#txncharge').val();
        //var verfycharge =$('#verfycharge').val();
        var dataString ='user_id='+user_id+'&server_ip='+server_ip+'&server_ip_second='+server_ip_second;//+'&_token='+token;
        $.ajax({
            type: "POST",
            url: "<?php echo e(url('save-get-user-detail')); ?>",
            data: dataString,
            datatype: "json",
            success: function (data) {
               alert(data.message);
               location.reload();
            }
        });
    }
</script> 
   <?php echo $__env->make('admin.admin-subtab.member-type', \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>
<div class="col-sm-12">
    <div class="col-lg-4 col-md-6">
        <h1 class="page-title" style="color:black;"><?php echo e('API Management'); ?></h1>
    </div>
</div>

<div class="box" style="overflow-x: scroll;"><!--style="overflow-x: scroll;"-->
	<table class="table table-bordered" id="example2">
		<thead>
			<tr style="color:#115798;">
				<th>User ID</th>
				<th>Name</th>
				<th>Mobile</th>
				<th>API Token</th>
				<th>Secret Key</th>
				<th>Server IP</th>
				<th>Server IP Second</th>
				<th>Action</th>
			</tr>
		</thead>
		<tbody>
		<?php $__currentLoopData = $getalluser; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $getallusers): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
			<tr>
				<td><?php echo e($getallusers->prefix); ?>-<?php echo e($getallusers->id); ?></td>
				<td><?php echo e($getallusers->name); ?></td>
				<td><?php echo e($getallusers->mobile); ?></td>
				<td><?php echo e($getallusers->api_token); ?></td>
				<td><?php echo e($getallusers->member->secret_key); ?></td>
				<td><?php echo e($getallusers->member->server_ip); ?></td>
				<td><?php echo e($getallusers->member->server_ip_second); ?></td>
					<td style="text-align: center; "><a onclick="getApiUserIp(<?php echo e($getallusers->id); ?>)" href="javascript:void(0)" class="table-action-btn"><i class="fa fa-edit"></i></a>
					</td>
			</tr>
		<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
		</tbody>
	</table>
</div>
<div id="con-close-modal-one" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display:none;">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                <h4 class="modal-title">Member Editor</h4>
            </div>
        </div>
    </div>
</div><!-- /.modal -->
<div id="con-close-modal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                <h4 class="modal-title">User Editor</h4>
            </div>
            <form id="frmTasks" name="frmTasks" class="form-horizontal" novalidate="" method="post">  
                <div class="modal-body">
                    <div style="display:none" id="name-error"></div>
                    <input type="hidden" name="id"  id="id">
                    <div class="form-group">
                        <label for="inputTask" class="col-sm-3 control-label">Server Ip</label>
                        <div class="col-sm-9">
                            <input type="text" class="form-control has-error" id="server_ip" name="server_ip" placeholder="Server IP" value="">
                        </div>
                    </div>
					<div class="form-group">
                        <label for="inputTask" class="col-sm-3 control-label">Server Ip Second</label>
                        <div class="col-sm-9">
                            <input type="text" class="form-control has-error" id="server_ip_second" name="server_ip_second" placeholder="Server IP Seond" value="">
                        </div>
                    </div>
                    <!-- <div class="form-group">
                        <label for="inputTask" class="col-sm-3 control-label">Transaction Charge</label>
                        <div class="col-sm-9">
                            <input type="text" class="form-control has-error" id="txncharge" name="txncharge" placeholder="Provider Name" value="">  
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="inputEmail3" class="col-sm-3 control-label">Verification Charge</label>
                        <div class="col-sm-9">
                            <input type="text" class="form-control" id="verfycharge" name="verfycharge" placeholder="Verification Charge" value="">
                        </div>
                    </div> -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-info waves-effect waves-light" id="btn-save" value="add" onclick="this.disabled=true;savedata()">Save Now
                    </button>
                    <button type="button" class="btn btn-default waves-effect" data-dismiss="modal">Close</button>
                </div>
            </form>
        </div>
    </div>
</div>
<meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
<?php $__env->stopSection(); ?>
<?php echo $__env->make('admin.layouts.templatetable', \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>