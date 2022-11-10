@extends('admin.layouts.templatetable')

@section('content')

    <script>
	  function updateRecord(id) {
           
            var dataString = 'record_id=' + id ;
			$.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                }
            })
            $.ajax({
                type: "get",
                url: "{{route('offine-report')}}",
                data: dataString,
                success: function (results) 
				{
					var data = results[0];
                    $('#id').val(data.id);
                    $('#report_number').val(data.id);
                    $('#txnid').val(data.txnid);
                    $('#amount').val(data.amount);
                    $('#number').val(data.number);
                    $('#company_name').val(data.company_name);
                    $('#a_id').val(data.name);
                    $('#status_id').val(data.status_id);
                    $('#btn-save').val("update");
                    $("#con-close-modal").modal("toggle");
                }
            })

        }
        function updateRecard(id) 
		{
			if(confirm('Are you sure to transfer?'))
			{
				var amount = $("#amount_"+id).val();
				var remark = $("#remark").val();
				var record_id = $("#id").val();
				var status_id = $("#status_id").val();
				var txnid = $("#txnid").val();
				var dataString = 'record_id='+record_id +"&status_id="+status_id+"&txnid="+txnid+"&remark="+remark;
				$.ajaxSetup({
						headers: {
							'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
						}
					})
				$.ajax({
					type: "put",
					url: "{{url('offine-report-update')}}/"+record_id,
					data: dataString,
					dataType: "json",
					beforeSend: function() {
						   
						},
					success: function (msg) 
					{
						if(msg.status == 1) {
							 swal("Success", msg.message, "success");
							 setTimeout(
								function() {
								 location.reload();
								}, 2000);
							 
						}else{
							
							swal("Failure", msg.message, "error");
							setTimeout(
							function() {
								 location.reload();
								}, 2000);
						}

					}
				});
			}
			else { }
        }
    </script>
	<script>
    function expand_textarea()
	{
		$('textarea.expand').keyup(function () {
		$(this).animate({ height: "4em", width: "13em" }, 500); 
		});
	}
	
	</script>
	@include('admin.admin-subtab.offlinerecord-type')
   <div class="panel panel-default">
        <div class="panel-body">
            <div class="col-md-12">
                <div class="col-md-4">
                    <h4>OffLine Reports</h4>
                </div>
                 
            </div>

        </div>
    </div>
    <div class="row">
        <div class="col-sm-12">
            <div class="card-box">
                 <div class="table-responsive">
		<table id="mytable"  class="table table-bordered " >

                       
                            <tr>
                                <!--<th data-field="state" data-checkbox="true"></th>-->
                                <th data-field="created_at" data-sortable="true"> Date&Time
                                <th data-field="id" data-sortable="true">
                                    ID
                                </th>
                                <th data-field="name" data-sortable="true">Name</th>
                                <th data-field="number" data-sortable="true">number</th>
                               
                                <th data-field="api_id" data-sortable="true">Product</th>
                                <th data-field="loan_emi_id" data-sortable="true">Company Name
                                </th>
								 
                                <th data-field="amount" data-align="center">Amount </th>
								<th data-field="charge" data-sortable="true">Charge</th>
                                <th data-field="txnid" data-align="center">Txn Id </th>
                                <th data-field="total_balance" data-align="center">Total Balance </th>
                                <th data-field="status_id" data-align="center">Status </th>
                               
                                @if(Auth::user()->role_id == 1)
                                <th data-field="amount" data-align="center" data-sortable="true"
                                    data-sorter="priceSorter">Action
                                </th>
                                @endif
                             
                                
                            </tr>
                            </thead>

                            <tbody>
                            @foreach($offlineReports as $report)
                                <tr>
                                   
                                    <td>{{ $report->created_at }}</td>
                                    <td>{{ $report->id }}</td>
                                    <td>{{ @$report->user->name }}</td>
                                    <td>{{ @$report->number }}</td>
                                    <td>{{ @$report->api->api_name }}</td>
                                    <td>{{ @$report->offlineservices->name }}</td>
                                    <td>{{ $report->amount }}</td>
									<td>{{ @$report->profit }}</td>
                                    
                                    <td>{{ $report->txnid }}</td>
                                   <td>{{ number_format($report->total_balance,2) }}</td>
                                    <td>{{ @$report->status->status }}</td>
                                   
									
                                    @if(Auth::user()->role_id == 1)
										<td><a onclick="updateRecord({{ $report->id }})" href="javascript:void(0)" class="table-action-btn"><span class="glyphicon glyphicon-edit"></span></a>
                                <a href="#" class="table-action-btn"><i class="md md-close"></i></a>
                            </td>
                                    @endif
								
                            
                                  
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="col-md-6">
                    </div>
                </div>
            </div>
        </div>
    </div>
	<div id="con-close-modal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
         aria-hidden="true" style="display: none;">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                    <h4 class="modal-title">Offline Reports</h4>
                </div>
                <div class="modal-body">
                    <div style="display:none" id="name-error"></div>

                    <form id="frmTasks" name="frmTasks" class="form-horizontal" novalidate="">
                        {!! csrf_field() !!}
                        <input type="hidden" name="user_id" id="user_id">
                        <input type="hidden" name="wallet" id="wallet">
                        <input type="hidden" name="id" id="id">
						<div class="form-group">
                            <label for="inputEmail3" class="col-sm-3 control-label">Report Number</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control" id="report_number" name="report_number"
                                       placeholder="report_number" value="" readonly readonly>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="inputEmail3" class="col-sm-3 control-label">Amount</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control" id="amount" name="amount"
                                       placeholder="Amount" value="" readonly readonly>
                            </div>
                        </div> 
						

                          <div class="form-group">
                            <label for="inputName" class="col-sm-3 control-label">Agent Name</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control" id="a_id" name="a_id"
                                       placeholder="Agent ID" value="" readonly>
                            </div>
                        </div>

                       
						<div class="form-group">
                            <label for="inputEmail3" class="col-sm-3 control-label">Company Name</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control" id="company_name" name="company_name"
                                       placeholder="Company Namae" value="" readonly>
                            </div>
                        </div>
						<div class="form-group">
                            <label for="inputEmail3" class="col-sm-3 control-label">A/C No</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control" id="number" name="number"
                                       placeholder="Account Number" value="" readonly>
                            </div>
                        </div>
						 <div class="form-group">
                            <label for="inputEmail3" class="col-sm-3 control-label">Txn Id</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control" id="txnid" name="txnid"
                                       placeholder="Refernece ID" value="" >
                            </div>
                        </div>
							<div class="form-group">
                            <label for="inputEmail3" class="col-sm-3 control-label">Remark</label>
							
								<div class="col-sm-9">
							
                                <!-- <input type="text" class="form-control" id="remark" name="remark"
                                       placeholder="Remark" value=""> -->
                             <input type="text" class="form-control" id="remark" name="remark"
                                       placeholder="Enter Remark" value="" >
                            </div>
							
							 
                        </div>
						<div class="form-group">
                            <label for="inputEmail3" class="col-sm-3 control-label">Status</label>
                            <div class="col-sm-9">
                                <select class="form-control" name="status_id" id="status_id">
                                    <option value="3">Pending</option>
                                    <option value="2">Reject</option>
                                    <option value="1">Approve</option>
                                </select>
                            </div>
                        </div>


                    </form>
                </div>
                <div class="modal-footer">
                <center><span id="imgr" style="display:none;"><img src="images/load2.gif" height="40px" width="40px"></span></center>
                    <button onclick="this.disabled=true;updateRecard()" type="button" class="btn btn-info waves-effect waves-light"
                            id="btn-save"
                            value="add">Send Now
                    </button>
                    <button type="button" class="btn btn-default waves-effect" data-dismiss="modal">Close</button>
                    <input type="hidden" id="id" name="id" value="0">
                </div>
            </div>
        </div>
    </div>
<meta name="_token" content="{!! csrf_token() !!}"/>
@endsection