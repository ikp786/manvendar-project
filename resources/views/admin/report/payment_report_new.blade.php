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
            var url = "payment-report-view";
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                }
            })
            var formData = {
                status_id: $('#status_id').val(),
                amount: $('#amount').val(),
                rer_id: $('#ref_id').val(),
                user_id: $('#user_id').val(),
				remark: $('#remark').val(),
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
                dataType: 'text',
                success: function (data) {
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
                }
            });
        }
		function update_re()
		{
			var p_update_id = $('#p_id').val();
			var payment_remark = $('#remark').val();
			var dataString = 'id=' + p_update_id + '&remark_update=' + payment_remark;
			 $.ajax({
                type:"get",
                url:"{{url('payment-report-update')}}",
				data: dataString,
                success:function (data) {
                    swal("Success",data,"success");
					$("#con-close-modal").modal("hide");
                    location.reload();
                }
            }) 
		}
        function updateRecord(id) {
            var token = $("input[name=_token]").val();
			var p_pay = $("#p_pay_"+id).val();
			var p_remark = $('#p_remark_'+id).val();
			var payment_edit_id = $('#p_id_'+id).val();
			$('#p_id').val(payment_edit_id);
			$('#p_id').val(id);
			$('#amount').val(p_pay);
			$('#remark').val('DTR'+'-'+p_remark);
            var dataString = 'id=' + id + '&_token=' + token;
             $("#con-close-modal").modal("toggle");
			/* $.ajax({
                type:"post",
                url:"{{url('payment-report-view/p_view')}}",
                data:dataString,
                success:function (data) {
                    $('#id').val(data.id);
                    $('#ref_id').val(data.bankref);
                    $('#amount').val(data.amount);
                    $('#user_id').val(data.user_id);
                    $('#btn-save').val("update");
                   
                }
            }) */
        }
    </script>
 @include('admin.admin-subtab.payment-report-type')
 
    <div class="row">
        <div class="col-sm-12">
            @include('search.only-search-with-export')
            <div class="table-responsive" >
				<table id="example2"  class="table table-bordered " >
                    <thead>
                    <tr>
					 <th data-field="date" data-sortable="true">&nbsp&nbsp&nbsp&nbspDate/Time &nbsp&nbsp&nbsp&nbsp&nbsp&nbsp </th>
					  <th>Request Date/time</th>
					 <th>Updated Date/time</th>
						<!--<th data-field="time" data-sortable="true" data-formatter="dateFormatter">Time </th>-->
                        <th>Order ID</th>
                        <th>Wallet</th>
                        <th>User</th>
                        <th>Transfer To/From</th>
                        <th>Firm Name</th>
                        <th>Ref Id</th>
                        <th>Description</th>
                        <th>Bank Ref</th>
                        <th>Agent Remark</th>
                        <th>Opening Bal</th>
                        <th>Credit Amount</th>
                        <th>Closing Bal</th>
                       <!-- <th data-field="upscheme" data-align="center" data-sortable="true"
                            data-sorter="priceSorter">Req Amount
                        </th> -->
                        <!-- <th data-field="profit" data-align="center" data-sortable="true"
                            data-sorter="priceSorter">Profit Amount
                        </th> -->
                        <th data-field="bankcharge" data-align="center" data-sortable="true" data-sorter="priceSorter">Bank Charge
                        </th>
						 <th data-field="remark" data-align="center" data-sortable="true">Remark
                        </th>
                        <th data-field="status" data-align="center" data-sortable="true" data-formatter="statusFormatter">Status
                        </th>
                    </tr>
                    </thead>
                    <tbody>
					 <?php $count=$totalAmount=0;?>
                    @foreach($reports as $key => $value)
					<?php $s = @$value->created_at;
						$dt = new DateTime($s);
						$u = @$value->payment->updated_at;
						$du = new DateTime($u);
						$r=@$value->payment->created_at;
						$dr = new DateTime($r);
						?>
                        <tr>
							<td>{{$dt->format('d/m/Y')}}<br>{{$dt->format('H:i:s')}}</td>
							@if($value->payment_id =='')
								<td></td>
								<td></td>
								@else
							<td>{{$dr->format('d/m/Y')}}<br>{{$dr->format('H:i:s')}}</td>
							<td>{{$du->format('d/m/Y')}}<br>{{$du->format('H:i:s')}}</td>  @endif                      
							<td>{{$value->id}}</td>
                            <td>{{($value->recharge_type == 1) ? 'Recharge' : 'Money' }}</td>
                            <td>{{$value->user->name}}({{ $value->user->id }})</td>
                            <td>@if(is_numeric($value->credit_by))
								{{@$value->creditBy->name}} ({{@$value->creditBy->prefix}}-{{@$value->creditBy->id}})<br>
								@else
									{{@$value->credit_by}}
								@endif 
							</td>                          
                            <td>@if(is_numeric($value->credit_by))
							{{@$value->creditBy->member->company}}@endif</td>
                            <td>{{$value->txnid}}</td>
                            <td>{{$value->description}}</td>
                            <td>{{@$value->payment->bankref}}</td>
                            <td>{{@$value->payment->request_remark}}</td>
                            <td>{{number_format($value->opening_balance,2)}}</td>
                            <td>{{number_format($value->amount,2)}}
                            <td>{{number_format($value->total_balance,2)}}
							<input type="hidden" name="p_amount" id="p_pay_{{ $value->id }}" value="{{ $value->amount }}">
						    <input type="hidden" name="p_id" id="p_id_{{ $value->id }}" value="{{ $value->id }}">
							</td>
                            <!--<td>
                            @if($value->bank_charge!=0)
								{{ $value->amount + $value->profit+ $value->bank_charge}}
                            @else
                            {{ $value->amount + $value->profit }}
                            @endif
                            </td> -->
                            <!-- <td>{{ $value->profit }}</td> -->
                            <td>{{ $value->bank_charge }}</td>
							<td>{{ $value->remark }} <a href="#" onclick="updateRecord({{ $value->id }})" class="table-action-btn"><i class="md md-edit"></i></a>
							<input type="hidden" name="p_remark" id="p_remark_{{ $value->id }}" value="{{ $value->remark }}">
							</td>
                            <td>{{@ $value->status->status }}</td>
							<?php
							    $totalAmount +=	$value->amount;
                                $count++;
                            ?>
                        </tr>
                    @endforeach
                    </tbody>
					<h4 style="color:red">Total Amount({{$count}}) : {{$totalAmount}}</h4>
                </table>
              {{$reports->appends(\Input::except('page'))->render() }}
            </div>
        </div>
    </div>
    <div id="con-close-modal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
         aria-hidden="true" style="display: none;">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                    <h4 class="modal-title">Balance Update</h4>
                </div>
                <div class="modal-body">
                    <div style="display:none" id="name-error"></div>

                    <form id="frmTasks" name="frmTasks" class="form-horizontal" novalidate="">
                        {!! csrf_field() !!}
                        <input type="hidden" name="user_id" id="user_id">
						<input type="hidden" name="p_id" id="p_id">
                        <div class="form-group">
                            <label for="inputEmail3" class="col-sm-3 control-label">Amount</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control" id="amount" name="amount"
                                 placeholder="Amount">
                            </div>
                        </div>
                       <!-- <div class="form-group">
                            <label for="inputEmail3" class="col-sm-3 control-label">Reference ID</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control" id="ref_id" name="ref_id"
                                placeholder="Refernece ID">
                            </div>
                        </div>-->
                        <div class="form-group">
                            <label for="inputEmail3" class="col-sm-3 control-label">Remark</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control" id="remark" name="remark"
                                       placeholder="Remark">
                            </div>
                        </div>
                       <!-- <div class="form-group">
                            <label for="inputEmail3" class="col-sm-3 control-label">Status</label>
                            <div class="col-sm-9">
                                <select class="form-control" name="status_id" id="status_id">
                                    <option value="3">Pending</option>
                                    <option value="2">Reject</option>
                                    <option value="1">Approve</option>
                                </select>
                            </div>
                        </div>-->
                    </form>
                </div>
                <div class="modal-footer">
                    <button onclick="update_re()" type="button" class="btn btn-info waves-effect waves-light" id="btn-save" value="add">Save Now
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