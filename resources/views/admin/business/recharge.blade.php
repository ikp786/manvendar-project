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
                    $('#amount').val(data.amount);
                    $('#btn-save').val("update");
                    $("#con-close-modal").modal("toggle");
                }
            })

        }
        function recharge_check_status(id)
        {
            var token = $("input[name=_token]").val();
            var dataString = 'request_id=' + id + '&_token=' + token;
            $.ajax({
                type: "put",
                url: "{{url('get-recharge-details')}}/"+id,
                data: dataString,
                datatype: "json",
                success: function (data) {
                    alert(data.message);
                    /* location.reload(); */
                }
            })
        }
		function refundTranaction()
        {
            if(confirm('Are you sure to refund amount ?'))
            {
                var id = $("#id").val();
                var number = $("#number").val();
                var txnid = $("#txnid").val();
                var status = $("#status").val();
                var token = $("input[name=_token]").val();
                var dataString = 'id=' + id +'&number='+ number + '&txnid=' + txnid +'&status='+ status +' &_token=' + token;
                $.ajax({
                    type: "POST",
                    url: "{{ url('report/update_recharge') }}",
                    data: dataString,
                    datatype: "json",
                    beforeSend: function() {
                            $.LoadingOverlay("show", {
                            image       : "",
                            fontawesome : "fa fa-spinner fa-spin"
                        });
                    },
                    success: function (res) 
                    {
                        $.LoadingOverlay("hide");
                      
                       if(res.status == 1)
                       {
                            alert(res.message);
                            $('#con-close-modal').modal('hide');
                            location.reload();
                       }
                       else
                       {
                            alert(res.message);
                            $('#con-close-modal').modal('hide');
                            location.reload();
                       }
                    }
                });
            }
            else
            {
                 $("#btn-save").prop("disabled", false);
            }
        }
    </script>

<div class="">
      
                <div class="col-md-6">
                    <h4 class="page-title" style="color: black;">{{'Recharge' }}</h4>
                </div>
          
    </div>
    <div class="panel panel-default">
        <div class="panel-body">
            <div class="col-md-12">
                <div class="col-md-4">
                    <form method="get" action="{{ url('recharge-searchall') }}" class="form-inline" role="form">
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
                 @if (in_array(Auth::user()->role_id,array(1,11,12,14)))
                <div class="col-md-8">
               
                    <form method="get" action="{{ url('recharge-searchall') }}">
                                    <div class="form-group col-md-4">
                                        <input name="fromdate" class="form-control" type="date">
                                    </div>
                                    <div class="form-group col-md-4">
                                        <input name="todate" class="form-control" type="date">
                                    </div>
                                    <div class="form-group col-md-2">
                                        <button name="export" value="Recharge Txn Report" type="submit"
                                                class="btn btn-primary "><span
                                                    class="glyphicon glyphicon-find"></span>Export
                                        </button> 
                                    </div>
                                   <!-- <div class="form-group col-md-2">
                                        <button value="search" name="search" type="submit"
                                                class="btn btn-success waves-effect waves-light m-l-10 btn-md"><span
                                                    class="glyphicon glyphicon-find"></span>Search
                                        </button>
                                    </div> -->
                                </form>
                  
                </div>
                  @endif
                <script>

                </script>
            </div>

        </div>
    </div><br>

    <!--===================================================-->


    <!--===================================================-->
    <div class="row">
        <div class="col-sm-12">
            
               
				<table id="mytable"  class="table table-bordered " >
                    <thead>
                    <tr>
                        <th data-field="date" >Date/Time </th>
                        <th data-field="id" data-sortable="true">
                            Id
                        </th>
                        <th data-field="name" data-sortable="true">Pay ID</th>
                        <th data-field="user" data-sortable="true">User</th>
					 <!-- <th data-field="time" data-sortable="true" data-formatter="dateFormatter"> </th>-->
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
                            data-sorter="priceSorter">Charge
                        </th>
                       
                        <th data-field="total" data-align="center" data-sortable="true"
                            data-sorter="priceSorter">Balance
                        </th>
                        <th data-field="status" data-align="center" data-sortable="true"
                            data-formatter="statusFormatter">Status
                        </th>
                        <th data-field="Checkstatus" data-align="center" data-sortable="true">Check Status
                        </th>
						@if(in_array(Auth::user()->role_id,array(1,5)))
                         <th data-field="refund" data-align="center" data-sortable="true">Refund
                        </th>
						@endif
                    </tr>
                    </thead>

                    <tbody>
                    @foreach($reports as $key => $value)
					<?php $s = $value->created_at;
						$dt = new DateTime($s);?>
                        <tr>
						 <td>{{ $dt->format('d/m/Y') }}<br>{{ $dt->format('H:i:s') }}</td>
                            <td>{{ $value->id }}</td>
                            <td>{{ $value->pay_id }}</td>
                            <td>
                                <a href="{{ url('user-recharge-report')}}/{{ $value->user_id }}">{{ $value->user->name }}</a>
                            </td>
                           
                            
                            <td>{{ $value->api->api_name }}</td>
                            <td>{{ @$value->provider->provider_name }}</td>
                            <td>{{ $value->number }}</td>
                            <td>{{ $value->txnid }}</td>
                            <td>{{ number_format($value->amount,2) }}</td>
                            <td>{{ number_format($value->profit,2) }}</td>
                          
                            <td>{{ number_format($value->total_balance,2) }}</td>
                            <td>{{ $value->status->status }}</td>
                          
                        @if($value->status_id==1 || $value->status_id==3)
                        <td><button onclick="recharge_check_status({{ $value->id }})" href="#" class="table-action-btn btn btn-primary btn-xs">Check</button></td>
                        @else
                        <td>--</td>
                       @endif
                       @if($value->refund==1 && in_array(Auth::user()->role_id,array(1,5)))
                       <td>
                         <a onclick="updateRecord({{ $value->id }})" href="#" class="table-action-btn">Refund</a>
                       </td>
                       @else
                       
                        @endif
                        </tr>

                    @endforeach

                    </tbody>
                </table>
                {!! $reports->links() !!}
           
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
                <!-- <form id="frmTasks" action="{{ url('report/update') }}" method="post" name="frmTasks"
                      class="form-horizontal" novalidate=""> -->
					  <form id="frmTasks" name="frmTasks" class="form-horizontal" novalidate="">
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
                            <label for="inputEmail3" class="col-sm-3 control-label">Amount</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control" id="amount" name="amount"
                                       placeholder="Provider Code" value="">
                            </div>
                        </div>
 

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-info waves-effect waves-light" id="btn-save"
                                value="add" onclick="this.disabled=true;refundTranaction()">Refund Now
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