@extends('admin.layouts.templatetable')

@section('content')

    <script>
        function transfernow(id) {
            var amount = $("#amount_"+id).val();
            $("#trbutton_"+id).text("Processing");
            $("#trbutton_"+id).prop("disabled", true);
            var token = $("input[name=_token]").val();
            var user_id = id;
            var commission = $("#commission").val();
			var chbox = $("#chbx_"+id).is(':checked');
			if (chbox == true && amount != '')
			{
            var dataString = 'amount=' + amount + '&user_id=' + user_id + '&commission=' + commission + '&_token=' + token;
            $.ajax({
                type: "POST",
                url: "{{url('fund-transfer-recharge')}}",
                data: dataString,
                dataType: "json",
                success: function (msg) {
                    if(msg.success == 'success') {
                        //location.reload();
                        $('#apibalancenew').load('http://teztm.moneyapi.in/mybal').fadeIn("slow");
                        swal("Success", msg.message, "success");
                        $("#trbutton_"+id).prop("disabled", false);
                        $("#trbutton_"+id).text("Transfer Now");
                        $("#amount_"+id).val('');
                    }else{
                        $("#trbutton_"+id).prop("disabled", false);
                        $("#trbutton_"+id).text("Transfer Now");
                        $("#amount_"+id).val('');
                        swal("Failure", msg.message, "error");
                    }

                }
            });
			}
			else
			{
				alert('Select Checkbox Or Amount');
			}
        }
    </script>
    <div class="row">
        <div class="col-sm-12">
            <h4 class="page-title" style="color:white; font-size:36px;">{{ $page_title or 'MONEY TO RECHARGE TRANSFER' }}</h4>
           
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
                               data-page-size="10">
                            <thead>
                            <tr>
                                <th>Select</th>
                                <th data-field="id" data-sortable="true">
                                    ID
                                </th>
                                <th data-field="name" data-sortable="true">Name</th>
                               
                                <th data-field="date" data-sortable="true" data-formatter="dateFormatter">Mobile
                                </th>
                                <th data-field="balance" data-align="center" data-sortable="true"
                                    data-sorter="priceSorter">Balance
                                </th>
                                @if(Auth::user()->role_id == 1)
                                <th data-field="recharge" data-align="center" data-sortable="true"
                                    data-sorter="priceSorter">Recharge Balance
                                </th>
                                @endif
                                <th data-field="parent_name" data-align="center" data-sortable="true"
                                >Amount
                                </th>
                                
                                <th data-field="status" data-align="center" data-sortable="true"
                                    data-formatter="statusFormatter">Action
                                </th>
                            </tr>
                            </thead>

                            <tbody>
                            @foreach($users as $user)
                            <?php $c_id = Auth::user()->company_id; ?>
                               
                                <tr>
								
                                    <td><input type="checkbox" id="chbx_{{ $user->id }}" value="{{ $user->id }}"></td>
                                    <td>{{ $user->id }}</td>
                                    <td>{{ $user->name }}</td>
                                    
                                    <td>{{ $user->mobile }}</td>
                                    <td>{{ $user->balance->user_balance }}</td>
                                    @if(Auth::user()->role_id == 1)
                                    <td>{{ $user->balance->user_commission }}</td>
                                    @endif

                                    <td><input id="amount_{{ $user->id }}" type="text" name="amount"

                                               style="width:60px; text-align:center; margin-right:5px; height:34px;"></td>
                                    <td><a id="trbutton_{{ $user->id }}" onclick="transfernow({{ $user->id }})" class="btn btn-success">Transfer Now</a> </td>
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