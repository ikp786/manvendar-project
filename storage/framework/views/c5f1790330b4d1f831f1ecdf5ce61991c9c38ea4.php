<?php if(in_array(Auth::user()->role->id,array(1))): ?>
    
    <?php $__env->startSection('title','User Docs'); ?>
    <?php $__env->startSection('content'); ?>
		<style>
	.custom-btn-style15{
	width: 105px !important;
    background-color: #ffbd4a !important;
    border: 1px solid #ffbd4a !important;
    height: 33px;
    text-align: center;
    padding: 6px 12px;
    color: white;
    border-radius: 22px;
}
.custom-btn-style16{
	width: 105px !important;
    background-color: #f05050 !important;
    border: 1px solid #f05050 !important;
    height: 33px;
    text-align: center;
    padding: 6px 12px;
    color: white;
    border-radius: 22px;
}
.custom-btn-style17{
	width: 105px !important;
    background-color: #81c868 !important;
    border: 1px solid #81c868 !important;
    height: 33px;
    text-align: center;
    padding: 6px 12px;
    color: white;
    border-radius: 22px;
}
.custom-btn-style18{
	width: 105px !important;
    background-color: #4800d47a !important;
    border: 1px solid #4800d47a !important;
    height: 33px;
    text-align: center;
    padding: 6px 12px;
    color: white;
    border-radius: 22px;
}
input.largerCheckbox
{
	width: 40px;
	height: 40px;
}
.span_message{
	color:green;
}

	</style>
	<script src="https://code.jquery.com/jquery-1.9.1.min.js"></script>
	<script type="text/javascript">
	$( document ).ready(function() {
			$('input[type="checkbox"]').change(function(){
							this.value = (Number(this.checked));
							 
						})
						
						$('.largerCheckbox').change(function(e)
						{
							this.value = (Number(this.checked));
							var id = $(this).attr("id");
							var val=$(this).val();
							var arr = id.split('_');
							var user_id = arr[1];
							var remark=$("#remark_"+user_id).val();
							/* var id = $(this).attr(""); */
							/* alert(id+' '+val+' '+user_id); */
							$.ajaxSetup({
										headers: {
											'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
										}
									})
									 
							var dataString = 'user_id=' + user_id+'&message='+remark+'&res_value='+val;
								$.ajax({
								type: "put",
								url: "<?php echo e(url('aDMiN/uSeR-reSTriCtIoN')); ?>/"+user_id,
								data: dataString,
								success: function (res) 
								{
									if(res.status == 1)
										$("#message_"+user_id).text(res.message);
									else 
										$("#message_"+user_id).text(res.message);
								}
							})
						})
		});
		
		function showRejectModel(id)
		{
			$("#user_reject_id").val(id);
			 $("#myModal").modal("toggle");
		}
		function rejectDocuments(id)
		{
			if(confirm("Are you want to reject Document of User ?"))
			{
				var user_id=id;
				var remark=$("#remark_"+id).val();
				$.ajaxSetup({
					headers: {
						'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
					}
				})
				 var dataString = 'user_id=' + user_id+'&remark='+remark;
						$.ajax({
							type: "put",
							url: "<?php echo e(url('admin/reject-doc')); ?>/"+user_id,
							data: dataString,
							success: function (result) {
								
								$("#user_reject_id").val('');
								$("#remark").val('');
								$("#save-btn-id").prop("disabled", false);
								if(result.status="Success"){
									alert(result.message);
									$("#doc_verify_"+user_id).text("Reject");
									$("#doc_verify_"+user_id).prop("class", "custom-btn-style16" );
									$( "#myModal" ).modal('hide');
								}
								else{
									$("#result_message").prop('class',"alert alert-danger");
									$("#result_message").text(result.message);
								
								}
								

						}
					})
			}else
				$("#save-btn-id").prop("disabled", false);
			
		}
		function approveDocument(id)
		{
			if(confirm("Are you want to approve Document of User ?"))
			{
				var remark=$("#remark_"+id).val();
				$.ajaxSetup({
					headers: {
						'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
					}
				})
				 var dataString = 'user_id=' + id +'&remark='+remark;
						$.ajax({
							type: "put",
							url: "<?php echo e(url('admin/approve-doc')); ?>/"+id,
							data: dataString,
							success: function (result) {
									if(result.status="Success"){
										alert(result.message);
										$("#doc_verify_"+id).text("Approved");
										$("#doc_verify_"+id).prop("class", "custom-btn-style17" );

										}
								}
					})
			}else
				$("#save-btn-id").prop("disabled", false);
		}
		function incompleteDocument(id)
		{
			if(confirm("Are you want to change document status of User ?"))
			{
				var profile_cb=(Number($("#profile_cb_"+id).val()))?1:0;
				var shop_cb=(Number($("#shop_cb_"+id).val()))?2:0;
				var pan_cb=(Number($("#pan_cb_"+id).val()))?3:0;
				var aadhaar_cb=(Number($("#aadhaar_cb_"+id).val()))?4:0;
				var cheque_cb=(Number($("#cheque_cb_"+id).val()))?5:0;
				var form_cb=(Number($("#form_cb_"+id).val()))?6:0;
				var aadhaar_back_cb=(Number($("#aadhaar_back_cb_"+id).val()))?7:0;
				
				var remark=$("#remark_"+id).val();
				var doc_list=[profile_cb,shop_cb,pan_cb,aadhaar_cb,cheque_cb,form_cb,aadhaar_back_cb];
				$.ajaxSetup({
					headers: {
						'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
					}
				})
				 var dataString = 'user_id=' + id +'&doc_list=' + doc_list+'&remark='+remark;
						$.ajax({
							type: "put",
							url: "<?php echo e(url('admin/incomplete-doc')); ?>/"+id,
							data: dataString,
							success: function (result) {
									if(result.status="Success"){
										$("#doc_verify_"+id).text("Incomplete");
										$("#doc_verify_"+id).prop("class", "custom-btn-style18" );

										}
								}
					})
			}else
				$("#save-btn-id").prop("disabled", false);
		}
		function showImage(url)
		{
			$("#imageDisplay").modal("toggle");
			$('#image').attr('src',url);
			
		}
		
	</script>
	
       
        <!--Basic Columns-->
        <!--===================================================-->


        <!--===================================================-->
        <div class="row">
            <div class="col-sm-12">
                <div class="card-box">
                    <div class="panel panel-default">
                        <div class="panel-body">
						<h4 class="page-title" style="color: black; font-size: 130%px;"><?php echo e('User Documents'); ?></h4>
                            <div class="col-md-12">
                                <div class="col-md-10">
									 <form method="get" action="<?php echo e(url('/admin/user-lists')); ?>" class="form-inline" role="form">
										<div class="form-group">
											<label class="sr-only" for="payid">Number</label>
											<input name="lm_id" type="text" class="form-control" placeholder="LM ID">
										</div>
										<div class="form-group">
											<?php echo e(Form::select('remark', $remark_lists, null,array('class' => 'form-control','id' => 'remark_id'))); ?>

										</div>
                                    <button type="submit"
                                            class="btn btn-success waves-effect waves-light m-l-10 btn-md"><span
                                                class="glyphicon glyphicon-find"></span>Search
                                    </button>
									<a href="<?php echo e(url('/admin/user-lists')); ?>"><button type="button" class="btn btn-primary waves-effect waves-light m-l-10 btn-md">Reset</button></a>
                                </form>
                                </div>
                            </div>
                        </div>
					</div>
                   <br>
                        <!-- <div class="container"> -->
						<div style="overflow-y: scroll; max-height:430px"><!--<div style="overflow-x:auto;">add this style -->
					<table class="table table-bordered table-striped"  id="demo-custom-toolbar" data-toggle="table" data-search="true"
                       data-show-toggle="true"
                       data-show-columns="true"
                       data-page-list="[10, 20, 30]"
                       data-page-size="80"
                       data-pagination="true" data-show-pagination-switch="true">
					<thead style="background-color:gray">
						<tr>
							<th style="width: 1px;"> ID </th>
						  <th style="width: 1px;">Name </th>
						  <th style="width: 1px;">Mobile </th>
						  <th style="width: 1px;">Company Id</th>
						  <th style="width: 1px;">Type</th>
						  <th  >Photo Img</th>
						  <th  >shop Img</th>
						  <th >Pan Img</th>
						  <th  >Aadhaar Img</th>
						  <th  >Aadhaar Back Img</th>
						  <th  >cheque Img</th>
						  <th  >form Img</th>
						  <th >Doc Status</th>
						 <!-- <th >Restrict</th> -->
						  <th >Write Remark</th>
						  <th style="width: 167px;!important">Action</th>
						</tr>
					</thead>

					<tbody>
					<?php $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
						<tr>
							<td><?php echo e($value->id); ?></td>
							<td><?php echo e($value->name); ?></td>
							<td><?php echo e($value->mobile); ?></td>
							<td> <?php echo e($value->company_id); ?></td>
							<td> <?php echo e($value->role->role_title); ?></td>
							 <?php 
								$doc_list_str=$value->profile->doc_list;
								$doc_list =explode(",",$doc_list_str);
								//print_r($doc_list);die;
								?>
							<?php if(Auth::user()->role_id==1): ?>
							<td> 
								<?php if($value->profile->profile_picture !='no.jpg'): ?>
									<a href="<?php echo e(url('')); ?>/user-uploaded-files/<?php echo e($value->id); ?>/<?php echo e($value->profile->profile_picture); ?>" download="<?php echo e($value->profile->profile_picture); ?>" class="btn btn-primary" role="button" title="Download"><span class="glyphicon glyphicon-download-alt"></span></a>
									<button  class="btn btn-info"  onclick="showImage('<?php echo e(url('')); ?>/user-uploaded-files/<?php echo e($value->id); ?>/<?php echo e($value->profile->profile_picture); ?>')">
									<span class="glyphicon glyphicon-eye-open"></span>
								</button><input type="checkbox" name="profile_cb_<?php echo e($value->id); ?>" value=<?php echo e((in_array(1,$doc_list))?1 : 0); ?> id="profile_cb_<?php echo e($value->id); ?>" <?php echo e((in_array(1,$doc_list))? 'checked': ''); ?>><br>
								<?php endif; ?>
							</td>
							<td> 
							  <?php if($value->profile->shop_image !=''): ?>
								  <a href="<?php echo e(url('')); ?>/user-uploaded-files/<?php echo e($value->id); ?>/<?php echo e($value->profile->shop_image); ?>" download="<?php echo e($value->profile->shop_image); ?>" class="btn btn-primary" role="button" title="Download"><span class="glyphicon glyphicon-download-alt"></span><i class="fa fa-download"></i> </a>
							   <button  class="btn btn-info"  onclick="showImage('<?php echo e(url('')); ?>/user-uploaded-files/<?php echo e($value->id); ?>/<?php echo e($value->profile->shop_image); ?>')" ><span class="glyphicon glyphicon-eye-open"></span><i class="fa fa-eye" style="color:black"></i> </button>
							   <input type="checkbox" name="shop_cb_<?php echo e($value->id); ?>"  id="shop_cb_<?php echo e($value->id); ?>" value=<?php echo e((in_array(2,$doc_list))?1 : 0); ?> id="profile_cb_<?php echo e($value->id); ?>" <?php echo e((in_array(2,$doc_list))? 'checked': ''); ?> /><br>
							  <?php endif; ?>
							</td>
							<td>
							   <?php if($value->profile->pan_card_image !=''): ?>
								   <a href="<?php echo e(url('')); ?>/user-uploaded-files/<?php echo e($value->id); ?>/<?php echo e($value->profile->pan_card_image); ?>" download="<?php echo e($value->profile->pan_card_image); ?>" class="btn btn-primary" role="button"><span class="glyphicon glyphicon-download-alt"></span><i class="fa fa-download"></i></a> 
									<button  class="btn btn-info"  onclick="showImage('<?php echo e(url('')); ?>/user-uploaded-files/<?php echo e($value->id); ?>/<?php echo e($value->profile->pan_card_image); ?>')" ><span class="glyphicon glyphicon-eye-open"></span><i class="fa fa-eye" style="color:black"></i> </button>
									<input type="checkbox" name="pan_cb_<?php echo e($value->id); ?>" id="pan_cb_<?php echo e($value->id); ?>" value=<?php echo e((in_array(3,$doc_list))?1 : 0); ?> id="profile_cb_<?php echo e($value->id); ?>" <?php echo e((in_array(3,$doc_list))? 'checked': ''); ?>><br><?php endif; ?>
							</td>
							<td>
							   <?php if($value->profile->aadhaar_card_image !=''): ?>
								   <a href="<?php echo e(url('')); ?>/user-uploaded-files/<?php echo e($value->id); ?>/<?php echo e($value->profile->aadhaar_card_image); ?>" download="<?php echo e($value->profile->aadhaar_card_image); ?>" class="btn btn-primary" role="button"><span class="glyphicon glyphicon-download-alt"></span><i class="fa fa-download"></i> </a>  
									<button  class="btn btn-info"  onclick="showImage('<?php echo e(url('')); ?>/user-uploaded-files/<?php echo e($value->id); ?>/<?php echo e($value->profile->aadhaar_card_image); ?>')" ><span class="glyphicon glyphicon-eye-open"></span><i class="fa fa-eye" style="color:black"></i> </button>
									<input type="checkbox" name="aadhaar_cb_<?php echo e($value->id); ?>"  id="aadhaar_cb_<?php echo e($value->id); ?>" value=<?php echo e((in_array(4,$doc_list))?1 : 0); ?> id="profile_cb_<?php echo e($value->id); ?>" <?php echo e((in_array(4,$doc_list))? 'checked': ''); ?> /><br>
								<?php endif; ?>
							</td>
							<td>
							   <?php if($value->profile->aadhaar_img_back !=''): ?>
								   <a href="<?php echo e(url('')); ?>/user-uploaded-files/<?php echo e($value->id); ?>/<?php echo e($value->profile->aadhaar_img_back); ?>" download="<?php echo e($value->profile->aadhaar_img_back); ?>" class="btn btn-primary" role="button"><span class="glyphicon glyphicon-download-alt"></span><i class="fa fa-download"></i> </a>  
									<button  class="btn btn-info"  onclick="showImage('<?php echo e(url('')); ?>/user-uploaded-files/<?php echo e($value->id); ?>/<?php echo e($value->profile->aadhaar_img_back); ?>')" ><span class="glyphicon glyphicon-eye-open"></span><i class="fa fa-eye" style="color:black"></i> </button>
									<input type="checkbox" name="aadhaar_back_cb_<?php echo e($value->id); ?>"  id="aadhaar_back_cb_<?php echo e($value->id); ?>" value=<?php echo e((in_array(7,$doc_list))?1 : 0); ?> id="profile_cb_<?php echo e($value->id); ?>" <?php echo e((in_array(7,$doc_list))? 'checked': ''); ?> /><br>
								<?php endif; ?>
							</td>
							<td>
								<?php if($value->profile->cheque_image !=''): ?>
								   <a href="<?php echo e(url('')); ?>/user-uploaded-files/<?php echo e($value->id); ?>/<?php echo e($value->profile->cheque_image); ?>" download="<?php echo e($value->profile->cheque_image); ?>" class="btn btn-primary" role="button"> <span class="glyphicon glyphicon-download"></span><i class="fa fa-download"></i>  </a>
									<button  class="btn btn-info"  onclick="showImage('<?php echo e(url('')); ?>/user-uploaded-files/<?php echo e($value->id); ?>/<?php echo e($value->profile->cheque_image); ?>')" ><span class="glyphicon glyphicon-eye-open"></span><i class="fa fa-eye" style="color:black"></i>  </button>
									<input type="checkbox" name="cheque_cb_<?php echo e($value->id); ?>" id="cheque_cb_<?php echo e($value->id); ?>" value=<?php echo e((in_array(5,$doc_list))?1 : 0); ?> id="profile_cb_<?php echo e($value->id); ?>" <?php echo e((in_array(5,$doc_list))? 'checked': ''); ?> /><br>
								<?php endif; ?>
							</td>
							<td>
								<?php if($value->profile->form_image !=''): ?>
									<a href="<?php echo e(url('')); ?>/user-uploaded-files/<?php echo e($value->id); ?>/<?php echo e($value->profile->form_image); ?>" download="<?php echo e($value->profile->form_image); ?>" class="btn btn-primary" role="button"><span class="glyphicon glyphicon-download-alt"></span><i class="fa fa-download"></i> </a>
									<button  class="btn btn-info"  onclick="showImage('<?php echo e(url('')); ?>/user-uploaded-files/<?php echo e($value->id); ?>/<?php echo e($value->profile->form_image); ?>')"><span class="glyphicon glyphicon-eye-open"></span><i class="fa fa-eye" style="color:black"></i>   </button>
									<input type="checkbox" name="form_cb_<?php echo e($value->id); ?>" id="form_cb_<?php echo e($value->id); ?>" value=<?php echo e((in_array(6,$doc_list))?1 : 0); ?> id="profile_cb_<?php echo e($value->id); ?>" <?php echo e((in_array(6,$doc_list))? 'checked': ''); ?> />
							<br>
							<?php endif; ?>
							</td>
							
							  <td><div class="custom-btn-style<?php echo e($value->profile->remark_id); ?>" id="doc_verify_<?php echo e($value->id); ?>" ><?php echo e(($value->profile->remark->remark)); ?></div></td>
							<!--  <td>
							  <input type="checkbox" class="largerCheckbox" name="checkBox" id="restrictionuser_<?php echo e($value->id); ?>" value="<?php echo e($value->profile->res_agent); ?>" <?php echo e(($value->profile->res_agent) ? 'checked': ''); ?> />
							  <span id="message_<?php echo e($value->id); ?>" class="span_message"></span></td> -->
							   <td>
							   
							   <textarea rows="2" cols="15" class="form control" id="remark_<?php echo e($value->id); ?>"><?php echo e($value->profile->message); ?></textarea>
							  
							   </td>
							  <?php endif; ?> 
							  <td>
							  
								<button type="button" class="btn btn-info btn-sl" onclick="rejectDocuments(<?php echo e($value->id); ?>)";><span class="fa fa-circle" style="color: red;" title="Cancel Doc" ></span></button>  
								<button type="button"  class="btn"onclick="incompleteDocument(<?php echo e($value->id); ?>)"><span class="fa fa-warning" style="color: blue;" title="Incomplete Doc"></span></button> 
								<button type="button"  class="btn"onclick="approveDocument(<?php echo e($value->id); ?>)"><span class="fa fa-check" style="color: green;" title="Approve Doc"></span></button>
							</td>
							  
						</tr>  
					  <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
					</tbody>
					</table></div>
					<?php echo e($users->links()); ?> 
                        <!-- </div> -->
                    </div>
                  </div>

            </div>
			<meta name="_token" content="<?php echo csrf_token(); ?>"/>
			
			<div id="myModal" class="modal fade" role="dialog">
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Document Reject</h4>
      </div>
      <div class="modal-body">
					
                    <div id="frmTasks" name="frmTasks" class="form-horizontal">
					<div id="result_message" style="text-align: center;"></div>
                        <div class="form-group">
                            <label for="inputTask" class="col-sm-3 control-label">Remark</label>
                            <div class="col-sm-9">
                                <input type="text" id="remark" id="newbalance" placeholder="Enter reason of Rejection " class="form-control">
                            </div>
                        </div>
                    </div>
                </div>
     
      <div class="modal-footer">
        <button onclick="this.disabled=true;rejectDocuments()" type="button" class="btn btn-info waves-effect waves-light"
                            id="save-btn-id"
                            value="add">Save
                    </button>
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        <input type="hidden" id="user_reject_id" value=""/>
      </div>
    </div>

  </div>
</div>
<div id="imageDisplay" class="modal fade" role="dialog">
  <div class="modal-dialog" style="width: 50%; height: 50%;">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Image</h4>
      </div>
      <div class="modal-body">
					
                    <img src = "" id = "image" style="width: 100%; height: 100%;"/>
      </div>
     
      <div class="modal-footer">
        
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        <input type="hidden" id="user_reject_id" value=""/>
      </div>
    </div>

  </div>
</div>
 <!--        </div> -->

    <?php $__env->stopSection(); ?>
    <?php endif; ?>
<?php echo $__env->make('admin.layouts.templatetable', \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>