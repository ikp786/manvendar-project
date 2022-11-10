@extends('admin.layouts.templatetable')

@section('content')

    <script>
        function transfernow(id) {
		if(confirm('Are you sure to transfer?'))
		{
            var amount = $("#amount_"+id).val();
			if(amount<10)
            {
                alert('Please Enter minimum 10 Rs');
                $("#trbutton_"+id).attr("disabled", false);
                return false;
            }
			var remark = $("#remark_"+id).val();
            var wallet = $("#wallet_"+id).val();
			var dt_scheme=$("#dtscheme_"+id).val();// Added by rajat
            //$("#trbutton_"+id).text("Processing");
            $("#trbutton_"+id).attr("disabled", true);
            var token = $("input[name=_token]").val();
            var user_id = id;
            var commission = $("#commission").val();
            var dataString = 'wallet=' + wallet + '&amount=' + amount + '&remark='+ remark + '&user_id=' + user_id + '&commission=' + commission + '&_token=' + token +'&dt_scheme='+dt_scheme;
            $.ajax({
                type: "POST",
                url: "{{url('commission-transfe')}}",
                data: dataString,
                dataType: "json",
                beforeSend: function() {
                        $("#trbutton_" + id).hide();
                        $('#imgr_' +id).show();
						$.LoadingOverlay("show", {
                            image       : "",
                            fontawesome : "fa fa-spinner fa-spin"
                        });
                    },
                success: function (msg) {
					 $.LoadingOverlay("hide");
                    if(msg.status == 'success') {
                        $("#trbutton_" + id).hide();
                        $('#imgr_' +id).show();
                        location.reload();
                        $('#apibalancenew').load('https://partners.levinm.com/mybal').fadeIn("slow");
                        swal("Success", msg.message, "success");
                        $("#trbutton_"+id).attr("disabled", false);
                        $("#trbutton_"+id).text("Transfer Now");
                        $("#amount_"+id).val('');
                    }else{
                        $("#trbutton_"+id).attr("disabled", false);
                        $("#trbutton_"+id).text("Transfer Now");
                        $("#amount_"+id).val('');
						$('#imgr_' +id).hide();
                        swal("Failure", msg.message, "error");
						 $("#trbutton_" + id).show();
                    }

                }
            });
		}
		else { }
        }
    </script>
	<script>
    function expand_textarea()
	{
		$('textarea.expand').keyup(function () {
		$(this).animate({ height: "4em", width: "13em" }, 500); 
		});
	}
	
	</script>
	
    <div class="row">
        <div class="col-sm-12">
            <h4 class="page-title" style="color: white; font-size: 36px;">{{ $page_title or 'FUND TRANSFER' }}</h4>
        </div>
    </div><br>
    <div class="row">
        <div class="col-sm-12">
            <div class="card-box">
                <div class="row">

                    {{ Form::open(array('url' => 'fund-transfer')) }}

                    {{ Form::close() }}
                    <div class="col-md-12">

                        <table id="demo-custom-toolbar" data-toggle="table"
                               data-toolbar="#demo-delete-row"
                               data-search="true"
                               data-page-list="[20, 10, 20]"
                               data-page-size="10"
                               data-pagination="true" data-show-pagination-switch="true" class="table-bordered ">
                            <thead>
                            <tr>
                                <th data-field="state" data-checkbox="true"></th>
                                <th data-field="id" data-sortable="true">
                                    ID
                                </th>
                                <th data-field="name" data-sortable="true">Name</th>
                                <th data-field="company" data-sortable="true">Shop Name</th>
                                <th data-field="mob" data-sortable="true">Mobile
                                </th>
                                <th data-field="balance" data-align="center" data-sortable="true"
                                    data-sorter="priceSorter">Money Balance
                                </th>
                               <th data-field="rechargebalance" data-align="center" data-sortable="true"
                                    data-sorter="priceSorter">Recharge Balance 
                                </th>
                                @if(Auth::user()->role_id == 1)
                                <th data-field="amount" data-align="center" data-sortable="true"
                                    data-sorter="priceSorter">Member Type
                                </th>
                                @endif
                                <th data-field="recharge_type" data-align="center" data-sortable="true"
                                >Wallet Type
                                </th>
								<th data-field="scheme" data-align="center" data-sortable="true"
                                    data-sorter="priceSorter">Scheme
                                </th>
                                <th data-field="parent_name" data-align="center" data-sortable="true"
                                >Amount
                                </th>
								
								 <th data-field="remark" data-align="center" data-sortable="true">
								 Remark
                                 </th>
								
								
                                <th data-field="status" data-align="center" data-sortable="true"
                                    data-formatter="statusFormatter">Status
                                </th>
                            </tr>
                            </thead>

                            <tbody>
                            @foreach($users as $user)
                                <tr>
                                    <td>{{ $user->id }}</td>
                                    <td>{{ $user->id }}</td>
                                    <td>{{ $user->name }}</td>
                                    <td>{{ $user->member->company }}</td>
                                    <td>{{ $user->mobile }}</td>
                                    <td>{{ number_format($user->balance->user_balance,2) }}</td>
                                  <td>{{ number_format($user->balance->user_commission,2) }}</td>
                                    @if(Auth::user()->role_id == 1)
                                    <td>{{ $user->role->role_title }}</td>
                                    @endif

                                    <td> <select id="wallet_{{ $user->id }}">
                                        
                                    <option value="0">Money</option>
                                    <option value="1">Recharge</option>

                                    </select> </td>
									<td>
									<!-- next php code added by rajat -->
                                    <?php 
                                        $index=$user->upscheme->scheme;
                                    $upscheme=array("0"=> 0,
                                                "$index"=>$user->upscheme->scheme)?>
                                        <div >
                                            {{ Form::select('dtscheme', $upscheme, $user->upscheme->scheme, array('class' => 'form-control','id' => "dtscheme_$user->id",'style'=> 'width: 80px;')) }}
                                        </div>
                                    </td>
									<!-- End php code added by rajat -->
                                    <td><input id="amount_{{ $user->id }}" type="text" name="amount"

                                               style="width:60px; text-align:center; margin-right:5px; height:34px;"></td>
                                    
									 <td><textarea placeholder=""onclick="expand_textarea()" class="expand" id="remark_{{ $user->id }}" rows="1" cols="7" name="remark"></textarea></td>
									
									<td><center><span id="imgr_{{ $user->id }}" style="display:none;"><img src="images/load2.gif" height="40px" width="40px"></span></center>
                                    <button id="trbutton_{{ $user->id }}" onclick="this.disabled=true;transfernow({{ $user->id }})" class="btn btn-success">Transfer Now</button> </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="col-md-6">
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection