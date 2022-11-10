@extends('layouts.app')

@section('content')
<style type="text/css">
	.divclass{
		right: -30px;
	}

	.inputclass{
		right: -150px;
	}

	.register .fieldrow .row_right.tripad
		{
		width:74%
	}
	.f_l{
		float:left
		}
	.tripimg
		{
			position:relative;left:5px;top:-2px
		}
	.clr
		{
			clear:both
			}
	.conform_trip{
		color:#599544;display:none
	}
	.row_left input[type="checkbox"]
	{
	float:right
	}
	.register.margin_b0{
	margin-bottom:0
	}
</style>

<script>
		function tour_advisor(){
			if($("#trip_advisor").is(":checked")){
			$('#tripadvisorid').attr('value','true');
			$('#tripadvisor_msg').show();
			}
			else
			{$('#tripadvisorid').attr('value','false');
			$('#tripadvisor_msg').hide();
		}}
</script>
<div class="super_container">

	
	
	<!-- Home -->

	<div class="home" style="background: #8d4fff;">
		
	
	</div>

	<!-- Search -->

	<div class="search" style="height: auto">
		

		<!-- Search Contents -->
		
		<div class="container fill_height">
			<div class="row fill_height">
				<div class="col fill_height">
						@include('partials.tab')
						
						<br>
						<div class="card" >
						<form class="table is-narrow " >
							<div style="right: -10px">
								
								<h4>Pickup Details</h4>
							</div>
							<div class="middiv">
								<fieldset class="register">
									<div class="fieldrow">
										<div class="row_left divclass">
										<label>Pickup Time</label>
										</div>
										<div class="row_right">
											<div class="selecttime" style="right:-180px">
												<select class="time" name="tHour" id="tHourSL" onchange="javascript: _setTime('tHourSL', 'tMinSL', 'selT', 'seltime', 'userTime');">
														<option value="00" selected="selected">00</option>
														<option value="01">01</option>
														<option value="02">02</option>
														<option value="03">03</option>
														<option value="04">04</option>
														<option value="05">05</option>
														<option value="06">06</option>
														<option value="07">07</option>
														<option value="08">08</option>
														<option value="09">09</option>
														<option value="10">10</option>
														<option value="11">11</option>
														<option value="12">12</option>
														<option value="13">13</option>
														<option value="14">14</option>
														<option value="15">15</option>
														<option value="16">16</option>
														<option value="17">17</option>
														<option value="18">18</option>
														<option value="19">19</option>
														<option value="20">20</option>
														<option value="21">21</option>
														<option value="22">22</option>
														<option value="23">23</option>
												</select>
											</div>
											<div class="selecttime" style="right:-230px;margin-top: -20px">
												<select class="time" name="tMin" id="tMinSL" onchange="javascript: _setTime('tHourSL', 'tMinSL', 'selT', 'seltime', 'userTime');">
													<option value="00" selected="selected">00</option>
													<option value="15">15</option>
													<option value="30">30</option>
													<option value="45">45</option>
												</select>
											</div>
											<span id="selT" class="showtime"><span id="seltime"></span></span>
											<script type="text/javascript">_setTime('tHourSL','tMinSL','selT','seltime','userTime');</script>
										</div>
										</div><br>
									<div class="fieldrow divclass">
										<div class="row_left">
										<label>Full Address</label>
										</div>
										<div class="row_right inputclass">
											<textarea name="address" id="address" ></textarea>
											<br/>
											<div style="clear: both;"></div>
											<small>Flight details required incase of Airport pickup</small>
										</div>
									</div><br>
									<div class="fieldrow divclass">
										<div class="row_left">
											<label>Pickup Location</label>
										</div>
										<div class="row_right inputclass">
											<input class="input-large" type="text" id="city_location_text" name="city_location_text" placeholder="Type Area...."/>
											
										</div>
									</div><br>
									<div class="fieldrow divclass">
										<div class="row_left">
											<label>Remarks</label>
										</div>
										<div class="row_right inputclass">
											<textarea name="remarks" id="remarks"></textarea>
											<br/>
										</div>
									</div>
								</fieldset>
							</div>
							<br>
							<div style="right: -10px">
								
								<h4>Contact Details</h4>
							</div>
							<div class="middiv">
								<fieldset class="register margin_b0">
									<div class="fieldrow">
										<div class="row_left divclass">
											<label>Mobile Number</label>
										</div>
										<div class="row_right" style="right: -180px">
											<input type="text" name="monumber" id="monumberX" value="" value="10 digit mobile number" onblur="if (value == '')
                                                 value = '10 digit mobile number'" maxlength="10" onkeypress="javascript: return _allowNumeric(event);" onfocus="if (value == '10 digit mobile number')
                                                             value = ''" onchange='datapopulateRetail();'/>
											<br/>
											<small>Your booking details will be SMSed to this number</small>
											<span class="pls_wait" id="populate">Please Wait..</span>
											<style type="text/css">.pls_wait{position:relative;top:-22px;left:33px;color:#35801b;display:none}</style>
										</div>
									</div>
									<br>
									<div class="fieldrow">
										<div class="row_left divclass">
											<label>Name</label>
										</div>
										<div class="row_right" style="right: -180px">
										<input type="text" name="name" id="txtname" value="Name" onblur="
										if (value == '')
                                             value = 'Name'" onfocus="if (value == 'Name')
                                                         value = ''" onkeypress="javascript: return _allowAlpha(event);"/>
										</div>
									</div>
									<br>
									<div class="fieldrow">
										<div class="row_left divclass">
											<label>Email</label>
										</div>
										<div class="row_right" style="right: -180px">
											<input type="text" name="email" id="txtemail" value="Email" onblur="if (value == '')
										                                                         value = 'Email'" onfocus="if (value == 'Email')
										                                                                     value = ''"/>
										</div>
									</div>
								</fieldset>
							</div>
							<br><br>

							<div class="heading"></div>
							<div class="middiv">
								<fieldset class="register margin_b0">
									<div class="fieldrow">
										<div class="inputclass" >
											<input type="checkbox" name="trip_advisor" id="trip_advisor" onclick="tour_advisor();">
										</div>
										
										<div class="row_right tripad">
												<div class="f_l" style="margin-top: -23px;right: -190px">I want to receive the city guide for my destination</div> 
												<div class="f_l tripimg"><!-- <img width="25%" src="images/logo23.png" data-pagespeed-url-hash="398380414" onload="pagespeed.CriticalImages.checkImageForCriticality(this);"> -->
												</div>
												<div class="clr"> </div>
												<div class="conform_trip inputclass" id="tripadvisor_msg">A pdf link will soon be sent to your registered email address after confirmation of the booking.
												</div>
										</div>
											<!-- <span id="tripadvisor_image" style="display:none;"><img src="images/logo23.png" data-pagespeed-url-hash="2670615167" onload="pagespeed.CriticalImages.checkImageForCriticality(this);"/>
											</span> -->
									</div>
								</fieldset>

							</div>
						<!-- <div class="heading" style='display:none'>Discount/Promotion Code <span style="font-size:12px">(Optional)</span></div>
						<div class="middiv">
						<fieldset class="register">
						<div class="fieldrow" id="promocode" style='display:none'>
						<div class="row_left">
						<label>Promotion Code</label>
						</div>
						<div class="row_right">
						<input type="text" onkeyup="activeinactivepayback()" name="empcode" id="empcode" maxlength="20" value="" placeholder="Promotion code"/>&nbsp;
						<span id="spDiscount"><a id="promodis" class="outstation" href="javascript: void(0);" onclick="javascript: _checkRetail(1759);">Apply Code <img src="" border="0" data-pagespeed-url-hash="2497877563" onload="pagespeed.CriticalImages.checkImageForCriticality(this);"/></a>
						</div>
						<span id='spancancledis' class="cancel_OT" style="display:none;"><a href="javascript:void()" onclick="canceloutstationdiscount(1759)">Cancel </a></span>
						</div>
						</fieldset>
						</div>
						<div class="heading" style="display:none" id="pblc">PAYBACK Loyalty Discount</div>
						<div class="middiv" style="display:none" id="pbpd">
						<fieldset class="register">
						<div class="fieldrow">
						<div class="row_left pay_design">
						<label>PAYBACK Loyalty Card No.</label>
						</div>
						<div class="row_right">
						<input type="text" name="txtcardnopb" id="txtcardnopb" maxlength="16" value="" onkeypress="javascript: return _allowNumeric(event);" onblur="javascript: if (this.value != '') {
						_redeemPoints();
						}"/>
						</div>
						</div>
						<div class="fieldrow">
						<div class="row_left pay_design">
						<label>Points In Card</label>
						</div>
						<div class="row_right">
						<input type="text" name="txtpointsincard" id="txtpointsincard" maxlength="16" value="" readonly="readonly"/>
						</div>
						</div>
						<div class="fieldrow">
						<div class="row_left pay_design">
						<label>Points to Redeem</label>
						</div>
						<div class="row_right">
						<input type="text" name="txtpbpoints" id="txtpbpoints" maxlength="10" value="" onkeypress="javascript: return _allowNumeric(event);" onblur="javascript: document.getElementById('txtamounttoredeem').readOnly = false;
						document.getElementById('txtamounttoredeem').value = parseInt(this.value / 4);
						document.getElementById('txtamounttoredeem').readOnly = true;"/>&nbsp;<br/>4 PAYBACK Points = 1 INR
						</div>
						</div>
						<div class="fieldrow">
						<div class="row_left pay_design">
						<label>Amount to Redeem</label>
						</div>
						<div class="row_right">
						<input type="text" name="txtamounttoredeem" id="txtamounttoredeem" maxlength="6" value="" readonly="readonly"/>
						</div>
						</div>
						</fieldset>
						</div>
						<div class="middiv" style="padding:0px 0px 0px 29px !important;">
						<fieldset class="register">
						<div class="fieldrow">
						<div class="row_left">&nbsp;</div>
						<div class="row_right">
						<div class="confirmbooking" id="cnfbooking"><a onclick="javascript: _makeBooking();" href="javascript: void(0);"></a></div>
						<div class="confirmbookingredeem" id="cnfbookingredeem"><a onclick="javascript: _getPBRedeem('1759');" href="javascript: void(0);"></a></div>
						</div>
						</div>
						</fieldset>
						</div>
						</div>
						</div>
						<div class="rightside">
						<h5>Booking Summary</h5>
						<div class="tpdiv">
						<img src="" alt="Budget" data-pagespeed-url-hash="3480736862" onload="pagespeed.CriticalImages.checkImageForCriticality(this);"/>
						<div class="clr"></div>
						<p><span>Car Type:</span> Budget - Swift Dzire or Equivalent<br/></p>
						<p><span>From:</span> Delhi</p>
						<p><span>Service:</span> Outstation </p>
						<p><span>Destination:</span> Aldona</p>
						<p><span>Pickup Date:</span> Tue 25-Sep, 2018</p>
						<p><span>Drop Date:</span> Tue 25-Sep, 2018</p>
						<p><span>Pickup Time:</span> Not Set</p>
						<p><span>Pickup Address:</span> Not Set</p>
						<h3>Round Trip Fare<br/>
						<span>Rs </span><span id="spPay">48306</span><span>/-</span> <span style="font-size:11px;margin:10px 30px 0px 0px;display:block;width:auto;float:right;font-weight:normal;color:#666666;">(Including GST)</span></h3>
						</div>
						<div class="tpdiv">
						<p><span>Fare Details</span></p>
						<ul>
						</ul>
						<p><span>Includes </span></p>
						<ul>
						<li>3813 Kms</li>
						<li>Per Km charge = Rs. 12.0</li>
						<li>No. of days = 1 day(s)</li>
						<li>Chauffeur charge = Rs.250 * 1</li>
						<li>Night Stay Allowance Charge =Rs. 250 * 0</li>
						<li>Minimum billable kms per day = 250 kms</li>
						<li>Discount Amount = Rs <span id="disA">0</span>/-</li>
						<li>GST</li>
						</ul>
						<p><span>Extra Charges</span></p>
						<ul>
						<li>Tolls, parking and state permits as per actuals</li>
						<li>Extra Km beyond 3813 kms = Rs.12.0/km</li>
						</ul>
						</div>
						<div class="tpdiv">
						<p><span>Rental Refund Policy</span></p>
						<ul>
						<li>If the booking is cancelled 2 Hrs prior to the pickup time - 100% refund will be provided.</li>
						<li>If the booking is cancelled within 2 Hrs prior to the pickup time – Minimum rental will be charged and rest will be refunded.</li>
						<li>If you didn't turn up to pick-up - NO refund will be made.</li>
						</ul>
						</div>
						<div class="tpdiv">
						<p>Final fare shall depend on actual kms traveled. </p>
						<div class="map">
						</div>
						</div>
							
							<button class="button search_button"><a href="{{route('travel-carsearch')}}">search</a></button>
 -->
							<br><br>
							<div style="right:-250px;">
								
								<button><a href="{{route('travel-car_confirmbooking')}}" style="right: -300px">Confirm Booking</a></button>
							</div>
						</form>
 					
					</div>


				</div>
			</div>
		</div>
	</div>				

 <meta name="_token" content="{!! csrf_token() !!}"/>
@endsection



