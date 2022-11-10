@extends('layouts.demo_template')


@section('content')

    <script>
        $(function () {
            $('#myid').change(function (e) {
                var id = $("#myid").val();
                if (id == 1) {
                    $("#netbank").hide();
                    $("#paybank").show();
                    $("#yourbank").show();
                    $("#btnn").hide();
                    $("#btn").show();
                    $("#refbox").show();
                }
                if (id == 2) {
                    $("#paybank").show();
                    $("#yourbank").hide();
                    $("#btnn").hide();
                    $("#netbank").hide();
                    $("#btn").show();
                    $("#refbox").show();
                }
                if (id == 3) {
                    $("#paybank").show();
                    $("#yourbank").hide();
                    $("#btn").show();
                    $("#btnn").hide();
                    $("#netbank").hide();
                    $("#refbox").show();
                }
                if (id == 4) {
                    $("#refbox").hide();
                    $("#paybank").hide();
                    $("#btn").hide();
                    $("#yourbank").hide();
                    $("#btnn").show();
                    $("#netbank").show();
                }
                if (id == 5) {
                    $("#refbox").hide();
                    $("#yourbank").hide();
                    $("#btn").hide();
                    $("#btnn").show();
                    $("#netbank").hide()
                    $("#refbox").hide();
                }
                if (id == 6) {
                    $("#refbox").show();
                    $("#yourbank").hide();
                    $("#btn").shwo();
                    $("#btnn").hide();
                    $("#refbox").show();
                }
            });
        });
        function sendDetail() {
            var btn = $("#btn").text();
            $("#btn").text("Successfully Submited");
            $("#btn").prop("disabled", true);
            var token = $("input[name=_token]").val();
            var amount = $("#amount").val();
            var pmethod = $("#myid").val();
            var wal = $("#wal").val();
            var payba = $("#paybankaccount").val();
            var yacc = $("#yourbankaccount").val();
            var bankref = $("#refid").val();
            if (amount != '' && bankref != '') {
                var dataString = 'amount=' + amount + '&pmethod=' + pmethod + '&payba=' + payba + '&yacc=' + yacc + '&bankref=' + bankref + '&wallet=' + wal + '&_token=' + token;
                
                $.ajax({
                    type: "POST",
                    url: "{{url('loadcash')}}",
                    data: dataString,
                    success: function (msg) {
                        $("#btn").prop("disabled", false);
                        swal("Success", 'success', "success");
                        location.reload();
                    }
                });
            } else {
                $("#btn").prop("disabled", false);
                location.reload();
                swal("Failure", 'All Feilds are required', "error");
            }
        }
        function sendDetailnet() {
            $("#btnn").text("Processing");
            $("#sub").click();
        }
    </script>


    <!-- Optional header components (ex: slider) -->
    <!-- Importing slider content -->
    <!-- include('includes.sliders.layer-slider-shop') -->


    <div class="row"><br>
        <div class="col-md-8" style="background:#115798; color:white;">
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
        <!-- Cart table -->
            <table class="table table-cart table-responsive">
                <tr>
                    <td>ID</td>
                    <td>Date & Time</td>
                    <td>Payment Method</td>
                    <td>Bank Name</td>
                    <td>Reference ID</td>
                    <td>Amount</td>
                    <td>Request Type</td>
                    <td>Status</td>
                </tr>
                <tbody>
                @foreach($loadcashes as $loadcash)
                    <tr>
                        <td>{{ $loadcash->id }}</td>
                        <td>{{ $loadcash->created_at }}</td>
                        <td>{{ $loadcash->pmethod->payment_type }}</td>
                        <td>{{ $loadcash->netbank->bank_name }}</td>
                        <td>{{ $loadcash->bankref }}</td>
                        <td>{{ $loadcash->amount }}</td>
                        <td>{{ ($loadcash->wallet == 1) ? 'Recharge' : 'Money' }}</td>
                        <td>{{ $loadcash->status->status }}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>

                {!! $loadcashes->links() !!}

        </div>

        <div class="col-md-4">
            <div class="panel panel-default panel-sidebar-1" style="background:#115798 !important;">
                <div class="panel-heading" style="background:#115798 !important; color:white !important;">
                    <h2>Balance Request</h2>
                </div>
                {{ Form::open(array('url' => 'load-cash', 'method' => 'POST', 'class' => 'form-light')) }}
                <div class="panel-body bb">
                    <div class="col-md-12 input-group">
                        <select class="form-control" id="wal">
                            @if(Auth::user()->id!=113)
							<option value="0">Money Transfer</option>
							@endif
                            <option value="1">Recharge</option>
                        </select>
                    </div>
                </div>
                <div class="panel-body bb">
                    <div class="col-md-12 input-group">
                        <input id="amount" name="amount" placeholder="Enter Amount" type="text"
                               class="form-control">
                    </div>
                </div>
                <div class="panel-body bb">
                    <table class="table table-cart-subtotal">
                        <tbody>
                        <tr>
                            <td colspan="2" class="no-padding">
                                <div class="form-group">
                                    {{ Form::select('pmethod', $pmethods,'db', ['class' => 'form-control select2me', 'id' => 'myid']) }}

                                </div>
                            </td>
                        </tr>
                        
                        <tr style="display:none" id="netbank">
                            <td colspan="2" class="no-padding">
                                <div class="form-group">
                                    {{ Form::select('netbanking', $netbankings, '',array('class' => 'form-control', 'id' => 'netbankking')) }}
                                </div>
                            </td>
                        </tr>


                        <tr style="display: none;" id="paybank">
                            <td colspan="2" class="no-padding">
                                <div class="form-group">
                                    {{ Form::select('paybankaccount', $netbankings_pay, '',array('class' => 'form-control', 'id' => 'paybankaccount')) }}
                                </div>
                            </td>
                        </tr>
                        <tr style="display:none" id="yourbank">
                            <td colspan="2" class="no-padding">
                                <div class="form-group">
                                    {{ Form::select('yourbankname', $netbankings, '',array('class' => 'form-control', 'id' => 'yourbankname')) }}
                                </div>
                            </td>
                        </tr>
                        <tr style="display:none" id="refbox">
                            <td colspan="2" class="no-padding">
                                <div class="form-group">
                                    <input id="refid" placeholder="Enter Reference Number/Branch Code "
                                           type="text" class="form-control left">
                                </div>
                            </td>
                        </tr>
                        </tbody>
                    </table>
					
                </div>
                <button type="submit" id="sub" style="display:none"></button>
                {{ Form::close() }}
                <div class="row">
                    <div class="col-md-12">
                        <button id="btn" onclick="sendDetail()"
                                class="btn btn-lg btn-block btn-alt btn-icon btn-icon-right btn-icon-go pull-right">
                            <span>Send Request</span>
                        </button>
                        <button style="display:none" id="btnn" onclick="sendDetailnet()"
                                class="btn btn-lg btn-block btn-alt btn-icon btn-icon-right btn-icon-go pull-right">
                            <span>Continue</span>
                        </button>
                    </div>
                </div>
            </div>

        </div>
    </div>
@endsection