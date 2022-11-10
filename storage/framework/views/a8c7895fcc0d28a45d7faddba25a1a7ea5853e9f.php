
<?php $__env->startSection('content'); ?>

<style type="text/css">
.center {
  margin: auto;
  width: 10%;
 }
</style>
<script type="text/javascript">

    function patternRule() 
    { 
        var pan_number_pattern =/^([A-Za-z]){5}([0-9]){4}([A-Za-z]){1}?$/;
        var adhar_number_pat = /^[0-9]+$/;
        var email_pattern =  /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/; 
        var profile_picture =   $('#profile_picture').val();
        var adhar_card_image =  $('#aadhaar_card_image').val();
        var aadhaar_img_back =  $('#aadhaar_img_back').val();
        var shop_image       =  $('#shop_image').val();
        var pan_card_image   =  $('#pan_card_image').val
        if(!pan_number.match(pan_number_pattern))
        {
            alert('Please Enter password according to password policy');
            $('#pan_number').focus();
            return false;
        }
        else if(pan_number.length != 10 )
        {
            alert('Please 10 digits PAN card number ');
            $('#pan_number').focus();
            return false;
        }
        else if(!adhar_number.match(adhar_number_pat))
        {
            alert("Please Enter valid aadhaar card number");
            $('#adhar_number').focus();
            return false;  
        }
        else if(!mobile.match(adhar_number_pat))
        {
            alert('Only Number is allowed');
            $('#mobile').focus();
            return false;
        }
        if( profile_picture !='')
            {
                var profile_image_size=(document.getElementById('profile_picture').files[0].size/1024);
                if(profile_image_size >200)
                {
                    $("#profile-image-error").text('file size must be less that 200 kb.');
                    return false;
                }
                else
                {
                    $("#profile-image-error").text('');
                }
            }
        if( shop_image !='')
            {
                var shop_image_size=(document.getElementById('shop_image').files[0].size/1024);
                if(shop_image_size >500 )
                {
                    $("#shop-image-error").text('file size should be less than 500 KB.');
                    return false;
                }
                else
                {
                    $("#shop-image-error").text('');
                }  
            }
        if(pan_card_image =='')
            {
                alert('Please Choose PAN card image');
                return false;
            }
        if( pan_card_image !=''){
             var pan_image_size=(document.getElementById('pan_card_image').files[0].size/1024);
            if(pan_image_size >500 )
            {
                $("#pan-image-error").text('file size must be less than 500 kb.');
                return false;
            }
            else
            {
                $("#pan-image-error").text('');
            } 
        }    
        if( adhar_card_image !='')
            {
                var adhar_image_size=(document.getElementById('adhar_card_image').files[0].size/1024);
                if(adhar_image_size >500)
                {
                    $("#adhar-image-error").text('file size must be 500 kb.');
                    return false;
                }
                else
                {
                    $("#adhar-image-error").text('');
                }
            }
        if( aadhaar_img_back !='') // Added
        {
            var aadhaar_img_back=(document.getElementById('aadhaar_img_back').files[0].size/1024);
            if(aadhaar_img_back >500)
            {
                $("#adhar-image-back-error").text('file size must be 300 kb.');
                return false;
            }
            else
            {
                $("#adhar-image-back-error").text('');
            }
        }           
    }

</script>
<div class="panel panel-body" style="background-color:pink;border-color:red ">
    <div class="row justify-content">
       
      <?php if(count($errors)): ?>
        <div class="alert alert-danger">
            <strong>Whoops!</strong>There were some problems with your input.
            <br/>
            <ul>
                <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <li><?php echo e($error); ?></li>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </ul>
        </div>
        <?php endif; ?>
         <center>
          <?php if(Session::has('message')): ?>
            <font style="color:red"><?php echo session('message'); ?></font>
             <?php endif; ?>
         </center>
        <div class="col-md-12">     
            <div class="card">
                   <!-- <h3 class="center">SignUp</h3> -->
                   <br>
                <div class="card-body">
                      
                    <form method="POST" action="<?php echo e(route('storesignup')); ?>" autocomplete="off"  enctype="multipart/form-data">
                         <?php echo e(csrf_field()); ?>

                        <div class="col-md-6"> 
                            <div class="form-group">
                                <label for="name" class="col-md-4 col-form-label text-md-right">Name</label>
                                <div class="col-md-6">
                                    <input id="name" type="text" class="form-control<?php echo e($errors->has('name') ? ' is-invalid' : ''); ?>" name="name" required="" value="<?php echo e(old('name')); ?>">
                                    <?php if($errors->has('name')): ?>
                                        <span class="invalid-feedback" role="alert">
                                            <strong><?php echo e($errors->first('name')); ?></strong>
                                        </span>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="email" class="col-md-4 col-form-label text-md-right">E-Mail Address</label>
                                <div class="col-md-6">
                                    <input id="email" type="email" class="form-control<?php echo e($errors->has('email') ? ' is-invalid' : ''); ?>" name="email" value="<?php echo e(old('email')); ?>" required>
                                    <?php if($errors->has('email')): ?>
                                        <span class="invalid-feedback" role="alert">
                                            <strong><?php echo e($errors->first('email')); ?></strong>
                                        </span>
                                    <?php endif; ?>
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <label for="mobile" class="col-md-4 col-form-label text-md-right">Mobile</label>
                                <div class="col-md-6">
                                    <input id="mobile"  class="form-control<?php echo e($errors->has('name') ? ' is-invalid' : ''); ?>" name="mobile" required maxlength="10" value="<?php echo e(old('mobile')); ?>" required="">
                                    <?php if($errors->has('mobile')): ?>
                                        <span class="invalid-feedback" role="alert">
                                            <strong><?php echo e($errors->first('mobile')); ?></strong>
                                        </span>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="address" class="col-md-4 col-form-label text-md-right">Address Detail</label>
                                <div class="col-md-6">
                                    <textarea id="address" class="form-control" name="address" value=
                                    "<?php echo e(old('address')); ?>"></textarea>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="inputEmail3" class="col-md-4 col-form-label text-md-right">Region</label>
                                <div class="col-md-6">
                                     <?php echo e(Form::select('region', ['1' => 'North', '2' => 'South','3'=>'East','4'=>'West'], null, ['class'=>'form-control','id'=>'region'])); ?>

                                </div>
                            </div>
                            <div class="form-group">
                                <label for="inputEmail3" class="col-md-4 col-form-label text-md-right">State</label>
                                <div class="col-md-6">
                                    <?php echo e(Form::select('state_id', $state_list, old('state_id'), array('class' => 'form-control','id' => 'state_id','style'=>'max-width: 338%;'))); ?>

                                </div>
                            </div>
                            <div class="form-group">
                                <label for="inputEmail3" class="col-md-4 col-form-label text-md-right"> Shop Name </label>
                                <div class="col-md-6">
                                    <?php echo e(Form::text('company', null,['class' => 'form-control form-input-field','id'=>'company','placeholder'=>'Enter Your Shop Name'])); ?>

                                </div>
                            </div>
                            <div class="form-group">
                                <label for="shop" class="col-md-4 col-form-label text-md-right">Shop Address</label>
                                <div class="col-md-6">
                                    <textarea id="office_address" class="form-control" name="office_address" value="<?php echo e(old('office_address')); ?>" ></textarea>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="inputEmail3" class="col-md-4 col-form-label text-md-right">Pin Code </label>
                                <div class="col-md-6">
                                    <input type="text" class="form-control" id="pin_code" name="pin_code" placeholder="Pin Code" value="<?php echo e(old('pin_code')); ?>" maxlength="6" required="">
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="password-confirm" class="col-md-4 col-form-label text-md-right">Pancard Number</label>
                                <div class="col-md-6">
                                    <input id="pan_number" type="text" class="form-control<?php echo e($errors->has('pan_number') ? ' is-invalid' : ''); ?>" name="pan_number" required value="<?php echo e(old('pan_number')); ?>" maxlength="10" required="">
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="password-confirm" class="col-md-4 col-form-label text-md-right">Aadharcard Number</label>
                                <div class="col-md-6">
                                    <input id="adhar_number" type="text" class="form-control<?php echo e($errors->has('adhar_number') ? ' is-invalid' : ''); ?>" name="adhar_number" required maxlength="12" value="<?php echo e(old('adhar_number')); ?>">
                                </div>
                            </div>
                            <div class="form-group">
                                 <label for="password-confirm" class="col-md-4 col-form-label text-md-right">Member Type</label>
                                <div class="col-md-6">
                                   <select name="role_id" class="form-control<?php echo e($errors->has('role_id') ? ' is-invalid' : ''); ?>" required="" value="<?php echo e(old('role_id')); ?>">
                                      <!-- <option>--------Please Select Member Type--------</option>
                                       <option value="3">Master Distribuiter</option>
                                       <option value="4">Distribuiter</option>-->
                                       <option value="5">Retailer</option>
                                   </select> 

                                    <?php if($errors->has('role_id')): ?>
                                        <span class="invalid-feedback" role="alert">
                                            <strong><?php echo e($errors->first('role_id')); ?></strong>
                                        </span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>    
                        <div class="col-md-6">  
                            <div class="form-group">
                                <label for="photo" class="col-md-4 col-form-label text-md-right">Photo</label>
                                <div class="col-md-6" id="show_profile">
                                    <input type="file" name="profile_picture" id="profile_picture" class="form-control">
                                   <!--  <?php echo e(Form::file('profile_picture', array('class' => 'form-control','id' => 'profile_picture','style'=>'max-width: 338%;'))); ?> -->
                                    <span id="profile-image-error" class="has-error" style="color:red;"> <!-- file size : 200 KB --></span>
                                    <span id="profile-image" class="span-img"><img src="" /></span>
                                </div>            
                            </div>
                            
                           <!--   <div class="form-group">
                                <label for="photo" class="col-md-4 col-form-label text-md-right">Shop Image</label>
                                <div class="col-md-6 hide-upload-image" id="show_shop">-->
                                    <!--  <?php echo e(Form::file('shop_image', array('class' => 'form-control','id' => 'shop_image'))); ?> -->
                                      <!-- <input type="file" name="shop_image" id="shop_image" class="form-control">
                                    <span id="shop-image" class="span-img">
                                    <img src=""  /></span>
                                    <span id="shop-image-error" class='has-error' style="color:red;"></span >          
                                </div>
                            </div>-->
                            <div class="form-group">
                                 <label for="pan" class="col-md-4 col-form-label text-md-right">Pan Image<span style="color:red"> *</span></label>
                                <div class="col-md-6 hide-upload-image" id="show_pan">
                                  <!--   <?php echo e(Form::file('pan_card_image', array('class' => 'form-control','id' => 'pan_card_image'))); ?> -->
                                    <input type="file" name="pan_card_image" id="pan_card_image" class="form-control" required="">
                                     <span id="pan-card-image" class="span-img">
                                    <img src=""  /></span>
                                    <span id="pan-image-error" class='has-error' style="color:red;"><!-- file size : 500 KB --></span>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="inputEmail3" class="col-md-4 col-form-label text-md-right">Aadhaar Front Img<span style="color:red"> *</span></label>
                                <div class="col-md-6 hide-upload-image" id="show_aadhaar">
                                    <!-- <?php echo e(Form::file('aadhaar_card_image', array('class' => 'form-control','id' => 'adhar_card_image'))); ?> -->
                                    <input type="file" name="aadhaar_card_image" id="aadhaar_card_image" class="form-control" required="">
                                    <span id="adhar-card-image" class="span-img">
                                    <img src=""  /></span>
                                    <span id="adhar-image-error" class='has-error' style="color:red;"><!-- File size : 500 KB --></span>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="inputEmail3" class="col-md-4 col-form-label text-md-right">Aadhaar Back Img<span style="color:red"> </span></label>
                                <div class="col-md-6 hide-upload-image" id="show_aadhaar_back">
                                   <!--  <?php echo e(Form::file('aadhaar_img_back', array('class' => 'form-control','id' => 'aadhaar_img_back'))); ?> -->
                                   <input type="file" name="aadhaar_img_back" id="aadhaar_img_back" class="form-control" >
                                    <span id="adhar-card-back-image" class="span-img">
                                    <img src=""  /></span>
                                    <span id="adhar-image-back-error" class='has-error' style="color:red;"><!-- File size : 500 KB --></span>
                                </div>
                            </div>
							<div class="form-group" style="float: left">
                                <h2>Declaration By Applicant </h2>
                                <input class="togglefunction" type="checkbox" name="declaration" required="" value="1"> <a href="href" data-toggle="modal" data-target="#myModal"><label> Terms and Condition:</label></a>


                                <div class="modal fade" id="myModal" role="dialog">
                                    <div class="modal-dialog">
                                      <!-- Modal content-->
                                      <div class="modal-content">
                                        <div class="modal-header">
                                          <button type="button" class="close" data-dismiss="modal">&times;</button>
                                          <h3 class="modal-title">Terms and Condition</h3>
                                        </div>
                                        <div class="modal-body">
                                        <h3> I hereby understand and agree that:-</h3>
                                            <p>1. Details  submitteded by me for impanelment is true & correct and belongs to me.</p>
                                            <p>2. I have understood the terms of BC Business and agree to comply with Bank's, Company's, Provider's and RBI's guidelines from me-to-me.</p>
                                            <p>3. I will maintain the required details for each transaction processed by me on behalf of customer.</p>
                                            <p>4. I will not misuse Company's, Provider's or Bank's systems for unlawful transacons.</p>
                                            <p>5. I will abide by the terms of agreement & service for which I am being empanelled, experiences etc is found to be improper, incorrect or not as per ICICI Bank, Provider, Company's or RBI's guidelines for impanelment.</p>
                                            <p>6. I authorize A2Z Suvidhaa, Provider & Bank to verify the details mentioned above and such other details as they may deem fit in connection with my impanelment.</p>
                                            <p>7. I confirm that I am not associated with any company providing money transfer or such BC Business Services or I am willing to resign from any such company for the purpose of onboarding with A2Z Suvidhaa.</p>
                                            <p>8. I promise not to share the customer details with others.</p>
                                            <p>9. I undertake that I will not use the Bank's services offered to me by Distributor Partner for any purpose which is illegal in the eyes of law. </p>
                                        </div>
                                        <div class="modal-footer">
                                          <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                        </div>
                                      </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                        <div class="form-group center ">
                            <button type="submit" class="btn btn-primary" onclick="patternRule()">SignUp</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>    
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.apps', \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>