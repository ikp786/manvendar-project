
   
                <div class="col-md-2" style="margin-right:150px">
                   
						@if(Auth::user()->role_id !=10)
                        <p class="pull-right" style="margin-top:-55px;"> Current Balance
                            :   <strong id="apibalancenew">{{ number_format(@Auth::user()->balance->user_balance,2) }}</strong>
                    
						</p>
                   @endif
						
              </div>
           
          