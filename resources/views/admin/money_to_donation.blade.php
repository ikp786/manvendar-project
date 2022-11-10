@extends('admin.layouts.templatetable')

@section('content')

    <script>
        function transfernow(id) {
            var amount = $("#amount_"+id).val();
            $("#trbutton_"+id).text("Processing");
            $("#trbutton_"+id).prop("disabled", true);
            var token = $("input[name=_token]").val();
            var user_id = id;
            var doner = $('#doner').val();
             var doner_mob = $('#doner_mob').val();
            var commission = $("#commission").val();
			var chbox = $("#chbx_"+id).is(':checked');
			if (chbox == true && amount != '' && doner != '' && doner_mob!='')
			{
            var dataString = 'amount=' + amount + '&user_id=' + user_id + '&doner='+ doner +'&doner_mob='+ doner_mob +'&commission=' + commission + '&_token=' + token;
            $.ajax({
                type: "POST",
                url: "{{url('fund-transfer-donation')}}",
                data: dataString,
                dataType: "json",
                success: function (msg) {
                    if(msg.success == 'success') {
                        //location.reload();
                        $('#apibalancenew').load('http://teztm.moneyapi.in/mybal').fadeIn("slow");
                       // swal("Success", msg.message, "success");
                       $("#customer_name").html(msg.username);
                        $("#showamount").html(amount);
                        $('#doner_mobile').html(msg.doner_m);
                        $("#showid").html(msg.txnid);
                        $("#myModal").modal("toggle");
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
				alert('Select Checkbox,Doner Or Amount');
			}
        }
    </script>
    <script type="text/javascript">
        function PrintDiv() {
            var contents = document.getElementById("dvContents").innerHTML;
            var frame1 = document.createElement('iframe');
            frame1.name = "frame1";
            frame1.style.position = "absolute";
            frame1.style.top = "-1000000px";
            document.body.appendChild(frame1);
            var frameDoc = frame1.contentWindow ? frame1.contentWindow : frame1.contentDocument.document ? frame1.contentDocument.document : frame1.contentDocument;
            frameDoc.document.open();
            frameDoc.document.write('<html><head><title>DIV Contents</title>');
            frameDoc.document.write('</head><body>');
            frameDoc.document.write(contents);
            frameDoc.document.write('</body></html>');
            frameDoc.document.close();
            setTimeout(function () {
                window.frames["frame1"].focus();
                window.frames["frame1"].print();
                document.body.removeChild(frame1);
            }, 500);
            return false;
        }
    </script>
    <div class="row">
        <div class="col-sm-12">
            <h4 class="page-title" style="color:white; font-size:36px;">{{ $page_title or 'MONEY TO DONATION TRANSFER' }}</h4>
           
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
                                <th data-field="donername" data-sortable="true">Doner Name</th>
                                 <th data-field="donarmob" data-sortable="true">Doner Mobile</th>
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
                           
                                <tr>
								
                                    <td><input type="checkbox" id="chbx_{{ $user->id }}" value="{{ $user->id }}"></td>
                                    <td>{{ $user->id }}</td>
                                    <td>{{ $user->name }}</td>
                                    <td>{{ $user->mobile }}</td>
                                     <td><input required type="text" name="doner" id="doner"></td>
                                     <td><input type="number" name="doner_mob" id="doner_mob"></td>
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
                    <div class="col-md-12">
                    <img src="images/Army22.gif" width="100%">
                    </div>
                </div>
            </div>
        </div>
    </div>
  <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header warning" style="background:orange;">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel">Transaction Detail</h4>
                </div>

                <div class="modal-body">
                    <div id="dvContents" style="border: 1px dotted black; padding: 5px; width: 100%">
                        <form class="form-horizontal">
                            <input type="hidden" value="{{ $user_id }}" id="user_id" name="user_id">
                            <input id="c_bene_id" type="hidden">
                            <input id="c_sender_id" type="hidden">
                            <div class="form-group">
                                <label for="bank_account" class="control-label col-sm-4">
                                    Agent Name : </label>

                                <label for="bank_account" class="control-label col-sm-4">
                                    {{ Auth::user()->name }}</label>

                            </div>
                            <div class="form-group">
                                <label for="bank_account" class="control-label col-sm-4">
                                    Doner Name : </label>

                                <label for="bank_account" id="customer_name" class="control-label col-sm-4">
                                </label>

                            </div>
                            
                             <div class="form-group">
                                <label for="doner_mobile" class="control-label col-sm-4">
                                    Doner Mobile : </label>

                                <label for="doner_mobile" id="doner_mobile" class="control-label col-sm-4">
                                </label>

                            </div>
                            
                            
                            <div class="form-group">
                                <label for="bank_account" class="control-label col-sm-4">
                                    Amount : </label>

                                <label id="showamount" for="bank_account" class="control-label col-sm-4">
                                </label>

                            </div>
                            
                            <div class="form-group">
                                <label for="bank_account" class="control-label col-sm-4">
                                    LM Id : </label>

                                <label id="showid" for="bank_account" class="control-label col-sm-4">
                                </label>
                            </div>

                            <div class="form-group">
                                <label for="bank_account" class="control-label col-sm-4">
                                    Status : </label>

                                <label id="statusnew" for="bank_account" class="control-label col-sm-4">
                                    Success</label>
                            </div>

                            <div class="form-group">
                                <label for="bank_account" class="control-label col-sm-4">
                                    Date &amp; Time : </label>

                                <label for="bank_account" class="control-label col-sm-4">

                                    {{ $mytime = Carbon\Carbon::now() }}


                                </label>
                            </div>
                            <div class="form-group">
                                <label for="bank_account" class="control-label col-sm-4">
                                    Thanks! {{ Auth::user()->company->company_name }} </label>

                                <label for="bank_account" class="control-label col-sm-4">

                                    {{ Auth::user()->mobile }}

                                </label>
                            </div>
                            <div class="form-group">
                            <label for="thanx" class="control-label col-sm-12">
                            धन्यवाद........ ! हमे आप पर गर्व है की आपने RS. <span id="showthanxamount"></span> इंडियन आर्मी को डोनेट किये !
                            </label>
                            </div>
                        </form>
                    </div>
                </div>
                <div class="modal-footer">
                    <input type="button" onclick="PrintDiv();" value="Print"/>
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
@endsection