@extends('layouts.app')
@section('content')
@include('agent.fund.fund-type')
<script type="text/javascript">
    $(document).ready(function () {
        $.noConflict();
        $('.customDatepicker').datepicker({
              autoclose: true,  
            format: "dd-mm-yyyy"
        });
    }); 
</script>
<br>
 <div class="col-md-12">
    <form method="get" action="{{Request::url()}}" class="form-inline" role="form">    
		{{Form::select('noOfRecord',['1' =>'20','2' =>'40','3' =>'60'],app('request')->input('noOfRecord'), ['class'=>'form-control','placeholder'=>"--All--"])}}
        <input name="fromdate" class="form-control customDatepicker" type="text" placeholder="From date" value="{{ (app('request')->input('fromdate')) ? app('request')->input('fromdate') : date('d-m-Y')}}">		<input name="todate" class="form-control customDatepicker" type="text" placeholder="To date" value="{{(app('request')->input('todate')) ? app('request')->input('todate') : date('d-m-Y')}}">
		{{Form::select('searchOf',['6' => 'Debit','7' =>'Credit'],app('request')->input('searchOf'), ['class'=>'form-control','placeholder'=>"--Select--"])}}
        <button name="SEARCH" value="SEARCH" type="submit" class="btn btn-primary btn-md">
        <i class="fa fa-search"></i></button>
        <a href="{{Request::url()}}" class="btn btn-info btn-md"><i class="fa fa-refresh"></i></a>
		<button name="export" value="EXPORT" type="submit" class="btn btn-basic"><i class="fa fa-file-excel-o" aria-hidden="true"></i></button>
		
    </form>
</div><br><br>
<div class="box">
    <table id="example2" class="table table-bordered">
        <thead>
            <tr>
    		   <th>Date/Time</th>
                <th>Order ID</th>
                <th>Wallet</th>
                <th>User</th>
                <th>Transfer To/From</th>
                <th>Firm Name</th>
                <th>Ref Id</th>
                <th>Description</th>
                <th>Opening Bal</th>
                <th>Credit Amount</th>
                <th>Closing Bal</th>
                <th>Bank Charge</th>
    			<th>Remark</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
		<?php $totalAmount=$count=0;?>
        @foreach($reports as $key => $value)
		<?php $s = $value->created_at;
			$dt = new DateTime($s);?>
            <tr>
			<td>{{ $dt->format('d/m/Y') }}<br>{{ $dt->format('H:i:s') }}</td>
                <td>{{ $value->id }}
				</td>
                <td>{{ ($value->recharge_type == 1) ? 'Recharge' : 'Money' }}</td>
                <td>{{ $value->user->name }} ({{@$value->user->prefix}}-{{ $value->user->id }})</td>
                <td>@if(is_numeric($value->credit_by))							{{@$value->creditBy->name}}({{@$value->creditBy->prefix}}-{{@$value->creditBy->id}})<br>
    			@else
    				{{@$value->credit_by}}
    			@endif 
				</td>
                <td>@if(is_numeric($value->credit_by)){{@$value->creditBy->member->company}}@endif</td>
                <td>{{ $value->txnid }}</td>
                <td>{{ $value->description }}</td>
                <td>{{ number_format($value->opening_balance,2) }}</td>
                <td>{{ number_format($value->amount,2) }}
                <td>{{ number_format($value->total_balance,2) }}</td>
                <td>{{ $value->bank_charge }}</td>
				<td>{{ $value->remark }} </td>
                <td>{{@$value->status->status }}</td>	
            </tr>
			 <?php 
			$totalAmount +=$value->amount;
			$count++;
			 ?>
        @endforeach
        </tbody>
		 <h4 style="color:red">Total Amount({{$count}}) : {{number_format($totalAmount,2)}}</h4>
    </table>
  {{$reports->appends(\Input::except('page'))->render() }}
</td>
@endsection