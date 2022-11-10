@extends('layouts.app')
@section('content')

    <script type="text/javascript">
	
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
			$('input[name="mapping_parent_type"]').change(function () 
			{
				$("#parent-lists").html('');
				var mapped_to = $("input[name='mapping_parent_type']:checked").val();
				$.ajaxSetup({
					headers: {
						'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
					}
				})
				var formData ="mapped_to=" + mapped_to;
				$.ajax({
					type: "get",
					url: "{{url('admin/get-parent-details')}}",
					data: formData,
					dataType: 'json',
					success: function (results) 
					{
						if(results.status == 1)
						{
								  var parent_lists = $("<select></select>").attr("id", "parent_list_id").attr("name", "parent_list_id").attr("class", "form-control");

									$.each(results.message, function (id, detail) {
										parent_lists.append("<option value="+detail.id+">" + detail.name + "</option>");
									});
									$("#parent-lists").html(parent_lists);
						}
						else
						$("#parent-lists").html("No Record Found");
					}

				});

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
			$('#email').prop('readonly',false);//added by AVI
            $('#name').prop('readonly',false);// added by AVI
            $('#frmTasks').trigger("reset");
            $("#registrationForm").modal("toggle");
        } 
		
        //create new task / update existing task
		function savedata() 
		{ 
				var pan_card_image = $('input[name=pan_card_image]').val();
                var name =($('#name').val()).trim();
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
				var pan_card_new = /^([A-Za-z]){5}([0-9]){4}([A-Za-z]){1}?$/;
				var email_pattern =  /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
				var mobile_pat = /^[6789]\d{9}$/;
				var pan_numbers_pat = /^[A-Za-z0-9]+$/;
				var adhar_number_pat = /^[0-9]+$/;
				
				var all_res;
				
				if(name =='')
                {
                    alert('Please enter full name.');
                    $('#name').val('');
                    $('#name').focus();
                    return false;
                }
				else if(!name.match(name_pattern))
				{
					alert('Only characters allowed in full name');
					$('#name').focus();
					return false;
				}					
				/* else if(email =='')
                {
                    alert('Please enter email id.');
                     $('#email').val('');
                    $('#email').focus();
                    return false;
                }
				else if(!email.match(email_pattern))
				{
					alert('Please enter valid email id');
					$('#email').focus();
					return false;
				} */
                else if(mobile =='')
                {
                    alert('Please enter mobile number.');
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
					alert("Please enter 10 digits mobile number");
					$('#mobile').focus();
                    return false;
					
				}else if(!mobile.match(mobile_pat))
				{
					alert('Mobile number should start with 6,7,8,9');
					$('#mobile').focus();
					return false;
				}
                
                else if(company =='')
                {
                    alert('Please enter your shop name.');
                    $('#company').val('');
                    $('#company').focus();
                    return false;
                }
				else if(state_id == 0)
                {
                    alert('Please select your state.');
                    $('#state_id').focus();
                    return false;
                }
				else if(pin_code == '')
				{
					alert("Please enter PIN code");
					$('#pin_code').focus();
                    return false;
					
				}
				else if(!pin_code.match(adhar_number_pat))
				{
					alert("Please enter valid pin code number");
					$('#pin_code').focus();
                    return false;
					
				}
				else if(pin_code.length != 6)
				{
					alert("Please enter 6 digits pin code");
					$('#pin_code').focus();
                    return false;
					
				}
				else if(address == '')
                {
                    alert('Enter your address.');
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
                    alert('Please enter pan card number ');
                     $('#pan_number').val('');
                    $('#pan_number').focus();
                    return false;
                }
				else if(pan_number.length != 10 )
                {
                    alert('Please 10 digits PAN card number ');
                    $('#pan_number').focus();
                    return false;
                }
				else if(!pan_number.match(pan_card_new))
				{
					alert("Please enter valid pan card number");
					$('#pan_number').focus();
                    return false;
					
				}
				/* else if(adhar_number == '')
                {
                    alert('Please Enter Aadhaar card number ');
                    $('#adhar_number').val('');
                    $('#adhar_number').focus();
                    return false;
                }
				else if(adhar_number.length != 12 )
                {
                    alert('Please 12 digits Aadhaar Card Number ');
                    $('#adhar_number').focus();
                    return false;
                }
				else if(!adhar_number.match(adhar_number_pat))
				{
					alert("Please Enter valid aadhaar card number");
					$('#adhar_number').focus();
                    return false;
					
				} */
				/* else if(amount == '')
				{
					alert("Please Enter amount");
					$('#amount').focus();
                    return false;
					
				} */
				if(state == 'add')
                {
					/*if(shop_image == '')
                    {
                        alert('Please Choose Shop Image ');
                        return false;
                    }
					else if(profile_picture == '')
                    {
                        alert('Please Choose Your Photo ');
                        return false;
                    }
                    else */if(pan_card_image =='')
                    {
                        alert('Please Choose PAN card image');
						return false;
                    }
                    
                     /*else if(adhar_card_image == '')//updated
                    {
                        alert('Please Choose Adhar card Front Image ');
                        return false;
                    } 
					else if(aadhaar_img_back == '')//added
                    {
                        alert('Please Choose Aadhaar card Back Image ');
                        return false;
                    }*/
					/* else if(cheque_image == '')
                    {
                        alert('Please Choose cheque image ');
                        return false;
                    } */
					
					
                }
				if(state == 'update')
				{
				}
               	if( shop_image !='')
                {
					var shop_image_size=(document.getElementById('shop_image').files[0].size/1024);
					if(shop_image_size >500 )
					{
						$("#shop-image-error").text('file size should be less than 500 KB.');
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
						$("#profile-image-error").text('file size must be less that 200 kb.');
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
						$("#pan-image-error").text('file size must be less than 500 kb.');
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
				/* if( cheque_image !='')
				{
					var cheque_image_size=(document.getElementById('cheque_image').files[0].size/1024);
					if(cheque_image_size >500)
					{
						$("#check-image-error").text('file size less than 500 KB.');
						return false;
					}
					else
					{
						$("#check-image-error").text('');
					}
				} */
				
	var type = "POST";
				var task_id = $('#id').val();
				var url = "{{ url('member') }}";
				var my_url = url;
				$.ajaxSetup({
					headers: {
					'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
					}
				})
			if (state == "update") 
            {
				if(confirm("Are you sure to Approve the member"))
				{
					type = "POST"; //for updating existing resource
					my_url += '/' + task_id;
					//$('#myImageForm').attr('method','PUT');
					var uploadfile = new FormData($("#myImageForm")[0]);
					$.ajax({
						type: type,
						url: my_url,
						data: uploadfile,
						// data: formData,
						enctype: 'multipart/form-data',
						processData: false,  // Important!
						contentType: false,
						cache: false,
						dataType: "json",
						 beforeSend: function() {
                           
                           /*  $.LoadingOverlay("show", {
                             image       : "",
                             fontawesome : "fa fa-spinner fa-spin"
                             }); */
                        },
						success: function (data) {
							//$.LoadingOverlay("hide");
							if (data.status == 'success') {
								alert(data.message);
								location.reload();
							}
							if (data.status == 'failure') {
								alert(data.message);
								//location.reload();
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
            else
            {  
				if(confirm("Are you sure to Create new member!"))
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
                           
                          /*   $.LoadingOverlay("show", {
                             image       : "",
                             fontawesome : "fa fa-spinner fa-spin"
                             }); */
                        },
						success: function (data) {
							//$.LoadingOverlay("hide");
							if (data.status == 'success') {
								alert(data.message);
								location.reload();
							}
							if (data.status == 'failure') {
								alert(data.message);
								//location.reload();
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
		}
        
		function updateRecord(id) {
          
			$('#div-otp-button').hide();
            $('#otp-box').hide();// add by AVI
            $('.span-img img').show();//add by AVI
			$('#email').prop('readonly',false);//added by AVI
            $('#name').prop('readonly',false);// by AVI
            $('#btn-save').prop('disabled',false);// by AVI
            var my_id = $('#my_id').val();
            if(my_id!=1){ $('#btn-save').hide(); }
            var token = $("input[name=_token]").val();
            var dataString = 'id=' + id + '&_token=' + token;
            $.ajax({
                type: "get",
                url: "{{url('member/view')}}",
                data: dataString,
                dataType: "json",
                success: function (data) 
				{
					if(data.doc_verify == 1)
						$('.hide-upload-image').hide();
					else
						$('.hide-upload-image').show();
					if (data.show_profile == 1)
						$('#show_profile').hide();
					if (data.show_shop == 2)
						$('#show_shop').hide();
					if (data.show_pan == 3)
						$('#show_pan').hide();
					if (data.show_aadhaar == 4)
						$('#show_aadhaar').hide();
					if (data.show_cheque == 5)
						$('#show_cheque').hide();
					if (data.show_form == 6)
						$('#show_form').hide();
					if (data.show_back_aadhaar == 7)
						$('#show_aadhaar_back').hide();
                    $('#id').val(data.id);
                    $('#name').val(data.name);
                    $('#mobile').val(data.mobile);
                    $('#pan_number').val(data.pan_number);
                    $('#adhar_number').val(data.adhar_number);
                    $('#email').val(data.email);
                    $('#company').val(data.company);
                    $('#member_type').val(data.membertype);
                    $('#kyc').val(data.kyc),
                    $('#office_address').val(data.office_address),
                    $('#pin_code').val(data.pin_code);
                    $('#address').val(data.address);
                    $('#state_id').val(data.state_id);
                    $('#role_id').val(data.role_id);
                    $('#parent_id').val(data.parent_id);
                    $('#agentcode').val(data.agentcode);
                    $('#yagentcode').val(data.yagentcode);
                    $('#api_code').val(data.api_code);
                    $('#scheme_id').val(data.scheme_id);
                    $('#status_id').val(data.status_id);
                    $('#upscheme').val(data.upscheme);
                    $('#amount').val(data.amount);
					$('#voucher_scheme_id').val(data.voucher_scheme_id);
                    $('#offline_scheme_id').val(data.offline_scheme_id);
                    $('#travel_scheme_id').val(data.travel_scheme_id);
                    $('#billpayment_scheme_id').val(data.billpayment_scheme_id);
                    $('#region').val(data.region);
                    $('#txn_details').val(data.txn_details);
                    
					/* For Image Display*/
					$('.span-img img').attr("height",'50px')
                    $('.span-img img').attr("width",'50px')
					$('#pan-card-image img').attr('src',"{{ url('user-uploaded-files')}}/"+data.id+"/"+data.pan_card_image);
					$('#adhar-card-image img').attr("src","{{url('user-uploaded-files')}}/"+data.id+"/"+data.aadhaar_card_image);
					$('#adhar-card-back-image img').attr("src","{{url('user-uploaded-files')}}/"+data.id+"/"+data.aadhaar_img_back);
                    $('#shop-image img').attr("src","{{url('user-uploaded-files')}}/"+data.id+"/"+data.shop_image);
                    $('#profile-image img').attr("src","{{url('user-uploaded-files')}}/"+data.id+"/"+data.profile_picture);
                    $('#check-image img').attr("src","{{url('user-uploaded-files')}}/"+data.id+"/"+data.cheque_image);
                    $('#form-image img').attr("src","{{url('user-uploaded-files')}}/"+data.id+"/"+data.form_image);
					/* $('#saral_res').val(data.saral_res);
					if(data.saral_res)
						$('#saral_res').prop('checked','checked');
					else
							$('#saral_res').prop('checked',false);
					$('#smart_res').val(data.smart_res);
					if(data.smart_res)
						$('#smart_res').prop('checked','checked');
					else
						$('#smart_res').prop('checked',false);
					 */
					$('#super_res').val(data.super_res);
					if(data.super_res)
						$('#super_res').prop('checked','checked');
					else
							$('#super_res').prop('checked',false);
					$('#super_p').val(data.super_p);
					if(data.super_p)
						$('#super_p').prop('checked','checked');
					else
							$('#super_p').prop('checked',false);
					/* $('#recharge_res').val(data.recharge_res) */
					/* if(data.recharge_res)
							$('#recharge_res').prop('checked','checked');
						else
							$('#recharge_res').prop('checked',false); */
                    $('#btn-save').val("update");
                    $("#registrationForm").modal("toggle");
                }
            })

        }
        
        
       
        function setNewPassword(user_id) {
			
			$("#new_password").val("");
			$("#change_pswd_user").val(user_id);
			$("#changePasswordModel").modal("toggle");

        }
		function updatePassword(user_id) {
			if(confirm('Are you sure want to change password'))
			{
				var new_password = $("#new_password").val();
				var user_id = $("#change_pswd_user").val();
				var token = $("input[name=_token]").val();
				var dataString = 'user_id=' + user_id + '&new_password=' + new_password ;
				$.ajaxSetup({
					headers: {
					'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
					}
				})
				$.ajax({
					type: "put",
					url: "{{ url('memberdp') }}/"+user_id,
					data: dataString,
					success: function (data) {
						alert(data.message);
					   
					}
				})
			}

        }

        
        
        

       
        function changeStatus(id)
        {
			if(confirm('Do you want to make disable the user'))
			{
				var user_id = id;
				$.ajaxSetup({
					headers: {
						'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
					}
				});
				var dataString = 'user_id=' + user_id;
				$.ajax({
					type: "put",
					url: "{{url('admin/make-disable-agent')}}/"+ user_id,
					data: dataString,
					dataType: "json",
					success: function (res) {
					   if(res.status == 1)
					   {
						   $("#chage_status_"+id).attr('class','btn-success');
						   alert("Agent has been disabled");
					   }
					   else
						   alert(res.message);
					}
				});
			}
        }

		function checkValidation()
		{
			return true;
		}
		
		function customSearch()
		{
			var content = $("#customeSearch").val();
			var searchType = $("#searchType").val();
			var urlName = $("#urlName").val();
			$.ajax({
					type: "get",
					url: "{{url('member-custom-serach')}}",
					data: "content="+content +"&searchType="+searchType+"&urlName="+urlName,
					beforeSend:function(){
						$("#totalCount").html('')
					},
					success: function (result) {
					console.log("Hello");
					var content='';
					if(result.totalCount >0){
						data = result.data;
						$("#totalCount").html("TOTAL USER :"+result.totalCount)
						for (var key in data) 
						{
							content += "<tr>"
							content += "<td>"+data[key].created_at+"</td>";
							content += "<td>"+data[key].userPrefix+ ' '+data[key].user_id+"</td>";
							content += "<td>"+data[key].name+"</td>";
							content += "<td>"+data[key].mobile+"</td>";
							content += "<td>"+data[key].user_balance+"</td>";
							content += "<td>"+data[key].role_title+"</td>";
							content += "<td>"+data[key].parentName+' ('+data[key].parentPrefix+' '+data[key].parentId+")</td>";
							@if(Auth::user()->role_id == 1)
								content += "<td> <a href='javascript::void(0)' onclick='getDmtScheme("+data[key].user_id+",\"DMT\")'>DMT 1</a></td>";
								content += "<td> <a id='updatePassword_' onclick='setNewPassword("+data[key].user_id+")' href='javascript:void(0)' class='btn btn-info btn-xs'> <span class='glyphicon glyphicon-send'></span> Change Pswd</a></td>";
							@else
								content += "<td> <a href='javascript::void(0)' onclick='showDmtScheme("+data[key].user_id+",\"DMT\")'>DMT 1</a></td>";
							@endif
							
							content += "<td>"+data[key].status+"</td>";
							content += "<td><a href='{{url("")}}/user-recharge-report/"+data[key].user_id+"'class='table-action-btn'>Report</a></td>";
							@if(Auth::user()->role_id == 1)
							content += '<td> <a onclick="updateRecord('+data[key].user_id+')" href="javascript:void(0)" class="table-action-btn">Update</a></td>';
							@else 
								if(data[key].status == "Active")
								content += '<td> <button onclick="changeStatus('+data[key].user_id+')" href="javascript:void(0)" class="btn btn-success btn-xs" id="chage_status_'+data[key].user_id+'" title="make Agent Disable"><span class="glyphicon glyphicon-ok-circle"></span></button></td>';
								else
								content += '<td> <button class="btn btn-danger btn-xs" id="chage_status_'+data[key].user_id+'" title="Agent Disabled"><span class="glyphicon glyphicon-ban-circle"></span></button></td>';
							
							
							@endif
					
						}
					}else content="<div style='color:red'>No Record Found</div>";
						$('#memberTbody').html(content);
					
					}
				});
			
		}
    </script>

<?php ini_set('memory_limit', '-1'); ?>
				   @if(in_array(Auth::user()->role_id,array(5)))
					  
					  {{--<form method="get" action="{{ url('export_data')}}" onSubmit="return checkValidation();" class="form-inline">
							<div class="col-sm-2">
							 {{ Form::select('searchType', ['NAME' => 'Name', 'MOB' => 'Mobile','ID'=>'ID'], null, ['class'=>'form-control','id'=>'searchType']) }}
							</div>
							<div class="col-sm-2">
							<input type="text" id="customeSearch" value="" class="form-control" onKeyup="customSearch()" placeholder="Search Here">
							</div>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
							<div class="col-sm-2">
								<select name="export_user_id" id="export_user_id" class="form-control js-example-basic-single">
										<option value="0"> -- All Member  --</option>
										 @foreach($export_users as $m_key => $moeny_agent)
										  
											<option value="{{ $m_key }}">{{ strtoupper($moeny_agent) .' (' . $m_key . ')' }}</option>
											
											@endforeach
										 </select>
							</div> 
							
					  </form>--}}
						<div class="pull-right">
						<button style=" float:right;" onclick="add_record()" id="demo-add-row" class="btn btn-primary">Create Sub User</button></div><br><br>
						<!--<a href="{{url('purchageVoucherProduct')}}" class="btn btn-basic"><button ><i
                                class="fa fa-plus m-r-5"></i>Voucher
						</button></a> --> 
					   
				   
				@endif

    <!--Basic Columns-->
    <!--===================================================-->
<style>

td{
    white-space: normal;
}

</style>

    <!--===================================================-->
	<div class="">
	
		<div  class="" style="overflow-y: scroll; max-height:430px">
        
				<h3 id="totalCount" style="text-align: center;font-family: time;"></h3>
                <table id="mytable" class="table table-bordered ">
                    <thead>
                    <tr>
						<th data-field="date" data-sortable="true">Date/Time </th>
						<!--<th data-field="time" data-sortable="true">Time </th>-->
						<th data-field="id" data-sortable="true">ID </th>
                        <th data-field="name" data-sortable="true">Name</th>
                        <th data-field="mobile" data-sortable="true" data-formatter="dateFormatter">Mobile</th>
                        <th data-field="balance" data-align="center" data-sortable="true" data-sorter="priceSorter">Balance </th>
                        <th data-field="amount" data-align="center" data-sortable="true" data-sorter="priceSorter">Member Type </th>
                        <th data-field="parent_name" data-sortable="true">Parent Name</th>
						<th data-field="status" data-align="center" data-sortable="true" data-formatter="statusFormatter">Status </th>
						<th data-field="statusreport" data-align="center" data-sortable="true" data-formatter="statusFormatter">Report </th>
						<th data-field="statusreport" data-align="center" data-sortable="true" data-formatter="statusFormatter">Action </th>
                       
                    </tr>
                    </thead>
                    <tbody id="memberTbody">
					
                    @foreach($users as $user)
					<?php $class_name_a=$class_name_m=$class_name='';
					
						$s = $user->created_at;
						$dt = new DateTime($s);
					?>
					
                                
								
                        <tr>
                            <td>{{ $dt->format('d/m/Y') }}<br>{{ $dt->format('H:i:s') }}</td>
                           
                            <td>{{ $user->prefix}} {{$user->id }} 
								<br><span style="color:darkgoldenrod">{{$user->member->company}}</span></td>
                            <td>{{ $user->name }}</td>
                           <!--  <td>{{ $user->company_id }}</td> -->
                            <td>{{ $user->mobile }}</td>
                            <td>{{ number_format(@$user->balance->user_balance,3) }}</td>
                            <!--<td>{{ number_format($user->balance->user_commission,2) }}</td> -->
                            <td>{{ @$user->role->role_title }}</td>
                            <td>{{ $user->parent->name }} ({{$user->parent->prefix}} {{ $user->parent_id }})</td>
							
                          
                            <!-- <td><a onclick="updateRecordcomm({{ $user->id }})" href="#"
                                   class="table-action-btn">{{ @$user->upscheme->scheme }}</a>
                            </td> -->
                           
                                
                            
                            <td>{{($user->status_id) ? "Active" : "In-active"}}</td>
       
                                <td><a href="{{url('sub-user-report')}}/{{$user->id}}"
                                       class="table-action-btn">Report</a></td>
                           
                            @if(Auth::user()->role_id==1)
                                <td>
                                    <a onclick="updateRecord({{ $user->id }})" href="javascript:void(0)" class="table-action-btn"><i
                                                class="md md-edit"></i>Update</a>
                                </td>

                            @endif
							 @if(Auth::user()->role_id==5 && $user->status_id == 1)
                                <td>
                                  <button class="btn-success btn-xs" id="chage_status_{{$user->id}}"onclick="changeStatus({{ $user->id }})" title="make Agent Disable"> <i class="fa fa-check-circle"></i></button></td>
                                
								@elseif(in_array(Auth::user()->role_id,array(5)) && $user->status_id == 0)
									<td>
									<button class="btn-danger btn-xs" id="chage_status_{{$user->id}}" title="Agent Disabled"> <i class="fa fa-times-circle fa-0x" ></i></button>
									</td>
							@endif                          
                        </tr>
                    @endforeach
                    </tbody>
                </table>
                {{$users->links()}} 
			</div>
		</div>
		<div id="registrationForm" class="modal fade">
			<div class="modal-dialog modal-lg">
				<div class="modal-content">
				<ul class="nav nav-tabs navtab-bg nav-justified">
                    <li class="active">
                        <a href="#home-2" data-toggle="tab" aria-expanded="false" class="btn btn-primary">
                            <span class="visible-xs"><i class="fa fa-home"></i></span>
                            <span class="hidden-xs">Home</span>
                        </a>
                    </li>
                    <li class="">
                        <a href="#profile-2" data-toggle="tab" aria-expanded="false" class="btn btn-primary">
                            <span class="visible-xs"><i class="fa fa-user"></i></span>
                            <span class="hidden-xs">Profile</span>
                        </a>
                    </li>

                </ul>
				 
                <div class="tab-content">
                    <div style="display:none" id="name-error" tabindex="-1" ></div>
                    <div class="tab-pane active" id="home-2" style="background-color:white">
                        <div>
                            <div class="modal-body">

                                <!-- <form>
                                    {!! csrf_field() !!}
                                </form> -->
								{!! Form::open(array('url' =>'#','id'=>'myImageForm','files'=>true,)) !!}
                                 <div id="frmTasks" name="frmTasks" class="form-inline">
								 
									<div class="card-body">
									<div class="form-inline col-md-12">
                                    <div class="form-group col-md-6">
                                       <label for="inputTask" class="control-label col-md-5">Full Name<span style="color:red"> *</span></label>
                                        <div class="col-sm-6 ">
                                            <input type="text" class="form-control has-error" id="name" name="name"
                                                   placeholder="Full Name" value="">
                                        </div>
                                    </div>
									<div class="form-group col-md-6">
                                        <label for="inputEmail3" class="control-label col-md-5">Email</label>
                                        <div class="col-sm-6">
                                            <input type="text" class="form-control" id="email" name="email"
                                                   placeholder="Email Id" value="">
                                        </div>
                                    </div></div>
									<div class="form-inline col-md-12">
	                                    <div class="form-group col-md-6">
	                                        <label for="inputEmail3" class="control-label col-md-5">Mobile Number<span style="color:red"> *</span></label>
	                                        <div class="col-sm-6">
	                                            <input type="text" class="form-control" id="mobile" name="mobile" placeholder="Mobile number" value="" maxlength="10">
	                                        </div>
	                                    </div>
										<div class="form-group col-md-6">
	                                        <label for="inputEmail3" class="control-label col-md-5">Photo<span style="color:red">  </span></label>
	                                        <div class="form-group col-sm-3 hide-upload-image" id="show_profile">
	                                            {{ Form::file('profile_picture', array('class' => 'form-control','id' => 'profile_picture','style'=>'max-width: 255%;')) }}
	                                            <span id="profile-image-error" class="has-error" style="color:red;"> <!-- file size : 200 KB --></span>
												 <span id="profile-image" class="span-img"><img src="" /></span>
	                                        </div>
	                                       
	                                    </div>
                                    </div>
									<div class="form-inline col-md-12">
                                    <div class="form-group col-md-6">
                                        <label for="inputEmail3" class="control-label col-md-5">Address Detail <span style="color:red"> * </span></label>
                                        <div class="col-sm-6">
                                            <textarea id="address" class="form-control" name="address"></textarea>
                                        </div>
                                    </div>
									
                                    <div class="form-group col-md-6">
                                        <label for="inputEmail3" class="control-label col-md-5">Shop Address <span style="color:red"> * </span></label>
                                        <div class="col-sm-6">
                                            <textarea id="office_address" class="form-control"name="office_address"></textarea>
                                        </div>
                                    </div>
									</div>
									<div class="form-inline col-md-12">
										<div class="form-group col-md-6">
                                            <label for="inputEmail3" class="control-label col-md-5">State<span style="color:red"> *</span></label>
                                            <div class="col-sm-3">
                                                {{ Form::select('state_id', $state_list, old('state_id'), array('class' => 'form-control','id' => 'state_id','style'=>'max-width: 255%;')) }}
                                            </div>
                                        </div>
										<div class="form-group col-md-6">
                                            <label for="inputEmail3" class="control-label col-md-5">Region<span style="color:red"> *</span></label>
                                            <div class="col-sm-6">
                                                 {{ Form::select('region', ['1' => 'North', '2' => 'South','3'=>'East','4'=>'West'], null, ['class'=>'form-control','id'=>'region']) }}
                                            </div>
                                        </div>
                                    </div>
									<div class="form-inline col-md-12">
									<div class="form-group col-md-6">
										<label for="inputEmail3" class="control-label col-md-5">Pin Code <span style="color:red"> * </span></label>
										<div class="col-sm-6">
											<input type="text" class="form-control" id="pin_code" name="pin_code"
                                           placeholder="Pin Code" value="" maxlength="6">
										</div>
									</div>
                                   </div>
									<div class="form-inline col-md-12">
									<div class="form-group col-md-6">
                                        <label for="inputEmail3" class="control-label col-md-5"> Shop Name <span style="color:red"> * </span> </label>
                                        <div class="col-sm-6">
                                            {{ Form::text('company', null,['class' => 'form-control form-input-field','id'=>'company','placeholder'=>'Enter Your Shop Name']) }}
                                        </div>
										</div>
										<div class="form-group col-md-6">
										  <label for="inputEmail3" class="control-label col-md-5">Shop Image<span style="color:red">  </span></label>
                                      
                                        <div class="form-group col-sm-3 hide-upload-image" id="show_shop">
                                            {{ Form::file('shop_image', array('class' => 'form-control','id' => 'shop_image','style'=>'max-width: 255%;')) }}
											<span id="shop-image" class="span-img">
                                            <img src=""  /></span>
                                            <span id="shop-image-error" class='has-error' style="color:red;"><!-- file size : 500 KB --></span>
                                        </div>
                                    </div>
                                    </div>
									
									<div class="form-inline col-md-12">
									<div class="form-group col-md-6">
                                        <label for="inputEmail3" class="control-label col-md-5">Pan Card<span style="color:red"> * </span></label>
                                        
                                        <div class="col-sm-4">
                                            {{ Form::text('pan_number', null,['class' => 'form-control form-input-field','id'=>'pan_number','placeholder'=>'Enter Pan Card Number','maxlength'=>10, "style"=>"text-transform: uppercase;"]) }}
                                        
                                        </div></div>
										<div class="form-group col-md-6">
										 <label for="inputEmail3" class="control-label col-md-5">Pan Image<span style="color:red">  </span></label>
                                       
                                        <div class="form-group col-sm-3 hide-upload-image" id="show_pan">
                                            {{ Form::file('pan_card_image', array('class' => 'form-control','id' => 'pan_card_image','style'=>'max-width: 255%;')) }}
											 <span id="pan-card-image" class="span-img">
                                            <img src=""  /></span>
                                            <span id="pan-image-error" class='has-error' style="color:red;"><!-- file size : 500 KB --></span>
                                        </div>
                                        
                                    </div>
									</div>
									<div class="form-group col-md-6">
                                        <label for="inputEmail3" class="control-label col-md-5">Aadhaar Card<span style="color:red"> </span></label>&nbsp;&nbsp;
                                        <div class="col-sm-6">
                                            {{ Form::text('adhar_number', null,['class' => 'form-control form-input-field','id'=>'adhar_number','placeholder'=>'Enter Aadhaar Card Number','maxlength'=>12]) }}
                                        </div>
                                       
                                    </div>
									<div class="form-inline col-md-12">
									<div class="form-group col-md-6">
                                        <label for="inputEmail3" class="control-label col-md-5">Aadhaar Front Img<span style="color:red">  </span></label>
                                        
                                       
                                        <div class="col-sm-3 hide-upload-image" id="show_aadhaar">
                                            {{ Form::file('aadhaar_card_image', array('class' => 'form-control','id' => 'adhar_card_image','style'=>'max-width: 255%;')) }}
											<span id="adhar-card-image" class="span-img">
                                            <img src=""  /></span>
                                            <span id="adhar-image-error" class='has-error' style="color:red;"><!-- File size : 500 KB --></span>
                                        </div>
                                    </div>
									<div class="form-group col-md-6">
                                        <label for="inputEmail3" class="control-label col-md-5">Aadhaar Back Img<span style="color:red">  </span></label>
                                        
                                      
                                        <div class="col-sm-3 hide-upload-image" id="show_aadhaar_back">
                                            {{ Form::file('aadhaar_img_back', array('class' => 'form-control','id' => 'aadhaar_img_back','style'=>'max-width: 255%;')) }}
											 <span id="adhar-card-back-image" class="span-img">
                                            <img src=""  /></span>
                                            <span id="adhar-image-back-error" class='has-error' style="color:red;"><!-- File size : 500 KB --></span>
                                        </div>
                                    </div>
									</div>
									<div class="form-inline col-md-12">
									<div class="form-group col-md-6">
                                        <label for="inputEmail3" class="control-label col-md-5">Cheque <!--<span style="color:red"> * </span>--></label>
                                        
                                       
                                        <div class="col-sm-3 hide-upload-image" id="show_cheque">
                                            {{ Form::file('cheque_image', array('class' => 'form-control','id' => 'cheque_image','style'=>'max-width: 255%;')) }}
											 <span id="check-image" class="span-img">
                                            <img src=""  /></span>
                                            <span id="check-image-error" class='has-error' style="color:red;"><!-- file size : 500 KB --></span>
                                        </div>
                                    </div>
									<div class="form-group col-md-6">
                                        <label for="inputEmail3" class="control-label col-md-5">Application Form<span style="color:red">  </span></label>
                                        
                                       
                                        <div class="col-sm-3 hide-upload-image" id="show_form">
                                            {{ Form::file('form_image', array('class' => 'form-control','id' => 'form_image','style'=>'max-width: 255%;')) }}
											 <span id="form-image" class="span-img">
                                            <img src=""  /></span>
                                            <span id="form-image-error" class='has-error' style="color:red;"><!-- file size : 3 MB --></span>
                                        </div>
                                    </div></div>
									
                                </div>
								</div>
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane" id="profile-2">
                        <div id="frmTasks" name="frmTasks" class="form-horizontal">
						<div class="form-inline col-md-12">
                          <div class="form-group col-md-6">
                                <label for="inputEmail3" class="col-sm-3 control-label">User Status</label>
                                <div class="col-sm-9">
								@if(Auth::user()->role_id == 5)
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
                            </div></div>
							@if(Auth::user()->role_id == 5)
							<div class="form-group">	
								 <label for="inputEmail3" class="col-sm-3 control-label"> Products:</label>
								 <label class="checkbox-inline"><input type="checkbox" name="super_res" id="super_res" value="1" checked>DMT</label> 
								 <label class="checkbox-inline"><input type="checkbox" name="super_p" id="super_p" value="1" checked>DMT 2</label>
							</div>
						</div>
							@endif
							
                    </div>
						
                    </div>
					<span style='color:red'> * Required Fields.</span> <br>
					<span style='color:red'>Please upload documents with self attested.</span>
                     
                    <div class="modal-footer"style="padding: 15px">
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
