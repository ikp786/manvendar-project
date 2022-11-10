@if (Auth::user()->role->id < 5)
<!DOCTYPE html>
<html style="background:#115798 !important;">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="A fully featured admin theme which can be used to build CRM, CMS, etc.">
    <meta name="author" content="Coderthemes">

    <link rel="shortcut icon" href="assets/images/favicon_1.ico">

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

    <!-- HTML5 Shiv and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
    <script src="https://oss.maxcdn.com/libs/respond.js/1.3.0/respond.min.js"></script>
    <![endif]-->

    <script src="{{ asset('ad/assets/js/modernizr.min.js') }}"></script>

</head>


<body style="background:#115798 !important;">


<!-- Navigation Bar-->
<header id="topnav">

    @include('admin.layouts.ufxtopo')

    @include('admin.layouts.ufxtopnav')
</header>
<!-- End Navigation Bar-->


<div class="wrapper">


    <div class="container">

        @yield('content')
                <!-- Footer -->
        @include('admin.layouts.ufxfooter')


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
</script>


</body>
</html>
@endif