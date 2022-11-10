@extends('admin.layouts.templatetable')

@section('content')
<script>
        function savebalance()
        {
			$.ajaxSetup({
					headers: {
						'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
					}
				})
            var bal = $('#newbalance').val();
            var dataString = 'balance=' + bal;
             $.ajax({
                type: "post",
                url: "{{url('admin/purchase-balance')}}",
                data: dataString,
				dataType:"json",
                success: function (data) 
				{
					if(data.status == 1)
					{
                    alert("Success", data.message, "success");
						setTimeout( function(){ 
						location.reload();
					}  , 2000 );
					
					}
					else
						 alert("Failure", data.message, "error");
                }
            });
        }
        function add_bal()
        {
            $('#btn-save').val("add");
            $('#frmTasks').trigger("reset");
            $("#con-close-modalpurchase").modal("toggle");
            
        }

    $(document).ready(function () {
        $.noConflict();
        $('.customDatepicker').datepicker({
              autoclose: true,  
            format: "dd-mm-yyyy"
        });
    }); 


</script>

    <!-- Page-Title -->
    <div class="panel panel-default">
        <div class="panel-body">
			<div class="">
                <h1 class="page-title" style="color:balack; font-size:20px;">{{'PURCHASE BALANCE'}}</h1>
			</div>
            <div class="pull-right">
                <button style="background:white !important; color:blue !important; font-size:20px;"onclick="add_bal()" id="demo-add-row" class="btn btn-success" data-toggle="modal" href="#example"><i class="fa fa-plus m-r-5"></i>Purchase balance </button>
            </div>
            <form method="get" action="{{ Request::url() }}" class="form-inline" role="form" >
                <div class="form-group">
                    <input name="fromdate" class="form-control customDatepicker" type="text" value="{{ (app('request')->input('fromdate')) ? app('request')->input('fromdate') : date('d-m-Y')}}" autocomplete="off"> 
                </div>
                <div class="form-group">
                    <input name="todate" class="form-control customDatepicker" type="text" value="{{ (app('request')->input('todate')) ? app('request')->input('todate') : date('d-m-Y')}}" autocomplete="off">
                </div>
                <button name="export" value="SEARCH" type="submit" class="btn btn-primary btn-md"><i class="fa fa-search"></i></button>
                <a href="{{ Request::url() }}" class="btn btn-info btn-md"><i class="fa fa-refresh"></i></a>
            </form>
        </div>
    </div>
    <!--Basic Columns-->
    <!--===================================================-->


    <!--===================================================-->
    <div class="">
        <div class="">
             <div class="box" style="overflow-y: scroll;max-height: 600px">
				<table id="example2" class="table table-bordered ">
                    <thead>
                        <tr style="color:#115798;"> 
                          <th data-field="date" data-sortable="true" data-formatter="dateFormatter">Date/Time </th>
    					 <!-- <th data-field="time" data-sortable="true" data-formatter="dateFormatter">Time </th>-->
    					   <th data-field="id" data-sortable="true">ID</th>
                            <th data-field="name" data-sortable="true">Member Name</th>
                            <th data-field="description" data-sortable="true" data-formatter="dateFormatter">Description</th>
                            <th data-field="amount" data-sortable="true">Purchase Value</th>
                            <th data-field="balance" data-sortable="true">Balance</th> 
                        </tr>
                    </thead>
                    <tbody>
                    @foreach($purchageBalanceReports as $report)
					<?php $s = $report->created_at;
						$dt = new DateTime($s);?>
                        <tr>
						    <td>{{ $dt->format('d-m-Y') }}<br>{{ $dt->format('H:i:s') }}</td>
                            <td>{{ $report->id }}</td>
                            <td>{{ $report->user->name }}</td>
                            <td>{{ $report->txnid }}</td>
                            <td>{{ $report->amount }}</td>
                            <td>{{ $report->total_balance }}</td>   
                        </tr>
                    @endforeach
                    </tbody>
                </table> 
            </div>
        </div>
    </div>
<div id="con-close-modal-one" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                <h4 class="modal-title">Member Editor</h4>
            </div>
        </div>
    </div>
</div><!-- /.modal -->
<div class="container" id="con-close-modalpurchase">
    <div id="example" class="modal fade" style="display: none;">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 style="color:white;" class="modal-title" id="myModalLabel">purchase Balance</h4>
                </div>
                <div class="modal-body">
                    <div id="frmTasks" name="frmTasks" class="form-horizontal">
                        <div class="form-group">
                            <label for="inputTask" class="col-sm-3 control-label">Balance</label>
                            <div class="col-sm-9">
                                <input type="text" name="newbalance" id="newbalance" placeholder="Enter Amount "class="form-control">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button onclick="savebalance()" type="button" class="btn btn-info waves-effect waves-light" id="btn-savecomm" value="add">Update Now</button>
                    <button type="button" class="btn btn-default waves-effect" data-dismiss="modal">Close</button>
                    <input type="hidden" id="idnew" name="idnew" value="0">
                </div>
            </div>
        </div>
    </div>
</div>    
<meta name="_token" content="{!! csrf_token() !!}"/>
@endsection