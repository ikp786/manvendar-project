@extends('admin.layouts.templatetable')

@section('content')
@include('search.date-search-export')
<div class="" style="overflow: auto;">
	<table id="example1"  class="table table-bordered hover" data-search="true">
                    <tr>
                         <th >Date/Time </th>
                     
                         <th>ID</th>
                        <th >User </th>
                        <th >Product </th>
                        <th >Bank Name </th>
						<th>Name</th>
                        <th>Acc/Mobile Number</th>
                        <th>Tx ID</th>
                        <th>Description</th>
                        <th>Amount</th>
                        <th>Credit</th>
						<th>Debit</th>
                        <th>Balance</th>
                        <th>Remark</th>
                        <th>Status</th>
					</tr>
                    </thead>

                    <tbody>
                    @foreach($reports as $report)
                    <?php $s = $report->created_at;
                        $dt = new DateTime($s);?>
                        <tr>
                            <td>{{ $dt->format('d/m/Y') }}<br>{{ $dt->format('H:i:s') }}<br></td>
                            <td>{{ $report->id }}</td>
                            <td>{{ $report->user->name }}({{ $report->user->prefix }}{{ $report->user->id }} :{{ $report->user->mobile }})</td>
							<td>{{ @$report->api->api_name}}</td>
							<td>
								@if($report->recharge_type ==1)
									{{ @$report->provider->provider_name}}
								@else
								{{ @$report->beneficiary->bank_name}}
								@endif
							</td>
							
							 <td>@if(is_numeric($report->credit_by))
										{{@$report->creditBy->name}} ({{(@$report->creditBy->role_id == 4) ? "D - " : "R - "}} {{$report->credit_by}})
									@else
										{{@$report->credit_by}}
									@endif </td>
                      
                            <td>{{ $report->number }}</td>
                            <td>{{ $report->txnid }}</td>
                            <td> {{ $report->description }}</td>
							<td>{{ number_format($report->amount,2) }}</td>
							<td>{{ number_format($report->credit_charge,2) }}</td>
							<td>{{ number_format($report->debit_charge,2) }}</td>
							<td>{{ number_format($report->total_balance,2) }}</td>
							<td> {{$report->remark}}</td>
                            <td>{{ @$report->status->status }}</td>
                            
                        </tr>
                    @endforeach

                    </tbody>
                </table>
                {!! $reports->links() !!}
            </div>
        
    <meta name="_token" content="{!! csrf_token() !!}"/>
@endsection