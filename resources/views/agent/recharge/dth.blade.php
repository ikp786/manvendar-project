@extends('layouts.app')

@section('content')
 @include('layouts.submenuheader')

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
							refreshBalance();
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
		
		function getOffer()
		{
			var provider =$("#mobile_provider option:selected").text();
			var mobile_provider =$("#mobile_provider").val();
			if(mobile_provider =='')
			{
				alert("Please Enter DTH Number");
				return false;
			}
			var dataString = 'provider=' + provider + '&mobile_provider=' + mobile_provider;
			 $.ajax({
                type: "GET",
                url: "{{url('getDTHOffer')}}",
                data: dataString,
                dataType: 'json',
				beforeSend:function(){
					$("#getOffer").text("Processing...");

				},
                success: function (result) 
				{
					$("#getOffer").text("Get Offers");
					if(result.status=='FAILED')
					{
						content = "<p style='color:red;font-size: 22px;font-family: time;'>Offers is not available right now. <br>Please try again after sometime</p>";
						$('#offer_div').html(content);
						console.log(result.error);
					}
					else if(result.status=='SUCCESS')
					{
						var content = '<table class="table table-hover table-dark" id="example2"><thead><tr><th>Price</th><th>Offer</th></tr></thead> <tbody>';
						 $.each(result.Response, function(key,val) {             
							content +='<tr onClick="getOfferAmount('+val.price+')"><td>'+ val.price +'</td><td>'+ val.offer +'</td></tr>';
						});  

						content +=' </tbody></table>';
						$('#offer_div').html(content);
					}
				}
            });
		}
		function getOfferAmount(amount){
			$("#mobile_amount").val(amount);
		}


function openRechargeModel(provider_id) {
	$('#offer_div').html(" ");
	$('#mobile_amount').val(" ");
	$("#mobile_provider").val(provider_id);
	var providerName = $("#mobile_provider option:selected").text();
	$("#preRchHeader").html(providerName+ " Recharge");
	
	$("#myModaldth").modal("toggle");
}
function getClickOfferAmount(amount)
		{ 
			$("#mobile_amount").val(amount);
		}
function countNumberOfDigits()
{
	var consumerNumber = $("#mobile_number").val();
	var provider_id = $("#mobile_provider").val();
	if(provider_id == 17 && consumerNumber.length == 10 && $.isNumeric(consumerNumber))
		getCustomerInfo();
	else if($provider_id == 12 && consumerNumber.length == 11 && $.isNumeric(consumerNumber))
		getCustomerInfo();
	else if($provider_id == 13 && consumerNumber.length == 10 && $.isNumeric(consumerNumber))
		getCustomerInfo();
}	
function getCustomerInfo()
{
	var consumerNumber = $("#mobile_number").val();
	//var provider_id = $("#mobile_provider").val();
	var dataString = 'consumerNumber=' + consumerNumber+'&providerId=' + provider_id;
			 $.ajax({
                type: "GET",
                url: "{{url('get-dth-customer-info')}}",
                data: dataString,
                dataType: 'json',
				beforeSend:function(){
					$("#getCustomerInfo").hide();
					$("#getCustInfoImage").show();
					$('#dthCustomerInfo').html();
				},
                success: function (result) 
				{
					//#gthImage').hide();
					var content = result.records[0];
					$("#getCustomerInfo").show();
					$("#getCustInfoImage").hide();
					//3004564078
					var data=  '<p> Consumer Information</p><table class="table table-bordered">';
						data +='<tr><td>Balance</td><td>'+ content.Balance +'</td></tr><tr><td>MonthlyRecharge</td><td>'+ content.MonthlyRecharge +'</td></tr><tr><td>NextRechargeDate</td><td>'+ content.NextRechargeDate +'</td></tr><tr><td>customerName</td><td>'+ content.customerName +'</td></tr><tr><td>planname</td><td>'+ content.planname +'</td></tr><tr><td>status</td><td>'+ content.status +'</td></tr>';
						
						data +=' </table>';
				/* 	var data = "<div>"
						data +="<span>Balance : "+ content.Balance+"</span>";
						data +="<span>MonthlyRecharge : "+ content.MonthlyRecharge+"</span>";
						data +="<span>NextRechargeDate : "+ content.NextRechargeDate+"</span>";
						data +="<span>status : "+ content.status	+"</span>";
						data +="<span>planname : "+ content.planname+"</span>"; */
						$('#dthCustomerInfo').html(data);
				}

            });
}
</script>
@include('agent.recharge.recharge-type')
<div class="row col-md-12">
	@foreach($provider as $prov)
	<img src="{{url('/')}}/{{$prov->provider_image}}" id="providerId_{{$prov->id}}" class="providerImage" onClick="openRechargeModel({{$prov->id}})" style=" width: 7%;height: 1%;padding-right:8px" data-toggle="tooltip" data-placement="top" title="{{$prov->provider_name}}">
	@endforeach
</div>
<div class="modal fade" id="myModaldth" role="dialog">
    <div class="modal-dialog modal-lg">
      <!-- Modal content-->
      <div class="modal-content">
        <div class="modal-header">
        	<h4 class="modal-title" id="preRchHeader"></h4>
          <button type="button" class="close" data-dismiss="modal">&times;</button>
        </div>
        <div class="modal-body col-md-12 row">
        					
			<div class="col-md-4">					
				<div class="form-group">
					<label class="label" style="color:black">Operator</label>
					<select id="mobile_provider" class='form-control' name="provider" readonly="" disabled="disabled" >
					@foreach($provider as $prov)
					<option value="{{$prov->id}}">{{$prov->provider_name}}</option>
					@endforeach
					</select>
				
				</div>
				<div class="form-group">
					<input class="form-control" type="text" name="number" id="mobile_number" placeholder="Enter DTH Number" onKeyUp="countNumberOfDigits()">
				</div>
				<div class="form-group">
				  <input type="text" value="" class="form-control" name="amount" id="mobile_amount" placeholder="Enter Amount">
				</div>
				
				 <button onclick="recharge_pay();" type="button"  class="btn btn-success">Submit</button>
				 <img src="{{url('/loader/loader.gif')}}" id="accVerifyLoaderImg" class="loaderImg" style="margin-top: 16px;display:none"/>
				 <button type="button" class="btn btn-success" id="getOffer" onClick="getOffer()">Get Offers</button>
				
				 <!--<button type="button" id="getCustomerInfo" class="btn btn-outline-primary" onClick="getCustomerInfo()">Get Cust info</button>
				 <img src="{{url('/loader/loader.gif')}}" id="getCustInfoImage" class="loaderImg" style="margin-top: 16px;display:none"/>-->
				 
				 <div id="dthCustomerInfo"></div>
			</div>
			<!--<div class="col-md-8" style="overflow-y: scroll;max-height: 400px;cursor: pointer;">				
				<div id="frmTasks" name="frmTasks" class="form-horizontal">
					<table id="tableTypeThree" class="table table-bordered">
						<thead>
							<tr>
								<th> Plan</th>
								<th> Desc</th>
								<th> Plan Name</th>
								<th> Date</th>
							</tr>
						</thead>
						 <img src="{{url('/loader/loader.gif')}}" id="gthImage" class="loaderImg" style="margin-top: 16px;display:none"/>
						<tbody id="offerTBody">
							
						</tbody>
					</table>
				</div>			 
			</div>-->
			<div class="col-md-8" style="overflow-y: scroll;max-height: 250px;cursor: pointer;">
				<div class="col-md-8 table-responsive" id="offer_div" >
				</div>	
			</div>
		
		</div>
		<h4 style="color: red">NOTE:Special offer is Available for Dishtv ,VidoconD2H ,Airtel Digital TV </h4>
        <div class="modal-footer">
			
          <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        </div>
      </div>
    </div>
  </div>			
<!--<div class="col-md-8 table-responsive text-center" id="offer_div" >
</div>-->
		
	
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
 <div class="text-right">
	<a href="{{route('recharge-txn-history')}}?service_id={{@$serviceId}}" > Transaction History</a>
 </div>
	<div  style="overflow-y: scroll">  
    <table id="tableTypeThree" class="table table-bordered table-hover">
        <thead>

            <tr>
              <th align="center">Date/Time</th>
              <th>ID </th>
              <th>User</th>
              <th>Txn ID </th>
              <th >Provider</th>
              <th>Number</th>
              <th>Amount</th>
              <th>Commission</th>
               <th>Status</th>
               <th>Action</th>
               <th>Report</th>
            </tr>
        </thead>
        <tbody>
            @foreach($report as $recharge_reports)
                <?php $s = $recharge_reports->created_at;
                $dt = new DateTime($s);?>
            <tr class="odd gradeX" style="background-color:white">
              <td align="center">{{ $dt->format('d/m/Y') }}<br>{{ $dt->format('H:i:s') }}</td>
              <td>{{ $recharge_reports->id }}</td>
              <td>{{ $recharge_reports->user->name }}</td>
              <td>{{ $recharge_reports->txnid }}</td>
              <td>{{ @$recharge_reports->provider->provider_name }}</td>
              <td>{{ $recharge_reports->number }}</td>
              <td>{{ $recharge_reports->amount }}</td>
              <td>{{ $recharge_reports->profit }}</td>
              <td> {{ $recharge_reports->status->status }}</td>
              <td>  @if(in_array($recharge_reports->status_id,array(1,3,9)))<button id="re_check_{{ $recharge_reports->id}}"onclick="this.disabled=true;recharge_status({{ $recharge_reports->id}});" class="btn btn-primary">Check</button> @endif</td>
                <td style="text-align:center">
                  @if(in_array($recharge_reports->status_id,array(1,3,9)))
                    <a target="_blank" href="{{ url('invoice') }}/{{ $recharge_reports->id }}">
                        <span class="btn btn-success btn-xs" style="font-size: 14px;"><i class="md md-visibility"></i>Receipt</span>
                    </a>
                @endif
                </td>  
            </tr>
            @endforeach
        </tbody>
    </table>
</div>                				
 <meta name="_token" content="{!! csrf_token() !!}"/>
@endsection
