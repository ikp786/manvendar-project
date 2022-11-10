	<style type="text/css">
		.col-sm-2 {
		 width: 10%; 
	}
	</style>
@if(Auth::user()->role_id==5)
<div class="row">                      
    <div class="col-1 col-sm-2 col-md-2">
        <a href="{{route('summary_report')}}" data-toggle="tooltip" data-placement="top" title="Usage Report">
        <span class="info-box-text textheader {{Request::is('summary_report') ? 'active' : ''}}">Usage Report</span>
        </a>
    </div>   
    <div class="col-1 col-sm-2 col-md-2">
        <a href="{{url('recharge-nework')}}" data-toggle="tooltip" data-placement="top" title="Ledger Report">
        <span class="info-box-text textheader {{(Request::is('recharge-nework') && (app('request')->input('searchOf')!='4')) ? 'active' : ''}}">Ledger Report</span> 
        </a>
    </div>
	
    <div class="col-1 col-sm-2 col-md-2">
        <a href="{{route('businessview')}}" data-toggle="tooltip" data-placement="top" title="Business View">
        <span class="info-box-text textheader {{Request::is('businessview') ? 'active' : ''}}">Business View</span> 
        </a>
    </div>
   
    <div class="col-1 col-sm-2 col-md-2">
        <a href="{{url('recharge-nework').'?searchOf=4&product=&mode=&SEARCH=SEARCH'}}" data-toggle="tooltip" data-placement="top" title="Refunded">
        <span class="info-box-text textheader {{(app('request')->input('searchOf')=='4') ? 'active' : ''}}">Refunded</span> 
        </a>
    </div>

    <!-- <div class="col-3 col-sm-2 col-md-1">
        <a href="{{url('view-commission')}}">
        <span class="info-box-text {{Request::is('view-commission') ? 'active' : ''}}">View Commision</span> 
        </a>
    </div>  --> 
   <!--  <div class="col-3 col-sm-2 col-md-1">
        <a href="{{url('complain')}}">
        <span class="info-box-text {{Request::is('*complain') ? 'active' : ''}}">View Complain</span> 
        </a>
    </div>  --> 
        <!--<li class="{{Request::is('*money_transfer_report') ? 'active' : ''}}">
			   <a href="{{route('money_transfer_report')}}"><span>&nbsp; Sales Report</span></a>
			</li>-->
			<!--<li class="{{Request::is('*all-recharge-report') ? 'active' : ''}}">
				<a href="{{route('all-recharge-report')}}"><span>&nbsp; Recharge Report</span></a>
			</li>-->
			<!--<li class="{{Request::is('*summary_report') ? 'active' : ''}}">
				<a href="{{route('summary_report')}}"><span>&nbsp; Summary Report</span></a>
			</li>-->

			<!--<li class="{{Request::is('*load-cash') ? 'active' : ''}}">
				<a href="{{route('load-cash')}}"><span>&nbsp; Fund Request</span></a>
			</li>-->

			<!--<li class="{{Request::is('*business-report') ? 'active' : ''}}">
				<a href="{{url('business-report')}}"><span>&nbsp; Business View</span></a>
			</li>-->                        
</div>@endif
<br>