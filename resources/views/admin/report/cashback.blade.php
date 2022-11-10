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
                <h4 class="page-title" style="color: white; font-size: 36px;">{{ $page_title or 'ALL TRANSACTION REPORT' }}</h4>
                
            </div>
            <div class="col-lg-6 col-md-6">
                <!-- <div class="pull-right">
                    <button onclick="add_record()" id="demo-add-row" class="btn btn-success"><i
                                class="fa fa-plus m-r-5"></i>Add Record
                    </button>
                </div> -->
            </div>
        </div>
    </div><br>

    <!--Basic Columns-->
    <!--===================================================-->


    <!--===================================================-->
    <div class="row">
        <div class="col-sm-12">
            <div class="card-box">
                <h4 class="m-t-0 header-title"><b>{{ $page_title or 'Provider Detail' }}</b></h4>
                <p class="text-muted font-13">
                    All Recharge Detail
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

                <div class="panel panel-default">
                    <div class="panel-body">
                        <div class="col-md-12">
                            <div class="col-md-4">
                                <form method="get" action="{{ url('searchall') }}" class="form-inline" role="form">
                                    {!! csrf_field() !!}
                                    <div class="form-group">
                                        <label class="sr-only" for="payid">Number</label>
                                        <input name="number" type="text" class="form-control" id="exampleInputEmail2"
                                               placeholder="Number">
                                    </div>
                                    <button onclick="tekdeail()" type="submit"
                                            class="btn btn-success waves-effect waves-light m-l-10 btn-md"><span
                                                class="glyphicon glyphicon-find"></span>Search
                                    </button>
                                </form>
                            </div>
                             @if(Auth::user()->role_id==1)
                            <div class="col-md-8">
                           
                                <form method="get" action="{{ url('searchall') }}">
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
									<div class="form-group col-md-2">
                                        <button name="export2" value="export2" type="submit"
                                                class="btn btn-success waves-effect waves-light m-l-10 btn-md"><span
                                                    class="glyphicon glyphicon-find"></span>Export2
                                        </button>
                                    </div>
									
                                    <div class="form-group col-md-2">
                                        <button value="api_user_report" name="api_user_report" type="submit"
                                                class="btn btn-success waves-effect waves-light m-l-10 btn-md"><span
                                                    class="glyphicon glyphicon-find"></span>API User Report
                                        </button>
                                    </div>
                                </form>
                               
                            </div>
                             @endif
                            <script>

                            </script>
                        </div>

                    </div>
                </div>

                <table id="demo-custom-toolbar" data-toggle="table"
                       data-toolbar="#demo-delete-row"
                       data-search="true"
                       data-show-export="true"
                       data-page-list="[10, 20, 30]"
                       data-page-size="80"
                       data-pagination="true" class="table-bordered ">
                    <thead>
                    <tr>
                        <th data-field="id" data-sortable="true">
                            Id
                        </th>
                       
                        <th data-field="user" data-sortable="true">User</th>
                        <th data-field="date" data-sortable="true" data-formatter="dateFormatter">Date & Time
                        </th>
                        <th data-field="provider" data-align="center" data-sortable="true"
                            data-sorter="priceSorter">Product
                        </th>

                        <th data-field="provider_name" data-align="center" data-sortable="true"
                            data-sorter="priceSorter">Provider
                        </th>
						<th data-field="bank_name" data-sortable="true" data-formatter="dateFormatter">Bank Name
                        </th>
                        <th data-field="number" data-sortable="true" data-formatter="dateFormatter">Number
                        </th>
                        <th data-field="txnid" data-sortable="true">Ref Id
                        </th>

                        <th data-field="amount" data-align="center" data-sortable="true"
                            data-sorter="priceSorter">Amount
                        </th>
                        <th data-field="profit" data-align="center" data-sortable="true"
                            data-sorter="priceSorter">S Charge
                        </th>
                        <th data-field="total" data-align="center" data-sortable="true"
                            data-sorter="priceSorter">Total
                        </th>
                        <th data-field="status" data-align="center" data-sortable="true"
                            data-formatter="statusFormatter">Status
                        </th>
                        @if(Auth::user()->role_id == 1)
                        <th data-field="action" data-align="center" data-sortable="true">Action
                        </th>
                        @endif
                    </tr>
                    </thead>

                    <tbody>
                    @foreach($reports as $key => $value)
                    @if(Auth::id()==110)
                    @if($value->status_id!=14)
                        <tr>
                            <td>{{ $value->id }}</td>
                           
                            <td>
                                <a href="{{ url('user-recharge-report')}}/{{ $value->user_id }}">{{ $value->user->name }}</a>
                            </td>
                            <td>{{ $value->created_at }}</td>
                            <td>{{ $value->api->api_name }}</td>
                            <td>{{ $value->provider->provider_name }}</td>
                             @if($value->api_id==2)
                             <td>{{ @$value->description }}</td>
                             @else
                             <td>{{ @$value->beneficiary->bank_name }}</td>
                             @endif
                             @if($value->api_id==0)
                             <td>{{ $value->credit_by}}</td>
                             @else
							<td>{{ $value->number }}</td>
                            @endif
                            <td>{{ $value->txnid }}</td>
                            <td>@if($value->api->api_name=='Verify'){{'Verify'}} @elseif($value->user->name=='MONEY SERVM'){{ 'Per TXN'}}@else {{ number_format($value->amount,2) }} @endif</td>
                            <td>{{ number_format($value->profit,2) }}</td>
                            <td>{{ ($value->recharge_type == 1) ? number_format($value->total_balance,2) : number_format($value->total_balance,2) }}</td>
                            <td>{{ @$value->status->status }}</td>
                            @if(Auth::user()->role_id == 1)
                            <td><a onclick="updateRecord({{ $value->id }})" href="javascript::void(0)" class="table-action-btn"><i
                                            class="md md-edit"></i></a>
                                <a href="#" class="table-action-btn"><i class="md md-close"></i></a>
                            </td>
                            @endif
                        </tr>
                        @endif
                        @else
                        @if($value->status_id!=14 && $value->user->company_id==Auth::user()->company_id)
                        <tr>
                            <td>{{ $value->id }}</td>
                           
                            <td>
                                <a href="{{ url('user-recharge-report')}}/{{ $value->user_id }}">{{ $value->user->name }}</a>
                            </td>
                            <td>{{ $value->created_at }}</td>
                            <td>{{ $value->api->api_name }}</td>
                            <td>{{ $value->provider->provider_name }}</td>
                             @if($value->api_id==2)
                             <td>{{ @$value->description }}</td>
                             @else
                             <td>{{ @$value->beneficiary->bank_name }}</td>
                             @endif
                             @if($value->api_id==0)
                             <td>{{ $value->credit_by}}</td>
                             @else
                            <td>{{ $value->number }}</td>
                            @endif
                            <td>{{ $value->txnid }}</td>
                            <td>@if($value->api->api_name=='Verify'){{'Verify'}} @elseif($value->user->name=='MONEY SERVM'){{ 'Per TXN'}}@else {{ number_format($value->amount,2) }} @endif</td>
                            <td>{{ number_format($value->profit,2) }}</td>
                            <td>{{ ($value->recharge_type == 1) ? number_format($value->total_balance,2) : number_format($value->total_balance,2) }}</td>
                            <td>{{ @$value->status->status }}</td>
                            @if(Auth::user()->role_id == 1)
                            <td><a onclick="updateRecord({{ $value->id }})" href="#" class="table-action-btn"><i
                                            class="md md-edit"></i></a>
                                <a href="#" class="table-action-btn"><i class="md md-close"></i></a>
                            </td>
                            @endif
                        </tr>
                        @endif
                        @endif
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
                        <input type="hidden" name="id" value="" id="id">
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