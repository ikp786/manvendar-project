<?php $__env->startSection('content'); ?>   

<?php echo $__env->make('layouts.submenuheader', \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>   

<script>
    $(document).ready(function() {
    //alert("Hello");
        getLMS();
        getCMS();
        getTS();
    getPendingComlain();
    });
    function getLMS(){
        var dataString="type=LMS";
        $.ajax({
            type: "GET",
            url: "<?php echo e(url('get-txn-sale')); ?>",
            data: dataString,
            datatype: "json",
            beforeSend:function()
            {

            },
            success: function (res) {

                $("#LMSVolume").html(res.totalVolume);
                $("#LMSCount").html(res.txnCount);
            }
        })
    }

    function getTS(){
        var dataString="type=TS";
        $.ajax({
            type: "GET",
            url: "<?php echo e(url('get-txn-sale')); ?>",
            data: dataString,
            datatype: "json",
            beforeSend:function()
            {

            },
            success: function (res) {

                $("#TSVolume").html(res.totalVolume);
                $("#TSCount").html(res.txnCount);
            }
        })
    }

    function getCMS(){
        var dataString="type=CMS";
        $.ajax({
            type: "GET",
            url: "<?php echo e(url('get-txn-sale')); ?>",
            data: dataString,
            datatype: "json",
            beforeSend:function()
            {

            },
            success: function (res) {

                $("#CMSVolume").html(res.totalVolume);
                $("#CMSCount").html(res.txnCount);
            }
        })
    }
</script>
<style type="text/css">
   
	.slideshow > div {
	  position: absolute;  
	}

	.slidetwo > div {
	  position: absolute;	  
	}
	
	.img-responsive
	{
		display: block;
		max-width: 100%;
		height: auto;
		position:fixed;
	}
	.customeSlideSize{
		height:470px;width:600px;
	}
		
	.Success{
		background-color:green;
	}
	.Failure{
		background-color:red;
	}
	.Pending{
		background-color:yellow;
	} 
	.Refunded{
		background-color: skyblue;
		}
	.RefundSuccess{
		background-color:#23fdf3;
	}
	.Successfully Submitted{
		background-color: yellowgreen ;	  
	}
		
</style>

	    <div class="row">

        
        <div class="col-md-2 col-sm-6 col-xs-12 myContainer">
            <div class="info-box" style="text-align: center;background-color: #f4f4f4;align-content: space-between;">
                <span class="info-box-text" style="color: rgba(76,63,49,0.94);padding-top: 5px;">Last Month Sales</span>
                <span class="info-box-number" style="color:rgba(76,63,49,0.94)">Amount : <span id="LMSVolume"></span></span>
                <span class="info-box-text" style="color:rgba(76,63,49,0.94);margin-top: 10px">Count : <span id="LMSCount"></span></span>
            </div>
        </div>
        <div class="col-md-2 col-sm-6 col-xs-12 myContainer">
            <div class="info-box" style="text-align: center;background-color: #f4f4f4;
			align-content: space-between;">
                <span class="info-box-text" style="color: rgba(76,63,49,0.94);padding-top: 5px;">This Month Sales</span>
                <span class="info-box-number" style="color:rgba(76,63,49,0.94)">Amount : <span id="CMSVolume"></span></span>
                <span class="info-box-text" style="color:rgba(76,63,49,0.94);margin-top: 10px">Count : <span id="CMSCount"></span></span>
            </div>
        </div>
		
        <div class="col-md-2 col-sm-6 col-xs-12 myContainer">
            <div class="info-box" style="text-align: center;background-color: #f4f4f4;
			align-content: space-between;">
                <span class="info-box-text" style="color: rgba(76,63,49,0.94);padding-top: 5px;">Today Sales</span>
                <span class="info-box-number" style="color:rgba(76,63,49,0.94)">Amount : <span id="TSVolume"></span></span>
                <span class="info-box-text" style="color:rgba(76,63,49,0.94);margin-top: 10px">Count : <span id="TSCount"></span></span>
            </div>
        </div>



        
     

             <?php $__currentLoopData = $holidays; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key=>$holiday): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>

            <div class="col-md-2 col-sm-6 col-xs-12 myContainer">
                <div class="info-box" style="text-align: center;background-color: #ffecbe;
			align-content: space-between;">
                    <span class="info-box-text" style="color: rgba(76,63,49,0.94);padding-top: 5px;">Holiday <?php echo e(++$key); ?></span>
                    <span class="info-box-number" style="color:rgba(76,63,49,0.94)"><?php echo e($holiday->name); ?></span>
                    <span class="info-box-text" style="color:rgba(76,63,49,0.94);margin-top: 10px"><?php echo e(date("d-m-Y",strtotime($holiday->holiday_date))); ?></span>
                </div>
            </div>

            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>


    </div>
<div>
	Bank Down List : <marquee style="color:red"><?php echo e(@$down_bank_list); ?></marquee>
</div>
<div class="row">
	<div class="col-md-7">	
		<table class="table table-bordered">
			<thead style="color:white;background-color:#2c588e;">
				<th> Date & Time</th>
				<th> Operator</th>
				<th> Mob Number</th>
				<th> Amount</th>
				<th> Status</th>
			</thead>
			<tbody style="color:black">
				<?php $__currentLoopData = $reports; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $report): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
					<tr  style="line-height:0px;">	
					<td><?php echo e($report->created_at); ?></td>
					
					<td><?php echo e(($report->recharge_type==1) ? @$report->provider->provider_name :@$report->api->api_name); ?></td>
					<td> <?php if(in_array($report->api_id,array(2,9,3,4,5,16))): ?>
							<?php echo e($report->customer_number); ?>

						<?php else: ?>
							<?php echo e(@$report->number); ?>

					<?php endif; ?>
					</td>
					<td><?php echo e(($report->api_id==2)? $report->debit_charge : $report->amount); ?></td>
					<td class="<?php echo e($report->status->status); ?> info-box-number" ><h4><b><?php echo e(@$report->status->status); ?></b></h4></td>
					</tr>
				<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
			</tbody>
		</table>	
	</div>
	
	<div class="col-md-5">
		<div class="slidetwo" > 
			
			<div><img src="<?php echo e(url('newlog/images/IMAG2.jpg')); ?>" class="" style="height:410px"></div>
			<div><img src="<?php echo e(url('newlog/images/download.png')); ?>" class="" style="height:410px"></div>
			
						
		</div> 
	</div>
</div>

<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>