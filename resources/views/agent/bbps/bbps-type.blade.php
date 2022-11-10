<style type="text/css">
  .imageLogo{
    height:70%;
    width:80%;
	 border-radius: 31%;
  }
  </style>
<div class="row">
 <div class="col-1 col-sm-2 col-md-1">
              <a href="{{route('bbps')}}">
                <span class="info-box-text textheader {{(Request::is('bbps') ? 'active' : '')}}">Electricity</span>
              </a>   
          </div>
          <div class="col-1 col-sm-2 col-md-1">
            <a href="{{route('bbps-postpaid')}}">
              <span class="info-box-text textheader {{(Request::is('bbps-postpaid') ? 'active' : '')}}">PostPaid</span>
            </a>   
          </div> 
           <div class="col-1 col-sm-2 col-md-1">
            <a href="{{route('bbps-broadband')}}">
              <span class="info-box-text textheader {{(Request::is('bbps-broadband') ? 'active' : '')}}">BroadBand</span>
            </a>   
          </div> 
           <div class="col-1 col-sm-2 col-md-1">
            <a href="{{route('bbps-landline')}}">
              <span class="info-box-text textheader {{(Request::is('bbps-landline') ? 'active' : '')}}">LandLine</span> 
            </a>  
          </div> 
          <div class="col-1 col-sm-2 col-md-1">
            <a href="{{route('bbps-water')}}">
              <span class="info-box-text textheader {{(Request::is('bbps-water') ? 'active' : '')}}">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Water</span> 
            </a>  
          </div> 
          <div class="col-1 col-sm-2 col-md-1">
            <a href="{{route('bbps-gas')}} ">
              <span class="info-box-text textheader {{(Request::is('bbps-gas') ? 'active' : '')}}">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Gas</span>  
            </a> 
          </div> 
          <div class="col-1 col-sm-2 col-md-1">
            <a href="{{route('bbps-insurance')}}">
              <span class="info-box-text textheader {{(Request::is('bbps-insurance') ? 'active' : '')}}">Insurance</span>  
            </a> 
          </div> 
      </div><br><br>