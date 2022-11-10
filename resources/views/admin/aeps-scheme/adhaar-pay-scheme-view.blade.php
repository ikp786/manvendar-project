@extends('admin.layouts.templatetable')

@section('content')
<style>
	.div-space{
		padding-bottom: 1% !important;
	}
</style>
    <script>
        function add_record() {
            $('#btn-save').val("add");
            $('#frmTasks').trigger("reset");
            $("#con-close-modal").modal("toggle");
        }
        
        function savedata() {
            var url = "scheme-manage";
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                }
            })
            var formData = {
                scheme_name: $('#scheme_name').val(),
            }
           
            var state = $('#btn-save').val();

            var type = "POST"; 
            var task_id = $('#id').val();
            var my_url = url;

            if (state == "update") {
                type = "PUT"; 
                my_url += '/' + task_id;
            }
            $.ajax({
                type: type,
                url: my_url,
                data: formData,
                dataType: 'text',
                success: function (data) {
                    var obj = $.parseJSON(data);
                    if (obj.success == false) {
                        var obj1 = obj.errors;
                        //alert(obj1["provider_name"]);
                        var html = "";
                        for (var key in obj1)
                                //alert(obj1[key]);
                        {
                            html += "<li>" + obj1[key] + "</li>";
                        }
                        $("#name-error").show();
                        $("#name-error").html("<div class='alert alert-danger'><ul>" + html + "</ul></div>");
                    } else {
                        var html = "";
                        for (var key in obj) {
                            html += "<li>" + obj[key] + "</li>";
                        }
                        $("#name-error").show();
                        $("#name-error").html("<div class='alert alert-success'><ul>" + html + "</ul></div>");
                    }
                }

            });
        }
        function updateRecord(id) {
            var token = $("input[name=_token]").val();
            var dataString = 'id=' + id + '&_token=' + token;
            $.ajax({
                type: "post",
                url: "{{url('scheme-manage/view')}}",
                data: dataString,
                success: function (data) {
                    $('#id').val(data.id);
                    $('#scheme_name').val(data.scheme_name);
                    $('#btn-save').val("update");
                    $("#con-close-modal").modal("toggle");
                }
            })

        }
        function save_record() {

            $("#commfor").click();
           
        }
		function deleteRow(recordId)
		{
			var token = $("input[name=_token]").val();
			var wallet_scheme_id = $('#wallet_scheme_id').val();
			var schemeFor = $('#schemeFor').val();
			var dataString = 'wallet_scheme_id=' + wallet_scheme_id + '&_token=' + token+ '&schemeFor=' + schemeFor+ '&recordId=' + recordId;
            $.ajax({
                type: "post",
                url: "{{url('delete-row')}}",
                data: dataString,
				dataType:"json",
                success: function (resp) {
                    
                    $('#id').val(resp.id);
					alert(resp.message);
					location.reload();
                    
                }
            })
		}
    </script>
@include('admin.admin-subtab.companymaster-type')

<div class="col-lg-12 col-md-12 pull-right">
                <div class="col-lg-6">
					<div> Wallet Scheme Name  : {{@$schemeName}}</div>
				</div>
				<div class="col-lg-6">
					<div class="pull-right">
                    <button onclick="add_record()" id="demo-add-row" class="btn btn-outline-success btn sm"><i
                                class="fa fa-plus m-r-5"></i>Add New Row
                    </button>
                </div>
				</div>
				<div class="col-lg-6">
                    {{ Form::open(array('url' => '/updateAdhaayPayScheme', 'method' => 'POST','id' => 'commform')) }}
					<div class="pull-right">
					<button type="submit" id="commfor" class="btn btn-outline-info">Save All
					</button>
						<input type="hidden" name="wallet_scheme_id" id="wallet_scheme_id" value="{{$wallet_scheme_id}}"/>
						<input type="hidden" name="schemeFor"id="schemeFor"  value="{{$schemeFor}}"/>
					</div>
				</div>
            </div>
     <div class="panel-body">
    <div class="table table-responsive">
        <table border='1' class="table table-striped table-bordered table-hover" id="dataTables-example">
			<thead>
				<tr>
					<th>ID</th>
					<th>Min Amount</th>
					<th>Max Amount</th>
					<th>Agent charge Type</th>
					<th>Agent charge</th>
					<th>Agent Comm Type</th>
					<th>Agent Comm</th>
					<th>Dist Comm Type</th>
					<th>Dist Comm</th>
					<th>MD Comm Type</th>
					<th>MD Comm</th>
					<th>Admin Comm Type</th>
					<th>Admin Comm</th>
					<th>Action</th>
					<th>Is Error</th>
				</tr>
			</thead>
				<tbody>
				   @foreach($walletSchemeDetails as $value)
					<tr class="odd gradeX">
						
		<td>{{ @$value->id }}</td>
		<td>{{ @$value->min_amt }}</td>
		<td>{{ @$value->max_amt }}</td>
		<td>
			{{ Form::select($value->id.'[agent_charge_type]', ['0' => '%', '1' => 'Rs'],  $value->agent_charge_type, ['class'=>'form-control']) }}
		</td>
		<td>
			<input type="text" name="{{$value->id}}[agent_charge]"
				   value="{{ $value->agent_charge }}"
				   style="width:60px; text-align:center; margin-right:5px; height:34px;" class='form-control'/>
		</td>
		<td>
			{{ Form::select($value->id.'[agent_comm_type]', ['0' => '%', '1' => 'Rs'],  $value->agent_comm_type, ['class'=>'form-control']) }}
		</td>
		<td>
			<input type="text" name="{{$value->id}}[agent_comm]"
				   value="{{ $value->agent_comm }}"
				   style="width:60px; text-align:center; margin-right:5px; height:34px;" class='form-control'/>
		</td>
		<td>
			{{ Form::select($value->id.'[dist_comm_type]', ['0' => '%', '1' => 'Rs'],  $value->dist_comm_type, ['class'=>'form-control']) }}
		</td>
		<td>
			<input type="text" name="{{$value->id}}[dist_comm]"
				   value="{{ $value->dist_comm }}"
				   style="width:60px; text-align:center; margin-right:5px; height:34px;" class='form-control'/>
		</td>
		<td>
			{{ Form::select($value->id.'[md_comm_type]', ['0' => '%', '1' => 'Rs'],  $value->md_comm_type, ['class'=>'form-control']) }}
		</td>
		<td>
			<input type="text" name="{{$value->id}}[md_comm]"
				   value="{{ $value->md_comm }}"
				   style="width:60px; text-align:center; margin-right:5px; height:34px;" class='form-control'/>
		</td><td>
			{{ Form::select($value->id.'[admin_comm_type]', ['0' => '%', '1' => 'Rs'],  $value->admin_comm_type, ['class'=>'form-control']) }}
		</td>
		<td>
			<input type="text" name="{{$value->id}}[admin_comm]"
				   value="{{ $value->admin_comm }}"
				   style="width:60px; text-align:center; margin-right:5px; height:34px;" class='form-control'/>
		</td>
		<td>
			<button type="button" class="btn btn-outline-danger btn-sm" onClick="deleteRow({{$value->id}})"><i class="fa fa-trash " aria-hidden="true"></i></button>
		</td>		
		<td>
		<span style="color:Red">{{ ($value->is_error)?"Error":''}}</span>
		</td>
						
					</tr>
					@endforeach  
				</tbody>
			</table>
			{{ Form::close() }}
		</div>
	</div>
    
    <div id="con-close-modal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
         aria-hidden="true" style="display: none;">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                    <h4 class="modal-title">Scheme Editor</h4>
                </div>
                <div class="modal-body">
                    <form id="frmTasks" name="frmTasks" action="{{url('create-new-adhaar-pay-row')}}"class="form-horizontal" method="post">
                        {!! csrf_field() !!}
						<input type="hidden" name="wallet_scheme_id" value="{{$wallet_scheme_id}}"/>
                        <div class="div-space row">
                            <label for="inputTask" class="col-sm-5 control-label">Min Amount</label>
                            <div class="col-sm-7">
                                <input type="text" class="form-control has-error" id="min_amt" name="min_amt"
                                       placeholder="Min Amount" required>
                            </div>
                        </div>
						<div class="div-space row">
                            <label for="inputTask" class="col-sm-5 control-label">Max Amount</label>
                            <div class="col-sm-7">
                                <input type="text" class="form-control has-error" id="max_amt" name="max_amt"
                                       placeholder="Max Amount" required>
                            </div>
                        </div>
						<div class="div-space row">
                            <label for="inputTask" class="col-sm-5 control-label">Agent Charge Type</label>
                            <div class="col-sm-7">
                                {{ Form::select('agent_charge_type', ['0' => '%', '1' => 'Rs'],  null, ['class'=>'form-control']) }}
                            </div>
                        </div>
						<div class="div-space row">
                            <label for="inputTask" class="col-sm-5 control-label">Agent Charge</label>
                            <div class="col-sm-7">
                                <input type="text" class="form-control has-error" id="agent_charge" name="agent_charge"
                                       placeholder="Agent Charge"required>
                            </div>
                        </div>
						<div class="div-space row">
                            <label for="inputTask" class="col-sm-5 control-label">Agent Comm Type</label>
                            <div class="col-sm-7">
                                {{ Form::select('agent_comm_type', ['0' => '%', '1' => 'Rs'],  null, ['class'=>'form-control']) }}
                            </div>
                        </div>
						<div class="div-space row">
                            <label for="inputTask" class="col-sm-5 control-label">Agent Comm</label>
                            <div class="col-sm-7">
                                <input type="text" class="form-control has-error" id="agent_comm" name="agent_comm"
                                       placeholder="Agent Comm" required>
                            </div>
                        </div>
						<div class="div-space row">
                            <label for="inputTask" class="col-sm-5 control-label">Dist Comm Type</label>
                            <div class="col-sm-7">
                                {{ Form::select('dist_comm_type', ['0' => '%', '1' => 'Rs'],  null, ['class'=>'form-control']) }}
                            </div>
                        </div>
						<div class="div-space row">
                            <label for="inputTask" class="col-sm-5 control-label">Dist Comm</label>
                            <div class="col-sm-7">
                                <input type="text" class="form-control has-error" id="dist_comm" name="dist_comm"
                                       placeholder="Dist Comm" required>
                            </div>
                        </div>
						<div class="div-space row">
                            <label for="inputTask" class="col-sm-5 control-label">Md Comm Type</label>
                            <div class="col-sm-7">
                                {{ Form::select('md_comm_type', ['0' => '%', '1' => 'Rs'],  null, ['class'=>'form-control']) }}
                            </div>
                        </div>
						<div class="div-space row">
                            <label for="inputTask" class="col-sm-5 control-label">Md Comm</label>
                            <div class="col-sm-7">
                                <input type="text" class="form-control has-error" id="md_comm" name="md_comm"
                                       placeholder="Md Comm" required>
                            </div>
                        </div><div class="div-space row">
                            <label for="inputTask" class="col-sm-5 control-label">Admin Comm Type</label>
                            <div class="col-sm-7">
                                {{ Form::select('admin_comm_type', ['0' => '%', '1' => 'Rs'],  null, ['class'=>'form-control']) }}
                            </div>
                        </div>
						<div class="div-space row">
                            <label for="inputTask" class="col-sm-5 control-label">Admin Comm</label>
                            <div class="col-sm-7">
                                <input type="text" class="form-control has-error" id="admin_comm" name="admin_comm"
                                       placeholder="Admin Comm" required>
                            </div>
                        </div>
						<button onclick="savedata()" type="submit" class="btn btn-info waves-effect waves-light"
                            id="btn-save"
                            value="add">Save Now
                    </button>
					</form>
                </div>
                <div class="modal-footer">
                   
                    <button type="button" class="btn btn-default waves-effect" data-dismiss="modal">Close</button>
                    <input type="hidden" id="id" name="id" value="0">
                </div>
            </div>
        </div>
    </div>
    <meta name="_token" content="{!! csrf_token() !!}"/>
    <!-- END wrapper -->
@endsection