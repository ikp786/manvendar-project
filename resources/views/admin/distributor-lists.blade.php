@extends('admin.layouts.templatetable')
@section('content')
<style type="text/css">
.has-error {
    color: #FF0000;
}
.form-control {
    border: 1px solid #9E9E9E;
}
</style>

    <!-- Page-Title -->
    <div class="row" style="margin-top:100px;">
        <div class="col-sm-12">
            <div class="col-lg-6 col-md-6">
            <h4 class="page-title" style="color: white; font-size: 36px;">{{ $page_title or 'Distributor List' }}</h4>
                
            </div>
           
        </div>
    </div><br>

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
                       data-page-size="10"
                       data-pagination="true" data-show-pagination-switch="true" class="table-bordered ">
                    <thead>
                    <tr>
                        <th data-field="id" data-sortable="true">
                            S. No
                        </th>
                        <th data-field="c_id" data-sortable="true">Company Id</th>
                        <th data-field="p_id" data-sortable="true">Parent Id</th>
                        <th data-field="name" data-sortable="true">User</th>
                        <th data-field="mobile" data-sortable="true" data-formatter="dateFormatter">Mobile
                        </th>
                        <th data-field="email_id" data-align="center" data-sortable="true"
                            >Email
                        </th>
                        <th data-field="Pan" data-align="center" data-sortable="true"
                            >Pan No
                        </th>
                        <th data-field="status" data-align="center" data-sortable="true"
                            data-formatter="statusFormatter">Status
                        </th>
                        <th>Report</th>
                       
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($users as $key => $user)
					
                        <tr>
                            <td>{{ $key+1 }}</td>
                            <td>{{ $user->company_id }}</td>
                            <td>{{ $user->parent_id }}</td>
                            <td>{{ $user->name }} ({{ $user->id}})</td>
                            <td>{{ $user->mobile }}</td>
                            <td>{{ $user->email }}</td>
                            <td>{{ $user->member->pan_number }}</td>
                            <td>{{ @$user->status->status }}</td>
                           <td><a href="{{ url('admin/distributors-reports')}}/{{ $user->id}}" class="btn btn-success" role="button">Reports</a></td>
                           
                        </tr>
                    @endforeach
                    </tbody>
                </table>
                
            </div>
        </div>
    </div>

@endsection
