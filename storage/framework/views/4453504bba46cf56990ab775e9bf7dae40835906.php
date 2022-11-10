<?php $__env->startSection('content'); ?>
    <script>
        function flush_record() {
			if(confirm("Are you want to flush OTP for Distributor and Retailer"))
			{
            var dataString = 'case=otp_flush';
			$.ajaxSetup({
					headers: {
					'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
					}
				})
             $.ajax({
                type: "POST",
                url: "<?php echo e(url('flush_otp')); ?>",
                data: dataString,
                success: function (data) {
                    alert(data);
                }
            })
		}
        }
    </script>

    <!-- Page-Title -->
    
        <div class="col-md-12">
            <div class="col-md-6">
                <h3 class="page-title" style="color:black; "><?php echo e('Logged In Users'); ?></h3>
               
            </div>
            <div class="col-md-6 pull-right">
                <div class="pull-right">
                    <button onclick="flush_record()" id="demo-add-row" class="btn btn-success">Flush OTP
                    </button>
				</div>
            </div>
        </div>
    

    <!--Basic Columns-->
    <!--===================================================-->


    <!--===================================================-->
   
             <div class="">
        
                <table id="mytable" class="table table-bordered hover">
                    <thead>
                    <tr style="color:#115798;">
                        <th>ID</th>
                        <th>Member Name</th>
                        <th>Mobile</th>
						<th>Email</th>
                        <th>Member Otp</th>
                        <th>Total Logins</th>
                        <th>Last Login Time</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php $__currentLoopData = $users_otp; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr>
                            <td><?php if($user->role_id == 4): ?><?php echo e("D ".$user->id); ?><?php elseif($user->role_id==5): ?> <?php echo e("R ".$user->id); ?><?php elseif($user->role_id == 1): ?> <?php echo e("A ".$user->id); ?><?php elseif($user->role_id==3): ?> <?php echo e("M ".$user->id); ?> <?php endif; ?></td>
                            <td><?php echo e($user->name); ?></td>
                            <td><?php echo e($user->mobile); ?></td>
                            <td><?php echo e($user->email); ?></td>
                            <td><?php echo e($user->otp_number); ?></td>
                            <td><?php echo e($user->total_logins); ?></td>
                            <td><?php echo e($user->updated_at); ?></td>
							
                           
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>
            </div>
     
    
    <meta name="_token" content="<?php echo csrf_token(); ?>"/>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('admin.layouts.templatetable', \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>