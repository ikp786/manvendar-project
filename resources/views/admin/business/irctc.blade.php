@extends('admin.layouts.templatetable')

@section('content')
<style>
.bootstrap-table{
    margin-top: -28px;
}
</style>
    @include('admin.admin-subtab.business-type')

    <!-- Page-Title -->
    <div class="">
        <div class="col-sm-12">
            <div class="col-lg-6 col-md-6">
                <h4 class="page-title" style="color: black; ">{{ $page_title or 'IRCTC' }}</h4>
                
            </div>
            
        </div>
		<div class="panel panel-default">
        <div class="panel-body">
                            <div class="col-md-4">
                                <form method="get" action="recharge" class="form-inline" role="form">
                                   
                                    <div class="form-group">
                                        <label class="sr-only" for="payid">Number</label>
                                        <input name="number" type="text" class="form-control" id="exampleInputEmail2"
                                               placeholder="Number">
                                    </div>
                                    <button onclick="tekdeail()" type="submit"
                                            class="btn btn-success waves-effect waves-light m-l-10 btn-md"><span
                                                class="glyphicon glyphicon-find"></span>Search
                                    </button>
                                </form>
                            </div>
                            
                            <div class="col-md-8">
                           
                                <form method="get" action="#">
                                    <div class="form-group col-md-4">
                                        <input name="fromdate" class="form-control" type="date">
                                    </div>
                                    <div class="form-group col-md-4">
                                        <input name="todate" class="form-control" type="date">
                                    </div>
                                    <div class="form-group col-md-2">
                                        <button name="export" value="All Txn Reports" type="submit"
                                                class="btn btn-primary waves-effect waves-light m-l-10 btn-md"><span
                                                    class="glyphicon glyphicon-find"></span>Export Report
                                        </button>
                                    </div>
									
                                </form>
                               
                            </div>
                            
                        </div>
    </div><br>

    <!--Basic Columns-->
    <!--===================================================-->


    <!--===================================================-->
    <div class="row">
        <div class="col-sm-12">
           <div class="table-responsive">
		<table id="mytable"  class="table table-bordered">
                    <tr>
					  <th data-field="date" data-sortable="true" data-formatter="dateFormatter">Date/Time </th>
					  <!--<th data-field="time" data-sortable="true" data-formatter="dateFormatter"> </th>-->
                      <th data-field="id" data-sortable="true">
                            Id
                        </th>
                       
                        <th data-field="user" data-sortable="true">User</th>
                      
                        <th data-field="provider" data-align="center" data-sortable="true"
                            data-sorter="priceSorter">Product
                        </th>

                        <th data-field="provider_name" data-align="center" data-sortable="true"
                            data-sorter="priceSorter">Provider
                        </th>
                        
						<th data-field="bank_name" data-sortable="true" data-formatter="dateFormatter">Bank Name
                        </th>
                        <th data-field="number" data-sortable="true" data-formatter="dateFormatter">Number
                        </th>
                        <th data-field="txnid" data-sortable="true">Ref Id
                        </th>
                        <!-- <th data-field="ackno" data-sortable="true">C_ref_Id</th> -->
                        <th data-field="amount" data-align="center" data-sortable="true"
                            data-sorter="priceSorter">Amount
                        </th>
                        <th data-field="profit" data-align="center" data-sortable="true"
                            data-sorter="priceSorter">Charges
                        </th>
                        <th data-field="total" data-align="center" data-sortable="true"
                            data-sorter="priceSorter">Balance
                        </th>
                        <th data-field="status" data-align="center" data-sortable="true"
                            data-formatter="statusFormatter">Status
                        </th>
                       
                        <th data-field="action" data-align="center" data-sortable="true">Action
                        </th>
                       
                    </tr>
                    </thead>

                    <tbody>
                   

                    </tbody>
                </table>
                
            </div>
        </div>
    </div>
    <div id="con-close-modal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
         aria-hidden="true" style="display: none;">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">??</button>
                    <h4 class="modal-title">Recharge Editor</h4>
                </div>
               <!-- <form id="frmTasks" action="{{ url('report/update') }}" method="post" name="frmTasks" class="form-horizontal" novalidate=""> -->
                <form id="frmTasks" name="frmTasks" class="form-horizontal" novalidate="">
                   

                    <div class="modal-body">
                        <div style="display:none" id="name-error"></div>
                        <input type="hidden" name="id" value="" id="id">
                        <div class="form-group">
                            <label for="inputTask" class="col-sm-3 control-label">Number</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control has-error" id="number" name="number"
                                       placeholder="Provider Name" value="">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="inputEmail3" class="col-sm-3 control-label">Transaction ID</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control" id="txnid" name="txnid"
                                       placeholder="Provider Code" value="">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="inputEmail3" class="col-sm-3 control-label">Status</label>
                            <div class="col-sm-9">
                                <select class="form-control" name="status" id="status">
                                    <option value="1">Success</option>
                                    <option value="2">Failure</option>
                                    <option value="3">Pending</option>
                                    <option value="20">Refund Pending</option>
                                </select>
                            </div>
                        </div>


                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-info waves-effect waves-light" id="btn-save" value="add" onclick="this.disabled=true;refundTranaction()">Save Now
                        </button>
                        <button type="button" class="btn btn-default waves-effect" data-dismiss="modal">Close</button>
                    </div>
                </form>
            </div>
        </div>
    </div><!-- /.modal -->
    <!-- END wrapper -->

     <div id="con-close-modal-nkyc" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
         aria-hidden="true" style="display: none;">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">??</button>
                    <h4 class="modal-title">Recharge Editor</h4>
                </div>

                   

                    <div class="modal-body">
                       <table style="font-size: 14px; color:black;"
                           class="table table-responsive">
                        <thead>
                        <tr>
                            <th>Number</th>
                            <th>Txn ID</th>
                            <th>Amount</th>
                            <th>Fail</th>

                        </tr>
                        </thead>
                        <tbody id="response" style="font-family: sans-serif;">

                        </tbody>
                    </table>

                    <div style="display:none" id="name-error"></div>
                        <input type="hidden" name="id" value="" id="id">
                        <div class="form-group">
                            <label for="inputTask" class="col-sm-3 control-label">Number</label>
                            <div class="col-sm-9">
                <input type="text" class="form-control has-error" id="nkyc_one_number" name="nkyc_one_number"
                                       placeholder="Account Number">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="inputEmail3" class="col-sm-3 control-label">Transaction ID</label>
                            <div class="col-sm-9">
                     <input type="text" class="form-control" id="nkyc_one_txnid" name="nkyc_one_txnid" placeholder="Txn ID">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="inputEmail3" class="col-sm-3 control-label">Status</label>
                            <div class="col-sm-9">
                                <select class="form-control" name="status" id="nkyc_one_status">
                                    <option value="1">Success</option>
                                    <option value="2">Failure</option>
                                    <option value="3">Pending</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                         <button onclick=nkyc_one_update(); type="submit" class="btn btn-info waves-effect waves-light" id="btn-save"
                                value="add">Save Now
                        </button>
                        <button type="button" class="btn btn-default waves-effect" data-dismiss="modal">Close</button>
                    </div>
               
            </div>
        </div>
    </div>
    <meta name="_token" content="{!! csrf_token() !!}"/>
@endsection