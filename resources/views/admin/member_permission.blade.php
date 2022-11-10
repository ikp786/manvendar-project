@extends('admin.layouts.templatetable')
@section('content')
 
    <div class="row">
        <div class="col-sm-12">
            <div class="card-box">  
<!------------------------------------------------------------------------------------>
                <form class="cmxform form-horizontal user_form"  enctype="multipart/form-data" id="form_cord_eval" action="{{url('/admin/member_permission/insert_data')}}" role="form" method="post">
                     {!! csrf_field() !!}    
                    <div class="row">
                        <div class="col-md-10">
                            <div class="form-group">
                                <div class="col-sm-5">
                                    <h4 class="m-t-0 header-title"><b>User Permission detail</b></h4>
                                </div> 
                                <div class="col-sm-5">
                                    <!-- <select class="form-control" name="user_role" id="user_role">
                                        <option value="">--Select Role--</option>
                                        @foreach(@$GetRole as $DataRole)
                                        <option {{@$Type == $DataRole->id ? 'selected=""' : "" }} value="{{$DataRole->id}}">{{$DataRole->role_title}}</option>
                                        @endforeach
                                    </select> -->
                                    <input type="hidden" name="user_perm_id" value="{{@$Userid}}" />
                                </div>
                                <div class="col-sm-2">
                                    <button type="submit" class="btn btn-primary waves-effect waves-light pull-right">
                                        Save Now
                                    </button> 
                                </div>
                            </div>
                        </div>
                        <div class="col-md-10">
     
                            @if(Session::has('message'))
                                <div class="alert alert-success"><span
                                            class="glyphicon glyphicon-ok"></span>{{ Session::get('message') }}
                                </div>
                            @endif
                            <div class="form-horizontal">  
<!--                            
<style> 
    .panel-heading  a:before {
       font-family: 'Glyphicons Halflings';
       content: "\e114";
       float: right;
       transition: all 0.5s;
    }
    .panel-heading.active a:before {
    	-webkit-transform: rotate(180deg);
    	-moz-transform: rotate(180deg);
    	transform: rotate(180deg);
    } 
</style>

<div class="panel panel-default">
    <div class="panel-heading" role="tab" id="headingTwo">
      <h4 class="panel-title">
        <a class="collapsed" role="button" data-toggle="collapse" data-parent="#accordion" href="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
          Collapsible Group Item #2
        </a>
      </h4>
    </div>
    <div id="collapseTwo" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingTwo">
      <div class="panel-body">
        Anim pariatur cliche reprehenderit, enim eiusmod high life accusamus terry richardson ad squid. 3 wolf moon officia aute, non cupidatat skateboard dolor brunch. Food truck quinoa nesciunt laborum eiusmod. Brunch 3 wolf moon tempor, sunt aliqua put a bird on it squid single-origin coffee nulla assumenda shoreditch et. Nihil anim keffiyeh helvetica, craft beer labore wes anderson cred nesciunt sapiente ea proident. Ad vegan excepteur butcher vice lomo. Leggings occaecat craft beer farm-to-table, raw denim aesthetic synth nesciunt you probably haven't heard of them accusamus labore sustainable VHS.
      </div>
    </div>
</div>   -->         
                          
                                    <div class="form-group"> 
    	                                <div class="col-md-12"> 
    	                                    <ul class="list-group"> 
    	                                        @php $m=1; @endphp
    	                                    	@foreach(@$Menus as $DataMenu)
    	                                    	    @php
									        	        $GetMenuAssignedPer = App\RolePermission::where('user_id', @$Userid)->where('action_id', $DataMenu->id)->first();
									        	    @endphp
    												        	    
    											    <li class="list-group-item panel-heading">
    											    	<label class="checkbox-inline">
    											    		<input data-id="{{$m}}" {{ (@$GetMenuAssignedPer->action_id==$DataMenu->id) ? '' : ' checked ' }} type="checkbox" class="opc1" value="{{$DataMenu->id}}" >
    											    		<input type="hidden" id="sm_perm_{{$m}}" name="permission_to[]" value="" />
    											    		<span> {{$DataMenu->name}} </span>
    											    	</label>
     <!-- <a class="collapsed" role="button" data-toggle="collapse" data-parent="#accordion" href="#collapse{{$DataMenu->id}}" aria-expanded="false" aria-controls="collapse{{$DataMenu->id}}">
          </a> -->
        
        
    											    	@php 
    														$GetAddedSubMenu = App\Action::where('parent_id', @$DataMenu->id)->get();  
    														$k=1;
    													@endphp
    													@if(!$GetAddedSubMenu->isEmpty()) 
    												        <ul class="list-group inner" 
    												        
    	id="collapse{{$DataMenu->id}}" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingTwo"
    	
    												        role="tabpanel">
    												        	@foreach(@$GetAddedSubMenu as $DataSubMenu)
    												        	    @php
    												        	        $GetAssignedPer = App\RolePermission::where('user_id', @$Userid)->where('action_id', $DataSubMenu->id)->first();
    												        	    @endphp
    													            <li class="list-group-item"> 
    													            	<label class="checkbox-inline">
    															    		<input data-id="{{$m.$k}}" class="opc1" {{ (@$GetAssignedPer->action_id==$DataSubMenu->id) ? '' : ' checked ' }} type="checkbox"  value="{{$DataSubMenu->id}}" >
    															    		<input type="hidden" id="sm_perm_{{$m.$k}}" name="permission_to[]" value="" />
    															    		<span> {{$DataSubMenu->name}} </span>
    															    	</label>  
    													            </li>
    													            @php $k++; @endphp
    													        @endforeach
    												        </ul>
    												    @endif
    											    </li> 
    											    @php $m++; @endphp
    											@endforeach
    										</ul>
    	                                </div>
    	                            </div>   
                            </div>
                        </div> 
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

