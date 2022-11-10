@extends('admin.layouts.templatetable')

@section('content')

        <!-- Page-Title -->
        <div class="row">
            <div class="col-sm-12">
                <div class="col-lg-6 col-md-6">
                    <h4 class="page-title">{{ $page_title or 'Provider Detail' }}</h4>
                    <ol class="breadcrumb">
                        <li>
                            <a href="{{ url('dashboard') }}">Home</a>
                        </li>
                        <li class="active">
                            {{ $page_title or 'Recharge Report' }}
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
                            <th data-field="provider" data-sortable="true">Provider Name</th>
                            <th data-field="amount" data-sortable="true">Total Amount</th>
                        </tr>
                        </thead>

                        <tbody>
                        @foreach($reports as $key => $value)
                            <tr>
                                <td>{{ $value->id }}</td>
                                <td>{{ $value->provider->provider_name }}</td>
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