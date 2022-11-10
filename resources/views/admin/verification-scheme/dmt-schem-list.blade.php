@extends('admin.layouts.templatetable')
@section('content')

<script>
    function add_record() {
        $('#btn-save').val("add");
        $('#frmTasks').trigger("reset");
        $("#con-close-modal").modal("toggle");
    }
    //create new task / update existing task
    function savedata() {
	var url = "{{url('add-new-wallet-scheme')}}";
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
            }
        })
		var state = $('#btn-save').val();
		var type = "POST"; //for creating new resource
        var task_id = $('#id').val();
        var my_url = url;

        if (state == "update") 
		{
            type = "PUT"; //for updating existing resource
            my_url += '/' + task_id;
        }
		var uploadfile = $("#newMoneyScheme").serialize();
        $.ajax({
            type: type,
            url: my_url,
            data: uploadfile,
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
                } else 
				{
                    var html = "";
                    for (var key in obj) {
                        html += "<li>" + obj[key] + "</li>";
                    }
                    $("#name-error").show();
                    $("#name-error").html("<div class='alert alert-success'><ul>" + html + "</ul></div>");
					alert("Successfully Created");
					location.reload();
					
                }
            }

        });
    }
	
    function updateWalletSchemeName(id) {
        var token = $("input[name=_token]").val();
        var dataString = 'id=' + id + '&_token=' + token;
        $.ajax({
            type: "get",
            url: "{{route('getWalletSchemeName')}}",
            data: dataString,
            success: function (data) {
                $('#id').val(id);
                $('#name').val(data.message);
                $('#btn-save').val("update");
                $("#con-close-modal").modal("toggle");
            }
        })

    }
    function CommissionPage(id){
        
    }
</script>

    <div class="row">
        <div class="col-sm-12">
            <div class="col-lg-6 col-md-6">
                <h3 class="page-title" style="color: black;">{{  'IMPS 1 SCHEME DETAIL' }}</h3>
                
            </div>
            <div class="col-lg-6 col-md-6 pull-right">
                <div class="pull-right">
                    <button onclick="add_record()" id="demo-add-row" class="btn btn-success"><i
                                class="fa fa-plus m-r-5"></i>Add Record
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!--Basic Columns-->
    <!--===================================================-->


    <!--===================================================-->
   <div class="panel-body">
    <div class="table table-responsive">
		@include('partials.message_error')
        <table border='1' class="table table-striped table-bordered table-hover" id="dataTables-example">
					<thead>
						<tr>
							<th>ID</th>
							<th>Scheme Name</th>
							<th>Status</th>
							<th>Commission</th>
							<th>Action</th>
							
						</tr>
					</thead>
					<tbody>
					   @foreach($dmtSchemeList as $key => $value)
						<tr class="odd gradeX">
							<td>{{ $value->id }}</td>
							<td>{{ $value->name }}</td>
							
							<td>{{ ($value->status_id) ? "SUCESS" :"DISABLE" }}</td>
                                            
                            <td>{{ Form::open(array('url' => 'wallet-commission-update')) }}
                                {{ Form::hidden('schemeName', $value->name) }}
                                {{ Form::hidden('id', $value->id) }}
                                {{ Form::hidden('scheme_for', $value->scheme_for) }}
                                {{ Form::submit('View DMT SCHEME', array('class' => 'btn btn-primary')) }}
                                {{ Form::close() }}
                            </td>
                            
                            <td><a onclick="updateWalletSchemeName({{ $value->id }})" href="#" class="table-action-btn">Edit</a>
                               
                            </td>
                                            
                                               
                                        </tr>
                                        @endforeach  
                                    </tbody>
                                </table>
                                
                            </div>
                        </div>

    <div id="con-close-modal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
     aria-hidden="true" style="display: none;">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                <h4 class="modal-title">IMPS 1 Scheme Create</h4>
            </div>
            <div class="modal-body">
                <div style="display:none" id="name-error"></div>
				<div  class="form-horizontal">
               {!! Form::open(array('url' =>'#','id'=>'newMoneyScheme')) !!}
                    <div class="form-group">
                        <label for="inputTask" class="col-sm-3 control-label">Money Scheme Name</label>
                        <div class="col-sm-9">
                            <input type="text" class="form-control has-error" id="name" name="name"
                                   placeholder="Enter Wallet Scheme Name" value="">
                        </div>
                    </div>
					<div class="form-group">
                        <label for="inputTask" class="col-sm-3 control-label">Scheme For</label>
                        <div class="col-sm-9">
                            {{ Form::select('scheme_for', ['3' => 'IMPS 1'], null, ['class'=>'form-control']) }}
                        </div>
                    </div>
					<input type="hidden" id="id" name="id" value="0">
                {!! Form::close() !!}
				</div>
            </div>
            <div class="modal-footer">
                <button onclick="savedata()" type="button" class="btn btn-info waves-effect waves-light" id="btn-save"
                        value="add">Save Now
                </button>
                <button type="button" class="btn btn-default waves-effect" data-dismiss="modal">Close</button>
                
            </div>
        </div>
    </div>
</div>
<meta name="_token" content="{!! csrf_token() !!}"/>

<script>
$(document).ready(function () {
$('#dataTables-example').DataTable( {
        "order": [[ 0, "desc" ]],
        "targets": "no-sort",
        "bSort": false,
    } );
    });
</script>
@endsection
