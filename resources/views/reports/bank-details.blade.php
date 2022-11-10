@extends('layouts.app')

@section('content')

    
<div class="row">
        <div class="col-sm-12">
            <div class="card-box">
                <table class="table">
                    <thead>
                    <tr>						
						<th data-field="id" data-sortable="true">ID </th>
						<th>Bank Owner Name</th>                       
                        <th>Bank Name</th>
                        <th>Account Number</th>
                        <th>IFSC</th>
                        <th>Branch</th>
                        <th>OUT-LET NAME</th>
                        <th>TYPE</th>                       
                    </tr>
                    </thead>
                    <tbody>
					
                    @foreach($bankDetails as $bankDetail)		
                        <tr>
                            <td>{{ $bankDetail->id }}</td>
							 <td>{{$bankDetail->user->name}}({{$bankDetail->user->mobile}})</td>
                            
                            <td>{{ $bankDetail->bank_name }}</td>
                            <td>{{ $bankDetail->account_number }}</td>
                            <td>{{ $bankDetail->ifsc_code }}</td>
                            <td>{{ $bankDetail->branch_name }}</td>
                            <td>{{ $bankDetail->message_one }}</td>
                            <td>{{ $bankDetail->message_two }}</td>
                           
                        </tr>
                    @endforeach
                    </tbody>
                </table>
                
            </div>
        </div>
    </div>
<h4 style="color:red">Note:Before deposit the money to his Distributor account once confirm him. Company is not responsible for any wrong details. </h4>
@endsection
