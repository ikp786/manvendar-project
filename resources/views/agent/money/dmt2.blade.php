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
function verification()
   {
        var number = $('#number').val();
        if(number=='')
        {
            alert('Please enter mobile number!');
            return false;
        }
        var dataString = 'mobile_number=' + number;
        $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                }
            })
                    $.ajax({
                    type: "POST",
                    url: "{{url('/validate-mobiledmt2')}}",
                    data: dataString,
                    dataType: "json",
                    beforeSend: function () {
                        
                    },
                    success: function (msg) {
                            if(msg.status_id==21)
                            {
                                $('#registration').show();

                            }
                            else if(msg.status_id==20)
                            {
                               
                               $('#m_validate_txnid').val(msg.transaction_id);
                               $('#otp_frm').show();

                            }
                            else
                            {
                                  $('#registration').hide();
                                  $('#otp_frm').hide();
                                  $('#show_addbene_button').show();
                                  $('#ben_frm').hide();
                                  $('#bene_otp_frm').hide();
                                  $('#limit_msg').hide();
                                  $('#get_bene_name').text('Bene Name: '+msg.sender_name);
                                  sender_limit();
                                  get_bene();
                            }

                        }
                });
	}
</script>


<div class="super_container">

	<div class="home">
		
	
	</div>

	<!-- Search -->

	<div class="search">
		

		<!-- Search Contents -->
		
		<div class="">
			<div class="">
				<div class="">

                   	@include('partials.tab')
					@include('agent.money.money-type')
					<br>
					  <h1 style="margin-left: 40%;color:white">Comming Soon........</h1>
						
			  <!-- <div class="form-horizontal">

                            <div class="form-group{{ $errors->has('number') ? ' has-error' : '' }}">

                                <label class="col-sm-4 control-label text-white-color">Mobile Number</label>
                               
                                <div class="col-md-4">

                                    <div class="input-group">
                                       
                                        <input id="number" class="form-control" maxlength="10">
                                        <input type="hidden" name="m_validate_txnid" id="m_validate_txnid">
                                        
                                    
                                    <span class="input-group-btn">
                                        <button id="submit" class="btn btn-primary" type="button" onclick="verification();">Click Now </button>
                                    </span>
                                    </div>
                                    @if ($errors->has('name'))

                                    <span class="help-block">

                                        <strong>{{ $errors->first('number') }}</strong>

                                    </span>

                                    @endif

                                </div>
                                <div class="col-md-4">
                                    <button style="display: none;" onclick="show_add_bene();" id="show_addbene_button" class="btn btn-primary">Add Beneficiary</button>
                                </div>

                            </div>
                            <div class="form-group">
                                <div class="col-md-6" id="limit_msg">
                                </div>
                                <div class="col-md-4" id="remaining_msg">
                                </div>
                            </div>
                        </div>--!>
                        <div class="panel-body content-bg-color" id="tbl" style="display: none;">
                            <div class="col-md-3" style="border-left: 1px solid;">
                            <div class="col-md-12" style="font-size:16px" id="get_bene_name"></div>
                            <div class="col-md-12" style="font-size:16px" id="get_bal_limit"></div>
                            <div class="col-md-12" style="font-size:16px" id="get_bal_remaining"></div>
                            <!-- <span> Limit : <b> 0.0 </b></span><br>
                            <span> Remaining : <b> 0.0 </b></span><br> -->

                            </div>
                            <div class="col-md-9">
                                <table style="font-size: 14px;" class="table table-responsive">
                                        <thead>
                                        <tr>
                                           
                                            <th>Bene details</th>
                                            <th>Amount</th>
                                            <th>Mode</th>
                                            <th>Action</th>

                                        </tr>
                                        </thead>
                                        <tbody id="bene_list">

                                        </tbody>
                                   </table>
                                </div>
                            </div>
                         <div class="panel-body" id="registration" style="display: none; ">

                                                           <div class="form-horizontal">

                                                                <div class="form-group">

                                                                    <label for="inputEmail"

                                                                           class="control-label col-sm-4 text-white-color">

                                                                        First Name</label>

                                                                    <div class="col-sm-4">

                                                                        <input style="text-transform: uppercase;" type="text" required

                                                                               class="form-control"

                                                                               id="f_name"

                                                                               placeholder="First Name">

                                                                    </div>

                                                                </div>

                          <div class="form-group">

                               <label for="inputEmail" class="control-label col-sm-4 text-white-color">Last Name</label>

                                     <div class="col-sm-4">

                                        <input style="text-transform: uppercase;" type="text" required class="form-control" id="l_name" placeholder="Last Name"> </div>

                                       </div>

                                                                <div class="form-group">

                                                                    <div class="col-xs-offset-4 col-xs-8">

                                                                        <button id="newnextstep" onclick="mobile_register();" class="btn btn-primary">Register </button>

                                                                    </div>

                                                                </div>

                                                            </div>

                                                        </div>
                                                        <div class="panel-body" id="otp_frm" style="display: none;">

                                                            <div class="form-horizontal">
                                                                <div class="form-group">
                                                                    <label for="otp" class="control-label col-sm-4 text-white-color">Enter

                                                                        OTP </label>

                                                                    <div class="col-sm-4">

                                                                        <input type="text" class="form-control" placeholder="Enter OTP"

                                                                               id="otp">
                                                                    </div>

                                                                </div>

                                                                <div class="form-group">

                                                                    <div class="col-xs-offset-4 col-xs-8">

                                                                        <a class="btn btn-success"

                                                                           onclick="otp_confirm();">Confirm</a>

                                                                        <a class="btn btn-primary"

                                                                           onclick="re_otp();">Resend

                                                                            OTP</a>

                                                                    </div>

                                                                </div>

                                                            </div>

                                                            <span class="success" id="sendercreate"

                                                                  style="display: none;"></span>

                                                        </div>

                                                         <div class="panel-body" id="deletebene_otp_frm" style="display: none;">

                                                            <div class="form-horizontal">
                                                                <div class="form-group">
                                                                    <label for="otp" class="control-label col-sm-4 text-white-color">Enter

                                                                        OTP </label>

                                                                    <div class="col-sm-4">

                                                                        <input type="text" class="form-control" placeholder="Enter OTP" id="del_bene_otp">
                                                                    </div>

                                                                </div>

                                                                <div class="form-group">

                                                                    <div class="col-xs-offset-4 col-xs-8">

                                                                        <a class="btn btn-success"

                                                                           onclick="otp_confirm();">Confirm</a>

                                                                        <a class="btn btn-primary"

                                                                           onclick="re_otp();">Resend

                                                                            OTP</a>

                                                                    </div>

                                                                </div>

                                                            </div>

                                                            <span class="success" id="sendercreate"

                                                                  style="display: none;"></span>

                                                        </div>
			       <!--  <div class="col-md-12">
			        	<h4 style="color:black; margin-left: 23%">Mobile Registration</h4>
			        	<div class="col-md-2">
								
				         </div>
				     <div class="col-md-4">
				     	<input type="number" class="form-control" id="mobile_number" placeholder="Enter Mobile number">
				     </div>
				     <div class="col-md-2">
				     	
				     </div>
						
						
					</div>	
					<div class="col-md-12">
						<div class="col-md-2">
							
				         </div>
				     <div class="col-md-4" id="mobile_register">
				     	<input type="text" name="f_name" id="f_name" placeholder="First Name" class="form-control">
				     	<input type="text" name="l_name" id="l_name" placeholder="Last Name" class="form-control"><br>
				     	<button onclick="mobile_register()"class="btn btn-success">Submit</button>
				     </div>

				     <div class="col-md-4" id="otp_frm" style="display: none;;">
				     	<input type="text" name="otp" id="otp" placeholder="Enter OTP" class="form-control">
				     	<button onclick="otp_confirm()"class="btn btn-success">Submit</button> <button onclick="re_otp()"class="btn btn-success">Re-OTP</button>
				     </div>
				    
					</div> -->
                    <div class="col-md-12">
                       <div class="panel-body" id="ben_frm" style="display:none;">
                            <div class="form-horizontal">
                                <input type="hidden" id="bene_id">
                                            <div class="form-group">
                        <label for="bank_name" id="bbank" class="control-label col-sm-4">Bank Name </label>
                                                <div class="col-sm-4">
                                                                      {{ Form::select('bank_name', $netbanks, old('service_id'), array('class' => 'form-control','id' => 'service_id')) }}
                                                                    </div>
                                                                </div>
                                                                <div id="an" class="form-group">
                                                                    <label for="bank_account" class="control-label col-sm-4">
                                                                        Account Number </label>
                                                                    <div class="col-sm-4">
                                                                        <div class="input-group">
                                                                            <input type="text" pattern=".{5,10}" required class="form-control"
                                                                                   id="bank_account"
                                                                                   placeholder="Bank Account Number">
                                                                           <!--  <span class="input-group-btn">
                                                                            <button id="bnv" class="btn btn-primary"
                                                                                    type="button"
                                                                                    onclick="account_name_verify();">Verify
                                                                            </button>
                                                                        </span> -->
                                                                        
                                                                        </div>
                                                                        <input type="hidden" id="acc_digit_val">
                                                                         <p id="digit_error" style="color:black; display:none;"></p>

                                                                    </div>
                                                                </div>
                                                                 <!-- <div id="fn" class="form-group">
                                                                    <label for="first_name" class="control-label col-sm-4">
                                                                        Branch Name </label>
                                                                    <div class="col-sm-4">
                                                                        <input style="text-transform: uppercase;" type="text" class="form-control" id="branch_name" placeholder="Beneficiary Name">

                                                                    </div>
                                                                </div> -->
                                                                <div id="fn" class="form-group">
                                                                    <label for="first_name"
                                                                           class="control-label col-sm-4">
                                                                        Name </label>
                                                                    <div class="col-sm-4">
                                                                        <input style="text-transform: uppercase;" type="text" class="form-control"
                                                                               id="first_name"
                                                                               placeholder="Beneficiary Name">

                                                                    </div>
                                                                </div>
                                                                <div id="bm" class="form-group">
                                                                    <label for="bene_mobile"
                                                                           class="control-label col-sm-4">Beneficiary
                                                                        Mobile</label>
                                                                    <div class="col-sm-4">
                                                                        <input type="text" class="form-control"
                                                                               id="bene_mobile"
                                                                               placeholder="Mobile Number">

                                                                    </div>
                                                                </div>

                                                                <div id="ic" class="form-group">
                                                                    <label for="ifsc" class="control-label col-sm-4">
                                                                        IFSC
                                                                        Code </label>
                                                                    <div class="col-sm-4">
                                                                        <input type="text" style="text-transform:uppercase;"class="form-control"
                                                                               id="ifsc"
                                                                               placeholder="IFSC Code">
                                                                    </div>
                                                                </div>
                                                                <div id="bn" class="form-group">
                                                                    <label for="ifsc" class="control-label col-sm-4"></label>
                                                                    <div class="col-md-4">
                                                                        <button class="form-control btn btn-success" type="button" onclick="addbene();">Add bene</button>
                                                                          
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <span class="success" id="sendercreate"
                                                                  style="display: none;"></span>
                                                        </div>
                       <div class="form-group col-md-4" id="bene_otp_frm" style="display: none;">
                        <input type="text" name="bene_otp" id="bene_otp" placeholder="Enter OTP" class="form-control">
                        <button onclick="bene_otp_confirm()"class="btn btn-success">Submit</button> <button onclick="re_otp()"class="btn btn-success">Re-OTP</button>
                         </div>

                    </div>
					
				</div>

			</div>
		</div>		
	</div>

	@include('layouts.footer')

</div>
<div class="modal fade" id="bene_del_otp" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <!-- <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                aria-hidden="true">&times;</span></button> -->
                    <h4 class="modal-title" id="myModalLabel">Delete beneficiarty</h4>
                </div>
                <div class="modal-body">
                	<input type="hidden" id="otp_bene_id">
                    <form class="form-horizontal">
                        <div class="form-group">
                            <label for="bank_account" class="control-label col-sm-4">
                                OTP </label>
                            <div class="col-sm-6">
                                <input type="text" class="form-control" id="deleteBeneOtp"
                                       placeholder="Entrer OTP">
                            </div>
                        </div>

                    </form>
                </div>
                <div class="modal-footer">
                   <button id="benedelete_success" type="button" onclick="this.disabled=true;benedelete_success()" class="btn btn-primary">Confirm
                    </button>
                </div>
            </div>
        </div>
    </div>

<meta name="_token" content="{!! csrf_token() !!}"/>
@endsection
