@extends('layouts.app')

@section('content')
<style>
.loaderImage {
    width: 50px;
    height: 50px;
}
</style>
<script type="text/javascript">
	function recharge_pay() {
		var ca_number = $("#ca_number").val();
		var provider = $("#provider").val();
		var amount = $("#amount").val();
		
		if(ca_number =='')
			{
				alert("Please enter valid CA Number");
				return false;
			}
			else if(amount =='')
			{
				alert("Please enter amount");
				return false;
			}

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                }
            })
            if (confirm('Are you sure you want to Recharge Amount '+ amount +'?')) {
            var dataString = 'number=' + ca_number + '&provider=' + provider + '&amount=' + amount;
            $.ajax({
                type: "POST",
                url: "{{url('store-bbps')}}",
                data: dataString,
				beforeSend:function()
				{
					$("#recharge_button").prop('disabled',true);
					$("#recharge_button").hide();
					$("#billPayLoader").show();
				},
                success: function (msg) {
					$("#recharge_button").show();
					$("#billPayLoader").hide();
               		$('#recharge_button').prop('disabled',false);
                    $("#ca_number").val('');
                    $("#provider").val('');
                    $("#amount").val('');
                    $("#trbutto").prop("disabled", false);
                    $("#amount").val();
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
</script>
                   
@include('agent.bbps.bbps-type')


<div class="form-group col-md-3 ">
	@include('partials.message_error')
	<div class="form-group">
		<label class="label" style="color:black">Operator</label>
		  {{ Form::select('provider', $provider, old('provider'), array('class' => 'form-control','id'=>'provider')) }}
	</div>
    <div class="form-group">
		<label class="label" style="color:black">CA Number</label>
		<input value="" class="form-control" name="ca_number" placeholder="Enter valid CA Number" id ="ca_number">	
	</div>
	<div class="form-group">
		<label class="label" style="color:black">Amount</label>
		<input value="" class="form-control" name="amount" placeholder="Enter valid Amount" id="amount"></div>
	<div class="form-group">
		<button type="button" class="btn btn-success" id="recharge_button" onclick="recharge_pay()"
           value="add">Pay Now
		</button>
		 <img src="{{url('/img/loader.gif')}}" id="billPayLoader" class="loaderImage" style="display:none"/>
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
