@extends('admin.layouts.templatetable')

@section('content')

<!-- Page-Title -->
        <div class="row">
            <div class="col-sm-12">
                <div class="col-lg-6 col-md-6">
                    <h4 class="page-title" style="color: white; font-size: 36px;">{{ $page_title or ' VOL/COUNT MONTH WISE' }}</h4>
                </div>
                <div class="col-lg-6 col-md-6">
                    <!-- <div class="pull-right">
                        <button onclick="#" id="demo-add-row" class="btn btn-success"><i
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
                                <form method="get" action="{{ url('report-distributor') }}">
                                    <div class="form-group col-md-2">
                                        <input name="fromdate" class="form-control" type="date">
                                    </div>
                                    <div class="form-group col-md-2">
                                        <input name="todate" class="form-control" type="date">
                                    </div>
                                    <div class="form-group col-md-4">
                            <select class="form-control" name="searchall_api">
                              <option value="0">Select Your Product</option>
                              <option value="3">Saral</option>
                              <option value="4">Smart</option>
                              <option value="5">Sharp</option>
                              <option value="1">Recharge1</option>
                              <option value="8">Recharge2</option>
                              <option value="115">UDIO</option>
                          </select>
                                    </div>
                                      <div class="form-group col-md-2">
                          <select class="form-control" name="searchall_user">
                             
                              <option value="0">Select user</option>
                              
                              <option value="1018">1018</option>
                              <option value="162">162</option>
                              <option value="115">115</option>
                              <option value=""> </option>
                           
                          </select>
                                    </div>

                                    <div class="form-group col-md-2">
                                        <button name="export" value="export" type="submit"
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
                <div class="card-box">
                    <h4 class="m-t-0 header-title"><b>{{ $page_title or 'Provider Detail' }}</b></h4>
                    <p class="text-muted font-13">
                        All Recharge Detail
                    </p>

                    <table style="float:left;" id="demo-custom-toolbar" data-toggle="table"
                           data-toolbar="#demo-delete-row"
                           data-search="true"
                           data-sort-name="id"
                           data-page-list="[50, 100, 150]"
                           data-page-size="200"
                           >
                        <thead>
                        <tr>
                  <th data-field="month" data-sortable="true">Month</th>
                  <th data-field="product" data-sortable="true">Product</th>
                  <th data-field="provider" data-sortable="true">Total Volume</th>
                  <th data-field="agentcontact" data-sortable="true">Total Count</th>
                  <th data-field="users" data-sortable="true">User ID</th>
                        </tr>
                        </thead>
                        <tbody>
               
              @foreach($reports as $value)
              <tr>
                <td><?php 
                    $yrdata= strtotime($value->created_at);
                    echo date('M-Y', $yrdata);
                 ?></td>
                 <td>{{ @$value->api->api_name }}</td>
                <td>{{ number_format(@$value->total_sales,2) }}</td>
                <td>{{ @$value->total_count }}</td>
                <td> {{ @$user }} </td>
                
                
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