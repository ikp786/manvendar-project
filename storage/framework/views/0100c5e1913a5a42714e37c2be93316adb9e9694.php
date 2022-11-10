<?php $__env->startSection('content'); ?>
<script>
$(document).ready(function(){
    $('[data-toggle="popover"]').popover();   
});
</script>
<script>

function ConfirmMessage(){
        var  transactionpin=$("#transactionpin").val();
        var  confirm_transactionpin=$("#confirm_transactionpin").val();
       if(transactionpin != confirm_transactionpin)
        {
            alert("Transaction PIN did not match with confirm Transaction PIN Please Try Again ");
            $('#confirm_transactionpin').focus();
            return false;
        }
    }
    
function chekPswdValidation() 
	{
		
		var pswd_patter =/^(?=.*[A-Z])(?=.*[a-z])(?=.*\d)(?=.*?[#@$&*]).{8,}$/
		
		var password = matching_password = $("#password").val();
		var otp = $("#otp").val();
		matching_password = matching_password.toUpperCase();
		
		var confrim_password = $("#password-confirm").val();
		if(otp == '')
		{
			alert('Please Enter OTP');
			$('#otp').focus();
			return false;
		}
		else if(password == '')
		{
			alert('Please Enter New Password with 8 characters long');
			$('#password').focus();
			return false;
		}
		else if(matching_password.indexOf(' ') >=0)
		{
			alert('Space character is not allowed');
			$('#password').focus();
			return false;
		}
		else if(!password.match(pswd_patter))
		{
			alert('Please Enter password according to password policy');
			$('#password').focus();
			return false;
		}
		else if(password != confrim_password)
		{
			alert("Please Enter same password in Confirm Password");
			$('#confrim_password').focus();
			return false;
		}
	}
	</script>
<div class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default">
                <div class="panel-heading">Transaction Pin Update</div>
				 
					<?php if(session('fails')): ?>
                        <div class="alert alert-danger">
                           <?php echo e(session('fails')); ?>

                        </div>
                    <?php elseif(session('message')): ?>
                        <div class="alert alert-info"><?php echo e(Session::get('message')); ?></div>
                    <?php endif; ?>
                        
                   
                <div class="panel-body">
                    <form class="form-horizontal" role="form" method="post" action="<?php echo e(url('generateTransactionpinFrontEnd')); ?>" onSubmit="return ConfirmMessage()">
                        <?php echo e(csrf_field()); ?>


                        <input type="hidden" name="token" value="<?php echo e($token); ?>">
                        
                        <input type="hidden" class="form-control" name="front_verification" id="front_verification" value="1" />
                        <input type="hidden" name="ver_user_id" id="ver_user_id" value="<?php echo e(@$user_id); ?>" />
                       
						 <div class="form-group<?php echo e($errors->has('otp') ? ' has-error' : ''); ?>">
                            <label for="otp" class="col-md-4 control-label">OTP</label>
                            <div class="col-md-6">
                                <input id="otp" type="password" class="form-control" name="otp" placeholder="Enter Opt" >
                            </div>
                        </div>
                        <div class="form-group<?php echo e($errors->has('password') ? ' has-error' : ''); ?>">
                            <label for="password" class="col-md-4 control-label">Transaction Pin</label>

                            <div class="col-md-6">
                                <input type="password" class="form-control" name="txn_pin" id="transactionpin" placeholder="New PIN" maxlength="15" required> 
                            </div>
							<div class="col-md-2">
							
                            </div>
                        </div>
						

                        <div class="form-group<?php echo e($errors->has('password_confirmation') ? ' has-error' : ''); ?>">
                            <label for="password-confirm" class="col-md-4 control-label">Confirm Transaction Pin</label>
                            <div class="col-md-6"> 
                                <input type="password" class="form-control" name="confirm_txn_pin" id="confirm_transactionpin" placeholder="Confirm Pin" maxlength="15" required>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-md-6 col-md-offset-4">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fa fa-btn fa-refresh"></i> Submit
                                </button>
								<a href="<?php echo e(url('/')); ?>"
								 <button type="button" class="btn btn-primary">
                                    <i class="glyphicon glyphicon-chevron-left"></i> Go Back
                                </button></a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>