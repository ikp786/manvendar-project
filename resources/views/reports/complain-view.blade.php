@extends('layouts.app')
@section('content')


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
	<table class="table table-bordered " id="example2" role="grid" aria-describedby="example2_info">
			<thead>
				<tr>
					  
					<th>Complain Id</th>
					<th>Issue Date</th>
					<th>Txn ID</th>
					<th>Raised By</th>
					<th>Isue Type</th>
					<th>Customer Remark</th>
					<th>Current Remark</th>
					<th>Status</th>
					<th>Action</th>
				</tr>
			</thead>
			<tbody>
				@foreach($complainDetails as $value)
				
				<tr>
				   <?php $s = $value->created_at;
					$dt = new DateTime($s);?>
					<td>{{ $value->id }}</td>
					<td>{{ $dt->format('d/m/y')}}<br>{{ $dt->format('H:i:s') }}</td>
					<td>{{ $value->txn_id }}</td>
					<td>{{ $value->user->name }} ({{ $value->user_id}})</td>
					<td>{{$value->issue_type }}</td>
					<td>{{ $value->remark }}</td>
					<td>{{ $value->current_status_remark }}</td>
					<td ><span class="{{ $value->status->status }}">{{ $value->status->status }}</span></td>
				</tr>
                  @endforeach
            </tbody>
    </table>
            {!! $complainDetails->appends(Request::all())->links() !!}
             
</div>
    <!-- END wrapper -->
    <meta name="_token" content="{!! csrf_token() !!}"/>
@endsection