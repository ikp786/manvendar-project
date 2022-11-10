@extends('admin.layouts.templatetable')
@section('content')
   

    <!--===================================================-->
    <div class="row">
        <div class="col-sm-12">
            @include('search.only-search-with-export')
            <div class="table-responsive" style="overflow-y: scroll; max-height:430px">
				<table id="mytable"  class="table table-bordered">
                    <thead>
                    <tr>
						<th>Date</th>
                        <th>DealerName</th>
                        <th>Active Agent</th>
                        <th>Payment Load</th>
                        <th>Refunded</th>
                        <th>Uses</th>
                        <th>Payment Transfer</th>
						<th>SurCharge</th>
						<th>Commission</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($reports as $key => $value)
					<?php
							/* $fromdate=(app('request')->input('fromdate')) ? app('request')->input('fromdate') : date('Y-m-d');
							$todate =	(app('request')->input('todate')) ? app('request')->input('todate') : date('Y-m-d'); */
							 $fromdate=  $key;
							 $todate=  $key;
					?>
						<tr>
							<td> {{$key}}</td>
							<td> {{Auth::user()->name}}</td>
							<td> {{(@$value->ActiveAgent)?$value->ActiveAgent : 0}}</td>
							<td> <a href="{{url('payment-report')}}?fromdate={{$fromdate}}&todate={{ $todate}}">{{(@$value->loadAmount)?$value->loadAmount : 0}}</a></td>
							<td> <a href="{{url('all-transaction-reports')}}?fromdate={{$fromdate}}&todate={{ $todate}}&searchOf=4&export=SEARCH">{{(@$value->refundAmount)?$value->refundAmount : 0}}</a></td>
							<td> <a href="{{url('all-transaction-reports')}}?fromdate={{$fromdate}}&todate={{ $todate}}&searchOf=SPE&export=SEARCH">{{(@$value->txnAmount)?$value->txnAmount : 0}}</a></td>
							<td> <a href="{{url('payment-load')}}?fromdate={{$fromdate}}&todate={{ $todate}}">{{(@$value->transferAmaount)?$value->transferAmaount : 0}}</a></td>
							<td> {{(@$value->surCharge)?$value->surCharge : 0}}</td>
							<td> <a href="{{url('all-transaction-reports')}}?fromdate={{$fromdate}}&todate={{ $todate}}&searchOf=22&export=SEARCH">{{(@$value->commission)?number_format($value->commission,2) : 0}}</a></td>
                    @endforeach

                    </tbody>
                </table>

              
            </div>
        </div>
    </div>

    <footer class="footer">
        2015 © Ubold.
    </footer>	
    </div>
    <!-- ============================================================== -->
    <!-- End Right content here -->
    <!-- ============================================================== -->

    </div>
    <div id="con-close-modal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
         aria-hidden="true" style="display: none;">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                    <h4 class="modal-title">Balance Update</h4>
                </div>
                <div class="modal-body">
                    <div style="display:none" id="name-error"></div>

                    <form id="frmTasks" name="frmTasks" class="form-horizontal" novalidate="">
                        {!! csrf_field() !!}
                        <input type="hidden" name="user_id" id="user_id">
						<input type="hidden" name="p_id" id="p_id">
                        <div class="form-group">
                            <label for="inputEmail3" class="col-sm-3 control-label">Amount</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control" id="amount" name="amount"
                                       placeholder="Amount">
                            </div>
                        </div>
                       <!-- <div class="form-group">
                            <label for="inputEmail3" class="col-sm-3 control-label">Reference ID</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control" id="ref_id" name="ref_id"
                                       placeholder="Refernece ID">
                            </div>
                        </div>-->
                        <div class="form-group">
                            <label for="inputEmail3" class="col-sm-3 control-label">Remark</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control" id="remark" name="remark"
                                       placeholder="Remark">
                            </div>
                        </div>
                       <!-- <div class="form-group">
                            <label for="inputEmail3" class="col-sm-3 control-label">Status</label>
                            <div class="col-sm-9">
                                <select class="form-control" name="status_id" id="status_id">
                                    <option value="3">Pending</option>
                                    <option value="2">Reject</option>
                                    <option value="1">Approve</option>
                                </select>
                            </div>
                        </div>-->


                    </form>
                </div>
                <div class="modal-footer">
                    <button onclick="update_re()" type="button" class="btn btn-info waves-effect waves-light" id="btn-save"
                            value="add">Save Now
                    </button>
                    <button type="button" class="btn btn-default waves-effect" data-dismiss="modal">Close</button>
                    <input type="hidden" id="id" name="id" value="0">
                </div>
            </div>
        </div>
    </div><!-- /.modal -->
    <!-- END wrapper -->
    <meta name="_token" content="{!! csrf_token() !!}"/>
@endsection