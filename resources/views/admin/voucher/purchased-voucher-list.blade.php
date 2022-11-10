@if(Auth::user()->role->id ==1)
    @extends('admin.layouts.templatetable')
    @section('title','Account Monthly Salary')
    @section('content')
	</style>
	
<script>
                    
          function showMoreProductCode(voucher_bulk_id)
        {
            var dataString = 'voucher_bulk_id=' + voucher_bulk_id;
			$.ajax({
                type: "get",
                url: "{{url('get-bulk-vouche-code')}}",
                data: dataString,
                success: function (data) 
				{
					if(data.status == 1)
						
						{
					 var combo = $('<table class="table table-bordered"><thead><tr><th>Id</th><th>Product Name</th><th>Voucher Name</th><th>Voucher Value</th><th>Voucher Code</th><th>Message</th><tbody>');
                          
					$.each(data.message, function (i, el) 
					{
					combo.append("<tr><td>" + el.id + "</td><td>" + el.product_name + "</td><td>" + el.voucher_name + "</td><td>" + el.voucher_value + "</td><td>" + el.voucher_no + "</td><td>" + el.message + "</td></tr>");
					});
					combo.append("</tbody></table>");
					$("#vouche_codes").html(combo);
		
					$("#VoucherCodeModel").modal("toggle");
						}
						else
							alert(data.message);
                }
            });
            
        }  
            
</script>



        <!-- Page-Title -->
       
        

        <!--Basic Columns-->
        <!--===================================================-->


        <!--===================================================-->
        
		<div class="super_container">

	
	<div class="home" style="background: #8d4fff;">
		
	
	</div>

	<!-- Search -->

	<div class="search">
		

		<!-- Search Contents -->
		
		<div class="container fill_height">
			<div class="row fill_height">
				<div class="col fill_height">

					<!-- Search Tabs -->

					
						
							<table id="dtBasicExample" class="table table-bordered table-striped"  style="background-color:white;margin-left: -10px">
						  <thead>
							<tr>
							  <th>ID </th>
							  <th>Date/Time</th>
							  <th>Product</th>
							  <th>Category</th>
							  <th>Brand</th>
							  <th>Qty</th>
							  <th>Txn ID</th>
							  <th>Amount</th>
							  <th>Charge</th>
							  <th>Total balance</th>
							   <th>Status</th>
							   <th>Action</th>
							</tr>
						  </thead>
					  <tbody>
						@foreach($purchasedVouchers as $voucher)
						<tr>
						  <td>{{ $voucher->id }}</td>
						  <td>{{ $voucher->created_at }}</td>
						  <td>{{ @$voucher->api->api_name }}</td>
						  <td>{{ @$voucher->vouchercategory->name}}</td>
						  <td>{{ @$voucher->voucherbrand->name }}</td>
						  <td>{{ @$voucher->qty }}</td>
						  <td>{{ $voucher->txnid }}</td>
						  <td>{{ $voucher->amount }}</td>
						  <td>{{ $voucher->profit }}</td>
						   <td>{{ number_format($voucher->total_balance,2) }}</td>
						  <td>{{ $voucher->status->status }}</td>
						  <td> 
						  @if($voucher->qty >1 && in_array($voucher->status_id,array(1,3)))
						  <a href="javascript:void(0)"  onclick="showMoreProductCode({{ $voucher->id }});">Show More Product </a>
						@endif</td>
						</tr>
						@endforeach
					  </tbody>
					  
					</table>		
							
                        
		</div>
				  
				   
				   
					<br>
			
					
					
				</div>
			</div>
		</div>	
		</div>
		<div id="VoucherCodeModel" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
         aria-hidden="true" style="display: none;">
        <div class="modal-dialog" role="document" style="    width: 80%;">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel">Voucher Code</h4>
                </div>
                <div class="modal-body">
                    <div  class="form-horizontal">
						<div id="vouche_codes"></div>
                       
                        
                    </div>
                </div>
                <div class="modal-footer">
                   
                    <button type="button" class="btn btn-default waves-effect" data-dismiss="modal">Close
                    </button>
                    <input type="hidden" id="idnew" name="idnew" value="0">


                </div>
            </div>
        </div>
		</div>
		
		
	

</div>
        <meta name="_token" content="{!! csrf_token() !!}"/>
    @endsection
@endif
