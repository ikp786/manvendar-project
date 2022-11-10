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
	$(function() {
  $('#datetimepicker1').datetimepicker({
    language: 'pt-BR'
  });
});
 $(document).ready(function () {
        $.noConflict();
        $('.customDatepicker').datepicker({
              autoclose: true,  
            format: "dd-mm-yyyy"
        });

    }); 
	function getUser()
	{
		 var userInfo = $("#userInfo").val();
		 //alert(userInfo);
		$.ajax({
			type: "get",
			url: "<?php echo e(url('get-user-info')); ?>",
			data: "userInfo="+userInfo, 
			dataType:"json",
			beforeSend: function () 
			{
				//$('.login_attempt').html('Processing...');
			},
			success: function (results) {
				if(results.count >= 1)
				{
				  var userDetails = $("<select onChange='getSelectedUser()'></select>").attr("id", "userInfoDiv").attr("class", "form-control");

					$.each(results.content, function (id, detail) {
						userDetails.append("<option value="+detail.id+">" + detail.name + "</option>");
					});
					if(results.count == 1)
					{
						var userId = $("#userInfoDiv").val();
						var userInfo = $("#userInfoDiv option:selected").text();
						$("#userId").val(userId);
						$("#userInfo").val(userInfo);
					}
					else{
						
					}
					$("#dropDownListDiv").html(userDetails);
				}
			}
		});
	}
	function getSelectedUser()
	{
		var userId = $("#userInfoDiv").val();
		var userInfo = $("#userInfoDiv option:selected").text();
		$("#userId").val(userId);
		$("#userInfo").val(userInfo);
		//$("#dropDownListDiv").text('');
	}
</script>

<div class="panel panel-default">
	<div class="panel-body">
		<form method="get" action="<?php echo e(Request::url()); ?>" class="form-inline" role="form" >
		  <!--<div class="form-group">
			   <?php echo e(Form::select('searchType', ['NUMBER' => 'Number', 'TXNID' => 'Txn Id','MOB'=>"Mob No",'RID'=>"Record Id"], app('request')->input('searchType'), ['class'=>'form-control '])); ?>


			</div> -->
			<div class="form-group">
				<input name="number" type="text" class="form-control" id="number" value="<?php echo e(app('request')->input('number')); ?>" placeholder="Search Text">
			</div>
			<div class="form-group">
				<input name="amount" type="text" class="form-control" id="amount" value="<?php echo e(app('request')->input('amount')); ?>" placeholder="Search Amount">
			</div>
			 
			<!--<button type="submit" class="btn btn-success  btn-md" onSubmit="return validateForm()"><i class="fa fa-search"></i></button> -->  
			<div class="form-group">
				<input name="fromdate" class="form-control customDatepicker" type="text" value="<?php echo e((app('request')->input('fromdate')) ? app('request')->input('fromdate') : date('d-m-Y')); ?>" autocomplete="off"> 
			</div>
			<div class="form-group">
				<input name="todate" class="form-control customDatepicker" type="text" value="<?php echo e((app('request')->input('todate')) ? app('request')->input('todate') : date('d-m-Y')); ?>" autocomplete="off">
			</div>
			<div class="form-group">
				<?php if(Auth::user()->role_id == 1): ?>	
				<?php echo e(Form::select('searchOf', ['1' => 'Success', '2' => 'Failed','3'=>"Pending",'4'=>"Refunded",'21' => 'Manual Success', '28' => 'Manual Failed','18'=>'InProcess'], app('request')->input('searchOf'), ['class'=>'form-control','placeholder'=>'--Select--'])); ?>

				<?php else: ?>	
				<?php echo e(Form::select('searchOf', ['1' => 'Success', '2' => 'Failed','3'=>"Pending",'4'=>"Refunded",'21' => 'Refund Success'], app('request')->input('searchOf'), ['class'=>'form-control','placeholder'=>'--Select--'])); ?>

				<?php endif; ?>
			</div>
			<div class="form-group">	
				<?php echo e(Form::select('product', ['4' => 'DMT1', '5' => 'A2Z wallet','10' => 'AEPS'], app('request')->input('product'), ['class'=>'form-control','placeholder'=>'--Select--'])); ?>		
			</div> 					
		   <!--<div class="form-group" >
			<div>
				<input type="text" class="form-control" id="userInfo" value=""
					   placeholder="userInfo" onKeyUp="getUser()" autocomplete="off">
			</div>
				<div id="dropDownListDiv">
				</div>
				 <input name="userId" type="hidden" class="form-control" id="userId">
			</div>-->
				<button name="export" value="SEARCH" type="submit" class="btn btn-primary btn-md"><i class="fa fa-search"></i></button>
				<button name="export" value="EXPORT" type="submit" class="btn btn-basic btn-md"><i class="fa fa-file-excel-o" aria-hidden="true"></i></button>
			  <a href="<?php echo e(Request::url()); ?>"/ class="btn btn-info  btn-md"><i class="fa fa-refresh"></i></a>  
		</form> 
	</div>
</div>           