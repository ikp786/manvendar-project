@extends('layouts.app')

@section('content')
@include('agent.report.report-type')
<br>

<script>
/*for date->Calender*/

    $(document).ready(function () {
        $.noConflict();
        $('.customDatepicker').datepicker({
              autoclose: true,  
            format: "dd-mm-yyyy"
        });

    }); 
</script>

	 	<div class="col-md-12">
        <form method="get" action="{{url('searchall-all')}}" class="form-inline" role="form">    
			<div class="form-group">
				<label class="sr-only" for="payid">Number</label>
				<input name="search_number" type="text" class="form-control" id="exampleInputEmail2"  value="{{app('request')->input('search_number')}}" placeholder="Number">
			</div>
            <button type="submit" class="btn btn-success waves-effect waves-light m-l-10 btn-md"><span class="glyphicon glyphicon-find"></span><i class="fa fa-search"></i></button>
            <a href="{{url('transaction-report')}}" class="btn btn-primary  btn-md"><i class="fa fa-refresh"></i></a>
			<div class="form-group">
				<input name="fromdate" class="form-control customDatepicker" type="text" placeholder="From date" value="{{ (app('request')->input('fromdate')) ? app('request')->input('fromdate') : date('d-m-Y')}}">
			</div>
			<div class="form-group">
				<input name="todate" class="form-control customDatepicker" type="text" placeholder="To date" value="{{ (app('request')->input('todate')) ? app('request')->input('todate') : date('d-m-Y')}}">
			</div>
			<div class="form-group">
				<button name="SEARCH" value="SEARCH" type="submit" class="btn btn-primary btn-md">
				<i class="fa fa-search"></i></button>
				<button name="export" value="Account Statements" type="submit" class="btn btn-success waves-effect waves-light m-l-10 btn-md"><span class="glyphicon glyphicon-find"></span><i class="fa fa-file-excel-o" aria-hidden="true"></i>
				</button>
			</div>
        </form>
	</div>
<br><br>

	<div class="box" style="overflow-y: scroll;max-height: 600px">
		<table  class="table table-bordered table-striped" id="example2">
			   <thead >
							<tr>
							  <th>ID</th>
							  <th>Txn ID</th>
							   <th align="center">Date/Time</th>
							   <th>Amount</th>
							   <th>Credit/Debit</th>
							   <th>TDS</th>
							   <th>Service Tax</th>
							  <th>Balance</th>
							  <th>Description</th>
							  <th>Txn Type</th>
							  <th>Status</th>
							 <!-- <th>Complain</th>-->
							</tr>
						  </thead>
					  <tbody>
						@foreach($recharge_report as $recharge_reports)
						<tr style="background-color:white">
						 <td>{{ $recharge_reports->id }}</td>
						 <td>{{ $recharge_reports->txnid }}</td>
						 <td align="center">{{ $recharge_reports->created_at }}</td>
						 <td>{{ number_format($recharge_reports->amount,2) }}</td>
						 <td>{{ $recharge_reports->type }}</td>
						 <td>{{ number_format($recharge_reports->tds,3) }}</td>
						 <td>{{ number_format($recharge_reports->gst,2) }}</td>
						 <td>{{ number_format($recharge_reports->total_balance,2) }}</td>
						 <td>@if($recharge_reports->recharge_type== 1)
								{{ @$recharge_reports->provider->provider_name }}  
								@else
								{{ @$recharge_reports->api->api_name }} 
							 @endif
						 </td>
						  
						  <td>{{ $recharge_reports->txn_type }}</td>
						  <td>{{ $recharge_reports->status->status }}</td>
						 <!-- <td><a onclick="Complain({{ $recharge_reports->id }})" class="btn btn-primary">
    						Complain</a>
    						 </td>-->
						</tr>
						
					@endforeach
					  </tbody>
					  
					</table>
					{{ $recharge_report->links() }}
	</div>

	
<script type="text/javascript">
	 function stor_complain(id)
        {
        	 var token = $("input[name=_token]").val();
            var product = $('#myid_'+id).val();
            var issue_type = $('#issue_type_'+id).val();
            var issue_date = $('#date_'+id).val();
            var txn_id = $('#txn_'+id).val();
            var account_number = $('#acno_'+id).val();
            var amount = $('#amount_'+id).val();
            var remark = $('#remark_'+id).val();
            if(issue_type!='' && remark!='')
            {
            var dataString = 'product=' + product + '&issue_type=' + issue_type + '&issue_date=' + issue_date + '&account_number=' + account_number + '&txn_id=' + txn_id + '&amount=' + amount +'&remark=' + remark+'&_token=' + token;
            $.ajax({
                type: "POST",
                url: "{{url('store_complain_req')}}",
                data: dataString,
                datatype: "json",
                success: function (msg) {
                    alert(msg.message);
                   $('#do_comp_'+id).hide();
                  // location.reload();
                }
            });
        }
         else { alert('Please Select Issu Type/Remark Required Field !'); }

        }

function Complain(id) {
            var token = $("input[name=_token]").val();
			var d = $('#do_comp_'+id).val();
            $('#remark').val(d);
            $('#issue_type').val(d);
           /* $('#issue_type').change(function(){
               var s_value =  $(this).val();
            $('#remark').val(s_value);
            });*/
			  $('#do_comp_'+id).modal("toggle");
        }
</script>


 <meta name="_token" content="{!! csrf_token() !!}"/>
@endsection