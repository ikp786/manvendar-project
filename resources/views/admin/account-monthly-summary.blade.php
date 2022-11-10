@if(Auth::user()->role->id ==1)
    @extends('admin.layouts.templatetable')
    @section('title','Account Monthly Salary')
    @section('content')
        <script>
          function getUserDetail(id)
          {

             var dataString = 'id=' + id ;
             var url="{{url('admin/getUserDetils')}}";
            $.ajax({

                        type: "get",
                        url: url,
                        data: dataString,
                        
                       
                        beforeSend: function () {
                        
                        },
                        complete: function(){
                           },
                        success: function (msg) {
                         $('#retailer').text(msg.result.retailer);
                            $('#distributor').text(msg.result.distributor);
                            $('#md').text(msg.result.md);
                            $('#guardian').text(msg.result.guardian);
                            $('#api_user').text(msg.result.api_user);
                            $('#user_relations').modal('show'); 
                        },
                         error: function (jqXHR, exception) 
                         {
                        }
                    });
            }
          
            </script>



        <!-- Page-Title -->
        <div class="row">
            <div class="col-sm-12">
                <div class="col-lg-6 col-md-6">
                    <h4 class="page-title" style="color: white; font-size: 36px;">{{ 'Account Monthly Summary' }}</h4>
                    
                </div>
               
            </div>
        </div><br>
        

        <!--Basic Columns-->
        <!--===================================================-->


        <!--===================================================-->
        <div class="row">
            <div class="col-sm-12">
                <div class="card-box">
				<h3><span style="font-family:time">Details of user with <u>number of transactions</u>, <u>no of days</u> and <u>total transactions amount</u> between two dates.</span></h3>
                    <div class="panel panel-default">
                        <div class="panel-body">
                            <div class="col-md-12">
                                <div class="col-md-10">
                                    <form action="{{url('admin/account-monthly-summary')}}" method="get">
                                        <div class="form-group col-md-4">
                                            <label class="form-group" for="From Date">From Date</label>
                                            <input name="from_date" class="form-control" type="date" id="from_date">

                                        </div>
                                        <div class="form-group col-md-4">
                                            <label class="form-group" for="To Date">To Date</label>
                                            <input name="to_date" class="form-control" type="date" id="to_date">
                                        </div>
                                        <div class="form-group col-md-2">

                                        <button type="submit"
                                                class="btn btn-success" name="search" value="search">
                                                Search
                                        </button>
                                        <button type="submit"
                                                class="btn btn-success" value="export" name="export">
                                                Export
                                        </button>
                                        
                                    </form>
                                    </div>
                                </div>
                                 
                            </div>

                        </div>
                    </div>
                        <div class="container">
                           <h3 style="font-family: time;"> Result of : <span id="span_date" style="color: #a56b15;">  {{ @$results['start_date']}} To {{ @$results['end_date'] }}</span></h3>
                            <table id="demo-custom-toolbar" data-toggle="table"
                               data-toolbar="#demo-delete-row"
                               data-search="true"
                               data-show-export="true"
                               data-page-list="[10, 20, 30]"
                               data-page-size="80"
                               data-pagination="true" class="table-bordered ">
                            <thead>
                            <tr>
                                <th data-field="user Id" data-sortable="true">User Id </th>
                                <th data-field="Account Number" data-sortable="true">Account No</th>
                                <th data-field="no_of_days" data-sortable="true">No of Days</th>
                                <th data-field="no_of_txn" data-sortable="true">No of Txn</th>
                                <th data-field="amount" data-sortable="true">Amount</th>                 
                            </tr>
                            </thead>
                            <tbody>
                                @foreach($account_report as $key => $value)
                                <tr>

                                        <td><span style="color: #2196F3; cursor: pointer;" onClick="getUserDetail({{ $value->user_id }})">{{ @$value->user->name }} ({{ $value->user_id }})</span> </td>
                                       
                                        <td>
                                           
                                            {{ $value->number}}
                                        </td>
                                        <td>{{ $value->no_of_days }}</td>
                                        <td>{{ $value->no_of_txn }}</td>
                                        <td>{{  round($value->total_amount,3) }}</td>
                                        
                                       
                                        
                                 </tr>  
                                @endforeach
                            </tbody>
                            </table>
                            <div class="modal fade" id="user_relations" role="dialog">
                            <div class="modal-dialog">
                            
                              <!-- Modal content-->
                              <div class="modal-content">
                                <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal">&times;</button>
                                  <h4 class="modal-title">Relations</h4>
                                </div>
                                <div class="modal-body">
                                    <table class="table table-bordered">
                                      <tr class="info">
                                        <td> Api User </td><td><sapn  id="api_user"></sapn></td>
                                      </tr>
                                      <tr class="success">
                                        <td> Retailer </td><td><sapn id="retailer"></sapn></td>
                                      </tr>
                                  <tr class="active"><td> Distributor </td><td><sapn  id="distributor"></sapn></td></tr>
                                  <tr class="success"><td> Master Distributor </td><td><sapn  id="md"></sapn></td></tr>
                                  <tr class="info"><td> Parent </td><td><sapn  id="guardian"></sapn></td>
                                  
                                  </tr></table>
                                </div>
                                <div class="modal-footer">
                                  <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                </div>
                              </div>
                              
                            </div>
                          </div>
                        </div>
                     </div>
            </div>
        </div>

    @endsection
    @endif