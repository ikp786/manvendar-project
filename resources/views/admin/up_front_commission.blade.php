@extends('admin.layouts.templatetable')

@section('content')

    <script>
        function transfernow() {
            $("#trbutton").text("Processing");
            $("#trbutton").prop("disabled", true);
            $("#btn").text("Processing");
            $("#btn").prop("disabled", true);
            var token = $("input[name=_token]").val();
            var amount = $("#amount").val();
            var user_id = $("#user_id").val();
            var ref_id = $("#ref_id").val();
            var commission = $("#commission").val();
            var dataString = 'amount=' + amount + '&user_id=' + user_id + '&ref_id=' + ref_id + '&commission=' + commission + '&_token=' + token;
            $.ajax({
                type: "POST",
                url: "{{url('fund-transfer')}}",
                data: dataString,
                success: function (msg) {
                    if (msg.success == 'success') {
                        swal("Success", msg.message, "success");
                        $("#trbutton").prop("disabled", false);
                    } else {
                        $("#trbutton").prop("disabled", false);
                        swal("Failure", msg.message, "error");
                    }

                }
            });
        }
    </script>
    <div class="row">
        <div class="col-sm-12">
            <h4 class="page-title" style="color: white; font-size: 36px;">{{ $page_title or 'UP FRONT COMMISIION' }}</h4>
        </div>
    </div><br>
    <div class="row">
        <div class="col-sm-12">
            <div class="card-box">
                <div class="row">
                    {{ Form::open(array('url' => 'up-commission' , 'method' => 'post')) }}
                    <div class="form-group">
                        <input class="form-control" type="upscheme" name="upscheme">
                    </div>
                    <button class="btn btn-primary" type="submit">Save Scheme</button>
                    {{ Form::close() }}
                    <div class="col-md-6">
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection