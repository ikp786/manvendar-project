<style type="text/css">
  .imageLogo{
    height:70%;
    width:80%;
	border-radius: 31%;
  }
</style>
  
  
  <div class="row">
        <div class="col-3 col-sm-4 col-md-1 ">
           <a href="{{route('recharge')}} ">
                <span class="info-box-text textheader {{(Request::is('recharge') ? 'active' : '')}}">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Prepaid</span>
               </a>
          </div>
        
          <div class="col-12 col-sm-6 col-md-1">
            <a href="{{route('dth-recharge')}}" >
              <span class="info-box-text textheader {{(Request::is('dth-recharge') ? 'active' : '')}}">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;DTH</span>
            </a>  
          </div>
          <div class="col-12 col-sm-6 col-md-1 ">
            <a href="{{route('datacard-recharge')}}">
                <span class="info-box-text textheader {{(Request::is('datacard-recharge') ? 'active' : '')}}">&nbsp;&nbsp;Data Card</span>
            </a>    
          </div>
  </div><br>