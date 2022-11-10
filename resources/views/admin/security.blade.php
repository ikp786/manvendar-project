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
                        <div class="col-md-12">
                            <div class="col-md-6">
							<p>(Closing balance with date)</p>
                                <form method="get" action="{{ url('export-cl-bln') }}">
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
							
							<div class="col-md-6">
							<p>(Export Opening balance)</p>
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
						 <div class="col-md-12">
                            <div class="col-md-6">
							<p>(Closing balance exept choosing date)</p>
                                <form method="get" action="{{ url('cl_bln_ex_date') }}">
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
							
							<div class="col-md-6">
							<p>(Export failure Balance)</p>
                                <form method="get" action="{{ url('failure-amount') }}">
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
                            
                        </div>

						<div class="col-md-12">
                            <div class="col-md-6">
							<p>( Export balance for admin to distt)</p>
                                <form method="get" action="{{ url('admin-to-dist') }}">
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
							
							<div class="col-md-6">
							<p>( Export balance for Distributor to Agent)</p>
                                <form method="get" action="{{ url('distt-to-agent') }}">
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
                            
                        </div>
						
						<div class="col-md-12">
                            <div class="col-md-6">
							<p>( Export Refundable Balance)</p>
                                <form method="get" action="{{ url('refunded-balance') }}">
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
							
							<div class="col-md-6">
							<p>( Export Balance for Distt to all Agents)</p>
                                <form method="get" action="{{ url('distt-to-all-agent') }}">
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
										<select class="form-control" name="select_users">
										<option value="">Select User</option>
										@foreach($all_users as $value)
										<option value="{{ $value->id}}">{{ $value->name}} ({{$value->id}})</option>
										@endforeach
										</select>
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
						
						<th data-field="op_date" data-align="center" data-sortable="true"
                            data-sorter="priceSorter">closing Date
                        </th>
						
						
                        <th data-field="op_balance" data-align="center" data-sortable="true"
                            data-sorter="priceSorter">Closing Balance
                        </th>
						
						
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($main_report as $key => $value)
                        <tr>
                            <td>{{ $value->id }}</td>
                            <td>{{ $value->user->name }} ({{$value->user->id}})</td>
							<td>{{ $value->created_at }}</td>
							<td>{{ number_format($value->total_balance,2) }}</td>
							
							
                    @endforeach
                    </tbody>
                </table>
				</div>

        </div>
    </div>

@endsection