@extends('admin.layouts.templatetable')

@section('content')
<style>
.bootstrap-table{
    margin-top: -28px;
}
</style>
    <script>
        
    </script>



    <!-- Page-Title -->
    <div class="">
        <div class="col-sm-12">
            <div class="col-lg-6 col-md-6">
                <h4 class="page-title" style="color: black; ">{{ $page_title or 'Business Report' }}</h4>
                
            </div>
            
        </div>
		<div class="panel panel-default">
        <div class="panel-body">
		 <div class="col-md-12">
                           
                                <form method="get" action="{{ Request::url() }}" onSubmit="return validateExportForm()">
                                    <div class="form-group col-md-3">
                                        <input name="fromdate" class="form-control" type="date" value="{{ (app('request')->input('fromdate')) ? app('request')->input('fromdate') : date('Y-m-d')}}">
                                    </div>
                                    <div class="form-group col-md-3">
                                        <input name="todate" class="form-control" type="date" value="{{ (app('request')->input('todate')) ? app('request')->input('todate') : date('Y-m-d')}}">
                                    </div>
									<div class="form-group col-md-2">
                                       {{ Form::select('agent', $members, old('agent'), array('class' => 'form-control','id' => 'agent','placeholder'=>'-- Agent--')) }}
                                    </div>
									<div class="form-group col-md-2">
                                       {{ Form::select('product', $products, old('product'), array('class' => 'form-control','id' => 'product','placeholder'=>'-- Product--')) }}
                                    </div>
									
									
                                    <button type="submit"
                                            class="btn btn-success  btn-md"><span
                                                class="glyphicon glyphicon-find"></span>Search
                                    </button> 
									<a href="{{ Request::url() }}"/ class="btn btn-primary  btn-md">Reset
                                    </a>
                                </form>
                            </div>
                            
                           
                               
                            
                            
                            
    </div>
</div>
		<br>

    <!--Basic Columns-->
    <!--===================================================-->


    <!--===================================================-->
    <div class="row">
        <div class="">
           <div class="col-md-12 faq-desc-item" style="    font-size: 16px; font-family: time;">
		
                    @foreach($all_reports as $key => $values)
					
						<div class="col-md-6 flip-container text-center"> <p style=" background: chocolate;">{{$key}}</p>
						@foreach($values as $d_key=>$d_value)
								<div class="col-md-4 front" style="background: antiquewhite;">
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
									<p style="{{$style}}">{{$d_key}}</p>
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
    
    <meta name="_token" content="{!! csrf_token() !!}"/>
@endsection