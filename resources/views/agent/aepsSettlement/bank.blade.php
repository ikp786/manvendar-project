@extends('layouts.app')
@section('content')
<div class="super_container">
	<div class="home">
	</div>
	<div class="search">					
            @include('partials.tab')
            @include('agent.aepsSettlement.aepsSettlement-type')
            <br>
		<div class="row" id="showbalancesettlement">
		    <div class="col-sm-12">
		        <div class="">
		            <table class="table table-bordered">
		                <thead style="color: white">
		                    <tr>
								<th data-field="id" data-sortable="true">ID </th>
								 <th>Account Holder Name</th>
		                        <th>IFSC Code</th>
		                        <th>Bank Name</th>
		                        <th>Balance</th>
		                        <th>Status</th>
		                    </tr>
		                </thead>
		                <tbody>
		                @foreach($bankDetails as $bankDetail)		
		                    <tr style="background-color:white">
		                        <td>{{ $bankDetail->id }}</td>
		                        <td>{{ $bankDetail->name }}</td>
		                        <td>{{ $bankDetail->ifsc}}</td>
		                        <td>{{ $bankDetail->bank_name }}</td>		       
		                        <td>{{ number_format($bankDetail->user->balance->user_balance,2)}}</td>	
		                        <td>{{$bankDetail->status->status}}</td>	          
		                    </tr>
		                @endforeach
		                </tbody>
		            </table>
		        </div>
		    </div>
		</div>
	</div>
</div>
 <meta name="_token" content="{!! csrf_token() !!}"/>
@endsection
