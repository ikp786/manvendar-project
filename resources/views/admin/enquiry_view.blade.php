@extends('admin.layouts.templatetable')
@section('content')
   <script>
        function add_record() {
            $('#btn-save').val("add");
            $('#frmTasks').trigger("reset");
            $("#con-close-modal").modal("toggle");
        }
        //create new task / update existing task
        function savedata() {
           var remark1 = $('#remark').val();
		   var c_id = $('#c_id').val();
		    var s_id = $('#status_id').val();
		    var dataString = 'rem=' + remark1 + '&comp_id=' + c_id + '&s_id=' + s_id;
		   $.ajax({
                type: "get",
                url: "{{url('complain-request-update')}}",
                data: dataString,
                success: function (data) {
                 $("#con-close-modal").modal("hide");
				// location.reload();
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
				//confirm("Are you sure to delete this item");
				
				 location.reload();
                }
            }) 
        }
		
		
        function updateRecord(id) {
            var token = $("input[name=_token]").val();
            var dataString = 'id=' + id;
			var d = $('.re_'+id).val();
			var com_id = $('.c_'+id).val();
			$('#remark').html(d);
		    $('#c_id').val(com_id);
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
	<style>
	.Resolved .label-undefined
	{
	background:green;
	}
	.InProcess .label-undefined
	{
	background:blue;
	}
    .SentToBank .label-undefined
    {
        background:red;
    }
    .Pending .label-undefined
    {
        background:orange;
    }
	</style>
    <div class="row">
        <div class="col-sm-12">
            <div class="col-lg-6 col-md-6">
                <h4 class="page-title">{{ $page_title or 'Provider Detail' }}</h4>
                <ol class="breadcrumb">
                    <li>
                        <a href="{{ url('dashboard') }}">Home</a>
                    </li>
                    <li class="active">
                        {{ $page_title or 'Complain Request Detail' }}
                    </li>
                </ol>
            </div>
            <div class="col-lg-6 col-md-6">
                <div class="pull-right">
                    <button onclick="add_record()" id="demo-add-row" class="btn btn-success"><i
                                class="fa fa-plus m-r-5"></i>Add Record
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!--Basic Columns-->
    <!--===================================================-->


    <!--===================================================-->
    <div class="row">
        <div class="col-sm-12">
            <div class="card-box">
                <h4 class="m-t-0 header-title"><b>{{ $page_title or 'Enquiry Request Detail' }}</b></h4>
                <p class="text-muted font-13">
                    Add or Update Service Provider Detail
                </p>

                <button id="demo-delete-row" class="btn btn-danger" disabled><i
                            class="fa fa-times m-r-5"></i>Delete
                </button>
                <table id="demo-custom-toolbar" data-toggle="table"
                       data-toolbar="#demo-delete-row"
                       data-search="true"
                       data-show-refresh="true"
                       data-show-toggle="true"
                       data-show-columns="true"
                       data-sort-name="id"
                       data-page-list="[5, 10, 20]"
                       data-page-size="50"
                       data-pagination="true" data-show-pagination-switch="true" class="table-bordered ">
                    <thead>
                    <tr>
                        <th>ID</th>
						<th data-field="date" data-sortable="true" data-formatter="dateFormatter">Date</th>
						<th data-field="name">Name</th>
                        <th data-field="email" data-sortable="true">Email</th>
                        <th data-field="location" data-sortable="true">Location</th>
                        <th data-field="mobile" data-sortable="true">Mobile</th>
                        <th data-field="message" data-sortable="true">Message</th>
                        <th data-field="remark" data-sortable="true">Remark</th>
                        <th data-field="status" data-align="center" data-sortable="true"
                            data-formatter="statusFormatter">Status
                        </th>
                        <th data-field="action" data-align="center" data-sortable="true">Action
                        </th>
                    </tr>
                    </thead>

                    <tbody>
                    @foreach($enquiry as $key => $value)
                        <tr>
                            <td>{{ $value->id }}</td>
							<td></td>
							<td>{{ $value->name }}</td>
							<td>{{ $value->email }}</td>
                            <td>{{ $value->location }}</td>
                            <td>{{ $value->mobile }}</td>
                            <td>{{ $value->message }}</td>
							<td>{{ $value->remark }}</td>
                            <td class="{{ $value->status }}">{{ $value->status }}</td>
                            <td><a onclick="updateRecord({{ $value->com_id }})" href="#" class="table-action-btn"><i
                                            class="md md-edit"></i></a>
                                <a href="#" onclick="delete_req({{ $value->com_id }})" class="table-action-btn"><i class="md md-close"></i></a>
								<input type="hidden" id="d_{{ $value->com_id }}" value="{{ $value->com_id }}">
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
    <div id="con-close-modal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
         aria-hidden="true" style="display: none;">
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
                        <input type="hidden" name="user_id" id="user_id">
                        <input type="hidden" name="id" id="id">
						
                        <?php foreach($admin_complain as $key => $value) { ?>
						<input type="hidden" class="re_<?php echo $value->com_id; ?>" value="<?php echo $value->remark; ?>">
						<input type="hidden" class="c_<?php echo $value->com_id; ?>" value="<?php echo $value->com_id; ?>">

						<?php } ?>
                        <div class="form-group">
                            <label for="inputEmail3" class="col-sm-3 control-label">Remark</label>
							<input type="hidden" id="c_id">
                            <div class="col-sm-9">
							
                                <textarea id="remark" rows="5" class="form-control" name="remark" value=""></textarea>
							</div>
                            </div>
								
                        </div>
						
                        <div class="form-group">
                            <label for="inputEmail3" class="col-sm-3 control-label">Status</label>
                            <div class="col-sm-9">
                                <select class="form-control" name="status_id" id="status_id">
                                    <option value="Pending">Pending</option>
                                    <option value="Reject">Reject</option>
									<option value="InProcess">In Process</option>
									<option value="Resolved">Resolved</option>
                                    <option value="SentToBank">Sent to Bank</option>
									
                                </select>
                            </div>
                        </div><br><br><br><br>


                    </form>
                </div>
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
    </div><!-- /.modal -->
    <!-- END wrapper -->
    <meta name="_token" content="{!! csrf_token() !!}"/>
@endsection