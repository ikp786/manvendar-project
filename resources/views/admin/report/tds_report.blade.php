@extends('admin.layouts.templatetable')
@section('content')
<script>
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
        <h4 class="page-title" style="color: black; ">{{'TDS Report' }}</h4>
    </div>
</div>		
<div class="panel panel-default">
    <div class="panel-body">
        <h4 class="page-title" style="color: black;">{{@$title }}</h4>
        <form method="get" action="{{ Request::url() }}" class="form-inline">
              
            {{Form::select('type', ['1' => "Summary", '2' => "Individual"], null, ['class'=>'form-control'])}}
            
            <input name="fromdate" class="form-control customDatepicker" type="text" value="{{ (app('request')->input('fromdate')) ? app('request')->input('fromdate') : date('d-m-Y')}}" autocomplete="off"> 
            
            <input name="todate" class="form-control customDatepicker" type="text" value="{{ (app('request')->input('todate')) ? app('request')->input('todate') : date('d-m-Y')}}" autocomplete="off">
              
            {{Form::select('product',['1' => "Recharge",'2' => "Verify",'4'=>"DMT1",'5'=>"A2Z Wallet",'10'=>"AEPS",'8'=>"Recharge2"], null, ['class'=>'form-control','placeholder'=>"--Select--"])}}
            
            {{Form::select('user',$users, null, ['class'=>'form-control','placeholder'=>"--Select--"])}}

            <button name="export" value="SEARCH" type="submit" class="btn btn-primary btn-md"><i class="fa fa-search"></i></button>
          <button name="export" value="EXPORT" type="submit" class="btn btn-basic btn-md"><i class="fa fa-file-excel-o" aria-hidden="true"></i></button>
            <a href="{{ Request::url() }}" class="btn btn-info  btn-md"><i class="fa fa-refresh"></i></a>
        </form>      
    </div>
</div>
	
<div class="box">
    <table  class="table table-bordered" id="example2">
        <thead>
            <tr>
			    <th>s.no</th>
				<th>User</th>
                <th>Firm Name</th>
                <th>Pan Card</th>
                <th>Mobile</th>
                <th>Member Type</th>
                <th>Volume</th>
				<th>TDS</th>
				<th>Service Charge</th>
                <th>Commission</th>
                <th>Net Commission</th>
            </tr>
        </thead>               
        <tbody>
            @foreach($reports as  $key =>$value)
				<tr>
					<td>{{ ++$key }}</td>
                    <td>{{ @$value->user->name }}({{ @$value->user->prefix }}-{{ @$value->user->id }})</td>
                    <td>{{@$value->user->member->company}}</td>    
                    <td>{{@$value->user->member->pan_number}}</td> 
                    <td>{{@$value->user->mobile}}</td>   
                    <td>{{@$value->user->role->role_title}}</td>
                    <td>{{ number_format($value->txn_value ,2)}}</td>
					<td>{{ number_format($value->tds,3) }}</td>
					<td>{{ number_format($value->service_charge,2) }}</td>
                    <td>{{ number_format($value->commission,2) }}</td>
                    <td>{{number_format($value->commission-$value->tds,2)}}</td>
				</tr>
            @endforeach
        </tbody>
    </table>
        {{$reports->appends(\Input::except('page'))->render() }} 
</div>
  
    <meta name="_token" content="{!! csrf_token() !!}"/>
@endsection