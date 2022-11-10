<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
  <title>A2ZSUVIDHAA</title>
  <link rel="shortcut icon" type="image/png" href="<?php echo e(url('newlog/images/Logo168.png')); ?>"/>
  <!-- Bootstrap 3.3.7 -->
  <link rel="stylesheet" href="<?php echo e(url('bower_components/bootstrap/dist/css/bootstrap.min.css')); ?>">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="<?php echo e(url('bower_components/font-awesome/css/font-awesome.min.css')); ?>">
  <!-- Ionicons -->
  <link rel="stylesheet" href="<?php echo e(url('bower_components/Ionicons/css/ionicons.min.css')); ?>">
  <!-- DataTables -->
  <link rel="stylesheet" href="<?php echo e(url('bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css')); ?>">
  <!-- Theme style -->
  <link rel="stylesheet" href="<?php echo e(url('dist/css/AdminLTE.min.css')); ?>">
  <!-- AdminLTE Skins. Choose a skin from the css/skins folder instead of downloading all of them to reduce the load. -->
  <link rel="stylesheet" href="<?php echo e(url('dist/css/skins/_all-skins.min.css')); ?>">
   <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic">
   
  <link rel="stylesheet" href="<?php echo e(url('css/style.css')); ?>">
  

   <!-- for datepicker->Calendar -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.4.1/css/bootstrap-datepicker3.css"/>
  <!-- Google Font: Source Sans Pro -->
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>

</head>
<body class="sidebar-mini skin-green-light">
<div class="wrapper">

  <?php if(Auth::check()): ?> 
    <?php echo $__env->make('layouts.header', \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>
		<?php if(Auth::user()->role_id==7): ?>
          <?php echo $__env->make('layouts.api-user-sidebar', \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>
        <?php else: ?>
    <?php echo $__env->make('layouts.sidebar', \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>
	<?php endif; ?>
  <?php endif; ?>
    <div class="content-wrapper">
        <div class="content"> 
         <!--  <?php echo $__env->make('layouts.subheader', \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?> -->
          <?php echo $__env->yieldContent('content'); ?>   
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
<script src="<?php echo e(('bower_components/datatables.net/js/jquery.dataTables.min.js')); ?>"></script>
<script src="<?php echo e(('bower_components/datatables.net-bs/js/dataTables.bootstrap.min.js')); ?>"></script>
<!-- jQuery 3 -->
<script src="<?php echo e(url('bower_components/jquery/dist/jquery.min.js')); ?>"></script>
<!-- Bootstrap 3.3.7 -->
<script src="<?php echo e(url('bower_components/bootstrap/dist/js/bootstrap.min.js')); ?>"></script>
<!-- FastClick -->
<script src="<?php echo e(url('bower_components/fastclick/lib/fastclick.js')); ?>"></script>
<!-- AdminLTE App -->
<script src="<?php echo e(url('dist/js/adminlte.min.js')); ?>"></script>
<!-- Sparkline -->
<script src="<?php echo e(url('bower_components/jquery-sparkline/dist/jquery.sparkline.min.js')); ?>"></script>

<!-- AdminLTE for demo purposes -->
<script src="<?php echo e(url('dist/js/demo.js')); ?>"></script>
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
</script>
</body>
</html>