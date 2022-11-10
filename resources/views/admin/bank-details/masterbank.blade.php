@extends('admin.layouts.templatetable')
@section('content')

    <script type="text/javascript">
        function add_record() {
			$('#companyButton').val("ADD");
			$('#myCompanyBankForm').trigger("reset");
			$('#name-error').text('');
			$("#myModal").modal("toggle");
        } 
		
        //create new task / update existing task
		function saveRecord() 
		{ 
				var type = "POST";
				var actionType = $("#companyButton").val();
				var task_id = $('#id').val();
				var url = "{{ url('master-bank-detail') }}";
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
							if (data.status == 'failure') {
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
                url: "{{url('master-bank-view')}}/"+id,
               dataType: "json",
                success: function (data) 
				{
                    $('#id').val(data.details.id);
                    $('#bank_name').val(data.details.bank_name);
                    $('#ifsc_code').val(data.details.ifsc);                    
                    $('#bank_sort_name').val(data.details.bank_sort_name);                    
                    $('#bank_code').val(data.details.bank_code);                    
                    $('#account_digit').val(data.details.account_digit);
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
					url:"{{url('delete-masterbank-details')}}",
					data: 'id='+id,
					dataType: "json",
					beforeSend: function() {
					},
					
					success: function (data) 
					{
						alert(data.message)
						location.reload();
					}
				});
			}
		}
        
    </script>

<?php ini_set('memory_limit', '-1'); ?>
<div class="col-sm-12">
    <div class="col-lg-5 col-md-5">
        <h4 class="page-title" style="color:black;">{{'Master Bank Details'}}</h4>
    </div>
    <div class="pull-right">
		 <button type="button" class="btn btn-primary " data-toggle="modal" onClick="add_record()">Add Masterbank Details</button>
    </div>
</div>

<div class="box" style="overflow-y: scroll;max-height: 600px">
	<input id="myInput" type="text" placeholder="Search.." class="pull-right">
	<table class="table table-bordered table-striped" id="example2">
        <thead>
	        <tr>
				<th data-field="id" data-sortable="true">ID </th>
	            <th>Bank Name</th>
	            <th>Bank Sort Name</th>
	            <th>IFSC</th>
	            <th>Bank Code</th>
	            <th>Acc Digits count</th>
			    <th>Status</th>
	            <th>Action</th>		
	        </tr>
        </thead>
        <tbody id="myTable">
        @foreach($bankDetails as $bankDetail)	
            <tr>
                <td>{{ $bankDetail->id }}</td>
                <td>{{ $bankDetail->bank_name }}</td>
                <td>{{ @$bankDetail->bank_sort_name }}</td>
                <td>{{ $bankDetail->ifsc }}</td>
                <td>{{ $bankDetail->bank_code }}</td>
                <td>{{ @$bankDetail->account_digit }}</td>
               <td>{{ ($bankDetail->status_id)? "Active" :"De-Active" }}</td>
                <td>
				<button type="button" class="btn btn-outline-info btn-sm" onclick="updateRecord({{ $bankDetail->id }})"><i class="fa fa-edit " aria-hidden="true"></i></button>
				<button type="button" class="btn btn-outline-danger btn-sm" onclick="deleteRow({{ $bankDetail->id }})"><i class="fa fa-trash " ></i></button>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table> 
    
</div>             
<div class="modal" id="myModal">
  <div class="modal-dialog">
    <div class="modal-content">
      <!-- Modal Header -->
      <div class="modal-header">
        <h4 class="modal-title">Master Bank Details</h4>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>
      <!-- Modal body -->
    <div class="modal-body">
	  	<div id="name-error" name="name-error"></div>
	   	{!! Form::open(array('url' =>'#','id'=>'myCompanyBankForm','files'=>true,)) !!}
		<div class="form-group">
			<input type="text" class="form-control" id="bank_name" name="bank_name" placeholder = "Bank Name">
			<input type="hidden" class="form-control" id="id" name="id">
		</div>		
		<div class="form-group">
			<input type="text" class="form-control" id="ifsc_code" name="ifsc" placeholder = "IFSC Code">
		</div>
		<div class="form-group">
			<input type="text" class="form-control" id="bank_code" name="bank_code" placeholder = "Bank Code">
		</div>
		<div class="form-group">
			<input type="text" class="form-control" id="account_digit" name="account_digit" placeholder = "Enter Number of digits of Bank Account">
		</div>
		<div class="form-group">
			<input type="text" class="form-control" id="bank_sort_name" name="bank_sort_name" placeholder = "Enter Bank Sort Name">
		</div>
		<div class="form-group">
			<select name="status_id" id="status_id" class="form-control">
				<option value="1">Active</option>
				<option value="0">Deactive</option>
			</select>
		</div>
		{!! Form::close() !!}
    </div>

      <!-- Modal footer -->
      <div class="modal-footer">
	   <input type="button" id="companyButton" class="btn btn-outline-success btn-sm" onClick="saveRecord()" value="ADD"/>
	    <img src="{{url('/loader/loader.gif')}}" id="submitLoaderImg" class="beneLoaderImg" style="display:none;width:7%"/>
        <button type="button" class="btn btn-outline-danger btn-sm" data-dismiss="modal">Close</button>
      </div>

    </div>
  </div>
</div>
<meta name="_token" content="{!! csrf_token() !!}"/>
@endsection
