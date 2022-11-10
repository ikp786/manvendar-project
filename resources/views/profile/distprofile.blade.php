@extends('admin.layouts.templatetable')
@section('content')
<script type="text/javascript">

    function updateRecord(id) {
            $('.span-img img').show();//add by AVI
            $('#email').prop('readonly',false);//added by AVI
            $('#name').prop('readonly',false);// by AVI
            $('#btn-save').prop('disabled',false);// by AVI
            var my_id = $('#my_id').val();
            var token = $("input[name=_token]").val();
            var dataString = 'id=' + id + '&_token=' + token;
            $.ajax({
                type: "get",
                url: "{{url('member/view')}}",
                data: dataString,
                dataType: "json",
                success: function (data) 
                {
                    $('#id').val(data.id);
                    $('#name').val(data.name);
                    $('#mobile').val(data.mobile);
                    $('#pan_number').val(data.pan_number);
                    $('#adhar_number').val(data.adhar_number);
                    $('#email').val(data.email);
                    $('#company').val(data.company);
                    $('#office_address').val(data.office_address),
                    $('#pin_code').val(data.pin_code);
                    $('#address').val(data.address);
                    $('#role_id').val(data.role_id);
                    $('#parent_id').val(data.parent_id);
                    $('#agentcode').val(data.agentcode);
                    $('#yagentcode').val(data.yagentcode);
                    /* For Image Display*/
                    $('.span-img img').attr("height",'50px')
                    $('.span-img img').attr("width",'50px')
                    $('#profile-image img').attr("src","{{url('user-uploaded-files')}}/"+data.id+"/"+data.profile_picture);
                    $('#btn-save').val("update");
                   
                }
            })

        }
</script>
<style type="text/css">
#frmTasks{
     overflow-x: scroll; 
}
*, ::after, ::before {
     box-sizing: border-box; 
}
.form-group {
     margin-bottom: 0rem;
   
</style>
<section class="row">
   <div id="frmTasks" name="frmTasks" class="form-group-row">
        <div class="form-group row col-md-12">
                <h6> Joining Date : {{ date("d-m-Y", strtotime(Auth::user()->created_at)) }}
                           &nbsp; &nbsp; &nbsp; 
                    Last Update : {{ date("d-m-Y", strtotime(Auth::user()->updated_at)) }}</h6>
            </div>
             @include('partials.message_error')  
          <br>
        <div class="form-group row">
                   
            <div class="form-group row col-md-6">
               <label for="inputTask" class="control-label col-md-3">Full Name<span style="color:red"> *</span></label>
                <div class="col-md-6">
                    <input type="text" class="form-control has-error" id="name" name="name" placeholder="Full Name" value="{{ Auth::user()->name}}">
                </div>
            </div>
            <div class="form-group row col-md-6">
                <label for="inputEmail3" class="control-label col-md-3">Email</label>
                <div class="col-md-6">
                    <input type="text" class="form-control" id="email" name="email" placeholder="Email Id" value="{{Auth::user()->email}}">
                </div>
            </div>
        </div>
        <div class="form-group row">
            <div class="form-group row col-md-6">
                <label for="inputEmail3" class="control-label col-md-3">Mobile Number</label>
                 <div class="col-md-6">
                    <input type="text" class="form-control" id="mobile" name="mobile" placeholder="Mobile number" value="{{Auth::user()->mobile}}" maxlength="10" required="">
                </div>
            </div>
      
            <div class="form-group row col-md-6">
                <label for="inputEmail3" class="control-label col-md-3">Pin Code </label>
                <div class="col-md-6">
                    <input type="text" class="form-control" id="pin_code" name="pin_code" placeholder="Pin Code" value="{{ Auth::user()->member->pin_code }}" maxlength="6">
                </div>
            </div>
        </div> 
        <div class="form-group row">
              <div class="form-group row col-md-6">
                <label for="inputEmail3" class="control-label col-md-3"> Shop Name</label>
                 <div class="col-md-6">
                     <input type="text" class="form-control" id="company" name="company" placeholder="Enter Your Shop Name" value="{{ Auth::user()->member->company }}" >
                </div>
            </div>
              <div class="form-group row col-md-6">
                <label for="inputEmail3" class="control-label col-md-3">Aadhaar Card<span style="color:red"> *</span></label>
                <div class="col-md-6">
                     <input type="text" class="form-control" id="adhar_number" name="adhar_number" value="{{ Auth::user()->member->adhar_number }}" placeholder="Enter Aadhaar Card Number" style="text-transform: uppercase" maxlength="12">
                </div>
            </div>
            
        </div>    
        <div class="form-group row">
           <div class="form-group row col-md-6">
                <label for="inputEmail3" class="control-label col-md-3">Pan Card</label>
                <div class="col-md-6">
                    <input type="text" class="form-control" id="pan_number" name="pan_number" value="{{ Auth::user()->member->pan_number }}" placeholder="Enter Pan Card Number" style="text-transform: uppercase" maxlength="10" required="">
                </div>
            </div>
            <div class="form-group row col-md-6">
                <label for="inputEmail3" class="control-label col-md-3">Shop Address</label>
                <div class="col-md-6">
                    <textarea id="office_address" class="form-control"name="office_address">{{ Auth::user()->member->office_address }}</textarea>
                </div>
            </div>
          
        </div>
        <div class="form-group row">
           
            <div class="form-group row col-md-6">
            <label for="inputEmail3" class="control-label col-md-3">Photo<span style="color:red">  </span></label>
            <div class="form-group col-md-6 hide-upload-image" id="show_profile">
               
                <img width="40%" height="30%" src="{{url('/user-uploaded-files')}}/{{Auth::id()}}/{{Auth::user()->profile->profile_picture}}" alt="{{ Auth::user()->name }}" ><br>
                  <input type="file"  id="profile_picture" name="profile_picture" value="{{ Auth::user()->member->profile_picture }}" >
               
                <span id="profile-image-error" class="has-error" style="color:red;"> <!-- file size : 200 KB --></span>
            </div>
            <span id="profile-image" class="span-img"><img src="" /></span>
        </div>    
         <div class="form-group row col-md-6">
                <label for="inputEmail3" class="control-label col-md-3">Address</label>
                <div class="col-md-6">
                    <textarea id="address" class="form-control" name="address">{{ Auth::user()->member->address }}</textarea>
                </div>
            </div>
        </div>
      <!--  <div class="col-md-6" style="text-align: center;">
            <button  onclick="updateRecord({{ $user->id }})" href="javascript:void(0)" type="button" class="btn-info waves-effect waves-light" id="btn-save" value="add">Update Now</button>
        </div> -->
    </div>
</section>




@endsection