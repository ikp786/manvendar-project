@extends('layouts.app')

@section('content')

<style>

element.style {

	height:170px;
}
</style>

  <script type="text/javascript">
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
	/*for date->Calender*/

    $(document).ready(function () {
        $.noConflict();
        $('.customDatepicker').datepicker({
              autoclose: true,  
            format: "dd-mm-yyyy"
        });

    }); 
  </script>
<div class="super_container">
	<div class="search" >	

		<div class="">

			<div class="">

				<div class="">

					   <div class="panel-body">
	  				 	<div class="row col-md-12">
		                   
                                <form action="{{url('rechrge-report-filter')}}" method="get" class="form-inline" role="form">
                                   
                                    <div class="form-group">
                                      
                                        <input type="text" name="number" class="form-control" id="number"  placeholder="Number" required value="{{app('request')->input('number')}}">
                                    </div> 
                                    <button type="submit" name="export" class="btn btn-success waves-effect waves-light m-l-10 btn-md"><span class="glyphicon glyphicon-find"></span><i class="fa fa-search"></i>
                                            </button>
                                      <a href="{{url('all-recharge-report')}}" class="btn btn-primary  btn-md"><i class="fa fa-refresh"></i>
                                    </a>
                                    <div class="form-group" >
                                       <input name="fromdate" class="form-control customDatepicker" type="text" placeholder="From date">
                                    </div>

                                    <div class="form-group">
                                        <input name="todate" class="form-control customDatepicker" type="text" placeholder="To date">

                                    </div>
                                    <div class="form-group">
                                            <button name="export" value="Recharge Reports" type="submit" class="btn btn-success waves-effect waves-light m-l-10 btn-md"><span class="glyphicon glyphicon-find"></span><i class="fa fa-file-excel-o" aria-hidden="true"></i>
                                            </button>
                                    </div>
                               </form>
                           

                          
                              <!--   <form action="{{url('rechrge-report-export')}}" method="get" class="form-inline">
                                   
                                   
                                    
                                </form> -->
                          
							</div>
                        </div><br>
				
					 
				<div class="ex1" style="overflow-y: scroll; max-height:430px">	
					<table id="dtBasicExample" class="table table-bordered table-striped">

						  <thead>

							<tr>
 							 <th align="center">Date/Time</th>

							 
							  <th>ID </th>
							  <th>User</th>
							  <th>Txn ID </th><th >Provider</th>
                <th>Number</th>
							  <th>Amount</th>
							  <th>Commission</th>
							   <th>Status</th>
							   <th>Action</th>
							   <th>Report</th>
                 
							</tr>

						  </thead>

					  <tbody>

						@foreach($recharge_report as $recharge_reports)
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
    											  @if(in_array(@$recharge_reports->status_id,array(1,3,9)))
    												<a target="_blank" href="{{ url('invoice') }}/{{ $recharge_reports->id }}">
    													<span class="btn btn-success btn-xs" style="font-size: 14px;"><i class="md md-visibility"></i>Receipt</span>
    												</a>
    											@endif
    											</td>  

                         
						</tr>

						@endforeach

					  </tbody>

					  

					</table>
					{!! $recharge_report->appends(Request::all())->links() !!}
					</div>
				</div>
			</div>

		</div>

	</div>
</div>
<script type="text/javascript">

/*$(document).ready(function () {

  $('#dtBasicExample').DataTable();

  $('.dataTables_length').addClass('bs-select');

});*/

</script>



 <meta name="_token" content="{!! csrf_token() !!}"/>

@endsection