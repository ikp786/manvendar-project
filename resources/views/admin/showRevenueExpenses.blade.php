@if(Auth::user()->role->id ==1)
    @extends('admin.layouts.templatetable')
    @section('title','Revenue & Expenses')
    @section('content')
	<style>
	.tr-text-fromat {
    font-size: 20px;
    color: black;
    text-align: center;
}
h2{
	color:white;
	text-align: center;
}
	table tbody:first-child {
    background-color: #0058a0 !important;
    color: black !important;
}
	
	</style>
        <script>
          function getDate(content)
          {
            var start_date = $('#from_date').val();
            var end_date = $('#to_date').val();
            if(start_date == '')
            {
                alert('Please select From Date');
                return false;
            } 
             var dataString = 'start_date=' + start_date +'&end_date='+end_date;
             var url="{{url('show-revenue-expenses')}}";
                        $.ajax({
                        type: "get",
                        url: url,
                        data: dataString,
                        
                       
                        beforeSend: function () {
                        //var image= "/images/loading.gif'";
                         $("#loading").html("<img src= 'images/loading.gif' style='width: 250px;height: 150px;' ></img> ");
                          $('#span_date').text("Please Wait...");
                        },
                        complete: function(){
                            $('#loading').html("");                      },
                        success: function (msg) {
                          //  alert((msg.result)->date);
                            console.log(msg)
                            
                            $('#upfront').text(msg.result.upfront);
                            $('#pretxn').text(msg.result.pretxn);
                            $('#bank_charge').text(msg.result.bank_charge);
                            $('#varification').text(msg.result.varification);
                            $('#ptx_balance').text(msg.result.ptx_balance);
                            $('#span_date').text(msg.result.start_date +' To '+msg.result.end_date);
                            /*  Expenses*/
                            $('#shighra_f_txn').text(msg.result.shighra_f_txn);
                            $('#shighra_f_profit').text(msg.result.shighra_f_profit);
                            $('#shighra_f_amount').text(msg.result.shighra_f_amount);
                          
                            
                            /* Count */
                            $('#varification_txn').text(msg.result.varification_txn);
                            $('#varificaiton_profit').text(msg.result.varificaiton_profit);
                            $('#varificaiton_amount').text(msg.result.varificaiton_amount);
                            $('#total_revenue').text(msg.result.total_revenue);

                            $('#total_expenses').text(msg.result.total_expenses);


                            $('#exp_shighra_f_txn').text(msg.result.exp_shighra_f_txn);
                            $('#exp_shighra_f_profit').text(msg.result.exp_shighra_f_profit);

                             $('#exp_shighra_f_amount').text(msg.result.exp_shighra_f_amount);
                           
                           
                        },
                         error: function (jqXHR, exception) 
                         {
                                var msg = '';
                                
                                alert(msg);
                        }
                    });
            }
          
            //create new task / update existing task
           
            </script>



        <!-- Page-Title -->
       

        <!--Basic Columns-->
        <!--===================================================-->

@include('admin.admin-subtab.accounting-type')
        <!--===================================================-->
		<div class="row">
            <div class="col-sm-12">
                <div class="col-lg-6 col-md-6">
                    <h4 class="page-title" style="color: black; font-size: 140%;">{{ 'Revenue & Expenses' }}</h4>
                    
                </div>
               
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12">
                <div class="card-box">
                  
                    <div class="panel panel-default">
					<h3><span style="font-family:time">Please Search the record only for a month or within month 
                  </span></h3>
                        <div class="panel-body">
                            <div class="col-md-12">
                                <div class="col-md-10">
                                  <!-- <button type="button" class="btn btn-info btn-lg" data-toggle="modal" data-target="#add-state">Add State</button> 
                                  <button type="button" class="btn btn-info btn-lg" data-toggle="modal" data-target="#state-code">Update State</button>-->
                                    <form action="{{url('downloade-revenue-expenses')}}" method="get" target="_blank">
                                        <div class="form-group col-md-3 ">
                                            <label class="form-group" for="From Date" style="color:white">From Date</label>
                                            <input name="from_date" class="form-control" type="date" id="from_date">

                                        </div>
                                        <div class="form-group col-md-3">
                                            <label class="form-group" for="To Date" style="color:white">To Date</label>
                                            <input name="to_date" class="form-control" type="date" id="to_date">
                                        </div>

                                        
                                        <div class="form-group col-md-12" style="margin-top: 36px;">
                                         
                                        <button onclick="getDate(this)" type="button"
                                                class="btn btn-success" value="search">
                                                Search
                                        </button>
                                        <button  
                                                class="btn btn-success" type="submit" name="export" value='excel'>Excel</button>
                                        
                                        <button type="sumbit" class="btn btn-primary" name='export' value='upfront'>
                                            <span class="glyphicon glyphicon-export" ></span> Export Upfront
                                     </button>
                                      <button type="sumbit" class="btn btn-primary" name='export' value='pretxn-a'>
                                    <span class="glyphicon glyphicon-export" ></span> Export PerTxn(A)</button>
                              <button type="sumbit" class="btn btn-primary" name='export' value='pretxn-m'>
                                    <span class="glyphicon glyphicon-export" ></span> Export PerTxn(M)</button>
                               <button type="sumbit" class="btn btn-primary" name='export' value='varification'>
                                    <span class="glyphicon glyphicon-export" ></span> Export
                             Varification</button><br>
                             </div>
                             <span id="span_summary" class="form-group col-md-12" style="display:none">
                             <button type="sumbit" class="btn btn-info" name='export' value='upfront_summary'>
                                    <span class="glyphicon glyphicon-export" ></span> Export
                             UpFront Summary</button>
                             <button type="sumbit" class="btn btn-info" name='export' value='pertxn_a_summary'>
                                    <span class="glyphicon glyphicon-export" ></span> Export
                             Ptx(A) Summary</button>
                             <button type="sumbit" class="btn btn-info" name='export' value='pertxn_m_summary'>
                                    <span class="glyphicon glyphicon-export" ></span> Export
                             Ptx(M)Summary</button>
                             <button type="sumbit" class="btn btn-info" name='export' value='varification_summary'>
                                    <span class="glyphicon glyphicon-export" ></span> Export
                             Varificatin Summary</button>
                            
                           </span>
                           <span class="form-group col-md-12" style="display:none">
                            <button type="sumbit" class="btn btn-info" name='export' value='new_pertxn_a_summary'>
                                    <span class="glyphicon glyphicon-export" ></span> New Export
                             Ptx(A) Summary</button>
                           </span>
                     
                                    </form>
                                    </div>
                                </div>
                                 
                            </div>

                        </div>
                    <!-- </div> -->
                        <div class="container">
                            <h4 style="color: #a56b15; font-family: time;">Result of : <span id="span_date">  {{ @$results->start_date}} To {{ @$results->end_date }}</span>
                          <div id="loading" style="padding-left: 20%;"></div>
                        <table style="width: 100%;">
                        <tr>
                            <td style="width: 50%;">
                                <h2>Revenue</h2>
                          <table class="table table-bordered">
                            
                          <tr class="success tr-text-fromat">
                              <td>Name </td>
                              <td>No of Txn</td>
                              <td>Revenue</td>
                          </tr>
                          <tr class="info">
                            <td>DT & Upfront </td>
                            <td><span id="upfront_txn">{{ $results->upfront_txn}}</span></td> 
                            <td><span id='upfront_profit'>{{ $results->upfront_profit }}</span> </td>
                          </tr>
                          <tr class="active">
                            <td>DMT 1 </td>
                            <td><span id="shighra_f_txn">{{ $results->shighra_f_txn}}</span></td> 
                            <td> <span id='shighra_f_profit'>{{ $results->shighra_f_profit }} </span></td></tr>
							 <tr class="active">
                            <td>DMT 2 </td>
                            <td><span id="shighra_f_txn">{{ $results->shighra_p_txn}}</span></td> 
                            <td> <span id='shighra_f_profit'>{{ $results->shighra_p_profit }} </span></td></tr>
                          <tr class="info">
                            <td>Bank Charge</td>
                            <td></td> 
                            <td><span id='bank_charge'>0</span></td>
                          </tr> 

                         
                          <tr class="success">
                            <td>Varification </td>
                            <td><span id="varification_txn">{{ $results->varification_txn}}</span></td> 
                            <td><span id='varificaiton_amount'>{{ $results->varificaiton_amount}}</span></td>
                          </tr>
                            
                           
                            <tr class="danger">
                              <td>Total</td> 
                              <td><span id='total_revenue_txn'>{{$results->total_revenue_txn}}</span></td>
                              <td><span id='total_revenue'>{{$results->total_revenue}}</span></td></tr>

                          </table>
                        </td>
                       
                        <td style="padding-left: 10px;">
                          <h2>Expenses</h2>
                                
                          <table class="table table-bordered ">
                            
                            <tr class="success tr-text-fromat">
                              <td>Name </td>
                              <td>No of Txn</td>
                              <td>Expenses</td>
                          </tr>
						   <tr class="info">
                              <td><span id="eko_f">0</span></td>
                              <td><span id="exp_shighra_f_txn">0</span></td> 
                              <td><span id='exp_shighra_f_profit'>0</span></td>
                            </tr>
                            <tr class="info">
                              <td><span id="eko_f">DMT 1</span></td>
                              <td><span id="exp_shighra_f_txn">{{ $results->exp_shighra_f_txn}}</span></td> 
                              <td><span id='exp_shighra_f_profit'>{{ $results->exp_shighra_f_profit}}</span></td>
                            </tr>

                            <tr class="active">
                              <td> <span id="eko_s">DMT 2</span></td> 
                              <td><span id="exp_shighra_p_txn">{{ $results->exp_shighra_p_txn}}</span></td>
                              <td> <span id='exp_shighra_p_profit'>{{ $results->exp_shighra_p_profit}}</span></td></tr>

                           
						   <tr class="info">
                             
							    <td></td> 
                              <td><span id='total_exp_txn'>0</span></td>
                              <td><span id='total_exp_exp'>0</span></td>
                            </tr>
                            <tr class="success">
                             <td>Varification</td> 
                              <td><span id="expenses_varification_txn">{{ $results->expenses_varification_txn}}</span></td> 
                              <td><span id='expenses_varification'> {{ $results->expenses_varification }}</span></td>
                            </tr>
                            


                             <tr class="danger">
                              <td>Total</td>
                               <td><span id="total_exp_txn">{{ $results->total_exp_txn}}</span></td> 
                              <td><span id='total_expenses'>{{ $results->total_revenue }}</span></td>
                            </tr>
                           


                          </table>
                           </td> </tr></table>
 
                       </div>
                       <div style="padding-left: 6%; font-size: 18px;font-family: initial;color: white; background: #795548;">TOTAL BALANCE : <span id="tatal_amount">{{ @$results->tatal_amount }}</span>
                       </div>
                       <div style="padding-left: 6%; font-size: 18px;font-family: initial;color: white; background: gray;">Revenue - Expenses : <span id="profit">{{ @$results->profit }}</span>
                       </div>
                      

                    </div>

            </div>

        </div>

    @endsection
    @endif