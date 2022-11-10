@extends('layouts.app')
@section('content')
<style>
.pg-opt{
    background:white !important;
}  

</style>
<script>
   

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
        else if(matching_password.indexOf('Payjst') >=0)
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

$(document).ready(function(){
  $("#generate_mpin").click(function(){
    $(".showform").show();
   // $("#reset_mpin").hide();

  });
});

        
</script>
<div class="row">
	                 
	<div id="frmTasks" name="frmTasks" class="form-inline">
								 
									<div class="card-body">
									<div class="form-inline col-md-12">
                                    <div class="form-group col-md-6">
                                       <label for="inputTask" class="control-label col-md-5">Full Name<span style="color:red"> *</span></label>
                                        <div class="col-sm-7 ">
                                            <input type="text" class="form-control has-error" id="name" name="name"
                                                   placeholder="Full Name" value="{{Auth::user()->name}}" readonly>
                                        </div>
                                    </div>
									<div class="form-group col-md-6">
                                        <label for="inputEmail3" class="control-label col-md-5">Email</label>
                                        <div class="col-sm-7">
                                            <input type="text" class="form-control" id="email" name="email"
                                                   placeholder="Email Id" value="{{Auth::user()->email}}" readonly>
                                        </div>
                                    </div></div>
									<div class="form-inline col-md-12">
	                                    <div class="form-group col-md-6">
	                                        <label for="inputEmail3" class="control-label col-md-5">Mobile Number<span style="color:red"> *</span></label>
	                                        <div class="col-sm-7">
	                                            <input type="text" class="form-control" id="mobile" name="mobile" placeholder="Mobile number" value="{{Auth::user()->mobile}}" maxlength="10" readonly>
	                                        </div>
	                                    </div>
										<div class="form-group col-md-6">
                                        <label for="inputEmail3" class="control-label col-md-5"> Shop Name <span style="color:red"> * </span> </label>
                                        <div class="col-sm-7">
                                            {{ Form::text('company', Auth::user()->member->company,['class' => 'form-control form-input-field','id'=>'company','placeholder'=>'Enter Your Shop Name','readonly'=>'readonly']) }}
                                        </div>
										</div>
                                    </div>
                                   
                                    
									<div class="form-inline col-md-12">
                                    <div class="form-group col-md-6">
                                        <label for="inputEmail3" class="control-label col-md-5">Address Detail <span style="color:red"> * </span></label>
                                        <div class="col-sm-7">
                                            <textarea id="address" class="form-control" name="address" readonly>{{Auth::user()->member->address}}</textarea>
                                        </div>
                                    </div>
									
                                    <div class="form-group col-md-6">
                                        <label for="inputEmail3" class="control-label col-md-5">Shop Address <span style="color:red"> * </span></label>
                                        <div class="col-sm-6">
                                            <textarea id="office_address" class="form-control"name="office_address" readonly>{{Auth::user()->member->office_address}}</textarea>
                                        </div>
                                    </div>
									</div>
									
									<div class="form-inline col-md-12">
										<div class="form-group col-md-6">
                                            <label for="inputEmail3" class="control-label col-md-5">State<span style="color:red"> *</span></label>
                                            <div class="col-sm-3">
                                                {{ Form::select('state_id', $state_list, Auth::user()->member->state_id, array('class' => 'form-control','id' => 'state_id','style'=>'max-width: 255%;','readonly'=>'readonly')) }}
                                            </div>
                                        </div>
										<div class="form-group col-md-6">
                                            <label for="inputEmail3" class="control-label col-md-5">Region<span style="color:red"> *</span></label>
                                            <div class="col-sm-6">
                                                 {{ Form::select('region', ['1' => 'North', '2' => 'South','3'=>'East','4'=>'West'], Auth::user()->profile->region, ['class'=>'form-control','id'=>'region','readonly'=>'readonly']) }}
                                            </div>
                                        </div>
                                    </div>
										<div class="form-inline col-md-12">
										<div class="form-group col-md-6">
										<label for="inputEmail3" class="control-label col-md-5">Pin Code <span style="color:red"> * </span></label>
										<div class="col-sm-6">
											<input type="text" class="form-control" id="pin_code" name="pin_code"
                                           placeholder="Pin Code" value="{{Auth::user()->member->pin_code}}" maxlength="6" readonly>
										</div>
									</div>
									<div class="form-group col-md-6">
                                        <label for="inputEmail3" class="control-label col-md-5">Pan Card<span style="color:red"> * </span></label>
                                        
                                        <div class="col-sm-4">
                                            {{ Form::text('pan_number', Auth::user()->member->pan_number,['class' => 'form-control form-input-field','id'=>'pan_number','placeholder'=>'Enter Pan Card Number','maxlength'=>10,'readonly'=>'readonly']) }}
                                        
                                        </div></div>
										
									</div>
									<div class="form-inline col-md-12">
										<div class="form-group col-md-6">
											<label for="inputEmail3" class="control-label col-md-5">Aadhaar Card<span style="color:red"> </span></label>&nbsp;&nbsp;
											<div class="col-sm-6">
												{{ Form::text('adhar_number', Auth::user()->member->adhar_number,['class' => 'form-control form-input-field','id'=>'adhar_number','placeholder'=>'Enter Aadhaar Card Number','maxlength'=>12,'readonly'=>'readonly']) }}
											</div>
										</div>
									</div>
								</div>
</div>
    <meta name="_token" content="{!! csrf_token() !!}"/>
@endsection