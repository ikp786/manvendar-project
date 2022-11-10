@extends('admin.layouts.templatetable')


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



    <!-- Page-Title -->
    <div class="row">
        <div class="col-sm-12">
            <div class="col-lg-6 col-md-6">
                <h4 class="page-title" style="color: white; ">{{ $page_title or 'Fund Request Report' }}</h4>
            </div>
            <div class="col-lg-6 col-md-6">
                <!-- <div class="pull-right">
                    <button onclick="add_record()" id="demo-add-row" class="btn btn-success"><i
                                class="fa fa-plus m-r-5"></i>Add Record
                    </button>
                </div> -->
            </div>
        </div>
    </div><br>

<div class="row">
        <div class="col-sm-12">
            <div class="card-box">
               
                
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
                <tr class ="tr-text-fromat">
				 <td>Date </td>
				 <td>Time </td>
                    <td>ID</td>
                   
                    <td>Payment Method</td>
                    <td>Bank Name</td>
                    <td>Reference ID</td>
                    <td>Amount</td>
                    <td>Remark</td>
                    <td>Status</td>
                </tr>
                <tbody>
                @foreach($loadcashes as $loadcash)
                    <tr>
					<?php $s = $loadcash->created_at;
						$dt = new DateTime($s);
						?>
						<td>{{ $dt->format('d-m-Y') }}</td>
                            <td>{{ $dt->format('H:i:s') }}</td>
                        <td>{{ $loadcash->id }}</td>
                        
                        <td>{{ @$loadcash->pmethod->payment_type }}</td>
                        <td>{{ @$loadcash->netbank->bank_name }}</td>
                        <td>{{ $loadcash->bankref }}</td>
                        <td>{{ $loadcash->amount }}</td>
                        <td>{{ @$loadcash->remark->remark }}</td>
                        <td>{{ @$loadcash->status->status }}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>

                {!! $loadcashes->links() !!}

        </div>

    
    </div>

    <div class="row" style="display:none">
        <div class="col-md-12">
            <div class="card-box">

                <table class="table">

                    <tr>

                        <th> Account Name</th>
                        <th> Bank Name</th>
                        <th> Account Number</th>
                        <th> IFSC</th>
                        <th> Branch</th>

                    </tr>
                    @foreach($banks as $bank)
                        <tr>
                            <td> {{ $bank->bank_account_name }}</td>
                            <td> {{ $bank->bank_name }}</td>
                            <td> {{ $bank->bank_account_number }}</td>
                            <td> {{ $bank->bank_ifsc }}</td>
                            <td> {{ $bank->bank_address }}</td>

                        </tr>
                    @endforeach

                </table>

            </div>
        </div>

    </div>
@endsection