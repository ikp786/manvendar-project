@extends('admin.layouts.templatetable')
@section('content')
<script type="text/javascript">
	
        function add_record() {
			$('#companyButton').val("ADD");
			$('#myCompanyBankForm').trigger("reset");
			$("#myModal").modal("toggle");
        } 
        //create new task / update existing task
		function saveRecord() 
		{ 
			var type = "POST";
			var actionType = $("#companyButton").val();
			var task_id = $('#id').val();
			var url = "{{url('aeps/bank-details')}}";
			var my_url = url;
			$.ajaxSetup({
				headers: {
				'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
				}
			})
			if (actionType == "UPDATE") 
            {
				type = "POST"; //for updating existing resource
					my_url += '/' + task_id;
					//$('#myImageForm').attr('method','PUT');
					var uploadfile = new FormData($("#myCompanyBankForm")[0]);
					$.ajax({
						type: type,
						url: my_url,
						data: $('#myCompanyBankForm').serialize(),
						dataType: "json",
						beforeSend: function() {
   
                        },
						success: function (data) {
							if (data.status == 'success') {
								alert(data.message);
								location.reload();
							}
							else if (data.status == 'failure') {
								alert(data.message);
								
							} else {
								var errorString = '<div class="alert alert-danger"><ul>';
								$.each(data.errors, function (key, value) {
									errorString += '<li>' + value + '</li>';
								});
								errorString += '</ul></div>';
								$("#name-error").show();
								$('#name-error').html(errorString); //appending to a <div id="form-errors"></div> inside form
								$('#name-error').focus();
							}
						}
					});
            }
            else
            {  
				var uploadfile = new FormData($("#myCompanyBankForm")[0]);
					$.ajax({
						type: type,
						url: my_url,
						data: uploadfile,
						// data: formData,
						//enctype: 'multipart/form-data',
						processData: false,  // Important!
						contentType: false,
						cache: false,
						dataType: "json",
						beforeSend: function() {
							$("#submitLoaderImg").show()
							$("#companyButton").hide()
							$("#name-error").text('');
                        },
						success: function (data) {
							$("#submitLoaderImg").hide()
							$("#companyButton").show()
							if (data.status == 'success') {
								alert(data.message);
								location.reload();
							}
							else if (data.status == 'failure') {
								alert(data.message);
							} else {
								var errorString = '<div class="alert alert-danger"><ul>';
								$.each(data.errors, function (key, value) {
									errorString += '<li>' + value + '</li>';
								});
								errorString += '</ul></div>';
								$("#name-error").show();
								$('#name-error').html(errorString); //appending to a <div id="form-errors"></div> inside form
								$('#name-error').focus(); //appending to a <div id="form-errors"></div> inside form
								$('#name-error').focus();
							} 
						}
					});
			}
		}
        
		function updateRecord(id) {
            $.ajax({
                type: "get",
                url: "{{url('aeps/bank-details-view')}}/"+id,
               dataType: "json",
                success: function (data) 
				{
                    $('#id').val(data.details.id);
                    $('#bank_name').val(data.details.bank_name);
                    $('#account_number').val(data.details.account_number);
                    $('#accountHolderName').val(data.details.name);
                    $('#ifsc_code').val(data.details.ifsc);
                    $('#branch_name').val(data.details.branch_name);
                    $('#status_id').val(data.details.status_id);
                    $('#companyButton').val("UPDATE");
					$("#myModal").modal("toggle");
                }
            })
        }

		function deleteRow(id)
		{
			$.ajaxSetup({
					headers: {
					'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
					}
				})
			if(confirm('Are you sure you want to Delete this')){
				$.ajax({
					type: "POST",
					url:"{{url('aeps/delete-bank-details')}}",
					data: 'id='+id,
					dataType: "json",
					success: function (data) 
					{
						alert(data.message)
						location.reload();
					}
				});
			}	
		}
        
</script>
<div class="super_container">
	<div class="home">
	</div>
	<div class="search">					
            @include('partials.tab')
		
<br>	
<div class="row">
    <div class="col-sm-12">
        <div class="">
            <table class="table table-bordered">
                <thead style="color: white">
                    <tr>
						<th data-field="id" data-sortable="true">ID </th>
                        <th>Account Holder Name</th>
                        <th>Bank Name</th>
                        <th>Account Number</th>
                        <th>IFSC Code</th>
                        <th>Branch Name</th>
                        <th>Status</th>
                       @if(Auth::user()->role_id == 1)
                        <th>User Name</th>
                        <th>Action</th>	
                       @endif
                    </tr>
                </thead>
                <tbody>
                @foreach($bankDetails as $bankDetail)		
                    <tr style="background-color:white">
                        <td>{{ $bankDetail->id }}</td>
                        <td>{{ $bankDetail->name }}</td>
                        <td>{{ $bankDetail->bank_name }}</td>
                        <td>{{ $bankDetail->account_number }}</td>
                        <td>{{ $bankDetail->ifsc}}</td>
                        <td>{{ $bankDetail->branch_name }}</td>
                        <td>{{ $bankDetail->status->status }}</td>
                        
						@if(Auth::user()->role_id == 1)
						<td>{{$bankDetail->user->name}}({{$bankDetail->user_id}})</td>
						<td>
                        <button type="button" class="btn btn-outline-info btn-sm" onclick="updateRecord({{ $bankDetail->id }})"><i class="fa fa-pencil-square-o" aria-hidden="true"></i>Edit</button>
						
						</td>
						@endif
                        
                    </tr>
                @endforeach
                </tbody>

            </table>

        </div>
    </div>
</div>
</div>
</div>
<div class="modal" id="myModal">
  <div class="modal-dialog">
    <div class="modal-content">

      <!-- Modal Header -->
      <div class="modal-header">
	 
        <h4 class="modal-title">Bank Details</h4>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>

      <!-- Modal body -->
      <div class="modal-body">
	  <div id="name-error" name="name-error"></div>
	   {!! Form::open(array('url' =>'#','id'=>'myCompanyBankForm','files'=>true,)) !!}
	   	<div class="form-group">
			<label>Account Holder Name</label>
			<input type="text" class="form-control" id="accountHolderName" name="name" placeholder = "Account Holder Name" readonly="readonly">
		</div>
		<div class="form-group">
			<label>Bank Name</label>
			<input type="text" class="form-control" id="bank_name" name="bank_name" placeholder = "Bank Name" readonly="readonly">
			<input type="hidden" class="form-control" id="id" name="id">
			
		</div>
		<div class="form-group">
			<label>Account Number</label>
			<input type="text" class="form-control" id="account_number" name="account_number" placeholder = "Account Number" readonly="readonly">
		</div>
		
		<div class="form-group">
			<label>IFSC Code</label>
			<input type="text" class="form-control" id="ifsc_code" name="ifsc" placeholder = "IFSC Code" readonly="readonly">
		</div>
		<div class="form-group">
			<label>Branch Name</label>
			<input type="text" class="form-control" id="branch_name" name="branch_name" placeholder = "Branch Name" readonly="readonly">
		</div>
		<div class="form-group">
			<label>Status</label>
			<select id="status_id" name="status_id" class="form-control" type="text">
				<option value="1">Success</option>
				<option value="3">Pending</option>
				<option value="29">Reject</option>
			</select>
		</div>

		{!! Form::close() !!}
      </div>

      <!-- Modal footer -->
      <div class="modal-footer">
	   <input type="button" id="companyButton" class="btn btn-outline-success btn-sm" onClick="saveRecord()" value="ADD"/>
	    <img src="{{url('/loader/loader.gif')}}" id="submitLoaderImg" class="beneLoaderImg" style="display:none;width:20%;height: 20%"/>
        <button type="button" class="btn btn-outline-danger btn-sm" data-dismiss="modal">Close</button>
      </div>

    </div>
  </div>
</div>

 <meta name="_token" content="{!! csrf_token() !!}"/>
@endsection
