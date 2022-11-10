@extends('layouts.app')

@section('content')
@include('agent.report.report-type')
<br>
<style>

element.style {

	height:170px;
}
.pStyle{
	background: green;
    font-size: 15px;
    font-family: time;
    color: white;
}
</style>

  <script type="text/javascript">
  	function recharge_status(id)
  	{
  		var token = $("input[name=_token]").val();
            var dataString = 'id=' + id;
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                }
            })
            $.ajax({
                type: "POST",
                url: "{{url('/re-check-status')}}",
                data: dataString,
                datatype: "json",
                success: function (data) {
                	$('#re_check_'+id).prop('disabled',false);
                    alert(data.message);
                }
            });
  		
  	}
	/*for date->Calender*/

    $(document).ready(function () {
        $.noConflict();
        $('.customDatepicker').datepicker({
              autoclose: true,  
            format: "dd-mm-yyyy"
        });

    }); 
  </script>
<div class="super_container">
	<div class="home">

	
		</div>

	<div class="search" >	
 <div class="panel-body">
							
							<div class="panel panel-default">
								<div class="panel-body">
								<div class="col-lg-3 col-md-3">
									<h4 class="page-title" style="color: black; ">{{'View Commission' }}</h4>
									
								</div>
									<div class="col-lg-9 col-md-9">
                           
										<form method="get" action="{{ Request::url() }}" onSubmit="return validateExportForm()" class="form-inline">
											<div class="form-group">
												<input name="fromdate" class="form-control customDatepicker" type="text" value="{{ (app('request')->input('fromdate')) ? app('request')->input('fromdate') : date('d-m-Y')}}">
											</div>
											<div class="form-group">
												<input name="todate" class="form-control customDatepicker" type="text" value="{{ (app('request')->input('todate')) ? app('request')->input('todate') : date('d-m-Y')}}">
											</div>
											
											<div class="form-group" >
												<button type="submit" value="SEARCH" name="export"
													class="btn btn-primary  btn-md"><span class="glyphicon glyphicon-find"></span><i class="fa fa-search"></i>
												</button> 
												<button type="submit" value="EXPORT" name="export"
													class="btn btn-basic  btn-md"><span
														class="glyphicon glyphicon-find"></span><i class="fa fa-file-excel-o" aria-hidden="true"></i>
												</button> 
												<a href="{{ Request::url() }}"/ class="btn btn-info  btn-md"><i class="fa fa-refresh"></i>
												</a>
											</div>	
										</form>
									</div>
								</div>
							</div>
                        </div>
					<div class="panel-body">
	  				 	<div class="col-md-12 faq-desc-item form-inline" style="    font-size: 16px; font-family: time;">
							@foreach($all_reports as $key => $values)
								<div class="col-md-6 flip-container text-center" style="    padding: 1%;"> 
									<p style=" background: #8e50ff;color: white;font-size: 16px;font-family: time;">{{$key}}</p>
										@foreach($values as $d_key=>$d_value)
											<div class="col-md-4 front" style="background: white;padding: 2%;">
											<?php $style='';?>
												@if($d_key=="Pending")
													<?php $style="background: yellow;";?>
												@elseif($d_key=="Success")
													<?php $style="background: green;";?>
												@elseif($d_key=="Failure")
													<?php $style="background: red;";?>
												@elseif($d_key=="PtxnCredit")
													<?php $style="background: blue;";?>
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

@include('layouts.footer')
<meta name="_token" content="{!! csrf_token() !!}"/>

@endsection