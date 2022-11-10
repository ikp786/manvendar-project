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
    $("#mobile_provider").val(provider_id);
    var providerName = $("#mobile_provider option:selected").text();
    $("#preRchHeader").html(providerName+ "Bill");
    //$("#myModal").model(sh);
    $("#myModalbill").modal("toggle");
}      	
</script>
@include('agent.bbps.bbps-type')
<div class=" row col-md-12">
    @foreach($provider as $prov)
        <img src="{{url('/')}}/{{$prov->provider_image}}" id="providerId_{{$prov->id}}" class="providerImage" onClick="openRechargeModel({{$prov->id}})" style=" width: 8%;height: 2%;padding-right:9px" data-toggle="tooltip" data-placement="top" title="{{$prov->provider_name}}">
     @endforeach
</div> 
<div class="modal fade" id="myModalbill" role="dialog">
    <div class="modal-dialog">
      <!-- Modal content-->
      <div class="modal-content">
        <div class="modal-header">
             <h4 class="modal-title" id="preRchHeader">Postpaid Bill</h4>
          <button type="button" class="close" data-dismiss="modal">&times;</button>
        </div>
        <div class="modal-body"> 
            <div class="col-md-8">
                @include('partials.message_error')
        	<!-- Recharge Panel -->
        	   <div class="form-group">
        			<label class="label" style="color:black">Operator</label>
        			<select id="mobile_provider" class='form-control' name="provider" readonly="" disabled="disabled" >
                    @foreach($provider as $prov)
                    <option value="{{$prov->id}}">{{$prov->provider_name}}</option>
                    @endforeach
                    </select>	
         		</div>
        		<div class="form-group">
        		  <label class="label" style="color:black">Broad Band Number</label>
                  <input class="form-control" type="text" value="" name="number" id="mobile_number" placeholder="Enter Valid Number" maxlength="10">
        		</div>

        		<div class="form-group">
                  <label class="label" style="color:black">Amount</label>
                  <input type="number" value="" class="form-control" name="amount" id="mobile_amount" placeholder="Enter valid Amount">
        		</div>
        		
        		 <button  onclick="this.disabled=true;recharge_pay();" type="button" class="btn btn-success">Submit</button>
            </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        </div>
      </div>
    </div>
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
              <td>@if(in_array($recharge_reports->status_id,array(1,3,9)))<button id="re_check_{{ $recharge_reports->id}}"onclick="this.disabled=true;recharge_status({{ $recharge_reports->id}});" class="btn btn-primary">Check</button> @endif</td>
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
