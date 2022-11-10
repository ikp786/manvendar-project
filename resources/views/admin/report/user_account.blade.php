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
            var dataString = 'ref_id=' + id + '&_token=' + token;
            $.ajax({
                type: "POST",
                url: "{{url('get-report')}}",
                data: dataString,
                datatype: "json",
                success: function (data) {
                    $('#id').val(data.id);
                    $('#status').val(data.status_id);
                    $('#number').val(data.number);
                    $('#api_id').val(data.api_id);
                    $('#txnid').val(data.txnid);
                    $('#btn-save').val("update");
                    $("#con-close-modal").modal("toggle");
                }
            })

        }
    </script>

    <script>
        $(document).ready(function () {
            $('.pricenew').each(function () {
                calculateSum();
            });
        });

        function calculateSum() {

            var sum = 0;
// iterate through each td based on class and add the values
            $(".pricenew").each(function () {

                var value = $(this).text();
                // add only if the value is number
                if (!isNaN(value) && value.length != 0) {
                    sum += parseFloat(value);
                }
            });
            $('#result').text(sum);
        }

    </script>


    <!--Basic Columns-->
    <!--===================================================-->


    <!--===================================================-->
    <div class="row">
        <div class="col-sm-12">
            <div class="card-box">
                <h4 class="m-t-0 header-title"><b>{{ $page_title or 'Vender Reports' }}</b></h4>
                @if (session('status'))
                    <div class="alert alert-success">
                        {{ session('status') }}
                    </div>
                @endif
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                <div class="panel panel-default">
                    <div class="panel-body">
                        <div class="col-md-12">


                        </div>

                    </div>
                </div>
                <button id="demo-delete-row" class="btn btn-danger" disabled><i
                            class="fa fa-times m-r-5"></i>Delete
                </button>
                <table id="demo-custom-toolbar" data-toggle="table"
                       data-toolbar="#demo-delete-row"
                       data-search="true"
                       data-show-footer="true"
                       data-show-refresh="true"
                       data-show-toggle="true"
                       data-show-columns="true"
                       data-page-size="40"
                       data-pagination="true" data-show-pagination-switch="true" class="table">
                    <thead>
                    <tr>
                        <th data-field="id" data-sortable="true">
                            Name
                        </th>
                        <th data-field="total_success_opbal" data-sortable="true">Opening Balance</th>
                        <th data-field="total_success" data-sortable="true">Total Purchase</th>
                        <th data-field="total_success_profit" data-sortable="true">Total Sale (Success)</th>
                        <th data-field="total_success_profit_pending" data-sortable="true">Total Sale (Pending)</th>
                        <th data-field="total_success_commisison" data-sortable="true">Total Commission</th>
                        <th data-field="total_success_remain_balance" data-sortable="true">Current Balance</th>


                    </tr>
                    </thead>

                    <tbody>
                    @foreach($reports as $key => $value)
                        <tr>
                            <td>{{ $value->user_name }}</td>
                            <td>{{ $value->order_opbal }}</td>
                            <td class="pricenew">{{ $value->order_credit }}</td>
                            <td>{{ $value->order_success }}</td>
                            <td>{{ $value->order_pending }}</td>
                            <td>{{ $value->order_profit }}</td>
                            <td>{{ $value->user_balance }}</td>


                        </tr>

                    @endforeach
                    <tfoot>
                    <tr>
                        <td></td>
                        <td><b id="result"></b></td>
                        <td></td>
                        <td></td>
                        <td></td>
                    </tr>
                    </tfoot>
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
                    <h4 class="modal-title">Recharge Editor</h4>
                </div>
                <form id="frmTasks" action="{{ url('report/update') }}" method="post" name="frmTasks"
                      class="form-horizontal" novalidate="">
                    {!! csrf_field() !!}
                    <div class="modal-body">
                        <div style="display:none" id="name-error"></div>
                        <div class="form-group">
                            <label for="inputTask" class="col-sm-3 control-label">Number</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control has-error" id="id" name="id"
                                       placeholder="Provider Name" value="">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="inputTask" class="col-sm-3 control-label">Number</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control has-error" id="number" name="number"
                                       placeholder="Transaction id" value="">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="inputEmail3" class="col-sm-3 control-label">Transaction ID</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control" id="txnid" name="txnid"
                                       placeholder="Transaction id" value="">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="inputEmail3" class="col-sm-3 control-label">Status</label>
                            <div class="col-sm-9">
                                <select class="form-control" name="status" id="status">
                                    <option value="1">Success</option>
                                    <option value="2">Failure</option>
                                    <option value="3">Pending</option>
                                    <option value="4">Refunded</option>
                                </select>
                            </div>
                        </div>


                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-info waves-effect waves-light" id="btn-save"
                                value="add">Save Now
                        </button>
                        <button type="button" class="btn btn-default waves-effect" data-dismiss="modal">Close</button>
                    </div>
                </form>
            </div>
        </div>
    </div><!-- /.modal -->
    <!-- END wrapper -->
    <meta name="_token" content="{!! csrf_token() !!}"/>
@endsection