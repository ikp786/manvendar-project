<script>
	$(document).ready(function () {
	
	 $.noConflict();
        $('.customDatepicker').datepicker({
              autoclose: true,  
            format: "dd-mm-yyyy"
        });

    }); 
	</script>	<div class="panel panel-default">
<div class="panel-body">
            <div class="col-lg-3 col-md-3">
                <h4 class="page-title" style="color: black; ">{{@$title }}</h4>
                
			</div>
	
			<div class="row col-md-9">
				<form method="get" action="{{ Request::url() }}" onSubmit="return validateExportForm()" class="form-inline">
                    <div class="form-group">
                        <input name="fromdate" class="form-control customDatepicker" type="text" value="{{ (app('request')->input('fromdate')) ? app('request')->input('fromdate') : date('d-m-Y')}}" autocomplete="off"> 
                    </div>
                    <div class="form-group">
                        <input name="todate" class="form-control customDatepicker" type="text" value="{{ (app('request')->input('todate')) ? app('request')->input('todate') : date('d-m-Y')}}" autocomplete="off">
                    </div><div class="form-group">
                       {{ Form::select('searchOf', ['3' => 'Pending', '9' => 'In-Process','30'=>"Resolved",'29'=>"Reject"], app('request')->input('searchOf'), ['class'=>'form-control','placeholder'=>'--Select--']) }}
                    </div>
                    <button name="export" value="SEARCH" type="submit" class="btn btn-primary btn-md"><i class="fa fa-search"></i></button>
					 <button name="export" value="EXPORT" type="submit" class="btn btn-basic btn-md"><i class="fa fa-file-excel-o" aria-hidden="true"></i></button>
                    <a href="{{ Request::url() }}"/ class="btn btn-info  btn-md"><i class="fa fa-refresh"></i></a>
                      
                </form>
               
            </div>
            </div> 
			</div>