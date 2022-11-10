<?php $__env->startSection('content'); ?>
<script>
	$(function(){
        $('input[type=radio]').change(function(){
            var status=$(this).val();
            var name=$(this).attr('name');
            var dataString="status="+status+"&field_name="+name;
            $.ajaxSetup({
                headers: {
                'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                }
            })
            $.ajax({
                type: "post",
                url: "<?php echo e(url('update-security')); ?>",
                data: dataString,
                dataType: "JSON",
                beforeSend:function(){
                },
                success: function (data) {
                    alert(data.message);
                }
            })
        })
    })
	
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
    function SchemeConfirmMessage()
    {
        var  Schemepin=$("#Schemepin").val();
        var  Confirm_Schemepin=$("#Confirm_Schemepin").val();
       if(Schemepin != Confirm_Schemepin)
        {
            alert("Scheme PIN did not match with confirm Scheme PIN Please Try Again ");
            $('#Confirm_Schemepin').focus();
            return false;
        }
    }
    function passwordRule() 
    { 
        var pswd_patter =/^(?=.*[A-Z])(?=.*[a-z])(?=.*\d)(?=.*?[#@$&*]).{8,}$/
        var exampleInputEmail1 = $("#exampleInputEmail1").val();
        var password = matching_password = $("#password").val();
        matching_password = matching_password.toUpperCase(); 
        var confrim_password = $("#confrim_password").val();
        if(exampleInputEmail1 == '')
        {
            alert('Please Enter Old Password');
            $('#exampleInputEmail1').focus();
            return false;
        }
        else if(password == '')
        {
            alert('Please Enter New Password with 8 minimum characters long');
            $('#password').focus();
            return false;
        }
        else if(matching_password.indexOf(' ') >=0)
        {
            alert('Space character is not allowed');
            $('#password').focus();
            return false;
        }
        else if(matching_password.indexOf('a2zsuvidhaa') >=0)
        {
            alert('Please Do not user Company name as Password ');
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
            alert("Please Enter same password in Confirm Password Field");
            $('#confrim_password').focus();
            return false;
        }
    }

// $(document).ready(function(){
  // $("#generate_mpin").click(function(){
    // $(".showform").show();
   // $("#reset_mpin").hide();
  // });
// });
function generateOTP()
{ 
        var  transactionpin=$("#transactionpin").val();
        var  confirm_transactionpin=$("#confirm_transactionpin").val();
        var Schemepin=$("#Schemepin").val();
        var Confirm_Schemepin=$("#Confirm_Schemepin").val();
       // if(transactionpin =='')
        // {
            // alert("Enter Transaction Pin");
            // return false;
        // }
       
       if(transactionpin != confirm_transactionpin || Schemepin!= Confirm_Schemepin)
        {
            alert("PIN did not match with Confirm PIN Please Try Again ");
            $('#confirm_transactionpin').focus();
            $('#Confirm_Schemepin').focus();
            return false;
        }
    $.ajaxSetup({
                    headers: {
                    'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                    }
                })
             $.ajax({
                type: "post",
                url: "<?php echo e(url('generate-otp')); ?>",
                data: "Case",
                dataType: "JSON",
                beforeSend:function(){
                    $("#otpBtn").hide()
                    $("#otpLoaderImg").show()
                    
                },
                success: function (data) {
                    //$("addBeneBtn").show()
                    $("#otpLoaderImg").hide()
                    alert(data.message);
                }
            })
}
</script>
        <!-- Optional header components (ex: slider) -->
<?php echo $__env->make('partials.tab', \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>
<!-- MAIN CONTENT -->
            <!-- <div class="pg-opt">
                <div class="container">
                    <div class="row">
                        <div class="col-md-6">
                            <h1><?php echo e(Auth::user()->role->role_title); ?> account</h1>
                        </div>
                       <div class="col-md-5">
                            <ol class="breadcrumb">
                               <li><a href="<?php echo e(url('/home/')); ?>">Home</a></li>
                                <li class="active"><?php echo e(Auth::user()->role->role_title); ?> account</li> 
                            </ol>
                        </div> 
                    </div>
                </div>
            </div>-->
                   
<section class="slice bg-white" >
    <div class="wp-section user-account">
        <div class="container">
            <div class="row">
                <div class="col-md-3">
                    <div class="user-profile-img">
                        <img src="<?php echo e(url('/user-uploaded-files')); ?>/<?php echo e(Auth::id()); ?>/<?php echo e(Auth::user()->profile->profile_picture); ?>" alt="<?php echo e(Auth::user()->name); ?>" style="height:50%;width:100%;">
                    </div>
                </div>
                <div class="col-md-9">
                    <?php if(session('status')): ?>
                        <div class="alert alert-success">
                            <?php echo e(session('status')); ?>

                        </div>
                    <?php endif; ?>
                    <?php if(session('statusfail')): ?>
                        <div class="alert alert-danger">
                            <?php echo e(session('statusfail')); ?>

                        </div>
                    <?php endif; ?>
                    <?php if($errors->any()): ?>
                        <div class="alert alert-danger">
                            <ul>
                                <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <li><?php echo e($error); ?></li>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </ul>
                        </div>
                    <?php endif; ?>
                    <?php if(Auth::user()->profile->pswd_res == 0): ?>
                        <div style="color:orange">
                            <h3>Warning : Please Change the password first.</h3>
                        </div>
                    <?php endif; ?>
                    <div class="tabs-framed">                    
                        <ul class="nav nav-pills" >
                            <li  style="background-color:gainsboro"><a href="#tab-1" data-toggle="tab">About me</a></li>
                            <li style="background-color:gainsboro"><a href="#tab-2"  data-toggle="tab">Change Password</a></li> 
                            <li style="background-color:gainsboro"><a href="#tab-3"  data-toggle="tab">Transaction Pin</a></li>
                            <?php if(Auth::user()->role_id==1): ?>
                            <li style="background-color:gainsboro"><a href="#tab-4"  data-toggle="tab">Scheme Pin</a></li>
							<!--<li style="background-color:gainsboro"><a href="#tab-5"  data-toggle="tab">Report Pin</a></li>-->
                            <?php endif; ?>
							<li style="background-color:gainsboro"><a href="#tab-5" data-toggle="tab">Login Authentication</a></li>
                        </ul>        
                        <ul class="tabs clearfix">
                        </ul>
                        <div class="tab-content">
                            <div class="tab-pane" id="tab-1">
                                <div class="tab-body" style="background-color: #ecf0f5;">
                                    <dl class="dl-horizontal style-2">
                                        <h3 class="title title-lg">Personal information</h3>
                                        <p class="mb-20"></p>
                                         <dt>Your ID :</dt>
                                                <dd><?php echo e(Auth::user()->prefix); ?> <?php echo e(Auth::user()->id); ?></span></dd>
                                        <dt>Your name :</dt>
                                        <dd><?php echo e(Auth::user()->name); ?></dd>
                                        <dt>Shop Name :</dt>
                                        <dd><?php echo e(Auth::user()->member->company); ?> </dd>
                                        <dt>Email :</dt>
                                        <dd><?php echo e(Auth::user()->email); ?></dd>
                                        <dt>Mobile :</dt>
                                        <dd><?php echo e(Auth::user()->mobile); ?></dd>
                                        <dt>Address :</dt>
                                        <dd><?php echo e(Auth::user()->member->address); ?></dd>
                                        <dt>Shop Address :</dt>
                                        <dd><?php echo e(Auth::user()->member->office_address); ?></dd>
                                        <dt>Joining Date :</dt>
                                        <dd><?php echo e(date("d-m-Y", strtotime(Auth::user()->created_at))); ?></dd>
                                        <dt>Last Update :</dt>
                                        <dd> <?php echo e(date("d-m-Y", strtotime(Auth::user()->updated_at))); ?></dd>
                                    </dl>
                                </div>
                            </div>
                            <div class="tab-pane" id="tab-2">
                                <div class="tab-body" style="background-color: #ecf0f5;">
                                    <h3 class="title title-lg">Change Password</h3>                                          
                                    <div class="row">    
                                        <form  method="post" action="<?php echo e(url('change_password')); ?>" onSubmit="return passwordRule()">
                                        <div class="col-md-12">
                                        <div class="col-md-6">
                                            <?php echo csrf_field(); ?>

                                            <div class="form-group">
                                                <label for="old_passwor">Old Password</label>
                                                    <input type="password" name="old_password" class="form-control" id="exampleInputEmail1" placeholder="Old Password">
                                            </div>
                                        </div>    
                                    </div>
                                    <div class="col-md-12">
                                        <div class="col-md-6">                                          
                                            <div class="form-group">
                                                <label for="exampleInputPassword1">New Password</label>
                                                <input type="password" class="form-control" name="password" id="password" placeholder="New Password">
                                            </div>
                                        </div>
                                            <div class="col-md-6" >
                                            <p >Password Policy : One Capital letter, One small letter, One Number, One special characters should be in #@$&* and password length should be 8 characters long. Password should not contain words a2zsuvidhaa.
                                            </p></div>
                                     </div>
                                    <div class="col-md-12">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="exampleInputPassword1">Confirm Password</label>
                                                <input type="password" class="form-control" id="confrim_password" placeholder="Confirm Password">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6" style="text-align: center;"><button type="submit" class="btn btn-info">Change Password</button></div>  
                                   </form>
                                </div>
                            </div>
                         </div>
                        <div class="tab-pane" id="tab-3" >
                            <div class="tab-body" style="background-color: #ecf0f5;">
                               <!--  <h3 class="title title-lg">GENARATE Transaction PIN</h3> -->
                              <div class="row">
                                <form  method="post" action="<?php echo e(url('generateTransactionpin')); ?>" onSubmit="return ConfirmMessage()">  <?php echo csrf_field(); ?>

                                    <div class="col-md-12" >
                                        <div class="col-md-6">                                       
                                            <div class="form-group">
                                                <label >Transaction PIN</label>
                                                <input type="password" class="form-control" name="txn_pin" id="transactionpin" placeholder="New PIN" maxlength="15" required>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="col-md-6">  
                                            <div class="form-group">
                                                <label >Confirm Transaction PIN</label>
                                                <input type="password" class="form-control" name="confirm_txn_pin" id="confirm_transactionpin" placeholder="Confirm Pin" maxlength="15" required>
                                            </div>
                                        </div>
                                   </div>                                           
                                   <div class="col-md-12 ">
                                        <div class="col-md-6">  
                                            <div class="form-group">
                                                <label >OTP</label>
                                                <input type="password" class="form-control" name="otp" id="otp" placeholder="Enter OTP" >
                                            </div>
                                        </div>
                                   </div>  
                                    <div class="col-md-6" style="text-align: center;">
                                        <a onClick="generateOTP()" id="otpBtn" class="btn btn-basic showform" style="background:khaki;">Send OTP</a><img src="<?php echo e(url('/loader/loader.gif')); ?>" id="otpLoaderImg" class="loaderImg" style="display:none"/>
                                        <button type="submit" class="btn btn-basic">Generate</button>
                                    </div>
                                 </form>
                                </div>
                            </div>
                        </div>
                        <!-- scheme pin -->
                        <?php if(Auth::user()->role_id==1): ?>
                        <div class="tab-pane" id="tab-4">
                            <div class="tab-body" style="background-color:#ecf0f5;">
                               <!--  <h3 class="title title-lg">GENARATE Transaction PIN</h3> -->
                              <div class="row">
                                <form  method="post" action="<?php echo e(url('generateSchemepin')); ?>" onSubmit="return SchemeConfirmMessage()">  <?php echo csrf_field(); ?>

                                    <div class="col-md-12" >
                                        <div class="col-md-6">                                       
                                            <div class="form-group">
                                                <label >Scheme PIN</label>
                                                <input type="password" class="form-control" name="scheme_pin" id="Schemepin" placeholder="New Scheme PIN" maxlength="15" required>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="col-md-6">  
                                            <div class="form-group">
                                                <label >Confirm Scheme PIN</label>
                                                <input type="password" class="form-control" name="confirm_scheme_pin" id="Confirm_Schemepin" placeholder="Confirm Scheme Pin" maxlength="15" required>
                                            </div>
                                        </div>
                                   </div>                                           
                                   <div class="col-md-12 ">
                                        <div class="col-md-6">  
                                            <div class="form-group">
                                                <label >OTP</label>
                                                <input type="password" class="form-control" name="otp" id="otp" placeholder="Enter OTP" required>
                                            </div>
                                        </div>
                                   </div>  
                                    <div class="col-md-6" style="text-align: center;">
                                        <a onClick="generateOTP()" id="otpBtn" class="btn btn-basic showform" style="background:khaki;">Send OTP</a><img src="<?php echo e(url('/loader/loader.gif')); ?>" id="otpLoaderImg" class="loaderImg" style="display:none"/>
                                        <button type="submit" class="btn btn-basic">Generate</button>
                                    </div>
                                 </form>
                                </div>
                            </div>
                        </div>
                        <?php endif; ?>
						<div class="tab-pane" id="tab-5" >
                            <div class="tab-body" style="background-color: #ecf0f5;">
                              <div class="row">
                                <form method="post"> 
                                     <?php echo csrf_field(); ?>

                                    <div class="col-md-12" >
                                        <div class="col-md-6">                          
                                            <div class="form-group">
                                            <label >New System Verification</label><br>
                                            <input type="radio" name="is_sys_verification" value="1" <?php echo e((Auth::user()->profile->is_sys_verification) ? "checked" : ""); ?>>Yes<br>
                                            <input type="radio" name="is_sys_verification" value="0" <?php echo e((Auth::user()->profile->is_sys_verification==0) ? "checked" : ""); ?>>No<br>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="col-md-6">  
                                            <div class="form-group">
                                                <label >Daily OTP Serice</label><br>
                                                <input type="radio" name="is_opt_verification" value="1" <?php echo e((Auth::user()->profile->is_opt_verification) ?  "checked" : ""); ?> > Yes<br>
                                                <input type="radio" name="is_opt_verification" value="0" <?php echo e((Auth::user()->profile->is_opt_verification==0) ?  "checked" : ""); ?> > No<br>
                                            </div>
                                        </div>
                                    </div>      
                                 </form>
                               </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<meta name="_token" content="<?php echo csrf_token(); ?>"/>



<?php $__env->stopSection(); ?>
<?php echo $__env->make('admin.layouts.templatetable', \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>