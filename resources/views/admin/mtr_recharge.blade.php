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
                type: "GET",
                url: "{{url('epay/get-report')}}",
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



    <!-- Page-Title -->
    <div class="row">
        <div class="col-sm-12">
            <div class="col-lg-6 col-md-6">
                <h4 class="page-title" style="color: white; font-size: 36px;">{{ $page_title or 'MTR RECHARGE REPORT' }}</h4>
            </div>
        </div>
    </div><br>

    <!--Basic Columns-->
    <!--===================================================-->


    <!--===================================================-->
	<div class="panel panel-default">
                        <div class="panel-body">
                            <div class="col-md-12">
							<div class="col-md-6">
                                    <form method="get" action="{{ url('export-account-statement') }}">
									
                                        <div class="form-group col-md-4">
                                            <input name="fromdate" class="form-control" type="date">
                                        </div>
                                        <div class="form-group col-md-4">
                                            <input name="todate" class="form-control" type="date">
                                        </div>
					
                                        <div class="form-group col-md-2">
                                            <button name="export" value="export" type="submit"
                                                    class="btn btn-success waves-effect waves-light m-l-10 btn-md"><span
                                                        class="glyphicon glyphicon-find"></span>Export
                                            </button>
                                        </div>
                                       
                                    </form>
									
                                </div>
								
								<div class="col-md-6">
                                    <form method="get" action="{{ url('search-account-statement') }}">
									
                                        <div class="form-group col-md-4">
                                            <input name="fromdate" class="form-control" type="date">
                                        </div>
                                        <div class="form-group col-md-4">
                                            <input name="todate" class="form-control" type="date">
                                        </div>
					
                                        <div class="form-group col-md-2">
                                            <button name="export" value="export" type="submit"
                                                    class="btn btn-success waves-effect waves-light m-l-10 btn-md"><span
                                                        class="glyphicon glyphicon-find"></span>Search
                                            </button>
                                        </div>
                                       
                                    </form>
									
                                </div>
								
							</div>
							</div>
							</div>
    <div class="row">
	
        <div class="col-sm-12">
            <div class="card-box">
                <h4 class="m-t-0 header-title"><b>{{ $page_title or 'Account statement' }}</b></h4>
                <p class="text-muted font-13">
                    All satement Detail
                </p>
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

                <table id="demo-custom-toolbar" data-toggle="table"
                       data-toolbar="#demo-delete-row"
                       data-search="true"
                       data-page-list="[10, 20, 30]"
                       data-page-size="10">
                    <thead>
                    <tr>
                        <th data-field="date" data-sortable="true" data-formatter="dateFormatter">
                            Date
                        </th>
                        
                         <th data-field="product" data-sortable="true" data-formatter="dateFormatter">
                            Product
                        </th>
                       
                        <th data-field="name" data-sortable="true">Name</th>
                        <th data-field="number" data-sortable="true">Mobile Number</th>
                        <th data-field="account_number" data-sortable="true" data-formatter="dateFormatter">
                            Tx ID
                        </th>
                        <th data-field="amount" data-align="center" data-sortable="true"
                            data-sorter="priceSorter">Description
                        </th>
                        <th data-field="credit" data-align="center" data-sortable="true"
                            data-sorter="priceSorter">Credit
                        </th>

                        <th data-field="balance" data-align="center" data-sortable="true"
                            data-sorter="priceSorter">Balance
                        </th>
                        <th data-field="status" data-align="center" data-sortable="true"
                            data-formatter="statusFormatter">Status
                        </th>

                    </tr>
                    </thead>

                    <tbody>
                    @foreach($reports as $report)
                        <tr>
                            <td>{{ $report->created_at }}</td>
                            <td>{{ $report->api->api_name }}</td>
                            <td>{{ $report->credit_by }}</td>
                            <td>{{ $report->number }}</td>
                            <td>{{ $report->txnid }}</td>
                            <td> {{ $report->description }}</td>
                            @if($report->status_id == 7)
                                <td>{{ number_format($report->amount,2) }}</td>
                            @endif
                          
                            
                            <td>{{ number_format($report->amount,2) }} </td>
                          
                            <td>{{ number_format($report->total_balance2,4) }}</td>
                            @if($report->status_id == 6 || $report->status_id == 7)
                                <td>Success</td>
                            @else
                                <td>{{ $report->status->status }}</td>
                            @endif
                        </tr>
                    @endforeach

                    </tbody>
                </table>
                {!! $reports->links() !!}
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
                        <input type="text" name="id" value="" id="id">
                        <div class="form-group">
                            <label for="inputTask" class="col-sm-3 control-label">Number</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control has-error" id="number" name="number"
                                       placeholder="Provider Name" value="">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="inputEmail3" class="col-sm-3 control-label">Transaction ID</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control" id="txnid" name="txnid"
                                       placeholder="Provider Code" value="">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="inputEmail3" class="col-sm-3 control-label">Status</label>
                            <div class="col-sm-9">
                                <select class="form-control" name="status" id="status">
                                    <option value="1">Success</option>
                                    <option value="2">Failure</option>
                                    <option value="3">Pending</option>
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