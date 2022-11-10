@section('content')
@extends('admin.layouts.templatetable')

   <script>
        function add_record() {
            $('#btn-save').val("add");
            $('#frmTasks').trigger("reset");
            $("#con-close-modal").modal("toggle");
        }
        //create new task / update existing task
        function savedata() {
           var complainId = $('#complainId').val();
		   var status_id = $('#status_id').val();
		   var current_status_remark = $('#current_status_remark').val();
		    var dataString = 'current_status_remark=' + current_status_remark + '&complainId=' + complainId + '&status_id=' + status_id;
		   $.ajax({
                type: "get",
                url: "{{url('complain-request-update')}}",
                data: dataString,
                success: function (data) {
                 if(data.status==1)
					location.reload();
                }
            }) 
        }
		
		function delete_req(id) {
           
		   var d_id = $('#d_'+id).val();
		   
		    var dataString = 'del_id=' + d_id;
		   $.ajax({
                type: "get",
                url: "{{url('complain-request-delete')}}",
                data: dataString,
                success: function (data) {
			     alert(data);
				 location.reload();
                }
            }) 
        }
		
		
        function updateRecord(id) {
            var token = $("input[name=_token]").val();
            var dataString = 'id=' + id;
            $('#complainId').val(id);
            $('#select_remark').change(function(){
               var s_value =  $(this).val();
            $('#current_status_remark').val(s_value);
            });

			// $('#remarks').html(d);
   //          $('#remarks').val(d);
		   
			 $("#con-close-modal").modal("toggle");
            /* $.ajax({
                type: "post",
                url: "{{url('complain-request-view')}}",
                data: dataString,
                success: function (data) {
                   alert(data);
                }
            }) 
 */
        }
    </script>
@include('search.date-search-export-status')	

<div class="">
<table class="table table-bordered dataTable no-footer" id="example2" role="grid" aria-describedby="example2_info">
			<thead>
				<tr>
					  
					<th>Complain Id</th>
					<th>Issue Date</th>
					
					<th>Txn ID</th>
					<th>Raised By</th>
					<th>Isue Type</th>
					
					<th>Remark</th>
					<th>Current Remark</th>
					<th>Status</th>
					<th>Action</th>
				</tr>
				</thead>
				<tbody>
					@foreach($complainDetails as $value)
					<tr class="odd gradeX">
						<td>{{ $value->id }}</td>
						<td>{{ date("d/m/y H:i:s",strtotime($value->created_at)) }}</td>
					   
						<td><a href="{{url('/')}}/recharge-nework?number={{$value->txn_id}}&amount=&fromdate={{date('d-m-Y')}}&todate={{date('d-m-Y')}}&searchOf=&product=&export=SEARCH">{{ $value->txn_id }}</a></td>
						
						 <td>{{ $value->user->name }} ({{ $value->user_id}})</td>
						
						<td>{{$value->issue_type }}</td>
						<td>{{ $value->remark }}</td>
						<td>{{ $value->current_status_remark }}</td>
						<td ><span class="{{ $value->status->status }}">{{ $value->status->status }}</span></td>
						<td>
							@if(!in_array($value->status_id,array(30,29)) && Auth::user()->role_id == 1)
							<a onclick="updateRecord({{ $value->id }})" data-toggle="modal" href="#example">Edit</a>/
							<a href="#" onclick="delete_req({{ $value->id }})" class="table-action-btn"><i class="md md-close"></i>Delete</a>
						  @endif
						</td>
					</tr>
                  @endforeach
             </tbody>
        </table>
            {!! $complainDetails->appends(Request::all())->links() !!}
             
    </div>
    <div class="container" id="doComplan">
    <div id="example" class="modal fade" style="display: none;">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                    <h4 class="modal-title">Complain Update</h4>
                </div>
                <div class="modal-body">
                    <div style="display:none" id="name-error"></div>

                    <form id="frmTasks" name="frmTasks" class="form-horizontal" novalidate="">
                        {!! csrf_field() !!}
                        <input type="hidden" name="complainId" id="complainId" value>
						<div class="form-group">
                            <label for="inputEmail3" class="col-sm-3 control-label">Remark</label>
                            <div class="col-sm-9">
							
                               <select id="select_remark" class="form-control" name="select_remark" value="">
                                <option id="remarks"></option>
                                <option value="a2zsuvidhaa,  Yeh Transaction Hamare taraf se Customer ke Bank account main bhej diya gaya hai. Yeh Transaction Success ya Failed hai.. Iska Javab aana baki hai.">a2zsuvidhaa,  Yeh Transaction Hamare taraf se Customer ke Bank account main bhej diya gaya hai. Yeh Transaction Success ya Failed hai.. Iska Javab aana baki hai.</option>
                                <option value="a2zsuvidhaa,  Yeh Transaction Failed hai. Money Transaction Report main ja kar , Refund button Click kar ke..OTP Dal kar Refund lein.">a2zsuvidhaa,  Yeh Transaction Failed hai. Money Transaction Report main ja kar , Refund button Click kar ke..OTP Dal kar Refund lein.</option>
								 <option value="a2zsuvidhaa, Yeh Transaction Success hai.. Customer ko apna account ka passbook copy mei aaj ka entry kara ke… helpdesk info@a2zsuvidhaa.com ko mail kare">a2zsuvidhaa, Yeh Transaction Success hai.. Customer ko apna account ka passbook copy mei aaj ka entry kara ke… helpdesk "info@a2zsuvidhaa.com" ko mail kare</option>
                               
                                </select>
							</div>
                            </div>
                            <div class="form-group">
                            <label for="inputEmail3" class="col-sm-3 control-label">Update Remark</label>
                            <div class="col-sm-9">
                                <textarea id="current_status_remark" rows="5" class="form-control" name="current_status_remark" value=""></textarea>
                            </div>
                            </div>
								
                        </div>
						
                        <div class="form-group">
                            <label for="inputEmail3" class="col-sm-3 control-label">Status</label>
                            <div class="col-sm-9">
                                <select class="form-control" name="status_id" id="status_id">
                                    <option value="3">Pending</option>
                                    <option value="29">Reject</option>
									<option value="9">In Process</option>
									<option value="30">Resolved</option>
                                    <option value="31">Sent to Bank</option>
									
                                </select>
                            </div>
                        </div><br><br><br><br>


                    </form>
                     <div class="modal-footer">
                    <button onclick="savedata()" type="button" class="btn btn-info waves-effect waves-light"
                            id="btn-save"
                            value="add">Update Now
                    </button>
                    <button type="button" class="btn btn-default waves-effect" data-dismiss="modal">Close</button>
                    <input type="hidden" id="id" name="id" value="0">
                </div>
                </div>
               
            </div>
        </div>
    </div><!-- /.modal -->
    <!-- END wrapper -->
    <meta name="_token" content="{!! csrf_token() !!}"/>
@endsection