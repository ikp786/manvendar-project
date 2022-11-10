@extends('layouts.template')
@section('content')

    <script xmlns="http://www.w3.org/1999/html">
        function sendRecharge() {
            $("#trbutto").prop("disabled", true);
            var token = $("input[name=_token]").val();
            $("#mobilbtn").text("Processing...");
            var mobile_number = $("#mobile_number").val();
            var mobile_provider = $("#mobile_provider").val();
            var mobile_amount = $("#mobile_amount").val();
            var dataString = 'number=' + mobile_number + '&provider=' + mobile_provider + '&amount=' + mobile_amount + '&_token=' + token;
            $.ajax({
                type: "POST",
                url: "{{url('recharge')}}",
                data: dataString,
                success: function (msg) {
                    $("#trbutto").prop("disabled", false);
                    $("#mobile_amount").val();
                    $("#mobilbtn").text("Pay Now");
                    if (msg.success == 'success') {
                        swal("Success", msg.message, "success");
                    } else {
                        swal("Faild", msg.message, "error");
                    }
                }
            });
        }
    </script>

    <section class="slice light-gray bb">
        <div class="wp-section">
            <div class="container">
                @include('layouts.nav')
                <div class="row">
                    <div class="col-md-8">
                        <div class="wp-block default user-form">
                            <div class="form-header">
                                <div class="nav">
                                    <div class="btn-group pull-left">
                                        <h2>Search Flight</h2>
                                    </div>

                                    <div class="btn-group pull-right">
                                        <button type="button" class="btn btn-info">Domestic</button>
                                        <button type="button" class="btn btn-primary">International</button>
                                    </div>
                                </div>

                            </div>
                            <div class="form-body">
                                <form action="" id="frmRegister" class="sky-form">
                                    <fieldset class="no-padding">

                                        <label class="radio-inline"><input type="radio" checked name="mode">One
                                            Way</label>
                                        <label class="radio-inline"><input type="radio" name="mode">Round
                                            Trip</label>
                                        <section>
                                            <hr>
                                            <section>
                                                <div class="row">
                                                    <div class="col-xs-6">
                                                        <div class="form-group">
                                                            <label class="label">FROM</label>
                                                            <label class="input">
                                                                <i class="icon-append fa fa-plane"></i>
                                                                <input value="" type="text" name="from"
                                                                       placeholder="">
                                                                <b class="tooltip tooltip-bottom-right">Needed
                                                                    to enter the From Airport</b>
                                                            </label>
                                                        </div>
                                                    </div>
                                                    <div class="col-xs-6">
                                                        <div class="form-group">
                                                            <div class="form-group">
                                                                <label class="label">To</label>
                                                                <label class="input">
                                                                    <i class="icon-append fa fa-plane"></i>
                                                                    <input value="" type="email" name="to"
                                                                           placeholder="">
                                                                    <b class="tooltip tooltip-bottom-right">
                                                                        to enter the From Airport</b>
                                                                </label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </section>
                                        </section>

                                    </fieldset>
                                    <fieldset>
                                        <div class="row">
                                            <section class="col-xs-3">
                                                <label class="label">Departure Date</label>
                                                <label class="input">
                                                    <i class="icon-append fa fa-calendar"></i>
                                                    <input type="text" id="from" name="departure"
                                                           placeholder="Departure Date">
                                                </label>
                                            </section>
                                            <section class="col-xs-3">
                                                <label class="label">Return Date</label>
                                                <label class="input">
                                                    <i class="icon-append fa fa-calendar"></i>
                                                    <input type="text" id="to" name="return" placeholder="Return Date">
                                                </label>
                                            </section>

                                            <section class="col-xs-2">
                                                <label class="label">Adult:(12+y)</label>
                                                <label class="input">
                                                    <input type="number" name="adult" placeholder="Adult">
                                                </label>
                                            </section>

                                            <section class="col-xs-2">
                                                <label class="label">Child:(2-11y)</label>
                                                <label class="input">
                                                    <input type="number" name="child" placeholder="Child">
                                                </label>
                                            </section>
                                            <section class="col-xs-2">
                                                <label class="label">Infant:(0-2y)</label>
                                                <label class="input">
                                                    <input type="number" name="infant" placeholder="Infant">
                                                </label>
                                            </section>
                                        </div>
                                        <div class="row">
                                            <section class="col-xs-3">
                                                <label class="label">CLASS:</label>
                                                <label class="input">
                                                    <input type="number" name="class" placeholder="Class">
                                                </label>
                                            </section>

                                            <section class="col-xs-4">
                                                <label class="label">AIRLINE PREFRENCE (OPTIONAL):</label>
                                                <label class="input">
                                                    <input type="number" name="airline" placeholder="Airline Prefrence">
                                                </label>
                                            </section>
                                            <section class="col-xs-3">
                                                <label class="label">Search Now</label>
                                                <label class="input">
                                                    <a class="btn btn-base btn-icon fa-search"><span>Search Flight</span></a>
                                                </label>

                                            </section>
                                        </div>

                                    </fieldset>

                                </form>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="panel panel-default panel-sidebar-1">
                            <div class="panel-heading">
                                <h2>Offer</h2>
                            </div>
                            <div class="panel-body bb">


                            </div>

                        </div>
                        <div class="row">

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <script>
        $(function () {
            $("#from").datepicker({
                changeMonth: true,
                numberOfMonths: 1,
                onClose: function (selectedDate) {
                    $("#to").datepicker("option", "minDate", selectedDate);
                }
            });
            $("#to").datepicker({
                changeMonth: true,
                numberOfMonths: 1,
                onClose: function (selectedDate) {
                    $("#from").datepicker("option", "maxDate", selectedDate);
                }
            });
        });
    </script>
@endsection
