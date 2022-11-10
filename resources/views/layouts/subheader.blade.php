<style type="text/css"> 
 .active{
  background-color: #0843e8;  
}
  #navbar-nav{
  background: #343a40;
	max-width: 63.1rem;
}
.imagelogoheader{
  height: 95%;
  width: 80%;
	border-radius: 30%;
}
.imagelogoheader:hover {
  background-color: red;
  color: black;
}
</style>
<div class="row " id="navbar-nav">
		 <div class="col-1 col-sm-1 col-md-1">
            <a href="{{route('premium-wallet')}}">
            <img src="logo/wallet.png" class="imagelogoheader  {{(Request::is('*wallet') ? 'active' : '')}}">  
            </a>    
          </div>
		   <div class="col-1 col-sm-1 col-md-1 ">
            <a href="{{route('money')}}">
            <img src="logo/money-transfer.png" class="imagelogoheader {{(Request::is('money*') ? 'active' : '')}}"> 
            </a>    
          </div>
        <div class="col-1 col-sm-1 col-md-1 ">
           <a href="{{route('recharge')}} ">
             <img src="logo/recharges.png" class="imagelogoheader {{(Request::is('*recharge') ? 'active' : '')}}" >
               </a>
          </div>
          <div class="col-1 col-sm-1 col-md-1 ">
            <a href="{{route('bbps')}}">
             <img src="logo/BILL-PAYMENTS.png" class="imagelogoheader {{(Request::is('bbps*') ? 'active' : '')}}"> 
            </a>  
          </div>
          <div class="col-1 col-sm-1 col-md-1">
            <a href="#">
            <img src="logo/AEPS.png" class="imagelogoheader {{(Request::is('aeps*') ? 'active' : '')}}">   
            </a>    
          </div>
          <div class="col-1 col-sm-1 col-md-1 ">
            <a href="#">
            <img src="logo/gift-card.png" class="imagelogoheader {{(Request::is('gift*') ? 'active' : '')}}"> 
            </a>    
          </div>
          
           <div class="col-1 col-sm-1 col-md-1">
            <a href="#">
            <img src="logo/hotel.png" class="imagelogoheader  {{(Request::is('gift*') ? 'active' : '')}}">  
            </a>    
          </div>
          <div class="col-1 col-sm-1 col-md-1">
            <a href="#">
              <img src="logo/AIR.png" class="imagelogoheader  {{(Request::is('gift*') ? 'active' : '')}}"> 
            </a>    
          </div>
           <div class="col-1 col-sm-1 col-md-1">
            <a href="#">
              <img src="logo/bus.png" class="imagelogoheader  {{(Request::is('gift*') ? 'active' : '')}}">
            </a>    
          </div>
           <div class="col-1 col-sm-1 col-md-1">
            <a href="#">
              <img src="logo/E-COMMERCE.png" class="imagelogoheader  {{(Request::is('gift*') ? 'active' : '')}}">
            </a>    
          </div>
           <div class="col-1 col-sm-1 col-md-1">
            <a href="#">
              <img src="logo/INSURANCE.png" class="imagelogoheader  {{(Request::is('gift*') ? 'active' : '')}}">
            </a>    
          </div>
           <div class="col-1 col-sm-1 col-md-1">
            <a href="#">
              <img src="logo/umbrella.png" class="imagelogoheader  {{(Request::is('gift*') ? 'active' : '')}}">
            </a>    
          </div>
           <div class="col-1 col-sm-1 col-md-1" style="background:#343a40;">
            <a href="#">
              <img src="logo/umbrella.png" class="imagelogoheader  {{(Request::is('gift*') ? 'active' : '')}}">
            </a>    
          </div>
  </div>


  