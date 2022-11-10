<?php $__env->startSection('content'); ?>
<script type="text/javascript">

function getUser() {
    var user = $("#user").val();
    var message = $("#message").val();
    if(confirm('Are your sure to sent the message '))
        {
            var dataString = 'user=' + user + '&message=' + message;
            $.ajaxSetup({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                        }
                    })
                $.ajax({
                type: "post",
                url: "<?php echo e(url('send-sms-memberwise')); ?>",
                data: dataString,
                dataType: "json",
                success: function (msg) {
                if(msg.status_id==1)
                    alert(msg.message);
                }
            });
        }        
    }

</script>
<div class="card-box">
    <h4 class="m-t-0 header-title"><b>Message Detail</b></h4> <br>
    <div class="row">
        <?php if(Session::has('message')): ?>
            <div class="alert alert-success">
                <span class="glyphicon glyphicon-ok"></span>
                <?php echo e(Session::get('message')); ?>

            </div>
        <?php endif; ?>
        <div class="form-horizontal">
            <div class="col-md-6">
                <h4 class="m-t-0 header-title"><b>Member Wise</b></h4> <br>
                <form method="post" action="<?php echo e(url('send-sms-rolewise')); ?>"  class="form-horizontal">
                    <div class="form-group"><?php echo e(csrf_field()); ?>

                        <label class="col-md-3 control-label">Select Member</label>
                        <div class="col-md-9">
                            <select name="role_id" class="form-control" value="<?php echo e(old('role_id')); ?>">
                               <option>------Please Select Member Type------</option>
                               <option value="3">Master Distribuiter</option>
                               <option value="4">Distribuiter</option>
                               <option value="5">Retailer</option>
                            </select>   
                        </div>
                    </div>                 
                    <div class="form-group">
                        <label class="col-md-3 control-label">Message</label>
                        <div class="col-md-9">
                            <textarea class="form-control" name="message" rows="5" value="<?php echo e(old('message')); ?>"></textarea>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-sm-offset-3 col-sm-9">
                            <button type="submit" class="btn btn-success">Submit</button>
                        </div>
                    </div>
                </form>
            </div>
            <div class="col-md-6">
                <form method="post" action="<?php echo e(url('send-sms-memberwise')); ?>"  class="form-horizontal" enctype="multipart/form-data" autocomplete="off"><?php echo e(csrf_field()); ?>


                    <h4 class="m-t-0 header-title"><b>Particular Member</b></h4> <br>
                    <div class="form-group">
                        <label class="col-md-3 control-label">Select Member</label>
                        <div class="col-md-9">
                            <input type="text" list="browsers" class="form-control" name="user" id="user" >
                            <datalist id="browsers">
                                <option value="">--Select Member--</option>
                                <?php $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key=>$user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($key); ?>"><?php echo e($user); ?></option>
                                 <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                 <!-- Both Are Correct Above and Down -->
                            
                            </datalist>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-3 control-label">Message</label>
                        <div class="col-md-9">
                            <textarea class="form-control" id="message" name="message" rows="5" value="<?php echo e(old('message')); ?>"></textarea>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-sm-offset-3 col-sm-9">
                            <button type="submit" class="btn btn-success" >Submit </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>  
<meta name="_token" content="<?php echo csrf_token(); ?>"/> 
<?php $__env->stopSection(); ?>
<?php echo $__env->make('admin.layouts.templatetable', \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>