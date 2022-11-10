<script type="text/javascript">
	function validateForm()
	{
		if($("#number").val() =='')
		{
			alert("Enter Number or Txn id");
			$("#number").focus();
			return false;
		}
		
	}
	function validateExportForm()
	{
		
		
		
	}


</script>
<div class="panel panel-default">
        <div class="panel-body">
                            <div class="col-md-5">
                                <form method="get" action="{{ Request::url() }}" class="form-inline" role="form" onSubmit="return validateForm()">
                                   <div class="form-group col-md-3">
                                       {{ Form::select('searchType', ['NUMBER' => 'Number', 'TXNID' => 'Txn Id','MOB'=>"Mob No"], null, ['class'=>'form-control ']) }}

                                    </div> 
									<div class="form-group col-md-4">
                                        <input name="number" type="text" class="form-control" id="number" value="{{app('request')->input('number')}}"
                                               placeholder="Number">
                                    </div>
									<div class="form-group col-md-5">
                                       <button type="submit"
												class="btn btn-success  btn-md">Search
										</button> 
                                   
                                       <a href="{{ Request::url() }}"/ class="btn btn-primary  btn-md">Reset
										</a>
                                    </div> 
									
                                </form>
                            </div>
                             @if (in_array(Auth::user()->role_id,array(1,11,12,14)))
                            <div class="col-md-7">
                           
                                <form method="get" action="{{ Request::url() }}" onSubmit="return validateExportForm()">
                                    <div class="form-group col-md-4">
                                        <input name="fromdate" class="form-control" type="date" value="{{ (app('request')->input('fromdate')) ? app('request')->input('fromdate') : date('Y-m-d')}}">
                                    </div>
                                    <div class="form-group col-md-4">
                                        <input name="todate" class="form-control" type="date" value="{{ (app('request')->input('todate')) ? app('request')->input('todate') : date('Y-m-d')}}">
                                    </div>
                                    <div class="form-group col-md-3">
                                       {{ Form::select('searchOf', ['1' => 'Success', '2' => 'Failed','3'=>"Pending"], null, ['class'=>'form-control','placeholder'=>"--Select--"]) }}

                                    </div> 
									<div class="form-group col-md-4">
                                        <button name="export" value="SEARCH" type="submit"
                                                class="btn btn-primary btn-md"></span>Search
                                        </button>
                                   
                                        <button name="export" value="EXPORT" type="submit"
                                                class="btn btn-basic btn-md"></span>Export
                                        </button>
                                    </div> 
                                </form>
                               
                            </div>
                             @endif
                            
    </div>
</div>