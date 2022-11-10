@if (Auth::user()->role->id < 5 || Auth::user()->role->id ==10)
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="A fully featured admin theme which can be used to build CRM, CMS, etc.">
    <meta name="author" content="Coderthemes">

    <!-- <link rel="shortcut icon" href="assets/images/favicon_1.ico"> -->

    <title>@if(Auth::user()->role->id == 1){{ Auth::user()->name }} @endif -  {{ $_SERVER['HTTP_HOST'] }}</title>

    <!--Morris Chart CSS -->
    <link rel="stylesheet" href="{{ asset('ad/assets/plugins/morris/morris.css') }}">
    <link href="{{ asset('ad/assets/plugins/sweetalert/dist/sweetalert.css') }}" rel="stylesheet" type="text/css">
    <link href="{{ asset('ad/assets/css/bootstrap.min.css') }}" rel="stylesheet" type="text/css"/>
    <link href="{{ asset('ad/assets/css/core.css') }}" rel="stylesheet" type="text/css"/>
    <link href="{{ asset('ad/assets/css/components.css') }}" rel="stylesheet" type="text/css"/>
    <link href="{{ asset('ad/assets/css/icons.css') }}" rel="stylesheet" type="text/css"/>
    <link href="{{ asset('ad/assets/css/pages.css') }}" rel="stylesheet" type="text/css"/>
    <link href="{{ asset('ad/assets/css/menu.css') }}" rel="stylesheet" type="text/css"/>
    <link href="{{ asset('ad/assets/css/responsive.css') }}" rel="stylesheet" type="text/css"/>
	<link rel="stylesheet" href="{{ url('css/style.css') }}">

    <!-- HTML5 Shiv and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
    <script src="https://oss.maxcdn.com/libs/respond.js/1.3.0/respond.min.js"></script>
    <![endif]-->
    <script src="{{ asset('ad/assets/js/modernizr.min.js') }}"></script>
</head>


<body>
@if(Auth::user()->company_id==13)
<?php echo $color='#1E679C'; ?>
@elseif(Auth::user()->company_id==9)
<?php echo $color='#FFAF1C'; ?>
@elseif(Auth::user()->company_id==5)
<?php echo $color='#3498DB'; ?>
@elseif(Auth::user()->company_id==4)
<?php echo $color='#00ACEF'; ?>
@elseif(Auth::user()->company_id==8)
<?php echo $color='#1FB5AD'; ?>
@elseif(Auth::user()->company_id==16)
<?php echo $color='#388DC3'; ?>
@elseif(Auth::user()->company_id==18)
<?php echo $color='#F08080'; ?>
@endif
@if (Auth::check())
<?php
	// $tap_nav_color =Auth::user()->company->color_nav;
	// $tap_nav_color =($tap_nav_color)?$tap_nav_color:'orange';
	// $content_color =Auth::user()->company->color_content;
	// $content_color =($content_color)?$content_color:'orange';
	
	$agent_header_color =Auth::user()->company->agent_header_color;
	$agent_header_color =($agent_header_color)?$agent_header_color:'orange';
	$agent_bg_color =Auth::user()->company->agent_bg_color;
	$agent_bg_color =($agent_bg_color)?$agent_bg_color:'orange';
	$md_bg_color =Auth::user()->company->md_bg_color;
	$md_bg_color =($md_bg_color)?$md_bg_color:'orange';
	
?>
@endif
<style type="text/css">
/**/
 .nav-bg-color{
		background-color:{{ $agent_header_color or 'orange'}} !important;
  }
  .content-bg-color{
	  background-color:{{ $agent_bg_color or 'orange'}} !important;
  }
  .panel panel-warning content-bg-color > .panel-heading{
	  background-color:{{ $agent_bg_color or 'orange'}} !important;
  }
/**/
html
{
    background:{{ $md_bg_color or '' }} !important;
}
body
{
    background:{{ $md_bg_color or '' }} !important;
}
#topnav .navigation-menu > li > a {
background:{{ $md_bg_color or '' }};
    }
    #topnav .navigation-menu > li .submenu {
        background:{{ $md_bg_color or '' }};
    }
    .form-control {
            border: 3px solid {{ $md_bg_color or '' }};
    }
    .bootstrap-table .table > thead > tr > th {
         background:{{ $md_bg_color or '' }};
    }
    .table > thead > tr > th, .table > tbody > tr > th, .table > tfoot > tr > th, .table > thead > tr > td, .table > tbody > tr > td, .table > tfoot > tr > td, .table > thead > tr > th, .table-bordered {
        border: 1px solid {{ $md_bg_color or '#f35319' }}
    }
    .hr
    {
        border: 2px solid {{ $md_bg_color or '#f35319' }}
    }
	.tr-text-fromat
	{
		font-size: 14px ;
		color: orange ;
		text-align: center;
	}
</style>
<!-- Navigation Bar-->
<header id="topnav">

    @include('admin.layouts.topo')
    @if(Auth::user()->role_id==10)
    @include('admin.layouts.saletopnav')
    @else
    @include('admin.layouts.topnav')
    @endif
</header>
<!-- End Navigation Bar-->


<div class="wrapper">


    <div class="container">

        @yield('content')
                <!-- Footer -->
        @include('admin.layouts.footer')


    </div>


</div>


<!-- jQuery  -->
<script src="{{ asset('ad/assets/js/jquery.min.js') }}"></script>
<script src="{{ asset('ad/assets/js/bootstrap.min.js') }}"></script>
<script src="{{ asset('ad/assets/js/detect.js') }}"></script>
<script src="{{ asset('ad/assets/js/fastclick.js') }}"></script>

<script src="{{ asset('ad/assets/js/jquery.slimscroll.js') }}"></script>
<script src="{{ asset('ad/assets/js/jquery.blockUI.js') }}"></script>
<script src="{{ asset('ad/assets/js/waves.js') }}"></script>
<script src="{{ asset('ad/assets/js/wow.min.js') }}"></script>
<script src="{{ asset('ad/assets/js/jquery.nicescroll.js') }}"></script>
<script src="{{ asset('ad/assets/js/jquery.scrollTo.min.js') }}"></script>

<script src="{{ asset('ad/assets/plugins/peity/jquery.peity.min.js') }}"></script>

<!-- jQuery  -->
<script src="{{ asset('ad/assets/plugins/waypoints/lib/jquery.waypoints.js') }}"></script>
<script src="{{ asset('ad/assets/plugins/counterup/jquery.counterup.min.js') }}"></script>

<script src="{{ asset('ad/assets/plugins/morris/morris.min.js') }}"></script>
<script src="{{ asset('ad/assets/plugins/raphael/raphael-min.js') }}"></script>

<script src="{{ asset('ad/assets/plugins/jquery-knob/jquery.knob.js') }}"></script>

<script src="{{ asset('ad/assets/pages/jquery.dashboard.js') }}"></script>

<script src="{{ asset('ad/assets/js/jquery.core.js') }}"></script>
<script src="{{ asset('ad/assets/js/jquery.app.js') }}"></script>

<!-- Sweet-Alert  -->
<script src="{{ asset('ad/assets/plugins/sweetalert/dist/sweetalert.min.js') }}"></script>
<script src="{{ asset('ad/assets/pages/jquery.sweet-alert.init.js') }}"></script>


<script type="text/javascript">
    jQuery(document).ready(function ($) {
        $('.counter').counterUp({
            delay: 100,
            time: 1200
        });

        $(".knob").knob();

    });

    @if(Auth::id()!=116)
$(document).keydown(function (event) {
    if (event.keyCode == 123) { // Prevent F12
        return false;
    } else if (event.ctrlKey && event.shiftKey && event.keyCode == 73) { // Prevent Ctrl+Shift+I        
        return false;
    }
});
$(document).on("contextmenu", function (e) {        
    e.preventDefault();
});
@endif
</script>


</body>
</html>
@endif