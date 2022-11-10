@if(Auth::user()->role->id ==1)
    @extends('admin.layouts.templatetable')
    @section('title','Transaction Missmatched')
    @section('content')
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
             var url="{{url('admin/transaction-missmatched')}}";
                        $.ajax({
                        type: "get",
                        url: url,
                        data: dataString,
                        
                       
                        beforeSend: function () {
                       
                        },
                        complete: function(){
                            },
                        success: function (msg) {
                          //  alert((msg.result)->date);
                            console.log(msg)
                            
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
        <div class="row">
            <div class="col-sm-12">
                <div class="col-lg-6 col-md-6">
                    <h4 class="page-title" style="color: white; font-size: 36px;">{{ 'Transaction Missmatched' }}</h4>
                    
                </div>
               
            </div>
        </div><br>
        

        <!--Basic Columns-->
        <!--===================================================-->


        <!--===================================================-->
        <div class="row">
            <div class="col-sm-12">
                <div class="card-box">
                    <div class="panel panel-default">
                        <div class="panel-body">
                            <div class="col-md-12">
                                <div class="col-md-10">
                                    <form action="{{url('admin/transaction-missmatched')}}" method="get">
                                        <div class="form-group col-md-4">
                                            <label class="form-group" for="From Date">From Date</label>
                                            <input name="from_date" class="form-control" type="date" id="from_date">

                                        </div>
                                        <div class="form-group col-md-4">
                                            <label class="form-group" for="To Date">To Date</label>
                                            <input name="to_date" class="form-control" type="date" id="to_date">
                                        </div>
                                        <div class="form-group col-md-2">

                                        <button  type="sumbit"
                                                class="btn btn-success" name="search" value="search">
                                                Search
                                        </button>
                                        <button  
                                                class="btn btn-success" type="submit" name="excel">
                                                Excel
                                        </button>
                                    </form>
                                    </div>
                                </div>
                                 
                            </div>

                        </div>
                    </div>
                        <div class="container">
                          <table id="demo-custom-toolbar" data-toggle="table"
                       data-toolbar="#demo-delete-row"
                       data-search="true"
                       data-show-export="true"
                       data-page-list="[10, 10, 10,10,10,10,10,10]"
                       data-page-size="80"
                       data-pagination="true" class="table-bordered ">
                    <thead>
                    <tr>
                        <th data-field="id" data-sortable="true">Id </th>
                        <th data-field="user" data-sortable="true">User</th>
                        <th data-field="mobile" data-sortable="true">Profit</th>
                        <th data-field="company" data-sortable="true">B Charge</th>
                        <th data-field="number" data-sortable="true" >Number</th>
                        <th data-field="status" data-sortable="true">Status</th>
                        <th data-field="amount" data-sortable="true">Amount</th>
                        <th data-field="api" data-sortable="true">API</th>
                        <th data-field="crated-at" data-sortable="true">Crated At</th>
                        <th data-field="txn" data-sortable="true">Txn Id</th>
                        <th data-field="total_balance" data-sortable="true">T Balance</th>
                        <th data-field="total_balance 2" data-sortable="true">T B 2</th>
                        <th data-field="diff_ balance" data-sortable="true">Diff</th>
                    </tr>
                    </thead>

                    <tbody>
                    @foreach($reports as $key => $value)
                    <tr>
                            <?php $index=$key;?>
                            <td>{{ $value->id }}</td>
                            <td>{{ $value->user->name }}</td>
                            <td>{{ $value->profit }}</td>
                            <td>{{ $value->bank_charge }}</td>
                            <td>{{ $value->number }}</td>
                            <td>{{ $value->status->status }}</td>
                            <td>{{ round($value->amount,2) }}</td>
                            <td>{{ $value->api->api_name }}</td>
                            <td>{{ $value->created_at }}</td>
                            <td>{{ $value->txnid }}</td>
                            <td>{{ round($value->total_balance,2)}}</td>
                            <td>{{ round($value->total_balance2,2)}}</td>
                            <td><?php 
if($index>0)
{
    if($userId != $value->id)
    {
        if($value->status_id == 6)
        {

        }
        else
        {
            $tbl1=(($reports[--$index]->total_balance) - ($value->profit + $value->bank_charge + $value->amount + $value->total_balance));
            if($tbl1 > 3)
            {
               echo ($reports[$index]->total_balance - ($value->profit + $value->bank_charge + $value->amount + $value->total_balance)); 
               echo", Something worng";
            }
            else
            {
                if(in_array($value->api_id,array(1,8,13)))
                {
                    if(($reports[$index]->total_balance2 - ($value->profit + $value->bank_charge + $value->amount + $value->total_balance2)) > 2)
                        {
                             echo ($reports[$index]->total_balance2 - ($value->profit + $value->bank_charge + $value->amount + $value->total_balance2));
                            echo " , wrong";
                        }
                        else
                        {
                            echo round(($reports[$index]->total_balance2 - ($value->profit + $value->bank_charge + $value->amount + $value->total_balance2)),2);
                            echo " , R Right";

                        }
                }
                else
                {
                    echo ($reports[$index]->total_balance - ($value->profit + $value->bank_charge + $value->amount + $value->total_balance));
                    echo " , Right";
                }
            }
        }
     }
 }
            $userId=$value->id;
?></td>
                           
                                                     
                     </tr>  
                    @endforeach

                    </tbody>
                </table>
                       </div>
                       
                      

                    </div>

            </div>

        </div>

    @endsection
    @endif