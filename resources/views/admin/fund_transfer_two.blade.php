@extends('admin.layouts.templatetable')

@section('content')

    <script>



        function transfernow(id) 
		{
			var amount = $("#amount_"+id).val();
			if(amount<10)
            {
                alert('Please Enter minimum 10 Rs');
                $("#trbutton_"+id).attr("disabled", false);
                return false;
            }
		if(confirm('Are you sure to transfer Amount : ' + amount))
		{
           
			var remark = $("#remark_"+id).val();
            var wallet = 0;
			var dt_scheme=0.0;// Added by rajat
            $("#trbutton_"+id).attr("disabled", true);
            var token = $("input[name=_token]").val();
            var user_id = id;
            var commission = $("#commission").val();
            var dataString = 'wallet=' + wallet + '&amount=' + amount + '&remark='+ remark + '&user_id=' + user_id + '&commission=' + commission + '&dt_scheme='+dt_scheme;
			$.ajaxSetup({
					headers: {
						'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
					}
				})
            $.ajax({
                type: "POST",
                url: "{{url('fund-transfer')}}",
                data: dataString,
                dataType: "json",
                beforeSend: function() {
                        $("#trbutton_" + id).hide();
                        $('#imgr_' +id).show();
						 //$.LoadingOverlay("show");
                    },
                success: function (msg) 
				{
					//$.LoadingOverlay("hide");
                    if(msg.status == 'success') {
                        $("#trbutton_" + id).hide();
                        $('#imgr_' +id).show();
                        //swal("Success", msg.message, "success");
                        $("#trbutton_"+id).attr("disabled", false);
                        $("#trbutton_"+id).text("Transfer Now");
                        $("#amount_"+id).val('');
						alert(msg.message)
						 location.reload();
                    }else{
                        $("#trbutton_"+id).attr("disabled", false);
                        $("#trbutton_"+id).text("Transfer Now");
                        $("#amount_"+id).val('');
						$('#imgr_' +id).hide();
                        //swal("Failure", msg.message, "error");
						 $("#trbutton_" + id).show();
						 alert(msg.message)
                    }

                }
            });
		}
		else { }
        }
		function openVerificationPin(userId,type)
		{
			var amountpattern = /^[0-9]+$/;
			var trasnferAmount = $("#amount_"+userId).val();
			if(!trasnferAmount.match(amountpattern))
			{
				alert('Invalid Amount Format. Please Enter Valid Amount');
				$("#amount_"+userId).focus();
				return false;
			}
			else if(trasnferAmount<10)
			{
				alert('Minimum amount should of Rs. 10');
				$("#amount_"+userId).focus();
				return false;
			}
			
			retailer_name = $("#sn_user_name_"+userId).val();
			retailer_amount= $("#amount_"+userId).val();
			$("#rt_name").text(retailer_name);
			$("#rt_amount").text(retailer_amount);
			
			amount= $("#amountInWords_"+userId).text();
		 
			
			
			$("#modalAmountInWords").text(amount);
			$("#schemeVerificationUser").val(userId);	
			$("#verificationPinType").val(type);
			$('#VerificationPinModal').modal("toggle");
			$("#schemeVerificationPin").val('')
		}
		function pinVerification()
		{
			var schemeVerificationPin = $("#schemeVerificationPin").val();
			var schemeVerificationUser = $("#schemeVerificationUser").val();
			var verificationPinType = $("#verificationPinType").val();
			var dataString = 'pin=' + schemeVerificationPin+'&type='+verificationPinType;
			$.ajax({
				type: "get",
				url: "{{url('scheme-verification-pin')}}",
				data: dataString,
				dataType: "json",
				beforeSend:function()
				{
					$("#schemeVerificationMessage").text('');
					$("#pinVerificationBtn").hide();
					$("#pinVerificationLoader").show();
				},
				success: function (res) {
					$("#schemeVerificationMessage").text(res.message);
				   if(res.status == 1)
				   {
						$("#schemeVerificationMessage").css('color','green');
						$("#VerificationPinModal").modal("hide");
						transfernow(schemeVerificationUser)
				   }
				   else
				   {
						$("#schemeVerificationMessage").css('color','red');
						$("#pinVerificationBtn").show();
						$("#pinVerificationLoader").hide();
				   }
				}
			});
		}
		function amountInWords(recordId)
		{
			$("#amountInWords_"+recordId).text('');
			var amount = $("#amount_"+recordId).val();
			if(amount =='')
				return false;
			else if(amount <0){
				alert("Negative amount not valid")
				
				return false;
			}
			else
			{
					var finalAmount = amount;
					$.ajax({
					type: "get",
					url: "{{url('/')}}/amount-in-words",
					data: "amount="+amount,
					dataType:"json",
					beforeSend:function(){
						
						//$("#updageFeeSpan").hide();
						
					},
					success: function (msg) 
					{
						
						$("#amountInWords_"+recordId).text(msg);
					}
				});
			}
			
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

<div class="panel panel-default">
    <div class="panel-body">
        <div class="col-md-12">
		 <div class="col-md-6">
            <h4> {{$title}}</h4>
			</div>
            <div class="col-md-6">
                <form method="get" action="{{ Request::url()}}" class="form-inline"
                      role="form">
                    {!! csrf_field() !!}
                    <div class="form-group">
                        {{ Form::select('SEARCH_TYPE', ['NAME'=>'Name','ID'=>'Id','MOB_NO'=>'Mobile No','COMPANY'=>'Shop Name'], (app('request')->input('SEARCH_TYPE')), ['class'=>'form-control', 'style'=>"height: 10%;"]) }}
                    </div>
                     <div class="form-group">
                        <label class="sr-only" for="payid">Number</label>
                        <input name="number" type="text" class="form-control" required 
                               value="{{app('request')->input('number')}}" placeholder="Number">
                    </div>
                    <button type="submit" class="btn btn-success waves-effect waves-light m-l-10 btn-md"><span class="glyphicon glyphicon-find"></span><i class="fa fa-search"></i>
                    </button>
                     <a href="{{Request::url()}}" class="btn btn-primary"><i class="fa fa-refresh"></i>
                    </a> 
                </form>
            </div>
        </div>
    </div>
</div><br>
<div class="row">
    <div class="col-sm-12">
        <div class="card-box">
            <div class="table-responsive">
				<table id="mytable"  class="table table-bordered " >
					<tr>
						<!--<th data-field="state" data-checkbox="true"></th>-->
						<th data-field="id" data-sortable="true">ID</th>
						<th data-field="name" data-sortable="true">Name</th>
						<th data-field="company" data-sortable="true">Shop Name</th>
						<th data-field="mob" data-sortable="true">Mobile</th>
						<th data-field="balance" data-align="center" data-sortable="true" data-sorter="priceSorter">Money Balance
						</th>
						@if(Auth::user()->role_id == 1)
						<th data-field="amount" data-align="center" data-sortable="true" data-sorter="priceSorter">Member Type
						</th>
						@endif
					   <th data-field="parent_name" data-align="center" data-sortable="true"
						>Amount
						</th>
						 <th data-field="remark" data-align="center" data-sortable="true">
						 Remark
						 </th>
						<th data-field="action" data-align="center" data-sortable="true">Action
						</th>
					</tr>
					</thead>
					<tbody>
						@foreach($users as $user)
							<tr>
								<td>{{ $user->prefix}}-{{ $user->id }}</td>
								<td>{{ $user->name }}
								<input id="sn_user_name_{{ $user->id }}" type="hidden" name="sn_user_name"  value="{{ $user->name }}" />    
								</td>
								<td>{{ $user->member->company }}</td>
								<td>{{ $user->mobile }}</td>
								<td>{{ number_format($user->balance->user_balance,2) }}</td>
								@if(Auth::user()->role_id == 1)
								<td>{{ $user->role->role_title }}</td>
								@endif
								<td><input id="amount_{{ $user->id }}" type="text" name="amount" style="text-align:center; margin-right:5px;" onfocusout="amountInWords({{ $user->id }})"><br>
								<span id="amountInWords_{{$user->id}}" style="font-size: 11px;    font-weight: bold;">	</span></td>
								 <td><textarea placeholder=""onclick="expand_textarea()" class="expand" id="remark_{{ $user->id }}" rows="1" cols="7" name="remark"></textarea></td>
								<td><center><span id="imgr_{{ $user->id }}" style="display:none;"><img src="images/load2.gif" height="40px" width="40px"></span></center>
								<button id="trbutton_{{ $user->id }}" onclick="openVerificationPin({{ $user->id }},'DT')" class="btn btn-success">Transfer Now</button> </td>
								
							</tr>
                        @endforeach
                        </tbody>
                    </table>
				{{$users->appends(\Input::except('page'))->render() }} 
                </div>
                <div class="col-md-6">
                </div>
            </div>
        </div>
    </div>
</div>
<div id="VerificationPinModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
    <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h4 class="modal-title" id="myModalLabel">Enter Verification Pin</h4>
			</div>
			<div class="modal-body">
				<div class="form-group">
				    <p><b>Name : </b> <span id="rt_name"></span></p>
				    <p><b>Amount : </b><span id="rt_amount"></span></p>
					<input type="password" class="form-control" id="schemeVerificationPin" placeholder="Enter Verification Security Pin"/>
					<input type="hidden" class="form-control" id="schemeVerificationUser"/>
					<input type="hidden" class="form-control" id="verificationPinType"/>
					<span id="schemeVerificationMessage"></span>
				</div>
			
				<div class="form-group">
					<span id="modalAmountInWords"></span>
				</div>
			
			<div class="modal-footer">
				<button id="pinVerificationBtn" onclick="pinVerification()" type="button" class="btn btn-info waves-effect waves-light" value="add">Verify
				</button>
				<img src="images/load2.gif" height="40px" width="40px" id="pinVerificationLoader" style="display:none">
				<button type="button" class="btn btn-default waves-effect" data-dismiss="modal">Close
				</button>
			</div>
        </div>
    </div>
</div>
<meta name="_token" content="{!! csrf_token() !!}"/>
@endsection