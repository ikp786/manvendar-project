@extends('admin.layouts.templatetable')

@section('content')
    <script>
        function flush_record() {
            var dataString = 'case=otp_flush';
             $.ajax({
                type: "get",
                url: "{{url('flush_otp')}}",
                data: dataString,
                success: function (data) {
                    alert(data);
                }
            })
        }
        //create new task / update existing task
        function savedata() {
            $("btn-save").prop("disabled", true);
            var url = "{{ url('member') }}";
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                }
            })
            var formData = {
                name: $('#name').val(),
                mobile: $('#mobile').val(),
                email: $('#email').val(),
                pin_code: $('#pin_code').val(),
                company: $('#company').val(),
                agentcode: $('#agentcode').val(),
                address: $('#address').val(),
                pan_number: $('#pan').val(),
                parent_id: $('#parent_id').val(),
                role_id: $('#role_id').val(),
                status_id: $('#status_id').val(),
                scheme_id: $('#scheme_id').val(),
                upscheme: $('#upscheme').val(),
				zone:$("#zone").val(),
				state:$("#state").val(),
				area:$("#area").val(),
				zbm:$("#zbm").val(),
				zsm:$("#zsm").val(),
				asm:$("#asm").val(),
				tsm:$("#tsm").val(),
				kyc: $('#kyc').val(),
                provider_picture: $('#provider_picture').val(),
            }

            //used to determine the http verb to use [add=POST], [update=PUT]
            var state = $('#btn-save').val();
            var type = "POST"; //for creating new resource
            var task_id = $('#id').val();
            var my_url = url;
            if (state == "update") {
                type = "PUT"; //for updating existing resource
                my_url += '/' + task_id;
            }
            $.ajax({
                type: type,
                url: my_url,
                data: formData,
                dataType: "json",
                success: function (data) {
                    if (data.status == 'success') {
						swal('Success',data.message,'success');
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

                    }
                }

            });
        }
        function updateRecord(id) {
            var token = $("input[name=_token]").val();
            var dataString = 'id=' + id + '&_token=' + token;
            $.ajax({
                type: "post",
                url: "{{url('member/view')}}",
                data: dataString,
                dataType: "json",
                success: function (data) {
                    $('#id').val(data.id);
                    $('#name').val(data.name);
                    $('#mobile').val(data.mobile);
                    $('#pan').val(data.pan);
                    $('#email').val(data.email);
                    $('#company').val(data.company);
                    $('#member_type').val(data.membertype);
                    $('#pin_code').val(data.pin_code);
                    $('#address').val(data.address);
                    $('#role_id').val(data.role_id);
                    $('#parent_id').val(data.parent_id);
                    $('#agentcode').val(data.agentcode);
                    $('#api_code').val(data.api_code);
                    $('#scheme_id').val(data.scheme_id);
                    $('#status_id').val(data.status_id);
                    $('#upscheme').val(data.upscheme);
                    $('#btn-save').val("update");
                    $("#con-close-modal").modal("toggle");
                }
            })

        }
        function savedatacomm() {
            var url = "{{ url('memberd') }}";
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                }
            })
            var formData = {
                status_id: $('#status_idnew').val(),
                upscheme: $('#upschemenew').val(),
            }

            //used to determine the http verb to use [add=POST], [update=PUT]
            var state = $('#btn-savecomm').val();
            var type = "PUT"; //for creating new resource
            var task_id = $('#idnew').val();
            var my_url = url;
            my_url += '/' + task_id;
            $.ajax({
                type: type,
                url: my_url,
                data: formData,
                dataType: 'text',
                success: function (data) {
                    $("#con-close-modalcomm").modal("hide");
                    swal("Success", data, "success");
                    if (data == 'Successfully Added') {
                        $("#myModal").modal("hide");
                        location.reload();
                    } else {
                        var obj = $.parseJSON(data);
                        if (obj.success == false) {
                            var obj1 = obj.errors;
                            //alert(obj1["provider_name"]);
                            var html = "";
                            for (var key in obj1)
                                    //alert(obj1[key]);
                            {
                                html += "<li>" + obj1[key] + "</li>";
                            }
                            $("#name-error").show();
                            $("#name-error").html("<div class='alert alert-danger'><ul>" + html + "</ul></div>");
                        } else {
                            var html = "";
                            for (var key in obj) {
                                html += "<li>" + obj[key] + "</li>";
                            }
                            $("#name-error").show();
                            $("#name-error").html("<div class='alert alert-success'><ul>" + html + "</ul></div>");
                        }
                        location.reload();
                    }
                }

            });
        }
        function updateRecordcomm(id) {
            var token = $("input[name=_token]").val();
            var dataString = 'id=' + id + '&_token=' + token;
            $.ajax({
                type: "post",
                url: "{{url('member/view')}}",
                data: dataString,
                success: function (data) {
                    $('#idnew').val(data.id);
                    $('#name').val(data.name);
                    $('#mobile').val(data.mobile);
                    $('#email').val(data.email);
                    $('#member_type').val(data.membertype);
                    $('#role_id').val(data.role_id);
                    $('#api_code').val(data.api_code);
                    $('#agentcode').val(data.agentcode);
                    $('#status_idnew').val(data.status_id);
                    $('#upschemenew').val(data.upscheme);
                    $('#btn-savecomm').val("update");
                    $("#con-close-modalcomm").modal("toggle");
                }
            })

        }
        function savedatapass() {
            var url = "{{ url('memberdp') }}";
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                }
            })
            var formData = {
                password: $('#passwordnew').val(),
            }

            //used to determine the http verb to use [add=POST], [update=PUT]
            var state = $('#btn-savecomm').val();
            var type = "PUT"; //for creating new resource
            var task_id = $('#idnew').val();
            var my_url = url;
            my_url += '/' + task_id;
            $.ajax({
                type: type,
                url: my_url,
                data: formData,
                dataType: 'text',
                success: function (data) {
                    $("#con-close-modalcomm").modal("hide");
                    swal("Success", data, "success");
                    if (data == 'Successfully Added') {
                        $("#myModal").modal("hide");
                        location.reload();
                    } else {
                        var obj = $.parseJSON(data);
                        if (obj.success == false) {
                            var obj1 = obj.errors;
                            //alert(obj1["provider_name"]);
                            var html = "";
                            for (var key in obj1)
                                    //alert(obj1[key]);
                            {
                                html += "<li>" + obj1[key] + "</li>";
                            }
                            $("#name-error").show();
                            $("#name-error").html("<div class='alert alert-danger'><ul>" + html + "</ul></div>");
                        } else {
                            var html = "";
                            for (var key in obj) {
                                html += "<li>" + obj[key] + "</li>";
                            }
                            $("#name-error").show();
                            $("#name-error").html("<div class='alert alert-success'><ul>" + html + "</ul></div>");
                        }
                        location.reload();
                    }
                }

            });
        }
        function updatePassword(id) {
            var token = $("input[name=_token]").val();
            var dataString = 'id=' + id + '&_token=' + token;
            $.ajax({
                type: "post",
                url: "{{url('member/view')}}",
                data: dataString,
                success: function (data) {
                    $('#idnew').val(data.id);
                    $('#name').val(data.name);
                    $('#mobile').val(data.mobile);
                    $('#email').val(data.email);
                    $('#member_type').val(data.membertype);
                    $('#role_id').val(data.role_id);
                    $('#api_code').val(data.api_code);
                    $('#agentcode').val(data.agentcode);
                    $('#status_idnew').val(data.status_id);
                    $('#upschemenew').val(data.upscheme);
                    $('#btn-savecomm').val("update");
                    $("#con-close-modalpass").modal("toggle");
                }
            })

        }
        
        function wallets_save(id,api_id,up_down)
        {
            if(confirm('Are You sure to Update?'))
            {
           
            var provider_code = $('#p_code_'+id).val();
            var dataString = 'id=' + id+'&api_id='+api_id+'&up_down='+up_down;
            $.ajax({
                type: "get",
                url: "{{url('wallets/save')}}",
                data: dataString,
                success: function (data) {
                    alert(data);
                    location.reload();
                    
                }
            });
        }
        else
        {

        }
    }
    function shigh_on_off(id,api_id,on_off)
        {
            if(confirm('Are You sure to Update?'))
            {
           
            var provider_code = $('#p_code_'+id).val();
            var dataString = 'id=' + id+'&api_id='+api_id+'&on_off='+on_off;
            $.ajax({
                type: "get",
                url: "{{url('wallets-on-off')}}",
                data: dataString,
                success: function (data) {
                    alert(data);
                    location.reload();
                    
                }
            });
        }
        else
        {

        }
    }
    
        function down_bank_record()
        {
            var product = $('select[name=select_product]').val();
            var s_comment = $('#saral_cmnt').val();
            var sm_comment = $('#saral_cmnt').val();
            var sh_comment = $('#saral_cmnt').val();
            var dataString = 'product=' + product+'&down=down';
            $.ajax({
                type: "get",
                url: "{{url('bank/save')}}",
                data: dataString,
                success: function (data) {
                    alert(data);
                    
                }
            })

        }
        function up_bank_record()
        {
            var product = $('select[name=select_product]').val();
            var dataString = 'product=' + product +'&up=up';
            $.ajax({
                type: "get",
                url: "{{url('bank/save')}}",
                data: dataString,
                success: function (data) {
                    alert(data);
                    
                }
            })    
        }
        function bnk_down_cmnt()
        {
            var s_comment = $('#saral_cmnt').val();
            var sm_comment = $('#smart_cmnt').val();
            var sh_comment = $('#sharp_cmnt').val();
            var dataString = 's_comment=' + s_comment +'&sm_comment='+sm_comment+'&sh_comment='+sh_comment;
             $.ajax({
                type: "get",
                url: "{{url('bank_cmnt/save')}}",
                data: dataString,
                success: function (data) {
                    alert(data);
                    $('#saral_cmnt').val('');
                    $('#smart_cmnt').val('');
                    $('#sharp_cmnt').val('');

                }
            });
        }
        function txn_onhold_up()
        {
            var hold_api = $('#hold_api').val();
            var dataString = 'hold_api=' + hold_api+'&hold_on=on';
             $.ajax({
                type: "get",
                url: "{{url('/txn_onhold/save')}}",
                data: dataString,
                success: function (data) {
                    alert(data.message);
                    location.reload();
                }
            });
        }
         function txn_onhold_down()
        {
            var hold_api = $('#hold_api').val();
            var dataString = 'hold_api=' + hold_api+'&hold_on=off';;
             $.ajax({
                type: "get",
                url: "{{url('/txn_onhold/save')}}",
                data: dataString,
                success: function (data) {
                    alert(data.message);
                    location.reload();
                }
            });
        }
		function shigh_f_verify(api_type,status)
		{
			
			if(confirm('Are You sure to Update status of '+api_type))
            {
				var dataString = 'api_type=' + api_type+'&status='+status;
				$.ajaxSetup({
					headers: {
					'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
					}
				})
             $.ajax({
                type: "post",
                url: "{{url('/verification_recharge/up_down')}}",
                data: dataString,
                success: function (data) {
                    alert(data.message);
                   location.reload();
                }
            });
			}
			
		}
    </script>

    <!-- Page-Title -->
    <div class="row">
        <div class="col-sm-12">
            <div class="col-lg-4 col-md-6">
                <h1 class="page-title" style="color:white; font-size:36px;">{{ $page_title or 'Wallet Management'}}</h1>
               
            </div>
        
        </div>
       
    </div>
    <div class="row">
        <div class="col-sm-12">
            <div class="card-box"style="    margin-bottom: 68px;"><!--style="    margin-bottom: 68px;" this is add in card box-->
                <table id="demo-custom-toolbar" data-toggle="table"
                       data-toolbar="#demo-delete-row"
                       data-search="true"
                       data-page-list="[20, 10, 20]"
                       data-page-size="10">
                    <thead>
                    <tr style="color:#115798;">
                        <th data-field="id" data-sortable="true">C_ID</th>
                        <th data-field="provider_name" data-sortable="true">Company Name</th>
                        <th data-field="product" data-sortable="true">Wallet</th>
                        <th data-field="saral" data-sortable="true">SHIGHRA-F</th>
                        <th data-field="smart" data-sortable="true">SHIGHRA-P</th>
                        <th data-field="shigh_on" data-sortable="true">SHIGHRA-F</th>
                        <th data-field="paytm_on" data-sortable="true">SHIGHRA-P</th>
						<th data-field="sh-f-ve" data-sortable="true">SHIGHRA-F-Verify</th>
                        <th data-field="sh-p-ve" data-sortable="true">SHIGHRA-P-Verify</th>
                        <th data-field="Rechage" data-sortable="true">RECHARGE</th>
                    </tr>
                    </thead>
                    <tbody style="
    text-align:  center;
">
                    @foreach($wallet_manage as $providers)
                        <tr>
                            <td>{{ $providers->id }}</td>
                            <td>{{'Shighra Pay'}}</td>
                            @if($providers->PaytmActive==1 && $providers->ShighrapayActive==0)
                            <td>{{'SHIGHR-P Is Active'}}</td>
                            @elseif($providers->PaytmActive==0 && $providers->ShighrapayActive==1)
                            <td>{{'SHIGHR-F Is Active'}}</td>
                            @else
                            <td>{{'Both Wallets Are Active'}}</td>
                            @endif
                            @if($providers->ShighrapayActive==0)
                            <td>
                            <a type="submit" href="#" onclick="wallets_save({{ $providers->id }},16,1)" class="btn btn-warning" id="b_save_{{ $providers->id }}">Priority-2</a></td>
                            @else
                            <td><a type="submit" href="#" onclick="wallets_save({{ $providers->id }},16,0)" class="btn btn-success" id="b_save_{{ $providers->id }}">Priority-1</a></td>
                            @endif
                             @if($providers->PaytmActive==0)
                            <td>
                           
                            <a type="submit" href="#" onclick="wallets_save({{ $providers->id }},17,1)" class="btn btn-warning" id="b_save_{{ $providers->id }}">Priority-2</a></td>
                            @else
                            <td><a type="submit" href="#" onclick="wallets_save({{ $providers->id }},17,0)" class="btn btn-success" id="b_save_{{ $providers->id }}">Priority-1</a></td>
                            @endif
                           
                            <td>
                            @if($providers->shighra_on==1)
                                <a type="submit" href="#" onclick="shigh_on_off({{ $providers->id }},16,0)" class="btn btn-success" id="b_save_{{ $providers->id }}">ON</a>
                            @else
                             <a type="submit" href="#" onclick="shigh_on_off({{ $providers->id }},16,1)" class="btn btn-warning" id="b_save_{{ $providers->id }}">OFF</a>
                            @endif
                            </td>
                           
                            <td>
                            @if($providers->paytm_on==1)
                                <a type="submit" href="#" onclick="shigh_on_off({{ $providers->id }},17,0)" class="btn btn-success" id="b_save_{{ $providers->id }}">ON</a>
                            @else
                             <a type="submit" href="#" onclick="shigh_on_off({{ $providers->id }},17,1)" class="btn btn-warning" id="b_save_{{ $providers->id }}">OFF</a>
                            @endif
                            </td>
							<td>
                            @if($providers->fino_verify_on==1)
                                <a type="submit" href="javascript:void(0)" onclick="shigh_f_verify('FINO',0)" class="btn btn-success" title="Click to make Off">ON</a>
                            @else
                             <a type="submit" href="javascript:void(0)" onclick="shigh_f_verify('FINO',1)" class="btn btn-danger"title="Click to make ON" >OFF</a>
                            @endif
                            </td>
							<td>
                            @if($providers->paytm_verify_on==1)
                                <a type="submit" href="javascript:void(0)" onclick="shigh_f_verify('PTM',0)" class="btn btn-success" title="Click to make Off">ON</a>
                            @else
                             <a type="submit" href="javascript:void(0)" onclick="shigh_f_verify('PTM',1)" class="btn btn-danger" title="Click to make ON" >OFF</a>
                            @endif
                            </td>
							<td>
                            @if($providers->recharge==1)
                                <a type="submit" href="javascript:void(0)" onclick="shigh_f_verify('RECHARGE',0)" class="btn btn-success" title="Click to make Off">ON</a>
                            @else
                             <a type="submit" href="javascript:void(0)" onclick="shigh_f_verify('RECHARGE',1)" class="btn btn-danger" title="Click to make ON">OFF</a>
                            @endif
                            </td>
                            
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div id="con-close-modal-one" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
         aria-hidden="true" style="display: none;">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                    <h4 class="modal-title">Member Editor</h4>
                </div>

            </div>
        </div>
    </div><!-- /.modal -->
    <meta name="_token" content="{!! csrf_token() !!}"/>
@endsection