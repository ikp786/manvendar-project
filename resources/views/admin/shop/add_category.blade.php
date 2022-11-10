@extends('admin.layouts.template')

@section('content')



    <div class="row">
        <div class="col-sm-12">
            <h4 class="page-title">{{ $page_title or 'Category Master' }}</h4>
            <ol class="breadcrumb">
                <li>
                    <a href="#">Category</a>
                </li>
                <li class="active">
                    Add Category
                </li>
            </ol>
        </div>
    </div>


    <div class="row">
        <div class="col-sm-12">
            <div class="card-box">
                <div class="row">
                    <div class="col-md-8">
                        <form action="{{ url('admin/add-category') }}" class="form-horizontal" role="form">
                            {!! csrf_field() !!}
                            <div class="form-group">
                                <label class="col-md-3 control-label">Category Name</label>
                                <div class="col-md-9">
                                    <input type="text" class="form-control" placeholder="Category Name">
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-md-3 control-label">Category Detail</label>
                                <div class="col-md-9">
                                    <textarea name="detail" class="form-control" rows="5"></textarea>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-md-3 control-label">Meta Tag Title</label>
                                <div class="col-md-9">
                                    <input type="text" class="form-control" placeholder="Meta Tag Title">
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-md-3 control-label">Meta Tag Description</label>
                                <div class="col-md-9">
                                    <input type="text" class="form-control" placeholder="Meta Tag Description">
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-md-3 control-label">Meta Tag Keyword</label>
                                <div class="col-md-9">
                                    <input type="text" class="form-control" placeholder="Meta Tag Title">
                                </div>
                            </div>
                            <div class="form-group m-b-0">
                                <div class="col-sm-offset-3 col-sm-9">
                                    <button type="submit" class="btn btn-default">Sign in</button>
                                </div>
                            </div>
                        </form>
                    </div>

                    <div class="col-md-4">

                    </div>
                </div>
            </div>
        </div>
    </div>




@endsection


