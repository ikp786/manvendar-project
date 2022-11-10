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
                <h4 class="page-title" style="color: black; ">{{ $page_title or 'Gift Voucher' }}</h4>
                
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
           <div class="">
		<table id="mytable"  class="table table-bordered">
						<thead>
							<tr>
							  <th>Date/Time</th>
							  <th>ID </th>
							  <th>Product</th>
							  <th>Category</th>
							  <th>Brand</th>
							  <th>Qty</th>
							  <th>Txn ID</th>
							  <th>Amount</th>
							  <th>Charge</th>
							  <th>Total balance</th>
							   <th>Status</th>
							   <th>Action</th>
							</tr>
						</thead>
					  <tbody>
						@foreach($voucherlist as $voucher)
						<?php $s = $voucher->created_at;
						$dt = new DateTime($s);?>
						<tr>
						  <td>{{ $dt->format('d/m/Y') }}<br>{{ $dt->format('H:i:s') }}</td>
						  <td>{{ $voucher->id }}</td>
						  <td>{{ $voucher->api->api_name }}</td>
						  <td>{{ @$voucher->voucherycategory->name }}</td>
						  <td>{{ @$voucher->voucherbrand->name}}</td>
						  <td>{{ @$voucher->qty }}</td>
						  <td>{{ $voucher->txnid }}</td>
						  <td>{{ $voucher->amount }}</td>
						  <td>{{ $voucher->profit }}</td>
						   <td>{{ number_format($voucher->total_balance,2) }}</td>
						  <td>{{ $voucher->status->status }}</td>
						  <td></td>
						</tr>
						@endforeach
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
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
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
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
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