@extends('layouts.mobtemplate')
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

    </script>
    <section class="slice light-gray bb">
        <div class="wp-section">
            <div class="container">
                 @foreach($news as $recharge_news)
                <marquee><p class="marquee-color">{{$recharge_news->recharge_news}}</p></marquee>
                @endforeach
                @include('layouts.nav')
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
                <div class="col-md-12">
                @if(Auth::user()->company_id==13)
                    <div class="col-md-4 my-block" style="background-image: url('images/egepaycolourUntitled.png');">
                    @else
                    <div class="col-md-4 my-block">
                    @endif
                        <div class="wp-block default user-form">
                            <div class="form-header">
                                <h4 style="color:white;    padding-top: 3%;"> {{ strtoupper(Request::segment(1)) }}</h4>
                            </div>
                            <div class="form-body">
                                <form method="POST" action="{{ url('/recharge') }}">
                                    {!! csrf_field() !!}
                                </form>
                                
                                    <section>
                                        <div class="form-group">
                                            <label class="label">{{ strtoupper(Request::segment(1)) }} Number</label>
                                            
                                                <i class="icon-append fa fa-mobile"></i>
                                                <input onkeyup="search()" class="form-control" type="number" value="{{ old('number') }}"
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
                                            {{ Form::select('provider', $provider, old('provider'), array('class' => 'form-control','id' => 'mobile_provider')) }}
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
                                                <input type="number" value="{{ old('amount') }}" name="amount"
                                                       id="mobile_amount" class="form-control" placeholder="Enter valid Amount">
                                            </label
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
                                                class="btn btn-base my-bt btn-icon btn-icon-right btn-sign-in pull-right">
                                            <span id="mobilbtn">Pay Now</span>
                                        </button>
                                    </section>
                               
                            </div>
                            <div class="form-footer">

                            </div>
                        </div>
                    </div>
                    <div class="col-md-8">


                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
