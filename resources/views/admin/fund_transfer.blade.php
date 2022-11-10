@extends('admin.layouts.template')

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
                    if(msg.success == 'success') {
                        swal("Success", msg.message, "success");
                        $("#trbutton").prop("disabled", false);
                    }else{
                        $("#trbutton").prop("disabled", false);
                        swal("Failure", msg.message, "error");
                    }

                }
            });
        }
    </script>
       
            <div class="col-sm-12">
                <h4 class="page-title">{{ $page_title or 'Fund Transfer' }}</h4>
                <ol class="breadcrumb">
                    <li>
                        <a href="{{ url('dashboard') }}">Home</a>
                    </li>
                    <li class="active">
                        {{ $page_title or 'Fund Transfer' }}
                    </li>
                </ol>
            </div>
        
        <div class="row">
            <div class="col-sm-12">
                <div class="card-box">
                    <div class="row">
                        {{ Form::open(array('url' => 'fund-transfer')) }}

                        {{ Form::close() }}
                        <div class="col-md-6">
                            <h5><b>Select Members</b></h5>
                            <div class="form-group">
                                <select id="user_id" class="form-control select2">
                                    @foreach($users as $user)
                                        <option value="{{ $user->id }}">
                                            {{ strtoupper($user->name) .' (' . $user->id . ') M'. $user->mobile . ' ' . $user->balance->user_balance }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Remark / Reference ID</label>
                                <input type="text" id="ref_id" name="refid" class="form-control">
                            </div>
                            <div class="form-group">
                                <label>Amount</label>
                                <input type="number" name="amount" id="amount" class="form-control">
                            </div>
                            <div class="form-group">
                                <label>Commission</label>
                                <input type="number" name="profit" id="commission" class="form-control">
                            </div>
                            <div class="form-group">
                                <button onclick="transfernow()" id="trbutton" class="btn btn-success"><i
                                            class="fa fa-ruppe"></i>Transfer
                                </button>
                            </div>
                        </div>
                        <div class="col-md-6">
                        </div>
                    </div>
                </div>
            </div>
        </div>

@endsection