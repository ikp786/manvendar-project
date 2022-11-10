@extends('admin.layouts.templatetable')

@section('content')
@include('search.date-search-export')

        <!--Basic Columns-->
        <!--===================================================-->


        <!--===================================================-->
        <div class="row">
            <table id="demo-custom-toolbar" data-toggle="table"
                           
                           data-search="true"
                          
                           data-sort-name="id"
                           data-page-list="[5, 10, 20]"
                           data-page-size="30"
                           data-pagination="true" data-show-pagination-switch="true" class="table table-bordered ">
                        <thead>
                        <tr>
                            
                            <th data-field="provider" data-sortable="true">Agent Name/Agent Id</th>
                            <th >Txn Count</th>
                            <th data-field="amount" data-sortable="true">Success Txn Amount</th>
                            <th>Txn Charge</th>
                            <th>Txn Commission</th>
                           
                           
                          <!--   <th data-field="amount_sale" data-sortable="true">Total Recharge Amount</th> -->
                        </tr>
                        </thead>

                        <tbody>
                        @foreach($reports as $key => $value)
                            <tr>
                                
                                <td>{{ @$value->user->name }}<br> (R {{  @$value->user->id }})</td>
								<td>{{ @$value->txn_count }}</td>
                                <td>{{ @$value->total_sales }}</td>
                                <td>{{ number_format(@$value->txn_charge,2) }}</td>
                                <td>{{  number_format(@$value->txn_commission,2) }}</td>
                              
                               <!--  <td>{{ @$value->total_sales_recharge }}</td> --> 
                                
                            </tr>
                        @endforeach

                        </tbody>
                    </table>

        </div>
    <!-- END wrapper -->
    <meta name="_token" content="{!! csrf_token() !!}"/>
@endsection