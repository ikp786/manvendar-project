@extends('admin.layouts.templatetable')

@section('content')
 <div class="row">
        <div class="col-sm-12">
            <div class="col-lg-6 col-md-6">
                <h4 class="page-title">{{ $page_title or 'Security Detail' }}</h4>
                <ol class="breadcrumb">
                    <li>
                        <a href="{{ url('dashboard') }}">Home</a>
                    </li>
                    <li class="active">
                        {{ $page_title or 'Security Detail' }}
                    </li>
                </ol>
            </div>
            <div class="col-lg-6 col-md-6">
                <div class="pull-right">
                    <button onclick="add_record()" id="demo-add-row" class="btn btn-success"><i
                                class="fa fa-plus m-r-5"></i>Add Member
                    </button>
                </div>
            </div>
        </div>
    </div>
 
 <div class="row">
        <div class="col-sm-12">
            
			  <div class="panel panel-default">
                    <div class="panel-body">
					<center><p>Search And Export Opening Balance</p></center><br>
                        <div class="col-md-12">
                            <div class="col-md-6">
                                <form method="get" action="{{ url('admin/security') }}">
                                    <div class="form-group col-md-4">
                                        <input name="fromdate" class="form-control" type="date">
                                    </div>
                                   
                                    <div class="form-group col-md-2">
                                        <button value="search" name="search" type="submit"
                                                class="btn btn-success waves-effect waves-light m-l-10 btn-md"><span
                                                    class="glyphicon glyphicon-find"></span>Search
                                        </button>
                                    </div>
                                </form>
                            </div>
							
							<div class="col-md-6">
                                <form method="get" action="{{ url('export-op-bln') }}">
                                    <div class="form-group col-md-4">
                                        <input name="fromdate" class="form-control" type="date">
                                    </div>
                                   
                                    <div class="form-group col-md-2">
                                        <button value="export" name="export" type="submit"
                                                class="btn btn-success waves-effect waves-light m-l-10 btn-md"><span
                                                    class="glyphicon glyphicon-find"></span>Export
                                        </button>
                                    </div>
                                </form>
                            </div>
                            <script>

                            </script>
                        </div>

                    </div>
                </div>
				
				<div class="panel panel-default">
                    <div class="panel-body">
						<center><p>Running Balance</p></center><br>
                        <div class="col-md-12">
					
                            <div class="col-md-12">
                                <form method="get" action="{{ url('admin/running') }}">
                                    <div class="form-group col-md-5">
										
                                    </div>
                                    <div class="form-group col-md-2">
                                        <input name="fromdate" class="form-control" type="date">
                                    </div>
									 <div class="form-group col-md-2">
                                        <input name="todate" class="form-control" type="date">
                                    </div>
                                    <div class="form-group col-md-3">
                                        <button value="export" name="export" type="submit"
                                                class="btn btn-success waves-effect waves-light m-l-10 btn-md"><span
                                                class="glyphicon glyphicon-find"></span>Export
                                        </button>
                                    </div>
                                </form>
                            </div>

                            <script>

                            </script>
                        </div>

                    </div>
                </div>
				
			<div class="card-box">
                <table id="demo-custom-toolbar" data-toggle="table"
                       data-toolbar="#demo-delete-row"
                       data-search="true"
                       data-show-refresh="true"
                       data-show-toggle="true"
                       data-show-columns="true"
                       data-page-list="[20, 10, 20]"
                       data-page-size="10"
                       data-pagination="true" data-show-pagination-switch="true" class="table-bordered ">
                    <thead>
                    <tr>
                        <th data-field="id" data-sortable="true">
                            ID
                        </th>
                        <th data-field="name" data-sortable="true">Name</th>
                        <th data-field="date" data-sortable="true" data-formatter="dateFormatter">Account
                        </th>
						
						<th data-field="op_date" data-align="center" data-sortable="true"
                            data-sorter="priceSorter">Opening Date
                        </th>
						
						<th data-field="cl_balance" data-align="center" data-sortable="true"
                            data-sorter="priceSorter">Closing Balance
                        </th>
                        <th data-field="op_balance" data-align="center" data-sortable="true"
                            data-sorter="priceSorter">Opening Balance
                        </th>
						<th data-field="txn" data-align="center" data-sortable="true"
                            data-sorter="priceSorter">Total Txn
                        </th>
						<th data-field="amount" data-align="center" data-sortable="true"
                            data-sorter="priceSorter">Total amount
                        </th>
						
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($employees1 as $key => $value)
                        <tr>
                            <td>{{ $value->id }}</td>
                            <td>{{ $value->user->name }} ({{$value->user->id}})</td>
                            <td></td>
							<td>{{ $value->created_at }}</td>
							<td></td>
							<td>{{ number_format($value->total_balance,2) }}</td>
							<td>{{ $value->transection }}</td>
							<td>{{ number_format($value->t_amount,2) }}</td>
							
                    @endforeach
                    </tbody>
                </table>
				
				<table id="demo-custom-toolbar" data-toggle="table"
                       data-toolbar="#demo-delete-row"
                       data-search="true"
                       data-show-refresh="true"
                       data-show-toggle="true"
                       data-show-columns="true"
                       data-page-list="[20, 10, 20]"
                       data-page-size="10"
                       data-pagination="true" data-show-pagination-switch="true" class="table-bordered ">
                    <thead>
                    <tr> 
						<th data-field="amount" data-align="center" data-sortable="true"
                            data-sorter="priceSorter">Total amount
                        </th>
						
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($employees2 as $key => $value1)
                        <tr>
                            <td>{{ $value1->d_amount }}</td>
                            
							
                    @endforeach
                    </tbody>
                </table>

            </div>
        </div>
    </div>

@endsection