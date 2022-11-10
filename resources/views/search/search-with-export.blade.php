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
                            <div class="col-md-6">
                                <form method="get" action="{{ Request::url() }}" class="form-inline" role="form" onSubmit="return validateForm()">
                                    <div class="form-group">
                                       {{ Form::select('searchType', ['NUMBER' => 'Number', 'TXNID' => 'Txn Id','MOB'=>"Mob No"], null, ['class'=>'form-control']) }}

                                    </div> <div class="form-group">
                                        <label class="sr-only" for="payid">Number</label>
                                        <input name="number" type="text" class="form-control" id="number" value="{{app('request')->input('number')}}"
                                               placeholder="Number">
                                    </div>
                                    <button type="submit"
                                            class="btn btn-success  btn-md"><span
                                                class="glyphicon glyphicon-find"></span>Search
                                    </button> 
									<a href="{{ Request::url() }}"/ class="btn btn-primary  btn-md">Reset
                                    </a>
                                </form>
                            </div>
                             @if (in_array(Auth::user()->role_id,array(1,11,12,14)))
                            <div class="col-md-6">
                           
                                <form method="get" action="{{ Request::url() }}" onSubmit="return validateExportForm()">
                                    <div class="form-group col-md-5">
                                        <input name="fromdate" class="form-control" type="date" value="{{ (app('request')->input('fromdate')) ? app('request')->input('fromdate') : date('Y-m-d')}}">
                                    </div>
                                    <div class="form-group col-md-5">
                                        <input name="todate" class="form-control" type="date" value="{{ (app('request')->input('todate')) ? app('request')->input('todate') : date('Y-m-d')}}">
                                    </div>
                                    <div class="form-group col-md-2">
                                        <button name="export" value="EXPORT" type="submit"
                                                class="btn btn-primary btn-md"></span>Export
                                        </button>
                                    </div>
									
                                </form>
                               
                            </div>
                             @endif
                            
    </div>
</div>