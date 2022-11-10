@extends('admin.layouts.ufxtemplate')
        @section('content')
    <!-- Page-Title -->
    <div class="row">
        <div class="col-sm-12">
            <h4 class="page-title">Dashboard</h4>
            <p class="text-muted page-title-alt">Welcome to {{ Auth::user()->role->role_title }} panel !</p>
        </div>
    </div>


    <div class="row">

        @if(Auth::user()->role_id == 1)
         <div class="col-md-6 col-lg-3">
            <div class="widget-bg-color-icon card-box fadeInDown animated">
                <div class="bg-icon bg-icon-info pull-left">
                    <i class="fa fa-inr text-info"></i>
                </div>
                <div class="text-right">
                    <h3 class="text-dark"><b class="counter">{{ number_format($distributed_balance,2) }}</b></h3>
                    
                </div>
                <div class="clearfix"></div>
            </div>
        </div>
        
        @else

        <div class="col-md-6 col-lg-3">
            <div class="widget-bg-color-icon card-box fadeInDown animated">
                
                      
                
                <div class="text-left">
                   <h3 class="text-muted">Total Balance</h3> <h3 class="text-dark"><b class="counter">{{ number_format(Auth::user()->balance->user_balance,2) }}</b></h3>
                 
                </div>
                <div class="clearfix"></div>
            </div>
        </div>
        @endif

        <div class="col-md-6 col-lg-3">
            <div class="widget-bg-color-icon card-box">
               
                <div class="text-left">
                     <h3 class="text-muted">Money Sales</h3><h3 class="text-dark"><b class="counter">510</b></h3>
                    
                </div>
                <div class="clearfix"></div>
            </div>
        </div>

        <div class="col-md-6 col-lg-3">
            <div class="widget-bg-color-icon card-box">
                
                <div class="text-left">
                  <h3 class="text-muted">Recharge Sales</h3>  <h3 class="text-dark"><b class="counter">95</b>%</h3>
                    
                </div>
                <div class="clearfix"></div>
            </div>
        </div>
		<div class="col-md-6 col-lg-3">
            <div class="widget-bg-color-icon card-box">
               
                <div class="text-left">
                    <h3 class="text-muted">Coming Soon...</h3><h3 class="text-dark"><b class="counter">95</b>%</h3>
                    
                </div>
                <div class="clearfix"></div>
            </div>
        </div>
        @if(Auth::user()->role_id == 1)
         <div class="col-md-6 col-lg-3">
            <div class="widget-bg-color-icon card-box fadeInDown animated">
                <div class="bg-icon bg-icon-info pull-left">
                    <i class="fa fa-inr text-info"></i>
                </div>
                <div class="text-right">
                    <h3 class="text-dark"><b class="counter">{{ number_format($recharge_balance,2) }}</b></h3>
                    <p class="text-muted">Recharge Distributed</p>
                </div>
                <div class="clearfix"></div>
            </div>
        </div>
        @endif

       
    </div>

    
    <!-- end row -->


    
    <!-- end row -->

@endsection