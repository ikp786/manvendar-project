<script>
	$(document).ready(function () {
	
	 $.noConflict();
        $('.customDatepicker').datepicker({
              autoclose: true,  
            format: "dd-mm-yyyy"
        });

    }); 
	</script>

<div class="panel panel-default">
    <div class="panel-body">
        <h4 class="page-title" style="color: black; ">{{@$title }}</h4>
        <form method="get" action="{{ Request::url() }}" onSubmit="return validateExportForm()" class="form-inline">
              
            {{ Form::select('type', ['1' => "Summary", '2' => "Individual"], null, ['class'=>'form-control']) }}
            
            <input name="fromdate" class="form-control customDatepicker" type="text" value="{{ (app('request')->input('fromdate')) ? app('request')->input('fromdate') : date('d-m-Y')}}" autocomplete="off"> 
            
            <input name="todate" class="form-control customDatepicker" type="text" value="{{ (app('request')->input('todate')) ? app('request')->input('todate') : date('d-m-Y')}}" autocomplete="off">
              
            {{ Form::select('product', ['1' => "Recharge", '2' => "Verify",'4'=>"DMT1",'5'=>"A2Z Wallet",'25'=>"A2Z Plus Wallet",'10'=>"AEPS",'8'=>"Recharge2"], (app('request')->input('product')), ['class'=>'form-control','placeholder'=>"--Select--"]) }}
			
            {{ Form::select('user', $users,  app('request')->input('user'), ['class'=>'form-control','placeholder'=>"--Select--"]) }}

            <button name="export" value="SEARCH" type="submit" class="btn btn-primary btn-md">Search </button>
            <!--<button name="export" value="EXPORT" type="submit" class="btn btn-basic btn-md">Export </button>-->
            <a href="{{ Request::url() }}" class="btn btn-info  btn-md">Reset</a>
             
        </form>      
    </div>
</div>
