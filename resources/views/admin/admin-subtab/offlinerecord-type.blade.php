<ul class="nav nav-tabs" >
	     <li class="{{(Request::is('admin/offline/record') ? 'active' : '')}}">
			<a href="{{url('admin/offline/record')}}"><span>OffLine</span></a></li>
	      <li class="{{(Request::is('admin/offline/updated-record') ? 'active' : '')}}"><a href="{{route('offline-updated')}}"><span>Update Offline</span></a></li>
     
</ul>