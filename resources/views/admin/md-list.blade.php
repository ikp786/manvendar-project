@if(in_array(Auth::user()->role->id,array(1)))
    @extends('admin.layouts.templatetable')
    @section('title','Md Lists')
    @section('content')
        <!-- Page-Title -->
        <div class="row">
            <div class="col-sm-12">
                <div class="col-lg-6 col-md-6">
                    <h4 class="page-title" style="color: white; font-size: 36px;">{{ 'Md Lists' }}</h4>
                    
                </div>
               
            </div>
        </div><br>
        

        <!--Basic Columns-->
        <!--===================================================-->


        <!--===================================================-->
        <div class="row">
            <div class="col-sm-12">
                <div class="card-box">
                    <div class="panel panel-default">
                        <div class="panel-body">
                            <div class="col-md-12">
                                <div class="col-md-10">
                                    <form action="{{url('downloade-revenue-expenses')}}" method="get">
                                       <!--  <div class="form-group col-md-4">
                                            <label class="form-group" for="From Date">From Date</label>
                                            <input name="from_date" class="form-control" type="date" id="from_date">

                                        </div> -->
                                        <!-- <div class="form-group col-md-4">
                                            <label class="form-group" for="To Date">To Date</label>
                                            <input name="to_date" class="form-control" type="date" id="to_date">
                                        </div> -->
                                       
<!-- 
                                        <button onclick="getDate(this)" type="button"
                                                class="btn btn-success" value="search">
                                                Search
                                        </button>
                                        <button  
                                                class="btn btn-success" type="submit" name="excel">
                                                Excel
                                        </button> -->
                                    </form>
                                    </div>
                                </div>
                                 
                            </div>

                        </div>
                    
                        <div class="container">
                          <table id="demo-custom-toolbar" data-toggle="table"
                             data-toolbar="#demo-delete-row"
                             data-search="true"
                             data-show-export="true"
                             data-page-list="[10, 20, 30]"
                             data-page-size="80"
                             data-pagination="true" class="table-bordered ">
                            <thead>
                              <tr>
                                  <th data-field="id" data-sortable="true">Company Id </th>
                                  <th data-field="user_id" data-sortable="true">User Id </th>
                                  <th data-field="user" data-sortable="true">User</th>
                                  <th data-field="md_report" data-sortable="true">MD Reprot</th>
                                  <th data-field="distributor_report" data-sortable="true">Distributor Reprot</th>
                                  <th data-field="retailser_report" data-sortable="true">Retailer Reprot</th>
                                  <th data-field="api_user_report" data-sortable="true">API Reprot</th>
                                  
                                  
                              </tr>
                            </thead>

                            <tbody>
							
                              @foreach($md_lists as $key => $value)
                                <tr>
                                  <td>{{ $value->id }}</td>
                                  <td>{{ $value->user_id }}</td>
                                  <td> {{ $value->company_name}}</td>
                                      @if(Auth::user()->role_id==1)
                                        @if($value->id !=3)
      								                      <td><a href="{{ url('admin/md-agent-reports')}}/{{ $value->id}}" class="btn btn-success" role="button">Get Invoice</a></td>
                                        @else
                                          <td></td>
                                        @endif
                                      @endif
                                      @if(in_array(Auth::user()->role_id,array(1,3)))
                                              <td><a href="{{ url('admin/distributors-list')}}/{{ $value->id}}" class="btn btn-success" role="button">Distributor List</a></td>
                                      @endif
                                      @if(in_array(Auth::user()->role_id,array(1,3,5)))
                                      <td><a href="{{ url('admin/retailers-list')}}/{{ $value->id}}" class="btn btn-success" role="button">Retailer List</a></td>
                                      @endif

                                  
                  
                                  
                                </tr>  
                              @endforeach
                            </tbody>
                          </table>
                        </div>
                    </div>
                  </div>

            </div>
			<input name ="_token" type="hidden" value="{{csrf_token()}}">

 <!--        </div> -->

    @endsection
    @endif