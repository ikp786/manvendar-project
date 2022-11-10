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
            var cashback_category = $('#cashback_category_'+id).val();
            var cashback_admin = $('#cashback_admin_'+id).val();
            var cashback_md = $('#cashback_md_'+id).val();
            var cashback_dist = $('#cashback_dist_'+id).val();
            var cashback_ret = $('#cashback_ret_'+id).val();
            var dataString = 'id=' + id +'&cashback_category='+cashback_category+'&cashback_admin='+cashback_admin+'&cashback_md='+ cashback_md +'&cashback_dist='+ cashback_dist +'&cashback_ret='+ cashback_ret +'&_token=' + token;
            $.ajax({
                type: "post",
                url: "{{url('scheme-manage/mcashback')}}",
                data: dataString,
                success: function (data) {
                    alert(data);
                    return false;
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
            <div class="col-lg-2 col-md-2">
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
            <div class="col-md-6">
                           
                                <form method="get" action="https://partners.levinm.com/export_operator_detail">
                                    <div class="form-group col-md-2">
                                       <select class="form-control" name="select_operator">
                                         
                                           <option value="{{@$value->provider->id }}">{{@$value->provider->id }}</option>
                                           
                                       </select>
                                    </div>
                                    <div class="form-group col-md-4">
                                        <input name="fromdate" class="form-control" type="date">
                                    </div>
                                    <div class="form-group col-md-4">
                                        <input name="todate" class="form-control" type="date">
                                    </div>
                                    <div class="form-group col-md-2">
                                        <button name="export" value="export" type="submit" class="btn btn-success waves-effect waves-light m-l-10 btn-md"><span class="glyphicon glyphicon-find"></span>Export
                                        </button>
                                    </div>
                                </form>
                            </div>
            <div class="col-lg-4 col-md-4">
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
                       data-page-size="100"
                       data-pagination="true" data-show-pagination-switch="true" class="table-bordered ">
                    <thead>
                    <tr>
                        <th data-field="state"></th>
                        <th data-field="sid">Company ID</th>
                        <th data-field="c_name" >
                           Company Name
                        </th>
                        <th>
                            Categories
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
                        <th data-field="action" data-align="center" data-sortable="true">Action
                        </th>
                    </tr>
                    </thead>

                    <tbody>


                    @foreach($cashback as $value)
                    
    
                        <tr>
                            <td>{{ $value->id }}</td>
                            <td>{{ @$value->c_id }}</td>
                            <td>{{ @$value->company->company_name }}</td>
                            <td>
                            <!-- <select class="form-control" name="cashback_category" id="cashback_category_{{ $value->id }}"
                                        style="width:100px; text-align:center; margin-right:5px; height:34px;">
                                    <option value="{{$value->service_id}}">{{$value->categories}}</option>
                                    <option value="3">SARAL</option>
                                    <option value="4">SMART</option>
                                    <option value="5">SHARP</option>
                                    <option value="14">SECURE</option>
                                   
                            </select> -->
                             <input type="hidden" name="cashback_category" value="{{$value->service_id}}" id="cashback_category_{{ $value->id }}">
                                <input type="text" disabled class="form-control" name="cashback_category1" value="{{$value->categories}}">
                            </td>
                            <td>
                                <input type="text" name="cashback_admin" id="cashback_admin_{{ $value->id }}" value="{{ @$value->admin }}" style="width:60px; text-align:center; margin-right:5px; height:34px;">
                            </td>
                            <td>
                                <input type="text" name="cashback_md" id="cashback_md_{{ $value->id }}" value="{{ @$value->md }}" style="width:60px; text-align:center; margin-right:5px; height:34px;">
                            </td>
                            <td>
                                <input type="text" name="cashback_dist" id="cashback_dist_{{ $value->id }}" value="{{ @$value->dist }}"
                                       style="width:60px; text-align:center; margin-right:5px; height:34px;">
                            </td>
                            <td>
                                <input type="text" name="cashback_ret" id="cashback_ret_{{ $value->id }}" value="{{ @$value->retailer }}"
                                       style="width:60px; text-align:center; margin-right:5px; height:34px;">
                            </td>
                            <td><a href="javascript::voide(0)" onclick="updateRecord({{ $value->id }})" class="btn btn-success">Save</a>
                               
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