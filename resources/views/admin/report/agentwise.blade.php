@extends('admin.layouts.templatetable')

@section('content')

<!-- Page-Title -->
        <div class="row">
            <div class="col-sm-12">
                <div class="col-lg-6 col-md-6">
                    <h4 class="page-title">{{ $page_title or 'Agent Detail' }}</h4>
                    <ol class="breadcrumb">
                        <li>
                            <a href="{{ url('dashboard') }}">Home</a>
                        </li>
                        <li class="active">
                            {{ $page_title or 'Agent Report' }}
                        </li>
                    </ol>
                </div>
                <div class="col-lg-6 col-md-6">
                    <div class="pull-right">
                        <button onclick="add_record()" id="demo-add-row" class="btn btn-success"><i
                                    class="fa fa-plus m-r-5"></i>Add Record
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!--Basic Columns-->
        <!--===================================================-->


        <!--===================================================-->
        <div class="row">
            <div class="col-sm-12">
			
            <div class="panel panel-default">
                    <div class="panel-body">
                        <div class="col-md-12">
                            <div class="col-md-8">
                                <form method="get" action="{{ url('searchall_txn_wise') }}">
                                    <div class="form-group col-md-4">
                                        <input name="fromdate" class="form-control" type="date">
                                    </div>
                                    <div class="form-group col-md-4">
                                        <input name="todate" class="form-control" type="date">
                                    </div>
                                    <div class="form-group col-md-2">
                                        <button name="export" value="export" type="submit"
                                                class="btn btn-success waves-effect waves-light m-l-10 btn-md"><span
                                                    class="glyphicon glyphicon-find"></span>Export
                                        </button>
                                    </div>
                                    <div style="float:left;"><p>All txn wise Invoice</p></div>
                                </form>
                            </div>
                            <script>

                            </script>
                        </div>

                    </div>
                </div>
			
                <div class="panel panel-default">
                    <div class="panel-body">
                        <div class="col-md-12">
                            <div class="col-md-8">
                                <form method="get" action="{{ url('searchall_txnperuser_wise') }}">
                                    <div class="form-group col-md-4">
                                        <input name="fromdate" class="form-control" type="date">
                                    </div>
                                    <div class="form-group col-md-4">
                                        <input name="todate" class="form-control" type="date">
                                    </div>
                                    <div class="form-group col-md-2">
                                        <button name="export" value="export" type="submit"
                                                class="btn btn-success waves-effect waves-light m-l-10 btn-md"><span
                                                    class="glyphicon glyphicon-find"></span>Export
                                        </button>
                                    </div>
                                    <div style="float:left;"><p>All txn per user wise Invoice</p></div>
                                </form>
                            </div>
                            <script>

                            </script>
                        </div>

                    </div>
                </div>


			<div class="panel panel-default">
                    <div class="panel-body">
                        <div class="col-md-12">
                            <div class="col-md-8">
                                <form method="get" action="{{ url('searchall_md_wise') }}">
                                    <div class="form-group col-md-4">
                                        <input name="fromdate" class="form-control" type="date">
                                    </div>
                                    <div class="form-group col-md-4">
                                        <input name="todate" class="form-control" type="date">
                                    </div>
                                    <div class="form-group col-md-2">
                                        <button name="export" value="export" type="submit"
                                                class="btn btn-success waves-effect waves-light m-l-10 btn-md"><span
                                                    class="glyphicon glyphicon-find"></span>Export
                                        </button>
                                    </div>
									<div style="float:left;"><p>Per txn MD wise Invoice</p></div>
                                </form>
                            </div>
                            <script>

                            </script>
                        </div>

                    </div>
                </div>
			
			

                <div class="panel panel-default">
                    <div class="panel-body">
                        <div class="col-md-12">
                            <div class="col-md-8">
                                <form method="get" action="{{ url('searchall_distributor_wise') }}">
                                    <div class="form-group col-md-4">
                                        <input name="fromdate" class="form-control" type="date">
                                    </div>
                                    <div class="form-group col-md-4">
                                        <input name="todate" class="form-control" type="date">
                                    </div>
                                    <div class="form-group col-md-2">
                                        <button name="export" value="export" type="submit"
                                                class="btn btn-success waves-effect waves-light m-l-10 btn-md"><span
                                                    class="glyphicon glyphicon-find"></span>Export
                                        </button>
                                    </div>
									<div style="float:left;"><p>Disributor wise Invoice</p></div>
                                </form>
                            </div>
                            <script>

                            </script>
                        </div>

                    </div>
                </div>
				
				 <div class="panel panel-default">
                    <div class="panel-body">
                        <div class="col-md-12">
                            <div class="col-md-6">
                                <form method="get" action="{{ url('searchall_agent_wise') }}">
                                    <div class="form-group col-md-4">
                                        <input name="fromdate" class="form-control" type="date">
                                    </div>
                                    <div class="form-group col-md-4">
                                        <input name="todate" class="form-control" type="date">
                                    </div>
                                    <div class="form-group col-md-2">
                                        <button name="export" value="export" type="submit"
                                                class="btn btn-success waves-effect waves-light m-l-10 btn-md"><span
                                                    class="glyphicon glyphicon-find"></span>Export
                                        </button>
                                    </div>
                                 
									<div style="float:left;"><p>Agent wise Invoice(Success)</p></div>
                                </form>
                            </div>

                            <div class="col-md-6">
                                <form method="get" action="{{ url('searchall_agent_wise_refunded') }}">
                                    <div class="form-group col-md-4">
                                        <input name="fromdate" class="form-control" type="date">
                                    </div>
                                    <div class="form-group col-md-4">
                                        <input name="todate" class="form-control" type="date">
                                    </div>
                                    <div class="form-group col-md-2">
                                        <button name="export" value="export" type="submit"
                                                class="btn btn-success waves-effect waves-light m-l-10 btn-md"><span
                                                    class="glyphicon glyphicon-find"></span>Export
                                        </button>
                                    </div>
                                 
                                    <div style="float:left;"><p>Agent wise Invoice(Refunded)</p></div>
                                </form>
                            </div>
                            <script>

                            </script>
                        </div>

                    </div>
                </div>
				
				<div class="panel panel-default">
                    <div class="panel-body">
                        <div class="col-md-12">
                            <div class="col-md-8">
                                <form method="get" action="{{ url('searchall_verify_wise') }}">
                                    <div class="form-group col-md-4">
                                        <input name="fromdate" class="form-control" type="date">
                                    </div>
                                    <div class="form-group col-md-4">
                                        <input name="todate" class="form-control" type="date">
                                    </div>
                                    <div class="form-group col-md-2">
                                        <button name="export" value="export" type="submit"
                                                class="btn btn-success waves-effect waves-light m-l-10 btn-md"><span
                                                    class="glyphicon glyphicon-find"></span>Export
                                        </button>
                                    </div>
                                   
									<div style="float:left;"><p>Verify wise Invoice</p></div>
                                </form>
                            </div>
                            <script>

                            </script>
                        </div>

                    </div>
                </div>
				
                <div class="card-box">
                    <h4 class="m-t-0 header-title"><b>{{ $page_title or 'Provider Detail' }}</b></h4>
                    <p class="text-muted font-13">
                        All Recharge Detail
                    </p>

                    <button id="demo-delete-row" class="btn btn-danger" disabled><i
                                class="fa fa-times m-r-5"></i>Delete
                    </button>
                    <table style="float:left;" id="demo-custom-toolbar" data-toggle="table"
                           data-toolbar="#demo-delete-row"
                           data-search="true"
                           data-show-refresh="true"
                           data-show-toggle="true"
                           data-show-columns="true"
                           data-sort-name="id"
                           data-page-list="[5, 10, 20]"
                           data-page-size="50"
                           data-pagination="true" data-show-pagination-switch="true" class="table-bordered ">
                        <thead>
                        <tr>
                        
							
							<th data-field="Parentid" data-sortable="true">Parent ID</th>
						    
							<th data-field="Agent ID" data-sortable="true">Agent ID</th>
                            
							<th data-field="provider" data-sortable="true">Agent Name</th>
							
							<th data-field="agentcontact" data-sortable="true">Agent Co. no.</th>

                            <th data-field="amount" data-sortable="true">Total TXN</th>
                        </tr>
                        </thead>

                        <tbody>
               
                            @foreach($reports as $key => $value)
                            <tr>
                                <td>{{ $value->user->parent_id }}</td>
								<td>{{ $value->id }}</td>
                                <td>{{ $value->user->name }}</td>
								<td>{{ $value->user->mobile }}</td>
								<td>{{ $value->saral_amount}}</td>
								
								</tr>  		
                 	@endforeach
					
 				   
                        </tbody>

                    </table>
					
				
					 
                </div>
            </div>
        </div>
    <!-- END wrapper -->
    <meta name="_token" content="{!! csrf_token() !!}"/>
@endsection