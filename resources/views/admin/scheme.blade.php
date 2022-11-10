@extends('admin.layouts.templatetable')
@section('content')

<script>
    function add_record() {
        $('#btn-save').val("add");
        $('#frmTasks').trigger("reset");
		$('#scheme_name').val('');
		$('#schemeBtn').val("Create");
        $("#addNewSchemeModel").modal("toggle");
    }
    //create new task / update existing task
    function createNewScheme() {
        var url = "recharge-scheme";
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
            }
        })
        var formData = {
            scheme_name: $('#scheme_name').val(),
        }

        //used to determine the http verb to use [add=POST], [update=PUT]
        var state = $('#schemeBtn').val();

        var type = "POST"; //for creating new resource
       
        var my_url = url;

        if (state == "update") {
			 var task_id = $('#schemeId').val();
            type = "PUT"; //for updating existing resource
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
					setInterval(function(){ location.reload();},1000);
                }
            }

        });
    }
    function updateRecord(id) {
        var token = $("input[name=_token]").val();
        var dataString = 'id=' + id + '&_token=' + token;
        $.ajax({
            type: "get",
            url: "{{url('recharge-scheme/view')}}",
            data: dataString,
            success: function (data) {
                $('#schemeId').val(id);
                $('#scheme_name').val(data.message.scheme_name);
                $('#schemeBtn').val("update");
                $("#addNewSchemeModel").modal("toggle");
            }
        })

    }
    function CommissionPage(id){
        
    }
</script>
@include('admin.admin-subtab.companymaster-type')
    <!-- Page-Title -->
    <div class="row">
        <div class="col-sm-12">
            <div class="col-lg-6 col-md-6">
                <h3 class="page-title" style="color: black;">{{  'Recharge Scheme' }}</h3>
                
            </div>
            <div class="col-lg-6 col-md-6">
                <div class="pull-right">
                    <button onclick="add_record()" id="demo-add-row" class="btn btn-success"><i
                                class="fa fa-plus m-r-5"></i>Add New Scheme
                    </button>
                </div>
            </div>
        </div>
    </div><br>

    <!--Basic Columns-->
    <!--===================================================-->


    <!--===================================================-->
   <div class="panel-body">
   @if (session('error'))
		<div class="alert alert-danger">
			{{ session('error') }}
		</div>
	@endif
	 @if (session('success'))
		<div class="alert alert-success">
			{{ session('success') }}
		</div>
	@endif
    <div class="table table-responsive">
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
                                       @foreach($schemes as $key => $value)
                                        <tr class="odd gradeX">
                                            <td>{{ $value->id }}</td>
                                            <td>{{ $value->scheme_name }}</td>
                                            <td>{{ $value->status->status }}</td>
                                            @if($value->scheme_name=='Cash Back')
                            <td>{{ Form::open(array('url' => 'commission-manage/cashback', 'class' => 'pull-right')) }}
                                {{ Form::hidden('id', $value->id) }}
								 {{ Form::hidden('scheme_name', $value->scheme_name) }}
                                {{ Form::submit('Commission Setup', array('class' => 'btn btn-primary btn-xs')) }}
                                {{ Form::close() }}
                            </td>
                            @elseif($value->scheme_name=='Money Cash Back')
                             <td>{{ Form::open(array('url' => 'commission-manage/money_cashback', 'class' => 'pull-right')) }}
                                {{ Form::hidden('id', $value->id) }}
								 {{ Form::hidden('scheme_name', $value->scheme_name) }}
                                {{ Form::submit('Commission Setup', array('class' => 'btn btn-primary btn-xs')) }}
                                {{ Form::close() }}
                            </td>
                            @elseif($value->scheme_name=='AEPS CashBack')
                             <td>{{ Form::open(array('url' => 'commission-manage/aeps_cashback', 'class' => 'pull-right')) }}
                                {{ Form::hidden('id', $value->id) }}
								 {{ Form::hidden('scheme_name', $value->scheme_name) }}
                                {{ Form::submit('Commission Setup', array('class' => 'btn btn-primary btn-xs')) }}
                                {{ Form::close() }}
                            </td>
                            @elseif($value->scheme_name=='PtxnCashBack')
                             <td>{{ Form::open(array('url' => 'commission-manage/ptxncashback', 'class' => 'pull-right')) }}
                                {{ Form::hidden('id', $value->id) }}
								 {{ Form::hidden('scheme_name', $value->scheme_name) }}
                                {{ Form::submit('Commission Setup', array('class' => 'btn btn-primary btn-xs')) }}
                                {{ Form::close() }}
                            </td>
                            @else
                            <td>{{ Form::open(array('url' => 'commission-manage/viewupdate', 'class' => 'pull-right')) }}
                                {{ Form::hidden('id', $value->id) }}
								 {{ Form::hidden('scheme_name', $value->scheme_name) }}
                                {{ Form::submit('Commission Setup', array('class' => 'btn btn-primary btn-xs')) }}
                                {{ Form::close() }}
                            </td>
                            @endif
                            <td>
							<button onclick="updateRecord({{ $value->id }})" class="btn btn-info btn-xs">Edit</button>
							{{ Form::open(array('url' => 'recharge-scheme/delete', 'class' => 'pull-right')) }}
							{{ Form::hidden('schemeId', $value->id) }}
							{{ Form::hidden('scheme_name', $value->scheme_name) }}
                            {{Form::submit('delete',array('class' => 'btn btn-danger btn-xs'))}}
                                {{ Form::close() }}
                           
                            </td>
                                            
                                               
                                        </tr>
                                        @endforeach  
                                    </tbody>
                                </table>
                                
                            </div>
                        </div>


<div class="modal fade" id="addNewSchemeModel" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel">Create New Recharge Scheme</h4>
            </div>
            <div class="modal-body">
			<div id="name-error"></div>
                <form class="form-horizontal">
                    <div class="form-group">
                        <label for="saral_ref_id" class="control-label col-sm-4"> Scheme Name </label>
                        <div class="col-sm-6">
                            <input type="text" class="form-control" id="scheme_name" placeholder="Enter Scheme Name">
                            <input type="hidden" class="form-control" id="schemeId" >
                        </div>
                    </div>
                </form>
            </div>

            <div class="modal-footer">
			<input id="schemeBtn" type="button" onclick="createNewScheme();" class="btn btn-primary" value="Create">
                </input>
				 <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                

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

