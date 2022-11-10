<div class="row">
	@if(Session::has('success'))
	   <div class="alert alert-success">{!!session('success')!!}</div>
	@endif
</div>
 @if ($errors->any())
   <div class="alert alert-danger">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
  
@endif