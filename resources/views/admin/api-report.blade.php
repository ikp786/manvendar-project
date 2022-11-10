@extends('admin.layouts.templatetable')

@section('content')
<style>
.bootstrap-table{
    margin-top: -28px;
}
</style>
<script>
$(document).ready(function () {
        $.noConflict();
        $('.customDatepicker').datepicker({
            autoclose: true,  
            format: "dd-mm-yyyy"
        });
    }); 
	</script>
	<div class="panel panel-default">
<div class="panel-body">
            <div class="col-lg-6 col-md-6">
                <h4 class="page-title" style="color: black; ">{{'Api Report' }}</h4>
                
			</div>
	
			<div class="row col-md-6">
				<form method="get" action="{{ Request::url() }}" onSubmit="return validateExportForm()" class="form-inline">
                    <div class="form-group">
                        <input name="fromdate" class="form-control customDatepicker" type="text" value="{{ (app('request')->input('fromdate')) ? app('request')->input('fromdate') : date('d-m-Y')}}" autocomplete="off"> 
                    </div>
                    <div class="form-group">
                        <input name="todate" class="form-control customDatepicker" type="text" value="{{ (app('request')->input('todate')) ? app('request')->input('todate') : date('d-m-Y')}}" autocomplete="off">
                    </div>
                    
                  
                        <button name="export" value="SEARCH" type="submit" class="btn btn-primary btn-md"></span><i class="fa fa-search"></i></button>
                        
                      <a href="{{ Request::url() }}"/ class="btn btn-info  btn-md"><i class="fa fa-refresh"></i></a>
                      
                </form>
               
            </div>
            </div> 
			</div>
<div class="">
	<table  class="table table-bordered" >
		        <thead>
                    <tr>
						<th>Api Name</th>
						<th>Success</th>
						<th>Pending</th>
						<th>Failed</th>
                    </tr>
                </thead>
				<?php $successVolume = $pendingVolume = $failVolume =0;?>
                <tbody>
                    @foreach($newArray as $key => $value)
						<tr>
						
							<td>{{$key}}</td>
							<td>{{@$value->SUCCESS}}</td>
							<td>{{@$value->PENDING}}</td>
							<td>{{@$value->FAILURE}}</td>
							<?php 
								$successVolume += @$value->SUCCESS;
								$pendingVolume += @$value->PENDING;
								$failVolume += @$value->FAILURE;
								?>
						</tr>
                    @endforeach
						<tr>
							<td>Total</td>
							<td>{{@$successVolume}}</td>
							<td>{{@$pendingVolume}}</td>
							<td>{{@$failVolume}}</td>
						</tr>
                </tbody>
            </table>
                
        </div>
   
    <meta name="_token" content="{!! csrf_token() !!}"/>
@endsection