@extends('admin.layouts.templatetable')

@section('content')
    <script>
        function add_record() {
            $('#btn-save').val("add");
            $('#frmTasks').trigger("reset");
            $("#con-close-modal").modal("toggle");
        }
        //create new task / update existing task
        function conversation_view(id)
        {
            var dataString = 'id='+id;
            $('#conv_id').val(id);
           var assignfor =  $('#assign_'+id).val();
           $('#assign').val(assignfor);
                $.ajax({
                type: "GET",
                url: "{{url('conversation-view')}}",
                data: dataString,
                 dataType: 'json',
                success: function (data) {
                    obj1=data.data;
                    var html = "";
                  for (var key in obj1) {
                     console.log(obj1);
                      var msg = obj1[key].message;
                    html += '<p><b>' + obj1[key].name+ '</b> : ' + msg + '</p><hr>';
                  }
                   $('#enquiry-history-modal').modal({
                    backdrop: 'static'
                   });
                   $('#conv').html(html);
                   $('#conv_comments').val('');
                 }
             })

        }
        function conversation_update()
        {

             var c_id = $('#conv_id').val();
             var assign =  $('#assign').val();
             var msg = $('#conv_comments').val();
             var dataString = 'conv_id='+c_id+'&msg='+msg+'&assign='+assign;
                $.ajax({
                type: "GET",
                url: "{{url('conversation-update')}}",
                data: dataString,
                 dataType: 'json',
                success: function (data) {
                    if(data.message!='')
                    {
                        conversation_view(c_id);
                    }
                 }
             })
        }
        function save_enquiry()
        {
          var assi  = $('#assigned').val();
          var msg = $('#message').val();
          var st =  $('#status_id').val();
          var enqid = $('#enquiry_id').val();
          var dataString = 'id=' + enqid + '&assign='+ assi +'&message='+ msg +'&status='+ st;
                $.ajax({
                type: "get",
                url: "{{url('update_enquiry')}}",
                data: dataString,
                success: function (data) {
                   if(data!='')
                   {
                        $('#status_id').val(data);
                        $('#status_id').html(data);

                   }
                   location.reload();
                    
                 }
             })

        }

        function updateRecord(id) {
        var assign = $('#assign_'+id).val();
        var message = $('#message_'+id).val();
        var enq_id = $('#enqid_'+id).val();
        var status = $('#stat_'+id).val();
        $('#enquiry_id').val(id);
        $('#assigned').val(assign);
        $('#message').val(message);
        $('#select_status').val(status);
        $('#select_status').html(status);

        $("#con-close-modal").modal("toggle");

        }
        function savedatacomm() {
            var url = "{{ url('memberd') }}";
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                }
            })
            var formData = {
                status_id: $('#status_idnew').val(),
                upscheme: $('#upschemenew').val(),
            }

            //used to determine the http verb to use [add=POST], [update=PUT]
            var state = $('#btn-savecomm').val();
            var type = "PUT"; //for creating new resource
            var task_id = $('#idnew').val();
            var my_url = url;
            my_url += '/' + task_id;
            $.ajax({
                type: type,
                url: my_url,
                data: formData,
                dataType: 'text',
                success: function (data) {
                    $("#con-close-modalcomm").modal("hide");
                    swal("Success", data, "success");
                    if (data == 'Successfully Added') {
                        $("#myModal").modal("hide");
                        location.reload();
                    } else {
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
                        location.reload();
                    }
                }

            });
        }
        function updateRecordcomm(id) {
            var token = $("input[name=_token]").val();
            var dataString = 'id=' + id + '&_token=' + token;
            $.ajax({
                type: "post",
                url: "{{url('member/view')}}",
                data: dataString,
                success: function (data) {
                    $('#idnew').val(data.id);
                    $('#name').val(data.name);
                    $('#mobile').val(data.mobile);
                    $('#email').val(data.email);
                    $('#kyc').val(data.kyc),
                    $('#office_address').val(data.office_address),
                    $('#member_type').val(data.membertype);
                    $('#role_id').val(data.role_id);
                    $('#api_code').val(data.api_code);
                    $('#agentcode').val(data.agentcode);
                    $('#status_idnew').val(data.status_id);
                    $('#upschemenew').val(data.upscheme);
                    $('#btn-savecomm').val("update");
                    $("#con-close-modalcomm").modal("toggle");
                }
            })

        }
        function savedatapass() {
            var url = "{{ url('memberdp') }}";
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                }
            })
            var formData = {
                password: $('#passwordnew').val(),
            }

            //used to determine the http verb to use [add=POST], [update=PUT]
            var state = $('#btn-savecomm').val();
            var type = "PUT"; //for creating new resource
            var task_id = $('#idnew').val();
            var my_url = url;
            my_url += '/' + task_id;
            $.ajax({
                type: type,
                url: my_url,
                data: formData,
                dataType: 'text',
                success: function (data) {
                    $("#con-close-modalcomm").modal("hide");
                    swal("Success", data, "success");
                    if (data == 'Successfully Added') {
                        $("#myModal").modal("hide");
                        location.reload();
                    } else {
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
                        location.reload();
                    }
                }

            });
        }
        function updatePassword(id) {
            var token = $("input[name=_token]").val();
            var dataString = 'id=' + id + '&_token=' + token;
            $.ajax({
                type: "post",
                url: "{{url('member/view')}}",
                data: dataString,
                success: function (data) {
                    $('#idnew').val(data.id);
                    $('#name').val(data.name);
                    $('#mobile').val(data.mobile);
                    $('#email').val(data.email);
                    $('#member_type').val(data.membertype);
                    $('#role_id').val(data.role_id);
                    $('#api_code').val(data.api_code);
                    $('#kyc').val(data.kyc),
                    $('#office_address').val(data.office_address),
                    $('#agentcode').val(data.agentcode);
                    $('#status_idnew').val(data.status_id);
                    $('#upschemenew').val(data.upscheme);
                    $('#btn-savecomm').val("update");
                    $("#con-close-modalpass").modal("toggle");
                }
            })

        }
    </script>
    <style>
    .Resolved .label-undefined
    {
    background:green;
    }
    .InProcess .label-undefined
    {
    background:blue;
    }
    .SentToBank .label-undefined
    {
        background:red;
    }
    .Pending .label-undefined
    {
        background:orange;
    }
    </style>

    <!-- Page-Title -->
    <div class="row">
        <div class="col-sm-12">
            <div class="col-lg-6 col-md-6">
                <h4 class="page-title" style="color: white; font-size: 36px;">{{ $page_title or 'MEMBER ENQUIRY' }}</h4>
            </div>
            <div class="col-lg-6 col-md-6">
               
				<div class="pull-right">
                    <button style="display:block" onclick="add_record()" id="demo-add-row" class="btn btn-success"><i
                                class="fa fa-plus m-r-5"></i>Add Query
                    </button>
                </div>
				
            </div>
        </div>
    </div><br>

    <!--Basic Columns-->
    <!--===================================================-->


    <!--===================================================-->
    <div class="row">
        <div class="col-sm-12">
            <div class="card-box">
                <table id="demo-custom-toolbar" data-toggle="table"
                       data-toolbar="#demo-delete-row"
                       data-search="true"
                       data-page-list="[20, 10, 20]"
                       data-page-size="10"
                       >
                    <thead>
                    <tr>
                    <th data-field="date_time" data-sortable="true">
                            Date
                        </th>
                        <th data-field="id" data-sortable="true">
                            ID
                        </th>
                        <th data-field="assigned" data-sortable="true">Assigned To</th>
                        <th data-field="name" data-sortable="true">Name</th>
                        <th data-field="date" data-sortable="true" data-formatter="dateFormatter">Mobile
                        </th>
                        
                        <th data-field="email" data-align="center" data-sortable="true"
                            data-sorter="priceSorter">Email
                        </th>
                        <th data-field="location" data-align="center" data-sortable="true"
                            data-sorter="priceSorter">Location
                        </th>
                        <th data-field="message" data-sortable="true"
                        >Message
                        </th>
                        <th data-field="sales_remark" data-align="center" data-sortable="true"
                        >Sales Remark
                        </th>
                        <th data-field="manager_remark" data-align="center"
                        >Manager Remark
                        </th>

                        <th data-field="status" data-align="center" data-sortable="true"
                            data-formatter="statusFormatter">Status</th>
                     
                            <th data-field="action" data-align="center" data-sortable="true">Action
                            </th>
                     
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($enquiry as $enquiries)
                        <tr>
                            <td>{{ $enquiries->created_at }}</td>
                            <td>{{ $enquiries->id }}</td>
                            <td><input type="hidden" id="assign_{{ $enquiries->id }}" value="{{ $enquiries->assigned }}">{{ $enquiries->assigned }}</td>
                            <td>{{ $enquiries->name }}</td>
                            <td>{{ $enquiries->mobile }}</td>
                            <td>{{ $enquiries->email }}</td>
                            <td>{{  $enquiries->location }}</td>
                            <td><input type="hidden" id="message_{{ $enquiries->id }}" value="{{ $enquiries->message }}">{{ $enquiries->message }}</td>
                            <td><textarea rows="3" cols="20" name="s_remark" id="s_remark"></textarea></td>
                            <td><a href="#" onclick="conversation_view({{ $enquiries->id }})" id="history">History</a><!--<textarea rows="3" cols="20" name="m_remark" id="m_remark"></textarea>--></td>
                             <td class="{{ $enquiries->status }}"><input type="hidden" id="stat_{{ $enquiries->id }}" value="{{ $enquiries->status }}">{{ $enquiries->status }}</td>
                                <td>
                                    <a onclick="updateRecord({{ $enquiries->id }})" href="#" class="table-action-btn"><i
                                                class="md md-edit"></i></a>
                                </td>
                          
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div id="con-close-modal-one" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
         aria-hidden="true" style="display: none;">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                    <h4 class="modal-title">Member Editor</h4>
                </div>

            </div>
        </div>
    </div><!-- /.modal -->
    
     <div id="con-close-modal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
         aria-hidden="true" style="display: none;">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                    <h4 class="modal-title">Enquiry Update</h4>
                </div>
                <div class="modal-body">
                    <div style="display:none" id="name-error"></div>

                    <form id="frmTasks" name="frmTasks" class="form-horizontal" novalidate="">
                        {!! csrf_field() !!}
                        <input type="hidden" name="user_id" id="user_id">
                        <input type="hidden" name="id" id="enquiry_id" value="">
                        <input type="hidden" class="re_" value="">
                        <input type="hidden" class="c_" value="">

                        <div class="form-group">
                            <label for="inputEmail3" class="col-sm-3 control-label">Assigned</label>
                            
                        <div class="col-sm-9">
 
                           <input type="text" id="assigned" rows="5" class="form-control" name="assign" value="">
                        
                        </div>
                        </div>
                        <div class="form-group">
                            <label for="inputEmail3" class="col-sm-3 control-label">Message</label>
                            <input type="hidden" id="c_id">
                            <div class="col-sm-9">
                            
                                <textarea id="message" rows="5" class="form-control" name="message" value=""></textarea>
                            </div>
                            </div>
                                
                        </div>
                        
                        <div class="form-group">
                            <label for="inputEmail3" class="col-sm-3 control-label">Status</label>
                            <div class="col-sm-9">
                                <select class="form-control" name="status_id" id="status_id">
                                    <option id="select_status"></option>
                                    <option value="Pending">Pending</option>
                                    <option value="Reject">Reject</option>
                                    <option value="InProcess">In Process</option>
                                    <option value="Resolved">Resolved</option>
                                </select>
                            </div>
                        </div><br><br><br><br>


                    </form>
                </div>
                <div class="modal-footer">
                    <button onclick="save_enquiry()" type="button" class="btn btn-info waves-effect waves-light"
                            id="btn-save"
                            value="add">Update Now
                    </button>
                    <button type="button" class="btn btn-default waves-effect" data-dismiss="modal">Close</button>
                    <input type="hidden" id="id" name="id" value="0">
                </div>
            </div>
        </div>
    


<div id="enquiry-history-modal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
         aria-hidden="true" style="display: none;">
        <div class="modal-dialog" style="overflow-y: initial;">
            <div class="modal-content" style="height: 400px;">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                    <h4 class="modal-title">Conversation Updates</h4>
                </div>
                <div class="modal-body">
                    <div style="display:none" id="name-error"></div>
                    
                            <div class="col-sm-12" id="conv">
                               
                            </div>
                       
                
                    </div>

                        
                </div>
                <div class="modal-footer">
                        {!! csrf_field() !!}
                        <input type="hidden" id="conv_id">
                        <input type="hidden" id="assign">
                        <div class="form-group">
                            <div class="col-sm-12">
                                <textarea id="conv_comments" rows="3" cols="67" placeholder="Enter Remark"></textarea>
                            </div>
                    <button type="button" onclick="conversation_update()" class="btn btn-info waves-effect waves-light pull-right" id="btn-save" value="add">Submit
                    </button>
                        </div>
                </div>
            </div>
        </div>
    


    <!-- reset Password Form -->

    
    <!-- END wrapper -->
    <meta name="_token" content="{!! csrf_token() !!}"/>
@endsection
