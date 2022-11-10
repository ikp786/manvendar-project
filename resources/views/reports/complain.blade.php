@extends('layouts.app')

@section('content')
@include('agent.report.report-type')
<br>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.4.0/css/font-awesome.min.css"/>

<link rel="stylesheet" href="css/dataTables.bootstrap.css"/>
<script src="js/jquery.dataTables.js"></script>
<script src="js/dataTables.bootstrap.js"></script>
<script>

$(document).ready(function () {
$('#dataTables-example').dataTable();
});
</script>
    
<script>
function editRequest(id)
{
	$('#e_remark_'+id).prop('disabled', false);
	$('#e_text_'+id).hide();
	$('#u_text_'+id).show();
	$('#u_text_'+id).addClass("e_update_"+id);
	
}
function upRequest(id) {
           var a_remark = $('#e_remark_'+id).val();
		   
		  var c_id = $('.cid_'+id).val();
		 
		    var dataString = 'a_rem=' + a_remark + '&co_id=' +c_id;
		   $.ajax({
                type: "get",
                url: "{{url('agent-request-update')}}",
                data: dataString,
                success: function (data) {
                 alert(data);
				 location.reload();
                }
            }) 
        }
        function deleteRequest(id)
        {
             
          var c_id = $('.cid_'+id).val();
            var dataString = 'co_id=' +c_id;
           $.ajax({
                type: "GET",
                url: "{{url('/agent-request-delete')}}",
                data: dataString,
                success: function (data) {
                    alert(data);
                 location.reload();
                }
            }) 

        }
		
</script>
 <style>
    .Resolved .label-warning
    {
    background:green;
    }
    .InProcess .label-warning
    {
    background:blue;
    }
    .SentToBank .label-warning
    {
        background:red;
    }
    .AMOUNT
    {
        background: yellow;
        padding: 5px; 
    }
    .DOUBLE
    {
        background: red;
        padding: 5px; 
    }
    .WRONG
    {
        background: red;
        padding: 5px; 
    }
</style>
<div class="super_container"> 
    <div class="home">
            
        
        </div>
    <div class="search" >

    <div class=""><!-- container -->
        <div class="cord-box">
    
            <div class="table-responsive"  style="overflow-y: scroll; max-height:430px">
                            <table class="table table-bordered table-hover" id="example2">
                                    <thead style="color: black">
                                        <tr>
                                            <th>DateTime</th>
                                            <th>Issue Date</th>
                                            <th>ID</th>
                                            <th>Txn ID</th>
                                            <th>User Name</th>
                                            <th>Issue Type</th>
                                            <th>Product</th>
                                            <th>Acc Number</th>
                                            <th>Amount</th>
                                            <th>Remark</th>
                                            <th>Status</th>
                                           <!-- <th>Action</th> -->
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($complain as $value)
                                        <tr class="odd gradeX" style="background-color: white">
                                            <td>{{ \date_format($value->created_at,"d-m-Y H:i:s") }}</td>
                                            <td>{{ date("d-m-Y", strtotime($value->date_txn)) }}</td>
                                            <td>{{ $value->com_id }}</td>
                                            <td>{{ $value->txn_id }}</td>
                                             <td>{{ $value->name }} ({{ $value->user_id}})</td>
                                             @if($value->issue_type=='DOUBLE TXN' && $value->status=='Pending' || $value->issue_type=='WRONG TXN' && $value->status=='Pending')
                                                <td><span style="background:red; padding: 5px;">{{ $value->issue_type }}</span></td></td>
                                                @else
                                                 <td class="{{ $value->status }}"> {{ $value->issue_type }}</td>
                                                @endif
                                                <td>{{ $value->product}}</td>
                                                <td>{{ $value->bank_ac }}</td>
                                                <td>{{ $value->amount }}</td>
                                                <td>{{ $value->remark }}</td>
                                                <td class="{{ $value->status }}">{{ $value->status }}</td>
                                        </tr>
                                     @endforeach
                                    </tbody>
                            </table>
           
           </div>
       </div>            
    </div>
  </div>
  </div>  
@endsection