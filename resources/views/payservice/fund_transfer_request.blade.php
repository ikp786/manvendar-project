@extends('layouts.mobtemplate')


@section('content')

    <script>
      
      
    </script>


    <!-- Optional header components (ex: slider) -->
    <!-- Importing slider content -->
    <!-- include('includes.sliders.layer-slider-shop') -->


    <div class="row">
        <div class="col-md-12">
            @if (session('status'))
                <div class="alert alert-success">
                    {{ session('status') }}
                </div>
            @endif
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
        @endif
        <!-- Cart table --><br>
        <div class="col-md-12">
                           
                                <form method="get" action="{{ route('fund-transfer-request') }}">
                                    <div class="form-group col-md-4">
                                        <input name="fromdate" class="form-control" type="date">
                                    </div>
                                    <div class="form-group col-md-4">
                                        <input name="todate" class="form-control" type="date">
                                    </div>
                                    <div class="form-group col-md-2">
                                        <button name="export" value="Fund Request Reports" type="submit"
                                                class="btn btn-success waves-effect waves-light m-l-10 btn-md"><span
                                                    class="glyphicon glyphicon-find"></span>Export
                                        </button>
                                    </div>
                                    
                                </form>
                               
                            </div>
							<div class="col-md-12">
							<div style="overflow-x:auto;">
            <table id="demo-custom-toolbar" data-toggle="table"
                       data-toolbar="#demo-delete-row"
                       data-search="true"
                       data-show-export="true"
                       data-page-list="[10, 20, 30]"
                       data-page-size="80"
                       data-pagination="true" class="table">
                    <thead>
                <tr>

					<th data-field="date" data-sortable="true" data-formatter="dateFormatter">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Date&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; </th>
					<th data-field="time" data-sortable="true" data-formatter="dateFormatter">Time </th>
                    <th>ID</th>
                    <th>Transfer By</th>
                   
                    <th>Description</th>
					 <th>Transfered Amount</th>
                    <th>Remaining Balance</th>
                    <th>Status</th>
                   
                </tr>
                <tbody>
                @foreach($reports as $loadcash)
                    <tr>
						<?php $s = $loadcash->created_at;
						$dt = new DateTime($s);?>
						<td>{{ $dt->format('d-m-Y') }}</td>
										<td>{{ $dt->format('H:i:s') }}</td>
                        <td>{{ $loadcash->id }}</td>
                        <td>{{  \App\User::find($loadcash->credit_by)->name }}</td>
                       <td>{{ $loadcash->description }}</td>
						 <td>{{ $loadcash->amount }}</td>
                        <td>{{ $loadcash->total_balance }}</td>
                       
                        <td>{{ $loadcash->status->status }}</td>
					
                    </tr>
                @endforeach
                </tbody>
            </table></div></div>

                {!! $reports->links() !!}

        </div>

        
    </div>

 
@endsection