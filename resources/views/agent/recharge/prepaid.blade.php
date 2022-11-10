@extends('layouts.app')
@section('content')
@include('layouts.submenuheader')

<script type='text/javascript'>

	function onEvent(event) {
	    if (event.key === "Enter") {
	        alert('Enter key press');
	    }
	};
   
</script>
<script type="text/javascript">

function recharge_pay() {
         
            $("#mobilbtn").text("Processing...");
            var mobile_number = $("#mobile_number").val();
            var mobile_provider = $("#mobile_provider").val();
			var mobile_amount = $("#mobile_amount").val();
			var numberPat = /^[0-9]+$/;
				
            if(mobile_number =='')
			{
				alert("Please enter valid mobile number");
				$("#mobile_number").focus();
				return false;
			}
			else if(!mobile_number.match(numberPat))
			{
				alert('Only Number is allowed');
				$('#mobile_number').focus();
				return false;
			}
			else if(mobile_number.length != 10)
			{
				alert("Please enter 10 digits mobile number");
				$('#mobile_number').focus();
				return false;	
			}
			else if(mobile_amount =='')
			{
				alert("Please enter amount");
				return false;
			}
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
                dataType: "json",
				beforeSend:function()
				{
					$('#recharge_button').prop('disabled',true);
					$('#recharge_button').hide();
					$("#rechargeImg").show();
				},
                success: function (msg) 
				{
               
               		$('#recharge_button').prop('disabled',false);
					$('#recharge_button').show();
					$("#rechargeImg").hide();
                    $("#mobile_number").val('');
                   // $("#mobile_provider").val('');
                    $("#mobile_amount").val('');
                    $("#trbutto").prop("disabled", false);
                    $("#mobile_amount").val();
                    $("#mobilbtn").text("Pay Now");
                    if (msg.status == 'success') 
					{
						refreshBalance();
						$("#showaccountnumber").html(mobile_number);
						$("#showcustomernumber").html(mobile_provider);
						$("#showamount").html(mobile_amount);
						$("#showid").html(msg.operator_ref);
						$("#customer_name").html(status);
						$("#myModal").modal("toggle");
						 alert(msg.message);
						// console.log('testing');
						 $('#myModalprepaid').modal('hide');
						 location.reload();

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
			var providerId =$("#mobile_provider").val();
			var circle_id =$("#circle_id").val();
			var mobile_number = $("#mobile_number").val();
			if(circle_id =='')
			{
				alert("Please Select Circle");
				return false;
			}
			var dataString = 'mobileNumber=' + mobile_number + '&providerId=' + providerId;
			 $.ajax({
                type: "GET",
                url: "{{url('getPrepedRechargeOffer')}}",
                data: dataString,
                dataType: 'json',
				beforeSend:function()
				{
					$("#mobileLoader").show()
					$('#offerTBody').html('');
				},
                success: function (result) 
				{
					$("#mobileLoader").hide();
					if(result.status == 1)
					{
						offer = result.records
						var count = offer.length;
						if(count > 1)
						{
							var content='';
						$.each(result.records, function(key,val) 
						{   
							
								content +='<tr onClick="getClickOfferAmount('+ val.rs+')"><div ><td>'+ val.rs +'</td><td>'+ val.desc + '</td></div></tr>';
						});						
						$('#offerTBody').html(content);
						}
							
						else{
						alert(offer.desc)
						}
						//console.log(result.error);
					}
					else
					{
					/* var content='';
				
						 $.each(result.data, function(key,val) {             
								
								 content +='<tr><td>'+ val.recharge_amount +'</td><td>'+ val.recharge_long_desc +'</td><td>' + val.recharge_validity + '</td></tr>';
						});  
						content +=' </tbody></table>';
						$('#offer_div').html(content); */
					}
				}

            });
		}
		function getClickOfferAmount(amount)
		{ 
			$("#mobile_amount").val(amount);
		}
		
		function getSpecialNumberOffer()
		{
			var provider =$("#mobile_provider option:selected").text();
			var mobile_number =$("#mobile_number").val();
			if(mobile_number =='')
			{
				alert("Please Enter Mobile Number");
				return false;
			}
			var dataString = 'provider=' + provider + '&mobile_number=' + mobile_number;
			 $.ajax({
                type: "GET",
                url: "{{url('special-number-offer')}}",
                data: dataString,
                dataType: 'json',
				beforeSend:function()
				{
					$("#mobileLoader").show()
					$("#getSpecialNumberOffer").hide()
				},
                success: function (result) 
				{
					$("#mobileLoader").hide()
					$("#getSpecialNumberOffer").show()
					if(result.status=='FAILED')
					{
						content = "<p style='color: red;font-size: 22px;font-family: time;'>Offers is not available right now. <br>Please try again after sometime</p>";
						$('#offer_div').html(content);
						console.log(result.error);
					}
					else if(result.status=='SUCCESS')
					{
						var content = '<table class="table table-hover table-dark" id="example2"><thead><tr ><th>Price</th><th>Offer</th></tr></thead> <tbody>';
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

function recharge_status(id)
    {
        var token = $("input[name=_token]").val();
            var dataString = 'id=' + id;
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                }
            })
            $.ajax({
                type: "POST",
                url: "{{url('/re-check-status')}}",
                data: dataString,
                datatype: "json",
                success: function (data) {
                    $('#re_check_'+id).prop('disabled',false);
                    alert(data.message);
                }
        }); 
    } 


	function openRechargeModel(provider_id) {
		$('#offer_div').html(" ");
		$('#mobile_amount').val(" ");
		$("#mobile_provider").val(provider_id);
		var providerName = $("#mobile_provider option:selected").text();
		$("#preRchHeader").html(providerName+ " Recharge");
		$("#mobile_number").val('');
		//$("#myModal").model(sh);
		$("#myModalprepaid").modal("toggle");
	}	
/*function callAutoFuncion()
{
	var username = $("#mobile_number").val();
	if (username.length == 10 && $.isNumeric(username)) 
	{
		getOffer();
	}
}*/
</script>
@include('agent.recharge.recharge-type')
<div class="row col-md-12">
	@foreach($provider as $prov)
		<img src="{{url('/')}}/{{$prov->provider_image}}" id="providerId_{{$prov->id}}" class="providerImage" onClick="openRechargeModel({{$prov->id}})" style=" width:7%;height:1%;padding-right:8px"  data-toggle="tooltip" data-placement="top" title="{{$prov->provider_name}}">
	 @endforeach
</div>	
<div class="modal fade" id="myModalprepaid" role="dialog">
    <div class="modal-dialog modal-lg" >
      <!-- Modal content-->
      <div class="modal-content">
        <div class="modal-header">
        	 <h4 class="modal-title" id="preRchHeader">Prepaid Recharge</h4>
          <button type="button" class="close" data-dismiss="modal">&times;</button>
        </div>
        <div class="modal-body col-md-12 row">
        	<div class="col-md-4">					
				
					<div class="form-group">
						<label class="label" style="color:black">Mobile Number</label>
						<input class="form-control" type="text" value="" name="number" id="mobile_number" placeholder="Enter Mobile Number" maxlength="10" onkeyup="callAutoFuncion()" autocomplete="off">
						
					</div>
					
					<div class="form-group">
						<label class="label" style="color:black">Operator</label>
						<select id="mobile_provider" class='form-control' name="provider" readonly="" disabled="disabled" >
							@foreach($provider as $prov)
							<option value="{{$prov->id}}">{{$prov->provider_name}}</option>
							@endforeach
						</select>
					</div>
					<div class="form-group">
					  <label class="label" style="color:black">Amount</label>
					  <input type="text" value="" class="form-control" name="amount" id="mobile_amount" placeholder="Enter valid Amount">
					</div>			 
					<div class="form-group col-md-15" >
					 <button id="recharge_button" onclick="recharge_pay()"  type="submit" class="btn btn-primary">Submit</button>
					 <img src="{{url('loader/loader.gif')}}" id="rechargeImg" class="loaderImg" style="display: none;">
					  <button type="button" class="btn btn-success" id="getSpecialNumberOffer" onClick="getSpecialNumberOffer()">Special Offers</button>
					  <img src="{{url('loader/loader.gif')}}" id="mobileLoader" class="loaderImg" style="display: none;"/>
					  
					</div>		  
							 
			</div>
			<!--<div class="col-md-8" style="overflow-y: scroll;max-height: 350px;cursor: pointer;">				
				<div id="frmTasks" name="frmTasks" class="form-horizontal">
					<table id="tableTypeThree" class="table table-bordered table-hover dataTable no-footer">
						<thead>
							<tr>
								<th> Rs.</th>
								<th> Offer</th>
							</tr>
						</thead>
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
		<h4 style="color: red">NOTE:Special offer is Available for Vodafone,Idea,Airtel </h4>
        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        </div>
      </div>
    </div>
  </div>
 <div class="text-right">
	<a href="{{route('recharge-txn-history')}}?service_id={{@$serviceId}}" > Transaction History</a>
 </div>
<div class="ex1" style="overflow-y: scroll;">  
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
              <td> {{ $recharge_reports->status->status }}</td>
             
                  <td>@if(in_array($recharge_reports->status_id,array(1,3,9)))<button id="re_check_{{ $recharge_reports->id}}"onclick="this.disabled=true;recharge_status({{ $recharge_reports->id}});" class="btn btn-primary">Check</button> @endif</td>
				
                  @if(in_array($recharge_reports->status_id,array(1,3,9)))
			  <td style="text-align:center">
                <a target="_blank" href="{{ url('invoice') }}/{{ $recharge_reports->id }}">
                 <span class="btn btn-success btn-xs" style="font-size: 14px;"><i class="md md-visibility"></i>Receipt</span>
                    </a>
					</td>  
                
				@endif
                
            </tr>
            @endforeach
        </tbody>
    </table>
</div>		 
 <meta name="_token" content="{!! csrf_token() !!}"/>
@endsection
