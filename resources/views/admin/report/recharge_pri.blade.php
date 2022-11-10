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
    
    <!--Basic Columns-->
    <!--===================================================-->


    <!--===================================================-->
    <div class="row">
        <div class="col-sm-12">
            <div class="card-box"style="margin-bottom:68px;"><!--style="margin-bottom:68px;"add this in card box-->
                <div class="col-md-12"><span style="    font-size: 20px;
    color: black;
    font-family: time;
    font-weight: bold;">Pending Refund and Initiated Reports:</span></div>
            <div class="col-md-12">
                <div class="col-md-4">
                   <form method="get" action="{{ url('recharge-pend-refd') }}" class="form-inline" role="form">
                      
					<div class="form-group col-md-6">
                           <select class="form-control" name="search_pri">
						   <option value="0">Select Your Status</option>
							   <option value="p">Pending</option>
							   <option value="r">Refund Pending</option>
                               <option value="i">Initiated</option>
							    <option value="dr">Refunded</option>
							    <!-- <option value="b">Blank TID</option>  -->
                                <option value="d_tid">Double Refunded</option>
                          </select>
                    </div>
					<div class="form-group col-md-6">
					 <button onclick="tekdeail()" type="submit"
                                class=" form-control btn btn-success btn-sm"><span
                                    class="glyphicon glyphicon-find"></span>Search
                        </button>
						<button type="submit" name='export' value='export' target="_blank"
                                class="btn btn-primary"><span class="glyphicon glyphicon-export"></span> Export
                        </button>
						</div>
						</form>
                        
						
                </div>
                <!--<div class="col-md-8">
                    <form method="get" action="{{ url('recharge-pend-refd') }}">
                        <div class="form-group col-md-4">
                            <input name="fromdate" class="form-control" type="date">
                        </div>
                        <div class="form-group col-md-4">
                            <input name="todate" class="form-control" type="date">
                        </div>
                        <div class="form-group col-md-2">
                            <button name="export" value="export" type="submit"
                                    class=" form-control btn btn-success btn-sm"><span
                                        class="glyphicon glyphicon-find"></span>Export
                            </button>
                        </div>
                        <div class="form-group col-md-2">
                            <button value="search" name="search" type="submit"
                                    class="form-control btn btn-success btn-sm"><span
                                        class="glyphicon glyphicon-find"></span>Search
                            </button>
                        </div>
						
						
                    </form>

                </div>-->

                <script>

                </script>
            </div>

               
                <table data-toggle="table"
                   data-search="true"
                   data-page-list="[10, 10, 20]" 
                   data-page-size="40">
                    <thead>
                    <tr>
                        <th data-field="id" data-sortable="true">
                            Id
                        </th>
                        <th data-field="name" data-sortable="true">Pay ID</th>
                        <th data-field="user" data-sortable="true">User</th>
                        <th data-field="date" data-sortable="true" data-formatter="dateFormatter">Date & Time
                        </th>
                        <th data-field="provider" data-align="center" data-sortable="true"
                            data-sorter="priceSorter">Product
                        </th>
                         <th data-field="provider_name" data-align="center" data-sortable="true"
                            data-sorter="priceSorter">Provider
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
                       <!-- <th style="display: none;" data-field="tds" data-align="center" data-sortable="true"
                            data-sorter="priceSorter">tds
                        </th> -->
                        <th data-field="total" data-align="center" data-sortable="true"
                            data-sorter="priceSorter">Total
                        </th>
                        <th data-field="status" data-align="center" data-sortable="true"
                            data-formatter="statusFormatter">Status
                        </th>
                        <th data-field="action" data-align="center" data-sortable="true">Action
                        </th>
                    </tr>
                    </thead>

                    <tbody>
                    @foreach($reports as $key => $value)
                        <tr>
                            <td>{{ $value->id }}</td>
                            <td>{{ $value->pay_id }}</td>
                            <td>
                                <a href="{{ url('user-recharge-report')}}/{{ $value->user_id }}">{{ $value->user->name }}</a>
                            </td>
                            <td>{{ $value->created_at }}</td>
                            <td>{{ $value->api->api_name }}</td>
                            <td>{{ $value->provider->provider_name }}</td>
                            <td>{{ $value->number }}</td>
                            <td>{{ $value->txnid }}</td>
                            <td>{{ number_format($value->amount,2) }}</td>
                            <td>{{ number_format($value->profit,2) }}</td>
                            <!--<td style="display: none;">{{ number_format($value->tds,4) }}</td> -->
                            <td>{{ number_format($value->total_balance2,2) }}</td>
                            <td>{{ $value->status->status }}</td>
                            @if(Auth::user()->role_id == 1)
                            <td><!--<a onclick="updateRecord({{ $value->id }})" href="#" class="table-action-btn"><i
                                            class="md md-edit"></i></a>
                                <a href="#" class="table-action-btn"><i class="md md-close"></i></a>-->--
                            </td>
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