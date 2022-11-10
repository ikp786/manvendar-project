<?php $__env->startSection('content'); ?>
    <script>

        /*fund request*/
        function showBankField() {
            var paymentMode = $("#upaymentMode").val()
            if (paymentMode != "Cash")
                $("#BankField").show()
            else
                $("#BankField").hide()
        }

        function companyPaymentMode() {
            var onLineCompany = $("#cpaymentMode").val()
            if (onLineCompany == "OnLine") {
                $("#onLinePaymentMode").show();
                $("#branchCodeDiv").hide();
				 $("#chargeAmountDisplay").text('');

            } else {
                if(onLineCompany=="Cash@Counter" || onLineCompany=="Cash@CDM")
					calculateCashDepositeCharge()
				else
					$("#chargeAmountDisplay").text('');
				$("#onLinePaymentMode").hide();
                $("#branchCodeDiv").show();
            }
        }
		function calculateCashDepositeCharge()
		{
			var onLineCompany = $("#cpaymentMode").val()
			if(onLineCompany=="Cash@Counter" || onLineCompany=="Cash@CDM"){
			var requestAmount = $("#camount").val();
			var cashDepositCharge = $("#cashDepositCharge").val()
			var cashDepositMinCharge = $("#cashDepositMinCharge").val()
			try 
			{ 
				if(requestAmount == "")  throw "Please enter amount first";
				if(isNaN(requestAmount)) throw "not a number";
				if(requestAmount < 1)  throw "too low";
			}
			catch(err) {
				$("#cpaymentMode").val('Cheque')
				alert("Amount " + err);
				return false;
			}
			var pcntCharge = (requestAmount*cashDepositCharge)/100;
			if(pcntCharge < cashDepositMinCharge)
				$("#chargeAmountDisplay").text("charge Amount: "+cashDepositMinCharge);
			else
				$("#chargeAmountDisplay").text("charge Amount: "+pcntCharge);
			}
		}
        function getRequestTo() {
            var requestTo = $("#requestTo").val()
            if (requestTo == 1 || requestTo == "1") {
                var requestToContent = "<?php echo e(Auth::user()->name); ?> (Rs. <?php echo e(number_format(Auth::user()->balance->user_balance,2)); ?>)"
                $("#upperParentDiv").show()
                $("#companyDiv").hide()
            } else {
                var requestToContent = "<?php echo e(Auth::user()->company->company_name); ?> (Email : <?php echo e(Auth::user()->company->company_email); ?> )"
                $("#upperParentDiv").hide()
                $("#companyDiv").show()
            }
            $("#aproveDetails").val(requestToContent)
        }

        function sendFundRequest() {
            var requestTo = $("#requestTo").val()
            if (requestTo == 1) {
                var amount = $("#uamount").val()
                var paymentDate = $("#upaymentDate").val()
                var paymentMode = $("#upaymentMode").val()
                if (paymentMode != "Cash") {
                    var bankName = $("#ubankName").val()
                    var refNumber = $("#urefNumber").val()
                    if (bankName == '') {
                        alert("Please Enter Bank Name");
                        $("#ubankName").focus()
                        return false;
                    } else if (refNumber == '') {
                        alert("Please enter reference Number");
                        $("#urefNumber").focus()
                        return false;
                    }
                }
                var remark = $("#uremark").val()
            } else {
                var amount = $("#camount").val()
                var paymentDate = $("#cpaymentDate").val()
                var paymentMode = $("#cpaymentMode").val()
                var loc_batch_code = $("#cloc_batch_code").val()
                if (loc_batch_code == '' && paymentMode != "OnLine") {
                    alert("Please Enter Location/Branch Code");
                    return false;
                }
                var refNumber = $("#crefNumber").val()
                if (refNumber == '') {
                    alert("Please enter reference Number");
                    $("#crefNumber").focus()
                    return false;
                }
                var remark = $("#cremark").val()
            }
            if (amount <= 0) {
                alert("Please enter valid amount");
                return false;
            } else if (paymentDate == '') {
                alert("Please choose payment Date");
                return false;
            } else if (paymentMode == '') {
                alert("Please select payment mode");
                return false;
            } else if (remark == '') {
                alert("Please Enter remark");
                return false;
            }
        }
        /*borrow request*/
        function sendFundBorrow() {
            var amount = $("#bAmount").val()
            var paymentDate = $("#bPaymentDate").val()
            var borrow_type = $("#bBorrowType").val()
            var remark = $("#bRemark").val()
            if (amount <= 0) {
                alert("Please enter valid amount");
                return false;
            } else if (paymentDate == '') {
                alert("Please choose payment Date");
                return false;
            } else if (borrow_type == '') {
                alert("Please select payment mode");
                return false;
            } else if (remark == '') {
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
		if(<?php echo e(Auth::user()->parent_id); ?> == 1)
			{
				getRequestTo();
			}
        });
    </script>
    <div class="container" style="background: white;">
        <!--<ul class="nav nav-tabs">
            <li class="active"><a data-toggle="tab" href="#fundRequest">Fund Request</a></li>
            <li><a data-toggle="tab" href="#borrowRequest">Borrow Request</a></li>
        </ul>--><br>
        <div class="col-md-5">
            <div class="tab-content" >
                
                <div class="row tab-pane fade in active" id="fundRequest" style="background-color: #fff;margin: 0">
                 
                    <form class="form-horizontal" action="<?php echo e(url('payment-request')); ?>" method="post" onSubmit="return sendFundRequest()" enctype="multipart/form-data">
                        <?php if($message = Session::get('success')): ?>
                            <div class="alert alert-success alert-block">
                                <button type="button" class="close" data-dismiss="alert">×</button>
                                <strong><?php echo e($message); ?></strong>
                            </div>
                        <?php endif; ?>

                        <?php if($message = Session::get('error')): ?>
                            <div class="alert alert-danger alert-block">
                                <button type="button" class="close" data-dismiss="alert">×</button>
                                <strong><?php echo e($message); ?></strong>
                            </div>
                        <?php endif; ?>

                        <?php if($message = Session::get('warning')): ?>
                            <div class="alert alert-warning alert-block">
                                <button type="button" class="close" data-dismiss="alert">×</button>
                                <strong><?php echo e($message); ?></strong>
                            </div>
                        <?php endif; ?>

                        <?php if($message = Session::get('info')): ?>
                            <div class="alert alert-info alert-block">
                                <button type="button" class="close" data-dismiss="alert">×</button>
                                <strong><?php echo e($message); ?></strong>
                            </div>
                        <?php endif; ?>


                        <?php if($errors->any()): ?>
                            <div class="alert alert-danger">
                                <button type="button" class="close" data-dismiss="alert">×</button>
                                Please check the form below for errors
                            </div>
                        <?php endif; ?>
                        <?php echo e(csrf_field()); ?>

                        <div class="form-group row">
                            <label class="col-sm-4 col-form-label text-right" for="email">Approver Details:</label>
                            <div class="col-sm-8">
                                <input type="text" readonly class="form-control-plaintext form-control" id="aproveDetails" value="<?php echo e(Auth::user()->name); ?> (Rs. <?php echo e(number_format(Auth::user()->balance->user_balance,2)); ?> )">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-4 col-form-label text-right" for="request">Request To:</label>
                            <div class="col-sm-8">
                                <select class="form-control" name="requestTo" id="requestTo" onChange="getRequestTo()">
    								<?php if(Auth::user()->parent->role_id !=1): ?>
    									<option value="1"><?php echo e(Auth::user()->parent->role->role_title); ?></option>
    								<?php endif; ?>
                                    <option value="2"> Company</option>
                                </select>
                            </div>
                        </div>
                        <div id="upperParentDiv">
                            <div class="form-group row">
                                <label class="col-sm-4 col-form-label text-right" for="amount">Amount:</label>
                                <div class="col-sm-8">
                                    <input type="text" class="form-control" id="uamount" placeholder="amount" name="uamount">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-4 col-form-label text-right" for="paymentDate">Payment Date:</label>
                                <div class="col-sm-8">
                                    <input type="text" class="form-control customDatepicker" id="upaymentDate" name="upaymentDate" value="<?php echo e(date('d-m-Y')); ?>">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-4 col-form-label text-right" for="email">Payment Mode:</label>
                                <div class="col-sm-8">
                                    <select class="form-control" id="upaymentMode" onChange="showBankField()" name="upaymentMode">
                                        <option value="Cash">Cash</option>
                                        <option value="OnLine">OnLine</option>
                                        <option value="Cheque">Cheque</option>
                                    </select>
                                </div>
                            </div>
                            <div id="BankField" style="display:none">
                                <div class="form-group row">
                                    <label class="col-sm-4 col-form-label text-right" for="bank Name">Bank Name:</label>
                                    <div class="col-sm-8">
                                        <input type="text" class="form-control" id="ubankName" name="ubankName">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-sm-4 col-form-label text-right" for="Ref Number">Ref Number:</label>
                                    <div class="col-sm-8">
                                        <input type="text" class="form-control" id="urefNumber" name="urefNumber">
                                    </div>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-4 col-form-label text-right" for="Remark">Remark:</label>
                                <div class="col-sm-8">
                                    <textarea class="form-control" id="uremark" placeholder="remark" name="uremark"></textarea>

                                </div>
                            </div>
                        </div>
                        <div id="companyDiv" style="display:none">
                            <div class="form-group row">
                                <label class="col-sm-4 col-form-label text-right" for="request"> To Account:</label>
                                <div class="col-sm-8">
                                    <?php echo e(Form::select('companyBank', $companyBanks, old('companyBank'), array('class' => 'form-control','id' => 'companyBank'))); ?>


                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-4 col-form-label text-right" for="amount">Amount:</label>
                                <div class="col-sm-8">
                                    <input type="text" class="form-control" id="camount" name="camount" placeholder="Amount" onkeyup="calculateCashDepositeCharge()" autocomplete="off">
									<span style="color:red" id="chargeAmountDisplay"></span>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-4 col-form-label text-right" for="paymentDate">Payment Date:</label>
                                <div class="col-sm-8">
                                    <input type="text" class="form-control customDatepicker" id="cpaymentDate" name="cpaymentDate" value="<?php echo e(date('d-m-Y')); ?>">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-4 col-form-label text-right" for="email">Payment Mode:</label>
                                <div class="col-sm-8">
                                    <select class="form-control" id="cpaymentMode" name="cpaymentMode" onChange="companyPaymentMode()">
                                        <option value="Cheque">Cheque</option>
                                        <option value="OnLine">OnLine</option>
										<option value="Cash@Counter">cash@Counter</option>
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
                                        <input type="text" class="form-control" id="cloc_batch_code" name="cloc_batch_code">
                                    </div>
                                </div>
                                <div class="form-group row" id="onLinePaymentMode" style="display:none">
                                    <label class="col-sm-4 col-form-label text-right" for="bank Name">Online Payment Mode:</label>
                                    <div class="col-sm-8">
                                        <select class="form-control" id="cOnlinePaymentMode" name="cOnlinePaymentMode">
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
    					<div class="form-group row">
                            <label class="col-sm-4 col-form-label text-right">Payment Slip</label>
                            <div class="col-sm-8">
                                <input type="file" name="d_picture" placeholder="Attach payment Slip" class="form-control">
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="text-center">
                                <button type="submit" class="btn btn-outline-success">Submit</button>
                            </div>
                        </div>
                    </form>
                </div>

                

                <div class="row tab-pane fade" id="borrowRequest1" style="background-color: #fff;margin: 0">
                    <div class="text-right"  style="margin-right: 100px"><a href="<?php echo e(url('payment-request-report')); ?>"> Fund Request History</a></div>
                    <form class="form-horizontal" action="<?php echo e(url('payment-request')); ?>" style='width: 55%;' method="post"
                          onSubmit="return sendFundBorrow()">
                        <?php if($message = Session::get('success')): ?>
                            <div class="alert alert-success alert-block">
                                <button type="button" class="close" data-dismiss="alert">×</button>
                                <strong><?php echo e($message); ?></strong>
                            </div>
                        <?php endif; ?>


                        <?php if($message = Session::get('error')): ?>
                            <div class="alert alert-danger alert-block">
                                <button type="button" class="close" data-dismiss="alert">×</button>
                                <strong><?php echo e($message); ?></strong>
                            </div>
                        <?php endif; ?>


                        <?php if($message = Session::get('warning')): ?>
                            <div class="alert alert-warning alert-block">
                                <button type="button" class="close" data-dismiss="alert">×</button>
                                <strong><?php echo e($message); ?></strong>
                            </div>
                        <?php endif; ?>


                        <?php if($message = Session::get('info')): ?>
                            <div class="alert alert-info alert-block">
                                <button type="button" class="close" data-dismiss="alert">×</button>
                                <strong><?php echo e($message); ?></strong>
                            </div>
                        <?php endif; ?>


                        <?php if($errors->any()): ?>
                            <div class="alert alert-danger">
                                <button type="button" class="close" data-dismiss="alert">×</button>
                                Please check the form below for errors
                            </div>
                        <?php endif; ?>
                        <?php echo e(csrf_field()); ?>


                        <div class="form-group row">
                            <label class="col-sm-4 col-form-label text-right" for="amount">Approver Details:</label>
                            <div class="col-sm-8">
                                <select class="form-control" name="requestTo" id="requestTo">
                                    <option value="3"><?php echo e(Auth::user()->parent->name); ?> (Mobile : <?php echo e(Auth::user()->parent->mobile); ?>

                                        )
                                    </option>
                                </select>
                            </div>
                        </div>


                        <div class="form-group row">
                            <label class="col-sm-4 col-form-label text-right" for="bAmount">Amount:</label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" id="bAmount" name="camount" placeholder="Amount">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-4 col-form-label text-right" for="bPaymentDate">Payment Date:</label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control customDatepicker" id="bPaymentDate" name="cpaymentDate" value="<?php echo e(date('d-m-Y')); ?>">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-4 col-form-label text-right" for="bBorrowType">Borrow Type:</label>
                            <div class="col-sm-8">
                                <select class="form-control" id="bBorrowType" name="borrow_type">
                                    <option value="1">Take Borrow</option>
                                    <option value="2">Pay Off</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-sm-4 col-form-label text-right" for="bRemark">Remark:</label>
                            <div class="col-sm-8">
                                <textarea class="form-control" id="bRemark" name="cremark" placeholder="Remark"></textarea>

                            </div>
                        </div>

                        <div class="form-group">
                            <div class="text-center">
                                <button type="submit" class="btn btn-outline-success">Submit</button>
                            </div>
                        </div>
                    </form>

                </div>

                <div class="row tab-pane fade" id="borrowRequest" style="background-color: #fff"><h2 style="padding: 100px">Coming soon...</h2></div>

            </div>
        </div>
        <div class="col-sm-6 ">
            <a href="<?php echo e(url('payment-request-report')); ?>"> Payment Request History</a>
			<div class="box" style="overflow-x: scroll;">
            <table class="table table-bordered">
                <thead>
                    <th>ID</th>
                    <th>Bank Name</th>
                    <th>Mode</th>
                    <th>Amount</th>
					<th>Remark</th>
                    <th>Status</th>
                </thead>
                <tbody>
                    <?php $__currentLoopData = $loadcashes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $loadcash): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr class="<?php echo e($loadcash->status->status); ?>-text">
                        <td><?php echo e($loadcash->id); ?></td>
                        <td><?php echo e(($loadcash->request_to == 1) ? @$loadcash->bank_name :@$loadcash->netbank->bank_name); ?></td>
                        <td><?php echo e($loadcash->payment_mode); ?></td>
                        <td><?php echo e($loadcash->amount); ?></td>
						<td><?php echo e($loadcash->request_remark); ?></td>
                        <td><?php echo e(@$loadcash->status->status); ?></td>
                    </tr>
                   <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
            </table></div>
        </div>     
    </div>
<input type="hidden" id="cashDepositCharge" value="<?php echo e(Auth::user()->balance->cash_deposit_charge); ?>"/>
<input type="hidden" id="cashDepositMinCharge" value="<?php echo e(Auth::user()->balance->cash_deposit_min_charge); ?>"/>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('admin.layouts.templatetable', \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>