@extends('admin.layouts.templatetable')
@section('content')
<div class="row">
        <div class="col-sm-12">
            @include('search.only-search-with-export')
            <div class="table-responsive" >
				<table id="example2"  class="table table-bordered " >
                    <thead>
                    <tr>
					 <th data-field="date" data-sortable="true">&nbsp&nbsp&nbsp&nbspDate/Time &nbsp&nbsp&nbsp&nbsp&nbsp&nbsp </th>
					 
                        <th>Order ID</th>
                        <th>Wallet</th>
                        <th>User</th>
                        <th>Transfer To/From</th>
                        <th>Firm Name</th>
                        <th>txnId</th>
                        <th>Ref Id</th>
                        <th>Description</th>
						<th>Opening Bal</th>
                        <th>Amount</th>
                        <th>Closing Bal</th>
                         <th>Agent Remark</th>
                        <th data-field="status" data-align="center" data-sortable="true" data-formatter="statusFormatter">Status
                        </th>
                    </tr>
                    </thead>
                    <tbody>
					 <?php $count=$totalAmount=0;?>
                    @foreach($rtoRReports as $key => $value)
					<?php $s = @$value->created_at;
						$dt = new DateTime($s);
						$u = @$value->payment->updated_at;
						$du = new DateTime($u);
						$r=@$value->payment->created_at;
						$dr = new DateTime($r);
						?>
                        <tr>
							<td>{{$dt->format('d/m/Y')}}<br>{{$dt->format('H:i:s')}}</td>
							
							            
							<td>{{$value->id}}</td>
                            <td>{{($value->recharge_type == 1) ? 'Recharge' : 'Money' }}</td>
                            <td>{{$value->user->name}}({{ $value->user->id }})</td>
                            <td>@if(is_numeric($value->credit_by))
								{{@$value->creditBy->name}} ({{@$value->creditBy->prefix}}-{{@$value->creditBy->id}})<br>
								@else
									{{@$value->credit_by}}
								@endif 
							</td>                          
                            <td>@if(is_numeric($value->credit_by))
							{{@$value->creditBy->member->company}}@endif</td>
                            <td>{{$value->txnid}}</td>
                            <td>{{$value->ref_id}}</td>
                            <td>{{$value->description}}</td>
							<td>{{number_format($value->opening_balance,2)}}</td>
                            <td>{{number_format($value->amount,2)}}</td>
                            <td>{{number_format($value->total_balance,2)}}</td>
							<td>{{ $value->remark }}</td>
                            
							<td>{{@ $value->status->status }}</td>
							<?php
							    $totalAmount +=	$value->amount;
                                $count++;
                            ?>
                        </tr>
                    @endforeach
                    </tbody>
					<h4 style="color:red">Total Amount({{$count}}) : {{$totalAmount}}</h4>
                </table>
              {{$rtoRReports->appends(\Input::except('page'))->render() }}
            </div>
        </div>
    </div>
   
@endsection