@extends('admin.layouts.templatetable')

@section('content')
<style>
hr
{
	margin-top:0px !important;
    border: 0;
    border-top: 2px solid orange !important;
}
</style>
        <!-- Page-Title -->
        <div class="row">
            <div class="col-sm-12">
                <div class="col-lg-6 col-md-6">
                    <h4 class="page-title" style="color: white; font-size: 36px;">{{ $page_title or 'PRODUCT MONTH WISE' }}</h4>
                </div>
                <div class="col-lg-6 col-md-6">
                    <!-- <div class="pull-right">
                        <button onclick="add_record()" id="demo-add-row" class="btn btn-success"><i
                                    class="fa fa-plus m-r-5"></i>Add Record
                        </button>
                    </div> -->
                </div>
            </div>
        </div><br>

        <!--Basic Columns-->
        <!--===================================================-->


        <!--===================================================-->
        <div class="row">
            <div class="col-sm-12">

                <div class="panel panel-default">
                    <div class="panel-body">
                        <div class="col-md-12">
                            <div class="col-md-8">
                               
								<form method="get" action="{{ url('searchall_agent_sharp') }}">
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
                                     <div class="form-group col-md-4">
                                        <p>Sharp Monthly Amount</p>
                                    </div>
                                </form>
                            </div>
						
							<div class="col-md-8"><hr>
                                <form method="get" action="{{ url('searchall_agent_smart') }}">
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
                                    <div class="form-group col-md-4">
                                        <p>Smart Monthly Amount</p>
                                    </div>
                                </form>
								
								
								
                            </div>
							
							<div class="col-md-8"><hr>
                                <form method="get" action="{{ url('searchall_agent_saral') }}">
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
                                    <div class="form-group col-md-4">
                                       <p>Saral Monthly Amount</p>
                                    </div>
                                </form>
								
								
								
                            </div>
							
							<div class="col-md-8"><hr>
                                <form method="get" action="{{ url('searchall_udio_report') }}">
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
                                    <div class="form-group col-md-4">
                                       <p>Udio Recharge Report</p>
                                    </div>
                                </form>
								
								
								
                            </div>
							
							<div class="col-md-8"><hr>
                                <form method="get" action="{{ url('searchall_udio_monthly') }}">
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
                                    <div class="form-group col-md-4">
                                       <p>Udio monthly wise</p>
                                    </div>
                                </form>
								
								
								
                            </div>
							
								<div class="col-md-8"><hr>
                                <form method="get" action="{{ url('searchall_recharge_monthly') }}">
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
                                    <div class="form-group col-md-4">
                                       <p>Recharge monthly wise</p>
                                    </div>
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
                    <table id="demo-custom-toolbar" data-toggle="table"
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
                            <th data-field="state" data-checkbox="true"></th>
                            <th data-field="provider" data-sortable="true">Agent Name</th>
                            <th data-field="amount" data-sortable="true">Total Amount (SHARP)</th>
                        </tr>
                        </thead>

                        <tbody>
                        @foreach($reports as $key => $value)
                            <tr>
                                <td>{{ $value->id }}</td>
                                <td>{{ $value->user->name }}</td>
                                <td>{{ $value->total_sales }}</td>
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