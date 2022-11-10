@extends('layouts.app')

@section('content')

<script type="text/javascript">
	
function TramocheckStatus(id,apiId)
{
	var token = $("input[name=_token]").val();
	var number = $("#number").val();
	var dataString = 'id=' + id;
	$.ajaxSetup({
	headers: {
		'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
	}
	});
	/* if(apiId== 3){
		//url="{{url('tramo/transaction_status')}}";
	}
	else if(apiId == 5) */
		url = "{{url('check-txn-status')}}"
	
	$.ajax({
		type: "post",
		url: url,
		data: dataString,
		dataType: "json",
		beforeSend:function(){
			$("#checkBtn_"+id).hide()
			$("#checkImg_"+id).show();
		},
		success: function (data) {
			$("#checkBtn_"+id).show()
			$("#checkImg_"+id).hide();
			alert(data.msg);	
		}
	})

}

/*for date->Calender*/

    $(document).ready(function () {
        $.noConflict();
        $('.customDatepicker').datepicker({
              autoclose: true,  
            format: "dd-mm-yyyy"
        });

    }); 
</script>
<div class="row">
<div class="row col-md-12">
	<div class="col-md-6">
		<form action="{{Request('')}}" method="get" class="form-inline">
		   <input type="hidden" name="service_id" value="{{@$serviceId}}"/>
			<div class="form-group">
				<input type="text" name="number" class="form-control" id="number"
					   placeholder="Number" required value="{{app('request')->input('number')}}">
			</div> 
			<button type="submit" name="export" value="numberSearch" class="btn btn-success waves-effect waves-light m-l-10 btn-md"><span class="glyphicon glyphicon-find"></span><i class="fa fa-search"></i></button>
			<a href="{{Request('')}}?service_id={{@$serviceId}}" class="btn btn-primary btn-md"><i class="fa fa-refresh"></i></a>
		</form>
	</div>
	 <div class="col-md-6">
			<form action="{{Request('')}}" method="get" class="form-inline">
			<div class="form-group" >
			   <input name="fromdate" class="form-control customDatepicker" type="text" value="{{app('request')->input('fromdate')}}" placeholder="From date">
				<input type="hidden" name="service_id" value="{{@$serviceId}}" />
			</div>
			<div class="form-group">
				<input name="todate" class="form-control customDatepicker" type="text" value="{{app('request')->input('todate')}}" placeholder="To date">
			</div>
			<div class="form-group">
				<button name="export" value="DATE_SEARCH" type="submit" class="btn btn-success "><span class="glyphicon glyphicon-find"></span><i class="fa fa-search"></i></button>
				<button name="export" value="Recharge Reports" type="submit" class="btn btn-basic "><span class="glyphicon glyphicon-find"></span><i class="fa fa-file-excel-o" aria-hidden="true"></i></button>
			</div>
	   </form>
	</div>
</div>
	<br>				
	
<div class="">	  
    <table id="dtBasicExample" class="table table-hover table-bordered" >
						<thead >
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
						@foreach($reportDetails as $recharge_reports)
                                  <?php $s = $recharge_reports->created_at;
                $dt = new DateTime($s);?>
                    <tr class="odd gradeX" style="background-color:white">
                      <td align="center">{{ $dt->format('d-m-Y') }}<br>{{ $dt->format('H:i:s') }}</td>
                      <td>{{ $recharge_reports->id }}</td>
                      <td>{{ $recharge_reports->user->name }}</td>
                      <td>{{ $recharge_reports->txnid }}</td>
                      <td>{{ @$recharge_reports->provider->provider_name }}</td>
                      <td>{{ $recharge_reports->number }}</td>
                      <td>{{ $recharge_reports->amount }}</td>
                      <td> {{ $recharge_reports->status->status }}</td>
              
                    @if(in_array($recharge_reports->status_id,array(1,3,9,34)))
					    <td>
				             
				            <img src="{{url('loader/loader.gif')}}" id="checkImg_{{$recharge_reports->id}}" class="loaderImg" style="display: none;">
				            @if($recharge_reports->api_id=='27')
                                 <a  href="javascript::voide(0)" disabled class="btn btn-outline-info btn-sm" id="checkBtn_{{$recharge_reports->id}}"> Check</a>
                            @else
                                <a onclick="TramocheckStatus({{ $recharge_reports->id }},{{$recharge_reports->api_id}})" href="javascript::voide(0)" 
                                class="btn btn-outline-info btn-sm"  id="checkBtn_{{$recharge_reports->id}}"> Check</a>
                            @endif
			            </td>
			            <td style="text-align:center">
                            <a target="_blank" href="{{ url('invoice') }}/{{ $recharge_reports->id }}">
                                <span class="btn btn-success btn-xs" style="font-size: 14px;"><i class="md md-visibility"></i>Receipt</span>
                            </a>
					    </td>  
                    @else
    					<td></td>
    					<td></td>
    				@endif
                    </tr>
                @endforeach
					  	</tbody>
					  
				</table>
				 {!! $reportDetails->appends(Request::all())->links() !!}
			</div>


</div>
 <meta name="_token" content="{!! csrf_token() !!}"/>
@endsection