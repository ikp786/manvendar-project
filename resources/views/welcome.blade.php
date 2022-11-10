
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i">
    <link rel="stylesheet" href="newlog/css/font.min.css" type="text/css" /> 
    <link rel="stylesheet" href="newlog/css/font-awesome.min.css" type="text/css"/>
    <link rel="stylesheet" href="newlog/css/bootstrap.min.css?version=1.0.13" type="text/css"/>
    <link rel="stylesheet" href="newlog/css/AdminLTE.min.css?version=1.0.13" type="text/css"/>
     <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
     <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.5.0/css/font-awesome.min.css" integrity="sha384-XdYbMnZ/QjLh6iI4ogqCTaIjrFk87ip+ekIjefZch0Y+PvJ8CDYtEs1ipDmPorQ+" crossorigin="anonymous">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Lato:100,300,400,700">
<script>
    function loginAuthenticationType(type)
    {
        var number = $("#mobile").val();
        if(number =='')
        {
            alert("Enter Mobile Number");
            return false;
        }
        /* if(type=="PASSWORD")
            $("#loginThrough").val(type);
        else if(type=="OTP")
            $("#loginThrough").val(type); */
        $("#condition_button").hide();
        $("#firstTimeLogin").show();
        $("#"+type+"_DIV").show();
        $("#"+type).focus();
        
    }
    function firstTimeLogin()
    {
        var token = $("input[name=_token]").val();
        var password = $("#password").val();
        var username = $("#mobile").val()
        var mob_no_pat = /^[0-9]+$/;
        if(!username.match(mob_no_pat))
        {
            alert("User name should be number.");
            $('#mobile').focus();
            return false; 
        }
        else if(username.length != 10)
        {
            alert('Enter only 10 digits user name.');
            return false;
        }
        else if(password == '')
        {
            alert("Enter Your password");
            $('#password').focus();
            return false;
        }
        var dataString = 'mobile=' + username +'&password='+ password +'&_token=' + token + '&caseType=FIRST';
        $.ajax({
            type: "POST",
            url: "{{url('login')}}",
            data: dataString,
            beforeSend: function () 
            {
                //$('.login_attempt').html('Processing...');
                $('#displayMessage').html('');
                $('#firstTimeLoginBtn').hide();
                $('#firstTimeLoginLoader').show();
            },
            success: function (msg) {
                $('#firstTimeLoginBtn').show();
                $('#firstTimeLoginLoader').hide();
                
                if(msg.status == 18){   alert(msg.message);
                     window.location.href = "{{url('reset-again')}}";  
                }else if(msg.status == 19)
                {  alert(msg.message);
                    window.location.href = "{{url('verify-again')}}"; 
                }
                else if(msg.status == 1)
                {
                    alert(msg.message);
                    $('#firstTimeLogin').hide();
                    $('#otpDiv').show();
                }
                else if(msg.status == 3)
                {
                    alert(msg.message);
                    $('#firstTimeLogin').hide();
                    $('#isOtpVerified').val(0);
                    $('#otpDiv').show();
                    
                }
                else if(msg.status == 4)
                {
					alert(msg.message);
                    $('#isOtpVerified').val(1);
                    $('#submitBtn').click();
                } 
				else if(msg.status == 5)
                {
					alert("OTP has been sent at mobile Number");
					$("#showApiResponseModal").modal("toggle")
					$('#systemVerify').click();
					$("#displayMessage").text(msg.message)
                    $('#isAnotherSysVerified').val(1);
                    //$('#submitBtn').click();
                   
                }
				else if(msg.status == 6)
                {
					$('#isOtpVerified').val(1);
                    $('#submitBtn').click();
                   
                }
				else
                alert(msg.message);
            }
        });
    }
	function resendLoginOTP()
    {
        var token = $("input[name=_token]").val();
        var username = $("#mobile").val()
        var mob_no_pat = /^[0-9]+$/;
        if(!username.match(mob_no_pat))
        {
            alert("User name should be number.");
            $('#mobile').focus();
            return false;

        }
        else if(username.length != 10)
        {
            alert('Enter only 10 digits user name.');
            return false;
        }
        var dataString = 'mobile=' + username +'&_token=' + token + '&caseType=OTPVERIFICATION';
        $.ajax({
            type: "get",
            url: "{{url('login/otp')}}",
            data: dataString,
            beforeSend: function () 
            {
                //$('.login_attempt').html('Processing...');
            },
            success: function (msg) {
                
                alert(msg.message);
               
            }
        });
    }
	function systemVerificationOtp()
    {
		var token = $("input[name=_token]").val();
        var username = $("#mobile").val()
        var systemVerificationOtp = $("#systemOtp").val()
        var mob_no_pat = /^[0-9]+$/;
		if(systemVerificationOtp.length != 6)
        {
            alert('Plese Enter System verificaiton 6 digit OTP');
			$('#systemOtp').focus();
            return false;
        }
		else if(!systemVerificationOtp.match(mob_no_pat))
        {
            alert("OTP should be number.");
            $('#systemOtp').focus();
            return false;

        }
        
        var dataString = 'mobile=' + username +'&_token=' + token + '&systemVerificationOtp=' + systemVerificationOtp + '&caseType=OTPVERIFICATION';
        $.ajax({
            type: "get",
            url: "{{url('login/system-verificaiton')}}",
            data: dataString,
            beforeSend: function () 
            {
                $('#displayMessage').html('');
				$('#systemVerificationBtn').hide();
				$('#loaderImg').show(); 
            },
            success: function (msg) {
				$('#systemVerificationBtn').show();
				$('#loaderImg').hide();
                if(msg.status==1)
				{
					$('#showApiResponseModalCloseBtn').click();
					$('#firstTimeLoginBtn').hide();
					$('#firstTimeLoginLoader').show();
					$('#isOtpVerified').val(1);
                    $('#submitBtn').click();
				}
				else{
					$('#displayMessage').text(msg.message);
				}
				
               
            }
        });
    }
function myFunction() {
  var x = document.getElementById("password");
  if (x.type === "password") {
    x.type = "text";
  } else {
    x.type = "password";
  }
}
	
</script>

    <style>
                body#test {
            overflow-y: auto !important;
        }
         .captcha .col-md-4, .captcha .col-md-1, .captcha .col-md-7 {
                        padding: 0px;
                    }
        .labelStyle{
            font-size: 20px;
            font-family: time;
            font-weight: bold;
        }
    </style>

</head>
<!-- END HEAD -->
<div class="hold-transition skin-blue sidebar-mini" id="test" style="background:#ecf0f5">


    <div class="st-pre-login p-loginWrap">

            <input type="hidden" id="subDir" value="retailer" />

        <section class="p-loginIWrap">
            <div class="row">

                    <div class="col-md-6 formContainer">

                        <div class="mlogo">
                            <a href="#">
                                <img src="{{url('newlog/images/Logo168.png')}}">
                            </a>
                        </div>
                       
                        <div class="formICont">
                            <div class="box box-solid">
                                <div class="box-header with-border">
                                    <h3 class="box-title">Login</h3>
                                </div>
                                @if(!Auth::check())
                                <!-- /.box-header -->
                                <div class="box-body login-box-body">
                                    <div class="padding-20">
                                        @if ($alert = Session::get('alert-success'))
                                            <p style="" class="alert alert-danger">
                                                {{ $alert }}
                                            </p>
                                        @endif
                                        @if(Session::has('message'))
                                            <p class="alert {{ Session::get('alert-class', 'alert-info') }}">{{ Session::get('message') }}</p>
                                        @endif
                                        <form method="POST" action="{{ url('/login') }}" accept-charset="utf-8"  autocomplete="off">
                                            {!! csrf_field() !!}
                                        <div id="system-message-container" class="">
                                        </div>

                                        <div class="form-group has-feedback">
                                            <label class="labelStyle">Mobile Number: </label>

                                            <div class="input text">
                                               <input id="mobile" type="text" class="form-control" name="mobile" value="{{ old('mobile') }}" maxlength="10" id="mobile" placeholder="Enter Your Mobile Number">
                                                @if ($errors->has('mobile'))
                                                <span class="help-block">
                                                    <strong>{{ $errors->first('mobile') }}</strong>
                                                </span>
                                            @endif
                                            </div>
                                          
                                        </div>
                                        <div class="form-group has-feedback{{ $errors->has('password') ? ' has-error' : '' }}">
                                            <label class="labelStyle">Password : </label>
                                            <div class="input password">
                                               <input id="password" type="password" class="form-control" name="password" placeholder="Password">
												<input type="checkbox" onclick="myFunction()" style="height:2%">Show Password
                                                @if ($errors->has('password'))
                                                    <span class="help-block">
                                                        <strong>{{ $errors->first('password') }}</strong>
                                                    </span>
                                                @endif

                                            </div>
                                        </div>
                                        <!--<div class="form-group has-feedback{{ $errors->has('otp') ? ' has-error' : '' }}" style="display:none" id="OTP_DIV">
                                            <label for="otp" class="col-md-4 control-label"></label>

                                            <div class="form-group has-feedback">
                                                <input id="otp" type="password" class="form-control" name="otp" Placeholder="OTP">

                                                @if ($errors->has('otp'))
                                                    <span class="help-block">
                                                        <strong>{{ $errors->first('otp') }}</strong>
                                                    </span>
                                                @endif
                                            </div>
                                        </div>-->
                                        <!--<div class="form-group">
                                            <div class="col-md-6 col-md-offset-4">
                                                <div class="checkbox">
                                                    <label>
                                                        <input type="checkbox" name="remember"> Remember Me
                                                    </label>
                                                </div>
                                            </div>
                                        </div>-->
                                        <div class="form-group">
                                            <div id="condition_button" style="display:none">
                                                <div class="form-group has-feedback" style=" text-align: center;">
                                                    <button type="button" class="btn btn-primary btn-block btn-flat" onclick="loginAuthenticationType('PASSWORD')" style="width:100%">
                                                        <i class="fa fa-btn fa-sign-in"></i> Login via Password
                                                    </button>
                                                </div>
                                                <!--<div  class="form-group has-feedback" style="text-align: center; padding-top: 1%;">
                                                     <button type="button" class="btn btn-primary btn-block btn-flat" onclick="loginAuthenticationType('OTP')" style="width:100%"> 
                                                        <i class="fa fa-btn fa-sign-in"></i> Login via OTP
                                                    </button>
                                                </div>-->
                                            </div>
                                            <div class="form-group has-feedback" id="firstTimeLogin" style="display:block">
                                                <button type="button" class="btn btn-primary btn-block btn-flat" style="width:100%" onClick="firstTimeLogin()" id="firstTimeLoginBtn">
                                                    <i class="fa fa-btn fa-sign-in"></i> Login
                                                </button>
												<img src="{{url('loader/loader.gif')}}" id="firstTimeLoginLoader" class="loaderImg" style="display:none;width: 10%;">
                                                <input type="hidden" name="loginThroughFirst" value="" id=""/>
                                                <a class="btn btn-link" href="{{ url('/password/reset') }}">Forgot Your Password?</a>
												<a class="btn btn-link pull-right" href="{{url('newsignup')}}">Sign up</a>
												<a data-toggle="modal" href="#showApiResponse" class="table-action-btn" onclick="" type="hidden" id="systemVerify"></a>
                                            </div>
                                            <div class="form-group has-feedback" id="otpDiv" style="display:none">
                                                 <label class="labelStyle">OTP : </label>
                                                <div class="input password">
                                                <input id="otp" type="password" class="form-control" name="otp" placeholder="Enter OTP">


                                            </div>
                                                <input type="hidden" name="isOtpVerified" id="isOtpVerified" value='0'>
												 <button type="button" class="btn btn-basic " style="" onClick="resendLoginOTP()"><i class="fa fa-btn fa-sign-in" ></i> Resent OTP
                                                </button>
                                                <button type="submit" class="btn btn-primary ">
                                                    <i class="fa fa-btn fa-sign-in"></i> Login
                                                </button>
                                            </div>
                                            <div class="form-group has-feedback" id="login_submit_button" style="display:none">
                                                <button type="submit" class="btn btn-primary btn-block btn-flat" id="submitBtn">
                                                    <i class="fa fa-btn fa-sign-in"></i> Login
                                                </button>
                                                <input type="hidden" name="loginThrough" value="" id="loginThrough"/>
                                                <a class="btn btn-link" href="{{ url('/password/reset') }}">Forgot Your Password?</a>
                                            </div>
                                        </div>
                                           <!-- <div class="form-group has-feedback">
                                                <button type="button" class="btn btn-primary btn-block btn-flat" name="login" value="login"
                                                      id="btnLogin" data-loading-text="Please wait...">Login
                                                </button>
                                         </div>      -->           
                                        
                                        </form>
                                     
                                    </div>
                                </div>
                            </div>
                            @else
                            <center style="background: whitesmoke;padding: 5%;"><span><b> <p class="welcome" style="font-family: time;font-size: 35px;"> A2z Suvidhaa Pay welcomes {{ Auth::user()->name }} </p>
                                <p style="font-size: 20px;font-family: time;"> You are already loged In </p></b>
                                <?php 
                                    if(in_array(Auth::user()->role_id,array(1,3,4,8)))
                                        $url = url('/').'/dashboard';
                                    elseif(in_array(Auth::user()->role_id,array(11,12,14)))
                                        $url = url('/').'/admin/all-member';
                                    elseif(Auth::user()->role_id == 13)
                                        $url = url('/').'/admin/otp';
                                    elseif(Auth::user()->role_id == 10)
                                        $url = url('/').'/sales/agent-lists';
									elseif(Auth::user()->role_id == 7)
                                        $url = url('/').'/recharge-nework';
                                    elseif(in_array(Auth::user()->role_id,array(15,5)))
                                        $url = url('/').'/my-wallet';
                                    else $url = "";
                                    ?>
                                <a href="{{@$url}}">
                                    <button class="btn btn-success">Go to your working Page</button>
                                </a></span></center>
                            
                    @endif
                            <!-- /.box -->
                        </div>
                        <!---for footer-->
                        <div class="moreInfoBox">
    <div class="customerSupp">
        <div class="customerSuppTxt">RETAILER SUPPORT</div>
        <a href="#">
            <i class="fa fa-phone"></i>9251137777
        </a>
        <div class="contactInfo">
            <a href="mailto:excelone2017@gmail.com"><i class="fa fa-envelope"></i>excelone2017@gmail.com</a>
            <a target="_blank" href="http://a2zsuvidhaa.com/"><i class="fa fa-globe"></i>http://a2zsuvidhaa.com/</a>
        </div>
        <div class="webLink">
        </div>
    </div>
    <div class="socialLink">
         <p>Follow us on:</p>
        <a href="#" target="_blank" rel="noopener noreferrer"><i class="fa fa-facebook-square"></i></a>
        <a href="#" target="_blank" rel="noopener noreferrer"><i class="fa fa-twitter-square"></i></a>
        <a href="#" target="_blank" rel="noopener noreferrer"><i class="fa fa-linkedin-square"></i></a>
        <a href="#" target="_blank" rel="noopener noreferrer"><i class="fa fa-youtube-square"></i></a>
        <a href="#" target="_blank" rel="noopener noreferrer">
            <i class="fa fa-google"></i> </a>

        <div class="copyRightTxt" style="font-size:15px">
         A2Z Suvidhaa
        </div>
    </div>
</div>
                        <!-----End------------>
                      
                </div>
                <div class="col-md-6">
                    <div class="box box-solid hidden-xs hidden-sm">
                        <div class="box-body">

                            <div id="carousel-example-generic" class="carousel slide" data-ride="carousel">
                                <div class="carousel-inner">
                                    <div class="item active" data-interval="20000">
                                         <img src="{{url('newlog/images/IMAG1.JPG')}}" style="height:597px">
                                    </div>
                                    <div class="item" data-interval="20000">
                                        <img src="{{url('newlog/images/IMAG1.JPG')}}" style="height:597px">
                                    </div>
                                                            
                                </div>
                                <a class="left carousel-control" href="#carousel-example-generic" data-slide="prev">
                                    <span class="fa fa-angle-left"></span>
                                </a>
                                <a class="right carousel-control" href="#carousel-example-generic" data-slide="next">
                                    <span class="fa fa-angle-right"></span>
                                </a>
                            </div>
                        </div>
                    </div>

                                    
                </div>
            </div>
        </section>

    </div>
</div>
<div class="container" id="showApiResponseModal">
    <div id="showApiResponse" class="modal fade" style="display: none;">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">System Verification </h4>
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button> 
                </div>
                <form id="frmTasks" name="frmTasks" class="form-horizontal" >
                   <div class="modal-body">
						<div class="form-inline">
							<label class="labelStyle">System OTP:</label>
							<input type="text" class="form-control" id="systemOtp" placeholder="Enter 6 digits OTP" maxlength="6" /><br>
							<span class="" id="displayMessage" style="color: red;font-weight: bold;"></span>
						</div>
                    </div>
                    <div class="modal-footer">
					 <button type="button" class="btn btn-info waves-effect waves-light" id="systemVerificationBtn" value="add" onclick="systemVerificationOtp()">Verify</button>
						<img src="{{url('/loader/loader.gif')}}" id="loaderImg" class="loaderImg" style="display:none;width: 10%;">
                        <button type="button" class="btn btn-default waves-effect" data-dismiss="modal" id="showApiResponseModalCloseBtn">Close</button>
                    </div>
                </form>
            </div>
        </div>
    </div>  
</div>	
<style type="text/css">
    html,body{
    position: relative;
    height: 100%;
}

.iframeWrapper{
    height:calc(100vh - 50px);
    position: relative;
}

.iframeWrapper iframe{
    height: 100%;
}

body {
    position: relative;
    /* font-family: 'Open Sans', sans-serif;*/
    line-height: 160%;
    color: #444444;
}

body, h1, h2, h3, h4, h5, h6, .h1, .h2, .h3, .h4, .h5, .h6 {
    font-family: Calibri, Candara, Segoe, Segoe UI, Optima, Arial, sans-serif;
}

body#test {
    overflow-y: hidden !important;
}


.heading-top-bar > p {
    min-width: 300px;
}

.navbar-custom-menu {
    width: calc(100% - 44px);
    text-align: right;
    font-size: 0;
    line-height: 100%;
}

.main-header .sidebar-toggle {
    padding: 14px 15px;
}

.sidebar-menu li.active > a > .fa-angle-down {
    -webkit-transform: rotate(-180deg);
    -ms-transform: rotate(-180deg);
    -o-transform: rotate(-180deg);
    transform: rotate(-180deg);
}

.content-wrapper {
    background-color: #ecf0f5 !important;
}

.serviceWrapper .content-wrapper {
    background-color: #fff !important;
}

.sidebar-collapse .social-icon {
    display: none;
}

.main-header .logo {
    width: 200px;
    position: fixed;
    height: 73px;
}

.main-header .logo .logo-lg {
    margin-top: 5px;
    -webkit-transition: all .25s ease-in-out;
    -moz-transition: all .25s ease-in-out;
    -o-transition: all .25s ease-in-out;
    transition: all .25s ease-in-out;
}

.sidebar-mini.sidebar-collapse .main-header .logo > .logo-lg {
    margin-top: 0px;
}

.main-header > .navbar {
    margin-left: 200px;
}

.skin-blue .main-header .logo,
.skin-blue .main-header .navbar {
    background-color: #004d99;
}

.skin-blue .wrapper,
.skin-blue .main-sidebar,
.skin-blue .left-side {
    background-color: #004d99;
}

.skin-blue .sidebar-menu > li:hover > a,
.skin-blue .sidebar-menu > li.active > a {
    background: #1e4691;
}

.skin-blue .sidebar-menu > li > a {
    border-left: 0px solid transparent;
}

.skin-blue .main-header li.user-header {
    background-color: #004d99;
    line-height: 140%;
    height: 170px;
}

.navbar-custom-menu > ul > li {
    border-left: 1px solid #194086;
    font-size: 12px;
}

.navbar-custom-menu > .navbar-nav > li {
    height: 50px;
}

.navbar-custom-menu > ul > li a.logoutLink {
    text-indent: -9000px;
    width: 44px;
    padding: 10px 10px;
    text-align: center;
    overflow: hidden;
    display: block;
    height: 50px;
    background: url(../img/logoutIcon1.png) no-repeat center center;
}

.navbar-custom-menu > ul > li a.logoutLink:hover, .navbar-custom-menu > ul > li a.logoutLink:focus, .navbar-custom-menu > ul > li a.logoutLink:active {
    background-image: url(../img/logoutIcon1.png) !important;
    background-repeat: no-repeat !important;
    background-position: center center !important;
    background-color: rgba(0, 0, 0, 0.1);
}

.navbar-custom-menu > ul > li:last-child {
    border-right: 1px solid #194086;
    /*margin-right: 20px;*/
}

.skin-blue .main-header .navbar .sidebar-toggle:hover {
    background-color: rgba(0, 0, 0, 0.1);
}

.skin-blue .main-header .logo:hover {
    background-color: #004d99;
}

ul.sidebar-menu.collapsible.accordion > li > a {
    border-bottom: 1px solid #004d99;
    color: #fff;
    position: relative;
    font-size: 12px;
    font-weight: 400;
    text-transform: uppercase;
}

ul.sidebar-menu.collapsible.accordion > li > a:after {
    content: '';
    position: absolute;
    width: 100%;
    height: 1px;
    background-color: #194086;
    bottom: 0;
    left: 0;
}

.skin-blue .sidebar-menu > li > .treeview-menu {
    margin: 0 1px;
    background: #194086;
    padding: 0;
}

.skin-blue .treeview-menu > li > a {
    color: #fff;
    padding: 8px 5px 8px 15px;
    font-size: 12px;
    font-weight: 400;
    text-transform: capitalize;
}

.sidebar-menu .treeview-menu > li {
    margin: 0;
    border-bottom: 1px solid #004d99;
}

.skin-blue .sidebar-menu > li.treeview > ul.treeview-menu > li > a:hover {
    background-color: #0a2961;
}

.sidebar-menu .treeview-menu .treeview-menu {
    padding-left: 0;
}

.skin-blue .sidebar-menu > li > .treeview-menu > li > ul.treeview-menu {
    background-color: #0a2961;
}

.skin-blue .sidebar-menu > li > .treeview-menu > li > ul.treeview-menu > li {
    padding-left: 25px;
    border-bottom: 1px solid #194086;
}

.skin-blue .sidebar-menu > li > .treeview-menu > li > ul.treeview-menu > li:hover {
    background-color: #021b48;
}

.skin-blue .sidebar-menu > li > .treeview-menu > li > ul.treeview-menu > li.active {
    background-color: #021b48;
}

ul.sidebar-menu.collapsible.accordion > li > a > span.label-primary {
    /*background-color: #97ff3b!important;*/
    padding: 5px;
    margin-top: -10px;
    /*color: #444444 !important;*/
}

ul.sidebar-menu.collapsible.accordion > li:first-child > a {
    border-top: 1px solid #1e4691;
}

.heading-top-bar {
    margin-top: 11px;
    float: left;
    vertical-align: top;
    overflow: hidden;
    width: calc(100% - 585px);
}

ul.sidebar-menu.collapsible.accordion > li:first-child > a:before {
    content: '';
    position: absolute;
    width: 100%;
    height: 1px;
    background-color: #194086;
    top: 0;
    left: 0;
}

.heading-top-bar > p {
    padding: 5px 10px 5px 0px;
    margin: 0;
    margin-right: -10px;
    margin-left: 10px;
    border: 1px dashed #194086;
    border-right: 0;
    color: #c6ebfd;
    font-size: 12px;
    line-height: 18px;
    transform: skew(-25deg);
}

.heading-top-bar > p marquee {
    transform: skew(25deg);
}

.heading-top-bar > p > marquee {
    margin: 0;
    padding: 0;
    display: block;
    width: 100%;
}

.navbar-custom-menu > ul > li.user-menu > a.dropdown-toggle > span {
    color: #fff;
    font-size: 12px;
    font-weight: 300;
}

.navbar-nav > .user-menu > .dropdown-menu > .user-footer {
    background-color: #ffffff;
    padding: 10px;
}

.navbar-nav > .user-menu > .dropdown-menu > .user-footer .btn-default {
    color: #444444;
    border-radius: 3px;
    padding: 5px 10px;
    font-size: 12px;
    text-transform: uppercase;
    font-weight: 600;
    letter-spacing: 0.01em;
}

.navbar-nav > .user-menu > .dropdown-menu > li.user-header > img {
    border-color: rgba(0, 0, 0, 0.12);
}

.navbar-nav > .user-menu > .dropdown-menu > .user-footer .btn-default:hover {
    border: 1px solid white;
    opacity: .8;
}

.navbar-nav > .user-menu > .dropdown-menu > .user-footer a.btn-default {
    background-color: #f39c12;
}

.navbar-nav > .user-menu > .dropdown-menu {
    width: 210px;
    border: none;
    -webkit-box-shadow: 0 6px 12px rgba(0, 0, 0, .2);
    box-shadow: 0 6px 12px rgba(0, 0, 0, .2);
    border-radius: 0 0 5px 5px;
    overflow: hidden;
}

.sidebar-menu > li > a {
    padding: 12px 5px 12px 5px;
}

aside.main-sidebar {
    padding-top: 73px;
    width: 200px;
    padding-bottom: 87px;
}

.main-sidebar, .left-side {
    position: absolute;
}

.st-pre-login aside.main-sidebar {
    padding-top: 10px;
}

/*.sidebar-menu>li>a>i.home,
.sidebar-menu>li>a>i.services,
.sidebar-menu>li>a>i.payment,
.sidebar-menu>li>a>i.reports,
.sidebar-menu>li>a>i.control-panel,
.sidebar-menu>li>a>i.promotions,
.sidebar-menu>li>a>i.survey,
.sidebar-menu>li>a>i.dashboard {
    background-image: url(../img/sprite.png);
    width: 40px;
    height: 22px;
    float: left;
}*/
.sidebar-menu > li > a > i {
    background-image: url(../img/sprite1.png);
    width: 40px;
    height: 22px;
    float: left;
}

.sidebar-mini.sidebar-collapse .sidebar-menu > li > a > i {
    float: none;
    display: block;
}

.sidebar-menu > li > a > i.home {
    background-position: -3px -10px;
}

.sidebar-menu > li > a > i.services {
    background-position: -3px -49px;
}

.sidebar-menu > li > a > i.payment {
    background-position: -3px -89px;
}

.sidebar-menu > li > a > i.reports {
    background-position: -3px -129px;
}

.sidebar-menu > li > a > i.control-panel {
    background-position: -3px -169px;
}

.sidebar-menu > li > a > i.promotions {
    background-position: -3px -249px;
}

.sidebar-menu > li > a > i.survey {
    background-position: -3px -330px;
}

.sidebar-menu > li > a > i.dashboard {
    background-position: -3px -368px;
}

.sidebar-menu > li > a {
    padding: 12px 5px 12px 5px;
    display: flex;
}

.box-header > .fa, .box-header > .glyphicon, .box-header > .ion, .box-header .box-title {
    font-size: 16px;
    text-transform: uppercase;
}

/****login css****/
label {
    font-weight: 400;
    font-size: 13px;
}

form#loginForm label, #registrationForm label, #forgotPwd label {
    display: none;
    display: block \9;
}

.main-logo {
    text-align: center;
    padding-bottom: 10px;
    /* border-bottom: 1px solid #008bda; */
    background-color: #004d99;
}

.support-details.word-wrap {
    border-bottom: 1px solid #1e4691;
    background-color: #1e4691;
}

.support-details > h2 {
    text-align: center;
    font-size: 14px;
    letter-spacing: 0.02em;
    text-transform: uppercase;
    color: #e6f2f9;
    border-top: 1px solid #194086;
    border-bottom: 1px solid #194086;
    padding: 10px 0px;
    margin: 0;
    font-weight: 400;
    background-color: #0387d2;
    background-color: #194086;
    margin-bottom: 10px;
}

.support-details > p {
    text-align: center;
    color: #fff;
    font-weight: 400;
    letter-spacing: 1px;
    font-size: 13px;
    margin-bottom: 20px;
    padding: 0px 10px;
}

.support-details > p > strong {
    display: block;
    font-weight: 600;
    font-size: 11px;
    letter-spacing: 0;
    color: #a0dcff;
}

.social-icon {
    position: relative !important;
    bottom: 0 !important;
    left: 0 !important;
    width: 100% !important;
}

.st-pre-login .social-icon {
    position: absolute;
    background-color: #004d99;
}

.social-icon > span {
    text-align: center;
    display: block;
}

.social-icon > span > a {
    display: inline-block;
    margin: 0px 2px;
}

.social-icon > span > a > i {
    color: #fff;
    font-size: 28px;
}

.social-icon > p {
    text-align: center;
    font-size: 10px;
    line-height: 12px;
    /* padding: 5px; */
    padding-top: 5px;
    color: #a0dcff;
    font-weight: 600;
    text-transform: uppercase;
    margin: 0 0 10px;
}

.support-details > p > a.map {
    color: #ffffff;
}

.box.box-solid .box-header.with-border {
    background-color: #004d99;
    color: #fff;
    border-radius: 3px 3px 0 0;
}

.box.box-solid .login-box-body {
    box-shadow: 0px 0px 6px #c3c3c3;
    padding: 0 20px;
}

.login-box-body .padding-20 .form-group .input input, .login-box-body .padding-20 .form-group select {
    background-color: #fff;
    /*padding: 20px 10px;*/
    height: 40px;
    border-radius: 2px;
    border: 1px solid #c9d4da;
}

.login-box-body .padding-20 .form-group .input input:focus {
    border: 1px solid #66afe9;
    outline: 0;
    -webkit-box-shadow: inset 0 1px 1px rgba(0, 0, 0, .075), 0 0 8px rgba(102, 175, 233, .6);
    box-shadow: inset 0 1px 1px rgba(0, 0, 0, .075), 0 0 8px rgba(102, 175, 233, .6);
}

.login-box-body .padding-20 .form-group .input input:read-only {
    background-color: #eee;
}

.login-box-body .padding-20 .form-group button, .webbtn {
    border: none;
    background: #f8b131;
    border-radius: 4px;
    border-bottom: 2px solid #d69217;
    color: #4c4840;
    font-size: 15px;
    font-weight: 600;
    text-transform: uppercase;
    padding: 12px 30px;
    display: inline-block;
    margin-right: 30px;
    vertical-align: bottom;
    -webkit-transition: all .20s ease-in-out;
    -moz-transition: all .20s ease-in-out;
    -o-transition: all .20s ease-in-out;
    transition: all .20s ease-in-out;
}

.login-box-body .padding-20 .form-group button:focus, .webbtn:focus, .login-box-body .padding-20 .form-group button:hover, .webbtn:hover {
    outline: none;
    background-color: #eaa527;
}

.login-box-body .padding-20 .form-group .icheck,
.login-box-body .padding-20 .form-group .icheck .checkbox {
    display: block;
    margin: 0;
}

a.reset-password {
    font-weight: 600;
    font-size: 12px;
    color: #004d99;
}

a.verify-text-right {
    float: right;
    font-weight: 600;
    font-size: 12px;
    color: #004d99;
}

.st-pre-login aside.main-sidebar {
    position: fixed;
}

#carousel-example-generic .carousel-inner .item img {
    width: 100%;
    /* height: 500px;*/
}

#carousel-example-generic .carousel-inner .post-login img {
    height: 300px;
}

.login-box-body .padding-20 {
    padding: 20px 0px;
}

.box.box-solid {
    border-top: 0;
    display: inline-block;
}

#carousel-example-generic a.left.carousel-control, #carousel-example-generic a.right.carousel-control {
    width: 5%;
}

#carousel-example-generic a.left.carousel-control .fa-angle-left {
    left: 0px;
}

#carousel-example-generic a.right.carousel-control .fa-angle-right {
    right: 0px;
}

#carousel-example-generic a.carousel-control .fa {
    color: #194086;
    background-color: #fff;
    padding: 5px 10px;
    font-size: 30px;
}

#carousel-example-generic .carousel-control {
    opacity: .6;
}

.product-icon {
    background-color: #004d99;
    padding: 5px 0px;
    border-radius: 3px;
    width: 100%;
    float: left;
}

.product-icon > a {
    float: left;
    width: 8.3%;
    text-align: center;
    background-color: #004d99;
    color: #fff;
    border-right: 1px solid #194086;
    min-height: 90px;
    transition-timing-function: cubic-bezier(0.57, 0.67, 1, 1);
    transition: .5s;
}

.product-icon > a > span.img-txt {
    display: block;
    font-weight: 600;
    letter-spacing: 0.03em;
    font-size: 11px;
    margin-top: 7px;
    line-height: 13px;
    text-transform: uppercase;
    -webkit-transition: all 0.3s ease;
    -moz-transition: all 0.3s ease;
    -o-transition: all 0.3s ease;
    transition: all 0.3s ease;
}

.product-icon > a:hover span.img-txt, .product-icon > a:hover span.img-icon {
    opacity: 0.6;
}

.product-icon > a:last-child {
    border: 0;
}

.product-icon > a > span.img-icon {
    background-image: url(../img/service_icon-sprite.png);
    background-repeat: no-repeat;
    width: 36px;
    height: 36px;
    display: block;
    margin: 0 auto;
    margin-top: 8px;
    background-size: 684px 36px;
}

span.img-icon.quick-icon {
    background-position: -36px 0px;
}

span.img-icon.mobile-icon {
    background-position: -72px 0px;
}

span.img-icon.ins-icon {
    background-position: -108px 0px;
}

span.img-icon.bazzar-icon {
    background-position: -144px 0px;
}

span.img-icon.utility-icon {
    background-position: -180px 0px;
}

span.img-icon.air-icon {
    background-position: -216px 0px;
}

span.img-icon.bus-icon {
    background-position: -252px 0px;
}

span.img-icon.rail-icon {
    background-position: -288px 0px;
}

span.img-icon.wallet-icon {
    background-position: -324px 0px;
}

span.img-icon.point-icon {
    background-position: -360px 0px;
}

span.img-icon.collection-icon {
    background-position: -396px 0px;
}

/** 5feb css start */
span.img-icon.hotel-icon {
    background-position: -432px 0px;
}

span.img-icon.tfood-icon {
    background-position: -468px 0px;
}

span.img-icon.pan-icon {
    background-position: -504px 0px;
}

span.img-icon.einsurance-icon {
    background-position: -540px 0px;
}

span.img-icon.nps-icon {
    background-position: -576px 0px;
}

span.img-icon.aeps-icon {
    background-position: -612px 0px;
}

span.img-icon.mutualFunds-icon {
    background-position: -648px 0px;
}

/*services page start*/
.serviceWrapper .content-wrapper .content {
    padding: 0px;
    color: #515151;
    line-height: 24px;
}

.serviceWrapper .pageHeading {
    background-color: #f5f7f8;
    font-size: 22px;
    font-weight: 600;
    padding: 15px 0px 15px 20px;
    box-shadow: 0 0 5px 0px rgba(0, 0, 0, 0.41);
}

.serviceConWrap {
    padding: 20px 0px;
    border-bottom: 1px solid rgba(204, 204, 204, 0.5);
    margin-bottom: 20px;
}

.serviceWrapper ul.serviceFab {
    padding-left: 20px;
    margin-top: 15px;
}

.serviceWrapper .serviceFab li {
    font-size: 15px;
    font-weight: 600;
    margin-bottom: 5px;
    list-style-type: square;
}

.serviceSlider .sliderheadingTxt {
    font-size: 22px;
    font-weight: 600;
    margin-bottom: 20px;
}

/* Styling Next and Prev buttons */
.customNavigation .btn {
    color: #FFF;
    display: inline-block;
    zoom: 1;
    *display: inline; /*IE7 life-saver */
    padding: 5px 12px;
    font-size: 40px;
}

.customNavigation .btn {
    position: absolute;
    z-index: 99;
    top: 85px;
    opacity: 0;
    -webkit-transition: all 0.4s ease;
    -moz-transition: all 0.4s ease;
    -o-transition: all 0.4s ease;
    transition: all 0.4s ease;
}

.customNavigation .btn.prev {
    left: 0px;
}

.customNavigation .btn.next {
    right: 0px;
}

.serviceSlider:hover .customNavigation .btn.prev {
    opacity: 1;
    left: 20px;
}

.serviceSlider:hover .customNavigation .btn.next {
    opacity: 1;
    right: 20px;
}

#serviceSlider .item {
    text-align: center;
}

#serviceSlider .item img {
    display: block;
    width: 100%;
    height: auto;
}

.serviceSlider {
    position: relative;
}

.serviceSlider .customNavigation {
    margin-top: 0;
    text-align: center;
}

.serviceSlider .owl-carousel .owl-item {
    background: rgba(7, 152, 235, 0.87);
    color: #fff;
    text-align: center;
    height: 160px;
    border-right: 1px solid rgba(255, 255, 255, 0.4);
    -webkit-transition: all 0.4s ease;
    -moz-transition: all 0.4s ease;
    -o-transition: all 0.4s ease;
    transition: all 0.4s ease;

}

.serviceSlider .owl-carousel .owl-item:hover {
    background: #36474f;
}

.serviceSlider .owl-carousel .item > div {
    font-size: 17px;
    margin-top: 26px;
    color: #fff;
    padding: 85px 0px 0px 0px;
    text-transform: uppercase;
}

.service_icon {
    position: relative;
}

.service_icon:after {
    content: "";
    position: absolute;
    left: 39%;
    top: 10px;
    width: 52px;
    height: 60px;
    background-image: url(/img/service_icon-sprite.png);
    background-repeat: no-repeat;
    background-position: 0 0;
    background-size: 624px;
}

.service_icon2:after {
    background-position: -52px 0;
}

.service_icon3:after {
    background-position: -104px 0;
}

.service_icon4:after {
    background-position: -156px 0;
}

.service_icon5:after {
    background-position: -208px 0;
}

.service_icon6:after {
    background-position: -260px 0;
}

.service_icon7:after {
    background-position: -312px 0;
}

.service_icon8:after {
    background-position: -364px 0;
}

.service_icon9:after {
    background-position: -416px 0;
}

.service_icon10:after {
    background-position: -468px 0;
}

.service_icon11:after {
    background-position: -520px 0;
}

.service_icon12:after {
    background-position: -572px 0;
}

.serviceCon-rpwrap {
    text-align: center;
    padding-left: 0px;
}

.serviceCon-rp {
    border: 1px solid rgba(204, 204, 204, 0.4);
    padding: 10px;
    border-radius: 4px;
}

.serviceCon-rp {
    border: 1px solid rgba(204, 204, 204, 0.4);
    padding: 10px;
    border-radius: 4px;
}

/*services page end*/

/*quicklink page start*/
.promo-banners.box {
    border-top: 0px;
}

.promo-banners .box-body {
    padding: 5px;
    border-radius: 0px;
}

.content .quick-links .product-icon > a {
    border: 1px solid #194086;
    width: 16.6%;
}

.box.box-primary.quick-links {
    border-left: 3px solid #004d99;
    border-bottom: 3px solid #004d99;
}

.content .quick-links .box-body {
    padding: 0px;
    border-radius: 0px;
}

.content .quick-links .product-icon {
    padding: 0;
    border-radius: 0px;
}

.content .quick-links .product-icon > a:hover {
    background-color: #1e4691;
}

.quick-links .product-icon > a {
    min-height: 100px;
}

.quick-links .product-icon > a > span.img-icon {
    margin-top: 10px;
}

.quick-links .product-icon > a > span.img-txt {
    font-size: 12px;
    margin-top: 10px;
}

div.message.success {
    background-color: #a6f591;
}

/*quicklink page end*/

/*common css start*/
.pw-yellowBTN {
    border: none;
    background: #f8b131;
    border-radius: 4px;
    border-top: 2px solid #f8b131;
    border-bottom: 2px solid #d69217;
    color: #2c2c2c;
    font-size: 16px;
    padding: 11px 30px;
    display: inline-block;
    vertical-align: bottom;
    -webkit-transition: all .05s ease-in-out;
    -moz-transition: all .05s ease-in-out;
    -o-transition: all .05s ease-in-out;
    transition: all .05s ease-in-out;
}

.pw-yellowBTN:hover {
    background: #f0ab2a;
    border-top: 2px solid #d69217;
    border-bottom: 2px solid #f0ab2a;
    color: #2c2c2c;
    padding: 13px 30px 9px;
}

.pw-grayBtn {
    border: none;
    background: #343434;
    border-radius: 4px;
    border-top: 2px solid #343434;
    border-bottom: 2px solid #272727;
    color: #e0e0e0;
    font-size: 16px;
    padding: 11px 30px;
    display: inline-block;
    vertical-align: bottom;
    -webkit-transition: all .05s ease-in-out;
    -moz-transition: all .05s ease-in-out;
    -o-transition: all .05s ease-in-out;
    transition: all .05s ease-in-out;
}

.pw-grayBtn:hover {
    background: #343434;
    border-top: 2px solid #272727;
    border-bottom: 2px solid #343434;
    color: #e0e0e0;
    padding: 13px 30px 9px;
}

.modal-header {
    background: #F4F7F9;
    border-radius: 4px 4px 0px 0px;
}

.modal-content {
    position: relative;
    background-color: #fff;
    -webkit-background-clip: padding-box;
    background-clip: padding-box;
    border: 1px solid #999;
    border: 1px solid rgba(0, 0, 0, .2);
    border-radius: 6px;
    outline: 0;
    -webkit-box-shadow: 0 3px 9px rgba(0, 0, 0, .5);
    box-shadow: 0 3px 9px rgba(0, 0, 0, .5);
}

.table.table-bordered th {
    background-color: #f2f2f2;
}

.table {
    color: #333;
}

li.dropdown.user.user-menu {
    position: relative;
    width: 210px;
}

li.dropdown.user.user-menu > a, li.dropdown.user.user-menu > a:hover, li.dropdown.user.user-menu > a:focus, li.dropdown.user.user-menu > a:active {
    background-color: transparent !important;
}

.navbar-nav > .user-menu .user-image {
    width: 44px;
    height: 44px;
    margin-right: 10px;
    margin-left: 15px;
    margin-top: 3px;
    border: 2px solid rgba(0, 0, 0, 0.12);
}

.navbar-nav > .user-menu .userInfoName {
    display: block;
    color: #fff;
    padding-top: 10px;
    height: 22px;
    width: 140px;
    padding-right: 30px;
    font-size: 12px;
    line-height: 14px;
    text-align: left;
    text-overflow: ellipsis;
    white-space: nowrap;
    overflow: hidden;
    position: relative;
}

.user-menu > a.dropdown-toggle:after {
    content: '\f107';
    font-family: FontAwesome;
    z-index: 10;
    position: absolute;
    right: 10px;
    top: 0;
    font-size: 11px;
    width: 20px;
    height: 50px;
    text-align: center;
    padding-top: 15px;
    padding-bottom: 15px;
    display: block;
}

.dropdown.user.user-menu .userBalanceInfo {
    position: absolute;
    z-index: 500;
    top: 23px;
    line-height: 160%;
    left: 65px;
    font-size: 12px;
    color: #fff;
}

.dropdown.user.user-menu .userBalanceInfo:after {
    clear: both;
    content: ' ';
    display: block;
}

.dropdown.user.user-menu .userBalanceInfo .userBalanceViewLink, .dropdown.user.user-menu .userBalanceInfo .userBalanceRefreshLink {
    text-align: center;
    position: relative;
    float: left;
    display: block;
    width: 20px;
    height: 25px;
}

.userBalanceViewLink.visible:before, .userBalanceRefreshLink:before, .userBalanceViewLink.unvisible:before {
    content: '\f06e';
    color: #ffd978;
    font-family: FontAwesome;
    position: absolute;
    left: 0;
    top: 1px;
    width: 100%;
}

.userBalanceViewLink.unvisible:before {
    content: '\f070';
}

.userBalanceRefreshLink:before {
    content: '\f021';
    color: #ffffff;
    font-size: 9px;
    top: 0;
}

.dropdown.user.user-menu .userBalanceInfo .userBalanceInner {
    float: left;
}

.WebRupee {
    padding: 0 4px;
}

.navbar-custom-menu > ul > li.dropdown.user.user-menu a {
    padding: 0;
}

.main-footer1 {
    background: #004d99;
    padding: 10px 5px;
    color: #b5e0f9;
    font-size: 11px;
    height: 87px;
    line-height: 16px;
    border-top: none;
    position: fixed;
    bottom: 0px;
    width: 200px;
    margin-left: 0;
    text-align: center;
    font-weight: 600;
    opacity: 1;
    -webkit-transition: all .25s ease-in-out;
    -moz-transition: all .25s ease-in-out;
    -o-transition: all .25s ease-in-out;
    transition: all .25s ease-in-out;
}

.main-footer1 .version {
    margin-bottom: 6px;
    background-color: #b5e0f9;
    color: #004d99;
    border-radius: 10px;
    display: inline;
    display: inline-block;
    padding: 2px 9px;
}

.main-footer1 > p {
    margin-bottom: 0;
}


.postloginbg {
    background-color: #ecf0f5;
    color: #444444;

}

.postloginWrap {
    padding-bottom: 20px;
}

.welcomebanner {
    border: 1px solid #e6eaef;
    background: #fff;
    padding: 8px;
    border-radius: 6px;
}

.welcomebanner img {
    width: 100%;
    border-radius: 6px;
    background-color: #fff;
}

.welcome-tagline {
    text-align: left;
    margin-bottom: 20px;
}

.welcome-tagline .panel.panel-default {
    text-align: center;
    margin-bottom: 0;
    margin-top: 10px;
}

.welcome-tagline .panel.panel-default img {
    max-width: 40px;
    max-height: 40px;
    margin-top: 10px;
}

.welcome-tagline h1 {
    text-transform: uppercase;
    font-weight: 300;
    font-size: 36px;
    color: #444;
}

.welcome-tagline em {
    font-weight: 400;
    font-size: 18px;
}

.ctaBox .panel {
    text-align: center;
    padding: 30px;
    font-weight: 400;
    font-size: 16px;
    border-color: #e6eaef;
    margin-bottom: 25px;
}

.ctaBox a .panel {
    color: #444444;
}

.ctaBox a:hover {
    text-decoration: none;
}

.accountDtlBox {
    background-color: #0798eb;
    color: #fff;
    padding: 20px;
    border-radius: 4px;
    margin-bottom: 10px;
}

.addMoneyBTN {
    font-size: 20px;
    font-weight: 500;
    padding: 15px 40px;
}

.addMoneyBTN:hover {
    font-size: 20px;
    font-weight: 500;
    padding: 17px 40px 13px;
}

.accountDtlBox .yourBalc em {
    font-size: 20px;
    font-weight: 300;
}

.accountDtlBox .yourBalc .fa-inr {
    font-size: 25px;
    position: relative;
    top: -10px;
}

.accountDtlBox .yourBalc .amountVal {
    font-size: 42px;
    margin-left: 5px;
    font-weight: 400;
    line-height: normal;
}

.addMoney em {
    margin-top: 10px;
    display: block;
}

.addMoney em {
    font-size: 13px;
}

.addMoney, .nortonCert {
    text-align: right;
}

.paymentMethod {
    padding: 0px 20px;
}

.weaccept em {
    font-size: 11px;
}

a {
    color: #004d99;
}

.box.box-primary {
    border-top-color: #004d99;
}

#my_profile .box-primary .box-body {
    padding: 15px 30px;
}

#my_profile .box-primary .form-group {
    margin: 0;
    padding: 3px 8px;
}

#my_profile .box-primary .box-body > .row {
    border: 1px solid #f1f1f1;
    border-top: 0;
}

#my_profile .box-primary .box-body > .row:first-child {
    border-top: 1px solid #f1f1f1;
}

#my_profile .box-primary .box-body > .row > div:first-child {
    border-right: 1px solid #f1f1f1;
}

#my_profile .box-primary .box-body > .row > div label {
    font-weight: 600;
    font-style: italic;
    margin: 0;
    font-size: 12px;
}

/*common css end*/
/*.nav-tabs > li > a {
    background-color: #f7f7f7;
    border: 1px solid #eaeaea;
}

.nav-tabs > li > a:hover {
    background-color: #fff;
    border: 1px solid #ddd;
}*/

#upload_document .docRow {
    border-bottom: 1px solid #f1f1f1;
    padding: 10px 0 10px 25px;
    position: relative;
}

#upload_document .docRow.docVerified:before, #upload_document .docRow.docPending:before, #upload_document .docRow.docAgain:before {
    content: ' ';
    content: '\f058';
    font-family: FontAwesome;
    position: absolute;
    left: 5px;
    top: 7px;
    font-size: 16px;
    color: #6bc71a;
}

#upload_document .docRow.docPending:before {
    content: '\f017';
    color: #fdae20;
}

#upload_document .docRow.docAgain:before {
    content: '\f06a';
    color: #ff5b5b;
}

#upload_document .docRow:after {
    clear: both;
    content: '';
    display: block;
}

#upload_document .docRow > div {
    float: left;
    width: 220px;
    padding: 0 10px;
}

#upload_document .docRow .docData1 .docTitle {
    font-size: 15px;
    color: #2c2c2c;
    font-weight: 600;
    line-height: 16px;
}

#upload_document .docRow .docData1 .docDetail {
    font-size: 12px;
    font-weight: 600;
    font-style: italic;
    line-height: 16px;
    letter-spacing: 0.03em;
    text-transform: uppercase;
}

#upload_document .docData2 input, #upload_document .docData3 input {
    width: 100%;
    padding: 4px 8px;
    border: 1px solid #d2d6de;
    border-radius: 2px;
}

#upload_document .docRow > .docData3 {
    width: 250px;
    line-height: 14px;
}

#upload_document .docRow > .docData4 {
    width: auto;
}

#upload_document .docRow > .docData4 .btn {
    border-radius: 2px;
    color: #555555;
    font-size: 13px;
    text-transform: uppercase;
    padding: 5px 15px;
    font-weight: 600;
}

#upload_document .docRow > .docData4 .btn:hover {
    padding: 7px 15px 3px;
}

#upload_document .docRow.docAgain .docData1 .docDetail, #upload_document .docRow.docPending .docData1 .docDetail {
    font-size: 10px;
    color: #e90f15;
    font-style: normal;
    font-weight: 900;
}

#upload_document .docRow.docPending .docData1 .docDetail {
    color: #f19504;
}

.online-paymoney-response-div .content .col-md-5 .box-body {
    text-align: center;
}

.online-paymoney-response-div .content .col-md-5 .box-body .ln1 {
    font-size: 26px;
    margin-bottom: 15px;
    padding-top: 150px;
    position: relative;
}

.online-paymoney-response-div .content .col-md-5 .box-body .ln2 {
    margin: 10px 0;
}

.online-paymoney-response-div .content .col-md-5 .box-body .success .ln1:before, .online-paymoney-response-div .content .col-md-5 .box-body .error .ln1:before {
    content: '\f058';
    font-family: FontAwesome;
    font-size: 110px;
    line-height: 70px;
    position: absolute;
    top: 30px;
    left: 50%;
    margin-left: -55px;
    color: #6bc71a;
}

.online-paymoney-response-div .content .col-md-5 .box-body .error .ln1:before {
    content: '\f06a';
    color: #ff5b5b;
}

/****end login css****/
.navbar-custom-menu > ul > li a.topMenuIcon{
    position:relative;
    padding: 28px 8px 0px 8px;
}

.navbar-custom-menu > ul > li a.topMenuIcon > div{
    font-size:9px;
    font-weight:bold;
    text-transform:uppercase;
    letter-spacing:1px;
}

.navbar-custom-menu > ul > li a.topMenuIcon:after{
    content: '';
    position: absolute;
    left: 50%;
    top: 10px;
    width: 18px;
    height: 16px;
    display: block;
    transform: translateX(-50%);
    background-size:72%;
}

.navbar-custom-menu > ul > li a.addMoneyMenuLink:after{
    background: url(../img/add-money1.png) no-repeat;
}
.navbar-custom-menu > ul > li a.plansMenuLink:after{
    background: url(../img/plan1.png) no-repeat;
}
.navbar-custom-menu > ul > li a.servicesMenuLink:after{
    background: url(../img/services1.png) no-repeat;
}
.navbar-custom-menu > ul > li a.bellMenuLink:after{
    background: url(../img/bell1.png) no-repeat;
}
.navbar-custom-menu > ul > li a.logoutMenuLink:after{
    background: url(../img/logoutIcon1.png) no-repeat;
}

.navbar-custom-menu .badge{
    position: absolute;
    top: 4px;
    right: 18px;
    font-size: 10px;
    z-index: 99;
}

.navbar-custom-menu > ul > li .planLink:hover {
    background-color: #ddd9d9;
}

.navbar-custom-menu > ul > li a.serviceLink {
    text-indent: -9000px;
    width: 44px;
    padding: 10px 10px;
    text-align: center;
    overflow: hidden;
    display: block;
    height: 50px;
    background: url(../img/plans.png) no-repeat center center;
}




/** Add Money CSS */
.navbar-custom-menu > ul > li a.addMoneyLink:hover, .navbar-custom-menu > ul > li a.addMoneyLink:focus, .navbar-custom-menu > ul > li a.addMoneyLink:active {
    background-image: url(../img/add-money1.png) !important;
    background-repeat: no-repeat !important;
    background-position: center center !important;
    background-color: rgba(0, 0, 0, 0.1);
}
/** Plan CSS */
.navbar-custom-menu > ul > li a.plansLink:hover, .navbar-custom-menu > ul > li a.plansLink:focus, .navbar-custom-menu > ul > li a.plansLink:active {
    background-image: url(../img/plan1.png) !important;
    background-repeat: no-repeat !important;
    background-position: center center !important;
    background-color: rgba(0, 0, 0, 0.1);
}
/** Services CSS */
.navbar-custom-menu > ul > li a.servicesLink:hover, .navbar-custom-menu > ul > li a.servicesLink:focus, .navbar-custom-menu > ul > li a.servicesLink:active {
    background-image: url(../img/services1.png) !important;
    background-repeat: no-repeat !important;
    background-position: center center !important;
    background-color: rgba(0, 0, 0, 0.1);
}

/** Bell CSS */
.navbar-custom-menu > ul > li a.bellLink:hover, .navbar-custom-menu > ul > li a.bellLink:focus, .navbar-custom-menu > ul > li a.bellLink:active {
    background-image: url(../img/bell1.png) !important;
    background-repeat: no-repeat !important;
    background-position: center center !important;
    background-color: rgba(0, 0, 0, 0.1);
}




/*add money css end*/

/*new login page start*/
.st-pre-login {
    background-color: #194086;
    padding: 10px;
}

.p-loginIWrap .mlogo {
    text-align: center;
    margin-top: 5px;
    margin-bottom: 15px;
}

.formContainer .formICont {
    margin: 0px 100px;
}

.formContainer .register-formICont {
    margin: 0px 15px;
}

.formContainer .box {
    background: none;
    box-shadow: 0px 3px 7px 0px rgba(0, 0, 0, 0.5);
}

.formContainer .box.box-solid .login-box-body {
    box-shadow: none;
}

.st-pre-login .box.box-solid .box-header.with-border {
    text-align: center;
}

.bannerContainer .box {
    box-shadow: 0px 3px 7px 0px rgba(0, 0, 0, 0.5);
}

.moreInfoBox, .moreInfoBox a {
    color: #fff;
    text-align: center;
}

.moreInfoBox {
    padding: 0px 20px;
}

.customerSupp {
    font-size: 16px;
}

.customerSupp .fa {
    margin-right: 5px;
}

.customerSuppTxt, .copyRightTxt {
    font-size: 12px;
    color: #aec7f4;
}

.socialLink {
    padding-top: 10px;
    margin-top: 10px;
    border-top: 1px solid #305392;
}

.socialLink .fa {
    font-size: 18px;
    margin: 0px 5px;
}

.webLink {
    margin-bottom: 5px;
}

.contactInfo a {
    margin: 0px 10px;
}

.bannerContainer #carousel-example-generic .carousel-control {
    opacity: 0;
    -webkit-transition: all .25s ease-in-out;
    -moz-transition: all .25s ease-in-out;
    -o-transition: all .25s ease-in-out;
    transition: all .25s ease-in-out;
}

.bannerContainer:hover #carousel-example-generic .carousel-control {
    opacity: 1;
}

/*new login page end*/

@media only screen and (max-width: 767px) {
    .accountDtlBox .yourBalc, .accountDtlBox .addMoney {
        width: 100%;
        text-align: center;
    }

    .flexi-div .nav > li a {
        width: 100%;
        overflow: hidden;
        padding: 5px;
        min-height: 52px;
    }

    .flexi-div .nav > li {
        width: 33%;
        overflow: hidden;
    }

    #carousel-example-generic .carousel-inner .post-login img {
        height: auto;
    }

    .content .quick-links .product-icon > a {
        width: 49.6%;
    }

    .formContainer .formICont {
        margin: 0px 40px;
    }

    .formContainer {
        margin-bottom: 20px;
    }

    .moreInfoBox {
        padding: 0px;
    }

}

@media only screen and (max-width: 479px) {
    .formContainer .formICont {
        margin: 0px;
    }

    .contactInfo a {
        display: block;
    }
}

@media (max-width: 991px) {
    .navbar-custom-menu > .navbar-nav > li > .dropdown-menu {
        right: 0;
    }

    .navbar-nav > .user-menu > .dropdown-menu > .user-footer .btn-default:hover {
        background-color: #f39c12;
    }

    #my_profile .box-primary .box-body > .row > div:first-child {
        border-right: none;
        border-bottom: 1px solid #f1f1f1;
    }

    #upload_document .docRow > div {
        display: block;
        float: none;
        margin-top: 8px;
    }

    #upload_document .docRow > div:first-child {
        margin-top: 0;
    }

    #upload_document .docRow > .docData2, #upload_document .docRow > .docData3 {
        width: 350px;
    }

    .new-dropdown {
        background-color: #004d99 !important;
        color: white;
    }

    .heading-top-bar {
        display: none;
    }

}

@media (min-width: 768px) {
    .sidebar-mini.sidebar-collapse .sidebar-menu > li:hover > a > span {
        margin-left: 0;
    }

    .sidebar-mini.sidebar-collapse .main-footer1{
        margin-left:-200px !important;
        opacity: 0;
    }

    .navbar-nav {
        float: right;
        margin: 0;
        width: 585px;
    }

    .sidebar-mini.sidebar-collapse .sidebar-menu > li:hover > a > span:not(.pull-right), .sidebar-mini.sidebar-collapse .sidebar-menu > li:hover > .treeview-menu {
        width: 220px;
    }

    .content-wrapper {
        margin-left: 200px;
    }

    .dropdown.user.user-menu .dropdown-menu .userBalanceInfo, .navbar-nav > .user-menu > .dropdown-menu > .user-footer.extra {
        display: none;
    }

    .navbar-nav > .user-menu > .dropdown-menu > li.user-header > p:first-of-type {
        text-overflow: ellipsis;
        overflow: hidden;
        white-space: nowrap;
    }
}

@media (max-width: 767px) {
    .main-header > .navbar {
        margin-left: 65px;
    }

    .skin-blue .main-header .navbar {
        background-color: #004d99;
        border-top: none;
    }

    .skin-blue .main-header .logo:hover {
        background-color: #004d99;
    }

    .heading-top-bar {
        width: 77%;
        display: none;
    }

    .product-icon > a {
        width: 50%;
    }

    .serviceCon-rpwrap {
        margin: 20px 0px;
    }

    .serviceSlider {
        margin-bottom: 20px;
    }

    .serviceCon-rpwrap {
        padding-left: 15px;
    }

    .postloginbg {
        margin: 0 -15px;
    }

    .main-sidebar, .left-side {
        padding-top: 60px !important;
    }

    .main-header .logo {
        height: 50px;
        position: absolute;
        width: 150px;
    }

    .main-header .logo .logo-lg {
        margin-top: 0;
    }

    .main-header {
        max-height: 50px;
    }

    .main-header .logo .logo-lg img {
        width: 120px;
    }

    .main-header > .navbar {
        width: calc(100% - 65px);
    }

    li.dropdown.user.user-menu {
        width: 50px;
    }

    .navbar-nav > .user-menu .userInfoName, .dropdown.user.user-menu .userBalanceInfo {
        display: none;
    }

    .user-menu > a.dropdown-toggle:after {
        content: '';
    }

    .navbar-nav > .user-menu .user-image {
        margin-right: 5px;
        margin-left: 3px;
    }

    .navbar-custom-menu > ul > li:last-child {
        border-right: none;
        margin-right: 0;
    }

    .main-header .sidebar-toggle {
        float: right;
        border-left: 1px solid #194086;
    }

    .navbar-nav > .user-menu > .dropdown-menu > li.user-header > p {
        margin-bottom: 0;
    }

    .dropdown.user.user-menu .user-header .userBalanceInfo {
        display: block;
        position: relative;
        left: 0;
        top: 0;
    }

    .dropdown.user.user-menu .user-header .userBalanceInfo .userBalanceInner {
        float: none;
        display: inline;
        padding-right: 8px;
    }

    .dropdown.user.user-menu .user-header .userBalanceInfo .userBalanceRefreshLink {
        float: none;
        display: inline;
    }

    .dropdown.user.user-menu .user-header .userBalanceInfo .userBalanceRefreshLink:before {
        font-size: 12px;
    }

    .dropdown.user.user-menu .user-header .userBalanceInfo .userBalanceInner:before {
        content: 'Balance: ';
    }

    .navbar-nav > .user-menu > .dropdown-menu > li.user-header {
        height: 185px;
    }

    .navbar-nav > .user-menu > .dropdown-menu {
        width: 224px;
    }

    .navbar-custom-menu > ul > li:last-child {
        display: none;
    }

    .navbar-nav > .user-menu > .dropdown-menu > .user-footer.extra {
        border-top: 1px solid #e2ebf1;
    }

    .st-pre-login aside.main-sidebar {
        position: relative;
        -webkit-transform: translate(0px, 0);
        -ms-transform: translate(0px, 0);
        -o-transform: translate(0px, 0);
        transform: translate(0px, 0);
        width: 100%;
        padding-top: 4px !important;
    }

    .st-pre-login aside.main-sidebar .support-details > h2 {
        font-size: 13px;
        padding: 4px 0px;
        border-bottom: none;
        margin-bottom: 5px;
    }

    .st-pre-login .social-icon {
        display: none;
    }

    .st-pre-login .support-details > p {
        margin-bottom: 5px;
        line-height: 120%;
    }

    .st-pre-login .support-details > p:nth-of-type(1) strong, .support-details > p:nth-of-type(2) strong {
        display: inline-block;
        text-align: center;
        text-indent: -9000px;
        width: 20px;
        height: 12px;
        overflow: hidden;
        position: relative;
    }

    .st-pre-login .support-details > p:nth-of-type(1) strong:before, .support-details > p:nth-of-type(2) strong:before {
        content: '\f095';
        font-family: FontAwesome;
        position: absolute;
        left: 0;
        top: 0px;
        font-size: 12px;
        color: #fff;
        text-indent: 0;
    }

    .st-pre-login .support-details > p:nth-of-type(2) strong:before {
        content: '\f0e0';
    }

    .st-pre-login .sidebar {
        padding-bottom: 0;
    }

    .st-pre-login .product-icon > a > span.img-icon {
        display: none;
    }

    .st-pre-login .product-icon > a {
        min-height: auto;
        border-bottom: 1px solid #194086;
        padding-bottom: 8px;
    }

    .st-pre-login .product-icon {
        padding: 0;
    }

    .st-pre-login section.content .col-md-7 {
        display: none;
    }

    #upload_document .docRow > .docData2, #upload_document .docRow > .docData3 {
        width: 100%;
    }

    #upload_document .docRow.docVerified:before {
        left: auto;
        right: 5px;
        font-size: 26px;
        top: 12px;
    }

    #upload_document .docRow {
        padding: 10px 0 10px 0;
    }
}

ul.sidebar-menu li.new {
    position: relative;
}

ul.sidebar-menu li.new:before {
    background-image: url(../img/new.png);
    background-position: left top;
    background-repeat: no-repeat;
    content: '';
    width: 28px;
    height: 28px;
    display: block;
    position: absolute;
    left: 0;
    top: 0;
    z-index: 10;
}

.span-note {
    font-size: 10px;
    font-weight: 600;
    color: #9c9c9c;
    display: inline-block;
}

.sidebar-menu > li > a > i.loan {
    background-position: -3px -410px;
}

.sidebar-menu > li > a > i.support {
    background-position: -3px -209px;
}

.sidebar-menu>li>a>i.notices {
    background-position: -3px -450px;
}

.custom-alert {
    border-radius: 2px;
    border-width: 0;
    display: block;
    box-shadow: 0 1px 3px rgba(0, 0, 0, .1), 0 1px 2px rgba(0, 0, 0, .18);
    padding: 10px;
    border: 1px solid transparent;
    margin-bottom: 20px;
}

.custom-alert-danger {
    color: #a94442;
    background-color: #f2dede;
    border-color: #ebccd1;
}

.custom-alert-success {
    color: #3c763d;
    background-color: #dff0d8;
    border-color: #d6e9c6;
}

/** Added by Sumit Parakh on 19 Feb 2018 **/
.logoMini {
    width: auto !important;
}

.pw-yellowBTN-custom{
    border: none;
    background: #f8b131;
    border-radius: 4px;
    border-top: 2px solid #f8b131;
    border-bottom: 2px solid #d69217;
    color: #2c2c2c !important;
    font-size: 16px;
    padding: 6px 15px;
    display: inline-block;
    vertical-align: bottom;
    -webkit-transition: all .05s ease-in-out;
    -moz-transition: all .05s ease-in-out;
    -o-transition: all .05s ease-in-out;
    transition: all .05s ease-in-out;
}
.pw-yellowBTN-custom:hover, .pw-yellowBTN-custom:focus {
    background: #f0ab2a !important;
    border-top: 2px solid #d69217 !important;
    border-bottom: 2px solid #f0ab2a !important;
    color: #2c2c2c !important;
}
.pw-yellowBTN-custom:hover {
    background: #f0ab2a;
    border-top: 2px solid #d69217;
    border-bottom: 2px solid #f0ab2a;
    color: #2c2c2c;
    padding: 6px 15px;
}

.new-dropdown{
    background-color: #004d99 !important;
    color: white;
}

.new-dropdown > li:not(:last-child){
    border:1px solid #1e4691;
    padding: 8px;
    margin-top: -1px;
}

.new-dropdown > li:not(:last-child):hover {
    background-color: white;
    color: #1e4691;
}

.services-tree{
    background-color: #2d3e72;
}

.services-tree:hover,.services-tree.active{
    background-color: #273662 !important;
}

.services-tree:hover>a,.services-tree.active>a{
    background-color: #273662 !important;
}

ul.sidebar-menu.collapsible.accordion > .services-tree > a{
    border-bottom: 1px solid #3b5976 !important;
}

.services-tree > .treeview-menu>li.active{
    background-color: #273662 !important;
    border-bottom: 1px solid #3b5976;
}


.services-tree > .treeview-menu>li{
    background-color: #2d3e72 !important;
    border-bottom: 1px solid #3b5976;
}

.services-tree > .treeview-menu > li.active > ul.treeview-menu > li{
    background-color: #1e2b53 !important;
    border-bottom: 1px solid #3b5976 !important;
}

.skin-blue .sidebar-menu > li > .treeview-menu > li > ul.treeview-menu > li:hover{
    background-color: #1b2751 !important;
}

ul.sidebar-menu.collapsible.accordion > .services-tree > a:hover{
    background-color: #2d3e72 !important;
}
@media (min-width: 268px) and (max-width: 768px) {
    .main-header .logo > .logo-mini {
        display: block !important;
        margin-left: -95px;
        margin-right: -15px;
        font-size: 18px;
    }

    /*.main-header .logo > .logo-lg {*/
    /*display: none !important;*/
    /*}*/
}



@media (max-width: 479px) {
    .navbar-custom-menu > ul > li a.topMenuIcon > div{
        display:none;
    } 

    .navbar-custom-menu > ul > li a.topMenuIcon{
        padding: 0px 18px;
        height:50px;
    }

    .navbar-custom-menu > ul > li a.topMenuIcon:after{
        top: 17px;
    }
    .navbar-custom-menu .badge{
        top: 9px;
        right: 8px;
    }

}
</style>
<script type="text/javascript" src="newlog/js/bootstrap.min.js?version=1.0.13"></script>
    <script type="text/javascript" src="newlog/js/main1.2.js?version=1.0.13"></script>
