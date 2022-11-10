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
            var url = "scheme-manage";
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                }
            })
            var formData = {
                scheme_name: $('#scheme_name').val(),
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
            //document.getElementById("commform").submit();
        }
    </script>

    <div class="row">
        <div class="col-sm-12">
            <div class="col-lg-6 col-md-6">
                <h4 class="page-title">{{ $page_title or 'API Detail' }}</h4>
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
                    <button onclick="save_record()" id="demo-add-row" class="btn btn-success"><i
                                class="fa fa-save m-r-5"></i>Save All
                    </button>
                    <button onclick="add_record()" id="demo-add-row" class="btn btn-info"><i
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
                {{ Form::open(array('url' => 'storeslab/', 'method' => 'POST','id' => 'commform')) }}
                <button style="display: none;" type="submit" id="commfor" class="btn btn-success"><i
                            class="fa fa-save m-r-5"></i>Save All
                </button>
                <button id="demo-delete-row" class="btn btn-danger" disabled><i
                            class="fa fa-times m-r-5"></i>Delete
                </button>
                <table id="demo-custom-toolbar" data-toggle="table"
                       data-toolbar="#demo-delete-row"
                       data-search="true"
                       data-show-refresh="true"
                       data-show-toggle="true"
                       data-show-columns="true"
                       data-page-list="[5, 10, 20]"
                       data-page-size="20"
                       data-pagination="true" data-show-pagination-switch="true" class="table-bordered ">
                    <thead>
                    <tr>
                        <th data-field="id" data-sortable="true">
                            Slab Name
                        </th>
                        <th>
                            Type
                        </th>
                        <th>
                            Admin
                        </th>
                        <th>
                            M D
                        </th>
                        <th>
                            Distributor
                        </th>
                        <th>
                            Retailer
                        </th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($inserID as $key => $value)
                        <tr>
                            <td>{{ $value }}</td>
                            <td>
                                <select class="form-control" name="commission_value[{{ $key }}][type]"
                                        style="width:60px; text-align:center; margin-right:5px; height:34px;">
                                    <option value="0">%</option>
                                    <option value="1">Rs</option>
                                </select>
                            </td>
                            <td>
                                <input type="text" name="commission_value[{{ $key }}][admin]"
                                       value=""
                                       style="width:60px; text-align:center; margin-right:5px; height:34px;">
                            </td>
                            <td>
                                <input type="text" name="commission_value[{{ $key }}][md]"
                                       value=""
                                       style="width:60px; text-align:center; margin-right:5px; height:34px;">
                            </td>
                            <td>
                                <input type="text" name="commission_value[{{$key}}][d]" value=""
                                       style="width:60px; text-align:center; margin-right:5px; height:34px;">
                            </td>
                            <td>
                                <input type="text" name="commission_value[{{$key}}][r]" value=""
                                       style="width:60px; text-align:center; margin-right:5px; height:34px;">
                            </td>

                        </tr>
                    @endforeach
                    {{ Form::close() }}
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
                            <label for="inputTask" class="col-sm-3 control-label">Scheme Name</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control has-error" id="scheme_name" name="scheme_name"
                                       placeholder="Api Name" value="">
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button onclick="savedata()" type="button" class="btn btn-info waves-effect waves-light"
                            id="btn-save"
                            value="add">Save Now
                    </button>
                    <button type="button" class="btn btn-default waves-effect" data-dismiss="modal">Close</button>
                    <input type="hidden" id="id" name="id" value="0">
                </div>
            </div>
        </div>
    </div>
    <meta name="_token" content="{!! csrf_token() !!}"/>
    <!-- END wrapper -->
@endsection