<script type="text/javascript">
	function validateForm()
	{
		if($("#number").val() =='')
		{
			alert("Enter Number or Txn id");
			$("#number").focus();
			return false;
		}
	}
	function validateExportForm()
	{		
	}
	$(document).ready(function () {
		$.noConflict();
        $('.customDatepicker').datepicker({
            autoclose: true,  
            format: "dd-mm-yyyy"
        });
    }); 
</script>
<div class="panel panel-default">
    <div class="panel-body">
		<div class="col-md-12">
			<div class="col-md-3">
	          <h4 class="page-title" style="color: black; "><?php echo e($page_title); ?></h4>
	        </div>                   
			<div class="col-md-9">
				<form method="get" action="<?php echo e(Request::url()); ?>"  class="form-inline" role="form">
					<input name="fromdate" class="form-control customDatepicker" type="text" value="<?php echo e((app('request')->input('fromdate')) ? app('request')->input('fromdate') : date('d-m-Y')); ?>" autocomplete="off"> 
					<input name="todate" class="form-control customDatepicker" type="text" value="<?php echo e((app('request')->input('todate')) ? app('request')->input('todate') : date('d-m-Y')); ?>" autocomplete="off">
					<?php if(in_array(Auth::user()->role_id,array(1,4))): ?>
						<?php echo e(Form::select('user', $users, app('request')->input('user'), ['class'=>'form-control','placeholder'=>"--Select--"])); ?>

					<?php endif; ?>
					<button name="export" value="Search" type="submit" class="btn btn-primary waves-effect waves-light m-l-10 btn-md"><i class="fa fa-search"></i></button>
				
					<button name="export" value="Payment Load" type="submit" class="btn btn-basic waves-effect waves-light m-l-10 btn-md"><span class="glyphicon glyphicon-find"></span><i class="fa fa-file-excel-o"></i></button>
				 	<a href="<?php echo e(Request::url()); ?>"/ class="btn btn-info  btn-md"><i class="fa fa-refresh"></i></a>
				</form>
			</div>                         
    	</div>
	</div>
</div>