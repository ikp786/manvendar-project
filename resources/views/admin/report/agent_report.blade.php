@extends('admin.layouts.templatetable')

@section('content')
@include('search.date-search-only')

<div class="row">
    <table id="example2" class="table table-bordered ">
        <thead>
			<tr>
				<th>Agent Name/Agent Id</th>
				<th>Member Type</th>
				<th>Txn Count</th>
				<th>Success Txn Amount</th>
				<th>Txn Charge</th>
				<th>Txn Commission</th>                    
			</tr>
        </thead>
        <tbody>
        @foreach($reports as $key => $value)
            <tr>
                <td>{{ @$value->user->name }}<br> (R {{  @$value->user->id }})</td>
                <td>{{$value->user->role->role_title}}</td>
				<td>{{ @$value->txn_count }}</td>
                <td>{{ @$value->total_sales }}</td>
                <td>{{ number_format(@$value->txn_charge,2) }}</td>
                <td>{{  number_format(@$value->txn_commission,2) }}</td>
                            
            </tr>
        @endforeach
        </tbody>
    </table>
    {!! $reports->links() !!}
</div>
   
    <meta name="_token" content="{!! csrf_token() !!}"/>
@endsection