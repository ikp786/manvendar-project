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
 <div class="">
        <div class="col-sm-12">
            <div class="col-lg-6 col-md-6">
                <h4 class="page-title" style="color: black; ">{{'Payment Transfer Report' }}</h4>
                
            </div>
            
        </div>
<div class="row">
	<div class="col-md-12" >
		<div class="">
				  
		   <form method="get" action="{{route('fund-req-report')}}" class="form-inline">
				<div class="form-group">
					<input name="fromdate" class="form-control customDatepicker" type="text" placeholder="From date">
				</div>
				<div class="form-group">
					<input name="todate" class="form-control customDatepicker" type="text" placeholder="To date">
				</div>
				<div class="form-group">
					<button name="export" value="Fund Request Reports" type="submit"
							class="btn btn-success waves-effect waves-light m-l-10 btn-md"><span
								class="glyphicon glyphicon-find"></span><i class="fa fa-file-excel-o" aria-hidden="true"></i>
					</button>
					
				</div>
				
			</form>
					   
		</div>
			
	</div><!-- <div class="col-md-4"></div></div>this class is added-->
	<br>	<br>			

	<div class="col-md-12">
		<div style="overflow-x:auto;">
			<table id="demo-custom-toolbar" data-toggle="table"
					   data-toolbar="#demo-delete-row"
						   data-search="true"
						   data-show-export="true"
						   data-page-list="[10, 20, 30]"
						   data-page-size="80"
						   data-pagination="true" class="table table-bordered"  cellspacing="0"  style="background-color:white">
					<thead>
						<tr>

							<th data-field="date" data-sortable="true"  data-formatter="dateFormatter"> Date</th>
							<th>ID</th>
							<th>Bank Name</th>
							<th>Mode</th>
							<th>Branch Code</th>
							<th>Deposit Date</th>
							<th>Amount</th>
							<th>Customer Remark</th>
							<th>Ref Id</th>
							<th>Status</th>
							<th>Remark</th>
							<th>Action</th>
						</tr>
					</thead>
					<tbody>
				@foreach($paymentTrasferReports as $paymentReport)
					<tr>
						<?php $s = $paymentReport->created_at;
						$dt = new DateTime($s);?>
						<td>{{ $dt->format('d/m/Y') }}<br>{{ $dt->format('H:i:s') }}</td>
						<td>{{ $paymentReport->id }}</td>
						<td>{{ $paymentReport->user_id}}</td>
						<td>{{ ($paymentReport->payment_id) ? @$paymentReport->payment->payment_mode : '' }}</td>
						<td>{{ @$paymentReport->payment->loc_batch_code }}</td>
						<td>{{ @$paymentReport->payment->deposit_date }}</td>
						<td>{{ $paymentReport->amount }}</td>
						<td>{{ @$paymentReport->payment->request_remark }}</td>
						<td>{{ @$paymentReport->payment->bankref }}</td>
						<td>{{ @$paymentReport->status->status }}</td>
						<td>
							
                        </td>
					</tr>
				@endforeach
			</tbody>
		</table>
	</div>
	</div>
                                            {!! $paymentTrasferReports->links() !!}

                                    

                                     
                               
                    	
                       
                    
                </div>
 <meta name="_token" content="{!! csrf_token() !!}"/>              
@endsection