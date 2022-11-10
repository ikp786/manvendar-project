@extends('admin.layouts.templatetable')
@section('content')
    <script>
		function updateRecord(user_id) 
		{
			if(confirm('Are you sure to transfer?'))
			{
				$.ajaxSetup({
					headers: {
						'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
					}
				})
				var formData = {
					user_id:user_id,
				}
				$.ajax({
					type: "put",
					url: "update-commission/"+user_id,
					data: formData,
					dataType: "json",
					 beforeSend: function() {
							$("#success_id"+user_id).hide();
							$('#imgr').show();
						},
					success: function (result) {
						if(result.status ==1)
						{
							 $("#success_id"+user_id).text("Success");
						}
						else
						{
							alert(result.message);
							 $("#success_id"+user_id).show();
						}
					}

				});
			}
			else
			{
				$("#success_id"+user_id).hide();
				$("#success_id"+user_id).prop("disabled", false);
				$('#imgr').hide();
			}
		}
		

    </script>

    <div class="row">
        <div class="col-sm-12">
            <div class="col-lg-6 col-md-6">
                <h4 class="page-title" style="color: white; font-size: 36px;">{{ $page_title or 'AGENT REQUEST VIEW' }}</h4>
            </div>
            <div class="col-lg-6 col-md-6">
                <!-- <div class="pull-right">
                    <button onclick="add_record()" id="demo-add-row" class="btn btn-success"><i
                                class="fa fa-plus m-r-5"></i>Add Record
                    </button>
                </div> -->
            </div>
        </div>
    </div><br>

    <!--Basic Columns-->
    <!--===================================================-->


    <!--===================================================-->
    <div class="row">
        <div class="col-sm-12">
            <div class="card-box">
                 <div class="col-lg-3 col-md-2">
                <h4 class="m-t-0 header-title"><b>{{ $page_title or 'Commission Credited Record' }}</b></h4>
                <p class="text-muted font-13">
                    Add or Update Service Provider Detail
                </p>
                </div>
				<div class="col-lg-9 col-md-6">
               
                </div>
                <table id="demo-custom-toolbar" data-toggle="table"
                       data-toolbar="#demo-delete-row"
                       data-search="true"
                       data-sort-name="id"
                       data-page-list="[5, 10, 20]">
                    <thead>
                    <tr>
                        <th data-field="id">ID</th>
                        <th data-field="name">User Name</th>
                        <th data-field="email" data-sortable="true">
                            Email
                        </th>
                        <th data-field="mobile" data-sortable="true">Mobile</th>
                        <th data-field="role" data-sortable="true">Role</th>
                        <th data-field="parent_id" data-sortable="true">Parent Name</th>
                        <th data-field="commitions" data-sortable="true" data-formatter="dateFormatter">Commission
                        </th>
                        <th data-field="amount" data-align="center" data-sortable="true"
                            data-sorter="priceSorter">Balance
                        </th>
						 <th data-field="stats" data-align="center" data-sortable="true"
                            data-sorter="status">Status
                        </th>
						
                        <th data-field="action" data-align="center" data-sortable="true">Action
                        </th>
                        
                    </tr>
                    </thead>

                    <tbody>
                    @foreach($commission_records as $key => $value)
                        <tr>
                            <td>{{ $value->id }}</td>
                            <td>{{ $value->name}}({{$value->id}})</td>
                            <td>{{ $value->email }} 
                            	<input type="hidden" id="u_a_{{ $value->id }}" value="{{ $value->name }}">
                            </td>
                            <td>{{ $value->mobile }}</td>
							<td>{{ $value->role->role_title}}</td>
                            <td>{{ $value->parent_id }}</td>
                            <td>{{ number_format($value->balance->user_commission,2) }}</td>
                            <td>{{ number_format($value->balance->user_balance,2) }}</td>
                            <td>{{ $value->status->status }}</td>
                            <td>@if($value->balance->user_commission > 0)
								<!--<a id="success_id{{$value->id}}" onclick="updateRecord({{ $value->id }})" href="#" class="btn btn-success">Transfer Commission</a>-->
                               
								@endif
                            </td>
                           
                        </tr>
                    @endforeach

                    </tbody>
                </table>
            </div>
        </div>
    </div>


    </div> <!-- container -->

    </div> <!-- content -->

    <footer class="footer">
        2015 © Ubold.
    </footer>

    </div>
    <!-- ============================================================== -->
    <!-- End Right content here -->
    <!-- ============================================================== -->

    </div>
    <div id="commission-model" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
         aria-hidden="true" style="display: none;">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                    <h4 class="modal-title">Update User Balance</h4>
                </div>
                <div class="modal-body">
                    <div style="display:none" id="name-error"></div>

                    <form id="frmTasks" name="frmTasks" class="form-horizontal" novalidate="">
                        {!! csrf_field() !!}
                        <input type="hidden" name="user_id" id="user_id">
                       
                        <div class="form-group">
                            <label for="inputEmail3" class="col-sm-3 control-label">User Id</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control" id="amount" name="amount"
                                       placeholder="Amount" value="" readonly>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="inputEmail3" class="col-sm-3 control-label">Commission Amount</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control" id="ref_id" name="ref_id"
                                       placeholder="Refernece ID" value="">
                            </div>
                        </div>

                        <div class="form-group" style="display: none;">
                            <label for="inputEmail3" class="col-sm-3 control-label">Remaining Amount</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control" id="pay_type" name="remark"
                                       placeholder="Remark" value="">
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
                    <button onclick="this.disabled=true;savedata()" type="button" class="btn btn-info waves-effect waves-light"
                            id="btn-save"
                            value="add">Send Now
                    </button>
                    <button type="button" class="btn btn-default waves-effect" data-dismiss="modal">Close</button>
                    <input type="hidden" id="id" name="id" value="0">
                </div>
            </div>
        </div>
    </div><!-- /.modal -->
    <!-- END wrapper -->

    <div class="modal fade" id="myModal" role="dialog">
			<div class="modal-dialog">
			
			  <!-- Modal content-->
				<div class="modal-content" style="width: 900px;">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal">&times;</button>
							<h4 class="modal-title"><strong>Last 15 Fund Approvel ( <span id='u_a_name'></span>) : <span id ="txn_user_name"></span></strong></h4>
					</div>
					<div class="modal-body">
						<table style="font-size: 14px;" class="table table-responsive">
                                                                <thead>
                                                                <tr>
                                                                    <th>ID</th>
                                                                    <th>Amount</th>
                                                                    <th>Bank Name</th>
                                                                    <th>Bankref</th>
                                                                    <th>Created_at</th>
                                                                </tr>
                                                                </thead>
                                                               <tbody id="response" style="font-family: sans-serif;">

                                                                </tbody>
                                                            </table>
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
					</div>
				</div>
			  
			</div>
		 </div>
		<!-- ===========================================================================================
		<!--			Show last 10 approved requested amount 
		<!-- =========================================================================================== -->

		<div class="modal fade" id="approved_requested_amount" role="dialog">
			<div class="modal-dialog">
			
			  <!-- Modal content-->
				<div class="modal-content" style="width: 900px;">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal">&times;</button>
							<h4 class="modal-title"><strong ><span id ="last-ten-result"></span></strong></h4>
					</div>
					<div class="modal-body">
						<table class="table" id="result_table">
						</table>
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
					</div>
				</div>
			  
			</div>
		 </div>
	<!-- ===========================================================================================
    <!--            Show last five successful transaction of users
    <!-- =========================================================================================== -->
    <meta name="_token" content="{!! csrf_token() !!}"/>
@endsection