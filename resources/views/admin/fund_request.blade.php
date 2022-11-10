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
            var url = "api-manage";
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                }
            })
            var formData = {
                api_name: $('#api_name').val(),
                api_url: $('#api_url').val(),
                username: $('#username').val(),
                password: $('#password').val(),
                api_key: $('#api_key').val(),
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
                url: "{{url('api-manage/view')}}",
                data: dataString,
                success: function (data) {
                    $('#id').val(data.id);
                    $('#api_name').val(data.api_name);
                    $('#api_url').val(data.api_url);
                    $('#username').val(data.username);
                    $('#password').val(data.password);
                    $('#api_key').val(data.api_key);
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
                <h4 class="page-title" style="color:white">{{ $page_title or 'Fund Request' }}</h4>
                <ol class="breadcrumb">
                    <li>
                        <a href="{{ url('dashboard') }}" style="color:#f89923">Home</a>
                    </li>
                    <li class="active" style="color:white">
                        {{ $page_title or 'Fund Request' }}
                    </li>
                </ol>
            </div>
        </div>
    </div>

    <!--Basic Columns-->
    <!--===================================================-->


    <!--===================================================-->
    <div class="row">
        <div class="col-sm-6">

            <div class="card-box">
                @if (session('status'))
                    <div class="alert alert-success">
                        {{ session('status') }}
                    </div>
                @endif
                <h4 class="m-t-0 header-title"><b>{{ $page_title or 'Fund Request' }}</b></h4>
                <p class="text-muted font-13">
                    Add Fund Request
                </p>
                {{ Form::open(array('url' => 'fund-request-save', 'method' => 'POST', 'class' => 'form-light','files' => true)) }}

<div class="form-group">

<select name="wallet" class="form-control">
<option value="0"> Money wallet</option>
<!-- <option value="1"> Recharge Wallet</option> -->

</select>

                </div>
                
                <div class="form-group">

                    <input type="text" name="amount" class="form-control" placeholder="Amount">
                </div>



                <div class="form-group">


                    {{ Form::select('pmethod', $pmethods,'db', ['class' => 'form-control select2me', 'id' => 'myid']) }}

                </div>
                <div class="form-group">


                    {{ Form::select('paybankaccount', $netbankings_pay,'db', ['class' => 'form-control select2me', 'id' => 'paybankaccount']) }}

                </div>
                <div class="form-group">


                    <input type="date" name="dod" id="date" placeholder="Date" class="form-control">

                </div>
                <div class="form-group">

                    <input type="text"   class="form-control" placeholder="Bank Reference Number" name="bankref">

                </div>
				<div class="form-group">
					 <label for="">Deposit Fund Slip</label>
                   {{ Form::file('d_picture', array('class' => 'form-control','id' => 'd_picture')) }}

                </div>
                <div class="form-group">

                    <label for="">Remark</label>
                    <textarea rows="2" class="form-control" name="remark"></textarea>

                </div>
                <button type="submit" class="btn btn-warning">Request Payment</button>

            </div>
        </div>
    </div>



    <meta name="_token" content="{!! csrf_token() !!}"/>
    <!-- END wrapper -->
@endsection
