<script type="text/javascript">

    function getTransactionOfMobile()
    {
      var mobileNumber = $("#number").val();
      dataString="mobileNumber="+ mobileNumber;
     
      $.ajax({
        type: "GET",
        url: "<?php echo e(url('get-mobile-transaction-report')); ?>",
        data: dataString,
        dataType: "json",
        beforeSend:function()
        {
          $('#noRecordFound').hide();
          $("#mobileTxnTBody").html('');
        },
        success: function (msg) 
          {
            if(msg.status==1){          
              var content='';
                $.each(msg.message, function (key, val) 
                {
                  content +='<tr><td>'+val.id+'</td><td>'+val.beneName+'</td><td>'+val.accountNumber+'</td><td>'+val.bankName+'</td><td>'+val.ifscCode+'</td><td>'+val.txnId+'</td><td>'+val.description+'</td><td>'+val.amount+'</td><td>'+val.status+'</td></tr>';
                });
                $('#myModal').trigger("reset");
                $("#myModal").modal('show')
                $("#mobileTxnTBody").html(content);
            }
            else{
              $('#myModal').trigger("reset");
              $("#myModal").modal('show')
              $('#noRecordFound').show();
            }
          }
        });
    }
</script>


<div class="modal fade" id="myModal" role="dialog">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title">Report</h4>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>
      <div class="modal-body">
       	<table class="table table-bordered" id="example2">
        	<thead>
    				<th>RecordID</th>
    				<th>Name</th>
    				<th>Acc</th>
    				<th>Bank Name</th>
    				<th>IFSC</th>
    				<th>TxnId</th>
					<th>Description</th>
    				<th>Amount</th>
    				<th>Status</th>
        	</thead>
        	<tbody id="mobileTxnTBody">
        		
        	</tbody>
        </table>
        <div id="noRecordFound" Style="color:red;display:none">No Record Found</div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>