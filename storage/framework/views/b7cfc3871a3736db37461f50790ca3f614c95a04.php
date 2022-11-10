<style>
    .modal-body {
        line-height: 21px;
    }
    .panel {
        color: #222 !important;
    }
    .modal-body p {
        line-height: 20px;
        margin-bottom: 0px;
    }
    .modalfoot {
        padding-top: 50px;
    }
   table {
  /*border-collapse: collapse;*/
  width: 100%;
}
</style>
<script>
    function updateFee()
    {
        var billAmount=$("#prt_trantotal").text();
        var billChargeAmount= $("#billChargeAmount").val();
        var finalAmount = Number(billChargeAmount) + Number(billAmount)
        $("#prt_trantotal").text(finalAmount);
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
                    console.log(msg);
                    $("#prt_tranword").text(msg); 
                }
            });
    }
</script>
<div id="myReciept" class="modal fade" role="dialog" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog" style="width: 900px; margin: 30px auto;">
        <div class="modal-content">
            <div class="modal-body" style="padding: 10px; border-radius: 0px;">
                <div class="col-md-12">
                    <button type="button" class="btn" data-dismiss="modal" style="padding: 6px ! important; top: -8px; right: -35px; background-color: rgb(255, 255, 255) ! important; position: absolute;">&times;</button>
                </div>
                <div class="containers" style="height: 500px; overflow: auto; width: 100%">
                    <div class="panel panel-primary">
                        <div class="panel-heading" style="margin-bottom: 3px; padding: 7px;width:95%">Print / Download Receipt of ID : <?php echo e(@$report->id); ?><span id="prt_hdtranid"></span>

                         <button class="btn btn-primary fullbtn" style="color:black !important; float: right; padding: 5px 8px; text-shadow: none;" id="printDiv" onClick="printDiv()"><i class="fa fa-print" style="margin-right: 4px;color: green"></i>PRINT</button>
                        </div>
                        <div class="panel-body" style="padding: 0% 4%">
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
                                                    <div class="col-md-2 col-sm-2 col-xs-2" style="padding:10px;">
                                                        <img src="<?php echo e(asset('newlog/images/Logo168.png')); ?>" style="width:90px;height:90px;margin-right: 690px" />
                                                    </div>
                                                    <div class="col-md-6 col-sm-6 col-xs-6 text-left" style="padding:10px;">
                                                        <div style="margin-top: -95px; float:left:10px;">
                                                            <b>Outlet Name: <?php echo e($report->user->member->company); ?></b>
                                                        </div>
                                                        <br/>
                                                        <div style="margin-top: -20px; float: left:10px;">
                                                            <b>Contact Number: <?php echo e($report->user->mobile); ?></b>
                                                        </div>
                                                    </div>
                                                  <div class="col-md-4 col-sm-4 col-xs-4 text-right" style="padding:10px;margin-top:-115px;float: right" id="trandetailbyheadnormal">
                                                       <b>Receipt # :</b> R -<?php echo e($report->id); ?> <b id="prt_bdtranid"></b>
                                                        <br /> 
                                                        <b>Date : <?php echo e(\date_format($report->created_at,"d-m-Y H:i:s")); ?></b>
                                                    </div> 
                                                </th>
                                            </tr>
                                           <tr></tr>
                                            <tr style="border-top:1px solid #ddd;margin-left:60px" id="trandetailbydmt">
                                                <td>
                                                    <b>Biller Name : <span><?php echo e(@$report->biller_name); ?> </span></b><br />
                                                    <b>Customer Mobile No : <span><?php echo e(@$report->customer_number); ?></span></b><br/>
                                                    <b>Payment channel :<span>Agent</span></b></br>
                                                   
                                                </td>
                                                <td>
                                                    <b>Consumer ID/Number : <span><?php echo e(@$report->number); ?></span></b><br/>
                                                    <b>Payment Mode :<span> Cash</span></b><br/>
                                                    <b>Date & Time : <span><?php echo e(\date_format($report->created_at,"d-m-Y H:i:s")); ?></span></b><br/>
                                                </td>
                                                <td></td>
                                            </tr>
                                            <tr>
                                                <td colspan="3" style="border-bottom: 1px solid #ccc; border-top: 1px solid #ccc;"><b>Transaction Details</b></td>
                                            </tr>
                                            <tr>
                                                <td colspan="3" class="nospace1">
                                                    <table class="table table-bordered">
                                                        <tr style="background:#ddd;">
                                                            <td class="phead"><b>Date</b></td>
                                                            <td class="phead"><b>Service Provider</b></td>
                                                           
                                                            <td class="phead"><b>BBPS Transaction ID</b></td>
                                                             <td class="phead"><b>Transaction ID </b></td>
                                                            <td class="phead"><b>Amount </b></td>
                                                            <td class="phead"><b>Status </b></td>
                                                        </tr>
                                                        <tr>
                                                            <td ><span id="prt_trandate"></span><?php echo e(\date_format($report->created_at,"d-m-Y H:i:s")); ?></td>
                                                            <td><span id="prt_tranoperator"><?php echo e(@$report->provider->provider_name); ?></span></td>
                                                            
                                                            <td><span id="prt_tranrefernce"><?php echo e($report->bank_ref); ?></span></td>
                                                            <td><span id="prt_tranid"><?php echo e($report->txnid); ?></span></td>
                                                            <td><span id="prt_tranamount"><?php echo e($report->amount); ?></span></td>
                                                            <td><span id="prt_transtatus"><?php echo e(@$report->status->status); ?></span></td>
                                                        </tr>
                                                    </table>
                                                </td>
                                            </tr>
                                           <tr>
                                                <td colspan="3">
                                                    <div class="col-md-6 col-sm-6 col-xs-6">
                                                        <b>Total Amount Rs. : </b>
                                                        <label id="prt_trantotal"><?php echo e($report->amount); ?></label>&nbsp;&nbsp;&nbsp;<span id="updageFeeSpan"><input type="text" id="billChargeAmount"placeholder="Enter Chage Amount"><button class="btn btn-basic" onClick="updateFee()">Save</button></span>
                                                    </div>
                                                  <!--  <div class="col-md-6 col-sm-6 col-xs-6">
                                                        <b>Total Amount Rs. : </b>
                                                        <label id="prt_trantotal"><?php echo e($report->amount); ?></label>
                                                    </div>-->
                                                </td>
                                            </tr> 
                                            <tr>
                                                <td colspan="3">
                                                    <div class="col-md-12 col-sm-12 col-xs-12">
                                                        <b>Amount in Words :</b>
                                                        <label id="prt_tranword"><?php echo e($report->amount); ?></label>
                                                    </div>
                                                </td>
                                            </tr> 
                                            <tr>
                                                <td colspan="3" class="modalfoot" style="text-align: center;">
                                                    <p>&copy; 2018 All Rights Reserved</p>
                                                    <p style="font-size: 12px">This is a system generated Receipt. Hence no seal or signature required.</p>
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
<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.8.3/jquery.min.js"></script>
<script type="text/javascript">

    function printDiv() 
    {
      var divToPrint=document.getElementById('dvContents');
      var newWin=window.open('','Print-Window');
      newWin.document.open();
      newWin.document.write('<html><body onload="window.print()">'+divToPrint.innerHTML+'</body></html>');
      newWin.document.close();
      setTimeout(function(){newWin.close();},10);
    }

    $(function () {
        $("#btnPrint").click(function () {
            var contents = $("#dvContents").html();
            var frame1 = $('<iframe />');
            frame1[0].name = "frame1";
            frame1.css({ "position": "absolute", "top": "-1000000px" });
            $("body").append(frame1);
            var frameDoc = frame1[0].contentWindow ? frame1[0].contentWindow : frame1[0].contentDocument.document ? frame1[0].contentDocument.document : frame1[0].contentDocument;
            frameDoc.document.open();
            //Create a new HTML document.
            frameDoc.document.write('<html><head><title>A2Z Suvidhaa</title>');
            frameDoc.document.write('</head><body>');
            //Append the external CSS file.
           /* frameDoc.document.write('<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" type="text/css" media="print" />');*/
            frameDoc.document.write('<link rel="stylesheet" href="../../Content/css/printReceipt.css" type="text/css" media="print" />');
            frameDoc.document.write('<link href="style.css" rel="stylesheet" type="text/css" />');
            //Append the DIV contents.
            frameDoc.document.write(contents);
            frameDoc.document.write('</body></html>');
            frameDoc.document.close();
            setTimeout(function () {
                window.frames["frame1"].focus();
                window.frames["frame1"].print();
                frame1.remove();
            }, 500);
        });
    });
</script>
