@extends('layouts.app')

@section('content')
<style>
.switch {
  position: relative;
  display: inline-block;
  width: 60px;
  height: 34px;
}
.switch input { 
  opacity: 0;
  width: 0;
  height: 0;
}
.slider {
  position: absolute;
  cursor: pointer;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background-color: #ccc;
  -webkit-transition: .4s;
  transition: .4s;
}

.slider:before {
  position: absolute;
  content: "";
  height: 26px;
  width: 26px;
  left: 4px;
  bottom: 4px;
  background-color: white;
  -webkit-transition: .4s;
  transition: .4s;
}

input:checked + .slider {
  background-color: #2196F3;
}

input:focus + .slider {
  box-shadow: 0 0 1px #2196F3;
}

input:checked + .slider:before {
  -webkit-transform: translateX(26px);
  -ms-transform: translateX(26px);
  transform: translateX(26px);
}

/* Rounded sliders */
.slider.round {
  border-radius: 34px;
}

.slider.round:before {
  border-radius: 50%;
}
</style>

<script>

$(document).click(function(){
  $('.switch').click(function(){
    alert("Press a button OK!\nThe button you pressed will be displayed on the screen");
    alert("your request is successfully submitted");
  });
});
function activePancardService()
{
	if(confirm("Pan Card Service Activation charge is 100. \n Are you sure want to activate service "))
	{
				var url="{{route('pan-card-activation')}}";
                var dataString = 'TYPE=FOR_ACTIVARION' ;
                $.ajaxSetup({
					headers: {
					'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
					}
				})
			   $.ajax({

                        type: "post",
                        url: url,
                        data: dataString,
                        dataType:"json",
						beforeSend: function () {
							$("#pan_btn").prop('disabled', true);
                            $("#pan_btn").text('Processing...');
                        },
                        success: function (msg) {
							$("#pan_btn").prop('disabled', true);
                            $("#pan_btn").text('Pending');
							alert(msg)
                            
								
                          
                            //  else
                            // alert(msg.message)
                        }
                        
                    });
          
	}
}
</script>
<div class="super_container">

  
  
  <!-- Home -->

  <div class="home">
    
  
  </div>

  <!-- Search -->

  <div class="search">
    

    <!-- Search Contents -->
    
    <div class="">
      <div class="">
        <div class="">

                   @include('partials.tab')
                  <!--  @include('agent.pancard.pancard-type') -->
                  <br> <br>
                  <h1 style="margin-left: 40%;color:white">Comming Soon........</h1>
                <!--  <div class="col-md-3" style="margin-left:80px;">
                <h4 style="color:black">PanCard Activation</h4>
                
             
             
			@if(Auth::user()->member->is_pan_active ==1)
				<button type="button"  class="btn btn-success">Pan Card Service Activated</button>
			@elseif(Auth::user()->member->is_pan_active ==3)
				<button type="button" id="pan_btn" class="btn btn-warning">Pan Service Activation Pening</button>
			@else
				<button type="button" id="pan_btn" class="btn btn-basic" onClick="activePancardService()">Activation</button>
			@endif
             
              
             </div> -->
            
             
          </div>  
        </div>
      </div>
    </div>    
  </div>
  
  @include('layouts.footer')

</div>

 <meta name="_token" content="{!! csrf_token() !!}"/>
@endsection
