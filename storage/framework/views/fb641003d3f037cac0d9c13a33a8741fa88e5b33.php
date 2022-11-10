<?php $__env->startSection('content'); ?>
 <script type="text/javascript">

$(document).ready(function() {
	//alert("Hello");
   getSPFReport();
   getPOReport();
   getPendingComlain();
   getBalanceRequest();
});
function getSPFReport(){
	 var dataString="type=SPF";
		$.ajax({
			type: "GET",
			url: "<?php echo e(url('count-txn-with-volume')); ?>",
			data: dataString,
			datatype: "json",
			beforeSend:function()
			{
				
			},
			success: function (res) {
				$("#successSpiner").hide();
				$("#successTxnCount").html(res.report.successTxnCount);
				$("#successTxnVolume").html(res.report.successVolume);
				$("#failTxnCount").html(res.report.failTxnCount);
				$("#failVolume").html(res.report.failVolume);
				$("#refundedTxnCount").html(res.report.refundedTxnCount);
				$("#refundedVolume").html(res.report.refundedVolume);
			}
        });
}
function getPOReport(){
	 var dataString="type=SPF";
		$.ajax({
			type: "GET",
			url: "<?php echo e(url('get-po-txn-count-volume')); ?>",
			data: dataString,
			datatype: "json",
			beforeSend:function()
			{
				
			},
			success: function (res) {
				$("#offLineTxnCount").html(res.report.offLineTxnCount);
				$("#offLineVolume").html(res.report.offLineVolume);
				$("#pendingTxnCount").html(res.report.pendingTxnCount);
				$("#pendingVolume").html(res.report.pendingVolume);
				
			}
        });
}
function getPendingComlain(){
	 var dataString="type=SPF";
		$.ajax({
			type: "GET",
			url: "<?php echo e(url('get-pending-complain')); ?>",
			data: dataString,
			datatype: "json",
			beforeSend:function()
			{
				
			},
			success: function (res) {
				$(".complainTxnCount").html(res.report.complainTxnCount);					
			}
        })
}
function getBalanceRequest(){
  var dataString="type=BALANCE_REQUEST";
    $.ajax({
            type:"GET",
            url:"<?php echo e(url('get-balance-request')); ?>",
            data:dataString,
            datatype:"json",
          beforeSend:function(){

          },
          success:function(res){
           /*  $("#balanceTxnCount").html(res.report.balanceTxnCount);
            $("#balanceVolume").html(res.report.balanceVolume); */
          }
  });
}
</script>
 <?php if(Auth::user()->role_id == 1): ?>
	<script>
$(document).ready(function() {
	getRoleWiseBalance();
	getApiBalance("TRAMO");
	getApiBalance("CYBER");
	getApiBalance("A2Z");
});
		function getRoleWiseBalance()
		{
			var dataString="type=BALANCE_REQUEST";
			$.ajax({
					type:"GET",
					url:"<?php echo e(route('get-role-balance')); ?>",
					data:dataString,
					datatype:"json",
					beforeSend:function(){

					},
				  success:function(res){
					if(res.status==1)
					{
						//console.log(res.details)
						for (i in res.details) {
							$("#availableBalance_"+res.details[i].roleId).text(res.details[i].availableBalance)
							$("#memberCount_"+res.details[i].roleId).text(res.details[i].userCount)
						}
					}
				}
		  });
		}
		function getApiBalance(balanceOf)
		{
			var dataString="getBalanceOf="+balanceOf;
			$.ajax({
					type:"GET",
					url:"<?php echo e(route('get-api-balance')); ?>",
					data:dataString,
					datatype:"json",
					beforeSend:function(){

					},
				  success:function(res){
					if(res.status==1)
					{
						for (i in res.details) {
						  $("#"+i).text(res.details[i])
						}
					}
				}
		  });
		}
 
	</script>
<div>
	Bank Down List : <marquee style="color:red"><?php echo e(@$down_bank_list); ?></marquee>
</div>	 
<div class="row col-md-12">
  <div class="col-md-4">
     <table class="table">
		<tbody>       
			  <tr><td style="background-color:lightgreen">Total Success</td><td>[<span id="successTxnCount"> </span>] ₹ <a href="<?php echo e(url('/')); ?>/recharge-nework?fromdate=<?php echo e(date('d-m-Y')); ?>&todate=<?php echo e(date('d-m-Y')); ?>&searchOf=1"><span id="successTxnVolume"><span></a><i class="fa fa-spin fa-refresh" style="color:black" id="successSpiner"></i></td></tr>
			  <tr><td style="background-color:#ff0000">Total Fail</td><td>[<span id="failTxnCount"> </span>] ₹ <a href="<?php echo e(url('/')); ?>/recharge-nework?fromdate=<?php echo e(date('d-m-Y')); ?>&todate=<?php echo e(date('d-m-Y')); ?>&searchOf=2"><span id="failVolume"><span></a></td></tr>
			  <tr><td style="background-color:#ffff00">Total Pending</td><td>[<span id="pendingTxnCount"></span>] ₹ <a href="<?php echo e(url('/')); ?>/recharge-nework?searchOf=3"><span id="pendingVolume"><span></a></td></tr>
			  <tr><td style="background-color:#ff03d4">Total Refunded</td><td>[<span id="refundedTxnCount"> </span>] ₹ <a href="<?php echo e(url('/')); ?>/recharge-nework?fromdate=<?php echo e(date('d-m-Y')); ?>&todate=<?php echo e(date('d-m-Y')); ?>&searchOf=4"><span id="refundedVolume"><span></a></td></tr>
			<tr><td></td></tr>
			<tr><td style="background-color:#10f1f11c">Balance Request</td><td>[<span id="balanceTxnCount"> </span>] ₹ <a href="<?php echo e(url('payment-request-view')); ?>"> 
			<span id="balanceVolume"></span></a></td></tr>
			<tr><td style="background-color:#ff03d4">Hold Txn</td><td>[<span id="offLineTxnCount"> </span>] ₹ <a href="<?php echo e(url('/')); ?>/recharge-nework?searchOf=24"><span id="offLineVolume"><span></a></td></tr><!-- offline request -->
			  <!-- over all data -->
			<tr><td style="background-color:#ff03d4">Complain</td><td>[<span class="complainTxnCount"> </span>] ₹ <a href="<?php echo e(url('/')); ?>/view-complain?searchOf=3&export=SEARCH"><span class="complainTxnCount"><span></a></td></tr>
			<tr><td></td></tr>
			<tr><td style="background-color:pink">Admin Balance</td><td>₹ <?php echo e(number_format(Auth::user()->balance->user_balance,2)); ?></td></tr>
		   
			  <tr><td style="background-color:#bfbfda">Master Dist Outlet</td><td><a href="<?php echo e(url('admin/master-distributor')); ?>">[<span style="color:red" id="memberCount_3"></span>] ₹ <span id="availableBalance_3"></span></a></td></tr>
			  <tr><td style="background-color:#cfdcdc">Dist Outlet</td><td><a href="<?php echo e(url('admin/distributor')); ?>">[<span style="color:red" id="memberCount_4"></span>] ₹ <span id="availableBalance_4"></span></a></td></tr>
			  <tr><td style="background-color:#98f598">Retailer Outlet</td><td><a href="<?php echo e(url('admin/retailer')); ?>">[<span style="color:red" id="memberCount_5"></span>] ₹ <span id="availableBalance_5"></span></a></td></tr>
			   <tr><td style="background-color:#98f598">Api Outlet</td><td><a href="<?php echo e(url('admin/retailer')); ?>"> [<span style="color:red" id="memberCount_7"></span>]₹ <span id="availableBalance_7"></span></a></td></tr>
		   <!-- <tr><td>Total Business Yesterday</td></tr>-->
		</tbody>
     </table>
  </div>
	<div class="col-md-8">
     <table class="table">
        <thead>
          <th class="text-center">Apiname</th>
          <th>Balance</th>
        </thead>    
		<tbody>
		   <tr><td class="text-center">A2z Wallet:</td><td><span id="TRAMOBalance"></span></td></tr>
		   <tr><td class="text-center">DMT 1:</td><td><span id="CYBERBalance"></span></td></tr>
		   <tr><td class="text-center">A2ZSuvidhaa:</td><span id="A2ZBalance"></span></tr>
		</tbody>
     </table>
	</div>
</div>   
     
<?php endif; ?> 
<?php if(Auth::user()->role_id == 4): ?>


     
     <div class="row">
        <div class="col-12 col-sm-6 col-md-3">
            <div class="info-box bg-info">
              <div class="info-box-content">
               <p class="heading">Payments</p>
         <p class="title">Transfered : <?php echo e(@$transferedBalance); ?></p>
         <p class="title">Received: <?php echo e(@$receivedBalance); ?></p>
			 
              </div>
            </div>
          </div>
          <div class="col-12 col-sm-6 col-md-3">
            <div class="info-box bg-success">
              <div class="info-box-content">
                <p class="heading">Payments</p>
               <p class="title">Today Network : <?php echo e(number_format(@$todayNetworkAmount)); ?></p>
               <p class="title">Monthly Network : <?php echo e(number_format(@$monthNetworkAmount)); ?></p>
				   
              </div>
            </div>
          </div>
          <div class="clearfix hidden-md-up"></div>
          <div class="col-12 col-sm-6 col-md-3">
            <div class="info-box mb-3" style="background-color:yellow">
              <span class="info-box-icon elevation-1" style="background-color:yellow"><i class="fa fa-money"></i></span>
              <div class="info-box-content">
                <p class="heading">Activation Kit</p><p class="title"><?php echo e(number_format($total_profit,2)); ?></p>
              </div>             
            </div>           
          </div>        
          <div class="col-12 col-sm-6 col-md-3">
            <div class="info-box mb-3 bg-warning">
              <span class="info-box-icon bg-warning elevation-1"><i class="fa fa-money"></i></span>
              <div class="info-box-content">
              <p class="heading">Purchase</p> <p class="title"><?php echo e(number_format(@$purchase_balance,2)); ?></p>
              </div>           
            </div>           
          </div>         		
			<div class="slidetwo"> 
				<div><img src="<?php echo e(url('newlog/images/IMAG2.jpg')); ?>" class="img-responsive"></div>
				
			</div> 
		
      </div>
   
<script type="text/javascript">
  setInterval(function() {
  $('.slidetwo > div:first')
    .fadeOut(2000)
    .next()
    .fadeIn(2000)
    .end()
    .appendTo('.slidetwo');
}, 8000)
</script>

<style type="text/css">
.slidetwo{
  position: absolute;
  margin-left: 45%; 
}
</style>  
 <?php endif; ?>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('admin.layouts.templatetable', \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>