@extends('layouts.template')

@section('content')
    <div class="pg-opt">
        <div class="container">
            <div class="row">
                <div class="col-md-6">
                    <h1>Billing information</h1>
                </div>
                <div class="col-md-6">
                    <ol class="breadcrumb">
                        <li><a href="{{ url('home') }}">Home</a></li>
                        <li class="active">Billing information</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <section class="slice bg-white">
        <div class="wp-section shop">
            <div class="container">
                <div class="row">
                    <div class="col-md-12">
                        <!-- Checkout process - Progress bar -->
                        <ol class="progtrckr hidden-xs" data-progtrckr-steps="6">
                            <li class="progtrckr-done">Checkout method</li>
                            <li class="progtrckr-done">Shipping</li>
                            <li class="progtrckr-done">Payment method</li>
                            <li class="progtrckr-todo">Order review</li>
                            <li class="progtrckr-todo">Delivered</li>
                        </ol>

                        <div class="row">
                            <div class="col-md-8">


                                <div class="tabs-framed">
                                    <ul class="tabs clearfix">
                                        <li class="active"><a href="#tab-1" data-toggle="tab">Net Banking</a></li>
                                        <li class=""><a href="#tab-2" data-toggle="tab">Credit Card</a></li>
                                        <li class=""><a href="#tab-3" data-toggle="tab">Debit Card</a></li>
                                        <li class=""><a href="#tab-4" data-toggle="tab">Others</a></li>
                                    </ul>

                                    <div class="tab-content">
                                        <!-- Tab 1 -->
                                        <div class="tab-pane fade active in" id="tab-1">
                                            <div class="tab-body">
                                                <form action="{{ url('payment-pay') }}" method="post" class="sky-form" novalidate="novalidate">
                                                    {!! csrf_field() !!}
                                                    <input type="hidden" name="pg" value="NB">
                                                    <input type="hidden" name="amount" value="{{ $amount }}">
                                                    <input type="hidden" name="mobile" value="{{ $mobile }}">
                                                    <input type="hidden" name="product_id" value="{{ $product_id }}">
                                                    <fieldset>
                                                        <section>
                                                            <div class="inline-group">

                                                            </div>
                                                        </section>
                                                        <section>
                                                            <div class="row">
                                                                <div class="form-group col-sm-8">

                                                                    <label for="">Choose Net Banking Bank</label>
                                                                    {{ Form::select('bankcode', $netbankings, '',array('class' => 'form-control input-lg', 'id' => 'netbankking')) }}

                                                                </div>
                                                            </div>
                                                            <div class="row">
                                                                <div class="col-md-8">
                                                                    <button id="btn" onclick="sendDetail()"
                                                                            class="btn btn-lg btn-block btn-alt btn-icon btn-icon-right btn-icon-go pull-right">
                                                                        <span>Proceed to Pay</span>
                                                                    </button>
                                                                    <button style="display:none" id="btnn"
                                                                            onclick="sendDetailnet()"
                                                                            class="btn btn-lg btn-block btn-alt btn-icon btn-icon-right btn-icon-go pull-right">
                                                                        <span>Continue</span>
                                                                    </button>
                                                                </div>
                                                            </div>
                                                        </section>
                                                    </fieldset>
                                                </form>

                                            </div>
                                        </div>

                                        <div class="tab-pane fade" id="tab-2">
                                            <div class="tab-body">
                                                <div class="form-body">
                                                    <form action="{{ url('payment-pay') }}" method="post" class="sky-form" novalidate="novalidate">
                                                        {!! csrf_field() !!}
                                                        <input type="hidden" name="amount" value="{{ $amount }}">
                                                        <input type="hidden" name="mobile" value="{{ $mobile }}">
                                                        <input type="hidden" name="product_id" value="{{ $product_id }}">
                                                        <input type="hidden" name="pg" value="CC">
                                                        <fieldset>
                                                            <section>
                                                                <div class="inline-group">
                                                                    <label class="radio"><input type="radio"
                                                                                                name="bankcode"
                                                                                                checked="" value="CC"><i></i>Visa/Master
                                                                        Card</label>
                                                                    <label class="radio"><input type="radio" name="bankcode" value="AMEX"><i></i>Omex</label>
                                                                </div>
                                                            </section>
                                                            <div class="form-group row">
                                                                <section class="col-md-8">

                                                                    <input name="ccnum" type="text" name="ccnum" id="card"
                                                                           placeholder="Card number"
                                                                           class="form-control input-lg">
                                                                </section>
                                                            </div>

                                                            <div class="row">
                                                                <section class="col-md-3">
                                                                    <label class="select">
                                                                        <select name="ccexpmon">
                                                                            <option value="0" selected="" disabled="">
                                                                                Month
                                                                            </option>
                                                                            <option value="1">January</option>
                                                                            <option value="1">February</option>
                                                                            <option value="3">March</option>
                                                                            <option value="4">April</option>
                                                                            <option value="5">May</option>
                                                                            <option value="6">June</option>
                                                                            <option value="7">July</option>
                                                                            <option value="8">August</option>
                                                                            <option value="9">September</option>
                                                                            <option value="10">October</option>
                                                                            <option value="11">November</option>
                                                                            <option value="12">December</option>
                                                                        </select>
                                                                        <i></i>
                                                                    </label>
                                                                </section>
                                                                <section class="col-md-3">
                                                                    <label class="input">
                                                                        <input type="text" name="ccexpyr" id="year"
                                                                               placeholder="Year"
                                                                               class="valid">
                                                                    </label>
                                                                </section>
                                                                <section class="col-md-2">
                                                                    <label class="input">
                                                                        <input type="text" name="ccvv" id="cvv"
                                                                               placeholder="CVV2">
                                                                    </label>
                                                                </section>
                                                            </div>
                                                            <div class="row">
                                                                <section class="col-md-8">
                                                                    <label class="input">
                                                                        <input type="text" name="ccname" id="card"
                                                                               placeholder="Card Holder Name"
                                                                               class="form-control input-lg valid">
                                                                    </label>
                                                                </section>
                                                            </div>
                                                            <div class="row">
                                                                <div class="col-md-8">
                                                                    <button id="btn" onclick="sendDetail()"
                                                                            class="btn btn-lg btn-block btn-alt btn-icon btn-icon-right btn-icon-go pull-right">
                                                                        <span>Proceed to Pay</span>
                                                                    </button>
                                                                    <button style="display:none" id="btnn"
                                                                            onclick="sendDetailnet()"
                                                                            class="btn btn-lg btn-block btn-alt btn-icon btn-icon-right btn-icon-go pull-right">
                                                                        <span>Continue</span>
                                                                    </button>
                                                                </div>
                                                            </div>
                                                        </fieldset>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="tab-pane fade" id="tab-3">
                                            <div class="tab-body">
                                                <div class="form-body">
                                                    <form action="{{ url('payment-pay') }}" method="post" class="sky-form" novalidate="novalidate">
                                                        {!! csrf_field() !!}
                                                        <input type="hidden" name="amount" value="{{ $amount }}">
                                                        <input type="hidden" name="mobile" value="{{ $mobile }}">
                                                        <input type="hidden" name="product_id" value="{{ $product_id }}">
                                                        <input type="hidden" name="pg" value="DC">
                                                        <fieldset>
                                                            <section>
                                                                <div class="inline-group">
                                                                    <label class="radio"><input type="radio"
                                                                                                name="bankcode"
                                                                                                checked="" value="VISA"><i></i>Visa
                                                                        </label>
                                                                    <label class="radio"><input type="radio"
                                                                                                name="bankcode" value="MAST"><i></i>Master</label>
                                                                    <label class="radio"><input type="radio"
                                                                                                name="bankcode" value="SMAE"><i></i>SBI Maestro</label>
                                                                    <label class="radio"><input type="radio"
                                                                                                name="bankcode" value="CITD"><i></i>CITI Debit Cards</label>
                                                                </div>
                                                            </section>
                                                            <div class="form-group row">
                                                                <section class="col-md-8">

                                                                    <input required name="ccnum" type="text" name="ccnum" id="card"
                                                                           placeholder="Card number"
                                                                           class="form-control input-lg">
                                                                </section>
                                                            </div>

                                                            <div class="row">
                                                                <section class="col-md-3">
                                                                    <label class="select">
                                                                        <select name="ccexpmon">
                                                                            <option value="0" selected="" disabled="">
                                                                                Month
                                                                            </option>
                                                                            <option value="1">January</option>
                                                                            <option value="1">February</option>
                                                                            <option value="3">March</option>
                                                                            <option value="4">April</option>
                                                                            <option value="5">May</option>
                                                                            <option value="6">June</option>
                                                                            <option value="7">July</option>
                                                                            <option value="8">August</option>
                                                                            <option value="9">September</option>
                                                                            <option value="10">October</option>
                                                                            <option value="11">November</option>
                                                                            <option value="12">December</option>
                                                                        </select>
                                                                        <i></i>
                                                                    </label>
                                                                </section>
                                                                <section class="col-md-3">
                                                                    <label class="input">
                                                                        <input type="text" name="ccexpyr" id="year"
                                                                               placeholder="Year"
                                                                               class="valid">
                                                                    </label>
                                                                </section>
                                                                <section class="col-md-2">
                                                                    <label class="input">
                                                                        <input type="text" name="ccvv" id="cvv"
                                                                               placeholder="CVV2">
                                                                    </label>
                                                                </section>
                                                            </div>
                                                            <div class="row">
                                                                <section class="col-md-8">
                                                                    <label class="input">
                                                                        <input type="text" name="ccname" id="card"
                                                                               placeholder="Card Holder Name"
                                                                               class="form-control input-lg valid">
                                                                    </label>
                                                                </section>
                                                            </div>
                                                            <div class="row">
                                                                <div class="col-md-8">
                                                                    <button id="btn" onclick="sendDetail()"
                                                                            class="btn btn-lg btn-block btn-alt btn-icon btn-icon-right btn-icon-go pull-right">
                                                                        <span>Proceed to Pay</span>
                                                                    </button>
                                                                    <button style="display:none" id="btnn"
                                                                            onclick="sendDetailnet()"
                                                                            class="btn btn-lg btn-block btn-alt btn-icon btn-icon-right btn-icon-go pull-right">
                                                                        <span>Continue</span>
                                                                    </button>
                                                                </div>
                                                            </div>
                                                        </fieldset>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="tab-pane fade" id="tab-4">
                                            <div class="tab-body">
                                                <div class="form-body">
                                                    <form action="" class="sky-form" novalidate="novalidate">
                                                        {!! csrf_field() !!}
                                                        <input type="hidden" name="pg" value="manual">
                                                        <fieldset>
                                                            <section>
                                                                <div class="inline-group">
                                                                    <label class="radio"><input type="radio"
                                                                                                name="radio-inline"
                                                                                                checked=""><i></i>NEFT
                                                                    </label>
                                                                    <label class="radio"><input type="radio"
                                                                                                name="radio-inline"><i></i>FUND TRANSFER</label>
                                                                    <label class="radio"><input type="radio"
                                                                                                name="radio-inline"><i></i>IMPS</label>
                                                                    <label class="radio"><input type="radio"
                                                                                                name="radio-inline"><i></i>CASH</label>
                                                                </div>
                                                            </section>
                                                            <div class="form-group row">
                                                                <section class="col-md-8">

                                                                    <input type="text" name="card" id="ref_id"
                                                                           placeholder="Bank Reference Number"
                                                                           class="form-control input-lg">
                                                                </section>
                                                            </div>


                                                            <div class="row">
                                                                <div class="col-md-8">
                                                                    <button id="btn" onclick="sendDetail()"
                                                                            class="btn btn-lg btn-block btn-alt btn-icon btn-icon-right btn-icon-go pull-right">
                                                                        <span>Proceed to Pay</span>
                                                                    </button>
                                                                    <button style="display:none" id="btnn"
                                                                            onclick="sendDetailnet()"
                                                                            class="btn btn-lg btn-block btn-alt btn-icon btn-icon-right btn-icon-go pull-right">
                                                                        <span>Continue</span>
                                                                    </button>
                                                                </div>
                                                            </div>
                                                        </fieldset>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>


                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="panel panel-default panel-sidebar-1">
                                    <div class="panel-heading">
                                        <h2>Cart summary</h2>
                                    </div>
                                    <div class="panel-body bb">
                                        <form role="form" class="form-light">
                                            <label for="">Do you have a promo code?</label>
                                            <div class="input-group">
                                                <input type="text" class="form-control left">
                                                <span class="input-group-btn">
                                                    <button class="btn btn-base" type="button">Apply code</button>
                                                </span>
                                            </div>
                                        </form>
                                    </div>
                                    <div class="panel-body bb">
                                        <table class="table table-cart-subtotal">
                                            <tbody>
                                            <tr>
                                                <th>Cart Subtotal</th>
                                                <td class="text-right"><span class="amount">{{ number_format($products->price,2) }}</span></td>
                                            </tr>
                                            <tr>
                                                <th>Service taxes</th>
                                                <td class="text-right">{{ number_format($products->price * 14.5 / 100) }}</td>
                                            </tr>
                                            <tr>
                                                <th>Total</th>
                                                <td class="text-right">{{ number_format($products->price + $products->price * 14.5 / 100) }}</td>
                                            </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                    <div class="panel-body">
                                        <h5></h5>
                                        <p>
                                            Invoice and detail of product will send to Email id, Please check after payment
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection