@extends('admin.layouts.templatetable')
@section('content')
<style>
    .div_bal{
        width: 16%;
        float: left;
        padding: 6px 6px;
    }
    #main_data{
        border-bottom: 1px solid #c571bd;
        overflow: auto;
    }
    .div_balance{   
        width: 100%; 
        background: #f9fafc;
    }
    .center { text-align:center }
    
    .update-card {
        color: #fff;
    }
    .bg-c-yellow {
        background: -webkit-gradient(linear, left top, right top, from(#fe9365), to(#feb798));
        background: linear-gradient(to right, #fe9365, #feb798);
    } 
    .bg-c-green {
        background: -webkit-gradient(linear, left top, right top, from(#0ac282), to(#0df3a3));
        background: linear-gradient(to right, #0ac282, #0df3a3);
    }
    .bg-c-pink {
        background: -webkit-gradient(linear, left top, right top, from(#fe5d70), to(#fe909d));
        background: linear-gradient(to right, #fe5d70, #fe909d);
    }
    .bg-c-lite-green {
        background: -webkit-gradient(linear, left top, right top, from(#01a9ac), to(#01dbdf));
        background: linear-gradient(to right, #01a9ac, #01dbdf);
    }
    .bg-c-green2 {
        background: -webkit-gradient(linear, left top, right top, from(#0ac282), to(#0df3a3));
        background: linear-gradient(to right, #14865e, #0df3a3);
    }
    .bg-c-pink2 {
        background: -webkit-gradient(linear, left top, right top, from(#fe5d70), to(#fe909d));
        background: linear-gradient(to right, #a05dd2, #fe909d);
    }
    .bg-simple-c-green { 
        background: -webkit-gradient(linear, left top, right top, from(#0ac282), to(#01dbdf));
        background: linear-gradient(to right, #01a9ac, #01dbdf);
    }
    .card{ 
        border-radius: 5px;
        -webkit-box-shadow: 0 1px 20px 0 rgba(69,90,100,0.08);
        box-shadow: 0 1px 20px 0 rgba(69,90,100,0.08);
        border: none;
        margin-bottom: 7px;      	
        }
    .card-block {
        padding: 2.55rem;
    }
    .text-white {
        color: #fff!important;
    }
</style>
 <script type="text/javascript">

$(document).ready(function() {
	//alert("Hello");
   getSPFReport();
   getPOReport();
   getPendingComlain();
   getBalanceRequest();
});
	function getSPFReport(){
		var dataString="type=SPF";
		$.ajax({
			type: "GET",
			url: "{{url('count-txn-with-volume')}}",
			data: dataString,
			datatype: "json",
			beforeSend:function()
			{
				
			},
			success: function (res) {
				$("#successSpiner").hide();
				$("#successTxnCount").html(res.report.successTxnCount);
				$("#successTxnVolume").html(res.report.successVolume);
				$("#failTxnCount").html(res.report.failTxnCount);
				$("#failVolume").html(res.report.failVolume);
				$("#refundedTxnCount").html(res.report.refundedTxnCount);
				$("#refundedVolume").html(res.report.refundedVolume);
			}
        });
	}
	function getPOReport(){
	 var dataString="type=SPF";
		$.ajax({
			type: "GET",
			url: "{{url('get-po-txn-count-volume')}}",
			data: dataString,
			datatype: "json",
			beforeSend:function()
			{
				
			},
			success: function (res) {
				$("#offLineTxnCount").html(res.report.offLineTxnCount);
				$("#offLineVolume").html(res.report.offLineVolume);
				$("#pendingTxnCount").html(res.report.pendingTxnCount);
				$("#pendingVolume").html(res.report.pendingVolume);
				
			}
        });
	}
	function getPendingComlain(){
	 var dataString="type=SPF";
		$.ajax({
			type: "GET",
			url: "{{url('get-pending-complain')}}",
			data: dataString,
			datatype: "json",
			beforeSend:function()
			{
				
			},
			success: function (res) {
				$(".complainTxnCount").html(res.report.complainTxnCount);					
			}
        });
	}
	function getBalanceRequest(){
	  var dataString="type=BALANCE_REQUEST";
		$.ajax({
			type:"GET",
			url:"{{url('get-balance-request')}}",
			data:dataString,
			datatype:"json",
			beforeSend:function(){

			},
			success:function(res){
				$("#balanceTxnCount").html(res.report.balanceTxnCount);
				$("#balanceVolume").html(res.report.balanceVolume); 
			}
		});
	}
</script>
 @if(Auth::user()->role_id == 1)
	<script>
		$(document).ready(function() {
			getRoleWiseBalance();
			getApiBalance("TRAMO");
			getApiBalance("CYBER");
			getApiBalance("A2Z");
			getApiBalance("MROBOTICS");
		});
		function getRoleWiseBalance()
		{
			var dataString="type=BALANCE_REQUEST";
			$.ajax({
				type:"GET",
				url:"{{route('get-role-balance')}}",
				data:dataString,
				datatype:"json",
				beforeSend:function(){

				},
				success:function(res){
					if(res.status==1)
					{
						//console.log(res.details)
						for (i in res.details) {
							$("#availableBalance_"+res.details[i].roleId).text(res.details[i].availableBalance)
							$("#memberCount_"+res.details[i].roleId).text(res.details[i].userCount)
						}
					}
				}
		    });
		}
		function getApiBalance(balanceOf)
		{
			var dataString="getBalanceOf="+balanceOf;
			$.ajax({
				type:"GET",
				url:"{{route('get-api-balance')}}",
				data:dataString,
				datatype:"json",
				beforeSend:function(){

				},
				success:function(res){
					if(res.status==1)
					{
						for (i in res.details) {
						  $("#"+i).text(res.details[i])
						}
					}
				}
			});
		}
 
	</script>
<div>
	Bank Down List : <marquee style="color:red">{{@$down_bank_list}}</marquee>
</div>	 
<div class="row col-md-12" id="main_data">
    <div class="col-md-4">
         <table class="table">
    		<tbody>       
    			<tr><td style="background-color:lightgreen">Total Success</td><td> <a href="{{url('/')}}/api-report?fromdate={{date('d-m-Y')}}&todate={{date('d-m-Y')}}&searchOf=1">[<span id="successTxnCount"> </span>]</a> ₹ <a href="{{url('/')}}/recharge-nework?fromdate={{date('d-m-Y')}}&todate={{date('d-m-Y')}}&searchOf=1"><span id="successTxnVolume"><span></a><i class="fa fa-spin fa-refresh" style="color:black" id="successSpiner"></i></td></tr>
    			<tr><td style="background-color:#ff0000">Total Fail</td><td><a href="{{url('/')}}/api-report?fromdate={{date('d-m-Y')}}&todate={{date('d-m-Y')}}&searchOf=2">[<span id="failTxnCount"></span>]</a> ₹ <a href="{{url('/')}}/recharge-nework?fromdate={{date('d-m-Y')}}&todate={{date('d-m-Y')}}&searchOf=2"><span id="failVolume"><span></a></td></tr>
    			<tr><td style="background-color:#ffff00">Total Pending</td><td><a href="{{url('/')}}/api-report?fromdate={{date('d-m-Y')}}&todate={{date('d-m-Y')}}&searchOf=3">[<span id="pendingTxnCount"></span>]</a> ₹ <a href="{{url('/')}}/recharge-nework?searchOf=3"><span id="pendingVolume"><span></a></td></tr>
    			<tr><td style="background-color:#ff03d4">Total Refunded</td><td><a href="{{url('/')}}/api-report?fromdate={{date('d-m-Y')}}&todate={{date('d-m-Y')}}&searchOf=4">[<span id="refundedTxnCount"></span>]</a> ₹ <a href="{{url('/')}}/recharge-nework?fromdate={{date('d-m-Y')}}&todate={{date('d-m-Y')}}&searchOf=4"><span id="refundedVolume"><span></a></td></tr>
    			<tr><td></td></tr>
    			<tr><td style="background-color:#10f1f11c">Balance Request</td><td>[<span id="balanceTxnCount"> </span>] ₹ <a href="{{url('payment-request-view')}}"> 
    			<span id="balanceVolume"></span></a></td></tr>
    			<tr><td style="background-color:#ff03d4">Hold Txn</td><td>[<span id="offLineTxnCount"> </span>] ₹ <a href="{{url('/')}}/recharge-nework?searchOf=24"><span id="offLineVolume"><span></a></td></tr><!-- offline request -->
    			  <!-- over all data -->
    			<tr><td style="background-color:#ff03d4">Complain</td><td>[<span class="complainTxnCount"> </span>] ₹ <a href="{{url('/')}}/view-complain?searchOf=3&export=SEARCH"><span class="complainTxnCount"><span></a></td></tr>
    			<tr><td></td></tr>
    			<tr><td style="background-color:pink">Admin Balance</td><td>₹ {{number_format(Auth::user()->balance->user_balance,2)}}</td></tr>
    		   
    			  <tr><td style="background-color:#bfbfda">Master Dist Outlet</td><td><a href="{{url('admin/master-distributor')}}">[<span style="color:red" id="memberCount_3"></span>] ₹ <span id="availableBalance_3"></span></a></td></tr>
    			  <tr><td style="background-color:#cfdcdc">Dist Outlet</td><td><a href="{{url('admin/distributor')}}">[<span style="color:red" id="memberCount_4"></span>] ₹ <span id="availableBalance_4"></span></a></td></tr>
    			  <tr><td style="background-color:#98f598">Retailer Outlet</td><td><a href="{{url('admin/retailer')}}">[<span style="color:red" id="memberCount_5"></span>] ₹ <span id="availableBalance_5"></span></a></td></tr>
    			   <tr><td style="background-color:#98f598">Api Outlet</td><td><a href="{{url('admin/api-member')}}"> [<span style="color:red" id="memberCount_7"></span>]₹ <span id="availableBalance_7"></span></a></td></tr>
    		   <!-- <tr><td>Total Business Yesterday</td></tr>-->
    		</tbody>
         </table>
     </div>
	<div class="col-md-8">
     <table class="table">
        <thead>
          <th class="text-center">Apiname</th>
          <th>Balance</th>
        </thead>    
		<tbody>
		   <tr><td class="text-center">A2z Wallet:</td><td><span id="TRAMOBalance"></span></td></tr>
		   <tr><td class="text-center">DMT 1:</td><td><span id="CYBERBalance"></span></td></tr>
		   <tr><td class="text-center">A2ZSuvidhaa:</td><td><span id="A2ZBalance"></span></td></tr>
		   <tr><td class="text-center">MRobotics(Jio):</td><td><span id="MROBOTICSBalance"></span></td></tr>
		</tbody>
     </table>
	</div>
</div> 
<div class="row col-md-12 div_balance">
        @php
            	$user_retailer = App\User::leftjoin('balances', 'users.id', '=', 'balances.user_id')->where('users.role_id',5)->select(DB::raw('sum(user_balance) as user_tot_balance, sum(user_id) as user_count'))->first();
    		    $user_master = App\User::leftjoin('balances', 'users.id', '=', 'balances.user_id')->where('users.role_id',3)->select(DB::raw('sum(user_balance) as user_tot_balance, sum(user_id) as user_count'))->first();
    		    $user_dist = App\User::leftjoin('balances', 'users.id', '=', 'balances.user_id')->where('users.role_id',4)->select(DB::raw('sum(user_balance) as user_tot_balance, sum(user_id) as user_count'))->first();
    		    $user_admin = App\User::leftjoin('balances', 'users.id', '=', 'balances.user_id')->where('users.role_id',1)->select(DB::raw('sum(user_balance) as user_tot_balance, sum(user_id) as user_count'))->first();
    		    $user_api = App\User::leftjoin('balances', 'users.id', '=', 'balances.user_id')->where('users.role_id',7)->select(DB::raw('sum(user_balance) as user_tot_balance, sum(user_id) as user_count'))->first();
                  
                $total = $user_master->user_tot_balance + $user_dist->user_tot_balance + $user_retailer->user_tot_balance + $user_admin->user_tot_balance + $user_api->user_tot_balance;
        @endphp
    <div class="div_bal center"> 
        <div class="card bg-c-yellow update-card">
            <div class="card-block">
                <div class="row align-items-end">
                    <div class="col-8">
                        <h4 class="text-white">₹ {{ number_format($user_master->user_tot_balance,2) }} </h4>
                        <h5 class="text-white m-b-0">Master Distributor</h5>
                    </div> 
                </div>
            </div> 
        </div> 
    </div>
    <div class="div_bal center"> 
        <div class="card bg-c-pink update-card">
            <div class="card-block">
                <div class="row align-items-end">
                    <div class="col-8">
                        <h4 class="text-white">₹ {{number_format($user_dist->user_tot_balance,2)}} </h4>
                        <h5 class="text-white m-b-0">Distributor</h5>
                    </div> 
                </div>
            </div> 
        </div> 
    </div>
    <div class="div_bal center"> 
        <div class="card bg-c-green update-card">
            <div class="card-block">
                <div class="row align-items-end">
                    <div class="col-8">
                        <h4 class="text-white">₹ {{number_format($user_retailer->user_tot_balance,2)}}</h4>
                        <h5 class="text-white m-b-0">Retailer</h5>
                    </div> 
                </div>
            </div> 
        </div>
    </div>
    <div class="div_bal center"> 
        <div class="card bg-c-lite-green update-card">
            <div class="card-block">
                <div class="row align-items-end">
                    <div class="col-8">
                        <h4 class="text-white">₹ {{number_format($user_admin->user_tot_balance,2)}} </h4>
                        <h5 class="text-white m-b-0">Admin</h5>
                    </div> 
                </div>
            </div> 
        </div>
    </div>
    <div class="div_bal center"> 
        <div class="card bg-c-green2 update-card">
            <div class="card-block">
                <div class="row align-items-end">
                    <div class="col-8">
                        <h4 class="text-white">₹ {{number_format($user_api->user_tot_balance,2)}} </h4>
                        <h5 class="text-white m-b-0">API User</h5>
                    </div> 
                </div>
            </div> 
        </div>
    </div>
    <div class="div_bal center"> 
        <div class="card bg-c-pink2 update-card">
            <div class="card-block">
                <div class="row align-items-end">
                    <div class="col-8">
                        <h4 class="text-white">₹ {{number_format($total,2)}} </h4>
                        <h5 class="text-white m-b-0">Total</h5>
                    </div> 
                </div>
            </div> 
        </div>
    </div>
</div>
             
@endif 
@if(Auth::user()->role_id == 4)

     <div class="row">
        <div class="col-12 col-sm-6 col-md-3">
            <div class="info-box bg-info">
              <div class="info-box-content">
               <p class="heading">Payments</p>
         <p class="title">Transfered : {{@$transferedBalance}}</p>
         <p class="title">Received: {{@$receivedBalance}}</p>
			 {{--<div><span class="pull-left"><a href="#">Submit Payment Request</a></span> : <span class="pull-right"><a href="#">View Balance</a></span></div>--}}
              </div>
            </div>
          </div>
          <div class="col-12 col-sm-6 col-md-3">
            <div class="info-box bg-success">
              <div class="info-box-content">
                <p class="heading">Payments</p>
               <p class="title">Today Network : {{number_format(@$todayNetworkAmount)}}</p>
               <p class="title">Monthly Network : {{number_format(@$monthNetworkAmount)}}</p>
				   {{-- <a href="#">Tds Certificate</a>--}}
              </div>
            </div>
          </div>
          <div class="clearfix hidden-md-up"></div>
          <div class="col-12 col-sm-6 col-md-3">
            <div class="info-box mb-3" style="background-color:yellow">
              <span class="info-box-icon elevation-1" style="background-color:yellow"><i class="fa fa-money"></i></span>
              <div class="info-box-content">
                <p class="heading">Activation Kit</p><p class="title">{{number_format($total_profit,2)}}</p>
              </div>             
            </div>           
          </div>        
          <div class="col-12 col-sm-6 col-md-3">
            <div class="info-box mb-3 bg-warning">
              <span class="info-box-icon bg-warning elevation-1"><i class="fa fa-money"></i></span>
              <div class="info-box-content">
              <p class="heading">Purchase</p> <p class="title">{{number_format(@$purchase_balance,2)}}</p>
              </div>           
            </div>           
          </div>   
          
			<div class="slidetwo"> 
				<div><img src="{{url('newlog/images/IMAG2.jpg')}}" class="img-responsive"></div>
				
			</div> 
		
      </div>
   
<script type="text/javascript">
  setInterval(function() {
  $('.slidetwo > div:first')
    .fadeOut(2000)
    .next()
    .fadeIn(2000)
    .end()
    .appendTo('.slidetwo');
}, 8000)
</script>

<style type="text/css">
.slidetwo{
  position: absolute;
  margin-left: 45%; 
}
</style>  
 @endif
@endsection