@extends('layouts.app')


@section('content')
<script>
$(document).ready(function(){
    $('[data-toggle="popover"]').popover();   
});
</script>
<script>
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
                <div class="panel-heading">Update Password</div>
				 
					@if (session('fails'))
                        <div class="alert alert-danger">
                           {{ session('fails') }}
                        </div>
                    @elseif(session('message'))
                        <div class="alert alert-info">{{ Session::get('message') }}</div>
                    @endif
                        
                   
                <div class="panel-body">
                    <form class="form-horizontal" role="form" method="post" action="{{ url('/password/password-reset') }}" onSubmit="return chekPswdValidation()">
                        {{ csrf_field() }}

                        <input type="hidden" name="token" value="{{ $token }}">

                        <div class="form-group{{ $errors->has('mobile') ? ' has-error' : '' }}" style="display:none"> 
                            <label for="mobile" class="col-md-4 control-label">Mobile</label>

                            <div class="col-md-6">
                                <input id="mobile" type="text" class="form-control" name="mobile" value="{{old('mobile') }}" disabled>

                                @if ($errors->has('mobile'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('mobile') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

						 <div class="form-group{{ $errors->has('otp') ? ' has-error' : '' }}">
                            <label for="otp" class="col-md-4 control-label">OTP</label>

                            <div class="col-md-6">
                                <input id="otp" type="password" class="form-control" name="otp">

                                @if ($errors->has('otp'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('otp') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
                        <div class="form-group{{ $errors->has('password') ? ' has-error' : '' }}">
                            <label for="password" class="col-md-4 control-label">Password</label>

                            <div class="col-md-6">
                                <input id="password" type="password" class="form-control" name="password">
                                @if ($errors->has('password'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('password') }}</strong>
                                    </span>
                                @endif
                            </div>
							<div class="col-md-2">
							
</div>
                        </div>
						

                        <div class="form-group{{ $errors->has('password_confirmation') ? ' has-error' : '' }}">
                            <label for="password-confirm" class="col-md-4 control-label">Confirm Password</label>
                            <div class="col-md-6">
                                <input id="password-confirm" type="password" class="form-control" name="password_confirmation">

                                @if ($errors->has('password_confirmation'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('password_confirmation') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
						<div class="form-group" style="text-align:center">
                           <a href="#" data-placement="bottom" data-toggle="popover" title="Password Policy" data-content="One Caps letter, One Number, One small letter, One special characters should be in #@$&* and password length should be 8 characters long."><button type="button" class="btn btn-default btn-sm">
          <span class="glyphicon glyphicon-menu-hamburger"></span> PSWD Rules
        </button></a>
                        </div>

                        <div class="form-group">
                            <div class="col-md-6 col-md-offset-4">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fa fa-btn fa-refresh"></i> Reset Password
                                </button>
								<a href="{{url('/')}}"
								 <button type="button" class="btn btn-primary">
                                    <i class="glyphicon glyphicon-chevron-left	
"></i> Go Back
                                </button></a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
