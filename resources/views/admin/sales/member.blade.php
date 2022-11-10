@extends('admin.layouts.templatetable')
@section('content')
 <script src="https://code.jquery.com/jquery-1.9.1.min.js"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
<script src="https://cdn.jsdelivr.net/npm/gasparesganga-jquery-loading-overlay@1.5.4/src/loadingoverlay.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/gasparesganga-jquery-loading-overlay@1.5.4/extras/loadingoverlay_progress/loadingoverlay_progress.min.js"></script>
<script>
 $( document ).ready(function() {
       /*  $('body').on('hidden.bs.modal', '.modal', function () {
            $('.form-input-field').val('');
            $('#mobile').prop('readonly',false);
        }); */
		
		$('input[type="checkbox"]').change(function(){
				this.value = (Number(this.checked));
				
			})
		
		$('input[name="same_shop_address"]').change(function () 
		{
			var address = ($('#address').val()).trim();
			if(address == '')
			{
				alert('Enter Your address.');
				$('#address').focus();
				this.value = 0;
				$('#same_shop_address').prop('checked',false);
				return false;
			}
            if (this.checked) 
			{
				$('#office_address').val(address);
			}
			else
				$('#office_address').val('');

		})
		});
        function add_record() {
			$('#btn-save').show();
			$('.hide-upload-image').show();
            $('#btn-save').val("add");
			$('#div-otp-button').show();//
            $('#otp-box').hide();//
            $('.span-img img').hide()
           // $('#btn-save').prop('disabled',true);//
            $('#btn-save').val("add");
            $('#frmTasks').trigger("reset");
			$('#email').prop('readonly',false);//added by rajat
            $('#name').prop('readonly',false);// added by rajat
            $('#frmTasks').trigger("reset");
            $("#con-close-modal").modal("toggle");
        } 
		
        //create new task / update existing task
		function savedata() 
		{ 
				var pan_card_image = $('input[name=pan_card_image]').val();
                var name =($('#name').val()).trim();
                var amount =($('#amount').val()).trim();
                var mobile = ($('#mobile').val()).trim();
                var email = ($('#email').val()).trim();
                var pin_code = ($('#pin_code').val()).trim();
				var login_user_id = $("#login_user_id").val();
                var company = ($('#company').val()).trim();
				var state_id = $('#state_id').val();
                var address = ($('#address').val()).trim();
                var office_address = ($('#office_address').val()).trim();
                var pan_number = ($('input[name=pan_number]').val()).trim();
                var adhar_card_image =  $('input[name=aadhaar_card_image]').val();
				var aadhaar_img_back =  $('input[name=aadhaar_img_back]').val();
                var cheque_image =  $('input[name=cheque_image]').val();
                /* var form_image =  $('input[name=form_image]').val(); */
                var shop_image =  $('input[name=shop_image]').val();
                // var gst_image =  ($('input[name=gst_image]').val()).trim();
                var profile_picture =  ($('input[name=profile_picture]').val()).trim();
                var adhar_number =  ($('input[name=adhar_number]').val()).trim();
                // var gst_number =  ($('input[name=gst_number]').val()).trim();
                var state = $('#btn-save').val();
				var name_pattern = /^[A-Za-z ]+$/;
				var email_pattern =  /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
				var mobile_pat = /^[6789]\d{9}$/;
				//var pan_numbers_pat = /^([A-Z]){5}([0-9]){4}([A-Z]){1}?$/;
				var pan_card_new = /^([A-Z]){5}([0-9]){4}([A-Z]){1}?$/;
				var adhar_number_pat = /^[0-9]+$/;
				
				var all_res;
				
				if(name =='')
                {
                    alert('Please Enter your full name.');
                    $('#name').val('');
                    $('#name').focus();
                    return false;
                }
				else if(!name.match(name_pattern))
				{
					alert('Only Characters allowed in full name');
					$('#name').focus();
					return false;
				}					
				else if(email =='')
                {
                    alert('Please Enter Email Id.');
                     $('#email').val('');
                    $('#email').focus();
                    return false;
                }
				else if(!email.match(email_pattern))
				{
					alert('Enter valid email id');
					$('#email').focus();
					return false;
				}
                else if(mobile =='')
                {
                    alert('Please Enter mobile number.');
                    $('#mobile').val('');
                    $('#mobile').focus();
                    return false;
                }

				else if(!mobile.match(adhar_number_pat))
				{
					alert('Only Number is allowed');
					$('#mobile').focus();
					return false;
				}
				else if(mobile.length != 10)
				{
					alert("Please Enter 10 Digits Mobile Number");
					$('#mobile').focus();
                    return false;
					
				}else if(!mobile.match(mobile_pat))
				{
					alert('Mobile Number should start with 6,7,8,9');
					$('#mobile').focus();
					return false;
				}
                
                else if(company =='')
                {
                    alert('Please Enter your shop name.');
                    $('#company').val('');
                    $('#company').focus();
                    return false;
                }
				else if(state_id == 0)
                {
                    alert('Please Select Your State.');
                    $('#state_id').focus();
                    return false;
                }
				else if(pin_code == '')
				{
					alert("Please Enter Pin Code");
					$('#pin_code').focus();
                    return false;
					
				}
				else if(!pin_code.match(adhar_number_pat))
				{
					alert("Please Enter valid Pin Code number");
					$('#pin_code').focus();
                    return false;
					
				}
				else if(pin_code.length != 6)
				{
					alert("Please Enter 6 Digits Pin Code");
					$('#pin_code').focus();
                    return false;
					
				}
				else if(address == '')
                {
                    alert('Enter Your address.');
                    $('#address').focus();
                    return false;
                }
				else if(office_address =='')
                {
                    alert('Enter your shop address');
                     $('#office_address').focus();
                    return false;
                }
                else if(pan_number =='')
                {
                    alert('Please Enter Pan Card Number ');
                     $('#pan_number').val('');
                    $('#pan_number').focus();
                    return false;
                }
				else if(pan_number.length != 10 )
                {
                    alert('Please 10 digits Pan Card Number ');
                    $('#pan_number').focus();
                    return false;
                }
				else if(!pan_number.match(pan_card_new))
				{
					alert("Please Enter valid pan card number");
					$('#pan_number').focus();
                    return false;
					
				}
				else if(adhar_number == '')
                {
                    alert('Please Enter Aadhaar Card Number ');
                    $('#adhar_number').val('');
                    $('#adhar_number').focus();
                    return false;
                }
				else if(adhar_number.length != 12 )
                {
                    alert('Please 12 digits aadhaar Card Number ');
                    $('#adhar_number').focus();
                    return false;
                }
				else if(!adhar_number.match(adhar_number_pat))
				{
					alert("Please Enter valid aadhaar card number");
					$('#adhar_number').focus();
                    return false;
					
				}
				else if(amount == '')
				{
					alert("Please Enter amount");
					$('#amount').focus();
                    return false;
					
				}else if(amount <=0)
				{
					alert("Amount should be greater than 0");
					$('#amount').focus();
                    return false;
					
				}
				
				if(state == 'add')
                {
					if(pan_card_image =='')
                    {
                        alert('Please Choose PAN card image');
						return false;
                    }
                    
                }
				if(state == 'update')
				{
				}
               	if( shop_image !='')
                {
					var shop_image_size=(document.getElementById('shop_image').files[0].size/1024);
					if(shop_image_size >500 )
					{
						$("#shop-image-error").text('file size less than 500 KB.');
						return false;
					}
					else
					{
						$("#shop-image-error").text('');
					}
					
				}
				if( profile_picture !='')
				{
					var profile_image_size=(document.getElementById('profile_picture').files[0].size/1024);
					if(profile_image_size >200)
					{
						$("#profile-image-error").text('file size must be 200 kb.');
						return false;
					}
					else
					{
						$("#profile-image-error").text('');
					}
				}
				if( pan_card_image !=''){
					/* var pan_image_size=(document.getElementById('pan_card_image').files[0].size/1024);
					if(pan_image_size >500 )
					{
						$("#pan-image-error").text('file size must be 500 kb.');
						return false;
					}
					else
					{
						$("#pan-image-error").text('');
					} */
				}
                if( adhar_card_image !='')
				{
					var adhar_image_size=(document.getElementById('adhar_card_image').files[0].size/1024);
					if(adhar_image_size >500)
					{
						$("#adhar-image-error").text('file size must be 500 kb.');
						return false;
					}
					else
					{
						$("#adhar-image-error").text('');
					}
				}
				if( aadhaar_img_back !='') // Added
				{
					var aadhaar_img_back=(document.getElementById('aadhaar_img_back').files[0].size/1024);
					if(aadhaar_img_back >500)
					{
						$("#adhar-image-back-error").text('file size must be 300 kb.');
						return false;
					}
					else
					{
						$("#adhar-image-back-error").text('');
					}
				}
				
				
	var type = "POST";
				var task_id = $('#id').val();
				var url = "{{ url('sales/create') }}";
				var my_url = url;
				$.ajaxSetup({
					headers: {
					'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
					}
				})
			if (false) 
            {
				
            }
            else
            {  
				if(confirm("Are you want to sure to Create new member!"))
				{
					var uploadfile = new FormData($("#myImageForm")[0]);
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
                           
                            $.LoadingOverlay("show", {
                             image       : "",
                             fontawesome : "fa fa-spinner fa-spin"
                             });
                        },
						success: function (data) {
							$.LoadingOverlay("hide");
							if (data.status == 'success') {
								alert(data.message);
								location.reload();
							}
							if (data.status == 'failure') {
								alert(data.message);
								location.reload();
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
			}
		}
       
		</script>
<?php ini_set('memory_limit', '-1'); ?>
    <!-- Page-Title -->
    <div class="row" style="margin-top:100px;">
        <div class="col-sm-12">
            <div class="col-lg-6 col-md-6">
                <h4 class="page-title" style="color: white; font-size: 36px;">{{ $page_title or 'MEMBER DETAIL' }}</h4>
                
            </div>
			<div class="pull-right">
                    <button style="display:block" onclick="add_record()" id="demo-add-row" class="btn btn-success"><i
                                class="fa fa-plus m-r-5"></i>Add Member
                    </button>
                </div>
            </div>
    </div><br>

    <!--Basic Columns-->
    <!--===================================================-->


    <!--===================================================-->
    <div class="row">
        <div class="col-sm-12">
            <div class="card-box">
                <table id="demo-custom-toolbar" data-toggle="table"
                       data-toolbar="#demo-delete-row"
                       data-search="true"
                       data-page-list="[20, 10, 20]"
                       data-page-size="10"
                       data-pagination="true" data-show-pagination-switch="true" class="table-bordered ">
                    <thead>
                    <tr>
					  <th data-field="date" data-sortable="true" data-formatter="dateFormatter">Date </th>
					  <th data-field="time" data-sortable="true" data-formatter="dateFormatter">Time </th>
                        <th data-field="id" data-sortable="true">
                            ID
                        </th>
                        <th data-field="name" data-sortable="true">Name</th>
                        
                        <th data-field="mobile" data-sortable="true" data-formatter="dateFormatter">Mobile
                        <th data-field="email" data-sortable="true">Email
                        </th>
                      
                        <th data-field="role_id" data-align="center" data-sortable="true"
                            data-sorter="priceSorter">Member Type
                        </th>
                        <th data-field="parent_name" data-sortable="true"
                        >Parent Name
                        </th>
						 <th data-field="amount" data-sortable="true"
                        >Amount
                        </th>
						 <th data-field="txn_details" data-sortable="true"
                        >Txn Details
                        </th>
                        <th data-field="pin_code" data-align="center" data-sortable="true"
                        >Pin Code
                        </th>
						<th data-field="status" data-align="center" data-sortable="true" data-formatter="statusFormatter">Status </th>
						
                       
                        
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($users as $user)
					<?php $class_name_a=$class_name_m=$class_name='';?>
					<?php $s = $user->created_at;
						$dt = new DateTime($s);?>
                                
                        <tr>
						<td>{{ $dt->format('d-m-Y') }}</td>
                            <td>{{ $dt->format('H:i:s') }}</td>
                            <td>{{ $user->id }}</td>
                            <td>{{ $user->name }}</td>
                           
                            <td>{{ $user->mobile }}</td>
                            <td>{{ $user->email }}</td>
                           
                            <td>{{ $user->role->role_title }}</td>
                            <td>{{ \App\User::find($user->parent_id)->name }} ({{ $user->parent_id }})</td>
                            <td>{{ $user->member->amount }}</td>
                            <td>{{ $user->member->txn_details }}</td>
                            <td>{{ $user->member->pin_code }}</td>
                             <td>{{ $user->status->status }}</td>
                           
                        </tr>
                    @endforeach
                    </tbody>
                </table>
                
            </div>
        </div>
    </div>
	<div id="con-close-modal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
         aria-hidden="true" style="display: none; ">
        <div class="modal-dialog" style="    width: 90%;" 	>
            <div class="modal-content p-0" style="width:100%">
                <ul class="nav nav-tabs navtab-bg nav-justified">
                    <li class="active">
                        <a href="#home-2" data-toggle="tab" aria-expanded="false">
                            <span class="visible-xs"><i class="fa fa-home"></i></span>
                            <span class="hidden-xs">Home</span>
                        </a>
                    </li>
                    <li class="">
                        <a href="#profile-2" data-toggle="tab" aria-expanded="false">
                            <span class="visible-xs"><i class="fa fa-user"></i></span>
                            <span class="hidden-xs">Profile</span>
                        </a>
                    </li>

                </ul>
                <div class="tab-content">
                    <div style="display:none" id="name-error" tabindex='-1'></div>
                    <div class="tab-pane active" id="home-2">
                        <div>
                            <div class="modal-body">

                                <!-- <form>
                                    {!! csrf_field() !!}
                                </form> -->
								{!! Form::open(array('url' =>'#','id'=>'myImageForm','files'=>true,)) !!}
                                <div id="frmTasks" name="frmTasks" class="form-horizontal">
                                    <div class="form-group">
                                       <label for="inputTask" class="col-sm-3 control-label">Full Name<span style="color:red"> *</span></label>
                                        <div class="col-sm-9">
                                            <input type="text" class="form-control has-error" id="name" name="name"
                                                   placeholder="Full Name" value="">
                                        </div>
                                    </div>
									<div class="form-group">
                                        <label for="inputEmail3" class="col-sm-3 control-label">Email<span style="color:red"> *</span></label>
                                        <div class="col-sm-9">
                                            <input type="text" class="form-control" id="email" name="email"
                                                   placeholder="Email Id" value="">
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="inputEmail3" class="col-sm-3 control-label">Mobile Number<span style="color:red"> *</span></label>
                                        <div class="col-sm-9">
                                            <input type="text" class="form-control" id="mobile" name="mobile"
                                                   placeholder="Mobile number" value="" maxlength="10">
                                        </div>
										
                                    </div>
									
									<div class="form-group">
                                        <label for="inputEmail3" class="col-sm-3 control-label"> Shop Name <span style="color:red"> * </span> </label>
                                        <div class="col-sm-4">
                                            {{ Form::text('company', null,['class' => 'form-control form-input-field','id'=>'company','placeholder'=>'Enter Your Shop Name']) }}
                                        </div>
										
										
                                        <span id="shop-image" class="span-img">
                                            <img src=""  /></span>
                                        <div class="col-sm-4 hide-upload-image" id="show_shop">
                                            {{ Form::file('shop_image', array('class' => 'form-control','id' => 'shop_image')) }}
                                            <span id="shop-image-error" class='has-error' style="color:red;"></span>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                            <label for="inputEmail3" class="col-sm-3 control-label">Member Type<span style="color:red"> *</span></label>
                                            <div class="col-sm-9">
                                                {{ Form::select('role_id', $roles, old('role_id'), array('class' => 'form-control','id' => 'role_id')) }}
                                            </div>
                                        </div>
                                   
                                        <div class="form-group">
                                            <label for="inputEmail3" class="col-sm-3 control-label">Parent
                                                Detail</label>
                                            <div class="col-sm-9">
                                                <input type="text" class="form-control" id="parent_id" name="parent_id" value="Admin" readonly/>
                                            </div>
                                        </div>
                                   
									
                                    <div class="form-group">
                                        <label for="inputEmail3" class="col-sm-3 control-label">Address Detail <span style="color:red"> * </span></label>
                                        <div class="col-sm-9">
                                            <textarea id="address" class="form-control" name="address"></textarea>
                                        </div>
                                    </div>
									 <div class="form-group">
                                        <label for="inputEmail3" class="col-sm-3 control-label"> </label>
                                        <div class="col-sm-9">
                                             <label class="checkbox-inline"><input type="checkbox" name="same_shop_address" id="same_shop_address" value="0" >Is Shop Address as Address Detail </label>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="inputEmail3" class="col-sm-3 control-label">Office/Shop Address <span style="color:red"> * </span></label>
                                        <div class="col-sm-9">
                                            <textarea id="office_address" class="form-control"name="office_address"></textarea>
                                        </div>
                                    </div>
									<div class="form-group">
                                            <label for="inputEmail3" class="col-sm-3 control-label">State<span style="color:red"> *</span></label>
                                            <div class="col-sm-9">
                                                {{ Form::select('state_id', $state_list, old('state_id'), array('class' => 'form-control','id' => 'state_id')) }}
                                            </div>
                                        </div>
										<div class="form-group">
										<label for="inputEmail3" class="col-sm-3 control-label">Pin Code <span style="color:red"> * </span></label>
										<div class="col-sm-9">
											<input type="text" class="form-control" id="pin_code" name="pin_code"
                                           placeholder="Pin Code" value="" maxlength="6">
										</div>
									</div>
                                    <div class="form-group">
                                        <label for="inputEmail3" class="col-sm-3 control-label"> Photo</label>
                                        <div class="col-sm-7 hide-upload-image" id="show_profile">
                                            {{ Form::file('profile_picture', array('class' => 'form-control','id' => 'profile_picture')) }}
                                            <span id="profile-image-error" class='has-error' style="color:red;"></span>
                                        </div>
                                        <span id="profile-image" class="span-img">
                                            <img src=""  /></span>
                                    </div>
									<div class="form-group">
                                        <label for="inputEmail3" class="col-sm-3 control-label">Pan Card <span style="color:red"> * </span></label>
                                        
                                        <div class="col-sm-4">
                                            {{ Form::text('pan_number', null,['class' => 'form-control form-input-field','id'=>'pan_number','placeholder'=>'Enter Pan Card Number','maxlength'=>10, "style"=>"text-transform: uppercase;"]) }}
                                        
                                        </div>
                                        <span id="pan-card-image" class="span-img">
                                            <img src=""  /></span>
                                        <div class="col-sm-3 hide-upload-image" id="show_pan">
                                            {{ Form::file('pan_card_image', array('class' => 'form-control','id' => 'pan_card_image')) }}
                                            <span id="pan-image-error" class='has-error' style="color:red;">file size : 500 KB</span>
                                        </div>
                                        
                                    </div>
									<div class="form-group">
                                        <label for="inputEmail3" class="col-sm-3 control-label">Aadhaar Card</label>
                                        <div class="col-sm-7">
                                            {{ Form::text('adhar_number', null,['class' => 'form-control form-input-field','id'=>'adhar_number','placeholder'=>'Enter Aadhaar Card Number','maxlength'=>12]) }}
                                        </div>
                                       
                                    </div>
									<div class="form-group">
                                        <label for="inputEmail3" class="col-sm-3 control-label">Aadhaar Front Img</label>
                                        
                                       <span id="adhar-card-image" class="span-img">
                                            <img src=""  /></span>
                                        <div class="col-sm-7 hide-upload-image" id="show_aadhaar">
                                            {{ Form::file('aadhaar_card_image', array('class' => 'form-control','id' => 'adhar_card_image')) }}
                                            <span id="adhar-image-error" class='has-error' style="color:red;"></span>
                                        </div>
                                    </div>
									<div class="form-group">
                                        <label for="inputEmail3" class="col-sm-3 control-label">Aadhaar Back Img</label>
                                        
                                       <span id="adhar-card-back-image" class="span-img">
                                            <img src=""  /></span>
                                        <div class="col-sm-7 hide-upload-image" id="show_aadhaar_back">
                                            {{ Form::file('aadhaar_img_back', array('class' => 'form-control','id' => 'aadhaar_img_back')) }}
                                            <span id="adhar-image-back-error" class='has-error' style="color:red;"></span>
                                        </div>
                                    </div>
									<div class="form-group">
                                        <label for="inputEmail3" class="col-sm-3 control-label">Cheque</label>
                                        
                                        <span id="check-image" class="span-img">
                                            <img src=""  /></span>
                                        <div class="col-sm-7 hide-upload-image" id="show_cheque">
                                            {{ Form::file('cheque_image', array('class' => 'form-control','id' => 'cheque_image')) }}
                                            <span id="check-image-error" class='has-error' style="color:red;"></span>
                                        </div>
                                    </div>
									<div class="form-group">
                                        <label for="inputEmail3" class="col-sm-3 control-label">Application Form</label>
                                        
                                        <span id="form-image" class="span-img">
                                            <img src=""  /></span>
                                        <div class="col-sm-7 hide-upload-image" id="show_form">
                                            {{ Form::file('form_image', array('class' => 'form-control','id' => 'form_image')) }}
                                            <span id="form-image-error" class='has-error' style="color:red;"></span>
                                        </div>
                                    </div>
									<div class="form-group">
                                        <label for="inputEmail3" class="col-sm-3 control-label">Amount <span style="color:red"> * </span></label>
                                        <div class="col-sm-7">
                                            {{ Form::number('amount', null,['class' => 'form-control form-input-field','id'=>'amount','placeholder'=>'Amount']) }}
                                        </div>
                                       
                                    </div>
									<div class="form-group">
                                        <label for="inputEmail3" class="col-sm-3 control-label">Txn Id:</label>
                                        <div class="col-sm-7">
                                            {{ Form::text('txn_details', null,['class' => 'form-control form-input-field','id'=>'txn_details','placeholder'=>'Txn ID']) }}
                                        </div>
                                       
                                    </div>
									
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane" id="profile-2">
                        <div id="frmTasks" name="frmTasks" class="form-horizontal">
                            <div class="form-group">
                                <label for="inputEmail3" class="col-sm-3 control-label">User Status</label>
                                <div class="col-sm-9">
								@if(Auth::user()->role_id == 1)
								  <select id="status_id" name="status_id" class="form-control">
                                        <option value="0">Disabled</option>
                                        <option value="1">Active</option>
                                    </select>
									@else
                                    <select id="status_id" name="status_id" class="form-control">
                                        <option value="0">Disabled</option>
                                      
                                    </select>
									@endif
                                </div>
                            </div>
							<div class="form-group">
								<label for="inputEmail3" class="col-sm-3 control-label"> Product:</label>
										<label class="checkbox-inline"><input type="checkbox" name="super_res" id="super_res" value="1" checked>Shighra F </label>
										
								
										</div>
							</div>
							
							
                        </div>
						
                    </div>
					<span style='color:red'> * Required Fields.</span> <br>
					<span style='color:red'>Please upload documents with self attested.</span>
                     
                    <div class="modal-footer">
                        <button onclick="savedata()" type="button" class="btn btn-info waves-effect waves-light"
                                id="btn-save"
                                value="add">Save Now
                        </button>
                        <button type="button" class="btn btn-default waves-effect" data-dismiss="modal">Close
                        </button>
                        <input type="hidden" id="my_id" value="{{ Auth::user()->id }}">
                        <input type="hidden" id="id" name="id" value="0">
                    </div>
                
                </div>
				{!! Form::close() !!}

            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
		 <meta name="_token" content="{!! csrf_token() !!}"/>
   
@endsection
