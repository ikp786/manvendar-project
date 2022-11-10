@extends('layouts.app')
@section('content')

<script>
/*for date->Calender*/

    $(document).ready(function () {
        $.noConflict();
        $('.customDatepicker').datepicker({
              autoclose: true,  
            format: "dd-mm-yyyy"
        });

    }); 
	function TramocheckStatus(id,apiId)
	{
		var token = $("input[name=_token]").val();
		var number = $("#number").val();
		var dataString = 'id=' + id + '&mobile_number='+number;
		$.ajaxSetup({
		headers: {
			'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
		}
		});
		
			url = "{{url('check-txn-status')}}"
		
		$.ajax({
			type: "post",
			url: url,
			data: dataString,
			dataType: "json",
			beforeSend:function(){
				$("#checkBtn_"+id).hide();
				$("#checkBtn_"+id).attr('disabled',true);
			},
			success: function (data) {
				$("#checkBtn_"+id).show();
				$("#checkBtn_"+id).attr('disabled',false);
				alert(data.msg);
				if(apiId==5 && data.status==43)
					$("#checkstatusMessage_"+id).text(data.bankRefNo)
			}
		})
	}
</script>
@include('agent.report.report-type')

	<div class="col-md-12">
        <form method="get" action="{{Request::url()}}" class="form-inline" role="form">    
			
			<label class="sr-only" for="payid">Number</label>
			<input name="number" type="text" class="form-control" id="exampleInputEmail2"  value="{{app('request')->input('number')}}" placeholder="Number">
		
			<input name="fromdate" class="form-control customDatepicker" type="text" placeholder="From date" value="{{ (app('request')->input('fromdate')) ? app('request')->input('fromdate') : date('d-m-Y')}}">
		
			<input name="todate" class="form-control customDatepicker" type="text" placeholder="To date" value="{{ (app('request')->input('todate')) ? app('request')->input('todate') : date('d-m-Y')}}">
			@if(in_array(Auth::user()->role_id,array(5)))
			{{Form::select('searchOf', ['1' => 'Success', '2' => 'Failed','3'=>"Pending",'4'=>"Refunded",'24'=>"Successfull",'21'=>"Refund Success",'18'=>"In-Process",'6'=>'DT'], (app('request')->input('searchOf')), ['class'=>'form-control','placeholder'=>"--Select Status--"])}}
			
			{{Form::select('product', ['1' => 'Recharge/Bill Payment', '2' => 'Verification','4' => 'DMT 1','16' => 'DMT 2','5'=>"A2Z wallet",'10'=>'AEPS'], (app('request')->input('product')), ['class'=>'form-control','placeholder'=>"--Select Product--"])}} 
			@else
			{{Form::select('searchOf', ['1' => 'Success', '2' => 'Failed','3'=>"Pending",'4'=>"Refunded",'21'=>"Refund Success",'6'=>'DT'], (app('request')->input('searchOf')), ['class'=>'form-control','placeholder'=>"--Select Status--"])}}
			@endif
			
			<button name="SEARCH" value="SEARCH" type="submit" class="btn btn-primary btn-md">
			<i class="fa fa-search"></i></button>
			<button name="export" value="EXPORT" type="submit" class="btn btn-success waves-effect waves-light m-l-10 btn-md"><span class="glyphicon glyphicon-find"></span><i class="fa fa-file-excel-o" aria-hidden="true"></i>
			</button>
			<a href="{{url('transaction-report')}}" class="btn btn-primary  btn-md"><i class="fa fa-refresh"></i></a>
        </form>
	</div>
	<br><br><br>
	
<div class="box">
		<table  class="table table-bordered table-striped" id="example2">
			<thead >
				<tr>
					<th>Date/Time</th>
					<th>ID</th>
					<th>Remitter Number</th>
					<th>Acc/Mob/K Number<br>Bene Name</th>
					<th style="min-width: 212px;font-size: 12px;">Bank Name/<br>IFSC</th>
					<th>Operator Txn Id<br>/Remark</th>
					<th>Amount</th>
					<th>Status</th>
					<th>Bank RR Number/<br>Check</th>
					<th>Description</th>
					<th>Receipt</th>
					<th>Credit/Debit</th>
					<th>Opening Bal</th>
					<th>Credit Amount</th>
					<th>Debit Amount</th>
					<th>TDS</th>
					<th>Service Tax</th>
					<th>Balance</th>
					<th>Txn Type</th>
					<th>fund Transfer</th>
					<th>Complain</th>
				</tr>
			</thead>
			<tbody>
				@foreach($reports as $recharge_reports)
				<?php $s = $recharge_reports->created_at;
				$dt = new DateTime($s);?>
				<tr class="{{$recharge_reports->status->status}}-text">
					<td>{{ $dt->format('d-m-y')}}<br>{{ $dt->format('H:i:s') }}</td>
					<td>{{ $recharge_reports->id }}</td>
					<td>{{ $recharge_reports->customer_number }}</td>
					<td>{{ $recharge_reports->number }}<p><span style="font-weight:bold;">					{{@$recharge_reports->beneficiary->name}}</p>
						@if(@$recharge_reports->api_id == 2)
							{{ @$recharge_reports->biller_name}}
						@endif
						</span>
					</td>
				    <td>
				   @if(in_array($recharge_reports->api_id,array(2,10)))
					<?php 
					$content = explode("(",$recharge_reports->description);
						try{
							echo $recharge_reports->description;
						//echo $content[0]; echo "<br>";echo  $content[1];
					}
					catch(\Exception $e)
					{
						echo $recharge_reports->description;
					}
					?>
				   
					@else
						<p style="font-weight: bold">
						{{ @$recharge_reports->beneficiary->bank_name }}</p>
						<p style="font-style: italic;">{{ @$recharge_reports->beneficiary->ifsc }}
						</p>
					  @endif  
				    </td>
					<td>{{$recharge_reports->txnid }}<br>{{$recharge_reports->remark}}</td>
				    <td>{{number_format($recharge_reports->amount,2) }}</td>
				    <td>{{$recharge_reports->status->status }}
						<span id="checkstatusMessage_{{$recharge_reports->id}}" style="color:green"></span>
						<p>@if($recharge_reports->recharge_type==0 && $recharge_reports->txnid !="DT" && !in_array($recharge_reports->api_id,array(2,10))) 
							{{($recharge_reports->channel==2)?"IMPS":"NEFT"}}</p>@endif
					</td>
					<td>@if($recharge_reports->status_id !=4)
						{{ $recharge_reports->bank_ref }} 	
						@endif						@if(in_array($recharge_reports->status_id,array(1,3,9,18)) && $recharge_reports->api_id !=10 )
						<input type="button" id ="checkBtn_{{$recharge_reports->id}}" onclick="TramocheckStatus({{ $recharge_reports->id }},{{$recharge_reports->api_id}})" class="btn btn-primary btn-xs" value="Check"/>
						
						@endif
					</td>

					<td>@if($recharge_reports->recharge_type== 1)
							{{ @$recharge_reports->provider->provider_name }}  
							@else
							{{ @$recharge_reports->api->api_name }} 
						 @endif
					 </td>
					<td style="text-align:center">
					@if(in_array($recharge_reports->status_id,array(1,3,9,18,24)))
					<a target="_blank" href="{{ url('invoice') }}/{{ $recharge_reports->id }}">
					<span class="btn btn-info btn-xs" style="font-size: 14px;">
					<i class="md md-visibility"></i>Receipt</span>
					</a>@endif
					</td> 						 
					<td>{{ $recharge_reports->type }}</td>
					<td>{{ number_format($recharge_reports->opening_balance,2) }}</td>
					<td>{{ $recharge_reports->credit_charge }}</td>
					<td>{{ $recharge_reports->debit_charge }}</td>
					<td>{{ number_format($recharge_reports->tds,3) }}</td>
					<td>{{ number_format($recharge_reports->gst,2) }}</td>
					<td>{{ number_format($recharge_reports->total_balance,2) }}</td>
					<td>{{ $recharge_reports->txn_type }}</td>
					<td>@if($recharge_reports->txnid=="DT")
							{{$recharge_reports->description}}
							@endif
					</td> 
					<td>
						@if(in_array($recharge_reports->status_id,array(1,3,9,18,24)))
							<a onclick="Complain({{ $recharge_reports->id }})" data-toggle="modal" href="#example">Complain</a> 
						@endif</td>
				</tr>
				@endforeach
			</tbody>
		</table>
			 {{$reports->appends(\Input::except('page'))->render() }} 
	</div>
<div class="container" id="doComplan">
		<div id="example" class="modal fade" style="display: none;">
			<div class="modal-dialog">
				<div class="modal-content">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
						<h4 class="modal-title">Complain</h4>
					</div>
					<div class="modal-body">
					  <form id="frmTasks" name="frmTasks" class="form-horizontal" novalidate="">
							{!! csrf_field() !!}
							
							<input type="hidden" id="complainTxnId">
						   
							  <div class="form-group">
								<label for="inputEmail3" class="col-sm-3 control-label">Select</label>
								<div class="col-sm-9">
								   <select id="issueType" class="form-control" name="issueType">
										<option value="AMOUNT NOT CREDIT">AMOUNT NOT CREDIT</option>
										<option value="RECHARGE NOT CREDIT">RECHARGE NOT CREDIT</option>
										<option value="PENDING TXN">PENDING TXN</option>
										<option value="OTHERS">OTHERS</option>
									</select>
								</div>
							</div>
							<div class="form-group">
								<label for="inputEmail3" class="col-sm-3 ">Remark</label>
								
								<div class="col-sm-9">
									<textarea id="complainRemark" class="form-control" name="complainRemark" value="" placeholder="Remarks...."></textarea>
								</div>
							</div>
						</form>			
					</div>   
					<div class="modal-footer" style="border-top:0px ">
						
						<button type="button" class="btn btn-info waves-effect waves-light"
								id="btn" onclick="saveComplain()" style="dispplay:none;color:white;">Submit
						</button>
						<button type="button" class="btn btn-default waves-effect" data-dismiss="modal">Close</button> 
					</div>
				</div>     
			</div>
		</div>
		</div>
	
<script type="text/javascript">
 function saveComplain()
        {
        	 var token = $("input[name=_token]").val();
        
            var complainTxnId = $('#complainTxnId').val();
            var issueType = $("#issueType").val();
            var complainRemark = $("#complainRemark").val();
            var dataString = 'complainTxnId=' + complainTxnId + '&issueType=' + issueType + '&complainRemark=' + complainRemark+'&_token=' + token;
            $.ajax({
                type: "POST",
                url: "{{url('store_complain_req')}}",
                data: dataString,
                datatype: "json",
                success: function (msg) {
					alert(msg.message);
					if(msg.status==1){
						location.reload();
					}
                }
            });

        }

		function Complain(id) {
            var token = $("input[name=_token]").val();
			$('#complainTxnId').val(id);
			$('#complainRemark').val('');
        }
</script>


 <meta name="_token" content="{!! csrf_token() !!}"/>
@endsection