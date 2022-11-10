@extends('admin.layouts.templatetable')


@section('content')
<script type="text/javascript">
	
	function  updateRecord(id)
	{
		//$('#change-bg-color-model').modal('show');
		if(confirm("Are you want to update record of Company id : "+id)){
		$.ajaxSetup({
			headers: {
				'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
				}
			})
        var formData = {
        		company_id: id,
                agent_header_color: $('#agent_header_color_'+id).val(),
                agent_bg_color: $('#agent_bg_color_'+id).val(),
				md_font_color: $('#md_font_color_'+id).val(),
                md_bg_color: $('#md_bg_color_'+id).val(),
				agent_font_color: $('#agent_font_color_'+id).val(),
                news: $('#news_'+id).val(),
			} 
          $.ajax({
                type: "PUT",
                url: "{{url('admin/update-background')}}",
                data: formData,
                dataType: "json",
                 beforeSend: function() {
                        $("#icon_"+id).hide();
                        $.LoadingOverlay("show", {
	                        image       : "",
	                        fontawesome : "fa fa-spinner fa-spin"
	                    });
                    },
                success: function (result) {
                        $.LoadingOverlay("hide");
                        $("#icon_"+id).show();
                        if (result.status == 1) {
                          alert(result.message)
                          location.reload();
                        }
                        else
                        	alert(result.message)
                    }

                });	
      }
      

	}

</script>
<div class="row">
    <div class="col-sm-12">
        <div class="card-box">
 			<table  data-toggle="table"  data-page-list="[5, 10, 20]">
                <thead>
                    <tr>
                        <th > S. No</th>
                        <th data-field="company-id">Company Id</th>
                        <th data-field="company_name">Company name</th>
                        <th data-field="user-id" data-sortable="true">User Id</th>
                        <th data-field="news" data-sortable="true">News</th>
                        <th data-field="heade-color" data-sortable="true">Agent Header Color</th>
                        <th data-field="ag-bg-color" data-sortable="true">Agent BG Color</th>
                        <th data-field="bg-color" data-sortable="true">Md BG Color</th>
						<th data-field="agent-font-color" data-sortable="true">Agent Font col</th>
                        <th data-field="md-agent-color" data-sortable="true">Md Font col</th>
                        <th data-field="action" data-align="center" data-sortable="true">Action
                        </th>
                       
                    </tr>
                </thead>
				<tbody>
                    @foreach($company_lists as $key => $list)

                        <tr>
                            <td>{{ $key+1 }}</td>
                            <td >{{ $list->id }}</td>
                            <td>{{ $list->company_name}}</td>
							<td>{{ $list->user_id }}</td>
                             <td><textarea rows="3" cols="20" id="news_{{ $list->id}}">{{ $list->news }}</textarea></td>
                            <td><input type="text" value="{{ $list->agent_header_color }}" id="agent_header_color_{{$list->id}}" style="width: 100%"></td>
                             <td><input type="text" value="{{ $list->agent_bg_color }}" id="agent_bg_color_{{$list->id}}" style="width: 100%"></td>
                            <td><input type="text" value="{{ $list->md_bg_color }}" id ="md_bg_color_{{$list->id}}" style="width: 100%"></td> 
                            <td><input type="text" value="{{ $list->agent_font_color }}" id ="agent_font_color_{{$list->id}}" style="width: 100%"></td> 
                            <td><input type="text" value="{{ $list->md_font_color }}" id ="md_font_color_{{$list->id}}" style="width: 100%"></td>
                            
                            <td><a href="#" id="icon_{{$list->id}}" onclick="updateRecord({{$list->id}})" title="Click to Update Record"><span class="glyphicon glyphicon-edit"></span></a></td>
                        </tr>
                    @endforeach

                </tbody>
            </table>
            
            <meta name="csrf-token" content="{{ csrf_token() }}">
           <!-- <div class="modal fade" id="change-bg-color-model" role="dialog">
			    <div class="modal-dialog" style="width: 50%">
			    
			      
			      <div class="modal-content">
			        <div class="modal-header">
			          <button type="button" class="close" data-dismiss="modal">&times;</button>
			          <h4 class="modal-title">Update Backgroud Color</h4>
			        </div>
			        <div class="modal-body">
			          <div class="container" style="background-color: white;   ">
					<div id='form-container'>
					  
					  @if(session()->has('message'))
						<div class="alert alert-success" style="text-align: center;width: 42%;margin-left: 11%;">
							{{ session()->get('message') }}
						</div>
					@endif
					@if(session()->has('err-message'))
						<div class="alert alert-danger" style="text-align: center;width: 42%;margin-left: 11%;">
							{{ session()->get('err-message') }}
						</div>
					@endif
					  <!-- {!! Form::open(['url' => 'admin/update-background','method']) !!} 
					    <div class="form-group col-md-9">
					      <label class="control-label col-sm-4" for="color" style="text-align: right;">Menu Bar Color:</label>
					      <div class="col-sm-7">
					        <input type="text" class="form-control" id="color_nav" placeholder="Enter Menu Color"  name="color_nav" autocomplete ="off">
					      </div>
					    </div>
					    <div class="form-group col-md-9">
					      <label class="control-label col-sm-4" for="bg color" style="text-align: right;">Background Color:</label>
					      <div class="col-sm-7">          
					        <input type="text" class="form-control" id="color_content" placeholder="Enter Background Color" name="color_content" autocomplete ="off" >
					        <input type="hidden" class="form-control"  name="company_id" autocomplete ="off" >
					      </div>
					    </div>
					    
					    <div class="form-group">        
					      <div class="col-sm-offset-3 col-sm-8">
					        <button type="submit" class="btn btn-default">Submit</button>
					      </div>
					    </div>
					  <!-- {!! Form::open(['url' => 'foo/bar']) !!}
					</div>
				</div>
			        </div>
			        <div class="modal-footer">
			          <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
			        </div>
			      </div>
			      
			    </div>
			 </div> --> 	
				
			</div>
		</div>
	</div>
@endsection

