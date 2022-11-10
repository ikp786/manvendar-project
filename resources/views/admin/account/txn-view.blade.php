@extends('admin.layouts.templatetable')

@section('content')
<style>
.bootstrap-table{
    margin-top: -28px;
}
</style>
<script type="text/javascript">
	$(document).ready(function () {
        $.noConflict();
        $('.customDatepicker').datepicker({
              autoclose: true,  
            format: "dd-mm-yyyy"
        });

    }); 
</script>
<div class="col-sm-12">
    <div class="col-lg-6 col-md-6">
        <h4 class="page-title" style="color: black; ">{{'Api Wise Report'}}</h4>  
    </div>
</div>		
<div class="panel panel-default">
	<div class="panel-body">	
		<div class="col-lg-9 col-md-9">
			<form method="get" action="{{ Request::url() }}" onSubmit="return validateExportForm()" class="form-inline">
				<div class="form-group">
					<input name="fromdate" class="form-control customDatepicker" type="text" value="{{ (app('request')->input('fromdate')) ? app('request')->input('fromdate') : date('d-m-Y')}}">
				</div>
				<div class="form-group">
					<input name="todate" class="form-control customDatepicker" type="text" value="{{ (app('request')->input('todate')) ? app('request')->input('todate') : date('d-m-Y')}}">
				</div>
				<button type="submit" value="SEARCH" name="export" class="btn btn-primary btn-md"><span class="glyphicon glyphicon-find"></span>Search</button> 
				
				<a href="{{ Request::url() }}"/ class="btn btn-info  btn-md">Reset
				</a>
			</form>
		</div>
	</div>
</div>
<br>
<div class="row">
    <div class="">
       <div class="" style="overflow-x:auto; width:100%">
			<div class="panel-body">
  				 	<div class="col-md-12 faq-desc-item" style="font-size: 16px; font-family: time;">
						@foreach($all_reports as $key => $values)
							<div class="col-md-12 flip-container text-center" style="padding: 1%;"> 
								<p style=" background: #5bc0de;color: black;font-size: 16px;font-family: time;">{{$key}}</p>
									@foreach($values as $d_key=>$d_value)
										<div class="col-md-2 front" style="background: #ececec;padding: 2%;border: 1px solid;border-radius: 23%;">
										<?php $style='';?>
											@if($d_key=="Pending")
												<?php $style="background: yellow;color: black;";?>
											@elseif($d_key=="Success")
												<?php $style="background: green;color: white;";?>
											@elseif($d_key=="Failure")
												<?php $style="background: red;color: white;";?>
											@elseif($d_key=="PtxnCredit")
												<?php $style="background: blue;color: white;";?>
											@elseif($d_key=="RefundSuc")
												<?php $style="background: #EE82EE;color: white;";?>
											@elseif($d_key=="RefundAvailable")
												<?php $style="background: #800000;color: white;";?>
											@elseif($d_key=="Refunded")
												<?php $style="background: #F20056;color: white;";?>
											@elseif($d_key=="Successfull")
												<?php $style="background:green;color:white";?>	
											@endif
											<p style="{{$style}}" class="pStyle">{{$d_key}}</p>
											@foreach($d_value as $p_key=>$p_value)
												<p><span>{{$p_key}}</span> : {{$p_value}}</p>
											@endforeach
										</div>	
									@endforeach
							</div>
                    
						@endforeach
					</div>
                </div>
        </div>
    </div>
</div>
   

     
@endsection