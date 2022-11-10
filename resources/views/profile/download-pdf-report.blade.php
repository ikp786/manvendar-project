<style type="text/css">
    .text-center {
        text-align: center;
    }
    
    table>thead> .text-center {
        text-align: center;
        font-size: 10px;
    }
    
    tbody {
        display: table-row-group;
        vertical-align: middle;
        border-color: inherit;
        font-size: 10px;
    }
    
    .row {
        margin-right: -15px;
        margin-left: -15px;
        font-size: 10px;
    }
    
    .pull-left {
        float: left!important;
    }
    
    .pull-right {
        float: right!important;
    }
    
    h5 {
        font-size: 14px;
    }
    
    h3 {
        font-size: 24px;
    }
    
    h2 {
        font-size: 30px;
    }
    
    p {
        margin: 0 0 10px;
    }
    
    img {
        vertical-align: middle;
    }
    
    img {
        border: 0;
    }
    
    tr {
        display: table-row;
        vertical-align: inherit;
        border-color: inherit;
    }
    th{ border-left: 1px solid #ddd;
        font-size: 12px; 
    }
    td{
        border-top: 1px solid #ddd;
        border-left: 1px solid #ddd;
        padding:2px;
    }
</style>
<div class="text-center"> <img src="{{url('newlog/images/Logo168.png')}}"> 
<h4 class="text-center"> Ledger Reports</h4>
<div class="text-center" >
    <table class="table table-bordered" style="border: 1px solid #ddd;"> 
            <thead>
                <tr>
                    <th>Date/Time</th>
                    <th>ID</th>
                    <th>Remitter Number</th>
                    <th>Acc/Mob/K Number<br>Bene Name</th>
                    <th>Bank Name/<br>IFSC</th>
                    <th>Operator Txn Id<br>/Remark</th>
                    <th>Amount</th>
                    <th>Web/App</th>
                    <th>Status</th>
                    <th>Bank RR Number/<br>Check</th>
                    <th>Description</th>
                    <th>Receipt</th>
                    <th>Credit/Debit</th>
                    <th>Opening Bal</th>
                    <th>Credit Amount</th>
                    <th>Debit Amount</th>
                    <th>TDS</th>
                    <th>Service Tax</th>
                    <th>Balance</th>
                    <th>Txn Type</th>
                    <th>fund Transfer</th>
                    <th>Complain</th>
                </tr>
            </thead>
            <tbody>
                @foreach($reports as $recharge_reports)
                <?php $s = $recharge_reports->created_at;
                $dt = new DateTime($s);?>
                <tr class="{{@$recharge_reports->status->status}}-text">
                    <td>{{ $dt->format('d-m-y')}}<br>{{ $dt->format('H:i:s') }}</td>
                    <td>{{ $recharge_reports->id }}</td>
                    <td>{{ $recharge_reports->customer_number }}</td>
                    <td>{{ $recharge_reports->number }}
                    <p>{{ @$recharge_reports->biller_name}}</p>
                    <p><span style="font-weight:bold;">{{@$recharge_reports->beneficiary->name}}</p></span>
                    </td>
                    <td>
                   @if(in_array($recharge_reports->api_id,array(2,10)))
                    <?php 
                        $content = explode("(",$recharge_reports->description);
                        try{
                            echo $recharge_reports->description; 
                        }
                        catch(\Exception $e)
                        {
                            echo $recharge_reports->description;
                        }
                    ?>
                   
                    @else
                        <p>
                        {{ @$recharge_reports->beneficiary->bank_name }}</p>
                        <p>{{ @$recharge_reports->beneficiary->ifsc }}
                        </p>
                      @endif  
                    </td>
                    <td>    
                        @if($recharge_reports->api_id=='25')
                            {{ $recharge_reports->paytm_txn_id}}
                        @else  
                            {{ $recharge_reports->txnid}}
                        @endif
                    <br>{{$recharge_reports->remark}}</td>
                    <td>{{number_format($recharge_reports->amount,2) }}</td>
                    <td>{{$recharge_reports->mode}}</td>
                    <td>{{@$recharge_reports->status->status }} 
                        <br>{{$recharge_reports->txn_initiated_date}}
                        <span id="checkstatusMessage_{{$recharge_reports->id}}" style="color:green"></span>
                        <p>@if($recharge_reports->recharge_type==0 && $recharge_reports->txnid !="DT" && !in_array($recharge_reports->api_id,array(2,10))) 
                            {{($recharge_reports->channel==2)?"IMPS":"NEFT"}}</p>@endif 
                    </td>
                    <td>@if(@$recharge_reports->status_id !=4)
                        {{ $recharge_reports->bank_ref }}   
                        @endif                      
                        @if(in_array(@$recharge_reports->status_id,array(1,3,9,18,34)) && $recharge_reports->api_id !=10 )
                            @if($recharge_reports->api_id=='27')
                                 <input type="button" disabled id ="checkBtn_{{$recharge_reports->id}}" class="btn btn-primary btn-xs" value="Check"/>
                             @else
                                 <input type="button" id ="checkBtn_{{$recharge_reports->id}}" onclick="TramocheckStatus({{ $recharge_reports->id }},{{$recharge_reports->api_id}})" class="btn btn-primary btn-xs" value="Check"/>
                            @endif
                            @elseif(@$recharge_reports->status_id ==2)
                           {{ @$recharge_reports->fail_msg }}
                        @endif
                    </td>

                    <td>@if($recharge_reports->recharge_type== 1)
                            {{ @$recharge_reports->provider->provider_name }}  
                            @else
                            {{ @$recharge_reports->api->api_name }} 
                         @endif
                     </td> 
                    <td style="text-align:center">
                    @if(in_array(@$recharge_reports->status_id,array(1,3,9,18,24,34)))
                    <a target="_blank" href="{{ url('invoice') }}/{{ $recharge_reports->id }}">
                    <span class="btn btn-info btn-xs" style="font-size: 14px;">
                    <i class="md md-visibility"></i>Receipt</span>
                    </a>@endif
                    </td>                        
                    <td>{{ $recharge_reports->type }}</td>
                    <td>{{ number_format($recharge_reports->opening_balance,2) }}</td>
                    <td>{{ $recharge_reports->credit_charge }}</td>
                    <td>{{ $recharge_reports->debit_charge }}</td>
                    <td>{{ number_format($recharge_reports->tds,3) }}</td>
                    <td>{{ number_format($recharge_reports->gst,2) }}</td>
                    <td>{{ number_format($recharge_reports->total_balance,2) }}</td>
                    <td>{{ $recharge_reports->txn_type }}</td>
                    <td>@if($recharge_reports->txnid=="DT")
                            {{$recharge_reports->description}}
                        @endif
                    </td> 
                    <td>
                        @if(in_array(@$recharge_reports->status_id,array(1,3,9,18,24,34)))
                            <a onclick="Complain({{ $recharge_reports->id }})" data-toggle="modal" href="#example">Complain</a> 
                        @endif</td>
                </tr>
                @endforeach
            </tbody>
    </table>
</div> 