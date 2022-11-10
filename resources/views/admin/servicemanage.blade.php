@extends('admin.layouts.templatetable')

@section('content')


    <!-- Page-Title -->
    <div class="row">
        <div class="col-sm-12">
            <div class="col-lg-6 col-md-6">
                <h3 class="page-title" style="color: black;">{{  'Service Management' }}</h3>
                
            </div>
            <div class="col-lg-6 col-md-6 pull-right">
                <div class="pull-right">
                    
                </div>
            </div>
        </div>
    </div>
<div class="row">
    <div class="col-sm-12">
        <div class="card-box">
            <table  data-toggle="table" data-toolbar="#demo-delete-row" data-search="true" data-page-list="[20, 10, 20]"
                   data-page-size="10" border='1' class="table table-striped table-bordered table-hover">
                <thead>
                    <tr>
                        <th data-field="id" data-sortable="true">ID</th>
                        <th data-field="name" data-sortable="true">Name</th>
						  <th data-field="category" data-sortable="true">category</th>
							<th data-field="saral" data-sortable="true">Message</th>         
							<th data-field="saral" data-sortable="true">Service Start/Stop</th>         
							<!--<th data-field="saral" data-sortable="true">Service Online/Offline</th>-->         
                    </tr>
                </thead>
                <tbody>
                	@foreach($services as $service)
	                	<tr>
	                		<td>{{$service->id}}</td>
	                		<td>{{$service->name}}</td>
							<td>{{$service->category}}</td>
							<td> <textarea class="form-control" rows="2" id="message_{{$service->id}}" onfocusout="update({{$service->id}})">{{$service->message}}</textarea></td>
	                		<td>
								{{ Form::select('status_id', ['1' => 'Start', '0' => 'Stop'],  $service->status_id, ['class'=>'form-control','style'=>($service->status_id) ? "border-color:green" : "border-color:red" ,'id'=>'status_id_'.$service->id,'onChange'=>"serviceOnOff($service->id,'status_id','RECHARGE')"]) }}
								<span id="messagestatus_id_{{$service->id}}"></span>
							</td>
							<!--<td>
								{{ Form::select('is_online_service', ['1' => 'Online', '0' => 'Offline'],  $service->is_online_service, ['class'=>'form-control','style'=>($service->is_online_service) ? "border-color:green" : "border-color:red" ,'id'=>'is_online_service_'.$service->id,'onChange'=>"serviceOnOff($service->id,'is_online_service','RECHARGE')"]) }}
													<span id="messageis_online_service_{{$service->id}}"></span>
							</td>  -->
							
	                	</tr>
                	@endforeach

				</tbody>
            </table>
        </div>
    </div>
</div>
<div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" style="display: none">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
        <div class="modal-body">
            <div class="form-group">
                <label for="inputTask" class="control-label">Name<span style="color:red"> *</span></label> 
                <input type="text" class="col-md-3 form-control" id="name" name="name" placeholder="Full Name" value="" required="required">    
            </div>
            <div class="form-group">
                <label for="inputEmail3" class="control-label">User Status</label>
                <select id="status_id" name="status_id" class="col-md-3 form-control">
                    <option value="1">Active</option>
                    <option value="0">Disabled</option>
                </select>
            </div>    
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-primary"  id="btn-save" value="add" onclick="savedata()">Save </button>
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        </div>
    </div>
  </div>
</div>
<meta name="_token" content="{!! csrf_token() !!}"/>
<script type="text/javascript">

	$('input[type="checkbox"]').change(function(){
		var $this = $(this);
		var status = this.value = (Number(this.checked));
		var res= $this.attr("id")
		var checkbox_id = res.split("_")[1];
		var message = $("#message_"+checkbox_id).val();
		var token = $("input[name=_token]").val();
		var dataString = 'status_id=' + status + '&id=' + checkbox_id+'&message=' + message;;
		$.ajax({
			url:"{{url('edit-services')}}",
			type:'get',
			data:dataString,
			success: function (msg) {
				alert(msg.message)
			}
		})	
	}); 

	function update(id)
	{
		var message = $("#message_"+id).val();	
		var dataString = 'message=' + message;
		if(confirm('Are your sure want to update the message of Service'))
			{
				$.ajaxSetup({
					headers: {
						'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
					}
				})
				$.ajax({
					type: "post",
					url: "{{url('update-services')}}/"+id,
					dataType:"json",
					  data:dataString,
					success: function (data) {
						alert(data.message)
					}
				});
			}	
	}

	function serviceOnOff(apiId,serviceVar,serviceType)
	{
		var newStatus = $("#"+serviceVar+"_"+apiId).val();
		if(confirm('Are your sure want to update'))
		{
			
			 var dataString = 'apiId=' + apiId +'&serviceVar='+serviceVar+'&serviceType='+serviceType+'&newStatus='+newStatus;
			 $.ajaxSetup({
				headers: {
					'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
				}
			})
			 $.ajax({
				type: "put",
				url: "{{url('edit-services')}}/"+apiId,
				data: dataString,
				dataType:"json",
				success: function (data) {
					$("#message"+serviceVar+"_"+apiId).html(data.message);
				}
			});
		}
		else
		{
			status = (newStatus) ? 0 : 1;
			$("#"+serviceVar+"_"+apiId).val(status);
		}
	}
</script>
@endsection