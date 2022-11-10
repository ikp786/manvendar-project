@extends('layouts.app')
@section('content')
<script>
	function showBankField()
	{
		var paymentMode = $("#upaymentMode").val()
		if(paymentMode != "Cash")
			$("#BankField").show()
		else
			$("#BankField").hide()
	}
	function companyPaymentMode()
	{
		var onLineCompany = $("#cpaymentMode").val()
		if(onLineCompany =="OnLine")
		{
			$("#onLinePaymentMode").show();
			$("#branchCodeDiv").hide();

		}
		else{
			$("#onLinePaymentMode").hide();
			$("#branchCodeDiv").show();
		}

	}

	function sendFundRequest()
	{
		var requestTo = $("#requestTo").val()
		if(requestTo == 1)
		{
			var amount = $("#uamount").val()
			var paymentDate = $("#upaymentDate").val()
			var paymentMode = $("#upaymentMode").val()
			if(paymentMode != "Cash"){
				var bankName = $("#ubankName").val()
				var refNumber = $("#urefNumber").val()
				if(bankName ==''){
					alert("Please Enter Bank Name");
					$("#ubankName").focus()
					return false;
				}
				else if(refNumber==''){
					alert("Please enter reference Number");
					$("#urefNumber").focus()
					return false;
				}


			}
			var remark = $("#uremark").val()
		}
		else{
			var amount = $("#camount").val()
			var paymentDate = $("#cpaymentDate").val()
			var paymentMode = $("#cpaymentMode").val()
			var loc_batch_code = $("#cloc_batch_code").val()
			if(loc_batch_code =='' && paymentMode != "OnLine"){
				alert("Please Enter Location/Branch Code");
				return false;
			}
			var refNumber = $("#crefNumber").val()
			if(refNumber==''){
					alert("Please enter reference Number");
					$("#crefNumber").focus()
					return false;
				}
			var remark = $("#cremark").val()
		}
		if(amount <=0){
			alert("Please enter valid amount");
			return false;
		}
		else if(paymentDate==''){
			alert("Please choose payment Date");
			return false;
		}
		else if(paymentMode==''){
			alert("Please select payment mode");
			return false;
		}

		else if(remark==''){
			alert("Please Enter remark");
			return false;
		}


	}

	/*for date->Calender*/

    $(document).ready(function () {
        $.noConflict();
        $('.customDatepicker').datepicker({
				autoclose: true,
				format: "dd-mm-yyyy"
        });

    });
</script>
	<div class="row">
	<div class="text-right"><a href="{{url('payment-request-report')}}"> Fund Request History</a></div>
	<form class="form-horizontal" action="{{url('payment-request')}}" style='width: 55%;' method="post" onSubmit="return sendFundRequest()">
		@if ($message = Session::get('success'))
<div class="alert alert-success alert-block">
	<button type="button" class="close" data-dismiss="alert">×</button>
        <strong>{{ $message }}</strong>
</div>
@endif


@if ($message = Session::get('error'))
<div class="alert alert-danger alert-block">
	<button type="button" class="close" data-dismiss="alert">×</button>
        <strong>{{ $message }}</strong>
</div>
@endif


@if ($message = Session::get('warning'))
<div class="alert alert-warning alert-block">
	<button type="button" class="close" data-dismiss="alert">×</button>
	<strong>{{ $message }}</strong>
</div>
@endif


@if ($message = Session::get('info'))
<div class="alert alert-info alert-block">
	<button type="button" class="close" data-dismiss="alert">×</button>
	<strong>{{ $message }}</strong>
</div>
@endif


@if ($errors->any())
<div class="alert alert-danger">
	<button type="button" class="close" data-dismiss="alert">×</button>
	Please check the form below for errors
</div>
@endif
		{{csrf_field()}}

			<div class="form-group row">
				<label class="col-sm-4 col-form-label text-right" for="amount">Approver Details:</label>
				<div class="col-sm-8">
					<select class="form-control" name="requestTo" id="requestTo" onChange="getRequestTo()">
					<option value="3">{{Auth::user()->company->company_name }} (Email : {{ Auth::user()->company->company_email}} )</option>
					</select>
				</div>
			</div>



		<div id="companyDiv" style="display:block">
			<div class="form-group row">
			<label class="col-sm-4 col-form-label text-right" for="request"> To Account:</label>
			<div class="col-sm-8">
			{{ Form::select('companyBank', $companyBanks, old('companyBank'), array('class' => 'form-control','id' => 'companyBank')) }}

			</div>
			</div>
			<div class="form-group row">
				<label class="col-sm-4 col-form-label text-right" for="amount">Amount:</label>
				<div class="col-sm-8">
					<input type="text" class="form-control" id="camount"name="camount" placeholder="Amount">
				</div>
			</div>
			<div class="form-group row">
				<label class="col-sm-4 col-form-label text-right" for="paymentDate">Payment Date:</label>
				<div class="col-sm-8">
					<input type="text" class="form-control customDatepicker" id="cpaymentDate"name="cpaymentDate" value="{{date('d-m-Y')}}">
				</div>
			</div>
			<div class="form-group row">
				<label class="col-sm-4 col-form-label text-right" for="email">Payment Mode:</label>
				<div class="col-sm-8">
					<select class="form-control" id="cpaymentMode"name="cpaymentMode" onChange="companyPaymentMode()">
						<option value="Cheque">Cheque</option>
						<option value="OnLine">OnLine</option>
						<option value="cash@Counte">cash@Counte</option>
						<option value="Cash@CDM">Cash@CDM</option>
					</select>
				</div>
			</div>
			<div id="CompanyBankField">

				<div class="form-group row">
					<label class="col-sm-4 col-form-label text-right" for="Ref Number">Ref Number:</label>
					<div class="col-sm-8">
						<input type="text" class="form-control" id="crefNumber" name="crefNumber">
					</div>
				</div>
				<div class="form-group row" id="branchCodeDiv">
					<label class="col-sm-4 col-form-label text-right" for="bank Name">Location/Branch Code:</label>
					<div class="col-sm-8">
						<input type="text" class="form-control" id="cloc_batch_code"name="cloc_batch_code">
					</div>
				</div>
				<div class="form-group row" id="onLinePaymentMode" style="display:none">
					<label class="col-sm-4 col-form-label text-right" for="bank Name">Online Payment Mode:</label>
					<div class="col-sm-8">
					<select class="form-control" id="cOnlinePaymentMode"name="cOnlinePaymentMode">
						<option value="IMPS">IMPS</option>
						<option value="NEFT">NEFT</option>
						<option value="RTGS">RTGS</option>
						<option value="OTHER">OTHER</option>
					</select>
					</div>
				</div>
			</div>
			<div class="form-group row">
				<label class="col-sm-4 col-form-label text-right" for="Remark">Remark:</label>
				<div class="col-sm-8">
					 <textarea class="form-control" id="cremark" name="cremark" placeholder="Remark"></textarea>

				</div>
			</div>

		</div>
		<div class="form-group">
				<div class="text-center">
					<button type="submit" class="btn btn-outline-success">Submit</button>
				</div>
		</div>
	</form>
	</div>
@endsection