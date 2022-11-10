@extends('admin.layouts.templatetable')

@section('content')

<!-- Page-Title -->
        <div class="row">
            <div class="col-sm-12">
                <div class="col-lg-6 col-md-6">
                    <h4 class="page-title">{{ $page_title or 'Tax Invoice Detail' }}</h4>
                    <ol class="breadcrumb">
                        <li>
                            <a href="{{ url('dashboard') }}">Home</a>
                        </li>
                        <li class="active">
                            {{ $page_title or 'Tax Invoice Detail' }}
                        </li>
                    </ol>
                </div>
                <div class="col-lg-6 col-md-6">
                    <div class="pull-right">
                        <button onclick="add_record()" id="demo-add-row" class="btn btn-success"><i
                                    class="fa fa-plus m-r-5"></i>Add Record
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

                <div class="panel panel-default">
                    <div class="panel-body">
                        <div class="col-md-12">
                            <div class="col-md-8">
							<div class="box-header with-border">
							<h3 class="box-title">Import Tax Invoice Data</h3>
							<div class="form-group">
							<form class="form-horizontal" action="{{url('import-invoice-data')}}" method="post" enctype="multipart/form-data">
							<input name="_token" hidden value="{!! csrf_token() !!}" />
							<div class="col-sm-6">
							<input type="file" class="form-control" id="import_invoice_data" name="import_invoice_data">
							</div>
							<button type="submit" class="btn btn-info pull-left">Save</button>
							</form>
							</div>
				
							</div>
                             
                            </div>
                            <script>

                            </script>
                        </div>

                    </div>
                </div>
				
				 <div class="panel panel-default">
                    <div class="panel-body">
                        <div class="col-md-12">
                            <div class="col-md-8">
                                <h3 class="box-title">Import TDS Data</h3>
								 <div class="form-group">
									<form class="form-horizontal" action="#" method="post" enctype="multipart/form-data">
									  <input name="_token" hidden value="{!! csrf_token() !!}" />
									<div class="col-sm-6">
										<input type="file" class="form-control" id="import_tds_data" name="import_tds_data">
									</div>
										 <button type="submit" class="btn btn-info pull-left">Save</button>
									</form>
									</div>
								
								
							
                            </div>
                            <script>

                            </script>
                        </div>

                    </div>
                </div>
				
				
				
               
        </div>
		</div>
    <!-- END wrapper -->
    <meta name="_token" content="{!! csrf_token() !!}"/>
@endsection