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
        var url = "provider";
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
            }
        })
        var formData = {
            provider_name: $('#provider_name').val(),
            provider_code: $('#provider_code').val(),
            service_id: $('#service_id').val(),
            api_code: $('#api_code').val(),
            api_id: $('#api_id').val(),
            provider_picture: $('#provider_picture').val(),
        }

        //used to determine the http verb to use [add=POST], [update=PUT]
        var state = $('#btn-save').val();

        var type = "POST"; //for creating new resource
        var task_id = $('#id').val();
        var my_url = url;

        if (state == "update") {
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
                }
            }

        });
    }
    function updateRecord(id) {
        var token = $("input[name=_token]").val();
        var dataString = 'id=' + id + '&_token=' + token;
        $.ajax({
            type: "post",
            url: "{{url('provider/view')}}",
            data: dataString,
            success: function (data) {
                $('#id').val(data.id);
                $('#provider_name').val(data.provider_name);
                $('#provider_code').val(data.provider_code);
                $('#service_id').val(data.service_id);
                $('#api_id').val(data.api_id);
                $('#api_code').val(data.api_code);
                $('#btn-save').val("update");
                $("#con-close-modal").modal("toggle");
            }
        })

    }
</script>



    <!-- Page-Title -->
    <div class="row">
        <div class="col-sm-12">
            <div class="col-lg-6 col-md-6">
                <h4 class="page-title">{{ $page_title or 'Provider Detail' }}</h4>
                <ol class="breadcrumb">
                    <li>
                        <a href="{{ url('dashboard') }}">Home</a>
                    </li>
                    <li class="active">
                        {{ $page_title or 'Provider Detail' }}
                    </li>
                </ol>
            </div>
            <div class="col-lg-6 col-md-6">
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
    <div class="row">
        <div class="col-sm-12">
            <div class="card-box">
                <h4 class="m-t-0 header-title"><b>{{ $page_title or 'Provider Detail' }}</b></h4>
                <p class="text-muted font-13">
                    Add or Update Service Provider Detail
                </p>

                <button id="demo-delete-row" class="btn btn-danger" disabled><i
                            class="fa fa-times m-r-5"></i>Delete
                </button>
                <table id="demo-custom-toolbar" data-toggle="table"
                       data-toolbar="#demo-delete-row"
                       data-search="true"
                       data-show-refresh="true"
                       data-show-toggle="true"
                       data-show-columns="true"
                       data-sort-name="id"
                       data-page-list="[5, 10, 20]"
                       data-page-size="5"
                       data-pagination="true" data-show-pagination-switch="true" class="table-bordered ">
                    <thead>
                    <tr>
                        <th data-field="state" data-checkbox="true"></th>
                        <th data-field="id" data-sortable="true">
                            Company Name
                        </th>
                        <th data-field="name" data-sortable="true">Code</th>
                        <th data-field="date" data-sortable="true" data-formatter="dateFormatter">Service
                        </th>
                        <th data-field="amount" data-align="center" data-sortable="true"
                            data-sorter="priceSorter">API
                        </th>
                        <th data-field="status" data-align="center" data-sortable="true"
                            data-formatter="statusFormatter">Status
                        </th>
                        <th data-field="action" data-align="center" data-sortable="true">Status
                        </th>
                    </tr>
                    </thead>

                    <tbody>
                    @foreach($providers as $key => $value)
                        <tr>
                            <td>{{ $value->id }}</td>
                            <td>{{ $value->provider_name }}</td>
                            <td>{{ $value->provider_code }}</td>
                            <td>{{ $value->service->service_name }}</td>
                            <td>{{ $value->api->api_name }}</td>
                            <td>{{ $value->status->status }}</td>
                            <td><a onclick="updateRecord({{ $value->id }})" href="#" class="table-action-btn"><i
                                            class="md md-edit"></i></a>
                                <a href="#" class="table-action-btn"><i class="md md-close"></i></a>
                            </td>
                        </tr>
                    @endforeach

                    </tbody>
                </table>
            </div>
        </div>
    </div>



<div id="con-close-modal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
     aria-hidden="true" style="display: none;">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                <h4 class="modal-title">Provider Editor</h4>
            </div>
            <div class="modal-body">
                <div style="display:none" id="name-error"></div>

                <form id="frmTasks" name="frmTasks" class="form-horizontal" novalidate="">
                    {!! csrf_field() !!}
                    <div class="form-group">
                        <label for="inputTask" class="col-sm-3 control-label">Provider Name</label>
                        <div class="col-sm-9">
                            <input type="text" class="form-control has-error" id="provider_name" name="task"
                                   placeholder="Provider Name" value="">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="inputEmail3" class="col-sm-3 control-label">Provider Code</label>
                        <div class="col-sm-9">
                            <input type="text" class="form-control" id="provider_code" name="provider_code"
                                   placeholder="Provider Code" value="">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="inputEmail3" class="col-sm-3 control-label">Service Type</label>
                        <div class="col-sm-9">
                            {{ Form::select('service_id', $services, old('service_id'), array('class' => 'form-control','id' => 'service_id')) }}
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="inputEmail3" class="col-sm-3 control-label">Api Vender Name</label>
                        <div class="col-sm-9">
                            {{ Form::select('api_id', $apis, old('api_id'), array('class' => 'form-control','id' => 'api_id')) }}
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="inputEmail3" class="col-sm-3 control-label">Api Code</label>
                        <div class="col-sm-9">
                            <input type="text" class="form-control" id="api_code" name="api_code"
                                   placeholder="Api Code" value="">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="inputEmail3" class="col-sm-3 control-label">Provider Picture</label>
                        <div class="col-sm-9">
                            {{ Form::file('provider_picture', array('class' => 'form-control','id' => 'provider_picture')) }}
                        </div>
                    </div>

                </form>
            </div>
            <div class="modal-footer">
                <button onclick="savedata()" type="button" class="btn btn-info waves-effect waves-light" id="btn-save"
                        value="add">Save Now
                </button>
                <button type="button" class="btn btn-default waves-effect" data-dismiss="modal">Close</button>
                <input type="hidden" id="id" name="id" value="0">
            </div>
        </div>
    </div>
</div><!-- /.modal -->
<!-- END wrapper -->
<meta name="_token" content="{!! csrf_token() !!}"/>
@endsection