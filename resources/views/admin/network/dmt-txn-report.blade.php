@extends('admin.layouts.templatetable')

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
</script>
 <div class="row col-md-12" >
	<div class="col-md-5">
    <form method="get" action="{{Request::url()}}" class="form-inline" role="form">
        
        <div class="form-group">
            {{ Form::select('SEARCH_TYPE', ['ID' => 'Record Id','TXN_ID' => 'Txn Id','ACC' => 'Account No', 'MOB_NO'=>'Mobile No'], app('request')->input('SEARCH_TYPE'), ['class'=>'form-control']) }}
        </div>
        <div class="form-group">
            <label class="sr-only" for="payid">Number</label>
            <input name="number" type="text" class="form-control" id="exampleInputEmail2" value="{{app('request')->input('number')}}" placeholder="Number">
			<input type="hidden" name="product" value="{{@$product}}"/>
			<input type="hidden" name="status_id" value="{{@$status_id}}"/>
        </div>
        <button type="submit" name="export" class="btn btn-success btn-md"><span class="glyphicon glyphicon-find" ></span><i class="fa fa-search"></i></button>
         <a href="{{Request::url()}}" class="btn btn-primary btn-md"><i class="fa fa-refresh"></i>
        </a>
		</form>
		</div>
		<div class="col-md-6">
		<form method="get" action="{{Request::url()}}" class="form-inline" role="form">
        <div class="form-group">
            <input name="fromdate" class="form-control customDatepicker" type="text" placeholder="From date">
        </div>
        <div class="form-group">
            <input name="todate" class="form-control customDatepicker" type="text" placeholder="To date">
        </div>
        <div class="form-group">
			<input type="hidden" name="product" value="{{@$product}}"/>
			<input type="hidden" name="status_id" value="{{@$status_id}}"/>
            <button name="export" value="EXPORT" type="submit" class="btn btn-success waves-effect waves-light m-l-10 btn-md"><span class="glyphicon glyphicon-find"></span><i class="fa fa-file-excel-o" aria-hidden="true"></i></button>
			
			<button class="btn btn-basic"><i class="fa fa-print"></i></button>
        </div>
    </form></div>
</div>
<br><br>
<div style="">	<!---overflow-y: scroll; max-height:650px-->
	<table id="tableTypeThree" class="table table-bordered table-hover">
            <thead>
                <tr>
				  <th>Select</th>
                   <th>Date/Time</th>
                    <th>ID</th> 
                    <th>User </th> 
                    <th>Counsumer No</th>
                    <th>Bene Name</th>
					<th>Bene Account</th>
                    <th>Ifsc</th>
                    <th>Bank Name</th>
                    <th>Amount</th>
					<th >Type</th>
					<th >Per Name</th>
					<th >Txn Type</th>
					<th>Operator</th>
					<th>Op Id</th>
					<th>Status</th>
					<th>slip</th>
                </tr>
            </thead>
            <tbody>
                @foreach($dmtTxnReport as $report)
      			 <?php $s = $report->created_at;
				$dt = new DateTime($s);?>
                <tr>
					<td><input type="checkbox" name = "checkbox[]"  value="{{@$report->id}}"></td>
					<td>{{ $dt->format('d/m/Y') }}<br>{{ $dt->format('H:i:s') }}</td>
					<td>{{ $report->id }}</td>
					<td>{{ $report->user->name}}({{ $report->user_id }})</td>
					<td>{{ @$report->number }}</td>	
					<td>{{ @$report->beneficiary->name }}</td>
					<td>{{ $report->number }}</td>
					<td>{{ @$report->beneficiary->ifsc }}</td>
					<td>{{ @$report->beneficiary->bank_name }} </td>
					<td>{{ $report->amount }} </td>
					<td>{{ $report->type }} </td>
					<td>{{ @$report->client_id }} </td>
					<td>{{ $report->txn_type }} </td>
					<td>{{ @$report->api->api_name }} </td>
					<td>{{ $report->txnid }}</td>	
					<td>{{ @$report->status->status }}</td>					
                    <td style="text-align:center">
					  @if(in_array($report->status_id,array(1,3,9)))
						<a target="_blank" href="{{ url('invoice') }}/{{ $report->id }}">
							<span class="btn btn-info" style="font-size: 14px;"><i class="md md-visibility"></i>Receipt</span>
						</a>
					@endif
					</td>  
           		 </tr>
           	</tbody>
          @endforeach
    </table>
</div>           		 
 @endsection	
