@extends('layouts.app')


@section('content')

<!-- <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.4.0/css/font-awesome.min.css"/>

<link rel="stylesheet" href="css/dataTables.bootstrap.css"/>
<script src="js/jquery.dataTables.js"></script>
<script src="js/dataTables.bootstrap.js"></script> -->
<script src="{{url('js/jquery-ui.min.js')}}"></script>  
<link rel="stylesheet" href="css/dataTables.bootstrap.css"/>
<script src="js/jquery.dataTables.js"></script>
<script src="js/dataTables.bootstrap.js"></script>
<style>

element.style {
	height:170px;
}
</style>

    <script>

    </script>
<div class="super_container"> 
    <div class="home">
    		
    	
    	</div>
    <div class="search" >	
    <div class="">
    			<div class="">
    				<div class="">


                            <div class="panel-body">
                                <div class="col-md-12">
                                    
                                        <form method="get" action="{{ url('business-report') }}" class="form-inline"
                                              role="form">
                                            {!! csrf_field() !!}
											<div class="form-group">
                                                <input name="fromdate" class="form-control" type="date">
                                            </div>
                                            <div class="form-group">
                                                <input name="todate" class="form-control" type="date">
                                            </div>
                                           <div class="form-group">
                                                {{ Form::select('product', ['3' => 'DMT 1','4' => 'DMT 2','1' => 'Recharge'], null, ['class'=>'form-control', 'style'=>"height: 10%;"]) }}
                                            </div> 
                                            <button type="submit"
                                                    class="btn btn-success waves-effect waves-light m-l-10 btn-md"><span
                                                        class="glyphicon glyphicon-find"></span>Search
                                            </button>
                                             <a href="{{url('money_transfer_report')}}" class="btn btn-primary  btn-md">Reset
                                            </a>
                                           <div class="form-group">
                                                <button name="export" value="DMT Reports" type="submit"
                                                        class="btn btn-success waves-effect waves-light m-l-10 btn-md"><span
                                                            class="glyphicon glyphicon-find"></span>Export
                                                </button>
                                            </div>
                                        </form>

                                </div>

                            </div><br>

		
<div class="ex1"  style="overflow-y: scroll; max-height:430px">	  
    <table id="dtBasicExample" class="table table-striped table-bordered table-sm" cellspacing="0" width="150%">
                                        <thead>
                                            <tr>
                                             
                                               <th>Product</th>
                                                <th>Success</th>
                                                <th>Pending</th>
    											
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($content as $report)
                                 
                                            <tr class="odd gradeX">
    											
    											@foreach($report as $key=>$data)
    											
													@if($key=="PRODUCT")
														<td class="center">{{ @$data }}</td>
													@endif
												
												
													@if($key=="SUCCESS")
														<td class="center">{{ @$data }}</td>
													@endif
												
												
													@if($key=="PENDING")
														<td class="center">{{ @$data }}</td>
													@endif
												
												 @endforeach
											</tr>
									@endforeach
									</tbody>
                     </table>
              
               
           </div>
    	   
    
    	
    	</div>
    	</div>
    	</div>
    	</div>
</div>
@include('layouts.footer')        
    <meta name="_token" content="{!! csrf_token() !!}"/>
@endsection