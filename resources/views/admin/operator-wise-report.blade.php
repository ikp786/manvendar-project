@extends('admin.layouts.templatetable')

@section('content')
@include('search.date-search-export')
<div class="">
	<table  class="table table-bordered" >
		        <thead>
                    <tr>
						<th>Provider Name</th>
						<th>Success</th>
						<th>Pending</th>
						<th>Failed</th>
						<th>Manul Success</th>
                    </tr>
                </thead>
				<?php $successVolume = $pendingVolume = $failVolume =$refundVolume = 0;?>
                <tbody>
                    @foreach($newArray as $key => $value)
						<tr>
						
							<td>{{$key}}</td>
							<td>{{@$value->SUCCESS}}</td>
							<td>{{@$value->PENDING}}</td>
							<td>{{@$value->FAILURE}}</td>
							<td>{{@$value->MANUALSUCCESS}}</td>
							<?php 
								$successVolume += @$value->SUCCESS;
								$pendingVolume += @$value->PENDING;
								$failVolume += @$value->FAILURE;
								$refundVolume += @$value->MANUALSUCCESS;
								?>
						</tr>
                    @endforeach
						<tr>
							<td>Total</td>
							<td>{{@$successVolume}}</td>
							<td>{{@$pendingVolume}}</td>
							<td>{{@$failVolume}}</td>
							<td>{{@$refundVolume}}</td>
						</tr>
                </tbody>
            </table>
                
        </div>
   
    <meta name="_token" content="{!! csrf_token() !!}"/>
@endsection