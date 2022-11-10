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
 <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.0/css/bootstrap.min.css">

<!-- jQuery library -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>

<!-- Latest compiled JavaScript -->
<!--<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.0/js/bootstrap.min.js"></script>-->
  
</head>
<body class="sidebar-mini skin-green-light">
<div class="wrapper">

  @if (Auth::check()) 
		@include('admin.layouts.topo')
		@if(in_array(Auth::user()->role_id,array(1,19)))
			@include('admin.layouts.topnav')
		@elseif(in_array(Auth::user()->role_id,array(3)))
			@include('admin.layouts.md-topnav') 
		@elseif(in_array(Auth::user()->role_id,array(4)))
		   @include('admin.layouts.dist-topnav')
		@endif
  @endif

  <div class="content-wrapper" >
      <div class="content">
	  
          @yield('content')
    </div>
</div>
<!-- Control Sidebar -->
 <div class="control-sidebar-bg">
   
 </div>
  
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
   
 var URL = '{{url('/')}}'; 
 $(document).ready(function(){
      $('.panel-collapse').on('show.bs.collapse', function () {
            $(this).siblings('.panel-heading').addClass('active');
      });

      $('.panel-collapse').on('hide.bs.collapse', function () {
            $(this).siblings('.panel-heading').removeClass('active');
      });
     
     $('.opc1').on("click",function(){ // alert($(this).prop('checked'));
         var check_val = $(this).attr('data-id');
         if($(this).prop('checked')) {  
            // $(this).prop("checked", true);
             $('#sm_perm_'+check_val).val('');
           
            // alert(check_val);
         }else{  
            $(this).removeAttr("checked");
             
            var my_sel_val = $(this).val();  
            $('#sm_perm_'+check_val).val(my_sel_val);
         }
    });

     $("#user_role").change(function(){ 
        Type = $(this).val();
        window.location = URL+"/admin/member_permission/"+btoa(Type);   
    });
    
     $('ul :checkbox').change(function(){  
		
			var checked=$(this).prop('checked')
				$(this).parent().next().find(':checkbox').prop('checked',checked)
				//if (checked)
				//	$(this).parents('ul').siblings('label').children(':checkbox').prop('checked',checked)
		});
		
		
      $("#myInput").on("keyup", function() {
        var value = $(this).val().toLowerCase();
        $("#myTable tr").filter(function() {
          $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
        });
      });
    });   
  
  
</script>
</body>
</html>