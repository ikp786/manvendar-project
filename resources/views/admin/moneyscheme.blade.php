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
        var url = "money-scheme-manage";
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
    function CommissionPage(id){
        
    }
</script>

    <!-- Page-Title -->
    <div class="row">
        <div class="col-sm-12">
            <div class="col-lg-6 col-md-6">
                <h3 class="page-title" style="color: white;">{{  'SCHEME DETAIL' }}</h3>
                
            </div>
            <div class="col-lg-6 col-md-6">
                <div class="pull-right">
                    <button onclick="add_record()" id="demo-add-row" class="btn btn-success"><i
                                class="fa fa-plus m-r-5"></i>Add Record
                    </button>
                </div>
            </div>
        </div>
    </div><br>

    <!--Basic Columns-->
    <!--===================================================-->


    <!--===================================================-->
   <div class="panel-body">
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
                                            
                            <td>{{ Form::open(array('url' => 'money-commission-manage/viewupdate')) }}
                                {{ Form::hidden('id', $value->id) }}
                                {{ Form::submit('View Commission', array('class' => 'btn btn-success')) }}
                                {{ Form::close() }}
                            </td>
                            
                            <td><a onclick="updateRecord({{ $value->id }})" href="#" class="table-action-btn">Edit</a>
                               
                            </td>
                                            
                                               
                                        </tr>
                                        @endforeach  
                                    </tbody>
                                </table>
                                
                            </div>
                        </div>

<div class="modal fade" id="myModalrefund" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
               <!--  <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button> -->
                <h4 class="modal-title" id="myModalLabel"><b>SUPER</b> Transaction Refund</h4>
            </div>
            <div class="modal-body">
                <form class="form-horizontal">
                    <input id="c_bene_id" type="hidden">
                    <input id="c_sender_id" type="hidden">
                    <div class="form-group">
                        <label for="bank_account" class="control-label col-sm-4"> Reference ID </label>
                            <div class="col-sm-6">
                                <input type="text" class="form-control" disabled id="payid" placeholder="Reference ID">
                            </div>
                    </div>
                    <div class="form-group">
                        <input type="hidden" name="api" id="api">
                        <label for="bank_account" class="control-label col-sm-4"> Customer Number </label>
                        <div class="col-sm-6">
                            <input type="text" class="form-control" disabled id="customer_number" placeholder="Customer Number">
                        </div>
                    </div>
                    <div class="form-group">
                            <label for="bank_account" class="control-label col-sm-4"> Transaction id </label>
                            <div class="col-sm-6">
                                <input type="text" class="form-control" disabled id="txnid" placeholder="Transaction id">
                            </div>
                    </div>
                    <div class="form-group">
                        <label for="bank_account" class="control-label col-sm-4"> Amount </label>
                        <div class="col-sm-6">
                            <input type="text" disabled class="form-control" id="amount" placeholder="Enter Amount">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="bank_account" class="control-label col-sm-4"> OTP </label>
                        <div class="col-sm-6"> 
                            <input type="text" class="form-control" id="otp" placeholder="Ender OTP">
                        </div>
                    </div>
                </form>
            </div>

            <div class="modal-footer">
                
                <button id="superOtp" type="button" onclick="this.disabled=true;superOtp()" class="btn btn-success">resend Otp</button>

                <button id="superRefund" type="button" onclick="this.disabled=true;superRefund()" class="btn btn-success">      Refund Now
                </button>

            </div>
            </div>
        </div>
    </div>

<div class="modal fade" id="saralRefundModel" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel"><b>SARAL</b> Transaction Refund</h4>
            </div>
            <div class="modal-body">
                <form class="form-horizontal">
                    <input id="c_bene_id" type="hidden">
                    <input id="c_sender_id" type="hidden">
                    <div class="form-group">
                        <label for="saral_ref_id" class="control-label col-sm-4"> Reference ID </label>
                        <div class="col-sm-6">
                            <input type="text" class="form-control" disabled id="saral_payid" placeholder="Reference ID">
                        </div>
                    </div>
                    <div class="form-group">
                        <input type="hidden" name="api" id="api">
                        <label for="saral_number" class="control-label col-sm-4"> Customer Number </label>
                        <div class="col-sm-6">
                            <input type="text" class="form-control" disabled id="saral_customer_number" placeholder="Customer Number">
                        </div>
                    </div>
                    <div class="form-group">
                            <label for="saral_txnid" class="control-label col-sm-4"> Transaction id </label>
                            <div class="col-sm-6">
                                <input type="text" class="form-control" disabled id="saral_txnid" placeholder="Transaction id">
                            </div>
                    </div>
                    <div class="form-group">
                        <label for="amount" class="control-label col-sm-4"> Amount </label>
                        <div class="col-sm-6">
                            <input type="text" disabled class="form-control" id="saral_amount" placeholder="Enter Amount">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="saral_otp" class="control-label col-sm-4"> OTP </label>
                        <div class="col-sm-6"> 
                            <input type="text" class="form-control" id="saral_otp" placeholder="Ender OTP">
                        </div>
                    </div>
                </form>
            </div>

            <div class="modal-footer">
                <button id="saralOtp" type="button" onclick="this.disabled=true;saralRefundOtp();" class="btn btn-primary">resend Otp</button>
                <button id="saralRefund" type="button" onclick="this.disabled=true;saral_refund_success();" class="btn btn-primary">Refund Now
                </button>

            </div>
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
                                   placeholder="Scheme Name" value="">
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
