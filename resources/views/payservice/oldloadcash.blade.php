@extends('layouts.template')


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
                    $("#paybank").hide();
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
            var payba = $("#paybankaccount").val();
            var yacc = $("#yourbankaccount").val();
            var bankref = $("#refid").val();
            if (amount != '' && bankref != '') {
                var dataString = 'amount=' + amount + '&pmethod=' + pmethod + '&payba=' + payba + '&yacc=' + yacc + '&bankref=' + bankref + '&_token=' + token;
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

    <!-- MAIN CONTENT -->
    <div class="pg-opt">
        <div class="container">
            <div class="row">
                <div class="col-md-6">
                    
                </div>
                <div class="col-md-6">
                    <ol class="breadcrumb">
                        <li><a href="{{ url('/home') }}">Home</a></li>
                        <li>Load Cash</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <section class="slice bg-white bb">
        <div class="wp-section shop">
            <div class="container">
                <div class="row">
                    <div class="col-md-8">
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
					<div class="pull-right">
						<img src="images/levinm-int.jpg">
                    </div>
					<div class="pull-right">
						<img src="images/master-visa-card.png">
                    </div>
					<!-- Cart table -->
                        <!--<table class="table table-cart table-responsive" border="1">
                            <tr>
                                <th>ID</th>
                                <th>User</th>
                                <th>Payment Method</th>
                                <th>Bank Name</th>
                                <th>Reference ID</th>
                                <th>Amount</th>
                                <th>Status</th>
                            </tr>
                            <tbody>
                            @foreach($loadcashes as $loadcash)
                                <tr>
                                    <td>{{ $loadcash->id }}</td>
                                    <td>{{ $loadcash->user->name }}</td>
                                    <td>{{ $loadcash->pmethod->payment_type }}</td>
                                    <td>{{ $loadcash->netbank->bank_name }}</td>
                                    <td>{{ $loadcash->bankref }}</td>
                                    <td>{{ $loadcash->amount }}</td>
                                    <td>{{ $loadcash->status->status }}</td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>-->

                        
                            
                        
                    </div>

                    <div class="col-md-4">
                        <div class="panel panel-default panel-sidebar-1">
                            <div class="panel-heading">
                                <h2>Balance Request</h2>
                            </div>
                            {{ Form::open(array('url' => 'load-cash', 'method' => 'POST', 'class' => 'form-light')) }}
                            <div class="panel-body bb">
                                <div class="col-md-12 input-group">
                                    <input id="amount" name="amount" placeholder="Enter Amount" type="text"
                                           class="form-control">
                                </div>
                            </div>
                            
                            <input type="hidden" name="loadpage" value="1">
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
                                        <span>Proceed to Pay</span>
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
            </div>
        </div>
    </section>
@endsection