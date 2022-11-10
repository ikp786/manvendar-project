<style type="text/css">
    .col-sm-2 {
     width: 10%; 
}
</style>
 @if(Auth::user()->role_id==5) 
<div class="row">
	<div class="col-1 col-sm-2 col-md-2">
        <a href="{{route('bank-fund')}}" data-toggle="tooltip" data-placement="top" title="Fund Request">
        <span class="info-box-text textheader {{Request::is('bank-fund') ? 'active' : ''}}">Fund Request</span> 
        </a>
    </div>
    <div class="col-1 col-sm-2 col-md-2">
        <a href="{{route('fund-request-summary')}}" data-toggle="tooltip" data-placement="top" title="Fund Report">
        <span class="info-box-text textheader {{Request::is('fund-request-summary') ? 'active' : ''}}">Fund Report</span> 
        </a>
    </div>
	<div class="col-1 col-sm-2 col-md-2">
        <a href="{{url('payment-request-report')}}" data-toggle="tooltip" data-placement="top" title="Payment Request Report">
        <span class="info-box-text textheader {{Request::is('payment-request-report') ? 'active' : ''}}">Payment Request Report</span> 
        </a>
    </div>
	<div class="col-1 col-sm-2 col-md-2">
        <a href="{{route('dt-report')}}" data-toggle="tooltip" data-placement="top" title="Business View">
        <span class="info-box-text textheader {{Request::is('dt-report') ? 'active' : ''}}">&nbsp;&nbsp;DT Report</span> 
        </a>
    </div>
    <div class="col-1 col-sm-2 col-md-2">
        <a href="{{url('transfer/r-to-r')}}" data-toggle="tooltip" data-placement="top" title="R-to-R Transfer">
         <span class="info-box-text textheader {{Request::is('transfer/r-to-r') ? 'active' : ''}}">R-to-R Transfer</span> 
        </a>
    </div>
</div>
@endif     