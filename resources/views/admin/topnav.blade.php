<script type="text/javascript">

  function comp_master()
  {
    $('#Company_master').toggle();
     // $('#member').hide();
     //  $('#report').hide();
     //   $('#analytic').hide();
  }
function member()
  {
    // $('#Company_master').hide();
     $('#member').toggle();
    //   $('#report').hide();
    //    $('#analytic').hide();
  }
  function report()
  {
   // $('#Company_master').hide();
   //   $('#member').hide();
      $('#report').toggle();
   //     $('#analytic').hide();
  }
  function analytic()
  {
  //   $('#Company_master').hide();
  //    $('#member').hide();
  //     $('#report').hide();
       $('#analytic').toggle();
   }
</script>

<div class="container">
        <div class="row">
          <div class="col-md-12 col-sm-12">

            <!-- Navigation -->
            <nav class="navbar navbar-default" style="background: #337ab7;">
              <!-- Brand and toggle get grouped for better mobile display -->
              <div class="navbar-header">
                <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target=".bs-example-navbar-collapse-1" aria-expanded="false">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                  </button>
                <!-- Brand -->
                <a class="navbar-brand page-scroll sticky-logo" href="index.html">
                  <h1 style="color: white;">Dashboard</h1>
                  <!-- Uncomment below if you prefer to use an image logo -->
                  <!-- <img src="img/logo.png" alt="" title=""> -->
                </a>
              </div>
              <!-- Collect the nav links, forms, and other content for toggling -->
              <div class="collapse navbar-collapse main-menu bs-example-navbar-collapse-1" id="navbar-example">
                <ul class="nav navbar-nav navbar-right">
                  
                  <li>
                    <a class="page-scroll" href="#" onclick="comp_master()">Company Master</a>
                  </li>
                  <li>
                    <a class="page-scroll" href="#" onclick="member()">Member</a>
                  </li>
                  <li>
                    <a class="page-scroll" href="#" onclick="report()">Report</a>
                  </li>
                  <li>
                    <a class="page-scroll" href="#" onclick="analytic()">Analytic</a>
                  </li>
                  
                </ul>
              </div>
              <!-- navbar-collapse -->
            </nav>
            <!-- END: Navigation -->
          </div>
        </div>
      </div>
      <div class="container" id="Company_master" style="display: none;"><div class="tabs"><ul><li><a href="#"><span>My Account</span></a></li> <li><a href="#"><span>Buy Credit</span></a></li> <li><a href="#"><span>mPos/MicroATM</span></a></li> <li><a href="#"><span>PAN CARD</span></a></li> <li><a href="#"><span>Manage Store</span></a></li> <li><a href="#"><span>Complain</span></a></li></ul></div></div>

       <div class="container" id="member" style="display: none;"><div class="tabs"><ul><li><a href="#"><span>My Account</span></a></li> <li><a href="#"><span>Buy Credit</span></a></li> <li><a href="#"><span>mPos/MicroATM</span></a></li> <li><a href="#"><span>PAN CARD</span></a></li> <li><a href="#"><span>Manage Store</span></a></li> <li><a href="#"><span>Complain</span></a></li></ul></div></div>

        <div class="container" id="report" style="display: none;"><div class="tabs"><ul><li><a href="#"><span>My Account</span></a></li> <li><a href="#"><span>Buy Credit</span></a></li> <li><a href="#"><span>mPos/MicroATM</span></a></li> <li><a href="#"><span>PAN CARD</span></a></li> <li><a href="#"><span>Manage Store</span></a></li> <li><a href="#"><span>Complain</span></a></li></ul></div></div>

         <div class="container" id="analytic" style="display: none;"><div class="tabs"><ul><li><a href="#"><span>My Account</span></a></li> <li><a href="#"><span>Buy Credit</span></a></li> <li><a href="#"><span>mPos/MicroATM</span></a></li> <li><a href="#"><span>PAN CARD</span></a></li> <li><a href="#"><span>Manage Store</span></a></li> <li><a href="#"><span>Complain</span></a></li></ul></div></div>