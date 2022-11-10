@extends('admin.layouts.templatetable')

@section('content')
    <script>
        function flush_month_limit() {
			if(confirm('Are you want to refresh monthly limits'))
			{
				var dataString = 'case=limit_flush';
				$.ajaxSetup({
					headers: {
						'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
					}
				})
				 $.ajax({
					type: "post",
					url: "{{url('flush_month_limit')}}",
					data: dataString,
					success: function (data) { 
						alert(data);
					}
				})
			}
        }
        //create new task / update existing task
       
    </script>

    <!-- Page-Title -->
    <div class="row">
        <div class="col-sm-12">
            <div class="col-lg-6 col-md-6">
                <h1 class="page-title" style="color:white; ">{{ $page_title or 'Users Monthly Details' }}</h1>
               <!-- <ol class="breadcrumb">
                    <li>
                        <a href="{{ url('dashboard') }}">Home</a>
                    </li>
                    <li class="active">
                        {{ $page_title or 'Member Detail' }}
                    </li>
                </ol>-->
            </div>
            <div class="col-lg-6 col-md-6">
                <div class="pull-right">
                    <button style="background:white !important; color:blue !important;"onclick="flush_month_limit()" id="demo-add-row" class="btn btn-success">Flush Monthly Limit
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!--Basic Columns-->
    <!--===================================================-->


    <!--===================================================-->
    <div class="row">
        <div class="col-sm-12">
            <div class="card-box">
                <table id="demo-custom-toolbar" data-toggle="table"
                       data-toolbar="#demo-delete-row"
                       data-search="true"
                       data-page-list="[20, 10, 20]"
                       data-page-size="10">
                    <thead>
                    <tr style="color:#115798;">
                        <th data-field="id" data-sortable="true">
                            ID
                        </th>
                        <th data-field="name" data-sortable="true">Member Name</th>
                        <th data-field="date" data-sortable="true" data-formatter="dateFormatter">Mobile
                        </th>
                       
                        <th data-field="used_limit" data-align="center" data-sortable="true"
                            data-sorter="priceSorter">Used Limit
                        </th>
                        <th data-field="remaining_limit" data-sortable="true"
                        >Remaining Limit
                        </th>
                       
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($month_limit as $user)
                        <tr>
                            <td>{{ $user->id }}</td>
                            <td>{{ $user->f_name }} {{ $user->l_name }}</td>
                            <td>{{ $user->mobile }}</td>
                            <td>{{ $user->used_limit }}</td>
                            <td>{{ $user->total_limit- $user->used_limit}}</td>
                            
                           
							
                           
                        </tr>
                    @endforeach
                    </tbody>
                </table>
                {!! $month_limit->links() !!}
            </div>
        </div>
    </div>
    <div id="con-close-modal-one" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
         aria-hidden="true" style="display: none;">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                    <h4 class="modal-title">Member Editor</h4>
                </div>

            </div>
        </div>
    </div><!-- /.modal -->
    <meta name="_token" content="{!! csrf_token() !!}"/>
@endsection