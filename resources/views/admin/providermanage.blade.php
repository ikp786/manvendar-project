@extends('admin.layouts.templatetable')

@section('content')
    <script>       
        function providerSwitch(id)
        {
            if(confirm('Are You sure to Update?'))
            {            
				var provider_code = $('#p_code_'+id).val();
				var dataString = 'id=' + id + '&api_id='+ provider_code;
				 $.ajaxSetup({
					headers: {
						'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
					}
				})
				$.ajax({
					type: "put",
					url: "{{url('switch/operator')}}/"+id,
					data: dataString,
					dataType:"json",
					success: function (data) {
						if(data.status == 0)
						{
							$("#message_"+id).css('color','red');
						}
						else
							$("#message_"+id).css('color','green');
						$("#message_"+id).text(data.message);						
					}
				});
        }
        else
        {

        }
    }
        function down_bank_record()
        {
            var product = $('select[name=select_product]').val();
            var s_comment = $('#saral_cmnt').val();
            var sm_comment = $('#saral_cmnt').val();
            var sh_comment = $('#saral_cmnt').val();
            var dataString = 'product=' + product+'&down=down';
            $.ajax({
                type: "get",
                url: "{{url('bank/save')}}",
                data: dataString,
                success: function (data) {
                    alert(data);                    
                }
            })
        }
        function up_bank_record()
        {
            var product = $('select[name=select_product]').val();
            var dataString = 'product=' + product +'&up=up';
            $.ajax({
                type: "get",
                url: "{{url('bank/save')}}",
                data: dataString,
                success: function (data) {
                    alert(data);                    
                }
            })    
        }
        function bnk_down_cmnt()
        {
            var s_comment = $('#saral_cmnt').val();
            var sm_comment = $('#smart_cmnt').val();
            var sh_comment = $('#sharp_cmnt').val();
            var dataString = 's_comment=' + s_comment +'&sm_comment='+sm_comment+'&sh_comment='+sh_comment;
             $.ajax({
                type: "get",
                url: "{{url('bank_cmnt/save')}}",
                data: dataString,
                success: function (data) {
                    alert(data);
                    $('#saral_cmnt').val('');
                    $('#smart_cmnt').val('');
                    $('#sharp_cmnt').val('');
                }
            });
        }
        function txn_onhold_up()
        {
            var hold_api = $('#hold_api').val();
            var dataString = 'hold_api=' + hold_api+'&hold_on=on';
             $.ajax({
                type: "get",
                url: "{{url('/txn_onhold/save')}}",
                data: dataString,
                success: function (data) {
                    alert(data.message);
                    location.reload();
                }
            });
        }
        function txn_onhold_down()
        {
            var hold_api = $('#hold_api').val();
            var dataString = 'hold_api=' + hold_api+'&hold_on=off';;
             $.ajax({
                type: "get",
                url: "{{url('/txn_onhold/save')}}",
                data: dataString,
                success: function (data) {
                    alert(data.message);
                    location.reload();
                }
            });
        }
		function serviceOnOff(providerId,serviceVar,serviceType)
		{
			var newStatus = $("#"+serviceVar+"_"+providerId).val();
			if(confirm('Are your sure want to update'))
			{				
				 var dataString = 'providerId=' + providerId +'&serviceVar='+serviceVar+'&serviceType='+serviceType+'&newStatus='+newStatus;
				 $.ajaxSetup({
					headers: {
						'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
					}
				})
				 $.ajax({
					type: "put",
					url: "{{url('switch/onoff')}}/"+providerId,
					data: dataString,
					dataType:"json",
					success: function (data) {
						$("#spanMessage_"+providerId).html(data.message);
					}
				});
			}
			else
			{				
				status = (newStatus) ? 0 : 1;
				$("#"+serviceVar+"_"+providerId).val(status);
			}
		}
	
		
    </script>

<div class="panel-body">
    <div class="box" style="overflow-y: scroll;max-height: 600px">
	<input id="myInput" type="text" placeholder="Search.." class="pull-right">
        <table border='1' class="table table-striped table-bordered table-hover" id="dataTables-example">
			<thead>
				<tr>
				   
					<th>ID</th>
					<th>Provider Name</th>
					 <th>Category</th>
					 <th>Cyber</th>
					 <th>RedPay</th>
					 <th>A2Z</th>
					  <th>MRobotics</th>
					 <th>sevice On/Off</th>
					<th>Product</th>
					<th>Switch</th>
					<th>Message</th>
				</tr>
			</thead>
			<tbody id="myTable">
				@foreach($provider_manage as $providers)
				<tr class="odd gradeX">
					<td>{{$providers->id}}</td>
					<td>{{$providers->provider_name}}</td>
					<td>{{@$providers->service->service_name}}</td>
					<td>{{@$providers->cyber}}</td>
					<td>{{@$providers->redpay}}</td>
					<td>{{@$providers->suvidhaa}}</td>
					<td>{{@$providers->provider_code2}}</td>
					<td> @if(in_array($providers->service_id,array(1,2)))
						{{ Form::select('isServiceActive', ['1' => 'Start', '0' => 'Stop'],$providers->is_service_active, ['class'=>'form-control','style'=>($providers->is_service_active) ? "border-color:green" : "border-color:red" ,'id'=>'is_service_active_'.$providers->id,'onChange'=>"serviceOnOff($providers->id,'is_service_active','RECHARGE')"]) }}
						<span id="spanMessage_{{$providers->id}}"></span>
						@else
						{{ Form::select('isSeviceOnline', ['1' => 'OnLine', '0' => 'OffLine'],  $providers->is_service_online, ['class'=>'form-control','style'=>($providers->is_service_online) ? "border-color:green" : "border-color:red",'id'=>'is_service_online_'.$providers->id,'onChange'=>"serviceOnOff($providers->id,'is_service_online','BILL')"]) }}
					  <span id="spanMessage_{{$providers->id}}"></span>
					  @endif						
					</td>
					<td>
					 {{ Form::select('product',['1' => 'Cyber Plat','8' => 'Red Pay','13'=>'Suvidhaa','27'=>'MRobotics'],$providers->api_id, ['class'=>'form-control','id'=>'p_code_'.$providers->id]) }}
					</td>
					<td><a href="javascipt:void(0)" onclick="providerSwitch({{ $providers->id }})" class="btn btn-primary btn-sm" id="b_save_{{ $providers->id }}">Switch</a></td>
					<td><span id="message_{{$providers->id}}" style="color:green"></span>             					   
				</tr>
				  @endforeach
			</tbody>
		</table>
	</div>
</div>

    <meta name="_token" content="{!! csrf_token() !!}"/>
@endsection