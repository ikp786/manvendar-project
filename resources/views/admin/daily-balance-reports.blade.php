@extends('admin.layouts.templatetable')

@section('content')
@include('admin.admin-subtab.report-type')
<div class="col-sm-12">
            <div class="col-lg-6 col-md-6">
                    <h4 class="page-title" style="color: black;">{{ $page_title or 'Daily Balance Reports' }}</h4>
                </div>
          
    </div>
    <div class="panel panel-default">
        <div class="panel-body">
            <!--<div class="col-md-6">
                
                <form method="get" action="{{ url('search-account-statement') }}" class="form-inline" role="form">
                      {!! csrf_field() !!}
                       <div class="form-group">
                            <label class="sr-only" for="payid">Number</label>
                            <input name="number" type="text" class="form-control" id="exampleInputEmail2"
                                   placeholder="Number" value="{{app('request')->input('number')}}">
                        </div>
                      <button type="submit" name="export" value="search" class="btn btn-success"><span class="glyphicon glyphicon-find"></span>Search
                        </button>
                     <a href="{{url('daily-balance-reports') }}"/ class="btn btn-primary  btn-md">Reset
                    </a>
                       
                </form>
                                    
            </div>-->
            @if (in_array(Auth::user()->role_id,array(1,11,12,14)))
                <div class="col-md-11">  
                    <form method="get" action="{{ route('daily-balance-reports') }}">
                    
                        <div class="form-group col-md-4">
                            <input name="fromdate" class="form-control" type="date">
                        </div>
                        <div class="form-group col-md-4">
                            <input name="todate" class="form-control" type="date">
                        </div>
						<div class="form-group col-md-1">
                                        <button value="search" name="export" type="submit"
                                                class="btn btn-success "><span
                                                    class="glyphicon glyphicon-find"></span>Search
                                        </button>
                                    </div>
									<div class="form-group col-md-1">
									  <a href="{{ Request::url() }}"/ class="btn btn-primary  btn-md">Reset
									</a></div>
                        <div class="form-group col-md-1">
                            <button name="export" value="export" type="submit"
                                    class="btn btn-success waves-effect waves-light m-l-10 btn-md"><span
                                        class="glyphicon glyphicon-find"></span>Export
                            </button>
                        </div>
                       
                    </form>
  
                </div>
            @endif
               
            </div>

        </div>
    </div>
        
        <div class="row">
            <div class="col-sm-12">
                <div class="card-box">
                    
                    <table id="demo-custom-toolbar" data-toggle="table"
                           
                           data-search="true"
                          
                           data-sort-name="id"
                           data-page-list="[5, 10, 20]"
                           data-page-size="30"
                           data-pagination="true" data-show-pagination-switch="true" class="table table-bordered ">
                        <thead>
                        <tr>
                           
                            <th data-field="parent_id" data-sortable="true">Parent Name</th>
                            <th data-field="txn_count" data-sortable="true">Txn Count</th>
                            <th data-field="txn_amount" data-sortable="true">Total Amount</th>
                           
                           
                          <!--   <th data-field="amount_sale" data-sortable="true">Total Recharge Amount</th> -->
                        </tr>
                        </thead>

                        <tbody>
                        @foreach($reports as $key => $value)
                            <tr>
                                <td>{{ @$value->parent->name }}({{@$value->parent_id}})</td>
                                <td>{{ @$value->txn_count }} </td>
                                <td>{{ @$value->txn_amount }}</td>
                              
                               <!--  <td>{{ @$value->total_sales_recharge }}</td> --> 
                                
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