@extends('layouts.app')

@section('content')
<style type="text/css">
	.leftside{
		right: -30px;
	}

	.inputclass{
		right: -30px;
	}
</style>

   
   <script type="text/javascript">
		var msg_box="Due to security reason,Right click not allowed";
		function dis_rightclickIE(){
			if(navigator.appName=='Microsoft Internet Explorer'&&(event.button==2||event.button==3))alert(msg_box)
		}
		function dis_rightclickNS(e){
			if((document.layers||document.getElementById&&!document.all)&&(e.which==2||e.which==3))
			{
				alert(msg_box)
		return false;
		}}
		if(document.layers){
			document.captureEvents(Event.MOUSEDOWN);
			document.onmousedown=dis_rightclickNS;
		}
		else if(document.all&&!document.getElementById){
			document.onmousedown=dis_rightclickIE;
		}
		document.oncontextmenu=new Function("alert(msg_box);return false")
</script>


<div class="super_container">

	
	
	<!-- Home -->

	<div class="home" style="background: #8d4fff;">
		
	
	</div>

	<!-- Search -->

	<div class="search" style="height:480px">
		

		<!-- Search Contents -->
		
		<div class="container fill_height">
			<div class="row fill_height">
				<div class="col fill_height">

					<!-- Search Tabs -->
                   	@include('partials.tab')
					<br>
					<div class="card">
			        <table class="table is-narrow">
					<div id="country3" class="tabcontent">
						<div class="main">
							<div class="innerpages">
							<div class="leftside">
								<h4>Payment</h4>
								<input type="hidden" name="cciid" id="cciid1759" value=""/>
								<form id="formPaymentOptions1" name="formPaymentOptions1" method="POST" action="">
									<input name="merchantTxnId" id="Order_Id" type="hidden" value="CORIC-1537867338847"/>
									<input name="addressState" type="hidden" value=""/>
									<input name="addressCity" type="hidden" value=""/>
									<input name="addressStreet1" type="hidden" value=""/>
									<input name="addressCountry" type="hidden" value=""/>
									<input name="addressZip" type="hidden" value=""/>
									<input name="firstName" type="hidden" value=""/>
									<input name="lastName" type="hidden" value=""/>
									<input name="phoneNumber" type="hidden" value=""/>
									<input name="email" type="hidden" value=""/>
									<input name="paymentMode" type="hidden" value=""/>
									<input name="issuerCode" type="hidden" value=""/>
									<input name="cardHolderName" type="hidden" value=""/>
									<input name="cardNumber" type="hidden" value=""/>
									<input name="expiryMonth" type="hidden" value=""/>
									<input name="cardType" type="hidden" value=""/>
									<input name="cvvNumber" type="hidden" value=""/>
									<input name="expiryYear" type="hidden" value=""/>
									<input name="returnUrl" type="hidden" value=""/>
									<input name="orderAmount" type="hidden" value=""/>
									<input type="hidden" name="reqtime" value=""/>
									<input type="hidden" name="secSignature" value=""/>
									<input type="hidden" name="currency" value=""/>
									<input type="hidden" id="dpSignature" name="dpSignature" value=""/>
									<input type="hidden" name="couponCode" value=""/>
									<input type="hidden" name="alteredAmount" value=""/>
									<fieldset class="payment">
										<div class="leftfieldset inputclass">
											<label>Pay Online</label>
											<ul class="listarrow">
												<li>Using VISA, MasterCard, AMEX and Debit Cards</li>
												<li>Using cash cards</li>
											</ul>
											<p style='margin-left:30px'><br/><input type="checkbox" name="tc" id="tc"/>&nbsp;I agree to the Tramo(India) <a class="paytc" target="_blank" href="{{route('travel-carbookingcondition')}}" 
											style="text-decoration:underline;cursor:pointer;">Terms &amp; Conditions</a></p>
										</div>
										<button style="right: -180px">Pay and Book</button><br><br>
										<div class="rightfieldset">
											<div class="payandbook">
												<a onclick="javascript: _checkPaymentSubmit(document.getElementById('formPaymentOptions1'));" href="javascript: void(0);">
													
												</a>
											</div>
										</div>
									</fieldset>
								</form>
								<form id="formPaymentOptions2" name="formPaymentOptions2" method="POST" action="./">
									<input type="hidden" id="hdCarModel1759" name="hdCarModel" value="<br/>"/>
									<input type="hidden" id="hdCat1759" name="hdCat" value="Budget"/>
									<input type="hidden" name="hdOriginName" id="hdOriginName1759" value=""/>
									<input type="hidden" name="hdOriginID" id="hdOriginID1759" value=""/>
									<input type="hidden" name="hdDestinationName" id="hdDestinationName1759" value=""/>
									<input type="hidden" name="hdDestinationID" id="hdDestinationID1759" value=""/>
									<input type="hidden" name="hdPkgID" id="hdPkgID1759" value="139392">	<input type="hidden" name="" id="hdCarCatID1759" value="3"/>
									<input type="hidden" name="dayRate" id="dayRate1759" value=""/>
									<input type="hidden" name="kmRate" id="kmRate1759" value=""/>
									<input type="hidden" name="duration" id="duration1759" value="1"/>
									<input type="hidden" name="totFare" id="totFare1759" value=""/>
									<input type="hidden" name="tab" id="tab1759" value="4"/>
									<input type="hidden" name="hdPickdate" id="pickdate1759" value=""/>
									<input type="hidden" name="hdDropdate" id="dropdate1759" value=""/>
									<input type="hidden" name="hdTourtype" id="tourtype1759" value=""/>
									<input type="hidden" name="hdDistance" id="distance1759" value=""/>
									<input type="hidden" name="monumber" id="mobile1759" value=""/>
									<input type="hidden" name="picktime" id="picktime1759" value=""/>
									<input type="hidden" name="pincode" id="pincode1759" value=""/>
									<input type="hidden" name="address" id="address1759" value=""/>
									<input type="hidden" name="name" id="name1759" value=""/>
									<input type="hidden" name="email" id="email1759" value=""/>
									<input type="hidden" name="cciid" id="cciid1759" value=""/>
									<input type="hidden" name="hdTransactionID" id="hdTransactionID1759" value=""/>
									<input type="hidden" name="hdTrackID" id="hdTrackID1759" value=""/>
									<input type="hidden" name="hdRemarks" id="hdRemarks1759" value=""/>
									<input type="hidden" name="PkgHrs" id="PkgHrs1759" value=""/>
									<input type="hidden" name="discount" id="discount1759" value=""/>
									<input type="hidden" name="discountAmt" id="discountAmt1759" value=""/>
									<input type="hidden" name="empcode" id="empcode1759" value=""/>
									<input type="hidden" name="disccode" id="disccode1759" value=""/>
									<input type="hidden" name="ChauffeurCharges" id="ChauffeurCharges1759" value=""/>
									<input type="hidden" name="NightStayAllowance" id="NightStayAllowance1759" value=""/>
									<input type="hidden" name="nightduration" id="nightduration1759" value=""/>
									<input type='hidden' id="city_location_lat" name="city_location_lat" value=""/>
									<input type='hidden' id="city_location_long" name="city_location_long" value=""/>
									<input type="hidden" name="refclient" id="refclient" value=""/>
									<input type='hidden' id="CGSTPercent" name="CGSTPercent" value=""/>
									<input type='hidden' id="SGSTPercent" name="SGSTPercent" value=""/>
									<input type='hidden' id="IGSTPercent" name="IGSTPercent" value=""/>
									<input type='hidden' id="GSTEnabledYN" name="GSTEnabledYN" value=""/>
									<input type='hidden' id="ClientGSTId" name="ClientGSTId" value=""/>
									<fieldset class="payment">
										<div class="leftfieldset inputclass">
											<label>Pay cash to the driver at start of journey</label>
											<ul class="listarrow">
												<li>You shall receive a confirmation call 2-4 hours prior to start of journey</li>
												<li>Cab will be dispatched to pick up location after receiving confirmation on call</li>
											</ul>
										</div>
										<button>Confirm Booking</button>
										<div class="rightfieldset">
											<div class="confirmbooking"><br/>
												<a onclick="javascript: _checkPaymentSubmit(document.getElementById('formPaymentOptions2'));" href="javascript: void(0);"></a>
											</div>
										</div>
									</fieldset>
								</form>
								<div class="border_b">
									
								</div>
								<div class="middiv inputclass">
								 <strong>NOTE</strong>
									<ul>
										<li>Final amount will be as per actual</li>
										<li>Pending amount after advance adjustment is to be paid to driver as cash at the end of journey</li>
										<li>0% cancellation charges on amount paid as advance</li>
									</ul>
								</div>
							</div>

							</div>
						</div>
					</div>
					</table>
					</div>
				</div>
			</div>
		</div>		
	</div>
	
	

</div>

 <meta name="_token" content="{!! csrf_token() !!}"/>
@endsection



