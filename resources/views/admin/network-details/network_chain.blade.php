@extends('admin.layouts.templatetable')

@section('content')
<div class="table-responsive"  style="overflow-y: scroll; max-height:430px">
            <table class="table table-bordered table-hover" id="example2">
                    <thead style="color: black">
                        <tr>
                            <th>Name</th>
                            <th>Mobile Number</th>
                            <th>Status</th>
                            <th>Email</th>
                            
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($users as $user)
                        <tr>
                        	<td>{{$user->name}}</td>
                        	<td>{{$user->mobile}}</td>
                        	<td>{{$user->status->status}}</td>
                        	<td>{{$user->email}}</td>
                        </tr>
                        @endforeach
                    </tbody>
            </table>
</div>            



@endsection
