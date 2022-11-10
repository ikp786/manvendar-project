@extends('admin.layouts.templatetable')
        @section('content')
 @if(Auth::user()->role_id == 1)      
<div class="container">
    <div class="card">
        
		<div class="">
                <div class="modal-header">
                    
                    <h4 class="modal-title" id="myModalLabel">Balance</h4>
                </div>
                <div class="modal-body">
                    <div id="frmTasks" name="frmTasks" class="form-horizontal">
                       <div class="form-group form-inline">
                            <label for="inputTask" class="col-sm-3 control-label">DMT 1 Balance</label>
                            <div class="col-sm-2">
                                <input type="text" disabled class="form-control" id="new_password" value="{{@$data}}"/>
                            </div>
                        </div>
						<div class="form-group form-inline">
                            <label for="inputTask" class="col-sm-3 control-label">A2Z Wallet Balance</label>
                            <div class="col-sm-2">
                                <input type="text" disabled class="form-control" value="{{@$tramoBalance}}"/>
                            </div>
                        </div>
						<div class="form-group form-inline">
                            <label for="inputTask" class="col-sm-3 control-label">Red Pay Api Balance</label>
                            <div class="col-sm-2">
                                <input type="text" disabled class="form-control" value="{{@$redPayBalance}}"/>
                            </div>
                        </div>
						<!--
						<div class="form-group form-inline">
                            <label for="inputTask" class="col-sm-3 control-label">Digital Balance</label>
                            <div class="col-sm-2">
                                <input type="text" disabled class="form-control" value="{{@$digitalBalance}}"/>
                                
                            </div>
                        </div>-->
						
                    </div>
                </div>
                
            </div>
		 <br> 
</div>  
</div>
   @endif
@endsection