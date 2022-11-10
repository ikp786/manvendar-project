


<?php $__env->startSection('content'); ?>
 <style type="text/css">
 .hideTxtField{
	 display:none;
 }
    </style>

<head>
	<script src="https://code.jquery.com/jquery-2.1.1.min.js" type="text/javascript"></script>
	<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.1/css/select2.min.css" rel="stylesheet" />
	<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.1/js/select2.min.js"></script>
	<script type="text/javascript">
		$(document).ready(function() {
			$.noConflict();
			var country = [];
			$("#bankName").select2({
			  data: country
			});
		});
	</script>
	</head>

        <table style="100%">
            <tr>
                <td width="20%">
                    <table style="border: 1;"class="hideTxtField">
                        <tr>
                            <td>
                                Initialized Framework
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <input class="btn btn-primary btn-200" onclick="discoverAvdm();" type="button" value="Discover AVDM">
                            </td>
                        </tr>
                        <tr class="hideTxtField">
                            <td>
                                <input class="btn btn-primary btn-200" onclick="deviceInfoAvdm();" type="button"
                                    value="Device Info">
                            </td>
                        </tr>
                    </table>
                </td>
                <td width="55%">
                    <table style="border: 1;" class="hideTxtField">
                        <tr>
                            <td>
                                Select Option to Capture
                            </td>
                        </tr>
                        <tr>
                            <td>
                                AVDM
                            </td>
                            <td colspan="6px">
                                <select id="ddlAVDM" class="form-control" style="width: 100%;">
                                    <option></option>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                Timeout
                            </td>
                            <td>
                                <select id="Timeout" class="form-control">
                                    <option>10000</option>
                                    <option>10000</option>
                                    <option>20000</option>
                                    <option>30000</option>
                                    <option>40000</option>
                                    <option>50000</option>
                                    <option>60000</option>
                                    <option>70000</option>
                                    <option>80000</option>
                                    <option>90000</option>
                                    <option>100000</option>
                                    <option>0</option>
                                </select>
                            </td>
                            <td>
                                PidVer
                            </td>
                            <td width="60px">
                                <select id="Pidver" class="form-control">
                                    <option>2.0</option>
                                </select>
                            </td>
                            <td>
                                Env
                            </td>
                            <td width="60px">
                                <select id="Env" class="form-control">
                                    <option>P</option>
                                    <option selected="true">P</option>
                                    <option>P</option>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                Wadh
                            </td>
                            <td colspan="4px">
                                <textarea id="txtWadh" style="width: 100%; height: 50px;" class="form-control"> </textarea>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                DataType
                            </td>
                            <td colspan="4px">
                                <select id="Dtype" style="width: 45px;" class="form-control">
                                    <option value="0">X</option>
                                    <option value="1">P</option>
                                </select>
                            </td>
                        </tr>
                    </table>
                </td>
                <td width="40%">
                    <table style="border: 1;" class="hideTxtField">
                        <tr>
                            <td>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                Finger Count
                            </td>
                            <td>
                                <select id="Fcount" class="form-control">
								  <option>0</option>
                                    <option selected="selected">1</option>
                                    <option>2</option>
                                    <option>3</option>
                                    <option>4</option>
                                    <option>5</option>
                                    <option>6</option>
                                    <option>7</option>
                                    <option>8</option>
                                    <option>9</option>
                                    <option>10</option>
                                </select>
                            </td>
                            <td>
                                Finger Type
                            </td>
                            <td>
                                <select id="Ftype" class="form-control">
                                    <option value="0">FMR</option>
                                    <option value="1">FIR</option>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                Iris Count
                            </td>
                            <td>
                                <select id="Icount" class="form-control">
                                    <option>0</option>
                                    <option>1</option>
                                    <option>2</option>
                                </select>
                            </td>
                            <td>
                                Iris Type
                            </td>
                            <td>
                                <select id="Itype" class="form-control">
                                    <option>SELECT</option>
                                    <option>ISO</option>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                Face Count
                            </td>
                            <td>
                                <select id="Pcount" class="form-control">
                                    <option>0</option>
                                    <option>1</option>
                                </select>
                            </td>
                            <td>
                                Face Type
                            </td>
                            <td>
                                <select id="Ptype" class="form-control">
                                    <option>SELECT</option>
                                </select>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
        <table class="hideTxtField">
            <tbody>
                <tr>
                    <td>
                        <strong>PERSONAL IDENTITY(PI)</strong><br />
                        <table border="1" width="600px">
                            <tbody>
                                <tr>
                                    <td style="text-align: right;">
                                        Name:
                                    </td>
                                    <td>
                                        <input id="txtName" type="text" />
                                    </td>
                                    <td style="text-align: right;">
                                        Match Value:
                                    </td>
                                    <td>
                                        <select id="drpMatchValuePI" class="form-control">
                                        </select>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="text-align: right;">
                                        Match Strategy:
                                    </td>
                                    <td>
                                        <input name="RDPI" id="rdExactPI" checked="true" type="radio">Exact</input>
                                        <input name="RDPI" id="rdPartialPI" type="radio">Partial</input>
                                        <input name="RDPI" id="rdFuzzyPI" type="radio">Fuzzy</input>
                                    </td>
                                    <td style="text-align: right;">
                                        Age:
                                    </td>
                                    <td>
                                        <input id="txtAge" type="text" />
                                    </td>
                                </tr>
                                <tr>
                                    <td style="text-align: right;">
                                        Local Name:
                                    </td>
                                    <td>
                                        <input id="txtLocalNamePI" type="text" />
                                    </td>
                                    <td style="text-align: right;">
                                        LocalMatchValue:
                                    </td>
                                    <td>
                                        <select id="drpLocalMatchValuePI">
                                        </select>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="text-align: right;">
                                        DOB:
                                    </td>
                                    <td>
                                        <input id="txtDOB" type="text" />
                                    </td>
                                    <td style="text-align: right;">
                                        Gender:
                                    </td>
                                    <td>
                                        <select id="drpGender" class="form-control">
                                            <option value="0">Select</option>
                                            <option>MALE</option>
                                            <option>FEMALE</option>
                                            <option>TRANSGENDER</option>
                                        </select>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="text-align: right;">
                                        Phone:
                                    </td>
                                    <td>
                                        <input id="txtPhone" type="text" />
                                    </td>
                                    <td style="text-align: right;">
                                        Email:
                                    </td>
                                    <td>
                                        <input id="txtEmail" type="text" />
                                    </td>
                                </tr>
                                <tr>
                                    <td style="text-align: right;">
                                        DOB Type:
                                    </td>
                                    <td>
                                        <select id="drpDOBType" class="form-control">
                                            <option value="0">select</option>
                                            <option>V</option>
                                            <option>D</option>
                                            <option>A</option>
                                        </select>
                                    </td>
                                    <td style="text-align: right;">
                                    </td>
                                    <td>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </td>
                    <td>
                        <strong>PERSONAL ADDRESS(PA)</strong><br />
                        <table border="1" width="600px">
                            <tbody>
                                <tr>
                                    <td style="text-align: right;">
                                        Care Of:
                                    </td>
                                    <td>
                                        <input id="txtCareOf" type="text" />
                                    </td>
                                    <td style="text-align: right;">
                                        Building:
                                    </td>
                                    <td>
                                        <input id="txtBuilding" type="text" />
                                    </td>
                                </tr>
                                <tr>
                                    <td style="text-align: right;">
                                        Landmark:
                                    </td>
                                    <td>
                                        <input id="txtLandMark" type="text" />
                                    </td>
                                    <td style="text-align: right;">
                                        Street:
                                    </td>
                                    <td>
                                        <input id="txtStreet" type="text" />
                                    </td>
                                </tr>
                                <tr>
                                    <td style="text-align: right;">
                                        Locality:
                                    </td>
                                    <td>
                                        <input id="txtLocality" type="text" />
                                    </td>
                                    <td style="text-align: right;">
                                        PO Name:
                                    </td>
                                    <td>
                                        <input id="txtPOName" type="text" />
                                    </td>
                                </tr>
                                <tr>
                                    <td style="text-align: right;">
                                        City:
                                    </td>
                                    <td>
                                        <input id="txtCity" type="text" />
                                    </td>
                                    <td style="text-align: right;">
                                        Sub Dist:
                                    </td>
                                    <td>
                                        <input id="txtSubDist" type="text" />
                                    </td>
                                </tr>
                                <tr>
                                    <td style="text-align: right;">
                                        District:
                                    </td>
                                    <td>
                                        <input id="txtDist" type="text" />
                                    </td>
                                    <td style="text-align: right;">
                                        State:
                                    </td>
                                    <td>
                                        <input id="txtState" type="text" />
                                    </td>
                                </tr>
                                <tr>
                                    <td style="text-align: right;">
                                        PinCode:
                                    </td>
                                    <td>
                                        <input id="txtPinCode" type="text" />
                                    </td>
                                    <td style="text-align: right;">
                                        Match Strategy:
                                    </td>
                                    <td>
                                        <input id="rdMatchStrategyPA" checked="true" type="radio">Exact</input>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </td>
                </tr>
            </tbody>
        </table>
        <table border="1" width="1200px" class="hideTxtField">
            <tbody>
                <tr>
                    <td colspan="6">
                        <strong>PERSONAL FULL ADDRESS(PFA)</strong>
                    </td>
                </tr>
                <tr>
                    <td style="text-align: right;">
                        Match Strategy:
                    </td>
                    <td>
                        <input name="RD" id="rdExactPFA" checked="true" type="radio">Exact</input>
                        <input name="RD" id="rdPartialPFA" type="radio">Partial </input>
                        <input name="RD" id="rdFuzzyPFA" type="radio">Fuzzy</input>
                    </td>
                    <td style="text-align: right;">
                        Match Value:
                    </td>
                    <td>
                        <select id="drpMatchValuePFA">
                        </select>
                    </td>
                    <td style="text-align: right;">
                        Local Match Value:
                    </td>
                    <td>
                        <select id="drpLocalMatchValue">
                        </select>
                    </td>
                </tr>
                <tr>
                    <td style="text-align: right;">
                        Address Value:
                    </td>
                    <td colspan="3">
                        <textarea id="txtAddressValue" style="width: 100%; height: 50px;" class="form-control"></textarea>
                    </td>
                    <td style="text-align: right;">
                        Local Address:
                    </td>
                    <td colspan="3">
                        <textarea id="txtLocalAddress" style="width: 100%; height: 50px;" class="form-control"></textarea>
                    </td>
                </tr>
            </tbody>
        </table>
        <table style="border: 1; width: 20%" class="hideTxtField">
            <tr>
                <td>
                    <input type="button" class="btn btn-primary btn-200" onclick="CaptureAvdm();" value="Capture" />
                </td>
                <td>
                    <input type="button" class="btn btn-primary btn-200" onclick="reset();" value="Reset" />
                </td>
                <!-- <td><input type="button" class="btn btn-primary btn-200" onclick="Demo();" value="Auth"/></td> -->
            </tr>
        </table>
		 <?php echo $__env->make('agent.aepsSettlement.aepsSettlement-type', \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>
		<div class="hideTxtField">
        <label>
            avdm/device info</label>
        <textarea id="txtDeviceInfo" style="width: 100%; height: 160px;" class="form-control"> </textarea>
        <label>
            pid options</label>
        <textarea id="txtPidOptions" style="width: 100%; height: 100px;" class="form-control"> </textarea>
		
			<label>
				pid data
			</label>
        <textarea id="txtPidDataT" style="width: 100%; height: 150px;" class="form-control"> </textarea>
        <label id="lblstatus">
		
        </label>
		</div>
		<div >
			<?php echo Form::open(array('url' =>'#','id'=>'myImageForm')); ?>

		<div class="form-group col-md-12 row">
							<div class="col-md-12">
                            <label for="inputTask" class="control-label">Biometric Device</label>
                            <div class="">
                                <select name="deviceName" id="deviceName" class="form-control" onChange="loadDevicepage()">
									<option value="MANTRA" selected>MANTRA</option>
									<option value="MORPHO" >MORPHO</option>
                                </select>
                            </div>
							</div>
							
                        </div> 
						<div class="form-group col-md-12 row">
							
							<div class="col-md-12">
							<label for="inputTask" class="control-label">Bank Name</label>
                            <div class="">
                                <?php echo e(Form::select('bankName', $bankLists,null, ['id'=>'bankName','class'=>'form-control ','placeholder'=>'-- Select Bank--'])); ?>

                            </div>
							</div>
							<input type="hidden" name="selectedBankName" id="selectedBankName"/>
                        </div>
						<div class="form-group col-md-12 row">
							<div class="col-md-12">
								<label class="control-label" for="email">Service:</label>
								<div class="">
									<select class="browser-default form-control" id="transactionType" name="transactionType" onChange="openBalanceDiv()">
									  <option value="BE">Enquiry</option>
									  <option value="CW">Widthdrawn</option>
								</select>
								</div>
							</div>
						</div>
						<div class="form-group col-md-12 row" style="display:none" id="balanceDiv">
						<div class="col-md-12">
							<label class="control-label" for="email">Amount</label>
							<div class="">
								<input type="text" class="form-control" id="amount" name="amount" placeholder="Enter Min Amount 100" value="0">
							</div>
							</div>
						</div>						
						<div class="form-group col-md-12 row">
							<div class="col-md-12">
                            <label for="inputTask" class="control-label">AadhaarNumber</label>
                            <div class="">
								<?php if(Auth::id()==4): ?>
									<input name="aadhaarNumber" type="text" class="form-control" id="aadhaarNumber" value="448014574212" placeholder="Enter Aadhaar Number" maxlength="12"/>
								<?php else: ?>
									<input name="aadhaarNumber" type="text" class="form-control" id="aadhaarNumber" value="" placeholder="Enter Aadhaar Number" maxlength="12"/>
								<?php endif; ?>
                            </div>
							</div>
							
                        </div>
						<div class="form-group col-md-12 row">
							<div class="col-md-12">
                            <label for="inputTask" class="control-label">Customer Number</label>
                            <div class="">
							<?php if(Auth::id()==4): ?>
								<input name="customerNumber" type="text" class="form-control" id="customerNumber" value="8285540407" placeholder="Enter Customer Number" maxlength="10">
							<?php else: ?>
							
								<input name="customerNumber" type="text" class="form-control" id="customerNumber" value="" placeholder="Enter Customer Number" maxlength="10">
								<?php endif; ?>
                            </div>
							</div>
							
                        </div> 
						 <textarea id="txtPidData" name="txtPidData"style="width: 100%; height: 150px;display:none" class="form-control" > </textarea>
						<div class="form-group col-md-12 row">
							<div class="col-md-12">
							<!-- <input class="btn btn-primary btn-200" onclick="discoverAvdm();" type="button" id="checkDeviceBank" value="Check Your Device">
							 <img src="<?php echo e(url('loader/loader.gif')); ?>" id="checkDeviceImage" class="loaderImg" style="display: none;">-->
							 <div id="scanDeviceDiv" style="display:block">
								<input type="button" class="btn btn-success btn-200" onclick="Capture();" value="Scan Finger" id="scanDeviceBtn"/>
								<img src="<?php echo e(url('img/newLoader.gif')); ?>" id="scanDeviceImage" class="loaderImg" style="display: none;">
								<img src="<?php echo e(url('img/scan_fp.gif')); ?>" class="" style="display: none;width: 15%;
    border-radius: 15%;" id="captureDeviceLoader" alt="Image">
								<img src="<?php echo e(url('img/check-circle.gif')); ?>" class="" style="display: none;" id="captureDeviceSuccessLoader" alt="Image">
								<a data-toggle="modal" href="#myReciept" class="table-action-btn" id="printAnchorSlip" style="display:none">Action</a>
							</div>
							 <!--<div id="scanDeviceDiv" style="display:none">
								<input type="button" class="btn btn-success btn-200" onclick="CaptureAvdm();" value="Scan Finger" id="scanDeviceBtn"/>
								<img src="<?php echo e(url('loader/loader.gif')); ?>" id="scanDeviceImage" class="loaderImg" style="display: none;">
							</div>-->
							<div class="scanDeviceDiv">
							
                        </div> 						
						</div>
						</div></div>
    <script language="javascript" type="text/javascript">

	$(document).ready(function (){ 	

			$('#closemodal').click(function() {
			location.reload();
});
			});
		var GetPIString='';
		var GetPAString='';
		var GetPFAString='';
		var DemoFinalString='';
		var select = '';
		select += '<option val=0>Select</option>';
		for (i=1;i<=100;i++){
			select += '<option val=' + i + '>' + i + '</option>';
		}
		$('#drpMatchValuePI').html(select);
		$('#drpMatchValuePFA').html(select);
		$('#drpLocalMatchValue').html(select);
		$('#drpLocalMatchValuePI').html(select);

		var finalUrl="";
		var MethodInfo="";
		var MethodCapture="";
		var OldPort=false;


function slipAmountInWord()
	{
		var amount=$("#amount").val();		
		var txnChargeAmout=$("#txnChargeAmout").val();	
		var amountFormat = /^[0-9]+$/;
		if(!txnChargeAmout.match(amountFormat) || txnChargeAmout =='')
		{
			$('#txnChargeAmout').focus();
			$('#slipAmountInWord').css('color','red');
			$('#slipAmountInWord').text('Please Enter Correct Amount');
			return false;
		}		
		var finalAmount = +amount + +txnChargeAmout;
		$.ajax({
                type: "get",
                url: "<?php echo e(url('amount-in-words')); ?>",
                data: "amount="+finalAmount,
				dataType:"json",
				beforeSend:function(){
					
					$("#updageFeeSpan").hide();
					
				},
                success: function (msg) 
				{
					$('#slipAmountInWord').css('color','black');
					console.log(msg);
					//$("#spanChargeAmount").text(txnChargeAmout);
					$("#slipAmountInWord").text(msg);
					$("#spanChargeAmount").text(finalAmount);
					
				}
            });
		
	}
 function printDiv() 
{

	  var divToPrint=document.getElementById('dvContents');

	  var newWin=window.open('','Print-Window');

	  newWin.document.open();

	  newWin.document.write('<html><body onload="window.print()">'+divToPrint.innerHTML+'</body></html>');

	  newWin.document.close();

	  setTimeout(function(){
		  newWin.close();
		  
	},10);

}
function openBalanceDiv()
		{
			if($("#transactionType").val() == "BE")
				$("#balanceDiv").hide();
			else
				$("#balanceDiv").show();
		}
function loadDevicepage()
		{
			if($("#deviceName").val() == "MANTRA")
				window.location.replace("<?php echo e(url('aeps')); ?>");
			else
				window.location.replace("<?php echo e(url('morpho-aeps')); ?>");
		}
		function test()
		{
			alert("I am calling..");
		}

		function reset()
		{
			$('#txtWadh').val('');
		    $('#txtDeviceInfo').val('');
			$('#txtPidOptions').val('');
			$('#txtPidData').val('');
		    $("select#ddlAVDM").prop('selectedIndex', 0);
		    $("select#Timeout").prop('selectedIndex', 0);
			$("select#Icount").prop('selectedIndex', 0);
			$("select#Fcount").prop('selectedIndex', 0);
			$("select#Icount").prop('selectedIndex', 0);
			$("select#Itype").prop('selectedIndex', 0);
			$("select#Ptype").prop('selectedIndex', 0);
			$("select#Ftype").prop('selectedIndex', 0);
			$("select#Dtype").prop('selectedIndex', 0);
		}
		// All New Function

		function Demo()
		{

		var GetPIStringstr='';
		var GetPAStringstr='';
		var GetPFAStringstr='';

			if(GetPI()==true)
			{
				GetPIStringstr ='<Pi '+GetPIString+' />';
				//alert(GetPIStringstr);
			}
			else
			{
				GetPIString='';
			}

			if(GetPA()==true)
			{
				GetPAStringstr ='<Pa '+GetPAString+' />';
				//alert(GetPAStringstr);
			}
			else
			{
				GetPAString='';
			}

			if(GetPFA()==true)
			{
				GetPFAStringstr ='<Pfa '+GetPFAString+' />';
				//alert(GetPFAStringstr);
			}
			else
			{
				GetPFAString='';
			}

			if(GetPI()==false && GetPA()==false && GetPFA()==false)
			{
				//alert("Fill Data!");
				DemoFinalString='';
			}
			else
			{
				DemoFinalString = '<Demo>'+ GetPIStringstr +' ' + GetPAStringstr + ' ' + GetPFAStringstr + ' </Demo>';
				//alert(DemoFinalString)
			}
		}

		function GetPI()
		{
			var Flag=false;
			GetPIString='';

			 if ($("#txtName").val().trim().length > 0)
            {
                Flag = true;
                GetPIString += "name="+ "\""+$("#txtName").val().trim()+"\"";
            }

            if ($("#drpMatchValuePI").val() > 0 && Flag)
            {
                Flag = true;
				GetPIString += " mv="+ "\""+$("#drpMatchValuePI").val().trim()+"\"";
            }

			if ($('#rdExactPI').is(':checked') && Flag)
            {
                Flag = true;
                GetPIString += " ms="+ "\"E\"";
            }
            else if ($('#rdPartialPI').is(':checked') && Flag)
            {
                Flag = true;
               GetPIString += " ms="+ "\"P\"";
            }
            else if ($('#rdFuzzyPI').is(':checked') && Flag)
            {
                Flag = true;
                GetPIString += " ms="+ "\"F\"";
            }
			if ($("#txtLocalNamePI").val().trim().length > 0)
            {
				Flag = true;
                GetPIString += " lname="+ "\""+$("#txtLocalNamePI").val().trim()+"\"";
            }

			if ($("#txtLocalNamePI").val().trim().length > 0 && $("#drpLocalMatchValuePI").val() > 0)
            {
				Flag = true;
				GetPIString += " lmv="+ "\""+$("#drpLocalMatchValuePI").val().trim()+"\"";
            }

            <!-- if ($("#drpGender").val() > 0) -->
            <!-- { -->

                if ($("#drpGender").val().trim() == "MALE")
                {
                    Flag = true;
					 GetPIString += " gender="+ "\"M\"";
                }
                else if ($("#drpGender").val().trim() == "FEMALE")
                {
                    Flag = true;
                     GetPIString += " gender="+ "\"F\"";
                }
                else if ($("#drpGender").val().trim() == "TRANSGENDER")
                {
                    Flag = true;
                   GetPIString += " gender="+ "\"T\"";
                }
            //}
			    if ($("#txtDOB").val().trim().length > 0 )
				{
					Flag = true;
					GetPIString += " dob="+ "\""+$("#txtDOB").val().trim()+"\"";
				}

				if ($("#drpDOBType").val() != "0")
				{
					Flag = true;
					GetPIString += " dobt="+ "\""+$("#drpDOBType").val().trim()+"\"";
				}

				if ($("#txtAge").val().trim().length)
				{
					Flag = true;
					GetPIString += " age="+ "\""+$("#txtAge").val().trim()+"\"";
				}

				if ($("#txtPhone").val().trim().length > 0 || $("#txtEmail").val().trim().length > 0)
				{
					Flag = true;
					GetPIString += " phone="+ "\""+$("#txtPhone").val().trim()+"\"";
				}
				if ($("#txtEmail").val().trim().length > 0)
				{
					Flag = true;
					GetPIString += " email="+ "\""+$("#txtEmail").val().trim()+"\"";
				}

			//alert(GetPIString);
			return Flag;
		}


		function GetPA()
		{
			var Flag=false;
			GetPAString='';

			if ($("#txtCareOf").val().trim().length > 0)
            {
				Flag = true;
                GetPAString += "co="+ "\""+$("#txtCareOf").val().trim()+"\"";
            }
            if ($("#txtLandMark").val().trim().length > 0 )
            {
                Flag = true;
                GetPAString += " lm="+ "\""+$("#txtLandMark").val().trim()+"\"";
            }
            if ($("#txtLocality").val().trim().length > 0 )
            {
               Flag = true;
                GetPAString += " loc="+ "\""+$("#txtLocality").val().trim()+"\"";
            }
            if ($("#txtCity").val().trim().length > 0 )
            {
                Flag = true;
                GetPAString += " vtc="+ "\""+$("#txtCity").val().trim()+"\"";
            }
            if ($("#txtDist").val().trim().length > 0 )
            {
                Flag = true;
                GetPAString += " dist="+ "\""+$("#txtDist").val().trim()+"\"";
            }
            if ($("#txtPinCode").val().trim().length > 0 )
            {
                Flag = true;
                GetPAString += " pc="+ "\""+$("#txtPinCode").val().trim()+"\"";
            }
            if ($("#txtBuilding").val().trim().length > 0 )
            {
                 Flag = true;
                GetPAString += " house="+ "\""+$("#txtBuilding").val().trim()+"\"";
            }
            if ($("#txtStreet").val().trim().length > 0 )
            {
                 Flag = true;
                GetPAString += " street="+ "\""+$("#txtStreet").val().trim()+"\"";
            }
            if ($("#txtPOName").val().trim().length > 0 )
            {
                 Flag = true;
                GetPAString += " po="+ "\""+$("#txtPOName").val().trim()+"\"";
            }
            if ($("#txtSubDist").val().trim().length > 0 )
            {
                  Flag = true;
                GetPAString += " subdist="+ "\""+$("#txtSubDist").val().trim()+"\"";
            }
            if ($("#txtState").val().trim().length > 0)
            {
                 Flag = true;
                GetPAString += " state="+ "\""+$("#txtState").val().trim()+"\"";
            }
            if ( $('#rdMatchStrategyPA').is(':checked') && Flag)
            {
                Flag = true;
                GetPAString += " ms="+ "\"E\"";
            }
			//alert(GetPIString);
			return Flag;
		}



		function GetPFA()
		{
			var Flag=false;
			GetPFAString='';

			if ($("#txtAddressValue").val().trim().length > 0)
            {
				Flag = true;
                GetPFAString += "av="+ "\""+$("#txtAddressValue").val().trim()+"\"";
            }

			if ($("#drpMatchValuePFA").val() > 0 && $("#txtAddressValue").val().trim().length > 0)
            {
                Flag = true;
				GetPFAString += " mv="+ "\""+$("#drpMatchValuePFA").val().trim()+"\"";
            }

			if ($('#rdExactPFA').is(':checked') && Flag)
            {
                Flag = true;
                GetPFAString += " ms="+ "\"E\"";
            }
            else if ($('#rdPartialPFA').is(':checked') && Flag)
            {
                Flag = true;
               GetPFAString += " ms="+ "\"P\"";
            }
            else if ($('#rdFuzzyPFA').is(':checked') && Flag)
            {
                Flag = true;
                GetPFAString += " ms="+ "\"F\"";
            }

			if ($("#txtLocalAddress").val().trim().length > 0)
            {
				Flag = true;
                GetPFAString += " lav="+ "\""+$("#txtLocalAddress").val().trim()+"\"";
            }

			if ($("#drpLocalMatchValue").val() > 0 && $("#txtLocalAddress").val().trim().length > 0)
            {
                Flag = true;
				GetPFAString += " lmv="+ "\""+$("#drpLocalMatchValue").val().trim()+"\"";
            }
			//alert(GetPIString);
			return Flag;
		}

		$( "#ddlAVDM" ).change(function() {
		//alert($("#ddlAVDM").val());
		discoverAvdmFirstNode($("#ddlAVDM").val());
		});


		$( "#chkHttpsPort" ).change(function() {
		    if($("#chkHttpsPort").prop('checked')==true)
		    {
		        OldPort=true;
		    }
		    else
		    {
		        OldPort=false;
		    }

		});

		function discoverAvdmFirstNode(PortNo)
		{

			$('#txtWadh').val('');
		    $('#txtDeviceInfo').val('');
			$('#txtPidOptions').val('');
			$('#txtPidData').val('');

		//alert(PortNo);

		var primaryUrl = "http://127.0.0.1:";
            url = "";
					 var verb = "RDSERVICE";
                        var err = "";
						var res;
						$.support.cors = true;
						var httpStaus = false;
						var jsonstr="";
						 var data = new Object();
						 var obj = new Object();

							$.ajax({
							type: "RDSERVICE",
							async: false,
							crossDomain: true,
							url: primaryUrl + PortNo,
							contentType: "text/xml; charset=utf-8",
							processData: false,
							cache: false,
							async:false,
							crossDomain:true,
							success: function (data) {
								httpStaus = true;
								res = { httpStaus: httpStaus, data: data };
							    //alert(data);

								//debugger;

								 $("#txtDeviceInfo").val(data);

								var $doc = $.parseXML(data);

								//alert($($doc).find('Interface').eq(1).attr('path'));


								if($($doc).find('Interface').eq(0).attr('path')=="/rd/capture")

								{
								  MethodCapture=$($doc).find('Interface').eq(0).attr('path');
								}
								if($($doc).find('Interface').eq(1).attr('path')=="/rd/capture")

								{
								  MethodCapture=$($doc).find('Interface').eq(1).attr('path');
								}

								if($($doc).find('Interface').eq(0).attr('path')=="/rd/info")

								{
								  MethodInfo=$($doc).find('Interface').eq(0).attr('path');
								}
								if($($doc).find('Interface').eq(1).attr('path')=="/rd/info")

								{
								  MethodInfo=$($doc).find('Interface').eq(1).attr('path');
								}

								<!-- MethodInfo=$($doc).find('Interface').eq(0).attr('path'); -->
								<!-- MethodCapture=$($doc).find('Interface').eq(1).attr('path'); -->

								 alert("RDSERVICE Discover Successfully");
							},
							error: function (jqXHR, ajaxOptions, thrownError) {
							$('#txtDeviceInfo').val("");
							//alert(thrownError);
								res = { httpStaus: httpStaus, err: getHttpError(jqXHR) };
							},
						});

						return res;
		}








		function discoverAvdm()
		{

            <!-- ddlAVDM.empty(); -->

			// New

			openNav();


			$('#txtWadh').val('');
		    $('#txtDeviceInfo').val('');
			$('#txtPidOptions').val('');
			$('#txtPidData').val('');

			//


			var SuccessFlag=0;
            var primaryUrl = "http://127.0.0.1:";

						 try {
							 var protocol = window.location.href;
							 if (protocol.indexOf("https") >= 0) {
								primaryUrl = "https://127.0.0.1:";
							}
						 } catch (e)
						{ }


            url = "";
			 $("#ddlAVDM").empty();
			//alert("Please wait while discovering port from 11100 to 11120.\nThis will take some time.");
			    for (var i = 11100; i <= 11120; i++)
                {
					if(primaryUrl=="https://127.0.0.1:" && OldPort==true)
					{
					   i="8005";
					}
				    $("#lblStatus1").text("Discovering RD service on port : " + i.toString());

						var verb = "RDSERVICE";
                        var err = "";
						SuccessFlag=0;
						var res;
						$.support.cors = true;
						var httpStaus = false;
						var jsonstr="";
						 var data = new Object();
						 var obj = new Object();



							$.ajax({

							type: "RDSERVICE",
							async: false,
							crossDomain: true,
							url: primaryUrl + i.toString(),
							contentType: "text/xml; charset=utf-8",
							processData: false,
							cache: false,
							crossDomain:true,
							beforeSend:function()
							{
								$("#checkDeviceBank").hide();
								$("#checkDeviceImage").show();
							},
							success: function (data) {

								httpStaus = true;
								res = { httpStaus: httpStaus, data: data };
							    //alert(data);
								finalUrl = primaryUrl + i.toString();
								var $doc = $.parseXML(data);
								var CmbData1 =  $($doc).find('RDService').attr('status');
								var CmbData2 =  $($doc).find('RDService').attr('info');
								
								if(CmbData1 == "NOTREADY")
								{
									alert("Please Plug your aeps Device");
									$("#checkDeviceBank").show();
									$("#checkDeviceImage").hide();
									return false;
								}
								if(RegExp('\\b'+ 'Mantra' +'\\b').test(CmbData2)==true)
								{
								    closeNav();
									$("#txtDeviceInfo").val(data);

									if($($doc).find('Interface').eq(0).attr('path')=="/rd/capture")
									{
									  MethodCapture=$($doc).find('Interface').eq(0).attr('path');
									}
									if($($doc).find('Interface').eq(1).attr('path')=="/rd/capture")
									{
									  MethodCapture=$($doc).find('Interface').eq(1).attr('path');
									}
									if($($doc).find('Interface').eq(0).attr('path')=="/rd/info")
									{
									  MethodInfo=$($doc).find('Interface').eq(0).attr('path');
									}
									if($($doc).find('Interface').eq(1).attr('path')=="/rd/info")
									{
									  MethodInfo=$($doc).find('Interface').eq(1).attr('path');
									}

									$("#ddlAVDM").append('<option value='+i.toString()+'>(' + CmbData1 +')'+CmbData2+'</option>')
									SuccessFlag=1;
									alert("RDSERVICE Discover Successfully");
									$("#checkDeviceBank").hide();
									$("#checkDeviceImage").hide();
									$("#scanDeviceDiv").show();
									return;

								}

								//alert(CmbData1);
								//alert(CmbData2);

							},
							error: function (jqXHR, ajaxOptions, thrownError) {
							if(i=="8005" && OldPort==true)
							{
								OldPort=false;
								i="11099";
							}
							$('#txtDeviceInfo').val("");
							//alert(thrownError);

								//res = { httpStaus: httpStaus, err: getHttpError(jqXHR) };
							},

						});



						if(SuccessFlag==1)
						{
						  break;
						}

						//$("#ddlAVDM").val("0");

                }

				if(SuccessFlag==0)
				{
				 alert("Connection failed Please try again.");
				}

				$("select#ddlAVDM").prop('selectedIndex', 0);

				//$('#txtDeviceInfo').val(DataXML);

				<!-- var PortVal= $('#ddlAVDM').val($('#ddlAVDM').find('option').first().val()).val(); -->

				<!-- if(PortVal>11099) -->
				<!-- { -->

				   <!-- discoverAvdmFirstNode(PortVal); -->
				<!-- } -->
				closeNav();
				return res;
		}


		function openNav() {
			<!-- document.getElementById("myNav").style.width = "100%"; -->
		}

		function closeNav() {
			<!-- document.getElementById("myNav").style.width = "0%"; -->
		}

		function deviceInfoAvdm()
		{
			//alert($("#ddlAVDM").val());
            <!-- ddlAVDM.empty(); -->





            url = "";

					<!-- alert(i.toString()); -->
                    // $("#lblStatus").text("Discovering RD Service on Port : " + i.toString());
					//Dynamic URL

						finalUrl = "http://127.0.0.1:" + $("#ddlAVDM").val();

						try {
							var protocol = window.location.href;
							if (protocol.indexOf("https") >= 0) {
								finalUrl = "https://127.0.0.1:" + $("#ddlAVDM").val();
							}
						} catch (e)
						{ }

					//
					 var verb = "DEVICEINFO";
                      //alert(finalUrl);

                        var err = "";

						var res;
						$.support.cors = true;
						var httpStaus = false;
						var jsonstr="";
						;
							$.ajax({

							type: "DEVICEINFO",
							async: false,
							crossDomain: true,
							url: finalUrl+MethodInfo,
							contentType: "text/xml; charset=utf-8",
							processData: false,
							success: function (data) {
							//alert(data);
								httpStaus = true;
								res = { httpStaus: httpStaus, data: data };

								$('#txtDeviceInfo').val(data);
							},
							error: function (jqXHR, ajaxOptions, thrownError) {
							alert(thrownError);
								res = { httpStaus: httpStaus, err: getHttpError(jqXHR) };
							},
						});

						return res;

		}


		function Capture()
		{
		    alert($("#bankName").val());
			var bankName = $("#bankName").val();
			var selectedBankName = $("#bankName option:selected").html();
			$("#selectedBankName").val(selectedBankName);
			var deviceName = $("#deviceName").val();
			var aadhaarNumber = $("#aadhaarNumber").val();
			var customerNumber = $("#customerNumber").val();
			var adhar_number_pat = /^[0-9]+$/;
			var mobile_pattern = /^[6789]\d{9}$/;
			var numberPattern = /^[0-9]+$/;
			
			if(bankName=='')
			{
				alert("Please Select Bank");
				return false;
			}else if(aadhaarNumber=='')
			{
				alert("Please Enter Aadhaar Number");
				$('#aadhaarNumber').focus();
				return false;
			}else if(aadhaarNumber.length != 12 )
			{
				alert('Please enter 12 digits aadhaar number ');
				$('#aadhaarNumber').focus();
				return false;
			}else if(!aadhaarNumber.match(adhar_number_pat) )
			{
				alert('Please enter correct aadhaar number ');
				$('#aadhaarNumber').focus();
				return false;
			}
			else if(customerNumber=='')
			{
				alert("Please enter customer mobile number");
				$('#customerNumber').focus();
				return false;
			}
			else if(customerNumber.length != 10)
			{
				alert("Please enter 10 digits mobile number");
				$('#customerNumber').focus();
				return false;
				
			}
			else if(!customerNumber.match(mobile_pattern))
			{
				alert('Invalid Mobile Number');
				$('#customerNumber').focus();
				return false;
			}
			if($("#transactionType").val() == "CW")
			{
					var amount =$('#amount').val();
					$('#spanChargeAmount').html(amount);
					if(amount == '')
					{
						alert("Please Enter amount");
						$('#amount').focus();
						return false;
						
					}
					else if(!amount.match(numberPattern))
					{
						alert("Please Enter valid aadhaar card number");
						$('#adhar_number').focus();
						return false;
						
					}
					else if(amount <1)
					{
						alert("Amount should be greater than 1");
						$('#amount').focus();
						return false;
						
					}
				
			}
			$("#scanDeviceBtn").hide();
			//alert(deviceName);
			$("#captureDeviceLoader").show();
			if(deviceName=="MANTRA")
			{
				var urlnew="http://127.0.0.1:11100/rd/capture";
				var XML='<?php echo '<?xml version="1.0"?>'; ?> <PidOptions ver="1.0"> <Opts fCount="'+$("#Fcount").val()+'" fType="'+$("#Ftype").val()+'" iCount="'+$("#Icount").val()+'" pCount="'+$("#Pcount").val()+'" format="'+$("#Dtype").val()+'"   pidVer="'+$("#Pidver").val()+'" timeout="'+$("#Timeout").val()+'" wadh="'+$("#txtWadh").val()+'" posh="UNKNOWN" env="'+$("#Env").val()+'" /> '+DemoFinalString+'<CustOpts><Param name="mantrakey" value="'+$("#txtCK").val()+'" /></CustOpts> </PidOptions>';
			}
			else if(deviceName=="MORPHO")
			{
				var urlnew="http://127.0.0.1:11100/capture";
				XML='<PidOptions ver="1.0"><Opts fCount="1" fType="0" iCount="" iType="" pCount="" pType="" format="1" pidVer="2.0" timeout="10000" otp="" wadh="$WADH" posh=""/></PidOptions>';
			}
			var xhr;//=createCORSRequest('CAPTURE',url);
            var ua = window.navigator.userAgent;
            var msie = ua.indexOf("MSIE");
            if (msie > 0 || !!navigator.userAgent.match(/Trident.*rv\:11\./)) // If Internet Explorer, return version number
            {
//IE browser
                xhr = new ActiveXObject("Microsoft.XMLHTTP");
            } else {
//other browser
                xhr = new XMLHttpRequest();
            }
			xhr.open('CAPTURE', urlnew, true);
            xhr.setRequestHeader("Content-Type", "text/xml");
            xhr.setRequestHeader("Accept", "text/xml");
            if (!xhr) 
			{
                toastr["success"]('Error for accessing device');
                console.log('CORS not supported');
                return
            }
			 xhr.onreadystatechange = function () 
			{
				$("#captureDeviceLoader").hide();
				console.log(xhr);
				console.log(xhr.readyState);
                if (xhr.readyState == 4) 
				{
                    var status = xhr.status;
					console.log(status);
                    if (status == 200) 
					{
                        xhr.response
						$('#txtPidData').val(xhr.response);
						console.log("-----")
						console.log( xhr.response);
						let parser=new DOMParser();
						let xml = parser.parseFromString(xhr.response,"application/xml");
						var pidContent=xml.getElementsByTagName('PidData')[0]; 
						var responseCode = pidContent.getElementsByTagName('Resp')[0].getAttribute("errCode");
						var errInfo = pidContent.getElementsByTagName('Resp')[0].getAttribute("errInfo");
						if(responseCode==700 || responseCode=="700")
						{
							alert(errInfo +" Please try again");
							//location.reload()
							$("#scanDeviceBtn").show();
							$("#captureDeviceLoader").hide();
							return false;
						}
						else if(responseCode== -1509 || responseCode=="-1509")
						{
							alert(errInfo +" Please try again");
							//location.reload()
							$("#scanDeviceBtn").show();
							$("#captureDeviceLoader").hide();
							return false;
						} 
						else if(responseCode !=0)
						{
							errorMessage = errInfo +" Please try again";
							alert(errorMessage)
							$("#scanDeviceBtn").show();
							$("#captureDeviceLoader").hide();
							//swal("Failure", errorMessage, "error");
							//setTimeout(function(){ location.reload(); }, 3000);
							return false;
						}
						$.ajaxSetup({
							headers: {
							'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
							}
						})
						var uploadfile = $('#myImageForm').serialize()
						$.ajax({
							type: "post",
							url: "<?php echo e(url('send-apaeps-request')); ?>",
							data: uploadfile,
							datatype: "json",
							beforeSend:function()
							{
									$("#scanDeviceImage").show();
								
							},
							success: function (res) {
									$("#scanDeviceImage").hide();
								if(res.status=="Success"||res.status=="Pending")
								{
									$('#printAnchorSlip')[0].click();
									refreshBalance();
									var content = "<tr><td>"+res.txnTime+"</td><td>Aeps</td><td>"+res.fpTransactionId+"</td><td>"+res.bankRRN+"</td><td>"+res.transactionAmount+"</td><td>"+res.availableBalance+"</td><td>"+res.status+"</td></tr>";
									
									$("#slipAadhaarNumber").html(aadhaarNumber);
									$("#slipCustomerNumber").html(customerNumber);
									$("#slipBankName").html(selectedBankName);
									/* $("#slipTxnAmount").html(res.transactionAmount);
									$("#slipAvailableBalance").html(res.availableBalance);
									$("#slipTxnId").html(res.fpTransactionId);
									$("#slipBankRefNumber").html(res.bankRRN); */
									$("#slipTxnType").html(res.transactionType);
									$("#slipStatus").html(res.status);
									$("#txnPrintSlip").html(content);
									$("#myReciept").modal("toggle");
								}
								else
								{
									alert(res.message);
									$("#scanDeviceImage").hide();
									location.reload();
								}
							}
						})
						
                    } else {
                        console.log(xhr.response);
                    }
                }
            };
            xhr.onerror = function () 
			{
                alert("Check If Morpho Service/Utility is Running");
				console.log(xhr.response);
				
				$("#checkDeviceLoader").show();
				$("#captureDeviceLoader").hide();
            }
            xhr.send(XML);
		}
		function CaptureAvdm()
		{
			var bankName = $("#bankName").val();
			var selectedBankName = $("#bankName option:selected").html();
			$("#selectedBankName").val(selectedBankName);
			var aadhaarNumber = $("#aadhaarNumber").val();
			var customerNumber = $("#customerNumber").val();
			var adhar_number_pat = /^[0-9]+$/;
			var mobile_pattern = /^[6789]\d{9}$/;
			var numberPattern = /^[0-9]+$/;
			
			if(bankName=='')
			{
				alert("Please Select Bank");
				return false;
			}else if(aadhaarNumber=='')
			{
				alert("Please Enter Aadhaar Number");
				$('#aadhaarNumber').focus();
				return false;
			}else if(aadhaarNumber.length != 12 )
			{
				alert('Please enter 12 digits aadhaar number ');
				$('#aadhaarNumber').focus();
				return false;
			}else if(!aadhaarNumber.match(adhar_number_pat) )
			{
				alert('Please enter correct aadhaar number ');
				$('#aadhaarNumber').focus();
				return false;
			}
			else if(customerNumber=='')
			{
				alert("Please enter customer mobile number");
				$('#customerNumber').focus();
				return false;
			}
			else if(customerNumber.length != 10)
			{
				alert("Please enter 10 digits mobile number");
				$('#customerNumber').focus();
				return false;
				
			}
			else if(!customerNumber.match(mobile_pattern))
			{
				alert('Invalid Mobile Number');
				$('#customerNumber').focus();
				return false;
			}
			if($("#transactionType").val() == "CW")
			{
					var amount =$('#amount').val();
					$('#spanChargeAmount').html(amount);
					if(amount == '')
					{
						alert("Please Enter amount");
						$('#amount').focus();
						return false;
						
					}
					else if(!amount.match(numberPattern))
					{
						alert("Please Enter valid aadhaar card number");
						$('#adhar_number').focus();
						return false;
						
					}
					else if(amount <1)
					{
						alert("Amount should be greater than 1");
						$('#amount').focus();
						return false;
						
					}
				
			}
		$("#scanDeviceBtn").hide();
		$("#scanDeviceImage").show();
	   Demo();
	   if($("#txtWadh").val().trim()!="")
	   {
		var XML='<?php echo '<?xml version="1.0"?>'; ?> <PidOptions version="1.0"> <Opts fCount="'+$("#Fcount").val()+'" fType="'+$("#Ftype").val()+'" iCount="'+$("#Icount").val()+'" pCount="'+$("#Pcount").val()+'" format="'+$("#Dtype").val()+'"   pidVer="'+$("#Pidver").val()+'" timeout="'+$("#Timeout").val()+'" wadh="'+$("#txtWadh").val()+'" posh="UNKNOWN" env="'+$("#Env").val()+'" /> '+DemoFinalString+'<CustOpts><Param name="mantrakey" value="'+$("#txtCK").val()+'" /></CustOpts> </PidOptions>';
	   }
	   else
	   {
		var XML='<?php echo '<?xml version="1.0"?>'; ?> <PidOptions version="1.0"> <Opts fCount="'+$("#Fcount").val()+'" fType="'+$("#Ftype").val()+'" iCount="'+$("#Icount").val()+'" pCount="'+$("#Pcount").val()+'" format="'+$("#Dtype").val()+'"   pidVer="'+$("#Pidver").val()+'" timeout="'+$("#Timeout").val()+'" posh="UNKNOWN" env="'+$("#Env").val()+'" /> '+DemoFinalString+'<CustOpts><Param name="mantrakey" value="'+$("#txtCK").val()+'" /></CustOpts> </PidOptions>';
	   }
			//alert(XML);

            <!-- url = ""; -->

					 var verb = "CAPTURE";


                        var err = "";

						var res;
						$.support.cors = true;
						var httpStaus = false;
						var jsonstr="";
						;
							$.ajax({

							type: "CAPTURE",
							async: false,
							crossDomain: true,
							url: finalUrl+MethodCapture,
							data:XML,
							contentType: "text/xml; charset=utf-8",
							processData: false,
							beforeSend:function()
							{
								$("#scanDeviceBtn").hide();
								$("#scanDeviceImage").show();
							},
							success: function (data) {
							//alert(data);
								httpStaus = true;
								res = { httpStaus: httpStaus, data: data };

								$('#txtPidData').val(data);
								$('#txtPidDataT').val(data);
								$('#txtPidOptions').val(XML);

								var $doc = $.parseXML(data);
								var Message =  $($doc).find('Resp').attr('errInfo');
								if(Message == "Success")
								{
									$.ajaxSetup({
									headers: {
									'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
									}
								})
									var uploadfile = $('#myImageForm').serialize()
									$.ajax({
										type: "post",
										url: "<?php echo e(url('send-aeps-request')); ?>",
										data: uploadfile,
										datatype: "json",
										beforeSend:function()
										{
											
											
										},
										success: function (res) {
											
											if(res.status=="Success"||res.status=="Pending")
											{
												refreshBalance();
												var content = "<tr><td>"+res.txnTime+"</td><td>Aeps</td><td>"+res.fpTransactionId+"</td><td>"+res.bankRRN+"</td><td>"+res.transactionAmount+"</td><td>"+res.availableBalance+"</td><td>"+res.status+"</td></tr>";
												
												$("#slipAadhaarNumber").html(aadhaarNumber);
												$("#slipCustomerNumber").html(customerNumber);
												$("#slipBankName").html(selectedBankName);
												/* $("#slipTxnAmount").html(res.transactionAmount);
												$("#slipAvailableBalance").html(res.availableBalance);
												$("#slipTxnId").html(res.fpTransactionId);
												$("#slipBankRefNumber").html(res.bankRRN); */
												$("#slipTxnType").html(res.transactionType);
												$("#slipStatus").html(res.status);
												$("#txnPrintSlip").html(content);
												$("#myReciept").modal("toggle");
											}
											else
											{
												alert(res.message);
												$("#scanDeviceImage").hide();
												location.reload();
											}
										}
									})
								}
								

							},
							error: function (jqXHR, ajaxOptions, thrownError) {
							//$('#txtPidOptions').val(XML);
							alert(thrownError);
								res = { httpStaus: httpStaus, err: getHttpError(jqXHR) };
							},
						});

						return res;
		}
		function getHttpError(jqXHR) {
		    var err = "Unhandled Exception";
		    if (jqXHR.status === 0) {
		        err = 'Service Unavailable';
		    } else if (jqXHR.status == 404) {
		        err = 'Requested page not found';
		    } else if (jqXHR.status == 500) {
		        err = 'Internal Server Error';
		    } else if (thrownError === 'parsererror') {
		        err = 'Requested JSON parse failed';
		    } else if (thrownError === 'timeout') {
		        err = 'Time out error';
		    } else if (thrownError === 'abort') {
		        err = 'Ajax request aborted';
		    } else {
		        err = 'Unhandled Error';
		    }
		    return err;
		}


    </script><div class="modal fade" id="transactionDetailsModel" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"  data-backdrop="static" data-keyboard="false">
			<div class="modal-dialog" role="document" style="">
				<div class="modal-content">
					<div class="modal-header">
						<h4 class="modal-title" id="myModalLabel">Slip</h4>
						<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
									aria-hidden="true">&times;</span></button>
					</div>
					<div class="modal-body" >
						<form class="form-horizontal">
							<table class="table">
								<tr>
									<td>
										<div class="col-md-2 col-sm-2 col-xs-2" >
											<img src="<?php echo e(asset('newlog/images/Logo168.png')); ?>"/>
										</div>
									</td>
									<td><div class="pull-right">
											<div style=" float: left:10px;">
												<b>Outlet Name :  <?php echo e(Auth::user()->member->company); ?></b>
											</div>
											
											<div style="">
												<b>Contact Number: <?php echo e(Auth::user()->mobile); ?></b>
											</div>
										</div>
										</td>
									</tr>														
							
							</table>
							<table class="table">
								<tbody id="balanceEnquirySlip">
									<!--<tr><th> Aadhaar Number</th><td id="slipAadhaarNumber"></td></tr>
									<tr><th> Customer Number</th><td id="slipCustomerNumber"></td></tr>
									<tr><th> Bank Name</th><td id="slipBankName"></td></tr>
									<tr><th> Txn Amount</th><td id="slipTxnAmount"></td></tr>
									<tr><th> Available Balance</th><td id="slipAvailableBalance"></td></tr>
									<tr><th> Txn Id</th><td id="slipTxnId"></td></tr>
									<tr><th> Bank Ref Number</th><td id="slipBankRefNumber"></td></tr>
									<tr><th> Txn Type</th><td id="slipTxnType"></td></tr>
									<tr><th> Status</th><td id="slipStatus"></td></tr>-->

								</tbody>
							</table>							
						</form>
					</div>
					<!--<div class="modal-footer">
						<button type="button" class="btn btn-danger" data-dismiss="modal" id="closemodal">Close</button>
					</div>-->
				</div>
			</div>
		</div>
	<meta name="_token" content="<?php echo csrf_token(); ?>"/>
<div id="myReciept" class="modal fade" role="dialog" data-backdrop="static" data-keyboard="false">
	<div class="modal-dialog modal-lg" role="document">
			<!-- Modal content-->
		  <div class="modal-content tx-size-sm" >
				<div class="modal-body">
					<div class="col-md-12">
						<button type="button" class="btn" data-dismiss="modal" style="padding: 6px ! important; top: -8px; right: -35px; background-color: rgb(255, 255, 255) ! important; position: absolute;" id="closemodal">&times;</button>
					</div>
					<div class="containers" style="height: 500px; overflow: auto; width: 100%">
						<div class="panel panel-primary">
							<div class="panel-heading" style="margin-bottom: 3px; padding: 7px;width:95%">Print / Download Receipt <span id="prt_hdtranid"></span>

							<button class="btn btn-basic fullbtn" style="color:black !important; float: right; padding: 5px 8px; text-shadow: none;" onClick="printDiv()"><i class="fa fa-print" style="margin-right: 4px;color: green"></i>PRINT</button></div>
							<div class="panel-body" style="padding: 0% 1%">
								<div class="clearfix"></div>
								<div class="row">
									<div class="col-md-9"></div>
									<div class="col-md-3">
									</div>
								</div>
								<div class="clearfix"></div>
								<div id="reciept" style="margin-top: 5px; margin-bottom: 7px;">

									<div class="row">
										<div class="col-md-12" id="dvContents">
											<style>
												td {
													padding: 5px;
												}
											</style>
											<table style="width: 100%; border: 1px solid #888;">
												<tr>
													<th colspan="2">
													<div class="col-md-12">
														<div class="col-md-2 col-sm-2 col-xs-2" style="padding:10px;">
															<img src="<?php echo e(asset('newlog/images/Logo168.png')); ?>" style="width:70px;margin-right: 690px" />
														</div>
														<div class="col-md-6 col-sm-6 col-xs-6 text-left" style="padding:10px;">
															<div style=" float: left:10px;">
																<b>Outlet Name: <?php echo e(Auth::user()->member->company); ?></b>
															</div>
															<br />
															<div style="margin-top: -20px; float: left:10px;">
																<b>Contact Number: <?php echo e(Auth::user()->mobile); ?></b>
															</div>
														</div>
													  
													  <div class="col-md-4 col-sm-4 col-xs-4 text-right" style="padding:10px;float: right" id="trandetailbyheadnormal">
														   <b>Receipt # :</b> <span class="slipTxnId"> <b id="prt_bdtranid"></b>
															<br /> 
															<b>Date : <?php echo e(date("d-m-Y H:i:s")); ?></b>
														</div> 
														</div>
													</th>


												</tr>

											  
											   <tr></tr>
											   <tr style="border-top:1px solid #ddd;margin-left:60px" id="trandetailbydmt">
													<td>
														
														<b>Aadhaar Number : <span id="slipAadhaarNumber"></span><br/>
														<b>Customer Number : <span id="slipCustomerNumber"></span><br/>
														
													</td>
													<td class="pull-right">
														<b>Bank Name : <span id="slipBankName"></span><br/>
														<b>Transaction Type : <span id="slipTxnType">IMPS</span></b><br />
													</td>
													<td></td>
												</tr>
											   
												<tr>
													<td colspan="3" style="border-bottom: 1px solid #ccc; border-top: 1px solid #ccc;"><b>Transaction Details</b></td>
												</tr>
												<tr>
													<td colspan="3" class="nospace1">
														<table class="table table-bordered">
															<thead class="thead-light">
																<tr style="background:#ddd;">
																	<td class="phead"><b>Date</b></td>
																	<td class="phead"><b>Service Provider</b></td>
																	<td class="phead"><b>Transaction ID </b></td>
																	<td class="phead"><b>IMPS/UTR No.</b></td>
																	<td class="phead"><b>Txn Amount </b></td>
																	<td class="phead"><b> Available Balance </b></td>
																	<td class="phead"><b>Status </b></td>
																</tr>
																</thead>
																<tbody id="txnPrintSlip"></tbody>
														   
														</table>
													</td>
												</tr>
											   <tr>
													<td colspan="3">
														
														<div class="col-md-12 col-sm-12 col-xs-12">
														<b>Total Amount Rs. : </b>
														<span id="spanChargeAmount"></span>
														<span id="updageFeeSpan">
														<input type="text" id="txnChargeAmout" onfocusout="amountInWords()"></span>
								
														</div>
													</td>
												</tr> 
												<tr>
													<td colspan="3">
														<div class="col-md-12 col-sm-12 col-xs-12">
														   <b>Amount in Words :</b>
															<label id="slipAmountInWord"></label>
														</div>
													</td>
												</tr> 
												<tr>
													<td colspan="3" class="modalfoot" style="">
														<h4>Terms & Conditions / Disclaimer:</h4>
													<p class="pull-left" style="    text-align: left;">
                                                    1. This transaction receipt is only a provisional acknowledgment and is issued to customer mentioned herein for accepting mentioned payment for the above order and as per the details provided by the customer.<br>
                                                    2. The customer is fully responsible for the accuracy of the details as provided by him before the transaction is initiated.<br>
                                                    3. The Merchant shall not charge any fee to the customer directly for services rendered by them. The customer is required to immediately report such additional/excess charges to a2zsuvidhaa.
                                                    <br> 
                                                    4. This is a system generated receipt hence does not require any signature. Is there anything you want to share with us? Feedback, comments, suggestions or compliments - do write to info@a2zsuvidhaa.com </p>
                                                    <p>This is a system generated Receipt. Hence no seal or signature required.</p>
													</td>
												</tr>
											</table>

										</div>
									</div>
								</div>
							</div>

						</div>
					</div>
				</div>
			</div>

		</div>
	</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>