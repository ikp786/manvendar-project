@extends('admin.layouts.templatetable')

@section('content')
    <script>
        function add_record() {
            $('#btn-save').val("add");
            $('#frmTasks').trigger("reset");
            $("#con-close-modal").modal("toggle");
        }
        //create new task / update existing task
        function savedata() {
            var url = "provider";
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                }
            })
            var formData = {
                provider_name: $('#provider_name').val(),
                provider_code: $('#provider_code').val(),
                service_id: $('#service_id').val(),
                api_code: $('#api_code').val(),
                api_id: $('#api_id').val(),
                provider_picture: $('#provider_picture').val(),
            }

            //used to determine the http verb to use [add=POST], [update=PUT]
            var state = $('#btn-save').val();

            var type = "POST"; //for creating new resource
            var task_id = $('#id').val();
            var my_url = url;

            if (state == "update") {
                type = "PUT"; //for updating existing resource
                my_url += '/' + task_id;
            }
            $.ajax({
                type: type,
                url: my_url,
                data: formData,
                dataType: 'text',
                success: function (data) {
                    var obj = $.parseJSON(data);
                    if (obj.success == false) {
                        var obj1 = obj.errors;
                        //alert(obj1["provider_name"]);
                        var html = "";
                        for (var key in obj1)
                                //alert(obj1[key]);
                        {
                            html += "<li>" + obj1[key] + "</li>";
                        }
                        $("#name-error").show();
                        $("#name-error").html("<div class='alert alert-danger'><ul>" + html + "</ul></div>");
                    } else {
                        var html = "";
                        for (var key in obj) {
                            html += "<li>" + obj[key] + "</li>";
                        }
                        $("#name-error").show();
                        $("#name-error").html("<div class='alert alert-success'><ul>" + html + "</ul></div>");
                    }
                }

            });
        }
      
    </script>

<style>
table {
    border-collapse: collapse;
}

table, th, td {
    border: 1px solid black;
}
</style>


    <!-- Page-Title -->
    <div class="row">
        <div class="col-sm-12">
            <div class="col-lg-6 col-md-6">
                <h4 class="page-title">{{ $page_title or 'invoice Detail' }}</h4>
                <ol class="breadcrumb">
                    <li>
                        <a href="{{ url('dashboard') }}">Home</a>
                    </li>
                    <li class="active">
                        {{ $page_title or 'invoice Report' }}
                    </li>
                </ol>
            </div>
            <div class="col-lg-6 col-md-6">
                <div class="pull-right">
                    <button onclick="add_record()" id="demo-add-row" class="btn btn-success"><i
                                class="fa fa-plus m-r-5"></i>Add Record
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!--Basic Columns-->
    <!--===================================================-->


    <!--===================================================-->
    <div class="row">
        <div class="col-sm-12">
           
            <div class="card-box">
                <h4 class="m-t-0 header-title"><b>{{ $page_title or ' Tax Invoice Report' }}</b></h4>
                <p class="text-muted font-13">
                    Tax Invoice Report
                </p>

                <button id="demo-delete-row" class="btn btn-danger" disabled><i
                            class="fa fa-times m-r-5"></i>Delete
                </button>
                <table id="demo-custom-toolbar" data-toggle="table"
                       data-toolbar="#demo-delete-row"
                       data-search="true"
                       data-show-refresh="true"
                       data-show-toggle="true"
                       data-show-columns="true"
                       data-page-list="[5, 10, 20]"
                       data-page-size="40"
                       data-pagination="true" data-show-pagination-switch="true" class="table-bordered ">
                    <thead>
                    <tr>
                        <th data-field="id">Invoice ID</th>
                        <th data-field="user" data-sortable="true">
                            Year/Month
                        </th>
                        <th data-field="name" data-sortable="true">Buyers Code</th>
                        <th data-field="bank_name" data-sortable="true">View</th>
                        <th data-field="date" data-sortable="true" data-formatter="dateFormatter">Download
                        </th>
                        

                    </tr>
                    </thead>

                    <tbody>
                   @foreach($tax_invoice as $value)
                        <tr>
                            <td>{{ $value->id }}</td>
							<td>{{ $value->month }}</td>
                            <td>{{ $value->buyers_code }}</td>
                            
                            <td>
							<form method="get" action="{{ url('invoice-manage') }}"><input type="hidden" name="month" value="{{ $value->month }}">
							<button type="submit" class="btn btn-info pull-left">view</a></form></td>
                            <td> <button type="submit" class="btn btn-info pull-left">download</button></td>
                            

                        </tr>
                @endforeach

                    </tbody>
                </table>
				
				
            </div>
        </div>
    </div>
 <div id="con-close-modal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
         aria-hidden="true" style="display: none;">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                    <h4 class="modal-title">Complain Update</h4>
                </div>
                <div class="modal-body">
                    <div style="display:none" id="name-error"></div>
					<table width="585">
						<tbody>
						<tr>
						<td colspan="7"><img src="invoice.png"></td>
						</tr>
						<tr>
						<td width="211" colspan="3" rowspan="1">
						<p id="api_name"></p>
						</td>

						<td width="71" colspan="5">
						<p>Date: 31/07/2016</p>
						</td>

						</tr>
						<tr>
						<td colspan="3" >
						<p>Buyer's Code: LM ( )<br /> Buyer's Name:</p>
						</td>
						<td colspan="5" width="315">
						<p>CIN No: U74140DL2016PTC290796</p>
						<p>PAN NUMBER : AADCL0110C</p>
						<p>SERVICE TAX NUMBER : AADCL0110CSD001</p>
						</td>
						</tr>

						<tr>
						<td width="211">
						<p>DESCRIPTION OF SERVICES</p>
						</td>
						<td width="59">
						<p>&nbsp;</p>
						</td>
						<td width="50">
						<p>NO.</p>
						</td>
						<td width="65">
						<p>CHARGES</p>
						</td>
						<td width="71">
						<p>AMOUNT</p>
						</td>
						<td width="65">
						<p>SERVICE TAX<br /> (15%)</p>
						</td>
						<td width="65">
						<p>AMOUNT<br /> (RS.)</p>
						</td>
						</tr>
						<tr>
						<td width="211">
						<p>Transaction value</p>
						</td>
						<td width="59">&nbsp;</td>
						<td width="50">
						<p>942500</p>
						</td>
						<td width="65">&nbsp;</td>
						<td width="71">
						<p>2073.50</p>
						</td>
						<td width="65">
						<p>270.46</p>
						</td>
						<td width="65">
						<p>1803.04</p>
						</td>
						</tr>
						<tr>
						<td width="211">
						<p>Service Charges per Transaction</p>
						</td>
						<td width="59">&nbsp;</td>
						<td width="50">
						<p>250</p>
						</td>
						<td width="65">&nbsp;</td>
						<td width="71">
						<p>750.00</p>
						</td>
						<td width="65">
						<p>97.83</p>
						</td>
						<td width="65">
						<p>652.17</p>
						</td>
						</tr>
						<tr>
						<td width="211">
						<p>Services charges for Verification</p>
						</td>
						<td width="59">&nbsp;</td>
						<td width="50">
						<p>110</p>
						</td>
						<td width="65">&nbsp;</td>
						<td width="71">
						<p>220.00</p>
						</td>
						<td width="65">
						<p>28.70</p>
						</td>
						<td width="65">
						<p>191.30</p>
						</td>
						</tr>
						<tr>
						<td colspan="4" width="385">
						<p>&nbsp;</p>
						</td>
						<td width="71">&nbsp;</td>
						<td width="65">&nbsp;</td>
						<td width="65">
						<p>0.00</p>
						</td>
						</tr>
						<tr>
						<td colspan="4" width="385">
						<p>&nbsp;</p>
						</td>
						<td width="71">&nbsp;</td>
						<td width="65">&nbsp;</td>
						<td width="65">
						<p>0.00</p>
						</td>
						</tr>
						<tr>
						<td colspan="4" width="385">
						<p>&nbsp;</p>
						</td>
						<td width="71">&nbsp;</td>
						<td width="65">&nbsp;</td>
						<td width="65">
						<p>0.00</p>
						</td>
						</tr>
						<tr>
						<td colspan="4" width="385">
						<p>&nbsp;</p>
						</td>
						<td width="71">&nbsp;</td>
						<td width="65">&nbsp;</td>
						<td width="65">
						<p>0.00</p>
						</td>
						</tr>
						<tr>
						<td colspan="5" width="456">
						<p><strong>AMOUNT</strong></p>
						</td>
						<td width="65">&nbsp;</td>
						<td width="65">
						<p>2646.52</p>
						</td>
						</tr>
						<tr>
						<td colspan="5" width="456">
						<p><strong>SERVICE TAX</strong></p>
						</td>
						<td width="65">
						<p>14%</p>
						</td>
						<td width="65">
						<p>370.51</p>
						</td>
						</tr>
						<tr>
						<td colspan="5" width="456">
						<p><strong>SBC</strong></p>
						</td>
						<td width="65">
						<p>0.50%</p>
						</td>
						<td width="65">
						<p>13.23</p>
						</td>
						</tr>
						<tr>
						<td colspan="5" width="456">
						<p><strong>KKC</strong></p>
						</td>
						<td width="65">
						<p>0.50%</p>
						</td>
						<td width="65">
						<p>13.23</p>
						</td>
						</tr>
						<tr>
						<td colspan="5" width="456">
						<p><strong>TOTAL AMOUNT</strong></p>
						</td>
						<td width="65">&nbsp;</td>
						<td width="65">
						<p>3043.50</p>
						</td>
						</tr>

						</tbody>
						</table>
                   
                </div>
                <div class="modal-footer">
                    <button onclick="savedata()" type="button" class="btn btn-info waves-effect waves-light"
                            id="btn-save"
                            value="add">Update Now
                    </button>
                    <button type="button" class="btn btn-default waves-effect" data-dismiss="modal">Close</button>
                    <input type="hidden" id="id" name="id" value="0">
                </div>
            </div>
        </div>
    <!-- END wrapper -->
    <meta name="_token" content="{!! csrf_token() !!}"/>
@endsection