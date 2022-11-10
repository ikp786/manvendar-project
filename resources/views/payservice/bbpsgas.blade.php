@extends('layouts.template')
@section('content')

    <script>
        function sendRecharge() {
            $("#trbutto").attr("disabled", true);
            var token = $("input[name=_token]").val();
            $("#mobilbtn").text("Processing...");
            var mobile_number = $("#mobile_number").val();
            var mobile_provider = $("#mobile_provider").val();
            var mobile_amount = $("#mobile_amount").val();
             if (confirm('Are you sure you want to Recharge Amount '+ mobile_amount +'?')) {
            var dataString = 'number=' + mobile_number + '&provider=' + mobile_provider + '&amount=' + mobile_amount + '&_token=' + token;
            $.ajax({
                type: "POST",
                url: "{{url('recharge')}}",
                data: dataString,
                success: function (msg) {
                    $("#trbutto").prop("disabled", false);
                    $("#mobile_amount").val();
                    $("#mobilbtn").text("Pay Now");
                    if (msg.status == 'success') {
                        swal("Success", msg.message, "success");
                        window.location.reload();
                    } else {
                        swal("Faild", msg.message, "error");
                        window.location.reload();
                    }
                }
            });
        }
        else
        {
                $("#trbutto").attr("disabled", false);
                $("#trbutto").text("Pay Now");
        }
    }

    function getproviderinfo()
    {
         var token = $("input[name=_token]").val();
         var mobile_provider = $("#mobile_provider").val();
            var dataString = 'provider_id=' + mobile_provider + '&_token=' + token;
            $.ajax({
                type: "GET",
                url: "{{url('/provider_info')}}",
                data: dataString,
                dataType: "json",
                success: function (msg) {
                    $('#pro_info').show();
                    if(msg.result.duedate=='N')
                    {
                        $('#due_date').html('NO').css('color','red');
                    }
                    else
                    {
                        $('#due_date').html('YES').css('color','green');
                    }
                    if(msg.result.partial_pay=='N')
                    {
                        $('#partial_pay').html('NO').css('color','red');
                    }
                    else
                    {
                        $('#partial_pay').html('YES').css('color','green');
                    }
                    $('#pro_location').html(msg.result.location);
                
                    $('#max_amount').html(msg.result.max_amnt);
                }
            });
    }
    </script>
    <section class="slice light-gray bb">
        <div class="wp-section">
            <div class="container">
                 @foreach($news as $recharge_news)
                <marquee><p style="color:red"><b
                                                    style="font-size:20px">{{$recharge_news->recharge_news}}</b>
                                        </p></marquee>
                @endforeach
                @include('layouts.re_nav')
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
                <div class="row">
                @if(Auth::user()->company_id==13)
                <div class="col-md-4 my-block" style="background-image: url('images/egepaycolourUntitled.png');">
                    
                    @else
                    <div class="col-md-4 my-block" >
                    @endif
                        <div class="wp-block default user-form">
                            <div class="form-header">
                                <h2 style="color:white;"> {{ strtoupper(Request::segment(1)) }}</h2>
                            </div>
                            <div class="form-body">
                                <form method="POST" action="{{ url('/recharge') }}">
                                    {!! csrf_field() !!}
                                </form>
                               
                                    <section>
                                        <div class="form-group">
                                            <label class="label">{{ strtoupper(Request::segment(1)) }} Number</label>
                                           
                                                <i class="icon-append fa fa-mobile"></i>
                                                <input onkeyup="search()" type="number" class="form-control" value="{{ old('number') }}"
                                                       name="number"
                                                       id="mobile_number" placeholder="Enter Valid Number">
                                          
                                            @if ($errors->has('number'))
                                                <span class="help-block">
                                                    <strong>{{ $errors->first('number') }}</strong>
                                                </span>
                                            @endif
                                        </div>
                                    </section>
                                    <section>
                                        <div class="form-group">
                                            <label class="label">Operator</label>
                                            {{ Form::select('provider', $provider, old('provider'), array('class' => 'form-control','id' => 'mobile_provider','onchange'=>'getproviderinfo()')) }}
                                            @if ($errors->has('password'))
                                                <span class="help-block">
                                                        <strong>{{ $errors->first('password') }}</strong>
                                                    </span>
                                            @endif
                                        </div>
                                        @if(Request::segment(1) == 'home' || Request::segment(1) == 'mobile')
                                            <div style="display: none;" class="form-group">
                                                <label class="label">Circle</label>
                                                {{ Form::select('circle', $circle, old('provider'), array('class' => 'form-control','id' => 'mobile_circle')) }}
                                                @if ($errors->has('circle'))
                                                    <span class="help-block">
                                                        <strong>{{ $errors->first('circle') }}</strong>
                                                    </span>
                                                @endif
                                            </div>
                                        @endif
                                    </section>
                                    <section>
                                        <div class="form-group">
                                            <label class="label">Amount</label>
                                            
                                                <i class="icon-append fa fa-user"></i>
                                                <input type="number" value="{{ old('amount') }}" class="form-control" name="amount"
                                                       id="mobile_amount" placeholder="Enter valid Amount">
                                           
                                            @if ($errors->has('amount'))
                                                <span class="help-block">
                                                            <strong>{{ $errors->first('amount') }}</strong>
                                                    </span>
                                            @endif
                                        </div>
                                    </section>

<!--                                    <section>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <label class="checkbox"><input type="checkbox" name="remember"
                                                                               checked><i></i>Have Promo Code
                                                </label>
                                            </div>
                                        </div>
                                    </section>-->

                                    <section>
                                        <button id="trbutto" onclick="this.disabled=true;sendRecharge()"
                                                class="btn btn-base btn-icon my-bt btn-icon-right btn-sign-in pull-right">
                                            <span id="mobilbtn">Pay Now</span>
                                        </button>
                                    </section>
                               
                            </div>
                            <div class="form-footer">

                            </div>
                        </div>
                    </div>
                   
                    <div class="col-md-8" id="pro_info" style="margin-top: 75px; display: none;">
                        <p><h4>Due Date recharge. <span id="due_date"></span></h4></p><br>
                        <p><h4>Provider Location. <span id="pro_location"></span></h4></p><br>
                        <p><h4>Partial Recharge Pay. <span id="partial_pay"></span></h4></p><br>
                        <p><h4>Maximum Amount. <span id="max_amount"></span></h4></p>

                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
