@extends('admin.layouts.template')

@section('content')


    <div class="row">
        <div class="col-sm-12">
            <h4 class="page-title">{{ $page_title or 'Change Password'}}</h4>
            <ol class="breadcrumb">
                <li>
                    <a href="{{ url('admin/dashboard') }}">Home</a>
                </li>
                <li class="active">
                    My Profile
                </li>
            </ol>
        </div>
    </div>


    <div class="row">
        <div class="col-sm-12">
            <div class="card-box">
                <div class="row">
                    <div class="col-md-8">

                        @include('common.error')
                        @if(Session::has('flash_message'))
                            <div class="alert alert-success"><span
                                        class="glyphicon glyphicon-ok"></span><em> {!! session('flash_message') !!}</em>
                            </div>
                        @endif
                            <div class="tab-body" style="padding-bottom: 0;">

                                <h3 class="title title-lg">Change Password</h3>
                                <hr>
                                <div class="row">
                                    <form class="col-xs-6" method="POST" action="{{ url('change_password') }}">
                                        {!! csrf_field() !!}
                                        <div class="form-group">
                                            <div>
                                                <label for="old_passwor">Old Password</label>
                                                <input type="password" name="old_password" class="form-control"
                                                       id="exampleInputEmail1"
                                                       placeholder="Old Password">
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label for="exampleInputPassword1">New Password</label>
                                            <input type="password" class="form-control" name="password"
                                                   id="password"
                                                   placeholder="New Password">
                                        </div>
                                        <div class="form-group">
                                            <label for="exampleInputPassword1">Confirm Password</label>
                                            <input type="password" class="form-control" id="confrim_password"
                                                   placeholder="Confirm Password">
                                        </div>
                                        <button type="submit" class="btn btn-info">Change Password</button>
                                    </form>
                                </div>
                                <hr>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4">

                    </div>


                </div>
        </div>
    </div>

@endsection


