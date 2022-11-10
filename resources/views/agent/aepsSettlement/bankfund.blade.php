@extends('layouts.app')
@section('content')
<style>
.textFormat{
	font-family: time;
}
</style>
<script type="text/javascript">
	function expand_textarea()
	{
		$('textarea.expand').keyup(function () {
		$(this).animate({ height: "4em", width: "13em" }, 500); 
		});
	}

    function transfernow(id) 
		{
			var amount = $("#amount").val();
            var remark = $("#remark").val();
            var channel = $("#channel").val();
			var numberFormat = /^[0-9]+$/;
			if(amount=='')
			{
				alert('Only Number is allowed');
				$('#amount').focus();
				return false;
			}
			else if(!amount.match(numberFormat))
			{
				alert("Please enter valid amount");
				$('#amount').focus();
				return false;
				
			}
			else if(amount<1)
			{
				alert('Miminum Amount should be Rs. 1');
				$('#amount').focus();
				return false;
			}
			
		if(confirm('Are you sure to transfer Amount : ' + amount))
		{          
            var dataString = 'amount=' + amount + '&remark='+ remark+ '&id='+ id+ '&channel='+ channel;
			$.ajaxSetup({
					headers: {
						'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
					}
				})
            $.ajax({
                type: "POST",
                url: "{{url('aeps/settlement-request')}}",
                data: dataString,
                dataType: "json",
				beforeSend: function() {
						$("#trbutton").attr("disabled", false);
						$("#loader").show();
						$("#Sendnow").hide();
                    },
                success: function (data) 
				{
					$("#loader").hide();
                    $("#Sendnow").show();
					if (data.status == 1) {
						alert(data.message);
						location.reload();
					} 
					else{
						alert(data.message);
						location.reload();
					}
					           
				}
			});
        }
    }
	
</script>
<div class="super_container">
	<div class="home">
	</div>
	<div class="search">					
            
            @include('agent.aepsSettlement.aepsSettlement-type')
            <br>
     
		<div class="row">
		    <div class="col-sm-12">
		        <div class="">
		            <table class="table table-bordered">
		                <thead style="">
		                    <tr>
								<th data-field="id" data-sortable="true">ID </th>
								<th>Account Holder Name</th>
		                        <th>Account Number<br>Bank Name</th>
		                        <th>IFSC Code<br>Branch Name</th>
		                        <th>Balance</th>
		                        <th>Minimum Avl Bal.</th>
		                        <th>Charge</th>
		                        <th>Amount</th>
		                         <th>Remark</th>
		                         <th>Payment Type</th>
		                        <th>Action</th>	
		                      
		                    </tr>
		                </thead>
		                <tbody>
		                @foreach($bankDetails as $bankDetail)		
		                    <tr style="background-color:white">
		                        <td>{{ $bankDetail->id }}</td>
		                        <td>{{ $bankDetail->name }}</td>
		                        <td>{{ $bankDetail->account_number }}<br>{{ $bankDetail->bank_name }}</td>
		                        <td>{{ $bankDetail->ifsc}}</br>{{ $bankDetail->branch_name }}</td>
		                        <td>{{ number_format($bankDetail->user->balance->user_balance,2)}}</td>	
		                        <td>{{ number_format(Auth::user()->member->aeps_blocked_amount,2)}}</td>	
		                        <td>{{ number_format(Auth::user()->member->aeps_charge,2)}}</td>	
		                        <td>
									@if(Auth::user()->member->aeps_blocked_amount <= $bankDetail->user->balance->user_balance)
										<input type="text" name="amount" id="amount">
									@else
										<span style="color:red">Minimum Availabel Bal not available</span>
									@endif
								</td>
		                        <td><textarea onclick="expand_textarea()" class="expand" rows="1" cols="7" name="remark" id="remark"></textarea></td>
								<td>
								
                     {{ Form::select('channel', ['2' => 'IMPS', '1' => 'NEFT'], null, ['class'=>'form-control','id'=>"channel"]) }}
								</td>
								
		                        <td>
									@if(Auth::user()->member->aeps_blocked_amount <= $bankDetail->user->balance->user_balance)<button onclick="transfernow({{$bankDetail->id}})" class="btn btn-success" id="Sendnow">Send Now</button> 
 								<img src="{{url('/loader/loader.gif')}}" id="loader" class="loaderImg" style="display:none"/>@endif
		                        </td>
		                    </tr>
		                @endforeach
		                </tbody>
		            </table>
		        </div>
				<div> <h2 class="textFormat">Terms and Condition:-</h2>
					<h4 class="textFormat"><b>1. </b>Available between 9am to 4pm.</h4>
					<h4 class="textFormat"><b>2. </b>Please check account number before settlement for imps option. Any transaction to wrong account using imps will not be returend back.</h3>
					<h4 class="textFormat"><b>3. </b>All settlement will be charged <span style="color:red">Rs {{ number_format(Auth::user()->member->aeps_charge,2)}} per Rs 25000.</span> which deducted from wallet amount. </h4>
					
				</div>
		    </div>
			
					
		</div>
	</div>
</div>
 <meta name="_token" content="{!! csrf_token() !!}"/>
@endsection
