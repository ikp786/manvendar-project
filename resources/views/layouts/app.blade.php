<!DOCTYPE html>
<html lang="en">
<head><meta http-equiv="Content-Type" content="text/html; charset=utf-8">
  
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
  <title>A2ZSUVIDHAA</title>
  <link rel="shortcut icon" type="image/png" href="{{url('newlog/images/Logo168.png')}}"/>
  <!-- Bootstrap 3.3.7 -->
  <link rel="stylesheet" href="{{url('bower_components/bootstrap/dist/css/bootstrap.min.css')}}">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="{{url('bower_components/font-awesome/css/font-awesome.min.css')}}">
  <!-- Ionicons -->
  <link rel="stylesheet" href="{{url('bower_components/Ionicons/css/ionicons.min.css')}}">
  <!-- DataTables -->
  <link rel="stylesheet" href="{{url('bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css')}}">
  <!-- Theme style -->
  <link rel="stylesheet" href="{{url('dist/css/AdminLTE.min.css')}}">
  <!-- AdminLTE Skins. Choose a skin from the css/skins folder instead of downloading all of them to reduce the load. -->
  <link rel="stylesheet" href="{{url('dist/css/skins/_all-skins.min.css')}}">
   <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic">
   
  <link rel="stylesheet" href="{{url('css/style.css')}}">
  

   <!-- for datepicker->Calendar -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.4.1/css/bootstrap-datepicker3.css"/>
  <!-- Google Font: Source Sans Pro -->
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>

</head>
<body class="sidebar-mini skin-green-light">
<div class="wrapper">

  @if(Auth::check()) 
    @include('layouts.header')
		@if(Auth::user()->role_id==7)
          @include('layouts.api-user-sidebar')
        @else
    @include('layouts.sidebar')
	@endif
  @endif
    <div class="content-wrapper">
        <div class="content"> 
         <!--  @include('layouts.subheader') -->
          @yield('content')   
		</div>
	</div>
<!-- Control Sidebar -->
 <div class="control-sidebar-bg"></div>
   <aside class="control-sidebar control-sidebar-dark">
    <!-- Control sidebar content goes here -->
  </aside>
  </div> 
 
<!-- ./wrapper -->
<!-- for datepicker->Calander -->
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.4.1/js/bootstrap-datepicker.min.js"></script>

<!-- DataTables -->
<script src="{{('bower_components/datatables.net/js/jquery.dataTables.min.js')}}"></script>
<script src="{{('bower_components/datatables.net-bs/js/dataTables.bootstrap.min.js')}}"></script>
<!-- jQuery 3 -->
<script src="{{url('bower_components/jquery/dist/jquery.min.js')}}"></script>
<!-- Bootstrap 3.3.7 -->
<script src="{{url('bower_components/bootstrap/dist/js/bootstrap.min.js')}}"></script>
<!-- FastClick -->
<script src="{{url('bower_components/fastclick/lib/fastclick.js')}}"></script>
<!-- AdminLTE App -->
<script src="{{url('dist/js/adminlte.min.js')}}"></script>
<!-- Sparkline -->
<script src="{{url('bower_components/jquery-sparkline/dist/jquery.sparkline.min.js')}}"></script>

<!-- AdminLTE for demo purposes -->
<script src="{{url('dist/js/demo.js')}}"></script>
<script>
    
   complain_count();
   
   setInterval(function()
    {
        complain_count();
    }, 15000);
            
   function complain_count(){
        
            var token = $("input[name=_token]").val();
    	    
    		var dataString = '';
    		$.ajaxSetup({
    		headers: {
    			'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
    		}
    		});
    		
    		url = "{{url('check-complain-status')}}"
    		
    		$.ajax({
    			type: "post",
    			url: url,
    			data: dataString,
    			dataType: "json",
    			success: function (data){   
    				  $('#complain_retailer_count').text(data);
    			}
    		});
   }    
        
    
  $(function () {
    $('#example1').DataTable()
    $('#example2').DataTable({
      'paging'      : false,
      'lengthChange': false,
      'searching'   : true,
      'ordering'    : false,
      'info'        : true,
      'autoWidth'   : true,
	  'scrollY'     : 500,
      'scrollX'     : true
    })
  })
</script>
</body>
</html>