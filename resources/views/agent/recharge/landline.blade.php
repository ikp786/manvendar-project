@extends('layouts.app')

@section('content')
<style>
.form-control {
height:34px !important;
}
.nav-tabs>li>a
{
color:white;
}
</style>
<script>
function recharge_pay() {
         
            $("#mobilbtn").text("Processing...");
            var mobile_number = $("#mobile_number").val();
            var mobile_provider = $("#mobile_provider").val();
            var mobile_amount = $("#mobile_amount").val();
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                }
            })
             if (confirm('Are you sure you want to Recharge Amount '+ mobile_amount +'?')) {
            var dataString = 'number=' + mobile_number + '&provider=' + mobile_provider + '&amount=' + mobile_amount;
            $.ajax({
                type: "POST",
                url: "{{url('recharge')}}",
                data: dataString,
                success: function (msg) {
               
                    $('#recharge_button').prop('disabled',false);
                    $("#mobile_number").val('');
                    $("#mobile_provider").val('');
                    $("#mobile_amount").val('');
                    $("#trbutto").prop("disabled", false);
                    $("#mobile_amount").val();
                    $("#mobilbtn").text("Pay Now");
                    if (msg.status == 'success') {
                         $("#showaccountnumber").html(mobile_number);
                            $("#showcustomernumber").html(mobile_provider);
                            $("#showamount").html(mobile_amount);
                            $("#showid").html(msg.operator_ref);
                            $("#customer_name").html(status);
                            $("#myModal").modal("toggle");
                        //swal("Success", msg.message, "success");
                        //window.location.reload();
                    } else {
                        alert(msg.message);
                        //window.location.reload();
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
<div class="super_container">

	
	
	<!-- Home -->

	<div class="home">
		
	
	</div>

	<!-- Search -->

	<div class="search">
		

		<!-- Search Contents -->
		
		<div class="container fill_height">
			<div class="row fill_height">
				<div class="col fill_height">

                   	@include('partials.tab')
					@include('agent.recharge.recharge-type')
					<br>
			        
			        <div class="col-md-4">
					<!-- Recharge Panel -->
					<div class="form-group">
							<label class="label" style="color:black">Operator</label>
							
							{{ Form::select('provider', $provider, old('provider'), array('class' => 'form-control','id' => 'mobile_provider')) }}
		         		</div>
						<div class="form-group">
						  <label class="label" style="color:black">LandLine Number</label>
		                  <input class="form-control" type="text" value="" name="number" id="mobile_number" placeholder="Enter Valid Number" maxlength="10">
						</div>
						
						<div class="form-group">
		                  <label class="label" style="color:black">Amount</label>
		                  <input type="number" value="" class="form-control" name="amount" id="mobile_amount" placeholder="Enter valid Amount">
						</div>
						
						 <button style="width:25%;" onclick="this.disabled=true;recharge_pay();" type="button" class="btn btn-success">Submit</button>
						 
					</div>
					<div class="col-md-6" style="margin-left: 183px">
                    

                            <div id="carousel-example-generic" class="carousel slide" data-ride="carousel">
                                <div class="carousel-inner">
                                    <div class="item active" data-interval="20000">
                                         <img src="newlog/images/IMAG.jpg" >
                                    </div>
                                    <div class="item" data-interval="20000">
                                        <img src="newlog/images/IMAG.jpg" st>
                                    </div>
                                                            
                                </div>
                                <a class="left carousel-control" href="#carousel-example-generic" data-slide="prev">
                                    <span class="fa fa-angle-left"></span>
                                </a>
                                <a class="right carousel-control" href="#carousel-example-generic" data-slide="next" style="margin-right: 10px">
                                    <span class="fa fa-angle-right"></span>
                                </a>
                            </div>  
                     </div>  
					
				</div>
			</div>
		</div>		
	</div>

	
	
	@include('layouts.footer')

</div>
   <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
                        <div class="modal-dialog" role="document">
                            <div class="modal-content">
                                <div class="modal-header warning">
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                                aria-hidden="true">&times;</span></button>
                                    <h4 class="modal-title" id="myModalLabel">Recharge Detail</h4>
                                </div>
                                <div class="modal-body">
                                    <div id="dvContents" style="border: 1px dotted black; padding: 5px; width: 100%">
                                        <form class="form-horizontal">
                                            <input type="hidden" value="" id="user_id" name="user_id">
                                            <input id="c_bene_id" type="hidden">
                                            <input id="c_sender_id" type="hidden">
                                            <div class="form-group">
                                                <label for="bank_account" class="control-label col-sm-4">
                                                    Agent Name : </label>

                                                <label for="bank_account" class="control-label col-sm-4">
                                                    {{ Auth::user()->name }}</label>

                                            </div>
                                            <!--<div class="form-group">
                                                <label for="bank_account" class="control-label col-sm-4">
                                                    Customer Name : </label>

                                                <label for="bank_account" id="customer_name" class="control-label col-sm-4">
                                                </label>

                                            </div>-->
                                           <!-- <div class="form-group">
                                                <label for="bank_account" class="control-label col-sm-4">
                                                    Customer Number : </label>
                                                <label id="showcustomernumber" for="bank_account" class="control-label col-sm-4">
                                                </label>

                                            </div>-->
                                            <div class="form-group">
                                                <label for="bank_account" class="control-label col-sm-4">
                                                    Mobile Number : </label>

                                                <label id="showaccountnumber" for="bank_account" class="control-label col-sm-4">
                                                </label>

                                            </div>
                                            <div class="form-group">
                                                <label for="bank_account" class="control-label col-sm-4">
                                                    Amount : </label>

                                                <label id="showamount" for="bank_account" class="control-label col-sm-4">
                                                </label>

                                            </div>
                                           <!-- <div class="form-group">
                                                <label for="bank_account" class="control-label col-sm-4">
                                                    Fee :</label>

                                                <label id="showfee" for="bank_account" class="control-label col-sm-4">
                                                    2%
                                                </label>

                                            </div>-->
                                            <div class="form-group">
                                                <label for="bank_account" class="control-label col-sm-4">
                                                    Txid : </label>

                                                <label id="showid" for="bank_account" class="control-label col-sm-4">
                                                </label>
                                            </div>
                                            <div class="form-group">
                                                <label for="bank_account" class="control-label col-sm-4">
                                                    Status : </label>

                                                <label id="statusnew" for="bank_account" class="control-label col-sm-4">
                                                    Success</label>
                                            </div>
                                            <div class="form-group">
                                                <label for="bank_account" class="control-label col-sm-4">
                                                    Date &amp; Time : </label>

                                                <label for="bank_account" class="control-label col-sm-4">

                                                    {{ $mytime = Carbon\Carbon::now() }}


                                                </label>
                                            </div>
                                            <div class="form-group">
                                                <label for="bank_account" class="control-label col-sm-4">
                                                    Thanks! {{ Auth::user()->company->company_name }} </label>

                                                <label for="bank_account" class="control-label col-sm-4">

                                                    {{ Auth::user()->mobile }}

                                                </label>
                                            </div>

                                        </form>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <input type="button" onclick="PrintDiv();" value="Print"/>
                                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                </div>
                            </div>
                        </div>
                    </div>
 <meta name="_token" content="{!! csrf_token() !!}"/>
@endsection
