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
            $("btn-save").prop("disabled", true);
            var url = "{{ url('employee') }}";
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                }
            })
            var formData = {
                name: $('#name').val(),
                mobile: $('#mobile').val(),
                email: $('#email').val(),
                pin_code: $('#pin_code').val(),
                company: $('#company').val(),
                agentcode: $('#agentcode').val(),
                address: $('#address').val(),
                pan_number: $('#pan').val(),
                parent_id: $('#parent_id').val(),
                role_id: $('#role_id').val(),
                status_id: $('#status_id').val(),
                scheme_id: $('#scheme_id').val(),
                upscheme: $('#upscheme').val(),
                kyc: $('#kyc').val(),
                office_address: $('#office_address').val(),
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
                dataType: "json",
                success: function (data) {
                    if (data.status == 'success') {
                        alert(data.message);
                        location.reload();
                    }
                    if (data.status == 'failure') {
                        alert(data.message);
                        //location.reload();
                    } else {
                        var errorString = '<div class="alert alert-danger"><ul>';
                        $.each(data.errors, function (key, value) {
                            errorString += '<li>' + value + '</li>';
                        });
                        errorString += '</ul></div>';
                        $("#name-error").show();
                        $('#name-error').html(errorString); //appending to a <div id="form-errors"></div> inside form

                    }
                }

            });
        }
        function updateRecord(id) {
          alert("Please contact customer care");
            var token = $("input[name=_token]").val();
            var dataString = 'id=' + id + '&_token=' + token;
            $.ajax({
                type: "post",
                url: "{{url('member/view')}}",
                data: dataString,
                dataType: "json",
                success: function (data) {
                    $('#id').val(data.id);
                    $('#name').val(data.name);
                    $('#mobile').val(data.mobile);
                    $('#pan').val(data.pan);
                    $('#email').val(data.email);
                    $('#company').val(data.company);
                    $('#member_type').val(data.membertype);
                    $('#kyc').val(data.kyc),
                    $('#office_address').val(data.office_address),
                    $('#pin_code').val(data.pin_code);
                    $('#address').val(data.address);
                    $('#role_id').val(data.role_id);
                    $('#parent_id').val(data.parent_id);
                    $('#agentcode').val(data.agentcode);
                    $('#api_code').val(data.api_code);
                    $('#scheme_id').val(data.scheme_id);
                    $('#status_id').val(data.status_id);
                    $('#upscheme').val(data.upscheme);
                    $('#btn-save').val("update");
                    $("#con-close-modal").modal("toggle");
                }
            })

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

    <!-- Page-Title -->
    <div class="row">
        <div class="col-sm-12">
            <div class="col-lg-6 col-md-6">
                <h4 class="page-title">{{ $page_title or 'Member Detail' }}</h4>
                <ol class="breadcrumb">
                    <li>
                        <a href="{{ url('dashboard') }}">Home</a>
                    </li>
                    <li class="active">
                        {{ $page_title or 'Member Detail' }}
                    </li>
                </ol>
            </div>
            <div class="col-lg-6 col-md-6">
               
				<div class="pull-right">
                    <button style="display:block" onclick="add_record()" id="demo-add-row" class="btn btn-success"><i
                                class="fa fa-plus m-r-5"></i>Add Member
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
                <table id="demo-custom-toolbar" data-toggle="table"
                       data-toolbar="#demo-delete-row"
                       data-search="true"
                       data-show-refresh="true"
                       data-show-toggle="true"
                       data-show-columns="true"
                       data-page-list="[20, 10, 20]"
                       data-page-size="10"
                       data-pagination="true" data-show-pagination-switch="true" class="table-bordered ">
                    <thead>
                    <tr>
                        <th data-field="id" data-sortable="true">
                            ID
                        </th>
                        <th data-field="name" data-sortable="true">Name</th>
                        <th data-field="date" data-sortable="true" data-formatter="dateFormatter">Mobile
                        </th>
                        <th data-field="balance" data-align="center" data-sortable="true"
                            data-sorter="priceSorter">Department
                        </th>
                        
                        <th data-field="amount" data-align="center" data-sortable="true"
                            data-sorter="priceSorter">Member Type
                        </th>
                        <th data-field="parent_name" data-sortable="true"
                        >Parent Name
                        </th>
                        <th data-field="pin_code" data-align="center" data-sortable="true"
                        >Pin Code
                        </th>
                        <th data-field="scheme" data-align="center"
                        >Scheme
                        </th>
                        @if(Auth::user()->role_id < 4)
                            <th data-field="reset" data-align="center"
                            >Reset
                            </th>
                        @endif
                        <th data-field="status" data-align="center" data-sortable="true"
                            data-formatter="statusFormatter">Status
                        </th>
                     <th data-field="statusreport" data-align="center" data-sortable="true"
                            data-formatter="statusFormatter">Report
                        </th>
                        @if(Auth::user()->role_id <= 3)
                            <th data-field="action" data-align="center" data-sortable="true">Action
                            </th>
                        @endif
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($users as $user)
                        <tr>
                            <td>{{ $user->id }}</td>
                            <td>{{ $user->name }}</td>
                            <td>{{ $user->mobile }}</td>
                            <td></td>
                            <td></td>
                            <td>{{ $user->role->role_title }}</td>
                            <td>{{ \App\User::find($user->parent_id)->name }} ({{ $user->parent_id }})</td>
                            <td>{{ $user->member->pin_code }}</td>
                            <td><a onclick="updateRecordcomm({{ $user->id }})" href="#"
                                   class="table-action-btn">{{ $user->upscheme->scheme }}</a>
                            </td>
                            @if(Auth::user()->role_id < 4)
                                <td><a onclick="updatePassword({{ $user->id }})" href="#"
                                       class="table-action-btn">Reset</a></td>
                            @endif
                            <td>{{ $user->status->status }}</td>
         @if(Auth::user()->role_id == 1)
                                <td><a href="https://partners.levinm.com/user-recharge-report/{{ $user->id }}"
                                       class="table-action-btn">Report</a></td>
                            @endif
                            @if(Auth::user()->role_id <= 3)
                                <td>
                                    <a onclick="updateRecord({{ $user->id }})" href="#" class="table-action-btn"><i
                                                class="md md-edit"></i></a>
                                </td>
                            @endif
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
            <div class="modal-content p-0">
                <ul class="nav nav-tabs navtab-bg nav-justified">
                    <li class="active">
                        <a href="#home-2" data-toggle="tab" aria-expanded="false">
                            <span class="visible-xs"><i class="fa fa-home"></i></span>
                            <span class="hidden-xs">Home</span>
                        </a>
                    </li>
                    <li class="">
                        <a href="#profile-2" data-toggle="tab" aria-expanded="false">
                            <span class="visible-xs"><i class="fa fa-user"></i></span>
                            <span class="hidden-xs">Profile</span>
                        </a>
                    </li>

                </ul>
                <div class="tab-content">
                    <div style="display:none" id="name-error">dd</div>
                    <div class="tab-pane active" id="home-2">
                        <div>
                            <div class="modal-body">

                                <form>
                                    {!! csrf_field() !!}
                                </form>
                                <div id="frmTasks" name="frmTasks" class="form-horizontal">
                                    <div class="form-group">
                                        <label for="inputTask" class="col-sm-3 control-label">Full Name</label>
                                        <div class="col-sm-9">
                                            <input type="text" class="form-control has-error" id="name" name="task"
                                                   placeholder="Full Name" value="">
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="inputEmail3" class="col-sm-3 control-label">Mobile Number</label>
                                        <div class="col-sm-9">
                                            <input type="text" class="form-control" id="mobile" name="mobile"
                                                   placeholder="Mobile number" value="">
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="inputEmail3" class="col-sm-3 control-label">Email</label>
                                        <div class="col-sm-9">
                                            <input type="text" class="form-control" id="email" name="email"
                                                   placeholder="Email Id" value="">
                                        </div>
                                    </div>
                                    <!--<div class="form-group">
                                        <label for="inputEmail3" class="col-sm-3 control-label">Pan Number</label>
                                        <div class="col-sm-9">
                                            <input type="text" class="form-control" id="pan" name="pan"
                                                   placeholder="Pan Number" value="">
                                        </div>
                                    </div>-->
                                    <div class="form-group">
                                        <label for="inputEmail3" class="col-sm-3 control-label">Company Name</label>
                                        <div class="col-sm-9">
                                            <input type="text" class="form-control" id="company" name="company"
                                                   placeholder="Shop Name" value="">
                                        </div>
                                    </div>
                                    @if(Auth::user()->role_id <= 3)
                                        <div class="form-group">
                                            <label for="inputEmail3" class="col-sm-3 control-label">Member Type</label>
                                            <div class="col-sm-9">
                                                {{ Form::select('role_id', $roles, old('role_id'), array('class' => 'form-control','id' => 'role_id')) }}
                                            </div>
                                        </div>
                                    @endif
                                    @if(Auth::user()->role_id <= 3)
                                        <div class="form-group">
                                            <label for="inputEmail3" class="col-sm-3 control-label">Parent
                                                Detail</label>
                                            <div class="col-sm-9">
                                                <select name="parent_id" id="parent_id" class="form-control select2">
                                                    <option value="{{ Auth::id() }}">{{ Auth::user()->name }}</option>
                                                    @foreach($parent_id as $parent_id)

                                                        <option value="{{ $parent_id->id }}">
                                                            {{ strtoupper($parent_id->name) .' (' . $parent_id->id . ') M'. $parent_id->mobile . ' ' . $parent_id->balance->user_balance }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    @endif
                                    <div class="form-group">
                                        <label for="inputEmail3" class="col-sm-3 control-label">Address Detail</label>
                                        <div class="col-sm-9">
                                            <textarea id="address" class="form-control">New Delhi </textarea>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="inputEmail3" class="col-sm-3 control-label">Office/Shop Address</label>
                                        <div class="col-sm-9">
                                            <textarea id="office_address" class="form-control">New Delhi </textarea>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="inputEmail3" class="col-sm-3 control-label">Profile Picture</label>
                                        <div class="col-sm-9">
                                            {{ Form::file('profile_picture', array('class' => 'form-control','id' => 'profile_picture')) }}
                                        </div>
                                    </div>
@if(Auth::user()->role_id == 1)
                                   <!-- <div class="form-group">
                                        <label for="inputEmail3" class="col-sm-3 control-label">Member Type</label>
                                        <div class="col-sm-9">
                                            <select id="kyc" name="kyc" class="form-control">
                                                <option value="KYC-PENDING">KYC Pending</option>
                                                <option value="KYC-COMPLETE">KYC Complete</option>
                                            </select>
                                        </div>
                                    </div>-->
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane" id="profile-2">
                        <div id="frmTasks" name="frmTasks" class="form-horizontal">
                           <!-- <div class="form-group">
                                <label for="inputTask" class="col-sm-3 control-label">Scheme</label>
                                <div class="col-sm-9">
                                    {{ Form::select('scheme_id', $schemes, old('schemes_id'), array('class' => 'form-control','id' => 'scheme_id')) }}
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="inputTask" class="col-sm-3 control-label">Up Front Scheme</label>
                                <div class="col-sm-9">
                                    {{ Form::select('upscheme', $upfront, old('upscheme'), array('class' => 'form-control','id' => 'upscheme')) }}
                                </div>
                            </div>-->
                            <div class="form-group">
                                <label for="inputEmail3" class="col-sm-3 control-label">Response URL</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" id="mobile" name="response_url"
                                           placeholder="Response Url" value="">
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="inputEmail3" class="col-sm-3 control-label">Pin Code</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" id="pin_code" name="pin_code"
                                           placeholder="Pin Code" value="">
                                </div>
                            </div>
                            <!--<div class="form-group">
                                <label for="inputEmail3" class="col-sm-3 control-label">Agent Code</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" id="agentcode" name="agentcode"
                                           placeholder="Agent Code" value="">
                                </div>
                            </div>-->
                            <div class="form-group">
                                <label for="inputEmail3" class="col-sm-3 control-label">Member Type</label>
                                <div class="col-sm-9">
								@if(Auth::user()->role_id == 1)
								  <select id="status_id" name="status_id" class="form-control">
                                        <option value="0">Disabled</option>
                                        <option value="1">Active</option>
                                    </select>
									@else
                                    <select id="status_id" name="status_id" class="form-control">
                                        <option value="0">Disabled</option>
                                      
                                    </select>
									@endif
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button onclick="savedata()" type="button" class="btn btn-info waves-effect waves-light"
                                id="btn-save"
                                value="add">Save Now
                        </button>
                        <button type="button" class="btn btn-default waves-effect" data-dismiss="modal">Close
                        </button>
                        <input type="hidden" id="id" name="id" value="0">
                    </div>
                </div>

            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->

    <div id="con-close-modalcomm" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
         aria-hidden="true" style="display: none;">


        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel">Update Dealer</h4>
                </div>
                <div class="modal-body">
                    <div id="frmTasks" name="frmTasks" class="form-horizontal">
                        <div class="form-group">
                            <label for="inputTask" class="col-sm-3 control-label">Up Front Scheme</label>
                            <div class="col-sm-9">
                                {{ Form::select('upscheme', $upfront, old('upscheme'), array('class' => 'form-control','id' => 'upschemenew')) }}
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="inputEmail3" class="col-sm-3 control-label">Member Type</label>
                            <div class="col-sm-9">
                                <select id="status_idnew" name="status_id" class="form-control">
                                    <option value="0">Disabled</option>
                                    <option value="1">Active</option>
                                    <option value="1">Success</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button onclick="savedatacomm()" type="button" class="btn btn-info waves-effect waves-light"
                            id="btn-savecomm" value="add">Update Now
                    </button>
                    <button type="button" class="btn btn-default waves-effect" data-dismiss="modal">Close
                    </button>
                    <input type="hidden" id="idnew" name="idnew" value="0">


                </div>
            </div>
        </div>


    </div><!-- /.modal -->


    <!-- reset Password Form -->

    <div id="con-close-modalpass" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
         aria-hidden="true" style="display: none;">


        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel">Update Password</h4>
                </div>
                <div class="modal-body">
                    <div id="frmTasks" name="frmTasks" class="form-horizontal">
                        <div class="form-group">
                            <label for="inputTask" class="col-sm-3 control-label">New Password</label>
                            <div class="col-sm-9">
                                <input type="text" name="newpassword" id="passwordnew" placeholder="New Password"
                                       class="form-control">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button onclick="savedatapass()" type="button" class="btn btn-info waves-effect waves-light"
                            id="btn-savecomm" value="add">Update Now
                    </button>
                    <button type="button" class="btn btn-default waves-effect" data-dismiss="modal">Close
                    </button>
                    <input type="hidden" id="idnew" name="idnew" value="0">


                </div>
            </div>
        </div>


    </div><!-- /.modal -->
    <!-- END wrapper -->
    <meta name="_token" content="{!! csrf_token() !!}"/>
@endsection
