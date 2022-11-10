@if(Auth::user()->role_id = 1)
@extends('admin.layouts.templatetable')


@section('content')
   
    <div class="pg-opt">
        <div class="container">
            <div class="row">
                <div class="col-md-6">
                    <h1 style="color: white;">Refunded Reports</h1>
                </div>
            </div>
        </div>
    </div>
    <div class="panel panel-default">
        <div class="panel-body">
            <div class="col-md-12">
                <div class="col-md-4">
                    <form method="get" action="{{ url('refunded-reports') }}" class="form-inline" role="form">
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
                    </form><br>
					
						
                </div>
                <div class="col-md-8">
                    <form method="get" action="{{ urlrefund-pending-reports') }}">
                        <div class="form-group col-md-4">
                            <input name="from_date" class="form-control" type="date">
                        </div>
                        <div class="form-group col-md-4">
                            <input name="to_date" class="form-control" type="date">
                        </div>
                        <div class="form-group col-md-2">
                            <button name="export" value="Refunded Reports" type="submit"
                                    class="btn btn-success waves-effect waves-light m-l-10 btn-md"><span
                                        class="glyphicon glyphicon-find"></span>Export
                            </button>
                        </div>
                        <div class="form-group col-md-2">
                            <button value="search" name="search" type="submit"
                                    class="btn btn-success waves-effect waves-light m-l-10 btn-md"><span
                                        class="glyphicon glyphicon-find"></span>Search
                            </button>
                        </div>
						
						
                    </form>

                    
                        
                                        
                </div>

            </div>

        </div>
    </div>
    <div class="row">
        
        <div class="card-box">
            <table data-toggle="table"
                   data-search="true"
                   data-page-list="[10, 10, 20]"
                   data-page-size="40">
                <thead>
                <tr>
                <th data-field="state" data-checkbox="true" class="txncheckbox"></th>

                    <th data-field="date" data-sortable="true" data-formatter="dateFormatter">Order
                        Date
                    </th>
					<th data-field="ids" data-sortable="true">ID
                    </th>
                    <th data-field="ref_id" data-sortable="true">Ref ID
                    </th>
                    <th data-field="txn_id" data-sortable="true">Txn ID
                    </th>
                    <th data-field="mobilenumber" data-sortable="true">Mobile Number</th>
                    <th data-field="amount" data-sortable="true">Amount</th>

                </tr>
                </thead>
                <tbody>
              
                @foreach($refund_pend as $report)
                    <tr>
                    <td>{{ $report->id }}</td>
                        <td>{{ $report->created_at }}</td>
						<td>{{ $report->user_id }}</td>
                      
                        <td>{{ $report->number }}</td>
                        <td>{{ $report->status->status }}</td>
                        <td>{{ $report->amount }}</td>
                       
                    </tr>
                 
                @endforeach
                </tbody>
            </table>
           
        </div>
    </div>

@endsection
@endif