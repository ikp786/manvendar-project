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
			     alert(data);
				 location.reload();
                }
            }) 
        }
		
		
        function updateRecord(id) {
            var token = $("input[name=_token]").val();
            var dataString = 'id=' + id;
			var d = $('.re_'+id).val();
			var com_id = $('.c_'+id).val();
            $('#remarks').html(d);
            $('#remarks').val(d);
            $('#remark').val(d);
            $('#select_remark').change(function(){
               var s_value =  $(this).val();
            $('#remark').val(s_value);
            });

			// $('#remarks').html(d);
   //          $('#remarks').val(d);
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
                <h4 class="page-title" style="color: white; font-size: 36px;">{{ $page_title or 'COMPLAIN REQUEST VIEW' }}</h4>
            </div>
            <div class="col-lg-6 col-md-6">
                <!-- <div class="pull-right">
                    <button onclick="add_record()" id="demo-add-row" class="btn btn-success"><i
                                class="fa fa-plus m-r-5"></i>Add Record
                    </button>
                </div> -->
            </div>
        </div>
    </div><br>

    <!--Basic Columns-->
    <!--===================================================-->


    <!--===================================================-->
    <div class="row">
        <div class="col-sm-12">
            <div class="card-box">
                <h4 class="m-t-0 header-title"><b>{{ $page_title or 'Complain Request Detail' }}</b></h4>
                <p class="text-muted font-13">
                    Add or Update Service Provider Detail
                </p>
                <table id="demo-custom-toolbar" data-toggle="table"
                       data-toolbar="#demo-delete-row"
                       data-search="true"
                       data-page-size="50">
                    <thead>
                    <tr>
                        <th>C_ID</th>
						 <th data-field="date" data-sortable="true" data-formatter="dateFormatter">Date
                        </th>
						 <th data-field="Issuedate" data-sortable="true" data-formatter="dateFormatter">Issue Date
                        </th>
						 <th data-field="txn_id">Txn ID</th>
                        <th data-field="user_name" data-sortable="true">
                            User Name
                        </th>
                        <th data-field="name" data-sortable="true" data-formatter="statusFormatter">Isue Type</th>
                        <th data-field="bank_name" data-sortable="true">Product</th>
                       
                       <!-- <th data-field="bankref" data-align="center" data-sortable="true"
                            data-sorter="priceSorter">Ref Id
                        </th>-->
		            <th data-field="acount" data-align="center" data-sortable="true" data-sorter="priceSorter">Acc no.</th>
                        <th data-field="amount" data-align="center" data-sortable="true"
                            data-sorter="priceSorter">Amount
                        </th>
						
						<th data-field="remark" data-align="center" data-sortable="true"
                            data-sorter="priceSorter">Remark
                        </th>
						
                        <th data-field="status" data-align="center" data-sortable="true"
                            data-formatter="statusFormatter">Status
                        </th>
                        <th data-field="action" data-align="center" data-sortable="true">Action
                        </th>
                    </tr>
                    </thead>

                    <tbody>
                    @foreach($admin_complain as $key => $value)
                        <tr>
                            <td>{{ $value->com_id }}</td>
							<td>{{ $value->created_at }}</td>
							<td>{{ $value->date_txn }}</td>
							<td>{{ $value->txn_id }}</td>
                            <td>{{ $value->name }} ({{ $value->user_id}})</td>
                            @if($value->issue_type=='DOUBLE TXN' && $value->status=='Pending' || $value->issue_type=='WRONG TXN' && $value->status=='Pending')
                            <td><span style="background:red; padding: 5px;">{{ $value->issue_type }}</span></td></td>
                            @else
                             <td class="{{ $value->status }}"> {{ $value->issue_type }}</td>
                            @endif
                            
                            <td>{{ $value->product}}</td>
                            
                            <!--<td>{{ $value->bankref }}</td>-->
							 <td>{{ $value->bank_ac }}</td>
                            <td>{{ $value->amount }}</td>
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
							
                               <select id="select_remark" class="form-control" name="select_remark" value="">
                                <option id="remarks"></option>
                                <option value="सर, यह ट्रांसक्शन SUCCESS है और अकाउंट में  क्रेडिट है कृपया  चेक करे या पासबुक की कॉपी हमे भेजे ADMIN@LEVINM.COM.">सर, यह ट्रांसक्शन SUCCESS है और अकाउंट में  क्रेडिट है कृपया  चेक करे या पासबुक की कॉपी हमे भेजे ADMIN@LEVINM.COM.</option>
                                <option value="सर, यह ट्रांसक्शन फ़ैल हो गयी है कृपया MONEY TRANSACTION REPORT में जाकर कर OTP डाल कर रिफंड ले .">सर, यह ट्रांसक्शन फ़ैल हो गयी है कृपया MONEY TRANSACTION REPORT में जाकर कर OTP डाल कर रिफंड ले .</option>
                                <option value="सर, यह ट्रांसक्शन हमारी तरफ से कस्टमर के  बैंक को भेजी जा चुकी है और  यह SUCCESS है या FAILED  है इसका जवाब आना बाकी है ! कृप्या पैसा खाते में चेक करे या बैंक के रिप्लाई का इंतज़ार करे ">सर, यह ट्रांसक्शन हमारी तरफ से कस्टमर के  बैंक को भेजी जा चुकी है और  यह SUCCESS है या FAILED  है इसका जवाब आना बाकी है ! कृप्या पैसा खाते में चेक करे या बैंक के रिप्लाई का इंतज़ार करे </option>
                                </select>
							</div>
                            </div>
                            <div class="form-group">
                            <label for="inputEmail3" class="col-sm-3 control-label">Update Remark</label>
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