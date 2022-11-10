<?php $__env->startSection('content'); ?>
    <script>
    

        function updateBankStatus(id,statusId,fieldName)
        {
            var token = $("input[name=_token]").val();
            var dataString = 'id=' + id + '&currentBankStatus='+ statusId+ '&fieldName='+ fieldName;
			$.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                }
            })
            $.ajax({
                type: "post",
                url: "<?php echo e(url('make-bank-updown')); ?>",
                data: dataString,
				dataType:"json",
                success: function (data) {
                    $("#message_"+id).text(data.message);
                    
                }
            })
        }
		function checkCurrentBankStatus(id,bankSortName)
		{
			var token = $("input[name=_token]").val();
            var dataString = 'bankSortName='+ bankSortName+ '&accountNumber=025301595758';
			$.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                }
            })
            $.ajax({
                type: "get",
                url: "<?php echo e(url('tools/check-bank-status')); ?>",
                data: dataString,
				dataType:"json",
                success: function (res) {
					alert(JSON.stringify(res.details));
					console.log(res.details)
					$("#messageBankStatus_"+id).text();
                    
                }
            })
		}
        
        
        
    </script>
<style>
.switch {
  position: relative;
  display: inline-block;
  width: 60px;
  height: 34px;
}

.switch input { 
  opacity: 0;
  width: 0;
  height: 0;
}

.slider {
  position: absolute;
  cursor: pointer;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background-color: #ccc;
  -webkit-transition: .4s;
  transition: .4s;
}

.slider:before {
  position: absolute;
  content: "";
  height: 26px;
  width: 26px;
  left: 4px;
  bottom: 4px;
  background-color: white;
  -webkit-transition: .4s;
  transition: .4s;
}

input:checked + .slider {
  background-color: #2196F3;
}

input:focus + .slider {
  box-shadow: 0 0 1px #2196F3;
}

input:checked + .slider:before {
  -webkit-transform: translateX(26px);
  -ms-transform: translateX(26px);
  transform: translateX(26px);
}

/* Rounded sliders */
.slider.round {
  border-radius: 34px;
}

.slider.round:before {
  border-radius: 50%;
}
</style>
    <!-- Page-Title -->
    
        <div class="col-md-12">
            <div class="col-md-6">
                <h3 class="page-title" style="color:black;"><?php echo e('ALL Banking Management'); ?></h3>
               
            </div>
           
        </div>

    
   

    <!--Basic Columns-->
    <!--===================================================-->


    <!--===================================================-->
    <div class="row">
        <div class="col-sm-12">
                <div class="table-responsive"> 
                <table id="mytable" class="table table-bordred table-striped">
                    <thead>
                    <tr style="color:#115798;">
                        <th>ID</th>
                        <th>Bank Name</th>
                        <th>Bank Code</th>
                        <th>is imps txn Allowe</th>
                        <th>Check Current Status</th>
                        <th>From Down Time</th>
                        <th>Bank Status</th>
                        <th>Manual Bank Status</th>
					</tr>
                    </thead>
                    <tbody>
                    <?php $__currentLoopData = $bank_up_down; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $banks_up_down): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr>
                            <td><?php echo e($banks_up_down->id); ?></td>
                            <td><?php echo e($banks_up_down->bank_name); ?></td>
                            <td><?php echo e($banks_up_down->bank_code); ?></td>
                            <td><label class="checkbox-inline">
      <input type="checkbox" id="is_imps_txn_allow-<?php echo e($banks_up_down->id); ?>"value="<?php echo e($banks_up_down->is_imps_txn_allow); ?>" <?php echo e(($banks_up_down->is_imps_txn_allow) ?"checked":""); ?>>Yes
    </label></td>
                            <td style="text-align:center"><a href="javascript:void(0)" onClick="checkCurrentBankStatus(<?php echo e($banks_up_down->id); ?>,'<?php echo e($banks_up_down->bank_sort_name); ?>')">
          <span class="glyphicon glyphicon-refresh"></span>
        </a><span id="messageBankStatus_<?php echo e($banks_up_down->id); ?>"></span></td></td>
                            <td><?php echo e($banks_up_down->down_time); ?></td>
                            <td><?php echo e(Form::select('bank_status', ['1' => 'UP', '0' => 'Down'],  $banks_up_down->bank_status, ['class'=>'form-control','style'=>($banks_up_down->bank_status) ? "border-color:green" : "border-color:red" ,'id'=>'bank_status_'.$banks_up_down->id,'onChange'=>"updateBankStatus($banks_up_down->id,$banks_up_down->bank_status,'bank_status')"])); ?>

							<span id="message_<?php echo e($banks_up_down->id); ?>"></span></td>
							<td><?php echo e(Form::select('manual_status', ['1' => 'UP', '0' => 'Down'],  $banks_up_down->manual_status, ['class'=>'form-control','style'=>($banks_up_down->manual_status) ? "border-color:green" : "border-color:red" ,'id'=>'manual_status_'.$banks_up_down->id,'onChange'=>"updateBankStatus($banks_up_down->id,$banks_up_down->manual_status,'manual_status')"])); ?>

							<span id="message_<?php echo e($banks_up_down->id); ?>"></span></td>
                           
                            
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
   
    <meta name="_token" content="<?php echo csrf_token(); ?>"/>
	<script type="text/javascript">

 $('input[type="checkbox"]').change(function(){
	var $this = $(this);
	var status = this.value = (Number(this.checked));
	var res= $this.attr("id")
	var checkbox_id = res.split("-")[1];
	
	var token = $("input[name=_token]").val();
	var dataString = 'status_id=' + status + '&id=' + checkbox_id;
	$.ajax({
        url:"<?php echo e(url('tools/update-txn-allowed')); ?>",
        type:'get',
        data:dataString,
		success: function (msg) {
			alert(msg.message)
		}
	})
		
});
</script> 
<?php $__env->stopSection(); ?>
<?php echo $__env->make('admin.layouts.templatetable', \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>