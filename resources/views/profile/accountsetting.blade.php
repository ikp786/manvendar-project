@extends('admin.layouts.templatetable')
@section('content')
<script type="text/javascript">
function passwordRule() 
	{
		var pswd_patter =/^(?=.*[A-Z])(?=.*[a-z])(?=.*\d)(?=.*?[#@$%^&*-]).{8,}$/
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
		else if(matching_password.indexOf('SHIGHRA') >=0)
		{
			alert('Please Do not use Company name as Password ');
			$('#password').focus();
			return false;
		}else if(matching_password.indexOf('SP') ==0)
		{
			alert('Please do not use SP or sp word');
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
  $("#tab2").click(function(){
    $(".showform").show();
    $(".otpout").hide();
  });

  $("#tab1").click(function(){
    $(".otpout").show();
    $(".showform").hide();
  });

});
</script>
<div class="tabs-framed">				 
    <ul class="nav nav-pills" style="padding: 2px;">
      <!--   <li style="background-color:gainsboro" id="tab2"><a href="#tab-2"  data-toggle="tab" class="btn btn-primary">Change Password</a></li> -->
		<!-- <li class="active" style="background-color:gainsboro" id="tab1"><a href="#tab-1" data-toggle="tab" class="btn btn-primary">OPT OUT OTP</a></li> -->
    </ul>
    <br><br>
     @include('partials.message_error')
	<form  method="POST" action="{{ url('change_password')}}" onSubmit="return passwordRule()">
		<div class="showform">
		<div class="col-md-12 ">
			<div class="col-md-6">
		    	{!! csrf_field() !!}
		    	<div class="form-group row">
		            <label class="col-sm-4 col-form-label text-right">Old Password</label>
		            <div class="col-md-5">
		            	<input type="password" name="old_password" class="form-control" id="exampleInputEmail1" placeholder="Old Password">
		       		</div>
		    	</div>
			</div>
		</div>
		<div class="col-md-12">
			<div class="col-md-6">											
			    <div class="form-group row">
			        <label class="col-sm-4 col-form-label text-right">New Password </label>
			        <div class="col-md-5">
			        <input type="password" class="form-control" name="password" id="password" placeholder="New Password"></div>
				</div>
			</div>
			<div class="col-md-3" style="color:black;">
				<h5>Password Policy : One Caps letter, One Number, One small letter, One special characters should be in #@$%^&*- and password length should be 8 characters long. Password should not contain words Payjst.
				</h5>
			</div>
		</div>
		<div class="col-md-12">
			<div class="col-md-6">
			    <div class="form-group row">
			        <label class="col-sm-4 col-form-label text-right">Confirm Password</label>
			        <div class="col-md-5">
			        	<input type="password" class="form-control" id="confrim_password" placeholder="Confirm Password">
			        </div>
			    </div>
			</div> 
		</div>
		<div class="col-md-6" style="text-align: center;"><button type="submit" class="btn btn-info">Change Password</button>
		</div>  </div>
		<!-- <div class="otpout" style="display: none;float:middle">
			 <div class="form-group col-md-4">
                 <label class="radio-inline">
                 <input type="checkbox" name="opt">
                I understand the risk on disabling second factor authentication. I take the responsibility of any unauthorized access to my account.
                </label>
            </div>
		</div>	 -->
	</form>
</div></div>


@endsection


