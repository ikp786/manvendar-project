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
				var url = "{{ url('holiday') }}";
				var my_url = url;
				$.ajaxSetup({
					headers: {
					'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
					}
				})
			if (actionType == "UPDATE")
            {
				type = "PUT"; //for updating existing resource
					my_url ="{{url('holiday')}}/" + task_id;
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
                url: "{{url('holiday')}}/"+id,
               dataType: "json",
                success: function (data)
				{
                    $('#id').val(data.details.id);
                    $('#name').val(data.details.name);
                    $('#holiday_date').val(data.details.holiday_date);
                    $('#message_first').val(data.details.message_first);
                    $('#message_second').val(data.details.message_second);
                    $('#active_holiday').val(data.details.active_holiday);
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
			$.ajax({
				type: "POST",
				url:"{{url('delete-bank-details')}}",
				data: 'id='+id,
				dataType: "json",
				success: function (data)
				{
					alert(data.message)
					location.reload();
				}
			});
		}
		function holidayStatusChanged(id) {

			var checkId = "checkbox_"+id;
			var isChecked = document.getElementById(checkId).value;
			var a= this.checkbox.checked()+" hello";
        	alert(isChecked);


		}
$(document).ready(function () {
	
	 $.noConflict();
        $('.customDatepicker').datepicker({
              autoclose: true,  
            format: "yyyy-mm-dd"
        });
    }); 
    </script>
<?php ini_set('memory_limit', '-1'); ?>
        <div class="container-fluid">
            <div class="pull-left">
                <h4 class="page-title" style="color:black;">{{@$title}}</h4>
            </div>
			@if(Auth::user()->role_id == 1)
            <div class="pull-right">
				 <button type="button" class="btn btn-outline-primary btn-sm" data-toggle="modal" onClick="add_record()" href="#myModal">Add Holiday</button>
            </div>
			@endif
        </div>
<div class="row">
        <div class="col-sm-12">
            <div class="card-box">
                <table class="table">
                    <thead>
                    <tr>
						<th data-field="id" data-sortable="true">ID </th>
						<th>Name</th>
                        <th>Holiday Date</th>
                        <th>First Message</th>
                        <th>Second Message</th>
                        <th>Status</th>
						<th>Holiday Registered Date</th>
						<th>Edit</th>

                    </tr>
                    </thead>
                    <tbody>
                    @foreach($holidayList as $holiday)
                        <tr>
							<td>{{$holiday->id}}</td>
                            <td>{{ $holiday->name }}</td>
                            <td>{{ $holiday->holiday_date }}</td>
                            <td>{{ $holiday->message_first }}</td>
                            <td>{{ $holiday->message_second }}</td>
                            <td>{{ ($holiday->active_holiday) ? "Active" :"De-Active" }}</td>
                            <td>{{ $holiday->created_at }}</td>
						
                            <td>
							@if(Auth::user()->role_id == 1)
							<button type="button" class="btn btn-outline-info btn-sm"
								onclick="updateRecord({{ $holiday->id }})" href="#myModal" data-toggle="modal"><i class="fa fa-edit " aria-hidden="true"></i></button>
						
							@endif
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table> 
            </div>
        </div>
    </div>
<div class="modal" id="myModal">
  <div class="modal-dialog">
    <div class="modal-content">

      <!-- Modal Header -->
      <div class="modal-header">
	 
        <h4 class="modal-title">{{@$title}}</h4>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>

      <!-- Modal body -->
      <div class="modal-body">
	  <div id="name-error" name="name-error"></div>
	   {!! Form::open(array('url' =>'#','id'=>'myCompanyBankForm','files'=>true,)) !!}

		<div class="form-group">
			<input type="text" class="form-control" id="name" name="name" placeholder = "Holiday Name">
			<input type="hidden" class="form-control" id="id" name="id" >
		</div>
		<div class="form-group">
			<input type="text" class="form-control customDatepicker" id="holiday_date" name="holiday_date" placeholder = "Holiday Date" value="{{ (app('request')->input('holiday_date')) ? app('request')->input('holiday_date') : date('Y-m-d')}}">
		</div><div class="form-group">
			<input type="text" class="form-control" id="message_first" name="message_first" placeholder = "First Message">
		</div><div class="form-group">
			<input type="text" class="form-control" id="message_second" name="message_second" placeholder = "Second Message">
		</div>
		  <div class="form-group">
		<select name="active_holiday" id="active_holiday" class="form-control">
			<option value="0">Deactive</option>
			<option value="1">Active</option>
		</select>

		{!! Form::close() !!}
      </div>

      <!-- Modal footer -->
      <div class="modal-footer">
	   <input type="button" id="companyButton" class="btn btn-outline-success btn-sm" onClick="saveRecord()" value="ADD"/>
	    <img src="{{url('/loader/loader.gif')}}" id="submitLoaderImg" class="beneLoaderImg" style="display:none;width:5% height:5%"/>
        <button type="button" class="btn btn-outline-danger btn-sm" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>

    <meta name="_token" content="{!! csrf_token() !!}"/>
@endsection
