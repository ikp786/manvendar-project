@extends('admin.layouts.templatetable')
  @section('title','MD Agent Records')
@section('content')
    <script>
    function viewInvoice()
	{
        
           var start_date = $('#from_date').val();
            var end_date = $('#to_date').val();
            var company_id = $('#company_id').val();
            var page_type = $('#page_type').val();
            var user_id = $('#user_id').val();

            if(start_date == '')
            {
                alert('Please select From Date');
                return false;
            }
            else if(start_date == '')
            {
                  alert('Please select To Date');
                return false;
            }
            var date1 = new Date(start_date);
            var date2 = new Date(end_date);
            var timeDiff = Math.abs(date2.getTime() - date1.getTime());
            var diffDays = Math.ceil(timeDiff / (1000 * 3600 * 24)); 
            if(diffDays >31)
            {
                alert('Please select within 31 days.');
                return false;
            }
            if(page_type == 'md')
            {
                var url="{{url('admin/show-md-invoice')}}";
                var dataString = 'from_date=' + start_date +'&to_date='+end_date+'&company_id='+company_id+'&invoice_of='+page_type;;
                
            }
            else if(page_type == 'distributor')
            {
               var url="{{url('admin/show-distributor-invoice')}}";
               var dataString = 'from_date=' + start_date +'&to_date='+end_date+'&user_id='+user_id+'&invoice_of='+page_type;
            }
            else if(page_type == 'retailer')
            {
               var url="{{url('admin/show-distributor-invoice')}}";
               var dataString = 'from_date=' + start_date +'&to_date='+end_date+'&user_id='+user_id+'&invoice_of='+page_type;
            }
              
                    $.ajax({
                        type: "get",
                        url: url,
                        data: dataString,
                        beforeSend: function () {
                           $("#btn-invoice").show();
                          $("#view_invoice").hide();
                        },
                        complete: function(){
                            $('#loading').html("");                      },
                        success: function (result) 
                        {
                             $("#btn-invoice").hide();
                             $("#view_invoice").show();
                            console.log(result);
                           res = result.data;
                            if(res.status == 1)
                            {
                                var s_no=0;
                                invoice_form=res.invoice_form;
                                invoice_to = res.invoice_to;
                                $('#company_name').html(invoice_form.comp_name);
                                $('#company_address').html(invoice_form.comp_address);
                                $('#company_email').html(invoice_form.comp_email);
                                $('#company_mobile_number').html(invoice_form.comp_mobile);
                                $('#company_cin_number').html(invoice_form.comp_cin_no);
                                $('#company_pan_number').html(invoice_form.comp_pancard);
                                $('#company_gstin_number').html(invoice_form.comp_gstno);
                                $('#company_state').html(invoice_form.comp_state);
                                $('#company_state_code').html(invoice_form.comp_state_code);
                                $('#date_of_service').html(res.start_date +' TO '+ res.end_date);
                                $('#place_of_service').html('Delhi');
                                $('#invoice_date').html(res.end_date);
                                $('#bill_party_lm_id').html("LM "+invoice_to.invoice_lm_id);
                                $('#bill_party_name').html(invoice_to.invoice_name);
                                $('#biller_address').html(invoice_to.address);
                                $('#biller_pan_card').html(invoice_to.pan_card);
                                $('#biller_state').html(invoice_to.state);
                                $('#biller_state_code').html(invoice_to.state_code);
                                $('#total_amt_with_gst').html(result.total_amount);
                                $('#taxable_amount').html(result.taxbale_amount);
                                $('#total_amt_aft_tax').html(result.total_amount);
                                $('#cgst').html(result.cgst);
                                $('#sgst').html(result.sgst);
                                $('#igst').html(result.igst);
                                //$('#igst').html(total_amount);
                                var up_ad='<tr style="border: 1px solid black; border-collapse: collapse; font-size: 12px;"><td style="border: 1px solid black; border-collapse: collapse;     width: 10%; " >1</td><td style="border: 1px solid black; border-collapse: collapse;     width: 20%;">Upfront + AD</td><td style="border: 1px solid black; border-collapse: collapse;     width: 20%;">'+result.total_value+'</td><td style="border: 1px solid black; border-collapse: collapse;     width: 20%;"></td><td style="border: 1px solid black; border-collapse: collapse;     width: 20%;">'+ result.charges +'</td></tr >';
                                 $('#up_tbody').html(up_ad);
                                 var api_tbody ='';
                                var mhtml='';
                                var thead_result ="<tr class='tr-text-fromat'><td style='padding-left:10px;width:20px'>Product Name</td> <td style='padding-left:5px;width:20px'>Profit Charge</td><td style='padding-left:5px;width:20px'>Total Transaction</td><td style='padding-left:5px;width:20px'>Total Profit</td>";
                                $.each(res.result, function (key, val) 
                                {
                                    s_no=key + 1;
                                  mhtml += "<tr style='text-align:center'>"
                                    mhtml += "<td style='padding-left:10px;width:20px'> "+val.api_id+"</td><td style='padding-left:10px;width:20px'> " + val.profit_charge + "</td><td style='padding-left:10px;width:20px'> " + val.total_txn + "</td><td style='padding-left:10px;width:20px'> " + val.total_profit + "</td></tr>";
                                     api_tbody +='<tr style="border: 1px solid black; border-collapse: collapse; font-size: 12px;"><td style="border: 1px solid black; border-collapse: collapse;     width: 10%;" > '+s_no+'</td><td style="border: 1px solid black; border-collapse: collapse;     width: 20%;"> '+val.api_id+'</td><td style="border: 1px solid black; border-collapse: collapse;     width: 20%;"> '+val.total_txn+'</td><td style="border: 1px solid black; border-collapse: collapse;     width: 20%;"> '+val.profit_charge+'</td> <td style="border: 1px solid black; border-collapse: collapse;     width: 20%;">'+val.total_profit+'</td></tr >'
                                });
                                 $('#api_tbody').html(api_tbody);

                              console.log(res)
                                    $("#date_result").text(start_date +' To '+ end_date)
                                    $("#invoice-thead").html(thead_result);
                                    $("#invoice-tbody").html(mhtml);
                                    $("#md-invoice-model").modal("toggle");
                                    //$('#md-invoice-model').modal('show'); 
                            }
                            else
                                alert(res.result);
                            
                          
                        },
                         error: function (jqXHR, exception) 
                         {
                                var msg = '';
                                
                                alert(msg);
                        }
                    })
        
    }
    function printSlip() {
            //var contents = document.getElementById("print-slip").innerHTML;
            var contents = document.getElementById("invoice-slip").innerHTML;
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



    <!-- Page-Title -->
    <div class="row">
        <div class="col-sm-12">
            <div class="col-lg-6 col-md-6">
                <h4 class="page-title" style="color: white; font-size: 36px;">{{ $page_title or 'MD Agent List Transaction' }}</h4>
                
            </div>
            <div class="col-lg-6 col-md-6">
                <div class="pull-right">
                   
                </div>
            </div>
        </div>
    </div><br>

    <!--Basic Columns-->
    <!--===================================================-->


    <!--===================================================-->
    <div class="row">
        <div class="col-sm-12">
            <div class="card-box">
                <h4 class="m-t-0 header-title"><b>{{ $page_title or 'MD Agent List Transaction' }}</b></h4>
                <p class="text-muted font-13">
                    MD Agent List Transaction
                </p>
                @if (session('status'))
                    <div class="alert alert-success">
                        {{ session('status') }}
                    </div>
                @endif
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div class="panel panel-default">
                    <div class="panel-body">
                        <div class="col-md-12">
                            <div class="col-md-3">
                                <form method="get" action="{{ url('searchall') }}" class="form-inline" role="form"><!-- changed action from searchall to all-recharge-transaction and method type get to post -->
                                    {!! csrf_field() !!}
                                    <div class="form-group">
                                        <label class="sr-only" for="payid">Number</label>
                                        <input name="number" type="text" class="form-control" id="exampleInputEmail2"
                                               placeholder="Number">
                                    </div>
                                    <button onclick="tekdeail()" type="submit"
                                            class="btn btn-success waves-effect waves-light m-l-10 btn-md"><span
                                                class="glyphicon glyphicon-find"></span>Search
                                    </button>
                                </form>
                            </div>
                             @if(Auth::user()->role_id==1)
                            <div class="col-md-7>
                           
                                <form method="get" action="{{ url('search-md-agent-txn')}}">
                                    <div class="form-group col-md-2">
                                        <input name="from_date" id="from_date" class="form-control" type="date" value="{{date('Y-m-d')}}">
                                    </div>
                                    <div class="form-group col-md-2">
                                        <input name="to_date" id="to_date" class="form-control" type="date" value="{{date('Y-m-d')}}">
                                    </div>
                                    <span class="form-group col-md-4">
                                        <button type="sumbit" class="btn btn-success" name='export' value='search'>
                                                <span class="glyphicon glyphicon-search" ></span> Search</button>
                                     
                                        <button type="sumbit" class="btn btn-info" name='export' value='export'>
                                                <span class="glyphicon glyphicon-export" ></span> Export
                                         Summary</button>
                                        
                                        <button type="button" class="btn btn-primary" id='view_invoice' onclick="viewInvoice()" title="Generate Invoice">
                                                <span class="glyphicon glyphicon-eye-open" ></span> View Invoice</button>
                                     </span>
                                     <img id="btn-invoice" src="images/loader.gif" style="width: 116px;height: 60px;position: absolute;left: 78%;top: -27%; display: none">
                                </form>
								
								
                               
                            </div>
							
                             @endif
							 
                            <script>

                            </script>
                        </div>

                    </div>
                </div>

                <table id="demo-custom-toolbar" data-toggle="table"
                       data-toolbar="#demo-delete-row"
                       data-search="true"
                       data-show-export="true"
                       data-page-list="[10, 20, 30]"
                       data-page-size="80"
                       data-pagination="true" class="table-bordered ">
                    <thead>
                    <tr>
                        <th data-field="id" data-sortable="true">
                            Id
                        </th>
                       <th data-field="company_id" data-sortable="true">Company Id</th>
                        <th data-field="parent_id" data-sortable="true">Parent Id</th>
                        <th data-field="user" data-sortable="true">User</th>
                        
                        <th data-field="date" data-sortable="true" data-formatter="dateFormatter">Date & Time
                        </th>
                        <th data-field="provider" data-align="center" data-sortable="true"
                            data-sorter="priceSorter">Product
                        </th>

                        <th data-field="provider_name" data-align="center" data-sortable="true"
                            data-sorter="priceSorter">Provider
                        </th>
						<th data-field="bank_name" data-sortable="true" data-formatter="dateFormatter">Bank Name
                        </th>
                        <th data-field="number" data-sortable="true" data-formatter="dateFormatter">Number
                        </th>
                        <th data-field="txnid" data-sortable="true">Ref Id
                        </th>

                        <th data-field="amount" data-align="center" data-sortable="true"
                            data-sorter="priceSorter">Amount
                        </th>
                        <th data-field="profit" data-align="center" data-sortable="true"
                            data-sorter="priceSorter">S Charge
                        </th>
                        <th data-field="total" data-align="center" data-sortable="true"
                            data-sorter="priceSorter">Total
                        </th>
                        <th data-field="status" data-align="center" data-sortable="true"
                            data-formatter="statusFormatter">Status
                        </th>
                        
                    </tr>
                    </thead>

                    <tbody>
                    @foreach($reports as $key => $value)
                    @if($value->status_id!=14)
                        <tr>
                            <td>{{ $value->id }}</td>
                            <td>{{ $value->user->company_id }}</td>
                            <td>{{ $value->user->parent_id }}</td>
                           
                            <td>
                                <a href="{{ url('search-md-agent-txn')}}/{{ $value->user_id }}">{{ $value->user->name }} ({{$value->user_id}})</a>
                            </td>
                            <td>{{ $value->created_at }}</td>
                            <td>{{ @$value->api->api_name }}</td>
                            <td>{{ @$value->provider->provider_name }}</td>
                             @if($value->api_id==2)
                             <td>{{ @$value->description }}</td>
                             @else
                             <td>{{ @$value->beneficiary->bank_name }}</td>
                             @endif
							<td>{{ $value->number }}</td>
                            <td>{{ $value->txnid }}</td>
                            <td>@if(@$value->api->api_name=='Verify'){{'Verify'}} @elseif(@$value->user->name=='MONEY SERVM'){{ 'Per TXN'}}@else {{ number_format($value->amount,2) }} @endif</td>
                            <td>{{ number_format($value->profit,2) }}</td>
                            <td>{{ ($value->recharge_type == 1) ? number_format($value->total_balance,2) : number_format($value->total_balance,2) }}</td>
                            <td>{{ ($value->status_id ==15)?"Success": $value->status->status}}</td>
                            
                        </tr>
                        @endif
                    @endforeach

                    </tbody>
                </table>
               {{$reports->links()}}
            </div>
        </div>
    </div>
    <div class="modal fade" id="md-invoice-model" role="dialog">
        <div class="modal-dialog">
        
          <!-- Modal content-->
          <div class="modal-content">
            <div class="modal-header">
              <button type="button" class="close" data-dismiss="modal">&times;</button>
                 <h4 class="modal-title">Invoice</h4>
                 <span id="date_result"></span>
            </div>
            <div class="modal-body"  id="print-slip">
                <table class="table">
                    <thead id="invoice-thead">
                      
                    </thead>
                    <tbody id="invoice-tbody">
                      
                    </tbody>
                </table>
            </div>
            <div class="modal-footer">
                <input type="button" onclick="printSlip();" value="Print"/>
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
          </div>
          
        </div>
  </div>
  <input type="hidden" id="company_id" value="{{$company_id}}">
  <input type="hidden" id="page_type" value="{{$page_type}}">
  <input type="hidden" id="user_id" value="{{$user_id}}">
</div>

  <div id="invoice-slip" style="display: none;">
    <table>
        <tr><td colspan="3" style="text-align: center; background-color: orange ;width:100%;font-size: 14px;font-weight:  bold;"><span id="company_name"</span><td></tr>
        <tr>
            <td rowspan="8"><img class="img-responsive" src="/images/ers.le-logo.png" width="189px;"
    height="90px"; alt="logo"> </td>
        </tr>
        <tr colspan="2">
            <td ><span style="font-weight: bold; font-size: 12px">Registered Address : </span></td>
            <td ><P id="company_address" style="font-size: 12px"> </P></td>
        </tr>
        <tr colspan="2">
        <td><span style="font-weight: bold; font-size: 12px">Email : </span></td>
        <td><P id="company_email" style="display: inline; font-size: 12px;"></P></td>
        </tr>
        <tr colspan="2">
        <td><span style="font-weight: bold; font-size: 12px;">CIN Number : </span></td>
        <td><P id="company_cin_number" style="display: inline; font-size: 12px;"></P></td>
        </tr>
        <tr colspan="2">
        <td><span style="font-weight: bold; font-size: 12px;">PAN NO : </span></td>
        <td><P id="company_pan_number" style="display: inline; font-size: 12px;"> </P></td></tr>
        
        <tr colspan="2">
        <td><span style="font-weight: bold; font-size: 12px;">MOBILE NO : </span></td>
        <td><P id="company_mobile_number" style="display: inline; font-size: 12px;"></P></td>
        </tr>
        
        <tr colspan="2">
        <td><span style="font-weight: bold; font-size: 12px;">GSTIN : </span></td>
        <td><P id="company_gstin_number" style="display: inline; font-size: 12px;"> </P></td>
        </tr>
    </table>
    
    <table style=' width:100%;  border: 1px solid black; border-collapse: collapse;'>
        <tr style=" padding-left: 7px;">
            <td colspan="2"><p style='text-align: center;font-size: 15px;font-weight:  bold;margin: 2px;background: lightgray; '>Tax Invoice</p></td>
        </tr >
        <tr style="border: 1px solid black; border-collapse: collapse; ">
            <td style="border: 1px solid black; border-collapse: collapse;width:50%;  padding-left: 7px; "><span style="font-weight: bold; font-size: 12px;">Invoice No :</span><span id="invoice_number" style="font-size: 12px;"></span></td>
            <td rowspan="2" style="padding-left: 7px"><span style="font-weight: bold;  padding-left: 7px; font-size: 12px;">Date of Service : </span><span id="date_of_service" style="font-size: 12px;"></span></td>
        </tr>
        <tr style="border: 1px solid black; border-collapse: collapse;width: 50%; padding-left-left: 7px; ">
            <td style="border: 1px solid black; border-collapse: collapse; padding-left: 7px"><span style="font-weight: bold; font-size: 12px;" >Invoice date : </span><span id="invoice_date" style="font-size: 12px;"></span></td>
        </tr>

    </table>
    <table style=' width:100%; border: 1px solid black; border-collapse: collapse;'>
        <tr style="border: 1px solid black; border-collapse: collapse;  ">
            <td style="border: 1px solid black; border-collapse: collapse; width: 50%; padding-left: 7px" ><span style="font-weight: bold; font-size: 12px;">LM ID : </span> <span id="bill_party_lm_id" style="font-size: 12px;">1984</span></td>
            <td rowspan="2" style="border: 1px solid black; border-collapse: collapse; padding-left: 7px;width: 50%;"><span style="font-weight: bold; font-size: 12px;">Place of Service : </span><span  id="place_of_service" style="font-size: 12px;">Delhi</span ><span  style="font-weight: bold; font-size: 12px;">, State Code : </span> <span id="company_state_code"></span></td>
        </tr >
        <tr style="border: 1px solid black; border-collapse: collapse;">
        </tr>
    </table>
    <table style=' width:100%; border: 1px solid black; border-collapse: collapse;'>
        <tr style="border: 1px solid black; border-collapse: collapse;background-color: lightgray;text-align: center; ">
            <td style="border: 1px solid black; border-collapse: collapse;     width: 50%; padding-left: 7px" ><span style="font-weight: bold; font-size: 12px;">BILL TO PARTY </span></td>
            <td style="border: 1px solid black; border-collapse: collapse;     width: 50%; padding-left: 7px"><span style="font-weight: bold; font-size: 12px;">SAC Code : </span><span id="sac_code" style="font-size: 12px;">NILL</span></td>
            
        </tr >
    </table>
    <table style=' width:100%; border: 1px solid black; border-collapse: collapse;'>
        <tr style="border: 1px solid black; border-collapse: collapse; ">
            <td  colspan="2" style="border: 1px solid black; border-collapse: collapse;width: 50%; padding-left: 7px" ><span style="font-weight: bold; font-size: 12px;" >Name : </span><span id="bill_party_name" style="font-size: 12px;"></span> </td>
        </tr>
        <tr>
            <td  colspan="2" style="border: 1px solid black; border-collapse: collapse;width: 50%; padding-left: 7px"><span style="font-weight: bold; font-size: 12px;">Address : </span><span id="biller_address" style="font-size: 12px;"></span></td>
            
        </tr >
        <tr>
            <td  style="border: 1px solid black; border-collapse: collapse;width: 50%; padding-left: 7px"><span style="font-weight: bold;;font-size: 12px;>GSTIN : </span><span id="biller_gstin" style="font-size: 12px;"></span></td>
            <td  style="border: 1px solid black; border-collapse: collapse;width: 50%; padding-left: 7px"><span style="font-weight: bold; font-size: 12px;" >PAN CARD NO : </span><span id="biller_pan_card" style="font-size: 12px;"></span></td>
            
        </tr >
        <tr>
            <td  style="border: 1px solid black; border-collapse: collapse;width: 50%; padding-left: 7px"><span style="font-weight: bold; font-size: 12px; ">State : </span><span id="biller_state" style="font-size: 12px;"></span></td>
            <td  style="border: 1px solid black; border-collapse: collapse;width: 50%; padding-left: 7px"><span style="font-weight: bold; font-size: 12px;">Code : </span><span id="biller_state_code" style="font-size: 12px;font-size: 12px;"></span></td>
            
        </tr >
        <tr>
        </tr>
    </table>
    <table style=' width:100%; border: 1px solid black; border-collapse: collapse; text-align: center;'>
        <thead id="upfront_thead">
            <tr style="border: 1px solid black; border-collapse: collapse;     background-color: khaki;">
                <td style="border: 1px solid black; border-collapse: collapse;     width: 10%;font-weight: bold; font-size: 12px;" >S. No.</td>
                <td style="border: 1px solid black; border-collapse: collapse;     width: 20%;font-weight: bold; font-size: 12px;">Service Description</td>
                <td style="border: 1px solid black; border-collapse: collapse;     width: 20%;font-weight: bold; font-size: 12px;">Value</td>
                <td style="border: 1px solid black; border-collapse: collapse;     width: 20%;font-weight: bold; font-size: 12px;">Charge Rate</td>
                <td style="border: 1px solid black; border-collapse: collapse;     width: 20%;font-weight: bold; font-size: 12px;">Amount</td>
             </tr>
        </thead>

    <tbody id="up_tbody">
    </tbody>
    </table>
    
    <table style=' width:100%; border: 1px solid black; border-collapse: collapse;text-align: center;'>
        <thead id="api_thead">
        <tr style="border: 1px solid black; border-collapse: collapse;background-color: khaki;">
            <td style="border: 1px solid black; border-collapse: collapse;     width: 11%;font-weight: bold; font-size: 12px;" >S. No.</td>
            <td style="border: 1px solid black; border-collapse: collapse;     width: 22%;font-weight: bold; font-size: 12px;">Service Description</td>
            <td style="border: 1px solid black; border-collapse: collapse;     width: 22%;font-weight: bold; font-size: 12px;">TOTAL TRX + VERI</td>
            <td style="border: 1px solid black; border-collapse: collapse;     width: 22%;font-weight: bold; font-size: 12px;">PER TRX Rate</td>
            <td style="border: 1px solid black; border-collapse: collapse;     width: 20%;font-weight: bold; background-color:  white; font-size: 12px;"></td>
        </tr >
        </thead>
        <tbody id="api_tbody">
        
        </tbody>

        <tr >
            <td  colspan="4" style="border: 1px solid black; border-collapse: collapse;width: 50%; padding-left: 7px;font-weight: bold; font-size: 12px; ">Total Amount with GST</td>
            <td style="border: 1px solid black; border-collapse: collapse;width: 50%; padding-left: 7px;font-weight: bold;"> <span id="total_amt_with_gst" style="font-size: 12px;"></span></td>
        </tr>
        
        <tr>
            <td  colspan="4" style="border: 1px solid black; border-collapse: collapse;width: 50%; padding-left: 7px;font-weight: bold; font-size: 12px;">Taxable Amount</td>
            <td style="border: 1px solid black; border-collapse: collapse;width: 50%; padding-left: 7px;font-weight: bold;"> <span id="taxable_amount" style="font-size: 12px;"></span></td>
        </tr>
        
        <tr>
            <td  colspan="3" style="border: 1px solid black; border-collapse: collapse;width: 50%; padding-left: 7px;font-weight: bold;"><span style="font-size: 12px">CGST</span></td>
            <td style="border: 1px solid black; border-collapse: collapse; padding-left: 7px;font-weight: bold;"> <span style="font-size: 12px;">9%</span></td>
            <td style="border: 1px solid black; border-collapse: collapse; padding-left: 7px"> <span id="cgst" style="font-size: 12px;"></span></td>
        </tr>
        <tr>
            <td  colspan="3" style="border: 1px solid black; border-collapse: collapse;width: 50%; padding-left: 7px;font-weight: bold; font-size: 12px;">SGST</td>
            <td style="border: 1px solid black; border-collapse: collapse;padding-left: 7px;font-weight: bold; "> <span style="font-size: 12px;">9%</span></td>
            <td style="border: 1px solid black; border-collapse: collapse; padding-left: 7px"> <span id="sgst" style="font-size: 12px;"></span></td>
        </tr>
        <tr>
            <td  colspan="3" style="border: 1px solid black; border-collapse: collapse;width: 50%; padding-left: 7px;font-weight: bold; font-size: 12px;" >IGST</td>
            <td style="border: 1px solid black; border-collapse: collapse; padding-left: 7px;font-weight: bold;"> <span style="font-size: 12px;">18%</span></td>
            <td style="border: 1px solid black; border-collapse: collapse; padding-left: 7px"> <span id="igst" style="font-size: 12px;"></span></td>
        </tr>
        <tr>
            <td  colspan="4" style="border: 1px solid black; border-collapse: collapse;width: 50%; padding-left: 7px; font-weight: bold; font-size: 12px;">Total Amount after Tax:</td>
             
            <td style="border: 1px solid black; border-collapse: collapse; padding-left: 7px"> <span id="total_amt_aft_tax" style="font-size: 12px;"></span></td>
        </tr>
    </table>
</div> 
   
@endsection