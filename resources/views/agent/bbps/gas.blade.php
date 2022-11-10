@extends('layouts.app')

@section('content')
@include('layouts.submenuheader')
</style>
<script type="text/javascript">
	function recharge_pay() {
    $("#recharge_button").text("Processing...");
    var mobile_number = $("#mobile_number").val();
    var mobile_provider = $("#mobile_provider").val();
    var mobile_amount = $("#mobile_amount").val();
	if(mobile_number =='')
			{
				alert("Please enter valid mobile number");
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
                url: "{{url('store-bbps')}}",
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
                      alert(msg.message);
					  refreshBalance();
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
function openRechargeModel(provider_id) {
    $("#mobile_provider").val(provider_id);
    var providerName = $("#mobile_provider option:selected").text();
    $("#preRchHeader").html(providerName+ "Bill");
    
    $("#myModalbill").modal("toggle");
}            

</script>

@include('agent.bbps.bbps-type')
<div class=" row col-md-12">
    @foreach($provider as $prov)
        <img src="{{url('/')}}/{{$prov->provider_image}}" id="providerId_{{$prov->id}}" class="providerImage" onClick="openRechargeModel({{$prov->id}})" style=" width: 9%;height: 3%;padding-right:9px" data-toggle="tooltip" data-placement="top" title="{{$prov->provider_name}}">
     @endforeach
</div> 
<div class="modal fade" id="myModalbill" role="dialog">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
             <h4 class="modal-title" id="preRchHeader">Postpaid Bill</h4>
          <button type="button" class="close" data-dismiss="modal">&times;</button>
        </div>
        <div class="modal-body"> 	       
			<div class="col-md-8">
				@include('partials.message_error')
				<div class="form-group">
					<label class="label" style="color:black">Operator</label>
					 <select id="mobile_provider" class='form-control' name="provider" readonly="" disabled="disabled" >
		                    @foreach($provider as $prov)
		                    <option value="{{$prov->id}}">{{$prov->provider_name}}</option>
		                    @endforeach
		                    </select>
				</div>
		         <div class="form-group">
					<label class="label" style="color:black">CA Number</label>
					  <input value="" class="form-control" name="amount" placeholder="Enter valid Amount">
				</div>
				<div class="form-group">
					<label class="label" style="color:black">Amount</label>
					  <input value="" class="form-control" name="amount" placeholder="Enter valid Amount">
				</div>
				<div class="form-group col-md-12" >
					 <button type="button" class="btn btn-success">Submit</button>
				</div>
			</div>
		</div>	
 	 
        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        </div> 
      </div>
    </div>
</div>	
<div class="text-right">
	<a href="{{route('recharge-txn-history')}}?service_id={{@$serviceId}}" > Transaction History</a>
 </div>
<div class="ex1" style="overflow-y: scroll">  
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
              <td><button id="re_check_{{ $recharge_reports->id}}"onclick="this.disabled=true;recharge_status({{ $recharge_reports->id}});" class="btn btn-primary">Check</button></td>
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




